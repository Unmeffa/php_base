<?php
#inclusion de la class mere
require_once("class.site_fonction.php");
class site_affaire extends site_fonction 
{
	private $affaire_id;
	private $client_id;
	private $type_affaire_id;
	private $affaire_nom;
	private $affaire_etoile;
	private $affaire_website;
	private $affaire_email;
	private $affaire_tel1;
	private $affaire_tel2;
	private $affaire_fax;
	private $affaire_adresse;
	private $affaire_cp;
	private $affaire_ville;	
	private $affaire_prio;	
	private $affaire_actif;
	private $affaire_prixmin;
	private $affaire_localisation;
	private $affaire_adresse2;	
	private $affaire_adresse3;	
	private $affaire_id_resa;	
	private $affaire_region;
	private $affaire_ville_portail;
	
	public static function recupAffaire($id)
	{
		#recuperation affaire
		$query = "select * from ".self::table."_affaire where affaire_id=".$id;
		$rs = mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
		
		#creation de l'objet affaire
		$o = new site_affaire($row);
		
		#on retourne l'objet affaire
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
	
	#ajout d'une affaire
	public function ajoutAffaire()
	{
		
		#test pour la prio
		if($this->caracteristique_prio == "")
		{
			$rs = site_fonction::recup("affaire",'where type_affaire_id ='.$this->type_affaire_id);
			$nb = mysql_num_rows($rs);
			$this->affaire_prio = $nb+1;
		}
		
		#creration de la requete d'insertion
		$query = "INSERT INTO ".self::table."_affaire value(";
		
		#je parcours les champs de la table
		$i=0;
		foreach($this as $key => $value)
		{
			$i++;
			if($key != "prefixe")
			{
				if($i > 1){ $query .= ",";}
				$query .= "\"".site_fonction::insertString(nl2br($value))."\"";
			}
		}
		
		#fermeture de la requete			
		$query .= ")";
		
		#execution de la requete
		$rs = mysql_query($query) or die($query);
		
		#mise a jour de l'objet
		$this->affaire_id = mysql_insert_id();
		
	}
	
	#Mise a jour affaire
	public function majAffaire()
	{
		#creration de la requete d'insertion
		$query = "UPDATE  ".self::table."_affaire set ";
		
		#je parcours les champs de la table
		$i=0;
		foreach($this as $key => $value)
		{
			$i++;
			if($key != "prefixe")
			{
				if($i > 1){ $query .= ",";}
				$query .= "$key = \"".site_fonction::insertString(nl2br($value))."\"";
			}
		}
		
		#fermeture de la requete			
		$query .= " WHERE affaire_id = '$this->affaire_id'";
		
		#execution de la requete
		$rs = mysql_query($query) or die($query);
	}
	
	#Suppression d'un affaire
	public function supAffaire($chemin = "")
	{
		#On recupere les photo du affaire
		$rs = site_fonction::recup("photo","where produit_id = $this->affaire_id and photo_type = 'affaire'",0,1000,"photo_id");
		while( $ph = @mysql_fetch_row($rs) )
		{
			#suppresion des photos
			$o = site_photo::recupPhoto($ph[0]);
			$chemin = $o->chemin_dossier();
			$o->supPhoto();
		}
		#on supprime le dossier de photo du affaire si il existe
		if(file_exists($chemin))
		{
			rmdir($chemin);
		}
		#On recupere les documents des affaire
		$rs = site_fonction::recup("document","where produit_id = $this->affaire_id and document_type = 'affaire'",0,1000,"document_id");
		while( $ph = @mysql_fetch_row($rs) )
		{
			#suppresion des photos
			$o = site_document::recupDocument($ph[0]);
			$chemin = $o->chemin_dossier();
			$o->supDocument();
		}
		#on supprime le dossier de document du affaire si il existe
		if(file_exists($chemin))
		{
			rmdir($chemin);
		}
		
		#on supprime les details de ce affaire
		$query = "delete from ".self::table."_detail_affaire where affaire_id=".$this->affaire_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime les info supplémentaire
		$query = "delete from ".self::table."_infosupplementaire where affaire_id=".$this->affaire_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime le affaire
		$query = "delete from ".self::table."_affaire where affaire_id=".$this->affaire_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#mise a jour des prio
		site_prio::majPrio("affaire",$this->affaire_prio," and type_affaire_id = ".$this->type_affaire_id);
				
	}
	
	public static function traitementFormulaire($post)
	{
		
		#si la categorie a été changer
		if($post["type_affaire_id"] != $post["affaire_type_origine"]  && $post["affaire_type_origine"] != "")
		{
			#je met a jour les prio de son anciene categorie
			site_prio::majPrio("affaire",$post["affaire_prio"]," and type_affaire_id = ".$post["affaire_type_origine"]);
			
			#je reaffecte la nouvelle prio (derniere position)
			$rs = site_fonction::recup("affaire",'where type_affaire_id ='.$post["type_affaire_id"],"","","affaire_id");
			$nb = mysql_num_rows($rs);
			$post["affaire_prio"] = $nb+1;
			
		}
		#je passe mon affaire en actif par défaut
		if($post["affaire_actif"] == "") { $post["affaire_actif"] = 1;}
		#je degage le http:// du site
		$post["affaire_website"] = str_replace("http://","",$post["affaire_website"]);
		#transformation du tableaux ville en chaine
		$post["affaire_ville_portail"] = @implode("-",$post["affaire_ville_portail"]);

		#creation de l'objet affaire
		$p = new site_affaire($post);
		
		
		#Mise a jour de la base de données
		#Test pour savoir si c'est un insert ou un update
		if($p->get("affaire_id") == "")
		{
			$p->set("affaire_actif",1);
			$p->ajoutAffaire();
			$p->set("affaire_id",mysql_insert_id());
		}
		else
		{
			$p->majAffaire();
		}

		return $p;
	}


	#Ajout d'un type de affaire
	public static function ajoutTypeAffaire($page_id,$nom,$cat)
	{
		#Requete d'ajout de la affaire
		$query = "INSERT INTO ".self::table."_type_affaire value
				  (
					'',
					'$page_id',
					'$nom',
					'$cat'
				)";
		$rs = mysql_query($query);
	}
	
	#Suppression d'un affaire
	public function supTypeAffaire($id)
	{		
		#on supprime le type de affaire
		$query = "delete from ".self::table."_type_affaire where type_affaire_id=".$id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime les details du type de affaire
		$query = "delete from ".self::table."_detail_type where type_id=".$id." and detail_type_nom = 'affaire'";
		$rs = mysql_query($query) or die(mysql_error());
						
	}
	
	#modification d'un type de affaire
	public static function modTypeAffaire($id="",$page_id="",$nom="",$cat)
	{
		$query = "UPDATE  ".self::table."_type_affaire set
					page_id = '$page_id',
					type_affaire_nom = '$nom',
					type_affaire_categorie = '$cat'
					WHERE type_affaire_id = '$id'
					";
		$rs = mysql_query($query) or die($query);
	}
	
	
	#Methode pour recuperer le nom de page lié a une rubrique
	public function recupNomTypeAffaire()
	{
		$rs = site_fonction::recup("type_affaire","where type_affaire_id = $this->type_affaire_id",0,1,"type_affaire_nom");	
		$row = @mysql_fetch_row($rs);
		$nom = $row[0];
		if($nom == ""){ $nom = "aucune"; }
		return $nom;
	}
	
	#Methode pour recuperer le nom de page lié a une rubrique
	public function typeAffaire($lang = "fr")
	{
		$detail = site_fonction::recupDetail("type_affaire",$this->type_affaire_id,$lang,1);
		return $detail["titre"];
	}
	
	#Methode pour recuperer le nom de page lié a une rubrique
	public function recupPageTypeAffaire($id)
	{
		$rs = site_fonction::recup("page","where page_id = $id",0,1,"page_nom");	
		$row = @mysql_fetch_row($rs);
		$nom = $row[0];
		if($nom == ""){ $nom = "aucune"; }
		return $nom;
	}
	
	#metohde pour créer le lien avec insertion dans les stats
	public function creation_lien($autre = "",$img = "")
	{
		if($img == ""){ $img = $this->affaire_website; }
		$ch = "<a target='_blank' $autre href='".site_fonction::url_site."/admin/compte_stats_affaire.php?id=".$this->affaire_id."&type=affaire' title='$this->affaire_website'>$img</a>";
		return $ch;
	}
	
	public function recupVillePortail()
	{
		$rs =  site_fonction::recup("page","where page_id = $this->affaire_ville_portail",0,1,"page_nom");
		$row = mysql_fetch_row($rs);
		return $row[0];
	}
	
	function affPrixenImg($meta_h1 = "",$lang = "fr")
	{
		$prix=trim($this->affaire_prixmin);
		$tab_prix=str_split($prix);
		$AffichePrix="<span class='prix_affaire'>";
		$AffichePrix.="<img height='14px' src=\"images/nb/apd_".$lang.".png\" alt='".$meta_h1."' class='apd' />";
		$lonNombre=count($tab_prix);
		$compt=0;
		foreach($tab_prix as $chiffrep)
		{
			$compt++;
			if(($compt==2)&&($lonNombre>3)){
				$AffichePrix.="<img height='14px' src=\"images/nb/espace.png\" alt='".$meta_h1."' />";
			}	
			
			$AffichePrix.="<img height='14px' src=\"images/nb/".$chiffrep.".png\" alt='".$meta_h1."' />";
		}
		$AffichePrix.="<img height='14px' src=\"images/nb/euros.png\" class='euro' alt='".$meta_h1."' /></span>";
	
		return $AffichePrix;
	}
		
	
}

?>