<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_detail_article extends site_fonction 
{
	private $detail_article_id;
	private $article_id;
	private $detail_article_langue;
	private $detail_article_titre;
	private $detail_article_description;

	public static function recupDetail_article($detail_article_id)
	{
		#recuperation detail_article
		$query = "select * from ".self::table."_detail_article where detail_article_id='".$detail_article_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet detail_article
		$o = new site_detail_article($row);
			
		#on retourne l'objet detail_article
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->detail_article_id = $tab["detail_article_id"];
		$this->article_id = $tab["article_id"];
		$this->detail_article_langue = $tab["detail_article_langue"];
		$this->detail_article_titre = $tab["detail_article_titre"];
		$this->detail_article_description = $tab["detail_article_description"];

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
	
	#ajout d'une detail_article
	public function ajoutDetail_article()
	{

		#Requete d'ajout de la detail_article
		$query = "INSERT INTO ".self::table."_detail_article value
				  (
					'$this->detail_article_id',
					'".$this->article_id."',
					'$this->detail_article_langue',
					'".site_fonction::protec($this->detail_article_titre)."',
					'".site_fonction::protec($this->detail_article_description)."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		
	}
	
	#Mise a jour detail_article
	public function majDetail_article()
	{
		$query = "UPDATE  ".self::table."_detail_article set
					article_id = '$this->article_id',
					detail_article_langue = '$this->detail_article_langue',
					detail_article_titre = '".site_fonction::protec($this->detail_article_titre)."',
					detail_article_description = '".site_fonction::protec($this->detail_article_description)."'
					WHERE detail_article_id = '$this->detail_article_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un detail_article
	public function supDetail_article()
	{
		#on supprime la detail_article
		$query = "delete from ".self::table."_detail_article where detail_article_id=".$this->detail_article_id;
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet detail_article de la nouvelelle année
		$a = new site_detail_article($post);
		if($a->get("detail_article_id") != "")
		{
			$a->majDetail_article();
			echo site_fonction::message("Détail article","Modification effectuée");			
		}
		else 
		{
			$a->ajoutDetail_article();
			echo site_fonction::message("Détail article","Insertion effectuée");	
		}
		return $a;	
	}
	
	#ajout d'une detail_article
	public function ajoutCommentaire()
	{

		#Requete d'ajout de la detail_article
		$query = "INSERT INTO ".self::table."_detail_article value
				  (
					'$this->detail_article_id',
					'".$this->article_id."',
					'$this->detail_article_langue',
					'".site_fonction::protec($this->detail_article_titre)."',
					'".site_fonction::protec(nl2br($this->detail_article_description))."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		
	}
	

}

?>