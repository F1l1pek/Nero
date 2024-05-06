<?php
session_start();

$dbSpojeni = mysqli_connect("localhost", "root", null, "nero");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ověření a uložení registračních údajů
    $jmeno = $_POST['jmeno'];
    $prijmeni = $_POST['prijmeni'];
    $email = isset($_POST['email']) ? $_POST['email'] : null; // Ověření existence proměnné
    $password = $_POST['password'];
    $dat_nar = $_POST['dat_nar'];
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
        // Kontrola data narození
        $today = date("Y-m-d"); // Získání aktuálního data
        if ($dat_nar >= $today) {
            $error = "Datum narození musí být v minulosti.";
        } else {
            // Šifrování hesla
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Vložení nového uživatele do databáze
            $sql = "INSERT INTO user (jmeno, prijmeni, email, heslo, dat_nar, tel_cislo, typ_uzivatele) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $dbSpojeni->prepare($sql);
            $stmt->bind_param("sssssss", $jmeno, $prijmeni, $email, $hashedPassword, $dat_nar, $tel_cislo, $typ_uzivatele);
            $vysledek = $stmt->execute();
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
}
?>


    <link rel="stylesheet" type="text/css" href="loginy.css">

<?php include '../header.html'; ?> <!-- Připojení souboru header.html -->

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
                    <input type="date" id="dat_nar" name="dat_nar" max="<?php echo $today; ?>" required><br><br>
                    <label for="tel_cislo">Telefonní číslo:</label>
                    <input type="tel" id="tel_cislo" name="tel_cislo"><br><br>
                    <input type="submit" value="Registrovat">
                </form>
            </div>
        </div>
    </div>
</body>
</html>
