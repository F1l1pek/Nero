<?php
include_once '../db.php';
$dbSpojeni = connectToDB();

// Adresář pro ukládání obrázků na serveru
$obrazky_adresar = "../obrazky_svatby/";
$sql = "SELECT * FROM svatby";
$vysledek = mysqli_query($dbSpojeni, $sql);
$svatby = mysqli_fetch_all($vysledek, MYSQLI_ASSOC);
?>

    
    <title>Svatby</title>
    <link rel="stylesheet" href="../header.css">
    <link rel="stylesheet" href="svatby.css">


<?php include_once('../header.html');?>
<body>
<div class="prod">
            <div class="bublina">
                <div class="text">
                    <h2>Informace o našich svatbách</h2>
                        <p>
                            Naše specializovaná služba dodání jídla a dekorativních prvků pro svatby vám přináší nejen lahodné chuťové zážitky, 
                            ale také vizuální krásu, která dodá vaší svatbě jedinečný šarm. Nabízíme široký výběr menu od tradičních po moderní a exotické pokrmy, 
                            které splní vaše kulinařské představy. Naše dekorativní prvky jsou pečlivě vybrány tak, aby dokonale ladily s vaším tématem a stylizací svatebního dne. 
                            Od květinových aranžmá po stoleční dekorace a svícny, každý detail je promyšlen s láskou a péčí. S naší spolehlivou službou můžete své hosty ohromit nejen skvělým jídlem, 
                            ale i pohádkovou atmosférou, která zůstane v jejich paměti navždy.</p>
                        <p>V případě zájmu nás kontaktujte zde: <strong>nero@gmail.com</strong></p>
                      
                </div>
            </div>
        </div>
    
        <div class="produkty">
    <?php foreach ($svatby as $svatba): ?>
        <div class="produkt">
            <?php if (!empty($svatba['obrazek'])): ?>
                <div class="obr"> <img src="<?php echo $obrazky_adresar . htmlspecialchars($svatba['obrazek']); ?>" alt="<?php echo htmlspecialchars($svatba['nazev']); ?>"></div>
               
            <?php else: ?>
                <p>Obrázek není k dispozici</p>
            <?php endif; ?>
            <div class="info">
    <div class="title">
        <h2><?php echo $svatba['nazev']; ?></h2>
        <div class="spacer"></div>
        <h2><strong class="cena">Cena:</strong> <?php echo htmlspecialchars($svatba['cena']); ?> Kč</h2>
    </div>
    <p><strong>Popis:</strong> <?php echo htmlspecialchars($svatba['popis']); ?></p>
</div>
        </div>
                <?php endforeach; ?>
                </div>
                </div>
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
