<?php
require_once __DIR__ . "/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sap = trim($_POST["sap_id"] ?? "");
    $code = trim($_POST["course_code"] ?? "");

    if ($sap === "" || $code === "") {
        $message = "Please select both a student and a course.";
    } else {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO student_courses (sap_id, course_code)
                 VALUES (?, ?)"
            );
            $stmt->bind_param("ss", $sap, $code);
            $stmt->execute();
            $stmt->close();
            $message = "Course allocated successfully!";
        } catch (mysqli_sql_exception $exception) {
            error_log("Could not allocate course: " . $exception->getMessage());
            $message = "Allocation failed. Check that the student and course exist and that this allocation is not already recorded.";
        }
    }
}

$students = $conn->query("SELECT sap_id, student_name FROM students ORDER BY student_name");
$courses = $conn->query("SELECT course_code, course_name, credit_hours FROM courses ORDER BY course_name");
$records = $conn->query(
    "SELECT s.sap_id, s.student_name, c.course_code, c.course_name, c.credit_hours
     FROM student_courses sc
     JOIN students s ON sc.sap_id = s.sap_id
     JOIN courses c ON sc.course_code = c.course_code
     ORDER BY s.student_name"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Allocation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<aside class="sidebar">
    <h2>Student<span>Portal</span></h2>
    <a href="index.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="courses.php">Courses</a>
    <a class="active" href="allocation.php">Course Allocation</a>
</aside>

<main class="main">
    <header class="topbar"><strong>Course Allocation</strong><span>Administrator</span></header>
    <section class="content">
        <h1>Allocate Course</h1>
        <p class="subtitle">Select a student and assign a course.</p>

        <?php if ($message !== ""): ?>
            <p class="message"><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></p>
        <?php endif; ?>

        <div class="panel">
            <h2>New Allocation</h2>
            <form method="post">
                <label for="sap_id">Select Student</label>
                <select id="sap_id" name="sap_id" required>
                    <option value="">Choose student</option>
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($student["sap_id"], ENT_QUOTES, "UTF-8") ?>">
                            <?= htmlspecialchars($student["sap_id"] . " - " . $student["student_name"], ENT_QUOTES, "UTF-8") ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="course_code">Select Course</label>
                <select id="course_code" name="course_code" required>
                    <option value="">Choose course</option>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($course["course_code"], ENT_QUOTES, "UTF-8") ?>">
                            <?= htmlspecialchars($course["course_code"] . " - " . $course["course_name"] . " (" . $course["credit_hours"] . " credits)", ENT_QUOTES, "UTF-8") ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit">Allocate Course</button>
            </form>
        </div>

        <div class="panel">
            <h2>Allocation Records</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>SAP ID</th>
                            <th>Student Name</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credit Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($record = $records->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($record["sap_id"], ENT_QUOTES, "UTF-8") ?></td>
                            <td><?= htmlspecialchars($record["student_name"], ENT_QUOTES, "UTF-8") ?></td>
                            <td><?= htmlspecialchars($record["course_code"], ENT_QUOTES, "UTF-8") ?></td>
                            <td><?= htmlspecialchars($record["course_name"], ENT_QUOTES, "UTF-8") ?></td>
                            <td><?= (int) $record["credit_hours"] ?></td>
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
