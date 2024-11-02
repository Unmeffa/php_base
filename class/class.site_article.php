<?php
#inclusion de la class mere
require_once("class.site_fonction.php");
class site_article extends site_fonction 
{
	private $article_id;
	private $article_id_parent;
	private $client_id;
	private $article_nom;
	private $article_actif;
	private $article_date;
	

	public static function recupArticle($id = "")
	{
		if($id != "")
		{
			#recuperation article
			$query = "select * from ".self::table."_article where article_id=".$id;
			$rs = mysql_query($query);
			$row = @mysql_fetch_assoc($rs);
		}
		
		#creation de l'objet article
		$o = new site_article($row);
		
		#on retourne l'objet article
		return $o;
		
	}
	
	
	#constructeur
	public function __construct($tab = "")
	{
		foreach($this as $key => $value)
		{
			$this->$key = $tab[$key];
		}
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
	
	#ajout d'un article
	public function ajoutArticle()
	{
		
		#ceration de la requete d'insertion
		$query = "INSERT INTO ".self::table."_article value(";
		
		#je parcours les champs de la table
		$i=0;
		foreach($this as $key => $value)
		{
			$i++;
			if($key != "prefixe")
			{
				if($i > 1){ $query .= ",";}
				$query .= "\"".site_fonction::insertString($value)."\"";
			}
		}
		
		#fermeture de la requete			
		$query .= ")";
				
		#execution de la requete
		$rs = mysql_query($query) or die($query);
		
		#mise a jour de l'objet
		$this->article_id = mysql_insert_id();
		
	}
	
	#Mise a jour article
	public function majArticle()
	{
		
		#ceration de la requete d'insertion
		$query = "UPDATE  ".self::table."_article set ";
		
		#je parcours les champs de la table
		$i=0;
		foreach($this as $key => $value)
		{
			$i++;
			if($key != "prefixe")
			{
				if($i > 1){ $query .= ",";}
				$query .= "$key = \"".site_fonction::insertString($value)."\"";
			}
		}
		
		#fermeture de la requete			
		$query .= " WHERE article_id = '$this->article_id'";
		
		#execution de la requete
		$rs = mysql_query($query) or die($query);
	}
	
	public static function traitementFormulaire($post)
	{
		#creation de l'objet article
		$p = new site_article($post);
		
		#Mise a jour de la base de donnÃ©es
		#Test pour savoir si c'est un insert ou un update
		if($p->get("article_id") == "")
		{
			$p->ajoutarticle();
		}
		else
		{
			$p->majarticle();
		}
		return $p;
	}
	
	
	#Suppression d'un article
	public function supArticle()
	{
		#On recupere les photos de l article
		$rs = site_fonction::recup("photo","where produit_id = $this->article_id and photo_type = 'article'","","","photo_id");
		while( $ph = @mysql_fetch_row($rs) )
		{
			#suppresion des photos
			$o = site_photo::recupPhoto($ph[0]);
			$o->supPhoto();
		}
		
		#On recupere les documents de l article
		$rs = site_fonction::recup("document","where produit_id = $this->article_id and document_type = 'article'","","","document_id");
		while( $ph = @mysql_fetch_row($rs) )
		{
			#suppresion des photos
			$o = site_document::recupDocument($ph[0]);
			$o->supDocument();
		}
		
		#on supprime des details de l'article
		$query = "delete from ".self::table."_detail_article where article_id = $this->article_id";
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime les commentaires
		$query = "delete from ".self::table."_article where article_id_parent = $this->article_id";
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime le article
		$query = "delete from ".self::table."_article where article_id = $this->article_id";
		$rs = mysql_query($query) or die(mysql_error());
		
	}
	
	
	#Methode de recuperation du client di commentaire
	public function recupClient()
	{
		if($this->client_id == 0)
		{
			$info = site_fonction::recupInformation();
			$resu = $info["information_nom"];
		}
		else
		{
			$client= site_client::recupClient($this->client_id);
			if($client->get("client_civilite") != "") $resu .= $client->get("client_civilite")." ";
			if($client->get("client_prenom") != "") $resu .= $client->get("client_prenom")." ";
			if($client->get("client_nom") != "") $resu .= $client->get("client_nom")." ";
		}
		return $resu;
	}
	
	public function recupMessage($lang = "fr")
	{
		$detail = site_fonction::recupDetail("article",$this->article_id,$lang,0);
		return $detail["description"];
	}
	
	public function ajoutCommentaire($post)
	{
		#on ajoute l'article
		$article = site_article::traitementFormulaire($post);
		#on ajoute le detail de l'article
		$post["article_id"] = $article->get("article_id");
		if($post["detail_article_langue"] == ""){ $post["detail_article_langue"] = "fr"; }
		$post["detail_article_description"] = strip_tags($post["detail_article_description"]);
		$detail_article = new site_detail_article($post);
		$detail_article->ajoutCommentaire();
	}
	
	public function ajoutCommentaireSite($post)
	{
		#on ajoute le client
		$a = new site_client($post);
		$rs = site_fonction::recup("client","where client_mail = '".$a->get("client_mail")."'",0,1,"client_id");
		if(mysql_num_rows($rs) > 0)
		{
			$row = mysql_fetch_row($rs);
			$client_id = $row[0];
			
		}
		else
		{
			$a->ajoutClient();
			$client_id = $a->get("client_id");
		}
		#on ajoute l'article
		$post["client_id"] = $client_id;
		$article = site_article::traitementFormulaire($post);
		#on ajoute le detail de l'article
		$post["article_id"] = $article->get("article_id");
		if($post["detail_article_langue"] == ""){ $post["detail_article_langue"] = "fr"; }
		$post["detail_article_description"] = strip_tags($post["detail_article_description"]);
		$detail_article = new site_detail_article($post);
		$detail_article->ajoutCommentaire();
	}
	
	#fonction pour genrerer l url detail d un article de blog
	public function generer_url($lang_get= "fr",$article_detail = "")
	{
		if($article_detail == "")
		{
			$article_detail = site_fonction::recupDetail("article",$this->get("article_id"),$lang_get);
		}
		$lien = CHEMIN_DOSSIER."/".URL_BLOG.str_replace("--","-",site_fonction::clean(trim($article_detail["titre"])))."-".$this->article_id;
		return $lien;
		
	}
	
}

?>