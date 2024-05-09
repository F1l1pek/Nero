<?php
session_start();
require_once "../db.php";
$dbSpojeni = connectToDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ověření a uložení registračních údajů
    $jmeno = $_POST['jmeno'];
    $prijmeni = $_POST['prijmeni'];
    $email = isset($_POST['email']) ? $_POST['email'] : null; // Ověření existence proměnné
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    $tel_cislo = !empty($_POST['tel_cislo']) ? $_POST['tel_cislo'] : null;
    $typ_uzivatele = 'standart'; // Nastavení výchozího typu uživatele

    // Použití předpřipraveného dotazu pro zamezení SQL Injection
    $stmt = $dbSpojeni->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $existujiciUzivatel = $stmt->get_result();
    
    // Kontrola, zda uživatel s daným emailem již existuje
    if (mysqli_num_rows($existujiciUzivatel) > 0) {
        $error = "Uživatel s tímto emailem již existuje.";
    } else {
        if ($password1 === $password2) {
            // Hesla se shodují
            // Šifrování hesla
           $hashedPassword = password_hash($password1, PASSWORD_DEFAULT);

            // Vložení nového uživatele do databáze
            $sql = "INSERT INTO user (jmeno, prijmeni, email, heslo, tel_cislo, typ_uzivatele) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $dbSpojeni->prepare($sql);
            $stmt->bind_param("ssssss", $jmeno, $prijmeni, $email, $hashedPassword, $tel_cislo, $typ_uzivatele);
            $vysledek = $stmt->execute();
            if (!$vysledek) {
                $error = mysqli_error($dbSpojeni);
            } else {
                // Přihlášení uživatele po úspěšné registraci
                $_SESSION['email'] = $email; // Přihlašovací údaje po registraci
                header("Location: profil.php");
                exit();
            }
        } else {
            $error = "Hesla se neshodují. Zadejte prosím stejná hesla do obou polí.";
        }
    }
}
?>


    <link rel="stylesheet" type="text/css" href="loginy.css">

<?php include_once '../header.html'; ?> <!-- Připojení souboru header.html -->

    
        <div class="bublina"> <!-- Přidána třída bublina -->
            <div class="form-ohraniceni"> <!-- Přidána třída form-ohraniceni -->
                <h2>Zaregistrovat se</h2>
                <?php
                if (isset($error)) {
                    echo "<p class='error'>$error</p>";
                }
                ?>
                <form action="registrace.php" method="post" class="form">
                    <label for="jmeno">Jméno</label>
                    <input type="text" id="jmeno" name="jmeno" required><br><br>
                    <label for="prijmeni">Příjmení</label>
                    <input type="text" id="prijmeni" name="prijmeni" required><br><br>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required><br><br>
                    <label for="tel_cislo">Telefonní číslo</label>
                    <input type="tel" id="tel_cislo" name="tel_cislo"><br><br>
                    <label for="password">Heslo</label>
                    <input type="password" id="password1" name="password" required><br><br>
                    <label for="password">Heslo znovu</label>
                    <input type="password" id="password2" name="password" required><br><br>
                    <input type="submit" value="Zaregistrovat se">
                </form>
            </div>
            <p>Máš už účet?<a href="prihlaseni.php">Přihlasit se</a></p>
        </div>
</body>
</html>
