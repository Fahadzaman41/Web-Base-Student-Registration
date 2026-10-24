<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli("localhost", "root", "", "student_course_db");
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $exception) {
    error_log("Database connection failed: " . $exception->getMessage());
    http_response_code(500);
    exit("Database connection failed. Check that MySQL is running and student_course_db exists.");
}
