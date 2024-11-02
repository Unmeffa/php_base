<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_detail_type extends site_fonction 
{
	private $detail_type_id;
	private $type_id;
	private $detail_type_langue;
	private $detail_type_titre;
	private $detail_type_description;
	private $detail_type_nom;

	public static function recupDetail_type($detail_type_id)
	{
		#recuperation detail_type
		$query = "select * from ".self::table."_detail_type where detail_type_id='".$detail_type_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet detail_type
		$o = new site_detail_type($row);
			
		#on retourne l'objet detail_type
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->detail_type_id = $tab["detail_type_id"];
		$this->type_id = $tab["type_id"];
		$this->detail_type_langue = $tab["detail_type_langue"];
		$this->detail_type_titre = $tab["detail_type_titre"];
		$this->detail_type_description = $tab["detail_type_description"];
		$this->detail_type_nom = $tab["detail_type_nom"];

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
	
	#ajout d'une detail_type
	public function ajoutDetail_type()
	{
		#test pour la prio
		if($this->detail_type_id == "")
		{
			$this->detail_type_id = mysql_insert_id();
		}
		#Requete d'ajout de la detail_type
		$query = "INSERT INTO ".self::table."_detail_type value
				  (
					'$this->detail_type_id',
					'".$this->type_id."',
					'$this->detail_type_langue',
					'".site_fonction::protec($this->detail_type_titre)."',
					'".site_fonction::protec(nl2br($this->detail_type_description))."',
					\"$this->detail_type_nom\"
				  )";
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	#Mise a jour detail_type
	public function majDetail_type()
	{
		$query = "UPDATE  ".self::table."_detail_type set
					type_id = '$this->type_id',
					detail_type_langue = '$this->detail_type_langue',
					detail_type_titre = '".site_fonction::protec($this->detail_type_titre)."',
					detail_type_description = '".site_fonction::protec(nl2br($this->detail_type_description))."',
					detail_type_nom = \"$this->detail_type_nom\"
					WHERE detail_type_id = '$this->detail_type_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un detail_type
	public function supDetail_type()
	{
		#on supprime la detail_type
		$query = "delete from ".self::table."_detail_type where detail_type_id=".$this->detail_type_id;
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet detail_type de la nouvelelle année
		$a = new site_detail_type($post);
		if($a->get("detail_type_id") != "")
		{
			$a->majDetail_type();
		}
		else 
		{
			$a->ajoutDetail_type();
		}
		return $a;	
	}
	

}

?>