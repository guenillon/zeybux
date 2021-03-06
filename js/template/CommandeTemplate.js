;function CommandeTemplate() {	
	this.detailReservation = 
		"<div id=\"contenu\">" +
			"<div class=\"com-widget-window ui-widget ui-widget-content ui-widget-content-transparent ui-corner-all\">" +
				"<div class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">" +
					"Marché n°{comNumero}" +
				"</div>" +
				"<div id=\"resa-info-commande\">" +
					"Fin des réservations : Le {dateFinReservation} à {heureFinReservation}H{minuteFinReservation} <br/>" +
					"Marché : Le {dateMarcheDebut} de {heureMarcheDebut}H{minuteMarcheDebut} à {heureMarcheFin}H{minuteMarcheFin}" +
				"</div>" +
				"<div>" +
					"<span>Solde Actuel : </span><span class=\"{classSoldeActuel}\">{solde} {sigleMonetaire}</span><br/>" +
					"<span>Nouveau Solde : </span><span id=\"nouveau-solde\">{soldeNv}</span> <span id=\"nouveau-solde-sigle\">{sigleMonetaire}</span>" +
				"</div>" +
			"</div>" +
			"<div class=\"com-widget-window ui-widget ui-widget-content ui-widget-content-transparent ui-corner-all\">" +
				"<div class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">" +
					"Ma réservation" +
				"</div>" +
				"<table>" +
					"<!-- BEGIN categories -->" +
					"<td class=\"ui-widget-header ui-corner-all com-center\">{categories.nom}</td>" +
					"<td colspan=\"6\"></td>" +
					
					"<!-- BEGIN categories.produits -->" +
					"<tr >" +
						"<td class=\"detail-resa-npro\">{categories.produits.nproNom}</td>" +
						"<td class=\"td-edt\">" +
							"<span class=\"com-cursor-pointer com-btn-header ui-widget-content ui-corner-all btn-info-produit\" title=\"Information sur le produit\" id-produit=\"{categories.produits.proId}\">" +
								"<span class=\"ui-icon ui-icon-info\">" +
								"</span>" +
							"</span>" +
						"</td>" +
						"<td>{categories.produits.flagType}</td>" +
						"<td class=\"com-text-align-right detail-resa-qte\">{categories.produits.stoQuantite}</td>" +
						"<td class=\"detail-resa-unite\">{categories.produits.proUniteMesure}</td>" +
						"<td class=\"com-text-align-right detail-resa-prix\">{categories.produits.prix}</td>" +
						"<td>{sigleMonetaire}</td>" +
					"</tr>" +
					"<!-- END categories.produits -->" +
					"<!-- END categories -->" +
					"<tr>" +
						"<td class=\"com-text-align-right\" colspan=\"5\">Total : </td>" +
						"<td class=\"com-text-align-right\">{total}</td>" +
						"<td>{sigleMonetaire}</td>" +
					"</tr>" +
				"</table>" +
			"</div>" +
			"<div class=\"boutons-edition com-widget-header ui-widget ui-widget-header ui-corner-all com-center\">" +
				"{boutonsEdition}" +
			"</div>";			
		"</div>";
	
	this.boutonModifier = 
		"<button class=\"ui-state-default ui-corner-all com-button com-center\" id=\"btn-modifier\">Modifier</button>";
		
	this.boutonsModifierSupprimer = 
		"<button class=\"com-btn-edt-multiples ui-state-default ui-corner-all com-button com-center\" id=\"btn-modifier\">Modifier</button>" +	
		"<button class=\"ui-state-default ui-corner-all com-button com-center\" id=\"btn-supprimer\">Supprimer</button>";
	
	this.supprimerReservationDialog =
		"<div id=\"dialog-supprimer-reservation\" title=\"Supprimer la réservation\">" +
			"<p>Voulez-vous supprimer la réservation ?</p>" +
		"</div>";
	
	this.produitIndisponible = 
		"<tr>" +
			"<td>{cproNom}</td>" +
			"<td></td>" +
			"<td>{nproNom} n'est plus disponible.</td>" +
			"<td></td>" +
			"<td></td>" +
			"<td></td>" +
			"<td></td>" +
			"<td></td>" +
			"<td></td>" +
			"<td></td>" +
		"</tr>";

	this.lotUnique = 
		"<input type=\"hidden\" id=\"lot-{IdPdt}\" value=\"{valeur}\" /><span>{text}</span>";
	
	this.flagAbonnement = 
		"<span class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">Abonnement</span>";
	
	this.flagSolidaire = 
		"<span class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">Solidaire</span>";
	
	this.modifierReservation =
		"<div id=\"contenu\">" +
			"<div class=\"com-widget-window ui-widget ui-widget-content ui-widget-content-transparent ui-corner-all\">" +
				"<div class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">" +
					"Marché n°{comNumero}" +
				"</div>" +
				"<div id=\"resa-info-commande\">" +
					"Fin des réservations : Le {dateFinReservation} à {heureFinReservation}H{minuteFinReservation} <br/>" +
					"Marché : Le {dateMarcheDebut} de {heureMarcheDebut}H{minuteMarcheDebut} à {heureMarcheFin}H{minuteMarcheFin} <br/>" +
				"</div>" +
				"<div>" +
					"<span>Solde Actuel : </span><span class=\"{classSoldeActuel}\">{solde} {sigleMonetaire}</span><br/>" +
					"<span>Nouveau Solde : </span><span id=\"nouveau-solde\">{soldeNv}</span> <span id=\"nouveau-solde-sigle\">{sigleMonetaire}</span>" +
				"</div>" +
			"</div>" +
			"<div class=\"com-widget-window ui-widget ui-widget-content ui-widget-content-transparent ui-corner-all\">" +
				"<div class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">" +
					"Ma réservation : <span id=\"total\">{total}</span> {sigleMonetaire}" +
				"</div>" +
				"<div>" +
					"<table id=\"table-form-produit\">" +
						"<thead>" +
							"<tr>" +
								"<th></th>" +
								"<th></th>" +
								"<th>Produit</th>" +
								"<th></th>" +
								"<th></th>" +
								"<th></th>" +
								"<th></th>" +
								"<th></th>" +
								"<th></th>" +
								"<th></th>" +
							"</tr>" +
						"<thead>" +
						"<tbody>" +
						//"<!-- BEGIN categories -->" +
					/*	"<tr>" +
							"<td colspan=\"4\" class=\"ui-widget-header ui-corner-all com-center\">{categories.nom}</td>" +
							"<td colspan=\"7\"></td>" +
						"</tr>" +			*/			
						"<!-- BEGIN produits -->" +
						"{produits.detailProduit}" +
						"<!-- END produits -->" +
						//"<!-- END categories -->" +
					/*	"<tr>" +
							"<td colspan=\"10\" class=\"com-text-align-right\">Total : </td>" +
							"<td class=\"com-text-align-right detail-resa-prix\"><span id=\"total\">{total}</span> {sigleMonetaire}</td>" +
						"</tr>" +*/

						"</tbody>" +
					"</table>" +
				"</div>" +
			"</div>" +
			"<div class=\"com-widget-header ui-widget ui-widget-header ui-corner-all com-center\">" +
				"<button class=\"com-btn-edt-multiples ui-state-default ui-corner-all com-button com-center\" id=\"btn-annuler\">Annuler</button>" +
				"<button class=\"ui-state-default ui-corner-all com-button com-center\" id=\"btn-valider\">Valider</button>" +		
			"</div>" +
		"</div>";
	
	this.formReservationProduit =
		"<tr class=\"pdt\" data-id-produit=\"{proId}\" >" +
			"<td>{cproNom}</td>" +
			"<td><input data-id-produit=\"{proId}\" type=\"checkbox\" {checked}/></td>" +
			"<td id=\"commandes{proId}stoQuantite\">{nproNom} {flagType}</td>" +
			"<td>" +
				"<span class=\"com-cursor-pointer com-btn-header ui-widget-content ui-corner-all btn-info-produit\" title=\"Information sur le produit\" id-produit=\"{proId}\">" +
					"<span class=\"ui-icon ui-icon-info\">" +
					"</span>" +
				"</span>" +
			"</td>" +
			"<td>" +
				"<select id=\"lot-{proId}\" data-id-produit=\"{proId}\">" +
					"<!-- BEGIN lot -->" +
					"<option value=\"{lot.dcomId}\">par {lot.dcomTaille} {proUniteMesure}</option>" +
					"<!-- END lot -->" +
				"</select>" +
			"</td>" +
			"<td>à <span id=\"prix-unitaire-{proId}\">{prixUnitaire}</span> {sigleMonetaire}/{proUniteMesure}</td>" +
			"<td><span class=\"ui-helper-hidden resa-pdt-{proId} com-cursor-pointer com-btn-header ui-widget-content ui-corner-all\"><span data-id-produit=\"{proId}\" class=\"ui-icon-circle-minus ui-icon btn-moins\"></span></span></td>" +
			"<td><span class=\"ui-helper-hidden resa-pdt-{proId}\"><span id=\"qte-pdt-{proId}\"></span> {proUniteMesure}</span></td>" +
			"<td><span class=\"ui-helper-hidden resa-pdt-{proId} com-cursor-pointer com-btn-header ui-widget-content ui-corner-all\"><span data-id-produit=\"{proId}\" class=\"ui-icon-circle-plus ui-icon btn-plus\"></span></span></td>" +
			"<td class=\"td-montant\"><span class=\"ui-helper-hidden resa-pdt-{proId}\"><span id=\"prix-pdt-{proId}\"></span> {sigleMonetaire}</span></td>" +
		"</tr>";

	this.formReservationProduitInfo =
		"<tr class=\"pdt\" data-id-produit=\"{proId}\" >" +
			"<td>{cproNom}</td>" +
			"<td></td>" +
			"<td id=\"commandes{proId}stoQuantite\">{nproNom} {flagType}</td>" +
			"<td>" +
				"<span class=\"com-cursor-pointer com-btn-header ui-widget-content ui-corner-all btn-info-produit\" title=\"Information sur le produit\" id-produit=\"{proId}\">" +
					"<span class=\"ui-icon ui-icon-info\">" +
					"</span>" +
				"</span>" +
			"</td>" +
			"<td>" +
				"<select id=\"lot-{proId}\" data-id-produit=\"{proId}\" >" +
					"<!-- BEGIN lot -->" +
					"<option value=\"{lot.dcomId}\">par {lot.dcomTaille} {proUniteMesure}</option>" +
					"<!-- END lot -->" +
				"</select>" +
			"</td>" +
			"<td>à <span id=\"prix-unitaire-{proId}\">{prixUnitaire}</span> {sigleMonetaire}/{proUniteMesure}</td>" +
			"<td></td>" +
			"<td></td>" +
			"<td></td>" +
			"<td></td>" +
		"</tr>";
	
	this.formReservationProduitAbonnementInfo =
		"<tr class=\"pdt\" data-id-produit=\"{proId}\" >" +
			"<td>{cproNom}</td>" +
			"<td></td>" +
			"<td id=\"commandes{proId}stoQuantite\">{nproNom} {flagType}</td>" +
			"<td>" +
				"<span class=\"com-cursor-pointer com-btn-header ui-widget-content ui-corner-all btn-info-produit\" title=\"Information sur le produit\" id-produit=\"{proId}\">" +
					"<span class=\"ui-icon ui-icon-info\">" +
					"</span>" +
				"</span>" +
			"</td>" +
			"<td>" +
				"<select id=\"lot-{proId}\" disabled=\"disabled\" data-id-produit=\"{proId}\">" +
					"<!-- BEGIN lot -->" +
					"<option value=\"{lot.dcomId}\">par {lot.dcomTaille} {proUniteMesure}</option>" +
					"<!-- END lot -->" +
				"</select>" +
			"</td>" +
			"<td>à <span id=\"prix-unitaire-{proId}\">{prixUnitaire}</span> {sigleMonetaire}/{proUniteMesure}</td>" +
			"<td></td>" +
			"<td>{stoQuantiteReservation} {proUniteMesure}</td>" +
			"<td></td>" +
			"<td class=\"td-montant\">{prixReservation} {sigleMonetaire}</td>" +
		"</tr>";
	
	this.confirmationReservationCommande =
		"<div class=\"com-widget-window ui-widget ui-widget-content ui-widget-content-transparent ui-corner-all\">" +
			"<table>" +
				"<!-- BEGIN produit -->" +
				"<tr>" +
					"<td>{produit.NOM}</td>" +
					"<td>{produit.QUANTITE}</td>" +
					"<td>{produit.PRIX}</td>" +
				"</tr>" +
				"<!-- END produit -->" +
					"<tr>" +
					"<td></td><td>Total : </td>" +
					"<td>" +
					"{TOTAL_COMMANDE}" +
					"</td>" +
				"</tr>" +
			"</table>" +
			"<div class=\"ui-helper-hidden\">" +
				"<form id=\"form-confirmation-reservation-commande\">" +
					"<table>" +
						"<tr>" +
							"<td>" +
							"<input type=\"text\" name=\"id_commande\" value=\"{ID_COMMANDE}\" />" +
							"</td>" +
							"</tr>" +
						"<!-- BEGIN info_produit -->" +
						"<tr>" +
							"<td><input type=\"text\" name=\"id_pdt[]\" value=\"{info_produit.IDPDT}\" /></td>" +
							"<td><input type=\"text\" name=\"id_lot[]\" value=\"{info_produit.IDLOT}\" /></td>" +
							"<td><input type=\"text\" name=\"qte[]\" value=\"{info_produit.QTE}\" /></td>" +
						"</tr>" +
						"<!-- END info_produit -->" +								
					"</table>" +
				"</form>" +
			"</div>"+
		"</div>";
	
	this.reservationOk = 
		"<div id=\"contenu\">" +
			"<div class=\"com-widget-window ui-widget ui-widget-content ui-widget-content-transparent ui-corner-all\">" +
				"<div class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">Réservation</div>" +
				"<div class=\"com-widget-content\">" +
					"<p class=\"com-msg-confirm-icon\"><span class=\"com-float-left ui-icon ui-icon-check\"></span>Réservation effectuée avec succés.</p>" +
				"</div>" +
			"</div>" +
		"</div>";

	this.MonMarcheDebut =
		"<div id=\"contenu\">";
	
	this.listeMarche = 
		"<div id=\"liste_commande_int\">" +
			"<div class=\"com-widget-window ui-widget ui-widget-content ui-widget-content-transparent ui-corner-all\">" +
				"<div class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">Les Marchés</div>" +
					"<table class=\"com-table\">" +
						"<tr class=\"ui-widget ui-widget-header\">" +
							"<th class=\"com-table-th-debut com-center\" colspan=\"2\">N°</th>" +
							"<th class=\"com-table-th-med\">Date de cloture des Réservations</th>" +
							"<th class=\"com-table-th-med\">Marché</th>	" +
							"<th class=\"com-table-th-fin\"></th>" +
						"</tr>" +
						"<!-- BEGIN marche -->" +
						"<tr >" +
							"<td class=\"com-table-td-debut com-text-align-right lst-resa-th-num\">{marche.numero}</td>" +
							"<td class=\"com-table-td-med lst-resa-td-nom\">{marche.nom}</td>" +
							"<td class=\"com-table-td-med\">Le {marche.jourFinReservation} {marche.dateFinReservation} à {marche.heureFinReservation}H{marche.minuteFinReservation}</td>" +
							"<td class=\"com-table-td-med\">Le {marche.jourMarcheDebut} {marche.dateMarcheDebut} de {marche.heureMarcheDebut}H{marche.minuteMarcheDebut} à {marche.heureMarcheFin}H{marche.minuteMarcheFin}</td>" +
							"<td class=\"com-table-td-fin lst-resa-btn-commander\">" +
								"<button class=\"btn-commander ui-state-default ui-corner-all com-button com-center\" id=\"{marche.id}\">Réservation</button>" +
							"</td>" +
						"</tr>" +
						"<!-- END marche -->" +
					"</table>" +
				"</div>" +			
			"</div>" +
		"</div>";
	
	this.listeMarcheVide =
		"<div class=\"com-widget-window ui-widget ui-widget-content ui-widget-content-transparent ui-corner-all\">" +
			"<div class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">Les Marchés</div>" +
			"<p id=\"texte-liste-vide\">Aucun Marché en cours.</p>" +	
		"</div>";
	
	this.MonMarcheFin =
		"</div>";
	
	this.listeAchats = 
		"<div id=\"contenu\">" +
			"<div class=\"com-widget-window ui-widget ui-widget-content ui-widget-content-transparent ui-corner-all\">" +
				"<div class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">" +
					"Mes Achats" +
				"</div>" +
				"<table class=\"com-table\">" +
					"<tr class=\"ui-widget ui-widget-header\">" +
						"<th class=\"com-table-th-debut\">Marché</th>	" +
						"<th class=\"com-table-th-med com-center\" colspan=\"2\">N°</th>" +
						"<th class=\"com-table-th-fin liste-adh-th-solde\"></th>" +
					"</tr>" +
					"<!-- BEGIN achat -->" +
					"<tr class=\"com-cursor-pointer ligne-achat\" id=\"{achat.opeId}\" >" +
						"<td class=\"com-table-td-debut com-underline-hover\">Le {achat.jour} {achat.date}</td>" +
						"<td class=\"com-table-td-med com-underline-hover lst-resa-th-num com-text-align-right\">{achat.numero}</td>" +
						"<td class=\"com-table-td-med com-underline-hover lst-resa-td-nom\">{achat.nom}</td>" +
						"<td class=\"com-table-td-fin com-underline-hover liste-adh-td-solde\">" +
							"<span class=\"com-cursor-pointer com-btn-header-multiples ui-widget-content ui-corner-all\">" +
								"<span class=\"ui-icon ui-icon-triangle-1-e\"></span>" +
							"</span>" +
						"</td>" +
					"</tr>" +
					"<!-- END achat -->" +
				"</table>" +	
			"</div>" +
		"</div>";
	
	this.listeAchatVide =
		"<div id=\"contenu\">" +
			"<div class=\"com-widget-window ui-widget ui-widget-content ui-widget-content-transparent ui-corner-all\">" +
				"<div class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">Mes Achats</div>" +
				"<p id=\"texte-liste-vide\">Aucun achat effectué.</p>" +		
			"</div>" +
		"</div>";
	
	this.detailAchat = 
		"<div id=\"contenu\">" +
			"<div class=\"achat com-widget-window ui-widget ui-widget-content ui-widget-content-transparent ui-corner-all\">" +
				"<div class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">" +
					"Achat du {dateAchat} : {totalMarche} {sigleMonetaire}" +
				"</div>" +
				"<table class=\"com-table-100\">" +
					"<tr>" +
						"<td></td>" +
						"<td colspan=\"3\"><div class=\"ui-widget-header ui-corner-all com-center\">Achat</div></td>" +
						"<td colspan=\"3\"><div class=\"ui-widget-header ui-corner-all com-center\">Achat Solidaire</div></td>" +
					"</tr>" +
				"<!-- BEGIN categories -->" +
					"<tr>" +
						"<td><div class=\"ui-widget-header ui-corner-all com-center\">{categories.nom}</div></td>" +
						"<td colspan=\"6\"></td>" +
					"</tr>" +
					"<!-- BEGIN categories.achat -->" +
					"<tr class=\"com-ligne-hover\">" +
						"<td class=\"detail-achat-npro\">{categories.achat.nproNom}</td>" +
						
						"<td class=\"com-text-align-right detail-achat-qte\">{categories.achat.stoQuantite}</td>" +						
						"<td class=\"detail-achat-unite\">{categories.achat.proUniteMesure}</td>" +
						"<td class=\"com-text-align-right detail-achat-prix\">{categories.achat.prix} {categories.achat.sigleMonetaire}</td>" +
						
						"<td class=\"com-text-align-right detail-achat-qte\">{categories.achat.stoQuantiteSolidaire}</td>" +						
						"<td class=\"detail-achat-unite\">{categories.achat.proUniteMesureSolidaire}</td>" +
						"<td class=\"com-text-align-right detail-achat-prix\">{categories.achat.prixSolidaire} {categories.achat.sigleMonetaireSolidaire}</td>" +
					"</tr>" +
					"<!-- END categories.achat -->" +
				"<!-- END categories -->" +
					"<tr>" +
						"<td class=\"com-text-align-right\" colspan=\"3\">Total : </td>" +
						"<td class=\"com-text-align-right detail-achat-prix\">{total} {sigleMonetaire}</td>" +
						"<td class=\"com-text-align-right\" colspan=\"2\">Total Solidaire : </td>" +
						"<td class=\"com-text-align-right detail-achat-prix\">{totalSolidaire} {sigleMonetaire}</td>" +
					"</tr>" +
				"</table>" +
			"</div>" +
		"</div>";
	
	this.dialogInfoProduit = 
		"<div id=\"dialog-info-pro\" title=\"Produit\">" +
			"<div id=\"information-detail-producteur\">" +
				"<div class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">Informations</div>" +
				"<div class=\"com-widget-content\">" +
					"<table class=\"com-table-form\">" +
						"<tr>" +
							"<th class=\"com-table-form-th\">Nom : </th>" +
							"<td class=\"com-table-form-td\">{nom}</td>" +
						"</tr>" +
						"<tr>" +
							"<th class=\"com-table-form-th\">Catégorie : </th>" +
							"<td class=\"com-table-form-td\">{cproNom}</td>" +
						"</tr>" +
						"<tr>" +
							"<th class=\"com-table-form-th\">Description : </th>" +
							"<td class=\"com-table-form-td\">{description}</td>" +
						"</tr>" +
					"</table>" +
				"</div>" +
			"</div>" +
			
			"<div id=\"pro-prdt\">" +
				"<div class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">Producteurs</div>" +
				"<table class=\"com-table-form\">" +
					"<!-- BEGIN producteurs -->" +
					"<tr>" +
						"<td class=\"com-table-form-td\">" +
							"{producteurs.prdtPrenom} {producteurs.prdtNom}" +
						"</td>" +
					"</tr>" +
					"<!-- END producteurs -->" +
				"</table>" +
			"</div>" +
			
			"<div id=\"pro-car\">" +
				"<div class=\"com-widget-header ui-widget ui-widget-header ui-corner-all\">Caractéristiques</div>" +
				"<table class=\"com-table-form\">" +
					"<!-- BEGIN caracteristiques -->" +
					"<tr>" +
						"<td class=\"com-table-form-td\">" +
							"{caracteristiques.carNom}" +
						"</td>" +
					"</tr>" +
					"<!-- END caracteristiques -->" +
				"</table>" +
			"</div>" +
		"</div>";
}