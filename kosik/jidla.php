<?php
require_once '../db.php';
$dbSpojeni = connectToDB();
$obrazky_adresar = '../obrazky_jidla/';

if (!$dbSpojeni) {
    die("Connection failed: " . mysqli_connect_error());
}

// Zpracování formuláře pro přidání nového jídla
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["akce"]) && $_POST["akce"] == "pridat") {
    $nazev = mysqli_real_escape_string($dbSpojeni, $_POST["nazev"]);
    $typ = mysqli_real_escape_string($dbSpojeni, $_POST["typ"]);
    $cena = floatval($_POST["cena"]);
    $cena_s = floatval($_POST["cena_s"]);
    $cena_f = floatval($_POST["cena_f"]);
    $popis = mysqli_real_escape_string($dbSpojeni, $_POST["popis"]);

    // Zpracování obrázku
    $obrazek = $_FILES['obr']['name'];

    if ($obrazek) {
        $obrazekTmp = $_FILES['obr']['tmp_name'];
        $obrazekCesta = $obrazky_adresar . basename($obrazek);
        move_uploaded_file($obrazekTmp, $obrazekCesta);
        $sql_insert = "INSERT INTO jidla (název, typ, cena, cena_s, cena_f, popis, img) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($dbSpojeni, $sql_insert);
        mysqli_stmt_bind_param($stmt, "ssdddss", $nazev, $typ, $cena, $cena_s, $cena_f, $popis, basename($obrazek));
        mysqli_stmt_execute($stmt);
        header("Location: jidla.php");
    } else {
        $sql_insert = "INSERT INTO jidla (nazev, typ, cena, cena_s, cena_f, popis) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($dbSpojeni, $sql_insert);
        mysqli_stmt_bind_param($stmt, "ssddds", $nazev, $typ, $cena, $cena_s, $cena_f, $popis);
        mysqli_stmt_execute($stmt);
        header("Location: jidla.php");
    }
}

// Zpracování formuláře pro úpravu nebo odstranění existujícího jídla
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["id_jidla"]) && isset($_POST["atribut"])) {
        $id_jidla = $_POST["id_jidla"];
        $atribut = $_POST["atribut"];

        if ($atribut == "update") {
            $nazev = mysqli_real_escape_string($dbSpojeni, $_POST["nazev"]);
            $typ = mysqli_real_escape_string($dbSpojeni, $_POST["typ"]);
            $cena = floatval($_POST["cena"]);
            $cena_s = floatval($_POST["cena_s"]);
            $cena_f = floatval($_POST["cena_f"]);
            $popis = mysqli_real_escape_string($dbSpojeni, $_POST["popis"]);

            // Zpracování obrázku
            $obrazek = $_FILES['obrazek']['name'];

            if ($obrazek) {
                $obrazekTmp = $_FILES['obrazek']['tmp_name'];
                $obrazekCesta = $obrazky_adresar . basename($obrazek);
                move_uploaded_file($obrazekTmp, $obrazekCesta);
                $sql_update = "UPDATE jidla SET název = ?, typ = ?, cena = ?, cena_s = ?, cena_f = ?, popis = ?, img = ? WHERE ID_jidla = ?";
                $stmt = mysqli_prepare($dbSpojeni, $sql_update);
                mysqli_stmt_bind_param($stmt, "ssdddssi", $nazev, $typ, $cena, $cena_s, $cena_f, $popis, $obrazekCesta, $id_jidla);
            } else {
                $sql_update = "UPDATE jidla SET název = ?, typ = ?, cena = ?, cena_s = ?, cena_f = ?, popis = ? WHERE ID_jidla = ?";
                $stmt = mysqli_prepare($dbSpojeni, $sql_update);
                mysqli_stmt_bind_param($stmt, "ssdddsi", $nazev, $typ, $cena, $cena_s, $cena_f, $popis, $id_jidla);
            }

            mysqli_stmt_execute($stmt);
        } elseif ($atribut == "delete") {
            $sql_delete = "DELETE FROM jidla WHERE ID_jidla = ?";
            $stmt = mysqli_prepare($dbSpojeni, $sql_delete);
            mysqli_stmt_bind_param($stmt, "i", $id_jidla);
            mysqli_stmt_execute($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Úprava jídel</title>

    <link rel="stylesheet" href="prid_jid.css">
</head>
<body>
    <a href="../ucty/admin.php">Zpět na administraci</a>
    <div class="prid_jidla">
        <form method="POST" enctype="multipart/form-data">
            <label for="nazev">Název:</label>
            <input type="text" name="nazev" id="nazev">
            <label for="typ">Typ jídla:</label>
            <select name="typ" id="typ">
                <option value="" selected>---Vyberte typ---</option>
                <option value="Bagety">Bagety</option>
                <option value="Chlebíčky">Chlebíčky</option>
                <option value="Kaiserky">Kaiserky</option>
                <option value="Croissanty">Croissanty</option>
                <option value="Dezerty">Dezerty</option>
            </select>
            <label for="cena">Cena pro klasické uživatele:</label>
            <input type="number" name="cena" id="cena">
            <label for="cena_s">Cena pro školy:</label>
            <input type="number" name="cena_s" id="cena_s">
            <label for="cena_f">Cena pro firmy:</label>
            <input type="number" name="cena_f" id="cena_f">
            <label for="popis">Popis produktu:</label>
            <input type="text" name="popis" id="popis">
            <label for="obr">Obrázek produktu:</label>
            <input type="file" name="obr" id="obr">
            <input type="hidden" name="akce" value="pridat">
            <input type="submit" value="Odeslat">
        </form>
    </div>
<div class="bublina" id="bublina-tabulka-jidel">
    <h1>Úprava jídla</h1>
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
                <th>Akce</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql_select_jidla = "SELECT * FROM jidla";
        
            if (isset($_GET['order'])) {
                $order = $_GET['order'];
                $sql_select_jidla .= " ORDER BY $order";
            }
            
            $result_jidla = mysqli_query($dbSpojeni, $sql_select_jidla);
            if (mysqli_num_rows($result_jidla) > 0) {
                while ($row = mysqli_fetch_assoc($result_jidla)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ID_jidla']) . "</td>";
                    echo "<td><input type='text' form = 'jidlo_{$row['ID_jidla']}' name='nazev' value='" . htmlspecialchars($row['název']) . "'></td>";
                    echo "<td>
                            <select name='typ' form = 'jidlo_{$row['ID_jidla']}'>
                                <option form = 'jidlo_{$row['ID_jidla']}' value='Bagety'" . (htmlspecialchars($row['typ']) == 'Bagety' ? ' selected' : '') . ">Bagety</option>
                                <option form = 'jidlo_{$row['ID_jidla']}' value='Chlebíčky'" . (htmlspecialchars($row['typ']) == 'Chlebíčky' ? ' selected' : '') . ">Chlebíčky</option>
                                <option form = 'jidlo_{$row['ID_jidla']}' value='Kaiserky'" . (htmlspecialchars($row['typ']) == 'Kaiserky' ? ' selected' : '') . ">Kaiserky</option>
                                <option form = 'jidlo_{$row['ID_jidla']}' value='Croissanty'" . (htmlspecialchars($row['typ']) == 'Croissanty' ? ' selected' : '') . ">Croissanty</option>
                                <option form = 'jidlo_{$row['ID_jidla']}' value='Dezerty'" . (htmlspecialchars($row['typ']) == 'Dezerty' ? ' selected' : '') . ">Dezerty</option>
                            </select>
                          </td>";
                    echo "<td><input type='number' form = 'jidlo_{$row['ID_jidla']}' name='cena' value='" . htmlspecialchars($row['cena']) . "' step='0.01'></td>";
                    echo "<td><input type='number' form = 'jidlo_{$row['ID_jidla']}' name='cena_s' value='" . htmlspecialchars($row['cena_s']) . "' step='0.01'></td>";
                    echo "<td><input type='number' form = 'jidlo_{$row['ID_jidla']}' name='cena_f' value='" . htmlspecialchars($row['cena_f']) . "' step='0.01'></td>";
                    echo "<td><input type='text' form = 'jidlo_{$row['ID_jidla']}' name='popis' value='" . htmlspecialchars($row['popis']) . "'></td>";
                    echo "<td>
                            <input type='file' name='obrazek' form = 'jidlo_{$row['ID_jidla']}' accept='image/*'>
                            <img src='" . $obrazky_adresar . htmlspecialchars($row['img']) . "' alt='" . htmlspecialchars($row['název']) . "' style='width:100px;height:100px;'>
                          </td>";
                    echo "<td>
                            <form method='post' enctype='multipart/form-data' id = 'jidlo_{$row['ID_jidla']}'>
                            <input type='hidden' form = 'jidlo_{$row['ID_jidla']}' name='id_jidla' value='" . htmlspecialchars($row['ID_jidla']) . "'>
                            <input type='hidden' name='atribut' value='update'>
                            <input type='submit' value='Uložit'>
                            </form>
                            <form method='post'>
                            <input type='hidden' name='id_jidla' value='" . htmlspecialchars($row['ID_jidla']) . "'>
                            <input type='hidden' name='atribut' value='delete'>
                            <input type='submit' value='Odstranit'>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Žádná jídla k zobrazení.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
// Uzavření připojení k databázi
mysqli_close($dbSpojeni);
?>
