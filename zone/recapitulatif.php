<div class="zone recapitulatif commande">
    <div class="container">

        <?php
        $prix_adulte = $sejour->get("prix");
        $prix_enfant = $sejour->get("prix_enfant");
        $nb_adulte = $_SESSION["commande"]["adultes"];
        $nb_enfant = $_SESSION["commande"]["enfants"];
        $total_personne = $nb_adulte + $nb_enfant;

        $total = (($nb_adulte * $prix_adulte) + ($nb_enfant * $prix_enfant) + FRAIS_INSCRIPTION);
        $total2 = (($nb_adulte * $prix_adulte) + ($nb_enfant * $prix_enfant));

        $tiers = ($total2 * 0.30) + FRAIS_INSCRIPTION;
        $reste = $total2 - ($tiers) + FRAIS_INSCRIPTION;

        $assurance = $_SESSION["commande"]["assurance"];
        $paiement = $_SESSION["commande"]["mode_paiement"];

        ?>

        <div class="text">
            <div class="title">Récapitulatif de votre commande</div>
            <div class="overflow-table">
                <table>
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Details</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Prix Total</th>
                        </tr>
                    </thead>
                <tbody>
                <tr>
                    <td><?=$sejour->type()?></td>
                    <td>Adulte</td>
                    <td><?=$nb_adulte?></td>
                    <td><?=$prix_adulte?> €</td>
                    <td><?=number_format($prix_adulte * $nb_adulte, 2, ".", "")?> €</td>
                </tr>
                <?php
                if ($nb_enfant > 0) {
                ?>
                <tr>
                    <td><?=$sejour->type()?></td>
                    <td>Enfant</td>
                    <td><?=$nb_enfant?></td>
                    <td><?=$prix_enfant?> €</td>
                    <td><?=number_format($prix_enfant * $nb_enfant, 2, ".", "")?> €</td>
                </tr>
                <?php
                }
                ?>
                <tr>
                    <td>Assurance</td>
                    <td><?=$assurance?></td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Frais inscription</td>
                    <td>-</td>
                    <td>1</td>
                    <td><?=number_format(FRAIS_INSCRIPTION, 2, ".", "")?>€</td>
                    <td><?=number_format(FRAIS_INSCRIPTION, 2, ".", "")?>€</td>
                </tr>
                <tr class="last-line">
                    <td colspan="4">
                    <?php
                    if ($paiement == "delai") {
                    echo "<p>Vous avez choisi de régler 30% du montant total de la réservation, vous vous engagez à régler le montant restant au plus tard 30 jours avant le début du séjour.</p>";
                    } elseif ($paiement == "solde") {
                    echo "<p>Vous avez choisi de régler l'intégralité du séjour.</p>";
                    }
                    ?>
                </td>
                <td class="final">
                    <span>Montant total : </span>
                    <?=$total?> €
                    </td>
                </tr>
                <?php
                if ($paiement == "delai") {
                ?>
                <tr class="last-line">
                <td colspan="3" class="final">
                <span>Montant de l’acompte dû : </span>
                <?=number_format($tiers, 2, ".", "")?>
                €
                </td>
                <td colspan="5" class="final">
                <span>Montant à régler un mois avant le départ : </span>
                <?=number_format($reste, 2, ".", "")?>€
                </td>
                </tr>
                <?php
                }
                ?>
                </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="text">
            <div class="title">Récapitulatif des participants</div>
            <div class="overflow-table">
                <table>
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Mail</th>
                            <th>Taille T-Shirt</th>
                            <th>Année de naissance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($_SESSION["commande"]["participants"] as $key => $participant){
                            ?>
                            <tr>
                                <td><?=$key + 1?></td>
                                <td><?=$participant["nom_participant"]?></td>
                                <td><?=$participant["prenom_participant"]?></td>
                                <td><?=$participant["mail_participant"]?></td>
                                <td><?=$participant["tshirt_participant"]?></td>
                                <td><?=$participant["naissance_participant"]?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
    $mainParticipant = $_SESSION["commande"]["participants"][0];
    ?>
    <form class="form_prepay" action="<?=SITE_CONFIG_URL_SITE?>/ajax/prepay.php">
    <div class="container parts">
        <div class="part">
            <div class="text">
                <div class="title">Commentaires</div>
                <div class="description">
                    <?=$_SESSION["commande"]["commentaires"] != "" ? $_SESSION["commande"]["commentaires"] : "Aucun précision n'a été apportée à votre commande."; ?>
                </div>
            </div>
        </div>
        <div class="part">
            <div class="text">
                <div class="title">Adresse de facturation</div>
                
                    <?php

                    $adresses = [];
                    $client = client::recupClient($_SESSION["commande"]["client"]);
                    if($client->get("client_id") > 0){
                        $adresses["client_adresse"] = $client->get("client_adresse");
                        $adresses["client_cp"] = $client->get("client_cp");
                        $adresses["client_ville"] = $client->get("client_ville");
                        $adresses["client_pays"] = $client->get("client_pays");
						$mobilePhone = $client->get("client_port");
						if ($mobilePhone) {
							$mobilePhone = preg_replace('/\D/', '', $mobilePhone);
                    	}
						$adresses["client_port"] = $mobilePhone;
					}

                    if($_SESSION["commande"]["adresse"]){
                        $adresses = $_SESSION["commande"]["adresse"];
                    }

                    /*
                    <div class="form-group">
                        <label>Nom : </label>
                        <p><?=$mainParticipant["nom_participant"]?></p>
                    </div>
                    <div class="form-group">
                        <label>Prénom : </label>
                        <p><?=$mainParticipant["prenom_participant"]?></p>
                    </div>
                    */
                    ?>
                    <div class="c-form">
                        <div class="form-group">
                            <label for="client_adresse">Adresse *</label>
                            <input type="text" name="client_adresse" required value="<?=$adresses["client_adresse"]?>" />
                        </div>
                        <div class="form-group">
                            <label for="client_cp">Code postal *</label>
                            <input type="text" name="client_cp" required value="<?=$adresses["client_cp"]?>"  />
                        </div>
                        <div class="form-group">
                            <label for="client_ville">Ville *</label>
                            <input type="text" name="client_ville" required value="<?=$adresses["client_ville"]?>"  />
                        </div>
                        <div class="form-group">
                            <label for="client_pays">Pays *</label>
                            <select name="client_pays" required>
                                <option value="">Sélectionner un pays</option>>
                                <?php
                                $countries = nouveau_paiement::getCountryCodes();
                                foreach($countries as $k => $v) {
                                    ?>
                                    <option <?php if($k == $adresses["client_pays"]) { echo "selected"; }?> value="<?=$k?>"><?=$k?></option>
                                    <?php
                                }
                                ?>
                        </select>
                        </div>
						<div class="form-group">
                            <label for="client_port">N° téléphone portable *</label>
                            <input type="text" name="client_port" required value="<?=$adresses["client_port"]?>"  />
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <div class="container last">
        <div class="buttons">
            <a class="button" href="javascript:history.back()">Etape précédente</a>
            <button type="submit" class="button btn-select-payment">Paiement</a>
        </div>
    </div>
    </form>
</div>
