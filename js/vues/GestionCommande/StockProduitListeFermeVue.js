;function StockProduitListeFermeVue(pParam) {
	this.mListeFerme = {};
	
	this.construct = function(pParam) {
		$.history( {'vue':function() {StockProduitListeFermeVue(pParam);}} );
		var that = this;
		var lParam = {fonction:"ListeFerme"};
		$.post(	"./index.php?m=GestionCommande&v=StockProduit", "pParam=" + $.toJSON(lParam),
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
		
		if(lResponse.listeFerme.length > 0 && lResponse.listeFerme[0].ferId != null) {
			var lTemplate = lGestionCommandeTemplate.listeFerme;
			$.each(lResponse.listeFerme,function() {
				this.ferIdTri = this.ferNumero.replace("F","");
			});
			this.mListeFerme = lResponse.listeFerme;
			$('#contenu').replaceWith(that.affect($(lTemplate.template(lResponse))));
		} else {
			$('#contenu').replaceWith(that.affect($(lGestionCommandeTemplate.listeFermeVide)));
		}
		
	};
	
	this.affect = function(pData) {
		pData = this.affectTri(pData);
		pData = this.affectRecherche(pData);
		pData = this.affectDetailFerme(pData);		
		pData = this.affectExport(pData);
		pData = gCommunVue.comHoverBtn(pData);
		pData = gCommunVue.comNumeric(pData);
		return pData;
	};
		
	this.affectTri = function(pData) {
		pData.find('.com-table').tablesorter({sortList: [[0,0]]});
		return pData;
	};
	
	this.affectRecherche = function(pData) {
		pData.find("#filter").keyup(function() {
		    $.uiTableFilter( $('.com-table'), this.value );
		  });
		
		pData.find("#filter-form").submit(function () {return false;});
		
		return pData;
	};
			
	this.affectDetailFerme = function(pData) {
		pData.find(".compte-ligne").click(function() {
			StockProduitVue({idCompte:$(this).data('id-compte-ferme')});
		});
		return pData;
	};
	
	this.affectExport = function(pData) {	
		var that = this;
		pData.find('#btn-export').click(function() {
			that.dialogExport();
		});
		return pData;
	};
	
	this.dialogExport = function() {
		var lGestionCommandeTemplate = new GestionCommandeTemplate();
		var lDialog = $(lGestionCommandeTemplate.dialogExportStock.template({listeFerme:this.mListeFerme})).dialog({
			autoOpen: true,
			modal: true,
			draggable: false,
			resizable: false,
			width:800,
			buttons: {
				'Exporter': function() {
					// Récupération du formulaire
					var lIdFermes = '';
					$('input:checked',lTable.fnGetNodes()).each(function() {
						lIdFermes += $(this).val() + ',';
					});
					lIdFermes = lIdFermes.substr(0,lIdFermes.length-1);
					// Affichage
					$.download("./index.php?m=GestionCommande&v=StockProduit", {fonction:'export',id_fermes:lIdFermes});
					// Déselectionne les fermes
					$(":input[name=id_fermes]",lTable.fnGetNodes()).prop("checked", false);
				},
				'Annuler': function() {
					$(this).dialog('close');
				}
			},
			close: function(ev, ui) { $(this).remove(); Infobulle.init(); }
		});
		
		var lTable = lDialog.find('#liste-ferme')
			.dataTable({								
					"bJQueryUI": true,
			        "sPaginationType": "full_numbers",
			        "oLanguage": gDataTablesFr,
			        "iDisplayLength": 10,
					"aoColumnDefs": [
					    {	"bSortable": false, 
		  	                "bSearchable":false,
		  	                "aTargets": [ 0 ] 
	  	              	},
						{	 "sType": "numeric",
							 "mRender": function ( data, type, full ) {
								  	if (type === 'sort') {
							          return data.replace("F","");
							        }
							        return data;
							      },
							"aTargets": [ 1 ]
						 },
						 {	 "sType": "numeric",
						   	 "mRender": function ( data, type, full ) {
						   		  	if (type === 'sort') {
						   		  		data = data.replace("CF","");
						   		  		return data.replace("C","");
						   	        }
						   	        return data;
						   	      },
						   "aTargets": [ 2 ]
						 }]
			});
			
		// Bouton selection de tous les produits
		lDialog.find("#button-tp").click(function() {
			$(":input[name=id_fermes]",lTable.fnGetNodes()).prop("checked", true);
		});
		// Déselectionne les produits
		lDialog.find("#button-ap").click(function() {
			$(":input[name=id_fermes]",lTable.fnGetNodes()).prop("checked", false);
		});
	};
	
		
	this.construct(pParam);
};