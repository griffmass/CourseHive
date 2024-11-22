<?php
session_start();
require_once '../../vendor/autoload.php';

$databaseConnection = new MongoDB\Client;
$connection = $databaseConnection->quickstart;
$userCollection = $connection->users;

$email = $_SESSION['reset_email'] ?? '';
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = sha1($_POST['new_password']);
    $confirmPassword = sha1($_POST['confirm_password']);

    $user = $userCollection->findOne(['email' => $email]);
    if (!$user) {
        $error_message = "User not found.";
    } else {
        $currentPassword = $user['password'];

        if ($newPassword === $currentPassword) {
            $error_message = "New password cannot be the same as the current password.";
        } else if ($newPassword === $confirmPassword) {
            $userCollection->updateOne(
                ['email' => $email],
                ['$set' => ['password' => $newPassword]]
            );

            unset($_SESSION['reset_email']);

            $success_message = "Password changed successfully.";
        } else {
            $error_message = "Passwords do not match.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/logreg.css">
    <title>Reset Password</title>
    <style>
        .button-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
        }

        .button-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <span class="close-btn" onclick="goToHomepage()"><i class="fa-solid fa-xmark"></i></span>
        
        <h2>Reset Password</h2>

        <?php if (!empty($error_message)): ?>
            <center><h4 style="color: red;"><?php echo htmlspecialchars($error_message); ?></h4></center>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <center><h4 style="color: green;"><?php echo htmlspecialchars($success_message); ?></h4></center>
            <center>
                <a href="../index.php" class="button-link">Back to Login</a>
            </center>
        <?php else: ?>
            <form action="" method="POST">
                <input type="password" name="new_password" id="new_password" placeholder="New Password" class="input-field" required>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" class="input-field" required>
                <input type="submit" value="Change Password" class="submit-btn">
            </form>
        <?php endif; ?>
    </div>

    <script>
        function goToHomepage() {
            window.location.href = "homepage.php";
        }
    </script>
</body>
</html>
