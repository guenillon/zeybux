module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    
    uglify: {
      compress: {
        files : {
          "outilsDev/zeybu/zeybux-jquery.min.js" : ['js/jquery/libs/jquery-*.min.js', 'js/jquery/libs/jquery-ui-*.custom.min.js', "js/jquery/plugin/*.js"],  
  		  "outilsDev/zeybu/zeybux-configuration.min.js" : ["js/classes/utils/MessagesErreurs.js", "js/Configuration/Configuration.js"],
  		  "outilsDev/zeybu/zeybux-core.min.js" : ['js/classes/**/*.js', 
  		                             'js/template/IdentificationTemplate.js', 
  		                             'js/vues/Identification/*.js',
  		                             'js/template/CoreTemplate.js',
  		                             'js/vues/Core/*.js',
  		                             'js/template/TypePaiementServiceTemplate.js',
  		                             '!js/classes/utils/MessagesErreurs.js'],
  		                             
  			"outilsDev/zeybu/package-full/zeybux-Adhesion-min.js" : ["js/template/AdhesionTemplate.js", "js/vues/Adhesion/*.js"],
  			"outilsDev/zeybu/package-full/zeybux-Caisse-min.js" : ["js/template/CaisseTemplate.js", "js/vues/Caisse/*.js"],
  			"outilsDev/zeybu/package-full/zeybux-Commande-min.js" : ["js/template/CommandeTemplate.js", "js/vues/Commande/*.js"],
  			"outilsDev/zeybu/package-full/zeybux-CompteAssociation-min.js" : ["js/template/CompteAssociationTemplate.js", "js/vues/CompteAssociation/*.js"],
  			"outilsDev/zeybu/package-full/zeybux-CompteSolidaire-min.js" : ["js/template/CompteSolidaireTemplate.js", "js/vues/CompteSolidaire/*.js"],
  			"outilsDev/zeybu/package-full/zeybux-CompteZeybu-min.js" : ["js/template/CompteZeybuTemplate.js", "js/vues/CompteZeybu/*.js"],
  			"outilsDev/zeybu/package-full/zeybux-GestionAbonnement-min.js" : ["js/template/GestionAbonnementTemplate.js", "js/vues/GestionAbonnement/*.js"],
  			"outilsDev/zeybu/package-full/zeybux-GestionAdherents-min.js" : ["js/template/GestionAdherentsTemplate.js", "js/vues/GestionAdherents/*.js"],  	
  			"outilsDev/zeybu/package-full/zeybux-GestionCaisse-min.js" : ["js/template/GestionCaisseTemplate.js", "js/vues/GestionCaisse/*.js"],
  			"outilsDev/zeybu/package-full/zeybux-GestionCommande-min.js" : ["js/template/GestionCommandeTemplate.js", "js/vues/GestionCommande/*.js"],
  			"outilsDev/zeybu/package-full/zeybux-GestionComptesSpeciaux-min.js" : ["js/template/GestionComptesSpeciauxTemplate.js", "js/vues/GestionComptesSpeciaux/*.js"],
  			"outilsDev/zeybu/package-full/zeybux-GestionProducteur-min.js" : ["js/template/GestionProducteurTemplate.js", "js/vues/GestionProducteur/*.js"],
  			"outilsDev/zeybu/package-full/zeybux-GestionProduit-min.js" : ["js/template/GestionProduitTemplate.js", "js/vues/GestionProduit/*.js"],
  			"outilsDev/zeybu/package-full/zeybux-MonCompte-min.js" : ["js/template/MonCompteTemplate.js", "js/vues/MonCompte/*.js"],
  			"outilsDev/zeybu/package-full/zeybux-Parametrage-min.js" : ["js/template/ParametrageTemplate.js", "js/vues/Parametrage/*.js"],	                             
  			"outilsDev/zeybu/package-full/zeybux-RechargementCompte-min.js" : ["js/template/RechargementCompteTemplate.js", "js/vues/RechargementCompte/*.js"],
  
        }
      }
    },
    cssmin: {
    	zeybux: {
    	    files: [{
    	    	'outilsDev/zeybu/zeybux.min.css': ['css/**/*.css','!**/*.min.css', '!**/themes/**', '!**/Entete.css', '!**/MonCompteHTML/**', '!**/zeybux-html.css', '!**/zeybux.css']
    	    }]
    	  },
    	  zeybux_html: {
      	    files: [{
      	    	'outilsDev/zeybu/zeybux-html.min.css': ['!**.min.css', 'css/MonCompteHTML/*.css',  '!**/zeybux-html.css', '!**/zeybux.css']
      	    }]
      	  }
    },
    watch: {
        files: ["js/jquery/*.js"],
        tasks: ['goUgly']
   }
  });
  

  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-watch');
  
  grunt.registerTask('goUgly', ['uglify:compress']);
  grunt.registerTask('goCss', ['cssmin:zeybux', 'cssmin:zeybux_html']);
  grunt.registerTask('goCssOld', ['goConcat', 'cssmin:zeybux', 'cssmin:zeybux_html']);
  grunt.registerTask('goConcat', ['concat:css_zeybux', 'concat:css_zeybux_html']);
};