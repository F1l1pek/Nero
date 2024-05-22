<?php
session_start();
include_once '../db.php';
$dbSpojeni = connectToDB();

// Náhodný název pro obrázek
function guidv4($data = null) {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// Získání informací o přihlášeném uživateli
$email = $_SESSION['email'];
$stmt = $dbSpojeni->prepare("SELECT typ_uzivatele FROM user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Uživatel nalezen, získání typu uživatele
    $user = $result->fetch_assoc();
    $typ_uzivatele = $user['typ_uzivatele'];
} else {
    // Pokud uživatel není nalezen, přesměrovat na stránku přihlášení
    header("Location: prihlaseni.php");
    exit();
}

// Kontrola, zda je uživatel přihlášen a je "admin"
if ($typ_uzivatele !== 'admin') {
    // Pokud uživatel není přihlášen jako admin, přesměrovat ho na jinou stránku nebo zobrazit chybu
    header("Location: Profil.php"); // Uprav podle potřeby
    exit();
}

// Adresář pro ukládání obrázků na serveru
$obrazky_adresar = "../obrazky_svatby/";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Zkontrolovat, zda byl odeslán formulář pro přidání nové svatby
    if (!empty($_POST["nazev"]) && !empty($_POST["cena"]) && !empty($_POST["popis"]) && isset($_FILES["obrazek"])) {
        $nazev = mysqli_real_escape_string($dbSpojeni, $_POST["nazev"]);
        $cena = intval($_POST["cena"]);
        $popis = mysqli_real_escape_string($dbSpojeni, $_POST["popis"]);

        // Aktualizace názvu obrázku v databázi
        $sql_update_obrazek = "UPDATE svatby SET obrazek = ? WHERE id = ?";
        $stmt = mysqli_prepare($dbSpojeni, $sql_update_obrazek);

        if ($stmt) {
            // Náhodný název obrázku
            $novy_nazev_obrazku = guidv4();
            
            mysqli_stmt_bind_param($stmt, "si", $novy_nazev_obrazku, $id);
            if (mysqli_stmt_execute($stmt)) {
                echo "Nový obrázek byl úspěšně nahrán a aktualizován.";
            } else {
                echo "Chyba při aktualizaci obrázku v databázi: " . mysqli_error($dbSpojeni);
            }
            mysqli_stmt_close($stmt);
        }

        // Nový název obrázku
        $novy_nazev_obrazku = guidv4();

        // Nahrání obrázku na server
        $obrazek = $_FILES["obrazek"];
        $nazev_souboru = $obrazky_adresar . basename($novy_nazev_obrazku);
        $cesta_k_souboru = $nazev_souboru;

        // Uložení obrázku do složky na serveru
        if (move_uploaded_file($obrazek["tmp_name"], $cesta_k_souboru)) {
            // Vložení dat do databáze
            $sql_insert = "INSERT INTO svatby (nazev, cena, popis, obrazek) VALUES (?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($dbSpojeni, $sql_insert);
            
            if ($stmt_insert) {
                mysqli_stmt_bind_param($stmt_insert, "siss", $nazev, $cena, $popis, $novy_nazev_obrazku);
                if (mysqli_stmt_execute($stmt_insert)) {
                    echo "Nová svatba byla úspěšně přidána.";
                } else {
                    echo "Chyba při přidávání nové svatby: " . mysqli_error($dbSpojeni);
                }
                mysqli_stmt_close($stmt_insert);
            } else {
                echo "Chyba při přípravě dotazu na přidání nové svatby.";
            }
            // Přesměrování na stejnou stránku po zpracování formuláře
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Nahrání nového obrázku selhalo.";
        }
    } elseif (isset($_POST["id"]) && isset($_POST["atribut"])) {
        // Zkontrolovat, zda byl odeslán formulář pro úpravu nebo odstranění svatby
        $id = $_POST["id"];
        $atribut = $_POST["atribut"];

        // Aktualizace záznamu v databázi
        if ($atribut != "obrazek" && $atribut != "delete") {
            $nova_hodnota = $_POST["nova_hodnota"];
            $sql_update = "UPDATE svatby SET $atribut = ? WHERE id = ?";
            $stmt = mysqli_prepare($dbSpojeni, $sql_update);
            define('ERROR_MESSAGE', "Chyba při přípravě dotazu.");
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "si", $nova_hodnota, $id);
                if (mysqli_stmt_execute($stmt)) {
                    echo "Záznam byl úspěšně aktualizován.";
                } else {
                    echo "Chyba při aktualizaci záznamu v databázi: " . mysqli_error($dbSpojeni);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo ERROR_MESSAGE;
            }
        } elseif ($atribut == "obrazek") {
            // Nahrání nového obrázku
            $obrazek = $_FILES["obrazek"];
            $novy_nazev_obrazku = guidv4();
            $nazev_souboru = $obrazky_adresar . basename($novy_nazev_obrazku);
            $cesta_k_souboru = $nazev_souboru;

            // Uložení obrázku do složky na serveru
            if (move_uploaded_file($obrazek["tmp_name"], $cesta_k_souboru)) {
                // Aktualizace názvu obrázku v databázi
                $sql_update_obrazek = "UPDATE svatby SET obrazek = ? WHERE id = ?";
                $stmt = mysqli_prepare($dbSpojeni, $sql_update_obrazek);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "si", $novy_nazev_obrazku, $id);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "Nový obrázek byl úspěšně nahrán a aktualizován.";
                    } else {
                        echo "Chyba při aktualizaci obrázku v databázi: " . mysqli_error($dbSpojeni);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    echo ERROR_MESSAGE;
                }
            } else {
                echo "Nahrání nového obrázku selhalo.";
            }
        } elseif ($atribut == "delete") {
            // Odstranění záznamu z databáze
            $sql_delete = "DELETE FROM svatby WHERE id = ?";
            $stmt = mysqli_prepare($dbSpojeni, $sql_delete);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                if (mysqli_stmt_execute($stmt)) {
                    echo "Záznam byl úspěšně odstraněn.";
                } else {
                    echo "Chyba při odstraňování záznamu z databáze: " . mysqli_error($dbSpojeni);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo ERROR_MESSAGE;
            }
        }
        // Přesměrování na stejnou stránku po zpracování formuláře
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Některé potřebné parametry nebyly poskytnuty.";
    }
}
?>

