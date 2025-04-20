<?php 
    ob_start();
    $pageTitle = 'SkyTrack - A Propos';
    include "./include/header.inc.php"; 
?>

<main>
    <div class="about-container">
        <h1>Bienvenue sur SkyTrack. Merci pour votre visite !</h1>

        <section class="about-section">
            <h2>À propos de nous ?</h2>
            <p>Nous sommes Alexis SADE et Bilel BEN NAYA, deux étudiants en L2 Informatique passionnés par les enjeux de la météo et des statistiques, déterminés à apporter notre contribution à ce domaine crucial dans le cadre du projet du module Développement Web encadré par monsieur Lemaire.</p>
            <p>Ensemble, nous formons une équipe dynamique et complémentaire, prête à relever les défis et à proposer des solutions innovantes pour améliorer l'efficacité des prévisions météorologiques.</p>
        </section>

        <section class="about-section">
            <h2>Avantages</h2>
            <p>Ce site dédié à la consultation de la météo en Ile de France offre une multitude d'avantages aux utilisateurs :</p> 

            <ul>
                <li>Consultation facile de la météo pour chaque ville en France.</li>
                <li>Mises à jour en temps réel sur les alertes météorologiques.</li>
                <li>Prévisions de la météo sur les 7 prochains jours.</li>
            </ul>
        </section>

    </div>
</main>

<?php 
    require "./include/footer.inc.php"; 
    ob_end_flush();
?>
