<?php 
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $enteredOtp = $_POST['otp'];

    if (isset($_SESSION['otp']) && isset($_SESSION['otp_expiry'])) {
        $currentOtp = $_SESSION['otp'];
        $otpExpiry = $_SESSION['otp_expiry'];

        // Check if OTP is valid and not expired
        if (time() < $otpExpiry && $enteredOtp == $currentOtp) {
            // OTP is valid; redirect to reset password page
            header("Location: reset_password.php");
            exit();
        } else {
            $error = "Invalid or expired OTP.";
        }
    } else {
        $error = "No OTP found. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="favicon/check.png" type="image/x-icon">
</head>
<body>
<div class="wrapper mt-5">
   
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center" role="alert" style="color:red; ">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <div class="form-inner">
            <form method="POST" action="">
                <div class="field mb-3">
                    <label for="otp" class="form-label">Enter OTP</label>
                    <input type="text" id="otp" name="otp" placeholder="Enter your OTP" required>
                </div>
                <br>
              
                <div class="field btn">
                    <div class="btn-layer"></div>
                    <input type="submit" value="Verify OTP">
                </div>
                
                <div class="signup-link text-center">
                    Did you receive the OTP? <a href="forgot_password.php">Resend OTP</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
