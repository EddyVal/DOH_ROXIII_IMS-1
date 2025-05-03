<?php

session_start();

if(!isset($_SESSION["username"])){
    echo "<script>document.location='login.php'; </script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php
        require "../assets/styles_assets.php";
    ?>
    <title>INVENTORY MS | Supply Records - Request for Inspection</title>
</head>
<body style="color: black;">
    <div id="wrapper">

        <?php include("../assets/navbar.php"); ?>
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <?php include("../assets/topmenu.php"); ?>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 animated bounceInDown">
                    <div class="row wrapper border-bottom white-bg page-heading">
                        <div class="col-lg-10">
                            <h2>Request for Inspection</h2>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <button type="button" class="btn btn-default dim" data-toggle="modal" data-target="#add_rfi">
                                        <i class="fa fa-plus"></i> Add RFI
                                    </button>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 animated bounceInDown">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3><i class="fa fa-clipboard"></i> Request for Inspection</h3>
                        </div>
                        <div class="panel-body">
                            <div class="pull-right">
                                <div class="input-group m-b">
                                        <div class="input-group-prepend">
                                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                        </div>
                                        <input id="search_box" name="seach_box" type="text" class="form-control" placeholder="Search...">
                                    </div>
                            </div>
                            <div class="table-responsive" id="dynamic_content">

                            </div>
                        </div>
                    </div>
                    <?php require "reports/report_rfi.php"; ?>
                </div>
            </div>

            <div class="footer">
                <div>
                    <strong>Copyright</strong> <?php echo $_SESSION["company_title"]; ?> &copy; <?php echo date("Y"); ?>
                </div>
            </div>
            <?php
                require "../assets/small_chat.php";
                require "../modals/modal_add_rfi.php";
                require "../modals/modal_edit_rfi.php";
            ?>
        </div>
    </div>
    <?php
        require "../assets/scripts_assets.php";
    ?>

    <!-- RFI script -->
    <script src="js/js_general_functions.js"></script>
    <script src="js/js_rfi.js"></script>
    <script type="text/javascript">
        set_url("php/php_rfi.php");
    </script>

</body>
</html>
