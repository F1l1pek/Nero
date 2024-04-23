<?php
session_start();
$dbSpojeni = mysqli_connect("localhost", "root", null, "nero");
mysqli_set_charset($dbSpojeni, "utf8mb4");
if (!$dbSpojeni) {
    die("Chyba připojení k databázi: " . mysqli_connect_error());
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
$obrazky_adresar = "../obrazky_catering/";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Zkontrolovat, zda byl odeslán formulář pro přidání nové svatby
    if (!empty($_POST["nazev"]) && !empty($_POST["cena"]) && !empty($_POST["popis"]) && isset($_FILES["obrazek"])) {
        $nazev = mysqli_real_escape_string($dbSpojeni, $_POST["nazev"]);
        $cena = intval($_POST["cena"]);
        $popis = mysqli_real_escape_string($dbSpojeni, $_POST["popis"]);
        $nazev_obrazku = mysqli_real_escape_string($dbSpojeni, $_FILES["obrazek"]["name"]);

        // Nahrání obrázku na server
        $obrazek = $_FILES["obrazek"];
        $nazev_souboru = $obrazky_adresar . basename($obrazek["name"]);
        $cesta_k_souboru = $nazev_souboru;

        // Uložení obrázku do složky na serveru
        if (move_uploaded_file($obrazek["tmp_name"], $cesta_k_souboru)) {
            // Vložení dat do databáze
            $sql_insert = "INSERT INTO catering (nazev, cena, popis, obrazek) VALUES (?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($dbSpojeni, $sql_insert);

            if ($stmt_insert) {
                mysqli_stmt_bind_param($stmt_insert, "siss", $nazev, $cena, $popis, $nazev_obrazku);
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
            $sql_update = "UPDATE catering SET $atribut = ? WHERE id = ?";
            $stmt = mysqli_prepare($dbSpojeni, $sql_update);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "si", $nova_hodnota, $id);
                if (mysqli_stmt_execute($stmt)) {
                    echo "Záznam byl úspěšně aktualizován.";
                } else {
                    echo "Chyba při aktualizaci záznamu v databázi: " . mysqli_error($dbSpojeni);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Chyba při přípravě dotazu.";
            }
        } elseif ($atribut == "obrazek") {
            // Nahrání nového obrázku
            $obrazek = $_FILES["obrazek"];
            $nazev_souboru = $obrazky_adresar . basename($obrazek["name"]);
            $cesta_k_souboru = $nazev_souboru;

            // Uložení obrázku do složky na serveru
            if (move_uploaded_file($obrazek["tmp_name"], $cesta_k_souboru)) {
                // Aktualizace názvu obrázku v databázi
                $sql_update_obrazek = "UPDATE catering SET obrazek = ? WHERE id = ?";
                $stmt = mysqli_prepare($dbSpojeni, $sql_update_obrazek);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "si", $obrazek["name"], $id);
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
            $sql_delete = "DELETE FROM catering WHERE id = ?";
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
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Úprava svateb</title>
    <link rel="stylesheet" href="../admin.css"> <!-- Odkaz na nový CSS soubor -->
</head>
<body>

<div class="bublina" id="bublina-priprava-jidel">
    <h1>Přidávání svateb</h1> <!-- Popis přidávání svateb nad formulářem -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" class="form-ohraniceni"> <!-- Přidání třídy form-ohraniceni pro ohraničení formuláře -->
        <input type="text" name="nazev" placeholder="Název" required><br>
        <input type="number" name="cena" placeholder="Cena" min="0" step="1" required><br>
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
    <h1>Úprava svateb</h1> <!-- Popis úpravy svateb -->
    <!-- Tabulka svateb -->
    <table>
        <thead>
            <tr>
                <th><a href="?order=id">ID</a></th>
                <th><a href="?order=nazev">Název</a></th>
                <th><a href="?order=cena">Cena</a></th>
                <th>Popis</th>
                <th>Obrázek</th>
                <th>Odstranit</th>
            </tr>
        </thead>
        <tbody>
            <!-- PHP kód pro zobrazení svateb -->
            <?php
            $sql_select_svatby = "SELECT * FROM catering";

            // Přidání řazení podle zvoleného sloupce, pokud je to žádoucí
            if(isset($_GET['order'])){
                $order = $_GET['order'];
                $sql_select_svatby .= " ORDER BY $order";
            }

            $result_svatby = mysqli_query($dbSpojeni, $sql_select_svatby);

            if (mysqli_num_rows($result_svatby) > 0) {
                while ($row = mysqli_fetch_assoc($result_svatby)) {

                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td><form action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='id' value='".$row['id']."'><input type='hidden' name='atribut' value='nazev'><input type='text' name='nova_hodnota' value='" . $row['nazev'] . "'><input type='submit' value='Uložit'></form></td>";
                    echo "<td><form action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='id' value='".$row['id']."'><input type='hidden' name='atribut' value='cena'><input type='number' name='nova_hodnota' value='" . $row['cena'] . "'><input type='submit' value='Uložit'></form></td>";
                    echo "<td><form action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='id' value='".$row['id']."'><input type='hidden' name='atribut' value='popis'><input type='text' name='nova_hodnota' value='" . $row['popis'] . "'><input type='submit' value='Uložit'></form></td>";
                    echo "<td><form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'><input type='hidden' name='id' value='".$row['id']."'><input type='hidden' name='atribut' value='obrazek'><input type='file' name='obrazek' accept='image/*'><input type='submit' value='Nahrát nový obrázek'></form><img src='" . $obrazky_adresar . basename($row['obrazek']) . "' alt='" . $row['nazev'] . "' style='width:100px;height:100px;'></td>";
                    echo "<td><form action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='id' value='".$row['id']."'><input type='hidden' name='atribut' value='delete'><input type='submit' value='Odstranit'></form></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Žádné svatby k zobrazení.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
