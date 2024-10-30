<?php
session_start();
include 'db.php'; // Include your database connection

// Trim all posted data
$_POST = array_map('trim', $_POST);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['username']) && !empty($_POST['new_password'])) {
        // Hash the new password
        $hashedPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        // Update the user's password
        $stmt = $conn->prepare('UPDATE users SET password = ? WHERE username = ?');
        $stmt->bind_param("ss", $hashedPassword, $_POST['username']);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Your password has been updated successfully.';
        } else {
            $_SESSION['error'] = 'There was an error updating your password.';
        }
    } else {
        $_SESSION['error'] = 'Please fill in both fields.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="favicon\check.png" type="image/x-icon">
    <title>Forgot Password</title>
</head>
<body>
<div class="wrapper">
    <div class="title-text">
        <div class="title forgot-password">Forgot Password</div>
    </div>
    
    <?php
    // Display messages (error or success)
    if (isset($_SESSION['error'])) {
        echo "<p style='color: red; text-align: center;'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo "<p style='color: green; text-align: center;'>" . $_SESSION['success'] . "</p>";
        unset($_SESSION['success']);
    }
    ?>

    <div class="form-container">
        <div class="form-inner">
            <!-- Forgot Password Form -->
            <form action="forgot_password.php" method="POST" class="forgot-password">
                <div class="field">
                    <input type="text" name="username" placeholder="Enter your username" required>
                </div>
                <div class="field">
                    <input type="password" name="new_password" placeholder="New Password" required>
                </div>
                <div class="field btn">
                    <div class="btn-layer"></div>
                    <input type="submit" name="reset_password" value="Reset Password">
                </div>
                <div class="signup-link">
                    Remembered your password? <a href="L&R.php">Login now</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
