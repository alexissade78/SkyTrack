<?php
// Inclure le fichier de fonctions pour accéder à la fonction getHitCount()
require_once 'include/functions.inc.php';
?>

<footer>
    <div class="footer-container">
        <!-- Aide ? Section -->
        <div class="footer-section">
            <h3>Aide ?</h3>
            <ul>
                <li><a href="propos.php">À propos de nous</a></li>
                <li><a href="contact.php">Nous contacter</a></li>
            </ul>
        </div>

        <!-- Informations Section -->
        <div class="footer-section">
            <h3>Informations</h3>
            <ul>
                <li><a href="tech.php">Tech</a></li>
                <li><a href="plan.php">Plan du Site</a></li>
                <li>Hits : <?= getHitCount(); ?></li> <!-- Affiche le nombre de hits -->
            </ul>
        </div>

        <!-- Organisme Section -->
        <div class="footer-section">
            <h3>Organisme</h3>
            <ul>
                <li>CY Cergy Paris Université © 2025</li>
                <li>Réalisé par Alexis SADE - Bilel BEN NAYA ®</li>
            </ul>
        </div>
    </div>
</footer>

<!-- Fenêtre de demande d'acceptation des cookies -->
<div id="cookie-popup" class="cookie-popup">
    <p>Nous utilisons des cookies pour améliorer votre expérience. En continuant à utiliser ce site, vous acceptez notre politique de cookies.</p>
    <div class="cookie-buttons">
        <button onclick="acceptCookies()">Accepter</button>
        <button onclick="declineCookies()">Refuser</button>
    </div>
</div>

<script>
    // Vérifier si le cookie est déjà accepté ou refusé
    if (document.cookie.indexOf("cookiesAccepted=true") !== -1 || document.cookie.indexOf("cookiesDeclined=true") !== -1) {
        document.getElementById('cookie-popup').style.display = 'none'; // Masquer la popup si déjà accepté ou refusé
    }

    // Fonction pour accepter les cookies
    function acceptCookies() {
        // Masquer la fenêtre
        document.getElementById('cookie-popup').style.display = 'none';
        // Définir un cookie "cookiesAccepted" pour garder en mémoire le choix pendant un an
        document.cookie = "cookiesAccepted=true; path=/; max-age=31536000"; // 1 an
    }

    // Fonction pour refuser les cookies
    function declineCookies() {
        // Masquer la fenêtre
        document.getElementById('cookie-popup').style.display = 'none';
        // Définir un cookie "cookiesDeclined" pour garder en mémoire le choix pendant un an
        document.cookie = "cookiesDeclined=true; path=/; max-age=31536000"; // 1 an
    }
</script>
</body>
</html>
