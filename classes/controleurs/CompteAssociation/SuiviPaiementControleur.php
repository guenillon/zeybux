<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 13/02/2014
// Fichier : SuiviPaiementControleur.php
//
// Description : Classe SuiviPaiementControleur
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_RESPONSE . MOD_COMPTE_ASSOCIATION . "/ListePaiementResponse.php" );
include_once(CHEMIN_CLASSES_SERVICE . "OperationService.php" );
include_once(CHEMIN_CLASSES_VALIDATEUR . MOD_COMPTE_ASSOCIATION . "/SuiviPaiementValid.php" );
include_once(CHEMIN_CLASSES_SERVICE . "BanqueService.php" );
include_once(CHEMIN_CLASSES_SERVICE . "TypePaiementService.php" );
include_once(CHEMIN_CLASSES_TOVO . "OperationChampComplementaireToVO.php" );
include_once(CHEMIN_CLASSES_SERVICE . "JPIExportService.php" );
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php" );

/**
 * @name SuiviPaiementControleur
 * @author Julien PIERRE
 * @since 13/02/2014
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
		$lResponse->setListeCheque($lOperationService->getListeChequeAssociationNonEnregistre());
		$lResponse->setListeEspece($lOperationService->getListeEspeceAssociationNonEnregistre());		
		
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
			case 'espece':
				$lOperations = $lOperationService->getListeEspeceAssociationNonEnregistre();
				$lExportAttributes = array("header" => array("Date", "N°", "Compte", "Libellé/Nom", "Prénom", "Montant"));
				
				// Les données
				if(!is_null(current($lOperations)->getOpeId())) {
					$i = 2;
					foreach ( $lOperations as $lOperation ) {
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, StringUtils::dateDbToFr($lOperation->getOpeDate()));
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, $lOperation->getAdhNumero());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.$i, $lOperation->getCptLabel());
						$lAdhNom = $lOperation->getAdhNom();
						if(is_null($lAdhNom)) {
							$lAdhNom = $lOperation->getOpeLibelle();
						}
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.$i, $lAdhNom);
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.$i, $lOperation->getAdhPrenom());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('F'.$i, $lOperation->getOpeMontant());
						$i++;
					}
				}
				break;

			case 'cheque':
				$lOperations = $lOperationService->getListeChequeAssociationNonEnregistre();
					$lExportAttributes = array("header" => array("Remise de chèque", "Date", "N°", "Compte", "Libellé/Nom", "Prénom", "Montant", "N°"));
				
				// Les données
				if(!is_null(current($lOperations)->getOpeId())) {
					$i = 2;
					foreach ( $lOperations as $lOperation ) {
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, $lOperation->getNumeroRemiseCheque());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, StringUtils::dateDbToFr($lOperation->getOpeDate()));
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.$i, $lOperation->getAdhNumero());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.$i, $lOperation->getCptLabel());
						$lAdhNom = $lOperation->getAdhNom();
						if(is_null($lAdhNom)) {
							$lAdhNom = $lOperation->getOpeLibelle();
						}
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.$i, $lAdhNom);
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('F'.$i, $lOperation->getAdhPrenom());
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('G'.$i, $lOperation->getOpeMontant());
						$lOpeChCP = $lOperation->getOpeTypePaiementChampComplementaire();
						$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('H'.$i, $lOpeChCP[3]->getValeur());
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