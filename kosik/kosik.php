<?php
session_start(); // Inicializace session


// Připojení k databázi
include_once '../db.php';
$dbSpojeni = connectToDB();


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
}
// Inicializace pole pro ukládání položek v košíku
$polozky = array();

// Dotaz na košík pro konkrétního uživatele (např. s ID_U = 1)
$sql = "SELECT jidla.ID_jidla, jidla.`název`, jidla.typ, jidla.cena, jidla.cena_s, jidla.cena_f, košik.mnozstvi, košik.ID_O, košik.ID_U, košik.ID_J, SUM(košik.mnozstvi) AS celkove_mnozstvi
        FROM košik 
        INNER JOIN jidla ON košik.ID_J = jidla.ID_jidla 
        WHERE košik.ID_U = ?";
$stmt = $dbSpojeni->prepare($sql);
$stmt->bind_param("i", $ID_U); // "i" označuje, že se jedná o integer
$stmt->execute();
$vysledek = $stmt->get_result();
// Zpracování výsledku dotazu a uložení položek do pole, pokud košík existuje
if (mysqli_num_rows($vysledek) > 0) {
    while ($radek = mysqli_fetch_assoc($vysledek)) {
        $polozky[] = $radek;
    }
} else {
    // Pokud košík neobsahuje žádné položky, inicializujeme $polozky jako prázdné pole
    $polozky = array();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_order"])) {
    provedObjednavku($ID_U, $typ_uzivatele);
    
    // Přesměrování na stejnou stránku po úspěšném zpracování objednávky
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit(); // Ukončení běhu skriptu po přesměrování
}

// Funkce pro provedení objednávky
function provedObjednavku($ID_U, $typ_uzivatele) {
    global $dbSpojeni, $polozky;

    // Vytvoření nové objednávky
  
    $stav = "zaplaceno"; // Nová objednávka je v stavu "zaplaceno"
    $insert_order_query = "INSERT INTO obednavky (datum, ID_U, stav) VALUES (CURRENT_TIMESTAMP(), '$ID_U' , '$stav')";
    mysqli_query($dbSpojeni, $insert_order_query);

    // Získání ID nově vytvořené objednávky
    $idObjednavky = mysqli_insert_id($dbSpojeni);

    // Vložení položek z košíku do tabulky obednavky_produkty
    foreach ($polozky as $polozka) {
        $idJidla = $polozka['ID_jidla'];
        $mnozstvi = $polozka['celkove_mnozstvi'];
        
        // Přidání ceny položky
        $sql_cena = "SELECT cena, cena_s, cena_f FROM jidla WHERE ID_jidla = $idJidla";
        $result_cena = mysqli_query($dbSpojeni, $sql_cena);
        $radek_cena = mysqli_fetch_assoc($result_cena);
        $cena = $radek_cena['cena'];
        $cena_s = $radek_cena['cena_s'];
        $cena_f = $radek_cena['cena_f'];
        
        if($typ_uzivatele=="velkoodberatel_s"){$cena=$cena_s;}
        if($typ_uzivatele=="velkoodberatel_f"){$cena=$cena_f;}

        // Vložení položky do tabulky obednavky_produkty
        $insert_order_item_query = "INSERT INTO obednavky_produkty (ID_O, ID_J, mnozstvi, cena) VALUES ($idObjednavky, $idJidla, $mnozstvi, $cena)";
        mysqli_query($dbSpojeni, $insert_order_item_query);
    }
    $delete_cart_query = "DELETE FROM košik WHERE ID_U = $ID_U";
    mysqli_query($dbSpojeni, $delete_cart_query);
}
?>

    <link rel="stylesheet" href="../header.css">
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

<?php
include_once '../header.html';
?>
<div class="bod">
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
                if($typ_uzivatele=="admin" || $typ_uzivatele=="běžný"){$subtotal = $polozka['cena'] * $polozka['mnozstvi'];}
                if($typ_uzivatele=="velkoodberatel_s"){$subtotal = $polozka['cena_s'] * $polozka['mnozstvi'];}
                if($typ_uzivatele=="velkoodberatel_f"){$subtotal = $polozka['cena_f'] * $polozka['mnozstvi'];}
               // Add subtotal to the total price
                $celkova_cena += $subtotal;
            ?>
                <tr>
                    <td><?php echo $polozka['název']; ?></td>
                    <td><?php echo $polozka['typ']; ?></td>
                    <?php if($typ_uzivatele=="admin" || $typ_uzivatele=="běžný"): ?><td><?php echo $polozka['cena']; ?> Kč</td><?php endif; ?>
                    <?php if($typ_uzivatele=="velkoodberatel_s"): ?><td><?php echo $polozka['cena_s']; ?> Kč</td><?php endif; ?>
                    <?php if($typ_uzivatele=="velkoodberatel_f"): ?><td><?php echo $polozka['cena_f']; ?> Kč</td><?php endif; ?>
                   <td>
                        <!-- Tlačítka pro přidání a odebrání množství -->
                        <button aria-label="Decrease" onclick="decrement(<?php echo $polozka['ID_jidla']; ?>, <?php echo $polozka['ID_O']; ?>)" class="ikonka">-</button>
<input type="number" id="mnozstvi_<?php echo $polozka['ID_jidla']; ?>" name="mnozstvi_<?php echo $polozka['ID_jidla']; ?>" value="<?php echo $polozka['mnozstvi']; ?>" min="0">
<button aria-label="Increase" onclick="increment(<?php echo $polozka['ID_jidla']; ?>, <?php echo $polozka['ID_O']; ?>)" class="ikonka">+</button>
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
