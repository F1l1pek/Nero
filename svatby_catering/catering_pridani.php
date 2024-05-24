<?php
require_once '../db.php';
$dbSpojeni = connectToDB();
$obrazky_adresar = '../obrazky_catering/';

if (!$dbSpojeni) {
    die("Connection failed: " . mysqli_connect_error());
}

// Zpracování formuláře pro přidání nového jídla
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["akce"]) && $_POST["akce"] == "pridat") {
    $nazev = mysqli_real_escape_string($dbSpojeni, $_POST["nazev"]);
    $cena = floatval($_POST["cena"]);
    $popis = mysqli_real_escape_string($dbSpojeni, $_POST["popis"]);

    // Zpracování obrázku
    $obrazek = $_FILES['obr']['name'];

    if ($obrazek) {
        $obrazekTmp = $_FILES['obr']['tmp_name'];
        $obrazekCesta = $obrazky_adresar . basename($obrazek);
        move_uploaded_file($obrazekTmp, $obrazekCesta);
        $sql_insert = "INSERT INTO catering (nazev, cena, popis, obrazek) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($dbSpojeni, $sql_insert);
        mysqli_stmt_bind_param($stmt, "sdss", $nazev, $cena, $popis, basename($obrazek));
        mysqli_stmt_execute($stmt);
        header("Location: catering_pridani.php");
    } else {
        $sql_insert = "INSERT INTO catering (nazev, cena, popis) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($dbSpojeni, $sql_insert);
        mysqli_stmt_bind_param($stmt, "sds", $nazev, $cena, $popis);
        mysqli_stmt_execute($stmt);
        header("Location: catering_pridani.php");
    }
}

// Zpracování formuláře pro úpravu nebo odstranění existujícího jídla
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["id"]) && isset($_POST["atribut"])) {
        $id_catering = $_POST["id"];
        $atribut = $_POST["atribut"];

        if ($atribut == "update") {
            $nazev = mysqli_real_escape_string($dbSpojeni, $_POST["nazev"]);
            $cena = floatval($_POST["cena"]);
            $popis = mysqli_real_escape_string($dbSpojeni, $_POST["popis"]);

            // Zpracování obrázku
            $obrazek = $_FILES['obrazek']['name'];

            if ($obrazek) {
                $obrazekTmp = $_FILES['obrazek']['tmp_name'];
                $obrazekCesta = $obrazky_adresar . basename($obrazek);
                move_uploaded_file($obrazekTmp, $obrazekCesta);
                $sql_update = "UPDATE catering SET nazev = ?, cena = ?, popis = ?, obrazek = ? WHERE id = ?";
                $stmt = mysqli_prepare($dbSpojeni, $sql_update);
                mysqli_stmt_bind_param($stmt, "sdssi", $nazev, $cena, $popis, $obrazekCesta, $id_catering);
            } else {
                $sql_update = "UPDATE catering SET nazev = ?, cena = ?, popis = ? WHERE id = ?";
                $stmt = mysqli_prepare($dbSpojeni, $sql_update);
                mysqli_stmt_bind_param($stmt, "sdsi", $nazev, $cena, $popis, $id_catering);
                echo mysqli_error($dbSpojeni);
            }

            mysqli_stmt_execute($stmt);
        } elseif ($atribut == "delete") {
            $sql_delete = "DELETE FROM catering WHERE id = ?";
            $stmt = mysqli_prepare($dbSpojeni, $sql_delete);
            mysqli_stmt_bind_param($stmt, "i", $id_catering);
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
    <title>Úprava cateringů</title>

    <link rel="stylesheet" href="../kosik/prid_jid.css">
</head>
<body>
    <a href="../ucty/admin.php">Zpět na administraci</a>
    <div class="prid">
        <form method="POST" enctype="multipart/form-data">
            <label for="nazev">nazev:</label>
            <input type="text" name="nazev" id="nazev">
            <label for="cena">Cena:</label>
            <input type="number" name="cena" id="cena">
            <label for="popis">Popis produktu:</label>
            <input type="text" name="popis" id="popis">
            <label for="obr">Obrázek produktu:</label>
            <input type="file" name="obr" id="obr">
            <input type="hidden" name="akce" value="pridat">
            <input type="submit" value="Odeslat">
        </form>
    </div>
<div class="bublina" id="bublina-tabulka-jidel">
    <h1>Úprava svateb</h1>
    <table>
        <thead>
            <tr>
                <th><a href="?order=id">ID</a></th>
                <th><a href="?order=nazev">nazev</a></th>
                <th><a href="?order=cena">Cena</a></th>
                <th>Popis</th>
                <th>Obrázek</th>
                <th>Akce</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql_select_catering = "SELECT * FROM catering";
        
            if (isset($_GET['order'])) {
                $order = $_GET['order'];
                $sql_select_catering .= " ORDER BY $order";
            }
            
            $result_svatba = mysqli_query($dbSpojeni, $sql_select_catering);
            if (mysqli_num_rows($result_svatba) > 0) {
                while ($row = mysqli_fetch_assoc($result_svatba)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td><input type='text' form = 'jidlo_{$row['id']}' name='nazev' value='" . htmlspecialchars($row['nazev']) . "'></td>";
                    echo "<td><input type='number' form = 'jidlo_{$row['id']}' name='cena' value='" . htmlspecialchars($row['cena']) . "' step='0.01'></td>";
                    echo "<td><input type='text' form = 'jidlo_{$row['id']}' name='popis' value='" . htmlspecialchars($row['popis']) . "'></td>";
                    echo "<td>
                            <input type='file' name='obrazek' form = 'jidlo_{$row['id']}' accept='image/*'>
                            <img src='" . $obrazky_adresar . htmlspecialchars($row['obrazek']) . "' alt='" . htmlspecialchars($row['nazev']) . "' style='width:100px;height:100px;'>
                          </td>";
                    echo "<td>
                            <form method='post' enctype='multipart/form-data' id = 'jidlo_{$row['id']}'>
                            <input type='hidden' form = 'jidlo_{$row['id']}' name='id' value='" . htmlspecialchars($row['id']) . "'>
                            <input type='hidden' name='atribut' value='update'>
                            <input type='submit' value='Uložit'>
                            </form>
                            <form method='post'>
                            <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
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
