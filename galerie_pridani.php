<?php
session_start();
include_once 'db.php';
$dbSpojeni = connectToDB();

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

// Adresář pro ukládání obrázků galerie na serveru
$obrazky_adresar = "obrazky_galerie/";

// Náhodný název pro obrázek
function guidv4() {
    // Generování náhodného řetězce s délkou 32 znaků (256 bitů)
    return bin2hex(random_bytes(16)); // Vrací 32 znaků, každý znak reprezentuje 4 bity
}

// Odstranění obrázku
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    if (!empty($delete_id) && is_numeric($delete_id)) { // Ověření, zda je delete_id platné číslo
        // Příprava dotazu pomocí předpřipraveného dotazu pro získání názvu obrázku
        $sql_select_obrazek = "SELECT obrazek FROM obrazky WHERE id = ?";
        
        // Použití předpřipraveného dotazu
        $stmt = $dbSpojeni->prepare($sql_select_obrazek);
        $stmt->bind_param("i", $delete_id); // "i" označuje, že se jedná o integer
        $stmt->execute();
        $result_select = $stmt->get_result();
        
        // Ověření, zda byl vrácen nějaký výsledek
        if ($result_select->num_rows === 1) {
            // Získání názvu obrázku
            $row = $result_select->fetch_assoc();
            $obrazek = $row['obrazek'];
            
            // Odstranění záznamu z databáze
            $sql_delete = "DELETE FROM obrazky WHERE id = ?";
            
            // Použití předpřipraveného dotazu pro smazání záznamu
            $stmt_delete = $dbSpojeni->prepare($sql_delete);
            $stmt_delete->bind_param("i", $delete_id); // "i" označuje, že se jedná o integer
            if ($stmt_delete->execute()) {
                // Odstranění souboru z disku
                $obrazek_path = $obrazky_adresar . $obrazek;
                if (unlink($obrazek_path)) {
                    echo "Obrázek byl úspěšně smazán.";
                } else {
                    echo "Chyba při mazání souboru obrázku z disku.";
                }
            } else {
                echo "Chyba při mazání záznamu obrázku z databáze: " . $stmt_delete->error;
            }
        } else {
            echo "Obrázek nenalezen v databázi.";
        }
    } else {
        echo "Neplatné ID obrázku.";
    }
}

// Přidání nového obrázku
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["obrazek"]) && isset($_POST["nazev"])) {
        $nazev_obrazku = $_POST["nazev"];
        $obrazek = $_FILES["obrazek"];
        $novy_nazev_obrazku = guidv4(); // Generování nového náhodného názvu obrázku
        $nazev_souboru = basename($novy_nazev_obrazku);

        if (move_uploaded_file($obrazek["tmp_name"], $obrazky_adresar . $nazev_souboru)) {
            $sql_insert = "INSERT INTO obrazky (obrazek, nazev) VALUES (?, ?)";
            $stmt_insert = mysqli_prepare($dbSpojeni, $sql_insert);
            
            if ($stmt_insert) {
                mysqli_stmt_bind_param($stmt_insert, "ss", $nazev_souboru, $nazev_obrazku);
                if (mysqli_stmt_execute($stmt_insert)) {
                    echo "Nový obrázek byl úspěšně přidán do galerie.";
                } else {
                    echo "Chyba při přidávání nového obrázku do galerie: " . mysqli_error($dbSpojeni);
                }
                mysqli_stmt_close($stmt_insert);
            } else {
                echo "Chyba při přípravě dotazu na přidání nového obrázku do galerie.";
            }
        } else {
            echo "Nahrání nového obrázku do galerie selhalo.";
        }
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
    <title>Úprava galerie</title>
    <style>
        /* Styl pro omezení velikosti obrázků */
        .obrazek img {
            max-width: 85%;
            max-height: 85%;
        }
    </style>
    <link rel="stylesheet" href="admin.css"> <!-- Odkaz na nový CSS soubor -->
</head>
<body>

<div class="bublina" id="bublina-pridani-obrazku">
    <h1>Přidání obrázku do galerie</h1> <!-- Popis přidávání obrázku do galerie nad formulářem -->
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>" method="post" enctype="multipart/form-data" class="form-ohraniceni">
        <label for="obrazek">Vyberte obrázek:</label>
        <input type="file" name="obrazek" id="obrazek" accept="image/*" required><br>
        <label for="nazev">Název obrázku:</label>
        <input type="text" name="nazev" id="nazev" required><br>
        <input type="submit" value="Přidat do galerie">
    </form>
</div>


<!-- Tlačítko pro návrat na admin.php -->
<div class="bublina" id="bublina">
<a href="admin.php" class="navrat-button">Zpět na administrační panel</a>
</div>

<!-- Zobrazení galerie -->
<div class="bublina" id="bublina-galerie">
    <h1>Galerie</h1> <!-- Popis galerie -->
    <!-- Galerie -->
    <div class="galerie">
    <!-- PHP kód pro zobrazení obrázků v galerii -->
    <?php
    $sql_select_obrazky = "SELECT * FROM obrazky";
    $result_obrazky = mysqli_query($dbSpojeni, $sql_select_obrazky);

    if (mysqli_num_rows($result_obrazky) > 0) {
        while ($row = mysqli_fetch_assoc($result_obrazky)) {
            echo "<div class='obrazek'>";
            echo "<img src='" . $obrazky_adresar . $row['obrazek'] . "' alt='" . $row['nazev'] . "'>"; // Úprava cesty k obrázku
            echo "<p>" . $row['nazev'] . "</p>"; // Přidání názvu obrázku pod obrázek
            echo "<a href='?delete_id=".$row['id']."'>Smazat</a>"; // Odkaz pro smazání obrázku
            echo "</div>";
        }
    } else {
        echo "<p>Žádné obrázky k zobrazení.</p>";
    }
    ?>
</div>
</div>

</body>
</html>
