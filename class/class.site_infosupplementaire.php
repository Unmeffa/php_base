<?php
#inclusion de la class mere
require_once("class.site_fonction.php");
class site_infosupplementaire extends site_fonction 
{
	private $affaire_id;
	private $caracteristique_id;
	private $infosupplementaire_langue;
	private $infosupplementaire_valeur;

	public static function recupInfosupplementaire($id,$caracteristique_id,$langue = "fr")
	{
		#recuperation infosupplementaire
		$query = "select * from ".self::table."_infosupplementaire where affaire_id = ".$id." and caracteristique_id = $caracteristique_id and infosupplementaire_langue = '$langue'";
		$rs = mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
		
		#creation de l'objet infosupplementaire
		$o = new site_infosupplementaire($row);
		
		#on retourne l'objet infosupplementaire
		return $o;
	}
	
	public function recupValeurInfosupplementaire($id,$caracteristique_id,$langue,$site = 0)
	{
		$i = site_infosupplementaire::recupInfosupplementaire($id,$caracteristique_id,$langue);
		if($i->get("infosupplementaire_valeur") != "")
		{
			if($site == 1)
			{
				$c = site_caracteristique::recupCaracteristique($caracteristique_id);
				if($c->get("caracteristique_type") == "liste")
				{
					$o = site_option::recupOption($i->get("infosupplementaire_valeur"),$langue);
					if($langue == "fr" && $o->get("option_id") == "")
					{
						$o = site_option::recupOption($i->get("infosupplementaire_valeur"),"fr");
					}
					return $o->get("option_nom");
				}
				elseif($c->get("caracteristique_type") == "multiple")
				{
					$tab = explode("-",$i->get("infosupplementaire_valeur"));
					foreach($tab as $value)
					{
						$o = site_option::recupOption($value,$langue);
						if($langue == "fr" && $o->get("option_id") == "")
						{
							$o = site_option::recupOption($value,"fr");
						}
						$ph = site_fonction::recupPhotoPrincipale("option",$value);
						if($ph->get("photo_id") > 0)
						{
							$tab2[] = "<img src='".$ph->chemin_photo()."' title='".$o->get("option_nom")."' alt='".$o->get("option_nom")."' />";
						}
						else
						{
							if($caracteristique_id != 104) { $tab2[] = $o->get("option_nom"); };
						}
					}
					return $tab2;
				}
				elseif($c->get("caracteristique_type") == "booleen")
				{
					
					return site_infosupplementaire::valeurBooleen($i->get("infosupplementaire_valeur"),$langue);
				}
				else
				{
					return $i->get("infosupplementaire_valeur");
				}
			}
			else
			{
				return $i->get("infosupplementaire_valeur");
			}
		}
		else
		{
			return "";
		}
	}
	
	#methode qui renvoie le booleen avec oui ou non dans la langue passer en parametre
	public static function valeurBooleen($val,$langue)
	{
		if($langue == "fr")
		{
			if($val == 1){ $res = "oui"; } else { $res = "non"; }
		}
		elseif($langue == "en")
		{
			if($val == 1){ $res = "yes"; } else { $res = "no"; }
		}
		elseif($langue == "it")
		{
			if($val == 1){ $res = "si"; } else { $res = "non"; }
		}
		elseif($langue == "de")
		{
			if($val == 1){ $res = "ja"; } else { $res = "nicht"; }
		}
		elseif($langue == "ho")
		{
			if($val == 1){ $res = "ja"; } else { $res = "niet"; }
		}
		elseif($langue == "es")
		{
			if($val == 1){ $res = "s&iacute;"; } else { $res = "no"; }
		}
		return $res;
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
	
	#ajout d'une infosupplementaire
	public function ajoutInfosupplementaire()
	{
		
		#creration de la requete d'insertion
		$query = "INSERT INTO ".self::table."_infosupplementaire value(";
		
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
	
	#Mise a jour infosupplementaire
	public function majInfosupplementaire()
	{
		
		#ceration de la requete d'insertion
		$query = "UPDATE  ".self::table."_infosupplementaire set ";
		
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
		$query .= " WHERE caracteristique_id = '$this->caracteristique_id' and affaire_id = $this->affaire_id and infosupplementaire_langue = '$this->infosupplementaire_langue'";
		
		#execution de la requete
		$rs = mysql_query($query) or die($query);
	}
	
	#Suppression d'un infosupplementaire
	public static function supInfosupplementaire($id,$caracteristique)
	{
		
		#on supprime les details de ce infosupplementaire
		$query = "delete from ".self::table."_infosupplementaire where affaire_id = $id and caracteristique_id = '$caracteristique'";
		$rs = mysql_query($query) or die(mysql_error());
				
	}
	
	public static function recupInfosupplementaireAffaire($affaire_id)
	{
		$rs = site_fonction::recup("infosupplementaire","where affaire_id = $affaire_id","","","caracteristique_id");
		while($row = mysql_fetch_row($rs))
		{
			$tab[] = $row[0];
		}
		return $tab;
	}
	
	public static function traitementFormulaire($post)
	{
		#on supprime les details de ce infosupplementaire
		foreach($post["carac"] as $key => $value)
		{
			$info = site_infosupplementaire::recupInfosupplementaire($post["affaire_id"],$key,$post["infosupplementaire_langue"]);
			#si c'est un tableau cela veux dire que carac est de type case a cocher
			if(is_array($value))
			{
				#je treansforme mon tableau en chaine
				$valeur = implode("-",$value);
				#si il s'agit d'une modification
				if($info->get("affaire_id") == "")
				{
					unset($tab);
					$tab["affaire_id"] = $post["affaire_id"];
					$tab["caracteristique_id"] = $key;
					$tab["infosupplementaire_langue"] = $post["infosupplementaire_langue"];
					$tab["infosupplementaire_valeur"] = $valeur;
					$o = new site_infosupplementaire($tab);
					$o->ajoutInfosupplementaire();
				}
				#sinon il s'agit d'une insertion
				else
				{
					$info->set("infosupplementaire_valeur",$valeur);
					$info->majInfosupplementaire();
				}
			}
			else
			{
				if($info->get("affaire_id") == "")
				{
					unset($tab);
					$tab["affaire_id"] = $post["affaire_id"];
					$tab["caracteristique_id"] = $key;
					$tab["infosupplementaire_langue"] = $post["infosupplementaire_langue"];
					$tab["infosupplementaire_valeur"] = $value;
					$o = new site_infosupplementaire($tab);
					$o->ajoutInfosupplementaire();
				}
				else
				{
					$info->set("infosupplementaire_valeur",$value);
					$info->majInfosupplementaire();
				}
			}
		}
	}
	

}

?>