<?php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();

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

// Retrieve account_id and screen_id from URL parameters
$account_id = isset($_GET['account_id']) ? $_GET['account_id'] : '';
$screen_id = isset($_GET['screen_id']) ? $_GET['screen_id'] : '';

// Validate and sanitize the input (important for security)
$account_id = mysqli_real_escape_string($conn, $account_id);
$screen_id = mysqli_real_escape_string($conn, $screen_id);

// Fetch screen details
$sql_screen = "SELECT sg.screen_group_name, s.screen_name, s.screen_orientation
               FROM `3.screens_groups_and_accounts` s
               JOIN `2.groups_and_accounts` sg ON s.screen_group_id = sg.screen_group_id
               WHERE s.screen_account_id = ? AND s.unique_screen_id = ?";
$stmt_screen = $conn->prepare($sql_screen);
$stmt_screen->bind_param("ss", $account_id, $screen_id);
$stmt_screen->execute();
$result_screen = $stmt_screen->get_result();
$screen_details = $result_screen->fetch_assoc();
$stmt_screen->close();

$group_name = $screen_details['screen_group_name'] ?? '';
$screen_name = $screen_details['screen_name'] ?? '';
$orientation = $screen_details['screen_orientation'] ?? '';

// Fetch group_id
$sql_group = "SELECT sg.screen_group_id 
              FROM `2.groups_and_accounts` sg
              JOIN `3.screens_groups_and_accounts` s ON sg.screen_group_id = s.screen_group_id
              WHERE s.unique_screen_id = ?";
$stmt_group = $conn->prepare($sql_group);
$stmt_group->bind_param("s", $screen_id);
$stmt_group->execute();
$result_group = $stmt_group->get_result();
$group_id = $result_group->fetch_assoc()['screen_group_id'];
$stmt_group->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Signager Manage Payloads</title>
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
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    
    <style>
        .custom-modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.4);
}

.custom-modal-content {
  background-color: #fefefe;
  margin: 15% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 300px;
  text-align: center;
  border-radius: 5px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.custom-modal-content p {
  font-size: 18px;
  color: #333;
  margin-bottom: 20px;
}

.custom-modal-content button {
  background-color: #5E17EB;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

.custom-modal-content button:hover {
  background-color: #4a11c0;
}
    </style>
    
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
                <i class="fas fa-cog menu-icon"></i>
                <span class="menu-title">Settings</span>
              </a>
            </li>
          </ul>
        </nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper" style="background: #ffffff;">
            <div class="row">
              <!--------- Start Of Cards --->
              <div class="col-12 grid-margin stretch-card">
                <div class="card" style="background:#C9FFB2;">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                      </div>
                    </div>
                    
                    <!-- New card -->
                    <div class="card mt-3" style="background:#00BF63;">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap" style="color: white; font-weight: bold;">
                          <div>Group Name: <span id="groupName"><?php echo htmlspecialchars($group_name); ?></span></div>
                          <div>Screen ID: <span id="serialNumber"><?php echo htmlspecialchars($screen_id); ?></span></div>
                          <div>Screen Name: <span id="screenName"><?php echo htmlspecialchars($screen_name); ?></span></div>
                          <div>Orientation: <span id="orientation"><?php echo htmlspecialchars($orientation); ?></span></div>
                          <div>
                            <button id="pushPayloadBtn" class="btn btn-primary btn-sm mr-2" style="color:white;" onclick="pushPayload()">
                              <i class="fas fa-upload"></i><b>  Push Payload</b> 
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!---- Drag & Drop Area Starts Here ---->
              <div class="col-12 grid-margin stretch-card">
                <div class="card" style="background:#C9FFB2;">
                  <div class="card-body">
                    <h4 class="mb-3" style="color:#5E17EB;"><strong>Current Payload Sequence</strong></h4>
                    
                    <div class="d-flex justify-content-between">
                      <div class="drag-drop-area d-flex align-items-center justify-content-center" 
                           style="border: 2px dashed #007bff; border-radius: 5px; border-color:#5E17EB; background-color: #f8f9fa; width: 15%; height: 90px;"
                           ondrop="drop(event)" ondragover="allowDrop(event)">
                        <p style="color: #6c757d; font-size: 12px; margin: 0;">Drag and drop here</p>
                      </div>
                      <div class="drag-drop-area d-flex align-items-center justify-content-center" 
                           style="border: 2px dashed #007bff; border-radius: 5px; border-color:#5E17EB; background-color: #f8f9fa; width: 15%; height: 90px;"
                           ondrop="drop(event)" ondragover="allowDrop(event)">
                        <p style="color: #6c757d; font-size: 12px; margin: 0;">Drag and drop here</p>
                      </div>
                      <div class="drag-drop-area d-flex align-items-center justify-content-center" 
                           style="border: 2px dashed #007bff; border-radius: 5px; border-color:#5E17EB; background-color: #f8f9fa; width: 15%; height: 90px;"
                           ondrop="drop(event)" ondragover="allowDrop(event)">
                        <p style="color: #6c757d; font-size: 12px; margin: 0;">Drag and drop here</p>
                      </div>
                      <div class="drag-drop-area d-flex align-items-center justify-content-center" 
                           style="border: 2px dashed #007bff; border-radius: 5px; border-color:#5E17EB; background-color: #f8f9fa; width: 15%; height: 90px;"
                           ondrop="drop(event)" ondragover="allowDrop(event)">
                        <p style="color: #6c757d; font-size: 12px; margin: 0;">Drag and drop here</p>
                      </div>
                      <div class="drag-drop-area d-flex align-items-center justify-content-center" 
                           style="border: 2px dashed #007bff; border-radius: 5px; border-color:#5E17EB; background-color: #f8f9fa; width: 15%; height: 90px;"
                           ondrop="drop(event)" ondragover="allowDrop(event)">
                        <p style="color: #6c757d; font-size: 12px; margin: 0;">Drag and drop here</p>
                      </div>
                      <div class="drag-drop-area d-flex align-items-center justify-content-center" 
                           style="border: 2px dashed #007bff; border-radius: 5px; border-color:#5E17EB; background-color: #f8f9fa; width: 15%; height: 90px;"
                           ondrop="drop(event)" ondragover="allowDrop(event)">
                        <p style="color: #6c757d; font-size: 12px; margin: 0;">Drag and drop here</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!---- Drag & Drop Area Ends Here ---->

              <div class="row mt-4" style="margin-top: 0.1rem !important;">
                <!-- First card with title 'Uploaded Images' -->
                <div class="col-md-6 grid-margin stretch-card">
                  <div class="card" style="background:#f6f1ff;">
                    <div class="card-body">
                      <h4 style="color: #5E17EB; padding-bottom: 10px;"><b>Uploaded Images</b></h4>
                      <div id="uploaded-images" class="row">
                        <div class="col-12"><p>Loading images...</p></div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Second card with title 'Uploaded Videos' -->
                <div class="col-md-6 grid-margin stretch-card">
                  <div class="card" style="background:#f6f1ff;">
                    <div class="card-body">
                      <h4 style="color: #5E17EB; padding-bottom: 10px;"><b>Uploaded Videos</b></h4>
                      <div id="uploaded-videos" class="row">
                        <div class="col-12"><p>Loading videos...</p></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!--------- End Of Cards --->
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
    <!-- Custom js for this page-->
    <!-- End custom js for this page-->
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
      function displayError(message) {
            console.error(message);
            $('#uploaded-images, #uploaded-videos').html('<div class="col-12"><p style="color: red;">' + message + '</p></div>');
        }

        function loadContent() {
            console.log('Attempting to load content...');
            $.ajax({
                url: 'fetch_content.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Received response:', response);
                    if (!response) {
                        displayError('Error: Received empty response');
                        return;
                    }
                    if (response.status !== 'success') {
                        displayError('Error: ' + (response.message || 'Unknown error occurred'));
                        return;
                    }

                    // Process images
                    var imagesHtml = '';
                    if (Array.isArray(response.images) && response.images.length > 0) {
                        response.images.forEach(function(image, index) {
                            console.log('Processing image ' + index + ':', image);
                            imagesHtml += '<div class="col-3 mb-3"><img src="' + image + '" class="img-fluid draggable" alt="Uploaded Image" style="width: 100%; height: 100px; object-fit: cover;" draggable="true" ondragstart="drag(event)" data-type="image" onerror="this.onerror=null; this.src=\'placeholder.jpg\'; console.error(\'Failed to load image: ' + image + '\');"></div>';
                        });
                    } else {
                        imagesHtml = '<div class="col-12"><p>No images available</p></div>';
                    }
                    $('#uploaded-images').html(imagesHtml);

                    // Process videos
                    var videosHtml = '';
                    if (Array.isArray(response.videos) && response.videos.length > 0) {
                        response.videos.forEach(function(video, index) {
                            console.log('Processing video ' + index + ':', video);
                            videosHtml += '<div class="col-3 mb-3"><video controls class="img-fluid draggable" style="width: 100%; height: 100px; object-fit: cover;" draggable="true" ondragstart="drag(event)" data-type="video"><source src="' + video + '" type="video/mp4" onerror="console.error(\'Failed to load video: ' + video + '\');">Your browser does not support the video tag.</video></div>';
                        });
                    } else {
                        videosHtml = '<div class="col-12"><p>No videos available</p></div>';
                    }
                    $('#uploaded-videos').html(videosHtml);

                    console.log('Content loaded successfully');
                },
                error: function(xhr, status, error) {
                    displayError('AJAX error: ' + status + ' - ' + error);
                }
            });
        }

        // Initial load
        loadContent();
    });

    function allowDrop(event) {
        event.preventDefault();
    }

    function drag(event) {
        event.dataTransfer.setData("text", event.target.outerHTML);
        event.dataTransfer.setData("type", event.target.dataset.type);
    }

    function drop(event) {
        event.preventDefault();
        var data = event.dataTransfer.getData("text");
        var type = event.dataTransfer.getData("type");
        var dropArea = event.target.closest('.drag-drop-area');
        
        if (dropArea) {
            dropArea.innerHTML = data;
            var droppedElement = dropArea.firstElementChild;
            droppedElement.style.width = '100%';
            droppedElement.style.height = '100%';
            droppedElement.style.objectFit = 'cover';
            droppedElement.removeAttribute('draggable');
            droppedElement.removeAttribute('ondragstart');
        }
    }

    function pushPayload() {
    var accountId = '<?php echo $account_id; ?>';
    var groupId = '<?php echo $group_id; ?>';
    var screenId = '<?php echo $screen_id; ?>';
    
    var payloads = [];
    var dropAreas = document.querySelectorAll('.drag-drop-area');
    dropAreas.forEach(function(area, index) {
        var content = area.querySelector('img, video');
        if (content) {
            if (content.tagName.toLowerCase() === 'img') {
                payloads.push(content.src);
            } else if (content.tagName.toLowerCase() === 'video') {
                var source = content.querySelector('source');
                payloads.push(source ? source.src : content.src);
            }
        } else {
            payloads.push('');
        }
        console.log(`Payload ${index + 1}:`, payloads[payloads.length - 1]);
    });

    // Ensure we always have 8 elements in the payloads array
    while (payloads.length < 8) {
        payloads.push('');
    }

    var data = {
        account_id: accountId,
        group_id: groupId,
        screen_id: screenId,
        payloads: payloads
    };

    console.log('Sending data:', JSON.stringify(data));

    fetch('push_payload.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response data:', data);
        if (data.status === 'success') {
            showModal('Payload pushed successfully!');
        } else {
            showModal('Error pushing payload: ' + data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        showModal('An error occurred while pushing the payload: ' + error.message);
    });
}

function showModal(message) {
    var modal = document.getElementById('customModal');
    var modalMessage = document.getElementById('modalMessage');
    modalMessage.textContent = message;
    modal.style.display = 'block';
}

function closeModal() {
    var modal = document.getElementById('customModal');
    modal.style.display = 'none';
}
    </script>
    
    <div id="customModal" class="custom-modal">
  <div class="custom-modal-content">
    <p id="modalMessage"></p>
    <button onclick="closeModal()">Close</button>
  </div>
</div>
  </body>
</html>