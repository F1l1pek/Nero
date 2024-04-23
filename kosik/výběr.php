<?php
session_start();
$dbSpojeni = mysqli_connect("localhost", "root", null, "nero");
mysqli_set_charset($dbSpojeni, "UTF8");

// Adresář pro ukládání obrázků na serveru
$obrazky_adresar = "../obrazky_jidla/";

// Zpracování filtru podle typu
if (isset($_POST['typ_jidla'])) { 
    $typ_jidla = $_POST['typ_jidla'];
    if (!$typ_jidla) {
        $sql = "SELECT * FROM jidla";
    } else {
        $typ_jidla = mysqli_real_escape_string($dbSpojeni, $typ_jidla);
        $sql = "SELECT * FROM jidla WHERE typ = '$typ_jidla'";
    }
    $vysledek = mysqli_query($dbSpojeni, $sql);
    if ($vysledek) {
        $jidla = mysqli_fetch_all($vysledek, MYSQLI_ASSOC);
    } else {
        echo "Chyba při získávání dat: " . mysqli_error($dbSpojeni);
        $jidla = [];
    }
} else {
    // Načtení všech jídel z databáze
    $sql = "SELECT * FROM jidla";
    $vysledek = mysqli_query($dbSpojeni, $sql);
    if ($vysledek) {
        $jidla = mysqli_fetch_all($vysledek, MYSQLI_ASSOC);
    } else {
        echo "Chyba při získávání dat: " . mysqli_error($dbSpojeni);
        $jidla = [];
    }
}

// Zpracování změny množství a uložení do košíku
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'pridat') {
    $id_jidla = $_POST['ID_j'];
    $mnozstvi = $_POST['mnozstvi'];

    // Uložení změněného množství do košíku
    $sql = "INSERT INTO košik (ID_U, ID_J, mnozstvi) VALUES ('1', '$id_jidla', '$mnozstvi') ON DUPLICATE KEY UPDATE mnozstvi = '$mnozstvi'";
    $vysledek = mysqli_query($dbSpojeni, $sql);
    if (!$vysledek) {
        echo "Chyba při přidávání jídla: " . mysqli_error($dbSpojeni);
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="jidla.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="../footer.css">
    <title>Všechna jídla</title>
</head>
<body>
<?php include('../header.html');?>
<div class="container">
    <div class="left-column"></div> <!-- Hnědý sloupec vlevo -->
    <div class="content">
        <div class="nadpis-bublina">
            <h1 style="text-align: center;">Všechna jídla</h1> <!-- Přidáme centrování -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="filtr">
                <h2 style="text-align: center;">Filtr</h2> <!-- Přidáme centrování -->
                <label for="typ_jidla">Vyberte typ jídla:</label>
                <select name="typ_jidla" id="typ_jidla">
                    <option value="">Všechny typy</option>
                    <option value="Bagety">Bagety</option>
                    <option value="Chlebíčky">Chlebíčky</option>
                    <option value="Kaiserky">Kaiserky</option>
                    <option value="Croissanty">Croissanty</option>
                    <option value="Dezerty">Dezerty</option>
                </select>
                <button type="submit">Filtrovat</button>
            </form>
        </div>
        <div class="produkty">
            <?php if (!empty($jidla)): ?>
                <?php foreach ($jidla as $jidlo): ?>
                    <div class="produkt">
                        <?php if (!empty($jidlo['img'])): ?>
                            <img src="<?php echo $obrazky_adresar . $jidlo['img']; ?>" alt="<?php echo $jidlo['název']; ?>">
                        <?php else: ?>
                            <p>Obrázek není k dispozici</p>
                        <?php endif; ?>
                        <div class="produkt-info">
                            <h2><?php echo $jidlo['název']; ?></h2>
                            <p><strong>Typ:</strong> <?php echo $jidlo['typ']; ?></p>
                            <p><strong>Cena:</strong> <?php echo $jidlo['cena']; ?> Kč</p>
                            <p><strong>Popis:</strong> <?php echo $jidlo['popis']; ?></p>
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                <input type="hidden" name="ID_j" value="<?php echo $jidlo['ID_jidla']; ?>">
                                <div class="center">
                                    <span class="ikonka" onclick="decrement(<?php echo $jidlo['ID_jidla']; ?>)">-</span>
                                    <input type="number" name="mnozstvi" id="mnozstvi_<?php echo $jidlo['ID_jidla']; ?>" value="0" min="0">
                                    <span class="ikonka" onclick="increment(<?php echo $jidlo['ID_jidla']; ?>)">+</span>
                                </div>
                                <div class="center">
                                    <input type="hidden" name="action" value="pridat">
                                    <button type="submit" class="button-pridat">Přidat do košíku</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nebyla nalezena žádná jídla.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="right-column"></div> <!-- Hnědý sloupec vpravo -->
</div>
<script>
    function increment(itemId) {
        var input = document.getElementById('mnozstvi_' + itemId);
        var value = parseInt(input.value);
        input.value = value + 1;
        updateQuantity(itemId, value + 1);
    }

    function decrement(itemId) {
        var input = document.getElementById('mnozstvi_' + itemId);
        var value = parseInt(input.value);
        if (value > 0) {
            input.value = value - 1;
            updateQuantity(itemId, value - 1);
        }
    }

    function updateQuantity(itemId, quantity) {
        // Poslat požadavek na server pro aktualizaci množství v databázi
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_quantity.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                // Zpracování odpovědi
                console.log(xhr.responseText);
            }
        };
        xhr.send('item_id=' + itemId + '&quantity=' + quantity);
    }
</script>
<footer>
    <div class="container">
        <div class="footer-columns">
            <div class="footer-column">
                <h3>Adresa</h3>
                <ul>
                    <li>Matiční 2740</li>
                    <li>Moravská Ostrava 70200</li>
                    <li>Vchod z boku Střední průmyslové školy elektrotechniky a informatiky</li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Kontakt</h3>
                <ul>
                    <li>Telefon: 773 171 883</li>
                    <li>IČO: 08812683</li>
                    <li><a href="https://www.instagram.com/cafenero2022/" target="_blank">Instagram</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Otevírací doba</h3>
                <ul>
                    <li>Po - Pá: 8:00 - 16:00</li>
                </ul>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
