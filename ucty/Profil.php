<?php
session_start();

// Kontrola, zda je uživatel přihlášen
if (!isset($_SESSION['email'])) {
    // Uživatel není přihlášen, přesměrování na stránku přihlášení
    header("Location: prihlaseni.php");
    exit();
}

// Připojení k databázi
$dbSpojeni = mysqli_connect("localhost", "root", null, "nero");
if (!$dbSpojeni) {
    die("Chyba připojení k databázi: " . mysqli_connect_error());
}

// Získání informací o přihlášeném uživateli
$email = $_SESSION['email'];
$stmt = $dbSpojeni->prepare("SELECT * FROM user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Uživatel nalezen, získání informací
    $user = $result->fetch_assoc();
    $username = $email; // Uživatelské jméno je nahrazeno emailem
    $jmeno = $user['jmeno'];
    $prijmeni = $user['prijmeni'];
    $typ_uzivatele = $user['typ_uzivatele']; // Získání typu uživatele
}

// Odhlášení uživatele
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: prihlaseni.php");
    exit();
}
?>

    <link rel="stylesheet" type="text/css" href="admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php include '../header.html'; ?> <!-- Připojení souboru header.html -->

    
<main>
    <div id="content" class="bublina">
        <h2>Profil uživatele: <?php echo $username; ?></h2>
        <p><strong>Email:</strong> <?php echo $email; ?></p>
        <p><strong>Jméno:</strong> <?php echo $jmeno; ?></p>
        <p><strong>Příjmení:</strong> <?php echo $prijmeni; ?></p>
        <a href="zmena_hesla.php" class="button">Změna hesla</a>
        <a href="obnova_hesla.php" class="button">Zapomenuté heslo</a>

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
</body>
</html>


