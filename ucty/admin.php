<?php
session_start();

// Připojení k databázi
$dbSpojeni = mysqli_connect("localhost", "root", null, "nero");
if (!$dbSpojeni) {
    die("Chyba připojení k databázi: " . mysqli_connect_error());
}

// Získání informací o přihlášeném uživateli
$email = $_SESSION['email'];
$stmt = $dbSpojeni->prepare("SELECT typ_uzivatele FROM user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Uživatel nalezen, získání typu uživatele
    $user = $result->fetch_assoc();
    $typ_uzivatele = $user['typ_uzivatele'];
} else {
    // Pokud uživatel není nalezen, přesměrovat na stránku přihlášení
    header("Location: prihlaseni.php");
    exit();
}

// Kontrola, zda je uživatel přihlášen a je "admin"
if ($typ_uzivatele !== 'admin') {
    // Pokud uživatel není přihlášen jako admin, přesměrovat ho na jinou stránku nebo zobrazit chybu
    header("Location: Profil.php"); // Uprav podle potřeby
    exit();
}
?>

    <link rel="stylesheet" href="admin.css"> <!-- Odkaz na základní CSS soubor -->

<?php include 'header.html'; ?> <!-- Připojení souboru header.html -->

    <div id="content"> <!-- Přidáváme třídu 'bublina' a id 'content' -->
        <div class="bublina"> <!-- Přidáváme třídu 'bublina' -->
            <h1>Administrativní panel</h1>
            <div id="novy-obsah"> <!-- Přidání nového obsahu -->
                <a href="kosik/jidla.php" class="button">Přidání nových jídel</a>
                <a href="svatby_pridani.php" class="button">Přidání svateb</a>
                <a href="catering_pridani.php" class="button">Přidání cateringu</a>
                <a href="galerie_pridani.php" class="button">Přidání galerie</a>
                <a href="seznam_uzivatelu.php" class="button">Seznam uživatelů</a>
                <a href="seznam_objednavek.php" class="button">Seznam objednávek</a>
                <a href="profil.php" class="button">Zpět na profil</a>
            </div>
        </div>
    </div>
</body>
</html>
