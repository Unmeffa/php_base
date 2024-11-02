<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_client extends site_fonction 
{
	private $client_id;
	private $type_client_id;
	private $client_civilite;
	private $client_nom;
	private $client_prenom;
	private $client_societe;
	private $client_lang;
	private $client_mdp;
	private $client_mail;
	private $client_adresse;
	private $client_complement_adresse;
	private $client_cp;
	private $client_ville;
	private $client_pays;
	private $client_tel1;
	private $client_tel2;
	private $client_port;
	private $client_fax;
	private $client_date_naissance;
	private $client_lieu_naissance;
	private $client_profession;
	private $client_tva_intra;
	private $client_newsletter;
	private $client_commentaire;
	
	#methode de recuperation d'un client par son id
	public static function recupClient($client_id)
	{
		#recuperation client
		$query = "select * from ".self::table."_client where client_id='".$client_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet client
		$o = new site_client($row);
			
		#on retourne l'objet client
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{
		$this->client_id = $tab["client_id"];
		$this->type_client_id = $tab["type_client_id"];
		$this->client_civilite = $tab["client_civilite"];
		$this->client_nom = $tab["client_nom"];
		$this->client_prenom = $tab["client_prenom"];
		$this->client_societe = $tab["client_societe"];
		$this->client_lang = $tab["client_lang"];
		$this->client_mdp = $tab["client_mdp"];
		$this->client_mail = $tab["client_mail"];
		$this->client_adresse = $tab["client_adresse"];
		$this->client_complement_adresse = $tab["client_complement_adresse"];
		$this->client_cp = $tab["client_cp"];
		$this->client_ville = $tab["client_ville"];
		$this->client_pays = $tab["client_pays"];
		$this->client_tel1 = $tab["client_tel1"];
		$this->client_tel2 = $tab["client_tel2"];
		$this->client_port = $tab["client_port"];
		$this->client_fax = $tab["client_fax"];
		$this->client_date_naissance = $tab["client_date_naissance"];
		$this->client_lieu_naissance = $tab["client_lieu_naissance"];
		$this->client_profession = $tab["client_profession"];
		$this->client_tva_intra = $tab["client_tva_intra"];
		$this->client_newsletter = $tab["client_newsletter"];
		if($this->client_newsletter == ""){ $this->client_newsletter = 1; }
		$this->client_commentaire = $tab["client_commentaire"];
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
	
	#ajout d'une client
	public function ajoutClient()
	{
		#Requete d'ajout de la client
		$query = "INSERT INTO ".self::table."_client value
				  (
					'$this->client_id',
					'$this->type_client_id',
					'$this->client_civilite',
					\"".site_fonction::protec($this->client_nom)."\",
					\"".site_fonction::protec($this->client_prenom)."\",
					'".site_fonction::protec($this->client_societe)."',
					'$this->client_lang',
					'$this->client_mdp',
					'$this->client_mail',
					\"".site_fonction::protec($this->client_adresse)."\",
					\"".site_fonction::protec($this->client_complement_adresse)."\",
					'$this->client_cp',
					\"".site_fonction::protec($this->client_ville)."\",
					\"".site_fonction::protec($this->client_pays)."\",
					'$this->client_tel1',
					'$this->client_tel2',
					'$this->client_port',
					'$this->client_fax',
					'$this->client_date_naissance',
					'".site_fonction::protec($this->client_lieu_naissance)."',
					\"".site_fonction::protec($this->client_profession)."\",
					'$this->client_tva_intra',
					'$this->client_newsletter',
					'".site_fonction::protec($this->client_commentaire)."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		$this->client_id = mysql_insert_id();
	}
	
	#Mise a jour client
	public function majClient()
	{
		$query = "UPDATE  ".self::table."_client set
					type_client_id = '$this->type_client_id',
					client_civilite = '$this->client_civilite',
					client_nom = \"".site_fonction::protec($this->client_nom)."\",
					client_prenom = \"".site_fonction::protec($this->client_prenom)."\",
					client_societe = \"".site_fonction::protec($this->client_societe)."\",
					client_lang = '$this->client_lang',
					client_mdp = '$this->client_mdp',
					client_mail = '$this->client_mail',
					client_adresse = \"".site_fonction::protec($this->client_adresse)."\",
					client_complement_adresse = \"".site_fonction::protec($this->client_complement_adresse)."\",
					client_cp = '$this->client_cp',
					client_ville = \"".site_fonction::protec($this->client_ville)."\",
					client_pays = \"".site_fonction::protec($this->client_pays)."\",
					client_tel1 = '$this->client_tel1',
					client_tel2 = '$this->client_tel2',
					client_port = '$this->client_port',
					client_fax = '$this->client_fax',
					client_date_naissance = '$this->client_date_naissance',
					client_lieu_naissance = \"".site_fonction::protec($this->client_lieu_naissance)."\",
					client_profession = \"".site_fonction::protec($this->client_profession)."\",
					client_tva_intra = '$this->client_tva_intra',
					client_newsletter = '$this->client_newsletter',
					client_commentaire = \"".site_fonction::protec($this->client_commentaire)."\"
					WHERE client_id = '$this->client_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un client
	public function supClient()
	{
		#on supprime la client
		$query = "delete from ".self::table."_client where client_id=".$this->client_id;
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet client de la nouvelelle année
		$a = new site_client($post);
		if($a->get("client_id") != "")
		{
			$a->majClient();
			echo site_fonction::message("Client",utf8_encode("Modification effectuée"));			
		}
		else 
		{
			$a->ajoutClient();
			echo site_fonction::message("Client",utf8_encode("Insertion effectuée"));	
		}
		return $a;	
	}
	
	public static function traitementFormulaireSite($post)
	{
		#creation de l'objet client de la nouvelelle année
		$a = new site_client($post);
		$rs = site_fonction::recup("client","where client_mail = '".$a->get("client_mail")."'",0,1,"client_id");
		if(mysql_num_rows($rs) > 0)
		{
			$mes = "Vous êtes déja inscrit à notre newsletter";
			
		}
		else
		{
			$a->ajoutClient();
			$mes = "Inscription réussie";
		}
		return $mes;	
	}
	
	#methode pour tester si une adresse mai lest déja enregistrer
	public static function mailExiste($mail)
	{
		$rs = site_fonction::recup("client","where client_mail = '".$mail."'",0,1,"client_id");
		if(mysql_num_rows($rs) > 0)
		{
			$ok = 0;
		}
		else
		{
			$ok = 1;
		}
		return $ok;
	}
}

?>