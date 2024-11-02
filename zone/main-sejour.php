<div class="main-sejour">
    <div class="container">
        <?php
        $props = [];
        $props[] = array("label" => 'En Groupe', "value" => "De ".$th->get("cat_sejour_nb_personne_min")." à ".$th->get("cat_sejour_nb_personne")." pers");
        $props[] = array("label" => 'Niveau', "value" => $th->get("cat_sejour_difficulte"));
        $props[] = array("label" => 'Durée', "value" => $th->get("cat_sejour_nb_jour"). " jours");
        $props[] = array("label" => 'Période', "value" => $th->get("cat_sejour_periode"));
        ?>
        <div class="props">
            <?php
            foreach ($props as $p) {
                ?>
            <div class="prop">
                <span><?=$p["label"]?></span>
                <?=$p["value"]?>
            </div>
            <?php
            }
        ?>
            <div class="prop price">
                <span>Prix à partir de</span>
                <?=$th->get("cat_sejour_prix")?>
                €
                / pers
            </div>
        </div>
    </div>
</div>

<div class="zone sejour-details">
    <div class="container">
        <div class="main-details">
            <div class="tabs">
                <div class="tab active">Présentation</div>
                <div class="tab">Itinéraire / Jours</div>
                <div class="tab">Budget</div>
                <div class="tab">Documents</div>
            </div>
            <div class="content-details">
                <div class="tab-content active">
                    <div class="tab-title">Présentation</div>
                    <div class="text">
                        <div class="title">
                            <?=$th->get("cat_sejour_nom")?>
                        </div>
                        <div class="description">
                            <?=$page_detail->get("page_detail_desc")?>
                            <?=$page_detail->get("page_detail_texte")?>
                        </div>
                        <?php
                        include(SERVER_ROOT_URL."/include/links-sejour.php");
                        $secondPhoto = fonction::recupPhotoPrincipale('cat_sejour',$th->get("cat_sejour_id"),1);
                        if($secondPhoto->get("photo_id") > 0)
                        {
                            ?>
                            <div class="media">
                                <div class="photo lazy photo-resasoft" data-id="<?=$secondPhoto->get("photo_id")?>">
                                    <img src="" alt="" />
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-title">Jours / Itinéraire</div>
                    <div class="text">
                        <div class="title">
                            <?=$th->get("cat_sejour_nb_jour"). " jours d'itinéraire"?>
                        </div>
                        <div class="c-days">
                            <div class="nDays">
                            <?php
                            $rs_days = fonction::recup("jour_cat_sejour", "where cat_sejour_id = ".$th->get("id")." order by jour_cat_sejour_prio", "", "");
                            $nDays = mysql_num_rows($rs_days);
                            for($i = 1; $i <= $nDays; $i++){
                                ?>
                                <a href="#">Jour <?=$i?></a>
                                <?php
                            }
                            ?>
                            </div>
                             <div class="days">
                            <?php
        while($data_days = mysql_fetch_assoc($rs_days)) {
            $day = new jour_cat_sejour($data_days);
            $dday = fonction::recupDetail("jour_cat_sejour", $day->get("jour_cat_sejour_id"), $lang_get);
            include(SERVER_ROOT_URL."/zone/day.php");
        }
        ?>
                        </div>
                        </div>

                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-title">Budget</div>
                    <div class="text">
                        <div class="title">
                            Budget
                        </div>
                        <div class="description">
                             <?=$th->get("cat_sejour_tarif")?>
                        </div>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-title">Documents</div>
                    <div class="text">
                        <div class="title">
                            Télécharger les documents
                        </div>
                        <div class="documents">
                            <?php
                            $rs = fonction::recup("document", "where document_type = 'cat_sejour' and produit_id = ".$th->get("id")." order by document_prio", "", "", "document_id");
                            while ($row = mysql_fetch_row($rs)) {
                                $d = document::recupDocument($row[0]);
                                $detail_doc = fonction::recupDetail("document",$d->get("document_id"),$lang_get);
                                ?>
                                <a class="doc button" target="_blank" href="<?=$d->chemin_document()?>">
                                    <i class="fa-solid fa-file-pdf"></i>
                                    <?=$detail_doc["titre"]?>
                                </a>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sidebar">
            <?php  include(SERVER_ROOT_URL."/include/box_private.php"); ?>
            <?php  include(SERVER_ROOT_URL."/include/garanties.php"); ?>
        </div>
    </div>
</div>
