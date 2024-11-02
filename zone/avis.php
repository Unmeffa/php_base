<div class="zone avis">
    <div class="container">
    
        <div class="c-review">
        <div class="review">
            <div id="TA_selfserveprop984" class="TA_selfserveprop">
                <ul id="rtANrr7aj" class="TA_links l9xu55zw">
                <li id="zCY0RP" class="rZ3jiv2N">
                    <a target="_blank" href="https://www.tripadvisor.fr/"><img
                        src="https://www.tripadvisor.fr/img/cdsi/img2/branding/150_logo-11900-2.png"
                        alt="TripAdvisor" /></a>
                </li>
                </ul>
            </div>
        </div>
        <div class="write-review">
        <div id="TA_cdswritereviewlg891" class="TA_cdswritereviewlg">
            <ul id="byKTm0WPtS" class="TA_links YUUNlqY3Kg44">
            <li id="NqkF2S" class="TmeewcUMu">
                <a target="_blank" href="https://www.tripadvisor.fr/"><img
                    src="https://www.tripadvisor.fr/img/cdsi/img2/branding/medium-logo-12097-2.png"
                    alt="TripAdvisor" /></a>
            </li>
            </ul>
        </div>
        <script
            src="https://www.jscache.com/wejs?wtype=cdswritereviewlg&amp;uniq=891&amp;locationId=5123384&amp;lang=fr&amp;lang=fr&amp;display_version=2">
        </script>
         <script
            src="https://www.jscache.com/wejs?wtype=selfserveprop&amp;uniq=984&amp;locationId=5123384&amp;lang=fr&amp;rating=true&amp;nreviews=4&amp;writereviewlink=true&amp;popIdx=true&amp;iswide=true&amp;border=true&amp;display_version=2">
        </script>
        </div>
        </div>

        <div class="form-review">
            <div class="text">
                <div class="title">Laissez nous un message</div>
            </div>
            <form action="<?=SITE_CONFIG_URL_SITE?>/ajax/review.php">
                <div class="form-group">
                    <input type="text" required placeholder="Nom *" name="client_nom" />
                </div>
                <div class="form-group">
                    <input type="text" required placeholder="Prénom *" name="client_prenom" />
                </div>
                <div class="form-group">
                    <input type="email" required placeholder="Adresse Mail *" name="client_mail" />
                </div>
                <div class="form-group message">
                    <textarea name="message" required placeholder="Message *"></textarea>
                </div>
                <div class="form-group send">
                    <button type="submit" class="button">Envoyer</button>
                </div>
                <input type="hidden" name="form_type" value="review" />
            </form>
        </div>

        <div class="list">
        <?php
        $pagination = isset($_GET["pagination"]) && $_GET["pagination"] > 0 ? $_GET["pagination"] : 1;
        $limit = 10;
        $index = ($pagination - 1) * $limit;

        $pattern = '/Date : \d{4}-\d{2}-\d{2}/';

        $rs_total = fonction::recup("avis", "where 1 and avis_actif = 1 order by avis_date desc", "", "", "avis_id");
        $num = ceil(mysql_num_rows($rs_total) / 10);

        $rs_avis = fonction::recup("avis", "where 1 and avis_actif = 1 order by avis_date desc", $index, $limit, "avis_id");
        while ($row_avis = mysql_fetch_row($rs_avis)) 
        {
            $avis = avis::recupAvis($row_avis[0]);
            $date = explode(" ", $avis->get("date"));
            $new_date = explode("-", $date[0]);
            $client = client::recupClient($avis->get("client_id")); 
            ?>
            <div class="item text">
                <div class="description"><?=preg_replace($pattern,"",$avis->get("avis_message"))?></div>
                <div class="auteur"><span><?=$client->get("client_nom")." ".$client->get("client_prenom")?></div>
            </div>
        <?php
        }?>
        </div>
    <?php
    if ($num > 1) {
        ?>
    <div class="paginate">
      <?php
        for ($i = 1; $i <= $num; $i++) { ?>
        <a href="?pagination=<?=$i?>"class="pager <?=($i == $pagination) ? 'active' : ''?>"><?=$i?></a>
      <?php } ?>
        </div>
        <?php
    }
    ?>
    </div>
</div>