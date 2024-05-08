<?php
session_start();

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Přidání položky do košíku s ošetřením neexistujícího pole $_SESSION['cart']
    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Načtení informací o jídle z databáze na základě ID
    // Zde předpokládám, že máte přístup k databázi a můžete načíst informace o jídle pomocí dotazu SQL
    $servername = "localhost";
    $username = "root";
    $password = null;
    $dbname = "nero";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Dotaz na načtení informací o jídle z databáze
    $sql = "SELECT název, cena FROM jidla WHERE ID_jidla = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Načtení informací o jídle z výsledku dotazu
        $row = $result->fetch_assoc();
        $nazev = $row['název'];
        $cena = $row['cena'];

        // Přidání položky do košíku
        $_SESSION['cart'][$id] = array(
            'název' => $nazev,
            'cena' => $cena,
            'množství' => 1  // Přidání položky s výchozím množstvím 1
        );
    }

    $conn->close();
}

header("Location: výběr.php");
exit();
