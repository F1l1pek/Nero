<?php
/*use Intervention\Image\ImageManagerStatic as Image;
$manager = new ImageManager(
    new Intervention\Image\Drivers\Gd\Driver()
);


$image = $manager->read('obrazky_galerie/20230322_120001.jpg');

// resize image instance
$image->resize(height: 100);

// insert a watermark
$image->place('images/watermark.png');

// encode edited image
$encoded = $image->toJpeg();

// save encoded image
$encoded->save('zkomprimovane_obr/jidla.jpeg');*/
?>



<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domovská stránka</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="lightbox.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="lightbox.js"></script>
</head>
<?php include_once ('header.html');?>
<body>
    <div class="prvni">
            <div class="obrazek-container">
                <img src="obrazky_galerie/20230322_120001.jpg" alt="Popis obrázku">
                    <h2>Jídla</h2>
                    <p class="text">text o jidlechjfhd jkfbhjkdshgjdsb gjkfgbbdnjgbfgj bdfjkgbfjkdgbjf dgbjkpdfgdjkpsbgjkgdsj pgbudsbgjbdsj kgbsjbfjasb fgjipbsd gjipbdipb</p>
                    <a href="https://cafe-neroo.8u.cz/placeholder.php" class="odkaz">
                        <p class="info">Více informací</p>
                    </a>
            </div>
            <div class="obrazek-container">
                <img src="obrazky_galerie/IMG_20230912_113843_706.jpg" alt="Popis obrázku">
                    <h2>Catering</h2>
                    <p class="text">text o cateringujf hdjkfbhjkdshgjdsbgjk fgbbdnjgbfgjb dfjkgbfjkd gbjfdgbjkpdfgd jkpsbgjkgdsj pgbudsbgj bdsjkgbsj bfjasbfgjip bsdgjipbdipb</p>
                    <a href="https://cafe-neroo.8u.cz/svatby_catering/catering.php" class="odkaz">
                        <p class="info">Více informací</p>
                    </a>
            </div>
            <div class="obrazek-container">
                <img src="obrazky_galerie/received_2791426547596365.jpeg" alt="Popis obrázku">
                    <h2>Svatby</h2>
                    <p class="text">text o svatbáchjfhdjk fbhjkdshgjdsbgjkfgbbdn jgbfgjbdfjkgbfjkdg bjfdgbjkpdfgdjkps bgjkgdsjpgbuds bgjbdsjkgbsjbfj asbfgjipbsdgjipbdipb</p>
                    <a href="https://cafe-neroo.8u.cz/svatby_catering/svatby.php" class="odkaz">
                        <p class="info">Více informací</p>
                    </a>
            </div>
    </div>
    <div class="druhy">
        <div class="content-container">
            <div class="text">
                <h2>O nás</h2>
                    <p>
                        Cafe Nero je rodinná kavárna zaměřena na cukrářskou a pekařskou výrobu.
                        Připravujeme tradiční české koláče a moderní dezerty. Naše dezerty jsou
                        bez umělých barviv a konzervantů. Všechny produkty, které nabízíme jsou z kvalitních surovin.
                        Denně pro Vás připravujeme čerstvé bagety, panini, domácí limonády, dezerty,
                        máslavou baklavu, lahodnou kávu.  Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. 
                        Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet.
                        Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper porta.
                        Cafe Nero je rodinná kavárna zaměřena na cukrářskou a pekařskou výrobu.
                        Připravujeme tradiční české koláče a moderní dezerty. Naše dezerty jsou
                        bez umělých barviv a konzervantů. Všechny produkty, které nabízíme jsou z kvalitních surovin.
                        Denně pro Vás připravujeme čerstvé bagety, panini, domácí limonády, dezerty,
                        máslavou baklavu, lahodnou kávu.  Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. 
                        Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet. 
                        Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper porta.
                </p>
                </p>
            </div>
        </div>
            <img src="obrazky_svatby/c42dbc28-9001-4418-be72-ca7d02274f0b.jpeg">
    </div>
    <div class="treti">
        <h2 style="text-align: center;">Galerie</h2>

        <?php
            $directory = "obrazky_galerie/";
            $images = glob($directory . "*");
            if (count($images) > 0) {
                foreach($images as $image) {
                    // Pouze pokud je to obrázek
                    if (is_file($image) && getimagesize($image)) {
                        echo '<a href="' . $image . '" data-lightbox="gallery"><img class="thumbnail" src="' . $image . '" alt="Thumbnail"></a>';
                    }
                }
            } else {
                echo "No images found in the directory.";
            }
        ?>
    </div>
</body>
<?php include_once 'footer.html'; ?>
</html>

<script>
    document.addEventListener("DOMContentLoaded", function() {
  var navbarItems = document.querySelectorAll('.info');

  navbarItems.forEach(function(navbarItem) {
    navbarItem.addEventListener("mouseenter", function() {
      navbarItem.style.opacity = "0.7";
    });

    navbarItem.addEventListener("mouseleave", function() {
      navbarItem.style.opacity = "1";
    });
  });
});
</script>
