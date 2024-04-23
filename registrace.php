<?php
session_start();

$dbSpojeni = mysqli_connect("localhost", "root", null, "nero");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ověření a uložení registračních údajů
    $jmeno = $_POST['jmeno'];
    $prijmeni = $_POST['prijmeni'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dat_nar = $_POST['dat_nar'];
    $tel_cislo = !empty($_POST['tel_cislo']) ? $_POST['tel_cislo'] : null;

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $typ_uzivatele = "běžný";

    $existujiciUzivatel = mysqli_query($dbSpojeni, "SELECT * FROM user WHERE email = '{$email}'");
    if (mysqli_num_rows($existujiciUzivatel) > 0) {
        $error = "Uživatel s tímto emailem již existuje.";
    } else {
        $sql = "INSERT INTO user (jmeno, prijmeni, email, heslo, dat_nar, tel_cislo, typ_uzivatele) VALUES ('$jmeno', '$prijmeni', '$email', '$hashedPassword', '$dat_nar', '$tel_cislo', '$typ_uzivatele')";

        $vysledek = mysqli_query($dbSpojeni, $sql);
        if (!$vysledek) {
            $error = mysqli_error($dbSpojeni);
        } else {
            // Přihlášení uživatele po úspěšné registraci
            $_SESSION['email'] = $email; // Přihlašovací údaje po registraci
            header("Location: profil.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registrace</title>
    <link rel="stylesheet" type="text/css" href="loginy.css">
</head>
<?php include 'header.html'; ?> <!-- Připojení souboru header.html -->
<body>
    <div id="content">
        <div class="bublina"> <!-- Přidána třída bublina -->
            <div class="form-ohraniceni"> <!-- Přidána třída form-ohraniceni -->
                <h2>Registrace</h2>
                <?php
                if (isset($error)) {
                    echo "<p class='error'>$error</p>";
                }
                ?>
                <form action="registrace.php" method="post">
                    <label for="jmeno">Jméno:</label>
                    <input type="text" id="jmeno" name="jmeno" required><br><br>
                    <label for="prijmeni">Příjmení:</label>
                    <input type="text" id="prijmeni" name="prijmeni" required><br><br>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required><br><br>
                    <label for="password">Heslo:</label>
                    <input type="password" id="password" name="password" required><br><br>
                    <label for="dat_nar">Datum narození:</label>
                    <input type="date" id="dat_nar" name="dat_nar" required><br><br>
                    <label for="tel_cislo">Telefonní číslo:</label>
                    <input type="tel" id="tel_cislo" name="tel_cislo"><br><br>
                    <input type="submit" value="Registrovat">
                </form>
            </div>
        </div>
    </div>
</body>
</html>
