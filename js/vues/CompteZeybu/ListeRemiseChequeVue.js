;function ListeRemiseChequeVue(pParam) {
	
	this.construct = function(pParam) {
		$.history( {'vue':function() {ListeRemiseChequeVue(pParam);}} );
		var that = this;	
		var lParam = {};

		if(pParam && pParam.etat && pParam.etat == 1) { // Les remise archivées
			lParam.fonction = 'listeEncaissee';	
		} else { // Les remise en cours		
			lParam.fonction = 'listeActive';
		}
		
		$.post(	"./index.php?m=CompteZeybu&v=RemiseCheque", "pParam=" + toJsonURIEncode(lParam),
			function(lResponse) {
				Infobulle.init(); // Supprime les erreurs
				if(lResponse) {
					if(lResponse.valid) {
						if(pParam && pParam.vr) {
							Infobulle.generer(pParam.vr,'');
						}
						var lCompteZeybuTemplate = new CompteZeybuTemplate();						
						// RAZ si vide
						if(lResponse.liste[0].id == null) { lResponse.liste = [];}
						
						if(pParam && pParam.etat && pParam.etat == 1) {
							$('#contenu').replaceWith(that.affect($(lCompteZeybuTemplate.listeRemiseChequeArchive.template(lResponse))));
						} else {
							$('#contenu').replaceWith(that.affect($(lCompteZeybuTemplate.listeRemiseCheque.template(lResponse))));
						}
					} else {
						Infobulle.generer(lResponse,'');
					}
				}
			},"json"
		);
	};
	
	this.affect = function(pData) {
		pData = this.affectDetail(pData);
		pData = this.affectBtn(pData);
		pData = gCommunVue.comHoverBtn(pData);
		pData = this.affectDataTable(pData);
		return pData;
	};

	this.affectDetail = function(pData) {
		pData.find(".btn-detail-remise").click(function() {
			DetailRemiseChequeVue({id:$(this).data('id')});
		});
		return pData;
	};
	
	this.affectBtn = function(pData) {
		var that = this;
		pData.find("#lien-remise-archive").click(function() {
			that.construct({etat:1});
		});
		pData.find("#lien-remise-encours").click(function() {
			that.construct({etat:0});
		});
		return pData;
	};
	
	this.affectDataTable = function(pData) {		
		pData.find('#table-liste-remise-cheque').dataTable({
	        "bJQueryUI": true,
	        "sPaginationType": "full_numbers",
	        "oLanguage": gDataTablesFr,
	   //     "iDisplayLength": 10,
	        "aaSorting": [[1,'asc']],
	        "aoColumnDefs": [
                  {"sType": "date",
	                "mRender": function ( data, type, full ) {
                    	if (type === 'sort') {
                    		return data.replace(' ','T');
                    	}
	                	return data.extractDbDate().dateDbToFr();
	                },
	                "aTargets": [ 1 ]
	              }, 
                  {"sType": "numeric",
	                "mRender": function ( data, type, full ) {
         	        	if(type !== 'sort' && data.length > 0) {
         	        		return data.nombreFormate(2,',',' ') + ' ' + gSigleMonetaire;
         	        	}
         	        	return data;
	             	},
	                "sClass":"com-text-align-right",
                	"aTargets": [ 2 ] 
                  }]
	    });	
		return pData;
	};
	
	this.construct(pParam);
};