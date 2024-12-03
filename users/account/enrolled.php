<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php");
    exit;
}

require '../../vendor/autoload.php'; 

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->quickstart;

$enrollmentsCollection = $db->enrollments;
$coursesCollection = $db->courses;

$user = $_SESSION['user'];
$enrollments = $enrollmentsCollection->find(['user' => $user]);

$enrolledCourses = [];
foreach ($enrollments as $enrollment) {
    $course = $coursesCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($enrollment['course_id'])]);
    if ($course) {
        $enrolledCourses[] = [
            '_id' => (string)$course['_id'],
            'title' => $course['title'] ?? 'No Title',
            'description' => $course['description'] ?? 'No Description',
            'image' => $course['image'] ?? 'default-image.png',
            'creator' => $course['creator'] ?? 'Unknown',
            'enrolled_at' => isset($enrollment['enrolled_at']) && $enrollment['enrolled_at'] instanceof MongoDB\BSON\UTCDateTime
                ? $enrollment['enrolled_at']->toDateTime()->format('Y-m-d H:i:s')
                : 'N/A',
        ];
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrolled Courses</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>Your Enrolled Courses</h1>
        </div>

        <section class="article-list" id="enrolled-courses">
            <?php if (empty($enrolledCourses)): ?>
                <p>You are not enrolled in any courses.</p>
            <?php else: ?>
            <?php endif; ?>
        </section>
    </div>

    <script>
    const enrolledCourses = <?php echo json_encode($enrolledCourses, JSON_PRETTY_PRINT); ?>;
    const enrolledCoursesList = document.getElementById('enrolled-courses');

    if (enrolledCourses.length > 0) {
        enrolledCourses.forEach(course => {
            const articleItem = document.createElement('div');
            articleItem.classList.add('article-item');
            articleItem.setAttribute('data-id', course._id);

            const articleContent = `
                <div class="article-item" data-id="${course._id}">
                    <img src="${course.image}" alt="Course Image">
                    <div class="article-content">
                        <h3>${course.title}</h3>
                        <p class="meta">${course.creator} | Enrolled: ${course.enrolled_at}</p>
                        <p>${course.description}</p>
                    </div>
                    <div class="article-actions">
                        <button class="btn unenroll-btn">Unenroll</button>
                    </div>
                </div>
            `;

            articleItem.innerHTML = articleContent;
            enrolledCoursesList.appendChild(articleItem);
        });
    }

    document.addEventListener('click', async function (event) {
        if (event.target.classList.contains('unenroll-btn')) {
            const courseId = event.target.closest('.article-item').dataset.id;
            const confirmation = confirm("Are you sure you want to unenroll from this course?");
            if (confirmation) {
                try {
                    const response = await fetch('unenroll.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ courseId }),
                    });
                    const result = await response.json();
                    if (result.success) {
                        alert("You have successfully unenrolled from the course!");
                        location.reload();
                    } else {
                        alert("Failed to unenroll. Please try again.");
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert("An error occurred. Please try again.");
                }
            }
        }
    });
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
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
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
        }

        .article-actions {
            padding: 10px 15px;
            background-color: #f9f9f9;
            border-top: 1px solid #eaeaea;
        }

        .btn.unenroll-btn {
            background-color: #f44336;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .btn.unenroll-btn:hover {
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
