;function ReservationAdherentVue(pParam) {
	this.mAdherent = null;
	this.infoCommande = new Object();
	this.pdtCommande = new Array();
	this.reservation = new Array();
	this.reservationModif = new Array();
	this.solde = 0;
	this.soldeNv = 0;
	this.mPremiereReservation = true;
	this.mEtatReservation = null;
	this.retour = null;
	
	this.construct = function(pParam) {
		$.history( {'vue':function() {ReservationAdherentVue(pParam);}} );
		var that = this;
		pParam.fonction = "afficherReservation";
		if(pParam.retour) {
			this.retour = pParam.retour;
		}
		$.post(	"./index.php?m=GestionCommande&v=ReservationAdherent", "pParam=" + $.toJSON(pParam),
				function(lResponse) {
					Infobulle.init(); // Supprime les erreurs
					if(lResponse) {
						if(lResponse.valid) {
							if(pParam && pParam.vr) {
								Infobulle.generer(pParam.vr,'');
							}
							that.mAdherent = lResponse.adherent;
							
							that.infoCommande.comId = lResponse.marche.id;
							that.infoCommande.comNumero = lResponse.marche.numero;
							that.infoCommande.comNom = lResponse.marche.nom;
							that.infoCommande.comDescription = lResponse.marche.description;
							that.infoCommande.dateTimeFinReservation = lResponse.marche.dateFinReservation;
							that.infoCommande.dateFinReservation = lResponse.marche.dateFinReservation.extractDbDate().dateDbToFr();
							that.infoCommande.heureFinReservation = lResponse.marche.dateFinReservation.extractDbHeure();
							that.infoCommande.minuteFinReservation = lResponse.marche.dateFinReservation.extractDbMinute();
							that.infoCommande.dateMarcheDebut = lResponse.marche.dateMarcheDebut.extractDbDate().dateDbToFr();
							that.infoCommande.heureMarcheDebut = lResponse.marche.dateMarcheDebut.extractDbHeure();
							that.infoCommande.minuteMarcheDebut = lResponse.marche.dateMarcheDebut.extractDbMinute();
							that.infoCommande.heureMarcheFin = lResponse.marche.dateMarcheFin.extractDbHeure();
							that.infoCommande.minuteMarcheFin = lResponse.marche.dateMarcheFin.extractDbMinute();
							that.infoCommande.comArchive = lResponse.marche.archive;
							
							that.pdtCommande = lResponse.marche.produits;
							$.each(that.pdtCommande,function() {
								if(this.id) {
									var lIdProduit = this.id;
									$.each(this.lots, function() {
										if(this.id) {
											var lIdLot = this.id;
											$(lResponse.reservation).each(function() {
												if(this.idDetailCommande == lIdLot) {
													this.stoQuantite = this.quantite * -1;
													this.dcomId = this.idDetailCommande;
													this.proId = lIdProduit;
													that.reservation[lIdProduit] = this;
												}											
											});
										}
									});
								}
							});
							
							that.mEtatReservation = lResponse.etat;
							
							if(lResponse.reservation.length > 0)  {
								that.mPremiereReservation = false;
								that.afficher(1);								
							} else {
								that.afficher(2);					
							}
						} else {
							Infobulle.generer(lResponse,'');
						}
					}
				},"json"
		);
	};
	
	this.afficher = function(pType) {		
		if(pType == 1) {
			this.afficherDetailReservation();
		} else {
			this.afficherModifier();
		}	
	};
	
	this.afficherDetailReservation = function() {
		var that = this;
		var lGestionCommandeTemplate = new GestionCommandeTemplate();
		var lTemplate = lGestionCommandeTemplate.detailReservation;
		
		var lData = {};		
		lData.adhId = this.mAdherent.adhId;
		lData.adhNumero = this.mAdherent.adhNumero;
		lData.adhCompte = this.mAdherent.cptLabel;
		lData.adhNom = this.mAdherent.adhNom;
		lData.adhPrenom = this.mAdherent.adhPrenom;
		lData.adhSolde = this.mAdherent.cptSolde.nombreFormate(2,',',' ');
		lData.classSoldeActuel = "";
		if(this.mAdherent.cptSolde <= 0) {
			lData.classSoldeActuel ="com-nombre-negatif";
		}
		lData.sigleMonetaire = gSigleMonetaire;
		lData.comNumero = this.infoCommande.comNumero;
		lData.dateFinReservation = this.infoCommande.dateFinReservation;
		lData.heureFinReservation = this.infoCommande.heureFinReservation;
		lData.minuteFinReservation = this.infoCommande.minuteFinReservation;
		lData.dateMarcheDebut = this.infoCommande.dateMarcheDebut;
		lData.heureMarcheDebut = this.infoCommande.heureMarcheDebut;
		lData.minuteMarcheDebut = this.infoCommande.minuteMarcheDebut;
		lData.heureMarcheFin = this.infoCommande.heureMarcheFin;
		lData.minuteMarcheFin = this.infoCommande.minuteMarcheFin;
		//lData.categories = [];
		
		var lIdCategorie = 0;
		var lNomCategorie = '';
		var lCategoriesTrie = [];
		var lProduits = [];
		
		var lTotal = 0;
		$.each(this.pdtCommande, function() {
			var lInfoProduit = this;
			if(that.reservation[this.id]) {
				if(lIdCategorie == 0) {
					lIdCategorie = this.idCategorie;
					lNomCategorie = this.cproNom;
				}
				
				var lPdt = new Object;
				lPdt.proId = lInfoProduit.id;
				lPdt.nproNom = this.nom;
				lPdt.stoQuantite = parseFloat(that.reservation[this.id].stoQuantite);
				lPdt.proUniteMesure = this.unite;
				lPdt.prix = 0;
				var lDcomId = that.reservation[this.id].dcomId;
				$.each(this.lots, function() {
					if(this.id == lDcomId) {
						lPdt.prix = (lPdt.stoQuantite / this.taille) * this.prix;
					}
				});
				lTotal += lPdt.prix;
				
				lPdt.stoQuantite = lPdt.stoQuantite.nombreFormate(2,',',' ');		
				lPdt.prix = lPdt.prix.nombreFormate(2,',',' ');
				
				/*if(!lData.categories[this.idCategorie]) {
					lData.categories[this.idCategorie] = {nom:this.cproNom,produits:[]};
				}*/
				
				lPdt.flagType = "";
				if(lInfoProduit.type == 2) {
					lPdt.flagType = lGestionCommandeTemplate.flagAbonnement;
				}
				
				//lData.categories[this.idCategorie].produits.push(lPdt);
				
				if(lIdCategorie != this.idCategorie) {
					lCategoriesTrie.push({
							nom:lNomCategorie,
							produits:lProduits
						});			
					lIdCategorie = this.idCategorie;
					lNomCategorie = this.cproNom;
					lProduits = [];
				} 
				lProduits.push(lPdt);
			}			
		});
		lData.total = parseFloat(lTotal).nombreFormate(2,',',' ');
		
		// Ajout de la dernière catégorie
		lCategoriesTrie.push({
			nom:lNomCategorie,
			produits:lProduits
		});	
		
		lData.categories = lCategoriesTrie;
		
		
		$('#contenu').replaceWith(that.affect($(lTemplate.template(lData))));		
	};
	
	this.afficherModifier = function() {
		var that = this;
		var lGestionCommandeTemplate = new GestionCommandeTemplate();
		var lTemplate = lGestionCommandeTemplate.modifierReservation;
		var lData = {};
		lData.adhId = this.mAdherent.adhId;
		lData.adhNumero = this.mAdherent.adhNumero;
		lData.adhCompte = this.mAdherent.cptLabel;
		lData.adhNom = this.mAdherent.adhNom;
		lData.adhPrenom = this.mAdherent.adhPrenom;
		lData.adhSolde = this.mAdherent.cptSolde.nombreFormate(2,',',' ');
		lData.classSoldeActuel = "";
		if(this.mAdherent.cptSolde <= 0) {
			lData.classSoldeActuel ="com-nombre-negatif";
		}		
		lData.adhNouveauSolde = 0;
		lData.sigleMonetaire = gSigleMonetaire;
		lData.comNumero = this.infoCommande.comNumero;
		lData.dateFinReservation = this.infoCommande.dateFinReservation;
		lData.heureFinReservation = this.infoCommande.heureFinReservation;
		lData.minuteFinReservation = this.infoCommande.minuteFinReservation;
		lData.dateMarcheDebut = this.infoCommande.dateMarcheDebut;
		lData.heureMarcheDebut = this.infoCommande.heureMarcheDebut;
		lData.minuteMarcheDebut = this.infoCommande.minuteMarcheDebut;
		lData.heureMarcheFin = this.infoCommande.heureMarcheFin;
		lData.minuteMarcheFin = this.infoCommande.minuteMarcheFin;
		//lData.categories = [];
		
		var lIdCategorie = 0;
		var lNomCategorie = '';
		var lCategoriesTrie = [];
		var lProduits = [];
		
		var lTotal = 0;		
		$.each(this.pdtCommande, function() {
			// Test si la ligne n'est pas vide
			if(this.id) {
				if(lIdCategorie == 0) {
					lIdCategorie = this.idCategorie;
					lNomCategorie = this.cproNom;
				}
				
				var lPdt = {};
				lPdt.proId = this.id;
				lPdt.nproNom = this.nom;
				lPdt.proUniteMesure = this.unite;
				lPdt.cproNom = this.cproNom;
				lPdt.proMaxProduitCommande = parseFloat(this.qteMaxCommande);
				lPdt.flagType = "";
				
				// Recherche de la quantité reservée pour la déduire de la quantité max
				if(that.reservation[this.id]) {
					lPdt.stock = parseFloat(this.stockReservation) + parseFloat(that.reservation[this.id].stoQuantite);						
				} else {
					lPdt.stock = parseFloat(this.stockReservation);
				}
				
				/*if(parseFloat(lPdt.proMaxProduitCommande) < parseFloat(lPdt.stock)) {
					lPdt.max = lPdt.proMaxProduitCommande;
				} else {
					lPdt.max = lPdt.stock;
				}*/
				
				lPdt.lot = new Array();
				
				var lNoStock = false;
				if(parseFloat(this.qteMaxCommande) == -1 && parseFloat(this.stockInitial) == -1) { // Si ni stock ni qmax
					lNoStock = true;
				} else if(parseFloat(this.stockInitial) == -1) { // Si qmax mais pas stock
					lPdt.max = lPdt.proMaxProduitCommande;
				} else if(parseFloat(this.qteMaxCommande) == -1) { // Si stock mais pas qmax
					lPdt.max = lPdt.stock;
				} else { // Si stock et qmax
					if(parseFloat(lPdt.proMaxProduitCommande) < parseFloat(lPdt.stock)) {
						lPdt.max = lPdt.proMaxProduitCommande;
					} else {
						lPdt.max = lPdt.stock;
					}					
				}
				
			//	var i = 0;
				var lLotReservation = -1;
			//	var lLotInit = -1;
				
				lPdt.stoQuantiteReservation = 0;
				lPdt.prixReservation = 0;
				
				$.each(this.lots, function() {
					if(this.id) {
						if(lNoStock || (!lNoStock && parseFloat(this.taille) <= lPdt.max) ) {
							var lLot = {};
							lLot.dcomId = this.id;
							lLot.dcomTaille = parseFloat(this.taille).nombreFormate(2,',',' ');
							lLot.dcomPrix = parseFloat(this.prix).nombreFormate(2,',',' ');
							lLot.prixReservation = parseFloat(this.prix);
							lLot.stoQuantiteReservation = parseFloat(this.taille);
							
							if(that.reservation[lPdt.proId] && (that.reservation[lPdt.proId].dcomId == this.id)) {
									lLot.stoQuantiteReservation = parseFloat(that.reservation[lPdt.proId].stoQuantite);
									lLot.prixReservation = (lLot.stoQuantiteReservation / this.taille) * this.prix;
									lTotal += lLot.prixReservation;
									
									// Permet de cocher le lot correspondant à la résa
									lLotReservation = this.id;
									lLot.checked = 'checked="checked"';
									
									lPdt.stoQuantiteReservation = lLot.stoQuantiteReservation.nombreFormate(2,',',' ');
									lPdt.prixReservation = lLot.prixReservation.nombreFormate(2,',',' ');
							}
							
							if( lPdt.prixReservation == 0) {
								lPdt.stoQuantiteReservation = lLot.stoQuantiteReservation.nombreFormate(2,',',' ');
								lPdt.prixReservation = lLot.prixReservation.nombreFormate(2,',',' ');
							}
							
							lPdt.prixUnitaire = (lLot.prixReservation / lLot.stoQuantiteReservation).nombreFormate(2,',',' '); 						
														
							lPdt.lot.push(lLot);
						}
					}
				});
				
				lData.total = parseFloat(lTotal).nombreFormate(2,',',' ');
				
				// Si il y a une réservation pour ce produit on le coche
				if(lLotReservation != -1) {
					lPdt.checked = 'checked="checked"';
				} else {
					lPdt.checked = '';
				}
				
				/*if(lPdt.lot.length == 0) {		
					lPdt.checked = 'rel="indisponible"';
				}*/
				//lData.produit.push(lPdt);
				/*if(!lData.categories[this.idCategorie]) {
					lData.categories[this.idCategorie] = {nom:this.cproNom,produits:[]};
				}
				lData.categories[this.idCategorie].produits.push(lPdt);*/
				
				/*if(!lData.categories[this.idCategorie]) {
					lData.categories[this.idCategorie] = {nom:this.cproNom,produits:[]};
				}*/
				
				lPdt.sigleMonetaire = gSigleMonetaire;
				var lAjoutProduit = true;
				if(this.type == 0 ) {
					if(lPdt.lot.length == 0) {
						lPdt.nom = this.nom;
						lPdt.detailProduit = lGestionCommandeTemplate.produitIndisponible.template(lPdt);
					} else {
						lPdt.detailProduit = lGestionCommandeTemplate.formReservationProduit.template(lPdt);
					}
				} else if(this.type == 1 ) {
					lPdt.flagType = lGestionCommandeTemplate.flagSolidaire;
					lPdt.detailProduit = lGestionCommandeTemplate.formReservationProduitInfo.template(lPdt);
				} else if(this.type == 2 ) {
					lPdt.flagType = lGestionCommandeTemplate.flagAbonnement;					
					if(lPdt.lot.length == 0) { // L'admin peut modifier les réservations abonnement
						lAjoutProduit = false;
					} else {
						lPdt.detailProduit = lGestionCommandeTemplate.formReservationProduit.template(lPdt);
					}		
				}
				
				if(lAjoutProduit) {
					//lData.categories[this.idCategorie].produits.push(lPdt);
					/*if(lIdCategorie != this.idCategorie) {
						lCategoriesTrie.push({
								nom:lNomCategorie,
								produits:lProduits
							});			
						lIdCategorie = this.idCategorie;
						lNomCategorie = this.cproNom;
						lProduits = [];
					} */
					lProduits.push(lPdt);
				}
			}
		});
		
		// Ajout de la dernière catégorie
		lCategoriesTrie.push({
			nom:lNomCategorie,
			produits:lProduits
		});	
		
		lData.categories = lCategoriesTrie;
		lData.produits = lProduits;
		
		// Maj des reservations temp pour modif
		this.reservationModif = [];
		$(this.reservation).each(function() {
			if(this.proId) {
					that.reservationModif[this.proId] = {
						proId:this.proId,
						dcomId:this.dcomId,
						stoQuantite:this.stoQuantite
						};
			}
		});
		
		$('#contenu').replaceWith(that.affectModifier($(lTemplate.template(lData))));
		this.majNouveauSolde();
	};
	
	this.affectDroitArchive = function(pData) {
		pData.find(".boutons-edition").remove();
		pData.find(".boutons-edition-modification").remove();
		return pData;
	};
	
	this.affect = function(pData) {
		pData = this.affectModifierReservation(pData);
		pData = this.affectAnnulerDetailReservation(pData);
		pData = this.affectSupprimerReservation(pData);		
		pData = this.affectInfoProduit(pData);
		pData = gCommunVue.comHoverBtn(pData);
		// Si le marché est archivé on ne peut plus faire de modification
		// Si la réservation est achetée on ne peut plus faire de modification
		if(this.infoCommande.comArchive == 2 || this.mEtatReservation == 22 ) { 
			pData = this.affectDroitArchive(pData);
		}
		return pData;
	};
	
	this.affectDataTable = function(pData) {
		pData.find('#table-form-produit')
		.dataTable({								
				"bJQueryUI": true,
		        "oLanguage": gDataTablesFr,
		        "bPaginate": false,
		        "aaSorting": [[2,'asc']],
				"aoColumnDefs": [
								{ 	"aTargets": [ 0 ] ,
									"bSortable": false, 
									"bSearchable":false,
									"bVisible":false
								  },
								  { 	"aTargets": [ 1,3,4,5,6,7,8,9 ] ,
										"bSortable": false, 
										"bSearchable":false
								  }]
		}).rowGrouping({	
			iGroupingColumnIndex: 0,
			sGroupingClass:"ui-widget-header"
		});		
		return pData;
	};
		
	this.affectModifier = function(pData) {
		pData = this.affectBtnQte(pData);
		pData = this.affectChangementLot(pData);
		pData = this.affectChangementProduit(pData);
		pData = this.preparerAffichageModifier(pData);
		pData = this.affectValiderReservation(pData);
		pData = this.affectAnnulerReservation(pData);	
		pData = this.supprimerSelect(pData);	
		pData = this.affectNouveauSolde(pData);
		pData = this.affectInitLot(pData);
		pData = this.affectInfoProduit(pData);
		pData = gCommunVue.comHoverBtn(pData);
		if(this.infoCommande.comArchive == 2) { // Si le marché est archivé on ne peut plus faide de modification
			pData = this.affectDroitArchive(pData);
		}
		pData = this.affectDataTable(pData);
		return pData;
	};
	
	this.affectInfoProduit = function(pData) {
		//var that = this;
		pData.find('.btn-info-produit')
		.click(function() {		
			var lId = $(this).attr('id-produit');
			var lParam = {id:lId,fonction:"detailProduit"};
			$.post(	"./index.php?m=GestionCommande&v=ReservationAdherent", "pParam=" + $.toJSON(lParam),
					function(lResponse) {
						Infobulle.init(); // Supprime les erreurs
						if(lResponse) {
							if(lResponse.valid) {
								var lGestionCommandeTemplate = new GestionCommandeTemplate();
								var lTemplate = lGestionCommandeTemplate.dialogInfoProduit;
								
								lResponse.produit.sigleMonetaire = gSigleMonetaire;
								
								var lHtml = $(lTemplate.template(lResponse.produit));
								
								if(lResponse.produit.producteurs.length > 0 && lResponse.produit.producteurs[0].nPrdtIdNomProduit == null) {
									lHtml.find('#pro-prdt').remove();
								}
								if(lResponse.produit.caracteristiques.length > 0 && lResponse.produit.caracteristiques[0].carProIdNomProduit == null) {
									lHtml.find('#pro-car').remove();
								}
								
								$(lHtml).dialog({			
									autoOpen: true,
									modal: true,
									draggable: true,
									resizable: false,
									width:600,
									close: function(ev, ui) { $(this).remove(); Infobulle.init(); }				
								});								
							} else {
								Infobulle.generer(lResponse,'');
							}
						}
					},"json"
			);
			
		});
		return pData;
	};
	
	this.affectBtnQte = function(pData) {
		var that = this;
		pData.find('.btn-plus').click(function() {
			Infobulle.init(); // Supprime les erreurs
			//var lIdPdt = $(this).parent().parent().find(".pdt-id").text();
			var lIdPdt = $(this).data("id-produit");
			that.nouvelleQuantite( lIdPdt, $('#lot-' + lIdPdt).val(), 1);
		});	
		pData.find('.btn-moins').click(function() {
			Infobulle.init(); // Supprime les erreurs
			//var lIdPdt = $(this).parent().parent().find(".pdt-id").text();
			var lIdPdt = $(this).data("id-produit");
			that.nouvelleQuantite(lIdPdt,$('#lot-' + lIdPdt).val(),-1);
		});
		return pData;		
	};
	
	this.affectChangementLot = function(pData) {
		var that = this;
		pData.find('.pdt select').change(function() {
			Infobulle.init(); // Supprime les erreurs
			that.changerLot($(this).data("id-produit"),$(this).val());
		});
		return pData;
	};
	
	this.affectChangementProduit = function(pData) {
		var that = this;
		pData.find('.pdt :checkbox').click(function() {
			Infobulle.init(); // Supprime les erreurs
			that.changerProduit($(this).data("id-produit"));			
		});
		return pData;
	};
	
	this.affectValiderReservation = function(pData) {
		var that = this;
		pData.find('#btn-valider').click(function() {
			that.validerReservation();			
		});
		return pData;	
	};
	
	this.affectAnnulerReservation = function(pData) {
		var that = this;
		pData.find('#btn-annuler').click(function() {
			if(that.mPremiereReservation) {
				that.traiterRetour();
			} else {
				that.afficherDetailReservation();		
			}
		});
		return pData;
	};
	
	this.affectModifierReservation = function(pData) {
		var that = this;
		pData.find('#btn-modifier').click(function() {
			that.afficherModifier();		
		});
		return pData;
	};
	
	this.affectAnnulerDetailReservation = function(pData) {
		var that = this;
		pData.find('#btn-annuler').click(function() {
			that.traiterRetour();
		});
		return pData;
	};	
	
	this.affectInitLot = function(pData) {
		var that = this;
		pData.find('.pdt select').each(function() {
			var lIdPdt = $(this).data("id-produit");;
			var lIdLot = $(this).val();
			
			if(that.pdtCommande[lIdPdt] && that.pdtCommande[lIdPdt].lots[lIdLot]) {
				var lPrix = that.pdtCommande[lIdPdt].lots[lIdLot].prix;
				var lQte = that.pdtCommande[lIdPdt].lots[lIdLot].taille;
				var lprixUnitaire = (lPrix / lQte).nombreFormate(2,',',' '); 						
				
				$(pData).find('#prix-unitaire-' + lIdPdt).text(lprixUnitaire);
			}
		});
		return pData;
	};
	
	this.nouvelleQuantite = function(pIdPdt,pIdLot,pIncrement) {		
		// La quantité max soit qte max soit stock
		var lMax = parseFloat(this.pdtCommande[pIdPdt].qteMaxCommande);
		
		// Recherche de la quantité reservée pour la déduire de la quantité max
		var lStock = 0;
		if(this.reservation[pIdPdt]) {
			lStock = parseFloat(this.pdtCommande[pIdPdt].stockReservation) + parseFloat(this.reservation[pIdPdt].stoQuantite);						
		} else {
			lStock = parseFloat(this.pdtCommande[pIdPdt].stockReservation);
		}
		
		var lNoStock = false;
		if(parseFloat(this.pdtCommande[pIdPdt].qteMaxCommande) == -1 && parseFloat(this.pdtCommande[pIdPdt].stockInitial) == -1) { // Si ni stock ni qmax
			lNoStock = true;
		} else if(parseFloat(this.pdtCommande[pIdPdt].stockInitial) == -1) { // Si qmax mais pas stock
			lMax = this.pdtCommande[pIdPdt].qteMaxCommande;
		} else if(parseFloat(this.pdtCommande[pIdPdt].qteMaxCommande) == -1) { // Si stock mais pas qmax
			lMax = lStock;
		} else { // Si stock et qmax
			if(parseFloat(lStock) < parseFloat(lMax)) { lMax = lStock; }				
		}
		
		var lTaille = this.pdtCommande[pIdPdt].lots[pIdLot].taille;
		var lPrix = this.pdtCommande[pIdPdt].lots[pIdLot].prix;

		// Récupère le nombre de lot réservé
		var lQteReservation = 0;
		if(this.reservationModif[pIdPdt] && (this.reservationModif[pIdPdt].dcomId == pIdLot)) {
			lQteReservation = parseFloat(this.reservationModif[pIdPdt].stoQuantite)/parseFloat(lTaille);
		}
		lQteReservation += pIncrement;
		
		var lNvQteReservation = 0;		
		lNvQteReservation = lQteReservation * lTaille;
		
		// Test si la quantité est dans les limites
		if((lNoStock && lNvQteReservation > 0) || (!lNoStock && lNvQteReservation > 0 && lNvQteReservation <= lMax)) {
			var lNvPrix = 0;
			lNvPrix = (lQteReservation * lPrix).toFixed(2);

			// Mise à jour de la quantite reservée
			this.reservationModif[pIdPdt].stoQuantite = lNvQteReservation;			
			
			$('#qte-pdt-' + pIdPdt).text(parseFloat(lNvQteReservation).nombreFormate(2,',',' '));
			$('#prix-pdt-' + pIdPdt).text(parseFloat(lNvPrix).nombreFormate(2,',',' '));		

			this.majTotal();
		} else if(lNvQteReservation > lMax && lMax != -1) {			
			var lVr = new TemplateVR();
			lVr.valid = false;
			lVr.commandes = [];
			              
			var lProduit = new ReservationCommandeVR();              
			lProduit.valid = false;
			lProduit.stoQuantite.valid = false;
			var erreur = new VRerreur();
			erreur.code = ERR_304_CODE;
			erreur.message = ERR_304_MSG;
			lProduit.stoQuantite.erreurs.push(erreur);		
			lVr.commandes[pIdPdt] = lProduit;
			
			Infobulle.generer(lVr,'');
		}		
	};
	
	this.changerLot = function(pIdPdt,pIdLot) {
		var lPrix = this.pdtCommande[pIdPdt].lots[pIdLot].prix;
		var lQte = this.pdtCommande[pIdPdt].lots[pIdLot].taille;
		var lprixUnitaire = (lPrix / lQte).nombreFormate(2,',',' '); 						

		$('#prix-unitaire-' + pIdPdt).text(lprixUnitaire);
		
		if(this.reservationModif[pIdPdt]) {
			this.reservationModif[pIdPdt].dcomId = pIdLot;
			this.reservationModif[pIdPdt].stoQuantite = lQte;
			$('#qte-pdt-' + pIdPdt).text(lQte.nombreFormate(2,',',' '));
			$('#prix-pdt-' + pIdPdt).text(lPrix.nombreFormate(2,',',' '));
		}
		
		this.majTotal();
	};
	
	this.changerProduit = function(pIdPdt) {
		if(this.reservationModif[pIdPdt] != null) {
			$('.resa-pdt-' + pIdPdt).hide();
			$('#qte-pdt-' + pIdPdt).text('');
			$('#prix-pdt-' + pIdPdt).text('');
			this.reservationModif[pIdPdt] = null;
		} else {
			var lIdLot = $('#lot-' + pIdPdt).val();
			var lQte = this.pdtCommande[pIdPdt].lots[lIdLot].taille;			
			
			var lResa = {};
			lResa.comId = this.infoCommande.comId;
			lResa.proId = pIdPdt;
			lResa.dcomId = lIdLot;
			lResa.stoQuantite = lQte;						
			this.reservationModif[pIdPdt] = lResa;
			
			$('#qte-pdt-' + pIdPdt).text(lQte.nombreFormate(2,',',' '));
			var lPrix = this.pdtCommande[pIdPdt].lots[lIdLot].prix.nombreFormate(2,',',' ');
			$('#prix-pdt-' + pIdPdt).text(lPrix);
			
			$('.resa-pdt-' + pIdPdt).show();
		}
		this.majTotal();
	};
	
	this.majTotal = function() {
		$('#total').text(this.calculTotal().nombreFormate(2,',',' '));
		this.majNouveauSolde();
	};
	
	this.majNouveauSolde = function() {
		var lNvSolde = this.mAdherent.cptSolde - this.calculTotal();
		if(lNvSolde <= 0) {
			$("#nouveau-solde").addClass("com-nombre-negatif");
			$("#nouveau-solde-sigle").addClass("com-nombre-negatif");			
		} else {
			$("#nouveau-solde").removeClass("com-nombre-negatif");
			$("#nouveau-solde-sigle").removeClass("com-nombre-negatif");
		}
		$("#nouveau-solde").text(lNvSolde.nombreFormate(2,',',' '));
	};
	
	this.affectNouveauSolde = function(pData) {
		var lNvSolde = this.mAdherent.cptSolde - this.calculTotal();
		if(lNvSolde <= 0) {
			pData.find("#nouveau-solde").addClass("com-nombre-negatif");
			pData.find("#nouveau-solde-sigle").addClass("com-nombre-negatif");			
		} else {
			pData.find("#nouveau-solde").removeClass("com-nombre-negatif");
			pData.find("#nouveau-solde-sigle").removeClass("com-nombre-negatif");
		}
		pData.find("#nouveau-solde").text(lNvSolde.nombreFormate(2,',',' '));
		return pData;		
	};
	
	this.calculTotal = function() {
		var that = this;
		var lTotal = 0;
		$(this.reservationModif).each(function() {
			var lResa = this;
			if(lResa.stoQuantite) {
				if(that.pdtCommande[lResa.proId]) {
					$.each(that.pdtCommande[lResa.proId].lots, function() {
						if(lResa.dcomId == this.id) {
							lTotal += (lResa.stoQuantite / this.taille) * this.prix;
						}
					});					
				}				
			}
		});
		return lTotal;
	};
	
	this.preparerAffichageModifier = function(pData) {
		var that = this;
		
		$(pData).find('.pdt').each(function() {
			var lIdPdt = $(this).data("id-produit");
			if(that.reservation[lIdPdt] != null) {
				var lResa = that.reservation[lIdPdt];
				var lIdLot = lResa.dcomId;
				var lQte = lResa.stoQuantite;			
				$(pData).find('#qte-pdt-' + lIdPdt).text(lQte.nombreFormate(2,',',' '));
				
				var lPrix = ((that.pdtCommande[lIdPdt].lots[lIdLot].prix * lResa.stoQuantite)/that.pdtCommande[lIdPdt].lots[lIdLot].taille).nombreFormate(2,',',' ');
				$(pData).find('#prix-pdt-' + lIdPdt).text(lPrix);
				$(pData).find('#lot-' + lIdPdt).selectOptions(lIdLot);
				
				//$(pData).find('.resa-pdt-' + lIdPdt).show();
				$(pData).find('.resa-pdt-' + lIdPdt).css("display","inline"); //Show ne fonctionne pas sur chrome
			}
		});
		return pData;
	};
	
	this.validerReservation = function() {
		var that = this;
		Infobulle.init(); // Supprime les erreurs
		
		var lVo = new ListeReservationCommandeVO();
		var lNbPdt = 0;
		$(this.reservationModif).each(function() {
			if(this.stoQuantite) {
				var lVoResa = new ReservationCommandeVO();
				lVoResa.stoQuantite = this.stoQuantite * -1;
				lVoResa.stoIdDetailCommande = this.dcomId;
				lVo.detailReservation.push(lVoResa);
				lNbPdt++;
			}
		});
		
		if(lNbPdt > 0){
			var lValid = new ListeReservationCommandeValid();
			var lVR = lValid.validAjout(lVo);
			if(!lVR.valid){
				Infobulle.generer(lVR,'');
			} else {
				// Maj de la reservation
				lVo.fonction = "modifierReservation";
				lVo.id_compte = this.mAdherent.adhIdCompte;
				lVo.id_commande = this.infoCommande.comId;
				//lParam = {"reservation":lVo,"id_compte":this.mAdherent.adhIdCompte,fonction:"modifierReservation"};
				$.post(	"./index.php?m=GestionCommande&v=ReservationAdherent", "pParam=" + $.toJSON(lVo),
					function(lResponse) {
						Infobulle.init(); // Supprime les erreurs
						if(lResponse) {
							if(lResponse.valid) {							
								// Maj des reservations pour le recap
								/*that.reservation = new Array();
								$(that.reservationModif).each(function() {
									if(this.proId) {									
										that.reservation[this.proId] = {comId:this.comId,proId:this.proId,dcomId:this.dcomId,stoId:this.stoId,stoQuantite:this.stoQuantite,stoType:this.stoType,stoIdCompte:this.stoIdCompte};
									}
								});
								that.afficher();*/
								// Maj des reservations pour le recap
								that.reservation = [];
								$(that.reservationModif).each(function() {
									if(this.proId) {
										that.reservation[this.proId] = {
												proId:this.proId,
												dcomId:this.dcomId,
												stoQuantite:this.stoQuantite
												};
									}
								});
								
								if(that.mPremiereReservation)  {
									that.mPremiereReservation = false;								
									// Message d'information du bon enregistrement
									var lVr = new TemplateVR();
									lVr.valid = false;
									lVr.log.valid = false;
									var erreur = new VRerreur();
									erreur.code = ERR_350_CODE;
									erreur.message = ERR_350_MSG;
									lVr.log.erreurs.push(erreur);	
									Infobulle.generer(lVr,'');
									
								} else {								
									// Message d'information de la bonne modification
									var lVr = new TemplateVR();
									lVr.valid = false;
									lVr.log.valid = false;
									var erreur = new VRerreur();
									erreur.code = ERR_337_CODE;
									erreur.message = ERR_337_MSG;
									lVr.log.erreurs.push(erreur);	
									Infobulle.generer(lVr,'');
								}
								
								that.afficher(1);
								
								
								
							} else {
								Infobulle.generer(lResponse,'');
							}
						}
					},"json"
				);	
			}			
		} else {
			var lVR = new TemplateVR();
			lVR.valid = false;lVR.log.valid = false;var erreur = new VRerreur();erreur.code = ERR_207_CODE;erreur.message = ERR_207_MSG;lVR.log.erreurs.push(erreur);
			Infobulle.generer(lVR,'');
		}		
	};
	
	this.affectSupprimerReservation = function(pData) {
		var that = this;
		pData.find('#btn-supprimer').click(function() {
			var lGestionCommandeTemplate = new GestionCommandeTemplate();
			var lTemplate = lGestionCommandeTemplate.supprimerReservationDialog;
			$(lTemplate).dialog({
				autoOpen: true,
				modal: true,
				draggable: false,
				resizable: false,
				width:600,
				buttons: {
					'Supprimer': function() {
						var lParam = {	id_commande:that.infoCommande.comId,
										id_compte:that.mAdherent.adhIdCompte,
										fonction:"supprimerReservation"};
						var lDialog = this;
						$.post(	"./index.php?m=GestionCommande&v=ReservationAdherent", "pParam=" + $.toJSON(lParam),
								function(lResponse) {
									Infobulle.init(); // Supprime les erreurs
									if(lResponse) {
										if(lResponse.valid) {
											
											var lVr = new TemplateVR();
											lVr.valid = false;
											lVr.log.valid = false;
											var erreur = new VRerreur();
											erreur.code = ERR_303_CODE;
											erreur.message = ERR_303_MSG;
											lVr.log.erreurs.push(erreur);							
	
											// Redirection vers la vue edition
											that.traiterRetour(lVr);
																						
											$(lDialog).dialog("close");
											
										} else {
											Infobulle.generer(lResponse,'');
										}
									}
								},"json"
						);
					},
					'Annuler': function() { $(this).dialog("close"); }
					},
				close: function(ev, ui) { $(this).remove(); }
			});
		});
		return pData;
	};
	
	this.supprimerSelect = function(pData) {
		pData.find('.pdt select').each(function() {
			if($(this).find('option').size() == 1) {				
				var lGestionCommandeTemplate = new GestionCommandeTemplate();
				var lTemplate = lGestionCommandeTemplate.lotUnique;
				var lData = {};
				lData.IdPdt = $(this).data("id-produit");
				lData.valeur = $(this).val();
				lData.text = $(this).text();
				
				$(this).replaceWith(lTemplate.template(lData));
			}
		});
		
		return pData;
	};
	
	this.traiterRetour = function(pVr) {
		var lParam = {id_marche:this.infoCommande.comId};
		if(pVr) {
			lParam.vr = pVr;
		}
		
		if(this.retour == 'noAchat') {
			ReservationSansAchatVue(lParam);
		} else {
			ListeReservationMarcheVue(lParam);
		}
	};
	
	this.construct(pParam);
}