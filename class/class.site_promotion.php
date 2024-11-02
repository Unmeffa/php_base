<?php
#inclusion de la class mere
require_once("class.site_fonction.php");
class site_promotion extends site_fonction 
{
	private $promotion_id;
	private $affaire_id;
	private $promotion_debut;
	private $promotion_fin;
	private $promotion_pourcentage;

	public static function recupPromotion($id)
	{
		#recuperation promotion
		$query = "select * from ".self::table."_promotion where promotion_id = ".$id;
		$rs = mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
		
		#creation de l'objet promotion
		$o = new site_promotion($row);
		
		#on retourne l'objet promotion
		return $o;
	}

	#constructeur
	public function __construct($tab = "")
	{
		foreach($this as $key => $value)
		{
			$this->$key = $tab[$key];
		}
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
	
	#ajout d'une promotion
	public function ajoutPromotion()
	{
		
		#creration de la requete d'insertion
		$query = "INSERT INTO ".self::table."_promotion value(";
		
		#je parcours les champs de la table
		$i=0;
		foreach($this as $key => $value)
		{
			$i++;
			if($key != "prefixe")
			{
				if($i > 1){ $query .= ",";}
				$query .= "\"".site_fonction::insertString($value)."\"";
			}
		}
		
		#fermeture de la requete			
		$query .= ")";
		
		#execution de la requete
		$rs = mysql_query($query) or die($query);
		
	}
	
	#Mise a jour promotion
	public function majPromotion()
	{
		
		#ceration de la requete d'insertion
		$query = "UPDATE  ".self::table."_promotion set ";
		
		#je parcours les champs de la table
		$i=0;
		foreach($this as $key => $value)
		{
			$i++;
			if($key != "prefixe")
			{
				if($i > 1){ $query .= ",";}
				$query .= "$key = \"".site_fonction::insertString($value)."\"";
			}
		}
		
		#fermeture de la requete			
		$query .= " WHERE promotion_id = $this->promotion_id";
		
		#execution de la requete
		$rs = mysql_query($query) or die($query);
	}
	
	#Suppression d'un promotion
	public function supPromotion()
	{
		#on supprime les details de ce promotion
		$query = "delete from ".self::table."_promotion where promotion_id = $this->promotion_id";
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet promotion
		$p = new site_promotion($post);
		
		#Mise a jour de la base de donnÃ©es
		#Test pour savoir si c'est un insert ou un update
		if($p->get("promotion_id") == "")
		{
			$p->ajoutpromotion();
		}
		else
		{
			$p->majpromotion();
		}
		return $p;
	}
	
	

}

?>