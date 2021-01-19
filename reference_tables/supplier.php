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

    <title>INVENTORY MS | Reference Tables - Supplier</title>

</head>
<body>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                                <center><img alt="image" class="rounded-circle" src="../imgsys/DOH-logo.png" height="50" width="50"/>
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                    <span class="block m-t-xs font-bold"><?php echo $_SESSION["username"]; ?></span>
                                    <span class="text-muted text-xs block"><?php echo $_SESSION["role"]; ?><b class="caret"></b></span>
                                </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a class="dropdown-item" href="">Profile</a></li>
                                <li><a class="dropdown-item" href="">Contacts</a></li>
                                <li><a class="dropdown-item" href="">Mailbox</a></li>
                                <li class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../php/php_logout.php">Logout</a></li>
                            </ul>
                            </center>
                        </div>
                        <div class="logo-element">
                            IMS+
                        </div>
                    </li>
                    <li>
                        <a href="../index.php"><i class="fa fa-th-large"></i> <span class="nav-label"><?php echo $_SESSION["link0"]; ?></span></a>
                    </li>
                    <li>
                        <a href="../po.php"><i class="fa fa-list-alt"></i> <span class="nav-label">Purchase Orders</span></a>
                    </li>
                    <li>
                        <a href=""><i class="fa fa-clipboard"></i> <span class="nav-label"><?php echo $_SESSION["link1"]; ?></span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="../supply_records/iar.php"><i class="fa fa-clipboard"></i> IAR</a></li>
                            <li><a href="../supply_records/ics.php"><i class="fa fa-clipboard"></i> ICS</a></li>
                            <li><a href="../supply_records/par.php"><i class="fa fa-clipboard"></i> PAR</a></li>
                            <li><a href="../supply_records/ris.php"><i class="fa fa-clipboard"></i> RIS</a></li>
                            <li><a href="../supply_records/ptr.php"><i class="fa fa-clipboard"></i> PTR</a></li>
                        </ul>
                    </li>
                    <li class="active">
                        <a href=""><i class="fa fa-table"></i> <span class="nav-label"><?php echo $_SESSION["link2"]; ?></span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="area.php"><i class="fa fa-area-chart"></i> Area</a></li>
                            <li><a href="rcc.php"><i class="fa fa-code-fork"></i> RCC</a></li>
                            <li><a href="category.php"><i class="fa fa-tag"></i> Category</a></li>
                            <li><a href="item.php"><i class="fa fa-object-group"></i> Item</a></li>
                            <li><a href="unit.php"><i class="fa fa-balance-scale"></i> Unit</a></li>
                            <li class="active"><a href="supplier.php"><i class="fa fa-users"></i> Supplier</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="../stockcard.php"><i class="fa fa-bars"></i> <span class="nav-label">Stock Card</span></a>
                    </li>
                    <li>
                        <a href="../archives.php"><i class="fa fa-archive"></i> <span class="nav-label">Archive</span></a>
                    </li>
                    <li>
                        <a href="../php/php_logout.php"><i class="fa fa-power-off"></i> <span class="nav-label">Logout</span></a>
                    </li>
                </ul>

            </div>
        </nav>
        <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
            <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                <ul class="nav navbar-top-links navbar-left">
                    <li style="padding: 20px;">
                    <span class="m-r-sm text-muted welcome-message"><?php echo $_SESSION["link2"]; ?> | Supplier</span>
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
                            <h2>Supplier</h2>
                            <?php
                            if($_SESSION["role"] == "SUPPLY"){ ?>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <button type="button" class="btn btn-primary" onclick="add_supplier();">
                                            <i class="fa  fa-plus"></i> Add
                                        </button>
                                    </li>
                                </ol>
                            <?php
                                }
                            ?>
                        </div>
                        <div class="col-lg-2">

                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 animated bounceInDown">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5><i class="fa fa-houzz"></i> Supplier</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="table-responsive">
                                <table id="tbl_supplier" class="table table-bordered table-hover dataTables-example" >
                                    <thead>
                                    <tr>
                                        <th>Supplier</th>
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
                    <strong>Copyright</strong> DOH-CHD-CARAGA &copy; <?php echo date("Y"); ?>
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
            loadData("SELECT supplier, status FROM ref_supplier", ["supplier", "status"], "Supplier", "tbl_supplier");
        });

    </script>
    
</body>
</html>