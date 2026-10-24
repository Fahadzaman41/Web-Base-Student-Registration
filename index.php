<?php
require_once __DIR__ . "/db.php";

$students = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()["total"];
$courses = $conn->query("SELECT COUNT(*) AS total FROM courses")->fetch_assoc()["total"];
$allocations = $conn->query("SELECT COUNT(*) AS total FROM student_courses")->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<aside class="sidebar">
    <h2>Student<span>Portal</span></h2>
    <p class="menu-title">MAIN MENU</p>
    <a class="active" href="index.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="courses.php">Courses</a>
    <a href="allocation.php">Course Allocation</a>
</aside>

<main class="main">
    <header class="topbar">
        <strong>Academic Management</strong>
        <span>Administrator</span>
    </header>
    <section class="content">
        <p class="eyebrow">OVERVIEW</p>
        <h1>Dashboard</h1>
        <p class="subtitle">Welcome! Manage your students and courses here.</p>
        <div class="stats">
            <div class="card">
                <p>Registered Students</p>
                <h2><?= (int) $students ?></h2>
            </div>
            <div class="card">
                <p>Available Courses</p>
                <h2><?= (int) $courses ?></h2>
            </div>
            <div class="card">
                <p>Course Allocations</p>
                <h2><?= (int) $allocations ?></h2>
            </div>
        </div>
        <h2>Quick Actions</h2>
        <div class="actions">
            <a href="students.php">Register Student</a>
            <a href="courses.php">Add Course</a>
            <a href="allocation.php">Allocate Course</a>
        </div>
    </section>
</main>
</body>
</html>
