<?php

session_start();

if(!isset($_SESSION["username"])){
    echo "<script>document.location='../login.php'; </script>";
}

?>

<!DOCTYPE html>
<html>

<head>
    <?php
        require "../assets/styles_assets.php";
    ?>
    <link href="../css/plugins/dataTables/datatables.min.css" rel="stylesheet">

    <title>INVENTORY MS | Reference Tables - RCC</title>

</head>
<body style="color: black;">
    <div id="wrapper">
    <?php include("../assets/navbar.php"); ?>
        <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
            <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                <ul class="nav navbar-top-links navbar-left">
                    <li style="padding: 20px;"><?php echo $_SESSION["link2"]; ?> | Responsibility Center Codes
                </li>
                </ul>
            </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <a href="../php/php_logout.php">
                            <i class="fa fa-sign-out"></i> Log out
                        </a>
                    </li>
                    <li>
                        <a class="right-sidebar-toggle">
                            <i class="fa fa-tasks"></i>
                        </a>
                    </li>
                </ul>

            </nav>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 animated bounceInDown">
                    <div class="row wrapper border-bottom white-bg page-heading">
                        <div class="col-lg-10">
                            <h2>Responsibility Center Codes</h2>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <button type="button" class="btn btn-default">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                </li>
                            </ol>
                        </div>
                        <div class="col-lg-2">

                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 animated bounceInDown">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3><i class="fa fa-code-fork"></i> Responsibility Center Codes</h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table id="tbl_rcc" class="table table-bordered table-hover dataTables-example" >
                                    <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Acronym</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer">
                <div>
                    <strong>Copyright</strong> <?php echo $_SESSION["company_title"]; ?> &copy; <?php echo date("Y"); ?>
                </div>
            </div>
        </div>

    </div>

<!--end of wrapper !-->

    <?php
        require "../assets/small_chat.php";
        require "../assets/scripts_assets.php";
    ?>

    <script src="../js/plugins/dataTables/datatables.min.js"></script>
    <script src="../js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>
    <script src="js/general_functions.js"></script>
    <script>
        $(document).ready(function(){
            loadData("SELECT code, acronym, description, status FROM ref_rcc", ["code","acronym","description","status"], "RCC", "tbl_rcc");
        });

    </script>
</body>
</html>