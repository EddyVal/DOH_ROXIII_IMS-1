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
        require "../assets/scripts_assets.php";
        require "../assets/styles_assets.php";
    ?>
    <link href="../css/plugins/dataTables/datatables.min.css" rel="stylesheet">

    <title>INVENTORY MS | Supply Records - Property Transfer Report</title>

</head>
<body style="color: black;">
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                                <center><img alt="image" class="rounded-circle" src="../../archives/img/<?php echo $_SESSION["company_logo"]; ?>" height="50" width="50"/>
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
                    <li class="active">
                        <a href=""><i class="fa fa-clipboard"></i> <span class="nav-label"><?php echo $_SESSION["link1"]; ?></span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="iar.php"><i class="fa fa-clipboard"></i> IAR</a></li>
                            <li><a href="ics.php"><i class="fa fa-clipboard"></i> ICS</a></li>
                            <li><a href="par.php"><i class="fa fa-clipboard"></i> PAR</a></li>
                            <li><a href="ris.php"><i class="fa fa-clipboard"></i> RIS</a></li>
                            <li class="active"><a href="ptr.php"><i class="fa fa-clipboard"></i> PTR</a></li>
                            <li><a href="supply_records/ptr.php"><i class="fa fa-clipboard"></i> GATE PASS</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href=""><i class="fa fa-table"></i> <span class="nav-label"><?php echo $_SESSION["link2"]; ?></span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="../reference_tables/area.php"><i class="fa fa-area-chart"></i> Area</a></li>
                            <li><a href="../reference_tables/rcc.php"><i class="fa fa-code-fork"></i> RCC</a></li>
                            <li><a href="../reference_tables/category.php"><i class="fa fa-tag"></i> Category</a></li>
                            <li><a href="../reference_tables/item.php"><i class="fa fa-object-group"></i> Item</a></li>
                            <li><a href="../reference_tables/unit.php"><i class="fa fa-balance-scale"></i> Unit</a></li>
                            <li><a href="../reference_tables/supplier.php"><i class="fa fa-users"></i> Supplier</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="../stockcard.php"><i class="fa fa-bars"></i> <span class="nav-label">Stock Card</span></a>
                    </li>
                    <li>
                        <a href="../sai.php"><i class="fa fa-search"></i> <span class="nav-label">For SAI</span></a>
                    </li>
                    <li>
                        <a href="../archives.php"><i class="fa fa-archive"></i> <span class="nav-label">Archive</span></a>
                    </li>
                    <li>
                        <a href="https://drive.google.com/file/d/1H6DzrxhfjMdY0wh9NiCpfuq5_hdbgc5H/view?usp=sharing" target="_blank"><i class="fa fa-book"></i> <span class="nav-label">User Manual</span></a>
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
                    <li style="padding: 20px;"><?php echo $_SESSION["link1"]; ?> | Property Transfer Report
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
                            <h2>Property Transfer Report</h2>
                            <?php
                            if($_SESSION["role"] == "SUPPLY" || $_SESSION["role"] == "SUPPLY_SU"){ ?>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <button type="button" class="btn btn-default dim" id="btn_add_ptr" data-toggle="modal" data-target="#add_ptr">
                                            <i class="fa fa-plus"></i> Add PTR
                                        </button>
                                        <button type="button" class="btn btn-default dim" data-toggle="modal" data-target="#ptr_alloc">
                                            <i class="fa fa-upload"></i> Upload Allocation List
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
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3><i class="fa fa-clipboard"></i> Property Transfer Report</h3>
                        </div>
                        <div class="panel-body">
                            <div class="pull-right">
                                Search: <input type="text" name="search_box" id="search_box"/>
                            </div>
                            <div class="table-responsive" id="dynamic_content">
                                
                            </div>
                        </div>
                    </div>
                    <?php require "reports/report_ptr_gen.php"; ?>
                </div>
            </div>
            <div class="footer">
                <div>
                    <strong>Copyright</strong> <?php echo $_SESSION["company_title"]; ?> &copy; <?php echo date("Y"); ?>
                </div>
            </div>
        </div>

        <?php
            require "../modals/modal_add_ptr.php";
            require "../modals/modal_edit_ptr.php";
            require "../modals/modal_view_iss.php";
            require "../modals/modal_edit_dr.php";
            require "modals/modal_ptr_alloc.php";
            require "../modals/modal_add_item_issuances.php";
            require "modals/modal_ptr_preview.php";
        ?>

    </div>

    <!--end of wrapper !-->

    <?php
        require "../assets/small_chat.php";
    ?>
    <script src="../js/plugins/dataTables/datatables.min.js"></script>
    <script src="../js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

    <script src="../js/plugins/bs-custom-file/bs-custom-file-input.min.js"></script>
    <script src="js/js_general_functions.js"></script>

    <script src="js/js_ptr.js"></script>
    <script type="text/javascript">
        set_url("php/php_ptr.php");
    </script>
</body>
</html>