<?php
require_once __DIR__ . "/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sap = trim($_POST["sap_id"] ?? "");
    $name = trim($_POST["student_name"] ?? "");
    $father = trim($_POST["father_name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if ($sap === "" || $name === "" || $father === "" || $phone === "") {
        $message = "Please complete all student details.";
    } else {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO students (sap_id, student_name, father_name, phone)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $sap, $name, $father, $phone);
            $stmt->execute();
            $stmt->close();
            $message = "Student registered successfully!";
        } catch (mysqli_sql_exception $exception) {
            error_log("Could not register student: " . $exception->getMessage());
            $message = "Student registration failed. The SAP ID may already exist.";
        }
    }
}

$result = $conn->query("SELECT sap_id, student_name, father_name, phone FROM students ORDER BY sap_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<aside class="sidebar">
    <h2>Student<span>Portal</span></h2>
    <a href="index.php">Dashboard</a>
    <a class="active" href="students.php">Students</a>
    <a href="courses.php">Courses</a>
    <a href="allocation.php">Course Allocation</a>
</aside>

<main class="main">
    <header class="topbar"><strong>Student Management</strong><span>Administrator</span></header>
    <section class="content">
        <h1>Student Registration</h1>
        <p class="subtitle">Enter student details and save the record.</p>

        <?php if ($message !== ""): ?>
            <p class="message"><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></p>
        <?php endif; ?>

        <div class="panel">
            <h2>Student Details</h2>
            <form method="post">
                <label for="sap_id">SAP ID</label>
                <input id="sap_id" type="text" name="sap_id" required>

                <label for="student_name">Student Name</label>
                <input id="student_name" type="text" name="student_name" required>

                <label for="father_name">Father Name</label>
                <input id="father_name" type="text" name="father_name" required>

                <label for="phone">Phone Number</label>
                <input id="phone" type="tel" name="phone" required>

                <button type="submit">Save Student</button>
            </form>
        </div>

        <div class="panel">
            <h2>Registered Students</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>SAP ID</th>
                            <th>Student Name</th>
                            <th>Father Name</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row["sap_id"], ENT_QUOTES, "UTF-8") ?></td>
                            <td><?= htmlspecialchars($row["student_name"], ENT_QUOTES, "UTF-8") ?></td>
                            <td><?= htmlspecialchars($row["father_name"], ENT_QUOTES, "UTF-8") ?></td>
                            <td><?= htmlspecialchars($row["phone"], ENT_QUOTES, "UTF-8") ?></td>
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
