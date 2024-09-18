<?php
session_start();
require_once '../../host/db_config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to log debug information
function logDebug($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message);
}

logDebug("Script started");

if (!isset($_SESSION['user_id'])) {
    logDebug("User not logged in");
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

logDebug("User ID: " . $_SESSION['user_id']);
logDebug("screen_account_id: " . (isset($_SESSION['screen_account_id']) ? $_SESSION['screen_account_id'] : 'Not set'));

try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    logDebug("Database connected successfully");

    // If screen_account_id is not set in the session, try to fetch it
    if (!isset($_SESSION['screen_account_id'])) {
        $userStmt = $conn->prepare("SELECT account_id FROM `1.users_and_accounts` WHERE id = :user_id");
        $userStmt->bindParam(':user_id', $_SESSION['user_id']);
        $userStmt->execute();
        $userResult = $userStmt->fetch(PDO::FETCH_ASSOC);
        if ($userResult) {
            $_SESSION['screen_account_id'] = $userResult['account_id'];
            logDebug("Fetched screen_account_id: " . $_SESSION['screen_account_id']);
        } else {
            logDebug("Failed to fetch screen_account_id for user_id: " . $_SESSION['user_id']);
        }
    }

    $stmt = $conn->prepare("SELECT g.screen_group_id, g.screen_group_name, 
                                   COUNT(DISTINCT s.unique_screen_id) as screen_count 
                            FROM `2.groups_and_accounts` g 
                            LEFT JOIN `3.screens_groups_and_accounts` s 
                              ON g.screen_group_id = s.screen_group_id 
                              AND g.screen_account_id = s.screen_account_id
                            WHERE g.screen_account_id = :screen_account_id 
                            GROUP BY g.screen_group_id, g.screen_group_name
                            ORDER BY g.screen_group_id");
    
    logDebug("SQL Query: " . $stmt->queryString);
    logDebug("screen_account_id value: " . $_SESSION['screen_account_id']);

    $stmt->bindParam(':screen_account_id', $_SESSION['screen_account_id']);
    $stmt->execute();

    logDebug("Number of rows returned: " . $stmt->rowCount());

    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    logDebug("Fetched groups: " . print_r($groups, true));

    echo json_encode($groups);
} catch(PDOException $e) {
    logDebug("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch(Exception $e) {
    logDebug("General error: " . $e->getMessage());
    echo json_encode(['error' => 'General error: ' . $e->getMessage()]);
}

logDebug("Script ended");