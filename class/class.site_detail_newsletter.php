<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_detail_newsletter extends site_fonction 
{
	private $detail_newsletter_id;
	private $newsletter_id;
	private $detail_newsletter_langue;
	private $detail_newsletter_titre;
	private $detail_newsletter_description;

	public static function recupDetail_newsletter($detail_newsletter_id)
	{
		#recuperation detail_newsletter
		$query = "select * from ".self::table."_detail_newsletter where detail_newsletter_id='".$detail_newsletter_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet detail_newsletter
		$o = new site_detail_newsletter($row);
			
		#on retourne l'objet detail_newsletter
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->detail_newsletter_id = $tab["detail_newsletter_id"];
		$this->newsletter_id = $tab["newsletter_id"];
		$this->detail_newsletter_langue = $tab["detail_newsletter_langue"];
		$this->detail_newsletter_titre = $tab["detail_newsletter_titre"];
		$this->detail_newsletter_description = $tab["detail_newsletter_description"];

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
	
	#ajout d'une detail_newsletter
	public function ajoutDetail_newsletter()
	{
		#test pour la prio
		if($this->detail_newsletter_id == "")
		{
			$this->detail_newsletter_id = mysql_insert_id();
		}
		#Requete d'ajout de la detail_newsletter
		$query = "INSERT INTO ".self::table."_detail_newsletter value
				  (
					'$this->detail_newsletter_id',
					'".$this->newsletter_id."',
					'$this->detail_newsletter_langue',
					'".site_fonction::protec($this->detail_newsletter_titre)."',
					'".site_fonction::protec($this->detail_newsletter_description)."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		
		
	}
	
	#Mise a jour detail_newsletter
	public function majDetail_newsletter()
	{
		$query = "UPDATE  ".self::table."_detail_newsletter set
					newsletter_id = '$this->newsletter_id',
					detail_newsletter_langue = '$this->detail_newsletter_langue',
					detail_newsletter_titre = '".site_fonction::protec($this->detail_newsletter_titre)."',
					detail_newsletter_description = '".site_fonction::protec($this->detail_newsletter_description)."'
					WHERE detail_newsletter_id = '$this->detail_newsletter_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un detail_newsletter
	public function supDetail_newsletter()
	{
		#on supprime la detail_newsletter
		$query = "delete from ".self::table."_detail_newsletter where detail_newsletter_id=".$this->detail_newsletter_id;
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet detail_newsletter
		$a = new site_detail_newsletter($post);
		if($a->get("detail_newsletter_id") != "")
		{
			$a->majDetail_newsletter();
			echo site_fonction::message("D&eacute;tail newsletter",utf8_encode("Modification effectu&eacute;e"));			
		}
		else 
		{
			$a->ajoutDetail_newsletter();
			echo site_fonction::message("D&eacute;tail newsletter",utf8_encode("Insertion effectu&eacute;e"));	
		}
		return $a;	
	}
	

}

?>