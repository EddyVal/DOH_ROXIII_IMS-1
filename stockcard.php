<?php

session_start();

if(!isset($_SESSION["username"])){
    echo "<script>document.location='login.php'; </script>";
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- Toastr style -->
    <link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">

    <!-- Gritter -->
    <link href="js/plugins/gritter/jquery.gritter.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="sweetalert-master/dist/sweetalert.css">

    <link rel="shortcut icon" type="image/x-icon" href="../archives/img/<?php echo $_SESSION["company_logo"]; ?>">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="css/info_box.css">

    <link href="css/plugins/select2/select2.min.css" rel="stylesheet">
    <link href="css/plugins/select2/select2-bootstrap4.min.css" rel="stylesheet">

    <title>INVENTORY MS | Stock Card</title>

</head>
<body>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                                <center><img alt="image" class="rounded-circle" src="../archives/img/<?php echo $_SESSION["company_logo"]; ?>" height="50" width="50"/>
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                    <span class="block m-t-xs font-bold"><?php echo $_SESSION["username"]; ?></span>
                                    <span class="text-muted text-xs block"><?php echo $_SESSION["role"]; ?><b class="caret"></b></span>
                                </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a class="dropdown-item" href="">Profile</a></li>
                                <li><a class="dropdown-item" href="">Contacts</a></li>
                                <li><a class="dropdown-item" href="">Mailbox</a></li>
                                <li class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="php/php_logout.php">Logout</a></li>
                            </ul>
                            </center>
                        </div>
                        <div class="logo-element">
                            IMS+
                        </div>
                    </li>
                    <li>
                        <a href="index.php"><i class="fa fa-th-large"></i> <span class="nav-label"><?php echo $_SESSION["link0"]; ?></span></a>
                    </li>
                    <li>
                        <a href="po.php"><i class="fa fa-list-alt"></i> <span class="nav-label">Purchase Orders</span></a>
                    </li>
                    <li>
                        <a href=""><i class="fa fa-clipboard"></i> <span class="nav-label"><?php echo $_SESSION["link1"]; ?></span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="supply_records/iar.php"><i class="fa fa-clipboard"></i> IAR</a></li>
                            <li><a href="supply_records/ics.php"><i class="fa fa-clipboard"></i> ICS</a></li>
                            <li><a href="supply_records/par.php"><i class="fa fa-clipboard"></i> PAR</a></li>
                            <li><a href="supply_records/ris.php"><i class="fa fa-clipboard"></i> RIS</a></li>
                            <li><a href="supply_records/ptr.php"><i class="fa fa-clipboard"></i> PTR</a></li>
                            <li><a href="supply_records/ptr.php"><i class="fa fa-clipboard"></i> GATE PASS</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href=""><i class="fa fa-table"></i> <span class="nav-label"><?php echo $_SESSION["link2"]; ?></span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="reference_tables/area.php"><i class="fa fa-area-chart"></i> Area</a></li>
                            <li><a href="reference_tables/rcc.php"><i class="fa fa-code-fork"></i> RCC</a></li>
                            <li><a href="reference_tables/category.php"><i class="fa fa-tag"></i> Category</a></li>
                            <li><a href="reference_tables/item.php"><i class="fa fa-object-group"></i> Item</a></li>
                            <li><a href="reference_tables/unit.php"><i class="fa fa-balance-scale"></i> Unit</a></li>
                            <li><a href="reference_tables/supplier.php"><i class="fa fa-users"></i> Supplier</a></li>
                        </ul>
                    </li>
                    <li class="active">
                        <a href="stockcard.php"><i class="fa fa-bars"></i> <span class="nav-label">Stock Card</span></a>
                    </li>
                    <li>
                        <a href="sai.php"><i class="fa fa-search"></i> <span class="nav-label">For SAI</span></a>
                    </li>
                    <li>
                        <a href="archives.php"><i class="fa fa-archive"></i> <span class="nav-label">Archive</span></a>
                    </li>
                    <li>
                        <a href="https://drive.google.com/file/d/1H6DzrxhfjMdY0wh9NiCpfuq5_hdbgc5H/view?usp=sharing" target="_blank"><i class="fa fa-book"></i> <span class="nav-label">User Manual</span></a>
                    </li>
                    <li>
                        <a href="php/php_logout.php"><i class="fa fa-power-off"></i> <span class="nav-label">Logout</span></a>
                    </li>
                </ul>

            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg dashbard-1" style="color: black;">
        <div class="row border-bottom">
        <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            <ul class="nav navbar-top-links navbar-left">
                <li style="padding: 20px;">Stock Card
            </li>
            </ul>
        </div>
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <button class="btn btn-xs btn-default dim" onclick="$('#print_idr').modal();"><i class="fa fa-print"></i> Incoming Delivery Report</button>
                </li>&nbsp;&nbsp;
                <li>
                    <button class="btn btn-xs btn-default dim" onclick="$('#print_ppe').modal();"><i class="fa fa-print"></i> PPE (ICT/Various Supplies)</button>
                </li>&nbsp;&nbsp;
                <li>
                    <button class="btn btn-xs btn-default dim" onclick="$('#print_rsmi').modal();"><i class="fa fa-print"></i> RSMI (RIS-Consumables)</button>
                </li>&nbsp;&nbsp;
                <li>
                    <button class="btn btn-xs btn-default dim" onclick="generate_wi();"><i class="fa fa-print"></i> Warehouse Inventory</button>
                </li>&nbsp;&nbsp;
                <li>
                    <button class="btn btn-xs btn-default dim" onclick="get_rpci();"><i class="fa fa-print"></i> RPCI (All Categories)</button>
                </li>&nbsp;&nbsp;
                <li>
                    <a href="php/php_logout.php">
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
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-12">
                <br>
                <div class="row">
                    <div class="col-lg-5">
                        <!---
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label"><b>Select a category:</b></label>
                            <div class="col-lg-8">
                                <select id="category" class="select2_demo_1 form-control">
                                    <option value="" disabled selected></option>
                                </select>
                            </div>
                        </div>
                        !-->
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
                        <input type="text" id="searchkw" class="pull-right" placeholder="Search...">
                    </div>
                    <div class="ibox-content" style="height: 450px;overflow:auto;">
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
        <div class="small-chat-box fadeInRight animated">

            <div class="heading" draggable="true">
                <small class="chat-date float-right">
                    02.19.2015
                </small>
                Small chat
            </div>
            <div class="content">
            </div>
            <div class="form-chat">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control">
                    <span class="input-group-btn"> <button class="btn btn-primary dim" type="button">Send
                </button> </span></div>
            </div>

        </div>
        <div id="small-chat">

            <?php //<span class="badge badge-warning float-right"></span> ?>
            <a class="open-small-chat" href="">
                <i class="fa fa-comments"></i>

            </a>
        </div>
        <div id="right-sidebar" class="animated">
            <div class="sidebar-container">
                <ul class="nav nav-tabs navs-3">
                    <li>
                        <a class="nav-link active" data-toggle="tab" href="#tab-1"> Notes </a>
                    </li>
                    <li>
                        <a class="nav-link" data-toggle="tab" href="#tab-2"> Projects </a>
                    </li>
                    <li>
                        <a class="nav-link" data-toggle="tab" href="#tab-3"> <i class="fa fa-gear"></i> </a>
                    </li>
                </ul>
                <div class="tab-content">
                </div>
            </div>
        </div>
    </div>

    
    <!-- Mainly scripts -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Flot -->
    <script src="js/plugins/flot/jquery.flot.js"></script>
    <script src="js/plugins/flot/jquery.flot.tooltip.min.js"></script>
    <script src="js/plugins/flot/jquery.flot.spline.js"></script>
    <script src="js/plugins/flot/jquery.flot.resize.js"></script>
    <script src="js/plugins/flot/jquery.flot.pie.js"></script>

    <!-- Peity -->
    <script src="js/plugins/peity/jquery.peity.min.js"></script>
    <script src="js/demo/peity-demo.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>

    <!-- jQuery UI -->
    <script src="js/plugins/jquery-ui/jquery-ui.min.js"></script>

    <!-- GITTER -->
    <script src="js/plugins/gritter/jquery.gritter.min.js"></script>

    <!-- Sparkline -->
    <script src="js/plugins/sparkline/jquery.sparkline.min.js"></script>

    <!-- Sparkline demo data  -->
    <script src="js/demo/sparkline-demo.js"></script>

    <!-- Toastr -->
    <script src="js/plugins/toastr/toastr.min.js"></script>

    <script src="sweetalert-master/sweetalert.min.js"></script>
    <script src="js/plugins/select2/select2.full.min.js"></script>
    
    <script src="js/js_sc.js"></script>
    <script type="text/javascript">
    </script>
</body>
</html>