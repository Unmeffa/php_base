<?php
#inclusion de la class mere
require_once("class.site_fonction.php");
class site_caracteristique extends site_fonction 
{
	private $caracteristique_id;
	private $caracteristique_nom;
	private $caracteristique_type;
	private $caracteristique_unite;
	private $type_affaire_id;
	

	public static function recupCaracteristique($id = "")
	{
		if($id != "")
		{
			#recuperation caracteristique
			$query = "select * from ".self::table."_caracteristique where caracteristique_id=".$id;
			$rs = mysql_query($query);
			$row = @mysql_fetch_assoc($rs);
		}
		
		#creation de l'objet caracteristique
		$o = new site_caracteristique($row);
		
		#on retourne l'objet caracteristique
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
	
	#ajout d'un caracteristique
	public function ajoutCaracteristique()
	{
		
		#ceration de la requete d'insertion
		$query = "INSERT INTO ".self::table."_caracteristique value(";
		
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
		
		#mise a jour de l'objet
		$this->caracteristique_id = mysql_insert_id();
		
	}
	
	#Mise a jour caracteristique
	public function majCaracteristique()
	{
		
		#ceration de la requete d'insertion
		$query = "UPDATE  ".self::table."_caracteristique set ";
		
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
		$query .= " WHERE caracteristique_id = '$this->caracteristique_id'";
		
		#execution de la requete
		$rs = mysql_query($query) or die($query);
	}
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet caracteristique
		$p = new site_caracteristique($post);
		
		#Mise a jour de la base de donnÃ©es
		#Test pour savoir si c'est un insert ou un update
		if($p->get("caracteristique_id") == "")
		{
			$p->ajoutcaracteristique();
		}
		else
		{
			$p->majcaracteristique();
		}
		return $p;
	}
	
	
	#Suppression d'un caracteristique
	public function supCaracteristique()
	{
		#On recupere les photos de la caracteristique
		$rs = site_fonction::recup("photo","where produit_id = $this->caracteristique_id and photo_type = 'caracteristique'","","","photo_id");
		while( $ph = @mysql_fetch_row($rs) )
		{
			#suppresion des photos
			$o = site_photo::recupPhoto($ph[0]);
			$chemin = $o->chemin_dossier();
			$o->supPhoto();
		}
		
		#suppression des options
		$rs = site_fonction::recup("option","where caracteristique_id = $this->caracteristique_id","","","option_id");
		while($row = mysql_fetch_row($rs))
		{
			$o = site_option::supOption($row[0]);
		}

		#on supprime le caracteristique
		$query = "delete from ".self::table."_caracteristique where caracteristique_id = $this->caracteristique_id";
		$rs = mysql_query($query) or die(mysql_error());
		
	}
	
	public function creationFormulaire($valeur="")#$valeur est la valeur remplie dans la table produit pour cette caracteristique
	{
		#Si le format est une liste
		if($this->caracteristique_type == "liste")
		{
			#creation de l'element select
			$f = "<select name='carac[$this->caracteristique_id]' id='carac[$this->caracteristique_id]'><option value=''>&nbsp;</option>";
			
			#recuperation des options de la caracteristique
			$rs = site_fonction::recup("option","where caracteristique_id='$this->caracteristique_id' and option_langue = 'fr' order by option_nom","","","option_id,option_nom");
			while($o = mysql_fetch_row($rs))#ajout des options dans le select
			{
				$f .= "<option value='".$o[0]."'";
				
				#test pour selectionner la valeur du produit
				if($valeur == $o[0]){ $f.= ' selected="selected"';}
				
				$f .= ">&nbsp;&nbsp;".$o[1]."&nbsp;&nbsp;</option>";
			}
			
			#fermeture de la balise select
			$f .= "</select>";
		}
		
		#Si le format est un booleen
		elseif($this->caracteristique_type == "booleen")
		{
			#creation de l'element select
			$f = "<select name='carac[$this->caracteristique_id]' id='carac[$this->caracteristique_id]'><option value='' selected='selected'>&nbsp;</option>";
			$f .= "<option value='1' "; if($valeur== "1"){ $f .= " selected='selected'";} $f .= ">&nbsp;Oui&nbsp;</option>";
			$f .= "<option value='0' "; if($valeur== "0"){ $f .= " selected='selected'";} $f .= ">&nbsp;Non&nbsp;</option>";
			#fermeture de la balise select
			$f .= "</select>";
		}
		
		#Si le format est une ligne de texte
		elseif($this->caracteristique_type == "varchar")
		{
			#creation de l'élément html
			$f = "<input  style='width:400px;' type='text' id='carac[$this->caracteristique_id]' name='carac[$this->caracteristique_id]'";
			$f .= "value=\"".stripslashes($valeur)."\""; 
			$f .= " />";
		}
		#Si le format est numérique
		elseif($this->caracteristique_type == "numerique")
		{
			#creation de l'élément html
			$f = "<input  style='width:80px;'  type='text' id='carac[$this->caracteristique_id]' name='carac[$this->caracteristique_id]'";
			$f .= "value='".stripslashes($valeur)."'"; 
			$f .= " />".$this->caracteristique_unite;
		}
		#Si le format est du texte
		elseif($this->caracteristique_type == "texte")
		{
			#creation de l'élément html
			$f = "<textarea style='width:500px; height:150px;' id='carac[$this->caracteristique_id]' name='carac[$this->caracteristique_id]'>";
			$f .= stripslashes($valeur); 
			$f .="</textarea>";
		}
		
		#Si le format est multiple
		elseif($this->caracteristique_type == "multiple")
		{
			if($valeur != "")
			{
				$Tvaleur = explode("-",$valeur);
			}	
			#recuperation des options de la caracteristique
			$rs = site_fonction::recup("option","where caracteristique_id='$this->caracteristique_id' and option_langue = 'fr' order by option_nom","","","option_id,option_nom");
			while($o = mysql_fetch_row($rs))#ajout des cases a cocher
			{
				#creation de la checkbox avec name = tableau du nom de la caracteristique
				$f .= "<div style='width:198px; padding-left:2px; padding-right:10px; float:left'><input type='checkbox' id='carac-".$this->caracteristique_id.$o[0]."' name='carac[".$this->caracteristique_id."][]' value='".$o[0]."'";
				
				#Si l'option est dans dans le tableau
				if( (@in_array($o[0],$Tvaleur) == true)){ $f .= " checked='checked'";}
				
				#fermeture de la checkbox
				$f .= "/>&nbsp;&nbsp;".$o[1]." ".$this->caracteristique_unite;
				
				$f .= "</div>";
			}

		}
		
		return $f;
	}
	
}

?>