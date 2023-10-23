<?php

session_start();

if(isset($_SESSION["username"])){
    echo "<script>document.location='index.php'; </script>";
}

?>

<!DOCTYPE html>
<!-- saved from url=(0030)https://ocais.doh.gov.ph/login -->
<html class="no-js css-menubar" lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <meta http-equiv="X-UA-Compatible" content="text/html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap material admin template">
    <meta name="author" content="">
    
    <title>INVENTORY MS | Login</title>

    <link rel="shortcut icon" type="image/x-icon" href="../archives/img/<?php echo $_SESSION["company_logo"]; ?>">
    <link rel="stylesheet" type="text/css" href="sweetalert-master/dist/sweetalert.css">
    <link rel="stylesheet" href="css/animate.css">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="OCAIS_files/bootstrap.css">
    <link rel="stylesheet" href="OCAIS_files/bootstrap-extend.css">
    <link rel="stylesheet" href="OCAIS_files/site.css">
    <link rel="stylesheet" href="OCAIS_files/green.css" type="text/css">
    
    <!-- Plugins -->
    <link rel="stylesheet" href="OCAIS_files/material-ui.min.css">
    <link rel="stylesheet" href="OCAIS_files/bootstrap-tokenfield.min.css">
    <link rel="stylesheet" href="OCAIS_files/login-v2.css">

    <!-- Fonts -->
    <link rel="stylesheet" href="OCAIS_files/material-design.min.css">
    <link rel="stylesheet" href="OCAIS_files/font-awesome.css">
    <link rel="stylesheet" href="OCAIS_files/brand-icons.min.css">
    <link rel="stylesheet" href="OCAIS_files/fontgoogle.css">
    <link rel="stylesheet" href="OCAIS_files/coresite.css">
  
  <body id="siteBody" class="animsition page-login-v2 layout-full page-dark pt-0 fadeIn" style="animation-duration: 800ms; opacity: 1;">
    
    <!-- Page -->
    <div class="page  ml-0" data-animsition-in="fade-in" data-animsition-out="fade-out" style="min-height:calc(100%) !important;">
            <!-- Page Content -->
      <div class="page-content ">
        <div class="page-brand-info" style="margin:5px 100px 0 90px">
            
            <table>
                <tr>
                    <td><img class="brand-img" src="" alt="..." style="height: 180px"></td>
                    <td><span style="font-size: 20px;">DOH-CHD-CARAGA MATERIAL MANAGEMENT UNIT</span><br><span style="font-size: 70px; font-weight: bold;">DOH-ROXIII-IMS</span><br><span style="font-size: 20px;">Inventory Management System</span></td>
                </tr>
            </table> 
            <p class="font-size-20"></p>
            <div style="color: #fdec09;font-size: 18px;border-top: 1PX SOLID #BEBEBE;">
                <br>
                <b>NEW SYSTEM UPDATES (10/18/2023):</b><br>
                <br>
                <div class="row">
                    <div class="col-sm-12">
                        - Designation now appears instead of END USER when generating RIS.
                    </div>
                    <div class="col-sm-12">
                        - Edit Employee Details and change/update their designations.
                    </div>
                    <div class="col-sm-12">
                        - Add and edit, new and existing designations.
                    </div>
                    <div class="col-sm-12">
                        - Minor bugs fixed.
                    </div>
                </div>
            </div>
       </div>

        <div class="page-login-main">
            <div class="brand hidden-md-up text-center mb-20">
                <img class="brand-img" src="" alt="..." style="height:45px;width:45px">
                <h3 class="brand-text font-size-40" style="color: #4CAF4E;">DOH-ROXIII-IMS</h3>
                <h6 class="text-success mt-0">Inventory Management System</h6>
            </div>
            <h3 class="font-size-24">Sign In</h3>
            <p class="hidden-xs-down"></p>

            <form id="LoginForm" role="form">
                <div class="form-group form-material floating" data-plugin="formMaterial">
                    <input type="text" class="form-control empty" id="username" name="username" formgroup="LoginForm_Data" placeholder="Username" required="" autofocus="">
                </div>
                <div class="form-group form-material floating" data-plugin="formMaterial">
                    <input type="password" class="form-control empty" id="password" name="password" formgroup="LoginForm_Data" placeholder="Password" required="">
                </div>
                <div class="form-group mb-10 clearfix">
                    <p class="float-left">No account? <a class="ml-0" href="">Sign Up</a></p>
                    <a class="float-right" href="">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-10 waves-effect waves-classic">Sign in</button>
            </form> 
            
            <p></p>
            <footer class="page-copyright mb-10 text-secondary">
                
                <div class="social" style="margin-top:3rem">
                    <a class="mx-5" href="https://drive.google.com/file/d/1H6DzrxhfjMdY0wh9NiCpfuq5_hdbgc5H/view?usp=sharing" target="_blank">SYSTEM Guide</a>
                    <a class="mx-5" href="https://drive.google.com/file/d/1H6DzrxhfjMdY0wh9NiCpfuq5_hdbgc5H/view?usp=sharing" target="_blank">Downloads</a>
                    <a class="mx-5" href="https://drive.google.com/file/d/1H6DzrxhfjMdY0wh9NiCpfuq5_hdbgc5H/view?usp=sharing" target="_blank">Contact Us</a>
                </div>

                <p class="mt-10 company_title">DEPARTMENT OF HEALTH</p>
                <p>© <?php echo date('Y'); ?>. All RIGHT RESERVED.</p>
            
            </footer>
        </div>  

      </div>
    </div>
    <script src="sweetalert-master/dist/sweetalert.min.js"></script>
    <script src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
          $('form').on('submit', function(event) {
            $.ajax({
              type:"POST",
              data:"username="+$("#username").val()+"&password="+$("#password").val(),
              url:"php/php_login.php",
              }).done(function(data){
                if(data=="1"){
                  window.location = "index.php";
                }else{
                  swal("Login failed!", "Account not found.", "error");
                }
              });
              event.preventDefault();
          });
          $.ajax({
            url: "php/php_load_config.php",
            dataType: "JSON",
            success: function(data){
                $(".brand-img").attr("src", "../archives/img/"+data["company_logo"]);
                $(".company_title").html(data["company_title"]);
            }
          });
        });
    </script>
  
  </body>
  </html>