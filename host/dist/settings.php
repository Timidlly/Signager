<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location:/login.php");
    exit();
}

// If logged in, you can access the user's information like this:
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
?>






<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Signager Settings</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../host/assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="../../host/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../../host/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../host/assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../host/assets/vendors/mdi/css/materialdesignicons.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../../host/assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="host/assets/images/favicon.png" />
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:../../partials/_navbar.html -->
      <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
    <a class="navbar-brand brand-logo me-5" href="index.php"><img src="../../host/assets/images/signager_logo.png" class="me-2" alt="logo" /></a>
    <a class="navbar-brand brand-logo-mini" href="index.php"><img src="../../host/assets/images/signager_logo_mini.png" alt="logo" /></a>
  </div>
 <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
    <ul class="navbar-nav navbar-nav-right">
      <li class="nav-item dropdown">
        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-bs-toggle="dropdown">
          <i class="icon-bell mx-0"></i>
          <span class="count"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
          <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-success">
                <i class="ti-info-alt mx-0"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <h6 class="preview-subject font-weight-normal">Application Error</h6>
              <p class="font-weight-light small-text mb-0 text-muted"> Just now </p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-warning">
                <i class="ti-settings mx-0"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <h6 class="preview-subject font-weight-normal">Settings</h6>
              <p class="font-weight-light small-text mb-0 text-muted"> Private message </p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-info">
                <i class="ti-user mx-0"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <h6 class="preview-subject font-weight-normal">New user registration</h6>
              <p class="font-weight-light small-text mb-0 text-muted"> 2 days ago </p>
            </div>
          </a>
        </div>
      </li>
      <li class="nav-item nav-profile dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
          <img src="https://www.signager.cloud/host/dist/assets/images/faces/face28.jpg" alt="profile" />
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
          <a class="dropdown-item">
            <i class="ti-settings text-primary"></i> Settings </a>
          <a class="dropdown-item" href="/host/logout.php">
  <i class="ti-power-off text-primary"></i> Logout
</a>
        </div>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
      <span class="icon-menu"></span>
    </button>
  </div>
</nav>

      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:../../partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar" style="background:#D9D9D9;">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="../../host/dist/index.php">
        <i class="icon-grid menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
        <i class="icon-layout menu-icon"></i>
        <span class="menu-title">Manage Assets</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="sign-drive.php">Sign Drive</a></li>
         <li class="nav-item"> <a class="nav-link" href="groups.php">Groups</a></li>
          <li class="nav-item"> <a class="nav-link" href="create-a-screen.php">Create a screen</a></li>
          <li class="nav-item"> <a class="nav-link" href="manager-screens.php">Manage Screens</a></li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
              <a class="nav-link" href="analytics.php">
                <i class="icon-bar-graph menu-icon"></i>
                <span class="menu-title">Analytics</span>
                <span class="badge badge-pill badge-danger ml-auto" style="font-size: 0.5rem; padding: 0.2rem 0.5rem; margin-left: 10px; background-color: #ffcccc; color: #ff0000; border: 1px solid #ff0000;">Coming Soon</span>
              </a>
            </li>
    <li class="nav-item">
      <a class="nav-link" href="settings.php">
        <i class="icon-grid menu-icon"></i>
        <span class="menu-title">Settings</span>
      </a>
    </li>
  </ul>
</nav>

 <!-- First Section Starts Here -->
        <div class="main-panel">
          <div class="content-wrapper" style=" background: #ffffff;">
            <div class="row">
                
                <div class="col-md-6 grid-margin stretch-card" style="/* color: #F6F1FF; */--bs-body-bg: #F6F1FF; width: 35%;">
                <div class="card">
                  <div class="card-body">
                    <h4 style="color: #5E17EB;"><b>Change your password</b></h4>
                    <br>
                    <!----<p class="card-description">Add class <code>.btn-social-icon</code></p>-->
                    <div class="template-demo">
                        
                        <style>
                .form-control::placeholder {
                  color: #5E17EB;
                }
                .custom-radio .custom-control-input:checked ~ .custom-control-label::before {
                  background-color: #5E17EB;
                  border-color: #5E17EB;
                }
              </style>

             
        <!-----<input type="text" class="form-control" placeholder="Enter the group name" style="border-radius: 10px;padding-top: 12px; padding-bottom: 12px;"> ---->
       
<input type="text" class="form-control" placeholder="Enter old passoword" style="border-radius: 10px; padding-top: 12px; padding-bottom: 12px; border: white;"><br>
<input type="text" class="form-control" placeholder="Enter new passoword" style="border-radius: 10px; padding-top: 12px; padding-bottom: 12px; border: white;"><br>
<input type="text" class="form-control" placeholder="Re-enter new password" style="border-radius: 10px; padding-top: 12px; padding-bottom: 12px; border: white;">

 
        
       <button type="submit" class="btn btn-primary mt-2" style="border-radius: 10px; border-color:#00BF63; background:#00BF63;">
  <i class="fa-solid fa-key me-2"></i><b>Change Password</b>
</button>
      </div>
      
                  </div>
                </div>
              </div>
      
      
      
   <!-------- Second Card Starts Here --------->
              
<div class="col-md-6 grid-margin stretch-card" style="--bs-body-bg: #F6F1FF;">
  <div class="card">
    <div class="card-body">
      
      <div class="text-center mt-3">
        <img src="/host/dist/assets/images/success.webp" alt="Success" style="width: 20%;">
        <p class="mt-3" style="color: #5E17EB; "><b>Congratulations!</b><br>Your password is successfully changed.</p>
      </div>
    </div>
  </div>
</div>


       <!-------- Second Card Ends Here --------->   





       <!-- First Section Ends Here -->       


        <!-- partial -->
       
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="../../host/assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="../../host/assets/js/off-canvas.js"></script>
    <script src="../../host/assets/js/template.js"></script>
    <script src="../../host/assets/js/settings.js"></script>
    <script src="../../host/assets/js/todolist.js"></script>
    <!-- endinject -->
  </body>
</html>