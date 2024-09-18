<?php
require_once 'db_config.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => ''];

function generateAccountId($conn) {
    $stmt = $conn->query("SELECT MAX(CAST(SUBSTRING(account_id, 5) AS UNSIGNED)) as max_id FROM `1.users_and_accounts` WHERE account_id LIKE 'SIGN%'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $max_id = $result['max_id'] ?? 2869;
    $next_id = max(2869, intval($max_id)) + 1;
    return 'SIGN' . $next_id;
}

function createUserFolders($account_id) {
    $base_path = __DIR__ . '/dist/user-content/';
    error_log("Attempting to create folders in: $base_path");

    if (!file_exists($base_path)) {
        throw new Exception("Base path does not exist: $base_path");
    }
    
    if (!is_writable($base_path)) {
        throw new Exception("Base path is not writable: $base_path");
    }

    $account_folder = $base_path . $account_id;
    $image_folder = $account_folder . '/user-images';
    $video_folder = $account_folder . '/user-videos';

    if (!file_exists($account_folder)) {
        if (!mkdir($account_folder, 0755, true)) {
            throw new Exception("Failed to create account folder. Error: " . error_get_last()['message']);
        }
    }
    if (!file_exists($image_folder)) {
        if (!mkdir($image_folder, 0755, true)) {
            throw new Exception("Failed to create image folder. Error: " . error_get_last()['message']);
        }
    }
    if (!file_exists($video_folder)) {
        if (!mkdir($video_folder, 0755, true)) {
            throw new Exception("Failed to create video folder. Error: " . error_get_last()['message']);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = $_POST['user_name'] ?? '';
    $email_id = $_POST['email_id'] ?? '';
    $company_country = $_POST['company_country'] ?? '';
    $password = $_POST['password'] ?? '';
    $terms = isset($_POST['terms']) ? 1 : 0;

    if (empty($user_name) || empty($email_id) || empty($company_country) || empty($password)) {
        $response['message'] = 'All fields are required.';
    } elseif (!filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format.';
    } elseif ($terms !== 1) {
        $response['message'] = 'You must agree to the Terms & Conditions.';
    } else {
        try {
            $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Start transaction
            $conn->beginTransaction();

            // Check if email already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM `1.users_and_accounts` WHERE email_id = :email_id");
            $stmt->bindParam(':email_id', $email_id);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Email address already in use.');
            }

            $account_id = generateAccountId($conn);

            $stmt = $conn->prepare("INSERT INTO `1.users_and_accounts` (account_id, user_name, email_id, company_country, password, terms) VALUES (:account_id, :user_name, :email_id, :company_country, :password, :terms)");

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt->bindParam(':account_id', $account_id);
            $stmt->bindParam(':user_name', $user_name);
            $stmt->bindParam(':email_id', $email_id);
            $stmt->bindParam(':company_country', $company_country);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':terms', $terms);

            $stmt->execute();

            // Create user folders
            createUserFolders($account_id);

            // Commit transaction
            $conn->commit();

            $response['status'] = 'success';
            $response['message'] = 'Contact Signager Team @ 1800-191-007 <br>for account activation! <br><br> Your Account ID: ' . $account_id;
        } catch(Exception $e) {
            // Rollback transaction on error
            $conn->rollBack();
            $response['message'] = 'Registration failed: ' . $e->getMessage();
            error_log('Registration error: ' . $e->getMessage());
        }
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);