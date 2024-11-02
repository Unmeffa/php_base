<div class="zone recap">
    <div class="container">
        <div class="text">
            <div class="title">Confirmation de votre commande</div>
            <div class="description">
                <p>Cher <?=$o->client()?>, <br/>
				Votre commande pour le séjour <strong><?=$o->sejour()?></strong> a bien été enregistrée et nous vous remercions d'avoir choisi Corsica Natura.<br/><br/>
				Vous trouverez ci-dessous le récapitulatif de votre commande.
			    </p>
                <br/>
                <p>Commande effectué  le <strong><?=fonction_date::dateAnglaiseFrancaise($o->get("date"))?></strong></p>
                <p><strong>Etat de la commande : <span style="color:<?=$o->couleurEtat()?>"><?=$o->get("etat")?></span></strong></p>
                <?php
                if($o->get("commentaire_client") != "")
                {
                    ?>
                    <p>
				        <strong>Vos Commentaires : </strong><br/>
                        <?=str_replace("\n","<br />",$o->get("commentaire_client"))?>
                    </p>
                    <?php
                }
                ?>
                <br/>
                <p><strong>Nombre de participants :</strong> <?=$o->get("commande_nb_personne")?></p>
                <p><strong>Nombre d'enfants compris : <?=$o->get("nb_enfant")?></strong></p> 
            </div>
            <div class="overflow-table presta">
            <table>
					<thead>
						<tr>
							<td>Description</td>
							<td>Prix</td>
						</tr>
					</thead>
					<tbdody>
						<?php
						$rs = fonction::recup("prestation","where commande_id = ".$o->get("id")." order by prestation_id","","","prestation_id");
						$prix_sanspresta = $o->get("prix");
						while($row = mysql_fetch_row($rs))
						{
							$ra = prestation::recupPrestation($row[0]);
							$prix_sanspresta -= $ra->get("montant");
						}
						?>
						<tr>
							<td><?=$o->sejour_utf8()?></td>
							<td><strong><?=number_format($prix_sanspresta,2)?> €</strong></td>
						</tr>
						<?php
						$rs = fonction::recup("prestation","where commande_id = ".$o->get("id")." order by prestation_id","","","prestation_id");
						if(mysql_num_rows($rs) > 0)
						{
							while($row = mysql_fetch_row($rs))
							{
								$ra = prestation::recupPrestation($row[0]);
								?>
								<tr>
									<td><?=$ra->get("nom")?></td><td><strong><?=$ra->get("montant")?> &euro;</strong></td>
								</tr>
								<?php
							}
							echo "</div><div>";
						}
						
						if($o->remise() > 0)
						{ 
							?>
							<tr>
								<td>
									Remise : 
								</td>
								<td>
									<strong><?="- ".$o->remise()?> &euro;</strong>
								</td>
							</tr>
							<?php
						} 
						?>
						<tr>
							<td>Ce montant devra etre réglé dans sa <strong>totalité</strong>, au plus tard, <strong>30 jours avant le début du séjour</strong> soit avant le <?=fonction_date::ecrireDate(fonction_date::ajoutJour(-30,$s->get("debut")))?>.</td>
							<td></td>
						</tr>
						<tr>
							<td><strong>Montant total restant dû</strong></td>
							<td><strong><?=number_format($o->reste(),2)?> &euro;</strong></td>
						</tr>
					</tbdody>
				</table>
            </div>
            <?php
            if($o->reste() == $prix_sanspresta)
            {
                ?>
                 <div class="description">
                <p>Dés réception de votre paiement d'un montant de <strong><?=$o->reste()?> €</strong>, nous vous ferons parvenir un mail de confirmation.</p></p>
            </div>
                <?php
            }
            ?>
           
            <div class="title">Participants</div>
            <div class="overflow-table">
            <table>
                <thead>
                    <tr>
                        <td>N°</td>
                        <td>Nom</td>
                        <td>Prénom</td>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rs = fonction::recup("participant","where commande_id = ".$o->get("id")." order by participant_id","","","client_id");
                    if(mysql_num_rows($rs) > 0)
                    {
                        $i = 1;
                        while($row = mysql_fetch_row($rs))
                        {
                            $ra = client::recupClient($row[0]);
                            ?>
                            <tr>
                                <td><?=$i?></td>
                                <td><?=$ra->get("client_nom")?></td>
                                <td><?=$ra->get("client_prenom")?></td>
                            </tr>
                            <?php
                            $i++;
                        }
                        echo "</ul>";
                    }
                    else
                    {
                        ?>
                        <tr><td colspan="3">Aucun participant inscrit</td></tr>
                        <?php
                    }
                    ?>
                </tbody>
				</table>
            </div>
            <div class="title">Paiements</div>
            <div class="overflow-table">
            <table>
					<thead>
						<tr>
							<td>N °</td>
							<td>Date</td>
							<td>Type</td>
							<td>Montant</td>
						</tr>
					</thead>
					<tbody>
						<?php 
						$rs = fonction::recup("paiement","where commande_id = ".$o->get("id")." order by paiement_date","","","paiement_id");
						if(mysql_num_rows($rs) > 0)
						{
							$i = 1;
							while($row = mysql_fetch_row($rs))
							{

								$ra = paiement::recupPaiement($row[0]);
								?>
								<tr>
									<td><?=$i?></td>
									<td><?=fonction_date::dateAnglaiseFrancaise($ra->get("date"))?></td>
									<td><?=ucfirst($ra->get("type"))?></td>
									<td><strong><?=$ra->get("montant")?> &euro;</strong></td>
								</tr>
								<?php
								$i++;
							}
						}
						else
						{
							?>
							<tr><td colspan="4">Aucun paiement</td></tr>
							<?php
						}
						?>
					</tbody>
				</table>
            </div>
        </div>
    </div>
</div>