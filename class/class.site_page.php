<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_page extends site_fonction 
{
	private $page_id;
	private $page_nom;
	private $page_parent;
	private $page_actif;
	private $page_prio;
	private $page_type;
	private $page_menu;

	public static function recupPage($page_id)
	{
		#recuperation page
		$query = "select * from ".self::table."_page where page_id='".$page_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet page
		$o = new site_page($row);
			
		#on retourne l'objet page
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{
		$this->page_id = $tab["page_id"];
		$this->page_nom = $tab["page_nom"];
		$this->page_parent = $tab["page_parent"];
		$this->page_actif = $tab["page_actif"];
		if($this->page_actif == ""){ $this->page_actif = 1;}
		$this->page_prio = $tab["page_prio"];
		$this->page_type = $tab["page_type"];
		$this->page_menu = $tab["page_menu"];
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
	
	#ajout d'une page
	public function ajoutPage()
	{
		#test pour la prio
		if($this->page_prio == "")
		{
			$rs = site_fonction::recup("page","where page_parent = $this->page_parent and page_menu = '$this->page_menu'");
			$nb = mysql_num_rows($rs);
			$this->page_prio = $nb+1;
		}
		#Requete d'ajout de la page
		$query = "INSERT INTO ".self::table."_page value
				  (
					'$this->page_id',
					\"".site_fonction::protec($this->page_nom)."\",
					'$this->page_parent',
					'$this->page_actif',
					'$this->page_prio',
					'$this->page_type',
					'$this->page_menu'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		$this->page_id = mysql_insert_id();
		
		#creation de tous les contenus liée à cette page dans toutes les langues activer
		$f = new site_fonction();
		$Tlangue = $f->getLangue();
		foreach($Tlangue as $Tvalue)
		{
			$query = "INSERT INTO ".self::table."_page_detail (`page_detail_id`, `page_id`, `page_detail_langue`, `page_detail_nom`) value
			  (
			    '',
				'$this->page_id',
				'".$Tvalue[0]."',
				\"".site_fonction::protec($this->page_nom)."\"
			  )";
			$rs = mysql_query($query) or die(mysql_error());
		}
		
	}
	
	#Mise a jour page
	public function majPage()
	{
		$query = "UPDATE  ".self::table."_page set
					page_nom = \"".site_fonction::protec($this->page_nom)."\",
					page_parent = '$this->page_parent',
					page_actif = '$this->page_actif',
					page_prio = '$this->page_prio',
					page_type = '$this->page_type',
					page_menu = '$this->page_menu'
					WHERE page_id = '$this->page_id'
					";
		$rs = mysql_query($query) or die($query);
	}
	
	#Suppression d'un page
	public function supPage()
	{

		#On recupere les photo du produit
		$rs = site_fonction::recup("photo","where produit_id = $this->page_id and photo_type = 'page'","","","photo_id");
		while( $ph = @mysql_fetch_row($rs) )
		{
			#suppresion des photos
			$o = site_photo::recupPhoto($ph[0]);
			$chemin = $o->chemin_dossier();
			$o->supPhoto();
		}
		#on supprime le dossier de photo du produit si il existe
		if(file_exists($chemin))
		{
			rmdir($chemin);
		}
		#On recupere les documents du produit
		$rs = site_fonction::recup("document","where produit_id = $this->page_id and document_type = 'page'","","","document_id");
		while( $ph = @mysql_fetch_row($rs) )
		{
			#suppresion des photos
			$o = site_document::recupDocument($ph[0]);
			$chemin = $o->chemin_dossier();
			$o->supDocument();
		}
		#on supprime le dossier de document du produit si il existe
		if(file_exists($chemin))
		{
			rmdir($chemin);
		}
		
		#on supprime la page
		$query = "delete from ".self::table."_page where page_id=".$this->page_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime les details de cette page
		$query = "delete from ".self::table."_page_detail where page_id=".$this->page_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on met a jour les prio
		site_prio::majPrio("page",$this->page_prio,"and page_parent = $this->page_parent and page_menu = '$this->page_menu'");
				
	}
	
	public static function traitementFormulaire($post)
	{
		
		#si la page parent
		if($post["page_parent"] != $post["page_parent_origine"]  && $post["page_parent_origine"] != "")
		{
			#je met a jour les prio de son anciene categorie
			site_prio::majPrio("page",$post["page_prio"]," and page_parent = ".$post["page_parent_origine"]." and page_menu = '".$post["page_menu"]."'");
			
			#je reaffecte la nouvelle prio (derniere position)
			$rs = site_fonction::recup("page",'where page_parent ='.$post["page_parent"]." and page_menu = '".$post["page_menu"]."'","","","page_id");
			$nb = mysql_num_rows($rs);
			$post["page_prio"] = $nb+1;
		}
			
		#creation de l'objet page de la nouvelelle année
		$a = new site_page($post);
		if($a->get("page_id") != "")
		{
			$a->majPage();
			echo site_fonction::message("Page",utf8_encode("Modification effectu&eacute;e"));			
		}
		else 
		{
			$a->ajoutPage();
			echo site_fonction::message("Page",utf8_encode("Insertion effectu&eacute;e"));	
		}
		
		return $a;	
		
	}
	
	#methode de recuperation des detail d'un page dans une langue
	public function recupPage_detail($langue = "fr")
	{
		$rs = site_fonction::recup("page_detail","where page_id = $this->page_id and page_detail_langue = '$langue'",0,1,"page_detail_id");
		$row = mysql_fetch_row($rs);
		$pd = site_page_detail::recupPagedetail($row[0]);
		return $pd;
	}
	
	
	public function parent_id()
	{
		$id_parent = $this->page_id;
		$rs = site_fonction::recup("page","where page_id = ".$this->page_parent,0,1,"page_id,page_parent");
		if(mysql_num_rows($rs) > 0)
		{
			$row = mysql_fetch_row($rs);
			$rs2 = site_fonction::recup("page","where page_id = ".$row[1],0,1,"page_id");
			if(mysql_num_rows($rs2) > 0)
			{
				$row2 = mysql_fetch_row($rs2);
				$id_parent = $row2[0];
			}
			else
			{
				$id_parent = $row[0];
			}
		}
		return $id_parent;
	}
	
	public static function recupURL($page_id,$langue = "fr",$langurl = 1)
	{
		$rs = site_fonction::recup("page_detail","where page_id = ".$page_id." and page_detail_langue = '$langue'",0,1,"page_detail_id");
		$row = mysql_fetch_row($rs);
		$pd = site_page_detail::recupPagedetail($row[0]);
		$url = $pd->recupURL($langue,$langurl);
		return $url;
	}
	
	public function generer_fil_ariane($caractere_de_separation = ">",$lang = "fr",$langurl = 1,$activer_lien = 1,$last_page = 1)
	{

		$ch = "";
		if($this->page_parent > 0)
		{
			$page_parent = site_page::recupPage($this->page_parent);
			$ch .= $page_parent->generer_fil_ariane($caractere_de_separation,$lang,$langurl,XML_PAGE_PRINCIPALE,0);
		}
		$pd = $this->recupPage_detail($lang);
		if($last_page == 1)
		{
			$ch .= $caractere_de_separation."<a href='#' class='active' title='".$pd->get("page_detail_h1")."'>".str_replace("<br />"," ",$pd->get("page_detail_nom"))."</a>";
		}
		else
		{
			if($activer_lien == 1)
			{
				if($langurl == 1)
				{
					$lien = $pd->get("page_detail_url")."-".$pd->get("page_id")."-".$lang.".html";
				}
				else
				{
					$lien = $pd->get("page_detail_url")."-".$pd->get("page_id").".html";
				}
				$ch .= $caractere_de_separation."<a href='".$lien."' title='".$pd->get("page_detail_h1")."'>".str_replace("<br />"," ",$pd->get("page_detail_nom"))."</a>";
			}
			else
			{
				$ch .= $caractere_de_separation."<a href='#' title='".$pd->get("page_detail_h1")."'>".str_replace("<br />"," ",$pd->get("page_detail_nom"))."</a>";
			}
		}
		return $ch;
	}
	
	
	public static function generer_plan_du_site($niveau = 0,$lang = "fr",$langurl = 1,$id_principale)
	{
		$ch = "<ul>";
		$rs = site_fonction::recup("page","where page_parent = $niveau and page_actif = 1 order by page_menu,page_prio","","","page_id");
		while($row = mysql_fetch_row($rs))
		{
			$p = site_page::recupPage($row[0]);
			$pd = $p->recupPage_detail($lang);
			$rs2 = site_fonction::recup("page","where page_parent = $row[0] and page_actif = 1 order by page_prio","","","page_id");
			$nb_ssrubrique = mysql_num_rows($rs2);
			#j'ajoute l'url de la page
			if($langurl == 1)
			{
				if($row[0] == $id_principale && $lang == "fr")
				{
					$lien = "./";
				}
				else
				{
					$lien = $pd->recupURL($lang,$langue,$p->get("type_page"));
				}
			}
			else
			{
				if($row[0] == $id_principale)
				{
					$lien = "./";
				}
				else
				{
					$lien = $pd->recupURL($lang,$langue,$p->get("type_page"));
				}
			}
			if($nb_ssrubrique == 0)
			{
				if($p->get("page_type") == "avis"){
					$ch .= "<li><a href='".$lien."' title='Livre ".$pd->get("page_detail_h1")."'>Livre ".str_replace("<br />"," ",$pd->get("page_detail_nom"))."</a></li>";

				}
				else{
					$ch .= "<li><a href='".$lien."' title='".$pd->get("page_detail_h1")."'>".str_replace("<br />"," ",$pd->get("page_detail_nom"))."</a></li>";
				}
				
			}
			else
			{
				if(XML_PAGE_PRINCIPALE == 0)
				{
					$ch .= "<li>".$pd->get("page_detail_nom")."</li>";
				}
				else
				{
					
						$ch .= "<li><a href='".$lien."' title='".$pd->get("page_detail_h1")."'>".str_replace("<br />"," ",$pd->get("page_detail_nom"))."</a></li>";

					
				}
			}
			#récuperation des categorie d'affaire lier a la page
			if($p->get("page_type") == "blog")
			{
				$ch .= "<ul>";
				$rs_type = site_fonction::recup("article","where article_actif = 1");
				while($row_type = mysql_fetch_assoc($rs_type))
				{
					#je recupere l'article
					$art = new site_article($row_type);
					$detail = site_fonction::recupDetail("article",$art->get("article_id"),$lang);
					$ch .= "<li><a href='".SITE_CONFIG_URL_SITE."/".$art->generer_url()."'>".$detail["titre"]."</li>";
				}
				$ch .= "</ul>";
			}
			
			if($p->get("page_type") == "sejour")
			{
				$metaData = $pd->get("page_detail_metadonnees");

                $sejourCondition = '';
                $count = 0;
                if(strpos($metaData, "#") !== false) {
                    $explode = explode("#", $metaData);
                    foreach($explode as $piece) {
                        $sejourCondition .= $count === 0 ? 'AND (' : ' OR ';
                        $sejourCondition .= 'cat_sejour_id = '.$piece;
                        $count++;
                        $sejourCondition .= $count ===  count($explode) ? ')' : '';
                    }
                } else {
                    $sejourCondition = 'AND cat_sejour_id ='.$metaData;
                }
                
				$ch .= "<ul>";
				$rs_sejours = fonction::recup('cat_sejour', "where cat_sejour_en_ligne = 0 ".$sejourCondition." order by theme_sejour_id ASC");
				while($donnees = mysql_fetch_assoc($rs_sejours))
				{
					$sejour = new cat_sejour($donnees);
					$lien = $sejour->recupURL($pd->sejourParentUrl());	

					$ch .= "<li><a href='".$lien."'>".$sejour->get("nom")."</a></li>";
				}
				
				$ch .= "</ul>";
			}
			
			if($nb_ssrubrique > 0)
			{
				$ch .= site_page::generer_plan_du_site($row[0],$lang,$langurl,$id_principale);
			}
		}
		$ch .= "</ul>";
		return $ch;
	}

}

?>