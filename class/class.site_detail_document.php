<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_detail_document extends site_fonction 
{
	private $detail_document_id;
	private $document_id;
	private $detail_document_langue;
	private $detail_document_titre;
	private $detail_document_description;

	public static function recupDetail_document($detail_document_id)
	{
		#recuperation detail_document
		$query = "select * from ".self::table."_detail_document where detail_document_id='".$detail_document_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet detail_document
		$o = new site_detail_document($row);
			
		#on retourne l'objet detail_document
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->detail_document_id = $tab["detail_document_id"];
		$this->document_id = $tab["document_id"];
		$this->detail_document_langue = $tab["detail_document_langue"];
		$this->detail_document_titre = $tab["detail_document_titre"];
		$this->detail_document_description = $tab["detail_document_description"];

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
	
	#ajout d'une detail_document
	public function ajoutDetail_document()
	{
		#test pour la prio
		if($this->detail_document_id == "")
		{
			$this->detail_document_id = mysql_insert_id();
		}
		#Requete d'ajout de la detail_document
		$query = "INSERT INTO ".self::table."_detail_document value
				  (
					'$this->detail_document_id',
					'".$this->document_id."',
					'$this->detail_document_langue',
					'".site_fonction::protec($this->detail_document_titre)."',
					'".site_fonction::protec(nl2br($this->detail_document_description))."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	#Mise a jour detail_document
	public function majDetail_document()
	{
		$query = "UPDATE  ".self::table."_detail_document set
					document_id = '$this->document_id',
					detail_document_langue = '$this->detail_document_langue',
					detail_document_titre = '".site_fonction::protec($this->detail_document_titre)."',
					detail_document_description = '".site_fonction::protec(nl2br($this->detail_document_description))."'
					WHERE detail_document_id = '$this->detail_document_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un detail_document
	public function supDetail_document()
	{
		#on supprime la detail_document
		$query = "delete from ".self::table."_detail_document where detail_document_id=".$this->detail_document_id;
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet detail_document de la nouvelelle année
		$a = new site_detail_document($post);
		if($a->get("detail_document_id") != "")
		{
			$a->majDetail_document();
			echo site_fonction::message("Detail document",utf8_encode("Modification effectuée"));			
		}
		else 
		{
			$a->ajoutDetail_document();
			echo site_fonction::message("Detail document",utf8_encode("Insertion effectuée"));	
		}
		return $a;	
	}
	

}

?>