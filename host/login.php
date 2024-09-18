<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'db_config.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Create a database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM `1.users_and_accounts` WHERE email_id = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email_id'];
                $_SESSION['account_id'] = $user['account_id']; // Assuming 'account_id' is a field in your users table

                header("Location: /host/dist/index.php"); // Redirect to dashboard or home page
                exit();
            } else {
                $error_message = "Invalid email or password";
            }
        } else {
            $error_message = "Invalid email or password";
        }

        $stmt->close();
    } else {
        $error_message = "Database error: " . $conn->error;
    }

    $conn->close();
}
?>

 <!DOCTYPE html>
<html lang="en">
<head>
    <title>Login Test</title>
</head>
<body>
    <?php
    if (!empty($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html> 