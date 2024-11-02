<?php

class Information
{
	// Propriétés pour stocker les informations
	private $id;
	private $name;
	private $address;
	private $postalCode;
	private $city;
	private $phoneNumber;
	private $phoneNumberAlt;
	private $mail;
	private $receptionMail;
	private $map;

	private $facebook;
	private $instagram;
	private $analytics;

	// Propriété statique pour stocker l'instance unique
	private static $instance = null;

	// Constructeur privé pour empêcher l'instanciation directe
	private function __construct() {}

	// Méthode pour obtenir l'instance unique
	public static function getInstance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
			self::$instance->getInfos();
		}
		return self::$instance;
	}

	// Charger les informations depuis un tableau associatif
	public function loadFromArray(array $data)
	{
		foreach ($data as $key => $val) {
			if (property_exists($this, $key)) {
				$this->$key = $val;
			}
		}
	}

	// Charger la dernière entrée depuis la base de données
	public function getInfos()
	{
		$tablePrefix = DB::getTablePrefix();
		$pdo = DB::getInstance();

		$query = "SELECT * FROM " . $tablePrefix . "_information ORDER BY id DESC LIMIT 1";

		// Préparation de la requête
		$request = $pdo->prepare($query);
		$request->execute();

		$data = $request->fetch(PDO::FETCH_ASSOC);
		if ($data) {
			$this->loadFromArray($data);
		}
	}

	public function save()
	{
		if ($this->id === null) {
			throw new Exception("Impossible de mettre à jour, ID non défini.");
		}

		$tablePrefix = DB::getTablePrefix();
		$pdo = DB::getInstance();

		$query = "UPDATE " . $tablePrefix . "_information 
		SET name = :name, 
			address = :address, 
			postalCode = :postalCode, 
			city = :city, 
			phoneNumber = :phoneNumber, 
			phoneNumberAlt = :phoneNumberAlt, 
			mail = :mail, 
			receptionMail = :receptionMail, 
			map = :map, 
			facebook = :facebook, 
			instagram = :instagram, 
			analytics = :analytics
		WHERE id = :id";
		$request = $pdo->prepare($query);

		$request->bindValue(':name', $this->name);
		$request->bindValue(':address', $this->address);
		$request->bindValue(':postalCode', $this->postalCode);
		$request->bindValue(':city', $this->city);
		$request->bindValue(':phoneNumber', $this->phoneNumber);
		$request->bindValue(':phoneNumberAlt', $this->phoneNumberAlt);
		$request->bindValue(':mail', $this->mail);
		$request->bindValue(':receptionMail', $this->receptionMail);
		$request->bindValue(':map', $this->map);
		$request->bindValue(':facebook', $this->facebook);
		$request->bindValue(':instagram', $this->instagram);
		$request->bindValue(':analytics', $this->analytics);
		$request->bindValue(':id', $this->id);

		if ($request->execute()) {
			return true;
		}
		return false;
	}

	// Méthode set pour définir les propriétés dynamiquement
	public function set($name, $value)
	{
		if (property_exists($this, $name)) {
			$this->$name = $value;
		} else {
			throw new Exception("Propriété '$name' non définie.");
		}
	}

	// Méthode get pour accéder dynamiquement aux propriétés
	public function get($name)
	{
		if (property_exists($this, $name)) {
			return $this->$name;
		} else {
			throw new Exception("Propriété '$name' non définie.");
		}
	}
}
