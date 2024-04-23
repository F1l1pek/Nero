<?php
session_start(); // Inicializace session

// Připojení k databázi
$server = "localhost";
$username = "root";
$password = ""; // Pokud máte heslo, vyplňte ho
$database = "nero";

// Připojení k databázi
$dbSpojeni = mysqli_connect($server, $username, $password, $database);

// Kontrola připojení
if (!$dbSpojeni) {
    die("Chyba při připojení k databázi: " . mysqli_connect_error());
}

if (isset($_POST['submit_order'])) {
    provedObjednavku();
}

// Inicializace pole pro ukládání položek v košíku
$polozky = array();

// Dotaz na košík pro konkrétního uživatele (např. s ID_U = 1)
$sql = "SELECT jidla.ID_jidla, jidla.název, jidla.typ, jidla.cena, košik.mnozstvi, košik.ID_O, košik.ID_U, košik.ID_J, SUM(košik.mnozstvi) AS celkove_mnozstvi
        FROM košik 
        INNER JOIN jidla ON košik.ID_J = jidla.ID_jidla 
        WHERE košik.ID_U = 1
        GROUP BY jidla.ID_jidla"; // Změňte ID_U podle aktuálního uživatele
$vysledek = mysqli_query($dbSpojeni, $sql);

// Zpracování výsledku dotazu a uložení položek do pole, pokud košík existuje
if (mysqli_num_rows($vysledek) > 0) {
    while ($radek = mysqli_fetch_assoc($vysledek)) {
        $polozky[] = $radek;
    }
} else {
    // Pokud košík neobsahuje žádné položky, inicializujeme $polozky jako prázdné pole
    $polozky = array();
}

// Funkce pro provedení objednávky
function provedObjednavku() {
    global $dbSpojeni, $polozky;

    // Vytvoření nové objednávky
    $idUzivatele = 1; // ID uživatele (změňte podle aktuálního uživatele)
    $stav = "zaplaceno"; // Nová objednávka je v stavu "zaplaceno"
    $insert_order_query = "INSERT INTO obednavky (datum, ID_U, stav) VALUES (CURRENT_TIMESTAMP(), $idUzivatele, '$stav')";
    mysqli_query($dbSpojeni, $insert_order_query);

    // Získání ID nově vytvořené objednávky
    $idObjednavky = mysqli_insert_id($dbSpojeni);

    // Vložení položek z košíku do tabulky obednavky_produkty
    if (is_array($polozky) && count($polozky) > 0) {
        foreach ($polozky as $polozka) {
            $idJidla = $polozka['ID_jidla'];
            $mnozstvi = $polozka['celkove_mnozstvi'];
            $insert_order_item_query = "INSERT INTO obednavky_produkty (ID_O, ID_J, mnozstvi) VALUES ($idObjednavky, $idJidla, $mnozstvi)";
            mysqli_query($dbSpojeni, $insert_order_item_query);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="kosik_st.css" rel="stylesheet" type="text/css">
    <title>Košík</title>
    <style>
        /* Styl pro tlačítka */
        .ikonka {
            margin-left: 5px;
            margin-right: 5px;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        
    </style>
</head>
<body>
<?php
include '../header.html';
?><div class="bod">
    <h1>Košík</h1>
    <form method="post">
        <table>
            <tr>
                <th>Název jídla</th>
                <th>Typ</th>
                <th>Cena za kus</th>
                <th>Množství</th>
                <th>Celková cena</th>
            </tr>
            <?php
            // Initial total price variable
            $celkova_cena = 0;

            // Loop through items in the basket
            foreach ($polozky as $polozka):
                // Calculate subtotal for each item
                $subtotal = $polozka['cena'] * $polozka['mnozstvi'];
                // Add subtotal to the total price
                $celkova_cena += $subtotal;
            ?>
                <tr>
                    <td><?php echo $polozka['název']; ?></td>
                    <td><?php echo $polozka['typ']; ?></td>
                    <td><?php echo $polozka['cena']; ?> Kč</td>
                    <td>
                        <!-- Tlačítka pro přidání a odebrání množství -->
                        <span class="ikonka" onclick="decrement(<?php echo $polozka['ID_jidla']; ?>, <?php echo $polozka['ID_O']; ?>)">-</span>
                        <input type="number" id="mnozstvi_<?php echo $polozka['ID_jidla']; ?>" name="mnozstvi_<?php echo $polozka['ID_jidla']; ?>" value="<?php echo $polozka['mnozstvi']; ?>" min="0">
                        <span class="ikonka" onclick="increment(<?php echo $polozka['ID_jidla']; ?>, <?php echo $polozka['ID_O']; ?>)">+</span>
                    </td>
                    <td><?php echo $subtotal; ?> Kč</td>
                </tr>
            <?php endforeach; ?>
            <!-- Zobrazení celkové ceny košíku -->
            <tr>
                <td colspan="4"><strong>Celková cena košíku:</strong></td>
                <td id="celkova_cena"><strong><?php echo $celkova_cena; ?> Kč</strong></td>
            </tr>
        </table>
        <button type="submit" name="submit_order">Provést objednávku</button>
    </form>

    <script>
        // JavaScript functions
        function increment(itemId, orderId) {
            var input = document.getElementById('mnozstvi_' + itemId);
            var value = parseInt(input.value);
            input.value = value + 1;
            updateQuantity(itemId, value + 1, orderId);
        }

        function decrement(itemId, orderId) {
            var input = document.getElementById('mnozstvi_' + itemId);
            var value = parseInt(input.value);
            if (value > 0) {
                input.value = value - 1;
                updateQuantity(itemId, value - 1, orderId);
            }
        }

        function updateQuantity(itemId, quantity, orderId) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_quantity.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log(xhr.responseText);
                    updateTotalPrice(); // Update total price after updating quantity
                }
            };
            xhr.send('item_id=' + itemId + '&quantity=' + quantity + '&order_id=' + orderId);
        }

        function updateTotalPrice() {
            var celkovaCena = 0;
            var rows = document.querySelectorAll('table tr');
            
            rows.forEach(function (row, index) {
                if (index !== 0 && index !== rows.length - 1) {
                    var cells = row.querySelectorAll('td');
                    var cenaZaKus = parseFloat(cells[2].textContent); // Cena za kus
                    var mnozstviInput = cells[3].querySelector('input');
                    var mnozstvi = parseInt(mnozstviInput.value); // Množství
                    var celkovaCenaPolozky = cenaZaKus * mnozstvi;
                    cells[4].textContent = celkovaCenaPolozky.toFixed(2) + ' Kč'; // Aktualizace celkové ceny položky

                    celkovaCena += celkovaCenaPolozky;
                }
            });

            // Aktualizace celkové ceny košíku
            document.getElementById('celkova_cena').textContent = celkovaCena.toFixed(2) + ' Kč';
        }
    </script>
    </div>
</body>
</html>