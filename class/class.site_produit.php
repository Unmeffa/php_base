<?php
#inclusion de la class mere
require_once("class.site_fonction.php");
class site_produit extends site_fonction 
{
	private $produit_id;
	private $produit_nom;
	private $produit_prio;
	private $produit_prix;
	private $produit_type;
	private $produit_actif;
	private $prefixe = "produit_";
	
	public static function recupProduit($id)
	{
		#recuperation produit
		$query = "select * from ".self::table."_produit where produit_id=".$id;
		$rs = mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
		
		#creation de l'objet produit
		$o = new site_produit($row);
		
		#on retourne l'objet produit
		return $o;
		
	}
	
	
	#constructeur
	public function __construct($tab = "")
	{
		foreach($this as $key => $value)
		{
			if($key != "prefixe")
			{
				$this->$key = $tab[$key];
			}
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
	
	#methode de modification d'un attribut
	public function livraison()
	{
		if($this->$produit_livraison == 0)
		{
			$ch = "En boutique";
		}
		if($this->$produit_livraison == 1)
		{
			$ch = "Livrer";
		}
		else
		{
			$ch = "Livrer ou à venir chercher en boutique";
		}
	}
	
	#ajout d'un produit
	public function ajoutProduit()
	{
		
		#test pour la prio
		if($this->caracteristique_prio == "")
		{
			$rs = site_fonction::recup("produit",'where produit_type ='.$this->produit_type);
			$nb = mysql_num_rows($rs);
			$this->produit_prio = $nb+1;
		}
		
		#ceration de la requete d'insertion
		$query = "INSERT INTO ".self::table."_produit value(";
		
		#je parcours les champs de la table
		$i=0;
		foreach($this as $key => $value)
		{
			$i++;
			if($key != "prefixe")
			{
				if($i > 1){ $query .= ",";}
				$query .= "\"".site_fonction::protec($value)."\"";
			}
		}
		
		#fermeture de la requete			
		$query .= ")";
		
		#execution de la requete
		$rs = mysql_query($query) or die($query);
		
	}
	
	#Mise a jour produit
	public function majProduit()
	{
		#ceration de la requete d'insertion
		$query = "UPDATE  ".self::table."_produit set ";
		
		#je parcours les champs de la table
		$i=0;
		foreach($this as $key => $value)
		{
			$i++;
			if($key != "prefixe")
			{
				if($i > 1){ $query .= ",";}
				$query .= "$key = \"".site_fonction::protec($value)."\"";
			}
		}
		
		#fermeture de la requete			
		$query .= " WHERE produit_id = '$this->produit_id'";
		
		#execution de la requete
		$rs = mysql_query($query) or die($query);
	}
	
	#Suppression d'un produit
	public function supProduit($chemin = "")
	{
		#On recupere les photo du produit
		$rs = site_fonction::recup("photo","where produit_id = $this->produit_id and photo_type = 'produit'",0,1000,"photo_id");
		while( $ph = @mysql_fetch_row($rs) )
		{
			#suppresion des photos
			$o = site_photo::recupPhoto($ph[0]);
			$chemin = $o->chemin_dossier();
			$o->supPhoto();
		}
		#on supprime le dossier de photo du produit si il existe
		if(file_exists($chemin))
		{
			rmdir($chemin);
		}
		#On recupere les documents du produit
		$rs = site_fonction::recup("document","where produit_id = $this->produit_id and document_type = 'produit'",0,1000,"document_id");
		while( $ph = @mysql_fetch_row($rs) )
		{
			#suppresion des photos
			$o = site_document::recupDocument($ph[0]);
			$chemin = $o->chemin_dossier();
			$o->supDocument();
		}
		#on supprime le dossier de document du produit si il existe
		if(file_exists($chemin))
		{
			rmdir($chemin);
		}
		
		#on supprime les details de ce produit
		$query = "delete from ".self::table."_detail_produit where produit_id=".$this->produit_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime le produit
		$query = "delete from ".self::table."_produit where produit_id=".$this->produit_id;
		$rs = mysql_query($query) or die(mysql_error());
		

		#mise a jour des prio
		site_prio::majPrio("produit",$this->produit_prio," and produit_type = ".$this->produit_type);
				
	}
	
	public static function traitementFormulaire($post)
	{
		#si la categorie a été changer
		if($post["produit_type"] != $post["produit_type_origine"]  && $post["produit_type_origine"] != "")
		{
			#je met a jour les prio de son anciene categorie
			site_prio::majPrio("produit",$post["produit_prio"]," and produit_type = ".$post["produit_type_origine"]);
			
			#je reaffecte la nouvelle prio (derniere position)
			$rs = site_fonction::recup("produit",'where produit_type ='.$post["produit_type"],"0","1000"," produit_id");
			$nb = mysql_num_rows($rs);
			$post["produit_prio"] = $nb+1;
		}
		#creation de l'objet produit
		$p = new site_produit($post);

		#Mise a jour de la base de donnÃ©es
		#Test pour savoir si c'est un insert ou un update
		if($p->get("produit_id") == "")
		{
			$p->ajoutProduit();
			$p->set("produit_id",mysql_insert_id());
		}
		else
		{
			$p->majProduit();
		}
		
		return $p;
	}

	#methode de calcul d'un prix ttc par rapport a un prix HT
	public static function calculTTC($prixHT)
	{
		$prix = round($prixHT*1.196,2);
		$prix = site_fonction::arrondi($prix);
		return($prix);
	}
	
	#Ajout d'un type de produit
	public static function ajoutTypeProduit($page_id,$nom)
	{
		#Requete d'ajout de la produit
		$query = "INSERT INTO ".self::table."_type_produit value
				  (
					'',
					'$page_id',
					'".site_fonction::protec($nom)."'
				)";
		$rs = mysql_query($query);
	}
	
	#Suppression d'un produit
	public function supTypeProduit($id)
	{		
		#on supprime le type de produit
		$query = "delete from ".self::table."_type_produit where type_produit_id=".$id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime les details du type de produit
		$query = "delete from ".self::table."_detail_type where type_id=".$id." and detail_type_nom = 'produit'";
		$rs = mysql_query($query) or die(mysql_error());
						
	}
	
	#modification d'un type de produit
	public static function modTypeProduit($id="",$page_id="",$nom="")
	{
		$query = "UPDATE  ".self::table."_type_produit set
					page_id = '$page_id',
					type_produit_nom = '".site_fonction::protec($nom)."'
					WHERE type_produit_id = '$id'
					";
		$rs = mysql_query($query) or die($query);
	}
	

	
	#Methode pour recuperer le nom de page lié a une rubrique
	public static function recupPageRubrique($type_produit_id)
	{
		$rs = site_fonction::recup("type_produit","where type_produit_id = $type_produit_id",0,1,"page_id");	
		$row = mysql_fetch_row($rs);
		$p = site_page::recupPage($row[0]);
		$nom = $p->get("page_nom");
		if($nom == ""){ $nom = "aucune"; }
		return $nom;
	}
	#Methode pour recuperer le nom de page lié a une rubrique
	public static function recupIdPageRubrique($type_produit_id)
	{
		$rs = site_fonction::recup("type_produit","where type_produit_id = $type_produit_id",0,1,"page_id");	
		$row = mysql_fetch_row($rs);
		return $row[0];
	}	
	
}

?>