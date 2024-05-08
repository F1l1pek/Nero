<?php
if (!isset($_SESSION)){
    session_start();
}

include_once '../db.php';
function add_to_cart(int $product_id, int $quantity = 1) : void
{
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$product_id]);
        return;
    }
    $conn = connectToDB();
    $sql = "SELECT název, cena FROM jidla WHERE ID_jidla = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $nazev = $row['název'];
        $cena = $row['cena'];
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        $_SESSION['cart'][$product_id] = array(
            'nazev' => $nazev,
            'cena' => $cena,
            'mnozstvi' => $quantity
        );
    }
    $conn->close();
}


if(isset($_GET['id']) && isset($_GET['pocet'])) {
    $id = $_GET['id'];
    $pocet = $_GET['pocet'];
    
    // Přidání položky do košíku s ošetřením neexistujícího pole $_SESSION['cart']
    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Načtení informací o jídle z databáze na základě ID
    // Zde předpokládám, že máte přístup k databázi a můžete načíst informace o jídle pomocí dotazu SQL

    add_to_cart((int)$id, (int)$pocet);
}
header('Content-Type: application/json');
echo json_encode($_SESSION['cart']);
exit();
