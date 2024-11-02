<div
    id="zone<?=$zone->get("zone_id")?>"
    class="zone texte_photo <?=$zone->get("zone_variation")?>">
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

       <?php 
       if($zone->get("zone_id") == 30)
       {
        ?>
        <div class="links">
            <a href="https://www.corsicanatura-activites.fr/randonnee-pedestre-corsica-natura--bocognano-61" title="Randonnées à la journée en Corse" target="_blank">Découvrir les randonnées à la journée</a>
        </div>
        <?php
       }
       else{
        include(SERVER_ROOT_URL."/include/links.php"); 
       }
       
       ?>

    </div>
    <div class="medias">
        <?php
    $rs_photos = site_fonction::recup('photo', 'where produit_id = '.$zone->get("zone_id")." and photo_type = 'zone' order by photo_prio", 0, 2);
    while($data_photos = mysql_fetch_assoc($rs_photos)) {
        $photo = new site_photo(
            $data_photos["photo_id"],
            $data_photos["photo_nom"],
            $data_photos["photo_cleannom"],
            $data_photos["photo_prio"],
            $data_photos["photo_type"],
            $data_photos["produit_id"]
        );
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
