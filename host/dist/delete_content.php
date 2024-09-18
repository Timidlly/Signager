<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('DOMAIN_NAME', 'https://www.signager.cloud/host/dist'); // Replace with your actual domain

function logMessage($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, 'delete_log.txt');
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

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['file'])) {
        throw new Exception('Invalid request method or missing file path.');
    }

    $file_url = $_POST['file'];
    logMessage("File deletion attempt - URL: $file_url");

    // Extract the relative path from the full URL
    $relative_path = str_replace(DOMAIN_NAME, '', $file_url);
    $file_path = __DIR__ . $relative_path;

    // Validate the file path to ensure it's within the allowed directory
    $allowed_dir = __DIR__ . "/user-content/{$account_id}/";
    $real_path = realpath($file_path);
    
    if ($real_path === false || strpos($real_path, $allowed_dir) !== 0) {
        throw new Exception('Invalid file path');
    }
    
    // Delete the file
    if (!file_exists($file_path) || !unlink($file_path)) {
        throw new Exception('Failed to delete file');
    }

    // Delete the record from the database
    $stmt = $conn->prepare("DELETE FROM `4.sign_drive` WHERE account_id = :account_id AND content_location = :content_location");
    $stmt->bindParam(':account_id', $account_id);
    $stmt->bindParam(':content_location', $file_url);
    $stmt->execute();

    $response['status'] = 'success';
    $response['message'] = 'File deleted successfully.';
    logMessage("File deleted successfully - Path: $file_path");

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    logMessage("Error: " . $e->getMessage());
}

echo json_encode($response);