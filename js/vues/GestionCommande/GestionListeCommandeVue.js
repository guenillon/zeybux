;function GestionListeCommandeVue(pParam) {	
	this.construct = function(pParam) {
		$.history( {'vue':function() {GestionListeCommandeVue(pParam);}} );
		var that = this;
		$.post(	"./index.php?m=GestionCommande&v=ListeCommande", 
				function(lResponse) {
					Infobulle.init(); // Supprime les erreurs
					if(lResponse) {
						if(lResponse.valid) {
							if(pParam && pParam.vr) {
								Infobulle.generer(pParam.vr,'');
							}
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
		var lGestionCommandeTemplate = new GestionCommandeTemplate();
		if(lResponse.listeCommande.length > 0 && lResponse.listeCommande[0].comId != null) {
		
			var lListeCommande = new Object;
			lListeCommande.commande = new Array();
			
				$(lResponse.listeCommande).each(function() {
					var lCommande = new Object();
					lCommande.id = this.comId;
					lCommande.numero = this.comNumero;
					lCommande.nom = this.comNom;
					

					lCommande.jourFinReservation = jourSem(this.comDateFinReservation.extractDbDate());
					lCommande.dateFinReservation = this.comDateFinReservation.extractDbDate().dateDbToFr();
					lCommande.heureFinReservation = this.comDateFinReservation.extractDbHeure();
					lCommande.minuteFinReservation = this.comDateFinReservation.extractDbMinute();
					

					lCommande.jourMarcheDebut = jourSem(this.comDateMarcheDebut.extractDbDate());
					lCommande.dateMarcheDebut = this.comDateMarcheDebut.extractDbDate().dateDbToFr();
					lCommande.heureMarcheDebut = this.comDateMarcheDebut.extractDbHeure();
					lCommande.minuteMarcheDebut = this.comDateMarcheDebut.extractDbMinute();
					
					lCommande.heureMarcheFin = this.comDateMarcheFin.extractDbHeure();
					lCommande.minuteMarcheFin = this.comDateMarcheFin.extractDbMinute();
	
					lListeCommande.commande.push(lCommande);
				});
			
			var lTemplate = lGestionCommandeTemplate.listeCommandePage;
			$('#contenu').replaceWith(that.affect($(lTemplate.template(lListeCommande))));
		} else {
			$('#contenu').replaceWith(that.affect($(lGestionCommandeTemplate.listeCommandeVide)));
		}
	};
	
	this.affect = function(pData) {
		pData = this.affectLienEditer(pData);
		pData = this.affectLienListeCommandeArchive(pData);
		pData = this.affectNouveauMarche(pData);
		pData = gCommunVue.comHoverBtn(pData);
		return pData;
	};
	
	this.affectNouveauMarche = function(pData) {
		pData.find('#btn-nv-marche').click(function() {
			AjoutCommandeVue();
		});
		return pData;
	};
	
	this.affectLienEditer = function(pData) {
		pData.find('.btn-marche').click(function() {
			EditerCommandeVue( {"id_marche":$(this).attr('id')});
		});
		return pData;
	};
		
	this.affectLienListeCommandeArchive = function(pData) {
		pData.find('#lien-marche-archive').click(function() {
			ListeCommandeArchiveVue();
		});
		return pData;
	};
	
	this.construct(pParam);
}