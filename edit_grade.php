<?php
require_once 'config.php';

$student = $_GET['student'] ?? '';
$subject = $_GET['subject'] ?? '';

// Fetch student and subject IDs
$studentStmt = $pdo->prepare("SELECT id FROM students WHERE name = ?");
$studentStmt->execute([$student]);
$studentData = $studentStmt->fetch();

$subjectStmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_name = ?");
$subjectStmt->execute([$subject]);
$subjectData = $subjectStmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newStudentName = trim($_POST['student_name']);
    $newSubjectName = trim($_POST['subject_name']);
    $newGrade = intval($_POST['grade']);
    
    // Update the student's name (for all grades)
    if ($newStudentName !== $student) {
        $updateStudentStmt = $pdo->prepare("UPDATE students SET name = :new_name WHERE id = :student_id");
        $updateStudentStmt->execute([
            ':new_name' => $newStudentName,
            ':student_id' => $studentData['id']
        ]);
    }
    
    // Update the grade and subject for the selected student and subject
    $updateGradeStmt = $pdo->prepare("
        UPDATE grades 
        SET grade = :grade,
            student_id = (SELECT id FROM students WHERE name = :student_name),
            subject_id = (SELECT id FROM subjects WHERE subject_name = :subject_name)
        WHERE student_id = :student_id AND subject_id = :subject_id
    ");
    $updateGradeStmt->execute([
        ':grade' => $newGrade,
        ':student_name' => $newStudentName,
        ':subject_name' => $newSubjectName,
        ':student_id' => $studentData['id'],
        ':subject_id' => $subjectData['id']
    ]);
    
    // Redirect after update
    header("Location: index.php");
    exit;
}

// Fetch current grade
$stmt = $pdo->prepare("
    SELECT grade FROM grades 
    JOIN students ON grades.student_id = students.id 
    JOIN subjects ON grades.subject_id = subjects.id 
    WHERE students.name = ? AND subjects.subject_name = ?
");
$stmt->execute([$student, $subject]);
$currentGrade = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Grade</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="edit-container">
    <h2>Edit Grade for <?= htmlspecialchars($student) ?> - <?= htmlspecialchars($subject) ?></h2>
    <form method="post">
        <div class="form-group">
            <label for="student_name">Student Name</label>
            <input type="text" name="student_name" value="<?= htmlspecialchars($student) ?>" required>
        </div>

        <div class="form-group">
            <label for="subject_name">Subject</label>
            <input type="text" name="subject_name" value="<?= htmlspecialchars($subject) ?>" required>
        </div>

        <div class="form-group">
            <label for="grade">Grade</label>
            <input type="number" name="grade" min="1" max="10" value="<?= htmlspecialchars($currentGrade) ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn save-btn">💾 Save</button>
            <a href="index.php" class="btn cancel-btn">❌ Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
