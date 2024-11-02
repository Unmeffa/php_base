<?php
include("include/traitement.php");
/*
<?php include("./include/traitement.php"); ?>
<!DOCTYPE html>
<html lang="<?= $lang_get ?>">

<?php
$zones = [];
$assetsZone = [];
$libs = array("css" => [],"js" => []);
$swiperZones = ["list" => ['slider_sejour'], 'call' => false];
$pickerZones = ["list" => ['form_mesure'], 'call' => false];

$rs_zones = site_fonction::recup("zone", "where page_id = ".$id_get." order by zone_prio");
while($data_zone = mysql_fetch_assoc($rs_zones)) {
    $zones[] = $data_zone;
    if(!in_array($data_zone["zone_type"])) {
        $assetsZone[] = $data_zone["zone_type"];
    }
    if(in_array($data_zone["zone_type"], $swiperZones['list'])) {
        $libs["js"][] = SITE_CONFIG_URL_SITE."/js/".$data_zone["zone_type"].".js";
    }

    if(in_array($data_zone["zone_type"], $pickerZones['list'])) {
        if(!$pickerZones['call']) {
            $pickerZones['call'] = true;
            $libs["css"][] = 'https://unpkg.com/js-datepicker/dist/datepicker.min.css';
            $libs["js"][] = 'https://unpkg.com/js-datepicker';
        }
        $libs["js"][] = SITE_CONFIG_URL_SITE."/js/".$data_zone["zone_type"].".js";
    }
}
?>

<head>
    <?php include(SERVER_ROOT_URL."/include/meta.php"); ?>
    <?php include(SERVER_ROOT_URL."/include/analytics.php"); ?>
    <?php include(SERVER_ROOT_URL."/css/css.php"); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <?php
    if($type_page == "footer") {
        ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <?php
    }
include(SERVER_ROOT_URL."/css/zones.php");

if($id_get == $id_princ || $type_page == 'sejour') {
    echo '<link rel="stylesheet" href="'.SITE_CONFIG_URL_SITE.'/css/filters.css">';
    echo '<link rel="stylesheet" href="'.SITE_CONFIG_URL_SITE.'/css/cartouche.css">';
}
echo '<link rel="stylesheet" href="'.SITE_CONFIG_URL_SITE.'/css/form.css">';
echo '<link rel="stylesheet" href="'.SITE_CONFIG_URL_SITE.'/css/footer.css">';

foreach($libs["css"] as $cssLib) {
    echo '<link rel="stylesheet" href="'.$cssLib.'">';
}

if($type_page == "sejour") {
    echo '<link rel="stylesheet" href="'.SITE_CONFIG_URL_SITE.'/css/garanties.css">';
    echo '<link rel="stylesheet" href="'.SITE_CONFIG_URL_SITE.'/css/box_mesure.css">';
} elseif($type_page == "contact") {
    echo '<link rel="stylesheet" href="'.SITE_CONFIG_URL_SITE.'/css/contact.css">';
}
elseif($type_page == "avis"){
    echo '<link rel="stylesheet" href="'.SITE_CONFIG_URL_SITE.'/css/avis.css">';
}

include(SERVER_ROOT_URL."/css/fontawesome.php");
?>

</head>

<body class="<?=$type_page?>">
<?php

    include(SERVER_ROOT_URL."/include/header.php");
    if($id_get == $id_princ) {
        include(SERVER_ROOT_URL."/zone/hero.php");
        include(SERVER_ROOT_URL."/zone/filters.php");
    } elseif($type_page == "contact") {
        include(SERVER_ROOT_URL."/zone/contact.php");
    } else {
        include(SERVER_ROOT_URL."/zone/hero-page.php");
        if($type_page == "sejour") {
            include(SERVER_ROOT_URL."/zone/filters-page.php");
        }
        else if($type_page == "avis"){
            include(SERVER_ROOT_URL."/zone/avis.php");
        }
    }
    include(SERVER_ROOT_URL."/zone/zones.php");
    include(SERVER_ROOT_URL."/include/footer.php");


?>
    <script>
        const root = "<?=SITE_CONFIG_URL_SITE?>";
    </script>
    <script src="<?=SITE_CONFIG_URL_SITE?>/js/lazyload.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <?php
    if($type_page == "footer") {
        ?>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <?php
    }
?>
    <script src="<?=SITE_CONFIG_URL_SITE?>/js/header.js"></script>
    <?php
if($id_get == $id_princ) {
    echo '<script src="'.SITE_CONFIG_URL_SITE.'/js/filters.js"></script>';
} else {
    if($type_page == "sejour") {
        echo '<script src="'.SITE_CONFIG_URL_SITE.'/js/filters-page.js"></script>';
    }
    if($type_page == "footer") {
        echo '<script src="'.SITE_CONFIG_URL_SITE.'/js/galerie.js"></script>';
    }
} ?>
    <script src="<?=SITE_CONFIG_URL_SITE?>/js/form.js"></script>
    <?php
foreach($libs["js"] as $jsLib) {
    echo '<script src="'.$jsLib.'"></script>';
} ?>
</body>

</html>
*/
