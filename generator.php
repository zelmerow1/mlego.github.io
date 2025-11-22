<?php
session_start();

function generateRandomString($length = 5) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

function sanitize_input($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $imie = sanitize_input($_POST['imie']);
    $nazwisko = sanitize_input($_POST['nazwisko']);
    $birthdate = sanitize_input($_POST['birthdate']);
    $pesel = sanitize_input($_POST['pesel']);
    $link_zdjecia = ($_POST['link_zdjecia']);
    $plec = sanitize_input($_POST['plec']);
  
    $username = $_SESSION['username'];
  
    $dowodnowy_template = file_get_contents('templates/dowodnowy.html');
    $dashboard_template = file_get_contents('templates/dashboard.html');
    $index_template = file_get_contents('templates/index.html');
    $more_template = file_get_contents('templates/more.html');
    $documents_template = file_get_contents('templates/documents.html');
    $qr_template = file_get_contents('templates/qr.html');
    $services_template = file_get_contents('templates/services.html');
    $qr2_template = file_get_contents('templates/skanuj_qr.html');
    $qr3_template = file_get_contents('templates/pokaz_qr.html');

    $dowodnowy_template = str_ireplace('{IMIE}', $imie, $dowodnowy_template);
    $dowodnowy_template = str_ireplace('{NAZWISKO}', $nazwisko, $dowodnowy_template);
    $dowodnowy_template = str_ireplace('{BIRTHDATE}', $birthdate, $dowodnowy_template);
    $dowodnowy_template = str_ireplace('{PESEL}', $pesel, $dowodnowy_template);
    $dowodnowy_template = str_ireplace('{PŁEĆ}', $plec, $dowodnowy_template);

    $folder_name = "aplikacje/demObywatel_" . $username . "_" . $imie . "_" . generateRandomString();
    mkdir($folder_name);
	
	$img = 'zdjecie.png';  
	$savePath = $folder_name . DIRECTORY_SEPARATOR . $img;
	
	file_put_contents($savePath, file_get_contents($link_zdjecia)); 
    file_put_contents("$folder_name/index.html", $index_template);
    file_put_contents("$folder_name/dashboard.html", $dashboard_template);
    file_put_contents("$folder_name/dowodnowy.html", $dowodnowy_template);
    file_put_contents("$folder_name/qr.html", $qr_template);
    file_put_contents("$folder_name/more.html", $more_template);
    file_put_contents("$folder_name/services.html", $services_template);
    file_put_contents("$folder_name/documents.html", $documents_template);
    file_put_contents("$folder_name/skanuj_qr.html", $qr2_template);
    file_put_contents("$folder_name/pokaz_qr.html", $qr3_template);
    header("Location: $folder_name");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>demObywatel | Generator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="generator.css">
    <style>
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #121212;
            transition: opacity 0.75s, visibility 0.75s;
        }

        .loader--hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loader::after {
            content: "";
            width: 75px;
            height: 75px;
            border: 15px solid #dddddd;
            border-top-color: #1e1e1e;
            border-radius: 50%;
            animation: loading 0.75s ease infinite;
        }

        @keyframes loading {
            from { transform: rotate(0turn); }
            to { transform: rotate(1turn); }
        }

        .informacja {
            text-align: center;
        }
		.content button {
			padding: 10px;
			border: none;
			border-radius: 5px;
			background-color: #4caf50;
			color: white;
			font-size: 16px;
			cursor: pointer;
		}
    </style>
    <script>
        window.addEventListener("load", () => {
            const loader = document.querySelector(".loader");
            loader.classList.add("loader--hidden");
            loader.addEventListener("transitionend", () => {
                document.body.removeChild(loader);
            });
        });

        function generatePESEL() {
            const birthdate = document.getElementById("birthdate").value;
            const sex = document.getElementById("plec").value;

            if (!birthdate || !sex) {
                alert("Uzupełnij datę urodzenia i płeć przed generowaniem PESEL.");
                return;
            }

            const [day, month, year] = birthdate.split('.').map(Number);
            if (!day || !month || !year) {
                alert("Niepoprawny format daty. Użyj formatu DD.MM.RRRR");
                return;
            }

            let yy = year % 100;
            let mm = month;
            if (year >= 2000 && year <= 2099) mm += 20;
            else if (year >= 2100 && year <= 2199) mm += 40;
            else if (year >= 2200 && year <= 2299) mm += 60;
            else if (year >= 1800 && year <= 1899) mm += 80;

            const datePart = `${yy.toString().padStart(2, '0')}${mm.toString().padStart(2, '0')}${day.toString().padStart(2, '0')}`;
            let random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');

            let sexDigit = (sex === "Kobieta") ? (Math.floor(Math.random() * 5) * 2).toString() : (Math.floor(Math.random() * 5) * 2 + 1).toString();

            let partialPESEL = datePart + random + sexDigit;

            const weights = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3];
            let sum = 0;
            for (let i = 0; i < 10; i++) {
                sum += parseInt(partialPESEL[i], 10) * weights[i];
            }
            let checksum = (10 - (sum % 10)) % 10;

            document.getElementById("pesel").value = partialPESEL + checksum;
        }
    </script>
</head>
<body>
    <header>
        <h1>demObywatel | Generator <i class="fas fa-user"></i></h1> <br>
        <a href="dashboard.php" class="back-button"><i class="fas fa-arrow-left"></i> Powrót</a>
    </header>
    <div class="loader"></div>
    <div class="content">
        <form action="" method="post">
            <label for="imie">Imię:</label>
            <input type="text" id="imie" name="imie" placeholder="Jan" required><br><br>

            <label for="nazwisko">Nazwisko:</label>
            <input type="text" id="nazwisko" name="nazwisko" placeholder="Kowalski" required><br><br>

            <label for="birthdate">Data urodzenia:</label>
            <input type="text" id="birthdate" name="birthdate" placeholder="01.01.2000" required><br><br>

            <label for="pesel">PESEL:</label>
            <input type="text" id="pesel" name="pesel" placeholder="05210169617" required maxlength="11">
            <button type="button" onclick="generatePESEL()">Generuj PESEL</button><br><br>

            <label for="link_zdjecia">Link do zdjęcia:</label>
            <input type="text" id="link_zdjecia" name="link_zdjecia" required><br><br>

            <label for="plec" class="label-2">Płeć:</label><br>
            <select id="plec" name="plec" required>
                <option value="">Wybierz...</option>
                <option value="Mężczyzna">Mężczyzna</option>
                <option value="Kobieta">Kobieta</option>
            </select><br><br>

            <input type="submit" value="Generuj Aplikacje">
        </form>
    </div>
    <div class="navbar">
        <a href="/info.php" class="nav-link"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
    </div>
</body>
</html>
