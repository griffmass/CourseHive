<?php
session_start();
require '../../vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->quickstart;
$coursesCollection = $db->courses; 

if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please log in to edit a course.'); window.location.href = 'login.php';</script>";
    exit;
}

if (!isset($_GET['courseId'])) {
    echo "<script>alert('No course selected for editing.'); window.location.href = 'mycourses.php';</script>";
    exit;
}
$courseId = $_GET['courseId'];
$course = $coursesCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($courseId)]);

if (!$course) {
    echo "<script>alert('Course not found.'); window.location.href = 'mycourses.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $content = htmlspecialchars($_POST['content']);

    $imagePath = $course['image']; 
    if (isset($_FILES['course-image']) && $_FILES['course-image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imagePath = $uploadDir . basename($_FILES['course-image']['name']);
        move_uploaded_file($_FILES['course-image']['tmp_name'], $imagePath);
    }

    $updatedData = [
        'title' => $title,
        'description' => $description,
        'content' => $content,
        'image' => $imagePath,
    ];

    try {
        $coursesCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($courseId)],
            ['$set' => $updatedData]
        );
        echo "<script>alert('Course updated successfully!'); window.location.href = 'mycourses.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Failed to update course: {$e->getMessage()}');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h1>Edit Course</h1>
        <form method="POST" enctype="multipart/form-data">
            <label for="title">Course Title:</label>
            <input type="text" id="title" name="title" required value="<?php echo $course['title']; ?>">

            <label for="description">Course Description:</label>
            <textarea id="description" name="description" required><?php echo $course['description']; ?></textarea>

            <label for="course-image">Course Image:</label>
            <input type="file" id="course-image" name="course-image" accept="image/*">
            <img src="<?php echo $course['image']; ?>" alt="Current Course Image" width="100">

            <label for="content">Course Content:</label>
            <textarea id="content" name="content" required><?php echo $course['content']; ?></textarea>

            <div class="button-row">
                <button type="submit">Update Course</button>
                <button type="button" onclick="window.location.href='mycourses.php';">Cancel</button>
            </div>
        </form>


    </div>

    </script>

    <style>

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }
    body {
        display: flex;
        min-height: 100vh;
        background-color: #f8f9fa;
    }

    .main-content {
        flex: 1;
        padding: 20px;
    }

    .header {
        margin-bottom: 20px;
    }
    .header h1 {
        font-size: 24px;
        color: #333;
    }

    #course-form-section {
        margin-top: 20px;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }
    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    label {
        font-size: 16px;
        color: #333;
    }
    input[type="text"], input[type="file"], textarea {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        color: #333;
    }
    #content-editor {
        min-height: 150px;
    }
    .editor-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f4f4f4;
        margin-bottom: 15px;
    }

    .editor-toolbar button, .editor-toolbar select {
        padding: 8px 12px;
        border: none;
        background-color: #0056b3;
        color: white;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        transition: background-color 0.3s ease;
    }

    .editor-toolbar button:hover, .editor-toolbar select:hover {
        background-color: #003d80;
    }

    .editor-toolbar button i {
        font-size: 16px;
    }

    .editor-toolbar select {
        font-size: 14px;
        background-color: #fff;
        color: #333;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .editor-toolbar button:hover[title]::after, .editor-toolbar select:hover[title]::after {
        opacity: 1;
    }

    .editor {
        font-size: 14px;
        color: #333;
        border: 1px solid #ddd;
        border-radius: 5px;
        min-height: 150px;
        padding: 10px;
        position: relative;
    }
    .editor::before {
        content: "Write your post content here...";
        color: #555;
        position: absolute;
        top: 10px;
        left: 10px;
        pointer-events: none;
    }
    .editor:empty::before {
        display: block;
    }
    .editor:not(:empty)::before {
        display: none;
    }
    button[type="submit"] {
        padding: 10px;
        background-color: #0056b3;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    button[type="submit"]:hover {
        background-color: #003d80;
    }

    .button-row {
        display: flex;
        gap: 10px;
        justify-content: flex-start;
        margin-top: 10px;
    }

    button {
        padding: 10px 15px;
        background-color: #0056b3;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background-color: #003d80;
    }

    button[type="button"] {
        background-color: #6c757d;
    }

    button[type="button"]:hover {
        background-color: #5a6268;
    }
</style>

</body>
</html>
