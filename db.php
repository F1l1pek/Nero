<?php
function connectToDB() {
    $dbSpojeni = mysqli_connect("localhost", "root", "root", "nero");
    if (!$dbSpojeni) {
        die("Nelze se připojit k databázi: " . mysqli_connect_error());
    }
    mysqli_set_charset($dbSpojeni, "UTF8");
    return $dbSpojeni;
}
