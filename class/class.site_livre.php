<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_livre extends site_fonction 
{
	private $livre_id;
	private $client_id;
	private $livre_message;
	private $livre_date;
	private $livre_actif;

	public static function recupLivre($livre_id)
	{
		#recuperation resa
		$query = "select * from ".self::table."_livre where livre_id='".$livre_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet resa
		$o = new site_livre($row);
			
		#on retourne l'objet resa
		return $o;
	}
		
	#constructeur
	public function __construct($tab)
	{
		$this->livre_id = $tab["livre_id"];
		$this->client_id = $tab["client_id"];
		$this->livre_message = $tab["livre_message"];
		$this->livre_date = $tab["livre_date"];
		$this->livre_actif = $tab["livre_actif"];
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
	
	#ajout d'une resa
	public function ajoutLivre()
	{
		#Requete d'ajout de la resa
		$query = "INSERT INTO ".self::table."_livre value
				  (
					'$this->livre_id',
					'$this->client_id',
					\"".site_fonction::protec(nl2br(strip_tags(mysql_real_escape_string($this->livre_message))))."\",
					'$this->livre_date',
					'$this->livre_actif'
				  )";
		$rs = mysql_query($query) or die($query());
		$this->resa_id = mysql_insert_id();
	}
	
	#Mise a jour resa
	public function majLivre()
	{
		$query = "UPDATE  ".self::table."_livre set
					livre_message = \"".site_fonction::protec(nl2br(strip_tags(mysql_real_escape_string($this->livre_message))))."\",
					livre_date = '$this->livre_date',
					livre_actif = \"".site_fonction::protec($this->livre_actif)."\"
					where livre_id = $this->livre_id
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un resa
	public function supLivre()
	{
		#on supprime la resa
		$query = "delete from ".self::table."_livre where livre_id=".$this->livre_id;
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	#traitement du formulaire
	public static function traitementFormulaire($post)
	{
		#creation du client
		$c = new site_client($post);
		#test si le client est déja renseigné
		$rs = site_fonction::recup("client","where client_mail = '".$c->get("client_mail")."'",0,1,"client_id");
		if(mysql_num_rows($rs) == 0)
		{
			#si il n'existe pas on le crée
			$c->ajoutClient();
			#mise a jour du tableau
			$post["client_id"] = $c->get("client_id");
		}
		else
		{
			#recuperation du client
			$row = mysql_fetch_row($rs);
			$post["client_id"] = $row[0];
		}
		
		#creation de l'objet resa
		$a = new site_livre($post);
		
		if($a->get("livre_id") != "")
		{
			$a->majLivre();
			//echo site_fonction::message("Livre d'or",utf8_encode("Modification effectuée"));			
		}
		else 
		{
			$a->ajoutLivre();
			//echo site_fonction::message("Livre d'or",utf8_encode("Insertion effectuée"));	
		}
		
		return $a;	
	}
}
?>