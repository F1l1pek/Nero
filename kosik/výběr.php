<?php
session_start(); // Spuštění relace
var_dump($_SESSION);

// Připojení k databázi
$servername = "localhost";
$username = "root";
$password = null;
$dbname = "nero";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Načtení typu uživatele z databáze podle e-mailu
$email = $_SESSION['email'];
$sql_user = "SELECT typ_uzivatele FROM user WHERE email = '$email'";
$result_user = $conn->query($sql_user);
if ($result_user->num_rows == 1) {
    $row_user = $result_user->fetch_assoc();
    $typ_uzivatele = $row_user['typ_uzivatele'];
} else {
    // Pokud se nepodaří načíst typ uživatele, použijeme výchozí hodnotu
    $typ_uzivatele = "standard"; // Například standardní uživatel
}

// Načtení dat z databáze jidla podle filtru, pokud je vybrán
if (isset($_POST['typ_jidla']) && !empty($_POST['typ_jidla'])) { 
    $typ_jidla = $_POST['typ_jidla'];
    $sql_jidla = "SELECT * FROM jidla WHERE typ = ?";
    $stmt_jidla = $conn->prepare($sql_jidla);
    $stmt_jidla->bind_param("s", $typ_jidla);
    $stmt_jidla->execute();
    $result_jidla = $stmt_jidla->get_result();
} else {
    // Načtení všech jídel z databáze
    $sql_jidla = "SELECT * FROM jidla";
    $result_jidla = $conn->query($sql_jidla);
}

// Funkce pro přidání produktu do košíku
function addToCart($productId) {
    $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : array();
    $cart[$productId] = isset($cart[$productId]) ? $cart[$productId] + 1 : 1;
    setcookie('cart', json_encode($cart), time() + (86400 * 30), "/"); // Uložení košíku do cookies na 30 dní
}

// Funkce pro vynulování obsahu košíku
function resetCart() {
    setcookie('cart', '', time() - 3600, "/"); // Nastavení vypršení platnosti cookie na minulý čas, čímž ji vymaže
}

// Pokud bylo stisknuto tlačítko "Přidat do košíku"
if (isset($_POST['addToCart'])) {
    $productId = $_POST['productId'];
    addToCart($productId);
}

// Pokud bylo stisknuto tlačítko "Vynulovat košík"
if (isset($_POST['resetCart'])) {
    resetCart();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Výběr jídel</title>
    <style>
        img {
            max-width: 20px; /* Omezení šířky obrázku na 20px */
            height: auto; /* Zachování poměru stran */
        }
    </style>
</head>
<body>
    <?php
    include '../header.html';
    ?>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>" method="post" id="filtr">
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

    <h1>Seznam jídel</h1>
    <table>
        <tr>
            <th>Název</th>
            <th>Typ</th>
            <th>Popis</th>
            <th>Cena</th>
            <th>Obrázek</th>
            <th>Přidat do košíku</th>
        </tr>
        <?php
        if ($result_jidla->num_rows > 0) {
            // Výpis dat
            while($row_jidla = $result_jidla->fetch_assoc()) {
                // Určení ceny jídla podle typu uživatele
                switch ($typ_uzivatele) {
                    case "velkoodberatel_f":
                        $cena = $row_jidla['cena_f'];
                        break;
                    case "velkoodberatel_s":
                        $cena = $row_jidla['cena_s'];
                        break;
                    default:
                        $cena = $row_jidla['cena'];
                        break;
                }

                // Výpis řádku tabulky
                echo "<tr>";
                echo "<td>".$row_jidla['název']."</td>";
                echo "<td>".$row_jidla['typ']."</td>";
                echo "<td>".$row_jidla['popis']."</td>";
                echo "<td>".$cena."</td>";
                echo "<td><img src='../obrazky_jidla/".$row_jidla['img']."' alt='Obrázek'></td>";
                echo "<td>
                        <form method='post'>
                            <input type='hidden' name='productId' value='".$row_jidla['ID_jidla']."'>
                            <button type='submit' name='addToCart'>Přidat do košíku</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "Žádná data k dispozici.";
        }
        $conn->close();
        ?>
    </table>

<?php
// Vypsání obsahu košíku, pokud existuje
if (isset($_COOKIE['cart'])) {
    echo "<h2>Obsah košíku:</h2>";
    $cartArray = $_COOKIE['cart']; // Přečteme hodnotu z cookie

    // Kontrola, zda $cartArray je pole
    if (!is_array($cartArray)) {
        echo "Chyba: Obsah košíku není ve správném formátu.";
    } else {
        foreach ($cartArray as $productId => $quantity) {
            echo "Produkt ID: $productId, Množství: $quantity<br>";
        }
        // Tlačítko pro vynulování košíku
        echo "<form method='post'>
                <button type='submit' name='resetCart'>Vynulovat košík</button>
              </form>";
    }
} else {
    echo "<p>Košík je prázdný.</p>";
}
?>
</body>
</html>
