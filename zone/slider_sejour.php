<div class="zone slider_sejour">
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
        <div class="swiper">
            <div class="swiper-wrapper">
                <?php
                $rs_themes = site_fonction::recup("page", "where page_type = 'sejour' and page_actif = 1 order by page_prio");
            while($data_fetch = mysql_fetch_assoc($rs_themes)) {
                $page = new site_page($data_fetch);
                $pageDetail = $page->recupPage_detail($lang_get);
                $photo = site_fonction::recupPhotoPrincipale("diapo1", $page->get("page_id"));
                $lien = $pageDetail->recupURL();
                ?>
                <div class="swiper-slide">
                    <div class="item-slide">
                        <div class="photo lazy"
                            data-id="<?=$photo->get("photo_id")?>">
                            <img src="" alt="" />
                            <a href="<?=$lien?>"></a>
                        </div>
                        <div class="content text">
                            <div class="prettyTitle">
                                <?=$pageDetail->get("page_detail_nom")?>
                            </div>
                            <a href="<?=$lien?>" class="button">En
                                savoir plus</a>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</div>
