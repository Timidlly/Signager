<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('DOMAIN_NAME', 'https://www.signager.cloud/host/dist'); // Replace with your actual domain

function logMessage($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, 'upload_log.txt');
}

function removeSpaces($string) {
    return str_replace(' ', '', $string);
}

$db_config_path = __DIR__ . '/../../host/db_config.php';
if (file_exists($db_config_path)) {
    require_once $db_config_path;
    logMessage("db_config.php found and included");
} else {
    logMessage("Error: db_config.php not found at: $db_config_path");
    echo json_encode(['status' => 'error', 'message' => 'Server configuration error']);
    exit;
}

session_start();
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => ''];

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in.');
    }

    $user_id = $_SESSION['user_id'];
    
    // Fetch account_id from the database
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->prepare("SELECT account_id FROM `1.users_and_accounts` WHERE email_id = :email_id");
    $stmt->bindParam(':email_id', $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result || !isset($result['account_id'])) {
        throw new Exception('Account ID not found for the user.');
    }
    
    $account_id = $result['account_id'];
    logMessage("Account ID fetched: $account_id");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
        throw new Exception('Invalid request method or missing file.');
    }

    $file = $_FILES['file'];
    $type = $_POST['type'];
    logMessage("File upload attempt - Type: $type, File type: {$file['type']}, Size: {$file['size']}");

    $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    $allowed_video_types = ['video/mp4', 'video/mpeg', 'video/quicktime'];
    $max_file_size = 100 * 1024 * 1024; // 100 MB

    if ($type === 'image' && !in_array($file['type'], $allowed_image_types)) {
        throw new Exception("Invalid image type: {$file['type']}");
    } elseif ($type === 'video' && !in_array($file['type'], $allowed_video_types)) {
        throw new Exception("Invalid video type: {$file['type']}");
    }

    if ($file['size'] > $max_file_size) {
        throw new Exception('File size exceeds the limit.');
    }

    $upload_dir = __DIR__ . "/user-content/{$account_id}/user-{$type}s/";
    $relative_dir = "/user-content/{$account_id}/user-{$type}s/";

    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            throw new Exception('Failed to create upload directory.');
        }
    }

    $file_name = uniqid() . '_' . removeSpaces($file['name']);
    $file_path = $upload_dir . $file_name;
    $relative_file_path = $relative_dir . $file_name;
    $full_url_path = DOMAIN_NAME . $relative_file_path;

    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception('Failed to move uploaded file.');
    }

    logMessage("Attempting to insert into 4.sign_drive - Account ID: $account_id, Type: $type, Path: $full_url_path");

    $stmt = $conn->prepare("INSERT INTO `4.sign_drive` (account_id, content_type, content_location) VALUES (:account_id, :content_type, :content_location)");
    $stmt->bindParam(':account_id', $account_id);
    $stmt->bindParam(':content_type', $type);
    $stmt->bindParam(':content_location', $full_url_path);
    $stmt->execute();

    logMessage("Insertion into 4.sign_drive completed");

    $response['status'] = 'success';
    $response['message'] = 'File uploaded successfully.';
    $response['file_path'] = $full_url_path;
    logMessage("File uploaded successfully - Path: $full_url_path");

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    logMessage("Error: " . $e->getMessage());
}

echo json_encode($response);