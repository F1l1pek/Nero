<?php
session_start();
$dbSpojeni = mysqli_connect("localhost", "root", null, "nero");
mysqli_set_charset($dbSpojeni, "UTF8");
if (!$dbSpojeni) {
    die("Chyba připojení k databázi: " . mysqli_connect_error());
}

// Funkce pro generování GUID
function guidv4($data = null) {
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


// Získání informací o přihlášeném uživateli
$email = $_SESSION['email'];
// Ověření, zda je hodnota $_SESSION['email'] legitimní
if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Pouze pokračujeme, pokud je e-mailová adresa v platném formátu
    $stmt = $dbSpojeni->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Uživatel nalezen, získání informací
        $user = $result->fetch_assoc();
        $ID_U = $user['ID_user'];
        $typ_uzivatele = $user['typ_uzivatele']; // Získání typu uživatele
    }
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
$obrazky_adresar = "../obrazky_jidla/";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Zkontrolovat, zda byl odeslán formulář pro úpravu nebo odstranění jídla
    if (isset($_POST["id_jidla"]) && isset($_POST["atribut"])) {
        $id_jidla = $_POST["id_jidla"];
        $atribut = $_POST["atribut"];

        // Aktualizace záznamu v databázi
        if ($atribut != "img" && $atribut != "delete") {
            $nova_hodnota = $_POST["nova_hodnota"];
            $sql_update = "UPDATE jidla SET $atribut = ? WHERE ID_jidla = ?";
            $stmt = mysqli_prepare($dbSpojeni, $sql_update);

            // Zabezpečení proti SQL injection pomocí bind_param
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "si", $nova_hodnota, $id_jidla);
                if (mysqli_stmt_execute($stmt)) {
                    echo "Záznam byl úspěšně aktualizován.";
                } else {
                    echo "Chyba při aktualizaci záznamu v databázi: " . mysqli_error($dbSpojeni);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Chyba při přípravě dotazu.";
            }
        } elseif ($atribut == "img") {
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

            // Nový název obrázku
            $novy_nazev_obrazku = guidv4();

            // Nahrání nového obrázku
            $obrazek = $_FILES["obrazek"];
            $nazev_souboru = $obrazky_adresar . basename($novy_nazev_obrazku);
            $cesta_k_souboru = $nazev_souboru;

            // Uložení obrázku do složky na serveru
            if (move_uploaded_file($obrazek["tmp_name"], $cesta_k_souboru)) {
                // Aktualizace názvu obrázku v databázi
                $sql_update_obrazek = "UPDATE jidla SET img = ? WHERE ID_jidla = ?";
                $stmt = mysqli_prepare($dbSpojeni, $sql_update_obrazek);

                // Zabezpečení proti SQL injection pomocí bind_param
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "si", $novy_nazev_obrazku, $id_jidla);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "Nový obrázek byl úspěšně nahrán a aktualizován.";
                    } else {
                        echo "Chyba při aktualizaci obrázku v databázi: " . mysqli_error($dbSpojeni);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    echo "Chyba při přípravě dotazu.";
                }
            } else {
                echo "Nahrání nového obrázku selhalo.";
            }
        } elseif ($atribut == "delete") {
            // Odstranění záznamu z databáze
            $sql_delete = "DELETE FROM jidla WHERE ID_jidla = ?";
            $stmt = mysqli_prepare($dbSpojeni, $sql_delete);
        
            // Zabezpečení proti SQL injection pomocí bind_param
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id_jidla);
                if (mysqli_stmt_execute($stmt)) {
                    echo "Záznam byl úspěšně odstraněn.";
                } else {
                    echo "Chyba při odstraňování záznamu z databáze: " . mysqli_error($dbSpojeni);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Chyba při přípravě dotazu.";
            }
        }
        // Přesměrování na stejnou stránku po zpracování formuláře
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Některé potřebné parametry nebyly poskytnuty.";
    }
}

// Přidání nového jídla do databáze
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["nazev"]) && !empty($_POST["typ"]) && !empty($_POST["cena"]) && isset($_FILES["obrazek"])) {
    $nazev = mysqli_real_escape_string($dbSpojeni, $_POST["nazev"]);
    $typ = mysqli_real_escape_string($dbSpojeni, $_POST["typ"]);
    $cena = floatval($_POST["cena"]); // Nebo jiné vhodné formátování čísla
    $cena_s = floatval($_POST["cena_s"]);
    $cena_f = floatval($_POST["cena_f"]);
    $popis = mysqli_real_escape_string($dbSpojeni, $_POST["popis"]);

    // Náhodný název obrázku
    $novy_nazev_obrazku = guidv4();

    // Nahrání obrázku na server
    $obrazek = $_FILES["obrazek"];
    $nazev_souboru = $obrazky_adresar . basename($novy_nazev_obrazku);
    $cesta_k_souboru = $nazev_souboru;

    // Uložení obrázku do složky na serveru
    if (move_uploaded_file($obrazek["tmp_name"], $cesta_k_souboru)) {
        // Vložení dat do databáze
        $sql_insert = "INSERT INTO jidla (název, typ, cena, popis, img) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($dbSpojeni, $sql_insert);
        
        if ($stmt_insert) {
            mysqli_stmt_bind_param($stmt_insert, "ssdss", $nazev, $typ, $cena, $popis, $novy_nazev_obrazku);
            if (mysqli_stmt_execute($stmt_insert)) {
                echo "Nové jídlo bylo úspěšně přidáno.";
            } else {
                echo "Chyba při přidávání nového jídla: " . mysqli_error($dbSpojeni);
            }
            mysqli_stmt_close($stmt_insert);
        } else {
            echo "Chyba při přípravě dotazu na přidání nového jídla.";
        }
        // Přesměrování na stejnou stránku po zpracování formuláře
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Nahrání nového obrázku selhalo.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Úprava jídla</title>
    <link rel="stylesheet" href="../admin.css"> <!-- Odkaz na nový CSS soubor -->
</head>
<body>

<div class="bublina" id="bublina-priprava-jidel">
    <h1>Přidávání jídla</h1> <!-- Popis přidávání jídla nad formulářem -->
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>" method="post" enctype="multipart/form-data" class="form-ohraniceni">
        <input type="text" name="nazev" placeholder="Nazev" required><br>
        <label for="typ">Typ jídla:</label><br>
        <select id="typ" name="typ" required>
            <option value="Bagety">Bagety</option>
            <option value="Chlebíčky">Chlebíčky</option>
            <option value="Kaiserky">Kaiserky</option>
            <option value="Croissanty">Croissanty</option>
            <option value="Dezerty">Dezerty</option>
        </select><br>
        <input type="number" name="cena" placeholder="Cena" min="0" step="0.01" required><br>
        <input type="number" name="cena_s" placeholder="Cena pro školy" min="0" step="0.01" required><br>
        <input type="number" name="cena_f" placeholder="Cena pro firmy" min="0" step="0.01" required><br>
        <input type="text" name="popis" placeholder="Popis"><br>
        <label for="obrazek">Vyberte obrázek:</label>
        <input type="file" name="obrazek" id="obrazek" accept="image/*" required><br>
        <input type="submit" value="Uložit">
    </form>
</div>

<!-- Tlačítko pro návrat na admin.php -->
<div class="bublina" id="navrat-bublina">
<a href="../admin.php" class="navrat-button">Zpět na administrační panel</a>
</div>


<div class="bublina" id="bublina-tabulka-jidel">
    <h1>Úprava jídla</h1> <!-- Popis úpravy jídla -->
    <!-- Tabulka jídel -->
    <table>
        <thead>
            <tr>
                <th><a href="?order=ID_jidla">ID</a></th>
                <th><a href="?order=název">Název</a></th>
                <th><a href="?order=typ">Typ</a></th>
                <th><a href="?order=cena">Cena</a></th>
                <th><a href="?order=cena_s">Cena pro školy</a></th>
                <th><a href="?order=cena_f">Cena pro firmy</a></th>
                <th>Popis</th>
                <th>Obrázek</th>
                <th>Odstranit</th>
            </tr>
        </thead>
        <tbody>
            <!-- PHP kód pro zobrazení jídel -->
            <?php
            $sql_select_jidla = "SELECT * FROM jidla";
        
            // Přidání řazení podle zvoleného sloupce, pokud je to žádoucí
            if(isset($_GET['order'])){
                $order = $_GET['order'];
                $sql_select_jidla .= " ORDER BY $order";
            }
            
            $result_jidla = mysqli_query($dbSpojeni, $sql_select_jidla);
            
            if (mysqli_num_rows($result_jidla) > 0) {
                while ($row = mysqli_fetch_assoc($result_jidla)) {
                    
                    echo "<tr>";
                    echo "<td>" . $row['ID_jidla'] . "</td>"; // Oprava z 'id' na 'ID_jidla'
                    echo "<td><form method='post'><input type='hidden' name='id_jidla' value='".htmlspecialchars($row['ID_jidla'])."'><input type='hidden' name='atribut' value='název'><input type='text' name='nova_hodnota' value='" . htmlspecialchars($row['název']) . "'><input type='submit' value='Uložit'></form></td>";
                    echo "<td><form method='post'><input type='hidden' name='id_jidla' value='".htmlspecialchars($row['ID_jidla'])."'><input type='hidden' name='atribut' value='typ'><select name='nova_hodnota'><option value='Bagety'" . (htmlspecialchars($row['typ']) == 'Bagety' ? ' selected' : '') . ">Bagety</option><option value='Chlebíčky'" . (htmlspecialchars($row['typ']) == 'Chlebíčky' ? ' selected' : '') . ">Chlebíčky</option><option value='Kaiserky'" . (htmlspecialchars($row['typ']) == 'Kaiserky' ? ' selected' : '') . ">Kaiserky</option><option value='Croissanty'" . (htmlspecialchars($row['typ']) == 'Croissanty' ? ' selected' : '') . ">Croissanty</option><option value='Dezerty'" . (htmlspecialchars($row['typ']) == 'Dezerty' ? ' selected' : '') . ">Dezerty</option></select><input type='submit' value='Uložit'></form></td>";
                    echo "<td><form method='post'><input type='hidden' name='id_jidla' value='".htmlspecialchars($row['ID_jidla'])."'><input type='hidden' name='atribut' value='cena'><input type='number' name='nova_hodnota' value='" . htmlspecialchars($row['cena']) . "'><input type='submit' value='Uložit'></form></td>";
                    echo "<td><form method='post'><input type='hidden' name='id_jidla' value='".htmlspecialchars($row['ID_jidla'])."'><input type='hidden' name='atribut' value='cena_s'><input type='number' name='nova_hodnota' value='" . htmlspecialchars($row['cena_s']) . "'><input type='submit' value='Uložit'></form></td>";
                    echo "<td><form method='post'><input type='hidden' name='id_jidla' value='".htmlspecialchars($row['ID_jidla'])."'><input type='hidden' name='atribut' value='cena_f'><input type='number' name='nova_hodnota' value='" . htmlspecialchars($row['cena_f']) . "'><input type='submit' value='Uložit'></form></td>";
                    echo "<td><form method='post'><input type='hidden' name='id_jidla' value='".htmlspecialchars($row['ID_jidla'])."'><input type='hidden' name='atribut' value='popis'><input type='text' name='nova_hodnota' value='" . htmlspecialchars($row['popis']) . "'><input type='submit' value='Uložit'></form></td>";
                    echo "<td><form method='post' enctype='multipart/form-data'><input type='hidden' name='id_jidla' value='".htmlspecialchars($row['ID_jidla'])."'><input type='hidden' name='atribut' value='img'><input type='file' name='obrazek' accept='image/*'><input type='submit' value='Nahrát nový obrázek'></form><img src='" . $obrazky_adresar . htmlspecialchars($row['img']) . "' alt='" . htmlspecialchars($row['název']) . "' style='width:100px;height:100px;'></td>";
                    echo "<td><form method='post'><input type='hidden' name='id_jidla' value='".htmlspecialchars($row['ID_jidla'])."'><input type='hidden' name='atribut' value='delete'><input type='submit' value='Odstranit'></form></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Žádná jídla k zobrazení.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
