<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location:/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];

// Include the database configuration file
require_once '../../host/db_config.php';

$success_message = '';
$error_message = '';

function logDebug($message) {
    $logFile = __DIR__ . '/debug.log';
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, $logFile);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_name = trim($_POST['group_name']);
    
    if (!empty($group_name)) {
        try {
            $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            logDebug("Connected to database successfully.");
            logDebug("Current database: " . $conn->query('SELECT DATABASE()')->fetchColumn());
            
            $stmt = $conn->query("SHOW TABLES LIKE '2.groups_and_accounts'");
            logDebug("Table exists: " . ($stmt->rowCount() > 0 ? 'Yes' : 'No'));
            
            logDebug("Autocommit status: " . ($conn->getAttribute(PDO::ATTR_AUTOCOMMIT) ? 'Enabled' : 'Disabled'));

            // Get the account_id of the logged-in user
            $stmt = $conn->prepare("SELECT account_id FROM `1.users_and_accounts` WHERE id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user_result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user_result) {
                throw new Exception("User account not found for user_id: $user_id");
            }
            
            $screen_account_id = $user_result['account_id'];
            logDebug("Retrieved screen_account_id: $screen_account_id for user_id: $user_id");

            // Get the maximum screen_group_id
            $stmt = $conn->query("SELECT MAX(screen_group_id) as max_id FROM `2.groups_and_accounts`");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $max_id = $result['max_id'];

            $new_screen_group_id = max(246861, $max_id + 1);
            logDebug("New screen_group_id: $new_screen_group_id");
            
            // Insert new group
            $stmt = $conn->prepare("INSERT INTO `2.groups_and_accounts` (screen_group_id, screen_group_name, screen_account_id) VALUES (:screen_group_id, :group_name, :screen_account_id)");
            $stmt->bindParam(':screen_group_id', $new_screen_group_id, PDO::PARAM_INT);
            $stmt->bindParam(':group_name', $group_name);
            $stmt->bindParam(':screen_account_id', $screen_account_id);
            $stmt->execute();
            
            logDebug("Executed INSERT query. Rows affected: " . $stmt->rowCount());
            logDebug("PDO Error Info: " . print_r($conn->errorInfo(), true));
            
            // Immediately check for the inserted data
            $stmt = $conn->prepare("SELECT * FROM `2.groups_and_accounts` WHERE screen_group_id = :screen_group_id");
            $stmt->bindParam(':screen_group_id', $new_screen_group_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            logDebug("Immediately after INSERT, SELECT result: " . print_r($result, true));
            
            $success_message = "The screen group is successfully created.";
            logDebug("New group created - ID: $new_screen_group_id, Name: $group_name, Account ID: $screen_account_id");
        } catch(Exception $e) {
            $error_message = "Error: " . $e->getMessage();
            logDebug("Exception occurred: " . $error_message);
        }
        $conn = null;
    } else {
        $error_message = "Please enter a group name.";
        logDebug("Form submitted with empty group name.");
    }
}

// The rest of your HTML code goes here...
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Signager Groups</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../../host/assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="../../host/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../../host/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../host/assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../host/assets/vendors/mdi/css/materialdesignicons.min.css">
    <script src="https://kit.fontawesome.com/72fdd8a1dc.js" crossorigin="anonymous"></script>
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../../host/assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="/host/assets/images/favicon.png" />
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
      
     <!---<button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
      <span class="icon-menu"></span>
    </button>
    <ul class="navbar-nav mr-lg-2">
      <li class="nav-item nav-search d-none d-lg-block">
        <div class="input-group">
          <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
            <span class="input-group-text" id="search">
              <i class="icon-search"></i>
            </span>
          </div>
          <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now" aria-label="search" aria-describedby="search">
        </div>
      </li>
    </ul> -->
    
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
          <img src="assets/images/faces/face28.jpg" alt="profile" />
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
          <a class="dropdown-item">
            <i class="ti-settings text-primary"></i> Settings </a>
          <a class="dropdown-item" href="/logout.php">
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
      <a class="nav-link" href="/dist/settings.php">
        <i class="icon-grid menu-icon"></i>
        <span class="menu-title">Settings</span>
      </a>
    </li>
    
    
  </ul>
</nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper" style=" background: #ffffff;">
            <div class="row">
                
                <!------ Create Group Card Start ------->
                <div class="col-md-6 grid-margin stretch-card" style="/* color: #F6F1FF; */--bs-body-bg: #F6F1FF;width: 35%;">
                <div class="card">
                  <div class="card-body">
                    <h4 style="color: #5E17EB;"><b>Create a new screen group</b></h4><br>
                    <div class="template-demo">
                      <form method="POST" action="">
                        <style>
                          .form-control::placeholder {
                            color: #5E17EB;
                          }
                        </style>
                        <input type="text" name="group_name" class="form-control" placeholder="Enter the group name" style="border-radius: 10px; padding-top: 12px; padding-bottom: 12px; border: white;">
                        <button type="submit" class="btn btn-primary mt-2" style="border-radius: 10px;">
                          <i class="fa-solid fa-layer-group me-2"></i>Create Group
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              <!------ Create Group Card Ends ------->
              
              <!------ Group Creation Successful Card Starts  ------->
              <div class="col-md-6 grid-margin stretch-card" style="--bs-body-bg: #F6F1FF; width: 35%; <?php echo empty($success_message) ? 'display: none;' : ''; ?>" id="successCard">
                <div class="card">
                  <div class="card-body">
                    <div class="text-center mt-3">
                      <img src="/host/dist/assets/images/success.webp" alt="Success" style="width: 20%;">
                      <p class="mt-3" style="color: #5E17EB; "><b>Congratulations!</b> <br> Your new group is successfully created.<br><?php echo $success_message; ?></p>
                    </div>
                  </div>
                </div>
              </div>
              <!------ Group Creation Successful Card Ends ------->

              <?php if (!empty($error_message)): ?>
                <div class="col-md-6 grid-margin stretch-card" style="--bs-body-bg: #F6F1FF; width: 35%;">
                  <div class="card">
                    <div class="card-body">
                      <div class="text-center mt-3">
                        <p class="mt-3" style="color: #FF0000;"><b>Error:</b><br><?php echo $error_message; ?></p>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>




<!------ Group Table Starts Here ------->
<div class="col-12 grid-margin stretch-card">
  <div class="card" style="background:#F6F1FF;">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <h4 style="color: #5E17EB;"><b>Groups table</b></h4>
        </div>
      </div>
      <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped" style="border-radius: 50px;">
          <thead>
            <tr>
              <th>Serial No</th>
              <th>Group Name</th>
              <th>Number Of Screens</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="groupsTableBody">
            <!-- Table data will be dynamically inserted here -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!------ Group Table Ends Here ------->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function loadGroupsTable() {
    $.ajax({
        url: 'fetch_groups.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var tableBody = $('#groupsTableBody');
            tableBody.empty();
            if (data.error) {
                tableBody.append('<tr><td colspan="4" class="text-center">' + data.error + '</td></tr>');
            } else if (data.length === 0) {
                tableBody.append('<tr><td colspan="4" class="text-center">No groups found.</td></tr>');
            } else {
                $.each(data, function(index, group) {
                    var row = '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + group.screen_group_name + '</td>' +
                        '<td>' + group.screen_count + '</td>' +
                        '<td>' +
                        '<button class="btn btn-sm btn-primary mr-2 edit-group" style="border-radius: 5px; margin-right: 10px;" data-id="' + group.screen_group_id + '" data-name="' + group.screen_group_name + '">' +
                        '<i class="fas fa-edit me-1"></i><b>Edit</b>' +
                        '</button>' +
                        '<button class="btn btn-sm btn-danger text-white delete-group" style="border-radius: 5px;" data-id="' + group.screen_group_id + '">' +
                        '<i class="fas fa-trash-alt me-1"></i>Delete' +
                        '</button>' +
                        '</td>' +
                        '</tr>';
                    tableBody.append(row);
                });
            }
        },
        error: function() {
            $('#groupsTableBody').html('<tr><td colspan="4" class="text-center">Error fetching data.</td></tr>');
        }
    });
}

// Load table data when the page loads
$(document).ready(function() {
    loadGroupsTable();

    // Edit group button click handler
    $(document).on('click', '.edit-group', function() {
        var groupId = $(this).data('id');
        var groupName = $(this).data('name');
        $('#editGroupId').val(groupId);
        $('#editGroupName').val(groupName);
        $('#editGroupModal').modal('show');
    });

    // Delete group button click handler
    $(document).on('click', '.delete-group', function() {
        var groupId = $(this).data('id');
        $('#deleteGroupId').val(groupId);
        $('#deleteGroupModal').modal('show');
    });

    // Save group changes
    $('#saveGroupChanges').click(function() {
        var groupId = $('#editGroupId').val();
        var groupName = $('#editGroupName').val();
        $.ajax({
            url: 'update_group.php',
            type: 'POST',
            data: {
                group_id: groupId,
                group_name: groupName
            },
            success: function(response) {
                if (response.includes('success')) {
                    $('#editGroupModal').modal('hide');
                    loadGroupsTable();
                } else {
                    alert('Error updating group: ' + response);
                }
            },
            error: function() {
                alert('Error updating group');
            }
        });
    });

    // Confirm delete group
    $('#confirmDeleteGroup').click(function() {
        var groupId = $('#deleteGroupId').val();
        $.ajax({
            url: 'delete_group.php',
            type: 'POST',
            data: {
                group_id: groupId
            },
            success: function(response) {
                if (response.includes('success')) {
                    $('#deleteGroupModal').modal('hide');
                    loadGroupsTable();
                } else {
                    alert('Error deleting group: ' + response);
                }
            },
            error: function() {
                alert('Error deleting group');
            }
        });
    });
});

// Reload table data after a new group is created
$('form').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.includes('success_message')) {
                $('#successCard').show();
                loadGroupsTable();
            }
        }
    });
});
</script>
                        <!--------- End Of Table --->

                      <!--------- Update Groups Tables Starts --->
<!-- Edit Group Modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
  .modal-dialog-centered {
    display: flex;
    align-items: center;
    min-height: calc(100% - 1rem);
  }
  @media (min-width: 576px) {
    .modal-dialog-centered {
      min-height: calc(100% - 3.5rem);
    }
  }
</style>
<div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-color: #5E17EB;">
      <div class="modal-header" style="background: #5E17EB;">
        <h5 class="modal-title" id="editGroupModalLabel" style="color: white;">Edit Group Name</h5>
      </div>
      <div class="modal-body">
        <form id="editGroupForm">
          <input type="hidden" id="editGroupId" name="group_id">
          <div class="form-group">
            <label for="editGroupName">Group Name</label>
            <input type="text" class="form-control" id="editGroupName" name="group_name" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:5px; color:white;"> <i class="fa-solid fa-xmark"></i>&nbsp Close</button>
        <button type="button" class="btn btn-primary" id="saveGroupChanges" style="border-radius:5px"><i class="fa-solid fa-file-arrow-up"></i>&nbsp Save changes</button>
      </div>
    </div>
  </div>
</div>
<!--------- Update Groups Tables Ends --->


<!-- Delete Group Confirmation Modal Starts -->
<div class="modal fade" id="deleteGroupModal" tabindex="-1" aria-labelledby="deleteGroupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-color: #5E17EB;">
      <div class="modal-header" style="background: #5E17EB;">
        <h5 class="modal-title" id="deleteGroupModalLabel" style="color: white;">Confirm Group Deletion</h5>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this group?
        <input type="hidden" id="deleteGroupId">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:5px; color:white;"><i class="fa-solid fa-xmark"></i>&nbsp Cancel</button>
        <button type="button" class="btn btn-danger"  id="confirmDeleteGroup" style="border-radius:5px; color:white;"><i class="fas fa-trash-alt me-1" aria-hidden="true"></i>Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Group Confirmation Modal Ends -->




              
              
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
    <script src="../../host/assets/js/template.js"></script>
    <script src="../../host/assets/js/settings.js"></script>
    <script src="../../host/assets/js/todolist.js"></script>
    <!-- endinject -->
  </body>
</html>