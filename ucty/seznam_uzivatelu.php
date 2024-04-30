<?php
session_start();
$dbSpojeni = mysqli_connect("localhost", "root", null, "nero");
mysqli_set_charset($dbSpojeni, "UTF8");
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

// Přidání nového uživatele
if (isset($_POST['create_user'])) {
    $jmeno = $_POST['jmeno'];
    $prijmeni = $_POST['prijmeni'];
    $email = $_POST['email'];
    $heslo = password_hash($_POST['heslo'], PASSWORD_DEFAULT);
    $dat_nar = $_POST['dat_nar'];
    $tel_cislo = isset($_POST['tel_cislo']) ? $_POST['tel_cislo'] : null; // Možnost zadání telefonního čísla
    $typ_uzivatele = $_POST['typ_uzivatele'];

    // Kontrola existence e-mailu v databázi
    $stmt_check_email = $dbSpojeni->prepare("SELECT COUNT(*) AS pocet FROM user WHERE email = ?");
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $result_check_email = $stmt_check_email->get_result();
    $row_check_email = $result_check_email->fetch_assoc();
    $pocet_emailu = $row_check_email['pocet'];
    $stmt_check_email->close();

    if ($pocet_emailu > 0) {
        echo "Uživatel s tímto e-mailem již existuje.";
    } else {
        // Pokud e-mail neexistuje, vložíme nového uživatele
        $stmt_create_user = $dbSpojeni->prepare("INSERT INTO user (jmeno, prijmeni, email, heslo, dat_nar, tel_cislo, typ_uzivatele) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_create_user->bind_param("sssssss", $jmeno, $prijmeni, $email, $heslo, $dat_nar, $tel_cislo, $typ_uzivatele);
        if ($stmt_create_user->execute()) {
            echo "Uživatel byl úspěšně vytvořen.";
        } else {
            echo "Chyba při vytváření uživatele: " . $stmt_create_user->error;
        }
        $stmt_create_user->close();
    }
}

// Změna typu uživatele
if (isset($_POST['change_type'])) {
    $user_id = $_POST['user_id'];
    $new_type = $_POST['change_type'];

    $stmt_change_type = $dbSpojeni->prepare("UPDATE user SET typ_uzivatele = ? WHERE ID_user = ?");
    $stmt_change_type->bind_param("si", $new_type, $user_id);
    if ($stmt_change_type->execute()) {
        echo "Typ uživatele byl úspěšně změněn.";
    } else {
        echo "Chyba při změně typu uživatele: " . $stmt_change_type->error;
    }
    $stmt_change_type->close();
}

// Smazání účtu
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['delete_user'];

    $stmt_delete_user = $dbSpojeni->prepare("DELETE FROM user WHERE ID_user = ?");
    $stmt_delete_user->bind_param("i", $user_id);
    if ($stmt_delete_user->execute()) {
        echo "Uživatel byl úspěšně smazán.";
    } else {
        echo "Chyba při mazání uživatele: " . $stmt_delete_user->error;
    }
    $stmt_delete_user->close();
}

// Nastavení výchozího řazení podle ID uživatele
if(isset($_GET['order'])){
    $order = $_GET['order'];
    $sql_select_users = "SELECT ID_user, jmeno, prijmeni, email, tel_cislo, dat_nar, typ_uzivatele FROM user ORDER BY ?";
    $stmt = $dbSpojeni->prepare($sql_select_users);
    $stmt->bind_param("s", $order);
    $stmt->execute();
    $result_users = $stmt->get_result();
} else {
    // Pokud není zadané žádné řazení, výběr uživatelů bez řazení
    $sql_select_users = "SELECT ID_user, jmeno, prijmeni, email, tel_cislo, dat_nar, typ_uzivatele FROM user";
    $result_users = mysqli_query($dbSpojeni, $sql_select_users);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam uživatelů</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="bublina" id="bublina-priprava-jidel">
    <h1>Vytvoření uživatele</h1>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="form-ohraniceni">
        <label for="jmeno">Jméno:</label>
        <input type="text" name="jmeno" required><br>
        <label for="prijmeni">Příjmení:</label>
        <input type="text" name="prijmeni" required><br>
        <label for="email">Email:</label>
        <input type="email" name="email" required><br>
        <label for="heslo">Heslo:</label>
        <input type="password" name="heslo" required><br>
        <label for="dat_nar">Datum narození:</label>
        <input type="date" name="dat_nar" required><br>
        <label for="tel_cislo">Telefonní číslo:</label>
        <input type="text" name="tel_cislo" pattern="[0-9]{9}" title="Telefonní číslo musí být devítimístné číslo" placeholder="Např.: 123456789"><br>
        <label for="typ_uzivatele">Typ uživatele:</label>
        <select name="typ_uzivatele" required>
            <option value="běžný">Běžný</option>
            <option value="admin">Admin</option>
            <option value="velkoodberatel">Velkoodběratel</option>
        </select><br>
        <button type="submit" name="create_user" class="navrat-button">Vytvořit uživatele</button>
    </form>
</div>

<div class="bublina" id="navrat-bublina">
    <a href="admin.php" class="navrat-button">Zpět na administrační panel</a>
</div>

<div class="bublina" id="bublina-tabulka-jidel">
    <h1>Seznam uživatelů</h1>
    <table>
        <thead>
            <tr>
                <th><a href="?sort=ID_user">ID</a></th>
                <th><a href="?sort=jmeno">Jméno</a></th>
                <th><a href="?sort=prijmeni">Příjmení</a></th>
                <th><a href="?sort=email">Email</a></th>
                <th><a href="?sort=tel_cislo">Telefonní číslo</a></th>
                <th><a href="?sort=dat_nar">Datum narození</a></th>
                <th><a href="?sort=typ_uzivatele">Typ uživatele</a></th>
                <th>Operace</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result_users)) { ?>
                <tr>
                <td><?php echo $row['ID_user']; ?></td>
                    <td><?php echo $row['jmeno']; ?></td>
                    <td><?php echo $row['prijmeni']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['tel_cislo']; ?></td>
                    <td><?php echo $row['dat_nar']; ?></td>
                    <td>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <input type="hidden" name="user_id" value="<?php echo $row['ID_user']; ?>">
                            <select name="change_type">
                                <option value="běžný" <?php echo ($row['typ_uzivatele'] === 'běžný') ? 'selected' : ''; ?>>Běžný</option>
                                <option value="admin" <?php echo ($row['typ_uzivatele'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="velkoodberatel" <?php echo ($row['typ_uzivatele'] === 'velkoodberatel') ? 'selected' : ''; ?>>Velkoodberatel</option>
                            </select>
                            <button type="submit">Uložit</button>
                        </form>
                    </td>
                    <td>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <input type="hidden" name="delete_user" value="<?php echo $row['ID_user']; ?>">
                            <button type="submit">Smazat</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>