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

    <link rel="shortcut icon" type="image/x-icon" href="../archives/img/<?php echo $_SESSION["company_logo"]; ?>">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <link href="css/plugins/dataTables/datatables.min.css" rel="stylesheet">

    <!-- c3 Charts -->
    <link href="css/plugins/c3/c3.min.css" rel="stylesheet">

    <title>INVENTORY MS | Dashboard</title>

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
                    <li class="active">
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
                    <li>
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
                <li style="padding: 20px;"><?php echo $_SESSION["link0"]; ?></li>
            </ul>
        </div>
            <ul class="nav navbar-top-links navbar-right">
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
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content">
                   <div class="row">
                        <div class="col-lg-3 animated bounceIn">
                            <div class="ibox" onclick="load_list('PO');">
                                <div class="ibox-title">
                                    <span class="label label-info float-right">PO</span>
                                    <h5><i class="fa fa-list-alt"></i> Purchase Orders</h5>
                                </div>
                                <div class="ibox-content">
                                    <h1 class="no-margins" id="po_num"><i class="fa fa-spinner fa-spin"></i></h1>
                                    <small>Total Purchase Orders</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 animated bounceIn">
                            <div class="ibox" onclick="document.location = 'reference_tables/item.php';">
                                <div class="ibox-title">
                                    <span class="label label-primary float-right">IT</span>
                                    <h5><i class="fa fa-object-group"></i> Items</h5>
                                </div>
                                <div class="ibox-content">
                                    <h1 class="no-margins" id="it_num"><i class="fa fa-spinner fa-spin"></i></h1>
                                    <small>Total Number of Items Registered</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 animated bounceIn">
                            <div class="ibox" onclick="load_list('IS');">
                                <div class="ibox-title">
                                    <span class="label label-danger float-right">IS</span>
                                    <h5><i class="fa fa-shopping-cart"></i> Issuances</h5>
                                </div>
                                <div class="ibox-content">
                                    <h1 class="no-margins" id="is_num"><i class="fa fa-spinner fa-spin"></i></h1>
                                    <small>Total Number of Issuances</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 animated bounceIn">
                            <div class="ibox" onclick="load_list('UL');">
                                <div class="ibox-title">
                                    <span class="label label-warning float-right">UL</span>
                                    <h5><i class="fa fa-history"></i> User Logs</h5>
                                </div>
                                <div class="ibox-content">
                                    <h1 class="no-margins" id="ul_num"><i class="fa fa-spinner fa-spin"></i></h1>
                                    <small>Total Number of User Logs</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12" id="PO" style="display: none;">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4><i class="fa fa-list-alt"></i> Purchase Orders</h4>
                                </div>
                                <div class="panel-body">
                                    <table id="tbl_PO" class="table table-bordered table-hover dataTables-example">
                                        <thead>
                                            <tr>
                                                <th>PO Number</th>
                                                <th>Mode of Procurement</th>
                                                <th>End User</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12" id="IS" style="display: none;">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4><i class="fa fa-shopping-cart"></i> Issuances</h4>
                                </div>
                                <div class="panel-body">
                                    <center>
                                        <div id="pie" style="height: 450px; width: 100%;">
                                                    
                                        </div>
                                    </center>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12" id="UL" style="display: none;">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4><i class="fa fa-history"></i> User Logs</h4>
                                </div>
                                <div class="panel-body">
                                    <table id="tbl_UL" class="table table-bordered table-hover dataTables-example">
                                        <thead>
                                            <tr>
                                                <th class="first_col">Log ID</th>
                                                <th>Description</th>
                                                <th>Date</th>
                                                <th>Time</th>
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
            </div>
        </div>
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
                    <span class="input-group-btn"> <button
                        class="btn btn-primary" type="button">Send
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
<!--
                    <div id="tab-1" class="tab-pane active">

                    </div>

                    <div id="tab-2" class="tab-pane">

                    </div>

                    <div id="tab-3" class="tab-pane">

                    </div>
!-->
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
    <script src="js/plugins/dataTables/datatables.min.js"></script>
    <script src="js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

    <!-- CanvasJS -->
    <script src="canvasjs/canvasjs.min.js"></script>
    
    <script>
        $(document).ready(function() {
            var is_data = [];
            var is_num = "";
            get_data();
            setTimeout(function() {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };
                toastr.success('Inventory Management System', 'Welcome to <?php echo $_SESSION["entity_name"]; ?>');
            }, 1300);
        });

        function get_data(){
            $.ajax({
                url: "php/php_db.php",
                type: "POST",
                data: {call_func: "load_card"},
                dataType: "JSON",
                success: function(data){
                    $("#po_num").html(data["po_rows"].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    $("#it_num").html(data["it_rows"].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    $("#is_num").html(data["is_rows"].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    $("#ul_num").html(data["ul_rows"].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    is_data = data["is_data"];
                    is_num = data["is_rows"];
                    load_all();
                }
            });
        }

        function load_all(){
            $.ajax({
                url: "php/php_db.php",
                type: "POST",
                data: {call_func: "load_all"},
                dataType: "JSON",
                success: function(data){
                    $("table#tbl_PO tbody").html(data["po_data"]);
                    $("table#tbl_UL tbody").html(data["ul_data"]);
                    create_datatable();
                }
            });   
        }

        function load_list(name){
            $("#pie").html("");
            for(id of ["PO", "IT", "IS", "UL"]){
                if(id==name){
                    $("#"+id).slideDown("slow", ()=>{
                        if(name=="IS"){
                            draw_chart(is_data);
                        }
                    });
                }else{
                    $("#"+id).hide();
                }
            }
        }

        function create_datatable(){
            $('.dataTables-example').DataTable({
                pageLength: 10,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    { extend: 'copy'},
                    {extend: 'csv'},
                    {extend: 'excel', title: '_'},
                    {extend: 'pdf', title: '_'},

                    {extend: 'print',
                        customize: function (win){
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');
                            $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                        }
                    }
                ]
            });
            $(".first_col").click();
        }

        function draw_chart(data) {
            var tot = parseInt(is_num.toString());
            var chart = new CanvasJS.Chart("pie", {
                exportEnabled: true,
                animationEnabled: true,
                title:{
                    text: "Overall Issuances"
                },
                legend:{
                    cursor: "pointer",
                    itemclick: explodePie
                },
                data: [{
                    type: "pie",
                    showInLegend: true,
                    toolTipContent: "{name}: <strong>{y}%</strong>",
                    indexLabel: "{name} - {y}%",
                    dataPoints: [
                        { y: ((data[0] / tot) * 100).toFixed(2), name: "ICS ("+data[0]+")", exploded: true },
                        { y: ((data[1] / tot) * 100).toFixed(2), name: "PAR ("+data[1]+")" },
                        { y: ((data[2] / tot) * 100).toFixed(2), name: "PTR ("+data[2]+")" },
                        { y: ((data[3] / tot) * 100).toFixed(2), name: "RIS ("+data[3]+")" }
                    ]
                }]
            });
            chart.render();
        }

        function explodePie (e) {
            if(typeof (e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
                e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
            } else {
                e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
            }
            e.chart.render();
        }
    </script>
</body>
</html>