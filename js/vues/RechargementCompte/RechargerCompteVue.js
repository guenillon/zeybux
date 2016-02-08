;function RechargerCompteVue(pParam) {
	this.mTypePaiement = [];
	this.solde = 0;
	this.mBanques = [];
	
	this.construct = function(pParam) {
		$.history( {'vue':function() {RechargerCompteVue(pParam);}} );
		var that = this;
		var lParam = {fonction:"listeAdherent"};
		$.post(	"./index.php?m=RechargementCompte&v=RechargerCompte", "pParam=" + toJsonURIEncode(lParam),
				function(lResponse) {
					Infobulle.init(); // Supprime les erreurs
					if(lResponse) {
						if(lResponse.valid) {
							if(pParam && pParam.vr) {
								Infobulle.generer(pParam.vr,'');
							}							
							that.mTypePaiement  = lResponse.typePaiement;
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
		var lRechargementCompteTemplate = new RechargementCompteTemplate();
		
		if(lResponse.listeAdherent.length > 0 && lResponse.listeAdherent[0].adhId != null) {
			var lTemplate = lRechargementCompteTemplate.listeAdherent;
			
			lResponse.sigleMonetaire = gSigleMonetaire;
			$(lResponse.listeAdherent).each(function() {
				this.classSolde = '';
				if(this.cptSolde < 0){this.classSolde = "com-nombre-negatif";}
				this.cptSolde = this.cptSolde.nombreFormate(2,',',' ');
				this.adhIdTri = this.adhNumero.replace("Z","");
			});
			
			$('#contenu').replaceWith(that.affect($(lTemplate.template(lResponse))));
		} else {
			$('#contenu').replaceWith(lRechargementCompteTemplate.listeAdherentVide);
		}
		
	};
	
	this.affect = function(pData) {
		pData = this.affectTri(pData);
		pData = this.affectRecherche(pData);
		pData = this.affectLienCompte(pData);
		return pData;
	};
		
	this.affectTri = function(pData) {
		pData.find('.com-table').tablesorter({sortList: [[0,0]],headers: { 4: {sorter: false} }});
		return pData;
	};
	
	this.affectRecherche = function(pData) {
		pData.find("#filter").keyup(function() {
		    $.uiTableFilter( $('.com-table'), this.value );
		  });
		
		pData.find("#filter-form").submit(function () {return false;});
		
		return pData;
	};
	
	this.affectLienCompte = function(pData) {
		var that = this;
		pData.find('.compte-ligne').click(function() {
			
			
			var lParam = {'id':$(this).attr("id-adherent"),
							fonction:"infoRechargement"};
			
			$.post(	"./index.php?m=RechargementCompte&v=RechargerCompte", "pParam=" + toJsonURIEncode(lParam),
				function(lResponse) {
					Infobulle.init(); // Supprime les erreurs
					if(lResponse) {
						if(lResponse.valid) {
							that.mBanques = lResponse.banques;
							
							that.solde = parseFloat(lResponse.solde);
							
							lResponse.sigleMonetaire = gSigleMonetaire;
							lResponse.solde = lResponse.solde.nombreFormate(2,',',' ');
							lResponse.typePaiement = that.mTypePaiement;
							
							var lCompte = lResponse.idCompte;
							
							var lRechargementCompteTemplate = new RechargementCompteTemplate();
							that.affectDialog($(lRechargementCompteTemplate.dialogRecharger.template(lResponse))).dialog({
								autoOpen: true,
								modal: true,
								draggable: false,
								resizable: false,
								width:350,
								buttons: {
									'Valider': function() {
								
										var lVo = that.getRechargementVO();									
										lVo.idCompte = lCompte;
										
										var lValid = new OperationDetailValid();
										var lVr = lValid.validAjout(lVo);
										
										Infobulle.init(); // Supprime les erreurs
										if(lVr.valid) {
											lVo.fonction = "rechargerCompte";
											var lDialog = this;
											$.post(	"./index.php?m=RechargementCompte&v=RechargerCompte", "pParam=" + toJsonURIEncode(lVo),
												function(lResponse) {
													Infobulle.init(); // Supprime les erreurs
													if(lResponse.valid) {
														
														// Message d'information
														var lVr = new TemplateVR();
														lVr.valid = false;
														lVr.log.valid = false;
														var erreur = new VRerreur();
														erreur.code = ERR_306_CODE;
														erreur.message = ERR_306_MSG;
														lVr.log.erreurs.push(erreur);
														var lParam = {vr:lVr};
														that.construct(lParam);
														
														$(lDialog).dialog("close");										
													} else {
														Infobulle.generer(lResponse,'');
													}
												},"json"
											);
										}else {
											Infobulle.generer(lVr,'');
										}
									},
									'Annuler': function() { $(this).dialog("close"); }
									},
								close: function(ev, ui) { $(this).remove(); }
							});
							that.changerTypePaiement($(":input[name=typepaiement]"));
							that.majNouveauSolde();
						} else {
							Infobulle.generer(lResponse,'');
						}
					}
				},"json"
			);		
		});
		return pData;
	};
	
	this.affectDialog = function(pData) {
		pData = this.affectSelectTypePaiement(pData);
		pData = this.affectNouveauSolde(pData);
		pData = gCommunVue.comNumeric(pData);
		return pData;
	};
	
	this.affectSelectTypePaiement = function(pData) {
		var that = this;
		pData.find(":input[name=typepaiement]").change(function () {
			that.changerTypePaiement($(this));
		});
		return pData;
	};
	
	this.changerTypePaiement = function(pObj) {
		var lId = pObj.val();
		if(!this.mTypePaiement[lId] || (this.mTypePaiement[lId] && this.mTypePaiement[lId].champComplementaire.length == 0)) {
			$('.champ-complementaire').remove();
		} else {
			var lRechargementCompteTemplate = new RechargementCompteTemplate();
			var lTypePaiementService = new TypePaiementService();
			$('#ligne-operation').after(lTypePaiementService.affect($(lRechargementCompteTemplate.champComplementaire.template(this.mTypePaiement[lId])),this.mBanques));
		}
	};
	
	this.affectNouveauSolde = function(pData) {
		var that = this;
		pData.find(":input[name=montant-rechargement]").keyup(function() {
			that.majNouveauSolde();
		});
		return pData;
	};
	
	this.majNouveauSolde = function() {
		var lTotal = this.calculNouveauSolde();
		if(lTotal <= 0) {
			$("#nouveau-solde").addClass("com-nombre-negatif");
			$("#nouveau-solde-sigle").addClass("com-nombre-negatif");			
		} else {
			$("#nouveau-solde").removeClass("com-nombre-negatif");
			$("#nouveau-solde-sigle").removeClass("com-nombre-negatif");
		}
		$("#nouveau-solde").text(lTotal.nombreFormate(2,',',' '));
	};
	
	this.calculNouveauSolde = function() {
		var lRechargement = parseFloat($(":input[name=montant-rechargement]").val().numberFrToDb());
		if(isNaN(lRechargement)) {lRechargement = 0;}		
		return this.solde + lRechargement;
	};
	
	this.getRechargementVO = function() {
		var lVo = new OperationDetailVO();
		var lMontant = $(":input[name=montant-rechargement]").val().numberFrToDb();
		if(!isNaN(lMontant) && !lMontant.isEmpty()){
			lMontant = parseFloat(lMontant);
		}
		lVo.montant = lMontant;
		lVo.typePaiement = $(":input[name=typepaiement]").val();
		
		if(this.mTypePaiement[lVo.typePaiement]) {
			var lTypePaiementService = new TypePaiementService();
			lVo.champComplementaire = lTypePaiementService.getChampComplementaire(this.mTypePaiement[lVo.typePaiement].champComplementaire);
		}
		return lVo;
	};
	
	this.construct(pParam);
}