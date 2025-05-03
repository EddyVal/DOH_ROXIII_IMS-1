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
        require "assets/styles_assets.php";
    ?>

    <title>INVENTORY MS | Stock Card</title>

</head>
<body>
    <div id="wrapper">
        <?php include("assets/navbar.php"); ?>
        <div id="page-wrapper" class="gray-bg dashbard-1" style="color: black;">
            <div class="row border-bottom">
                <?php require "assets/topmenu.php"; ?> 
            </div>
        <br>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-12">
                <br>
                <div class="row">
                    <div class="col-lg-5">
                        
                    </div>
                    <div class="col-lg-7">
                        <button type="button" class="btn btn-default pull-right dim" data-toggle="modal" data-target="#gen_ics_par">
                            <i class="fa fa-print"></i> ICS/PAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-lg-4">
                <div class="ibox" style="border-style: solid; border-color: black; border-width: 1px;">
                    <div class="ibox-title">
                        <h5>Items&nbsp;&nbsp;<span id="num_items" class="label label-success" style="border-radius: 10px;">0</span></h5>
                        <div class="pull-right">
                            <div class="input-group m-b">
                                <div class="input-group-prepend">
                                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                </div>
                                <input id="searchkw" name="seach_box" type="text" class="form-control" placeholder="Search item or PO#...">
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content" style="height: 100vh;overflow:auto;">
                        <div class="dd" id="nestable">
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 animated bounceIn">
                <div class="ibox" style="border-style: solid; border-color: black; border-width: 1px;">
                    <div class="ibox-title">
                        <h5>Stockcard Preview&nbsp;&nbsp;&nbsp;<span id="loader" style="display: none;"><i class="fa fa-refresh fa-spin" style="color: black;"></i></span></h5>
                        <div class="pull-right">
                            <span><button class="btn btn-default btn-xs dim" onclick="load_sc_spec('1');" title="Show Released">✔️</button></span>
                            <span><button class="btn btn-default btn-xs dim" onclick="load_sc_spec('0');" title="Show Unreleased">❌</button></span>
                            <span><button class="btn btn-default btn-xs dim" onclick="print_sc();"><i class="fa fa-print"></i> Print</button></span>
                        </div>
                    </div>
                    <div class="ibox-content" style="color: black;">
                        Reference Number:
                        <select id="sc_refn" style="border-radius: 5px;">

                        </select>
                        <?php require "reports/report_sc.php"; ?>
                    </div>
                </div>
            </div>
            <?php require "modals/modal_ppe.php"; ?>
            <?php require "modals/modal_rsmi.php"; ?>
            <?php require "modals/modal_rpci.php"; ?>
            <?php require "modals/modal_gen_ics_par.php"; ?>
        </div>
        <br>
        <br>
            <div class="footer">
                <div>
                    <strong>Copyright</strong> <?php echo $_SESSION["company_title"]; ?> &copy; <?php echo date("Y"); ?>
                </div>
            </div>
        </div>
        <?php
            require "assets/small_chat.php";
        ?>
    </div>

    <?php
        require "assets/scripts_assets.php";
    ?>

    <script src="js/js_sc.js"></script>
    <script type="text/javascript">
    </script>
</body>
</html>