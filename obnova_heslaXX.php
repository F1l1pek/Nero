<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Připojení k databázi
    $dbSpojeni = mysqli_connect("localhost", "root", null, "nero");
    if (!$dbSpojeni) {
        die("Chyba připojení k databázi: " . mysqli_connect_error());
    }

    // Kontrola existence emailu v databázi
    $stmt = $dbSpojeni->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Email existuje v databázi, generujeme token pro obnovu hesla
        $token = bin2hex(random_bytes(32));

        // Uložení tokenu do databáze
        $updateStmt = $dbSpojeni->prepare("UPDATE user SET reset_token = ? WHERE email = ?");
        $updateStmt->bind_param("ss", $token, $email);
        $updateStmt->execute();
        $updateStmt->close();

        // Odeslat e-mail s odkazem na obnovu hesla
        $resetLink = "http://example.com/novy_heslo.php?email=" . urlencode($email) . "&token=" . urlencode($token);
        // Zde přidej kód pro odeslání e-mailu s odkazem na obnovu hesla

        // Přesměrování na stránku s potvrzením odeslání odkazu
        header("Location: obnova_hesla_uspesne.php");
        exit();
    } else {
        $error = "Zadaný email není registrován.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Obnova hesla</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div id="header">
        
    </div>
    <div id="content">
        <h2>Obnova hesla</h2>
        <?php
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
        <form action="obnova_hesla.php" method="post">
            <label for="email">Zadejte váš email:</label>
            <input type="email" id="email" name="email" required><br><br>
            <input type="submit" value="Odeslat odkaz pro obnovu hesla">
        </form>
    </div>
    <div id="footer">
        
    </div>
</body>
</html>
