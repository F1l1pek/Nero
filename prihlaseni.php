<?php
session_start();

$dbSpojeni = mysqli_connect("localhost", "root", null, "nero");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ověření a přihlášení uživatele
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    
    // Zkontrolovat, zda jsou email a heslo neprázdné
    if ($email && $password) {
        // Použití předpřipraveného dotazu pro zamezení SQL Injection
        $stmt = $dbSpojeni->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Zkontrolovat, zda byl uživatel nalezen
        if ($result->num_rows === 1) {
            // Získání řádku s uživatelem
            $user = $result->fetch_assoc();
            // Porovnání hesla s uloženým heslem v databázi
            if (password_verify($password, $user['password'])) {
                // Přihlášení uživatele, např. nastavení session nebo cookie
                // ...
                echo "Uživatel úspěšně přihlášen.";
            } else {
                echo "Neplatné heslo.";
            }
        } else {
            echo "Uživatel s tímto e-mailem neexistuje.";
        }
    } else {
        echo "Chybí e-mail nebo heslo.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Přihlášení</title>
    <link rel="stylesheet" type="text/css" href="loginy.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<?php include 'header.html'; ?> <!-- Připojení souboru header.html -->
<body>
    <div id="content">
        <div class="bublina"> <!-- Přidána třída bublina -->
            <div class="form-ohraniceni"> <!-- Přidána třída form-ohraniceni -->
                <h2>Přihlášení</h2>
                <?php
                if (isset($error)) {
                    echo "<p class='error'>$error</p>";
                }
                ?>
                <form action="prihlaseni.php" method="post">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required><br><br>
                    <label for="password">Heslo:</label>
                    <input type="password" id="password" name="password" required><br><br>
                    <input type="submit" value="Přihlásit">
                </form>
                <p>Nemáte ještě účet? <a href="registrace.php">Zaregistrovat se</a></p>
                <p>Zapomněli jste heslo? <a href="obnova_hesla.php" class="button">Zapomenuté heslo</a>
            </div>
        </div>
    </div>
</body>
</html>
