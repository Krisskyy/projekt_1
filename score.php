<?php
$conn = new mysqli('localhost', 'root', '', 'surveydb');
$conn->set_charset('utf8');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['score'])) {
        if (filter_var($_POST['score'], FILTER_VALIDATE_INT) !== false) {
            $stmt = $conn->prepare("INSERT INTO scores (score_count, user_id) VALUES (?, ?)");
            $user_id = 1;
            $stmt->bind_param("ii", $_POST['score'], $user_id);

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header('Location: quest_survey.html');
                exit;
            } else {
                echo "Błąd: " . $conn->error;
            }
        } else {
            echo "Punktacja musi być liczbą całkowitą.";
        }
    } else {
        echo "Brak przesłanej punktacji.";
    }
}
?>