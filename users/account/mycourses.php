<?php
session_start();

require '../../vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->quickstart;
$coursesCollection = $db->courses;

if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please log in to view your courses.'); window.location.href = 'login.php';</script>";
    exit;
}

$username = $_SESSION['username'];
$courses = $coursesCollection->find(['creator' => $username]);

$coursesArray = [];
foreach ($courses as $course) {
    $coursesArray[] = [
        '_id' => (string)$course['_id'], 
        'title' => $course['title'] ?? 'No Title', 
        'description' => $course['description'] ?? 'No Description', 
        'content' => $course['content'] ?? '', 
        'image' => $course['image'] ?? 'default-image.png', 
        'creator' => $course['creator'] ?? 'Unknown', 
        'created_at' => isset($course['created_at']) ? $course['created_at']->toDateTime()->format('Y-m-d H:i:s') : 'Unknown Date', 
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - CourseHive</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>My Courses</h1>
        </div>

        <section class="article-list" id="article-list">
        </section>
    </div>

    <script>
    const courses = <?php echo json_encode($coursesArray, JSON_PRETTY_PRINT); ?>;
    const articleList = document.getElementById('article-list');

    courses.forEach(course => {
        const articleItem = document.createElement('div');
        articleItem.classList.add('article-item');

        const articleContent = `
        <div class="article-item" data-course-id="${course._id}">
            <img src="${course.image}" alt="Course Image">
            <div class="article-content">
                <h3>${course.title}</h3>
                <p class="meta">${course.creator} | ${course.created_at}</p>
                <p>${course.description}</p>
            </div>
            <div class="article-actions">
                <button class="btn-edit" onclick="editCourse('${course._id}')">Edit</button>
                <button class="btn-delete" onclick="deleteCourse('${course._id}')">Delete</button>
            </div>
        </div>
    `;


        articleItem.innerHTML = articleContent;
        articleList.appendChild(articleItem);
    });

    function editCourse(courseId) {
        const confirmEdit = confirm("Do you want to edit this course?");
        if (confirmEdit) {
            window.location.href = `edit-course-form.php?courseId=${courseId}`;
        }
    }


    function deleteCourse(courseId) {
    const confirmDelete = confirm(
        "Are you sure you want to delete this course? This action cannot be undone."
    );
    if (confirmDelete) {
        fetch(`delete-course.php`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ courseId: courseId }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert("Course deleted successfully.");
                    // Remove the deleted course card from the DOM
                    document.querySelector(`[data-course-id="${courseId}"]`).remove();
                } else {
                    alert("Failed to delete the course. Please try again.");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("An error occurred while deleting the course.");
            });
    }
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

        .article-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px; 
        }

        .article-item {
            display: flex;
            flex-direction: column;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .article-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .article-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .article-content {
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .article-content h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }

        .article-content .meta {
            font-size: 12px;
            color: #777;
            margin-bottom: 15px;
        }

        .article-content p {
            font-size: 14px;
            color: #555;
            line-height: 1.5;
            flex-grow: 1; 
        }

        .article-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 10px 15px;
            background-color: #f9f9f9;
            border-top: 1px solid #eaeaea;
        }

        .article-actions button {
            padding: 8px 12px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .btn-edit {
            background-color: #4caf50;
            color: white;
        }

        .btn-edit:hover {
            background-color: #45a049;
        }

        .btn-delete {
            background-color: #f44336;
            color: white;
        }

        .btn-delete:hover {
            background-color: #e53935;
        }

        @media (max-width: 768px) {
            .article-list {
                grid-template-columns: repeat(2, 1fr); 
            }
        }

        @media (max-width: 480px) {
            .article-list {
                grid-template-columns: 1fr; 
            }
        }
    </style>
</body>
</html>
