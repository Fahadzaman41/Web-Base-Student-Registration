<?php
require_once __DIR__ . "/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = strtoupper(trim($_POST["course_code"] ?? ""));
    $name = trim($_POST["course_name"] ?? "");
    $credits = filter_input(INPUT_POST, "credit_hours", FILTER_VALIDATE_INT);

    if ($code === "" || $name === "" || $credits === false || $credits === null || $credits < 1 || $credits > 12) {
        $message = "Enter a course code and name, and credit hours from 1 to 12.";
    } else {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO courses (course_code, course_name, credit_hours)
                 VALUES (?, ?, ?)"
            );
            $stmt->bind_param("ssi", $code, $name, $credits);
            $stmt->execute();
            $stmt->close();
            $message = "Course added successfully!";
        } catch (mysqli_sql_exception $exception) {
            error_log("Could not add course: " . $exception->getMessage());
            $message = "Course could not be added. The course code may already exist.";
        }
    }
}

$result = $conn->query("SELECT course_code, course_name, credit_hours FROM courses ORDER BY course_code");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<aside class="sidebar">
    <h2>Student<span>Portal</span></h2>
    <a href="index.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a class="active" href="courses.php">Courses</a>
    <a href="allocation.php">Course Allocation</a>
</aside>

<main class="main">
    <header class="topbar"><strong>Course Management</strong><span>Administrator</span></header>
    <section class="content">
        <h1>Manage Courses</h1>
        <p class="subtitle">Add courses and their credit hours.</p>

        <?php if ($message !== ""): ?>
            <p class="message"><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></p>
        <?php endif; ?>

        <div class="panel">
            <h2>Add New Course</h2>
            <form method="post">
                <label for="course_code">Course Code</label>
                <input id="course_code" type="text" name="course_code" required>

                <label for="course_name">Course Name</label>
                <input id="course_name" type="text" name="course_name" required>

                <label for="credit_hours">Credit Hours</label>
                <input id="credit_hours" type="number" name="credit_hours" min="1" max="12" required>

                <button type="submit">Save Course</button>
            </form>
        </div>

        <div class="panel">
            <h2>Course List</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credit Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row["course_code"], ENT_QUOTES, "UTF-8") ?></td>
                            <td><?= htmlspecialchars($row["course_name"], ENT_QUOTES, "UTF-8") ?></td>
                            <td><?= (int) $row["credit_hours"] ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
</body>
</html>
