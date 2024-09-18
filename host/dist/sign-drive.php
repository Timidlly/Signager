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
    <title>Signager Signdrive</title>
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
    <style>
        .drop-area {
            border: 2px dashed #5e17eb;
            border-radius: 20px;
            width: 100%;
            padding: 20px;
            text-align: center;
            cursor: pointer;
        }
        .drop-area:hover {
            background-color: #f0f0f0;
        }
    </style>
    <style>
    /* ... (existing styles) ... */

    .content-item {
        position: relative;
    }
    .delete-btn {
        position: absolute;
        top: 5px;
        right: 15%;
        padding: 2px 5px;
        font-size: 12px;
        background:white;
        border-color:red;
        color:red;
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
            <div class="content-wrapper" style=" background: #ffffff;">
                <div class="row">
                    <!-- Images Card Starts -->
                    <div class="row mt-4" style="margin-top: 0.1rem !important;">
                        <!-- First card with title 'Images Upload' -->
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card" style="background:#f6f1ff;">
                                <div class="card-body">
                                    <h4 style="color: #5E17EB; padding-bottom: 10px;"><b>Images Upload</b></h4>
                                    <div id="image-drop-area" class="drop-area">
                                        <p style="color: #6c757d;">Drag and drop images here or click to select</p>
                                        <input type="file" id="imageInput" accept="image/*" multiple style="display: none;">
                                    </div>
                                    <div id="image-preview" class="row mt-3"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Second card with title 'Videos Upload' -->
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card" style="background:#f6f1ff;">
                                <div class="card-body">
                                    <h4 style="color: #5E17EB; padding-bottom: 10px;"><b>Videos Upload</b></h4>
                                    <div id="video-drop-area" class="drop-area">
                                        <p style="color: #6c757d;">Drag and drop videos here or click to select</p>
                                        <input type="file" id="videoInput" accept="video/*" multiple style="display: none;">
                                    </div>
                                    <div id="video-preview" class="row mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Images Card Ends -->
                    
                    
                     <!-- Images Card Starts -->
                    <div class="row mt-4" style="margin-top: 0.1rem !important;">
                        <!-- First card with title 'Uploaded Images' -->
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card" style="background:#f6f1ff;">
                                <div class="card-body">
                                    <h4 style="color: #5E17EB; padding-bottom: 10px;"><b>Uploaded Images</b></h4>
                                    <div id="uploaded-images" class="row"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Second card with title 'Uploaded Videos' -->
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card" style="background:#f6f1ff;">
                                <div class="card-body">
                                    <h4 style="color: #5E17EB; padding-bottom: 10px;"><b>Uploaded Videos</b></h4>
                                    <div id="uploaded-videos" class="row"></div>
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
<script src="../../host/assets/js/misc.js"></script>
<script src="../../host/assets/js/settings.js"></script>
<script src="../../host/assets/js/todolist.js"></script>
<!-- endinject -->

<div id="upload-progress" style="display: none; position: fixed; bottom: 20px; right: 20px; background-color: #f0f0f0; border: 1px solid #ddd; padding: 10px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
    <div id="progress-text">Uploading: 0%</div>
    <div id="progress-bar" style="width: 0%; height: 5px; background-color: #4CAF50; margin-top: 5px;"></div>
</div>

<script>
function showProgress(show) {
    document.getElementById('upload-progress').style.display = show ? 'block' : 'none';
}

function updateProgress(percent) {
    document.getElementById('progress-text').innerText = `Uploading: ${percent}%`;
    document.getElementById('progress-bar').style.width = `${percent}%`;
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drop(ev) {
    ev.preventDefault();
    let files = ev.dataTransfer.files;
    let type = ev.target.id.includes('image') ? 'image' : 'video';
    handleFiles(files, type);
}

document.getElementById('image-drop-area').addEventListener('dragover', allowDrop);
document.getElementById('image-drop-area').addEventListener('drop', drop);
document.getElementById('video-drop-area').addEventListener('dragover', allowDrop);
document.getElementById('video-drop-area').addEventListener('drop', drop);

document.getElementById('image-drop-area').addEventListener('click', function() {
    document.getElementById('imageInput').click();
});

document.getElementById('imageInput').addEventListener('change', function(event) {
    handleFiles(event.target.files, 'image');
});

document.getElementById('video-drop-area').addEventListener('click', function() {
    document.getElementById('videoInput').click();
});

document.getElementById('videoInput').addEventListener('change', function(event) {
    handleFiles(event.target.files, 'video');
});

function handleFiles(files, type) {
    for (let i = 0; i < files.length; i++) {
        let file = files[i];
        let formData = new FormData();
        formData.append('file', file);
        formData.append('type', file.type.startsWith('image/') ? 'image' : 'video');

        showProgress(true);
        updateProgress(0);
        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Raw response:', text);
            try {
                const data = JSON.parse(text);
                if (data.status === 'success') {
                    addPreview(file, data.file_path);
                    updateProgress(100);
                    setTimeout(() => {
                        showProgress(false);
                    }, 3000);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
                alert('Upload failed: ' + error.message);
                showProgress(false);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Upload failed: ' + error.message);
            showProgress(false);
        });

        // Simulating upload progress
        let progress = 0;
        let simulateProgress = setInterval(() => {
            progress += 10;
            if (progress > 90) {
                clearInterval(simulateProgress);
            } else {
                updateProgress(progress);
            }
        }, 500);
    }
}

function addPreview(file, filePath) {
    let previewArea = document.getElementById(file.type.startsWith('image/') ? 'image-preview' : 'video-preview');
    let element = document.createElement('div');
    element.classList.add('col-3', 'mb-3');

    if (file.type.startsWith('image/')) {
        element.innerHTML = `<img src="${URL.createObjectURL(file)}" class="img-fluid" style="width: 100%; height: 100px;">`;
    } else if (file.type.startsWith('video/')) {
        element.innerHTML = `<video controls class="video-fluid" style="width: 100%; height: 100px; object-fit: cover;"><source src="${URL.createObjectURL(file)}" type="${file.type}"></video>`;
    }

    previewArea.appendChild(element);

    // After adding the preview, fetch and display all content
    fetchAndDisplayContent();
}

function fetchAndDisplayContent() {
    fetch('fetch_content.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const imagesContainer = document.getElementById('uploaded-images');
                const videosContainer = document.getElementById('uploaded-videos');

                imagesContainer.innerHTML = '';
                videosContainer.innerHTML = '';

                data.images.forEach(imagePath => {
                    const imgElement = document.createElement('div');
                    imgElement.classList.add('col-3', 'mb-3', 'content-item');
                    imgElement.innerHTML = `
                        <img src="${imagePath}" class="img-fluid" style="width: 100%; height: 100px; object-fit: cover;">
                        <button class="btn btn-danger btn-sm delete-btn" onclick="deleteContent('${imagePath}', 'image')">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                    imagesContainer.appendChild(imgElement);
                });

                data.videos.forEach(videoPath => {
                    const videoElement = document.createElement('div');
                    videoElement.classList.add('col-3', 'mb-3', 'content-item');
                    videoElement.innerHTML = `
                        <video controls class="video-fluid" style="width: 100%; height: 100px; object-fit: cover;">
                            <source src="${videoPath}" type="video/mp4">
                        </video>
                        <button class="btn btn-danger btn-sm delete-btn" onclick="deleteContent('${videoPath}', 'video')">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                    videosContainer.appendChild(videoElement);
                });
            } else {
                console.error('Error fetching content:', data.message);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
}

// Call the function when the page loads
document.addEventListener('DOMContentLoaded', fetchAndDisplayContent);

// Call the function after successful upload
function addPreview(file, filePath) {
    // ... (existing code)

    // After adding the preview, fetch and display all content
    fetchAndDisplayContent();
}


function deleteContent(filePath, type) {
    if (confirm('Are you sure you want to delete this ' + type + '?')) {
        fetch('delete_content.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'file=' + encodeURIComponent(filePath)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('File deleted successfully');
                fetchAndDisplayContent(); // Refresh the content
            } else {
                alert('Error deleting file: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            alert('Error deleting file');
        });
    }
}

// Log to console when the script loads
console.log('Drag and drop script loaded');
</script>


</body>
</html>