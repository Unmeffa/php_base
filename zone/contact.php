<div class="zone contact">
    <div class="text">
        <div class="title">Accès &</div>
        <div class="prettyTitle">Contact</div>
        <ul class="infos">
            <li>
                <img data-src="<?=SITE_CONFIG_URL_SITE?>/img/location.png" alt="Adresse Corsica Natura" />
                <?php echo $info["information_nom"]?> <br/>
                <?php echo $info["information_adresse"]?> - <?php echo $info["information_cp"]." ".$info["information_ville"]?>
            </li>
             <li>
                <img data-src="<?=SITE_CONFIG_URL_SITE?>/img/phone.png" alt="Téléphone Corsica Natura" />
                <?php echo $info["information_telephone"]?>
                <?php echo $info["information_portable"]?>
            </li>
              <li>
                <img data-src="<?=SITE_CONFIG_URL_SITE?>/img/mail.png" alt="Mail Corsica Natura" />
                <?php echo $info["information_mail"]?> <br/>
            </li>
        </ul>
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
                <input type="hidden" name="form_type" value="contact" />
                <input type="hidden" value="<?=$rand?>" name="captchaVerif"  />
            </form>
    </div>
    <div class="medias">
            <div class="photo">
        <?php echo $info["information_geoloc"]?>
    </div>
        <?php
        $photo = site_fonction::recupPhotoPrincipale('diapo1',$id_get);
        if($photo->get("photo_id") > 0)
        {
            ?>
            <div class="photo lazy"
                data-id="<?=$photo->get("photo_id")?>">
                <img src="" alt="" />
            </div>
            <?php
        }
    ?>

    </div>
</div>
