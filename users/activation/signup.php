<?php
session_start();

require_once '../../vendor/autoload.php';

$databaseConnection = new MongoDB\Client;
$connection = $databaseConnection->quickstart;
$userCollection = $connection->users;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'shamizen43@gmail.com';
        $mail->Password = 'dfhlthmvefmryakg';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('shamizen43@gmail.com', 'CourseHive');
        $mail->addAddress($email, 'New User');

        $mail->isHTML(true);
        $mail->Subject = 'CourseHive Email Verification';
        $mail->Body    = "Your OTP for email verification is: $otp. It expires in 2 minutes.";
        $mail->AltBody = "Your OTP for email verification is: $otp. It expires in 2 minutes.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = sha1($_POST['password']);
    $otp = rand(100000, 999999);
    $expirationTime = time() + 125;

    $existingUser = $userCollection->findOne(['email' => $email]);
    if ($existingUser) {
        $error = "This email is already registered. Please use a different email.";
    } else {
        $tempCollection = $connection->temp_users;
        $tempCollection->insertOne([
            "username" => $username,
            "email" => $email,
            "password" => $password,
            "otp" => $otp,
            "created_at" => new MongoDB\BSON\UTCDateTime(),
            "expires_at" => new MongoDB\BSON\UTCDateTime($expirationTime * 1000)
        ]);

        $sendOtpResult = sendOTP($email, $otp);

        if ($sendOtpResult) {
            header("Location: verify-otp.php?email=$email&expires_at=$expirationTime");
            $_SESSION['username'] = $username;
            exit();
        } else {
            $error = "Error sending OTP. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourseHive - Signup</title>
    <link rel="stylesheet" href="../../styles/logreg.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="form-container">
        <span class="close-btn" onclick="goToHomepage()"><i class="fa-solid fa-xmark"></i></span>

        <h2>Create a CourseHive Account</h2>

        <?php if (isset($error)) { ?>
            <p style="color: red; text-align: center;"><?php echo $error; ?></p>
        <?php } ?>

        <form action="" method="POST" id="input-register" onsubmit="return validateRegister()">
            <input type="text" placeholder="Username" name="username" id="username" class="input-field" required>
            <input type="email" placeholder="Email" name="email" id="email" class="input-field" required>
            <input type="password" placeholder="Enter Password" name="password" id="password" class="input-field" required>
            <input type="password" placeholder="Confirm Password" name="confirmpassword" id="confirmpassword" class="input-field" required>
            <label>
                <input type="checkbox" id="termsCheckbox" class="checkbox" required>
                <span>I agree to the terms and conditions</span>
            </label>
            <input type="submit" name="signup" id="signup" value="Signup" class="submit-btn">
        </form>

        <p>Already have an account? <a href="../index.php">Login</a></p>
    </div>

    <script>
    function updateCountdown(expirationTime) {
        var countdownInterval;

        function updateTimer() {
            var now = new Date().getTime();
            var distance = expirationTime - now;

            if (distance <= 0) {
                document.getElementById("countdown").innerHTML = "OTP expired";
                clearInterval(countdownInterval);
            } else {
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById("countdown").innerHTML = minutes + "m " + seconds + "s ";
            }
        }

        countdownInterval = setInterval(updateTimer, 1000);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var expirationTime = <?php echo isset($_GET['expires_at']) ? $_GET['expires_at'] * 1000 : '0'; ?>;
        if (expirationTime > 0) {
            updateCountdown(expirationTime);
        }
    });

    function goToHomepage() {
        window.location.href = "../homepage.php";
    }
</script>

</body>
</html>
