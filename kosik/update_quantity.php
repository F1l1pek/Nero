<?php
session_start();
$server = "localhost";
$username = "root";
$password = ""; // Pokud máte heslo, vyplňte ho
$database = "nero";

// Připojení k databázi
$dbSpojeni = mysqli_connect($server, $username, $password, $database);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item_id']) && isset($_POST['quantity']) && isset($_POST['order_id'])) {
    // Získání ID položky, množství a ID objednávky z požadavku
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $order_id = $_POST['order_id'];

    // Aktualizace množství v databázi
    $update_query = "UPDATE košik SET mnozstvi = $quantity WHERE ID_J = $item_id AND ID_O = $order_id";

    if (mysqli_query($dbSpojeni, $update_query)) {
        echo "Množství bylo aktualizováno.";
    } else {
        echo "Chyba při aktualizaci množství: " . mysqli_error($dbSpojeni);
    }
}
?>