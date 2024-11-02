<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_detail_photo extends site_fonction 
{
	private $detail_photo_id;
	private $photo_id;
	private $detail_photo_langue;
	private $detail_photo_titre;
	private $detail_photo_description;
	private $detail_photo_x;
	private $detail_photo_y;
	private $detail_photo_effet;
	private $detail_photo_color;

	public static function recupDetail_photo($detail_photo_id)
	{
		#recuperation detail_photo
		$query = "select * from ".self::table."_detail_photo where detail_photo_id='".$detail_photo_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet detail_photo
		$o = new site_detail_photo($row);
			
		#on retourne l'objet detail_photo
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->detail_photo_id = $tab["detail_photo_id"];
		$this->photo_id = $tab["photo_id"];
		$this->detail_photo_langue = $tab["detail_photo_langue"];
		$this->detail_photo_titre = $tab["detail_photo_titre"];
		$this->detail_photo_description = $tab["detail_photo_description"];
		$this->detail_photo_x = $tab["detail_photo_x"];
		$this->detail_photo_y = $tab["detail_photo_y"];
		$this->detail_photo_effet = $tab["detail_photo_effet"];
		$this->detail_photo_color = $tab["detail_photo_color"];

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
	
	#ajout d'une detail_photo
	public function ajoutDetail_photo()
	{
		#test pour la prio
		if($this->detail_photo_id == "")
		{
			$this->detail_photo_id = mysql_insert_id();
		}
		#Requete d'ajout de la detail_photo
		$query = "INSERT INTO ".self::table."_detail_photo value
				  (
					'$this->detail_photo_id',
					'".$this->photo_id."',
					'$this->detail_photo_langue',
					'".site_fonction::protec($this->detail_photo_titre)."',
					'".site_fonction::protec(nl2br($this->detail_photo_description))."',
					'".$this->axis($this->detail_photo_x)."',
					'".$this->axis($this->detail_photo_y)."',
					'".site_fonction::protec($this->detail_photo_effet)."',
					'".site_fonction::protec($this->detail_photo_color)."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());	
	}
	
	#Mise a jour detail_photo
	public function majDetail_photo()
	{
		$query = "UPDATE  ".self::table."_detail_photo set
					photo_id = '$this->photo_id',
					detail_photo_langue = '$this->detail_photo_langue',
					detail_photo_titre = '".site_fonction::protec($this->detail_photo_titre)."',
					detail_photo_description = '".site_fonction::protec(nl2br($this->detail_photo_description))."',
					detail_photo_x = '".$this->axis($this->detail_photo_x)."',
					detail_photo_y = '".$this->axis($this->detail_photo_y)."',
					detail_photo_effet = '".site_fonction::protec($this->detail_photo_effet)."',
					detail_photo_color = '".site_fonction::protec($this->detail_photo_color)."'
					WHERE detail_photo_id = '$this->detail_photo_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un detail_photo
	public function supDetail_photo()
	{
		#on supprime la detail_photo
		$query = "delete from ".self::table."_detail_photo where detail_photo_id=".$this->detail_photo_id;
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	public function axis($axe)
	{
		if($axe > 100)
		{
			$axe = 100;
		}
		
		if($axe < 0)
		{
			$axe = 0;
		}
		
		return $axe;
	}
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet detail_photo de la nouvelelle année
		$a = new site_detail_photo($post);
		if($a->get("detail_photo_id") != "")
		{
			$a->majDetail_photo();
			echo site_fonction::message("Detail photo",utf8_encode("Modification effectuée"));			
		}
		else 
		{
			$a->ajoutDetail_photo();
			echo site_fonction::message("Detail photo",utf8_encode("Insertion effectuée"));	
		}
		return $a;	
	}
	

}

?>