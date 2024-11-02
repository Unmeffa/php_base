<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_type_partenaire extends site_fonction 
{
	private $type_partenaire_id;
	private $page_id;
	private $type_partenaire_nom;

	public static function recupType_partenaire($type_partenaire_id)
	{
		#recuperation type_partenaire
		$query = "select * from ".self::table."_type_partenaire where type_partenaire_id='".$type_partenaire_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet type_partenaire
		$o = new site_type_partenaire($row);
			
		#on retourne l'objet type_partenaire
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->type_partenaire_id = $tab["type_partenaire_id"];
		$this->page_id = $tab["page_id"];
		$this->type_partenaire_nom = stripslashes($tab["type_partenaire_nom"]);

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
	
	#ajout d'une type_partenaire
	public function ajoutType_partenaire()
	{
		#Requete d'ajout de la type_partenaire
		$query = "INSERT INTO ".self::table."_type_partenaire value
				  (
					'$this->type_partenaire_id',
					'$this->page_id',
					'".site_fonction::protec($this->type_partenaire_nom)."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		$this->type_partenaire_id = mysql_insert_id();
		
	}
	
	#Mise a jour type_partenaire
	public function majType_partenaire()
	{
		$query = "UPDATE  ".self::table."_type_partenaire set
					page_id = '$this->page_id',
					type_partenaire_nom = '".site_fonction::protec($this->type_partenaire_nom)."'
					WHERE type_partenaire_id = '$this->type_partenaire_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un type_partenaire
	public function supType_partenaire()
	{
		#on supprime la type_partenaire
		$query = "delete from ".self::table."_type_partenaire where type_partenaire_id=".$this->type_partenaire_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on enleve tous les type de partenaire existant sur les partenaires deja enregistrer
		$query = "UPDATE  ".self::table."_partenaire set type_partenaire_id = 0  where type_partenaire_id=".$this->type_partenaire_id;
		$rs = mysql_query($query) or die(mysql_error()); 
		
	}
	
	public static function traitementFormulaire($post)
	{
			
		#creation de l'objet type_partenaire de la nouvelelle année
		$a = new site_type_partenaire($post);
		if($a->get("type_partenaire_id") != "")
		{
			$a->majType_partenaire();
			echo site_fonction::message("Type partenaire",utf8_encode("Modification effectuée"));			
		}
		else 
		{
			$a->ajoutType_partenaire();
			echo site_fonction::message("Type partenaire",utf8_encode("Insertion effectuée"));	
		}
		
		return $a;	
		
	}
	
	#Methode pour recuperer le nom de page lié a une rubrique
	public function recupPagePartenaire()
	{
		$p = site_page::recupPage($this->page_id);
		$nom = $p->get("page_nom");
		if($nom == ""){ $nom = "aucune"; }
		return $nom;
	}
	

}

?>