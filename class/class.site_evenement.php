<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_evenement extends site_fonction 
{
	private $evenement_id;
	private $type_evenement_id;
	private $evenement_nom;
	private $evenement_prio;
	private $evenement_actif;
	private $evenement_site;
	private $evenement_debut;
	private $evenement_fin;
	
	

	public static function recupEvenement($evenement_id)
	{
		#recuperation evenement
		$query = "select * from ".self::table."_evenement where evenement_id='".$evenement_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet evenement
		$o = new site_evenement($row);
			
		#on retourne l'objet evenement
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{
		$this->evenement_id = $tab["evenement_id"];
		$this->type_evenement_id = $tab["type_evenement_id"];
		$this->evenement_nom = $tab["evenement_nom"];
		$this->evenement_prio = $tab["evenement_prio"];
		$this->evenement_actif = $tab["evenement_actif"];
		if($this->evenement_actif == ""){ $this->evenement_actif = 1;}
		$this->evenement_site = $tab["evenement_site"];
		$this->evenement_site = str_replace("http://","",$this->evenement_site);
		$this->evenement_debut = $tab["evenement_debut"];
		$this->evenement_fin = $tab["evenement_fin"];
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
	
	#ajout d'une evenement
	public function ajoutEvenement()
	{
		#test pour la prio
		if($this->evenement_prio == "")
		{
			$rs = site_fonction::recup("evenement","where type_evenement_id = '$this->type_evenement_id' order by evenement_prio","","","evenement_id");
			$nb = mysql_num_rows($rs);
			$this->evenement_prio = $nb+1;
		}
		#Requete d'ajout de la evenement
		$query = "INSERT INTO ".self::table."_evenement value
				  (
					'$this->evenement_id',
					'$this->type_evenement_id',
					\"".site_fonction::protec($this->evenement_nom)."\",
					'$this->evenement_prio',
					'$this->evenement_actif',
					'$this->evenement_site',
					'$this->evenement_debut',
					'$this->evenement_fin'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		$this->evenement_id = mysql_insert_id();
				
	}
	
	#Mise a jour evenement
	public function majEvenement()
	{
		$query = "UPDATE  ".self::table."_evenement set
					type_evenement_id = '$this->type_evenement_id',
					evenement_nom = \"".site_fonction::protec($this->evenement_nom)."\",
					evenement_prio = '$this->evenement_prio',
					evenement_debut = '$this->evenement_debut',
					evenement_actif = '$this->evenement_actif',
					evenement_site = '$this->evenement_site',
					evenement_fin = '$this->evenement_fin'
					WHERE evenement_id = '$this->evenement_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un evenement
	public function supEvenement()
	{

		#on supprime la evenement
		$query = "delete from ".self::table."_evenement where evenement_id=".$this->evenement_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime les details de ce evenement
		$query = "delete from ".self::table."_detail_evenement where evenement_id=".$this->evenement_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime les photos de ce evenement
		$rs = site_fonction::recup("photo","where produit_id = $this->evenement_id and photo_type = 'evenement'","","","photo_id");
		while($row = mysql_fetch_row($rs))
		{
			$ph = site_photo::recupPhoto($row[0]);
			$chemin = $ph->chemin_dossier();
			$ph->supPhoto();
		}
		#on supprime les documents de cet evenement
		$rs = site_fonction::recup("document","where produit_id = $this->evenement_id and document_type = 'evenement'","","","document_id");
		while($row = mysql_fetch_row($rs))
		{
			$ph = site_document::recupDocument($row[0]);
			$chemin = $ph->chemin_dossier();
			$ph->supDocument();
			
		}
		#suppression du dossier
		@rmdir($chemin);
		
		#on met a jour les prio
		site_prio::majPrio("evenement",$this->evenement_prio," and type_evenement_id = '".$this->type_evenement_id."'");
				
	}
	
	public static function traitementFormulaire($post)
	{
		#si la categorie a été changer
		if($post["type_evenement_id"] != $post["type_evenement_id_origine"]  && $post["type_evenement_id_origine"] != "")
		{
			#je met a jour les prio de son anciene categorie
			site_prio::majPrio("evenement",$post["evenement_prio"]," and type_evenement_id = ".$post["type_evenement_id_origine"]);
			
			#je reaffecte la nouvelle prio (derniere position)
			$rs = site_fonction::recup("evenement",'where type_evenement_id ='.$post["type_evenement_id"],"",""," evenement_id");
			$nb = mysql_num_rows($rs);
			$post["evenement_prio"] = $nb+1;
		}
			
		#creation de l'objet evenement de la nouvelelle année
		$a = new site_evenement($post);
		if($a->get("evenement_id") != "")
		{
			$a->majEvenement();
			echo site_fonction::message("Evenement",utf8_encode("Modification effectuée"));			
		}
		else 
		{
			$a->ajoutEvenement();
			echo site_fonction::message("Evenement",utf8_encode("Insertion effectuée"));	
		}
		
		return $a;	
		
	}
	
}

?>