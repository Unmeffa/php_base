<?php

class DB
{
	const table = SITE_CONFIG_TABLE;
	const hote = SITE_CONFIG_HOTE;
	const user = SITE_CONFIG_USER;
	const pass = SITE_CONFIG_PASS;
	const bdd = SITE_CONFIG_BDD;

	const langs = ['fr' => 1, 'en' => SITE_CONFIG_EN, 'co' => SITE_CONFIG_CO, 'de' => SITE_CONFIG_DE, 'it' => SITE_CONFIG_IT];

	private static $instance = null;
	private $pdo;

	private function __construct()
	{
		try {
			$dsn = 'mysql:host=' . self::hote . ';dbname=' . self::bdd . ';charset=utf8';

			$options = [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Activer les exceptions en cas d'erreur
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Mode de récupération par défaut en tableau associatif
				PDO::ATTR_EMULATE_PREPARES => false, // Désactiver l'émulation des requêtes préparées pour la sécurité
			];

			$this->pdo = new PDO($dsn, self::user, self::pass, $options);
		} catch (PDOException $e) {
			die('Erreur de connexion à la base de données : ' . $e->getMessage());
		}
	}

	public static function getInstance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance->pdo;
	}

	public static function getLangs()
	{
		return self::langs;
	}

	public static function getLangLabel($locale)
	{
		$label = null;
		switch ($locale) {
			case 'fr':
				$label =  'Français';
				break;
			case 'en':
				$label =  'Anglais';
				break;
			case 'co':
				$label =  'Corse';
				break;
			case  'it':
				$label =  'Italien';
				break;
			case  'de':
				$label =  'Allemand';
				break;
			default:
				$label =  null;
				break;
		}
		return $label;
	}

	public static function getTablePrefix()
	{
		return self::table;
	}
}
