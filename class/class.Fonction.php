<?php
require_once(__DIR__ . '/../conf/config.inc.php');
class Fonction
{

	public static function recup($table, $where = "WHERE 1", $start = null, $end = null, $fields = "*")
	{
		$limit = "";
		if (is_numeric($start) && is_numeric($end)) {
			$limit = " LIMIT :start :end";
		}

		$tablePrefix = DB::getTablePrefix();
		$query = "SELECT $fields FROM " . $tablePrefix . "_" . $table . " " . $where . $limit;
		$pdo = DB::getInstance();

		$request = $pdo->prepare($query);
		if ($limit) {
			$request->bindValue(':start', (int)$start, PDO::PARAM_INT);
			$request->bindValue(':end', (int)$end, PDO::PARAM_INT);
		}
		$request->execute();
		return $request->fetchAll();
	}

	public static function recupRecursivePages(?int $parentId = null): array
	{
		$tablePrefix = DB::getTablePrefix();
		$pdo = DB::getInstance();

		// Préparation de la requête pour récupérer les pages avec le parentId donné
		if (is_null($parentId)) {
			$query = "SELECT * FROM " . $tablePrefix . "_page WHERE parentId IS NULL order by prio ASC";
		} else {
			$query = "SELECT * FROM " . $tablePrefix . "_page WHERE parentId = :parentId order by prio ASC";
		}

		$request = $pdo->prepare($query);
		if (!is_null($parentId)) {
			$request->bindValue(':parentId', $parentId, PDO::PARAM_INT);
		}
		$request->execute();
		$pagesData = $request->fetchAll(PDO::FETCH_ASSOC);

		$pages = [];
		foreach ($pagesData as $pageData) {
			$page = new Page($pageData); // Instancier chaque Page
			$page->set('children', self::recupRecursivePages($page->get('id'))); // Récupération récursive des sous-pages
			$pages[] = $page; // Ajout de l'instance Page au tableau
		}

		return $pages;
	}

	public static function getZoneTypeLabel($key)
	{
		global $tZoneType;

		if (isset($tZoneType[$key])) {
			return $tZoneType[$key];
		}

		return "Type inconnu";
	}

	public static function getZoneVariation($key)
	{
		global $tZoneVariation;

		if (isset($tZoneVariation[$key])) {
			return $tZoneVariation[$key];
		}

		return "Variation inconnue";
	}

	public static function clean($string)
	{
		$string = str_replace(['\'', '"', '/', '\\'], '-', $string);
		return preg_replace('/[^A-Za-z0-9\-_]/', '', $string); // retire les caractères non-alphanumériques
	}
}
