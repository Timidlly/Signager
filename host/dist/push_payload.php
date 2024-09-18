<?php
session_start();
require_once '../../host/db_config.php';
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

function logDebug($message) {
    error_log("[DEBUG] " . $message);
}

function sendResponse($status, $message, $debug = null) {
    $response = ['status' => $status, 'message' => $message];
    if ($debug !== null) {
        $response['debug'] = $debug;
    }
    echo json_encode($response);
    exit;
}

// Wrap the entire script in a try-catch block
try {
    // Start output buffering
    ob_start();

    header('Content-Type: application/json');

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }

    // Get the POST data
    $rawData = file_get_contents('php://input');
    logDebug("Received raw data: " . $rawData);

    $data = json_decode($rawData, true);
    if (!$data) {
        throw new Exception('Invalid data received');
    }

    logDebug("Decoded data: " . json_encode($data));

    // Validate the data
    if (!isset($data['account_id'], $data['group_id'], $data['screen_id'], $data['payloads']) 
        || !is_array($data['payloads']) 
        || count($data['payloads']) !== 8) {
        throw new Exception('Invalid or incomplete data received');
    }

    // Validate each payload
    foreach ($data['payloads'] as $payload) {
        if (!is_string($payload)) {
            throw new Exception('Invalid payload data');
        }
    }

    // Create database connection
    logDebug("Attempting to connect to database");
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    logDebug("Successfully connected to database");

    // Prepare the SQL statement
    $sql = "INSERT INTO `5.payloads` (account_id, group_id, screen_id, delivery_1, delivery_2, delivery_3, delivery_4, delivery_5, delivery_6, delivery_7, delivery_8) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            delivery_1 = VALUES(delivery_1),
            delivery_2 = VALUES(delivery_2),
            delivery_3 = VALUES(delivery_3),
            delivery_4 = VALUES(delivery_4),
            delivery_5 = VALUES(delivery_5),
            delivery_6 = VALUES(delivery_6),
            delivery_7 = VALUES(delivery_7),
            delivery_8 = VALUES(delivery_8)";

    logDebug("Preparing SQL statement: " . $sql);

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    $stmt->bind_param("sssssssssss", 
        $data['account_id'], 
        $data['group_id'], 
        $data['screen_id'], 
        $data['payloads'][0], 
        $data['payloads'][1], 
        $data['payloads'][2], 
        $data['payloads'][3], 
        $data['payloads'][4], 
        $data['payloads'][5], 
        $data['payloads'][6], 
        $data['payloads'][7]
    );

    logDebug("Executing SQL statement with parameters: " . json_encode($data));

    if (!$stmt->execute()) {
        throw new Exception('Failed to insert/update data: ' . $stmt->error);
    }

    $affectedRows = $stmt->affected_rows;
    logDebug("Statement executed. Affected rows: $affectedRows");

    $stmt->close();
    $conn->close();
    logDebug("Database connection closed");

    $output = ob_get_clean(); // Get the buffered content and clear the buffer
    if (!empty($output)) {
        logDebug("Unexpected output: " . $output);
    }

    if ($affectedRows > 0) {
        sendResponse('success', 'Payload pushed successfully', ['debug_output' => $output]);
    } else {
        sendResponse('warning', 'No rows were affected. Data might already be up to date.', ['debug_output' => $output]);
    }

} catch (Exception $e) {
    $output = ob_get_clean(); // Get the buffered content and clear the buffer
    logDebug("Error: " . $e->getMessage() . "\nUnexpected output: " . $output);
    sendResponse('error', $e->getMessage(), ['debug_output' => $output]);
}
?>