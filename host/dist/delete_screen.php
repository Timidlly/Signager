<?php
session_start();
require_once '../../host/db_config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['screen_id'])) {
    echo 'error';
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    echo 'error';
    exit;
}

$screen_id = $_POST['screen_id'];
$account_id = $_SESSION['account_id'];

$sql = "DELETE FROM `3.screens_groups_and_accounts` WHERE unique_screen_id = ? AND screen_account_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $screen_id, $account_id);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}

$stmt->close();
$conn->close();
?>