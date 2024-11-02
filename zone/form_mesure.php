<?php
$photo = site_fonction::recupPhotoPrincipale("zone", $zone->get("zone_id"));
?>
<div class="zone form_mesure">
    <?php
    if($photo->get("photo_id") > 0) {
        ?>
    <div class="photo lazy paralax"
        data-id="<?=$photo->get("photo_id")?>">
    </div>
    <?php
    }
?>
    <div class="content">
        <div class="text">
            <div class="title">
                <?=$zoneDetail->get("detail_zone_titre")?>
            </div>
            <?php
        if(trim($zoneDetail->get("detail_zone_titre2")) != '') {
            ?>
            <div class="prettyTitle">
                <?=$zoneDetail->get("detail_zone_titre2")?>
            </div>
            <?php
        }?>
        </div>
        <div class="content-form">
            <form action="<?=SITE_CONFIG_URL_SITE?>/ajax/contact.php">
                <div class="form-group">
                    <input type="text" required placeholder="Nom *" name="client_nom" />
                </div>
                <div class="form-group">
                    <input type="text" required placeholder="Prénom *" name="client_prenom" />
                </div>
                <div class="form-group">
                    <input type="email" required placeholder="Adresse Mail *" name="client_mail" />
                </div>
                <div class="form-group">
                    <input type="text" required placeholder="Téléphone *" name="client_tel" />
                </div>
                <div class="form-group dates">
                    <input type="text" required placeholder="Arrivée *" name="resa_debut" />
                    <span class="toDate"></span>
                    <input type="text" required placeholder="Départ *" name="resa_fin" />
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
                <div class="form-group">
                    <textarea name="resa_message" placeholder="Décrivez-nous vos envies"></textarea>
                </div>
                <?php
                $rand = rand(0,count($captchaCustom) - 1);
                $selectCaptcha = $captchaCustom[$rand];
                ?>
                <div class="form-group send">
                    <input type="text" required="required" name="verifCode" placeholder="Code de vérification : <?=$selectCaptcha[0]?>" />
                </div>
                <div class="form-group send">
                    <button type="submit" class="button">Envoyer</button>
                </div>
                <input type="hidden" name="form_type" value="sur_mesure" />
                <input type="hidden" value="<?=$rand?>" name="captchaVerif"  />
            </form>
        </div>

    </div>
</div>
