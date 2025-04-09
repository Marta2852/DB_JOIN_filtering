<?php

require_once 'config.php';

$studentFilter = isset($_GET['student']) ? $_GET['student'] : '';
$subjectFilter = isset($_GET['subject']) ? $_GET['subject'] : '';

$sql = "
    SELECT students.name AS student_name, subjects.subject_name, grades.grade
    FROM grades
    JOIN students ON grades.student_id = students.id
    JOIN subjects ON grades.subject_id = subjects.id
";

$conditions = [];
$params = [];

if (!empty($studentFilter)){
    $conditions[] = "students.name = :student";
    $params[':student'] = $studentFilter;
}
if (!empty($subjectFilter)){
    $conditions[] = "subjects.subject_name = :subject";
    $params[':subject'] = $subjectFilter;
}

if (!empty($conditions)){
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY students.name, subjects.subject_name ";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

$students = $pdo->query("SELECT name FROM students ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
$subjects = $pdo->query("SELECT subject_name FROM subjects ORDER BY subject_name")->fetchAll(PDO::FETCH_COLUMN)

?>