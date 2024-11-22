<?php

session_start();
require '../../vendor/autoload.php';

$databaseConnection = new MongoDB\Client;
$connection = $databaseConnection->quickstart;

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
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;


        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );


        $mail->setFrom('shamizen43@gmail.com', 'CourseHive');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'CourseHive Email Verification - Resend OTP';
        $mail->Body    = "Your OTP for email verification is: $otp. It expires in 2 minutes.";
        $mail->AltBody = "Your OTP for email verification is: $otp. It expires in 2 minutes.";


        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) {
            file_put_contents('phpmailer_debug.log', "$str\n", FILE_APPEND);
        };

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $e->getMessage());
        return false;
    }
}

$email = isset($_GET['email']) ? $_GET['email'] : null;

if ($email) {
    $otp = rand(100000, 999999);
    $expirationTime = new MongoDB\BSON\UTCDateTime((time() + 125) * 1000);

    $tempCollection = $connection->temp_users;
    $tempUser = $tempCollection->findOne(['email' => $email]);

    if ($tempUser) {
        $updateResult = $tempCollection->updateOne(
            ['email' => $email],
            ['$set' => ['otp' => $otp, 'expires_at' => $expirationTime]]
        );

        if ($updateResult->getModifiedCount() > 0) {
            if (sendOTP($email, $otp)) {
                $_SESSION['success'] = "A new OTP has been sent to your email.";

                header("Location: verify-otp.php?email=" . urlencode($email));
                exit();
            } else {
                $_SESSION['error'] = "Error sending OTP. Please try again.";
            }
        } else {
            $_SESSION['error'] = "Error updating OTP. Please try again.";
        }
    } else {
        $_SESSION['error'] = "User not found. Please sign up again.";
    }
} else {
    $_SESSION['error'] = "Invalid request. Email address is required.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend OTP</title>
    <link rel="stylesheet" href="../../styles/logreg.css">
</head>
<body>
    <div class="form-container">
        <h2>Resend OTP</h2>

        <?php if (isset($_SESSION['success'])) { ?>
            <p style="color: green; text-align: center;"><?php echo $_SESSION['success']; ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php } elseif (isset($_SESSION['error'])) { ?>
            <p style="color: red; text-align: center;"><?php echo $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php } ?>

        <p><a href="verify-otp.php?email=<?php echo urlencode($email ?? ''); ?>">Return to OTP verification</a></p>
    </div>
</body>
</html>
