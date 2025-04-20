<?php
declare(strict_types=1);

// Initialisation des variables pour éviter les erreurs
$pageTitle = $pageTitle ?? "SkyTrack";
$logoPath = $logoPath ?? "images/Logo.png";
$lang = $_GET['lang'] ?? 'fr';

// Vérifier le cookie pour le style
$style = isset($_COOKIE['style']) ? $_COOKIE['style'] : ($_GET['style'] ?? 'standard');
$style = $style === "alternatif" ? "styles_alt.css" : "styles.css";

// Vérifier si l'utilisateur a accepté ou refusé les cookies
$cookiesAccepted = isset($_COOKIE['cookiesAccepted']) ? true : false;
$cookiesDeclined = isset($_COOKIE['cookiesDeclined']) ? true : false;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="<?= htmlspecialchars($style) ?>"/>
</head>
<body class="<?= $style === 'styles_alt.css' ? 'night-mode' : 'day-mode' ?> <?= $cookiesAccepted ? 'cookies-accepted' : ($cookiesDeclined ? 'cookies-declined' : '') ?>">

    <header>
        <div class="header-content">
            <img src="<?= htmlspecialchars($logoPath) ?>" alt="Logo" id="logo"/>
            <h1><?= htmlspecialchars($pageTitle) ?></h1>

            <!-- Bouton unique pour changer de mode -->
            <div class="mode-toggle" onclick="toggleMode()">
                <?= $style === 'styles_alt.css' ? '☀️' : '🌙' ?>
            </div>

            <!-- Barre de recherche sous le titre dans le header -->
            <div class="search-container">
                <form method="get" action="meteo.php">
                    <input type="text" name="ville" id="search-input" placeholder="Rechercher une ville" required="required"/>
                    <button type="submit">Rechercher</button>
                </form>
            </div>
        </div>
        
        <!-- Menu de navigation -->
        <div class="menu-container">
            <table class="menu-table">
                <tr>
                    <?php 
                    // Pages à afficher dans le menu avec htmlspecialchars pour éviter les erreurs liées aux caractères spéciaux
                    $pages = [
                        "index" => "Accueil", 
                        "map" => "Météo", 
                        "stats" => "Statistique"
                    ];
                    
                    // Générer les liens correctement échappés avec http_build_query()
                    foreach ($pages as $page => $label) : 
                        // Préparer les paramètres
                        $params = [
                            'style' => htmlspecialchars($_GET['style'] ?? 'standard'),
                            'lang' => htmlspecialchars($lang)
                        ];
                        $url = $page . ".php?" . http_build_query($params);
                    ?>
                        <td><a href="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($label) ?></a></td>
                    <?php endforeach; ?>
                </tr>
            </table>
        </div>
    </header>

    <script>
        function toggleMode() {
            const body = document.body;
            const currentStyle = body.classList.contains('day-mode') ? 'standard' : 'alternatif';
            const newStyle = currentStyle === 'standard' ? 'alternatif' : 'standard';
            const currentLang = new URLSearchParams(window.location.search).get('lang');

            // Préparer les paramètres à envoyer dans l'URL
            const params = {
                style: newStyle,
                lang: currentLang
            };

            const url = '?' + new URLSearchParams(params).toString();

            // Définir un cookie pour le style choisi
            document.cookie = `style=${newStyle}; path=/; max-age=31536000`; // Le cookie expire dans 1 an

            // Recharger la page avec le nouveau style
            window.location.href = url;
        }

        // Changer dynamiquement l'image du header en fonction du mode (avec image aléatoire)
        document.addEventListener('DOMContentLoaded', () => {
            const body = document.body;
            const header = document.querySelector('header');
            
            if (body.classList.contains('night-mode')) {
                // Changer l'image aléatoirement parmi les trois images nuit
                const imagesNuit = ['images/img1-nuit.jpg', 'images/img2-nuit.jpg', 'images/img3-nuit.jpg'];
                const randomImage = imagesNuit[Math.floor(Math.random() * imagesNuit.length)];
                header.style.backgroundImage = `url('${randomImage}')`;
            } else {
                // Changer l'image aléatoirement parmi les trois images jour
                const imagesJour = ['images/img1-jour.jpg', 'images/img2-jour.jpg', 'images/img3-jour.jpg'];
                const randomImage = imagesJour[Math.floor(Math.random() * imagesJour.length)];
                header.style.backgroundImage = `url('${randomImage}')`;
            }
        });
    </script>
