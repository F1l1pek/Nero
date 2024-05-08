<?php
session_start();

if (isset($_SESSION['cart'])) {
    echo "<h2>Obsah košíku:</h2>";
    echo "<ul>";
    foreach ($_SESSION['cart'] as $item) {
        echo "<li>" . $item['nazev'] . " - Cena: " . $item['cena'] . " Kč - Množství: " . $item['mnozstvi'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "Košík je prázdný.";
}
?>
