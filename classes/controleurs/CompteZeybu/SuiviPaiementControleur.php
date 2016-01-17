<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 12/05/2012
// Fichier : SuiviPaiementControleur.php
//
// Description : Classe SuiviPaiementControleur
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_RESPONSE . MOD_COMPTE_ZEYBU . "/ListePaiementResponse.php" );
include_once(CHEMIN_CLASSES_SERVICE . "OperationService.php" );
include_once(CHEMIN_CLASSES_VALIDATEUR . MOD_COMPTE_ZEYBU . "/SuiviPaiementValid.php" );
include_once(CHEMIN_CLASSES_SERVICE . "BanqueService.php" );
include_once(CHEMIN_CLASSES_SERVICE . "TypePaiementService.php" );
include_once(CHEMIN_CLASSES_SERVICE . "JPIExportService.php" );
include_once(CHEMIN_CLASSES_TOVO . "OperationChampComplementaireToVO.php" );
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php" );

/**
 * @name SuiviPaiementControleur
 * @author Julien PIERRE
 * @since 12/05/2012
 * @desc Classe controleur du suivi des paiements
 */
class SuiviPaiementControleur
{
	/**
	* @name getListePaiement()
	* @desc Donne liste des paiements non enregistrés
	*/
	public function getListePaiement() {
		$lOperationService = new OperationService();
		
		$lResponse = new ListePaiementResponse();
		$lResponse->setListeChequeAdherent($lOperationService->getListeChequeAdherentNonEnregistre());
		$lResponse->setListeEspeceAdherent($lOperationService->getListeEspeceAdherentNonEnregistre());
		$lResponse->setListeChequeFerme($lOperationService->getListeChequeFermeNonEnregistre());
		$lResponse->setListeEspeceFerme($lOperationService->getListeEspeceFermeNonEnregistre());
		$lResponse->setListeChequeInvite($lOperationService->getListeChequeInviteNonEnregistre());
		$lResponse->setListeEspeceInvite($lOperationService->getListeEspeceInviteNonEnregistre());
		
		
		$lBanqueService = new BanqueService();
		$lResponse->setBanques($lBanqueService->getAllActif());
		
		$lTypePaiementService = new TypePaiementService();
		$lResponse->setTypePaiement($lTypePaiementService->selectVisible());
		
		return $lResponse;		
	}
	
	/**
	* @name validerPaiement($pParam)
	* @desc valide un paiement
	*/
	public function validerPaiement($pParam) {
		$lVr = SuiviPaiementValid::validValider($pParam);
		if($lVr->getValid()) {
			$lOperationService = new OperationService();
			$lOperationService->validerPaiement($pParam["id"]);
		}		
		return $lVr;	
	}
	
	/**
	* @name supprimerPaiement($pParam)
	* @desc supprime un paiement
	*/
	public function supprimerPaiement($pParam) {
		$lVr = SuiviPaiementValid::validValider($pParam);
		if($lVr->getValid()) {
			$lOperationService = new OperationService();
			$lOperationService->delete($pParam["id"]);
		}		
		return $lVr;	
	}
		
	/**
	* @name modifierPaiement($pParam)
	* @desc modifie un paiement
	*/
	public function modifierPaiement($pParam) {
		$lVr = SuiviPaiementValid::validModifierPaiement($pParam);
		if($lVr->getValid()) {
			$lOperationService = new OperationService();
			$lOperationInitiale = $lOperationService->getDetail($pParam["id"]);
			
			// Si l'opération originale est de type débit : elle doit le rester
			if($lOperationInitiale->getMontant() < 0) {
				$pParam["montant"] = -1 * $pParam["montant"];
			}
			
			$lOperationInitiale->setTypePaiement($pParam["typePaiement"]);
			$lOperationInitiale->setMontant($pParam["montant"]);
			
			$lChampComplementaire = array();
			foreach($pParam['champComplementaire'] as $lChamp) {
				if(!is_null($lChamp)) {
					array_push($lChampComplementaire, OperationChampComplementaireToVO::convertFromArray($lChamp));
				}
			}
			$lOperationInitiale->setChampComplementaire($lChampComplementaire);
			$lOperationService->set($lOperationInitiale);
		}
		return $lVr;	
	}
	
	/**
	 * @name export($pParam)
	 * @desc Retourne le suivi de paiement
	 */
	public function export($pParam) {
		$lOperationService = new OperationService();
		$lExportService = new JPIExportService();
		
		$lTitre = 'Suivi Paiement';
		$lFormat = 'xls';
		
		// Création du phpExcel
		$lphpExcelObject = new PHPExcel();
			
		// Le titre de l'onglet
		$lphpExcelObject->getActiveSheet()->setTitle($lTitre);
		
		// Récupération des opérations
		switch($pParam['type']) {
			case 'espece-invite':
				$lOperations = $lOperationService->getListeEspeceInviteNonEnregistre();
				$lExportAttributes = array("header" => array("Date", "Montant"));
				
				// Les données
				if(!is_null(current($lOperations)->getOpeId())) {
					$i = 2;
					foreach ( $lOperations as $lOperation ) {
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, StringUtils::dateDbToFr($lOperation->getOpeDate()));
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, $lOperation->getOpeMontant());
						$i++;
					}
				}
				break;

			case 'cheque-invite':
				$lOperations = $lOperationService->getListeChequeInviteNonEnregistre();
				$lExportAttributes = array("header" => array("Remise de chèque", "Date", "Montant", "N°"));

				// Les données
				if(!is_null(current($lOperations)->getOpeId())) {
					$i = 2;
					foreach ( $lOperations as $lOperation ) {
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, $lOperation->getNumeroRemiseCheque());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, StringUtils::dateDbToFr($lOperation->getOpeDate()));
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.$i, $lOperation->getOpeMontant());
						$lOpeChCP = $lOperation->getOpeTypePaiementChampComplementaire();
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.$i, $lOpeChCP[3]->getValeur());
						$i++;
					}
				}
				break;
				
			case 'espece-adherent':
				$lOperations = $lOperationService->getListeEspeceAdherentNonEnregistre();
				$lExportAttributes = array("header" => array("Date", "N°", "Compte", "Nom", "Prénom", "Montant"));
				
				// Les données
				if(!is_null(current($lOperations)->getOpeId())) {
					$i = 2;
					foreach ( $lOperations as $lOperation ) {
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, StringUtils::dateDbToFr($lOperation->getOpeDate()));
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, $lOperation->getAdhNumero());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.$i, $lOperation->getCptLabel());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.$i, $lOperation->getAdhNom());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.$i, $lOperation->getAdhPrenom());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('F'.$i, $lOperation->getOpeMontant());
						$i++;
					}
				}
				break;

			case 'cheque-adherent':
				$lOperations = $lOperationService->getListeChequeAdherentNonEnregistre();
				$lExportAttributes = array("header" => array("Remise de chèque", "Date", "N°", "Compte", "Nom", "Prénom", "Montant", "N°"));
				
				// Les données
				if(!is_null(current($lOperations)->getOpeId())) {
					$i = 2;
					foreach ( $lOperations as $lOperation ) {
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, $lOperation->getNumeroRemiseCheque());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, StringUtils::dateDbToFr($lOperation->getOpeDate()));
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.$i, $lOperation->getAdhNumero());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.$i, $lOperation->getCptLabel());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.$i, $lOperation->getAdhNom());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('F'.$i, $lOperation->getAdhPrenom());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('G'.$i, $lOperation->getOpeMontant());
						$lOpeChCP = $lOperation->getOpeTypePaiementChampComplementaire();
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('H'.$i, $lOpeChCP[3]->getValeur());
						$i++;
					}
				}
				break;
				
			case 'espece-ferme':
				$lOperations = $lOperationService->getListeEspeceFermeNonEnregistre();
				$lExportAttributes = array("header" => array("Date", "N°", "Compte", "Nom", "Montant"));

				// Les données
				if(!is_null(current($lOperations)->getOpeId())) {
					$i = 2;
					foreach ( $lOperations as $lOperation ) {
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, StringUtils::dateDbToFr($lOperation->getOpeDate()));
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, $lOperation->getFerNumero());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.$i, $lOperation->getCptLabel());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.$i, $lOperation->getFerNom());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.$i, $lOperation->getOpeMontant());
						$i++;
					}
				}
				break;

			case 'cheque-ferme':
				$lOperations = $lOperationService->getListeChequeFermeNonEnregistre();
				$lExportAttributes = array("header" => array("Date", "N°", "Compte", "Nom", "Montant", "N°"));
				
				// Les données
				if(!is_null(current($lOperations)->getOpeId())) {
					$i = 2;
					foreach ( $lOperations as $lOperation ) {
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, StringUtils::dateDbToFr($lOperation->getOpeDate()));
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, $lOperation->getFerNumero());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.$i, $lOperation->getCptLabel());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.$i, $lOperation->getFerNom());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.$i, $lOperation->getOpeMontant());
						$lOpeChCP = $lOperation->getOpeTypePaiementChampComplementaire();
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('F'.$i, $lOpeChCP[3]->getValeur());
						$i++;
					}
				}
				break;
				
			default:
				$lOperations = array();
				$lExportAttributes = array();
		}

		// Le header
		$i = 'A';
		foreach($lExportAttributes["header"] as $nom) {
			$lphpExcelObject->setActiveSheetIndex(0)->setCellValue($i.'1', $nom)->getColumnDimension($i)->setAutoSize(true);
			$i++;
		}
		
		$lconfig = new JPIExportConfig(
				$lTitre, 
				$lFormat, 
				$lExportAttributes, 
				$lOperations, $lphpExcelObject);
		
		// Export
		$lExportService->export($lconfig);
	}
}
?>