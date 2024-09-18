<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

$db_config_path = __DIR__ . '/../../host/db_config.php';
if (file_exists($db_config_path)) {
    require_once $db_config_path;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Server configuration error']);
    exit;
}

try {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
        throw new Exception('User not logged in.');
    }

    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch account_id
    $stmt = $conn->prepare("SELECT account_id FROM `1.users_and_accounts` WHERE email_id = :email_id");
    $stmt->bindParam(':email_id', $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result || !isset($result['account_id'])) {
        throw new Exception('Account ID not found for the user.');
    }

    $account_id = $result['account_id'];

    // Fetch content
    $stmt = $conn->prepare("SELECT content_type, content_location FROM `4.sign_drive` WHERE account_id = :account_id");
    $stmt->bindParam(':account_id', $account_id);
    $stmt->execute();

    $images = [];
    $videos = [];
    $other = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $content_type = $row['content_type'];
        $content_location = $row['content_location'];

        if ($content_type === 'image') {
            $images[] = $content_location;
        } elseif ($content_type === 'video') {
            $videos[] = $content_location;
        } else {
            $other[] = $content_location;
        }
    }

    echo json_encode([
        'status' => 'success',
        'images' => $images,
        'videos' => $videos,
        'other' => $other
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}