<?php
$rs_photos = fonction::recup("photo","where photo_type = 'cat_sejour' and produit_id = ".$th->get("id"),"2","99","photo_id");
if(mysql_num_rows($rs_photos) > 0)
{
    ?>
    <div class="zone photo-slider">
        <div class="container">
            <div class="text">
                <div class="title">Des photos du séjour</div>
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
            while($data_fetch = mysql_fetch_assoc($rs_photos)) {
                $photo = photo::recupPhoto($data_fetch["photo_id"]);
                $lien = $photo->chemin_miniature(1000);
                ?>
                <div class="swiper-slide">
                    <div class="item-slide">
                        <div class="photo photo-resasoft lazy"
                            data-id="<?=$photo->get("photo_id")?>">
                            <img src="" alt="" />
                            <a data-fancybox="gallery" href="<?=$lien?>"></a>
                        </div>
                    </div>
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

