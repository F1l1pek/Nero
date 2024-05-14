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
?>

    <link rel="stylesheet" type="text/css" href="admin.css">
    <link rel="stylesheet" href="../header.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php include '../header.html'; ?> <!-- Připojení souboru header.html -->

    
<main>
    <div id="content" class="bublina">
        <h2><?php echo $jmeno; ?><?php echo $prijmeni; ?></h2>
        <p><strong>Typ:</strong> <?php echo $typ_uzivatele; ?></p>
        <h3>Kontaktní údaje</h3>
        <p><?php echo $email; ?></p>
        <p><?php echo $telefon; ?></p>
       
       
        <div class="settings" onclick="toggleMenu()">Nastavení</div>
    <div class="menu" id="menu">
    <a href="zmena_email.php" class="button">Upravit email</a>
    <a href="zmena_tel.php" class="button">Upravit telefonní číslo</a>
    <a href="zmena_hesla.php" class="button">Změna hesla</a>
    </div>
        <?php
        // Zobrazení tlačítka pro admina
        if ($typ_uzivatele === 'admin') {
           ?><a href="admin.php" class="button">Administrace</a><?php
        }
        ?>
        <form action="profil.php" method="GET">
            <input type="hidden" name="logout" value="true">
            <input type="submit" value="Odhlásit se" class="button">
        </form>
    </div>
</main>
<script>
        function toggleMenu() {
            var menu = document.getElementById('menu');
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        }
    </script>
</body>
</html>


