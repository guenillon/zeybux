;function ReservationSansAchatVue(pParam) {	
	this.mIdMarche = 0;
	
	this.construct = function(pParam) {
		$.history( {'vue':function() {ReservationSansAchatVue(pParam);}} );
		var that = this;
		pParam.fonction = 'afficher';
		this.mIdMarche = pParam.id_marche;
		$.post(	"./index.php?m=GestionCommande&v=ReservationSansAchat", "pParam=" + $.toJSON(pParam),
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
	
	this.afficher = function(pResponse) {		
		var that = this;
		var lGestionCommandeTemplate = new GestionCommandeTemplate();
		var lTemplate = lGestionCommandeTemplate.listeReservationSansAchat;

		pResponse.infoMarcheSelected = '';
		pResponse.listeReservationSelected = '';
		pResponse.listeAchatSelected = '';
		pResponse.resumeMarcheSelected = '';
		pResponse.reservationSansAchatSelected = 'ui-state-active';

		pResponse.editerMenu = lGestionCommandeTemplate.editerMarcheMenu.template(pResponse);

		$('#contenu').replaceWith(that.affect($(lTemplate.template(pResponse))));
	};
	
	this.affect = function(pData) {
		pData = this.affectMenu(pData);
		pData = this.affectReservation(pData);
		pData = gCommunVue.comHoverBtn(pData);
		pData = this.affectDataTable(pData);
		return pData;
	};
	
	this.affectMenu = function(pData) {
		var that = this;
		pData.find('#btn-information-marche').click(function() {
			EditerCommandeVue(that.mParam);
		});		
		pData.find("#btn-liste-achat-resa").click(function() {
			ListeAchatMarcheVue({id_marche:that.mIdMarche});
		});
		pData.find("#btn-liste-resa").click(function() {
			ListeReservationMarcheVue({id_marche:that.mIdMarche});
		});
		pData.find("#btn-resume-marche").click(function() {
			ResumeMarcheVue({id_marche:that.mIdMarche});
		});
		pData.find("#btn-reservation-noachat").click(function() {
			ReservationSansAchatVue({id_marche:that.mIdMarche});
		});
		return pData;
	};

	this.affectReservation = function(pData) {
		var that = this;
		pData.find('.compte-ligne').click(function() {
			ReservationAdherentVue({"id_commande":that.mIdMarche,"id_adherent":$(this).attr('id-adherent'), retour:"noAchat"});
		});
		return pData;
	};
	
	this.affectDataTable = function(pData) {
		pData.find('#liste-adherent').dataTable({
	        "bJQueryUI": true,
	        "sPaginationType": "full_numbers",
	        "oLanguage": gDataTablesFr,
	        "iDisplayLength": 25,
	        "aaSorting": [[2,'asc'], [3,'asc']],
	        "aoColumnDefs": [
                  {	 "sType": "numeric",
                	 "mRender": function ( data, type, full ) {
                		  	if (type === 'sort') {
                	          return data.replace("Z","");
                	        }
                	        return data;
                	      },
                	"aTargets": [ 0 ]
                  },
                  {	 "sType": "numeric",
                    	 "mRender": function ( data, type, full ) {
                    		  	if (type === 'sort') {
                    	          return data.replace("C","");
                    	        }
                    	        return data;
                    	      },
                    "aTargets": [ 1 ]
                  }]
	    });
		return pData;		
	};
	
	this.construct(pParam);
}