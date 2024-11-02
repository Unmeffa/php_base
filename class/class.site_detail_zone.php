<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_detail_zone extends site_fonction
{
    private $detail_zone_id;
    private $zone_id;
    private $detail_zone_langue;
    private $detail_zone_titre;
    private $detail_zone_description;
    private $detail_zone_titre2;
    private $detail_zone_description2;
    private $detail_zone_h1;
    private $prefixe = "detail_zone_";

    public static function recupZone($id)
    {

        #recuperation detail_zone
        $query = "select * from " . self::table . "_detail_zone where detail_zone_id=" . $id;
        $rs = mysql_query($query);
        $row = @mysql_fetch_assoc($rs);
        #creation de l'objet detail_zone
        $o = new site_detail_zone($row);
        #on retourne l'objet detail_zone
        return $o;
    }

    #constructeur
    public function __construct($tab = "")
    {
        foreach ($this as $key => $value) {
            if ($key != "prefixe") {
                $this->$key = $tab[$key];
            }
        }
    }

    #methode de récuperation d'un champ
    public function get($attribut)
    {
        return $this->$attribut;
    }

    #methode de modification d'un attribut
    public function set($attribut, $valeur)
    {
        $this->$attribut = $valeur;
    }

    #ajout d'un detail_zone
    public function ajoutZone()
    {


        #ceration de la requete d'insertion
        $query = "INSERT INTO " . self::table . "_detail_zone value(";
        #je parcours les champs de la table
        $i = 0;
        foreach ($this as $key => $value) {
            $i++;
            if ($key != "prefixe") {
                if ($i > 1) {
                    $query .= ",";
                }
                $query .= "\"" . site_fonction::protec(nl2br($value)) . "\"";
            }
        }
        #fermeture de la requete
        $query .= ")";
        #execution de la requete
        $rs = mysql_query($query) or die($query);
    }

    #Mise a jour detail_zone
    public function majZone()
    {
        #ceration de la requete d'insertion
        $query = "UPDATE  " . self::table . "_detail_zone set ";
        #je parcours les champs de la table
        $i = 0;
        foreach ($this as $key => $value) {
            $i++;
            if ($key != "prefixe") {
                if ($i > 1) {
                    $query .= ",";
                }
                $query .= "$key = \"" . site_fonction::protec(nl2br($value)) . "\"";
            }
        }
        #fermeture de la requete
        $query .= " WHERE detail_zone_id = '$this->detail_zone_id'";
        #execution de la requete
        $rs = mysql_query($query) or die($query);
    }

    #Suppression d'un detail_zone
    public function supZone($chemin = "")
    {

        #on supprime le detail_zone
        $query = "delete from " . self::table . "_detail_zone where detail_zone_id=" . $this->detail_zone_id;
        $rs = mysql_query($query) or die(mysql_error());
        #mise a jour des prio
    }

    public static function traitementFormulaire($post)
    {

        #creation de l'objet detail_zone
        $p = new site_detail_zone($post);
        #Mise a jour de la base de donnÃ©es
        #Test pour savoir si c'est un insert ou un update
        if ($p->get("detail_zone_id") == "") {
            $p->ajoutZone();
            $p->set("detail_zone_id", mysql_insert_id());
        } else {
            $p->majZone();
        }
        return $p;
    }
}
