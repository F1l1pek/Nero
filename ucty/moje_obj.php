<?php
session_start();
$dbSpojeni = mysqli_connect("localhost", "root", null, "nero");
mysqli_set_charset($dbSpojeni, "UTF8");
if (!$dbSpojeni) {
    die("Chyba připojení k databázi: " . mysqli_connect_error());
}
$email = $_SESSION['ID_U'];
$sql = "SELECT * FROM obednavky_produkty WHERE ";
$vysledek = mysqli_query($dbSpojeni, $sql);
$svatby = mysqli_fetch_all($vysledek, MYSQLI_ASSOC);


include_once ('../header.html');
?>

