<div class="main">
    <?php
    $client = null;
    if($_SESSION["commande"]["client"]){
        $client = client::recupClient($_SESSION["commande"]["client"]);
    }
    if(!$client){
        ?>
        <div class="form-client-commande">
        <div class="text">
            <div class="title">S'identifier</div>
            <?php
            include(SERVER_ROOT_URL."/include/form_login.php");
            ?>
            
                <div class="description">
                    <p>
                        <strong style="color:var(--color1)">Vous avez déjà un compte Corsica Natura ? </strong> Connectez vous avec vos identifiants pour préremplir vos informations.<br/>
                        <strong style="color:var(--color1)">Envie de rapidité ?</strong> Vous pouvez vous inscrire au séjour sans créer de compte client et remettre sa création à plus tard.
                    </p>
                </div>
            </div>
        </div>
        
        <?php
    }

    ?>
    <form class="form_commande" action="<?=SITE_CONFIG_URL_SITE?>/ajax/creation_commande.php">
        <?php include(SERVER_ROOT_URL."/include/form_participant.php"); ?>
        <?php include(SERVER_ROOT_URL."/include/assurance.php"); ?>
        <?php include(SERVER_ROOT_URL."/include/validation.php"); ?>
        <?php include(SERVER_ROOT_URL."/include/conditions.php"); ?>
    </form>
</div>
<div class="aside">
    <div class="title">Besoin d'aide ?</div>
    <div class="subtitle">Contactez nous au <?=$info["information_tel"]?></div>

    <?php
    if($client && $client->get("client_id") > 0){
        ?>
        <div class="content-aside">
            <div class="label">Utilisateur</div>
            <div class="line"><strong>Nom</strong> <?=$client->get("client_nom")?></div>
            <div class="line"><strong>Prénom</strong> <?=$client->get("client_prenom")?></div>
            <div class="line"><strong>Email</strong> <?=$client->get("client_mail")?></div>
            <div class="line"><strong>Téléphone</strong> <?=$client->get("client_tel1")?></div>
        </div>
        <?php
    }
    ?>

    <div class="content-aside">
        <div class="label">Séjour</div>
        <div class="line"><strong>Type</strong> <?=$sejour->type()?></div>
        <div class="line"><strong>Départ</strong> <?=fonction_date::dateAnglaiseFrancaise($sejour->get("debut"))?></div>
        <div class="line"><strong>Arrivée</strong> <?=fonction_date::dateAnglaiseFrancaise($sejour->get("fin"))?></div>
        <div class="line"><strong>Difficulté</strong> <?=$cat_sejour->get("cat_sejour_difficulte")?></div>
    </div>

    <?php  include(SERVER_ROOT_URL."/include/recap_price.php"); ?>

</div>
