<?php
session_start();
include 'db.php'; // Database connection

// Trim all posted data
$_POST = array_map('trim', $_POST);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if the user is trying to login
    if (isset($_POST['login'])) {
        // Login logic
        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?'); // Use $conn
            $stmt->bind_param("s", $_POST['username']); // Bind parameters for safety
            $stmt->execute();
            $result = $stmt->get_result(); // Get the result set

            if ($row = $result->fetch_assoc()) { // Fetch the associative array
                if (password_verify($_POST['password'], $row['password'])) {
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['user'] = 'Welcome, ' . $row['username'] . '!';
                    header('Location: index.php'); // Redirect to dashboard/home
                    exit();
                } else {
                    $_SESSION['error'] = 'Invalid password.';
                }
            } else {
                $_SESSION['error'] = "That username doesn't exist.";
            }
        } else {
            $_SESSION['error'] = 'Please fill in both the fields.';
        }
    }

    // Check if the user is trying to register
    if (isset($_POST['register'])) {
        // Registration logic
        if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
            $_SESSION['error'] = 'Please fill in all the fields.';
        } elseif (strlen($_POST['password']) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters.';
        } elseif ($_POST['password'] !== $_POST['confirm_password']) {
            $_SESSION['error'] = 'Passwords must match.';
        } else {
            // Check if username already exists
            $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
            $stmt->bind_param("s", $_POST['username']);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            if ($count > 0) {
                $_SESSION['error'] = 'Username already exists.';
            } else {
                // Insert new user
                $stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
                $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt->bind_param("sss", $_POST['username'], $_POST['email'], $hashedPassword);
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Account created successfully.';
                    header('Location: L&R.php'); // Redirect to the same page after successful registration
                    exit();
                } else {
                    $_SESSION['error'] = 'There was a problem creating your account.';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Login & Registration</title>
    <link rel="icon" href="favicon\check.png" type="image/x-icon">
</head>
<body>
<div class="wrapper">
  <div class="title-text">
    <div class="title login">Login Form</div>
    <div class="title signup">Signup Form</div>
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
    <div class="slide-controls">
      <input type="radio" name="slide" id="login" checked>
      <input type="radio" name="slide" id="signup">
      <label for="login" class="slide login">Login</label>
      <label for="signup" class="slide signup">Signup</label>
      <div class="slider-tab"></div>
    </div>
    <div class="form-inner">
      <!-- Login Form -->
      <form action="L&R.php" method="POST" class="login">
        <div class="field">
          <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="field">
          <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="pass-link">
          <a href="forgot_password.php">Forgot password?</a>
        </div>
        <div class="field btn">
          <div class="btn-layer"></div>
          <input type="submit" name="login" value="Login">
        </div>
        <div class="signup-link">
          Not a member? <a href="#">Signup now</a>
        </div>
      </form>

      <!-- Signup Form -->
      <form action="L&R.php" method="POST" class="signup">
        <div class="field">
          <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="field">
          <input type="email" name="email" placeholder="Email Address" required>
        </div>
        <div class="field">
          <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="field">
          <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </div>
        <div class="field btn">
          <div class="btn-layer"></div>
          <input type="submit" name="register" value="Signup">
        </div>
      </form>
    </div>
  </div>
</div>

<script>
    const loginText = document.querySelector(".title-text .login");
    const loginForm = document.querySelector("form.login");
    const loginBtn = document.querySelector("label.login");
    const signupBtn = document.querySelector("label.signup");
    const signupLink = document.querySelector("form .signup-link a");

    signupBtn.onclick = () => {
        loginForm.style.marginLeft = "-50%";
        loginText.style.marginLeft = "-50%";
    };

    loginBtn.onclick = () => {
        loginForm.style.marginLeft = "0%";
        loginText.style.marginLeft = "0%";
    };

    signupLink.onclick = () => {
        signupBtn.click();
        return false;
    };
</script>
</body>
</html>
