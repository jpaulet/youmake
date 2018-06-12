<?php
if (empty($global['systemRootPath'])) {
    $global['systemRootPath'] = '../';
}
require_once $global['systemRootPath'] . 'videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/bootGrid.php';
require_once $global['systemRootPath'] . 'objects/user.php';

class UserGroups {

    private $id;
    private $group_name;

    function __construct($id, $group_name = "") {
        if (empty($id)) {
            // get the category data from category and pass
            $this->group_name = $group_name;
        } else {
            // get data from id
            $this->load($id);
        }
    }

    private function load($id) {
        $user = self::getUserGroupsDb($id);
        if (empty($user))
            return false;
        foreach ($user as $key => $value) {
            $this->$key = $value;
        }
    }

    static private function getUserGroupsDb($id) {
        global $global;
        $id = intval($id);
        $sql = "SELECT * FROM users_groups WHERE  id = ? LIMIT 1";
        $res = sqlDAL::readSql($sql, "i", array($id));
        $data = sqlDAL::fetchAssoc($res);
        sqlDAL::close($res);
        if (!empty($data)) {
            $user = $data;
        } else {
            $user = false;
        }
        return $user;
    }

    function save() {
        global $global;
        if (empty($this->isAdmin)) {
            $this->isAdmin = "false";
        }
        $formats = "";
        $values = array();
        if (!empty($this->id)) {
            $sql = "UPDATE users_groups SET group_name = ?, modified = now() WHERE id = ?";
            $formats = "si";
            $values = array($this->group_name,$this->id);
        } else {
            $sql = "INSERT INTO users_groups ( group_name, created, modified) VALUES (?,now(), now())";
            $formats = "s";
            $values = array($this->group_name);
        }
        return sqlDAL::writeSql($sql,$formats,$values);
    }

    function delete() {
        if (!User::isAdmin()) {
            return false;
        }

        global $global;
        if (!empty($this->id)) {
            $sql = "DELETE FROM users_groups WHERE id = ?";
        } else {
            return false;
        }
        return sqlDAL::writeSql($sql,"i",array($this->id));
    }

    private function getUserGroup($id) {
        global $global;
        $id = intval($id);
        $sql = "SELECT * FROM users_groups WHERE  id = ? LIMIT 1";
        $res = sqlDAL::readSql($sql, "i", array($id));
        $data = sqlDAL::fetchAssoc($res);
        sqlDAL::close($res);
        if (!empty($data)) {
            $category = $data;
        } else {
            $category = false;
        }
        return $category;
    }

    static function getAllUsersGroups() {
        global $global;
        $sql = "SELECT *,"
                . " (SELECT COUNT(*) FROM videos_group_view WHERE users_groups_id = ug.id ) as total_videos, "
                . " (SELECT COUNT(*) FROM users_has_users_groups WHERE users_groups_id = ug.id ) as total_users "
                . " FROM users_groups as ug WHERE 1=1 ";

        $sql .= BootGrid::getSqlFromPost(array('group_name'));

        $res = sqlDAL::readSql($sql);
        $fullData = sqlDAL::fetchAllAssoc($res);
        sqlDAL::close($res);
        $arr = array();
        if ($res!=false) {
            foreach ($fullData as $row) {
                $arr[] = $row;
            }
            //$category = $res->fetch_all(MYSQLI_ASSOC);
        } else {
            $arr = false;
            die($sql . '\nError : (' . $global['mysqli']->errno . ') ' . $global['mysqli']->error);
        }
        return $arr;
    }

    static function getTotalUsersGroups() {
        global $global;
        $sql = "SELECT id FROM users_groups WHERE 1=1  ";

        $sql .= BootGrid::getSqlSearchFromPost(array('group_name'));
        $res = sqlDAL::readSql($sql);
        $numRows = sqlDAL::num_rows($res);
        sqlDAL::close($res);
        return $numRows;
    }

    function getGroup_name() {
        return $this->group_name;
    }

    function setGroup_name($group_name) {
        $this->group_name = $group_name;
    }

    // for users

    static function updateUserGroups($users_id, $array_groups_id){
        if (!User::isAdmin()) {
            return false;
        }
        if (!is_array($array_groups_id)) {
            return false;
        }
        self::deleteGroupsFromUser($users_id);
        global $global;
        $sql = "INSERT INTO users_has_users_groups ( users_id, users_groups_id) VALUES (?,?)";
        foreach ($array_groups_id as $value) {
            $value = intval($value);
            sqlDAL::writeSql($sql,"ii",array($users_id,$value));
        }

        return true;
    }

    static function getUserGroups($users_id) {
        global $global;
        $res = sqlDAL::readSql("SHOW TABLES LIKE 'users_has_users_groups'");
        $result = sqlDAL::num_rows($res);
        sqlDAL::close($res);
        if (empty($result)) {
            $_GET['error'] = "You need to <a href='{$global['webSiteRootURL']}update'>update your system to ver 2.3</a>";
            return array();
        }
        if (empty($users_id)) {
            return array();
        }
        $sql = "SELECT * FROM users_has_users_groups"
                . " LEFT JOIN users_groups ON users_groups_id = id WHERE users_id = ? ";
        $res = sqlDAL::readSql($sql,"i",array($users_id));
        $fullData = sqlDal::fetchAllAssoc($res);
        sqlDAL::close($res);
        $arr = array();
        if ($res!=false) {
            foreach ($fullData as $row) {
                $arr[] = $row;
            }
        } else {
            $arr = false;
            die($sql . '\nError : (' . $global['mysqli']->errno . ') ' . $global['mysqli']->error);
        }
        return $arr;
    }

    static private function deleteGroupsFromUser($users_id){
        if (!User::isAdmin()) {
            return false;
        }

        global $global;
        if (!empty($users_id)) {
            $sql = "DELETE FROM users_has_users_groups WHERE users_id = ?";
        } else {
            return false;
        }
        return sqlDAL::writeSql($sql,"i",array($users_id));
    }

    // for users end

    // for videos

    static function updateVideoGroups($videos_id, $array_groups_id) {
        if (!User::canUpload()) {
            return false;
        }
        if (!is_array($array_groups_id)) {
            return false;
        }
        self::deleteGroupsFromVideo($videos_id);
        global $global;

        $sql = "INSERT INTO videos_group_view ( videos_id, users_groups_id) VALUES (?,?)";
        foreach ($array_groups_id as $value) {
            $value = intval($value);
            sqlDAL::writeSql($sql,"ii",array($videos_id,$value));
        }

        return true;
    }

    static function getVideoGroups($videos_id) {
        if(empty($videos_id)){
            return array();
        }
        global $global;
        //check if table exists if not you need to update
        $sql = "SELECT 1 FROM `videos_group_view` LIMIT 1";
        $res = sqlDAL::readSql($sql);
        sqlDAL::close($res);
        if (!$res) {
            if (User::isAdmin()) {
                $_GET['error'] = "You need to Update YouPHPTube to version 2.3 <a href='{$global['webSiteRootURL']}update/'>Click here</a>";
            }
            return array();
        }

        $sql = "SELECT * FROM videos_group_view as v "
                . " LEFT JOIN users_groups as ug ON users_groups_id = ug.id WHERE videos_id = ? ";
        $res = sqlDAL::readSql($sql,"i",array($videos_id));
        $fullData = sqlDAL::fetchAllAssoc($res);
        sqlDAL::close($res);
        $arr = array();
        if ($res!=false) {
            foreach ($fullData as $row) {
                $arr[] = $row;
            }
        } else {
            $arr = false;
            die($sql . '\nError : (' . $global['mysqli']->errno . ') ' . $global['mysqli']->error);
        }
        return $arr;
    }

    static private function deleteGroupsFromVideo($videos_id){
        if (!User::canUpload()) {
            return false;
        }

        global $global;
        if (!empty($videos_id)) {
            $sql = "DELETE FROM videos_group_view WHERE videos_id = ?";
        } else {
            return false;
        }
        return sqlDAL::writeSql($sql,"i",array($videos_id));
    }

}
