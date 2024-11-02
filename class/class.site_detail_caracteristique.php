<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_detail_caracteristique extends site_fonction 
{
	private $detail_caracteristique_id;
	private $caracteristique_id;
	private $detail_caracteristique_langue;
	private $detail_caracteristique_titre;
	private $detail_caracteristique_description;

	public static function recupDetail_caracteristique($detail_caracteristique_id)
	{
		#recuperation detail_caracteristique
		$query = "select * from ".self::table."_detail_caracteristique where detail_caracteristique_id='".$detail_caracteristique_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet detail_caracteristique
		$o = new site_detail_caracteristique($row);
			
		#on retourne l'objet detail_caracteristique
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->detail_caracteristique_id = $tab["detail_caracteristique_id"];
		$this->caracteristique_id = $tab["caracteristique_id"];
		$this->detail_caracteristique_langue = $tab["detail_caracteristique_langue"];
		$this->detail_caracteristique_titre = $tab["detail_caracteristique_titre"];
		$this->detail_caracteristique_description = $tab["detail_caracteristique_description"];

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
	
	#ajout d'une detail_caracteristique
	public function ajoutDetail_caracteristique()
	{
		#test pour la prio
		if($this->detail_caracteristique_id == "")
		{
			$this->detail_caracteristique_id = mysql_insert_id();
		}
		#Requete d'ajout de la detail_caracteristique
		$query = "INSERT INTO ".self::table."_detail_caracteristique value
				  (
					'$this->detail_caracteristique_id',
					'".$this->caracteristique_id."',
					'$this->detail_caracteristique_langue',
					'".site_fonction::protec($this->detail_caracteristique_titre)."',
					'".site_fonction::protec(nl2br($this->detail_caracteristique_description))."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	#Mise a jour detail_caracteristique
	public function majDetail_caracteristique()
	{
		$query = "UPDATE  ".self::table."_detail_caracteristique set
					caracteristique_id = '$this->caracteristique_id',
					detail_caracteristique_langue = '$this->detail_caracteristique_langue',
					detail_caracteristique_titre = '".site_fonction::protec($this->detail_caracteristique_titre)."',
					detail_caracteristique_description = '".site_fonction::protec(nl2br($this->detail_caracteristique_description))."'
					WHERE detail_caracteristique_id = '$this->detail_caracteristique_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un detail_caracteristique
	public function supDetail_caracteristique()
	{
		#on supprime la detail_caracteristique
		$query = "delete from ".self::table."_detail_caracteristique where detail_caracteristique_id=".$this->detail_caracteristique_id;
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet detail_caracteristique de la nouvelelle année
		$a = new site_detail_caracteristique($post);
		if($a->get("detail_caracteristique_id") != "")
		{
			$a->majDetail_caracteristique();
			echo site_fonction::message("Detail caracteristique",utf8_encode("Modification effectu&eacute;e"));			
		}
		else 
		{
			$a->ajoutDetail_caracteristique();
			echo site_fonction::message("Detail caracteristique",utf8_encode("Insertion effectu&eacute;e"));	
		}
		return $a;	
	}
	

}

?>