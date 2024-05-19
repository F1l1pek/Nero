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
$email = $_SESSION['email'];
$stmt = $dbSpojeni->prepare("SELECT * FROM user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Uživatel nalezen, získání informací
    $user = $result->fetch_assoc();
    $ulozeneHeslo = $user['heslo'];
    $telefon = $user['tel_cislo'];
}
// Zpracování formuláře pro změnu telefonu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $novyTelefon = $_POST['novy_telefon'];
    $heslo = $_POST['heslo'];

    // Kontrola, zda je heslo správné pro daného uživatele


    if (password_verify($heslo, $ulozeneHeslo)) {
        // Heslo je správné, provedení změny telefonu v databázi
        $updateStmt = $dbSpojeni->prepare("UPDATE user SET tel_cislo = ? WHERE email = ?");
        $updateStmt->bind_param("ss", $novyTelefon, $email);
        $updateStmt->execute();
        $updateStmt->close();

        // Přesměrování na profil s potvrzením změny telefonu
        header("Location: profil.php?telefon_zmenen=true");
        exit();
    } else {
        $error = "Heslo je nesprávné.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Změna telefonu</title>
    <link rel="stylesheet" type="text/css" href="loginy.css">
</head>
<body>
    <div id="content" class="bublina">
        <h2>Změna telefonu</h2>
        <?php
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
        <form action="zmena_tel.php" method="post" class="form-ohraniceni">
            <label for="aktualni_telefon">Aktuální telefon:</label>
            <input type="text" id="aktualni_telefon" name="aktualni_telefon" value="<?php echo $telefon; ?>" disabled><br><br>
            <label for="novy_telefon">Nový telefon:</label>
            <input type="text" id="novy_telefon" name="novy_telefon" required><br><br>
            <label for="heslo">Heslo:</label>
            <input type="password" id="heslo" name="heslo" required><br><br>
            <input type="submit" value="Změnit telefon">
        </form>
        <a href="profil.php" class="button">Zpět na profil</a>
    </div>
</body>
</html>