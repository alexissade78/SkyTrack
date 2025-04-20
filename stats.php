<?php 
ob_start();
$pageTitle = 'SkyTrack - Statistiques';
include "include/header.inc.php"; 
require_once "include/functions.inc.php"; 

// Fichier CSV des consultations
$log_file = __DIR__ . "/data/consultations.csv";
$consultations = [];

// Lire le fichier CSV et extraire les villes
if (file_exists($log_file)) {
    $file_content = file_get_contents($log_file);
    $lines = explode(PHP_EOL, $file_content);

    foreach ($lines as $line) {
        $data = str_getcsv($line, ';');
        if (count($data) == 2) {
            $city = $data[0]; // Ville

            // Ajouter la ville aux consultations
            $consultations[] = $city;
        }
    }
}

// Compter les consultations par ville
$city_count = array_count_values($consultations);
arsort($city_count); // Trier par nombre de consultations décroissant

// Top 5 villes les plus consultées
$top_cities = array_slice($city_count, 0, 5);

// Obtenir les consultations par jour (sur les 7 derniers jours)
$consultationsParJour = getConsultationsParJour();

// Obtenir les dates des 7 derniers jours pour l'affichage sur le graphique
$dates = array_keys($consultationsParJour);
$consultationsValues = array_values($consultationsParJour);

?>

<main>
    <section class="stats-section">
        <h2>Statistiques des Consultations</h2>

        <!-- Graphique des consultations par jour -->
        <h3>Affluence des consultations sur les 7 derniers jours</h3>
        <canvas id="dailyConsultationsChart" width="400" height="200"></canvas>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctxDaily = document.getElementById('dailyConsultationsChart').getContext('2d');
            const dailyConsultationsChart = new Chart(ctxDaily, {
                type: 'line', // Graphique en courbe
                data: {
                    labels: <?php echo json_encode($dates); ?>, // Dates des 7 derniers jours
                    datasets: [{
                        label: 'Consultations par jour',
                        data: <?php echo json_encode($consultationsValues); ?>, // Nombre de consultations pour chaque jour
                        borderColor: 'rgba(58, 123, 213, 1)', // Couleur de la courbe
                        backgroundColor: 'rgba(58, 123, 213, 0.4)',
                        borderWidth: 2,
                        fill: true, // Remplir sous la courbe
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1 // StepSize pour rendre le graphique plus lisible
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
        </script>

        <!-- Graphique des top 5 villes les plus consultées -->
        <h3>Top 5 des Villes les Plus Consultées</h3>
        <canvas id="cityChart" width="100" height="50"></canvas>
        <script>
            const ctx = document.getElementById('cityChart').getContext('2d');
            const cityChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_keys($top_cities)); ?>,
                    datasets: [{
                        label: 'Nombre de consultations',
                        data: <?php echo json_encode(array_values($top_cities)); ?>,
                        backgroundColor: 'rgba(58, 123, 213, 0.4)',
                        borderColor: 'rgba(58, 123, 213, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    </section>
</main>

<?php
    include "include/footer.inc.php";
    ob_end_flush();
?>
