<?php
// Start of PHP file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourseHive - Welcome</title>
    <link rel="stylesheet" href="../styles/homepage.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="logo">CourseHive</div>
            <nav>
                <ul class="nav-links">
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="index.php" class="btn">Login</a></li>
                    <li><a href="activation/signup.php" class="btn">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <h1>Welcome to CourseHive</h1>
            <p>Create, share, and learn from a variety of courses.</p>
            <a href="#courses" class="btn">Explore Courses</a>
        </div>
    </section>

    <!-- Featured Courses Section -->
    <section id="courses" class="featured-courses">
        <h2>Featured Courses</h2>
        <div class="course-container">
            <div class="course-card">
                <h3>Web Development</h3>
                <p>Learn HTML, CSS, JavaScript, and more!</p>
                <a href="#" class="btn">Start Learning</a>
            </div>
            <div class="course-card">
                <h3>Data Science</h3>
                <p>Master data analysis and visualization.</p>
                <a href="#" class="btn">Start Learning</a>
            </div>
            <div class="course-card">
                <h3>Graphic Design</h3>
                <p>Create stunning visuals and graphics.</p>
                <a href="#" class="btn">Start Learning</a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section id="why-choose-us">
        <h2>Why Choose CourseHive?</h2>
        <ul>
            <li>Easy course creation</li>
            <li>Interactive quizzes</li>
            <li>Collaborative learning</li>
        </ul>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> CourseHive. All rights reserved.</p>
            <div class="social-links">
                <a href="#"><img src="icon-facebook.png" alt="Facebook"></a>
                <a href="#"><img src="icon-twitter.png" alt="Twitter"></a>
                <a href="#"><img src="icon-linkedin.png" alt="LinkedIn"></a>
            </div>
        </div>
    </footer>
</body>
</html>

<?php
// End of PHP file
?>
