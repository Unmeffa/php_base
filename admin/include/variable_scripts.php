<?php
include_once(__DIR__ . '/../../conf/config.inc.php');

// Autoload des classes
spl_autoload_register(function ($classe) {
	$filePath = SERVER_ROOT_URL . "/class/class." . $classe . ".php";
	if (file_exists($filePath)) {
		require_once $filePath;
	}
});

$adminUrl = BASE_URL . "/admin";
include('utils.php');
