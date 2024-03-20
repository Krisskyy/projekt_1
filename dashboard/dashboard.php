<?php
function showData(){
    $conn = new mysqli('localhost', 'root', '', 'surveydb');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset('utf8');
    $search = '';
    if(isset($_POST['search'])){
        $search = $_POST['search'];
    }
    
    if(!$search){
        $q = "SELECT
        users.userID,
        CONCAT(users.name, ' ', users.surname) AS full_name,
        DATE_FORMAT(scores.date, '%d.%m.%Y, %H:%i') AS date,
        scores.score,
        (SELECT COUNT(*) + 1
            FROM scores AS s
            WHERE s.score > scores.score
            OR (s.score = scores.score AND s.date < scores.date)) AS rank,
        scores.win,
        CONCAT(address.street, ' ', address.housenumber,
                IF(address.apartmentnumber <> '', CONCAT('/', address.apartmentnumber), ''),
                ' ', address.zipcode, ' ', address.location) AS full_address,
        surveynps.score AS nps_score,
        surveynps.comment AS nps_comment
        FROM
            users
        LEFT JOIN
            scores ON users.userID = scores.userID
        LEFT JOIN
            address ON users.addressID = address.addressID
        LEFT JOIN
            surveynps ON scores.surveyID = surveynps.surveyID";

        $stmt = $conn->prepare($q);
        $stmt->execute();

        $stmt->bind_result($userID, $full_name, $date, $score, $rank, $win, $full_address, $nps_score, $nps_comment);

        while($stmt->fetch()) {
            echo "<tr>";
            echo "<td><button class=\"wrapped\"></button></td>";
            echo "<td>" . htmlspecialchars($userID) . "</td>";
            echo "<td>" . htmlspecialchars($full_name) . "</td>";
            echo "<td>" . htmlspecialchars($date) . "</td>";
            echo "<td>" . htmlspecialchars($score) . "</td>";
            echo "<td>" . htmlspecialchars($rank) . "</td>";
            echo "<td>";
            echo ($win == 1) ? "<div class=\"stm-true\"><img src=\"./dashboardMedia/true.svg\" alt=\"true\">TAK</div>" : "<div class=\"stm-false\"><img src=\"./dashboardMedia/false.svg\" alt=\"false\">NIE</div>";
            echo "</td>";
            echo ($full_address != NULL) ? "<td>" . htmlspecialchars($full_address) . "</td>" : "<td> - </td>";
            echo "</tr>";
            echo "<tr class=\"none\">";
            echo "<td style=\"height: 120px;\" colspan=\"8\">";
            echo "<div class=\"info-container\">";
            echo "<div class=\"info-header\">";
            echo "<h2>Czas rozwiązania <br> konkursu <p>00:06:12</p></h2>";
            echo "<h2>Data przesłania <br> danych teleadresowych <p> ". htmlspecialchars($date) ."</p></h2>";
            echo ($nps_score != NULL) ? "<h2>Ocena NPS <p> ". htmlspecialchars($nps_score) ."</p></h2>" : "<h2>Ocena NPS <p> - </p></h2>";
            echo ($nps_comment != NULL) ? "<h2>Komentarz <p class=\"comment\">". htmlspecialchars($nps_comment) ."</p></h2>" : "<h2>Komentarz <p class=\"comment\"> - </p></h2>";
            echo "</div>";
            echo "</div>";
            echo "</td>";
            echo "</tr>";
        }

    $stmt->close();
    } else{
        $q = "SELECT
        users.userID,
        CONCAT(users.name, ' ', users.surname) AS full_name,
        DATE_FORMAT(scores.date, '%d.%m.%Y, %H:%i') AS date,
        scores.score,
        (SELECT COUNT(*) + 1
            FROM scores AS s
            WHERE s.score > scores.score
            OR (s.score = scores.score AND s.date < scores.date)) AS rank,
        scores.win,
        CONCAT(address.street, ' ', address.housenumber,
                IF(address.apartmentnumber <> '', CONCAT('/', address.apartmentnumber), ''),
                ' ', address.zipcode, ' ', address.location) AS full_address,
        surveynps.score AS nps_score,
        surveynps.comment AS nps_comment
        FROM
            users
        LEFT JOIN
            scores ON users.userID = scores.userID
        LEFT JOIN
            address ON users.addressID = address.addressID
        LEFT JOIN
            surveynps ON scores.surveyID = surveynps.surveyID 
        WHERE users.name LIKE '$search%' OR users.surname LIKE '$search%' OR concat(users.name, ' ', users.surname) LIKE '$search%';";

        $stmt = $conn->prepare($q);
        $stmt->execute();

        $stmt->bind_result($userID, $full_name, $date, $score, $rank, $win, $full_address, $nps_score, $nps_comment);

        while($stmt->fetch()) {
            echo "<tr>";
            echo "<td><button class=\"wrapped\"></button></td>";
            echo "<td>" . htmlspecialchars($userID) . "</td>";
            echo "<td>" . htmlspecialchars($full_name) . "</td>";
            echo "<td>" . htmlspecialchars($date) . "</td>";
            echo "<td>" . htmlspecialchars($score) . "</td>";
            echo "<td>" . htmlspecialchars($rank) . "</td>";
            echo "<td>";
            echo ($win == 1) ? "<div class=\"stm-true\"><img src=\"./dashboardMedia/true.svg\" alt=\"true\">TAK</div>" : "<div class=\"stm-false\"><img src=\"./dashboardMedia/false.svg\" alt=\"false\">NIE</div>";
            echo "</td>";
            echo ($full_address != NULL) ? "<td>" . htmlspecialchars($full_address) . "</td>" : "<td> - </td>";
            echo "</tr>";
            echo "<tr class=\"none\">";
            echo "<td style=\"height: 120px;\" colspan=\"8\">";
            echo "<div class=\"info-container\">";
            echo "<div class=\"info-header\">";
            echo "<h2>Czas rozwiązania <br> konkursu <p>00:06:12</p></h2>";
            echo "<h2>Data przesłania <br> danych teleadresowych <p> ". htmlspecialchars($date) ."</p></h2>";
            echo ($nps_score != NULL) ? "<h2>Ocena NPS <p> ". htmlspecialchars($nps_score) ."</p></h2>" : "<h2>Ocena NPS <p> - </p></h2>";
            echo ($nps_comment != NULL) ? "<h2>Komentarz <p class=\"comment\">". htmlspecialchars($nps_comment) ."</p></h2>" : "<h2>Komentarz <p class=\"comment\"> - </p></h2>";
            echo "</div>";
            echo "</div>";
            echo "</td>";
            echo "</tr>";
        }

        $stmt->close();
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>

    </style>
</head>
<body>
    <div class="container">
        <header class="padding">
            <img class="logo" src="./dashboardMedia/logo.svg" alt="logo">
            <div class="profile">
                <div>
                    <h3>Karolina Nowak</h3>
                    <h4>Panel raportowy</h4>
                </div>
                <img src="./dashboardMedia//ikona log out.svg" alt="Log Out">
            </div>
        </header>

        <div class="page padding">
            <h1>Wiosna Nadchodzi - Konkurs Mediaflex</h1>

            <div class="boxes">
                <div class="box">
                    <h5>Data rozpoczęcia konkursu</h5>
                    <h6>06.10.2022, 10:00</h6>
                </div>
                <div class="box">
                    <h5>Data zakończenia konkursu</h5>
                    <h6>30.10.2022, 10:00</h6>
                </div>
                <div class="box">
                    <h5>Limit udziałów</h5>
                    <h6>1</h6>
                </div>
                <div class="box">
                    <h5>Maksymalna ilość punktów do uzyskania</h5>
                    <h6>3</h6>
                </div>
                <div class="box">
                    <h5>Liczba zwycięzców</h5>
                    <h6>20</h6>
                </div>
            </div>

            <section class="content">
                <div class="content-header">
                    <button>Lista uczestników</button>
                    <button>Lista zwycięzców</button>
                </div>
                <div class="content-search">
                    <h3>Wyszukaj użytkownika</h3>
                    <div class="input-button">
                        <form action="dashboard.php" method="post">
                            <input type="text" name="search" id="search">
                            <button type="submit" class="search-button"></button>
                        </form>
                    </div>
                </div>
                <div class="rest">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>LP</th>
                                <th>Imię i nazwisko</th>
                                <th>Data wzięcia <br>udziału w konkursie</th>
                                <th>Ilość uzyskanych <br> punktów</th>
                                <th>Zajęte <br>miejsce</th>
                                <th>Wygrana</th>
                                <th>Sposób dostarczenia <br>nagrody</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php echo showData(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="content-footer">
                    <div class="footer-left">
                        <h5>Pozycje od 1 do 10 z 270 łącznie</h5>
                        <h6>
                            Pokaż 
                            <select name="pages" id="pages">
                                <option value="opt1">10</option>
                                <option value="opt2">25</option>
                                <option value="opt3">50</option>
                            </select>
                            pozycji
                        </h6>
                    </div>
                    <div class="footer-right">
                        <button><h3>Poprzednia</h3></button>
                        <button><p class="p-checked">1</p></button>
                        <button><p>2</p></button>
                        <button><p>3</p></button>
                        <button><p>4</p></button>
                        <button><p>5</p></button>
                        <button><h4>Następna</h4></button>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <script src="./dashboard_script.js"></script>
</body>
</html>