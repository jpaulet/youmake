<?php
if (empty($global['systemRootPath'])) {
    $global['systemRootPath'] = "../";
}
require_once $global['systemRootPath'] . 'videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/bootGrid.php';
require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/include_config.php';
require_once $global['systemRootPath'] . 'objects/video_statistic.php';
if (!class_exists('Video')) {

    class Video {

        private $id;
        private $title;
        private $clean_title;
        private $filename;
        private $description;
        private $views_count;
        private $status;
        private $duration;
        private $users_id;
        private $categories_id;
        private $old_categories_id;
        private $type;
        private $rotation;
        private $zoom;
        private $videoDownloadedLink;
        private $videoLink;
        private $next_videos_id;
        private $isSuggested;
        static $types = array('webm', 'mp4', 'mp3', 'ogg');
        private $videoGroups;
        private $videoAdsCount;
        static $statusDesc = array(
            'a' => 'active',
            'i' => 'inactive',
            'e' => 'encoding',
            'x' => 'encoding error',
            'd' => 'downloading',
            'xmp4' => 'encoding mp4 error',
            'xwebm' => 'encoding webm error',
            'xmp3' => 'encoding mp3 error',
            'xogg' => 'encoding ogg error',
            'ximg' => 'get image error');
        //ver 3.4
        private $youtubeId;
        static $typeOptions = array('audio', 'video', 'embed', 'linkVideo', 'linkAudio');

        function __construct($title = "", $filename = "", $id = 0) {
            global $global;
            $this->rotation = 0;
            $this->zoom = 1;
            if (!empty($id)) {
                $this->load($id);
            }
            if (!empty($title)) {
                $this->setTitle($title);
            }
            if (!empty($filename)) {
                $this->filename = $filename;
            }
        }

        function addView() {
            global $global;
            if (empty($this->id)) {
                return false;
            }
            $sql = "UPDATE videos SET views_count = views_count+1, modified = now() WHERE id = ?";


            $insert_row = sqlDAL::writeSql($sql,"i",array($this->id));

            if ($insert_row) {
                VideoStatistic::save($this->id);
                $this->views_count++;
                return $this->id;
            } else {
                die($sql . ' Error : (' . $global['mysqli']->errno . ') ' . $global['mysqli']->error);
            }
        }

        function load($id) {
            $video = self::getVideo($id, "", true);
            if (empty($video))
                return false;
            foreach ($video as $key => $value) {
                $this->$key = $value;
            }
        }

        function save($updateVideoGroups = false) {
            if (!User::isLogged()) {
                header('Content-Type: application/json');
                die('{"error":"' . __("Permission denied") . '"}');
            }
            if (empty($this->title)) {
                $this->title = uniqid();
            }
            if (empty($this->clean_title)) {
                $this->setClean_title($this->title);
            }
            $this->clean_title = self::fixCleanTitle($this->clean_title, 1, $this->id);
            global $global;

            if (empty($this->status)) {
                $this->status = 'e';
            }
            
            if (empty($this->type) || !in_array($this->type, self::$typeOptions)) {
                $this->status = 'video';
            }

            if (empty($this->isSuggested)) {
                $this->isSuggested = 0;
            } else {
                $this->isSuggested = 1;
            }

            if (empty($this->categories_id)) {
                $p = YouPHPTubePlugin::loadPluginIfEnabled("PredefinedCategory");
                if ($p) {
                    $this->categories_id = $p->getCategoryId();
                } else {
                    $this->categories_id = 1;
                }
            }
            $this->setTitle($global['mysqli']->real_escape_string(trim($this->title)));
            $this->setDescription($global['mysqli']->real_escape_string($this->description));
            $this->next_videos_id = intval($this->next_videos_id);
            if (empty($this->next_videos_id)) {
                $this->next_videos_id = 'NULL';
            }
            if (!empty($this->id)) {
                if (!$this->userCanManageVideo()) {
                    header('Content-Type: application/json');
                    die('{"error":"' . __("Permission denied") . '"}');
                }
                $sql = "UPDATE videos SET title = '{$this->title}',clean_title = '{$this->clean_title}',"
                        . " filename = '{$this->filename}', categories_id = '{$this->categories_id}', status = '{$this->status}',"
                        . " description = '{$this->description}', duration = '{$this->duration}', type = '{$this->type}', videoDownloadedLink = '{$this->videoDownloadedLink}', youtubeId = '{$this->youtubeId}', videoLink = '{$this->videoLink}', next_videos_id = {$this->next_videos_id}, isSuggested = {$this->isSuggested}, modified = now()"
                        . " WHERE id = {$this->id}";
            } else {
                $sql = "INSERT INTO videos "
                        . "(title,clean_title, filename, users_id, categories_id, status, description, duration,type,videoDownloadedLink, next_videos_id, created, modified, videoLink) values "
                        . "('{$this->title}','{$this->clean_title}', '{$this->filename}', {$_SESSION["user"]["id"]},{$this->categories_id}, '{$this->status}', '{$this->description}', '{$this->duration}', '{$this->type}', '{$this->videoDownloadedLink}', {$this->next_videos_id},now(), now(), '{$this->videoLink}')";
            }
            $insert_row = sqlDAL::writeSql($sql);
            if ($insert_row) {
                if (empty($this->id)) {
                    $id = $global['mysqli']->insert_id;
                    $this->id = $id;
                } else {
                    $id = $this->id;
                }
                if ($updateVideoGroups) {
                    require_once $global['systemRootPath'] . 'objects/userGroups.php';
                    // update the user groups
                    UserGroups::updateVideoGroups($id, $this->videoGroups);
                }
                Video::autosetCategoryType($id);
                if (!empty($this->old_categories_id)) {
                    Video::autosetCategoryType($this->old_categories_id);
                }

                return $id;
            } else {
                die($sql . ' Save Video Error : (' . $global['mysqli']->errno . ') ' . $global['mysqli']->error);
            }
        }

        static function autosetCategoryType($catId) {
            global $global, $config;
            if ($config->currentVersionLowerThen('5.01')) {
                return false;
            }
            $sql = "SELECT * FROM `category_type_cache` WHERE categoryId = ?";

            $res = sqlDAL::readSql($sql,"i",array($catId));
            $catTypeCache = sqlDAL::fetchAssoc($res);
            sqlDAL::close($res);
            
            $videoFound = false;
            $audioFound = false;
            if ($catTypeCache) {
                // 3 means auto
                if ($catTypeCache['manualSet'] == "0") {
                    // start incremental search and save
                    $sql = "SELECT * FROM `videos` WHERE categories_id = ?";
                    $res = sqlDAL::readSql($sql,"i",array($catId));
                    $fullResult = sqlDAL::fetchAllAssoc($res);
                    sqlDAL::close($res);
                    if ($res!=false) {
                        foreach($fullResult as $row) {
                            
                            if ($row['type'] == "audio") {
                                // echo "found audio";
                                $audioFound = true;
                            } else if ($row['type'] == "video") {
                                //echo "found video";
                                $videoFound = true;
                            }
                        }
                    }
                    
                    if (($videoFound == false) || ($audioFound == false)) {
                        $sql = "SELECT * FROM `categories` WHERE parentId = ?";
                        $res = sqlDAL::readSql($sql,"i",array($catId));
                        $fullResult = sqlDAL::fetchAllAssoc($res);
                        sqlDAL::close($res);
                        if ($res!=false) {
                            //$tmpVid = $res->fetch_assoc();
                            foreach ($fullResult as $row) {
                                $sql = "SELECT type,categories_id FROM `videos` WHERE categories_id = ?;";
                                $res = sqlDAL::readSql($sql,"i",array($row['parentId']));
                                $fullResult2 = sqlDAL::fetchAllAssoc($res);
                                sqlDAL::close($res);
                                foreach ($fullResult2 as $row) {
                                    if ($row['type'] == "audio") {
                                        //  echo "found audio";
                                        $audioFound = true;
                                    } else if ($row['type'] == "video") {
                                        //echo "found video";
                                        $videoFound = true;
                                    }
                                }
                            }
                        }
                    }
                    $sql = "UPDATE `category_type_cache` SET `type` = '";
                    if (($videoFound) && ($audioFound)) {
                        $sql .= "0";
                    } else if ($audioFound) {
                        $sql .= "1";
                    } else if ($videoFound) {
                        $sql .= "2";
                    }else{
                        $sql .= "0";
                    }
                    $sql .= "' WHERE `category_type_cache`.`categoryId` = ?;";
                    sqlDAL::writeSql($sql,"i",array($catId));
                }
            } else {
                // start incremental search and save - and a lot of this redundant stuff in a method.. 
                $sql = "SELECT type,categories_id FROM `videos` WHERE categories_id = ?;";
                $res = sqlDAL::readSql($sql,"i",array($catId));
                $fullResult2 = sqlDAL::fetchAllAssoc($res);
                sqlDAL::close($res);
                if ($res!=false) {
                    foreach($fullResult2 as $row) {
                        if ($row['type'] == "audio") {
                            $audioFound = true;
                        } else if ($row['type'] == "video") {
                            $videoFound = true;
                        }
                    }
                }
                if (($videoFound == false) || ($audioFound == false)) {
                    $sql = "SELECT parentId FROM `categories` WHERE parentId = ?;";
                    $res = sqlDAL::readSql($sql,"i",array($catId));
                    $fullResult2 = sqlDAL::fetchAllAssoc($res);
                    sqlDAL::close($res);
                    if ($res!=false) {
                        foreach($fullResult2 as $cat) {
                            $sql = "SELECT type,categories_id FROM `videos` WHERE categories_id = ?;";
                            $res = sqlDAL::readSql($sql,"i",array($cat['parentId']));
                            $fullResult2 = sqlDAL::fetchAllAssoc($res);
                            sqlDAL::close($res);
                            if ($res!=false) {
                                foreach($fullResult2 as $row) {
                                    if ($row['type'] == "audio") {
                                        $audioFound = true;
                                    } else if ($row['type'] == "video") {
                                        $videoFound = true;
                                    }
                                }
                            }
                        }
                    }
                }
                $sql = "SELECT * FROM `category_type_cache` WHERE categoryId = ?";
                $res = sqlDAL::readSql($sql,"i",array($catId));
                $exist = sqlDAL::fetchAssoc($res);
                sqlDAL::close($res);
                $sqlType = 99;
                if (($videoFound) && ($audioFound)) {
                    $sqlType = 0;
                } else if ($audioFound) {
                    $sqlType = 1;
                } else if ($videoFound) {
                    $sqlType = 2;
                }
                $values = array();
                if(empty($exist)){
                    $sql = "INSERT INTO `category_type_cache` (`categoryId`, `type`) VALUES (?, ?);";
                    $values = array($catId,$sqlType);
                } else {
                    $sql = "UPDATE `category_type_cache` SET `type` = ? WHERE `category_type_cache`.`categoryId` = ?;";
                    $values = array($sqlType,$catId);
                }
                sqlDAL::writeSql($sql,"ii",$values);
            }
        }

        // i would like to simplify the big part of the method above in this method, but won't work as i want.
        static function internalAutoset($catId, $videoFound, $audioFound) {
            global $config;
            if ($config->currentVersionLowerThen('5.01')) {
                return false;
            }
            $sql = "SELECT type,categories_id FROM `videos` WHERE categories_id = ?;";
            $res = sqlDAL::readSql($sql,"i",array($catId));
            $fullResult2 = sqlDAL::fetchAllAssoc($res);
            sqlDAL::close($res);
            if ($res!=false) {
                foreach($fullResult2 as $row) {
                    if ($row['type'] == "audio") {
                        $audioFound = true;
                    } else if ($row['type'] == "video") {
                        $videoFound = true;
                    }
                }
            }
            if (($videoFound == false) || ($audioFound == false)) {
                $sql = "SELECT parentId,categories_id FROM `categories` WHERE parentId = ?;";
                $res = sqlDAL::readSql($sql,"i",array($catId));
                $fullResult2 = sqlDAL::fetchAllAssoc($res);
                sqlDAL::close($res);
                if ($res!=false) {
                    foreach($fullResult2 as $cat) {
                        $sql = "SELECT type,categories_id FROM `videos` WHERE categories_id = ?;";
                        $res = sqlDAL::readSql($sql,"i",array($cat['parentId']));
                        $fullResult = sqlDAL::fetchAllAssoc($res);
                        sqlDAL::close($res);
                        if ($res!=false) {
                            foreach($fullResult as $row) {
                                if ($row['type'] == "audio") {
                                    $audioFound = true;
                                } else if ($row['type'] == "video") {
                                    $videoFound = true;
                                }
                            }
                        }
                    }
                }
            }
            return array($videoFound, audioFound);
        }

        function setClean_title($clean_title) {
            $clean_title = preg_replace('/[^0-9a-z]+/', '-', trim(strtolower(cleanString($clean_title))));
            $this->clean_title = $clean_title;
        }

        function setDuration($duration) {
            $this->duration = $duration;
        }

        function getIsSuggested() {
            return $this->isSuggested;
        }

        function setIsSuggested($isSuggested) {
            if (empty($isSuggested) || $isSuggested === "false") {
                $this->isSuggested = 0;
            } else {
                $this->isSuggested = 1;
            }
        }

        function setStatus($status) {
            if (!empty($this->id)) {
                global $global;
                $sql = "UPDATE videos SET status = '{$status}', modified = now() WHERE id = {$this->id} ";
                $res = sqlDAL::writeSql($sql);
                if ($global['mysqli']->errno != 0) {
                    die('Error on update Status: (' . $global['mysqli']->errno . ') ' . $global['mysqli']->error);
                }
            }
            $this->status = $status;
        }

        function setType($type) {
            $this->type = $type;
        }

        function setRotation($rotation) {
            $saneRotation = intval($rotation) % 360;

            if (!empty($this->id)) {
                global $global;
                $sql = "UPDATE videos SET rotation = '{$saneRotation}', modified = now() WHERE id = {$this->id} ";
                $res = sqlDAL::writeSql($sql);
                if ($global['mysqli']->errno!=0) {
                    die('Error on update Rotation: (' . $global['mysqli']->errno . ') ' . $global['mysqli']->error);
                }
            }
            $this->rotation = $saneRotation;
        }

        function getRotation() {
            return $this->rotation;
        }

        function getUsers_id() {
            return $this->users_id;
        }

        function setZoom($zoom) {
            $saneZoom = abs(floatval($zoom));

            if ($saneZoom < 0.1 || $saneZoom > 10) {
                die('Zoom level must be between 0.1 and 10');
            }

            if (!empty($this->id)) {
                global $global;
                $sql = "UPDATE videos SET zoom = '{$saneZoom}', modified = now() WHERE id = {$this->id} ";
                $res = sqlDAL::writeSql($sql);
                if ($global['mysqli']->errno!=0) {
                    die('Error on update Zoom: (' . $global['mysqli']->errno . ') ' . $global['mysqli']->error);
                }
            }

            $this->zoom = $saneZoom;
        }

        function getZoom() {
            return $this->zoom;
        }

        static private function getUserGroupsCanSeeSQL() {
            global $global;

            if (User::isAdmin()) {
                return "";
            }
            $sql = " (SELECT count(id) FROM videos_group_view as gv WHERE gv.videos_id = v.id ) = 0 ";
            if (User::isLogged()) {
                require_once 'userGroups.php';
                $userGroups = UserGroups::getUserGroups(User::getId());
                $groups_id = array();
                foreach ($userGroups as $value) {
                    $groups_id[] = $value['users_groups_id'];
                }
                if (!empty($groups_id)) {
                    $sql = " (({$sql}) OR ((SELECT count(id) FROM videos_group_view as gv WHERE gv.videos_id = v.id AND users_groups_id IN (" . implode(",", $groups_id) . ") ) > 0)) ";
                }
            }
            return " AND " . $sql;
        }

        static function getVideo($id = "", $status = "viewable", $ignoreGroup = false, $random = false, $suggetedOnly = false, $showUnlisted = false) {
            global $global, $config;
            if ($config->currentVersionLowerThen('5')) {
                return false;
            }
            $id = intval($id);
            $res = sqlDAL::readSql("SHOW TABLES LIKE 'likes'");
            $result = sqlDal::num_rows($res);
            sqlDAL::close($res);
            
            if (empty($result)) {
                $_GET['error'] = "You need to <a href='{$global['webSiteRootURL']}update'>update your system to ver 2.0</a>";
                header("Location: {$global['webSiteRootURL']}user?error={$_GET['error']}");
                return false;
            }
            $res = sqlDAL::readSql("SHOW TABLES LIKE 'likes'");
            $result = sqlDal::num_rows($res);
            sqlDAL::close($res);
            if (empty($result)) {
                $_GET['error'] = "You need to <a href='{$global['webSiteRootURL']}update'>update your system to ver 2.7</a>";
                header("Location: {$global['webSiteRootURL']}user?error={$_GET['error']}");
                return false;
            }

            $sql = "SELECT u.*, v.*, "
                    . " nv.title as next_title,"
                    . " nv.clean_title as next_clean_title,"
                    . " nv.filename as next_filename,"
                    . " nv.id as next_id,"
                    . " c.id as category_id,c.iconClass,c.name as category,c.iconClass,  c.clean_name as clean_category,c.description as category_description,c.nextVideoOrder as category_order, v.created as videoCreation, "
                    . " (SELECT count(id) FROM likes as l where l.videos_id = v.id AND `like` = 1 ) as likes, "
                    . " (SELECT count(id) FROM likes as l where l.videos_id = v.id AND `like` = -1 ) as dislikes, "
                    . " (SELECT count(id) FROM video_ads as va where va.videos_id = v.id) as videoAdsCount ";
            if (User::isLogged()) {
                $sql .= ", (SELECT `like` FROM likes as l where l.videos_id = v.id AND users_id = " . User::getId() . " ) as myVote ";
            } else {
                $sql .= ", 0 as myVote ";
            }
            $sql .= " FROM videos as v "
                    . "LEFT JOIN categories c ON categories_id = c.id "
                    . "LEFT JOIN users u ON v.users_id = u.id "
                    . "LEFT JOIN videos nv ON v.next_videos_id = nv.id "
                    . " WHERE 1=1 ";
            $sql .= static::getVideoQueryFileter();
            if (!$ignoreGroup) {
                $sql .= self::getUserGroupsCanSeeSQL();
            }
            if (!empty($_SESSION['type'])) {
                if ($_SESSION['type'] == 'video') {
                    $sql .= " AND (v.type = 'video' OR  v.type = 'embed' OR  v.type = 'linkVideo')";
                } else if ($_SESSION['type'] == 'audio') {
                    $sql .= " AND (v.type = 'audio' OR  v.type = 'linkAudio')";
                } else {
                    $sql .= " AND v.type = '{$_SESSION['type']}' ";
                }
            }

            if ($status == "viewable" || $status == "viewableNotAd" || $status == "viewableAdOnly") {
                $sql .= " AND v.status IN ('" . implode("','", Video::getViewableStatus($showUnlisted)) . "')";
                if ($status == "viewableNotAd") {
                    $sql .= " having videoAdsCount = 0 ";
                } elseif ($status == "viewableAd") {
                    $sql .= " having videoAdsCount > 0 ";
                }
            } elseif (!empty($status)) {
                $sql .= " AND v.status = '{$status}'";
            }

            if (!empty($_GET['catName'])) {
                $sql .= " AND c.clean_name = '{$_GET['catName']}'";
            }


            if (!empty($_GET['search'])) {
                $_POST['searchPhrase'] = $_GET['search'];
            }

            $sql .= BootGrid::getSqlSearchFromPost(array('v.title', 'v.description', 'c.name', 'c.description'));

            if (!empty($id)) {
                $sql .= " AND v.id = $id ";
            } elseif (empty($random) && !empty($_GET['videoName'])) {
                $sql .= " AND v.clean_title = '{$_GET['videoName']}' ";
            } elseif (!empty($random)) {
                $sql .= " AND v.id != {$random} ";
                $sql .= " ORDER BY RAND() ";
            } else if ($suggetedOnly && empty($_GET['videoName']) && empty($_GET['search']) && empty($_GET['searchPhrase'])) {
                $sql .= " AND v.isSuggested = 1 ";
                $sql .= " ORDER BY RAND() ";
            } else {
                $sql .= " ORDER BY v.Created DESC ";
            }

            $sql .= " LIMIT 1";

           // echo $sql."<br />";
            $res = sqlDAL::readSql($sql);
            $video = sqlDAL::fetchAssoc($res);
            sqlDAL::close($res);
            // to fix the bug null // should be fixed, but is still catched, when the sql-result is empty anyway - so prevent doing it double.
            /*if(is_null($video)){
                log_error("need to re-get the video ".$id." ".$status);
                $res = sqlDAL::readSql($sql, "", array(),true);
                $video = sqlDAL::fetchAssoc($res);
                sqlDAL::close($res);
            }*/
            
            
            if ($res!=false) {

                require_once 'userGroups.php';
                if (!empty($video)) {
                    $video['groups'] = UserGroups::getVideoGroups($video['id']);
                    $video['title'] = UTF8encode($video['title']);
                    $video['description'] = UTF8encode($video['description']);
                }
                
            } else {
                $video = false;
            }
            return $video;
        }

        static function getVideoFromFileName($fileName) {
            global $global;

            $sql = "SELECT id FROM videos WHERE filename = ? LIMIT 1";

            $res = sqlDAL::readSql($sql,"s",array($fileName));
            if ($res!=false) {
                $video = sqlDAL::fetchAssoc($res);
                sqlDAL::close($res);
                if (!empty($video['id'])) {
                    return self::getVideo($video['id'], "");
                }
                //$video['groups'] = UserGroups::getVideoGroups($video['id']);
            }
            error_log(" Not Found getVideoFromFileName({$fileName}) ");
            return false;
        }

        static function getVideoFromCleanTitle($clean_title) {
            // for some reason in some servers (CPanel) we got the error "Error while sending QUERY packet centos on a select"
            // even increasing the max_allowed_packet it only goes away when close and reopen the connection
            global $global, $mysqlHost, $mysqlUser, $mysqlPass, $mysqlDatabase, $mysqlPort;
            $global['mysqli']->close();
            $global['mysqli'] = new mysqli($mysqlHost, $mysqlUser, $mysqlPass, $mysqlDatabase, @$mysqlPort);

            $sql = "SELECT id  FROM videos  WHERE clean_title = ? LIMIT 1";
            $res = sqlDAL::readSql($sql,"s",array($clean_title));
            $video = sqlDAL::fetchAssoc($res);
            sqlDAL::close($res);
            if ($res) {
                return self::getVideo($video['id'], "");
                //$video['groups'] = UserGroups::getVideoGroups($video['id']);
            } else {
                return false;
            }
        }

        /**
         *
         * @global type $global
         * @param type $status
         * @param type $showOnlyLoggedUserVideos you may pass an user ID to filter results
         * @param type $ignoreGroup
         * @param type $videosArrayId an array with videos to return (for filter only)
         * @return boolean
         */
        static function getAllVideos($status = "viewable", $showOnlyLoggedUserVideos = false, $ignoreGroup = false, $videosArrayId = array(), $getStatistcs = false, $showUnlisted = false) {
            global $global, $config;
            if ($config->currentVersionLowerThen('5')) {
                return false;
            }
            $sql = "SELECT u.*, v.*, c.iconClass, c.name as category, c.clean_name as clean_category,c.description as category_description, v.created as videoCreation, v.modified as videoModified, "
                    . " (SELECT count(id) FROM video_ads as va where va.videos_id = v.id) as videoAdsCount, "
                    . " (SELECT count(id) FROM likes as l where l.videos_id = v.id AND `like` = 1 ) as likes, "
                    . " (SELECT count(id) FROM likes as l where l.videos_id = v.id AND `like` = -1 ) as dislikes "
                    . " FROM videos as v "
                    . " LEFT JOIN categories c ON categories_id = c.id "
                    . " LEFT JOIN users u ON v.users_id = u.id "
                    . " WHERE 1=1 ";

            $sql .= static::getVideoQueryFileter();
            if (!empty($videosArrayId) && is_array($videosArrayId)) {
                $sql .= " AND v.id IN ( " . implode(", ", $videosArrayId) . ") ";
            }

            if (!$ignoreGroup) {
                $sql .= self::getUserGroupsCanSeeSQL();
            }
            if (!empty($_SESSION['type'])) {
                if ($_SESSION['type'] == 'video') {
                    $sql .= " AND (v.type = 'video' OR  v.type = 'embed' OR  v.type = 'linkVideo')";
                } else if ($_SESSION['type'] == 'audio') {
                    $sql .= " AND (v.type = 'audio' OR  v.type = 'linkAudio')";
                } else {
                    $sql .= " AND v.type = '{$_SESSION['type']}' ";
                }
            }

            if ($status == "viewable" || $status == "viewableNotAd" || $status == "viewableAdOnly") {
                if(User::isLogged()){
                    $sql .= " AND (v.status IN ('" . implode("','", Video::getViewableStatus($showUnlisted)) . "') OR (v.status='u' AND v.users_id ='".User::getId()."'))";
                }else{
                    $sql .= " AND v.status IN ('" . implode("','", Video::getViewableStatus($showUnlisted)) . "')";
                }
                if ($status == "viewableNotAd") {
                    $sql .= " having videoAdsCount = 0 ";
                } elseif ($status == "viewableAd") {
                    $sql .= " having videoAdsCount > 0 ";
                }
            } elseif (!empty($status)) {
                $sql .= " AND v.status = '{$status}'";
            }
            if ($showOnlyLoggedUserVideos === true && !User::isAdmin()) {
                $sql .= " AND v.users_id = '" . User::getId() . "'";
            } elseif (!empty($showOnlyLoggedUserVideos)) {
                $sql .= " AND v.users_id = '{$showOnlyLoggedUserVideos}'";
            }

            if (!empty($_GET['catName'])) {
                $sql .= " AND c.clean_name = '{$_GET['catName']}'";
            }



            if (!empty($_GET['search'])) {
                $_POST['searchPhrase'] = $_GET['search'];
            }

            if (!empty($_GET['modified'])) {
                $_GET['modified'] = str_replace("'", "", $_GET['modified']);
                $sql .= " AND v.modified >= '{$_GET['modified']}'";
            }

            $sql .= BootGrid::getSqlFromPost(array('v.title', 'v.description', 'c.name', 'c.description'), empty($_POST['sort']['likes']) ? "v." : "");

            if (!empty($_GET['limitOnceToOne'])) {
                $sql .= " LIMIT 1";
                unset($_GET['limitOnceToOne']);
            }
            $res = sqlDAL::readSql($sql);
            $fullData = sqlDAL::fetchAllAssoc($res);
            sqlDAL::close($res);
            $videos = array();
            if ($res!=false) {
                require_once 'userGroups.php';
                foreach ($fullData as $row) {
                    if ($getStatistcs) {
                        $previewsMonth = date("Y-m-d 00:00:00", strtotime("-30 days"));
                        $previewsWeek = date("Y-m-d 00:00:00", strtotime("-7 days"));
                        $today = date('Y-m-d 23:59:59');
                        $row['statistc_all'] = VideoStatistic::getStatisticTotalViews($row['id']);
                        $row['statistc_today'] = VideoStatistic::getStatisticTotalViews($row['id'], false, date('Y-m-d 00:00:00'), $today);
                        $row['statistc_week'] = VideoStatistic::getStatisticTotalViews($row['id'], false, $previewsWeek, $today);
                        $row['statistc_month'] = VideoStatistic::getStatisticTotalViews($row['id'], false, $previewsMonth, $today);
                        $row['statistc_unique_user'] = VideoStatistic::getStatisticTotalViews($row['id'], true);
                    }
                    $row['groups'] = UserGroups::getVideoGroups($row['id']);
                    $row['tags'] = self::getTags($row['id']);
                    $row['title'] = UTF8encode($row['title']);
                    $row['description'] = UTF8encode($row['description']);
                    $videos[] = $row;
                }
                //$videos = $res->fetch_all(MYSQLI_ASSOC);
            } else {
                $videos = false;
                die($sql . '\nError : (' . $global['mysqli']->errno . ') ' . $global['mysqli']->error);
            }
            return $videos;
        }

        static function getTotalVideos($status = "viewable", $showOnlyLoggedUserVideos = false, $ignoreGroup = false, $showUnlisted = false) {
            global $global;
            $cn = "";
            if (!empty($_GET['catName'])) {
                $cn .= " c.clean_name as cn,";
            }

            $sql = "SELECT v.users_id, v.type, v.id, v.title,v.description, c.name as category, {$cn} "
                    . " (SELECT count(id) FROM video_ads as va where va.videos_id = v.id) as videoAdsCount "
                    . "FROM videos v "
                    . "LEFT JOIN categories c ON categories_id = c.id "
                    . " WHERE 1=1 ";

            $sql .= static::getVideoQueryFileter();
            if (!$ignoreGroup) {
                $sql .= self::getUserGroupsCanSeeSQL();
            }
            if ($status == "viewable" || $status == "viewableNotAd" || $status == "viewableAdOnly") {
                $sql .= " AND v.status IN ('" . implode("','", Video::getViewableStatus($showUnlisted)) . "')";
                if ($status == "viewableNotAd") {
                    $sql .= " having videoAdsCount = 0 ";
                } elseif ($status == "viewableAd") {
                    $sql .= " having videoAdsCount > 0 ";
                }
            } elseif (!empty($status)) {
                $sql .= " AND status = '{$status}'";
            }
            if ($showOnlyLoggedUserVideos === true && !User::isAdmin()) {
                $sql .= " AND v.users_id = '" . User::getId() . "'";
            } elseif (is_int($showOnlyLoggedUserVideos)) {
                $sql .= " AND v.users_id = {$showOnlyLoggedUserVideos}";
            }
            if (!empty($_GET['catName'])) {
                $sql .= " AND cn = '{$_GET['catName']}'";
            }
            if (!empty($_SESSION['type'])) {
                if ($_SESSION['type'] == 'video') {
                    $sql .= " AND (v.type = 'video' OR  v.type = 'embed' OR  v.type = 'linkVideo')";
                } else if ($_SESSION['type'] == 'audio') {
                    $sql .= " AND (v.type = 'audio' OR  v.type = 'linkAudio')";
                } else {
                    $sql .= " AND v.type = '{$_SESSION['type']}' ";
                }
            }
            $sql .= BootGrid::getSqlSearchFromPost(array('v.title', 'v.description', 'c.name'));   
            $res = sqlDAL::readSql($sql);
            $numRows = sqlDal::num_rows($res);
            sqlDAL::close($res);

            return $numRows;
        }

        static function getTotalVideosInfo($status = "viewable", $showOnlyLoggedUserVideos = false, $ignoreGroup = false, $videosArrayId = array(), $getStatistcs = false) {
            $obj = new stdClass();
            $obj->likes = 0;
            $obj->disLikes = 0;
            $obj->views_count = 0;
            $obj->total_minutes = 0;

            $videos = static::getAllVideos($status, $showOnlyLoggedUserVideos, $ignoreGroup, $videosArrayId, $getStatistcs);

            foreach ($videos as $value) {
                $obj->likes += intval($value['likes']);
                $obj->disLikes += intval($value['dislikes']);
                $obj->views_count += intval($value['views_count']);
                $obj->total_minutes += intval(parseDurationToSeconds($value['duration']) / 60);
            }

            return $obj;
        }

        static private function getViewableStatus($showUnlisted = false) {
            /**
              a = active
              i = inactive
              e = encoding
              x = encoding error
              d = downloading
              u = unlisted
              xmp4 = encoding mp4 error
              xwebm = encoding webm error
              xmp3 = encoding mp3 error
              xogg = encoding ogg error
             */
            $viewable = array('a', 'xmp4', 'xwebm', 'xmp3', 'xogg');
            if(!empty($_GET['videoName'])){
                if($showUnlisted || self::isOwnerFromCleanTitle($_GET['videoName']) || User::isAdmin()){
                    $viewable[]="u";
                }
            }
            return $viewable;
        }

        static function getVideoConversionStatus($filename) {
            global $global;
            require_once $global['systemRootPath'] . 'objects/user.php';
            if (!User::isLogged()) {
                die("Only logged users can upload");
            }

            $object = new stdClass();

            foreach (self::$types as $value) {
                $progressFilename = "{$global['systemRootPath']}videos/{$filename}_progress_{$value}.txt";
                $content = @url_get_contents($progressFilename);
                $object->$value = new stdClass();
                if (!empty($content)) {
                    $object->$value = self::parseProgress($content);
                } else {
                    
                }

                if (!empty($object->$value->progress) && !is_numeric($object->$value->progress)) {

                    $video = self::getVideoFromFileName($filename);
                    //var_dump($video, $filename);
                    if (!empty($video)) {
                        $object->$value->progress = self::$statusDesc[$video['status']];
                    }
                }

                $object->$value->filename = $progressFilename;
            }

            return $object;
        }

        static private function parseProgress($content) {
            //get duration of source

            $obj = new stdClass();

            $obj->duration = 0;
            $obj->currentTime = 0;
            $obj->progress = 0;
            //var_dump($content);exit;
            preg_match("/Duration: (.*?), start:/", $content, $matches);
            if (!empty($matches[1])) {

                $rawDuration = $matches[1];

                //rawDuration is in 00:00:00.00 format. This converts it to seconds.
                $ar = array_reverse(explode(":", $rawDuration));
                $duration = floatval($ar[0]);
                if (!empty($ar[1])) {
                    $duration += intval($ar[1]) * 60;
                }
                if (!empty($ar[2])) {
                    $duration += intval($ar[2]) * 60 * 60;
                }

                //get the time in the file that is already encoded
                preg_match_all("/time=(.*?) bitrate/", $content, $matches);

                $rawTime = array_pop($matches);

                //this is needed if there is more than one match
                if (is_array($rawTime)) {
                    $rawTime = array_pop($rawTime);
                }

                //rawTime is in 00:00:00.00 format. This converts it to seconds.
                $ar = array_reverse(explode(":", $rawTime));
                $time = floatval($ar[0]);
                if (!empty($ar[1])) {
                    $time += intval($ar[1]) * 60;
                }
                if (!empty($ar[2])) {
                    $time += intval($ar[2]) * 60 * 60;
                }

                if (!empty($duration)) {
                    //calculate the progress
                    $progress = round(($time / $duration) * 100);
                } else {
                    $progress = 'undefined';
                }
                $obj->duration = $duration;
                $obj->currentTime = $time;
                $obj->progress = $progress;
            }
            return $obj;
        }

        function delete() {
            if (!$this->userCanManageVideo()) {
                return false;
            }

            global $global;
            if (!empty($this->id)) {
                $video = self::getVideo($this->id);
                $sql = "DELETE FROM videos WHERE id = ?";
            } else {
                return false;
            }
            $resp = sqlDAL::writeSql($sql,"i",array($this->id));
            if ($resp==false) {
                die('Error (delete on video) : (' . $global['mysqli']->errno . ') ' . $global['mysqli']->error);
            } else {
                $this->removeFiles($video['filename']);
                $aws_s3 = YouPHPTubePlugin::loadPluginIfEnabled('AWS_S3');
                if (!empty($aws_s3)) {
                    $aws_s3->removeFiles($video['filename']);
                }
            }
            return $resp;
        }

        private function removeFiles($filename) {
            if (empty($filename)) {
                return false;
            }
            global $global;
            $file = "{$global['systemRootPath']}videos/original_{$filename}";
            $this->removeFilePath($file);

            $files = "{$global['systemRootPath']}videos/{$filename}";
            $this->removeFilePath($files);
        }

        private function removeFilePath($filePath) {
            if (empty($filePath)) {
                return false;
            }
            // Streamlined for less coding space.
            $files = glob("{$filePath}*");
            foreach ($files as $file) {
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
        }

        function setDescription($description) {
            $this->description = strip_tags($description);
        }

        function setCategories_id($categories_id) {
            // to update old cat as well when auto..
            if (!empty($this->categories_id)) {
                $this->old_categories_id = $this->categories_id;
            }
            $this->categories_id = $categories_id;
        }

        static function getCleanDuration($duration = "") {
            if (empty($duration)) {
                if (!empty($this) && !empty($this->duration)) {
                    $durationParts = explode(".", $this->duration);
                } else {
                    return "00:00:00";
                }
            } else {
                $durationParts = explode(".", $duration);
            }
            if (empty($durationParts[0])) {
                return "00:00:00";
            } else {
                $duration = $durationParts[0];
                $durationParts = explode(':', $duration);
                if (count($durationParts) == 1) {
                    return '0:00:' . static::addZero($durationParts[0]);
                } elseif (count($durationParts) == 2) {
                    return '0:' . static::addZero($durationParts[0]) . ':' . static::addZero($durationParts[1]);
                }
                return $duration;
            }
        }

        static private function addZero($str) {
            if (intval($str) < 10) {
                return '0' . intval($str);
            }
            return $str;
        }

        static function getItemPropDuration($duration = '') {
            $duration = static::getCleanDuration($duration);
            $parts = explode(':', $duration);
            return 'PT' . intval($parts[0]) . 'H' . intval($parts[1]) . 'M' . intval($parts[2]) . 'S';
        }

        static function getItemDurationSeconds($duration = '') {
            if ($duration == "EE:EE:EE") {
                return 0;
            }
            $duration = static::getCleanDuration($duration);
            $parts = explode(':', $duration);
            return intval($parts[0] * 60 * 60) + intval($parts[1] * 60) + intval($parts[2]);
        }

        static function getDurationFromFile($file) {
            global $global;
            require_once($global['systemRootPath'] . 'objects/getid3/getid3.php');
            // get movie duration HOURS:MM:SS.MICROSECONDS
            if (!file_exists($file)) {
                error_log('{"status":"error", "msg":"getDurationFromFile ERROR, File (' . $file . ') Not Found"}');
                return "EE:EE:EE";
            }
            // Initialize getID3 engine
            $getID3 = new getID3;
            // Analyze file and store returned data in $ThisFileInfo
            $ThisFileInfo = $getID3->analyze($file);
            return static::getCleanDuration(@$ThisFileInfo['playtime_string']);
        }

        function updateDurationIfNeed($fileExtension = ".mp4") {
            global $global;
            $source = self::getSourceFile($this->filename, $fileExtension, true);
            $file = $source['path'];

            if (!empty($this->id) && $this->duration == "EE:EE:EE" && file_exists($file)) {
                $this->duration = Video::getDurationFromFile($file);
                error_log("Duration Updated: " . print_r($this, true));

                $sql = "UPDATE videos SET duration = ?, modified = now() WHERE id = ?";
                $res = sqlDAL::writeSql($sql,"si",array($this->duration, $this->id));
                return $this->id;
            } else {
                error_log("Do not need update duration: " . print_r($this, true));
                return false;
            }
        }

        function getFilename() {
            return $this->filename;
        }

        function getStatus() {
            return $this->status;
        }

        function getId() {
            return $this->id;
        }

        function getVideoDownloadedLink() {
            return $this->videoDownloadedLink;
        }

        function setVideoDownloadedLink($videoDownloadedLink) {
            $this->videoDownloadedLink = $videoDownloadedLink;
        }

        static function isLandscape($pathFileName) {
            global $config;
            // get movie duration HOURS:MM:SS.MICROSECONDS
            if (!file_exists($pathFileName)) {
                echo '{"status":"error", "msg":"getDurationFromFile ERROR, File (' . $pathFileName . ') Not Found"}';
                exit;
            }
            eval('$cmd="' . $config->getExiftool() . '";');
            $resp = true; // is landscape by default
            exec($cmd . ' 2>&1', $output, $return_val);
            if ($return_val !== 0) {
                $resp = true;
            } else {
                $w = 1;
                $h = 0;
                $rotation = 0;
                foreach ($output as $value) {
                    preg_match("/Image Size.*:[^0-9]*([0-9]+x[0-9]+)/i", $value, $match);
                    if (!empty($match)) {
                        $parts = explode("x", $match[1]);
                        $w = $parts[0];
                        $h = $parts[1];
                    }
                    preg_match("/Rotation.*:[^0-9]*([0-9]+)/i", $value, $match);
                    if (!empty($match)) {
                        $rotation = $match[1];
                    }
                }
                if ($rotation == 0) {
                    if ($w > $h) {
                        $resp = true;
                    } else {
                        $resp = false;
                    }
                } else {
                    if ($w < $h) {
                        $resp = true;
                    } else {
                        $resp = false;
                    }
                }
            }
            //var_dump($cmd, $w, $h, $rotation, $resp);exit;
            return $resp;
        }

        function userCanManageVideo() {
            if (empty($this->users_id) || !User::canUpload()) {
                return false;
            }
            // if you not admin you can only manager yours video
            if (!User::isAdmin() && $this->users_id != User::getId()) {
                return false;
            }
            return true;
        }

        function getVideoGroups() {
            return $this->videoGroups;
        }

        function setVideoGroups($userGroups) {
            if (is_array($userGroups)) {
                $this->videoGroups = $userGroups;
            }
        }

        /**
         *
         * @param type $user_id
         * text
         * label Default Primary Success Info Warning Danger
         */
        static function getTags($video_id, $type = "") {
            $video = new Video("", "", $video_id);
            $tags = array();

            if (empty($type) || $type === "ad") {
                $obj = new stdClass();
                $obj->label = __("Is Ad");
                if ($video->getIsAd()) {
                    $obj->type = "success";
                    $obj->text = __("Yes");
                    $tags[] = $obj;
                } else {
                    $obj->type = "danger";
                    $obj->text = __("No");
                    $tags[] = $obj;
                }
            }

            /**
              a = active
              i = inactive
              e = encoding
              x = encoding error
              d = downloading
              xmp4 = encoding mp4 error
              xwebm = encoding webm error
              xmp3 = encoding mp3 error
              xogg = encoding ogg error
              ximg = get image error
             */
            if (empty($type) || $type === "status") {
                $obj = new stdClass();
                $obj->label = __("Status");
                switch ($video->getStatus()) {
                    case 'a':
                        $obj->type = "success";
                        $obj->text = __("Active");
                        break;
                    case 'i':
                        $obj->type = "warning";
                        $obj->text = __("Inactive");
                        break;
                    case 'e':
                        $obj->type = "info";
                        $obj->text = __("Encoding");
                        break;
                    case 'd':
                        $obj->type = "info";
                        $obj->text = __("Downloading");
                        break;
                    case 'xmp4':
                        $obj->type = "danger";
                        $obj->text = __("Encoding mp4 error");
                        break;
                    case 'xwebm':
                        $obj->type = "danger";
                        $obj->text = __("Encoding xwebm error");
                        break;
                    case 'xmp3':
                        $obj->type = "danger";
                        $obj->text = __("Encoding xmp3 error");
                        break;
                    case 'xogg':
                        $obj->type = "danger";
                        $obj->text = __("Encoding xogg error");
                        break;
                    case 'ximg':
                        $obj->type = "danger";
                        $obj->text = __("Get imgage error");
                        break;

                    default:
                        $obj->type = "danger";
                        $obj->text = __("Status not found");
                        break;
                }
                $obj->text = $obj->text;
                $tags[] = $obj;
            }
            if (empty($type) || $type === "userGroups") {
                require_once 'userGroups.php';
                $groups = UserGroups::getVideoGroups($video_id);
                $obj = new stdClass();
                $obj->label = __("Group");
                if (empty($groups)) {
                    $status = $video->getStatus();
                    if($status=='u'){                        
                        $obj->type = "info";
                        $obj->text = __("Unlisted");
                    }else{
                        $obj->type = "success";
                        $obj->text = __("Public");
                    }
                    $tags[] = $obj;
                } else {
                    foreach ($groups as $value) {
                        $obj->type = "warning";
                        $obj->text = $value['group_name'];
                        $tags[] = $obj;
                    }
                }
            }
            if (empty($type) || $type === "category") {
                require_once 'category.php';
                if (!empty($_POST['sort']['title'])) {
                    unset($_POST['sort']);
                }
                $category = Category::getCategory($video->getCategories_id());
                $obj = new stdClass();
                $obj->label = __("Category");
                if (!empty($category)) {
                    $obj->type = "default";
                    $obj->text = $category['name'];
                    $tags[] = $obj;
                }
            }
            if (empty($type) || $type === "source") {
                $url = $video->getVideoDownloadedLink();
                $parse = parse_url($url);
                $obj = new stdClass();
                $obj->label = __("Source");
                if (!empty($parse['host'])) {
                    $obj->type = "danger";
                    $obj->text = $parse['host'];
                    $tags[] = $obj;
                } else {
                    $obj->type = "info";
                    $obj->text = __("Local File");
                    $tags[] = $obj;
                }
            }

            return $tags;
        }

        function getCategories_id() {
            return $this->categories_id;
        }

        function getType() {
            return $this->type;
        }

        function getIsAd() {
            return !empty($this->videoAdsCount);
        }

        static function fixCleanTitle($clean_title, $count, $videoId) {
            global $global;

            $sql = "SELECT * FROM videos WHERE clean_title = '{$clean_title}' ";
            if (!empty($videoId)) {
                $sql .= " AND id != {$videoId} ";
            }
            $sql .= " LIMIT 1";
            $res = sqlDAL::readSql($sql); 
            $cleanTitleExists = sqlDAL::fetchAssoc($res);
            sqlDAL::close($res);
            if ($cleanTitleExists!=false) {
                return self::fixCleanTitle($clean_title . $count, $count + 1, $videoId);
            }
            return $clean_title;
        }

        /**
         * 
         * @global type $global
         * @param type $videos_id
         * @param type $users_id if is empty will use the logged user
         * @return boolean
         */
        static function isOwner($videos_id, $users_id = 0) {
            global $global;
            if (empty($users_id)) {
                $users_id = User::getId();
                if (empty($users_id)) {
                    return false;
                }
            }

            $video_owner = self::getOwner($videos_id);
            if ($video_owner) {
                if ($video_owner == $users_id) {
                    return true;
                }
            }
            return false;
        }
        
        static function isOwnerFromCleanTitle($clean_title, $users_id=0) {
            global $global;
            $video = self::getVideoFromCleanTitle($clean_title);
            return self::isOwner($video['id'], $users_id);
        }

        /**
         * 
         * @global type $global
         * @param type $videos_id
         * @param type $users_id if is empty will use the logged user
         * @return boolean
         */
        static function getOwner($videos_id) {
            global $global;
            $sql = "SELECT users_id FROM videos WHERE id = ? LIMIT 1";
            $res = sqlDAL::readSql($sql,"i",array($videos_id)); 
            $videoRow = sqlDAL::fetchAssoc($res);
            sqlDAL::close($res);
            if ($res) {
                require_once 'userGroups.php';
                if ($videoRow!=false) {
                    return $videoRow['users_id'];
                }
            } else {
                $videos = false;
                die($sql . '\nError : (' . $global['mysqli']->errno . ') ' . $global['mysqli']->error);
            }
            return false;
        }

        /**
         * 
         * @param type $videos_id
         * @param type $users_id if is empty will use the logged user
         * @return boolean
         */
        static function canEdit($videos_id, $users_id = 0) {
            if (empty($users_id)) {
                $users_id = User::getId();
                if (empty($users_id)) {
                    return false;
                }
            }
            $user = new User($users_id);
            if (empty($user)) {
                return false;
            }

            if ($user->getIsAdmin()) {
                return true;
            }

            return self::isOwner($videos_id, $users_id);
        }

        static function getRandom($excludeVideoId = false) {
            return static::getVideo("", "viewableNotAd", false, $excludeVideoId);
        }

        static function getVideoQueryFileter() {
            global $global;
            $sql = "";
            if (!empty($_GET['playlist_id'])) {
                require_once $global['systemRootPath'] . 'objects/playlist.php';
                $ids = PlayList::getVideosIdFromPlaylist($_GET['playlist_id']);
                if (!empty($ids)) {
                    $sql .= " AND v.id IN (" . implode(",", $ids) . ") ";
                }
            }
            return $sql;
        }

        function getTitle() {
            return $this->title;
        }

        function getDescription() {
            return $this->description;
        }

        function getExistingVideoFile() {
            global $global;
            $file = $global['systemRootPath'] . "videos/original_" . $this->getFilename();
            if (!file_exists($file)) {
                $file = $global['systemRootPath'] . "videos/" . $this->getFilename() . ".mp4";
                if (!file_exists($file)) {
                    $file = $global['systemRootPath'] . "videos/" . $this->getFilename() . ".webm";
                    if (!file_exists($file)) {
                        $videos = getVideosURL($this->getFilename());
                        foreach ($videos as $value) {
                            if ($value['type'] == 'video' && file_exists($value['path'])) {
                                return $value['path'];
                            }
                        }
                        $file = false;
                    }
                }
            }
            return $file;
        }

        function getYoutubeId() {
            return $this->youtubeId;
        }

        function setYoutubeId($youtubeId) {
            $this->youtubeId = $youtubeId;
        }

        function setTitle($title) {
            $this->title = strip_tags($title);
        }

        function setFilename($filename) {
            $this->filename = $filename;
        }

        function getNext_videos_id() {
            return $this->next_videos_id;
        }

        function setNext_videos_id($next_videos_id) {
            $this->next_videos_id = $next_videos_id;
        }

        function queue() {
            global $config;
            if (!User::canUpload()) {
                return false;
            }
            global $global;
            $obj = new stdClass();
            $obj->error = true;

            $target = $config->getEncoderURL() . "queue";
            $postFields = array(
                'user' => User::getUserName(),
                'pass' => User::getUserPass(),
                'fileURI' => $global['webSiteRootURL'] . "videos/original_{$this->getFilename()}",
                'filename' => $this->getFilename(),
                'videos_id' => $this->getId(),
                "notifyURL" => "{$global['webSiteRootURL']}"
            );
            error_log("SEND To QUEUE: " . print_r($postFields, true));
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $target);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            $r = curl_exec($curl);
            $obj->response = $r;
            if ($errno = curl_errno($curl)) {
                $error_message = curl_strerror($errno);
                //echo "cURL error ({$errno}):\n {$error_message}";
                $obj->msg = "cURL error ({$errno}):\n {$error_message}";
            } else {
                $obj->error = false;
            }
            error_log("QUEUE CURL: " . print_r($obj, true));
            curl_close($curl);
            return $obj;
        }

        function getVideoLink() {
            return $this->videoLink;
        }

        function setVideoLink($videoLink) {
            $this->videoLink = $videoLink;
        }

        /**
         * 
         * @param type $filename
         * @param type $type
         * @return type .jpg .gif _thumbs.jpg _Low.mp4 _SD.mp4 _HD.mp4
         */
        static function getSourceFile($filename, $type = ".jpg", $includeS3 = false) {
            global $global;
            /*
              $name = "getSourceFile_{$filename}{$type}_";
              $cached = ObjectYPT::getCache($name, 86400);//one day
              if(!empty($cached)){
              return (array) $cached;
              }
             * 
             */
            $aws_s3 = YouPHPTubePlugin::loadPluginIfEnabled('AWS_S3');
            if (!empty($aws_s3)) {
                $aws_s3_obj = $aws_s3->getDataObject();
                if (!empty($aws_s3_obj->useS3DirectLink)) {
                    $includeS3 = true;
                }
            }
            $token = "";
            $secure = YouPHPTubePlugin::loadPluginIfEnabled('SecureVideosDirectory');
            if (!empty($secure) && ($type == ".mp4" || $type == ".webm")) {
                $token = "?" . $secure->getToken($filename);
            }
            $source = array();
            $source['path'] = "{$global['systemRootPath']}videos/{$filename}{$type}";
            $source['url'] = "{$global['webSiteRootURL']}videos/{$filename}{$type}{$token}";
            /* need it because getDurationFromFile */
            if ($includeS3 && ($type == ".mp4" || $type == ".webm")) {
                if (!file_exists($source['path']) || filesize($source['path']) < 1024) {
                    if (!empty($aws_s3)) {
                        $source = $aws_s3->getAddress("{$filename}{$type}");
                    }
                }
            }

            if (!file_exists($source['path'])) {
                if ($type != "_thumbs.jpg" && $type != "_thumbsSmall.jpg") {
                    return array('path' => false, 'url' => false);
                }
            }

            //ObjectYPT::setCache($name, $source);
            return $source;
        }

        static function getStoragePath() {
            global $global;
            $path = "{$global['systemRootPath']}videos/";
            return $path;
        }

        static function getImageFromFilename($filename, $type = "video") {
            global $global;
            $advancedCustom = YouPHPTubePlugin::getObjectDataIfEnabled("CustomizeAdvanced");
            /*
              $name = "getImageFromFilename_{$filename}{$type}_";
              $cached = ObjectYPT::getCache($name, 86400);//one day
              if(!empty($cached)){
              return $cached;
              }
             * 
             */
            $obj = new stdClass();
            $gifSource = self::getSourceFile($filename, ".gif");
            $jpegSource = self::getSourceFile($filename, ".jpg");
            $thumbsSource = self::getSourceFile($filename, "_thumbs.jpg");
            $thumbsSmallSource = self::getSourceFile($filename, "_thumbsSmall.jpg");
            $obj->poster = $jpegSource['url'];
            $obj->thumbsGif = $gifSource['url'];
            $obj->thumbsJpg = $thumbsSource['url'];
            $obj->thumbsJpgSmall = $thumbsSmallSource['url'];
            if (file_exists($gifSource['path'])) {
                $obj->thumbsGif = $gifSource['url'];
            }
            if (file_exists($jpegSource['path'])) {
                $obj->poster = $jpegSource['url'];
                $obj->thumbsJpg = $thumbsSource['url'];
                // create thumbs
                if (!file_exists($thumbsSource['path']) && filesize($jpegSource['path']) > 1024) {
                    error_log("Resize JPG {$jpegSource['path']}, {$thumbsSource['path']}");
                    im_resize($jpegSource['path'], $thumbsSource['path'], 250, 140);
                }
                // create thumbs
                if (!file_exists($thumbsSmallSource['path']) && filesize($jpegSource['path']) > 1024) {
                    error_log("Resize Small JPG {$jpegSource['path']}, {$thumbsSmallSource['path']}");
                    im_resize($jpegSource['path'], $thumbsSmallSource['path'], 250, 140, 5);
                }
            } else {
                if (($type !== "audio")&&($type !== "linkAudio")) {
                    $obj->poster = "{$global['webSiteRootURL']}view/img/notfound.jpg";
                    $obj->thumbsJpg = "{$global['webSiteRootURL']}view/img/notfoundThumbs.jpg";
                    $obj->thumbsJpgSmall = "{$global['webSiteRootURL']}view/img/notfoundThumbsSmall.jpg";
                } else {
                    $obj->poster = "{$global['webSiteRootURL']}view/img/audio_wave.jpg";
                    $obj->thumbsJpg = "{$global['webSiteRootURL']}view/img/audio_waveThumbs.jpg";
                    $obj->thumbsJpgSmall = "{$global['webSiteRootURL']}view/img/audio_waveThumbsSmall.jpg";
                }
            }

            if (empty($obj->thumbsJpg)) {
                $obj->thumbsJpg = $obj->poster;
            }
            if (empty($obj->thumbsJpgSmall)) {
                $obj->thumbsJpgSmall = $obj->poster;
            }
            //ObjectYPT::setCache($name, $obj);
            if (!empty($advancedCustom->disableAnimatedGif)) {
                $obj->thumbsGif = false;
            }
            return $obj;
        }
        
        static function getImageFromID($videos_id, $type = "video") {
            $video = new Video("", "", $videos_id);
            return self::getImageFromFilename($video->getFilename());
        }

        function getViews_count() {
            return intval($this->views_count);
        }

        static function get_clean_title($videos_id) {
            global $global;

            $sql = "SELECT * FROM videos WHERE id = ? LIMIT 1";
            
            $res = sqlDAL::readSql($sql,"i",array($videos_id)); 
            $videoRow = sqlDAL::fetchAssoc($res);
            sqlDAL::close($res);

            if ($res!=false) {
                if ($videoRow!=false) {
                    return $videoRow['clean_title'];
                }
            } else {
                $videos = false;
                die($sql . '\nError : (' . $global['mysqli']->errno . ') ' . $global['mysqli']->error);
            }
            return false;
        }

        static function get_id_from_clean_title($clean_title) {
            global $global;

            $sql = "SELECT * FROM videos WHERE clean_title = ? LIMIT 1";
            $res = sqlDAL::readSql($sql,"s",array($clean_title)); 
            $videoRow = sqlDAL::fetchAssoc($res);
            sqlDAL::close($res);
            if ($res!=false) {
                if ($videoRow!=false) {
                    return $videoRow['id'];
                }
            } else {
                $videos = false;
                die($sql . '\nError : (' . $global['mysqli']->errno . ') ' . $global['mysqli']->error);
            }
            return false;
        }

        /**
         * 
         * @global type $global
         * @param type $videos_id
         * @param type $clean_title
         * @param type $embed
         * @param type $type URLFriendly or permalink
         * @return String a web link
         */
        static function getLinkToVideo($videos_id, $clean_title = "", $embed = false, $type = "URLFriendly") {
            global $global;
            if ($type == "URLFriendly") {
                if (!empty($videos_id) && empty($clean_title)) {
                    $clean_title = self::get_clean_title($videos_id);
                }
                if ($embed) {
                    return "{$global['webSiteRootURL']}videoEmbed/{$clean_title}";
                } else {
                    return "{$global['webSiteRootURL']}video/{$clean_title}";
                }
            } else {
                if (empty($videos_id) && !empty($clean_title)) {
                    $videos_id = self::get_id_from_clean_title($clean_title);
                }
                if ($embed) {
                    return "{$global['webSiteRootURL']}vEmbed/{$videos_id}";
                } else {
                    return "{$global['webSiteRootURL']}v/{$videos_id}";
                }
            }
        }

        static function getPermaLink($videos_id, $embed = false) {
            return self::getLinkToVideo($videos_id, "", $embed, "permalink");
        }

        static function getURLFriendly($videos_id, $embed = false) {
            return self::getLinkToVideo($videos_id, "", $embed, "URLFriendly");
        }

        static function getPermaLinkFromCleanTitle($clean_title, $embed = false) {
            return self::getLinkToVideo("", $clean_title, $embed, "permalink");
        }

        static function getURLFriendlyFromCleanTitle($clean_title, $embed = false) {
            return self::getLinkToVideo("", $clean_title, $embed, "URLFriendly");
        }

        static function getLink($videos_id, $clean_title, $embed = false) {
            $advancedCustom = YouPHPTubePlugin::getObjectDataIfEnabled("CustomizeAdvanced");
            if (!empty($advancedCustom->usePermalinks)) {
                $type = "permalink";
            } else {
                $type = "URLFriendly";
            }

            return self::getLinkToVideo($videos_id, $clean_title, $embed, $type);
        }

        static function getTotalVideosThumbsUpFromUser($users_id, $startDate, $endDate) {
            global $global;

            $sql = "SELECT id from videos  WHERE users_id = ?  ";

            $res = sqlDAL::readSql($sql,"i",array($users_id)); 
            $videoRows = sqlDAL::fetchAllAssoc($res);
            sqlDAL::close($res);

            $r = array('thumbsUp' => 0, 'thumbsDown' => 0);

            if ($res!=false) {
                foreach($videoRows as $row) {
                    $values = array($row['id']);
                    $format = "i";
                    $sql = "SELECT id from likes WHERE videos_id = ? AND `like` = 1  ";
                    if (!empty($startDate)) {
                        $sql .= " AND `created` >= ? ";
                        $format .="s";
                        $values[]=$startDate;
                    }

                    if (!empty($endDate)) {
                        $sql .= " AND `created` <= ? ";
                        $format .="s";
                        $values[]=$endDate;
                    }
                    $res = sqlDAL::readSql($sql,$format,$values); 
                    $countRow = sqlDAL::num_rows($res);
                    sqlDAL::close($res);
                    $r['thumbsUp']+=$countRow;

                    $format="";
                    $values=array();
                    $sql = "SELECT id from likes WHERE videos_id = {$row['id']} AND `like` = -1  ";
                    if (!empty($startDate)) {
                        $sql .= " AND `created` >= ? ";
                        $format .="s";
                        $values[]=$startDate;
                    }

                    if (!empty($endDate)) {
                        $sql .= " AND `created` <= ? ";
                        $format .="s";
                        $values[]=$endDate;
                    }
                    $res = sqlDAL::readSql($sql,$format,$values); 
                    $countRow = sqlDAL::num_rows($res);
                    sqlDAL::close($res);
                    $r['thumbsDown']+=$countRow;
                }
            }

            return $r;
        }

        static function deleteThumbs($filename) {
            if (empty($filename)) {
                return false;
            }
            global $global;
            $filePath = "{$global['systemRootPath']}videos/{$filename}";
            // Streamlined for less coding space.
            $files = glob("{$filePath}_thumbs*.jpg");
            foreach ($files as $file) {
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
        }

    }

}
// just to convert permalink into clean_title
if (!empty($_GET['v']) && empty($_GET['videoName'])) {
    $_GET['videoName'] = Video::get_clean_title($_GET['v']);
}
