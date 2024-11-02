<?php
$rs_pagesejour = site_fonction::recup("page","where page_type = 'sejour' and page_actif = 1");
$listSejours = [];
while($fetch_pageSejour = mysql_fetch_assoc($rs_pagesejour)){
    $pageSejour = new site_page($fetch_pageSejour);
    $pageSejourDetail = $pageSejour->recupPage_detail($lang_get);
    $metaData = $pageSejourDetail->get("page_detail_metadonnees");
    $dataIDs = strpos($metaData,"#") != false ? explode("#",$metaData) : $metaData;
    $listSejours[] = ["name" => $pageSejourDetail->get("page_detail_nom"), "ids" => $dataIDs];
}
?>
<div class="zone form form_private">
    <div class="container">
        <div class="text">
            <div class="title">Votre séjour</div>
            <form action="<?=SITE_CONFIG_URL_SITE?>/ajax/contact.php">
                <div class="form-group">
                    <select required name="sejour" id="">
                        <option value="">Sélectionner votre séjour *</option>
                        <?php
                        foreach($listSejours as $category){
                            if(count($category["ids"]) > 0){
                                ?>
                                <optgroup  label="<?=$category["name"]?>">
                                    <?php
                                    foreach($category["ids"] as $sejourId){
                                         $sejour = cat_sejour::recupCat_sejour($sejourId);
                                         if($sejour->get("id") > 0)
                                         {
                                            $selected = isset($_GET["sejour"]) && $_GET["sejour"] == $sejour->get("id") ? 'selected' : '';
                                            ?>
                                            <option <?=$selected?> value="<?=$sejour->get("cat_sejour_nom")?>"><?=$sejour->get("cat_sejour_nom")?></option>
                                            <?php
                                         }
                                    }
                                    ?>
                                </optgroup>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <input type="text" autocomplete="off" placeholder="Date d'arrivée *" required name="resa_debut" />
                    <div class="toDate"></div>
                    <input type="text" autocomplete="off" placeholder="Date de départ *" required name="resa_fin" />
                </div>
                <div class="form-group select">
                    <label for="adults">Adultes *</label>
                    <div class="actions">
                        <span class="minus">-</span>
                        <span class="value">1</span>
                        <span class="plus">+</span>
                    </div>
                    <input type="hidden" name="nb_adulte" value="1" />
                </div>
                <div class="form-group select">
                    <label for="childs">Enfants *</label>
                    <div class="actions">
                        <span class="minus">-</span>
                        <span class="value">0</span>
                        <span class="plus">+</span>
                    </div>
                    <input type="hidden" name="nb_enfant" value="0" />
                </div>
                <div class="title">Informations</div>
                <div class="form-group">
                    <input type="text" name="client_nom" placeholder="Nom *" required />
                    <input type="text" name="client_prenom" placeholder="Prénom *" required />
                </div>
                <div class="form-group">
                    <input type="email" name="client_mail" placeholder="Email *" required />
                    <input type="text" name="client_tel" placeholder="Téléphone *" required />
                </div>
                 <div class="form-group">
                    <input type="text" name="client_adresse" placeholder="Adresse *" required />
                    <input type="text" name="client_cp" placeholder="Code Postal *" required />
                    <input type="text" name="client_ville" placeholder="Ville *" required />
                </div>
                <div class="form-group">
                    <input type="text" name="resa_message" placeholder="Message" />
                </div>
              <div class="form-group send">
                    <button type="submit" class="button">Envoyer</button>
                </div>
                <input type="hidden" name="form_type" value="devis" />
            </form>
        </div>
    </div>
</div>
