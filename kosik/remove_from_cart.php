<?php
session_start();

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ošetření existence pole $_SESSION['cart']
    if(isset($_SESSION['cart']) && is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // Ošetření existence položky v košíku podle ID
        if(array_key_exists($id, $_SESSION['cart'])) {
            unset($_SESSION['cart'][$id]);
        }
    }
}

header("Location: výběr.php");
exit();
?>