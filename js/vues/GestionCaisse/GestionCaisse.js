;function GestionCaisseVue(pParam) {	
	this.etatCaisse = 0;
	
	this.construct = function(pParam) {
		$.history( {'vue':function() {GestionCaisseVue(pParam);}} );
		var that = this;
		var lParam = {'fonction':'etatCaisse'};
		$.post(	"./index.php?m=GestionCaisse&v=GestionCaisse", "pParam=" + toJsonURIEncode(lParam),
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
		this.etatCaisse = lResponse.etat;	
		
		var lGestionCaisseTemplate = new GestionCaisseTemplate();
		
		var lHtml = lGestionCaisseTemplate.etatCaisseDebut;		
		if(lResponse.etat == 1) {
			lHtml += lGestionCaisseTemplate.caisseOuverte;
		} else {
			lHtml += lGestionCaisseTemplate.caisseFermee;			
		}
		
		lHtml += lGestionCaisseTemplate.etatCaisseMilieu;
		
		if(lResponse.etat == 1) {
			lHtml += lGestionCaisseTemplate.boutonFermeture;
		} else {
			lHtml += lGestionCaisseTemplate.boutonOuverture;			
		}
		
		lHtml += lGestionCaisseTemplate.etatCaisseFin;
		
		lHtml = $(lHtml);
		
		$('#contenu').replaceWith(that.affect(lHtml));	
	};
	
	this.affect = function(pData) {
		pData = affectChangerEtatCaisse(pData);
		pData = gCommunVue.comHoverBtn(pData);
		return pData;
	};
	
	this.affectChangerEtatCaisse = function(pData) {
		if(this.etatCaisse == 1) {
			pData = this.affectFermerCaisse(pData);
		} else {
			pData = this.affectOuvrirCaisse(pData);
		}
		return pData;
	};
	
	this.affectFermerCaisse = function(pData) {
		var that = this;
		pData.find("#btn-caisse").click(function() {
			var lParam = {'fonction':'fermerCaisse'};
			$.post(	"./index.php?m=GestionCaisse&v=GestionCaisse", "pParam=" + toJsonURIEncode(lParam),
					function(lResponse) {
						Infobulle.init(); // Supprime les erreurs
						if(lResponse) {
							if(lResponse.valid) {
								that.construct();
							} else {
								Infobulle.generer(lResponse,'');
							}
						}
					},"json"
			);
		});
		return pData;
	};
	
	this.affectOuvrirCaisse = function(pData) {
		var that = this;
		pData.find("#btn-caisse").click(function() {
			var lParam = {'fonction':'ouvrirCaisse'};
			$.post(	"./index.php?m=GestionCaisse&v=GestionCaisse", "pParam=" + toJsonURIEncode(lParam),
					function(lResponse) {
						Infobulle.init(); // Supprime les erreurs
						if(lResponse) {
							if(lResponse.valid) {
								that.construct();
							} else {
								Infobulle.generer(lResponse,'');
							}
						}
					},"json"
			);
		});
		return pData;
	};
	
	this.construct(pParam);
}