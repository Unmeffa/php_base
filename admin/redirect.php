<?php
include_once('../conf/config.inc.php'); // Inclusion de la configuration pour BASE_URL

// Récupérer l'URL demandée
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Construire le chemin réel du fichier demandé sur le serveur
$path = realpath(__DIR__ . '/' . $url);

// Construire l'URL complète basée sur l'environnement (localhost ou production)
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['SCRIPT_NAME']);

// Vérification si le fichier ou dossier existe et s'il est dans le répertoire admin
if ($path && strpos($path, realpath(__DIR__)) === 0 && file_exists($path)) {
    // Si le fichier ou le dossier existe, rediriger vers l'URL demandée
    header('Location: ' . $baseUrl . '/' . $url);
    exit();
} elseif ($url === '404') {
    // Si on accède directement à /admin/404, afficher la page 404
    include('404.php');
    exit();
} else {
    // Si l'URL demandée ne correspond à aucun fichier ou dossier, rediriger vers la page 404
    header('Location: ' . $baseUrl . '/404');
    exit();
}
