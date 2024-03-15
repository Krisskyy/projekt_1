<?php
$conn = new mysqli('localhost', 'root', '', 'surveydb');
$conn->set_charset('utf8');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $score = $_POST['score'];
    $conn->query("INSERT INTO scores VALUES ('', '$score', 1)");
    $conn->close();
    header('Location: quest_survey.html');
    exit;
}
?>