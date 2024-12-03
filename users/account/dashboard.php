<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php");
    exit;
}

require '../../vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->quickstart;
$coursesCollection = $db->courses;

$currentUser = $_SESSION['username'];

$courses = $coursesCollection->find();

$coursesArray = [];
foreach ($courses as $course) {
    $isEnrolled = false;

    $enrollmentCollection = $db->enrollments;
    $enrollment = $enrollmentCollection->findOne(['user' => $currentUser, 'course_id' => $course['_id']]);
    
    if ($enrollment) {
        $isEnrolled = true; 
    }

    $coursesArray[] = [
        '_id' => (string)$course['_id'], 
        'title' => htmlspecialchars($course['title'] ?? 'No Title'),
        'description' => htmlspecialchars($course['description'] ?? 'No Description'),
        'content' => htmlspecialchars($course['content'] ?? ''),
        'image' => htmlspecialchars($course['image'] ?? 'default-image.png'),
        'creator' => htmlspecialchars($course['creator'] ?? 'Unknown'),
        'created_at' => isset($course['created_at']) ? $course['created_at']->toDateTime()->format('Y-m-d H:i:s') : 'Unknown Date',
        'enrolled' => $isEnrolled, 
    ];
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
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <div class="search-bar">
                <input type="text" placeholder="Search...">
                <button><i class="fas fa-search"></i></button>
            </div>
        </div>

        <div class="metrics">
            <div class="metric-card">
                <h3><?php echo count($coursesArray); ?></h3>
                <p>Courses Created</p>
            </div>
            <div class="metric-card">
                <h3>50</h3>
                <p>Courses Enrolled</p>
            </div>
        </div>

        <section class="article-list" id="article-list">
        </section>
    </div>

    <script>
const courses = <?php echo json_encode($coursesArray, JSON_PRETTY_PRINT); ?>;
const currentUser = "<?php echo htmlspecialchars($_SESSION['username']); ?>";
const articleList = document.getElementById('article-list');

courses.forEach(course => {
    const articleItem = document.createElement('div');
    articleItem.classList.add('article-item');
    articleItem.setAttribute('data-id', course._id);

    const actions = [];

    if (course.creator === currentUser) {
        actions.push('<button class="btn edit-btn">Edit</button>');
        actions.push('<button class="btn delete-btn">Delete</button>');
    } else {
        if (course.enrolled) {
            actions.push('<button class="btn enroll-btn" disabled>Enrolled</button>');
            actions.push('<button class="btn enroll-btn">Enroll</button>');  
        }
    }

    const articleContent = `
    <div class="article-item" data-id="${course._id}">
        <img src="${course.image}" alt="Course Image">
        <div class="article-content">
            <h3>${course.title}</h3>
            <p class="meta">${course.creator} | ${course.created_at}</p>
            <p>${course.description}</p>
        </div>
        <div class="article-actions">
            ${actions.join('')}
        </div>
    </div>
    `;

    articleItem.innerHTML = articleContent;
    articleList.appendChild(articleItem);
});

document.addEventListener('click', async function (event) {
    if (event.target.classList.contains('enroll-btn')) {
        const courseId = event.target.closest('.article-item').dataset.id;
        const confirmation = confirm("Are you sure you want to enroll in this course?");
        if (confirmation) {
            try {
                const response = await fetch('enroll.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ courseId })
                });
                const result = await response.json();
                if (result.success) {
                    alert("You have successfully enrolled in the course!");
                    event.target.textContent = "Enrolled"; 
                    event.target.disabled = true;  
                } else {
                    alert("Failed to enroll. Please try again.");
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            color: #333;
        }
        .header .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header .search-bar input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header .search-bar button {
            padding: 10px;
            background-color: #0056b3;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .metrics {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .metric-card {
            flex: 1;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .metric-card h3 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #0056b3;
        }
        .metric-card p {
            font-size: 16px;
            color: #555;
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

        .btn.edit-btn {
            background-color: #4caf50;
            color: white;
        }

        .btn.edit-btn:hover {
            background-color: #45a049;
        }

        .btn.delete-btn {
            background-color: #f44336;
            color: white;
        }

        .btn.delete-btn:hover {
            background-color: #e53935;
        }

        .btn.enroll-btn {
            background-color: #008CBA;
            color: white;
        }

        .btn.enroll-btn:hover {
            background-color: #007b8d;
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
