<?php
 $rs_total = fonction::recup("avis", "where cat_sejour_id = ".$th->get("id")." and avis_actif = 1 order by avis_date desc", "", "", "avis_id");
 if(mysql_num_rows($rs_total) > 0)
 {
    ?>
    <div class="zone avis avis_sejour">
    <div class="container">
        <div class="text">
            <div class="prettyTitle">Avis Clients</div>
        </div>
        <div class="list">
        <?php
        $pagination = isset($_GET["pagination"]) && $_GET["pagination"] > 0 ? $_GET["pagination"] : 1;
        $limit = 9;
        $index = ($pagination - 1) * $limit;

       
        $num = ceil(mysql_num_rows($rs_total) / $limit);
        $pattern = '/Date : \d{4}-\d{2}-\d{2}/';

        $rs_avis = fonction::recup("avis", "where cat_sejour_id = ".$th->get("id")." and avis_actif = 1 order by avis_date desc", $index, $limit, "avis_id");
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
    <?php
 }
 ?>
