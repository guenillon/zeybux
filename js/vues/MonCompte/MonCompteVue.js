;function MonCompteVue(pParam) {
	this.mInformationAdherent = {};
	this.mIdAdherent = 0;
	
	this.construct = function(pParam) {
		$.history( {'vue':function() {MonCompteVue(pParam);}} );
		var that = this;
		$.post(	"./index.php?m=MonCompte&v=MonCompte", 
				function(lResponse) {
					Infobulle.init(); // Supprime les erreurs
					if(lResponse) {
						if(lResponse.valid) {	
							if(pParam && pParam.vr) {
								Infobulle.generer(pParam.vr,'');
							}
							that.afficher(lResponse);
							
							// Maj du Menu
							gCommunVue.majMenu('MonCompte','MonCompte');
						} else {
							Infobulle.generer(lResponse,'');
						}
					}
				},"json"
		);
	};
	
	this.afficher = function(lResponse) {
		var that = this;

		var lMonCompteTemplate = new MonCompteTemplate();
		
		if(lResponse.adherent.adhId == null) { //SuperZeybu
			lResponse.adherent.opeMontant = 0;
			lResponse.adherent.adhDateNaissance = '0000-00-00';
			lResponse.adherent.adhDateAdhesion = '0000-00-00';
		} else {
			this.mIdAdherent = lResponse.adherent.adhId;
		}
		lResponse.cptSolde = lResponse.adherent.cptSolde.nombreFormate(2,',',' ');
		
		lResponse.sigleMonetaire = gSigleMonetaire;

		lResponse.adherent.adhDateNaissance = lResponse.adherent.adhDateNaissance.extractDbDate().dateDbToFr();
		lResponse.adherent.adhDateAdhesion = lResponse.adherent.adhDateAdhesion.extractDbDate().dateDbToFr();
		
		if(lResponse.adherent.adhEtat == 1) {
			if(lResponse.nbAdhesionEnCours > 0) {
				lResponse.adherent.adhesion = lMonCompteTemplate.adhesionOK;
			} else {
				lResponse.adherent.adhesion = lMonCompteTemplate.adhesionKO;			
			}
		}
		
		this.mInformationAdherent.nom = lResponse.adherent.adhNom;
		this.mInformationAdherent.prenom = lResponse.adherent.adhPrenom;
		this.mInformationAdherent.courrielPrincipal = lResponse.adherent.adhCourrielPrincipal;
		this.mInformationAdherent.courrielSecondaire = lResponse.adherent.adhCourrielSecondaire;
		this.mInformationAdherent.telephonePrincipal = lResponse.adherent.adhTelephonePrincipal;
		this.mInformationAdherent.telephoneSecondaire = lResponse.adherent.adhTelephoneSecondaire;
		this.mInformationAdherent.adresse = lResponse.adherent.adhAdresse;
		this.mInformationAdherent.codePostal = lResponse.adherent.adhCodePostal;
		this.mInformationAdherent.ville = lResponse.adherent.adhVille;
		this.mInformationAdherent.dateNaissance = lResponse.adherent.adhDateNaissance;
		this.mInformationAdherent.dateAdhesion = lResponse.adherent.adhDateAdhesion;
		this.mInformationAdherent.commentaire = lResponse.adherent.adhCommentaire;

		$(lResponse.operationPassee).each(function() {
			if(this.date != null) {
				this.date = this.date.extractDbDate().dateDbToFr();
				if(this.tppType == null) {this.tppType ='';} // Si ce n'est pas un paiement il n'y a pas de type				
				if(this.typePaiement == 2) { // Affiche le N° de chèque
					this.opeTypePaiementChampComplementaire = ' N° ' + this.champComplementaire[3].valeur;
				} else {
					this.opeTypePaiementChampComplementaire = '';
				}
				if(this.montant < 0) {
					this.debit = (this.montant * -1).nombreFormate(2,',',' ') + ' ' + gSigleMonetaire;
					this.credit = '';
				} else {
					this.debit = '';
					this.credit = this.montant.nombreFormate(2,',',' ') + ' ' + gSigleMonetaire;
				}
			}
		});
		
		var lNvSolde = parseFloat(lResponse.adherent.cptSolde);
		var lRechargementPrecedent = 0;
		$(lResponse.operationAvenir).each(function() {
			if(this.opeDate != null) {
				lNvSolde += parseFloat(this.opeMontant);
				this.nouveauSolde = lNvSolde.nombreFormate(2,',',' ');
				this.rechargement = 0;				
				var lSoldeCible = 5;
				if(lNvSolde < lSoldeCible) {
					this.rechargement = (Math.ceil((lSoldeCible-lNvSolde)/lSoldeCible) * lSoldeCible) - lRechargementPrecedent;
				}
				lRechargementPrecedent += this.rechargement;
				this.rechargement = this.rechargement.nombreFormate(2,',',' ');
				
				this.opeDate = this.opeDate.extractDbDate().dateDbToFr();
				this.comDateMarche = this.comDateMarche.extractDbDate().dateDbToFr();
				this.opeMontant = (this.opeMontant * -1).nombreFormate(2,',',' ');
			}
		});
		
		var lCoreTemplate = new CoreTemplate();
		
		if(lResponse.adherent.adhId == lResponse.adherent.cptIdAdherentPrincipal) { // Adhérent Principal
			lResponse.adherent.adherentPrincipal = lMonCompteTemplate.adherentPrincipal;
		} else { // Adhérent Secondaire
			lResponse.adherent.adherentPrincipal = lMonCompteTemplate.adherentSecondaire;
		}
		
		if(lResponse.adherentCompte.length == 1) {
			lResponse.adherent.adherentPrincipalSelect = lMonCompteTemplate.adherentPrincipalUnique.template(lResponse.adherent);
		} else {
			$.each(lResponse.adherentCompte, function() {
				if(this.id == lResponse.adherent.cptIdAdherentPrincipal) {
					this.selected = 'selected="selected"';
				} else {
					this.selected = '';
				}
			});
			lResponse.adherent.adherentPrincipalSelect = lMonCompteTemplate.adherentPrincipalSelect.template({adherent:lResponse.adherentCompte});
		};
		
		//var lTemplate = lMonCompteTemplate.monCompte;
		
		var lHtml = lCoreTemplate.debutContenu;		
		lHtml += lMonCompteTemplate.infoCompteAdherent.template(lResponse.adherent);
		lHtml += lMonCompteTemplate.listeOperationAdherentDebut.template(lResponse);
		lHtml += lMonCompteTemplate.listeOperationPassee.template(lResponse);
		// Affiche des opérations avenir uniquement si elles existent
		if(isArray(lResponse.operationAvenir) && lResponse.operationAvenir[0].opeLibelle != null) {
			lResponse.achatFuturLabel = "Achat Futur";
			if(lResponse.operationAvenir.length > 1) {
				lResponse.achatFuturLabel = "Achats Futurs";
			}
			lHtml += lMonCompteTemplate.listeOperationAvenir.template(lResponse);
		}
		lHtml += lMonCompteTemplate.listeOperationAdherentFin.template(lResponse);
		lHtml += lCoreTemplate.finContenu;
		
		lHtml = $(lHtml);
		if(lResponse.adherent.cptSolde < 0) {
			lHtml = this.soldeNegatif(lHtml);
		}
		
		// Ne pas afficher la pagination si il y a moins de 10 éléments
		if(lResponse.operationPassee.length < 11) {
			lHtml = this.masquerPagination(lHtml);
		} else {
			lHtml = this.paginnation(lHtml);
		}		

		$('#contenu').replaceWith(that.affect(lHtml));	
	};
	
	this.affect = function(pData) {
		pData = this.nouveauSoldeNegatif(pData);
		pData = this.affectHover(pData);
		pData = this.affectEditionPass(pData);
		pData = this.affectEditionCompte(pData);
		pData = this.affectDate(pData);
		pData = gCommunVue.comHoverBtn(pData);
		return pData;
	};
	
	this.paginnation = function(pData) {
		pData.find("#table-operation")
			.tablesorter({headers: { 
				0: {sorter: false},
	            1: {sorter: false},
	            2: {sorter: false},
	            3: {sorter: false},
	            4: {sorter: false} 
	        } })
			.tablesorterPager({container: pData.find("#content-nav-liste-operation"),positionFixed:false}); 
		return pData;
	};
	this.nouveauSoldeNegatif = function(pData) {
		pData.find('.nouveau-solde-val').each(function() {
			if(parseFloat($(this).text().numberFrToDb()) < 0 ) {
				$(this).closest('.nouveau-solde').addClass("com-nombre-negatif");
			}
		});
		return pData;
	};
	
	this.soldeNegatif = function(pData) {
		pData.find('#solde').addClass("com-nombre-negatif");
		return pData;
	};
	
	this.affectHover = function(pData) {
		pData.find('#icone-nav-liste-operation-w,#icone-nav-liste-operation-e').hover(function() {$(this).addClass("ui-state-hover");},function() {$(this).removeClass("ui-state-hover");});
		return pData;
	};
	
	this.masquerPagination = function(pData) {
		pData.find('#content-nav-liste-operation').hide();
		return pData;
	};
	
	this.affectDate = function(pData) {
		var that = this;
		pData.find('#dateNaissance').datepicker({
			changeMonth: true,
			changeYear: true,
			maxDate:that.mInformationAdherent.dateAdhesion,
			yearRange:'1900:c'});
		return pData;
	};
	
	this.affectEditionCompte = function(pData) {		
		var that = this;
		pData.find('#btn-edt-compte').click(function() {
			Infobulle.init(); // Supprime les erreurs
			$(':input[name=nom]').val(htmlDecode(that.mInformationAdherent.nom));
			$(':input[name=prenom]').val(htmlDecode(that.mInformationAdherent.prenom));
			$(':input[name=courriel_principal]').val(htmlDecode(that.mInformationAdherent.courrielPrincipal));
			$(':input[name=courriel_secondaire]').val(htmlDecode(that.mInformationAdherent.courrielSecondaire));
			$(':input[name=telephone_principal]').val(htmlDecode(that.mInformationAdherent.telephonePrincipal));
			$(':input[name=telephone_secondaire]').val(htmlDecode(that.mInformationAdherent.telephoneSecondaire));
			$(':input[name=adresse]').val(htmlDecode(that.mInformationAdherent.adresse));
			$(':input[name=code_postal]').val(htmlDecode(that.mInformationAdherent.codePostal));
			$(':input[name=ville]').val(htmlDecode(that.mInformationAdherent.ville));
			$(':input[name=date_naissance]').val(htmlDecode(that.mInformationAdherent.dateNaissance));
			$(':input[name=commentaire]').html(that.mInformationAdherent.commentaire);
			
			$('.edt-info-compte').toggle();
		});
		
		pData.find('#btn-edt-annuler').click(function() {
			$('.edt-info-compte').toggle();
		});
				
		pData.find('#btn-edt-valider').click(function() {
			that.modifInformation();
		});
		
		return pData;
	};
	
	this.affectEditionPass = function(pData) {		
		var that = this;
		pData.find('#btn-edt-pass').click(function() {
			var lMonCompteTemplate = new MonCompteTemplate();
			var lTemplate = lMonCompteTemplate.dialogEditionPass;
			
			var lDialog = $(lTemplate).dialog({
				autoOpen: true,
				modal: true,
				draggable: false,
				resizable: false,
				width:500,
				buttons: {
					'Valider': function() {
						that.changerMotPasse(this);
					},
					'Annuler': function() {
						$(this).dialog('close');
					}
				},
				close: function(ev, ui) { $(this).remove(); }
			});
			lDialog.find(':input').keyup(function(event) {
				if (event.keyCode == '13') {
					that.changerMotPasse(lDialog);
				}
			});
		});
		
		return pData;
	};
	
	this.modifInformation = function() {
		var that = this;
		var lVo = new AdherentVO();
		if($('#idAdherentPrincipal').length == 0) {
			lVo.idAdherentPrincipal = this.mIdAdherent;
		} else {
			lVo.idAdherentPrincipal = $('#idAdherentPrincipal').val();
		}
		
		lVo.nom = $(':input[name=nom]').val();
		lVo.prenom = $(':input[name=prenom]').val();
		lVo.courrielPrincipal = $(':input[name=courriel_principal]').val();
		lVo.courrielSecondaire = $(':input[name=courriel_secondaire]').val();
		lVo.telephonePrincipal = $(':input[name=telephone_principal]').val();
		lVo.telephoneSecondaire = $(':input[name=telephone_secondaire]').val();
		lVo.adresse = $(':input[name=adresse]').val();
		lVo.codePostal = $(':input[name=code_postal]').val();
		lVo.ville = $(':input[name=ville]').val();
		lVo.dateNaissance = $(':input[name=date_naissance]').val().dateFrToDb();
		lVo.dateAdhesion = $(':input[name=date_adhesion]').val().dateFrToDb();
		lVo.commentaire = $(':input[name=commentaire]').val();

		if(lVo.dateAdhesion == "") {
			lVo.dateAdhesion = getDateAujourdhuiDb();
		}
		
		var lValid = new AdherentValid();
		var lVr = lValid.validUpdateInformation(lVo);
		
		if(lVr.valid) {

			lVo.fonction = "information";
			Infobulle.init(); // Supprime les erreurs
			// Ajout de l'adherent
			$.post(	"./index.php?m=MonCompte&v=ModifierMonCompte", "pParam=" + $.toJSON(lVo),
				function(lResponse) {
					Infobulle.init(); // Supprime les erreurs
					if(lResponse) {
						if(lResponse.valid) {
							
							that.mInformationAdherent.nom = lVo.nom;
							that.mInformationAdherent.prenom = lVo.prenom;
							that.mInformationAdherent.courrielPrincipal = lVo.courrielPrincipal;
							that.mInformationAdherent.courrielSecondaire = lVo.courrielSecondaire;
							that.mInformationAdherent.telephonePrincipal = lVo.telephonePrincipal;
							that.mInformationAdherent.telephoneSecondaire = lVo.telephoneSecondaire;
							that.mInformationAdherent.adresse = lVo.adresse;
							that.mInformationAdherent.codePostal = lVo.codePostal;
							that.mInformationAdherent.ville = lVo.ville;
							that.mInformationAdherent.dateNaissance = lVo.dateNaissance.extractDbDate().dateDbToFr();
							that.mInformationAdherent.commentaire = lVo.commentaire;

							var lVr = new TemplateVR();
							lVr.valid = false;
							lVr.log.valid = false;
							var erreur = new VRerreur();
							erreur.code = ERR_316_CODE;
							erreur.message = ERR_316_MSG;
							lVr.log.erreurs.push(erreur);							
							
							Infobulle.generer(lVr,'');
	
							$('#adh-nom').text(that.mInformationAdherent.nom);
							$('#adh-prenom').text(that.mInformationAdherent.prenom);
							$('#adh-courriel-principal').text(that.mInformationAdherent.courrielPrincipal);
							$('#adh-courriel-secondaire').text(that.mInformationAdherent.courrielSecondaire);
							$('#adh-telephone-principal').text(that.mInformationAdherent.telephonePrincipal);
							$('#adh-telephone-secondaire').text(that.mInformationAdherent.telephoneSecondaire);
							$('#adh-adresse').text(that.mInformationAdherent.adresse);
							$('#adh-code-postal').text(that.mInformationAdherent.codePostal);
							$('#adh-ville').text(that.mInformationAdherent.ville);
							$('#adh-date-naissance').text(that.mInformationAdherent.dateNaissance);
							$('#adh-commentaire').text(that.mInformationAdherent.commentaire);
							
							var lMonCompteTemplate = new MonCompteTemplate();
							if(lVo.idAdherentPrincipal == that.mIdAdherent) {
								$('#adh-principal').text(lMonCompteTemplate.adherentPrincipal);
							} else {
								$('#adh-principal').text(lMonCompteTemplate.adherentSecondaire);
							}
							
							$('.edt-info-compte').toggle();
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
	
	this.changerMotPasse = function(pDialog) {
		var lVo = new InfoAdherentVO();
		var lForm = $('#dialog-edt-info-cpt form');
		
		lVo.motPasse = lForm.find(':input[name=pass]').val();
		lVo.motPasseNouveau = lForm.find(':input[name=pass_nouveau]').val();
		lVo.motPasseConfirm = lForm.find(':input[name=pass_confirm]').val();

		var lValid = new InfoAdherentValid();
		var lVr = lValid.validAjout(lVo);
		
		if(lVr.valid) {
			lVo.fonction = "pass";
			$.post(	"./index.php?m=MonCompte&v=ModifierMonCompte", "pParam=" + $.toJSON(lVo),
				function(lResponse) {
					Infobulle.init(); // Supprime les erreurs
					if(lResponse) {
						if(lResponse.valid) {										
							var lVr = new TemplateVR();
							lVr.valid = false;
							lVr.log.valid = false;
							var erreur = new VRerreur();
							erreur.code = ERR_302_CODE;
							erreur.message = ERR_302_MSG;
							lVr.log.erreurs.push(erreur);							
							
							Infobulle.generer(lVr,'');
							$(pDialog).dialog('close');
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
	
	this.construct(pParam);
}