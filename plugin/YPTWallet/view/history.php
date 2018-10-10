<?php
require_once '../../../videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/functions.php';

if (!User::isLogged()) {
    header("Location: {$global['webSiteRootURL']}?error=" . __("You can not do this"));
    exit;
}

$users_id = 0;
if (!empty($_GET['users_id'])) {
    $users_id = $_GET['users_id'];
}else{
    $users_id = User::getId();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title>Support Author</title>
        <?php include $global['systemRootPath'] . 'view/include/head.php'; ?>
        <style>
            .disabled{ display:none !important; }
        </style>
    </head>

    <body>
        <?php
        include $global['systemRootPath'] . 'view/include/navbar.php';
        ?>
        <div class="container-fluid" style='min-width:620px;'>
            <div class="col-xs-12">
                <div class="panel-heading"  style="height: 70px;">
                    <img src="<?php echo User::getPhoto($users_id); ?>" class="img img-responsive img-circle pull-left" style="height: 50px; margin-right: 10px;">
                    <h1><?php echo User::getNameIdentificationById($users_id); ?></h1>
                </div>
                <div class="panel-body" style='min-width:580px;'>
                    <div class="row bgWhite list-group-item" style='box-shadow:none;'>
                        <table id="grid" class="table table-condensed table-hover table-striped">
                            <thead>
                                <tr>
                                    <th data-column-id="valueText"  data-width="100px"><?php echo __("Value"); ?></th>
                                    <th data-column-id="description" data-width="300px"><?php echo __("Description"); ?></th>
                                    <th data-column-id="status" data-formatter="status" ><?php echo __("Status"); ?></th>
                                    <th data-column-id="created" data-order="desc" data-width="150px"><?php echo __("Date"); ?></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php include $global['systemRootPath'] . 'view/include/footer.php'; ?>
        <script>
            $(document).ready(function () {

                var grid = $("#grid").bootgrid({
                    labels: {
                        noResults: "<?php echo __("No results found!"); ?>",
                        all: "<?php echo __("All"); ?>",
                        infos: "<?php echo __("Showing {{ctx.start}} to {{ctx.end}} of {{ctx.total}} entries"); ?>",
                        loading: "<?php echo __("Loading..."); ?>",
                        refresh: "<?php echo __("Refresh"); ?>",
                        search: "<?php echo __("Search"); ?>",
                    },
                    css: { 
                        header: 'bootgrid-header',
                        footer: 'bootgrid-footer',
                    },
                    ajax: true,
                    url: "<?php echo $global['webSiteRootURL']; ?>plugin/YPTWallet/view/log.json.php?users_id=<?php echo $users_id; ?>",
                    formatters: {
                        "status": function (column, row) {
                            var status = "";
                            if (row.type == "<?php echo YPTWallet::MANUAL_ADD; ?>" || row.type == "<?php echo YPTWallet::MANUAL_WITHDRAW; ?>") {
                                status = "<span class='label label-success'>Success</span>";
                                if (row.status == 'pending') {
                                    status = "<span class='label label-warning'>Pending</span>";
                                } else if (row.status == 'canceled') {
                                    status = "<span class='label label-danger'>Canceled</span>";
                                }
                                
                                <?php if (User::isAdmin()) { ?>
                                    status += "<br><br><div class=\"btn-group\"><button class='btn btn-default btn-xs command-status-success'>Success</button>";
                                    status += "<button class='btn btn-default btn-xs command-status-pending'>Pending</button>";
                                    status += "<button class='btn btn-default btn-xs command-status-canceled'>Canceled</button><div>";
                                <?php } ?>

                            }
                            return status;
                        }
                    }
                }).on("loaded.rs.jquery.bootgrid", function () {
                    
                    <?php if (User::isAdmin()) { ?>
                        /* Executes after data is loaded and rendered */
                        grid.find(".command-status-success").on("click", function (e) {
                            var row_index = $(this).closest('tr').index();
                            var row = $("#grid").bootgrid("getCurrentRows")[row_index];
                            setStatus("success", row.id);
                        });

                        grid.find(".command-status-pending").on("click", function (e) {
                            var row_index = $(this).closest('tr').index();
                            var row = $("#grid").bootgrid("getCurrentRows")[row_index];
                            setStatus("pending", row.id);
                        });

                        grid.find(".command-status-canceled").on("click", function (e) {
                            var row_index = $(this).closest('tr').index();
                            var row = $("#grid").bootgrid("getCurrentRows")[row_index];
                            setStatus("canceled", row.id);
                        });
                    <?php } ?>
                });
            });
            
            <?php if (User::isAdmin()) { ?>
                function setStatus(status, wallet_log_id) {
                    modal.showPleaseWait();
                    $.ajax({
                        url: '<?php echo $global['webSiteRootURL']; ?>plugin/YPTWallet/view/changeLogStatus.json.php',
                        type: "POST",
                        data: {
                            status: status,
                            wallet_log_id: wallet_log_id
                        },
                        success: function (response) {
                            $(".walletBalance").text(response.walletBalance);
                            modal.hidePleaseWait();
                            if (response.error) {
                                setTimeout(function () {
                                    swal("<?php echo __("Sorry!"); ?>", response.msg, "error");
                                }, 500);
                            } else {
                                $("#grid").bootgrid("reload");
                            }
                        }
                    });
                }
            <?php } ?>
        </script>
    </body>
</html>
