<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_type_evenement extends site_fonction 
{
	private $type_evenement_id;
	private $page_id;
	private $type_evenement_nom;

	public static function recupType_evenement($type_evenement_id)
	{
		#recuperation type_evenement
		$query = "select * from ".self::table."_type_evenement where type_evenement_id='".$type_evenement_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet type_evenement
		$o = new site_type_evenement($row);
			
		#on retourne l'objet type_evenement
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->type_evenement_id = $tab["type_evenement_id"];
		$this->page_id = $tab["page_id"];
		$this->type_evenement_nom = stripslashes($tab["type_evenement_nom"]);

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
	
	#ajout d'une type_evenement
	public function ajoutType_evenement()
	{
		#Requete d'ajout de la type_evenement
		$query = "INSERT INTO ".self::table."_type_evenement value
				  (
					'$this->type_evenement_id',
					'$this->page_id',
					'".site_fonction::protec($this->type_evenement_nom)."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		$this->type_evenement_id = mysql_insert_id();
		
	}
	
	#Mise a jour type_evenement
	public function majType_evenement()
	{
		$query = "UPDATE  ".self::table."_type_evenement set
					page_id = '$this->page_id',
					type_evenement_nom = '".site_fonction::protec($this->type_evenement_nom)."'
					WHERE type_evenement_id = '$this->type_evenement_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un type_evenement
	public function supType_evenement()
	{
		#on supprime la type_evenement
		$query = "delete from ".self::table."_type_evenement where type_evenement_id=".$this->type_evenement_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on enleve tous les type de evenement existant sur les evenements deja enregistrer
		$query = "UPDATE  ".self::table."_evenement set type_evenement_id = 0  where type_evenement_id=".$this->type_evenement_id;
		$rs = mysql_query($query) or die(mysql_error()); 
		
	}
	
	public static function traitementFormulaire($post)
	{
			
		#creation de l'objet type_evenement de la nouvelelle année
		$a = new site_type_evenement($post);
		if($a->get("type_evenement_id") != "")
		{
			$a->majType_evenement();
			echo site_fonction::message("Type evenement",utf8_encode("Modification effectuée"));			
		}
		else 
		{
			$a->ajoutType_evenement();
			echo site_fonction::message("Type evenement",utf8_encode("Insertion effectuée"));	
		}
		
		return $a;	
		
	}
	
	#Methode pour recuperer le nom de page lié a une rubrique
	public function recupPageEvenement()
	{
		$p = site_page::recupPage($this->page_id);
		$nom = $p->get("page_nom");
		if($nom == ""){ $nom = "aucune"; }
		return $nom;
	}

	

}

?>