<footer class='row' style='display:none;min-height:1px; background-color: #9f7eff; background: -webkit-gradient(linear, left top, right bottom, from(#E550FF ), to(#4AB6FF )) fixed; margin-top:20px; border-top:0px;'>
    <?php
    $custom = "";
    if (YouPHPTubePlugin::isEnabled("c4fe1b83-8f5a-4d1b-b912-172c608bf9e3")) {
        require_once $global['systemRootPath'] . 'plugin/Customize/Objects/ExtraConfig.php';
        $ec = new ExtraConfig();
        $custom = $ec->getFooter();
    }
    if (!empty($custom)) {
        echo $custom;
    }
    ?>
</footer>
<script>
    window.onerror = function myErrorHandler(errorMsg, url, lineNumber) {
        if(url==""){
            url="embed in html";
        }
        $.ajax({
            url: webSiteRootURL+"objects/ajaxErrorCatcher.php?error="+encodeURI("JS-Err: "+errorMsg+" @ line "+lineNumber+" in file "+url+" at visit on <?php echo $_SERVER['REQUEST_URI']; ?>"),
            context: document.body
        }).done(function() {
            console.log("<?php echo 'A Javascript-error happend. Please tell your admin to clear the folder videos/cache. \r\n If this doesn\'t help, attach these infos to a github-pull-request:'; ?> \r\n Msg:" + errorMsg+" \r\n Url: "+url+ ", line: "+lineNumber+", Address: <?php echo $_SERVER['REQUEST_URI'] ?>");
        });
    return false;
    }
    
    // Just for testing
    // throw "A Bug"; 
    $(function () {
<?php
if (!empty($_GET['error'])) {
    ?>
            swal({title: "Sorry!", text: "<?php echo $_GET['error']; ?>", type: "error", html: true});
    <?php
}
?>
<?php
if (!empty($_GET['msg'])) {
    ?>
            swal({title: "Ops!", text: "<?php echo $_GET['msg']; ?>", type: "info", html: true});
    <?php
}
?>
    });
</script>
<!-- <script src="<?php echo $global['webSiteRootURL']; ?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script> -->
<?php
    $jsFiles = array();
    $jsFiles[] = "view/bootstrap/js/bootstrap.min.js";
    $jsFiles[] = "view/js/seetalert/sweetalert.min.js";
    $jsFiles[] = "view/js/bootpag/jquery.bootpag.min.js";
    $jsFiles[] = "view/js/bootgrid/jquery.bootgrid.js";
    $jsFiles[] = "view/bootstrap/bootstrapSelectPicker/js/bootstrap-select.min.js";
    $jsFiles[] = "view/js/script.js";
    $jsFiles[] = "view/js/bootstrap-toggle/bootstrap-toggle.min.js";
    $jsFiles[] = "view/js/js-cookie/js.cookie.js";
    $jsFiles[] = "view/css/flagstrap/js/jquery.flagstrap.min.js";
    $jsFiles[] = "view/js/jquery.lazy/jquery.lazy.min.js";
    $jsFiles[] = "view/js/jquery.lazy/jquery.lazy.plugins.min.js";
    if(!empty($_SESSION['type'])){
        
        $waveSurferEnabled = YouPHPTubePlugin::getObjectDataIfEnabled("CustomizeAdvanced");
        if($waveSurferEnabled==false){
           $waveSurferEnabled = true;
        } else {
            $waveSurferEnabled = $waveSurferEnabled->EnableWavesurfer;
        }
        if((($_SESSION['type']=="audio")||($_SESSION['type']=="linkAudio"))&&($waveSurferEnabled)){
            $jsFiles[] = "view/js/videojs-wavesurfer/wavesurfer.min.js";
            $jsFiles[] = "view/js/videojs-wavesurfer/dist/videojs.wavesurfer.min.js";
        }   
    }
    $jsFiles = array_merge($jsFiles,YouPHPTubePlugin::getJSFiles());
    $jsURL =  combineFiles($jsFiles, "js");

?>
<script src="<?php echo $jsURL; ?>" type="text/javascript"></script>
<?php
require_once $global['systemRootPath'] . 'plugin/YouPHPTubePlugin.php';
echo YouPHPTubePlugin::getFooterCode();
if(isset($_SESSION['savedQuerys'])){
    echo "<!-- Saved querys: ".$_SESSION['savedQuerys']." -->";
}
?>
