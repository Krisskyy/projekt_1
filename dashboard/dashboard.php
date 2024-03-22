<?php
class SurveyProcessor {
    private $conn;
    protected $positions = 5;

    function __construct() {
        $this->conn = new mysqli('localhost', 'root', '', 'surveydb');
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        $this->conn->set_charset('utf8');
    }

    function __destruct() {
        $this->conn->close();
    }

    public function showData() {
        $search = '';
        if(isset($_POST['search'])){
            $search = $_POST['search'];
        }

        if(!$search) {
            $positions = $this->getPositions();

            $between = $this->getBetweenValues($positions);

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
                        surveynps ON scores.surveyID = surveynps.surveyID WHERE users.userid BETWEEN ? AND ? LIMIT ?";

            $stmt = $this->conn->prepare($q);
            $stmt->bind_param("iii", $between['betweenOne'], $between['betweenTwo'], $positions);
        } else {
            $positions = $this->getPositions();
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
                    WHERE users.name LIKE ? OR users.surname LIKE ? OR concat(users.name, ' ', users.surname) LIKE ? LIMIT ?";

            $stmt = $this->conn->prepare($q);
            $search = "%$search%";
            $stmt->bind_param("sssi", $search, $search, $search, $positions);
        }

        $stmt->execute();
        $stmt->bind_result($userID, $full_name, $date, $score, $rank, $win, $full_address, $nps_score, $nps_comment);

        while($stmt->fetch()) {
            echo "<tr>";
            echo "<td><button class=\"wrapped\" type=\"button\"></button></td>";
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

    private function getPositions() {
        $items = 0;
        $q = "SELECT COUNT(scores.scoreID) AS total FROM scores;";
        $result = $this->conn->query($q);
        $row = $result->fetch_assoc();
        $total_pages = $row['total'];

        if(isset($_POST['positions'])) {
            if ($_POST['positions'] > $total_pages) {
                $items = $total_pages;
            } else {
                $items = $_POST['positions'];
            }

        } else {
            $items = 5;
        }
        return $items;
    }

    private function getBetweenValues($positions) {
        if ($positions == 5) {
            $betweenOne = 1;
            $betweenTwo = 5;
            if (isset($_POST['page'])) {
                $page = $_POST['page'];
                $betweenOne = 5 * ($page - 1) + 1;
                $betweenTwo = 5 * $page;
            }
        } elseif ($positions == 10) {
            $betweenOne = 1;
            $betweenTwo = 10;
            if (isset($_POST['page'])) {
                $page = $_POST['page'];
                $betweenOne = 10 * ($page - 1) + 1;
                $betweenTwo = 10 * $page;
                }
                } elseif ($positions == 15) {
                    $betweenOne = 1;
                    $betweenTwo = 15;
                if (isset($_POST['page'])) {
                    $page = $_POST['page'];
                    $betweenOne = 15 * ($page - 1) + 1;
                    $betweenTwo = 15 * $page;
                }
                }
            return array("betweenOne" => $betweenOne, "betweenTwo" => $betweenTwo);
        }
                
        public function pages() {
            $q = "SELECT COUNT(scores.scoreID) AS total FROM scores";
            $stmt = $this->conn->prepare($q);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $total_pages = intval($row['total']);
            $stmt->close();
            return $total_pages;
        }


        public function getPageNumber() {
            $conn = new mysqli('localhost', 'root', '', 'surveydb');
                
            $q = "SELECT COUNT(scores.scoreID) AS total FROM scores;";
            $result = $conn->query($q);
            $row = $result->fetch_assoc();
            $total_pages = $row['total'];
                
            $items = 0;
            if(isset($_POST['positions'])) {
                if ($_POST['positions'] > $total_pages) {
                    $items = $total_pages;
                } else {
                    if($_POST['positions'] == 5) {
                        $items = 5;
                    } else if($_POST['positions'] == 10) {
                        $items = 10;
                    } else if($_POST['positions'] == 15) {
                        $items = 15;
                    }
                }
            } else {
                $items = 5;
            }
                
            if(isset($_POST['page'])) {
                $page = intval($_POST['page']);
                $positions = isset($_POST['positions']) ? $_POST['positions'] : 5;
                $itemsPerPage = intval($positions);

                $items = $itemsPerPage * $page;
                if ($items > $total_pages) {
                    $items = $total_pages;
                }
            }
                
                
            return $items;
        }

        public  function getSecondPageNumber() {
            $itemsTwo = 1;
            $positions = $this->getPageNumber();
                
            if(isset($_POST['positions'])) {
                if($_POST['positions'] == 5) {
                    $itemsTwo = $positions - 4; 
                } else if($_POST['positions'] == 10) {
                    $itemsTwo = $positions - 9; 
                } else if($_POST['positions'] == 15) {
                    $itemsTwo = $positions - 14; 
                }
            }
            return $itemsTwo;
        }
    }

    class SurveyProcessorWinners {
        private $conn;
        protected $positionsWinners = 5;
    
        function __construct() {
            $this->conn = new mysqli('localhost', 'root', '', 'surveydb');
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
            $this->conn->set_charset('utf8');
        }
    
        function __destruct() {
            $this->conn->close();
        }
    
        public function showData() {
            $searchWinners = '';
            if(isset($_POST['searchWinners'])){
                $searchWinners = $_POST['searchWinners'];
            }
    
            if(!$searchWinners) {
                $positionsWinners = $this->getPositions();
    
                $between = $this->getBetweenValues($positionsWinners);
    
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
                            surveynps ON scores.surveyID = surveynps.surveyID WHERE scores.win = '1' AND users.userid BETWEEN ? AND ? LIMIT ?";
    
                $stmt = $this->conn->prepare($q);
                $stmt->bind_param("iii", $between['betweenOne'], $between['betweenTwo'], $positionsWinners);
            } else {
                $positionsWinners = $this->getPositions();
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
                        WHERE scores.win = '1' AND users.name LIKE ? OR users.surname LIKE ? AND scores.win = '1' OR concat(users.name, ' ', users.surname) LIKE ? AND scores.win = '1' LIMIT ?";
    
                $stmt = $this->conn->prepare($q);
                $searchWinners = "%$searchWinners%";
                $stmt->bind_param("sssi", $searchWinners, $searchWinners, $searchWinners, $positionsWinners);
            }
    
            $stmt->execute();
            $stmt->bind_result($userID, $full_name, $date, $score, $rank, $win, $full_address, $nps_score, $nps_comment);
    
            while($stmt->fetch()) {
                echo "<tr>";
                echo "<td><button class=\"wrapped\" type=\"button\"></button></td>";
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
    
        private function getPositions() {
            $items = 0;
            $q = "SELECT COUNT(scores.scoreID) AS total FROM scores;";
            $result = $this->conn->query($q);
            $row = $result->fetch_assoc();
            $total_pages = $row['total'];
    
            if(isset($_POST['positionsWinners'])) {
                if ($_POST['positionsWinners'] > $total_pages) {
                    $items = $total_pages;
                } else {
                    $items = $_POST['positionsWinners'];
                }
    
            } else {
                $items = 5;
            }
            return $items;
        }
    
        private function getBetweenValues($positionsWinners) {
            if ($positionsWinners == 5) {
                $betweenOne = 1;
                $betweenTwo = 5;
                if (isset($_POST['page'])) {
                    $page = $_POST['page'];
                    $betweenOne = 5 * ($page - 1) + 1;
                    $betweenTwo = 5 * $page;
                }
            } elseif ($positionsWinners == 10) {
                $betweenOne = 1;
                $betweenTwo = 10;
                if (isset($_POST['page'])) {
                    $page = $_POST['page'];
                    $betweenOne = 10 * ($page - 1) + 1;
                    $betweenTwo = 10 * $page;
                    }
                    } elseif ($positionsWinners == 15) {
                        $betweenOne = 1;
                        $betweenTwo = 15;
                    if (isset($_POST['page'])) {
                        $page = $_POST['page'];
                        $betweenOne = 15 * ($page - 1) + 1;
                        $betweenTwo = 15 * $page;
                    }
                    }
                return array("betweenOne" => $betweenOne, "betweenTwo" => $betweenTwo);
            }
                    
            public function pages() {
                $q = "SELECT COUNT(scores.scoreID) AS total FROM scores";
                $stmt = $this->conn->prepare($q);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $total_pages = intval($row['total']);
                $stmt->close();
                return $total_pages;
            }
    
    
            public function getPageNumber() {
                $conn = new mysqli('localhost', 'root', '', 'surveydb');
                    
                $q = "SELECT COUNT(scores.scoreID) AS total FROM scores;";
                $result = $conn->query($q);
                $row = $result->fetch_assoc();
                $total_pages = $row['total'];
                    
                $items = 0;
                if(isset($_POST['positionsWinners'])) {
                    if ($_POST['positionsWinners'] > $total_pages) {
                        $items = $total_pages;
                    } else {
                        if($_POST['positionsWinners'] == 5) {
                            $items = 5;
                        } else if($_POST['positionsWinners'] == 10) {
                            $items = 10;
                        } else if($_POST['positionsWinners'] == 15) {
                            $items = 15;
                        }
                    }
                } else {
                    $items = 5;
                }
                    
                if(isset($_POST['page'])) {
                    $page = intval($_POST['page']);
                    $positionsWinners = isset($_POST['positionsWinners']) ? $_POST['positionsWinners'] : 5;
                    $itemsPerPage = intval($positionsWinners);
                    
                    $items = $itemsPerPage * $page;
                    if ($items > $total_pages) {
                        $items = $total_pages;
                    }
                }
                    
                    
                return $items;
            }
    
            public  function getSecondPageNumber() {
                $itemsTwo = 1;
                $positionsWinners = $this->getPageNumber();
                    
                if(isset($_POST['positionsWinners'])) {
                    if($_POST['positionsWinners'] == 5) {
                        $itemsTwo = $positionsWinners - 4; 
                    } else if($_POST['positionsWinners'] == 10) {
                        $itemsTwo = $positionsWinners - 9; 
                    } else if($_POST['positionsWinners'] == 15) {
                        $itemsTwo = $positionsWinners - 14; 
                    }
                }
                return $itemsTwo;
            }
        }
    $surveyProcessor = new SurveyProcessor();
    $surveyProcessorWinners = new SurveyProcessorWinners();
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
            <form action="dashboard.php" method="post" id="pages">
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

                <section class="content all">
                    <div class="content-header">
                        <button type="button" class="activated-button" onclick="switchInformations('all')">Lista uczestników</button>
                        <button type="button" class="disabled-button" id="winnersButton" onclick="switchInformations('winners')">Lista zwycięzców</button>
                    </div>
                    <div class="content-search">
                        <h3>Wyszukaj użytkownika</h3>
                        <div class="input-button">
                                <input type="text" name="search" id="search">
                                <button type="submit" class="search-button"></button>
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
                            <?php $surveyProcessor->showData(); ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="content-footer">
                        <div class="footer-left">
                            <h5>Pozycje od <?php echo $surveyProcessor->getSecondPageNumber(); ?> do <?php echo $surveyProcessor->getPageNumber(); ?> z <?php echo $surveyProcessor->pages(); ?> łącznie</h5>
                            <h6>
                                Pokaż 
                                    <select id="positions" name="positions" onchange="submitForm()">
                                    <option value="5" <?php if(isset($_POST['positions']) && $_POST['positions'] == "5") echo "selected"; ?>>5</option>
                                    <option value="10" <?php if(isset($_POST['positions']) && $_POST['positions'] == "10") echo "selected"; ?>>10</option>
                                    <option value="15" <?php if(isset($_POST['positions']) && $_POST['positions'] == "15") echo "selected"; ?>>15</option>
                                    </select>
                                pozycji
                            </h6>
                        </div>
                        <div class="footer-right">
                            <button type="submit" name="previous">Poprzednia</button>
                            <button class="page" type="submit" name="page" value="1">1</button>
                            <button type="submit" name="page" value="2">2</button>
                            <button type="submit" name="page" value="3">3</button>
                            <button type="submit" name="page" value="4">4</button>
                            <button type="submit" name="page" value="5">5</button>
                            <button type="submit" name="next">Następna</button>
                        </div>
                    </div>
                </section>
            </form>

            
            <form action="dashboard.php" method="post" id="pagesWinners">
                <section class="content winners none">
                    <div class="content-header">
                        <button type="button" class="disabled-button" onclick="switchInformations('all')">Lista uczestników</button>
                        <button type="button" class="activated-button" id="winnersButton" onclick="switchInformations('winners')">Lista zwycięzców</button>
                    </div>
                    <div class="content-search">
                        <h3>Wyszukaj użytkownika</h3>
                        <div class="input-button">
                                <input type="text" name="searchWinners" id="searchWinners">
                                <button type="submit" class="search-button"></button>
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
                            <?php $surveyProcessorWinners->showData(); ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="content-footer">
                        <div class="footer-left">
                            <h5>Pozycje od <?php echo $surveyProcessorWinners->getSecondPageNumber(); ?> do <?php echo $surveyProcessorWinners->getPageNumber(); ?> z <?php echo $surveyProcessorWinners->pages(); ?> łącznie</h5>
                            <h6>
                                Pokaż 
                                    <select id="positionsWinners" name="positionsWinners" onchange="submitFormWinners()">
                                    <option value="5" <?php if(isset($_POST['positionsWinners']) && $_POST['positionsWinners'] == "5") echo "selected"; ?>>5</option>
                                    <option value="10" <?php if(isset($_POST['positionsWinners']) && $_POST['positionsWinners'] == "10") echo "selected"; ?>>10</option>
                                    <option value="15" <?php if(isset($_POST['positionsWinners']) && $_POST['positionsWinners'] == "15") echo "selected"; ?>>15</option>
                                    </select>
                                pozycji
                            </h6>
                        </div>
                        <div class="footer-right">
                            <button type="submit" name="previous">Poprzednia</button>
                            <button class="page" type="submit" name="page" value="1">1</button>
                            <button type="submit" name="page" value="2">2</button>
                            <button type="submit" name="page" value="3">3</button>
                            <button type="submit" name="page" value="4">4</button>
                            <button type="submit" name="page" value="5">5</button>
                            <button type="submit" name="next">Następna</button>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </div>
    <script src="./dashboard_script.js"></script>
</body>
</html>