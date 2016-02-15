;function AccueilVue(pParam) {
	this.construct = function(pParam) {
		if(pParam && pParam.vr) {
			Infobulle.generer(pParam.vr,'');
		}
		this.afficher();
	};	
	
	this.afficher = function() {
		var lIdentificationTemplate = new IdentificationTemplate();
		if($.browser.msie && $.browser.version < 11) {
			
			$('noscript').replaceWith(lIdentificationTemplate.infoNavIncompatible);
		} else {	
			var that = this;
			if(!$.trim($("#contenu").html())) {
				$("#contenu").prepend(lIdentificationTemplate.formConnexion);
			}
			$.getScript("./js/zeybux-configuration.php",function() {
				that.init();
				IdentificationVue();
			});
		}		
	};
	
	this.init = function() {
		this.initObj();
		this.initAction();
	};
	
	this.initObj = function() {
		// Initialisation des objets globaux
		TemplateData = new TemplateData();
		Infobulle = new Infobulles();
		gCommunVue = new CommunVue();
		gIdConnexion = null;
	};
	
	this.initAction = function() {
		// Affichage des infobulles pour les erreurs	
		$("#widget_message_information").click(function() {$(this).delay(gTempsTransition).fadeOut(gTempsTransitionUnique);});		
		/*$("#loading").ajaxStart( function() {$(this).fadeIn(gTempsTransition);} );
		$("#loading").ajaxStop( function() {$(this).fadeOut(gTempsTransition);} );	*/	

		$.history.callback = function ( pReinstate, pCursor ) {
			var lDefault = {
				vue: function() { return false; }
			};
			lDefault = $.extend(lDefault,pReinstate);
			// check to see if were back to the beginning without any stored data
			if (typeof(pReinstate) == 'undefined') { return false; }
			else { $(".ui-dialog").remove(); lDefault.vue(); }
		};
		
		
		
	};
	
	this.construct(pParam);
}