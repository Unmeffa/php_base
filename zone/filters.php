<?php
$rs_coupdecoeur = fonction::recup('coupdecoeur', "where 1");
if(mysql_num_rows($rs_coupdecoeur) > 0) {

    $sejourCondition = '';
    $count = 0;
    while($data_favorites = mysql_fetch_assoc($rs_coupdecoeur)) {
        $sejourCondition .= $count === 0 ? 'AND (' : ' OR ';
        $sejourCondition .= 'cat_sejour_id = '.$data_favorites['cat_sejour_id'];
        $count++;
        $sejourCondition .= $count ===  mysql_num_rows($rs_coupdecoeur) ? ')' : '';
    }?>
<div class="zone filters">
    <div class="container">
        <div class="text">
            <div class="prettyTitle">Nos séjours</div>
            <h2 class="title"><?=$page_detail->get("page_detail_titre3")?></h2>
        </div>
        <div class="list">
            <div class="items">
                <?php

    $initPageDetail = $page_detail;

    $rs_sejours = fonction::recup('cat_sejour', "where cat_sejour_en_ligne = 0 ".$sejourCondition." order by theme_sejour_id ASC");
    $rs_pagesejour = site_fonction::recup("page","where page_type = 'sejour' and page_actif = 1");
    $listSejours = [];
    while($fetch_pageSejour = mysql_fetch_assoc($rs_pagesejour)){
        $pageSejour = new site_page($fetch_pageSejour);
        $pageSejourDetail = $pageSejour->recupPage_detail($lang_get);
        $metaData = $pageSejourDetail->get("page_detail_metadonnees");
        $dataIDs = strpos($metaData,"#") != false ? explode("#",$metaData) : $metaData;
        $listSejours[] = ["page_detail" => $pageSejourDetail, "ids" => $dataIDs];
    }

    $categories = [];
    while($donnees=mysql_fetch_assoc($rs_sejours)) {

        $sejour = new cat_sejour($donnees);
        foreach($listSejours as $s){
            if(in_array($sejour->get("id"),$s["ids"])){
                $page_detail = $s["page_detail"];
                break;
            }
        }
        if(!isset($categories[$donnees['theme_sejour_id']])) {
            $category = $sejour->getMainCategory();
            if(isset($category) && $category['name'] != '') {
                $categories[$donnees['theme_sejour_id']] = array('name' => $category['category'], 'filter' => 'cat-'.$donnees['theme_sejour_id']);
            }
        }
        include(SERVER_ROOT_URL."/include/cartouche.php");
    }
    $page_detail = $initPageDetail;
    ?>
            </div>
            <?php
            if(count($categories) > 1) {
                ?>
            <div class="tabs">
                <a href="#" class="active" data-filter="*">Tout</a>
                <?php
                foreach($categories as $category) {
                    ?>
                <a href="#"
                    data-filter="<?=$category['filter']?>"><?=$category['name']?></a>
                <?php
                }
                ?>
            </div>
            <?php
            }
    ?>
        </div>

    </div>
</div>
<?php
}
?>
