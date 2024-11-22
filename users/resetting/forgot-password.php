<?php
session_start();
require_once '../../vendor/autoload.php';

$databaseConnection = new MongoDB\Client;
$connection = $databaseConnection->quickstart;
$userCollection = $connection->users;
$tempResetCollection = $connection->temp_reset;

$error_message = '';
$username = '';
$email = '';

function sendOtp($recipientEmail, $otp, $subject) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'shamizen43@gmail.com';
        $mail->Password = 'dfhlthmvefmryakg';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

        $mail->setFrom('shamizen43@gmail.com', 'CourseHive');
        $mail->addAddress($recipientEmail);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = "Your OTP for resetting your password is: <b>$otp</b>.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $email = trim($_POST['email']);
    $user = $userCollection->findOne(['email' => $email]);

    if ($user) {
        $username = $user['username'];
    } else {
        $error_message = "No account found for this email.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $email = trim($_POST['email']);
    $user = $userCollection->findOne(['email' => $email]);

    if ($user) {
        $otp = random_int(100000, 999999);
        $expires_at = new MongoDB\BSON\UTCDateTime((time() + 125) * 1000);

        try {

            $tempResetCollection->deleteMany(['email' => $email]);


            $tempResetCollection->insertOne([
                'email' => $email,
                'otp' => $otp,
                'expires_at' => $expires_at,
            ]);


            if (sendOtp($email, $otp, "Reset Password")) {
                $_SESSION['reset_email'] = $email;
                header('Location: verify-reset.php');
                exit();
            } else {
                $error_message = "Failed to send OTP. Please try again later.";
            }
        } catch (Exception $e) {
            error_log("Error while processing reset request: " . $e->getMessage());
            $error_message = "An error occurred. Please try again.";
        }
    } else {
        $error_message = "No account found for this email.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/logreg.css">
    <title>Forgot Password</title>
</head>
<body>
    <div class="form-container">
        <span class="close-btn" onclick="goToHomepage()"><i class="fa-solid fa-xmark"></i></span>
        
        <h2>Forgot Password</h2>

        <?php if (!empty($error_message)): ?>
            <center><h4 style="color: red;"><?php echo $error_message; ?></h4></center>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="email" placeholder="Search Account by Email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" class="input-field" required>
            <input type="submit" name="search" value="Search" class="submit-btn">
        </form>

        <?php if (!empty($username)): ?>
            <p>Account found! Username: <b><?php echo htmlspecialchars($username); ?></b></p>
            <form action="" method="POST">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="submit" name="confirm" value="Send OTP" class="submit-btn">
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
