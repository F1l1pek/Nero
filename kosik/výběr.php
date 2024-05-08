<?php
session_start();

include_once '../db.php';
$conn = connectToDB();
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

    <!-- Header -->
<?php include_once '../header.html'; ?>
    <style>
        img {
            max-width: 20px;
            height: auto;
        }
    </style>
    <script src="vyber.js"></script>

    <title>Výběr jídel</title>

    <!-- Filtr -->
    <form method="post" id="filtr">
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
                //if session is set then it will show how many items there are in the cart
                $cartItem = $_SESSION['cart'][$row_jidla['ID_jidla']];
                if ($cartItem) {
                    // If item is already in the cart, show the quantity and a button for adding more or removing
                    echo "<td class='cart-item' >";
                    echo "<button aria-label='Decrease'  class='decrease'>-</button>";
                    echo "<input type='number' data-id='{$row_jidla['ID_jidla']}' value='{$cartItem['mnozstvi']}' min='0'>";
                    echo "<button aria-label='Increase' class='increase'>+</button>";
                    echo "</td>";
                } else {
                    echo "<td class='cart-item' >
                            <button class='add-to-cart' data-id='{$row_jidla['ID_jidla']}'>Přidat do košíku</button>
                          </td>";
                }
                echo "</tr>";
            }
        } else {
            echo "Žádná data k dispozici.";
        }
        ?>
    </table>
</body>
</html>
