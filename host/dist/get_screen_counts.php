<?php
session_start();
require_once '../../host/db_config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed']);
    exit();
}

// Get account_id
$stmt = $conn->prepare("SELECT account_id FROM `1.users_and_accounts` WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $account_id = $user['account_id'];
} else {
    echo json_encode(['error' => 'User not found']);
    exit();
}

$stmt->close();

// Get total screens
$screen_stmt = $conn->prepare("SELECT COUNT(*) as total FROM `3.screens_groups_and_accounts` WHERE screen_account_id = ?");
$screen_stmt->bind_param("s", $account_id);
$screen_stmt->execute();
$screen_result = $screen_stmt->get_result();
$total_screens = $screen_result->fetch_assoc()['total'];
$screen_stmt->close();

// Get online screens (pinged in last 30 seconds)
$ping_stmt = $conn->prepare("
    SELECT COUNT(DISTINCT p.unique_screen_id) as ping_count
    FROM `6.ping` p
    JOIN `3.screens_groups_and_accounts` s ON p.unique_screen_id = s.unique_screen_id
    WHERE p.account_id = ? AND s.screen_account_id = ? AND p.timestamp >= NOW() - INTERVAL 30 SECOND
");
$ping_stmt->bind_param("ss", $account_id, $account_id);
$ping_stmt->execute();
$ping_result = $ping_stmt->get_result();
$online_screens = $ping_result->fetch_assoc()['ping_count'];
$ping_stmt->close();

$conn->close();

$offline_screens = $total_screens - $online_screens;

echo json_encode([
    'online' => $online_screens,
    'offline' => $offline_screens
]);
?>