;function ProduitAbonnementValid() { 
	this.validAjout = function(pData) { 
		var lVR = new ProduitAbonnementVR();
		//Tests Techniques
		if(!pData.idNomProduit.checkLength(0,11)) {lVR.valid = false;lVR.idNomProduit.valid = false;var erreur = new VRerreur();erreur.code = ERR_101_CODE;erreur.message = ERR_101_MSG;lVR.idNomProduit.erreurs.push(erreur);}
		if(isNaN(parseInt(pData.idNomProduit))) {lVR.valid = false;lVR.idNomProduit.valid = false;var erreur = new VRerreur();erreur.code = ERR_104_CODE;erreur.message = ERR_104_MSG;lVR.idNomProduit.erreurs.push(erreur);}
		if(!pData.unite.checkLength(0,20)) {lVR.valid = false;lVR.unite.valid = false;var erreur = new VRerreur();erreur.code = ERR_101_CODE;erreur.message = ERR_101_MSG;lVR.unite.erreurs.push(erreur);}
		if(!pData.stockInitial.checkLength(0,12) || pData.stockInitial > 999999999.99) {lVR.valid = false;lVR.stockInitial.valid = false;var erreur = new VRerreur();erreur.code = ERR_101_CODE;erreur.message = ERR_101_MSG;lVR.stockInitial.erreurs.push(erreur);}
		if(!pData.stockInitial.isFloat()) {lVR.valid = false;lVR.stockInitial.valid = false;var erreur = new VRerreur();erreur.code = ERR_109_CODE;erreur.message = ERR_109_MSG;lVR.stockInitial.erreurs.push(erreur);}
		if(!pData.max.checkLength(0,12) || pData.max > 999999999.99) {lVR.valid = false;lVR.max.valid = false;var erreur = new VRerreur();erreur.code = ERR_101_CODE;erreur.message = ERR_101_MSG;lVR.max.erreurs.push(erreur);}
		if(!pData.max.isFloat()) {lVR.valid = false;lVR.max.valid = false;var erreur = new VRerreur();erreur.code = ERR_109_CODE;erreur.message = ERR_109_MSG;lVR.max.erreurs.push(erreur);}
		if(!pData.frequence.checkLength(0,200)) {lVR.valid = false;lVR.frequence.valid = false;var erreur = new VRerreur();erreur.code = ERR_101_CODE;erreur.message = ERR_101_MSG;lVR.frequence.erreurs.push(erreur);}
		
		//Tests Fonctionnels
		if(pData.idNomProduit.isEmpty()) {lVR.valid = false;lVR.idNomProduit.valid = false;var erreur = new VRerreur();erreur.code = ERR_201_CODE;erreur.message = ERR_201_MSG;lVR.idNomProduit.erreurs.push(erreur);}
		if(pData.unite.isEmpty()) {lVR.valid = false;lVR.unite.valid = false;var erreur = new VRerreur();erreur.code = ERR_201_CODE;erreur.message = ERR_201_MSG;lVR.unite.erreurs.push(erreur);}
		if(pData.stockInitial.isEmpty()) {lVR.valid = false;lVR.stockInitial.valid = false;var erreur = new VRerreur();erreur.code = ERR_201_CODE;erreur.message = ERR_201_MSG;lVR.stockInitial.erreurs.push(erreur);}
		if(pData.max.isEmpty()) {lVR.valid = false;lVR.max.valid = false;var erreur = new VRerreur();erreur.code = ERR_201_CODE;erreur.message = ERR_201_MSG;lVR.max.erreurs.push(erreur);}
		if(pData.frequence.isEmpty()) {lVR.valid = false;lVR.frequence.valid = false;var erreur = new VRerreur();erreur.code = ERR_201_CODE;erreur.message = ERR_201_MSG;lVR.frequence.erreurs.push(erreur);}

		if(pData.stockInitial <= 0 ) {lVR.valid = false;lVR.stockInitial.valid = false;var erreur = new VRerreur();erreur.code = ERR_215_CODE;erreur.message = ERR_215_MSG;lVR.stockInitial.erreurs.push(erreur);}
		if(pData.max <= 0 && pData.max != -1) {lVR.valid = false;lVR.max.valid = false;var erreur = new VRerreur();erreur.code = ERR_215_CODE;erreur.message = ERR_215_MSG;lVR.max.erreurs.push(erreur);}
		if(pData.max != -1 && parseFloat(pData.max) > parseFloat(pData.stockInitial)) {lVR.valid = false;lVR.stockInitial.valid = false;lVR.max.valid = false;var erreur = new VRerreur();erreur.code = ERR_205_CODE;erreur.message = ERR_205_MSG;lVR.stockInitial.erreurs.push(erreur);lVR.max.erreurs.push(erreur);}
		
		//Tests des Lots
		if(isArray(pData.lots)) {
			if(pData.lots.length > 0) {
				var lValidLot = new DetailCommandeValid();
				var i = 0, lPetitLotTaille = pData.lots[0].taille;
				while(pData.lots[i]) {
					var lVrLot = lValidLot.validAjout(pData.lots[i]);				
					if(!lVrLot.valid){lVR.valid = false;}
					//if(parseFloat(pData.lots[i].taille) > parseFloat(pData.qteMaxCommande)) {lVR.valid = false;lVrLot.valid = false;lVrLot.taille.valid = false;var erreur = new VRerreur();erreur.code = ERR_206_CODE;erreur.message = ERR_206_MSG;lVrLot.taille.erreurs.push(erreur);}
					if(parseFloat(pData.lots[i].taille) < lPetitLotTaille) { lPetitLotTaille = parseFloat(pData.lots[i].taille); }
					lVR.lots.push(lVrLot);
					i++;
				}
				if(pData.qteMaxCommande != -1 && lPetitLotTaille > parseFloat(pData.qteMaxCommande)) {lVR.valid = false;lVR.qteMaxCommande.valid = false;var erreur = new VRerreur();erreur.code = ERR_241_CODE;erreur.message = ERR_241_MSG;lVR.qteMaxCommande.erreurs.push(erreur);}
				if(pData.qteRestante != -1 && lPetitLotTaille > parseFloat(pData.qteRestante)) {lVR.valid = false;lVR.qteRestante.valid = false;var erreur = new VRerreur();erreur.code = ERR_241_CODE;erreur.message = ERR_241_MSG;lVR.qteRestante.erreurs.push(erreur);}
				
			} else  {lVR.valid = false;lVR.log.valid = false;var erreur = new VRerreur();erreur.code = ERR_243_CODE;erreur.message = ERR_243_MSG;lVR.log.erreurs.push(erreur);}
			
		} else {lVR.valid = false;lVR.log.valid = false;var erreur = new VRerreur();erreur.code = ERR_110_CODE;erreur.message = ERR_110_MSG;lVR.log.erreurs.push(erreur);}
		
		return lVR;
	};
	
	this.validDelete = function(pData) { 
		var lVR = new ProduitAbonnementVR();
		//Tests Techniques
		if(!pData.id.checkLength(0,11)) {lVR.valid = false;lVR.id.valid = false;var erreur = new VRerreur();erreur.code = ERR_101_CODE;erreur.message = ERR_101_MSG;lVR.id.erreurs.push(erreur);}
		if(isNaN(parseInt(pData.id))) {lVR.valid = false;lVR.id.valid = false;var erreur = new VRerreur();erreur.code = ERR_104_CODE;erreur.message = ERR_104_MSG;lVR.id.erreurs.push(erreur);}
		
		//Tests Fonctionnels
		if(pData.id.isEmpty()) {lVR.valid = false;lVR.id.valid = false;var erreur = new VRerreur();erreur.code = ERR_201_CODE;erreur.message = ERR_201_MSG;lVR.id.erreurs.push(erreur);}
		return lVR;
	};
	
	this.validUpdate = function(pData) { 
		var lVR = new ProduitAbonnementVR();
		//Tests Techniques
		if(!pData.id.checkLength(0,11)) {lVR.valid = false;lVR.id.valid = false;var erreur = new VRerreur();erreur.code = ERR_101_CODE;erreur.message = ERR_101_MSG;lVR.id.erreurs.push(erreur);}
		if(isNaN(parseInt(pData.id))) {lVR.valid = false;lVR.id.valid = false;var erreur = new VRerreur();erreur.code = ERR_104_CODE;erreur.message = ERR_104_MSG;lVR.id.erreurs.push(erreur);}
		if(!pData.unite.checkLength(0,20)) {lVR.valid = false;lVR.unite.valid = false;var erreur = new VRerreur();erreur.code = ERR_101_CODE;erreur.message = ERR_101_MSG;lVR.unite.erreurs.push(erreur);}
		if(!pData.stockInitial.checkLength(0,12) || pData.stockInitial > 999999999.99) {lVR.valid = false;lVR.stockInitial.valid = false;var erreur = new VRerreur();erreur.code = ERR_101_CODE;erreur.message = ERR_101_MSG;lVR.stockInitial.erreurs.push(erreur);}
		if(!pData.stockInitial.isFloat()) {lVR.valid = false;lVR.stockInitial.valid = false;var erreur = new VRerreur();erreur.code = ERR_109_CODE;erreur.message = ERR_109_MSG;lVR.stockInitial.erreurs.push(erreur);}
		if(!pData.max.checkLength(0,12) || pData.max > 999999999.99) {lVR.valid = false;lVR.max.valid = false;var erreur = new VRerreur();erreur.code = ERR_101_CODE;erreur.message = ERR_101_MSG;lVR.max.erreurs.push(erreur);}
		if(!pData.max.isFloat()) {lVR.valid = false;lVR.max.valid = false;var erreur = new VRerreur();erreur.code = ERR_109_CODE;erreur.message = ERR_109_MSG;lVR.max.erreurs.push(erreur);}
		if(!pData.frequence.checkLength(0,200)) {lVR.valid = false;lVR.frequence.valid = false;var erreur = new VRerreur();erreur.code = ERR_101_CODE;erreur.message = ERR_101_MSG;lVR.frequence.erreurs.push(erreur);}
		
		//Tests Fonctionnels
		if(pData.id.isEmpty()) {lVR.valid = false;lVR.id.valid = false;var erreur = new VRerreur();erreur.code = ERR_201_CODE;erreur.message = ERR_201_MSG;lVR.id.erreurs.push(erreur);}
		if(pData.unite.isEmpty()) {lVR.valid = false;lVR.unite.valid = false;var erreur = new VRerreur();erreur.code = ERR_201_CODE;erreur.message = ERR_201_MSG;lVR.unite.erreurs.push(erreur);}
		if(pData.stockInitial.isEmpty()) {lVR.valid = false;lVR.stockInitial.valid = false;var erreur = new VRerreur();erreur.code = ERR_201_CODE;erreur.message = ERR_201_MSG;lVR.stockInitial.erreurs.push(erreur);}
		if(pData.max.isEmpty()) {lVR.valid = false;lVR.max.valid = false;var erreur = new VRerreur();erreur.code = ERR_201_CODE;erreur.message = ERR_201_MSG;lVR.max.erreurs.push(erreur);}
		if(pData.frequence.isEmpty()) {lVR.valid = false;lVR.frequence.valid = false;var erreur = new VRerreur();erreur.code = ERR_201_CODE;erreur.message = ERR_201_MSG;lVR.frequence.erreurs.push(erreur);}

		if(pData.stockInitial <= 0 ) {lVR.valid = false;lVR.stockInitial.valid = false;var erreur = new VRerreur();erreur.code = ERR_215_CODE;erreur.message = ERR_215_MSG;lVR.stockInitial.erreurs.push(erreur);}
		if(pData.max <= 0 && pData.max != -1) {lVR.valid = false;lVR.max.valid = false;var erreur = new VRerreur();erreur.code = ERR_215_CODE;erreur.message = ERR_215_MSG;lVR.max.erreurs.push(erreur);}
		if(pData.max != -1 && parseFloat(pData.max) > parseFloat(pData.stockInitial)) {lVR.valid = false;lVR.stockInitial.valid = false;lVR.max.valid = false;var erreur = new VRerreur();erreur.code = ERR_205_CODE;erreur.message = ERR_205_MSG;lVR.stockInitial.erreurs.push(erreur);lVR.max.erreurs.push(erreur);}
		
		if(pData.quantiteReservation != -1 && parseFloat(pData.stockInitial) < pData.quantiteReservation) {lVR.valid = false;lVR.stockInitial.valid = false;var erreur = new VRerreur();erreur.code = ERR_259_CODE;erreur.message = ERR_259_MSG;lVR.stockInitial.erreurs.push(erreur);}
		if(pData.max != -1 && pData.tailleLotResaMax != -1 && parseFloat(pData.max) < pData.tailleLotResaMax) {lVR.valid = false;lVR.max.valid = false;var erreur = new VRerreur();erreur.code = ERR_260_CODE;erreur.message = ERR_260_MSG;lVR.max.erreurs.push(erreur);}
		
		
		//Tests des Lots
		if(isArray(pData.lots)) {
			if(pData.lots.length > 0) {
				var lValidLot = new DetailCommandeValid();
				var i = 0, lPetitLotTaille = pData.lots[0].taille;
				while(pData.lots[i]) {
					var lVrLot = lValidLot.validAjout(pData.lots[i]);				
					if(!lVrLot.valid){lVR.valid = false;}
					//if(parseFloat(pData.lots[i].taille) > parseFloat(pData.qteMaxCommande)) {lVR.valid = false;lVrLot.valid = false;lVrLot.taille.valid = false;var erreur = new VRerreur();erreur.code = ERR_206_CODE;erreur.message = ERR_206_MSG;lVrLot.taille.erreurs.push(erreur);}
					if(parseFloat(pData.lots[i].taille) < lPetitLotTaille) { lPetitLotTaille = parseFloat(pData.lots[i].taille); }
					lVR.lots.push(lVrLot);
					i++;
				}
				if(pData.qteMaxCommande != -1 && lPetitLotTaille > parseFloat(pData.qteMaxCommande)) {lVR.valid = false;lVR.qteMaxCommande.valid = false;var erreur = new VRerreur();erreur.code = ERR_241_CODE;erreur.message = ERR_241_MSG;lVR.qteMaxCommande.erreurs.push(erreur);}
				if(pData.qteRestante != -1 && lPetitLotTaille > parseFloat(pData.qteRestante)) {lVR.valid = false;lVR.qteRestante.valid = false;var erreur = new VRerreur();erreur.code = ERR_241_CODE;erreur.message = ERR_241_MSG;lVR.qteRestante.erreurs.push(erreur);}
				
			} else  {lVR.valid = false;lVR.log.valid = false;var erreur = new VRerreur();erreur.code = ERR_243_CODE;erreur.message = ERR_243_MSG;lVR.log.erreurs.push(erreur);}
			
		} else {lVR.valid = false;lVR.log.valid = false;var erreur = new VRerreur();erreur.code = ERR_110_CODE;erreur.message = ERR_110_MSG;lVR.log.erreurs.push(erreur);}
		
		if(!isArray(pData.lotRemplacement)) {lVR.valid = false;lVR.log.valid = false;var erreur = new VRerreur();erreur.code = ERR_110_CODE;erreur.message = ERR_110_MSG;lVR.log.erreurs.push(erreur);}
		
		return lVR;
	};

}