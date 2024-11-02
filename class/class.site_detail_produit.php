<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_detail_produit extends site_fonction 
{
	private $detail_produit_id;
	private $produit_id;
	private $detail_produit_langue;
	private $detail_produit_titre;
	private $detail_produit_description;
	private $detail_produit_meta_titre;
	private $detail_produit_meta_desc;
	private $detail_produit_meta_key;
	private $detail_produit_h1;	

	public static function recupDetail_produit($detail_produit_id)
	{
		#recuperation detail_produit
		$query = "select * from ".self::table."_detail_produit where detail_produit_id='".$detail_produit_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet detail_produit
		$o = new site_detail_produit($row);
			
		#on retourne l'objet detail_produit
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->detail_produit_id = $tab["detail_produit_id"];
		$this->produit_id = $tab["produit_id"];
		$this->detail_produit_langue = $tab["detail_produit_langue"];
		$this->detail_produit_titre = $tab["detail_produit_titre"];
		$this->detail_produit_description = $tab["detail_produit_description"];
		$this->detail_produit_meta_titre = $tab["detail_produit_meta_titre"];
		$this->detail_produit_meta_desc = $tab["detail_produit_meta_desc"];
		$this->detail_produit_meta_key = $tab["detail_produit_meta_key"];
		$this->detail_produit_h1 = $tab["detail_produit_h1"];							

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
	
	#ajout d'une detail_produit
	public function ajoutDetail_produit()
	{
		#test pour la prio
		if($this->detail_produit_id == "")
		{
			$this->detail_produit_id = mysql_insert_id();
		}
		#Requete d'ajout de la detail_produit
		$query = "INSERT INTO ".self::table."_detail_produit value
				  (
					'$this->detail_produit_id',
					'".$this->produit_id."',
					'$this->detail_produit_langue',
					'".site_fonction::protec($this->detail_produit_titre)."',
					'".site_fonction::protec($this->detail_produit_description)."',
					'".site_fonction::protec($this->detail_produit_meta_titre)."',
					'".site_fonction::protec($this->detail_produit_meta_desc)."',
					'".site_fonction::protec($this->detail_produit_meta_key)."',
					'".site_fonction::protec($this->detail_produit_h1)."'																
				  )";
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	#Mise a jour detail_produit
	public function majDetail_produit()
	{
		$query = "UPDATE  ".self::table."_detail_produit set
					produit_id = '$this->produit_id',
					detail_produit_langue = '$this->detail_produit_langue',
					detail_produit_titre = '".site_fonction::protec($this->detail_produit_titre)."',
					detail_produit_description = '".site_fonction::protec($this->detail_produit_description)."',
					detail_produit_meta_titre = '".site_fonction::protec($this->detail_produit_meta_titre)."',
					detail_produit_meta_desc = '".site_fonction::protec($this->detail_produit_meta_desc)."',
					detail_produit_meta_key = '".site_fonction::protec($this->detail_produit_meta_key)."',
					detail_produit_h1 = '".site_fonction::protec($this->detail_produit_h1)."'																		
					WHERE detail_produit_id = '$this->detail_produit_id'
					";
		
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un detail_produit
	public function supDetail_produit()
	{
		#on supprime la detail_produit
		$query = "delete from ".self::table."_detail_produit where detail_produit_id=".$this->detail_produit_id;
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet detail_produit de la nouvelelle année
		$a = new site_detail_produit($post);
		if($a->get("detail_produit_id") != "")
		{
			$a->majDetail_produit();
			echo site_fonction::message("D&eacute;tail produit",utf8_encode("Modification effectu&eacute;e"));			
		}
		else 
		{
			$a->ajoutDetail_produit();
			echo site_fonction::message("D&eacute;tail produit",utf8_encode("Insertion effectu&eacute;e"));	
		}
		return $a;	
	}
	

}

?>