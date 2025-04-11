<?php
require_once 'config.php';

$student = $_GET['student'] ?? '';
$subject = $_GET['subject'] ?? '';

if ($student && $subject) {
    $stmt = $pdo->prepare("
        DELETE FROM grades 
        WHERE student_id = (SELECT id FROM students WHERE name = :student) 
        AND subject_id = (SELECT id FROM subjects WHERE subject_name = :subject)
    ");
    $stmt->execute([
        ':student' => $student,
        ':subject' => $subject
    ]);
}

header("Location: index.php");
exit;
