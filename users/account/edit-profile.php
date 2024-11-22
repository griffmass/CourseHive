<?php
session_start();

require_once '../../vendor/autoload.php';
use MongoDB\BSON\ObjectID;

$databaseConnection = new MongoDB\Client;
$connection = $databaseConnection->quickstart;
$userCollection = $connection->users;

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

$userEmail = $_SESSION['email'];

$fetch = $userCollection->findOne(['email' => $userEmail]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $phoneNo = $_POST['phoneNo'];
    $hidden_id = $_POST['hidden_id'];

    try {
        $objectId = new ObjectID($hidden_id);

        $data = [
            '$set' => [
                "Firstname" => $fname,
                "Lastname" => $lname,
                "Email" => $email,
                "Phone Number" => $phoneNo,
            ]
        ];

        $update = $userCollection->updateOne(['_id' => $objectId], $data);

        if ($update->getModifiedCount() > 0) {
            header('Location: profile.php');
            exit();
        } else {
            $message = "<h4 style='color: red;'>Update Failed: No changes detected.</h4>";
        }
    } catch (Exception $e) {
        $message = "<h4 style='color: red;'>Invalid user ID.</h4>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../../styles/profile.css">
</head>
<body>
    <div class="container">
        <?php if ($fetch): ?>
            <h3>Edit Profile</h3>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="fname">First Name:</label>
                    <input type="text" value="<?php echo htmlspecialchars($fetch['Firstname'] ?? ''); ?>" name="fname" id="fname" required>
                </div>
                <div class="form-group">
                    <label for="lname">Last Name:</label>
                    <input type="text" value="<?php echo htmlspecialchars($fetch['Lastname'] ?? ''); ?>" name="lname" id="lname" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" value="<?php echo htmlspecialchars($fetch['email']); ?>" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="phoneNo">Phone Number:</label>
                    <input type="text" value="<?php echo htmlspecialchars($fetch['Phone Number'] ?? ''); ?>" name="phoneNo" id="phoneNo" required>
                </div>
                <input type="hidden" name="hidden_id" value="<?php echo $fetch['_id']; ?>">
                <input type="submit" name="update" value="Update Info">
            </form>
            <a href="profile.php">Back to Profile</a>
            <?php if (isset($message)) echo $message; ?>
        <?php else: ?>
            <h4 style="color: red;">User not found.</h4>
        <?php endif; ?>
    </div>
</body>
</html>
