<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_detail_evenement extends site_fonction 
{
	private $detail_evenement_id;
	private $evenement_id;
	private $detail_evenement_langue;
	private $detail_evenement_titre;
	private $detail_evenement_description;

	public static function recupDetail_evenement($detail_evenement_id)
	{
		#recuperation detail_evenement
		$query = "select * from ".self::table."_detail_evenement where detail_evenement_id='".$detail_evenement_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet detail_evenement
		$o = new site_detail_evenement($row);
			
		#on retourne l'objet detail_evenement
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->detail_evenement_id = $tab["detail_evenement_id"];
		$this->evenement_id = $tab["evenement_id"];
		$this->detail_evenement_langue = $tab["detail_evenement_langue"];
		$this->detail_evenement_titre = $tab["detail_evenement_titre"];
		$this->detail_evenement_description = $tab["detail_evenement_description"];

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
	
	#ajout d'une detail_evenement
	public function ajoutDetail_evenement()
	{
		#test pour la prio
		if($this->detail_evenement_id == "")
		{
			$this->detail_evenement_id = mysql_insert_id();
		}
		#Requete d'ajout de la detail_evenement
		$query = "INSERT INTO ".self::table."_detail_evenement value
				  (
					'$this->detail_evenement_id',
					'".$this->evenement_id."',
					'$this->detail_evenement_langue',
					'".site_fonction::protec($this->detail_evenement_titre)."',
					'".site_fonction::protec(nl2br($this->detail_evenement_description))."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	#Mise a jour detail_evenement
	public function majDetail_evenement()
	{
		$query = "UPDATE  ".self::table."_detail_evenement set
					evenement_id = '$this->evenement_id',
					detail_evenement_langue = '$this->detail_evenement_langue',
					detail_evenement_titre = '".site_fonction::protec($this->detail_evenement_titre)."',
					detail_evenement_description = '".site_fonction::protec(nl2br($this->detail_evenement_description))."'
					WHERE detail_evenement_id = '$this->detail_evenement_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un detail_evenement
	public function supDetail_evenement()
	{
		#on supprime la detail_evenement
		$query = "delete from ".self::table."_detail_evenement where detail_evenement_id=".$this->detail_evenement_id;
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet detail_evenement de la nouvelelle année
		$a = new site_detail_evenement($post);
		if($a->get("detail_evenement_id") != "")
		{
			$a->majDetail_evenement();
			echo site_fonction::message("D&eacute;tail evenement",utf8_encode("Modification effectu&eacute;e"));			
		}
		else 
		{
			$a->ajoutDetail_evenement();
			echo site_fonction::message("D&eacute;tail evenement",utf8_encode("Insertion effectu&eacute;e"));	
		}
		return $a;	
	}
	

}

?>