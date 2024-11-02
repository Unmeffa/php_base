<div class="zone offrir">
    <div class="container">
        <div class="text">
            <div class="title">Offrir un séjour</div>
            <form>
                <div class="form-group">
                    <select required name="sejour" id="">
                        <option value="">Sélectionner votre séjour *</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="text" placeholder="Date d'arrivée *" required name="resa_debut" />
                    <div class="toDate"></div>
                    <input type="text" placeholder="Date de départ *" required name="resa_fin" />
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
                <div class="title">Bénéficiare : </div>
                 <div class="form-group">
                    <input type="text" name="destinataire" placeholder="Bénéficiare" />
                    <input type="email" name="mail_destinataire" placeholder="Email du bénéficiaire" />
                    <input type="text" name="tel_destinataire" placeholder="Telephone du bénéficiaire" />
                </div>
                 <div class="form-group">
                    <input type="text" name="adresse" placeholder="Adresse de livraison" />
                    <input type="email" name="cp" placeholder="Code Postal" />
                    <input type="text" name="ville" placeholder="Ville" />
                </div>
                <div class="form-group">
                    <input type="text" name="resa_message" placeholder="Message" />
                </div>
              <div class="form-group send">
                    <button type="submit" class="button">Envoyer</button>
                </div>
                <input type="hidden" name="form_type" value="bon_cadeau" />
            </form>
        </div>
    </div>
</div>
