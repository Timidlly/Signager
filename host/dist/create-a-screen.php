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

function getScreenGroupName($conn, $screen_group_id) {
    $sql = "SELECT screen_group_name FROM `2.groups_and_accounts` WHERE screen_group_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $screen_group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['screen_group_name'];
    }
    return "Unknown Group";
}

function generateUniqueScreenId($conn) {
    do {
        $unique_screen_id = sprintf("%06d", mt_rand(0, 999999));
        $sql_check = "SELECT COUNT(*) as count FROM `3.screens_groups_and_accounts` WHERE unique_screen_id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $unique_screen_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();
        $stmt_check->close();
    } while ($row_check['count'] > 0);
    
    return $unique_screen_id;
}

// Get the current user's email
$email = $_SESSION['email'];

// Fetch the account_id for the current user
$sql_account = "SELECT account_id FROM `1.users_and_accounts` WHERE email_id = ?";
$stmt_account = $conn->prepare($sql_account);
$stmt_account->bind_param("s", $email);
$stmt_account->execute();
$result_account = $stmt_account->get_result();

if ($row_account = $result_account->fetch_assoc()) {
    $account_id = $row_account['account_id'];

    // Fetch screen group names and IDs for the current account
    $sql_groups = "SELECT screen_group_id, screen_group_name FROM `2.groups_and_accounts` WHERE screen_account_id = ?";
    $stmt_groups = $conn->prepare($sql_groups);
    $stmt_groups->bind_param("s", $account_id);
    $stmt_groups->execute();
    $result_groups = $stmt_groups->get_result();

    $screen_groups = array();
    while ($row_groups = $result_groups->fetch_assoc()) {
        $screen_groups[] = $row_groups;
    }
} else {
    $screen_groups = array(); // Empty array if no account found
    $account_id = null;
}

$stmt_account->close();
$stmt_groups->close();

$success_message = '';
$error_message = '';
$new_screen_data = null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("Form submitted. POST data: " . print_r($_POST, true));

    $screen_orientation = isset($_POST['screen_orientation']) ? $_POST['screen_orientation'] : '';
    $screen_group_id = isset($_POST['screen_group']) ? $_POST['screen_group'] : '';
    $screen_name = isset($_POST['screen_name']) ? $_POST['screen_name'] : '';
    $screen_password = isset($_POST['screen_password']) ? $_POST['screen_password'] : '';

    error_log("Parsed form data:");
    error_log("screen_orientation: $screen_orientation");
    error_log("screen_group_id: $screen_group_id");
    error_log("screen_name: $screen_name");
    error_log("screen_password: $screen_password");

    // Validate input
    if (empty($screen_orientation) || empty($screen_group_id) || empty($screen_name) || empty($screen_password)) {
        $error_message = "All fields are required. Please check the following:";
        if (empty($screen_orientation)) $error_message .= " Screen Orientation,";
        if (empty($screen_group_id)) $error_message .= " Screen Group,";
        if (empty($screen_name)) $error_message .= " Screen Name,";
        if (empty($screen_password)) $error_message .= " Screen Password,";
        $error_message = rtrim($error_message, ",");
    } else {
        // Generate a unique 6-digit screen ID
        $unique_screen_id = generateUniqueScreenId($conn);

        // Insert the new screen into the database
        $sql_insert = "INSERT INTO `3.screens_groups_and_accounts` (unique_screen_id, screen_name, screen_password, screen_orientation, screen_group_id, screen_account_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssssss", $unique_screen_id, $screen_name, $screen_password, $screen_orientation, $screen_group_id, $account_id);

        if ($stmt_insert->execute()) {
            $success_message = "Screen created successfully!";
            $screen_group_name = getScreenGroupName($conn, $screen_group_id);
            
            $new_screen_data = array(
                'unique_screen_id' => $unique_screen_id,
                'screen_name' => $screen_name,
                'screen_password' => $screen_password,
                'screen_group_id' => $screen_group_id,
                'screen_group_name' => $screen_group_name
            );
        } else {
            $error_message = "Error creating screen: " . $conn->error;
        }

        $stmt_insert->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Signager Create A Screen</title>
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
                <img src="assets/images/faces/face28.jpg" alt="profile"/>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                <a class="dropdown-item">
                  <i class="ti-settings text-primary"></i>
                  Settings
                </a>
                <a class="dropdown-item" href="/logout.php">
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
              <div class="col-md-6 grid-margin stretch-card" style="--bs-body-bg: #F6F1FF; width: 35%;">
                <div class="card">
                  <div class="card-body">
                    <h4 style="color: #5E17EB;"><b>Create a new screen</b></h4>
                    <p style="color:#5E17EB;">Select screen orientation</p><br>
                    
                    <?php if ($error_message): ?>
                      <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                   
                    
                    <form method="POST" action="">
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

                        <!-- Images and Radio Buttons -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                          <div class="text-center">
                            <img src="/host/dist/assets/images/horizantol_digital_signage.webp" alt="horizantal digital signage" style="width: 35%;">
                            <div class="custom-control custom-radio mt-2">
                              <input type="radio" id="customRadio1" name="screen_orientation" value="Horizontal" class="custom-control-input" required>
                              <label class="custom-control-label" for="customRadio1">Horizontal</label>
                            </div>
                          </div>
                          <div class="text-center">
                            <img src="/host/dist/assets/images/vertical_digital_signage-.webp" alt="vertical digital signage" style="width: 35%;">
                            <div class="custom-control custom-radio mt-2">
                              <input type="radio" id="customRadio2" name="screen_orientation" value="Vertical" class="custom-control-input" required>
                              <label class="custom-control-label" for="customRadio2">Vertical</label>
                            </div></div>
                        </div>

                        <style>
                          .form-control::placeholder {
                            color: #5E17EB;
                          }
                        </style>

                        <!-- Placeholder with Dropdown Start-->
                        <select name="screen_group" class="form-select mb-3" style="border-radius: 10px; padding-top: 12px; padding-bottom: 12px; border: .2px ; color: #5E17EB;" required>
                          <option value="" selected disabled>Select screen group</option>
                          <?php foreach ($screen_groups as $group): ?>
                            <option value="<?php echo htmlspecialchars($group['screen_group_id']); ?>"><?php echo htmlspecialchars($group['screen_group_name']); ?></option>
                          <?php endforeach; ?>
                        </select>
                        <!-- Placeholder with Dropdown End-->

                        <input type="text" name="screen_name" class="form-control" placeholder="Enter a unique screen name" style="border-radius: 10px; padding-top: 12px; padding-bottom: 12px; border: white;" required><br>
                        <input type="password" name="screen_password" class="form-control" placeholder="Enter a screen password" style="border-radius: 10px; padding-top: 12px; padding-bottom: 12px; border: white;" required>

                        <button type="submit" class="btn btn-primary mt-2" style="border-radius: 10px; border-color:#00BF63; background:#00BF63;">
                          <i class="fas fa-desktop me-2"></i><b>Create a screen!</b>
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <!--  Create A Screen Ends Here -->
              
              <!--  Screen Creation Successful Starts Here -->
<?php if ($new_screen_data): ?>
<div class="col-md-6 grid-margin stretch-card" style="--bs-body-bg: #F6F1FF; width: 35%;">
  <div class="card">
    <div class="card-body">
      <div class="text-center mt-3">
        <img src="/host/dist/assets/images/success.webp" alt="Success" style="width: 20%;">
        <p class="mt-3" style="color: #5E17EB; "><b>Congratulations!</b><br>The new screen is successfully added.</p>
      </div>
      
      <div class="mt-4">
        <div class="card bg-white">
          <div class="card-body">
            <p id="sampleText">Your new signager credentials! <br><br>
              Screen Group: <?php echo htmlspecialchars($new_screen_data['screen_group_name']); ?><br>
              Screen Name: <?php echo htmlspecialchars($new_screen_data['screen_name']); ?><br>
              Screen ID: <?php echo htmlspecialchars($new_screen_data['unique_screen_id']); ?><br>
              Screen Password: <?php echo htmlspecialchars($new_screen_data['screen_password']); ?></p>
          </div>
        </div><br>
        <div class="mt-2">
          <button class="btn btn-primary me-2 fw-bold text-white" style="border-radius:10px" onclick="copyToClipboard()">
            <i class="fas fa-copy me-2"></i>Copy to Clipboard
          </button>
          <br><br>
          <button class="btn btn-success fw-bold text-white" style="background:#00BF63;border-radius:10px" onclick="shareOnWhatsApp()">
            <i class="fab fa-whatsapp me-2"></i>Share on WhatsApp
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
<!--  Screen Creation Successful Ends Here -->
              
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
    <script>
    function copyToClipboard() {
      var text = document.getElementById("sampleText");
      navigator.clipboard.writeText(text.innerText).then(function() {
        alert("Text copied to clipboard!");
      }, function(err) {
        console.error('Could not copy text: ', err);
      });
    }
    function shareOnWhatsApp() {
      var text = document.getElementById("sampleText").innerText;
      var encodedText = encodeURIComponent(text);
      var whatsappUrl = "https://wa.me/?text=" + encodedText;
      window.open(whatsappUrl, '_blank');
    }
    </script>
    <!-- End custom js for this page-->
  </body>
</html>