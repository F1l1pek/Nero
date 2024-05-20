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

// Získání informací o přihlášeném uživateli
$email = $_SESSION['email'];
$stmt = $dbSpojeni->prepare("SELECT * FROM user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Uživatel nalezen, získání informací
    $user = $result->fetch_assoc();
    $jmeno = $user['jmeno'];
    $prijmeni = $user['prijmeni'];
    $typ_uzivatele = $user['typ_uzivatele']; // Získání typu uživatele
    $telefon = $user['tel_cislo'];
}

// Odhlášení uživatele
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: prihlaseni.php");
    exit();
}
?>

    <link rel="stylesheet" type="text/css" href="admin.css">
    <link rel="stylesheet" href="../header.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php include_once '../header.html'; ?> <!-- Připojení souboru header.html -->

    
<main>
    <div id="content" class="bublina">
        <div class="zarovnani">
            <h2><?php echo $jmeno; ?> <?php echo $prijmeni; ?></h2>
        <p class="typ"> <?php echo $typ_uzivatele; ?></p>
    </div>
       
        <h3>Kontaktní údaje</h3>
       
         <div class="snizeni"> <p><span class="material-symbols-outlined">alternate_email</span><div class="text"><?php echo $email; ?></div></p></div>
        <div class="snizeni"> <p><span class="material-symbols-outlined">call</span><div class="text"><?php echo $telefon; ?></div></p></div>
        
        <form action="profil.php" method="GET">
            <input type="hidden" name="logout" value="true">
            <input type="submit" value="Odhlásit se" class="button1">
        </form>
        <div class="settings" onclick="toggleMenu()"><span class="material-symbols-outlined">tune</span><div class="text-m">Nastavení</div></div>
    <div class="menu" id="menu">

    <div class="button" onclick="toggleEmail()"><div class="zarovnani1"><span class="material-symbols-outlined">alternate_email</span></div><div class="text-m">Upravit email</div></div>
    <div class="menu1" id="Email"><div id="formContainer1" class="hidden input-container">
    <label for="nove_heslo">Nový email:</label>
    <input type="email" id="novyemail" placeholder="Vložte text">
    <button id="submitButton1">Odeslat</button></div></div>
    

    <div class="button" onclick="toggleTel()"><div class="zarovnani1"><span class="material-symbols-outlined">call</span></div><div class="text-m">Upravit telefonní číslo</div></div>
    <div class="menu1" id="Tel"><div id="formContainer2" class="hidden input-container">
    <label for="nove_heslo">Nové telefoní číslo:</label>
    <input type="tel" id="novytel" placeholder="Vložte text">
    <button id="submitButton2">Odeslat</button></div></div>

    <div class="button" onclick="toggleHeslo()"><div class="zarovnani1"><span class="material-symbols-outlined">lock</span></div><div class="text-m">Změna hesla</div></div>
    <div class="menu1" id="Heslo"><div id="formContainer3" class="hidden input-container">
    <label for="nove_heslo">Nové heslo:</label>
    <input type="password" id="novytel" placeholder="Vložte text">
    <label for="nove_heslo">Potvrďte heslo:</label>
    <input type="password" id="novytel" placeholder="Vložte text">
    <button id="submitButton3">Odeslat</button></div></div>

</div>
        <?php
        // Zobrazení tlačítka pro admina
        if ($typ_uzivatele === 'admin') {
           ?><a href="admin.php" class="button">Administrace</a><?php
        }
        ?>
        
    </>
</main>
<script>
        function toggleMenu() {
            var menu = document.getElementById('menu');
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        }
        function toggleEmail() {
            var Email = document.getElementById('Email');
            if (Email.style.display === 'none' || Email.style.display === '') {
                Email.style.display = 'block';
            } else {
                Email.style.display = 'none';
            }
        }
        function toggleTel() {
            var Tel = document.getElementById('Tel');
            if (Tel.style.display === 'none' || Tel.style.display === '') {
                Tel.style.display = 'block';
            } else {
                Tel.style.display = 'none';
            }
        }
        function toggleTel() {
            var Tel = document.getElementById('Tel');
            if (Tel.style.display === 'none' || Tel.style.display === '') {
                Tel.style.display = 'block';
            } else {
                Tel.style.display = 'none';
            }
        }
        function toggleHeslo() {
            var Heslo = document.getElementById('Heslo');
            if (Heslo.style.display === 'none' || Heslo.style.display === '') {
                Heslo.style.display = 'block';
            } else {
                Heslo.style.display = 'none';
            }
        }
    </script>
</body>
</html>


