<?php

#inclusion de la class mere

require_once("class.site_fonction.php");



class site_detail_affaire extends site_fonction 

{

	private $detail_affaire_id;

	private $affaire_id;

	private $detail_affaire_langue;

	private $detail_affaire_titre;

	private $detail_affaire_description;



	public static function recupDetail_affaire($detail_affaire_id)

	{

		#recuperation detail_affaire

		$query = "select * from ".self::table."_detail_affaire where detail_affaire_id='".$detail_affaire_id."'";

		$rs = @mysql_query($query);

		$row = @mysql_fetch_assoc($rs);

			

		#creation de l'objet detail_affaire

		$o = new site_detail_affaire($row);

			

		#on retourne l'objet detail_affaire

		return $o;		

	}

		

	#constructeur

	public function __construct($tab)

	{



		$this->detail_affaire_id = $tab["detail_affaire_id"];

		$this->affaire_id = $tab["affaire_id"];

		$this->detail_affaire_langue = $tab["detail_affaire_langue"];

		$this->detail_affaire_titre = $tab["detail_affaire_titre"];

		$this->detail_affaire_description = $tab["detail_affaire_description"];



	}

	

	

	#methode de rÃ©cuperation d'un champ

	public function get($attribut)

	{

		return($this->$attribut);

	}

	

	#methode de modification d'un attribut

	public function set($attribut,$valeur)

	{

		$this->$attribut = $valeur;

	}

	

	#ajout d'une detail_affaire

	public function ajoutDetail_affaire()

	{

		#test pour la prio

		if($this->detail_affaire_langue == "")

		{

			$rs = site_fonction::recup("detail_affaire");

			$nb = mysql_num_rows($rs);

			$this->detail_affaire_langue = $nb+1;

		}

		#Requete d'ajout de la detail_affaire

		$query = "INSERT INTO ".self::table."_detail_affaire value

				  (

					'$this->detail_affaire_id',

					'".$this->affaire_id."',

					'$this->detail_affaire_langue',

					'".site_fonction::protec($this->detail_affaire_titre)."',

					'".site_fonction::protec($this->detail_affaire_description)."'

				  )";

		$rs = mysql_query($query) or die(mysql_error());

		$this->detail_affaire_id = mysql_insert_id();

		

	}

	

	#Mise a jour detail_affaire

	public function majDetail_affaire()

	{

		$query = "UPDATE  ".self::table."_detail_affaire set

					affaire_id = '$this->affaire_id',

					detail_affaire_langue = '$this->detail_affaire_langue',

					detail_affaire_titre = '".site_fonction::protec($this->detail_affaire_titre)."',

					detail_affaire_description = '".site_fonction::protec($this->detail_affaire_description)."'

					WHERE detail_affaire_id = '$this->detail_affaire_id'

					";

		$rs = mysql_query($query) or die($query);			

	}

	

	#Suppression d'un detail_affaire

	public function supDetail_affaire()

	{

		#on supprime la detail_affaire

		$query = "delete from ".self::table."_detail_affaire where detail_affaire_id=".$this->detail_affaire_id;

		$rs = mysql_query($query) or die(mysql_error());

	}

	

	public static function traitementFormulaire($post)

	{

		#creation de l'objet detail_affaire de la nouvelelle année

		$a = new site_detail_affaire($post);

		if($a->get("detail_affaire_id") != "")

		{

			$a->majDetail_affaire();

			echo site_fonction::message("D&eacute;tail affaire",utf8_encode("Modification effectu&eacute;e"));			

		}

		else 

		{

			$a->ajoutDetail_affaire();

			echo site_fonction::message("D&eacute;tail affaire",utf8_encode("Insertion effectu&eacute;e"));	

		}

		return $a;	

	}

	



}



?>