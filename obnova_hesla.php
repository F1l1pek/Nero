<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    // Pro testovací účely můžeš vytvořit náhodný token
    $token = bin2hex(random_bytes(16));

    // Připojení k databázi
    $dbSpojeni = mysqli_connect("localhost", "root", "", "nero");
    if (!$dbSpojeni) {
        die("Chyba připojení k databázi: " . mysqli_connect_error());
    }

    // Kontrola existence e-mailu v databázi
    $stmt = $dbSpojeni->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Uživatel nalezen, uložení tokenu do databáze
        $updateStmt = $dbSpojeni->prepare("UPDATE user SET reset_token = ? WHERE email = ?");
        $updateStmt->bind_param("ss", $token, $email);
        $updateStmt->execute();
        $updateStmt->close();

        // Odeslání e-mailu s odkazem pro obnovu hesla
        $subject = "Obnova hesla";
        $message = "Pro obnovení hesla klikněte na tento odkaz: http://localhost/nero/novy_heslo.php?email=$email&token=$token";
        $headers = "From: webmaster@example.com";

        // Pro XAMPP můžeš použít Mailhog pro zachycení odeslaných e-mailů
        // Nastavení SMTP serveru na localhost a port 1025
        ini_set("SMTP", "localhost");
        ini_set("smtp_port", "1025");

        // Odeslání e-mailu
        mail($email, $subject, $message, $headers);

        // Přesměrování na stránku s potvrzením odeslání e-mailu
        header("Location: obnova_hesla_uspesne.php");
        exit();
    } else {
        $error = "Zadaný e-mail neexistuje.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Obnova hesla</title>
    <link rel="stylesheet" type="text/css" href="loginy.css">
</head>
<body>
    <div id="content" class="bublina"> <!-- Přidána třída bublina -->
        <h2>Obnova hesla</h2>
        <?php
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
        <form action="obnova_hesla.php" method="post">
            <label for="email">Zadejte svůj e-mail:</label>
            <input type="email" id="email" name="email" required><br><br>
            <input type="submit" value="Odeslat odkaz pro obnovu hesla">
        </form>
        <a href="profil.php" class="button">Zpět na profil</a>
    </div>
</body>
</html>
