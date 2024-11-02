<?php

class site_fonction
{
	#Constante de connexion a la bdd
	const table = SITE_CONFIG_TABLE;
	const hote = SITE_CONFIG_HOTE;
	const user = SITE_CONFIG_USER;
	const pass = SITE_CONFIG_PASS;
	const bdd = SITE_CONFIG_BDD;

	#Constante pour les mails, fax et google map
	const mail_client = SITE_CONFIG_MAIL_CLIENT;
	const config_code_analytics = SITE_CONFIG_CODE_ANALYTICS;

	#Constante pour la config du menu
	const menu_produit = SITE_CONFIG_MENU_PRODUIT;
	const menu_affaire = SITE_CONFIG_MENU_AFFAIRE;
	const menu_caracteristique = SITE_CONFIG_MENU_CARACTERISTIQUE;
	const menu_lien = SITE_CONFIG_MENU_LIEN;
	const menu_client = SITE_CONFIG_MENU_CLIENT;
	const menu_newsletter = SITE_CONFIG_MENU_NEWSLETTER;
	const menu_evenement = SITE_CONFIG_MENU_EVENEMENT;
	const menu_tarif = SITE_CONFIG_MENU_TARIF;
	const menu_reservation = SITE_CONFIG_MENU_RESERVATION;
	const menu_stat = SITE_CONFIG_MENU_STAT;
	const menu_livre_d_or = SITE_CONFIG_MENU_LIVRE_D_OR;
	const menu_livre_blog = SITE_CONFIG_MENU_BLOG;

	#Constante pour la config des langues
	const en = SITE_CONFIG_EN;
	const de = SITE_CONFIG_DE;
	const it = SITE_CONFIG_IT;
	const es = SITE_CONFIG_ES;
	const ho = SITE_CONFIG_HO;
	const co = SITE_CONFIG_CO;


	#tableau des langue du site activer
	private $Tlangue;


	public function __construct()
	{
		#On remplie le tableau Tlangue avec toutes les langues actives
		#par default le français
		$Tlangue[] = array("fr", "Fran&ccedil;ais");
		#on teste les autres langues
		if (self::co == 1) {
			$Tlangue[] = array("co", "Corse");
		}
		if (self::en == 1) {
			$Tlangue[] = array("en", "Anglais");
		}
		if (self::de == 1) {
			$Tlangue[] = array("de", "Allemand");
		}
		if (self::es == 1) {
			$Tlangue[] = array("es", "Espagnol");
		}
		if (self::ho == 1) {
			$Tlangue[] = array("ho", "Hollandais");
		}
		if (self::it == 1) {
			$Tlangue[] = array("it", "Italien");
		}

		#On remplie l'attribut de la classe
		$this->Tlangue = $Tlangue;
	}

	public function getLangue()
	{
		return $this->Tlangue;
	}

	public static function se_connecter()
	{
		$connecter = mysql_connect(self::hote, self::user, self::pass) or die('impossible');
		$selection_base = mysql_select_db(self::bdd);
		mysql_query("SET NAMES 'utf8'");
	}

	#fausse methode pour degager tous les utf8_decode
	public function utf8decode($ch)
	{
		return $ch;
	}


	#methode pour recuperer ts les enregistrements d'une table
	public static function recup($table, $where = "", $debut = "", $lim = "", $champs = "*")
	{
		if (is_numeric($debut) && is_numeric($lim)) {
			$limite = " limit " . $debut . "," . $lim;
		} else {
			$limite = "";
		}
		$query = "select " . $champs . " from " . self::table . "_" . $table . " " . $where . " " . $limite;
		$rs = mysql_query($query) or die($query);
		return ($rs);
	}

	public static function clean($chaine)
	{

		$nom_chaine = strtolower(preg_replace("( +[?])", "", $chaine));

		// $nom_chaine = strtr( $nom_chaine, " ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ()[]'#~$&%*@ç!?;,:/^¨€{}|+-.²", "-AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn---------------------E-----2");

		$car_rech = array(" ", "À", "Á", "Â", "Ã", "Ä", "Å", "à", "á", "â", "ã", "ä", "å", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "ò", "ó", "ô", "õ", "ö", "ø", "È", "É", "Ê", "Ë", "è", "é", "ê", "ë", "Ç", "ç", "Ì", "Í", "Î", "Ï", "ì", "í", "î", "ï", "Ù", "Ú", "Û", "Ü", "ù", "ú", "û", "ü", "ÿ", "Ñ", "ñ", "(", ")", "[", "]", "'", "#", "~", "$", "&", "%", "*", "@", "ç", "!", "?", ";", ",", ":", "/", "^", "¨", "€", "{", "}", "|", "+", "-", ".", "²", "’");

		$car_repl = array("-", "A", "A", "A", "A", "A", "A", "a", "a", "a", "a", "a", "a", "O", "O", "O", "O", "O", "O", "o", "o", "o", "o", "o", "o", "E", "E", "E", "E", "e", "e", "e", "e", "C", "c", "I", "I", "I", "I", "i", "i", "i", "i", "U", "U", "U", "U", "u", "u", "u", "u", "y", "N", "n", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "E", "-", "-", "-", "-", "-", "-", "2", "-");

		$nom_chaine = str_replace($car_rech, $car_repl, $nom_chaine);

		$nom_chaine = strtolower(str_replace("[...]", "",  $nom_chaine));

		$nom_chaine = str_replace("…", "", $nom_chaine);
		$nom_chaine = str_replace(":", "-",  $nom_chaine);
		$nom_chaine = strtolower(str_replace("[-]{2,}", "-",  $nom_chaine));
		$nom_chaine = strtolower(str_replace("[\]", "",  $nom_chaine));
		$nom_chaine = str_replace(".", "-",  $nom_chaine);
		$nom_chaine = str_replace("²", "2",  $nom_chaine);
		$nom_chaine = str_replace("°", "",  $nom_chaine);
		return $nom_chaine;
	}
	public static function clean_accent($chaine)
	{
		// $nom_chaine = strtr($chaine, "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ", "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn");

		$car_rech = array("À", "Á", "Â", "Ã", "Ä", "Å", "à", "á", "â", "ã", "ä", "å", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "ò", "ó", "ô", "õ", "ö", "ø", "È", "É", "Ê", "Ë", "è", "é", "ê", "ë", "Ç", "ç", "Ì", "Í", "Î", "Ï", "ì", "í", "î", "ï", "Ù", "Ú", "Û", "Ü", "ù", "ú", "û", "ü", "ÿ", "Ñ", "ñ");

		$car_repl = array("A", "A", "A", "A", "A", "A", "a", "a", "a", "a", "a", "a", "O", "O", "O", "O", "O", "O", "o", "o", "o", "o", "o", "o", "E", "E", "E", "E", "e", "e", "e", "e", "C", "c", "I", "I", "I", "I", "i", "i", "i", "i", "U", "U", "U", "U", "u", "u", "u", "u", "y", "N", "n");

		$nom_chaine = str_replace($car_rech, $car_repl, $chaine);

		return $nom_chaine;
	}

	#decouper la date
	public static function decoupeDate($date)
	{
		$tdate = explode('-', $date);
		return $tdate;
	}


	#Generateur de mot de pass
	public static function generer_pass($nb_car)
	{
		// Ensemble des caractères utilisés pour le créer
		$cars = "az0erty2ui3op4qs5df6gh7jk8lm9wxcvbn";
		// Combien on en a mis au fait ?
		$wlong = strlen($cars);
		// Au départ, il est vide ce mot de passe ;)
		$wpas = "";
		// Combien on veut de caractères pour ce mot de passe ?
		$taille = $nb_car;
		// On initialise la fonction aléatoire
		srand((float)microtime() * 1000000);
		// On boucle sur le nombre de caractères voulus
		for ($i = 0; $i < $taille; $i++) {
			// Tirage aléatoire d'une valeur entre 1 et wlong
			$wpos = rand(0, $wlong - 1);
			// On cumule le caractère dans le mot de passe
			$wpas = $wpas . substr($cars, $wpos, 1);
			// On continue avec le caractère suivant à générer
		}
		return $wpas;
	}



	#pour redimensionnez une photo au format paysage
	public static function create_mini_paysage($chemin_photo, $nom_file, $new_width, $new_height = 0, $qualite = 75)
	{
		//Creation d'une variable contenant le jpg contenu dans le fichier pointé par $chemin_photo
		$tab_chemin = explode("/", $chemin_photo);
		$nom_photo = $tab_chemin[sizeof($tab_chemin) - 1];
		$tab_nom_photo = explode(".", $nom_photo);
		$extension = strtolower($tab_nom_photo[sizeof($tab_nom_photo) - 1]);
		//Creation d'une variable contenant le jpg,png ou gif contenu dans le fichier pointé par $chemin_photo
		if (strtolower($extension) == "png") {
			$image = imagecreatefrompng($chemin_photo);
		} elseif (strtolower($extension) == "gif") {
			$image = imagecreatefromgif($chemin_photo);
		} else {
			$image = imagecreatefromjpeg($chemin_photo);
		}
		//Definition des dimensions de la miniature
		//Récupération des dimensions de l'image que l'on veut redimensionner
		list($width, $height) = getimagesize($chemin_photo);
		//Si la hauteur n'est pas spécifié, on redimensionne proportionnellement
		if ($new_height == 0) {
			$new_height = ($new_width / $width) * $height;
		}
		//Création d'une image vide de la taille de la miniature
		$cadre = imagecreatetruecolor($new_width, $new_height);
		if ($extension == "png" or $extension == "gif") {
			#pour conserver la transparence
			imagesavealpha($cadre, true);
			$trans_color = imagecolorallocatealpha($cadre, 0, 0, 0, 127);
			imagefill($cadre, 0, 0, $trans_color);
		}
		//On copie $image dans le cadre vide (0,0,0,0 sert a définir quelle partie de l'image on veut copier, ici je veux copier l'image en entier donc je commence en haut a gauche)
		imagecopyresampled($cadre, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		//J'enregistre $cadre contenant la miniature dans le dossier pointé par $nom_file
		if (strtolower($extension) == "png") {
			if ($qualite > 9) {
				$qualite = 9;
			}
			imagepng($cadre, $nom_file, $qualite);
		} elseif (strtolower($extension) == "gif") {
			imagegif($cadre, $nom_file);
		} else {
			imagejpeg($cadre, $nom_file, $qualite);
		}
		#liberation des ressources-image
		imagedestroy($image);
		imagedestroy($cadre);
	}

	#pour cropez une photo
	public static function create_mini_2($chemin_photo, $nom_file, $cadreW, $cadreH, $qualite = 75)
	{
		$tab_chemin = explode("/", $chemin_photo);
		$nom_photo = $tab_chemin[sizeof($tab_chemin) - 1];
		$tab_nom_photo = explode(".", $nom_photo);
		$extension = strtolower($tab_nom_photo[sizeof($tab_nom_photo) - 1]);
		#si c'est une png
		if (strtolower($extension) == "png") {
			if ($qualite > 9) {
				$qualite = 9;
			}
			//Creation d'une variable contenant le png contenu dans le fichier pointé par $chemin_photo
			$image = imagecreatefrompng($chemin_photo);
			//Definition des dimensions de la miniature
			//Récupération des dimensions de l'image que l'on veut redimensionner
			list($imgW, $imgH) = getimagesize($chemin_photo);
			if (($imgW / $imgH) >= ($cadreW / $cadreH)) {

				$newimgH = $imgH;
				$newimgW = ($cadreW / $cadreH) * $newimgH;
				$Hor = floor(($imgW - $newimgW) / 2);
				$Vert = 0;
			} else {

				$newimgW = $imgW;
				$newimgH = ($cadreH / $cadreW) * $newimgW;
				$Hor = 0;
				$Vert = floor(($imgH - $newimgH) / 2);
			}
			//Création d'une image vide de la taille de la miniature
			$cadre = imagecreatetruecolor($cadreW, $cadreH);
			#pour conserver la transparence
			imagesavealpha($cadre, true);
			$trans_color = imagecolorallocatealpha($cadre, 0, 0, 0, 127);
			imagefill($cadre, 0, 0, $trans_color);
			//On copie $image dans le cadre vide (0,0,0,0 sert a définir quelle partie de l'image on veut copier, ici je veux copier l'image en entier donc je commence en haut a gauche)
			//imagecopyresampled($cadre, $image, 0, 0,128, 0,   $cadreW, $cadreH, 768, 768);
			imagecopyresampled($cadre, $image, 0, 0, $Hor, $Vert, $cadreW, $cadreH, $newimgW, $newimgH);
			//J'enregistre $cadre contenant la miniature dans le dossier pointé par $nom_file
			imagepng($cadre, $nom_file, $qualite);
		}
		#si c'est une png
		else if (strtolower($extension) == "gif") {
			//Creation d'une variable contenant le png contenu dans le fichier pointé par $chemin_photo
			$image = imagecreatefromgif($chemin_photo);
			//Definition des dimensions de la miniature
			//Récupération des dimensions de l'image que l'on veut redimensionner
			list($imgW, $imgH) = getimagesize($chemin_photo);
			if (($imgW / $imgH) >= ($cadreW / $cadreH)) {

				$newimgH = $imgH;
				$newimgW = ($cadreW / $cadreH) * $newimgH;
				$Hor = floor(($imgW - $newimgW) / 2);
				$Vert = 0;
			} else {

				$newimgW = $imgW;
				$newimgH = ($cadreH / $cadreW) * $newimgW;
				$Hor = 0;
				$Vert = floor(($imgH - $newimgH) / 2);
			}
			//Création d'une image vide de la taille de la miniature
			$cadre = imagecreatetruecolor($cadreW, $cadreH);
			#pour conserver la transparence
			imagesavealpha($cadre, true);
			$trans_color = imagecolorallocatealpha($cadre, 0, 0, 0, 127);
			imagefill($cadre, 0, 0, $trans_color);
			//On copie $image dans le cadre vide (0,0,0,0 sert a définir quelle partie de l'image on veut copier, ici je veux copier l'image en entier donc je commence en haut a gauche)
			//imagecopyresampled($cadre, $image, 0, 0,128, 0,   $cadreW, $cadreH, 768, 768);
			imagecopyresampled($cadre, $image, 0, 0, $Hor, $Vert, $cadreW, $cadreH, $newimgW, $newimgH);
			//J'enregistre $cadre contenant la miniature dans le dossier pointé par $nom_file
			imagegif($cadre, $nom_file);
		}
		#sinon c'est un jpg
		else {
			//Creation d'une variable contenant le jpg contenu dans le fichier pointé par $chemin_photo
			$image = imagecreatefromjpeg($chemin_photo);
			//Definition des dimensions de la miniature
			//Récupération des dimensions de l'image que l'on veut redimensionner
			list($imgW, $imgH) = getimagesize($chemin_photo);
			if (($imgW / $imgH) >= ($cadreW / $cadreH)) {

				$newimgH = $imgH;
				$newimgW = ($cadreW / $cadreH) * $newimgH;
				$Hor = floor(($imgW - $newimgW) / 2);
				$Vert = 0;
			} else {

				$newimgW = $imgW;
				$newimgH = ($cadreH / $cadreW) * $newimgW;
				$Hor = 0;
				$Vert = floor(($imgH - $newimgH) / 2);
			}
			//Création d'une image vide de la taille de la miniature
			$cadre = imagecreatetruecolor($cadreW, $cadreH);
			//On copie $image dans le cadre vide (0,0,0,0 sert a définir quelle partie de l'image on veut copier, ici je veux copier l'image en entier donc je commence en haut a gauche)
			//imagecopyresampled($cadre, $image, 0, 0,128, 0,   $cadreW, $cadreH, 768, 768);
			imagecopyresampled($cadre, $image, 0, 0, $Hor, $Vert, $cadreW, $cadreH, $newimgW, $newimgH);
			//J'enregistre $cadre contenant la miniature dans le dossier pointé par $nom_file
			imagejpeg($cadre, $nom_file, $qualite);
		}
		#liberation des ressources-image
		imagedestroy($image);
		imagedestroy($cadre);
	}




	#Pour redimensionnez une photo au format portrait
	public static function create_mini_portrait($chemin_photo, $nom_file, $new_height, $new_width = 0, $qualite = 75)
	{
		$tab_chemin = explode("/", $chemin_photo);
		$nom_photo = $tab_chemin[sizeof($tab_chemin) - 1];
		$tab_nom_photo = explode(".", $nom_photo);
		$extension = strtolower($tab_nom_photo[sizeof($tab_nom_photo) - 1]);
		//Creation d'une variable contenant le jpg,png ou gif contenu dans le fichier pointé par $chemin_photo
		if (strtolower($extension) == "png") {
			$image = imagecreatefrompng($chemin_photo);
		} elseif (strtolower($extension) == "gif") {
			$image = imagecreatefromgif($chemin_photo);
		} else {
			$image = imagecreatefromjpeg($chemin_photo);
		}
		//Definition des dimensions de la miniature
		//Récupération des dimensions de l'image que l'on veut redimensionner
		list($width, $height) = getimagesize($chemin_photo);
		//Si la hauteur n'est pas spécifié, on redimensionne proportionnellement
		if ($new_width == 0) {
			$new_width = ($new_height / $height) * $width;
		}
		//Création d'une image vide de la taille de la miniature
		$cadre = imagecreatetruecolor($new_width, $new_height);
		if ($extension == "png" or $extension == "gif") {
			#pour conserver la transparence
			imagesavealpha($cadre, true);
			$trans_color = imagecolorallocatealpha($cadre, 0, 0, 0, 127);
			imagefill($cadre, 0, 0, $trans_color);
		}
		//On copie $image dans le cadre vide (0,0,0,0 sert a définir quelle partie de l'image on veut copier, ici je veux copier l'image en entier donc je commence en haut a gauche)
		imagecopyresampled($cadre, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		//J'enregistre $cadre contenant la miniature dans le dossier pointé par $nom_file
		if (strtolower($extension) == "png") {
			if ($qualite > 9) {
				$qualite = 9;
			}
			imagepng($cadre, $nom_file, $qualite);
		} elseif (strtolower($extension) == "gif") {
			imagegif($cadre, $nom_file);
		} else {
			imagejpeg($cadre, $nom_file, $qualite);
		}
		#liberation des ressources-image
		imagedestroy($image);
		imagedestroy($cadre);
	}

	#fonction pour recuperer les informations de l'entreprise (table information de la bdd)
	public static function recupInformation()
	{
		$query = "select * from " . self::table . "_information";
		$rs = mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
		return $row;
	}

	#fonction pour afficher les message
	public static function message($titre, $message, $style = "")
	{
		$grow = '<script type="text/javascript">
					$(document).ready(function(){$.growlUI("' . $titre . '","' . $message . '","' . $style . '");}); </script>';
		return $grow;
	}

	#methode pour arrondir un float
	public static function arrondi($valeur)
	{
		#je recupre la partie decimal du nombre
		$Tnb = explode(".", $valeur);

		#je teste si la partie decimal est egale a 00 et si c'est le cas j'arrondie a l'entier le plus proche
		if ($Tnb[1] == "00") {
			$valeur = round($valeur);
		}

		return ($valeur);
	}

	#fonction pour protgerer une chaine de caractere
	public static function protec($ch)
	{
		$ch = str_replace('"', "'", $ch);
		$ch = str_replace('"', "`", $ch);
		$ch = str_replace("\n", "", $ch);
		$ch = str_replace("/n", "", $ch);
		$ch = str_replace("/r", "", $ch);
		$ch = str_replace("\r", "", $ch);
		$ch = str_replace("%0D", "", $ch);
		$ch = str_replace("%OD", "", $ch);
		if (get_magic_quotes_gpc()) {
			$ch = stripslashes($ch);
		}
		if (!is_numeric($ch)) {
			$ch = mysql_real_escape_string($ch);
		}
		return ($ch);
	}

	#fonction pour tester la connexion de l'admin
	public static function verif_connexion($securise, $login)
	{
		#test pour savoir si la page demande une connexion
		if ($securise == 1) {
			#si login ok
			if ($login == "client" || $login == "edcorses") {
				return true;
			} else {
				return false;
			}
		}
		#si la page n'est pas securise
		else {
			return true;
		}
	}

	#fonction pour retourner a la pagge desirer en javascript
	public static function redirige($chemin = "index.php")
	{
		echo '<script>window.location.replace("' . $chemin . '")</script>';
	}

	#Methode pour les détails d'un produit, d'une newsletter, d'un partenaire ou d'une photo, d'un type d'hebergement ou d'un document dans la langue correspondante
	public static function recupDetail($type, $id, $langue = "fr", $remplace = 1)
	{
		if ($type == "type_produit") {
			$rs = site_fonction::recup("detail_type", "where type_id=$id and detail_type_langue = '$langue' and detail_type_nom = 'produit'", 0, 1, "detail_type_id");
			$row = mysql_fetch_row($rs);
			#je crée l'objet detail_newsletter
			$o = site_detail_type::recupDetail_type($row[0]);

			#on met le titre et si il existe pas on met le nom
			$tab["titre"] = $o->get("detail_type_titre");
			if ($tab["titre"] == "" && $remplace == 1) {
				$rs = site_fonction::recup($type, "where " . $type . "_id = $id", 0, 1, $type . "_nom");
				$row = mysql_fetch_row($rs);
				$tab["titre"] = $row[0];
			}
			#on met la description
			$tab["description"] = $o->get("detail_type_description");
		} else if ($type == "type_partenaire") {
			$rs = site_fonction::recup("detail_type", "where type_id=$id and detail_type_langue = '$langue' and detail_type_nom = 'partenaire'", 0, 1, "detail_type_id");
			$row = mysql_fetch_row($rs);
			#je crée l'objet detail_newsletter
			$o = site_detail_type::recupDetail_type($row[0]);

			#on met le titre et si il existe pas on met le nom
			$tab["titre"] = $o->get("detail_type_titre");
			if ($tab["titre"] == "" && $remplace == 1) {
				$rs = site_fonction::recup($type, "where " . $type . "_id = $id", 0, 1, $type . "_nom");
				$row = mysql_fetch_row($rs);
				$tab["titre"] = $row[0];
			}
			#on met la description
			$tab["description"] = $o->get("detail_" . $type . "_description");
		} else if ($type == "type_affaire") {
			$rs = site_fonction::recup("detail_type", "where type_id=$id and detail_type_langue = '$langue' and detail_type_nom = 'affaire'", 0, 1, "detail_type_id");
			$row = mysql_fetch_row($rs);
			#je crée l'objet detail_newsletter
			$o = site_detail_type::recupDetail_type($row[0]);

			#on met le titre et si il existe pas on met le nom
			$tab["titre"] = $o->get("detail_type_titre");
			if ($tab["titre"] == "" && $remplace == 1) {
				$rs = site_fonction::recup($type, "where " . $type . "_id = $id", 0, 1, $type . "_nom");
				$row = mysql_fetch_row($rs);
				$tab["titre"] = $row[0];
			}
			#on met la description
			$tab["description"] = $o->get("detail_" . $type . "_description");
		} else if ($type == "type_evenement") {
			$rs = site_fonction::recup("detail_type", "where type_id=$id and detail_type_langue = '$langue' and detail_type_nom = 'evenement'", 0, 1, "detail_type_id");
			$row = mysql_fetch_row($rs);
			#je crée l'objet detail_newsletter
			$o = site_detail_type::recupDetail_type($row[0]);

			#on met le titre et si il existe pas on met le nom
			$tab["titre"] = $o->get("detail_type_titre");
			if ($tab["titre"] == "" && $remplace == 1) {
				$rs = site_fonction::recup($type, "where " . $type . "_id = $id", 0, 1, $type . "_nom");
				$row = mysql_fetch_row($rs);
				$tab["titre"] = $row[0];
			}
			#on met la description
			$tab["description"] = $o->get("detail_type_description");
		} else {
			#je recupere l'enregistrement
			$rs = site_fonction::recup("detail_" . $type, "where " . $type . "_id = " . $id . " and detail_" . $type . "_langue = '" . $langue . "'", 0, 1, "detail_" . $type . "_id");
			if (@mysql_num_rows($rs) > 0) {
				#je recupere l'id de l'enregistrement concerner
				$row = mysql_fetch_row($rs);

				if ($type == "produit") {
					#je crée l'objet detail_produit
					$o = site_detail_produit::recupDetail_produit($row[0]);
					$rs2 = site_fonction::recup("detail_produit", "where produit_id = $id and detail_produit_langue = '$langue'", 0, 1);
					$row2 = mysql_fetch_row($rs2);
					$tab["sstitre"] = $row2[5];
					$tab["code"] = $row2[6];
					$tab["meta_titre"] = $row2[7];
					$tab["meta_description"] = $row2[8];
					$tab["meta_keywords"] = $row2[9];
					$tab["meta_h1"] = $row2[10];
				} elseif ($type == "newsletter") {
					#je crée l'objet detail_newsletter
					$o = site_detail_newsletter::recupDetail_newsletter($row[0]);
				} elseif ($type == "partenaire") {
					#je crée l'objet detail_partenaire
					$o = site_detail_partenaire::recupDetail_partenaire($row[0]);
				} elseif ($type == "typehebergement") {
					#je crée l'objet detail_partenaire
					$o = site_detail_typehebergement::recupDetail_typehebergement($row[0]);
				} elseif ($type == "document") {
					#je crée l'objet detail_partenaire
					$o = site_detail_document::recupDetail_document($row[0]);
				} elseif ($type == "tarif") {
					#je crée l'objet detail_tarif
					$o = site_detail_tarif::recupDetail_tarif($row[0]);
				} elseif ($type == "evenement") {
					#je crée l'objet detail_evenement
					$o = site_detail_evenement::recupDetail_evenement($row[0]);
				} elseif ($type == "affaire") {
					#je crée l'objet detail_affaire
					$o = site_detail_affaire::recupDetail_affaire($row[0]);
				} elseif ($type == "caracteristique") {
					#je crée l'objet detail_affaire
					$o = site_detail_caracteristique::recupDetail_caracteristique($row[0]);
				} elseif ($type == "article") {
					#je crée l'objet detail_affaire
					$o = site_detail_article::recupDetail_article($row[0]);
				} else {
					#je crée l'objet detail_produit
					$o = site_detail_photo::recupDetail_photo($row[0]);
					$rs2 = site_fonction::recup("detail_photo", "where photo_id = $id and detail_photo_langue = '$langue'", 0, 1);
					$row2 = mysql_fetch_row($rs2);
					$tab["axe_x"] = $row2[5];
					$tab["axe_y"] = $row2[6];
					$tab["effet"] = $row2[7];
					$tab["color"] = $row2[8];
				}
			} else {
				$o = new site_detail_produit($tab);
			}
			#on met le titre et si il existe pas on met le nom
			$tab["titre"] = $o->get("detail_" . $type . "_titre");
			if ($tab["titre"] == "" && $remplace == 1) {
				$rs = site_fonction::recup($type, "where " . $type . "_id = $id", 0, 1, $type . "_nom");
				$row = mysql_fetch_row($rs);
				$tab["titre"] = $row[0];
			}
			#on met la description
			$tab["description"] = $o->get("detail_" . $type . "_description");
		}
		#on renvoie le tableau

		return $tab;
	}

	#methode de récuperation des documents liées a un produit, un partnaire, newsletter ...
	public static function recupDocument($type, $id, $debut = "", $lim = "")
	{
		$rs = site_fonction::recup("document", "where produit_id = " . $id . " and document_type = '" . $type . "' order by document_prio", $debut, $lim, "document_id");
		return $rs;
	}

	#methode de récuperation des photos liées a un produit, un partnaire, newsletter ou une page
	public static function recupPhoto($type, $id, $debut = "", $lim = "")
	{
		$rs = site_fonction::recup("photo", "where produit_id = " . $id . " and photo_type = '" . $type . "' order by photo_prio", $debut, $lim, "photo_id");
		return $rs;
	}


	#methode pour recuperer la photo principale d'un produit, d'une page, d'une newsletter ou d'un partenaire
	public static function recupPhotoPrincipale($type, $id, $nb = 0)
	{
		$rs = site_fonction::recupPhoto($type, $id, $nb, 1);
		$row = mysql_fetch_row($rs);
		$ph = site_photo::recupPhoto($row[0]);
		return $ph;
	}

	#couper en fonction du nombre de mots
	public static function couper_texte($texte, $mots)
	{
		$tab1 = "";

		$exp = explode(' ', $texte);

		if (is_numeric($mots) && count($exp) > $mots) {
			for ($i = 0; $i < $mots; $i++) {
				$tab1 .= $exp[$i] . ' ';
			}
			$tab1 .= "...";
		} else {
			$tab1 = $texte;
		}

		return $tab1;
	}


	#affiche flas
	public static function AfficheFlash($chemin, $largeur, $hauteur, $transparent, $flashv, $bgcolor)
	{
		$tab = explode("/", $chemin);
		$tailletab = count($tab);
		$nomfichier = $tab[$tailletab - 1];
		$name = str_replace(".swf", "", $nomfichier);
		$movie = str_replace(".swf", "", $chemin);

		if ($transparent == "0") {
			$transparent1 = "'wmode', 'transparent',";
			$transparent2 = "<param name=\"wmode\" value=\"transparent\" />";
			$transparent3 = "wmode=\"transparent\"";
		} else {
			$transparent1 = "";
			$transparent2 = "";
			$transparent3 = "";
		}

		if ($flashv == "1") {
			$flashv1 = "";
			$flashv2 = "";
		} else {
			$flashv1 = ",'flashvars',\"titretest=" . $flashv . "\"";
			$flashv2 = "<param name=\"flashvars\" value=\"titretest=" . $flashv . "\"/>";
		}
?>
		<script language="javascript">
			if (AC_FL_RunContent == 0) {
				alert("Cette page nécessite le fichier AC_RunActiveContent.js.");
			} else {
				AC_FL_RunContent(
					'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0',
					'width', '<?= $largeur ?>',
					'height', '<?= $hauteur ?>',
					'src', '<?= $movie ?>',
					'quality', 'high',
					'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
					'align', 'middle',
					'play', 'true',
					'loop', 'true',
					'scale', 'showall',
					<?= $transparent1 ?> 'devicefont', 'false',
					'id', '<?= $name ?>',
					'bgcolor', '<?= $bgcolor ?>',
					'name', '<?= $name ?>',
					'menu', 'true',
					'allowFullScreen', 'false',
					'allowScriptAccess', 'sameDomain',
					'movie', '<?= $movie ?>',
					'salign', ''
					<?= $flashv1 ?>
				); //end AC code
			}
		</script>
		<noscript>
			<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
				codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0"
				width="<?= $largeur ?>"
				height="<?= $hauteur ?>"
				id="<?= $name ?>" align="middle">
				<param name="allowScriptAccess" value="sameDomain" />
				<param name="allowFullScreen" value="false" />
				<param name="movie" value="<?= $chemin ?>" />
				<param name="quality" value="high" /><?= $transparent2 ?>
				<param name="bgcolor" value="<?= $bgcolor ?>" /><?= $flashv2 ?><embed
					src="<?= $chemin ?>" quality="high" <?= $transparent3 ?> bgcolor="<?= $bgcolor ?>" width="<?= $largeur ?>" height="<?= $hauteur ?>" name="<?= $name ?>" align="middle" allowScriptAccess="sameDomain"
					allowFullScreen="false" type="application/x-shockwave-flash"
					pluginspage="http://www.macromedia.com/go/getflashplayer" />
			</object>
		</noscript>
		<?
	}

	#recup des pages en récursif
	public static function getPages($id_parent = 0, $niveau = 0, $menu = 1)
	{
		global $tTailleDiapo;
		$rs = site_fonction::recup("page", "where page_parent=" . $id_parent . " and page_menu = $menu  order by page_prio asc");
		while ($c = mysql_fetch_assoc($rs)) {

			switch ($niveau) {
				case 0:
					$bgcol = "#212121";
					$fontsize = "14px";
					break;
				case 1:
					$bgcol = "#434040";
					$fontsize = "13px";
					break;
				case 2:
					$bgcol = "#575757";
					$fontsize = "12px";
					break;
			}
		?>
			<div id="a<?= $c["page_id"] ?>">
				<table class="tableone">
					<tr class="ligne lignehover"
						style="background-color:<?= $bgcol ?>">
						<?php
						$colspan = 2;
						if ($niveau > 0) {
							$width_td = 0;
							$img_ret_chariot = '';
							for ($i = 1; $i <= $niveau; $i++) {
								$width_td += 15;
								$img_ret_chariot .= '<img src="img/retour-chariot.png" style="margin-bottom:-10px" />';
							}
						?>

							<td align="center"
								style="padding:0;margin:0;width:inherit;width:<?= ($width_td + 8) ?>px;">
								<?= $img_ret_chariot ?>
							</td>
						<?php
							$colspan = 1;
						}
						?>
						<td align="center" width="1%" colspan="<?= $colspan ?>">
							<p style="margin-bottom:4px"><a
									href="javascript:prio(<?= $c["page_prio"] ?>,<?= $c["page_prio"] - 1 ?>,'page',<?= $c["page_parent"] ?>)"><img
										src="img/haut.png" /></a></p>
							<a
								href="javascript:prio(<?= $c["page_prio"] ?>,<?= $c["page_prio"] + 1 ?>,'page',<?= $c["page_parent"] ?>)"><img
									src="img/bas.png" /></a>
						</td>
						<td width="50" align="center"
							id="active<?php echo $c["page_id"]; ?>">
							<?php
							if ($c["page_actif"] == 1) {
							?>
								<a
									href="javascript:active(<?php echo $c["page_id"] ?>,0)"><img
										alt="Activer" height="22px" title="Etat actuel actif -> Cliquez pour d&eacute;sactiver"
										src="img/check48.png" style="border:0px;" /></a>
							<?php
							} else {
							?>
								<a
									href="javascript:active(<?php echo $c["page_id"] ?>,1)"><img
										alt="Desactiver" height="22px" title="Etat actuel non actif -> Cliquez pour activer"
										src="img/check49.png" style="border:0px;" /></a>
							<?php
							}
							?>
						</td>
						<td align="center" style="font-size:<?= $fontsize ?>">
							<?= $c["page_nom"] ?>
						</td>
						<td width="60px;" align="center"><a
								href="gestion_photo.php?id=<?= $c["page_id"] ?>&type=page"
								title="gerer les photos de la page : <?= $c["page_nom"] ?>"><img
									alt="Photos" height="26px" src="img/nouveau.png" style="border:0px;" /></a></td>
						<?php
						$indice_diapo = 1;
						foreach ($tTailleDiapo as $tDiapo) {
							echo '<td width="60px;" align="center"><a href="gestion_photo.php?id=' . $c["page_id"] . '&type=diapo&nb_type=' . $indice_diapo . '"  title="gerer les diapos ' . $indice_diapo . ' de la page : ' . $c["page_nom"] . '"><img alt="Diapo" src="img/diapo.png" width="26"  style="border:0px;"/></a></td>';
							$indice_diapo++;
						}
						?>
						<td width="60px" align="center"><a
								href="maj-texte-site.php?nump=<?= $c["page_id"] ?>&menu=<?= $c["page_menu"] ?>"
								title="gerer le contenu de la page : <?= $c["page_nom"] ?>"><img
									alt="Photos" src="img/ajouter.png" height="26" style="border:0px;" /></a></td>
						<td width="60px" align="center"><a
								href="gestion_document.php?id=<?= $c["page_id"] ?>&type=page"
								title="gerer les documents liés à la page : <?= $c["page_nom"] ?>"><img
									alt="Photos" src="img/open.png" height="26" style="border:0px;" /></a></td>
						<td width="70px" align="center">
							<a title="modifier la page <?= $c["page_nom"] ?>"
								href="javascript:modPage(<?= $c["page_id"] ?>,'<?= addslashes($c["page_nom"]) ?>',<?= $c["page_parent"] ?>,<?= $c["page_actif"] ?>,<?= $c["page_prio"] ?>,'<?= $c["page_type"] ?>')">
								<img alt="Modifier" src="img/modifier.png" height="26" />
							</a>
						</td>
						<td width="80px" align="center">
							<a title="Supprimer la page <?= $c["page_nom"] ?>"
								href="javascript:confirmation(<?= $c["page_id"] ?>)"><img
									alt="suprimer" src="img/supprimer.png" height="26" /></a>
						</td>
					</tr>
				</table>
			</div>
<?php
			$new_niveau = $niveau + 1;
			site_fonction::getPages($c["page_id"], $new_niveau, $c["page_menu"]);
		}
	}

	public static function getOptionsPage($id = 0, $id_parent = 0, $niveau = 0, $menu = 1)
	{
		$rs_init = site_fonction::recup("page", "where page_id = $id order by page_prio asc", "", "", "page_parent");
		$parent_init = 0;
		if (mysql_num_rows($rs_init) > 0) {
			$row_init = mysql_fetch_row($rs_init);
			$parent_init = $row_init[0];
		}
		$rs = site_fonction::recup("page", "where page_parent = $id_parent and page_menu = $menu order by page_prio asc", "", "", "page_id,page_nom");
		$parent_init == 0 ? $selected = ' selected="selected"' : $selected = '';
		if ($niveau == 0) echo "<option value='0'" . $selected . ">&nbsp; Aucune &nbsp;</option>\n";
		while ($row = mysql_fetch_row($rs)) {
			if ($row[0] != $id) {
				$str_nbsp = '&nbsp;';
				if ($niveau > 0) {
					for ($i = 0; $i <= $niveau; $i++) $str_nbsp .= '&nbsp;';
					$str_nbsp .= ' -';
				}
				$parent_init == $row[0] ? $selected = ' selected="selected"' : $selected = '';
				echo "<option value='$row[0]'" . $selected . ">" . $str_nbsp . " $row[1] &nbsp;</option>\n";
				$new_niveau = $niveau + 1;
				site_fonction::getOptionsPage($id, $row[0], $new_niveau, $menu);
			}
		}
	}

	public static function getOptionsPageContenu($id = 0, $id_parent = 0, $niveau = 0, $menu = 1)
	{
		$rs = site_fonction::recup("page", "where page_parent = $id_parent and page_menu = '" . $menu . "'  order by page_prio asc", "", "", "page_id,page_nom");
		while ($row = mysql_fetch_row($rs)) {
			$str_nbsp = '&nbsp;';
			if ($niveau > 0) {
				for ($i = 0; $i <= $niveau; $i++) $str_nbsp .= '&nbsp;';
				$str_nbsp .= ' -';
			}
			$id == $row[0] ? $selected = ' selected="selected"' : $selected = '';
			echo "<option value='$row[0]'" . $selected . ">" . $str_nbsp . " $row[1] &nbsp;</option>\n";
			$new_niveau = $niveau + 1;
			site_fonction::getOptionsPageContenu($id, $row[0], $new_niveau, $menu);
		}
	}

	public static function deletePages($id)
	{
		$rs = site_fonction::recup("page", "where page_parent = " . $id . " order by page_prio asc", "", "", "page_id,page_nom");
		if (mysql_num_rows($rs) > 0) {
			while ($row = mysql_fetch_row($rs)) {
				site_fonction::deletePages($row[0]);
			}
		}
		$a = site_page::recupPage($id);
		$a->supPage();
	}

	public static function getMenu($type = 'h', $page_parent = 0, $niveau = 0/*, $niveau_deb = ''*/, $niveau_fin = '', $tLimits = array())
	{
		global $lang_get;
		//if(!is_integer($niveau_deb) || is_integer($niveau_deb) && $niveau_deb <= $niveau){
		$niveau == 0 ? $id_ul = ' id="menu_' . $type . '"' : $id_ul = '';
		echo '
			<ul' . $id_ul . '>';

		$limit1 = '';
		$limit2 = '';
		if (sizeof($tLimits > 0) && isset($tLimits[$niveau])) {
			$limit1 = $tLimits[$niveau][0];
			$limit2 = $tLimits[$niveau][1];
		}
		/*
			$inPageParent = '';
			if(is_integer($niveau_deb) && $niveau_deb == $niveau){
				for($cptNiveau = 0; $cptNiveau < $niveau_deb - 1; $cptNiveau++)
					$inPageParent .= 'SELECT page_id FROM '.site_fonction::table.'_page WHERE page_parent IN (';

				$inPageParent .= 'SELECT page_id FROM '.site_fonction::table.'_page WHERE page_parent=0 order by page_prio asc';

				for($cptNiveau = 0; $cptNiveau < $niveau_deb - 1; $cptNiveau++) $inPageParent .= ') order by page_prio asc';
			}
			else */
		$inPageParent = $page_parent;

		$rs = site_fonction::recup("page", "where page_parent IN (" . $inPageParent . ") AND page_actif=1 order by page_prio asc", $limit1, $limit2, "page_id");
		while ($row = mysql_fetch_row($rs)) {
			$p = site_page::recupPage($row[0]);
			$pd = $p->recupPage_detail($lang_get);

			$rs_ss = site_fonction::recup("page", "where 1 AND page_parent=" . $p->get("page_id") . " AND page_actif=1 order by page_prio asc", "", "", "page_id");
			$nb_ss = mysql_num_rows($rs_ss);

			$class_m = '';
			if ($row[0] == $id_get) $class_m .= " class='selected'";

			echo "
				<li>
					<a href='" . $pd->get("page_detail_url") . "-" . $pd->get("page_id") . "-" . $lang_get . ".html' title=\"" . htmlentities($pd->get("page_detail_titre")) . "\"$class_m>
						<span>" . htmlentities($pd->get("page_detail_nom")) . "</span>
					</a>";
			$new_niveau = $niveau + 1;
			if ($nb_ss > 0 && $p->get('page_type') != 'phototheque' && (!is_integer($niveau_fin) || is_integer($niveau_fin) && $niveau_fin >= $new_niveau)) {
				site_fonction::getMenu($type, $row[0], $new_niveau/*, $niveau_deb*/, $niveau_fin, $tLimits);
			}
			echo "
				</li>";
		}
		echo '
			</ul>';
		/*}
		else{
			$new_niveau = $niveau + 1;
			site_fonction::getMenu($type, $page_parent, $new_niveau, $niveau_deb, $niveau_fin, $tLimits);
		}*/
	}

	public static function generer_url_sitemap($active_page_principale = 0, $langue = "")
	{
		$rs = site_fonction::recup("page", "where page_actif = 1 and page_parent = 0 order by page_menu,page_prio", "", "", "page_id");
		while ($row = mysql_fetch_row($rs)) {
			#creation de la ligne pour le xml
			$ligne .=  site_fonction::generer_xml_page($row[0], $active_page_principale, $langue);
		}
		return $ligne;
	}


	#creation de l'url pour une page
	public static function generer_xml_page($page_id, $active_page_principale = 0, $langue)
	{
		#gestion des langue
		if ($langue != "") {
			$where_lang = " and page_detail_langue = '$langue'";
		} else {
			$where_lang = "";
		}
		$page = site_page::recupPage($page_id);
		$pd = $page->recupPage_detail();
		#recuperation de la page_detail dans toutes les langues
		$rs = site_fonction::recup("page_detail", "where page_id = " . $page_id . $where_lang);
		$rs2 = site_fonction::recup("page", "where page_parent = $page_id and page_actif = 1 order by page_prio", "", "", "page_id");
		while ($row = mysql_fetch_assoc($rs)) {
			$page_detail = new site_page_detail($row);
			#recuperation de l'url
			if ($page->get("page_prio") == 1 && $page->get("page_parent") == 0 && $page->get("page_menu") == 1) {
				if ($row["page_detail_langue"] == "fr") {
					$url = "";
				} else {
					$url = $page_detail->recupURL($row["page_detail_langue"], $langue, $page->get("type_page"));
				}
			} else {
				$url = $page_detail->recupURL($row["page_detail_langue"], $langue, $page->get("type_page"));
			}

			#creation de la ligne pour le xml
			if (mysql_num_rows($rs2) == 0 || $active_page_principale == 1) {
				$ligne .= "<url><loc>" . self::url_site . $url . "</loc></url>\n";
			}
		}

		if ($page->get("page_type") == "sejour") {
			$metaData = $pd->get("page_detail_metadonnees");

			$sejourCondition = '';
			$count = 0;
			if (strpos($metaData, "#") !== false) {
				$explode = explode("#", $metaData);
				foreach ($explode as $piece) {
					$sejourCondition .= $count === 0 ? 'AND (' : ' OR ';
					$sejourCondition .= 'cat_sejour_id = ' . $piece;
					$count++;
					$sejourCondition .= $count ===  count($explode) ? ')' : '';
				}
			} else {
				$sejourCondition = 'AND cat_sejour_id =' . $metaData;
			}


			$rs_sejours = fonction::recup('cat_sejour', "where cat_sejour_en_ligne = 0 " . $sejourCondition . " order by theme_sejour_id ASC");
			while ($donnees = mysql_fetch_assoc($rs_sejours)) {
				$sejour = new cat_sejour($donnees);
				$lien = $sejour->recupURL($pd->sejourParentUrl());

				$ligne .= "<url><loc>" . SITE_CONFIG_URL_SITE . "/" . $lien . "</loc></url>";
			}
		}

		#récuperation des categorie d'affaire lier a la page
		if ($page->get("page_type") == "blog") {
			$rs_type = site_fonction::recup("article", "where article_actif = 1");
			while ($row_type = mysql_fetch_assoc($rs_type)) {
				#je recupere l'article
				$art = new site_article($row_type);
				$ligne .= "<url><loc>" . SITE_CONFIG_URL_SITE . "/" . trim($art->generer_url(), "/") . "</loc></url>";
			}
		}



		#recuperation des sous page
		while ($row = mysql_fetch_row($rs2)) {
			#creation de la ligne pour le xml
			$ligne .=  site_fonction::generer_xml_page($row[0], $active_page_principale, $langue);
		}

		return $ligne;
	}

	public static function generer_url_activite($partenaire, $lang = "fr")
	{
		$detail = site_fonction::recupDetail("partenaire", $partenaire->get("partenaire_id"), $lang);

		if ($lang == "it") {
			$url = "dettagli-attivita-regione-" . site_fonction::clean($detail["titre"]);
		} else if ($lang_get == "fr") {
			$url = "details-activite-region-" . site_fonction::clean($detail["titre"]);
		} else {
			$url = "details-activity-area-" . site_fonction::clean($detail["titre"]);
		}

		$url .= "-" . $partenaire->get("partenaire_id") . "-" . $lang . ".html";

		$url = str_replace("--", "-", $url);
		$url = str_replace("--", "-", $url);
		$url = str_replace("--", "-", $url);
		return $url;
	}

	public static function generer_url_produit($produit, $lang = "fr")
	{
		$detail = site_fonction::recupDetail("produit", $produit->get("produit_id"), $lang);

		if ($lang == "it") {
			$url = "dettagli-servizi-" . site_fonction::clean($detail["titre"]);
		} else {
			$url = "details-services-" . site_fonction::clean($detail["titre"]);
		}

		$url .= "-" . $produit->get("produit_id") . "-" . $lang . ".html";

		$url = str_replace("--", "-", $url);
		$url = str_replace("--", "-", $url);
		$url = str_replace("--", "-", $url);
		return $url;
	}

	public static function generer_url_evenement($evenement, $lang = "fr")
	{
		$detail = site_fonction::recupDetail("evenement", $evenement->get("evenement_id"), $lang);

		if ($lang == "it") {
			$url = "dettagli-eventi-" . site_fonction::clean($detail["titre"]);
		} else if ($lang == "fr") {
			$url = "details-evenements-" . site_fonction::clean($detail["titre"]);
		} else {
			$url = "details-events-" . site_fonction::clean($detail["titre"]);
		}

		$url .= "-" . $evenement->get("evenement_id") . "-" . $lang . ".html";

		$url = str_replace("--", "-", $url);
		$url = str_replace("--", "-", $url);
		$url = str_replace("--", "-", $url);
		return $url;
	}



	public static function ecrire_sitemap($chemin = "../sitemap.xml", $active_page_principale = 0)
	{
		if (XML_MULTI_LANGUE == 0) {
			#si le chemin n'est pas remplie
			if ($chemin == "") {
				$chemin = "../sitemap.xml";
			}

			#Creation du fichier xml
			$fp =  fopen($chemin, 'w+') or die("Impossible de créer le fichier sitemap.xml");

			$ligne = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
			$ligne .= "   <urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\" >\n";


			$ligne .= site_fonction::generer_url_sitemap($active_page_principale);

			$ligne .= "   </urlset>";

			#ecriture et fermeture du fichier
			fputs($fp, $ligne) or die("Impossible d'ecrire le texte du fichier sitemap.xml");
			fclose($fp);
		} else {
			$f = new site_fonction();
			$Tlangue = $f->getLangue();
			foreach ($Tlangue as $Tvalue) {
				$chemin = "../sitemap-" . $Tvalue[0] . ".xml";

				#Creation du fichier xml
				$fp =  fopen($chemin, 'w+') or die("Impossible de créer le fichier sitemap-" . $Tvalue[0] . ".xml");

				$ligne = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
				$ligne .= "   <urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\" >\n";


				$ligne .= site_fonction::generer_url_sitemap($active_page_principale, $Tvalue[0]);

				$ligne .= "   </urlset>";

				#ecriture et fermeture du fichier
				fputs($fp, $ligne) or die("Impossible d'ecrire le texte du fichier sitemap-" . $Tvalue[0] . ".xml");
				fclose($fp);
			}
		}
	}

	#protection d'une chaine de caracterere
	public static function insertString($text = '')
	{
		$text = self::protec($text);
		$text = str_replace(array("<br />\n"), "\n", $text);
		// if (get_magic_quotes_gpc())
		// {
		// $text = stripslashes($text);
		// }
		// if (!is_numeric($text))
		// {
		// $text = mysql_real_escape_string($text);
		// }
		str_replace("`", "'", $text);
		return $text;
	}

	public static function getMenuSelect($page_parent = 0, $niveau = 0, $id = 0)
	{
		$lang_get = "fr";;
		$inPageParent = $page_parent;
		$rs = site_fonction::recup("page", "where page_parent IN (" . $inPageParent . ")  order by page_prio asc", "", "", "page_id");
		while ($row = mysql_fetch_row($rs)) {
			$p = site_page::recupPage($row[0]);
			$pd = $p->recupPage_detail($lang_get);

			$rs_ss = site_fonction::recup("page", "where 1 AND page_parent=" . $p->get("page_id") . "  order by page_prio asc", "", "", "page_id");
			$nb_ss = mysql_num_rows($rs_ss);

			$class_m = '';
			if ($row[0] == $id) $class_m = " selected='selected'";

			$espace = "";
			for ($i = 0; $i < $niveau; $i++) {
				$espace .= "&nbsp;&nbsp;";
			}
			echo "
			<option value='" . $p->get("page_id") . "'" . $class_m . ">" . $espace . " " . $p->get("page_nom") . "</option>";
			$new_niveau = $niveau + 1;
			if ($nb_ss > 0) {
				site_fonction::getMenuSelect($row[0], $new_niveau, $id);
			}
		}
	}

	public static function htmlentities_en_iso($ch, $enqote = "ENT_NOQUOTES", $encodage = "iso-8859-1") #je met les parametre au cas ou si j'avais déja changer
	{
		$ch = htmlentities($ch, ENT_NOQUOTES, $encodage);
		return $ch;
	}

	public static function utf8encode($ch)
	{
		return $ch;
	}
}
