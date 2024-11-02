<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_information extends site_fonction 
{
	private $type_information_id;
	private $type_type_information_nom;


	public static function recupinformation($type_information_id)
	{
		#recuperation information
		$query = "select * from ".self::table."_information where type_information_id='".$type_information_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet information
		$o = new site_information($row);
			
		#on retourne l'objet information
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->type_information_id = $tab["type_information_id"];
		$this->type_information_nom = stripslashes($tab["type_information_nom"]);


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
	
	#ajout d'une page
	public function ajoutInformation()
	{
		
		#Requete d'ajout de la information
		$query = "INSERT INTO ".self::table."_information value
				  (
					'$this->type_information_id',
					'".site_fonction::protec($this->type_information_nom)."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		$this->type_information_id = mysql_insert_id();
		
	}
	
	#Mise a jour information
	public function majInformation()
	{
		$query = "UPDATE  ".self::table."_information set
					type_information_nom = '".site_fonction::protec($this->type_information_nom)."'
					WHERE type_information_id = '$this->type_information_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un information
	public function supInformation()
	{

		#on supprime la information
		$query = "delete from ".self::table."_information where type_information_id=".$this->type_information_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime les details de cette information
		$query = "delete from ".self::table."_information_detail where type_information_id=".$this->type_information_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on met a jour les prio
		site_prio::majPrio("information",$this->information_prio);
				
	}
	
	public static function traitementFormulaire($post)
	{
			
		#creation de l'objet information de la nouvelelle année
		$a = new site_information($post);
		if($a->get("type_information_id") != "")
		{
			$a->majInformation();
			echo site_fonction::message("information",utf8_encode("Modification effectuée"));			
		}
		else 
		{
			$a->ajoutInformation();
			echo site_fonction::message("information",utf8_encode("Insertion effectuée"));	
		}
		
		return $a;	
		
	}
	

}

?>