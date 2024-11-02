<div class="zone form espace_client">
    <div class="container">
        <?php
        $client = null;
        
        if($_SESSION["client"]){
            $client = client::recupClient($_SESSION["client"]);
        }
        if(!$client){
            include(SERVER_ROOT_URL."/include/form_login.php");
        }
        else{
            ?>
            <div class="text">
                <div class="title">Commandes</div>
                <div class="description">Listing de commandes</div>
                <div class="overflow-table">
                    <table>
                        <thead>
                            <tr>
                                <td>N °</td>
                                <td>Date</td>
                                <td>Etat</td>
                                <td>Prix</td>
                                <td>Reste à payer</td>
                                <td>Details</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $tabReste = [];
                            $rs_commande = fonction::recup("commande", "where client_id = ".$client->get("client_id")." order by commande_date", "", "", "commande_id");
                            if (mysql_num_rows($rs_commande) > 0) {
                                while ($row_commande = mysql_fetch_row($rs_commande)) {
                                    $cm = commande::recupCommande($row_commande[0]); 

                                    ?>
                                    <tr>
                                        <td><?=$cm->get("id")?></td>
                                        <td><?=$cm->get("date")?></td>
                                        <td><strong><?=$cm->get("etat")?></strong></span>
                                        </td>
                                        <td><?=$cm->get("prix")?>€</td>

                                        <?php
                                        if ($cm->reste() > 0) 
                                        {
                                            $cm->get("etat") != "annulée" ? $tabReste[$cm->get("id")] = $cm->reste() : '';
                                             $tabReste[$cm->get("id")] = $cm->reste();
                                            ?>
                                            <td><?=number_format($cm->reste(), 2, ".", "")?>€</td>
                                            <?php
                                        } 
                                        else 
                                        {
                                            $tabReste[$cm->get("id")] = 0; ?>
                                            <td>-</td>
                                        <?php
                                        } 
                                        ?>
                                        <td>
                                            <a 
                                                data-fancybox="async-recap<?=$cm->get("id")?>"
                                                href="<?=SITE_CONFIG_URL_SITE?>/include/commande-recap.php?id=<?=$cm->get("id")?>" 
                                                class="button">
                                                Details
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                foreach ($tabReste as $key => $val) {
                                    if ($val > 0) {
                                        ?>
                                        <tr>
                                            <td colspan="6">
                                                <a class="solde-btn" data-fancybox="async-solde<?=$cm->get("id")?>"
                                                    href="<?=SITE_CONFIG_URL_SITE?>/include/solde-commande.php?id=<?=$key?>"
                                                    >Solder ma commande
                                                    N°<?=$key?> d'un montant de <?=number_format($val, 2, ".", "")?> €</a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                            else{
                                ?>
                                <tr>
                                    <td colspan="6">Aucune commande n'a été validée ou enregistrée</td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
            if(count($tabReste) > 0)
            {
                ?>
                <div class="text">
                    <div class="title">Documents</div>
                    <?php
                    foreach($tabReste as $key => $val)
                    {
                        ?>
                        <div class="title mini-title">Commande n° <?=$key?></div>
                        <div class="links">
                        <?php
                         $o = commande::recupCommande($key);
                         $rs_doc = fonction::recup("document", "where document_type = 'sejour' and produit_id = ".$o->get("sejour_id")." and document_fichier like '%.pdf' order by document_prio", "", "", "document_id");
                         while ($row_doc = mysql_fetch_row($rs_doc)) {
                             $d = document::recupDocument($row_doc[0]); ?>
 
                                 
                                     <a href="<?=$d->chemin_document()?>"
                                         title="<?=$d->get("document_nom")?>"
                                         class="button"
                                         target="_blank">
                                         <i
                                             class="fa fa-file-pdf-o"></i><span><?=$d->get("document_nom")?></span>
                                     </a>
                                 
                                 <?php
                         }
                         if (mysql_num_rows($rs_doc) == 0) {
                            $rs_doc = fonction::recup("sejour", "where sejour_id = ".$o->get("sejour_id"), 0, 1, "cat_sejour_id");
                            $row_doc = mysql_fetch_row($rs_doc);
                            $rs_doc = fonction::recup("document", "where document_type = 'cat_sejour' and produit_id = ".$row_doc[0]." order by document_prio", "", "", "document_id");
                            while ($row_doc = mysql_fetch_row($rs_doc)) {
                                $d = document::recupDocument($row_doc[0]); ?>
                                
                                    <a href="<?=$d->chemin_document()?>"
                                        class="button"
                                        title="<?=$d->get("document_nom")?>"
                                        target="_blank">
                                        <i
                                            class="fa fa-file-pdf-o"></i><span><?=$d->get("document_nom")?></span>
                                    </a>
                                
                                <?php
                            }
                            if ($o->date_solde() != 0) {
                                ?>
                                    <a target="_blank" class="button"
                                            href="/resasoft/pdf/pdf-accompagnement-solde.php?id=<?=$o->get("id")?>&client_id=<?=$o->get("client_id")?>">
                                            <i class="fa fa-file-pdf-o"></i><span>Lettre d'accompagnement avec solde</span>
                                        </a>
                                    <?php
                            } elseif ($o->date_acompte() != 0) {
                                ?>
                                    
                                        <a target="_blank" class="button"
                                            href="/resasoft/pdf/pdf-accompagnement-accompte.php?id=<?=$o->get("id")?>&client_id=<?=$o->get("client_id")?>">
                                            <i class="fa fa-file-pdf-o"></i><span>Lettre d'accompagnement avec
                                                acompte</span>
                                        </a>
                                   
                                    <?php
                            }
                            ?>
                                                      
                                    <a target="_blank" class="button"
                                        href="/resasoft/document/lettre-de-prise-en-charge-personnelle.pdf">
                                        <i class="fa fa-file-pdf-o"></i><span>Lettre de prise en charge
                                            personnelle</span>
                                    </a>
                                

                                
                                    <a target="_blank" class="button"
                                        href="/resasoft/pdf/pdf-bilan-reservation.php?id=<?=$o->get("id")?>&client_id=<?=$o->get("client_id")?>">
                                        <i class="fa fa-file-pdf-o"></i><span>Bilan de réservation</span>
                                    </a>
                                
                                <?php
                                if ($o->facturer() == 1) {
                                    ?>
                                       
                                            <a target="_blank" class="button"
                                                href="/resasoft/pdf/pdf-facture.php?id=<?=$o->get("id")?>&client_id=<?=$o->get("client_id")?>">
                                                <i class="fa fa-file-pdf-o"></i><span>Facture</span>
                                            </a>
                                        
                                        <?php
                                }
                        }
                        ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            ?>

            <div class="text">
                <div class="title">Informations</div>
                <div class="description"><p>Modifier vos informations</p></div>
                <form action="<?=SITE_CONFIG_URL_SITE?>/ajax/update_info.php" class="form_infos">
                    
                    <div class="description">
                        <p>Modifier votre mot de passe</p>
                    </div>

                    <div class="form-group">
                        <input type="password" name="client_mdp" placeholder="Changer de mot de passe *" />
                        <input type="password" name="client_mdp2" placeholder="Vérifiez le mot de passe *" />
                    </div>

                    <div class="description">
                        <p>Modifier votre adresse</p>
                    </div>


                    <div class="form-group">
                        <input type="text" value="<?=$client->get("client_adresse")?>" required="required" name="client_adresse" placeholder="Adresse *" />
                        <input type="text" value="<?=$client->get("client_cp")?>" required="required" name="client_cp" placeholder="Code Postal *" />
                    </div>
                    <div class="form-group">
                        <input type="text" value="<?=$client->get("client_ville")?>" required="required" name="client_ville" placeholder="Ville *" />
                        <input type="text" value="<?=$client->get("client_pays")?>" required="required" name="client_pays" placeholder="Pays *" />
                    </div>
                    <div class="form-group">
                        <button type="submit" class="button">Mettre à jour</button>
                    </div>
                </form>
            </div>

            <div class="text">
                
                    <a class="button disconnect" href="<?=SITE_CONFIG_URL_SITE?>/ajax/deconnexion.php">Déconnexion</a>
               
            </div>
            <?php
        }
        ?>
    </div> 
</div>