<?php
session_start();
require '../../vendor/autoload.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = json_decode(file_get_contents('php://input'), true);
    $courseId = $input['courseId'];

    if (!isset($_SESSION['username'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in.']);
        exit;
    }

    $client = new MongoDB\Client("mongodb://localhost:27017");
    $db = $client->quickstart;
    $enrollmentsCollection = $db->enrollments;

    $enrollment = [
        'user' => $_SESSION['username'],
        'course_id' => new MongoDB\BSON\ObjectId($courseId),
        'enrolled_at' => new MongoDB\BSON\UTCDateTime() 
    ];

    $existingEnrollment = $enrollmentsCollection->findOne([
        'user' => $_SESSION['username'],
        'course_id' => new MongoDB\BSON\ObjectId($courseId)
    ]);
    if ($existingEnrollment) {
        echo json_encode(['success' => false, 'message' => 'Already enrolled in this course.']);
        exit;
    }

    $enrollmentsCollection->insertOne($enrollment);
    echo json_encode(['success' => true, 'message' => 'Successfully enrolled.']);
    exit;
}
