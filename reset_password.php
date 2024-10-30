<?php 
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    $username = $_SESSION['username'];

    // Update the user's password in the database
    $query = "UPDATE users SET password = ? WHERE username = ?";
    $statement = $conn->prepare($query);
    $statement->bind_param("ss", $newPassword, $username);
    $statement->execute();

    // Clear session variables related to OTP
    unset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['username']);

    // Redirect to login page after password reset
    header("Location: L&R.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="icon" href="favicon\check.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
<div class="wrapper mt-5">
    <div class="title-text">
        <div class="title reset-password">Reset Password</div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center" role="alert" style="color:red; ">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <div class="form-inner">
            <form method="POST" action="">
                <div class="field ">
                    <label for="new_password" >New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter your new password" required>
                </div>
                <br>
                <div class="field btn">
          <div class="btn-layer"></div>
          <input type="submit"  value="Reset Password">
        </div>
                <div class="signup-link text-center">
                    Remembered your password? <a href="L&R.php">Login now</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
