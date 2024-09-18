<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location:/login.php");
    exit();
}

// Include database configuration
require_once '../../host/db_config.php';

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current user's email and account_id
$email = $_SESSION['email'];
$sql_account = "SELECT account_id FROM `1.users_and_accounts` WHERE email_id = ?";
$stmt_account = $conn->prepare($sql_account);
$stmt_account->bind_param("s", $email);
$stmt_account->execute();
$result_account = $stmt_account->get_result();
$account_id = $result_account->fetch_assoc()['account_id'];
$stmt_account->close();

$_SESSION['account_id'] = $account_id; // Store account_id in session

// Fetch screens for the current account
$sql_screens = "SELECT sg.screen_group_name, s.unique_screen_id, s.screen_name, s.screen_orientation, s.screen_password 
                FROM `3.screens_groups_and_accounts` s
                JOIN `2.groups_and_accounts` sg ON s.screen_group_id = sg.screen_group_id
                WHERE s.screen_account_id = ?";
$stmt_screens = $conn->prepare($sql_screens);
$stmt_screens->bind_param("s", $account_id);
$stmt_screens->execute();
$result_screens = $stmt_screens->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Signager Manage Screens</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../../host/assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="../../host/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../../host/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../host/assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../host/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://kit.fontawesome.com/72fdd8a1dc.js" crossorigin="anonymous"></script>

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
                    <p class="font-weight-light small-text mb-0 text-muted">
                      Just now
                    </p>
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
                    <p class="font-weight-light small-text mb-0 text-muted">
                      Private message
                    </p>
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
                    <p class="font-weight-light small-text mb-0 text-muted">
                      2 days ago
                    </p>
                  </div>
                </a>
              </div>
            </li>
            <li class="nav-item nav-profile dropdown">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
                <img src="assets/images/faces/face28.jpg" alt="profile" />
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                <a class="dropdown-item">
                  <i class="ti-settings text-primary"></i>
                  Settings
                </a>
                <a class="dropdown-item" href="/host/logout.php">
                  <i class="ti-power-off text-primary"></i>
                  Logout
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
          <div class="content-wrapper" style="background: #ffffff;">
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card" style="background:#F6F1FF;">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <h4 style="color: #5E17EB;"><b>Screens table</b></h4>
                      </div>
                    </div>
                    <div class="table-responsive mt-3">
                      <table class="table table-bordered table-striped" style="border-radius: 50px;">
                        <thead>
                          <tr>
                            <th>Serial No</th>
                            <th>Group Name</th>
                            <th>Screen Name</th>
                            <th>Screen ID</th>
                            <th>Screen Password</th>
                            <th>Orientation</th>
                            <th>Payload Configuration</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                          $serial_no = 1;
                          while ($row = $result_screens->fetch_assoc()) : 
                          ?>
                          <tr>
                            <td><?php echo $serial_no++; ?></td>
                            <td><?php echo htmlspecialchars($row['screen_group_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['screen_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['unique_screen_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['screen_password']); ?></td>
                            <td><?php echo htmlspecialchars($row['screen_orientation']); ?></td>
                            <td>
                              <a href="manager-payloads.php?account_id=<?php echo urlencode($_SESSION['account_id']); ?>&screen_id=<?php echo urlencode($row['unique_screen_id']); ?>" class="btn btn-sm btn-primary" style="border-radius: 5px; border-color:#FF4747; background:#FF4747;">
                                <b>View Payload </b> &nbsp; <i class="fa-solid fa-circle-chevron-right"></i>
                              </a>
                            </td>
                            <td>
                              <button class="btn btn-sm btn-danger delete-screen" data-screen-id="<?php echo htmlspecialchars($row['unique_screen_id']); ?>" style="border-radius: 5px; color:white;">
                                <i class="fa fa-trash"></i> Delete
                              </button>
                            </td>
                          </tr>
                          <?php endwhile; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:../../partials/_footer.html -->
          
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
    <script src="../../host/assets/js/hoverable-collapse.js"></script>
    <script src="../../host/assets/js/template.js"></script>
    <script src="../../host/assets/js/settings.js"></script>
    <script src="../../host/assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <!-- End custom js for this page-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
      $('.delete-screen').click(function() {
        var screenId = $(this).data('screen-id');
        if (confirm('Are you sure you want to delete this screen?')) {
          $.ajax({
            url: 'delete_screen.php',
            method: 'POST',
            data: { screen_id: screenId },
            success: function(response) {
              if (response === 'success') {
                alert('Screen deleted successfully');
                location.reload();
              } else {
                alert('Error deleting screen');
              }
            },
            error: function() {
              alert('Error communicating with the server');
            }
          });
        }
      });
    });
    </script>
  </body>
</html>