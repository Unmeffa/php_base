<div class="hero hero-sejour">
    <div class="content">
        <div class="ariane">
            <?php $lastPage = getLastPage(); ?>
            <a href="<?=$lastPage?>" class="back">Retour sur nos
                séjours</a>
        </div>
        <div class="text">
            <div class="c-title">
                <div class="title">
                    <?=$page_detail->get("page_detail_nom")?>
                </div>
                <div class="prettyTitle">
                    <?=$th->get("cat_sejour_nom")?>
                </div>
            </div>

            <?php
           include(SERVER_ROOT_URL."/include/links-sejour.php");
            ?>
        </div>
    </div>
    <div class="media">
        <div class="swiper">
            <div class="swiper-wrapper">
                <?php
        $rs_photo = fonction::recup("photo", "where produit_id = ".$th->get("cat_sejour_id")." and photo_type = 'cat_sejour' order by photo_prio", "0", "1", "photo_id");
            while($fetch = mysql_fetch_assoc($rs_photo)) {
                ?>
                <div class="swiper-slide">
                    <div class="photo lazy photo-resasoft"
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
