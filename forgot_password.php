<?php 
session_start();
include('db.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];

    // Check if the username exists in the users table
    $query = "SELECT email FROM users WHERE username = ?";
    $statement = $conn->prepare($query);
    $statement->bind_param("s", $username);
    $statement->execute();
    $result = $statement->get_result();

    if ($result->num_rows > 0) {
        // Fetch the email associated with the username
        $row = $result->fetch_assoc();
        $email = $row['email'];

        // Generate OTP and store it in the session
        $otp = rand(100000, 999999);
        $otpExpiry = time() + 300; // OTP valid for 5 minutes
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = $otpExpiry;
        $_SESSION['username'] = $username;

        // Update OTP in the database for the user
        $updateQuery = "UPDATE users SET otp = ?, otp_expiry = ? WHERE username = ?";
        $statement = $conn->prepare($updateQuery);
        $expiryDateTime = date('Y-m-d H:i:s', $otpExpiry); 
        $statement->bind_param("sss", $otp, $expiryDateTime, $username);
        $statement->execute();

        // Send the OTP via email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = ''; // Your Gmail address
            $mail->Password = ''; // Your Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('enter the sender email ', 'TODO-Password Reset');
            $mail->addAddress($email); // Send to user's email based on the username

            $mail->isHTML(true);
            $mail->Subject = 'Your Password Reset OTP';
            $mail->Body = "<p style='font-size: 20px;'>Your OTP for password reset is: <strong>" . $otp . "</strong></p>";

            $mail->send();
            header("Location: verify_otp.php");
            exit();
        } catch (Exception $e) {
            $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "Username not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="favicon\check.png" type="image/x-icon">
</head>
<body>
<div class="wrapper mt-5">
    <div class="title-text">
        <div class="title forgot-password">Forgot Password</div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center" role="alert" style="color:red; ">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <div class="form-inner">
            <form method="POST" action="" class="forgot-password">
                <div class="field">
                    <input type="text"  id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="field btn">
          <div class="btn-layer"></div>
          <input type="submit"  value="Send OTP">
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
