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

// Zpracování formuláře pro změnu hesla
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['email'];
    $aktualniHeslo = $_POST['aktualni_heslo'];
    $noveHeslo = $_POST['nove_heslo'];
    $znovuNoveHeslo = $_POST['znovu_nove_heslo'];

    // Kontrola, zda je aktuální heslo správné pro daného uživatele
    $stmt = $dbSpojeni->prepare("SELECT heslo FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $ulozeneHeslo = $row['heslo'];

    if (password_verify($aktualniHeslo, $ulozeneHeslo)) {
        // Aktuální heslo je správné, kontrola nového hesla
        if ($noveHeslo === $znovuNoveHeslo) {
            // Nová hesla se shodují, provedení změny hesla v databázi
            $hashedNoveHeslo = password_hash($noveHeslo, PASSWORD_DEFAULT);
            $updateStmt = $dbSpojeni->prepare("UPDATE user SET heslo = ? WHERE email = ?");
            $updateStmt->bind_param("ss", $hashedNoveHeslo, $email);
            $updateStmt->execute();
            $updateStmt->close();

            // Přesměrování na profil s potvrzením změny hesla
            header("Location: profil.php?heslo_zmeneno=true");
            exit();
        } else {
            $error = "Nová hesla se neshodují.";
        }
    } else {
        $error = "Aktuální heslo je nesprávné.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Změna hesla</title>
    <link rel="stylesheet" type="text/css" href="loginy.css">
</head>
<body>
    <div id="content" class="bublina">
        <h2>Změna hesla</h2>
        <?php
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
        <form action="zmena_hesla.php" method="post" class="form-ohraniceni">
            <label for="email">Přihlášený email:</label>
            <input type="email" id="email" name="email" value="<?php echo $_SESSION['email']; ?>" disabled><br><br>
            <label for="aktualni_heslo">Aktuální heslo:</label>
            <input type="password" id="aktualni_heslo" name="aktualni_heslo" required><br><br>
            <label for="nove_heslo">Nové heslo:</label>
            <input type="password" id="nove_heslo" name="nove_heslo" required><br><br>
            <label for="znovu_nove_heslo">Znovu nové heslo:</label>
            <input type="password" id="znovu_nove_heslo" name="znovu_nove_heslo" required><br><br>
            <input type="submit" value="Změnit heslo">
        </form>
        <a href="profil.php" class="button">Zpět na profil</a>
    </div>
</body>
</html>
