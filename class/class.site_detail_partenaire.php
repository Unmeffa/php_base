<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_detail_partenaire extends site_fonction 
{
	private $detail_partenaire_id;
	private $partenaire_id;
	private $detail_partenaire_langue;
	private $detail_partenaire_titre;
	private $detail_partenaire_description;

	public static function recupDetail_partenaire($detail_partenaire_id)
	{
		#recuperation detail_partenaire
		$query = "select * from ".self::table."_detail_partenaire where detail_partenaire_id='".$detail_partenaire_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet detail_partenaire
		$o = new site_detail_partenaire($row);
			
		#on retourne l'objet detail_partenaire
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->detail_partenaire_id = $tab["detail_partenaire_id"];
		$this->partenaire_id = $tab["partenaire_id"];
		$this->detail_partenaire_langue = $tab["detail_partenaire_langue"];
		$this->detail_partenaire_titre = $tab["detail_partenaire_titre"];
		$this->detail_partenaire_description = $tab["detail_partenaire_description"];

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
	
	#ajout d'une detail_partenaire
	public function ajoutDetail_partenaire()
	{
		#test pour la prio
		if($this->detail_partenaire_id == "")
		{
			$this->detail_partenaire_id = mysql_insert_id();
		}
		#Requete d'ajout de la detail_partenaire
		$query = "INSERT INTO ".self::table."_detail_partenaire value
				  (
					'$this->detail_partenaire_id',
					'".$this->partenaire_id."',
					'$this->detail_partenaire_langue',
					'".site_fonction::protec($this->detail_partenaire_titre)."',
					'".site_fonction::protec(nl2br($this->detail_partenaire_description))."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	#Mise a jour detail_partenaire
	public function majDetail_partenaire()
	{
		$query = "UPDATE  ".self::table."_detail_partenaire set
					partenaire_id = '$this->partenaire_id',
					detail_partenaire_langue = '$this->detail_partenaire_langue',
					detail_partenaire_titre = '".site_fonction::protec($this->detail_partenaire_titre)."',
					detail_partenaire_description = '".site_fonction::protec(nl2br($this->detail_partenaire_description))."'
					WHERE detail_partenaire_id = '$this->detail_partenaire_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un detail_partenaire
	public function supDetail_partenaire()
	{
		#on supprime la detail_partenaire
		$query = "delete from ".self::table."_detail_partenaire where detail_partenaire_id=".$this->detail_partenaire_id;
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet detail_partenaire de la nouvelelle année
		$a = new site_detail_partenaire($post);
		if($a->get("detail_partenaire_id") != "")
		{
			$a->majDetail_partenaire();
			echo site_fonction::message("D&eacute;tail partenaire",utf8_encode("Modification effectu&eacute;e"));			
		}
		else 
		{
			$a->ajoutDetail_partenaire();
			echo site_fonction::message("D&eacute;tail partenaire",utf8_encode("Insertion effectu&eacute;e"));	
		}
		return $a;	
	}
	

}

?>