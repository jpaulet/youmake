<?php
require_once '../videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';
$email = "";
if (User::isLogged()) {
    $email = User::getEmail_();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title><?php echo $config->getWebSiteTitle(); ?> :: <?php echo __("Contact"); ?></title>
        <?php
        include $global['systemRootPath'] . 'view/include/head.php';
        ?>
    </head>

    <body>
        <?php
        include $global['systemRootPath'] . 'view/include/navbar.php';
        ?>

        <div class="container list-group-item">
            <div style="display: none;" id="messageSuccess">
                <div class="alert alert-success clear clearfix">
                    <div class="col-md-3">
                        <i class="fa fa-5x fa-check-circle-o"></i>
                    </div>
                    <div class="col-md-9">
                        <h1><?php echo __("Congratulations!"); ?></h1>
                        <h2><?php echo __("Your message has been sent!"); ?></h2>
                    </div>
                </div>
                <a class="btn btn-success btn-block" href="<?php echo $global['webSiteRootURL']; ?>"><?php echo __("Go back to the main page"); ?></a>
            </div>
            <form class="well form-horizontal" action=" " method="post"  id="contact_form">
                <fieldset>

                    <!-- Form Name -->
                    <legend><?php echo __("Contact Us Today!"); ?></legend>

                    <!-- Text input-->

                    <div class="form-group">
                        <label class="col-md-4 control-label"><?php echo __("Name"); ?></label>
                        <div class="col-md-4 inputGroupContainer">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                <input  name="first_name" placeholder="<?php echo __("Name"); ?>" class="form-control"  type="text" required="true">
                            </div>
                        </div>
                    </div>


                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-4 control-label"><?php echo __("E-mail"); ?></label>
                        <div class="col-md-4 inputGroupContainer">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                <input name="email" placeholder="<?php echo __("E-mail Address"); ?>" class="form-control" value="<?php echo $email; ?>"  type="email"  required="true">
                            </div>
                        </div>
                    </div>


                    <!-- Text input-->
                    <div class="form-group <?php echo empty($advancedCustom->doNotShowWebsiteOnContactForm) ? "" : "hidden" ?>">
                        <label class="col-md-4 control-label"><?php echo __("Website"); ?></label>
                        <div class="col-md-4 inputGroupContainer">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                                <input name="website" placeholder="<?php echo __("Website"); ?>" class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                    <!-- Text area -->

                    <div class="form-group">
                        <label class="col-md-4 control-label"><?php echo __("Message"); ?></label>
                        <div class="col-md-4 inputGroupContainer">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-pencil"></i></span>
                                <textarea class="form-control" name="comment" placeholder="<?php echo __("Message"); ?>"></textarea>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-md-4 control-label"><?php echo __("Type the code"); ?></label>
                        <div class="col-md-4 inputGroupContainer">
                            <div class="input-group">
                                <span class="input-group-addon"><img src="<?php echo $global['webSiteRootURL']; ?>captcha" id="captcha"></span>
                                <span class="input-group-addon"><span class="btn btn-xs btn-success" id="btnReloadCapcha"><span class="glyphicon glyphicon-refresh"></span></span></span>
                                <input name="captcha" placeholder="<?php echo __("Type the code"); ?>" class="form-control" type="text" style="height: 60px;" maxlength="5" id="captchaText">
                            </div>
                        </div>
                    </div>
                    <!-- Button -->
                    <div class="form-group">
                        <label class="col-md-4 control-label"></label>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary" ><?php echo __("Send"); ?> <span class="glyphicon glyphicon-send"></span></button>
                        </div>
                    </div>

                </fieldset>
            </form>
        </div>

    </div><!--/.container-->

    <?php
    include $global['systemRootPath'] . 'view/include/footer.php';
    ?>

    <script>
        $(document).ready(function () {

            $('#btnReloadCapcha').click(function () {
                $('#captcha').attr('src', '<?php echo $global['webSiteRootURL']; ?>captcha?' + Math.random());
                $('#captchaText').val('');
            });

            $('#contact_form').submit(function (evt) {
                evt.preventDefault();
                modal.showPleaseWait();
                $.ajax({
                    url: '<?php echo $global['webSiteRootURL']; ?>sendEmail',
                    data: $('#contact_form').serializeArray(),
                    type: 'post',
                    success: function (response) {
                        modal.hidePleaseWait();
                        if (!response.error) {
                            swal("<?php echo __("Congratulations!"); ?>", "<?php echo __("Your message has been sent!"); ?>", "success");
                            
                            $("#contact_form").hide();
                            $("#messageSuccess").fadeIn();
                        } else {
                            swal("<?php echo __("Your message could not be sent!"); ?>", response.error, "error");
                        }
                        $('#btnReloadCapcha').trigger('click');
                    }
                });
                return false;
            });

        });

    </script>
</body>
</html>
