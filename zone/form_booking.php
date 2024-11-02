<?php
$rs_pagesejour = site_fonction::recup("page","where page_type = 'sejour' and page_actif = 1");
$listSejours = [];
while($fetch_pageSejour = mysql_fetch_assoc($rs_pagesejour)){
    $pageSejour = new site_page($fetch_pageSejour);
    $pageSejourDetail = $pageSejour->recupPage_detail($lang_get);
    $metaData = $pageSejourDetail->get("page_detail_metadonnees");
    $dataIDs = strpos($metaData,"#") != false ? explode("#",$metaData) : $metaData;
    $listSejours[] = ["name" => $pageSejourDetail->get("page_detail_nom"), "ids" => $dataIDs];
}
?>

<div class="zone form booking_form">
    <div class="container">
        <div class="text">
            <div class="title">Votre séjour</div>
            <form action="<?=SITE_CONFIG_URL_SITE?>/ajax/sejours.php">
                <div class="form-group">
                    <select name="sejour" id="">
                        <option value="">Sélectionner votre séjour</option>
                        <?php
                        foreach($listSejours as $category){
                            if(count($category["ids"]) > 0){
                                ?>
                                <optgroup  label="<?=$category["name"]?>">
                                    <?php
                                    foreach($category["ids"] as $sejourId){
                                         $sejour = cat_sejour::recupCat_sejour($sejourId);
                                         if($sejour->get("id") > 0)
                                         {
                                            $selected = isset($_GET["sejour"]) && $_GET["sejour"] == $sejour->get("id") ? 'selected' : '';
                                            ?>
                                            <option <?=$selected?> value="<?=$sejour->get("id")?>"><?=$sejour->get("cat_sejour_nom")?></option>
                                            <?php
                                         }
                                    }
                                    ?>
                                </optgroup>
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <input type="text" autocomplete="off" placeholder="Date de séjour" name="resa_debut" />
                </div>
                <div class="form-group">
                    <button type="submit" class="button">Rechercher</button>
                </div>
            </form>
        </div>
    </div>
</div>
