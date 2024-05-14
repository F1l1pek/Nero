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

// Zpracování formuláře pro změnu emailu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staryEmail = $_SESSION['email'];
    $novyEmail = $_POST['novy_email'];
    $heslo = $_POST['heslo'];

    // Kontrola, zda je heslo správné pro daného uživatele
    $stmt = $dbSpojeni->prepare("SELECT heslo FROM user WHERE email = ?");
    $stmt->bind_param("s", $staryEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $ulozeneHeslo = $row['heslo'];

    if (password_verify($heslo, $ulozeneHeslo)) {
        // Heslo je správné, kontrola nového emailu
       
            // Nové emaily se shodují, provedení změny emailu v databázi
            $updateStmt = $dbSpojeni->prepare("UPDATE user SET email = ? WHERE email = ?");
            $updateStmt->bind_param("ss", $novyEmail, $staryEmail);
            $updateStmt->execute();
            $updateStmt->close();

            // Aktualizace session
            $_SESSION['email'] = $novyEmail;

            // Přesměrování na profil s potvrzením změny emailu
            header("Location: profil.php?email_zmenen=true");
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
    <title>Změna emailu</title>
    <link rel="stylesheet" type="text/css" href="loginy.css">
</head>
<body>
    <div id="content" class="bublina">
        <h2>Změna emailu</h2>
        <?php
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
        <form action="zmena_emailu.php" method="post" class="form-ohraniceni">
            <label for="stary_email">Aktuální email:</label>
            <input type="email" id="stary_email" name="stary_email" value="<?php echo $_SESSION['email']; ?>" disabled><br><br>
            <label for="novy_email">Nový email:</label>
            <input type="email" id="novy_email" name="novy_email" required><br><br>
            <label for="heslo">Heslo:</label>
            <input type="password" id="heslo" name="heslo" required><br><br>
            <input type="submit" value="Změnit email">
        </form>
        <a href="profil.php" class="button">Zpět na profil</a>
    </div>
</body>
</html>