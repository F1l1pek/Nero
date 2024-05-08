<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $token = $_POST['token'];
    $noveHeslo = $_POST['nove_heslo'];
    $znovuNoveHeslo = $_POST['znovu_nove_heslo'];

    // Připojení k databázi
    $dbSpojeni = mysqli_connect("localhost", "root", null, "nero");
    if (!$dbSpojeni) {
        die("Chyba připojení k databázi: " . mysqli_connect_error());
    }

    // Kontrola existence tokenu v databázi pro daný e-mail
    $stmt = $dbSpojeni->prepare("SELECT * FROM user WHERE email = ? AND reset_token = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Token platný, kontrola shody nových hesel
        if ($noveHeslo === $znovuNoveHeslo) {
            // Nová hesla se shodují, provedení změny hesla v databázi
            $hashedNoveHeslo = password_hash($noveHeslo, PASSWORD_DEFAULT);
            $updateStmt = $dbSpojeni->prepare("UPDATE user SET heslo = ?, reset_token = NULL WHERE email = ?");
            $updateStmt->bind_param("ss", $hashedNoveHeslo, $email);
            $updateStmt->execute();
            $updateStmt->close();

            // Přesměrování na stránku s potvrzením změny hesla
            header("Location: nove_heslo_uspesne.php");
            exit();
        } else {
            $error = "Nová hesla se neshodují.";
        }
    } else {
        $error = "Neplatný odkaz pro obnovu hesla.";
    }
}
?>


    <link rel="stylesheet" type="text/css" href="loginy.css"> <!-- Odkaz na loginy.css -->
    <link rel="stylesheet" href="../header.css">

<?php include_once '../header.html'; ?> <!-- Připojení souboru footer.html -->


    <div id="content" class="bublina"> <!-- Přidání třídy bublina pro formátování -->
        <h2>Nastavení nového hesla</h2>
        <?php
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
        <form action="novy_heslo.php" method="post">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8'); ?>">
            <label for="nove_heslo">Nové heslo:</label>
            <input type="password" id="nove_heslo" name="nove_heslo" class="form-control" required><br><br> <!-- Přidání třídy form-control -->
            <label for="znovu_nove_heslo">Znovu nové heslo:</label>
            <input type="password" id="znovu_nove_heslo" name="znovu_nove_heslo" class="form-control" required><br><br> <!-- Přidání třídy form-control -->
            <input type="submit" value="Nastavit" class="btn btn-primary"> <!-- Přidání tříd btn a btn-primary -->
        </form>
        <form action="profil.php">
            <input type="submit" value="Zpět na profil" class="btn btn-secondary"> <!-- Přidání tříd btn a btn-secondary -->
        </form>
    </div>
  
</body>
</html>
