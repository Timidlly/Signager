<?php
session_start();
require_once '../../host/db_config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['group_id']) || !isset($_POST['group_name'])) {
    echo "Error: Invalid request";
    exit;
}

try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("UPDATE `2.groups_and_accounts` SET screen_group_name = :group_name WHERE screen_group_id = :group_id AND screen_account_id = :account_id");
    $stmt->bindParam(':group_name', $_POST['group_name']);
    $stmt->bindParam(':group_id', $_POST['group_id']);
    $stmt->bindParam(':account_id', $_SESSION['screen_account_id']);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "success";
    } else {
        echo "Error: No changes made or group not found";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}