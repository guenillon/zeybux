<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 10/04/2012
// Fichier : ListeAchatMarcheControleur.php
//
// Description : Classe ListeAchatMarcheControleur
//
//****************************************************************

// Inclusion des classes
include_once(CHEMIN_CLASSES_VALIDATEUR . MOD_GESTION_COMMANDE . "/EditerCommandeValid.php" );
include_once(CHEMIN_CLASSES_VALIDATEUR . MOD_GESTION_COMMANDE . "/ExportListeAchatEtReservationValid.php" );
include_once(CHEMIN_CLASSES_SERVICE . "MarcheService.php");
include_once(CHEMIN_CLASSES_SERVICE . "AchatService.php");
include_once(CHEMIN_CLASSES_SERVICE . "ReservationService.php");
include_once(CHEMIN_CLASSES_UTILS . "CSV.php");
include_once(CHEMIN_CLASSES_UTILS . "phpToPDF.php");
include_once(CHEMIN_CLASSES_VIEW_MANAGER . "AdherentViewManager.php");
include_once(CHEMIN_CLASSES_VO . "IdAchatVO.php");


include_once(CHEMIN_CLASSES_RESPONSE . MOD_GESTION_COMMANDE . "/ListeAchatEtReservationResponse.php" );
include_once(CHEMIN_CLASSES_MANAGERS . "AdherentManager.php" );


/**
 * @name ListeAchatMarcheControleur
 * @author Julien PIERRE
 * @since 27/10/2010
 * @desc Classe controleur d'une EditerCommande
 */
class ListeAchatMarcheControleur
{	
	/**
	* @name getListeAchatEtReservation($pParam)
	* @param Id du marché
	* @desc Retourne la liste des adhérents un flag achat et réservation sur le marché
	*/
	public function getListeAchatEtReservation($pParam) {		
		$lVr = EditerCommandeValid::validGetInfoCommande($pParam);
		if($lVr->getValid()) {
			$lResponse = new ListeAchatEtReservationResponse();
			
			// Les achats adhérents
			$lMarcheService = new MarcheService();
			$lResponse->setListeAchatEtReservation($lMarcheService->getListeAdherentAchatMarche($pParam["id_marche"]) );
			
			// Les achats du compte invité				
			$lAchatService = new AchatService();
			$lResponse->setListeAchatInvite($lAchatService->selectOperationAchat(-3,$pParam["id_marche"]));
			
			return $lResponse;
		}
		return $lVr;
	}
	
	/**
	 * @name getListeAchatEtReservationExport($pParam)
	 * @return array()
	 * @desc Retourne la liste des achats et réservations pour une commande et la liste de produits demandés
	 */
	private function getListeAchatEtReservationExport($pParam) {
		$lIdProduits = $pParam['id_produits'];
	
		$lAchatService = new AchatService();
		$lAchatsEtReservations = $lAchatService->getAchatEtReservationProduit($lIdProduits);
	
		// Mise en forme des données par produit
		$lTableauAR = array();
		$lQuantiteAR = array();
	
		foreach($lAchatsEtReservations as $lReservation) {
			$lLigne = array();
				
			$lLigne['compte'] = $lReservation->getCptLabel();
			$lLigne['prenom'] = $lReservation->getAdhPrenom();
			$lLigne['nom'] = $lReservation->getAdhNom();
			$lLigne['telephonePrincipal'] = $lReservation->getAdhTelephonePrincipal();
			
			if(isset($lTableauAR[$lReservation->getCptLabel()])) {
				if(!is_null($lReservation->getStoQuantiteReservation()) ) {$lTableauAR[$lLigne['compte']][$lReservation->getProId()]['reservation'] = $lReservation->getStoQuantiteReservation() * -1;}
				if(!is_null($lReservation->getStoQuantiteAchat()) ) {$lTableauAR[$lLigne['compte']][$lReservation->getProId()]['qteAchat'] = $lReservation->getStoQuantiteAchat() * -1;}
				if(!is_null($lReservation->getDopeMontantAchat()) ) {$lTableauAR[$lLigne['compte']][$lReservation->getProId()]['prixAchat'] = $lReservation->getDopeMontantAchat() * -1;}
				if(!is_null($lReservation->getStoQuantiteSolidaire()) ) {$lTableauAR[$lLigne['compte']][$lReservation->getProId()]['qteSolidaire'] = $lReservation->getStoQuantiteSolidaire() * -1;}
				if(!is_null($lReservation->getDopeMontantSolidaire()) ) {$lTableauAR[$lLigne['compte']][$lReservation->getProId()]['prixSolidaire'] = $lReservation->getDopeMontantSolidaire() * -1;}
				$lTableauAR[$lLigne['compte']][$lReservation->getProId()]['unite'] = $lReservation->getProUniteMesure();
			} else {
				foreach($lIdProduits as $lIdProduit) {
					$lLigne[$lIdProduit] = array();
					$lLigne[$lIdProduit]['reservation'] = '';
					$lLigne[$lIdProduit]['qteAchat'] = '';
					$lLigne[$lIdProduit]['prixAchat'] = '';
					$lLigne[$lIdProduit]['qteSolidaire'] = '';
					$lLigne[$lIdProduit]['prixSolidaire'] = '';
					$lLigne[$lIdProduit]['unite'] = '';
					if($lReservation->getProId() == $lIdProduit) {
						if(!is_null($lReservation->getStoQuantiteReservation()) ) {$lLigne[$lIdProduit]['reservation'] = $lReservation->getStoQuantiteReservation() * -1;}
						if(!is_null($lReservation->getStoQuantiteAchat()) ) {$lLigne[$lIdProduit]['qteAchat'] = $lReservation->getStoQuantiteAchat() * -1;}
						if(!is_null($lReservation->getDopeMontantAchat()) ) {$lLigne[$lIdProduit]['prixAchat'] = $lReservation->getDopeMontantAchat() * -1;}
						if(!is_null($lReservation->getStoQuantiteSolidaire()) ) {$lLigne[$lIdProduit]['qteSolidaire'] = $lReservation->getStoQuantiteSolidaire() * -1;}
						if(!is_null($lReservation->getDopeMontantSolidaire()) ) {$lLigne[$lIdProduit]['prixSolidaire'] = $lReservation->getDopeMontantSolidaire() * -1;}
						$lLigne[$lIdProduit]['unite'] = $lReservation->getProUniteMesure();
					}
				}
				$lTableauAR[$lLigne['compte']] = $lLigne;
			}
				
			// Calcul du total par produit
			if(isset($lQuantiteAR[$lReservation->getProId()])) {				
				if(!is_null($lReservation->getStoQuantiteReservation()) ) {$lQuantiteAR[$lReservation->getProId()]['reservation'] += $lReservation->getStoQuantiteReservation() * -1;}
				if(!is_null($lReservation->getStoQuantiteAchat()) ) {$lQuantiteAR[$lReservation->getProId()]['qteAchat'] += $lReservation->getStoQuantiteAchat() * -1;}
				if(!is_null($lReservation->getDopeMontantAchat()) ) {$lQuantiteAR[$lReservation->getProId()]['prixAchat'] += $lReservation->getDopeMontantAchat() * -1;}
				if(!is_null($lReservation->getStoQuantiteSolidaire()) ) {$lQuantiteAR[$lReservation->getProId()]['qteSolidaire'] += $lReservation->getStoQuantiteSolidaire() * -1;}
				if(!is_null($lReservation->getDopeMontantSolidaire()) ) {$lQuantiteAR[$lReservation->getProId()]['prixSolidaire'] += $lReservation->getDopeMontantSolidaire() * -1;}
			} else {				
				$lQuantiteAR[$lReservation->getProId()] = array();
				$lQuantiteAR[$lReservation->getProId()]['reservation'] = '';
				$lQuantiteAR[$lReservation->getProId()]['qteAchat'] = '';
				$lQuantiteAR[$lReservation->getProId()]['prixAchat'] = '';
				$lQuantiteAR[$lReservation->getProId()]['qteSolidaire'] = '';
				$lQuantiteAR[$lReservation->getProId()]['prixSolidaire'] = '';
				if(!is_null($lReservation->getStoQuantiteReservation()) ) {$lQuantiteAR[$lReservation->getProId()]['reservation'] = $lReservation->getStoQuantiteReservation() * -1;}
				if(!is_null($lReservation->getStoQuantiteAchat()) ) {$lQuantiteAR[$lReservation->getProId()]['qteAchat'] = $lReservation->getStoQuantiteAchat() * -1;}
				if(!is_null($lReservation->getDopeMontantAchat()) ) {$lQuantiteAR[$lReservation->getProId()]['prixAchat'] = $lReservation->getDopeMontantAchat() * -1;}
				if(!is_null($lReservation->getStoQuantiteSolidaire()) ) {$lQuantiteAR[$lReservation->getProId()]['qteSolidaire'] = $lReservation->getStoQuantiteSolidaire() * -1;}
				if(!is_null($lReservation->getDopeMontantSolidaire()) ) {$lQuantiteAR[$lReservation->getProId()]['prixSolidaire'] = $lReservation->getDopeMontantSolidaire() * -1;}
			}
		}		
		return array('quantite' => $lQuantiteAR, 'detail' => $lTableauAR );
	}
		
	/**
	 * @name getListeAchatEtReservationCSV($pParam)
	 * @return Un Fichier CSV
	 * @desc Retourne la liste des achats et réservations pour une commande et la liste de produits demandés
	 */
	public function getListeAchatEtReservationCSV($pParam) {
		$lVr = ExportListeAchatEtReservationValid::validAjout($pParam);
	
		if($lVr->getValid()) {
			$lIdProduits = $pParam['id_produits'];
				
			$lInfoAR = $this->getListeAchatEtReservationExport($pParam);
			$lQuantiteAR = $lInfoAR['quantite'];
			$lTableauAR = $lInfoAR['detail'];
	
			$lCSV = new CSV();
			$lCSV->setNom('AchatEtRéservations.csv'); // Le Nom
	
			// L'entete
			$lEntete = array("Compte","Nom","Prénom","Tel.");
			$lLigne2 = array("","","","");
			$lLigne3 = array("","","","Total");
				
			foreach($lIdProduits as $lIdProduit) {
				$lProduit = ProduitManager::select($lIdProduit);
				$lNomProduit = NomProduitManager::select($lProduit->getIdNomProduit());
				$lLabelNomProduit = htmlspecialchars_decode($lNomProduit->getNom(), ENT_QUOTES);
				if($lProduit->getType() == 2) {
					$lLabelNomProduit .= " (Abonnement)";
				}
				array_push($lEntete,$lLabelNomProduit,"","","","","","","","","");
				array_push($lLigne2,"Réservation","","Achat","","","","Solidaire","","","");
					
				$lQuantiteReservation = '';
				$lUniteReservation = '';
				if(isset($lQuantiteAR[$lIdProduit]['reservation']) && !empty($lQuantiteAR[$lIdProduit]['reservation'])) { $lQuantiteReservation = $lQuantiteAR[$lIdProduit]['reservation']; $lUniteReservation = $lProduit->getUniteMesure(); }
				$lQuantiteAchat = '';
				$lUniteAchat = '';
				if(isset($lQuantiteAR[$lIdProduit]['qteAchat']) && !empty($lQuantiteAR[$lIdProduit]['qteAchat'])) { $lQuantiteAchat = $lQuantiteAR[$lIdProduit]['qteAchat']; $lUniteAchat = $lProduit->getUniteMesure();}
				$lPrixAchat = '';
				$lSiglePrixAchat = '';
				if(isset($lQuantiteAR[$lIdProduit]['prixAchat']) && !empty($lQuantiteAR[$lIdProduit]['prixAchat'])) { $lPrixAchat = $lQuantiteAR[$lIdProduit]['prixAchat']; $lSiglePrixAchat = SIGLE_MONETAIRE;}
				$lQuantiteSolidaire = '';
				$lUniteSolidaire = '';
				if(isset($lQuantiteAR[$lIdProduit]['qteSolidaire']) && !empty($lQuantiteAR[$lIdProduit]['qteSolidaire'])) { $lQuantiteSolidaire = $lQuantiteAR[$lIdProduit]['qteSolidaire']; $lUniteSolidaire = $lProduit->getUniteMesure();}
				$lPrixSolidaire = '';
				$lSiglePrixSolidaire = '';
				if(isset($lQuantiteAR[$lIdProduit]['prixSolidaire']) && !empty($lQuantiteAR[$lIdProduit]['prixSolidaire'])) { $lPrixSolidaire = $lQuantiteAR[$lIdProduit]['prixSolidaire']; $lSiglePrixSolidaire = SIGLE_MONETAIRE;}
				array_push($lLigne3,$lQuantiteReservation, $lUniteReservation,$lQuantiteAchat,$lUniteAchat,$lPrixAchat,$lSiglePrixAchat,$lQuantiteSolidaire,$lUniteSolidaire,$lPrixSolidaire,$lSiglePrixSolidaire);
			}
			$lCSV->setEntete($lEntete);
				
			// Les données
			$contenuTableau = array();
			array_push($contenuTableau,$lLigne2);
			array_push($contenuTableau,$lLigne3);
			foreach($lTableauAR as $lVal) {
				$lLigne = array();	
				array_push($lLigne,$lVal['compte']);
				array_push($lLigne,$lVal['nom']);
				array_push($lLigne,$lVal['prenom']);
				array_push($lLigne,$lVal['telephonePrincipal']);
	
				foreach($lIdProduits as $lIdProduit) {
					$lQuantiteReservation = '';
					$lUniteReservation = '';
					if(isset($lVal[$lIdProduit]['reservation']) && !empty($lVal[$lIdProduit]['reservation'])) { $lQuantiteReservation = $lVal[$lIdProduit]['reservation']; $lUniteReservation = $lVal[$lIdProduit]['unite']; }
					$lQuantiteAchat = '';
					$lUniteAchat = '';
					if(isset($lVal[$lIdProduit]['qteAchat']) && !empty($lVal[$lIdProduit]['qteAchat'])) {$lQuantiteAchat = $lVal[$lIdProduit]['qteAchat']; $lUniteAchat = $lVal[$lIdProduit]['unite']; }
					$lPrixAchat = '';
					$lSiglePrixAchat = '';
					if(isset($lVal[$lIdProduit]['prixAchat']) && !empty($lVal[$lIdProduit]['prixAchat'])) { $lPrixAchat = $lVal[$lIdProduit]['prixAchat']; $lSiglePrixAchat = SIGLE_MONETAIRE; }
					$lQuantiteSolidaire = '';
					$lUniteSolidaire = '';
					if(isset($lVal[$lIdProduit]['qteSolidaire']) && !empty($lVal[$lIdProduit]['qteSolidaire'])) { $lQuantiteSolidaire = $lVal[$lIdProduit]['qteSolidaire']; $lUniteSolidaire = $lVal[$lIdProduit]['unite']; }
					$lPrixSolidaire = '';
					$lSiglePrixSolidaire = '';
					if(isset($lVal[$lIdProduit]['prixSolidaire']) && !empty($lVal[$lIdProduit]['prixSolidaire'])) { $lPrixSolidaire = $lVal[$lIdProduit]['prixSolidaire']; $lSiglePrixSolidaire = SIGLE_MONETAIRE; }
					array_push($lLigne,$lQuantiteReservation,$lUniteReservation,$lQuantiteAchat,$lUniteAchat,$lPrixAchat,$lSiglePrixAchat,$lQuantiteSolidaire,$lUniteSolidaire,$lPrixSolidaire,$lSiglePrixSolidaire);
				}
				array_push($contenuTableau,$lLigne);
			}
			$lCSV->setData($contenuTableau);
				
			// Export en CSV
			$lCSV->output();
		} else {
			return $lVr;
		}
	}
}
?>