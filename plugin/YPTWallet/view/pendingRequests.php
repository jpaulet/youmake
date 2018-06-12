<?php
require_once '../../../videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/functions.php';

if (!User::isAdmin()) {
    header("Location: {$global['webSiteRootURL']}?error=" . __("You can not do this"));
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title>Support Author</title>
        <?php
        include $global['systemRootPath'] . 'view/include/head.php';
        ?>
    </head>

    <body>
        <?php
        include $global['systemRootPath'] . 'view/include/navbar.php';
        ?>
        <div class="container-fluid">
            <div class="col-lg-8 col-lg-offset-2 col-sm-10 col-sm-offset-1 col-xs-12">
                <div class="panel-heading">
                    <?php
                    echo __("Pending Requests");
                    ?>
                </div>
                <div class="panel-body" style='background-color:#fff;border-radius:8px;'>
                    <div class="row list-group-item">
                        <table id="grid" class="table table-condensed table-hover table-striped">
                            <thead>
                                <tr>
                                    <th data-column-id="user"  data-width="150px"><?php echo __("User"); ?></th>
                                    <th data-column-id="valueText"  data-width="150px"><?php echo __("Value"); ?></th>
                                    <th data-column-id="description" ><?php echo __("Description"); ?></th>
                                    <th data-column-id="status" data-formatter="status"  data-width="150px"><?php echo __("Status"); ?></th>
                                    <th data-column-id="created" data-order="desc" data-width="150px"><?php echo __("Date"); ?></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php
        include $global['systemRootPath'] . 'view/include/footer.php';
        ?>
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
                    ajax: true,
                    url: "<?php echo $global['webSiteRootURL']; ?>plugin/YPTWallet/view/pendingRequests.json.php",
                    formatters: {
                        "status": function (column, row) {
                            var status = "";
                            status = "<div class=\"btn-group\"><button class='btn btn-success btn-xs command-status-success'>Confirm</button>";
                            status += "<button class='btn btn-danger btn-xs command-status-canceled'>Cancel</button><div>";
                            return status;
                        }
                    }
                }).on("loaded.rs.jquery.bootgrid", function () {

                    /* Executes after data is loaded and rendered */
                    grid.find(".command-status-success").on("click", function (e) {
                        var row_index = $(this).closest('tr').index();
                        var row = $("#grid").bootgrid("getCurrentRows")[row_index];
                        setStatus("success", row.id);
                    });

                    grid.find(".command-status-canceled").on("click", function (e) {
                        var row_index = $(this).closest('tr').index();
                        var row = $("#grid").bootgrid("getCurrentRows")[row_index];
                        setStatus("canceled", row.id);
                    });

                });
            });
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

        </script>
    </body>
</html>
