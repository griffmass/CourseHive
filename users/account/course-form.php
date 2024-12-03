<?php
session_start();

require '../../vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->quickstart;
$coursesCollection = $db->courses;

if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please log in to create a course.'); window.location.href = 'login.php';</script>";
    exit;
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $content = htmlspecialchars($_POST['content']);

    $imagePath = null;
    if (isset($_FILES['course-image']) && $_FILES['course-image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imagePath = $uploadDir . basename($_FILES['course-image']['name']);
        move_uploaded_file($_FILES['course-image']['tmp_name'], $imagePath);
    }

    $courseData = [
        'title' => $title,
        'description' => $description,
        'content' => $content,
        'image' => $imagePath,
        'creator' => $username,
        'created_at' => new MongoDB\BSON\UTCDateTime(),
    ];

    try {
        $coursesCollection->insertOne($courseData);
        echo "<script>alert('Course Created Successfully!');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Failed to create course: {$e->getMessage()}');</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourseHive Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php' ?>

    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1>Create a New Course</h1>
        </div>

        <section id="course-form-section">
        <form id="course-form" method="POST" enctype="multipart/form-data" onsubmit="prepareContentForSubmission(event)">
            <label for="title">Course Title:</label>
            <input type="text" id="title" name="title" required placeholder="Enter course title here...">

            <label for="description">Course Description:</label>
            <textarea id="description" name="description" required placeholder="Enter course description here..."></textarea>

            <label for="course-image">Upload Course Image:</label>
            <input type="file" id="course-image" name="course-image" accept="image/*">

            <div class="editor-toolbar">
                <select onchange="changeFontSize(this.value)" title="Change Font Size">
                    <option value="" disabled selected>Font Size</option>
                    <option value="1">Small</option>
                    <option value="3">Normal</option>
                    <option value="5">Large</option>
                </select>
                <button type="button" onclick="formatText('bold')" title="Bold"><i class="fas fa-bold"></i></button>
                <button type="button" onclick="formatText('italic')" title="Italic"><i class="fas fa-italic"></i></button>
                <button type="button" onclick="formatText('underline')" title="Underline"><i class="fas fa-underline"></i></button>
                <button type="button" onclick="formatText('justifyLeft')" title="Align Left"><i class="fas fa-align-left"></i></button>
                <button type="button" onclick="formatText('justifyCenter')" title="Align Center"><i class="fas fa-align-center"></i></button>
                <button type="button" onclick="formatText('justifyRight')" title="Align Right"><i class="fas fa-align-right"></i></button>
                <button type="button" onclick="formatText('insertUnorderedList')" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                <button type="button" onclick="insertImage()" title="Insert Image"><i class="fas fa-image"></i></button>
                <button type="button" onclick="insertLink()" title="Insert Link"><i class="fas fa-link"></i></button>
            </div>

            <div id="content-editor" contenteditable="true" class="editor"></div>
            <input type="hidden" id="content" name="content">

                <button type="submit">Create Course</button>
        </form>
    </section>

    <script>
        function formatText(command) {
            document.execCommand(command, false, null);
        }

        function changeFontSize(size) {
            document.execCommand("fontSize", false, size);
        }

        function insertImage() {
            const url = prompt("Enter the image URL:");
            if (url) document.execCommand("insertImage", false, url);
        }

        function insertLink() {
            const url = prompt("Enter the URL:");
            if (url) document.execCommand("createLink", false, url);
        }

        function prepareContentForSubmission(event) {
            event.preventDefault();
            const editorContent = document.getElementById('content-editor').innerHTML;
            document.getElementById('content').value = editorContent;
            document.getElementById('course-form').submit();

        }
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
    </style>

</body>
</html>
