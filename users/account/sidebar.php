<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .sidebar {
    width: 250px;
    background-color: #0056b3;
    color: #fff;
    display: flex;
    flex-direction: column;
    padding: 20px;
}

.sidebar h2 {
    margin-bottom: 20px;
    text-align: center;
}

.sidebar ul {
    list-style: none;
}

.sidebar ul li {
    margin: 15px 0;
}

.sidebar ul li a {
    text-decoration: none;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border-radius: 5px;
    transition: background 0.3s ease;
}

.sidebar ul li a:hover {
    background-color: #003d80;
}

    </style>
</head>
<body>
    <!-- sidebar.php -->
<aside class="sidebar">
    <h2>CourseHive</h2>
    <ul>
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="mycourses.php"><i class="fas fa-book"></i> My Courses</a></li>
        <li><a href="course-form.php"><i class="fas fa-plus"></i> Create Course</a></li>
        <li><a href="enrolled.php"><i class="fas fa-graduation-cap"></i> Enrolled Courses</a></li>
        <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
        <li><a href="profile.php"><i class="fas fa-user-cog"></i> Profile</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</aside>

</body>
</html>