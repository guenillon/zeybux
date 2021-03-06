;function ProduitBonDeLivraisonValid() { 
	this.validAjout = function(pData) { 
		var lVR = new ProduitBonDeLivraisonVR();
		//Tests Techniques
		if(pData.quantite != '' && (!pData.quantite.checkLength(0,12) || pData.quantite > 999999999.99)) {lVR.valid = false;lVR.quantite.valid = false;var erreur = new VRerreur();erreur.code = ERR_101_CODE;erreur.message = ERR_101_MSG;lVR.quantite.erreurs.push(erreur);}
		if(pData.quantite != '' && !pData.quantite.isFloat()) {lVR.valid = false;lVR.quantite.valid = false;var erreur = new VRerreur();erreur.code = ERR_109_CODE;erreur.message = ERR_109_MSG;lVR.quantite.erreurs.push(erreur);}
		if(pData.prix != '' && (!pData.prix.checkLength(0,12) || pData.prix > 999999999.99)) {lVR.valid = false;lVR.prix.valid = false;var erreur = new VRerreur();erreur.code = ERR_101_CODE;erreur.message = ERR_101_MSG;lVR.prix.erreurs.push(erreur);}
		if(pData.prix != '' && !pData.prix.isFloat()) {lVR.valid = false;lVR.prix.valid = false;var erreur = new VRerreur();erreur.code = ERR_109_CODE;erreur.message = ERR_109_MSG;lVR.prix.erreurs.push(erreur);}
		if(pData.quantiteSolidaire != '' && (!pData.quantiteSolidaire.checkLength(0,12) || pData.quantiteSolidaire > 999999999.99)) {lVR.valid = false;lVR.quantiteSolidaire.valid = false;var erreur = new VRerreur();erreur.code = ERR_101_CODE;erreur.message = ERR_101_MSG;lVR.quantiteSolidaire.erreurs.push(erreur);}
		if(pData.quantiteSolidaire != '' && !pData.quantiteSolidaire.isFloat()) {lVR.valid = false;lVR.quantiteSolidaire.valid = false;var erreur = new VRerreur();erreur.code = ERR_109_CODE;erreur.message = ERR_109_MSG;lVR.quantiteSolidaire.erreurs.push(erreur);}
		
		//Tests Fonctionnels
		if(pData.prix != '' && pData.quantite.isEmpty()) {lVR.valid = false;lVR.quantite.valid = false;var erreur = new VRerreur();erreur.code = ERR_201_CODE;erreur.message = ERR_201_MSG;lVR.quantite.erreurs.push(erreur);}
		if(pData.quantite != '' && pData.prix.isEmpty()) {lVR.valid = false;lVR.prix.valid = false;var erreur = new VRerreur();erreur.code = ERR_201_CODE;erreur.message = ERR_201_MSG;lVR.prix.erreurs.push(erreur);}

		if(pData.quantite != '' && pData.quantite < 0) {lVR.valid = false;lVR.quantite.valid = false;var erreur = new VRerreur();erreur.code = ERR_215_CODE;erreur.message = ERR_215_MSG;lVR.quantite.erreurs.push(erreur);}
		if(pData.quantiteSolidaire != '' && pData.quantiteSolidaire < 0) {lVR.valid = false;lVR.quantiteSolidaire.valid = false;var erreur = new VRerreur();erreur.code = ERR_215_CODE;erreur.message = ERR_215_MSG;lVR.quantiteSolidaire.erreurs.push(erreur);}
		if(pData.prix != '' && pData.prix < 0) {lVR.valid = false;lVR.prix.valid = false;var erreur = new VRerreur();erreur.code = ERR_215_CODE;erreur.message = ERR_215_MSG;lVR.prix.erreurs.push(erreur);}
		
		return lVR;
	};
}