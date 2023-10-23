<?php
    session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <?php
        require "../assets/styles_assets.php";
    ?>
    
    <title>INVENTORY MS | CONFIGURATION</title>

</head>
<body>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                                <center><img alt="image" class="rounded-circle" src="../../archives/img/<?php echo $_SESSION["company_logo"]; ?>" height="50" width="50"/>
                                <a href="index.php">
                                    <span class="block m-t-xs font-bold">The Admin</span>
                                    <span class="text-muted text-xs block">User <b class="caret"></b></span>
                                </a>
                            </center>
                        </div>
                        <div class="logo-element">
                            IMS+
                        </div>
                    </li>
                    <li><a href="../login.php"><i class="fa fa-sign-in"></i> <span class="nav-label">Login Page</span></a></li>
                </ul>
            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg dashbard-1" style="color: black;">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                        <ul class="nav navbar-top-links navbar-left">
                            <li style="padding: 20px;">IMS | Configuration</span>
                        </li>
                        </ul>
                    </div>
                </nav>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12">
                    <div class="row wrapper border-bottom white-bg page-heading">
                        <div class="col-lg-10">
                            <h2><i class="fa fa-cog"></i> System Settings (Configure and Customize)</h2>
                        </div>
                        <div class="col-lg-2">

                        </div>
                    </div> 
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-8">
                    <div class="ibox" style="border-style: solid; border-color: black; border-width: 1px;">
                        <div class="ibox-title">
                            <h5><i class="fa fa-building-o"></i> Organization Details</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Company Title:</label>
                                <div class="col-lg-7">
                                    <input id="company_title" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Supporting Title:</label>
                                <div class="col-lg-7">
                                    <input id="supporting_title" type="text" class="form-control">
                                </div>
                                <label class="col-lg-2 col-form-label" style="color: red;">**Optional**</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Entity Name:</label>
                                <div class="col-lg-7">
                                    <input id="entity_name" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Director/Head:</label>
                                <div class="col-lg-7">
                                    <select id="director_head" class="select2_demo_1 form-control">
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Company Logo:</label>
                                <div class="col-lg-7">
                                    <input type="file" class="file" name="file_upload" id="file_upload" accept="image/x-png,image/gif,image/jpeg"><br>
                                    <center><img id="img_logo" src="" height="150" width="150" /><br>Current Logo</center>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Warehouse Name:</label>
                                <div class="col-lg-7">
                                    <input id="warehouse_name" type="text" class="form-control">
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-primary" onclick="save_organizational();">Save</button>
                        </div>
                    </div><hr>
                    <div class="ibox" style="border-style: solid; border-color: black; border-width: 1px;">
                        <div class="ibox-title">
                            <h5><i class="fa fa-file-o"></i> Reporting Details</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Property Custodian:</label>
                                <div class="col-lg-7">
                                    <select id="property_custodian" class="select2_demo_1 form-control">
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Division Chief:</label>
                                <div class="col-lg-7">
                                    <select id="division_chief" class="select2_demo_1 form-control">
                                        
                                    </select>
                                </div>
                            </div>
                            <p style="text-align: center; font-weight: bold;">For Issuances PPE and Other Supplies Report</p>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Prepared by:</label>
                                <div class="col-lg-7">
                                    <select id="ppe_prepared_by" class="select2_demo_1 form-control">
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Noted by:</label>
                                <div class="col-lg-7">
                                    <select id="ppe_noted_by" class="select2_demo_1 form-control">
                                        
                                    </select>
                                </div>
                            </div>
                            <p style="text-align: center; font-weight: bold;">For Warehouse Inventory Report</p>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Prepared by:</label>
                                <div class="col-lg-7">
                                    <select id="wi_prepared_by" class="select2_demo_1 form-control">
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Reviewed by:</label>
                                <div class="col-lg-7">
                                    <select id="wi_reviewed_by" class="select2_demo_1 form-control">
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Noted by:</label>
                                <div class="col-lg-7">
                                    <select id="wi_noted_by" class="select2_demo_1 form-control">
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Approved by:</label>
                                <div class="col-lg-7">
                                    <select id="wi_approved_by" class="select2_demo_1 form-control">
                                              
                                    </select>
                                </div>
                            </div>
                            <p style="text-align: center; font-weight: bold;">For Report on the Physical Count of Inventories</p>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Prepared by:</label>
                                <div class="col-lg-7">
                                    <select id="rpci_prepared_by" class="select2_demo_1 form-control">
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Certified Correct:</label>
                                <div class="col-lg-7">
                                    <select id="rpci_certified" class="select2_demo_1 form-control">
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Noted by:</label>
                                <div class="col-lg-7">
                                    <select id="rpci_noted_by" class="select2_demo_1 form-control">
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Approved by:</label>
                                <div class="col-lg-7">
                                    <select id="rpci_approved_by" class="select2_demo_1 form-control">
                                              
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Noted by (COA Personnel):</label>
                                <div class="col-lg-7">
                                    <input id="rpci_coa" type="text" class="form-control">
                                    <input id="rpci_coa_designation" type="text" class="form-control" placeholder="Designation">
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-primary" onclick="save_reporting();">Save</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="ibox" style="border-style: solid; border-color: black; border-width: 1px;">
                        <div class="ibox-title">
                            <h5><i class="fa fa-users"></i> Active Employee List</h5>
                            <button class="btn btn-xs btn-default pull-right" data-toggle="modal" data-target="#modal_add_employee"><i class="fa fa-plus"></i> Add Employee</button>
                        </div>
                        <div class="ibox-content">
                            <div class="chat-users" style="margin: 0px;">
                                <div class="users-list" id="users-list">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ibox" style="border-style: solid; border-color: black; border-width: 1px;">
                        <div class="ibox-title">
                            <h5><i class="fa fa-list"></i> Designations List</h5>
                            <button class="btn btn-xs btn-default pull-right" data-toggle="modal" data-target="#modal_add_designation"><i class="fa fa-plus"></i> Add Designation</button>
                        </div>
                        <div class="ibox-content">
                            <div class="chat-users" style="margin: 0px;">
                                <div class="designation-list" id="designation-list">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br><br>
            <div class="footer">
                <div>
                    <strong>Copyright</strong> <?php echo $_SESSION["company_title"]; ?> &copy; <?php echo date("Y"); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
        require "modals/add_employee.php";
        require "modals/edit_employee.php";
        require "modals/add_designation.php";
        require "modals/edit_designation.php";
        require "../assets/small_chat.php";
        require "../assets/scripts_assets.php";
    ?>
    <!-- Typehead -->
    <script src="../js/plugins/typehead/bootstrap3-typeahead.min.js"></script>
    <script src="js/config.js"></script>
</body>
</html>