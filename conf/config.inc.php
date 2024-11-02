<?php
ini_set('display_errors', 'off');
ini_set('max_execution_time', 3600);
ini_set('max_input_time', 3600);
ini_set('memory_limit', '1024M');
ini_set('upload_max_filesize', '1024M');

/** * Configuration */

define('SITE_CONFIG_TABLE', "");
define('SITE_CONFIG_HOTE', "localhost:3306");
define('SITE_CONFIG_USER', "root");
define('SITE_CONFIG_PASS', "");
define('SITE_CONFIG_BDD', "");
define('SITE_CONFIG_MAIL_CLIENT', "");

/* ---------------------------------------------------------
//
 ----------------------------------------------------------------------------
//
 URL des réseaux sociaux
//
 ----------------------------------------------------------------------------
*/
define('URL_FACEBOOK', "");
define('URL_TWITTER', "");

/* ----------------------------------------------------------------------------
//
 Activation des menus
//
 ----------------------------------------------------------------------------
*/

define('SITE_CONFIG_MENU_PRODUIT', "1");
define('SITE_CONFIG_MENU_BLOG', "1");
define('SITE_CONFIG_MENU_LIEN', "1");
define('SITE_CONFIG_MENU_AFFAIRE', "0");
define('SITE_CONFIG_MENU_CARACTERISTIQUE', "0");
define('SITE_CONFIG_MENU_CLIENT', "0");
define('SITE_CONFIG_MENU_NEWSLETTER', "0");
define('SITE_CONFIG_MENU_TARIF', "1");
define('SITE_CONFIG_MENU_RESERVATION', "0");
define('SITE_CONFIG_MENU_STAT', "0");
define('SITE_CONFIG_MENU_EVENEMENT', "0");
define('SITE_CONFIG_MENU_LIVRE_D_OR', "0");

/* ----------------------------------------------------------------------------
//
 Activation des langues
//
 ---------------------------------------------------------------------------- */

define('SITE_CONFIG_CO', "0");
define('SITE_CONFIG_DE', "0");
define('SITE_CONFIG_EN', "1");
define('SITE_CONFIG_ES', "0");
define('SITE_CONFIG_HO', "0");
define('SITE_CONFIG_IT', "0");

/* ----------------------------------------------------------------------------
//
 Activation des zones de texte
//
 ---------------------------------------------------------------------------- */

define('ZONE1', "1");
define('TITRE_ZONE1', "1");
define('ZONE1_EDITABLE', "1");
define('LABEL_ZONE1', "Zone de texte");
define('ZONE2', "1");
define('TITRE_ZONE2', "0");
define('ZONE2_EDITABLE', "0");
define('LABEL_ZONE2', "Zone de texte 2");
define('ZONE3', "1");
define('TITRE_ZONE3', "1");
define('ZONE3_EDITABLE', "1");
define('LABEL_ZONE3', "Chapeau");
define('ZONE4', "0");
define('TITRE_ZONE4', "0");
define('ZONE4_EDITABLE', "0");
define('LABEL_ZONE4', "Acc&egrave;s (code google map)");
define('ZONE5', "0");
define('TITRE_ZONE5', "0");
define('ZONE5_EDITABLE', "0");
define('LABEL_ZONE5', "informations suppl&eacute;mentaires 2");
define('ZONE_META', "1");
define('ZONE_META_PRODUIT', "0");
/* si on inclus les pages parents dans le xml */
define('XML_PAGE_PRINCIPALE', 1);
/* si on genere un fichier XML par langue */
define('XML_MULTI_LANGUE', 0);
/* ----------------------------------------------------------------------------//
 Clef google map + Url site//
 ---------------------------------------------------------------------------- */
define('SITE_CONFIG_CODE_ANALYTICS', '');
$dirname = dirname(__DIR__);
define('SERVER_ROOT_URL', $dirname);
define('BASE_URL', 'http://localhost:8080/');
global $tPageType;
$tPageType = array(
    'page' => 'Page',
    'contact' => 'Contact',
);

global $captchaCustom;
$captchaCustom = array(0 => array("2 + 2 = ", 4),    1 => array("3 x 6 = ", 18),    2 => array("10 - 4 = ", 6));
global $tMenu;
$tMenu = array('1' => 'Menu Principal',);
global $tZoneType;
$tZoneType = array(
    "diapo" => "Diaporama",
    "texte" => "Texte"
);
global $tZoneVariation;
$tZoneVariation = array("normal" => "Normal", "invert" => "Inversé");
