;function ListeAdherentVue(pParam) {
	this.mParam = {};
	
	this.construct = function(pParam) {
		$.history( {'vue':function() {ListeAdherentVue(pParam);}} );
		var that = this;
		var lVo = {fonction:"afficher"};
		this.mParam = $.extend(lVo, pParam);
		$.post(	"./index.php?m=GestionAdherents&v=ListeAdherent", "pParam=" + toJsonURIEncode(lVo),
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
		var lGestionAdherentsTemplate = new GestionAdherentsTemplate();
		
		if(lResponse.listeAdherent.length > 0 && lResponse.listeAdherent[0].adhId != null) {
			var lTemplate = lGestionAdherentsTemplate.listeAdherent;
			
			lResponse.sigleMonetaire = gSigleMonetaire;
			$(lResponse.listeAdherent).each(function() {
				this.classSolde = '';
				if(this.cptSolde < 0){this.classSolde = "com-nombre-negatif";}
				//this.cptSolde = this.cptSolde.nombreFormate(2,',',' ');
				this.adhIdTri = this.adhNumero.replace("Z","");
				this.cptIdTri = this.cptLabel.replace("C","");
			});
			
			if(lResponse.listeAdherent.length == 1) {
				if(this.mParam.type == 3) {
					lResponse.totalAdherent = "Le non adhérent";
				} else {
					lResponse.totalAdherent = "L'adhérent";					
				}
			} else {
				if(this.mParam.type == 3) {
					lResponse.totalAdherent = "Les " + lResponse.listeAdherent.length + " non adhérents";
				} else {
					lResponse.totalAdherent = "Les " + lResponse.listeAdherent.length + " adhérents";
				}
			}
			$('#contenu').replaceWith(that.affect($(lTemplate.template(lResponse))));
		} else {
			$('#contenu').replaceWith(that.affect($(lGestionAdherentsTemplate.listeAdherentVide)));
		}
	};
	
	this.affect = function(pData) {
		pData = this.affectLienCompte(pData);
		pData = this.affectAjoutAdherent(pData);
		pData = this.affectDataTable(pData);
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
                  },

                  {"sType": "numeric",
	                "mRender": function ( data, type, full ) {
         	        	if(type !== 'sort' && data.length > 0) {
         	        		return data.nombreFormate(2,',',' ') + ' ' + gSigleMonetaire;
         	        	}
         	        	return data;
	             	},
	                "sClass":"com-text-align-right",
                	"aTargets": [ 5 ] 
                  }]
	    });
		return pData;		
	};
	

	this.affectAjoutAdherent = function(pData) {
		if(this.mParam.type == 3) {
			pData.find('#btn-nv-adherent').remove();
		} else {
			pData.find('#btn-nv-adherent').click(function() {
				AjoutAdherentVue();
			});			
		}
		return pData;
	};
				
	this.affectLienCompte = function(pData) {
		pData.find(".compte-ligne").click(function() {
			CompteAdherentVue({id: $(this).attr("id-adherent")});
		});
		return pData;
	};
	
	this.construct(pParam);
}