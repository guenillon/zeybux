;function DetailAbonneAbonnementVue(pParam) {
	this.idCompte = 0;
	this.produit = {};
	this.abonnement = {};
	this.idAdherent = 0;
	this.mDateDebutSuspension = '';
	this.mDateFinSuspension = '';
	this.reservationModif = {};
	this.reservation = {};
	
	this.construct = function(pParam) {
		$.history( {'vue':function() {DetailAbonneAbonnementVue(pParam);}} );
		var that = this;		
		var lParam = {fonction:"detailAbonne"};
		lParam = $.extend(lParam,pParam);
		$.post(	"./index.php?m=GestionAbonnement&v=ListeAbonne", "pParam=" + toJsonURIEncode(lParam),
				function(lResponse) {
					Infobulle.init(); // Supprime les erreurs
					if(lResponse) {
						if(lResponse.valid) {
							if(pParam && pParam.vr) {
								Infobulle.generer(pParam.vr,'');
							}
							that.idAdherent = pParam.id;
							that.idCompte= lResponse.adherent.cptId;
							that.afficher(lResponse);
						} else {
							Infobulle.generer(lResponse,'');
						}
					}
				},"json"
		);
	};
	
	this.afficher = function(lResponse) {
		var that = this;
		var lGestionAbonnementTemplate = new GestionAbonnementTemplate();
		var lData = lResponse.adherent;
		var lDateDebutSuspension = gDateTimeNulle;
		var lDateFinSuspension = gDateTimeNulle;
		var lAujourdhui = getDateTimeAujourdhuiDb();
		
		if(lResponse.produits && lResponse.produits.fermes && lResponse.produits.fermes.length > 0 && lResponse.produits.fermes[0].nom != null) {
			lDateDebutSuspension = lResponse.produits.fermes[0].categories[0].produits[0].dateDebutSuspension;
			lDateFinSuspension = lResponse.produits.fermes[0].categories[0].produits[0].dateFinSuspension;
			
			$.each(lResponse.produits.fermes,function() {
				$.each(this.categories,function() {
					$.each(this.produits,function() {
						this.quantite = this.quantite.nombreFormate(2,',',' ');
					});
				});
			});
			
			lData.listeProduit = lGestionAbonnementTemplate.detailAbonneListeProduit.template(lResponse.produits);
		} else {
			lData.listeProduit = lGestionAbonnementTemplate.detailAbonneListeProduitVide;
		}
		
		if(dateTimeEstPLusGrandeEgale(lDateFinSuspension,lAujourdhui,'db')) { // Abonnement suspendu
			lData.dateDebutsuspension = lDateDebutSuspension.extractDbDate().dateDbToFr();
			lData.dateFinsuspension = lDateFinSuspension.extractDbDate().dateDbToFr();
			lData.suspension = lGestionAbonnementTemplate.buttonModifierSuspendre.template(lData);
			
			this.mDateDebutSuspension = lData.dateDebutsuspension;
			this.mDateFinSuspension = lData.dateFinsuspension;
		} else { // Abonnement en cours
			lData.suspension = lGestionAbonnementTemplate.buttonSuspendre;
		}
		
		$('#contenu').replaceWith(that.affect($(lGestionAbonnementTemplate.detailAbonne.template(lData))));
		
	};
	
	this.affect = function(pData) {
		pData = this.affectAjoutAbonnement(pData);
		pData = this.affectModifierAbonnement(pData);
		pData = this.affecSupprimerAbonnement(pData);
		pData = this.affectRetour(pData);
		pData = this.affectAjoutSuspension(pData);
		pData = this.affectModifierSuspension(pData);
		pData = this.affectSupprimerSuspension(pData);
		pData = gCommunVue.comHoverBtn(pData);
		return pData;
	};
	
	this.affectAjoutSuspension = function(pData) {		
		var that = this;
		pData.find('#btn-ajout-suspension').click(function() {
			that.dialogAjoutSuspension();
		});
		return pData;
	};
	
	this.dialogAjoutSuspension = function() {
		var that = this;
		var lGestionAbonnementTemplate = new GestionAbonnementTemplate();
		var lTemplate = lGestionAbonnementTemplate.dialogAjoutSuspension;
		
		$(that.affectDialogAjoutSuspension($(lTemplate))).dialog({			
			autoOpen: true,
			modal: true,
			draggable: true,
			resizable: false,
			width:900,
			buttons: {
				'Suspendre': function() {
					that.suspendreAbonnement($(this));
				},
				'Annuler': function() {
					$(this).dialog('close');
				}
			},
			close: function(ev, ui) { $(this).remove(); Infobulle.init(); }				
		});
	};
	
	this.affectDialogAjoutSuspension = function(pData) {
		pData = gCommunVue.comLienDatepicker('dateDebutSuspension','dateFinSuspension',pData);
		pData.find('#dateDebutSuspension').datepicker( "option", "yearRange", 'c:c+10' );
		pData.find('#dateFinSuspension').datepicker( "option", "yearRange", 'c:c+10' );
		return pData;
	};
	
	this.suspendreAbonnement = function(pDialog) {
		var that = this;
		
		var lCompteAbonnementVO = new CompteAbonnementVO();
		lCompteAbonnementVO.idCompte = this.idCompte;
		lCompteAbonnementVO.dateDebutSuspension = pDialog.find("#dateDebutSuspension").val().dateFrToDb();
		lCompteAbonnementVO.dateFinSuspension = pDialog.find("#dateFinSuspension").val().dateFrToDb();
		
		var lValid = new CompteAbonnementValid();
		var lVr = lValid.validAjoutSuspension(lCompteAbonnementVO);
		
		if(lVr.valid) {	
			Infobulle.init();
			lCompteAbonnementVO.fonction = "suspendre";
			$.post(	"./index.php?m=GestionAbonnement&v=ListeAbonne", "pParam=" + toJsonURIEncode(lCompteAbonnementVO),
				function (lResponse) {		
					if(lResponse) {
						if(lResponse.valid) {
							Infobulle.init(); // Supprime les erreurs
							var lVR = new Object();
							var erreur = new VRerreur();
							erreur.code = ERR_348_CODE;
							erreur.message = ERR_348_MSG;
							lVR.valid = false;
							lVR.log = new VRelement();
							lVR.log.valid = false;
							lVR.log.erreurs.push(erreur);
							
							that.construct({id:that.idAdherent,vr:lVR});
							pDialog.dialog('close');
						} else {
							Infobulle.generer(lResponse,'');
						}
					}
				},"json"
			);
		} else {
			Infobulle.generer(lVr,'');
		}
	};

	this.affectModifierSuspension = function(pData) {		
		var that = this;
		pData.find('#btn-modif-suspension').click(function() {
			that.dialogModifierSuspension();
		});
		return pData;		
	};	
	
	this.dialogModifierSuspension = function() {
		var that = this;
		var lGestionAbonnementTemplate = new GestionAbonnementTemplate();
		var lTemplate = lGestionAbonnementTemplate.dialogAjoutSuspension;
		
		var lData = {dateDebutSuspension:this.mDateDebutSuspension,dateFinSuspension:this.mDateFinSuspension};
		
		$(that.affectDialogAjoutSuspension($(lTemplate.template(lData)))).dialog({			
			autoOpen: true,
			modal: true,
			draggable: true,
			resizable: false,
			width:900,
			buttons: {
				'Suspendre': function() {
					that.suspendreAbonnement($(this));
				},
				'Annuler': function() {
					$(this).dialog('close');
				}
			},
			close: function(ev, ui) { $(this).remove(); Infobulle.init(); }				
		});
	};
	
	this.affectSupprimerSuspension = function(pData) {
		var that = this;
		pData.find('#btn-supp-suspension').click(function() {
			that.dialogSupprimerSuspension();
		});
		return pData;		
	};
	
	this.dialogSupprimerSuspension = function() {
		var that = this;
		var lGestionAbonnementTemplate = new GestionAbonnementTemplate();
		var lTemplate = lGestionAbonnementTemplate.dialogSupprimerSuspension;
		
		$(that.affectDialogAjoutSuspension($(lTemplate))).dialog({			
			autoOpen: true,
			modal: true,
			draggable: true,
			resizable: false,
			width:900,
			buttons: {
				'Supprimer': function() {					
					var lCompteAbonnementVO = new CompteAbonnementVO();
					lCompteAbonnementVO.idCompte = that.idCompte;
					
					var lValid = new CompteAbonnementValid();
					var lVr = lValid.validDeleteSuspension(lCompteAbonnementVO);
					
					if(lVr.valid) {	
						Infobulle.init();
						lCompteAbonnementVO.fonction = "arretSuspension";
						var lDialog = $(this);
						$.post(	"./index.php?m=GestionAbonnement&v=ListeAbonne", "pParam=" + toJsonURIEncode(lCompteAbonnementVO),
							function (lResponse) {		
								if(lResponse) {
									if(lResponse.valid) {
										Infobulle.init(); // Supprime les erreurs
										var lVR = new Object();
										var erreur = new VRerreur();
										erreur.code = ERR_349_CODE;
										erreur.message = ERR_349_MSG;
										lVR.valid = false;
										lVR.log = new VRelement();
										lVR.log.valid = false;
										lVR.log.erreurs.push(erreur);
										
										that.construct({id:that.idAdherent,vr:lVR});
										lDialog.dialog('close');
									} else {
										Infobulle.generer(lResponse,'');
									}
								}
							},"json"
						);
					} else {
						Infobulle.generer(lVr,'');
					}
				},
				'Annuler': function() {
					$(this).dialog('close');
				}
			},
			close: function(ev, ui) { $(this).remove(); Infobulle.init(); }				
		});
	};
	
	this.affectAjoutAbonnement = function(pData) {
		var that = this;
		pData.find('#btn-nv-abonnement').click(function() {
			that.dialogAjoutAbonnement();
		});
		return pData;
	};
	
	this.dialogAjoutAbonnement = function(pData) {
		var that = this;
		var lParam = {fonction:"listeFerme"};
		$.post(	"./index.php?m=GestionAbonnement&v=ListeAbonne", "pParam=" + toJsonURIEncode(lParam),
				function(lResponse) {
					Infobulle.init(); // Supprime les erreurs
					if(lResponse) {
						if(lResponse.valid) {
							var lGestionAbonnementTemplate = new GestionAbonnementTemplate();
							var lTemplate = lGestionAbonnementTemplate.dialogAjoutAbonnement;
							
							$(that.affectAjoutAbonnementSelectFerme($(lTemplate.template(lResponse)))).dialog({			
								autoOpen: true,
								modal: true,
								draggable: true,
								resizable: false,
								width:900,
								buttons: {
									'Abonner': function() {
										that.ajouterAbonnement($(this));
									},
									'Annuler': function() {
										$(this).dialog('close');
									}
								},
								close: function(ev, ui) { $(this).remove(); Infobulle.init(); }				
							});
						} else {
							Infobulle.generer(lResponse,'');
						}
					}
				},"json"
		);	
		return pData;
	};
	
	this.affectAjoutAbonnementSelectFerme = function(pData) {
		var that = this;
		pData.find("#pro-idFerme select").change(function() {
			var lId = $(this).val();
			$("#pro-idCategorie select, #pro-idProduit select").prop("disabled",true).selectOptions("0");
			$("#detail-produit").replaceWith("<div id=\"detail-produit\"></div>");
			if(lId > 0) {
				var lParam = {fonction:"listeProduit",id:that.idCompte,idFerme:lId};
				$.post(	"./index.php?m=GestionAbonnement&v=ListeAbonne", "pParam=" + toJsonURIEncode(lParam),
					function (lResponse) {		
						if(lResponse) {
							if(lResponse.valid) {
								Infobulle.init(); // Supprime les erreurs
								
								if(lResponse.listeProduit.length > 0 && lResponse.listeProduit[0].nproId != null) {
								
									that.mProduits = [];
									//that.mListeProduit = [];
								
									var lIdCategorie = 0;
									var lListeCategorie = [];
									$.each(lResponse.listeProduit,function() {
										if(this.ferId == lId) {
											if(that.mProduits[this.cproId]) {
												that.mProduits[this.cproId].listeProduit.push(this);
											} else {
												that.mProduits[this.cproId] = {nom:this.cproNom,listeProduit:[this]};
											}
											if(lIdCategorie != this.cproId) {
												lListeCategorie.push({cproId:this.cproId,cproNom:this.cproNom});
												lIdCategorie = this.cproId;
											}
										}
									});
									
	
									var lGestionAbonnementTemplate = new GestionAbonnementTemplate();
									var lTemplate = lGestionAbonnementTemplate.ajoutAbonnementSelectCategorie;
									
									$("#pro-idCategorie").replaceWith(that.affectAjoutAbonnementSelectCategorie($(lTemplate.template({listeCategorie:lListeCategorie}))));
									
								} else {
									// Message d'information
									var lVr = new TemplateVR();
									lVr.valid = false;
									lVr.log.valid = false;
									var erreur = new VRerreur();
									erreur.code = ERR_332_CODE;
									erreur.message = ERR_332_MSG;
									lVr.log.erreurs.push(erreur);
									Infobulle.generer(lVr,'');
								}
							} else {
								Infobulle.generer(lResponse,'');
							}
						}
					},"json"
				);
			} 
						
		});
		return pData;
	};
	
	this.affectAjoutAbonnementSelectCategorie = function(pData) {
		var that = this;
		pData.find("select").change(function() {
			var lId = $(this).val();
			$("#pro-idProduit select").prop("disabled",true).selectOptions("0");
			$("#detail-produit").replaceWith("<div id=\"detail-produit\"></div>");
			if(lId > 0) {
				
				var lGestionAbonnementTemplate = new GestionAbonnementTemplate();
				var lTemplate = lGestionAbonnementTemplate.ajoutAbonnementSelectProduit;
				
				$("#pro-idProduit").replaceWith(that.affectAjoutAbonnementSelectProduit($(lTemplate.template(that.mProduits[lId]))));
				
			}		
		});
		return pData;
	};
	
	this.affectAjoutAbonnementSelectProduit = function(pData) {
		var that = this;
		pData.find("select").change(function() {
			var lId = $(this).val();
			if(lId > 0) {
				var lParam = {fonction:"detailProduit",id:lId};
				$.post(	"./index.php?m=GestionAbonnement&v=ListeAbonne", "pParam=" + toJsonURIEncode(lParam),
					function (lResponse) {		
						if(lResponse) {
							if(lResponse.valid) {
								Infobulle.init(); // Supprime les erreurs
								that.produit = lResponse.produit[0];
								
								var lGestionAbonnementTemplate = new GestionAbonnementTemplate();
								var lTemplate = lGestionAbonnementTemplate.detailProduitAjoutAbonnement;
								
								var lData = lResponse.produit[0];
								if(lData.proAboMax == -1) {
									lData.proAboMaxLabel = "Pas de limite";
								} else {
									lData.proAboMaxLabel = lData.proAboMax.nombreFormate(2,',',' ') + " " + lData.proAboUnite;
								}
								lData.qteRestant = (parseFloat(lData.proAboStockInitial) - parseFloat(lData.proAboReservation)).nombreFormate(2,',',' ');
								
								lData.lot = [];
								var lLots = [];
								$.each(lResponse.produit[0].lots, function() {
									if(this.id) {
										var lLot = {};
										lLot.dcomId = this.id;
										lLot.dcomTaille = parseFloat(this.taille).nombreFormate(2,',',' ');
										lLot.dcomPrix = parseFloat(this.prix).nombreFormate(2,',',' ');
										if(!lData.prixUnitaire) {
											lData.prixUnitaire = (parseFloat(this.prix) / parseFloat(this.taille)).nombreFormate(2,',',' ');
											lData.qteInit = lLot.dcomTaille;
											lData.prixInit = lLot.dcomPrix;
											that.reservationModif.dcomId = this.id;
											that.reservationModif.stoQuantite = this.taille;
										}
										lData.lot.push(lLot);
										lLots[this.id] = this;
									}
								});
								that.produit.lots = lLots;
								
								lData.sigleMonetaire = gSigleMonetaire;
								
								$("#detail-produit").replaceWith(that.affectDetailProduit($(lTemplate.template(lData))));
							} else {
								Infobulle.generer(lResponse,'');
							}
						}
					},"json"
				);
			} else {
				$("#detail-produit").replaceWith($("<div id=\"detail-produit\">"));
			}			
		});
		return pData;
	};
	
	this.affectDetailProduit = function(pData) {
		pData = this.affectChangementLot(pData);
		pData = this.affectBtnQte(pData);
		pData = gCommunVue.comHoverBtn(pData);
		pData = gCommunVue.comNumeric(pData);
		return pData;		
	};
	
	this.affectChangementLot = function(pData) {
		var that = this;
		pData.find('#lot').change(function() {
			Infobulle.init(); // Supprime les erreurs
			that.changerLot($(this).val());
		});
		return pData;
	};
	
	this.changerLot = function(pIdLot) {
		var lPrix = this.produit.lots[pIdLot].prix;
		var lQte = this.produit.lots[pIdLot].taille;
		var lprixUnitaire = (lPrix / lQte).nombreFormate(2,',',' '); 
		
		$('#prix-unitaire').text(lprixUnitaire);
		this.reservationModif.dcomId = pIdLot;
		this.reservationModif.stoQuantite = lQte;
		$('#qte-pdt').text(lQte.nombreFormate(2,',',' '));
		$('#prix-pdt').text(lPrix.nombreFormate(2,',',' '));
	};
	
	this.affectBtnQte = function(pData) {
		var that = this;
		pData.find('.btn-plus').click(function() {
			Infobulle.init(); // Supprime les erreurs
			that.nouvelleQuantite($('#lot').val(), 1);
		});	
		pData.find('.btn-moins').click(function() {
			Infobulle.init(); // Supprime les erreurs
			that.nouvelleQuantite($('#lot').val(),-1);
		});
		return pData;		
	};
	
	this.nouvelleQuantite = function(pIdLot,pIncrement) {
		// La quantité max soit qte max soit stock
		var lMax = parseFloat(this.produit.proAboMax);
		
		// Recherche de la quantité reservée pour la déduire de la quantité max
		var lStock = 0;
		if(this.reservation && this.reservation.stoQuantite) {
			lStock = parseFloat(this.produit.proAboStockInitial) - parseFloat(this.produit.proAboReservation) + parseFloat(this.reservation.stoQuantite);						
		} else {
			lStock = parseFloat(this.produit.proAboStockInitial) - parseFloat(this.produit.proAboReservation);
		}

		
		var lNoStock = false;
		if(parseFloat(this.produit.proAboMax) == -1 && parseFloat(this.produit.proAboStockInitial) == -1) { // Si ni stock ni qmax
			lNoStock = true;
		} else if(parseFloat(this.produit.proAboStockInitial) == -1) { // Si qmax mais pas stock
			lMax = this.produit.proAboMax;
		} else if(parseFloat(this.produit.proAboMax) == -1) { // Si stock mais pas qmax
			lMax = lStock;
		} else { // Si stock et qmax
			if(parseFloat(lStock) < parseFloat(lMax)) { lMax = lStock; }				
		}
		
		var lTaille = this.produit.lots[pIdLot].taille;
		var lPrix = this.produit.lots[pIdLot].prix;
		
		// Récupère le nombre de lot réservé
		var lQteReservation = 0;
		if(this.reservationModif && (this.reservationModif.dcomId == pIdLot)) {
			lQteReservation = parseFloat(this.reservationModif.stoQuantite)/parseFloat(lTaille);
		}
		lQteReservation += pIncrement;
		
		var lNvQteReservation = lQteReservation * lTaille;

		// Test si la quantité est dans les limites
		if((lNoStock && lNvQteReservation > 0) || (!lNoStock && lNvQteReservation > 0 && lNvQteReservation <= lMax)) {
			var lNvPrix = (lQteReservation * lPrix).toFixed(2);

			// Mise à jour de la quantite reservée
			this.reservationModif.stoQuantite = lNvQteReservation;			
			
			$('#qte-pdt').text(parseFloat(lNvQteReservation).nombreFormate(2,',',' '));
			$('#prix-pdt').text(parseFloat(lNvPrix).nombreFormate(2,',',' '));		

		} else if(lNvQteReservation > lMax && lMax != -1) {
			var lVr = new TemplateVR();
			lVr.valid = false;
			              
			var lProduit = new ReservationCommandeVR();              
			lProduit.valid = false;
			lProduit.stoQuantite.valid = false;
			var erreur = new VRerreur();
			erreur.code = ERR_304_CODE;
			erreur.message = ERR_304_MSG;
			lProduit.stoQuantite.erreurs.push(erreur);		
			lVr.produit = lProduit;
			
			Infobulle.generer(lVr,'');
		}		
	};
	
	this.ajouterAbonnement = function(pDialog) {
		var that = this;		
		var lIdNomProduit = pDialog.find(':input[name=produit]').val();

		if(lIdNomProduit != 0) {
			var lCompteAbonnementVO = new CompteAbonnementVO();
			lCompteAbonnementVO.idCompte = this.idCompte;
			lCompteAbonnementVO.idProduitAbonnement = lIdNomProduit;
			lCompteAbonnementVO.quantite = this.reservationModif.stoQuantite;
			lCompteAbonnementVO.idLotAbonnement = pDialog.find("#lot").val();
			
			var lValid = new CompteAbonnementValid();
			var lVr = lValid.validAjout(lCompteAbonnementVO,that.produit);
			
			if(lVr.valid) {	
				Infobulle.init();
				lCompteAbonnementVO.fonction = "ajouter";
				$.post(	"./index.php?m=GestionAbonnement&v=ListeAbonne", "pParam=" + toJsonURIEncode(lCompteAbonnementVO),
					function (lResponse) {		
						if(lResponse) {
							if(lResponse.valid) {
								Infobulle.init(); // Supprime les erreurs
								var lVR = new Object();
								var erreur = new VRerreur();
								erreur.code = ERR_345_CODE;
								erreur.message = ERR_345_MSG;
								lVR.valid = false;
								lVR.log = new VRelement();
								lVR.log.valid = false;
								lVR.log.erreurs.push(erreur);
								
								that.construct({id:that.idAdherent,vr:lVR});
								pDialog.dialog('close');
							} else {
								Infobulle.generer(lResponse,'');
							}
						}
					},"json"
				);
			} else {
				Infobulle.generer(lVr,'');
			}
		}
		return true;
	};
	
	this.affectModifierAbonnement = function(pData) {
		var that = this;
		pData.find(".btn-modifier").click(function() {
			that.dialogModifierAbonnement($(this).attr("idProduit"),$(this).attr("idCompteAbonnement"));
		});
		return pData;
	};
	
	this.dialogModifierAbonnement = function(pIdProduit,pIdCompteAbonnement) {
		var that = this;
		var lParam = {fonction:"detailAbonnement",idProduit:pIdProduit,idCompteAbonnement:pIdCompteAbonnement};
		$.post(	"./index.php?m=GestionAbonnement&v=ListeAbonne", "pParam=" + toJsonURIEncode(lParam),
			function (lResponse) {		
				if(lResponse) {
					if(lResponse.valid) {
						Infobulle.init(); // Supprime les erreurs
						that.produit = lResponse.produit[0];
						that.abonnement = lResponse.abonnement;
						
						var lGestionAbonnementTemplate = new GestionAbonnementTemplate();
						var lTemplate = lGestionAbonnementTemplate.dialogModifierAbonnement;
						
						var lData = lResponse.produit[0];
						if(lData.proAboMax == -1) {
							lData.proAboMaxLabel = "Pas de limite";
						} else {
							lData.proAboMaxLabel = lData.proAboMax.nombreFormate(2,',',' ') + " " + lData.proAboUnite;
						}
						lData.qteRestant = (parseFloat(lData.proAboStockInitial) - parseFloat(lData.proAboReservation) + parseFloat(lResponse.abonnement.cptAboQuantite) ).nombreFormate(2,',',' ');
						lData.cptAboQuantite = lResponse.abonnement.cptAboQuantite.nombreFormate(2,',',' ');
						lData.proAboId = pIdProduit;
						lData.idCompteAbonnement = pIdCompteAbonnement;
						
						lData.lot = [];
						var lLots = [];
						$.each(lResponse.produit[0].lots, function() {
							if(this.id) {
								var lLot = {};
								lLot.dcomId = this.id;
								lLot.dcomTaille = parseFloat(this.taille).nombreFormate(2,',',' ');
								lLot.dcomPrix = parseFloat(this.prix).nombreFormate(2,',',' ');
								lLot.selected = '';
								if(this.id == lResponse.abonnement.cptAboIdLotAbonnement) {
									lData.prixUnitaire = (parseFloat(this.prix) / parseFloat(this.taille)).nombreFormate(2,',',' ');
									lData.qteInit = lResponse.abonnement.cptAboQuantite.nombreFormate(2,',',' ');
									lData.prixInit = (lResponse.abonnement.cptAboQuantite * this.prix / this.taille).nombreFormate(2,',',' ');
									that.reservationModif.dcomId = this.id;
									that.reservationModif.stoQuantite = lResponse.abonnement.cptAboQuantite;
									that.reservation.stoQuantite = lResponse.abonnement.cptAboQuantite;
									lLot.selected = "selected=\"selected\"";
								}
								lData.lot.push(lLot);
								lLots[this.id] = this;
							}
						});
						that.produit.lots = lLots;
						
						lData.sigleMonetaire = gSigleMonetaire;
						
						$(that.affectModifierProduit($(lTemplate.template(lData)))).dialog({		
							autoOpen: true,
							modal: true,
							draggable: true,
							resizable: false,
							width:900,
							buttons: {
								'Modifier': function() {
									that.modifierAbonnement($(this));
								},
								'Annuler': function() {
									$(this).dialog('close');
								}
							},
							close: function(ev, ui) { $(this).remove(); Infobulle.init(); }				
						});
						
					} else {
						Infobulle.generer(lResponse,'');
					}
				}
			},"json"
		);
	};
	
	this.affectModifierProduit = function(pData) {
		pData = this.affectChangementLot(pData);
		pData = this.affectBtnQte(pData);
		pData = gCommunVue.comHoverBtn(pData);
		pData = gCommunVue.comNumeric(pData);
		return pData;		
	};
	
	this.modifierAbonnement = function(pDialog){
		var that = this;
		var lCompteAbonnementVO = new CompteAbonnementVO();
		lCompteAbonnementVO.id = pDialog.find("#id").val();
		lCompteAbonnementVO.idCompte = this.idCompte;
		lCompteAbonnementVO.idProduitAbonnement = pDialog.find("#idProduitAbonnement").val();
		//lCompteAbonnementVO.quantite = pDialog.find("#quantite").val().numberFrToDb();
		lCompteAbonnementVO.quantite = this.reservationModif.stoQuantite;
		lCompteAbonnementVO.idLotAbonnement = pDialog.find("#lot").val();
		
		var lValid = new CompteAbonnementValid();
		var lVr = lValid.validUpdate(lCompteAbonnementVO,that.produit,that.abonnement);
		
		if(lVr.valid) {	
			Infobulle.init();
			lCompteAbonnementVO.fonction = "modifier";
			$.post(	"./index.php?m=GestionAbonnement&v=ListeAbonne", "pParam=" + toJsonURIEncode(lCompteAbonnementVO),
				function (lResponse) {		
					if(lResponse) {
						if(lResponse.valid) {
							Infobulle.init(); // Supprime les erreurs
							var lVR = new Object();
							var erreur = new VRerreur();
							erreur.code = ERR_346_CODE;
							erreur.message = ERR_346_MSG;
							lVR.valid = false;
							lVR.log = new VRelement();
							lVR.log.valid = false;
							lVR.log.erreurs.push(erreur);
							
							that.construct({id:that.idAdherent,vr:lVR});
							pDialog.dialog('close');
						} else {
							Infobulle.generer(lResponse,'');
						}
					}
				},"json"
			);
		} else {
			Infobulle.generer(lVr,'');
		}
	};
	
	this.affecSupprimerAbonnement = function(pData) {
		var that = this;
		pData.find(".btn-supp").click(function() {
			that.dialogSupprimerAbonnement($(this).attr("idCompteAbonnement"));
		});
		return pData;
	};
	
	this.dialogSupprimerAbonnement = function(pIdCompteAbonnement) {
		var that = this;
		var lGestionAbonnementTemplate = new GestionAbonnementTemplate();
		var lTemplate = lGestionAbonnementTemplate.dialogSuppressionAbonnement;
		//var lButton = this;
		$(lTemplate).dialog({
			autoOpen: true,
			modal: true,
			draggable: false,
			resizable: false,
			width:600,
			buttons: {
				'Supprimer': function() {
					var lParam = {fonction:"supprimer", id:pIdCompteAbonnement};
					var lDialog = this;
					$.post(	"./index.php?m=GestionAbonnement&v=ListeAbonne", "pParam=" + toJsonURIEncode(lParam),
							function(lResponse) {
								Infobulle.init(); // Supprime les erreurs
								if(lResponse) {
									if(lResponse.valid) {
										$(lDialog).dialog('close');

										var lVR = new Object();
										var erreur = new VRerreur();
										erreur.code = ERR_347_CODE;
										erreur.message = ERR_347_MSG;
										lVR.valid = false;
										lVR.log = new VRelement();
										lVR.log.valid = false;
										lVR.log.erreurs.push(erreur);
										
										that.construct({id:that.idAdherent,vr:lVR});
									} else {
										Infobulle.generer(lResponse,'');
									}
								}
							},"json"
					);
				},
				'Annuler': function() {
					$(this).dialog('close');
				}
			},
			close: function(ev, ui) { $(this).remove(); }
			
		});
	};

	this.affectRetour = function(pData) {
	//	var that = this;
		pData.find("#lien-retour").click(function() { ListeAbonneVue();});
		return pData;
	};
	
	this.construct(pParam);
}