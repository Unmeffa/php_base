<?php
$metaData = $page_detail->get("page_detail_metadonnees");
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
$rs_similar = fonction::recup("cat_sejour","where theme_sejour_id = ".$th->get("theme_sejour_id")." and cat_sejour_en_ligne = 0 ".$sejourCondition." and cat_sejour_id != ".$th->get("id"));
if(mysql_num_rows($rs_similar) > 0)
{

    ?>
    <div class="zone photo-slider similar-slide">
        <div class="container">
            <div class="text">
                <div class="title">Nos séjours qui pourraient vous plaire</div>
                <div class="controls">
                    <div class="swiper-button-prev">
                        <i class="fa-solid fa-arrow-left"></i>
                    </div>
                    <div class="swiper-button-next">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </div>
<div class="media">
        <div class="swiper">
            <div class="swiper-wrapper">
                <?php
            while($data_fetch = mysql_fetch_assoc($rs_similar)) {
                 $sejour = new cat_sejour($data_fetch);
                 $lien_similar = SITE_CONFIG_URL_SITE.$page_detail->recupURL()."/".$sejour->recupCatUrl();
                ?>
                <div class="swiper-slide">
                   <?php
                    include(SERVER_ROOT_URL."/include/cartouche.php");
                    ?>
                </div>
                <?php
            }
            ?>
            </div>

        </div>
    </div>
        </div>

    </div>
    <?php
}
?>

