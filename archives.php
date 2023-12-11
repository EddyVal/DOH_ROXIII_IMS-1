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
    <title>INVENTORY MS | Archives</title>
    <link rel="shortcut icon" type="image/x-icon" href="../archives/img/<?php echo $_SESSION["company_logo"]; ?>">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<style>
    div.gallery {
      border: 1px solid #ccc;
    }
    div.gallery:hover {
      border: 1px solid #777;
    }
    div.gallery img {
      width: 100%;
      height: auto;
    }
    div.desc {
      padding: 15px;
      text-align: center;
      color: black;
      border-top: 1px solid #ccc;
    }
    * {
      box-sizing: border-box;
    }
    .responsive {
      padding: 0 6px;
      float: left;
      width: 24.99999%;
    }
    @media only screen and (max-width: 700px) {
      .responsive {
        width: 49.99999%;
        margin: 6px 0;
      }
    }
    @media only screen and (max-width: 500px) {
      .responsive {
        width: 100%;
      }
    }
    .clearfix:after {
      content: "";
      display: table;
      clear: both;
    }
</style>

<body style="color: black;">

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
            <li>
                <a href="stockcard.php"><i class="fa fa-bars"></i> <span class="nav-label">Stock Card</span></a>
            </li>
            <li>
                <a href="sai.php"><i class="fa fa-search"></i> <span class="nav-label">For SAI</span></a>
            </li>
            <li class="active">
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

    <div id="page-wrapper" class="gray-bg">
    <div class="row border-bottom">
            <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                <ul class="nav navbar-top-links navbar-left">
                    <li style="padding: 20px;">Archives
                </li>
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
<div class="wrapper wrapper-content animated fadeInRight">
    <!--  
    <div class="responsive">
      <div class="gallery">
        <a target="_blank" href="imgsys/po_pdf.png">
          <img src="imgsys/po_pdf.png" alt="Cinque Terre" width="600" height="400">
        </a>
        <div class="desc">Add a description of the image here</div>
      </div>
    </div>
    <div class="responsive">
      <div class="gallery">
        <a target="_blank" href="imgsys/po_pdf.png">
          <img src="imgsys/po_pdf.png" alt="Cinque Terre" width="600" height="400">
        </a>
        <div class="desc">Add a description of the image here</div>
      </div>
    </div>
    <div class="responsive">
      <div class="gallery">
        <a target="_blank" href="imgsys/po_pdf.png">
          <img src="imgsys/po_pdf.png" alt="Cinque Terre" width="600" height="400">
        </a>
        <div class="desc">Add a description of the image here</div>
      </div>
    </div>
    !-->
    <embed id="img_po" src="../archives/" type="application/pdf" height="1000" width="100%"/>
</div>
<div class="footer">
    <div>
        <strong>Copyright</strong> <?php echo $_SESSION["company_title"]; ?> &copy; <?php echo date("Y"); ?>
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

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>

<script>

    $(document).ready(function () {

        // Add slimscroll to element
        $('.scroll_content').slimscroll({
            height: '200px'
        })
    });

</script>

</body>
</html>
