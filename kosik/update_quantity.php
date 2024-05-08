<?php
session_start();
$server = "localhost";
$username = "root";
$password = null; // Pokud máte heslo, vyplňte ho
$database = "nero";

// Připojení k databázi
include_once '../db.php';
$dbSpojeni = connectToDB();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item_id']) && isset($_POST['quantity']) && isset($_POST['order_id'])) {
    // Získání ID položky, množství a ID objednávky z požadavku
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $order_id = $_POST['order_id'];

    // Aktualizace množství v databázi pomocí předpřipraveného dotazu
    $update_query = "UPDATE košik SET mnozstvi = ? WHERE ID_J = ? AND ID_O = ?";
    
    // Použití předpřipraveného dotazu
    $stmt = $dbSpojeni->prepare($update_query);
    
    // Vazba parametrů
    $stmt->bind_param("iii", $quantity, $item_id, $order_id);
    
    // Spuštění dotazu
    if ($stmt->execute()) {
        echo "Množství bylo aktualizováno.";
    } else {
        echo "Chyba při aktualizaci množství: " . $stmt->error;
    }
}
?>