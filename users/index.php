<?php
session_start();
require_once '../vendor/autoload.php';

// Initialize MongoDB connection
$databaseConnection = new MongoDB\Client;
$connection = $databaseConnection->quickstart;
$userCollection = $connection->users;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = sha1($_POST['password']);

    // Query MongoDB for user credentials with correct field names
    $fetch = $userCollection->findOne(["email" => $email, "password" => $password]);

    if ($fetch) {
        $_SESSION['email'] = $fetch['email'];
        $_SESSION['logged_in'] = true;
        header('Location: account/dashboard.php');
        exit();
    } else {
        $error_message = "User Not Found";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourseHive - Login</title>
    <link rel="stylesheet" href="../styles/logreg.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="form-container">
        <span class="close-btn" onclick="goToHomepage()"><i class="fa-solid fa-xmark"></i></span>
    
        <h2>Login to CourseHive</h2>
        
        <?php if (isset($error_message)): ?>
            <center><h4 style="color: red;"><?php echo $error_message; ?></h4></center>
        <?php endif; ?>

        <form action="index.php" id="loginform" class="input-login" method="POST">
            <input type="email" placeholder="Email" name="email" id="email" class="input-field" required>
            <input type="password" placeholder="Password" name="password" id="password" class="input-field" required>
            <label>
                <input type="checkbox" class="checkbox">
                <span>Remember Me</span>
            </label>
            <input type="submit" name="login" id="login" value="Login" class="submit-btn"><br><br>
        </form>
        <p>Don't have an account? <a href="activation/signup.php">Create account</a></p>
        <p>Forgot your password? <a href="resetting/forgot-password.php">Reset password</a></p>
    </div>

    <script>
        function goToHomepage() {
            window.location.href = "homepage.php";
        }
    </script>
</body>
</html>
