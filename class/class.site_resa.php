<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_resa extends site_fonction
{
    private $resa_id;
    private $client_id;
    private $resa_message;
    private $resa_debut;
    private $resa_fin;
    private $resa_date;
    private $resa_hebergement;
    private $resa_referent;
    private $resa_confirmer;
    private $resa_adulte;
    private $resa_enfant;

    public static function recupResa($resa_id)
    {
        #recuperation resa
        $query = "select * from ".self::table."_resa where resa_id='".$resa_id."'";
        $rs = @mysql_query($query);
        $row = @mysql_fetch_assoc($rs);

        #creation de l'objet resa
        $o = new site_resa($row);

        #on retourne l'objet resa
        return $o;
    }

    #constructeur
    public function __construct($tab)
    {
        $this->resa_id = $tab["resa_id"];
        $this->client_id = $tab["client_id"];
        $this->resa_message = $tab["resa_message"];
        $this->resa_debut = $tab["resa_debut"];
        $this->resa_fin = $tab["resa_fin"];
        $this->resa_date = $tab["resa_date"];
        $this->resa_hebergement = $tab["resa_hebergement"];
        $this->resa_referent = $tab["resa_referent"];
        $this->resa_confirmer = $tab["resa_confirmer"];
        if($this->resa_confirmer == "") {
            $this->resa_confirmer = 0;
        }
        $this->resa_adulte = $tab["resa_adulte"];
        $this->resa_enfant = $tab["resa_enfant"];
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

    #ajout d'une resa
    public function ajoutResa()
    {
        #Requete d'ajout de la resa
        $query = "INSERT INTO ".self::table."_resa value
				  (
					'$this->resa_id',
					'$this->client_id',
					\"".site_fonction::protec(nl2br($this->resa_message))."\",
					'$this->resa_debut',
					'$this->resa_fin',
					'$this->resa_date',
					\"".site_fonction::protec($this->resa_hebergement)."\",
					\"".site_fonction::protec($this->resa_referent)."\",
					'$this->resa_confirmer',
					'$this->resa_adulte',
					'$this->resa_enfant'
				  )";
        $rs = mysql_query($query) or die($query());
        $this->resa_id = mysql_insert_id();
    }

    #Mise a jour resa
    public function majResa()
    {
        $query = "UPDATE  ".self::table."_resa set
					client_id = \"".site_fonction::protec($this->client_id)."\",
					resa_message = \"".site_fonction::protec(nl2br($this->resa_message))."\",
					resa_debut = '$this->resa_debut',
					resa_fin = '$this->resa_fin',
					resa_date = '$this->resa_date',
					resa_hebergement = \"".site_fonction::protec($this->resa_hebergement)."\",
					resa_referent = \"".site_fonction::protec($this->resa_referent)."\",
					resa_confirmer = '$this->resa_confirmer',
					resa_adulte = '$this->resa_adulte',
					resa_enfant = '$this->resa_enfant'
					WHERE resa_id = '$this->resa_id'
					";
        $rs = mysql_query($query) or die($query);
    }

    #Suppression d'un resa
    public function supResa()
    {
        #on supprime la resa
        $query = "delete from ".self::table."_resa where resa_id=".$this->resa_id;
        $rs = mysql_query($query) or die(mysql_error());
    }

    #traitement du formulaire
    public static function traitementFormulaire($post)
    {
        #creation du client
        $c = new site_client($post);
        #test si le client est déja renseigné
        $rs = site_fonction::recup("client", "where client_mail = '".$c->get("client_mail")."'", 0, 1, "client_id");
        if(mysql_num_rows($rs) == 0) {
            #si il n'existe pas on le crée
            $c->ajoutClient();
            #mise a jour du tableau
            $post["client_id"] = $c->get("client_id");
        } else {
            #recuperation du client
            $row = mysql_fetch_row($rs);
            $post["client_id"] = $row[0];
        }

        #creation de l'objet resa
        $a = new site_resa($post);

        #ajout de la resa dans la bdd
        $a->ajoutResa();

        return $a;
    }

    #methode pour conserver le referent
    public static function referent()
    {
        #on recupere l'addresse du site sans le http et les autre fichier
        $site_client = str_replace("http://", "", site_fonction::url_site);
        $site_client = str_replace("www.", "", $site_client);
        $site_client = explode("/", $site_client);
        $site_client = $site_client[0];

        #recuperation du referent
        $referent = str_replace("http://", "", $_SERVER['HTTP_REFERER']);
        $referent = str_replace("www.", "", $referent);
        $referent = explode("/", $referent);
        $referent = $referent[0];

        if($_SESSION['referent_ed'] == "") {
            if($referent == "" or $referent == $site_client) {
                $_SESSION['referent_ed'] = 'Référent Direct';
            } else {
                $_SESSION['referent_ed'] = $referent;
            }

        } else {
            if ($_SESSION['referent_ed'] != $referent && $referent != $site_client && $referent != "") {
                $_SESSION['referent_ed'] = $referent;
            }
        }
    }
}
