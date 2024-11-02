<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_type_information extends site_fonction 
{
	private $type_information_id;
	private $type_information_nom;


	public static function recupTypeNom($type_information_id)
	{
		#recuperation information
		$query = "select * from ".self::table."_type_information where type_information_id='".$type_information_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet information
		$o = new site_type_information($row);
			
		#on retourne l'objet information

		return $row['type_information_nom'];		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->type_information_id = '';
		$this->type_information_nom = $tab;

	}
	
	#methode de récuperation d'un champ
	public function get($attribut)
	{
		return($this->$attribut);
	}
	
	#methode de modification d'un attribut
	public function set($attribut,$valeur)
	{
		$this->$attribut = $valeur;
	}
	
	#ajout d'une page
	public function ajoutInformation()
	{
		
		#Requete d'ajout de la information
		$query = "INSERT INTO ".self::table."_type_information value
				  (
					'',
					'".site_fonction::protec($this->type_information_nom)."'
				  )";
		$rs = mysql_query($query) or die($query);
		$this->type_information_id = mysql_insert_id();
		
	}
	
	#Mise a jour information
	public function majInformation()
	{
		$query = "UPDATE  ".self::table."_type_information set
					type_information_nom = '".site_fonction::protec($this->type_information_nom)."'
					WHERE type_information_id = '$this->type_information_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un information
	public function supInformation()
	{

		#on supprime la information
		$query = "delete from ".self::table."_type_information where type_information_id=".$this->type_information_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime les details de cette information
		$query = "delete from ".self::table."_type_information_detail where type_information_id=".$this->type_information_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on met a jour les prio
		site_prio::majPrio("information",$this->information_prio);
				
	}
	
	public static function traitementFormulaire($post)
	{
			
		#creation de l'objet information de la nouvelelle année
			$a = new site_type_information($post);
	
			$a->ajoutInformation();
			echo site_fonction::message("information type",utf8_encode("Insertion effectuée"));	
			
		return $a;	
		
	}
	

}

?>