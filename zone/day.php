<div class="day text">
    <div class="title"><span>Jour
        <?=$day->get("prio")?></span>
        <?php echo $dday["titre"]?>
    </div>
    <div class="day-props">
        <?php
        $specs = [];
    $specs[] = array('value' => $day->get("jour_cat_sejour_temps_transfert"), 'name' => 'Transfert', 'icon' => 'duree');
    $specs[] = array('value' => $day->get("jour_cat_sejour_denivele_positif"), 'name' => 'Dénivelé positif', 'icon' => 'up','metrics' => 'm');
    $specs[] = array('value' => $day->get("jour_cat_sejour_denivele_negatif"), 'name' => 'Dénivelé négatif', 'icon' => 'down','metrics' => 'm');
    $specs[] = array('value' => $day->get("jour_cat_sejour_distance"), 'name' => 'Distance', 'icon' => 'direction','metrics' => 'km');
    $specs[] = array('value' => $day->get("jour_cat_sejour_hebergement"), 'name' => 'Hébergement', 'icon' => 'tent');
    $specs[] = array('value' => $day->get("jour_cat_sejour_assistance_bagage"), 'name' => 'Assistance Bagage', 'icon' => 'bag');
        foreach($specs as $spec){
            if($spec['value'] != '')
            {
                ?>
                <div class="day-prop">
                    <div class="picto">
                        <img alt="<?=$spec['name']?>" data-src="<?=SITE_CONFIG_URL_SITE?>/img/<?=$spec['icon']?>.png" />
                    </div>
                    <div class="value"><?=$spec['value']?> <?=$spec['metrics'] ? $spec['metrics'] : ''?></div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <div class="description">
        <?php echo $dday["description"]?>
    </div>
</div>
