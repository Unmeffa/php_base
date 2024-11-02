<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_detail_tarif extends site_fonction 
{
	private $detail_tarif_id;
	private $tarif_id;
	private $detail_tarif_langue;
	private $detail_tarif_titre;
	private $detail_tarif_description;

	public static function recupDetail_tarif($detail_tarif_id)
	{
		#recuperation detail_tarif
		$query = "select * from ".self::table."_detail_tarif where detail_tarif_id='".$detail_tarif_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet detail_tarif
		$o = new site_detail_tarif($row);
			
		#on retourne l'objet detail_tarif
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->detail_tarif_id = $tab["detail_tarif_id"];
		$this->tarif_id = $tab["tarif_id"];
		$this->detail_tarif_langue = $tab["detail_tarif_langue"];
		$this->detail_tarif_titre = $tab["detail_tarif_titre"];
		$this->detail_tarif_description = $tab["detail_tarif_description"];

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
	
	#ajout d'une detail_tarif
	public function ajoutDetail_tarif()
	{
		#test pour la prio
		if($this->detail_tarif_id == "")
		{
			$this->detail_tarif_id = mysql_insert_id();
		}
		#Requete d'ajout de la detail_tarif
		$query = "INSERT INTO ".self::table."_detail_tarif value
				  (
					'$this->detail_tarif_id',
					'".$this->tarif_id."',
					'$this->detail_tarif_langue',
					'".site_fonction::protec($this->detail_tarif_titre)."',
					'".site_fonction::protec(nl2br($this->detail_tarif_description))."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	#Mise a jour detail_tarif
	public function majDetail_tarif()
	{
		$query = "UPDATE  ".self::table."_detail_tarif set
					tarif_id = '$this->tarif_id',
					detail_tarif_langue = '$this->detail_tarif_langue',
					detail_tarif_titre = '".site_fonction::protec($this->detail_tarif_titre)."',
					detail_tarif_description = '".site_fonction::protec(nl2br($this->detail_tarif_description))."'
					WHERE detail_tarif_id = '$this->detail_tarif_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un detail_tarif
	public function supDetail_tarif()
	{
		#on supprime la detail_tarif
		$query = "delete from ".self::table."_detail_tarif where detail_tarif_id=".$this->detail_tarif_id;
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet detail_tarif de la nouvelelle année
		$a = new site_detail_tarif($post);
		if($a->get("detail_tarif_id") != "")
		{
			$a->majDetail_tarif();
			echo site_fonction::message("Detail tarif",utf8_encode("Modification effectuée"));			
		}
		else 
		{
			$a->ajoutDetail_tarif();
			echo site_fonction::message("Detail tarif",utf8_encode("Insertion effectuée"));	
		}
		return $a;	
	}
	

}

?>