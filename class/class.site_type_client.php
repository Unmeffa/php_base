<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_type_client extends site_fonction 
{
	private $type_client_id;
	private $type_client_titre;

	public static function recupType_client($type_client_id)
	{
		#recuperation type_client
		$query = "select * from ".self::table."_type_client where type_client_id='".$type_client_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet type_client
		$o = new site_type_client($row);
			
		#on retourne l'objet type_client
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->type_client_id = $tab["type_client_id"];
		$this->type_client_titre = stripslashes($tab["type_client_titre"]);

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
	
	#ajout d'une type_client
	public function ajoutType_client()
	{
		#Requete d'ajout de la type_client
		$query = "INSERT INTO ".self::table."_type_client value
				  (
					'$this->type_client_id',
					'".site_fonction::protec($this->type_client_titre)."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		$this->type_client_id = mysql_insert_id();
		
	}
	
	#Mise a jour type_client
	public function majType_client()
	{
		$query = "UPDATE  ".self::table."_type_client set
					type_client_titre = '".site_fonction::protec($this->type_client_titre)."'
					WHERE type_client_id = '$this->type_client_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un type_client
	public function supType_client()
	{
		#on supprime la type_client
		$query = "delete from ".self::table."_type_client where type_client_id=".$this->type_client_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on enleve tous les type de client existant sur les clients deja enregistrer
		$query = "UPDATE  ".self::table."_client set type_client_id = 0  where type_client_id=".$this->type_client_id;
		$rs = mysql_query($query) or die(mysql_error()); 
		
	}
	
	public static function traitementFormulaire($post)
	{
			
		#creation de l'objet type_client de la nouvelelle année
		$a = new site_type_client($post);
		if($a->get("type_client_id") != "")
		{
			$a->majType_client();
			echo site_fonction::message("Type client",utf8_encode("Modification effectuée"));			
		}
		else 
		{
			$a->ajoutType_client();
			echo site_fonction::message("Type client",utf8_encode("Insertion effectuée"));	
		}
		
		return $a;	
		
	}
	

}

?>