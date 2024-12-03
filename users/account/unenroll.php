<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../../vendor/autoload.php';

    $client = new MongoDB\Client("mongodb://localhost:27017");
    $db = $client->quickstart;

    $userEmail = $_SESSION['email']; 
    $enrollmentsCollection = $db->enrollments;

    $data = json_decode(file_get_contents('php://input'), true);
    $courseId = $data['courseId'];

    $deleteResult = $enrollmentsCollection->deleteOne([
        'user' => $_SESSION['username'],
        'course_id' => new MongoDB\BSON\ObjectId($courseId),
    ]);
    

    if ($deleteResult->getDeletedCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Not enrolled']);
    }
}
?>
