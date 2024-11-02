<?php

#inclusion de la class mere

require_once("class.site_fonction.php");

class site_photo extends site_fonction
{
    private $photo_id;
    private $photo_nom;
    private $photo_cleannom;
    private $photo_prio;
    private $photo_type;
    private $produit_id; #id externe
    private $photo_id_infotour; #id externe

    public static function recupPhoto($id)
    {
        #recuperation photo
        $query = "select * from ".self::table."_photo where photo_id=".$id;
        $rs = mysql_query($query);
        $row = @mysql_fetch_assoc($rs);

        #creation de l'objet photo
        $o = new site_photo($photo_id = $row['photo_id'], $photo_nom= $row['photo_nom'], $photo_cleannom = $row['photo_cleannom'], $photo_prio=$row['photo_prio'], $photo_type = $row["photo_type"], $produit_id= $row['produit_id'], $photo_id_infotour = $row["photo_id_infotour"]);

        #on retourne l'objet photo
        return $o;
    }

    #fonction de recuperation des photos d'un produit
    public static function recupPhotoProduit($id_produit)
    {
        #recuperation photo
        $query = "select * from ".self::table."_photo where produit_id=".$id_produit." order by photo_prio";
        $rs = mysql_query($query);

        #je retourne mes enregistrement
        return $rs;
    }

    #constructeur
    public function __construct($photo_id="", $photo_nom="", $photo_cleannom="", $photo_prio="", $photo_type="", $produit_id="", $photo_id_infotour = "")
    {
        $this->photo_id = $photo_id;
        $this->photo_nom = $photo_nom;
        if ($photo_cleannom == "") {
            $this->photo_cleannom = site_fonction::clean(stripslashes($photo_nom))."-".date("YmdHis");
        } else {
            $this->photo_cleannom = $photo_cleannom;
        }
        $this->photo_prio = $photo_prio;
        $this->photo_type = $photo_type;
        $this->produit_id = $produit_id;
        $this->photo_id_infotour = $photo_id_infotour;
    }

    #methode de récuperation d'un champ
    public function get($attribut)
    {
        return($this->$attribut);
    }

    #methode de modification d'un attribut
    public function set($attribut, $valeur)
    {
        $this->$attribut = $valeur;
    }

    #ajout d'une photo
    public function ajoutPhoto()
    {
        #test pour la prio
        if ($this->photo_prio == "") {
            $rs = site_fonction::recup("photo", "where produit_id = ".$this->produit_id." and photo_type = '$this->photo_type'");
            $nb = mysql_num_rows($rs);
            $this->photo_prio = $nb+1;
        }

        #Requete d'ajout de la photo
        $query = "INSERT INTO ".self::table."_photo value
				  (
					'$this->photo_id',
					\"".site_fonction::protec($this->photo_nom)."\",
					\"$this->photo_cleannom\",
					'$this->photo_prio',
					'$this->photo_type',
					'$this->produit_id',
					'$this->photo_id_infotour'
				  )";

        $rs = mysql_query($query) or die(mysql_error());
    }

    #Suppression d'une photo
    public function supPhoto($chemin="")
    {
        #si le chemin n'est pas specifier
        $chemin = $this->chemin_dossier();

        #je met a jour les prio
        site_prio::majPrio("photo", $this->photo_prio, "and produit_id=".$this->produit_id." and photo_type = '".$this->photo_type."'");

        #je recupere les minis photos
        $query = "select * from ".self::table."_mini_photo where photo_id=".$this->photo_id;
        $rs = mysql_query($query);

        while ($ph = mysql_fetch_row($rs)) {
            #je supprime les mini photo du serveur
            unlink($chemin."/".$ph[1]);

            #Je supprime les mini photo de la base de données
            $query = "delete from ".self::table."_mini_photo where mini_photo_id=".$ph[0];
            $resu = mysql_query($query) or die($query);
        }

        #suppression de la grande photo de la base de données
        $query = "delete from ".self::table."_photo where photo_id=".$this->photo_id;
        $rs = mysql_query($query) or die($query);

        #suppression de la grande photo sur le serveur
        unlink($chemin."/".$this->photo_cleannom);
    }

    public static function traitementFormulaire($post, $file, $chemin)#chemin = chemin en relatif vers le dossier photo
    {
        if (!file_exists(dirname(__dir__).'/photo/')) {
            #creation du dossier chemin_photo/id_produit
            mkdir(dirname(__dir__).'/photo/', 0705);
        }
        #mise a jour du chemin
        $chemin .= $_POST["photo_type"]."/";

        #creation de l'objet photo
        $p = new site_photo("", $_POST['photo_nom'], $photo_cleannom="", "", $_POST["photo_type"], $_POST['produit_id']);

        #récuperation du nom de la photo
        $verif_fichier = $file['image']['name'];
        if ($verif_fichier != "") {
            if ($p->get("photo_nom") != "") {
                if ($extension = strrchr(strtolower($verif_fichier), '.') == ".jpg" || strrchr(strtolower($verif_fichier), '.') == ".JPG" || strrchr(strtolower($verif_fichier), '.') == ".jpeg" || strrchr(strtolower($verif_fichier), '.') == ".JPEG") {
                    #recuperation des données de l'image
                    $tmp = $file['image']['tmp_name'];
                    $name = $file['image']['name'];
                    $taille_fichier = $file['image']['size'];

                    #je recupere l'extension
                    $extension = strrchr(strtolower($name), '.');

                    #Je modifie la valeur de clean nom avec l'extension qui convient
                    $p->set("photo_cleannom", $p->get("photo_cleannom").$extension);

                    #$rename_fichier = cleannom
                    $rename_fichier = $p->get("photo_cleannom");

                    #Si le dossier de photo du produit concerner n'existe pas
                    if (!file_exists($chemin.$p->get("produit_id"))) {
                        #creation du dossier chemin_photo/id_produit
                        mkdir($chemin.$p->get("produit_id"), 0705);
                    }

                    #dossier de telechargement des photos
                    $rep_imgs = $chemin.$p->produit_id."/";

                    #on charge la photo
                    move_uploaded_file($tmp, $rep_imgs.$rename_fichier);

                    //Redimensionnement de la grande photo
                    list($width, $height) = getimagesize($rep_imgs.$rename_fichier);

                    #si la photo depasse les 1000 de large ou les 800 de haut
                    if ($width > 2000 || $height > 1500) {
                        #Test pour savoir si la photo est portrait ou paysage
                        if ($width>$height) {
                            site_fonction::create_mini_paysage($rep_imgs.$rename_fichier, $rep_imgs.$rename_fichier, 2000);
                        } else {
                            site_fonction::create_mini_portrait($rep_imgs.$rename_fichier, $rep_imgs.$rename_fichier, 1500);
                        }
                    }

                    #Ajout de la photo dans la base de données
                    $p->ajoutPhoto();
                /*echo "<script>alert('Insertion effectu�e')</script>";*/
                } else {
                    echo "<script>alert('Votre image n'est pas au format jpg')</script>";
                }
            } else {
                echo "<script>alert('Vous devez mettre un titre � votre photo')</script>";
            }
        } else {
            echo "<script>alert('Vous n'avez selectionn� aucune photo')</script>";
        }
    }

    public function mini_photo($largeur="", $hauteur="", $chemin="", $autre="", $alt = "", $qualite = 75, $regenere = 0)
    {
        #mise a jour de la balise alt
        if ($alt == "") {
            $alt = $this->photo_nom;
        }
        if ($this->photo_id!="") {
            if ($largeur == "" && $hauteur == "") {
                $img = '<img src="'.SITE_CONFIG_URL_SITE.'/img/logo.png" alt="'.$alt.'" '.$autre.' />';
            } else {
                $chemin = $this->chemin_dossier();
                $root = "/".trim(SERVER_ROOT_URL, "/")."/photo/".$this->photo_type."/".$this->produit_id;

                #Pour former le nom de la nouvelle photo
                if ($largeur == "") {
                    $l = "auto";
                } else {
                    $l = $largeur;
                }
                if ($hauteur == "") {
                    $h = "auto";
                } else {
                    $h = $hauteur;
                }
                if ($qualite == "") {
                    $qualite = 75;
                }

                #teste pour la hauteur et largeur de la photo d'origine

                if ($largeur == "" || $hauteur == "") {
                    $taille = getimagesize($root."/".$this->get("photo_cleannom"));

                    if ($largeur > $taille[0]) {
                        $l = $taille[0];
                        $largeur = $taille[0];
                    }
                    if ($hauteur > $taille[1]) {
                        $h = $taille[1];
                        $hauteur = $taille[1];
                    }
                }

                #Creation du nom de la mini photo
                $Tnom = explode(".", $this->photo_cleannom);
                $nom = $Tnom[0]."_".$l."x".$h."x".$qualite.".".$Tnom[1];

                #Chemin d'acces du dossier
                $rep_imgs = $root."/";

                #Test si la photo existe deja
                //if(!file_exists($chemin.$this->produit_id."/".$nom))
                if (@mysql_num_rows(site_fonction::recup("mini_photo", "where mini_photo_nom='$nom'")) == 0 || $regenere == 1) {
                    #je recupre le nom de la photo
                    $photo = $this->photo_cleannom;

                    #on ajoute la mini photo dans la base de données
                    if ($regenere == 0) {
                        $query = "INSERT INTO ".self::table."_mini_photo value
								  (
									'',
									'".site_fonction::protec($nom)."',
									'$this->photo_id'
								  )";
                        $rs = mysql_query($query) or die(mysql_error());
                    }



                    #Test pour savoir si la photo est portrait ou paysage et creation de la mini
                    if ($largeur>$hauteur) {
                        if ($hauteur == "") {
                            site_fonction::create_mini_paysage($rep_imgs.$photo, $rep_imgs.$nom, $largeur, "", $qualite);
                        } else {
                            site_fonction::create_mini_2($rep_imgs.$photo, $rep_imgs.$nom, $largeur, $hauteur, $qualite);
                        }
                    } else {
                        if ($largeur == "") {
                            site_fonction::create_mini_portrait($rep_imgs.$photo, $rep_imgs.$nom, $hauteur, "", $qualite);
                        } else {
                            site_fonction::create_mini_2($rep_imgs.$photo, $rep_imgs.$nom, $largeur, $hauteur, $qualite);
                        }
                    }
                }

                #je retourne la photo
                $img = '<img src="'.$chemin.$nom.'" alt="'.$alt.'" '.$autre.' />';
            }
        } else {
            $img = "<img src='img/produitdefaut.jpg' width='$largeur' height='$hauteur' ".$autre." />";
        }
        return $img;
    }

    #fonction pour generer une miniature et renvoyer le chemin
    public function chemin_miniature($largeur ="", $hauteur ="", $chemin ="", $qualite = 75)
    {

        #Pour former le nom de la nouvelle photo
        if ($largeur == "") {
            $l = "auto";
        } else {
            $l = $largeur;
        }
        if ($hauteur == "") {
            $h = "auto";
        } else {
            $h = $hauteur;
        }
        if ($qualite == "") {
            $qualite = 75;
        }

        #teste pour la hauteur et largeur de la photo d'origine
        if ($largeur == "" || $hauteur == "") {
            $root = "/".trim(SERVER_ROOT_URL, "/")."/photo/".$this->photo_type."/".$this->produit_id;
            $taille = getimagesize($root."/".$this->photo_cleannom);
            if ($largeur > $taille[0]) {
                $l = $taille[0];
                $largeur = $taille[0];
            }
            if ($hauteur > $taille[1]) {
                $h = $taille[1];
                $hauteur = $taille[1];
            }
        }

        #Creation du nom de la mini photo
        $Tnom = explode(".", $this->photo_cleannom);
        $nom = $Tnom[0]."_".$l."x".$h."x".$qualite.".".$Tnom[1];

        #creation de la mini photo si elle n'est pas d�ja cr��e
        $myphoto = $this->mini_photo($largeur, $hauteur, $chemin, "", "", $qualite);

        #chemin vers la miniature
        $chemin = $this->chemin_dossier().$nom;

        #on renvoie le chemin de la photo
        return $chemin;
    }


    #methode pour retrouver le chemin vers le dossier contenant toutes les photos
    public function chemin_dossier()
    {
        $chemin = trim(SITE_CONFIG_URL_SITE, "/")."/photo/";

        #mise a jour du chemin
        $chemin .= $this->photo_type."/".$this->produit_id."/";

        return $chemin;
    }

    #methode pour afficher la grande photo
    public function chemin_photo()
    {
        $chemin = $this->chemin_dossier();
        $src = $chemin . $this->photo_cleannom;
        return $src;
    }

    public function chemin_repertoire()
    {
        $root = trim(SERVER_ROOT_URL, '/');
        if (trim($root) != '' && !preg_match('#^\/#', $root)) {
            $root = '/'.$root;
        }

        $chemin = (preg_match('#\/$#', $root) ? $root : $root.'/')."photo/";
        #mise a jour du chemin
        $chemin .= $this->photo_type."/".$this->produit_id."/";

        return $chemin;
    }

    #methode pour trouver le chemin de fichier depuis la racine de fichier
    public function chemin_fichier_photo()
    {
        $chemin = $this->chemin_repertoire();
        $src = $chemin . $this->photo_cleannom;
        return $src;
    }

    #methode de recuperation des d�tail d'une photo
    public function recupDetailPhoto($langue = "fr")
    {
        $rs = site_fonction::recup("detail_photo", "where photo_id = $this->photo_id and detail_photo_langue = '$langue'", 0, 1, " detail_photo_titre,detail_photo_description ");
        if (@mysql_num_rows($rs) == 0 && $langue != "fr") {
            #si il exite pas ds la langue je recupere le fran�ais
            $rs = site_fonction::recup("detail_photo", "where photo_id = $this->photo_id and detail_photo_langue = 'fr'", 0, 1, " detail_photo_titre,detail_photo_description ");
        }
        if (@mysql_num_rows($rs) > 0) {
            $d = @mysql_fetch_assoc($rs);
        } else {
            $d["detail_photo_titre"] = "";
            $d["detail_photo_description"] = "";
        }
        return $d;
    }
}
