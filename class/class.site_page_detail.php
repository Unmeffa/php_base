<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_page_detail extends site_fonction
{
    private $page_detail_id;
    private $page_id;
    private $page_detail_langue;
    private $page_detail_nom;
    private $page_detail_titre1;
    private $page_detail_titre2;
    private $page_detail_titre3;
    private $page_detail_titre4;
    private $page_detail_titre5;
    private $page_detail_texte;
    private $page_detail_texte2;
    private $page_detail_texte3;
    private $page_detail_texte4;
    private $page_detail_texte5;
    private $page_detail_titre;
    private $page_detail_desc;
    private $page_detail_tags;
    private $page_detail_h1;
    private $page_detail_metadonnees;
    private $page_detail_url;

    public static function recupPagedetail($page_detail_id)
    {
        #recuperation page
        $query = "select * from ".self::table."_page_detail where page_detail_id='".$page_detail_id."'";
        $rs = @mysql_query($query);
        $row = @mysql_fetch_assoc($rs);

        #creation de l'objet page
        $o = new site_page_detail($row);

        #on retourne l'objet page
        return $o;
    }

    #constructeur
    public function __construct($tab)
    {
        $this->page_detail_id = $tab["page_detail_id"];
        $this->page_id = $tab["page_id"];
        $this->page_detail_langue = $tab["page_detail_langue"];
        $this->page_detail_nom = $tab["page_detail_nom"];
        $this->page_detail_titre1 = $tab["page_detail_titre1"];
        $this->page_detail_titre2 = $tab["page_detail_titre2"];
        $this->page_detail_titre3 = $tab["page_detail_titre3"];
        $this->page_detail_titre4 = $tab["page_detail_titre4"];
        $this->page_detail_titre5 = $tab["page_detail_titre5"];
        $this->page_detail_texte = $tab["page_detail_texte"];
        $this->page_detail_texte2 = $tab["page_detail_texte2"];
        $this->page_detail_texte3 = $tab["page_detail_texte3"];
        $this->page_detail_texte4 = $tab["page_detail_texte4"];
        $this->page_detail_texte5 = $tab["page_detail_texte5"];
        $this->page_detail_titre = $tab["page_detail_titre"];
        $this->page_detail_desc = $tab["page_detail_desc"];
        $this->page_detail_tags = $tab["page_detail_tags"];
        $this->page_detail_h1 = $tab["page_detail_h1"];
        $this->page_detail_metadonnees = $tab["page_detail_metadonnees"];
        $this->page_detail_url = $tab["page_detail_url"];
    }


    #methode de rÃ©cuperation d'un champ
    public function get($attribut)
    {
        return($this->$attribut);
    }

    #methode de modification d'un attribut
    public function set($attribut, $valeur)
    {
        $this->$attribut = $valeur;
    }

    #ajout d'une page
    public function ajoutPagedetail()
    {
        #test pour le remplissage des métas
        $info = site_fonction::recupInformation();
        if($this->page_detail_h1 == "") {
            $this->page_detail_h1 = $info["information_nom"]." : ".site_type_information::recupTypeNom($info['information_type'])." ".$info["information_ville"]." - ".$this->page_detail_nom;
        }
        if($this->page_detail_url == "") {
            $this->page_detail_url = $this->page_detail_nom ;
        }
        if($this->page_detail_titre == "") {
            $this->page_detail_titre = $info["information_nom"]." : ".site_type_information::recupTypeNom($info['information_type'])." ".$info["information_ville"]." - ".$this->page_detail_nom;
        }

        #Requete d'ajout de la page
        $query = "INSERT INTO ".self::table."_page_detail value
				  (
				    '$this->page_detail_id',
					'$this->page_id',
					'$this->page_detail_langue',
					\"".site_fonction::protec($this->page_detail_nom)."\",
					\"".site_fonction::protec($this->page_detail_titre1)."\",
					\"".site_fonction::protec($this->page_detail_titre2)."\",
					\"".site_fonction::protec($this->page_detail_titre3)."\",
					\"".site_fonction::protec($this->page_detail_titre4)."\",
					\"".site_fonction::protec($this->page_detail_titre5)."\",
					\"".site_fonction::protec($this->page_detail_texte)."\",
					\"".site_fonction::protec($this->page_detail_texte2)."\",
					\"".site_fonction::protec($this->page_detail_texte3)."\",
					\"".site_fonction::protec($this->page_detail_texte4)."\",
					\"".site_fonction::protec($this->page_detail_texte5)."\",
					\"".site_fonction::protec($this->page_detail_titre)."\",
					\"".site_fonction::protec($this->page_detail_desc)."\",
					\"".site_fonction::protec($this->page_detail_tags)."\",
					\"".site_fonction::protec($this->page_detail_h1)."\",
					\"".mysql_real_escape_string($this->page_detail_metadonnees)."\",
					\"".site_fonction::clean($this->page_detail_url)."\"
					 )";
        $rs = mysql_query($query) or die("erreur sur la requete <br />$query<br />");
        $this->page_detail_id = mysql_insert_id();

    }

    #Mise a jour page
    public function majPagedetail()
    {
        #test pour le remplissage des métas
        $info = site_fonction::recupInformation();
        if($this->page_detail_h1 == "") {
            $this->page_detail_h1 = $this->page_detail_nom;
        }
        if($this->page_detail_url == "") {
            $this->page_detail_url = $this->page_detail_nom." ".$info["information_nom"]." ".site_type_information::recupTypeNom($info['information_type'])." ".$info["information_ville"];
        }
        if($this->page_detail_titre == "") {
            $this->page_detail_titre = $this->page_detail_nom." | ".$info["information_nom"]." ".site_type_information::recupTypeNom($info['information_type'])." ".$info["information_ville"];
        }

        $query = "UPDATE  ".self::table."_page_detail set
					page_id = '".$this->page_id."',
					page_detail_nom = \"".site_fonction::protec($this->page_detail_nom)."\",
					page_detail_titre1 = \"".site_fonction::protec($this->page_detail_titre1)."\",
					page_detail_titre2 = \"".site_fonction::protec($this->page_detail_titre2)."\",
					page_detail_titre3 = \"".site_fonction::protec($this->page_detail_titre3)."\",
					page_detail_titre4 = \"".site_fonction::protec($this->page_detail_titre4)."\",
					page_detail_titre5 = \"".site_fonction::protec($this->page_detail_titre5)."\",
					page_detail_texte = \"".site_fonction::protec($this->page_detail_texte)."\",
					page_detail_texte2 = \"".site_fonction::protec($this->page_detail_texte2)."\",
					page_detail_texte3 = \"".site_fonction::protec($this->page_detail_texte3)."\",
					page_detail_texte4 = \"".site_fonction::protec($this->page_detail_texte4)."\",
					page_detail_texte5 = \"".site_fonction::protec($this->page_detail_texte5)."\",
					page_detail_titre = \"".site_fonction::protec($this->page_detail_titre)."\",
					page_detail_desc = \"".site_fonction::protec($this->page_detail_desc)."\",
					page_detail_tags = \"".site_fonction::protec($this->page_detail_tags)."\",
					page_detail_h1 = \"".site_fonction::protec($this->page_detail_h1)."\",
					page_detail_metadonnees = \"".mysql_real_escape_string($this->page_detail_metadonnees)."\",
					page_detail_url = \"".site_fonction::clean($this->page_detail_url)."\"
					WHERE page_detail_id = '$this->page_detail_id'
					";

        $rs = mysql_query($query) or die("erreur sur la requete <br />$query<br />");
    }

    #Suppression d'un page
    public function supPagedetail()
    {

        #on supprime l'page
        $query = "delete from ".self::table."_page_detail where page_detail_id=".$this->page_detail_id;
        $rs = mysql_query($query) or die("erreur sur la requete <br />$query<br />");

    }

    public static function traitementFormulaire($post)
    {

        #creation de l'objet page de la nouvelelle année
        $a = new site_page_detail($post);
        if($a->get("page_detail_id") != "") {
            $a->majPagedetail();
            echo site_fonction::message("Page", utf8_encode("Modification effectu&eacute;"));
        } else {
            $a->ajoutPagedetail();
            echo site_fonction::message("Page", utf8_encode("Insertion effectu&eacute;"));
        }

        return $a;

    }

    public function recupURL($langue = "fr", $langurl = 0, $type_page = "")
    {
        if($type_page == "") {
            $page = site_page::recupPage($this->page_id);
            $type_page = $page->get("page_type");
        }
        if($type_page == "blog") {
            $url = CHEMIN_DOSSIER.'/'.URL_BLOG;
        } else {
            if($langurl == 1) {
                $url = CHEMIN_DOSSIER.'/'.$this->page_detail_url."-".$this->page_id."-".$langue;
            } else {
                $url = CHEMIN_DOSSIER.'/'.$this->page_detail_url."-".$this->page_id;
            }
        }
        return $url;
    }

    public function sejourParentUrl()
    {
        return $this->page_detail_url."-".$this->page_id;
    }


}
