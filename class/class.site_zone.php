<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_zone extends site_fonction
{
    private $zone_id;
    private $zone_nom;
    private $zone_prio;
    private $zone_type;
    private $page_id;
    private $evenement_id;
    private $article_id;
    private $link_page;
    private $zone_variation;

    private $prefixe = "zone_";

    public static function recupZone($id)
    {

        #recuperation zone
        $query = "select * from " . self::table . "_zone where zone_id=" . $id;
        $rs = mysql_query($query);
        $row = @mysql_fetch_assoc($rs);
        #creation de l'objet zone
        $o = new site_zone($row);
        #on retourne l'objet zone
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
        return ($this->$attribut);
    }

    #methode de modification d'un attribut
    public function set($attribut, $valeur)
    {
        $this->$attribut = $valeur;
    }

    #ajout d'un zone
    public function ajoutZone()
    {
        #test pour la prio
        if ($this->caracteristique_prio == "") {
            if ($this->evenement_id > 0) {
                $rs = site_fonction::recup("zone", 'where evenement_id =' . $this->evenement_id);
            } elseif ($this->article_id > 0) {
                $rs = site_fonction::recup("zone", 'where article_id =' . $this->article_id);
            } else {
                $rs = site_fonction::recup("zone", 'where page_id =' . $this->page_id);
            }
            $nb = mysql_num_rows($rs);
            $this->zone_prio = $nb + 1;
        }
        #ceration de la requete d'insertion
        $query = "INSERT INTO " . self::table . "_zone value(";
        #je parcours les champs de la table
        $i = 0;
        foreach ($this as $key => $value) {
            $i++;
            if ($key != "prefixe") {
                if ($i > 1) {
                    $query .= ",";
                }
                $query .= "\"" . site_fonction::protec($value) . "\"";
            }
        }
        #fermeture de la requete
        $query .= ")";
        #execution de la requete
        $rs = mysql_query($query) or die($query);
    }

    #Mise a jour zone
    public function majZone()
    {

        #ceration de la requete d'insertion
        $query = "UPDATE  " . self::table . "_zone set ";
        #je parcours les champs de la table
        $i = 0;
        foreach ($this as $key => $value) {
            $i++;
            if ($key != "prefixe") {
                if ($i > 1) {
                    $query .= ",";
                }
                $query .= "$key = \"" . site_fonction::protec($value) . "\"";
            }
        }
        #fermeture de la requete
        $query .= " WHERE zone_id = '$this->zone_id'";
        #execution de la requete
        $rs = mysql_query($query) or die($query);
    }

    #Suppression d'un zone
    public function supZone($chemin = "")
    {

        #on supprime le zone
        $query = "delete from " . self::table . "_zone where zone_id=" . $this->zone_id;
        $rs = mysql_query($query) or die(mysql_error());
        #mise a jour des prio
        if ($this->evenement_id > 0) {
            site_prio::majPrio("zone", $this->zone_prio, " and evenement_id = " . $this->evenement_id);
        } elseif ($this->article_id > 0) {
            site_prio::majPrio("zone", $this->zone_prio, " and article_id = " . $this->article_id);
        } else {
            site_prio::majPrio("zone", $this->zone_prio, " and page_id = " . $this->page_id);
        }
    }

    public static function traitementFormulaire($post)
    {

        #creation de l'objet zone
        $p = new site_zone($post);
        #Mise a jour de la base de donnÃ©es
        #Test pour savoir si c'est un insert ou un update
        if ($p->get("zone_id") == "") {
            $p->ajoutZone();
            $p->set("zone_id", mysql_insert_id());
        } else {
            $p->majZone();
        }
        return $p;
    }

    #methode de recuperation des detail d'un page dans une langue
    public function recupZone_detail($langue = "fr")
    {
        $rs = site_fonction::recup("detail_zone", "where zone_id = ".$this->zone_id." and detail_zone_langue = '".$langue."'", 0, 1, "detail_zone_id");
        $row = mysql_fetch_row($rs);
        $pd = site_detail_zone::recupZone($row[0]);
        return $pd;
    }

    public function generer_ligne($tr = 1)
    {
        $ch = "";
        if ($tr == 1) {
            $ch .= '<tr class="ligne" id="a' . $this->zone_id . '">';
        }

        if ($this->link_page == "" || $this->link_page == null) {
            $this->link_page = 0;
        }

        $ch .= '<td align="center">' . $this->zone_nom . '</td>
			<td width="80px;" align="center"><a href="gestion_photo.php?id=' . $this->zone_id . '&type=zone&evenement_id='.$this->evenement_id.'&article_id='.$this->article_id.'"  title="gerer les photos ' . $this->zone_nom . '"><img alt="Photos" src="img/nouveau.png" width="33"  style="border:0px;"/></a></td>
			<td width="80px" align="center"><a  href="gestion_detail_zone.php?zone_id='.$this->zone_id.'&evenement_id='.$this->evenement_id.'&article_id='.$this->article_id.'"  title="gerer les détails de la zone ' . $this->zone_nom . '"><img alt="Photos" src="img/ajouter.png" width="33"  style="border:0px;"/></a></td>';

        if ($this->evenement_id > 0) {
            $ch .= '
				<td width="90px" align="center">
					<a title="modifier la zone ' . addslashes($this->zone_nom) . '" href="javascript:modZone('. $this->zone_id . ',\''.addslashes($this->zone_nom).'\',' . $this->zone_prio . ',\'' . addslashes($this->zone_type) . '\',' . $this->evenement_id . ','.$this->link_page.',\''.addslashes($this->zone_variation).'\')">
						<img alt="Modifier" src="img/modifier.png" />
					</a>
				</td>';
        } elseif ($this->article_id > 0) {
            $ch .= '
                <td width="90px" align="center">
                    <a title="modifier la zone ' . addslashes($this->zone_nom) . '" href="javascript:modZone('. $this->zone_id . ',\''.addslashes($this->zone_nom).'\',' . $this->zone_prio . ',\'' . addslashes($this->zone_type) . '\',' . $this->article_id . ','.$this->link_page.',\''.addslashes($this->zone_variation).'\')">
                        <img alt="Modifier" src="img/modifier.png" />
                    </a>
                </td>';
        } else {
            $ch .= '
				<td width="90px" align="center">
					<a title="modifier la zone ' . addslashes($this->zone_nom) . '" href="javascript:modZone('. $this->zone_id . ',\''.addslashes($this->zone_nom).'\',' . $this->zone_prio . ',\'' . addslashes($this->zone_type) . '\',' . $this->page_id . ','.$this->link_page.',\''.addslashes($this->zone_variation).'\')">
						<img alt="Modifier" src="img/modifier.png" />
					</a>
				</td>';
        }

        $ch .=	'<td width="90px" align="center">
				<a title="Supprimer la zone ' . $this->zone_nom . '" href="javascript:confirmation(' . $this->zone_id . ')"><img alt="suprimer" src="img/supprimer.png" width="33" /></a>
			</td>';
        if ($tr == 1) {
            $ch .= '</tr>';
        }
        if ($tr == 1) {
        }
        return $ch;
    }
}
