<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../host/db_config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location:/login.html");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT user_name, account_id FROM `1.users_and_accounts` WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_name = $user['user_name'] ?? 'User';
    $account_id = $user['account_id'];
} else {
    $user_name = 'User';
    $account_id = null;
}

$stmt->close();

// Fetch total number of screens
$total_screens = 0;
if ($account_id) {
    $screen_stmt = $conn->prepare("SELECT COUNT(*) as total FROM `3.screens_groups_and_accounts` WHERE screen_account_id = ?");
    $screen_stmt->bind_param("s", $account_id);
    $screen_stmt->execute();
    $screen_result = $screen_stmt->get_result();
    if ($screen_result->num_rows > 0) {
        $total_screens = $screen_result->fetch_assoc()['total'];
    }
    $screen_stmt->close();

    // Fetch total number of groups
    $total_groups = 0;
    $group_stmt = $conn->prepare("SELECT COUNT(*) as total FROM `2.groups_and_accounts` WHERE screen_account_id = ?");
    $group_stmt->bind_param("s", $account_id);
    $group_stmt->execute();
    $group_result = $group_stmt->get_result();
    if ($group_result->num_rows > 0) {
        $total_groups = $group_result->fetch_assoc()['total'];
    }
    $group_stmt->close();
}

$conn->close();
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
          <img src="/host/assets/images/faces/face28.jpg" alt="profile" />
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
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper" style="background:white;">
            <div class="row">
              <div class="col-md-12 grid-margin">
                <div class="row">
                  <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Welcome <?php echo htmlspecialchars($user_name ?? 'User'); ?></h3>
                    <h6 class="font-weight-normal mb-0"><b>Signager Gen 1</b> all systems are running optimal! You have <span class="text-primary">3 unread alerts!</span></h6>
                  </div>
                  <div class="col-12 col-xl-4">
                    <div class="justify-content-end d-flex">
                      <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 grid-margin stretch-card">
                <div class="card tale-bg">
                  <div class="card-people mt-auto">
                    <img src="assets/images/dashboard/people.svg" alt="people">
                    <div class="weather-info">
                      <div class="d-flex">
                        <div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 grid-margin transparent">
                <div class="row">
                  <div class="col-md-6 mb-4 stretch-card transparent">
                    <div class="card card-tale">
                      <div class="card-body"style="background:#00BF63; border-radius: 20px;">
                        <p class="mb-4"><b>Screens Online</b></p>
                        <p class="fs-30 mb-2">Pinging</p>
                        <p>In last 30 seconds</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 mb-4 stretch-card transparent">
                    <div class="card card-dark-blue">
                      <div class="card-body"style="background:#FF4747; border-radius: 20px;">
                        <p class="mb-4"><b>Screens Offline</b></p>
                        <p class="fs-30 mb-2">Pinging</p>
                        <p>In last 30 seconds</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                    <div class="card card-light-blue">
                      <div class="card-body" style="background:#5E17EB; border-radius: 20px;">
                        <p class="mb-4"><b>Total Number Of Screens</b></p>
                        <p class="fs-30 mb-2"><?php echo $total_screens; ?></p>
                        <p>In your account</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 stretch-card transparent">
                    <div class="card card-light-danger">
                      <div class="card-body" style="background:#f9701c; border-radius: 20px;">
                        <p class="mb-4"><b>Total Number Of Groups</b></p>
                        <p class="fs-30 mb-2"><?php echo $total_groups; ?></p>
                        <p>In your account</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="assets/vendors/chart.js/chart.umd.js"></script>
    <script src="assets/vendors/datatables.net/jquery.dataTables.js"></script>
    <script src="assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js"></script>
    <script src="assets/js/dataTables.select.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/template.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    
    <script src="assets/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="assets/js/dashboard.js"></script>
    <!-- End custom js for this page-->
  </body>
</html>