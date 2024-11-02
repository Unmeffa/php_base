<div class="zone content_blog">
    <div class="container">
        <?php
       $rs_blog = site_fonction::recup("article", "where article_actif = 1 order by article_date desc");
        while($row_blog = mysql_fetch_row($rs_blog)) {
            $article = site_article::recupArticle($row_blog[0]);
            $detail_article = site_fonction::recupDetail("article", $article->get("article_id"), $lang_get);
            $ph = site_fonction::recupPhotoPrincipale("article", $article->get("article_id"));
            $lien = $article->generer_url($lang_get);
            ?>
        <div class="item">
            <?php
                if($ph->get("photo_id") > 0) {
                    ?>

                <div class="photo lazy"
                    data-id="<?=$ph->get("photo_id")?>">
                    <img src="" alt="" />
                    <a href="<?=$lien?>"></a>
                </div>

            <?php
                }
            ?>
            <div class="text">
                <div class="date"><?=$article->get("article_date")?></div>
                <div class="title"><?=$article->get("article_nom")?></div>
                <div class="description"><?=$detail_article["description"]?></div>
                <div class="links">
                    <a href="<?=$lien?>">En savoir plus</a>
                </div>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
</div>
