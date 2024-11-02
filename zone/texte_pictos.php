<div class="zone texte_pictos">
    <div class="text">

        <?php
        if(trim($zoneDetail->get("detail_zone_titre")) != '') {
            ?>
        <div class="prettyTitle">
            <?=$zoneDetail->get("detail_zone_titre")?>
        </div>
        <?php
        }
        if(trim($zoneDetail->get("detail_zone_h1")) != '') {
        ?>
        <h2 class="title">
            <?=$zoneDetail->get("detail_zone_h1")?>
        </h2>
        <?php
        }
        if(trim($zoneDetail->get("detail_zone_description")) != "") {
            ?>
        <div class="description">
            <?=$zoneDetail->get("detail_zone_description")?>
        </div>
        <?php
        }
            ?>

        <div class="items description">
            <?php
                $rs_pictos = site_fonction::recup("photo", "where produit_id = ".$zone->get("zone_id")." and photo_type = 'zone' order by photo_prio", 1, 20);
            while($data_picto = mysql_fetch_assoc($rs_pictos)) {
                $photo = new site_photo(
                    $data_picto["photo_id"],
                    $data_picto["photo_nom"],
                    $data_picto["photo_cleannom"],
                    $data_picto["photo_prio"],
                    $data_picto["photo_type"],
                    $data_picto["produit_id"]
                );
                $detail_photo = site_fonction::recupDetail("photo", $data_picto["photo_id"]);
                ?>
            <div class="item-pictos">
                <div class="photo lazy contain"
                    data-id="<?=$photo->get("photo_id")?>">
                    <img src="" alt="" />
                </div>
                <div class="item-content">
                    <div class="item-title">
                        <?=$detail_photo["titre"]?>
                    </div>
                    <div class="item-desc">
                        <?=$detail_photo["description"]?>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
           <?php include(SERVER_ROOT_URL."/include/links.php"); ?>
        </div>
    </div>
    <div class="medias">
        <?php
        $photo = site_fonction::recupPhotoPrincipale('zone', $zone->get("zone_id"));
            if($photo->get("photo_id") > 0) {
                ?>
        <div class="photo lazy"
            data-id="<?=$photo->get("photo_id")?>">
            <img src="" alt="" />
        </div>
        <?php
            }
            ?>

    </div>
</div>
