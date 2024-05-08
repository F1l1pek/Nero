<?php
session_start();
// Připojení k databázi
$servername = "localhost";
$username = "root";
$password = null;
$dbname = "nero";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Načtení typu uživatele z databáze podle e-mailu, pokud je uživatel přihlášený
$typ_uzivatele = "standard";
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql_user = "SELECT typ_uzivatele FROM user WHERE email = '$email'";
    $result_user = $conn->query($sql_user);
    if ($result_user->num_rows == 1) {
        $row_user = $result_user->fetch_assoc();
        $typ_uzivatele = $row_user['typ_uzivatele'];
    }
}

// Načtení dat z databáze jídel podle filtru, pokud je vybrán
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Výběr jídel</title>
    <style>
        img {
            max-width: 20px; 
            height: auto; 
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include_once '../header.html'; ?>

    <!-- Filtr -->
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>" method="post" id="filtr">
        <h2 style="text-align: center;">Filtr</h2> 
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

    <!-- Seznam jídel -->
    <h1>Seznam jídel</h1>
    <table>
        <tr>
            <th>Název</th>
            <th>Typ</th>
            <th>Popis</th>
            <th>Cena</th>
            <th>Obrázek</th>
            <th>Akce</th>
        </tr>
        <?php
        if ($result_jidla->num_rows > 0) {
            while ($row_jidla = $result_jidla->fetch_assoc()) {
                // Výpis řádku tabulky
                echo "<tr>";
                echo "<td>" . $row_jidla['název'] . "</td>";
                echo "<td>" . $row_jidla['typ'] . "</td>";
                echo "<td>" . $row_jidla['popis'] . "</td>";
                echo "<td>" . $row_jidla['cena'] . "</td>";
                echo "<td><img src='../obrazky_jidla/" . $row_jidla['img'] . "' alt='Obrázek'></td>";
                // Přidání tlačítka "Přidat do košíku" s voláním JavaScript funkce
                echo "<td><button onclick='addToCart(" . $row_jidla['ID_jidla'] . ")'>Přidat do košíku</button></td>";
                echo "</tr>";
            }
        } else {
            echo "Žádná data k dispozici.";
        }
        ?>
    </table>

    <!-- Obsah košíku -->
    <div id="cartContent">
        <?php
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            echo "<h2>Obsah košíku:</h2>";
            echo "<ul>";
            foreach ($_SESSION['cart'] as $item) {
                if (isset($item['nazev']) && isset($item['mnozstvi'])) {
                    echo "<li>" . $item['nazev'] . " - Cena: " . $item['cena'] . " Kč - Množství: " . $item['mnozstvi'] . "</li>";
                } else {
                    echo "<li>- Název není k dispozici: Cena není k dispozici Kč - Množství: Množství není k dispozici</li>";
                }
            }
            echo "</ul>";
        } else {
            echo "Košík je prázdný.";
        }
        ?>
    </div>

    <!-- JavaScript -->
    <script>
        function addToCart(id) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    if (response.status === 'success') {
                        updateCartContent();
                        alert("Jídlo bylo přidáno do košíku.");
                    } else {
                        alert("Přidání do košíku selhalo.");
                    }
                }
            };
            xhttp.open("GET", "add_to_cart.php?id=" + id, true);
            xhttp.send();
        }

        function updateCartContent() {
            var cartContainer = document.getElementById("cartContent");
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    cartContainer.innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "cart_content.php", true);
            xhttp.send();
        }
    </script>
</body>
</html>
