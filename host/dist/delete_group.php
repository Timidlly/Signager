<?php
session_start();
require_once '../../host/db_config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['group_id'])) {
    echo "Error: Invalid request";
    exit;
}

try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("DELETE FROM `2.groups_and_accounts` WHERE screen_group_id = :group_id AND screen_account_id = :account_id");
    $stmt->bindParam(':group_id', $_POST['group_id']);
    $stmt->bindParam(':account_id', $_SESSION['screen_account_id']);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "success";
    } else {
        echo "Error: Group not found or you don't have permission to delete it";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}