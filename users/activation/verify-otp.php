<?php
session_start();
require_once '../../vendor/autoload.php';

$databaseConnection = new MongoDB\Client;
$connection = $databaseConnection->quickstart;

$email = isset($_GET['email']) ? $_GET['email'] : null;

$tempCollection = $connection->temp_users;
$tempUser = $tempCollection->findOne(['email' => $email]);

$expirationTime = isset($tempUser['expires_at']) ? $tempUser['expires_at']->toDateTime()->getTimestamp() * 1000 : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify'])) {
    $enteredOtp = $_POST['otp'];

    $currentTime = new MongoDB\BSON\UTCDateTime();
    if ($tempUser && $tempUser['otp'] == $enteredOtp && $tempUser['expires_at'] > $currentTime) {
        $userCollection = $connection->users;
        $userCollection->insertOne([
            "username" => $tempUser['username'],
            "email" => $tempUser['email'],
            "password" => $tempUser['password']
        ]);

        $tempCollection->deleteOne(['email' => $email]);

        header("Location: ../index.php?status=success");
        exit();
    } else {
        $error = "Invalid or expired OTP. Please try again.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend'])) {
    if ($tempUser) {
        $newOtp = rand(100000, 999999);
        $expiration = new MongoDB\BSON\UTCDateTime((time() + 125) * 1000);

        $tempCollection->updateOne(
            ['email' => $email],
            ['$set' => ['otp' => $newOtp, 'expires_at' => $expiration]]
        );

        $successMessage = "A new OTP has been sent to your email.";
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="../../styles/verify.css">
    <style>
        #resendButton {
            display: none;
        }
    </style>
    <script>
        function updateCountdown(expirationTime) {
            var countdownInterval;

            function updateTimer() {
                var now = new Date().getTime();
                var distance = expirationTime - now;

                if (distance <= 0) {
                    document.getElementById("countdown").innerHTML = "OTP expired";
                    clearInterval(countdownInterval);
                    document.getElementById("resendButton").style.display = "block";

                    fetch("remove-expired-otp.php?email=<?php echo $email; ?>")
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log("Expired OTP removed successfully");
                            } else {
                                console.error("Failed to remove expired OTP");
                            }
                        });
                } else {
                    var minutes = Math.floor(distance / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000); 

                    document.getElementById("countdown").innerHTML = `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
                }
            }

            updateTimer(); 
            countdownInterval = setInterval(updateTimer, 1000);
        }


        document.addEventListener('DOMContentLoaded', function () {
            var expirationTime = <?php echo $expirationTime; ?>;
            if (expirationTime > 0) {
                updateCountdown(expirationTime);
            } else {
                document.getElementById("resendButton").style.display = "block";
            }
        });
    </script>
</head>
<body>
    <div class="form-container">
        <h2 class="form-title">Verify OTP</h2>

        <?php if (isset($error)) { ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php } ?>

        <?php if (isset($successMessage)) { ?>
            <p class="success-message"><?php echo $successMessage; ?></p>
        <?php } ?>

        <form action="" method="POST" class="otp-form">
            <input type="text" name="otp" placeholder="Enter OTP" required class="otp-input">
            <input type="submit" name="verify" value="Verify OTP" class="submit-button">
        </form>

        <p class="countdown-text">Remaining time: <span id="countdown" class="countdown-timer">2m 0s</span></p>

        <!-- Resend OTP Button -->
        <form action="resend-otp.php" method="GET" class="resend-form">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
            <input type="submit" id="resendButton" name="resend" value="Resend OTP" class="resend-button">
        </form>
    </div>
</body>
</html>
