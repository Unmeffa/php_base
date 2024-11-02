<div class="hero">
    <div class="content">
        <?php include(SERVER_ROOT_URL."/include/socials.php") ?>
        <div class="text">
            <div class="prettyTitle">
                <?=$page_detail->get("page_detail_titre1")?>
            </div>
            <h1 class="title">
                <?=$page_detail->get("page_detail_h1")?>
            </h1>
            <div class="description">
                <?=$page_detail->get("page_detail_texte3")?>
            </div>
            <?php
            include(SERVER_ROOT_URL."/include/links.php");
        ?>
        </div>
    </div>
    <div class="media">
        <div class="swiper">
            <div class="swiper-wrapper">
                <?php
            $rs_photo = site_fonction::recup("photo", "where produit_id = ".$id_get." and photo_type = 'diapo1' order by photo_prio", "", "", "photo_id");
        while($fetch = mysql_fetch_assoc($rs_photo)) {
            ?>
                <div class="swiper-slide">
                    <div class="photo lazy"
                        data-id="<?=$fetch["photo_id"]?>">
                        <img src="" alt="" class="" />
                    </div>
                </div>
                <?php
        }
        ?>
            </div>
        </div>
    </div>
</div>
