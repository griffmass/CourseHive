<?php
require '../../vendor/autoload.php'; 

header('Content-Type: application/json');

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->quickstart; 
$coursesCollection = $db->courses; 

$requestPayload = json_decode(file_get_contents('php://input'), true);

if (isset($requestPayload['courseId'])) {
    $courseId = $requestPayload['courseId'];

    try {
        $result = $coursesCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($courseId)]);

        if ($result->getDeletedCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Course deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Course not found.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid course ID.']);
}
?>
