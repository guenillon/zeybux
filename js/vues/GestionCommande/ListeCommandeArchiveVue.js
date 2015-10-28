;function ListeCommandeArchiveVue(pParam) {
	
	this.construct = function(pParam) {
		$.history( {'vue':function() {ListeCommandeArchiveVue(pParam);}} );
		var that = this;
		var lParam = {archive:1};
		$.post(	"./index.php?m=GestionCommande&v=ListeCommande", "pParam=" + $.toJSON(lParam),
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

					lCommande.dateTimeFinResa = this.comDateFinReservation.replace('-','').replace(' ','').replace(':','');
					lCommande.dateTimeMarche = this.comDateMarcheDebut.replace('-','').replace(' ','').replace(':','');
					
					lListeCommande.commande.push(lCommande);
				});
			
			var lTemplate = lGestionCommandeTemplate.listeCommandeArchivePage;
			var lHtml = $(lTemplate.template(lListeCommande));
			lHtml = that.affect(lHtml);
			
			// Ne pas afficher la pagination si il y a moins de 15 éléments
			if(lResponse.listeCommande.length < 16) {
				lHtml = this.masquerPagination(lHtml);
			} else {
				lHtml = this.paginnation(lHtml);
			}
			
			$('#contenu').replaceWith(lHtml);
			
		} else {
			$('#contenu').replaceWith(that.affect($(lGestionCommandeTemplate.listeCommandeArchiveVide)));
		}
	};
	
	this.affect = function(pData) {
		pData = this.affectLienListeCommandeArchive(pData);
		pData = this.affectLienDetail(pData);
		pData = gCommunVue.comHoverBtn(pData);
		return pData;
	};
	
	this.affectLienListeCommandeArchive = function(pData) {
		pData.find('#lien-marche-encours').click(function() {
			GestionListeCommandeVue();
		});
		return pData;
	};
	
	this.paginnation = function(pData) {
		pData.find("#table-marche-archive")
			.tablesorter({sortList: [[2,1]],headers: { 3: {sorter: false} } })
			.tablesorterPager({container: pData.find("#content-nav-liste-operation"),positionFixed:false,size:15}); 
		return pData;
	};
	
	this.masquerPagination = function(pData) {
		pData.find('#content-nav-liste-operation').hide();
		pData.find("#table-marche-archive").tablesorter({sortList: [[2,1]],headers: { 3: {sorter: false} }});
		return pData;
	};
	
	this.affectLienDetail = function(pData) {
		pData.find('.detail-commande-ligne').click(function() {
			var lparam = {"id_marche":$(this).attr('id-marche')};
			EditerCommandeVue(lparam);
		});
		return pData;
	};
	
	this.construct(pParam);
}