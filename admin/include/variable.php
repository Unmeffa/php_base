<?php
include_once('../conf/config.inc.php');

// Autoload des classes
spl_autoload_register(function ($classe) {
	$filePath = SERVER_ROOT_URL . "/class/class." . $classe . ".php";
	if (file_exists($filePath)) {
		require_once $filePath;
	}
});

// Récupérer l'instance d'Information (si nécessaire)
$information = Information::getInstance();
$adminUrl = BASE_URL . "/admin";

// Si on est sur la page 404 ou la page login, ne pas vérifier la session utilisateur
if (isset($_SERVER['REQUEST_URI']) && (strpos($_SERVER['REQUEST_URI'], '/admin/404') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/login') !== false)) {
	return;
}

// Vérification si l'utilisateur est connecté
if (!User::checkSession()) {
	// Rediriger vers la page de login si l'utilisateur n'est pas connecté
	header('Location: ' . BASE_URL . '/admin/login');
	exit();
}

include('utils.php');
