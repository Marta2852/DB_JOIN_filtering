<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentName = trim($_POST['student_name'] ?? '');
    $subjectName = trim($_POST['subject_name'] ?? '');
    $grade = intval($_POST['grade'] ?? 0); // cast grade to int

    if ($studentName && $subjectName && $grade >= 1 && $grade <= 10) {

        // Get or insert student
        $stmt = $pdo->prepare("SELECT id FROM students WHERE name = ?");
        $stmt->execute([$studentName]);
        $studentId = $stmt->fetchColumn();

        if (!$studentId) {
            $stmt = $pdo->prepare("INSERT INTO students (name) VALUES (?)");
            $stmt->execute([$studentName]);
            $studentId = $pdo->lastInsertId();
        }

        // Get or insert subject
        $stmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_name = ?");
        $stmt->execute([$subjectName]);
        $subjectId = $stmt->fetchColumn();

        if (!$subjectId) {
            $stmt = $pdo->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
            $stmt->execute([$subjectName]);
            $subjectId = $pdo->lastInsertId();
        }

        // Insert the grade
        $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_id, grade) VALUES (?, ?, ?)");
        $stmt->execute([$studentId, $subjectId, $grade]);

        // Redirect back
        header("Location: index.php");
        exit;
    } else {
        echo "Missing or invalid data.";
    }
} else {
    echo "Invalid request method.";
}
