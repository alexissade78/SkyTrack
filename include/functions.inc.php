<?php

/**
 * Récupère une ou plusieurs images aléatoires dans un dossier donné.
 *
 * @param string $dir Le chemin vers le dossier des images (ex: 'images/photos/')
 * @param int $count Nombre d'images à retourner
 * @return array Tableau de chemins d'images (relatifs au dossier public)
 */
function getRandomImage($dir = 'images/photos/', $count = 6): array {
    $fullPath = __DIR__ . '/../' . $dir;

    if (!is_dir($fullPath)) return [];

    $files = array_diff(scandir($fullPath), ['.', '..']);
    $images = array_filter($files, function ($file) use ($fullPath) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        return in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']) && is_file($fullPath . '/' . $file);
    });

    $images = array_values($images); // Réindexe proprement

    if (empty($images)) return [];

    shuffle($images); // Mélange aléatoirement

    $selected = array_slice($images, 0, min($count, count($images)));

    return array_map(fn($img) => $dir . $img, $selected);
}


/**
 * Récupère la liste des régions depuis region.csv
 * @return array Liste des noms de régions
 */
function getRegions() {
    $file = __DIR__ . '/../data/region.csv';
    $regions = [];

    if (!file_exists($file)) return $regions;

    if (($handle = fopen($file, 'r')) !== false) {
        fgetcsv($handle); // skip header
        while (($row = fgetcsv($handle)) !== false) {
            if (isset($row[2])) {
                $regions[] = trim($row[2]); // name
            }
        }
        fclose($handle);
    }

    sort($regions);
    return array_unique($regions);
}


/**
 * Récupère les départements d'une région donnée depuis departement.csv
 */
function getDepartements($regionNom) {
    $departements = [];
    $regionCode = null;

    $regionFile = __DIR__ . '/../data/region.csv';
    if (($handle = fopen($regionFile, 'r')) !== false) {
        fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            if (isset($row[2]) && trim($row[2]) === trim($regionNom)) {
                $regionCode = trim($row[1]); // code
                break;
            }
        }
        fclose($handle);
    }

    if (!$regionCode) return [];

    $deptFile = __DIR__ . '/../data/department.csv';
    if (!file_exists($deptFile)) return [];

    if (($handle = fopen($deptFile, 'r')) !== false) {
        fgetcsv($handle); // skip header
        while (($row = fgetcsv($handle)) !== false) {
            if (isset($row[1], $row[3]) && trim($row[1]) === $regionCode) {
                $departements[] = trim($row[3]); // name
            }
        }
        fclose($handle);
    }

    sort($departements);
    return array_unique($departements);
}


/**
 * Récupère les villes d'un département donné depuis cities.csv
 */
function getVilles($departementNom) {
    $villes = [];
    $deptCode = null;

    $deptFile = __DIR__ . '/../data/department.csv';
    $citiesFile = __DIR__ . '/../data/cities.csv';

    if (!file_exists($deptFile) || !file_exists($citiesFile)) return $villes;

    // Trouver le code du département à partir de son nom
    if (($handle = fopen($deptFile, 'r')) !== false) {
        fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            if (isset($row[3]) && trim($row[3]) === trim($departementNom)) {
                $deptCode = trim($row[2]); // code
                break;
            }
        }
        fclose($handle);
    }

    if (!$deptCode) return [];

    // Récupérer les villes correspondant au code département
    if (($handle = fopen($citiesFile, 'r')) !== false) {
        fgetcsv($handle); // skip header
        while (($row = fgetcsv($handle)) !== false) {
            if (isset($row[1], $row[4]) && trim($row[1]) === $deptCode) {
                $villes[] = trim($row[4]); // name
            }
        }
        fclose($handle);
    }

    $villes = array_unique($villes);
    sort($villes);
    return $villes;
}

function getMeteo($ville) {
    $apiKey = 'cb29e047444c473d952111608250104';

    $ville = str_replace(['’', '\''], '-', $ville);
    $ville = str_replace([' sur ', ' Sur '], '-sur-', $ville);
    $villeEncodee = urlencode($ville . ",France");

    $apiUrl = "https://api.weatherapi.com/v1/forecast.json?key=$apiKey&q=$villeEncodee&days=7&lang=fr";
    $response = @file_get_contents($apiUrl);
    $data = $response ? json_decode($response, true) : null;

    if (!$data || !isset($data['current'], $data['forecast']['forecastday'][0]['astro'])) {
        error_log("API WeatherAPI KO pour $ville");
        return null;
    }

    return [
        'current' => $data['current'],
        'forecast' => $data['forecast']['forecastday'],
        'sunrise' => $data['forecast']['forecastday'][0]['astro']['sunrise'],  
        'sunset' => $data['forecast']['forecastday'][0]['astro']['sunset']      
    ];
}


function getWeatherIcon($description, $iconCode = 'unknown') {
    $desc = strtolower(trim($description));

    $icons = [
        'soleil' => 'images/icons/sun.svg',
        'ensoleillé' => 'images/icons/sun.svg',
        'clair' => 'images/icons/sun.svg',
        'nuage' => 'images/icons/cloud-sun.svg',
        'pluie' => 'images/icons/rain.svg',
        'averse' => 'images/icons/rain.svg',
        'orage' => 'images/icons/storm.svg',
        'vent' => 'images/icons/wind.svg',
        'neige' => 'images/icons/snow.svg',
        'brouillard' => 'images/icons/fog.svg',
        'alerte' => 'images/icons/alert.svg',
    ];

    foreach ($icons as $pattern => $path) {
        if (strpos($desc, $pattern) !== false && file_exists(__DIR__ . '/../' . $path)) {
            return $path;
        }
    }

    // fallback vers l'icône WeatherAPI si aucune locale ne correspond
    $iconCode = $iconCode !== 'unknown' ? $iconCode : '113';
    return "https://cdn.weatherapi.com/weather/64x64/day/$iconCode.png";
}
/**
 * Récupère la ville et région à partir de l’adresse IP de l’utilisateur
 * Utilise ipinfo.io (gratuite avec token)
 *
 * @param string|null $ip Adresse IP (si null, récupérée automatiquement)
 * @return array|null Données géo ou null si erreur
 */
function getVilleParIp($ip = null) {
    if (!$ip) $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    
    $token = "b95e1aafd66f5a";
    $url = "https://ipinfo.io/{$ip}/json?token={$token}";

    $response = @file_get_contents($url);
    if (!$response) return null;

    $data = json_decode($response, true);
    if (!$data || !isset($data['city'])) return null;

    return [
        'ville'     => $data['city'] ?? null,
        'region'    => $data['region'] ?? null,
        'pays'      => $data['country'] ?? null,
        'latitude'  => isset($data['loc']) ? explode(',', $data['loc'])[0] : null,
        'longitude' => isset($data['loc']) ? explode(',', $data['loc'])[1] : null
    ];
}


// Fonction pour obtenir et incrémenter le nombre de hits
function getHitCount() {
    // Vérifier si le cookie 'hits' existe
    if (isset($_COOKIE['hits'])) {
        // Si le cookie existe, l'incrémenter
        $hitCount = (int)$_COOKIE['hits'] + 1;
    } else {
        // Si le cookie n'existe pas, initialiser à 1
        $hitCount = 1;
    }
    
    // Mettre à jour le cookie 'hits' avec la nouvelle valeur
    setcookie('hits', $hitCount, time() + 3600 * 24 * 30, "/"); // Cookie valide pendant 30 jours
    
    return $hitCount;
}


function logCitySearch($city) {
    $csvFile = __DIR__ . '/../data/cities.csv';
    $timestamp = date('Y-m-d H:i:s');
    $data = [$timestamp, $city];

    if (!file_exists(dirname($csvFile))) {
        mkdir(dirname($csvFile), 0755, true);
    }

    $file = fopen($csvFile, 'a');
    if ($file) {
        fputcsv($file, $data);
        fclose($file);
    }
}

function getCityStats() {
    $csvFile = __DIR__ . '/../data/cities.csv';
    $stats = [];

    if (file_exists($csvFile)) {
        $file = fopen($csvFile, 'r');
        while (($row = fgetcsv($file)) !== false) {
            if (isset($row[1])) {
                $city = trim($row[4]);
                $stats[$city] = ($stats[$city] ?? 0) + 1;
            }
        }
        fclose($file);
    }

    arsort($stats); // tri décroissant
    return $stats;
}

function getRegionFromCity($cityName) {
    // Exemple de code qui doit lier une ville à une région
    // Vous pouvez le faire en vérifiant les fichiers CSV département et région

    $departementsFile = __DIR__ . '/../data/departement.csv';
    $regionsFile = __DIR__ . '/../data/region.csv';

    $region = null;
    if (file_exists($departementsFile) && file_exists($regionsFile)) {
        // Lire le fichier des départements
        $departements = file($departementsFile, FILE_IGNORE_NEW_LINES);
        foreach ($departements as $line) {
            $data = str_getcsv($line, ';');
            if (isset($data[1]) && trim($data[1]) === $cityName) {
                // Trouver la région correspondante
                $regionCode = $data[2]; // Code de la région
                // Lire le fichier des régions
                $regions = file($regionsFile, FILE_IGNORE_NEW_LINES);
                foreach ($regions as $regionLine) {
                    $regionData = str_getcsv($regionLine, ';');
                    if (isset($regionData[0]) && $regionData[0] == $regionCode) {
                        $region = $regionData[1]; // Nom de la région
                        break;
                    }
                }
                break;
            }
        }
    }
    return $region;
}


function getMeteoNews($apiKey = 'pub_81907ec39401c2156d8e429e04dfc7b69d895') {
    $url = "https://newsdata.io/api/1/news?apikey=$apiKey&q=météo&language=fr&country=fr";

    $json = @file_get_contents($url);
    if (!$json) return [];

    $data = json_decode($json, true);
    if (!isset($data['results'])) return [];

    $news = [];
    foreach ($data['results'] as $item) {
        $title = $item['title'] ?? '';
        $link = $item['link'] ?? '#';
        $news[] = "<a href=\"$link\" target=\"_blank\">$title</a>";
    }

    return $news;
}

function addConsultationToCSV($city) {
    // Utilisation de __DIR__ pour obtenir le chemin du répertoire actuel du script
    // Puis on accède au répertoire 'data'
    $log_file = __DIR__ . "/../data/consultations.csv";  // 'data' est au même niveau que 'include'
    
    $date = date('Y-m-d H:i:s'); // Date et heure actuelles
    $entry = $city . ';' . $date . PHP_EOL; // Format de la ligne à ajouter

    // Vérifier si le répertoire data existe, sinon on le crée
    if (!file_exists(__DIR__ . "/../data")) {
        mkdir(__DIR__ . "/../data", 0755, true);  // Crée le dossier 'data' s'il n'existe pas
    }

    // Vérifier si le fichier existe et l'ajouter si nécessaire
    if (file_exists($log_file)) {
        file_put_contents($log_file, $entry, FILE_APPEND); // Ajouter au fichier CSV
    } else {
        // Si le fichier n'existe pas, le créer et ajouter la première entrée
        file_put_contents($log_file, "City;Date\n" . $entry); // Ajouter un en-tête au fichier CSV
    }

}

// Fonction pour obtenir l'affluence des consultations des 7 derniers jours
function getConsultationsParJour() {
    $log_file = __DIR__ . '/../data/consultations.csv';
    $consultationsParJour = [];
    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days')); // 7 derniers jours

    // Récupérer les dates des 7 derniers jours
    $dates = [];
    for ($i = 0; $i < 7; $i++) {
        $dates[] = date('Y-m-d', strtotime("-$i days"));
    }

    // Initialiser les jours avec zéro consultation
    foreach ($dates as $date) {
        $consultationsParJour[$date] = 0;
    }

    // Vérifier si le fichier existe
    if (file_exists($log_file)) {
        $file_content = file_get_contents($log_file);
        $lines = explode(PHP_EOL, $file_content);

        // Parcourir chaque ligne et extraire la date des consultations
        foreach ($lines as $line) {
            $data = str_getcsv($line, ';');
            if (count($data) == 2) {
                $date = substr($data[1], 0, 10); // Ne conserver que la date (YYYY-MM-DD)

                // Vérifier si la date est dans les 7 derniers jours
                if (isset($consultationsParJour[$date])) {
                    $consultationsParJour[$date]++;
                }
            }
        }
    }

    // Trier les résultats par date (assurer l'ordre des jours)
    ksort($consultationsParJour);

    return $consultationsParJour;
}

function getLastConsultedCity() {
    $log_file = __DIR__ . '/../data/consultations.csv';
    
    // Si le fichier n'existe pas, retourner null
    if (!file_exists($log_file)) {
        return null;
    }
    
    // Lire toutes les lignes du fichier
    $lines = file($log_file, FILE_IGNORE_NEW_LINES);
    
    // Si le fichier est vide, retourner null
    if (empty($lines)) {
        return null;
    }

    // Récupérer la dernière ligne
    $lastLine = end($lines);
    $data = str_getcsv($lastLine, ';');
    
    // Renvoyer la ville (qui est dans la première colonne)
    return $data[0] ?? null;
}


?>