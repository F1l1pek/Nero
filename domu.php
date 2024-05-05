<?php
$dbSpojeni = mysqli_connect("localhost", "root", null, "nero");
mysqli_set_charset($dbSpojeni, "UTF8");

$obrazky_adresar = "obrazky_galerie/";

$sql = "SELECT * FROM obrazky";
$vysledek = mysqli_query($dbSpojeni, $sql);
if ($vysledek) {
    $obrazky = mysqli_fetch_all($vysledek, MYSQLI_ASSOC);
} else {
    echo "Chyba při získávání dat: " . mysqli_error($dbSpojeni);
    $obrazky = [];
}
?>

<link rel="stylesheet" href="style.css">

<?php include('header.html');?>

    <main>
        <div class="main">
            <div class="nadpis-bublina">
                <h1>O nás</h1>
            </div>
            <div class="text">
                <h2>Kdo jsme?</h2>
                <p>Cafe Nero je rodinná kavárna zaměřená na cukrářskou a pekařskou výrobu.</p>
                <h2>Co připravujeme?</h2>
                <p>Připravujeme tradiční české koláče a moderní dezerty. Naše dezerty jsou bez umělých barviv a konzervantů. Všechny produkty, které nabízíme, jsou z kvalitních surovin.</p>
                <p><strong>Denně</strong> pro Vás připravujeme čerstvé bagety, panini, domácí limonády, dezerty, máslavou baklavu a lahodnou kávu.</p>
            </div>
        </div>
        <div class="galerie-bublina">
            <h2>Galerie</h2>
        </div>
        <div class="obr galerie">
    <?php foreach ($obrazky as $obrazek): ?>
        <div class="produkt">
            <?php if (!empty($obrazek['obrazek'])): ?>
                <div class="obrazek-bublina">
                    <img src="<?php echo $obrazky_adresar . $obrazek['obrazek']; ?>" alt="<?php echo $obrazek['nazev']; ?>">
                </div>
            <?php else: ?>
                <p>Obrázek není k dispozici</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

    </main>
    <?php include 'footer.html'; ?>
