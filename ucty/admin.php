<?php
session_start();

// Připojení k databázi
include_once '../db.php';
$dbSpojeni = connectToDB();

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
    <link rel="stylesheet" href="../header.css">
    
<?php include '../header.html'; ?> <!-- Připojení souboru header.html -->
        <div class="bublina"> <!-- Přidáváme třídu 'bublina' -->
            <h1>Administrativní panel</h1>
            <div class="odkazy">
                <a href="../kosik/jidla.php" class="button">Přidání nových jídel</a>
                <a href="../svatby_catering/svatby_pridani" class="button">Přidání svateb</a>
                <a href="../svatby_catering/catering_pridani" class="button">Přidání cateringu</a>
                <a href="../galerie_pridani.php" class="button">Přidání galerie</a>
                <a href="seznam_uzivatelu.php" class="button">Seznam uživatelů</a>
                <a href="seznam_objednavek.php" class="button">Seznam objednávek</a>
                <a href="profil.php" class="button">Zpět na profil</a>
            </div>             
        </div>
</body>
</html>
