<?php
#inclusion de la class mere
require_once("class.site_fonction.php");
class site_option extends site_fonction 
{
	private $option_id;
	private $option_langue;
	private $caracteristique_id;
	private $option_nom;
	private $option_description;

	public static function recupOption($id,$langue)
	{
		#recuperation option
		$query = "select * from ".self::table."_option where option_id = ".$id." and option_langue = '$langue'";
		$rs = mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
		
		#creation de l'objet option
		$o = new site_option($row);
		
		#on retourne l'objet option
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
	
	#ajout d'une option
	public function ajoutOption()
	{
		
		#creration de la requete d'insertion
		$query = "INSERT INTO ".self::table."_option value(";
		
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
		$this->option_id = mysql_insert_id();
		
	}
	
	#Mise a jour option
	public function majOption()
	{
		#creration de la requete d'insertion
		$query = "UPDATE  ".self::table."_option set ";
		
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
		$query .= " WHERE option_id = '$this->option_id' and option_langue = '$this->option_langue'";
		
		#execution de la requete
		$rs = mysql_query($query) or die($query);
	}
	
	#Suppression d'un option
	public static function supOption($id)
	{
		#On recupere les photos de l'option
		$rs = site_fonction::recup("photo","where produit_id = $id and photo_type = 'option'","","","photo_id");
		while( $ph = @mysql_fetch_row($rs) )
		{
			#suppresion des photos
			$o = site_photo::recupPhoto($ph[0]);
			$chemin = $o->chemin_dossier();
			$o->supPhoto();
		}
		
		#on supprime les details de ce option
		$query = "delete from ".self::table."_option where option_id= $id";
		$rs = mysql_query($query) or die(mysql_error());
				
	}
	
	public static function traitementFormulaire($post)
	{
		#récupération des langues actives
		$f = new site_fonction();
		$Tlangue = $f->getLangue();
		
		if($post["option_id"] == "")
		{
			foreach($Tlangue as $Tvalue)
			{
				#creation de mon tableau avec le post
				unset($tab);
				$tab["option_id"] = $post["option_id"];
				$tab["caracteristique_id"] = $post["caracteristique_id"];
				$tab["option_langue"] = $Tvalue[0];
				$tab["option_nom"] = utf8_decode($post["option_nom_".$Tvalue[0]]);
				$tab["option_description"] = utf8_decode($post["option_description_".$Tvalue[0]]);
				
				#creation et insertion de l'objet option
				$p = new site_option($tab);
				$p->ajoutOption();
				$p->set("option_id",mysql_insert_id());
				$post["option_id"] = $p->get("option_id");
				
			}
		}
		else
		{
			foreach($Tlangue as $Tvalue)
			{
				$rs = site_fonction::recup("option","where option_id = ".$post["option_id"]." and option_langue = '".$Tvalue[0]."'",0,1,"option_id,option_langue");
				if(mysql_num_rows($rs) > 0)
				{
					$row = mysql_fetch_row($rs);
					#creation de mon tableau avec le post
					$c = site_option::recupOption($row[0],$row[1]);
					$c->set("option_nom",utf8_decode($post["option_nom_".$Tvalue[0]]));
					$c->set("option_description",utf8_decode($post["option_description_".$Tvalue[0]]));
					$c->majOption();
				}
				else
				{
					$tab["option_id"] = $post["option_id"];
					$tab["caracteristique_id"] = $post["caracteristique_id"];
					$tab["option_langue"] = $Tvalue[0];
					$tab["option_nom"] = utf8_decode($post["option_nom_".$Tvalue[0]]);
					$tab["option_description"] = utf8_decode($post["option_description_".$Tvalue[0]]);
					
					#creation et insertion de l'objet option
					$p = new site_option($tab);
					$p->ajoutOption();
					$p->set("option_id",mysql_insert_id());
				}
				
			}
		}
	}
	
	#recuperation de tous les enregistrement en multi langue
	public static function recupMultiLangue($id)
	{
		#récupération des langues actives
		$f = new site_fonction();
		$Tlangue = $f->getLangue();
		
		foreach($Tlangue as $Tvalue)
		{
			#creation de mon tableau avec le post
			$rs = site_fonction::recup("option","where option_id = $id and option_langue = '".$Tvalue[0]."'",0,1,"option_nom");
			if(mysql_num_rows($rs) > 0)
			{
				#Si j ai un enregistrement je l'ajoute a mon tableau
				$row = mysql_fetch_row($rs);
				$tab[] = $row[0];
			}
			else
			{
				$tab[] = "";
			}
			
		}
		
		return($tab);
	}

}

?>