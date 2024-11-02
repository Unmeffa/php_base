<div
    class="zone list_photo <?=$zone->get("zone_variation")?>">
    <div class="container">
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
        ?>
        <div class="description">
            <?=$zoneDetail->get("detail_zone_description")?>
        </div>
    </div>
    <div class="medias">
        <?php
    $rs_photos = site_fonction::recup('photo', 'where produit_id = '.$zone->get("zone_id")." and photo_type = 'zone' order by photo_prio");
    while($data_photos = mysql_fetch_assoc($rs_photos)) {
        $photo = new site_photo(
            $data_photos["photo_id"],
            $data_photos["photo_nom"],
            $data_photos["photo_cleannom"],
            $data_photos["photo_prio"],
            $data_photos["photo_type"],
            $data_photos["produit_id"]
        );
        $detail_ph = site_fonction::recupDetail("photo", $photo->get("photo_id"), $lang_get);
        ?>
        <div class="item">
            <div class="photo lazy"
                data-id="<?=$photo->get("photo_id")?>">
                <img src="" alt="" />
            </div>
            <div class="text">
                <div class="title">
                    <?=$detail_ph["titre"]?>
                </div>
                 <div class="description">
                    <?=$detail_ph["description"]?>
                </div>
            </div>
        </div>

        <?php
    }
    ?>
    </div>
    </div>
</div>
