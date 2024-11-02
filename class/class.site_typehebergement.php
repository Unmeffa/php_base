<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_typehebergement extends site_fonction 
{
	private $typehebergement_id;
	private $typehebergement_nom;
	private $typehebergement_actif;
	private $typehebergement_prio;
	private $produit_id;
	
	#methode de recuperation d'un objet hebergement
	public static function recupTypehebergement($typehebergement_id)
	{
		$query = "SELECT * FROM " . site_fonction::table . "_typehebergement WHERE typehebergement_id=" . $typehebergement_id;
		$rs = mysql_query($query) or die($query);
		$row = mysql_fetch_assoc($rs);
		$h = new site_Typehebergement($row["typehebergement_id"],$row["typehebergement_nom"],$row["typehebergement_actif"],$row["typehebergement_prio"],$row["produit_id"]);
		return $h;
	}
	

	#constructeur//////////////////////////////////////////
	public function __construct($typehebergement_id = "",$typehebergement_nom = "",$typehebergement_actif = 1,$typehebergement_prio = "",$produit_id = "" )
	{
		$this->typehebergement_id = $typehebergement_id;	
		$this->typehebergement_nom = stripslashes(site_fonction::protec($typehebergement_nom));
		$this->typehebergement_actif = $typehebergement_actif;
		if($this->typehebergement_actif == ""){ $this->typehebergement_actif = 1; }
		$this->typehebergement_prio = $typehebergement_prio;
		$this->produit_id = $produit_id;

	}
	
	//Mise a jour d'un typehebergement
	public function majTypehebergement()
	{
	    $query = "UPDATE  " . site_fonction::table . "_typehebergement set
					typehebergement_nom = \"".$this->typehebergement_nom."\",
					produit_id = '$this->produit_id',
					typehebergement_prio = '$this->typehebergement_prio',
					typehebergement_actif = '$this->typehebergement_actif',
					produit_id = '$this->produit_id'
					WHERE typehebergement_id = $this->typehebergement_id
				";
		$rs = mysql_query($query) or die($query);

	}
	
	//Ajout d'un typehebergement
	public function ajoutTypehebergement()
	{
		#test pour la prio
		if($this->typehebergement_prio == "")
		{
			$rs = site_fonction::recup("typehebergement");
			$nb = mysql_num_rows($rs);
			$this->typehebergement_prio = $nb+1;
		}

		$query = "INSERT INTO ".site_fonction::table."_typehebergement value
		(
				'',
				\"" .$this->typehebergement_nom. "\",
				'$this->typehebergement_actif',
				'$this->typehebergement_prio',
				'$this->produit_id'
				
		)";
		$rs = mysql_query($query) or die($query);
		
		#mise a jour de l'id du type d'hebergement
		$this->typehebergement_id = mysql_insert_id();
		
	}
	
	//supression d'un hebergeemnt
	public function supTypehebergement()
	{	
	
			#On recupere les photos du type d'hebergement
			$rs = site_fonction::recup("photo","where produit_id = $this->typehebergement_id and photo_type = 'typehebergement'","","","photo_id");
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
			#On recupere les documents du type d hebergement
			$rs = site_fonction::recup("document","where produit_id = $this->typehebergement_id and document_type = 'typehebergement'","","","document_id");
			while( $ph = @mysql_fetch_row($rs) )
			{
				#suppresion des photos
				$o = site_document::recupDocument($ph[0]);
				$chemin = $o->chemin_dossier();
				$o->supDocument();
			}
			#on supprime le dossier de document du type d hebergement si il existe
			if(file_exists($chemin))
			{
				rmdir($chemin);
			}
			
			#on recupere tous les tarifs de ce type d'hebergement et on les supprime
			$rs = site_fonction::recup("tarif","where typehebergement_id = $this->typehebergement_id","","","tarif_id");
			while( $ph = @mysql_fetch_row($rs) )
			{
				#suppresion des photos
				$o = site_tarif::recupTarif($ph[0]);
				$o->supTarif();
			}
			
			#on supprime la typehebergement
			$query = "delete from ".self::table."_typehebergement where typehebergement_id=".$this->typehebergement_id;
			$rs = mysql_query($query) or die(mysql_error());
			
			#on supprime les details de ce typehebergement
			$query = "delete from ".self::table."_detail_typehebergement where typehebergement_id=".$this->typehebergement_id;
			$rs = mysql_query($query) or die(mysql_error());
			
			#mise a jour des prio
			site_prio::majPrio("typehebergement",$this->typehebergement_prio);

	}	
	
	#methode de récuperation d'un champ////////////////////////////////
	public function get($attribut)
	{
		return($this->$attribut);
	}
	#methode de modification d'un attribut///////////////////////////////
	public function set($attribut,$valeur)
	{
		$this->$attribut = $valeur;
	}


	# methode de recuperatuon des hebergement d'un meme type
	public function recupHebergement($debut="",$lim="")
	{
		$rs = site_fonction::recup("hebergement","where typehebergement_id = ".$this->typehebergement_id." order by hebergement_prio",$debut,$lim);
		return $rs;
	}
	
	# methode de recuperatuon des hebergement d'un meme type
	public function recupTarifhebergement($datedebut = "",$datefin = "")
	{
		if($datedebut == "")
		{
			$rs = site_fonction::recup("tarifhebergement","where typehebergement_id = ".$this->typehebergement_id." order by tarifhebergement_prio");
			return $rs;
		}
		else
		{
			#recuperation des tarifs avec contrainte de date
			$sql1 = "SELECT DISTINCT(t.tarifhebergement_id),t.typehebergement_id,t.tarifhebergement_nom 
			         FROM ".site_fonction::table."_tarifhebergement as t,".site_fonction::table."_periodehebergement as p 
					 WHERE p.periodehebergement_actif=1 and typehebergement_id = ".$this->typehebergement_id." 
					 AND (( (periodehebergement_datedebut <= '$datedebut') AND (periodehebergement_datefin >= '$datedebut'))  OR ( (periodehebergement_datedebut <= '$datefin') AND (periodehebergement_datefin >= '$datefin')))
					 and t.tarifhebergement_id = p.tarifhebergement_id";
			$rs = mysql_query($sql1);
			return $rs;
		}
	}
	
	public static function traitementFormulaire($id,$nom,$actif,$prio,$produit_id)
	{
		#creation de l'objet typehebergement de la nouvelelle année
		$a = new site_typehebergement($id,$nom,$actif,$prio,$produit_id);
		if($a->get("typehebergement_id") != "")
		{
			$a->majTypehebergement();
			echo site_fonction::message("Type tarif",utf8_encode("Modification effectuée"));			
		}
		else 
		{
			$a->ajoutTypehebergement();
			echo site_fonction::message("Type tarif",utf8_encode("Insertion effectuée"));	
		}
		return $a;	
	}
	
	
	
}

?>