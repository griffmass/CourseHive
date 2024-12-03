<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

require_once '../../vendor/autoload.php';

$databaseConnection = new MongoDB\Client;

$connection = $databaseConnection->quickstart;

$userCollection = $connection->users;

$userEmail = $_SESSION['email'];

$fetch = $userCollection->findOne(['email' => $userEmail]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../../styles/profile.css">
</head>
<body>

 

    <div class="container">
        <h2>Profile</h2>
        
        <table>
            <tr>
                <td>Username:</td>
                <td><?php echo isset($fetch['username']) ? htmlspecialchars($fetch['username']) : 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><?php echo isset($fetch['email']) ? htmlspecialchars($fetch['email']) : 'N/A'; ?></td>
            </tr>
            <tr>
                <td><a href="edit-profile.php?id=<?php echo isset($fetch['_id']) ? $fetch['_id'] : ''; ?>">Edit</a></td>
                <td><a href="logout.php">Logout</a></td>
            </tr>
        </table>
    </div>
</body>
</html>
