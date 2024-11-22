<?php
session_start();
require_once '../../vendor/autoload.php';

$databaseConnection = new MongoDB\Client;
$connection = $databaseConnection->quickstart;
$tempResetCollection = $connection->temp_reset;

$email = $_SESSION['reset_email'] ?? '';
if (!$email) {
    header("Location: forgot-password.php");
    exit();
}

$resetRecord = $tempResetCollection->findOne(['email' => $email]);

$error_message = '';
$success_message = '';

function isValidOTP($otp) {
    return preg_match('/^\d{6}$/', $otp);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify'])) {
    $otp = trim($_POST['otp']);

    if (!isValidOTP($otp)) {
        $error_message = "Invalid OTP format. Please enter a 6-digit OTP.";
    } elseif (!$resetRecord || !isset($resetRecord['otp'])) {
        $error_message = "OTP record not found. Please try resending the OTP.";
    } else {
        $storedOtp = (string)$resetRecord['otp']; 
        $inputOtp = (string)$otp;

        if ($storedOtp === $inputOtp) {
            try {
                $tempResetCollection->deleteOne(['email' => $email]);
                header('Location: reset-password.php');
                exit();
            } catch (Exception $e) {
                error_log("Error deleting OTP record: " . $e->getMessage());
                $error_message = "An error occurred while processing your request. Please try again.";
            }
        } else {
            $error_message = "Invalid OTP.";
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
    <title>Verify OTP</title>
</head>
<body>
    <div class="form-container">
        <span class="close-btn" onclick="goToHomepage()"><i class="fa-solid fa-xmark"></i></span>
        
        <h2>Verify OTP</h2>

        <?php if ($error_message): ?>
            <center><h4 style="color: red;"><?php echo htmlspecialchars($error_message); ?></h4></center>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="text" name="otp" id="otp" placeholder="Enter OTP" class="input-field" required>
            <input type="submit" name="verify" value="Submit" class="submit-btn">
        </form>
    </div>

    <script>
        function goToHomepage() {
            window.location.href = "homepage.php";
        }
    </script>
</body>
</html>
