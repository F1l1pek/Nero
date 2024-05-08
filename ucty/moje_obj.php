<?php
session_start();
include_once '../db.php';
$dbSpojeni = connectToDB();

$email = $_SESSION['ID_U'];
$sql = "SELECT * FROM obednavky_produkty WHERE ";
$vysledek = mysqli_query($dbSpojeni, $sql);
$svatby = mysqli_fetch_all($vysledek, MYSQLI_ASSOC);


include_once ('../header.html');
?>

<link rel="stylesheet" href="../header.css">
    
</body>
</html>