<?php 
    ob_start();
    $pageTitle = 'SkyTrack - Prévisions Météo';
    include "./include/header.inc.php";
    include "./include/functions.inc.php"; 

    $lastCity = getLastConsultedCity();
    $meteo = $lastCity ? getMeteo($lastCity) : null;

?>

<main>
    <section class="hero">
        <div class="hero-content">
            <h2>Suivez la Météo en Temps Réel</h2>
            <p>Consultez les prévisions météorologiques pour votre ville et restez informé des conditions climatiques de la semaine.</p>
            <a href="tech.php" class="btn">Tester les APIs Météo</a>
        </div>
    </section>

    <section class="about">
        <h2>À propos du site</h2>
        <p>Notre site web vous permet d'accéder facilement aux prévisions météo locales et globales. Nous utilisons des APIs publiques pour afficher les données météo en temps réel et d'autres informations utiles, comme votre géolocalisation approximative.</p>
        <p>Grâce à une interface simple et conviviale, vous pouvez consulter les prévisions pour aujourd'hui et les jours à venir pour toutes les villes de France.</p>
    </section>

    <section class="features">
        <h2>Fonctionnalités principales</h2>
        <div class="feature-item">
            <h3>Prévisions Météo</h3>
            <p>Obtenez les prévisions du jour et des jours suivants en fonction de votre ville. Les données sont actualisées en temps réel grâce aux APIs météo.</p>
            <a href="map.php" class="btn">Les Prévisions de SkyTrack</a>
        </div>
        <div class="feature-item">
            <h3>Géolocalisation</h3>
            <p>Visualisez votre position approximative et découvrez la météo pour votre région grâce à notre fonctionnalité de géolocalisation basée sur votre adresse IP.</p>
        </div>
        <div class="feature-item">
            <h3>Image du jour (APOD)</h3>
            <p>Découvrez chaque jour une image ou une vidéo fascinante de l'Univers fournie par la NASA (APOD).</p>
        </div>
    </section>

    <section class="recent-weather">
        <h2>Dernière ville consultée</h2>
        <?php if ($lastCity && $meteo): ?>
            <div class="last-weather-card">
                <div class="weather-info">
                    <img src="<?= getWeatherIcon($meteo['current']['condition']['text']) ?>" alt="<?= htmlspecialchars($meteo['current']['condition']['text']) ?>" class="weather-icon"/>
                    <h3><?= htmlspecialchars($lastCity) ?></h3>
                    <p><strong>Température :</strong> <?= $meteo['current']['temp_c'] ?>°C</p>
                    <p><strong>Conditions :</strong> <?= htmlspecialchars($meteo['current']['condition']['text']) ?></p>
                    <div class="additional-info">
                        <p><strong>Vent :</strong> <?= $meteo['current']['wind_kph'] ?> km/h</p>
                        <p><strong>Humidité :</strong> <?= $meteo['current']['humidity'] ?>%</p>
                        <p><strong>Lever du soleil :</strong> <?= $meteo['sunrise'] ?></p>
                        <p><strong>Coucher du soleil :</strong> <?= $meteo['sunset'] ?></p>
                    </div>
                    <a class="btn" href="meteo.php?ville=<?= urlencode($lastCity) ?>">Voir plus</a>
                </div>
            </div>
        <?php else: ?>
            <p>Aucune météo consultée récemment.</p>
        <?php endif; ?>
    </section>


    <section class="media-news-section">
        <div class="image-gallery">
            <h2>Paysages Français</h2>
            <div class="random-images">
                <?php 
                    $randomImages = getRandomImage();
                    foreach ($randomImages as $img): ?>
                    <img src="<?= htmlspecialchars($img) ?>" alt="Paysage de France"/>
                <?php endforeach; ?>
            </div>

        </div>

        <div class="weather-news">
            <h2>Actualités Météo</h2>
            <ul class="news-list">
                <?php
                    $actus = getMeteoNews(); 
                    if (empty($actus)) {
                        echo "<li>Aucune actualité météo disponible actuellement.</li>";
                    } else {
                        foreach ($actus as $item) {
                            echo "<li>$item</li>";
                        }
                    }
                ?>
            </ul>
        </div>
    </section>


    <section class="cta">
        <h2>Envie de découvrir plus ?</h2>
        <p>Accédez à la page technique pour tester l'intégration des différentes APIs et explorez notre collection d'images et de vidéos spatiales.</p>
        <a href="tech.php" class="btn">Accéder à la page technique</a>
    </section>
</main>

<script>
// Mémoriser la dernière ville consultée dans un cookie
const lastCity = '<?= $lastCity ?>';
if (lastCity) {
    document.cookie = "last_city=" + lastCity + "; path=/; max-age=31536000"; // Le cookie expire dans 1 an
}
</script>

<?php 
    require "./include/footer.inc.php";
    ob_end_flush(); 
?>
