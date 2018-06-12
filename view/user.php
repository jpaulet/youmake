<?php
require_once '../videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';

$tags = User::getTags(User::getId());
$tagsStr = "";
foreach ($tags as $value) {
    $tagsStr .= "<span class=\"label label-{$value->type} fix-width\">{$value->text}</span>";
}
$json_file = url_get_contents("{$global['webSiteRootURL']}plugin/CustomizeAdvanced/advancedCustom.json.php");
// convert the string to a json object
$advancedCustom = json_decode($json_file);
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title><?php echo $config->getWebSiteTitle(); ?> :: <?php echo __("User"); ?></title>
        <?php
        include $global['systemRootPath'] . 'view/include/head.php';
        ?>
        <link href="<?php echo $global['webSiteRootURL']; ?>js/Croppie/croppie.css" rel="stylesheet" type="text/css"/>
        <script src="<?php echo $global['webSiteRootURL']; ?>js/Croppie/croppie.min.js" type="text/javascript"></script>
        <?php
            require_once $global['systemRootPath'].'plugin/YouPHPTubePlugin.php'; 
            $theme = $config->getTheme();
            $cssFiles[] = "view/css/custom/{$theme}.css";
            $cssURL =  combineFiles($cssFiles, "css");
        ?>
        <link href="<?php echo $cssURL; ?>" rel="stylesheet" type="text/css"/>
    </head>

    <body>
        <?php include $global['systemRootPath'] . 'view/include/navbar.php'; ?>

        <div class="container-fluid">
            <?php
            if (User::isLogged()) {
                $user = new User("");
                $user->loadSelfUser();
                ?>
                <div class="row">
                    <div class=' class="col-lg-8 col-lg-offset-2 col-sm-10 col-sm-offset-1 col-xs-12"'>
                        <div class="panel-heading" style='font-weight:600;'><?php echo __("Update your user") ?></div>
                        <form class="form-compact well form-horizontal"  id="updateUserForm" onsubmit="" style='background-color:#fff;border-radius:8px;margin:0px;'>
                            <?php //echo $tagsStr; ?>
                            <fieldset>
                                <div class="form-group">
                                    <label class="col-md-2 control-label control-label-make"><?php echo __("Name"); ?></label>
                                    <div class="col-md-8 inputGroupContainer">
                                        <div class="input-group">
                                            <span class="input-group-addon input-group-addon-make"><i class="glyphicon glyphicon-pencil"></i></span>
                                            <input  id="inputName" placeholder="<?php echo __("Name"); ?>" class="form-control form-control-make"  type="text" value="<?php echo $user->getName(); ?>" required >
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label control-label-make"><?php echo __("User"); ?></label>
                                    <div class="col-md-8 inputGroupContainer">
                                        <div class="input-group">
                                            <span class="input-group-addon input-group-addon-make"><i class="glyphicon glyphicon-user"></i></span>
                                            <input  id="inputUser" placeholder="<?php echo __("User"); ?>" class="form-control form-control-make"  type="text" value="<?php echo $user->getUser(); ?>" required >
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label control-label-make"><?php echo __("E-mail"); ?></label>
                                    <div class="col-md-6 inputGroupContainer">
                                        <div class="input-group">
                                            <span class="input-group-addon input-group-addon-make"><i class="glyphicon glyphicon-envelope"></i></span>
                                            <input  id="inputEmail" placeholder="<?php echo __("E-mail"); ?>" class="form-control form-control-make"  type="email" value="<?php echo $user->getEmail(); ?>" required >
                                        </div>
                                    </div>                                    
                                    <div class="col-md-2">
                                        <?php
                                        if ($user->getEmailVerified()) {
                                            ?>
                                            <span class="btn btn-success"><i class="fa fa-check"></i> <?php echo __("E-mail Verified"); ?></span>
                                            <?php
                                        } else {
                                            ?>
                                            <button class="btn btn-warning" id="verifyEmail"><i class="fa fa-envelope"></i> <?php echo __("Verify e-mail"); ?></button>

                                            <script>
                                                $(document).ready(function () {

                                                    $('#verifyEmail').click(function (e) {
                                                        e.preventDefault();
                                                        modal.showPleaseWait();
                                                        $.ajax({
                                                            type: "POST",
                                                            url: "<?php echo $global['webSiteRootURL'] ?>objects/userVerifyEmail.php?users_id=<?php echo $user->getBdId(); ?>"
                                                        }).done(function (response) {
                                                            if(response.error){
                                                                swal("<?php echo __("Sorry!"); ?>", response.msg, "error");
                                                            }else{
                                                                swal("<?php echo __("Congratulations!"); ?>", "<?php echo __("Verification Sent"); ?>", "success");
                                                            }
                                                            modal.hidePleaseWait();
                                                        });
                                                    });

                                                });
                                            </script>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label control-label-make"><?php echo __("New Password"); ?></label>
                                    <div class="col-md-8 inputGroupContainer">
                                        <div class="input-group">
                                            <span class="input-group-addon input-group-addon-make"><i class="glyphicon glyphicon-lock"></i></span>
                                            <input  id="inputPassword" placeholder="<?php echo __("New Password"); ?>" class="form-control form-control-make"  type="password" value="" >
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label control-label-make"><?php echo __("Confirm New Password"); ?></label>
                                    <div class="col-md-8 inputGroupContainer">
                                        <div class="input-group">
                                            <span class="input-group-addon input-group-addon-make"><i class="glyphicon glyphicon-lock"></i></span>
                                            <input  id="inputPasswordConfirm" placeholder="<?php echo __("Confirm New Password"); ?>" class="form-control form-control-make"  type="password" value="" >
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label control-label-make"><?php echo __("Channel Name"); ?></label>
                                    <div class="col-md-8 inputGroupContainer">
                                        <div class="input-group">
                                            <span class="input-group-addon input-group-addon-make"><i class="fab fa-youtube"></i></span>
                                            <input  id="channelName" placeholder="<?php echo __("Channel Name"); ?>" class="form-control form-control-make"  type="text" value="<?php echo $user->getChannelName(); ?>" >
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label control-label-make"><?php echo __("About"); ?></label>
                                    <div class="col-md-8 inputGroupContainer">
                                        <textarea id="textAbout" placeholder="<?php echo __("About"); ?>" class="form-control form-control-make"  ><?php echo $user->getAbout(); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label control-label-make"><?php echo __("Avatar"); ?></label>
                                    <div class="col-md-8 ">
                                        <div id="croppie"></div>
                                        <center>
                                            <a id="upload-btn" class="btn btn-primary"><i class="fa fa-upload"></i> <?php echo __("Upload a Photo"); ?></a>
                                        </center>
                                    </div>
                                    <input type="file" id="upload" value="Choose a file" accept="image/*" style="display: none;" />
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <div id="croppieBg"></div>
                                        <center>
                                            <a id="upload-btnBg" class="btn btn-success"><i class="fa fa-upload"></i> <?php echo __("Upload a Background"); ?></a>
                                        </center>
                                    </div>
                                    <input type="file" id="uploadBg" value="Choose a file" accept="image/*" style="display: none;" />
                                </div>


                                <!-- Button -->
                                <div class="form-group">
                                    <hr>
                                    <div class="col-md-12">
                                        <center>
                                            <button type="submit" class="btn btn-primary btn-lg" ><?php echo __("Save"); ?> <span class="fa fa-save"></span></button>
                                        </center>
                                    </div>
                                </div>
                            </fieldset>
                        </form>

                    </div>
                </div>
                <script>
                    var uploadCrop;
                    function readFile(input, crop) {
                        console.log(input);
                        console.log($(input)[0]);
                        console.log($(input)[0].files);
                        if ($(input)[0].files && $(input)[0].files[0]) {
                            var reader = new FileReader();

                            reader.onload = function (e) {
                                crop.croppie('bind', {
                                    url: e.target.result
                                }).then(function () {
                                    console.log('jQuery bind complete');
                                });

                            }

                            reader.readAsDataURL($(input)[0].files[0]);
                        } else {
                            swal("Sorry - you're browser doesn't support the FileReader API");
                        }
                    }
                    $(document).ready(function () {
                        $('#upload').on('change', function () {
                            readFile(this, uploadCrop);
                        });
                        $('#upload-btn').on('click', function (ev) {
                            $('#upload').trigger("click");
                        });
                        $('#uploadBg').on('change', function () {
                            readFile(this, uploadCropBg);
                        });
                        $('#upload-btnBg').on('click', function (ev) {
                            $('#uploadBg').trigger("click");
                        });

                        uploadCrop = $('#croppie').croppie({
                            url: '<?php echo $user->getPhoto(); ?>',
                            enableExif: true,
                            enforceBoundary: false,
                            mouseWheelZoom: false,
                            viewport: {
                                width: 150,
                                height: 150
                            },
                            boundary: {
                                width: 300,
                                height: 300
                            }
                        });
                        setTimeout(function () {
                            uploadCrop.croppie('setZoom', 1);
                        }, 1000);

                        uploadCropBg = $('#croppieBg').croppie({
                            url: '<?php echo $user->getBackgroundURL(); ?>',
                            enableExif: true,
                            enforceBoundary: false,
                            mouseWheelZoom: false,
                            viewport: {
                                width: 750,
                                height: 250
                            },
                            boundary: {
                                width: 750,
                                height: 300
                            }
                        });
                        setTimeout(function () {
                            uploadCropBg.croppie('setZoom', 1);
                        }, 1000);
                        $('#updateUserForm').submit(function (evt) {
                            evt.preventDefault();
                            modal.showPleaseWait();
                            var pass1 = $('#inputPassword').val();
                            var pass2 = $('#inputPasswordConfirm').val();
                            // password dont match
                            if (pass1 != '' && pass1 != pass2) {
                                modal.hidePleaseWait();
                                swal("<?php echo __("Sorry!"); ?>", "<?php echo __("Your password does not match!"); ?>", "error");
                                return false;
                            } else {
                                $.ajax({
                                    url: 'updateUser',
                                    data: {
                                        "user": $('#inputUser').val(),
                                        "pass": $('#inputPassword').val(),
                                        "email": $('#inputEmail').val(),
                                        "name": $('#inputName').val(),
                                        "about": $('#textAbout').val(),
                                        "channelName": $('#channelName').val()
                                    },
                                    type: 'post',
                                    success: function (response) {
                                        if (response.status > "0") {
                                            uploadCrop.croppie('result', {
                                                type: 'canvas',
                                                size: 'viewport'
                                            }).then(function (resp) {
                                                $.ajax({
                                                    type: "POST",
                                                    url: "savePhoto",
                                                    data: {
                                                        imgBase64: resp
                                                    }
                                                }).done(function (o) {
                                                    uploadCropBg.croppie('result', {
                                                        type: 'canvas',
                                                        size: 'viewport'
                                                    }).then(function (resp) {
                                                        $.ajax({
                                                            type: "POST",
                                                            url: "saveBackground",
                                                            data: {
                                                                imgBase64: resp
                                                            }
                                                        }).done(function (o) {
                                                            modal.hidePleaseWait();
                                                        });
                                                    });
                                                });
                                            });
                                        } else if (response.error) {
                                            swal("<?php echo __("Sorry!"); ?>", response.error, "error");
                                            modal.hidePleaseWait();
                                        } else {
                                            swal("<?php echo __("Sorry!"); ?>", "<?php echo __("Your user has NOT been updated!"); ?>", "error");
                                            modal.hidePleaseWait();
                                        }
                                    }
                                });
                                return false;
                            }
                        });
                    });
                </script>
                <?php
            } else {
                ?>
                <div class="row">
                    <div class="hidden-xs col-sm-2 col-md-3 col-lg-4"></div>
                    <div class="col-xs-12 col-sm-8  col-md-6 col-lg-4 list-group-item ">
                        <fieldset>
                            <legend><?php echo __("Please sign in"); ?></legend>


                            <?php
                            if (empty($advancedCustom->disableNativeSignIn)) {
                                ?>
                                <form class="form-compact well form-horizontal"  id="loginForm">

                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php echo __("User"); ?></label>
                                        <div class="col-md-8 inputGroupContainer">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                                <input  id="inputUser" placeholder="<?php echo __("User"); ?>" class="form-control"  type="text" value="" required >
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php echo __("Password"); ?></label>
                                        <div class="col-md-8 inputGroupContainer">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                                <input  id="inputPassword" placeholder="<?php echo __("Password"); ?>" class="form-control"  type="password" value="" >
                                            </div>
                                            <?php
                                            if (empty($advancedCustom->disableNativeSignUp)) {
                                                ?>
                                                <small><a href="#" id="forgotPassword"><?php echo __("I forgot my password"); ?></a></small>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <!-- Button -->
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-success  btn-block" id="mainButton" ><span class="fas fa-sign-in-alt"></span> <?php echo __("Sign in"); ?></button>
                                        </div>
                                    </div>

                                </form>
                                <?php
                                if (empty($advancedCustom->disableNativeSignUp)) {
                                    ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <a href="signUp" class="btn btn-primary btn-block"  id="facebookButton"><span class="fa fa-user-plus"></span> <?php echo __("Sign up"); ?></a>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <hr>
                            <div class="row">
                                <?php
                                $login = YouPHPTubePlugin::getLogin();
                                foreach ($login as $value) {
                                    if (is_string($value) && file_exists($value)) { // it is a include path for a form
                                        include $value;
                                    } else if (is_array($value)) {
                                        ?>
                                        <div class="col-md-6">
                                            <a href="login?type=<?php echo $value['parameters']->type; ?>" class="<?php echo $value['parameters']->class; ?>" ><span class="<?php echo $value['parameters']->icon; ?>"></span> <?php echo $value['parameters']->type; ?></a>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <hr>
                        </fieldset>

                    </div>
                    <div class="hidden-xs col-sm-2 col-md-3 col-lg-4"></div>
                </div>
                <script>
                    $(document).ready(function () {
    <?php
    if (!empty($_GET['error'])) {
        ?>
                            swal("<?php echo __("Sorry!"); ?>", "<?php echo addslashes($_GET['error']); ?>", "error");
        <?php
    }
    $refererUrl = $_SERVER["HTTP_REFERER"];
    if (strpos($_SERVER["HTTP_REFERER"], "?error=" . __("You%20can%20not%20manage")) != false) {
        $refererUrl = substr($_SERVER["HTTP_REFERER"], 0, strpos($_SERVER["HTTP_REFERER"], "?"));
    }
    ?>
                        $('#loginForm').submit(function (evt) {
                            evt.preventDefault();
                            modal.showPleaseWait();
                            $.ajax({
                                url: 'login',
                                data: {"user": $('#inputUser').val(), "pass": $('#inputPassword').val()},
                                type: 'post',
                                success: function (response) {
                                    if (!response.isLogged) {
                                        modal.hidePleaseWait();
                                        if(response.error){
                                            swal("<?php echo __("Sorry!"); ?>", response.error, "error");
                                        }else{
                                            swal("<?php echo __("Sorry!"); ?>", "<?php echo __("Your user or password is wrong!"); ?>", "error");
                                        }
                                    } else {
                                        document.location = '<?php echo $global['webSiteRootURL']; ?>'
                                    }
                                }
                            });
                        });
                        $('#forgotPassword').click(function () {
                            var user = $('#inputUser').val();
                            if (!user) {
                                swal("<?php echo __("Sorry!"); ?>", "<?php echo __("You need to inform what is your user!"); ?>", "error");
                                return false;
                            }
                            var capcha = '<span class="input-group-addon"><img src="<?php echo $global['webSiteRootURL']; ?>captcha" id="captcha"></span><span class="input-group-addon"><span class="btn btn-xs btn-success" id="btnReloadCapcha"><span class="glyphicon glyphicon-refresh"></span></span></span><input name="captcha" placeholder="<?php echo __("Type the code"); ?>" class="form-control" type="text" style="height: 60px;" maxlength="5" id="captchaText">';
                            swal({
                                title: user + ", <?php echo __("Are you sure?"); ?>",
                                text: "<?php echo __("We will send you a link, to your e-mail, to recover your password!"); ?>" + capcha,
                                type: "warning",
                                html: true,
                                showCancelButton: true,
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "Yes, send it!",
                                closeOnConfirm: false
                            },
                                    function () {
                                        modal.showPleaseWait();
                                        $.ajax({
                                            url: 'recoverPass',
                                            data: {"user": $('#inputUser').val(), "captcha": $('#captchaText').val()},
                                            type: 'post',
                                            success: function (response) {
                                                if (response.error) {
                                                    swal("<?php echo __("Error"); ?>", response.error, "error");
                                                } else {
                                                    swal("<?php echo __("E-mail sent"); ?>", "<?php echo __("We sent you an e-mail with instructions"); ?>", "success");
                                                }
                                                modal.hidePleaseWait();
                                            }
                                        });

                                    });

                            $('#btnReloadCapcha').click(function () {
                                $('#captcha').attr('src', '<?php echo $global['webSiteRootURL']; ?>captcha?' + Math.random());
                                $('#captchaText').val('');
                            });
                        });
                    }
                    );

                </script>
                <?php
            }
            ?>

        </div><!--/.container-->

        <?php
        include $global['systemRootPath'] . 'view/include/footer.php';
        ?>

    </body>
</html>
