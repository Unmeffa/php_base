<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_detail_typehebergement extends site_fonction 
{
	private $detail_typehebergement_id;
	private $typehebergement_id;
	private $detail_typehebergement_langue;
	private $detail_typehebergement_titre;
	private $detail_typehebergement_description;

	public static function recupDetail_typehebergement($detail_typehebergement_id)
	{
		#recuperation detail_typehebergement
		$query = "select * from ".self::table."_detail_typehebergement where detail_typehebergement_id='".$detail_typehebergement_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet detail_typehebergement
		$o = new site_detail_typehebergement($row);
			
		#on retourne l'objet detail_typehebergement
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->detail_typehebergement_id = $tab["detail_typehebergement_id"];
		$this->typehebergement_id = $tab["typehebergement_id"];
		$this->detail_typehebergement_langue = $tab["detail_typehebergement_langue"];
		$this->detail_typehebergement_titre = $tab["detail_typehebergement_titre"];
		$this->detail_typehebergement_description = $tab["detail_typehebergement_description"];

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
	
	#ajout d'une detail_typehebergement
	public function ajoutDetail_typehebergement()
	{
		#test pour la prio
		if($this->detail_typehebergement_id == "")
		{
			$this->detail_typehebergement_id = mysql_insert_id();
		}
		#Requete d'ajout de la detail_typehebergement
		$query = "INSERT INTO ".self::table."_detail_typehebergement value
				  (
					'$this->detail_typehebergement_id',
					'".$this->typehebergement_id."',
					'$this->detail_typehebergement_langue',
					\"".site_fonction::protec($this->detail_typehebergement_titre)."\",
					\"".site_fonction::protec($this->detail_typehebergement_description)."\"
				  )";
				  				  
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	#Mise a jour detail_typehebergement
	public function majDetail_typehebergement()
	{
		$query = "UPDATE  ".self::table."_detail_typehebergement set
					typehebergement_id = '".$this->typehebergement_id."',
					detail_typehebergement_langue = '$this->detail_typehebergement_langue',
					detail_typehebergement_titre = \"".site_fonction::protec($this->detail_typehebergement_titre)."\",
					detail_typehebergement_description = \"".site_fonction::protec($this->detail_typehebergement_description)."\"
					WHERE detail_typehebergement_id = '$this->detail_typehebergement_id'
					";
		$rs = mysql_query($query) or die($query);			
		

	}
	
	#Suppression d'un detail_typehebergement
	public function supDetail_typehebergement()
	{
		#on supprime la detail_typehebergement
		$query = "delete from ".self::table."_detail_typehebergement where detail_typehebergement_id=".$this->detail_typehebergement_id;
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet detail_typehebergement de la nouvelelle année
		$a = new site_detail_typehebergement($post);
		if($a->get("detail_typehebergement_id") != "")
		{
			$a->majDetail_typehebergement();
			echo site_fonction::message("D&eacute;tail Type h&eacute;bergement",utf8_encode("Modification effectu&eacute;e"));			
		}
		else 
		{
			$a->ajoutDetail_typehebergement();
			echo site_fonction::message("D&eacute;tail Type h&eacute;bergement",utf8_encode("Insertion effectu&eacute;e"));	
		}
		return $a;	
	}
	

}

?>