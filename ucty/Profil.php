<?php
session_start();

// Kontrola, zda je uživatel přihlášen
if (!isset($_SESSION['email'])) {
    // Uživatel není přihlášen, přesměrování na stránku přihlášení
    header("Location: prihlaseni.php");
    exit();

}

// Připojení k databázi
include_once '../db.php';
$dbSpojeni = connectToDB();

// Získání informací o přihlášeném uživateli
$email = $_SESSION['email'];
$stmt = $dbSpojeni->prepare("SELECT * FROM user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Uživatel nalezen, získání informací
    $user = $result->fetch_assoc();
    $ID_u= $user['ID_user'];
    $jmeno = $user['jmeno'];
    $prijmeni = $user['prijmeni'];
    $typ_uzivatele = $user['typ_uzivatele']; // Získání typu uživatele
    $telefon = $user['tel_cislo'];
}

// Odhlášení uživatele
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: prihlaseni.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["currentPassword"]) && !empty($_POST["currentPassword"])) {
        $currentPassword = $_POST["currentPassword"];

        // Získání aktuálního hashovaného hesla z databáze
        $stmt = $dbSpojeni->prepare("SELECT password FROM user WHERE ID_user = ?");
        $stmt->bind_param("i", $ID_u);
        $stmt->execute();
        $stmt->bind_result($heslo);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($currentPassword, $heslo)) {
            echo "Heslo ověřeno.<br>";

            // Aktualizace e-mailu
            if (isset($_POST["novyemail"]) && !empty($_POST["novyemail"])) {
                $novyemail = $_POST["novyemail"];
                $stmt = $dbSpojeni->prepare("UPDATE user SET email = ? WHERE ID_user = ?");
                $stmt->bind_param("si", $novyemail, $ID_u);
                if ($stmt->execute()) {
                    $_SESSION['email'] = $novyemail; // Aktualizace e-mailu v relaci
                    $response['emailMessage'] = "Email úspěšně aktualizován na $novyemail.";
                } else {
                    $response['emailError'] = "Chyba při aktualizaci e-mailu: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Email nebyl zadán.<br>";
            }

            // Aktualizace telefonního čísla
            if (isset($_POST["novytel"]) && !empty($_POST["novytel"])) {
                $novytel = $_POST["novytel"];
                $stmt = $dbSpojeni->prepare("UPDATE user SET tel_cislo = ? WHERE ID_user = ?");
                $stmt->bind_param("si", $novytel, $ID_u);
                if ($stmt->execute()) {
                    $response['telMessage'] = "Telefonní číslo úspěšně aktualizováno na $novytel.";
                } else {
                    $response['telError'] = "Chyba při aktualizaci telefonního čísla: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Telefonní číslo nebylo zadáno.<br>";
            }

            // Aktualizace hesla
            if (isset($_POST["novyheslo"]) && isset($_POST["novyheslo1"])) {
                if ($_POST["novyheslo"] === $_POST["novyheslo1"]) {
                    $noveheslo = password_hash($_POST["novyheslo"], PASSWORD_DEFAULT);
                    $stmt = $dbSpojeni->prepare("UPDATE user SET password = ? WHERE ID_user = ?");
                    $stmt->bind_param("si", $noveheslo, $ID_u);
                    if ($stmt->execute()) {
                        $response['passwordMessage'] = "Heslo úspěšně změněno.";
                    } else {
                        $response['passwordError'] = "Chyba při změně hesla: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    echo "Hesla se neshodují.<br>";
                }
            } else {
                echo "Nové heslo nebo potvrzení nového hesla nebylo zadáno.<br>";
            }
        } else {
            echo "Chyba: Zadané heslo není správné.<br>";
        }
    } else {
        echo "Chyba: Heslo je povinné.<br>";
    }
}
?>

    <link rel="stylesheet" type="text/css" href="admin.css">
    <link rel="stylesheet" href="../header.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php include_once '../header.html'; ?> <!-- Připojení souboru header.html -->

    
<main>
    <div id="content" class="bublina">
        <div class="zarovnani">
            <h2><?php echo $jmeno; ?> <?php echo $prijmeni; ?></h2>
        <p class="typ"> <?php echo $typ_uzivatele; ?></p>
    </div>
       
        <h3>Kontaktní údaje</h3>
       
         <div class="snizeni"> <p><span class="material-symbols-outlined">alternate_email</span><div class="text"><?php echo $email; ?></div></p></div>
        <div class="snizeni"> <p><span class="material-symbols-outlined">call</span><div class="text"><?php echo $telefon; ?></div></p></div>
        
        <form action="profil.php" method="GET">
            <input type="hidden" name="logout" value="true">
            <input type="submit" value="Odhlásit se" class="button1">
        </form>
        <div class="settings" onclick="toggleMenu()"><span class="material-symbols-outlined">tune</span><div class="text-m">Nastavení</div></div>
    <div class="menu" id="menu">

    <div class="button" onclick="toggleEmail()"><div class="zarovnani1"><span class="material-symbols-outlined">alternate_email</span></div><div class="text-m">Upravit email</div></div>
    <div class="menu1" id="Email"><div id="formContainer1" class="hidden input-container">
    <form method="POST">
    <label for="nove_heslo">Nový email:</label>
    <input type="email" id="novyemail" name="novyemail" placeholder="Vložte text">
    <button id="submitButton1"type="button">Odeslat</button></form></div></div>
    
    

    <div class="button" onclick="toggleTel()"><div class="zarovnani1"><span class="material-symbols-outlined">call</span></div><div class="text-m">Upravit telefonní číslo</div></div>
    <div class="menu1" id="Tel"><div id="formContainer2" class="hidden input-container">
    <form method="POST">
    <label for="nove_heslo">Nové telefoní číslo:</label>
    <input type="tel" id="novytel" name="novytel" placeholder="Vložte text">
    <button id="submitButton2"type="button">Odeslat</button></form></div></div>

    <div class="button" onclick="toggleHeslo()"><div class="zarovnani1"><span class="material-symbols-outlined">lock</span></div><div class="text-m">Změna hesla</div></div>
    <div class="menu1" id="Heslo"><div id="formContainer3" class="hidden input-container">
    <form method="POST">
    <label for="nove_heslo">Nové heslo:</label>
    <input type="password" id="novyheslo" name="novyheslo" placeholder="Vložte text">
    <label for="nove_heslo">Potvrďte heslo:</label>
    <input type="password" id="novyheslo1"name="novyheslo1" placeholder="Vložte text">
    <button id="submitButton3"type="button">Odeslat</button></form></div></div>

</div>

        <?php
        // Zobrazení tlačítka pro admina
        if ($typ_uzivatele === 'admin') {
           ?><a href="admin.php" class="button">Administrace</a><?php
        }
        ?>
        
        <div id="passwordokno" class="okno">
        <div class="okno-content">
            <span class="close">&times;</span>
            <form id="changeForm">
                <h2>Potvrdit změnu</h2>
                <p>Opravdu chcete provést tuo změnu?</p>
                <label for="currentPassword">Zadejte heslo:</label>
                <input type="password" id="currentPassword" name="currentPassword" required><br><br>
                <button type="submit" >Změnit email</button>
            </form>
        </div>
    </div>
</main>
<script>
        function toggleMenu() {
            var menu = document.getElementById('menu');
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
                Email.style.display = 'none';
                Tel.style.display = 'none';
                Heslo.style.display = 'none';
            }
        }
        function toggleEmail() {
            var Email = document.getElementById('Email');
            if (Email.style.display === 'none' || Email.style.display === '') {
                Email.style.display = 'block';
                Tel.style.display = 'none';
                Heslo.style.display = 'none';
            } else {
                Email.style.display = 'none';
            }
        }
        function toggleTel() {
            var Tel = document.getElementById('Tel');
            if (Tel.style.display === 'none' || Tel.style.display === '') {
                Tel.style.display = 'block';
                Email.style.display = 'none';
                Heslo.style.display = 'none';
            } else {
                Tel.style.display = 'none';
            }
        }
        function toggleHeslo() {
            var Heslo = document.getElementById('Heslo');
            if (Heslo.style.display === 'none' || Heslo.style.display === '') {
                Heslo.style.display = 'block';
                Email.style.display = 'none';
                Tel.style.display = 'none';
            } else {
                Heslo.style.display = 'none';
            }
        }

        /*----------------------------------------------------------------------*/
        var okno = document.getElementById("passwordokno");

// Získání elementu <span>, který zavře modální okno
var span = document.getElementsByClassName("close")[0];

// Když uživatel klikne na <span> (x), zavře se modální okno
span.onclick = function() {
    okno.style.display = "none";
}

// Když uživatel klikne kdekoli mimo modální okno, zavře se
window.onclick = function(event) {
    if (event.target == okno) {
        okno.style.display = "none";
    }
}

// Funkce pro otevření modálního okna
function openokno() {
    okno.style.display = "block";
}

// Přidání event listeneru pro tlačítko odeslání emailu
        document.getElementById("submitButton1").addEventListener("click", function() {
            openokno();
        });

        document.getElementById("submitButton2").addEventListener("click", function() {
            openokno();
        });

        document.getElementById("submitButton3").addEventListener("click", function() {
            openokno();
        });

// Přidání event listeneru pro odeslání formuláře
document.getElementById("changeForm").addEventListener("submit", function(event) {
            event.preventDefault();
            var currentPassword = document.getElementById('currentPassword').value;
            var newEmail = document.getElementById('novyemail').value;
            var newTel = document.getElementById('novytel').value;
            var newPassword = document.getElementById('novyheslo').value;
            var confirmPassword = document.getElementById('novyheslo1').value;
            console.log("Aktuální heslo:", currentPassword);
    console.log("Nový email:", newEmail);
    console.log("Nové telefonní číslo:", newTel);
    console.log("Nové heslo:", newPassword);
    console.log("Potvrzení nového hesla:", confirmPassword);
    success: function(response) {
    console.log(response.emailMessage);
    console.log(response.telMessage);
    console.log(response.passwordMessage);
},
error: function(response) {
    console.error(response.emailError);
    console.error(response.telError);
    console.error(response.passwordError);
}


            $.ajax({
                url: 'profil.php',
                type: 'POST',
                data: {
                    currentPassword: currentPassword,
                    novyemail: newEmail,
                    novytel: newTel,
                    novyheslo: newPassword,
                    novyheslo1: confirmPassword
                },
                success: function(response) {
                    alert('Změna byla úspěšně provedena!');
                    okno.style.display = 'none';
                },
                error: function() {
                    alert('Nastala chyba při změně údajů.');
                }
            });
        });
    </script>
</body>
</html>


