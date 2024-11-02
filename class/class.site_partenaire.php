<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_partenaire extends site_fonction
{
	private $partenaire_id;
	private $partenaire_nom;
	private $partenaire_prio;
	private $partenaire_site;
	private $partenaire_tel;
	private $partenaire_mail;
	private $partenaire_actif;
	private $type_partenaire_id;


	public static function recupPartenaire($partenaire_id)
	{
		#recuperation partenaire
		$query = "select * from ".self::table."_partenaire where partenaire_id='".$partenaire_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);

		#creation de l'objet partenaire
		$o = new site_partenaire($row);

		#on retourne l'objet partenaire
		return $o;
	}

	#constructeur
	public function __construct($tab)
	{

		$this->partenaire_id = $tab["partenaire_id"];
		$this->partenaire_nom = $tab["partenaire_nom"];
		$this->partenaire_prio = $tab["partenaire_prio"];
		$this->partenaire_site = $tab["partenaire_site"];
		$this->partenaire_site = str_replace("http://","",str_replace("https://","",$this->partenaire_site));
		$this->partenaire_tel = $tab["partenaire_tel"];
		$this->partenaire_mail = $tab["partenaire_mail"];
		$this->partenaire_actif = $tab["partenaire_actif"];
		if($this->partenaire_actif == ""){ $this->partenaire_actif = 1;}
		$this->type_partenaire_id = $tab["type_partenaire_id"];

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

	#ajout d'une partenaire
	public function ajoutPartenaire()
	{
		#test pour la prio
		if($this->partenaire_prio == "")
		{
			$rs = site_fonction::recup("partenaire","where type_partenaire_id = '$this->type_partenaire_id'");
			$nb = mysql_num_rows($rs);
			$this->partenaire_prio = $nb+1;
		}
		#Requete d'ajout de la partenaire
		$query = "INSERT INTO ".self::table."_partenaire value
				  (
					'$this->partenaire_id',
					\"".site_fonction::protec($this->partenaire_nom)."\",
					'$this->partenaire_prio',
					'$this->partenaire_site',
					'$this->partenaire_tel',
					'$this->partenaire_mail',
					'$this->partenaire_actif',
					'$this->type_partenaire_id'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		$this->partenaire_id = mysql_insert_id();

	}

	#Mise a jour partenaire
	public function majPartenaire()
	{
		$query = "UPDATE  ".self::table."_partenaire set
					partenaire_nom = \"".site_fonction::protec($this->partenaire_nom)."\",
					partenaire_prio = '$this->partenaire_prio',
					partenaire_site = '$this->partenaire_site',
					partenaire_tel = '$this->partenaire_tel',
					partenaire_mail = '$this->partenaire_mail',
					partenaire_actif = '$this->partenaire_actif',
					type_partenaire_id = '$this->type_partenaire_id'
					WHERE partenaire_id = '$this->partenaire_id'
					";
		$rs = mysql_query($query) or die($query);
	}

	#Suppression d'un partenaire
	public function supPartenaire()
	{

		#on supprime la partenaire
		$query = "delete from ".self::table."_partenaire where partenaire_id=".$this->partenaire_id;
		$rs = mysql_query($query) or die(mysql_error());

		#on supprime les details de ce partenaire
		$query = "delete from ".self::table."_detail_partenaire where partenaire_id=".$this->partenaire_id;
		$rs = mysql_query($query) or die(mysql_error());

		#on supprime les photos de ce partenaire
		$rs = site_fonction::recup("photo","where produit_id = $this->partenaire_id and photo_type = 'partenaire'","","","photo_id");
		while($row = mysql_fetch_row($rs))
		{
			$ph = site_photo::recupPhoto($row[0]);
			$chemin = $ph->chemin_dossier();
			$ph->supPhoto();

		}

		#suppression du dossier
		@rmdir($chemin);

		#on met a jour les prio
		site_prio::majPrio("partenaire",$this->partenaire_prio," and type_partenaire_id = '".$this->type_partenaire_id."'");

	}

	public static function traitementFormulaire($post)
	{
		#si la categorie a été changer
		if($post["type_partenaire_id"] != $post["type_partenaire_id_origine"]  && $post["type_partenaire_id_origine"] != "")
		{
			#je met a jour les prio de son anciene categorie
			site_prio::majPrio("partenaire",$post["partenaire_prio"]," and type_partenaire_id = ".$post["type_partenaire_id_origine"]);

			#je reaffecte la nouvelle prio (derniere position)
			$rs = site_fonction::recup("partenaire",'where type_partenaire_id ='.$post["type_partenaire_id"],"",""," partenaire_id");
			$nb = mysql_num_rows($rs);
			$post["partenaire_prio"] = $nb+1;
		}

		#creation de l'objet partenaire de la nouvelelle année
		$a = new site_partenaire($post);
		if($a->get("partenaire_id") != "")
		{
			$a->majPartenaire();
			echo site_fonction::message("Lien",utf8_encode("Modification effectuée"));
		}
		else
		{
			$a->ajoutPartenaire();
			echo site_fonction::message("Lien",utf8_encode("Insertion effectuée"));
		}

		return $a;

	}

	#metohde pour créer le lien avec insertion dans les stats
	public function creation_lien($autre = "")
	{
		$ch = "<a target='_blank' $autre href='".site_fonction::url_site."/include/compte_stats.php?id=".$this->partenaire_id."&type=lien' title='$this->partenaire_site'>$this->partenaire_site</a>";
		return $ch;
	}
	
	public function creation_lien2($autre = "")
	{
		$ch = site_fonction::url_site."/include/compte_stats.php?id=".$this->partenaire_id."&type=lien";
		return $ch;
	}

}

?>
