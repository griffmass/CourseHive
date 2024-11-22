<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CourseHive</title>
    <link rel="stylesheet" href="../../styles/dashboard.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>CourseHive</h2>
            </div>
            <ul class="sidebar-nav">
                <li><a href="view_courses.php">View Available Courses</a></li>
                <li><a href="my_courses.php">My Courses</a></li>
                <li><a href="create_course.php">Create Course</a></li>
                <li><a href="enrolled_courses.php">View Enrolled Courses</a></li>
                <li><a href="notifications.php">View Notifications</a></li>
                <li><a href="profile.php">Manage Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="content">
            <h1>Welcome to your Dashboard</h1>
            <p>Here you can view and manage your courses, notifications, and profile settings.</p>
        </main>
    </div>
</body>
</html>
