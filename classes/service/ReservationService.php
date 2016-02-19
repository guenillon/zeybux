<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 14/07/2011
// Fichier : ReservationService.php
//
// Description : Classe ReservationService
//
//****************************************************************

// Inclusion des classes
include_once(CHEMIN_CLASSES_VO . "ReservationVO.php");
include_once(CHEMIN_CLASSES_SERVICE . "StockService.php" );
include_once(CHEMIN_CLASSES_SERVICE . "DetailOperationService.php" );
include_once(CHEMIN_CLASSES_SERVICE . "OperationService.php" );
include_once(CHEMIN_CLASSES_VALIDATEUR . "ReservationValid.php");
include_once(CHEMIN_CLASSES_VIEW_MANAGER . "ReservationDetailViewManager.php");
include_once(CHEMIN_CLASSES_VIEW_MANAGER . "DetailMarcheViewManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "HistoriqueStockManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "CommandeManager.php");
include_once(CHEMIN_CLASSES_SERVICE . "AdherentService.php" );
include_once(CHEMIN_CLASSES_SERVICE . "MarcheService.php" );

/**
 * @name ReservationService
 * @author Julien PIERRE
 * @since 14/07/2011
 * @desc Classe Service d'une Reservation
 */
class ReservationService
{		
	/**
	* @name set($pReservation)
	* @param ReservationVO
	* @return integer
	* @desc Ajoute ou modifie une réservation
	*/
	public function set($pReservation) {
		$lReservationValid = new ReservationValid();
		if($lReservationValid->insert($pReservation)) {
			if($this->reservationCompteAutorise($pReservation->getId()->getIdCompte(), $pReservation->getId()->getIdCommande())) {
				$lOperations = $this->selectOperationReservation($pReservation->getId());
				$lOperation = $lOperations[0];
				$lIdOperation = $lOperation->getId();
				
				if(is_null($lIdOperation) || $lOperation->getTypePaiement() != 0) {
					return $this->insert($pReservation);			
				} else if($lReservationValid->update($pReservation)) {
					return $this->update($pReservation);
				}
			}
		}
		return false;
	}
	
	/**
	* @name insert($pReservation)
	* @param ReservationVO
	* @return integer
	* @desc Ajoute une réservation
	*/
	private function insert($pReservation) {
		// Calcul du total
		$lTotal = 0;
		foreach($pReservation->getDetailReservation() as $lProduit) {			
			$lTotal += $lProduit->getMontant();
		}
		
		// L'operation
		$lOperation = new OperationDetailVO();
		$lOperation->setIdCompte($pReservation->getId()->getIdCompte());
		$lOperation->setMontant($lTotal);
		$lOperation->setLibelle("Marché N°" . $pReservation->getId()->getIdCommande());
		$lOperation->setTypePaiement(0);
		$lOperation->setType(0);
		
		$lChampComplementaire = array();
		
		// Id Marché
		$lOperationChampComplementaire = new OperationChampComplementaireVO();
		$lOperationChampComplementaire->setChcpId(1);
		$lOperationChampComplementaire->setValeur($pReservation->getId()->getIdCommande());
		
		array_push($lChampComplementaire, $lOperationChampComplementaire);
		
		$lOperation->setChampComplementaire($lChampComplementaire);
//		
//		$lOperation->setIdCommande($pReservation->getId()->getIdCommande());

		$lListeIdDetailCommande = array();
		foreach($pReservation->getDetailReservation() as $lProduit) {
			array_push($lListeIdDetailCommande, $lProduit->getIdDetailCommande());
		}
		$lDetailMarche = DetailMarcheViewManager::selectByIdDetailCommande($lListeIdDetailCommande);

		$lOperationService = new OperationService();
		$lIdOperation = $lOperationService->set($lOperation);

		// Ajout detail operation		
		$lStockService = new StockService;		
		$lDetailOperationService = new DetailOperationService;
		foreach($pReservation->getDetailReservation() as $lProduit) {
			// Ajout du stock
			$lStock = new StockVO();
			$lStock->setQuantite($lProduit->getQuantite());
			$lStock->setType(0);
			$lStock->setIdCompte($pReservation->getId()->getIdCompte());
			$lStock->setIdDetailCommande($lProduit->getIdDetailCommande());
			$lStock->setIdOperation($lIdOperation);
			$lStock->setIdNomProduit($lDetailMarche[$lProduit->getIdDetailCommande()]->getProIdNomProduit());
			$lStock->setUnite($lDetailMarche[$lProduit->getIdDetailCommande()]->getProUniteMesure());
			$lStockService->set($lStock);
				
			// Ajout du détail de l'operation
			$lDetailOperation = new DetailOperationVO();
			$lDetailOperation->setIdOperation($lIdOperation);
			$lDetailOperation->setIdCompte($pReservation->getId()->getIdCompte());
			$lDetailOperation->setMontant($lProduit->getMontant());
			$lDetailOperation->setLibelle("Marché N°" . $pReservation->getId()->getIdCommande());
			$lDetailOperation->setTypePaiement(0);
			$lDetailOperation->setIdDetailCommande($lProduit->getIdDetailCommande());
			$lDetailOperation->setIdNomProduit($lDetailMarche[$lProduit->getIdDetailCommande()]->getProIdNomProduit());
			$lDetailOperationService->set($lDetailOperation);
		}

		return $lIdOperation;
	}
	
	/**
	* @name update($pReservation)
	* @param ReservationVO
	* @return integer
	* @desc Met à jour une réservation
	*/
	private function update($pReservation) {
		$lTestDetailReservation = $pReservation->getDetailReservation();
		if(!empty($lTestDetailReservation)) { // Si il y a encore des produits dans la réservation
			$lReservationsActuelle = $this->get($pReservation->getId());
			
			$lOpeReservations = $this->selectOperationReservation($pReservation->getId());
			$lIdOperation = $lOpeReservations[0]->getId();
			/*$lOperation = $lOperations[0];
			$lIdOperation = $lOperation->getId();*/
			$lOperationService = new OperationService();
			$lOperation = $lOperationService->getDetail($lIdOperation);
			$lTotal = 0;
	
			$lStockService = new StockService();
			$lDetailOperationService = new DetailOperationService();
			

			$lListeIdDetailCommande = array();
			foreach($pReservation->getDetailReservation() as $lProduit) {
				array_push($lListeIdDetailCommande, $lProduit->getIdDetailCommande());
			}
			$lDetailMarche = DetailMarcheViewManager::selectByIdDetailCommande($lListeIdDetailCommande);

			
			foreach($lReservationsActuelle->getDetailReservation() as $lReservationActuelle) {
				$lTestUpdate = false;
				foreach($pReservation->getDetailReservation() as $lReservationNouvelle) {
					if($lReservationActuelle->getIdDetailCommande() == $lReservationNouvelle->getIdDetailCommande()) {
						$lTotal += $lReservationNouvelle->getMontant();

						// Maj du stock
						$lStock = new StockVO();
						$lStock->setId($lReservationActuelle->getId()->getIdStock());
						$lStock->setQuantite($lReservationNouvelle->getQuantite());
						$lStock->setType(0);
						$lStock->setIdCompte($pReservation->getId()->getIdCompte());
						$lStock->setIdDetailCommande($lReservationActuelle->getIdDetailCommande());
						$lStock->setIdOperation($lIdOperation);
						$lStock->setIdNomProduit($lDetailMarche[$lReservationActuelle->getIdDetailCommande()]->getProIdNomProduit());
						$lStock->setUnite($lDetailMarche[$lReservationActuelle->getIdDetailCommande()]->getProUniteMesure());
						$lStockService->set($lStock);
						
						// Maj du détail Opération
						$lDetailOperation = new DetailOperationVO();
						$lDetailOperation->setId($lReservationActuelle->getId()->getIdDetailOperation());
						$lDetailOperation->setIdOperation($lIdOperation);
						$lDetailOperation->setIdCompte($pReservation->getId()->getIdCompte());
						$lDetailOperation->setMontant($lReservationNouvelle->getMontant());
						$lDetailOperation->setLibelle("Marché N°" . $pReservation->getId()->getIdCommande());
						$lDetailOperation->setTypePaiement(0);
						$lDetailOperation->setIdDetailCommande($lReservationActuelle->getIdDetailCommande());
						$lDetailOperation->setIdNomProduit($lDetailMarche[$lReservationActuelle->getIdDetailCommande()]->getProIdNomProduit());
						$lDetailOperationService->set($lDetailOperation);					
						
						$lTestUpdate = true;
					}
				}
				if(!$lTestUpdate) {
					// Suppression du stock et du detail operation
					$lStockService->delete($lReservationActuelle->getId()->getIdStock());
					$lDetailOperationService->delete($lReservationActuelle->getId()->getIdDetailOperation());
				}
			}
			
			foreach($pReservation->getDetailReservation() as $lReservationNouvelle) {
				$lTestInsert = true;
				foreach($lReservationsActuelle->getDetailReservation() as $lReservationActuelle) {
					if($lReservationActuelle->getIdDetailCommande() == $lReservationNouvelle->getIdDetailCommande()) {
						$lTestInsert = false;
					}
				}
				if($lTestInsert) {
					$lTotal += $lReservationNouvelle->getMontant();
						
					// Ajout du stock
					$lStock = new StockVO();
					$lStock->setQuantite($lReservationNouvelle->getQuantite());
					$lStock->setType(0);
					$lStock->setIdCompte($pReservation->getId()->getIdCompte());
					$lStock->setIdDetailCommande($lReservationNouvelle->getIdDetailCommande());
					$lStock->setIdOperation($lIdOperation);
					$lStock->setIdNomProduit($lDetailMarche[$lReservationNouvelle->getIdDetailCommande()]->getProIdNomProduit());
					$lStock->setUnite($lDetailMarche[$lReservationNouvelle->getIdDetailCommande()]->getProUniteMesure());
					$lStockService->set($lStock);
					
					// Ajout du détail Opération
					$lDetailOperation = new DetailOperationVO();
					$lDetailOperation->setIdOperation($lIdOperation);
					$lDetailOperation->setIdCompte($pReservation->getId()->getIdCompte());
					$lDetailOperation->setMontant($lReservationNouvelle->getMontant());
					$lDetailOperation->setLibelle("Marché N°" . $pReservation->getId()->getIdCommande());
					$lDetailOperation->setTypePaiement(0);
					$lDetailOperation->setIdDetailCommande($lReservationNouvelle->getIdDetailCommande());
					$lDetailOperation->setIdNomProduit($lDetailMarche[$lReservationNouvelle->getIdDetailCommande()]->getProIdNomProduit());
					$lDetailOperationService->set($lDetailOperation);	
				}
			}
	
			// Maj de l'opération
			
			$lOperation->setMontant($lTotal);
			$lOperationService->set($lOperation);
		} else { // La réservation est vide on la supprime
			$this->delete($pReservation->getId());
		}
	}
	
	/**
	* @name delete($pId)
	* @param IdReservationVO
	* @desc Met à jour une réservation
	*/
	public function delete($pIdReservation) {
		if($this->reservationCompteAutorise($pIdReservation->getIdCompte(), $pIdReservation->getIdCommande())) {
			$lReservationsActuelle = $this->get($pIdReservation);
			if($lReservationsActuelle != false) { // Si la réservation existe
				$lOperations = $this->selectOperationReservation($pIdReservation);
				$lOperation = $lOperations[0];
				$lIdOperation = $lOperation->getId();
				
				// Suppression de l'opération
				$lOperationService = new OperationService();
				$lOperationService->delete($lIdOperation);
				
				$lStockService = new StockService();
				$lDetailOperationService = new DetailOperationService();
				foreach($lReservationsActuelle->getDetailReservation() as $lReservationActuelle) {
					// Suppression du stock et du detail operation
					$lStockService->delete($lReservationActuelle->getId()->getIdStock());
					$lDetailOperationService->delete($lReservationActuelle->getId()->getIdDetailOperation());
				}
			}
		}
	}
	
	/**
	 * @name updateEnAchat($pIdReservation)
	 * @param IdReservationVO
	 * @desc Met à jour une réservation
	 */
	public function updateEnAchat($pIdReservation) {
		$lOperations = $this->selectOperationReservation($pIdReservation);
		$lOperation = $lOperations[0];
				
		$lIdOperation = $lOperation->getId();
		if(!is_null($lIdOperation)) {
			// Maj du type paiement
			$lOperationService = new OperationService();
			$lOperation = $lOperationService->getDetail($lIdOperation);
			$lOperation->setTypePaiement(22);			
			$lOperationService->set($lOperation);
		}
	}
		
	/**
	* @name get($pId)
	* @param integer
	* @return array(ReservationVO) ou ReservationVO
	* @desc Retourne une liste de reservation
	*/
	public function get($pId = null, $pActive = false) {
		$lReservationValid = new ReservationValid();
		if(!is_null($pId) && $lReservationValid->select($pId)) {
			return $this->select($pId, $pActive);
		} else {
			return false;
		}
	}
	
	/**
	* @name selectOperationReservation($pId, $pActive)
	* @param IdReservation
	* @param bool
	* @return array(OperationVO)
	* @desc Retourne une liste d'operation
	*/
	public function selectOperationReservation($pId, $pActive = false) {
		return OperationManager::selectOperationReservation($pId, $pActive);
	}

	/**
	* @name existe($pId)
	* @param IdReservation
	* @return bool
	* @desc Retourne si une réservation existe
	*/
	public function existe($pId) {		
		$lOperations = $this->selectOperationReservation($pId);
		$lIdOperation = $lOperations[0]->getId();
		return !is_null($lIdOperation);
	}
	
	/**
	* @name enCours($pId)
	* @param IdReservation
	* @return bool
	* @desc Retourne si une réservation est en cours
	*/
	public function enCours($pId) {		
		$lOperations = $this->selectOperationReservation($pId);
		$lOperation = $lOperations[0];
		return $lOperation->getTypePaiement() == 0 && !is_null($lOperation->getTypePaiement());
	}
	
	/**
	 * @name enCoursOuAchete($pId)
	 * @param IdReservation
	 * @return bool
	 * @desc Retourne si une réservation est en cours ou achete
	 */
	public function enCoursOuAchete($pId) {
		$lOperations = $this->selectOperationReservation($pId);
		$lOperation = $lOperations[0];
		return ($lOperation->getTypePaiement() == 0 || $lOperation->getTypePaiement() == 22 ) && !is_null($lOperation->getTypePaiement());
	}
	
	/**
	* @name select($pId, $pActive)
	* @param IdReservationVO
	* @param bool
	* @return ReservationVO
	* @desc Retourne une Reservation
	*/
	public function select($pId, $pActive = false) {			
		$lOperations = $this->selectOperationReservation($pId, $pActive);
		$lReservation = new ReservationVO();
		$lReservation->setId($pId);
		
		// Recherche du détail de la reservation
		$lDetailOperationService = new DetailOperationService();
		$lStockService = new StockService();
		if(!is_null($lOperations[0]->getTypePaiement())) {
			
			$lReservation->setEtat($lOperations[0]->getTypePaiement());
			
			switch($lOperations[0]->getTypePaiement()) {
			/*	case 7: // Un achat
					foreach($lOperations as $lOperation) {
						if($lOperation->getTypePaiement() == 7) {
							// Mise à jour du détail de réservation à partir de l'historique du stock
							HistoriqueStockManager::selectReservation($lOperation->getId(), $lReservation);
							$lReservation->setTotal($lOperation->getMontant());
						}		
					}	
					break;*/
				case 22: // Reservation achetée
				case 0: // Reservation en cours
					$lOperation = $lOperations[0];
					$lDetailsReservation = ReservationDetailViewManager::selectDetail($lOperation->getId(),0,0);
					foreach($lDetailsReservation as $lDetail) {
						if($lDetail->getDopeTypePaiement() == 0) {
							$lDetailReservation = new DetailReservationVO();
							$lDetailReservation->getId()->setIdStock($lDetail->getStoId());
							$lDetailReservation->getId()->setIdDetailOperation($lDetail->getDopeId());
							$lDetailReservation->setIdDetailCommande($lDetail->getStoIdDetailCommande());
							$lDetailReservation->setMontant($lDetail->getDopeMontant());
							$lDetailReservation->setQuantite($lDetail->getStoQuantite());
							$lDetailReservation->setIdProduit($lDetail->getDcomIdProduit());
							$lDetailReservation->setIdNomProduit($lDetail->getDcomIdNomProduit());
							$lDetailReservation->setUnite($lDetail->getStoUnite());
							
							$lReservation->addDetailReservation($lDetailReservation);
						}
					}
					$lReservation->setTotal($lOperation->getMontant());
					break;
					
				case 15: // Reservation non récupérée
					$lOperation = $lOperations[0];
					$lDetailsReservation = ReservationDetailViewManager::selectDetail($lOperation->getId(),15,5);
					foreach($lDetailsReservation as $lDetail) {
						if($lDetail->getDopeTypePaiement() == 15) {
							$lDetailReservation = new DetailReservationVO();
							$lDetailReservation->getId()->setIdStock($lDetail->getStoId());
							$lDetailReservation->getId()->setIdDetailOperation($lDetail->getDopeId());
							$lDetailReservation->setIdDetailCommande($lDetail->getStoIdDetailCommande());
							$lDetailReservation->setMontant($lDetail->getDopeMontant());
							$lDetailReservation->setQuantite($lDetail->getStoQuantite());
							$lDetailReservation->setIdProduit($lDetail->getDcomIdProduit());
							$lDetailReservation->setIdNomProduit($lDetail->getDcomIdNomProduit());
							$lDetailReservation->setUnite($lDetail->getStoUnite());
							
							$lReservation->addDetailReservation($lDetailReservation);
						}
					}
					$lReservation->setTotal($lOperation->getMontant());
					break;
					
				case 16: // Reservation annulée
					$lOperation = $lOperations[0];
					$lDetailsReservation = ReservationDetailViewManager::selectDetail($lOperation->getId(),16,6);
					foreach($lDetailsReservation as $lDetail) {
						if($lDetail->getDopeTypePaiement() == 16) {					
							$lDetailReservation = new DetailReservationVO();
							$lDetailReservation->getId()->setIdStock($lDetail->getStoId());
							$lDetailReservation->getId()->setIdDetailOperation($lDetail->getDopeId());
							$lDetailReservation->setIdDetailCommande($lDetail->getStoIdDetailCommande());
							$lDetailReservation->setMontant($lDetail->getDopeMontant());
							$lDetailReservation->setQuantite($lDetail->getStoQuantite());
							$lDetailReservation->setIdProduit($lDetail->getDcomIdProduit());
							$lDetailReservation->setIdNomProduit($lDetail->getDcomIdNomProduit());
							$lDetailReservation->setUnite($lDetail->getStoUnite());
							
							$lReservation->addDetailReservation($lDetailReservation);
						}
					}
					$lReservation->setTotal($lOperation->getMontant());
					break;
			}
		}
		return $lReservation;
	}
	
	/**
	* @name selectAll()
	* @return array(ReservationVO)
	* @desc Retourne une liste d'Reservation
	*/
	public function selectAll() {
	//	return ReservationManager::selectAll();
	}

	/**
	* @name reservationSurLot($pId)
	* @param integer
	* @return bool
	* @desc Si des réservations sont positionnées sur le lot
	*/
	public function reservationSurLot($pId) {
		$lDetail = ReservationDetailViewManager::selectReservationEnCoursByLot($pId,0,0);
		return !is_null($lDetail[0]->getStoId());
	}
	
	/**
	* @name getReservationSurLot($pId)
	* @param integer
	* @return array(ReservationDetailViewVO)
	* @desc Retourne les réservations positionnées sur le lot
	*/
	public function getReservationSurLot($pId) {
		return ReservationDetailViewManager::selectReservationEnCoursByLot($pId,0,0);
	}
	
	/**
	* @name getReservationProduit($pIdMarche, $pIdProduits)
	* @param integer
	* @param array(integer)
	* @return array(ListeReservationVO)
	* @desc Retourne les réservations positionnées sur les produits
	*/
	public function getReservationProduit($pIdMarche, $pIdProduits) {
		return CommandeManager::rechercheReservation(
			array(CommandeManager::CHAMP_COMMANDE_ID,ProduitManager::CHAMP_PRODUIT_ID),
			array('=','in'),
			array($pIdMarche, $pIdProduits),
			array(AdherentManager::CHAMP_ADHERENT_NOM,AdherentManager::CHAMP_ADHERENT_PRENOM), 
			array('ASC','ASC'));
	}
	
	/**
	 * @name getReservationNonAchete($pIdMarche)
	 * @param integer
	 * @return array(ReservationDetailViewVO)
	 * @desc Retourne les réservations non achetées sur un marché
	 */
	public function getReservationNonAchete($pIdMarche) {
		return CommandeManager::selectReservationNonAchete($pIdMarche);
	}
	
	/**
	 * @name getReservationNonAcheteExport($pIdMarche)
	 * @param integer
	 * @return xls
	 * @desc Exporte les réservations non achetées sur un marché
	 */
	public function getReservationNonAcheteExport($pIdMarche) {
		$lExportService = new JPIExportService();
		
		$lTitre = 'Réservations sans achat';
		$lFormat = 'xls';
		
		// Création du phpExcel
		$lphpExcelObject = new PHPExcel();
			
		// Le titre de l'onglet
		$lphpExcelObject->getActiveSheet()->setTitle($lTitre);
		
		// Le header
		$lExportAttributes = array("header" => array("N°", "Compte", "Nom", "Prénom"));
		$i = 'A';
		foreach($lExportAttributes["header"] as $nom) {
			$lphpExcelObject->setActiveSheetIndex(0)->setCellValue($i.'1', $nom)->getColumnDimension($i)->setAutoSize(true);
			$i++;
		}
		
		// Les données : Récupération des réservations
		$lAdherents = $this->getReservationNonAchete($pIdMarche);
		$i = 2;
		foreach ( $lAdherents as $lAdherent ) {
			$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, $lAdherent->getAdhNumero());
			$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, $lAdherent->getCptLabel());
			$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.$i, $lAdherent->getAdhNom());
			$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.$i, $lAdherent->getAdhPrenom());
			$i++;
		}
		
		$lconfig = new JPIExportConfig(
				$lTitre,
				$lFormat,
				array(),
				array(), $lphpExcelObject);
		
		// Export
		$lExportService->export($lconfig);
	}
	
	/**
	 * @name nbReservationNonAdherent($pIdMarche)
	 * @param integer
	 * @return array(ReservationDetailViewVO)
	 * @desc Retourne le nombre de réservations non adhérent positionnées sur le marche
	 */
	public function nbReservationNonAdherent($pIdMarche) {
		$lReservation = OperationManager::selectOperationReservationAdherent($pIdMarche,3);
		$lFirstId = $lReservation[0]->getId();
		if(is_null($lFirstId)) {
			$lNbReservation = 0;
		} else {
			$lNbReservation = count($lReservation);
		}
			
		return $lNbReservation;
	}
	
	/**
	 * @name reservationCompteAutorise($pIdCompte, $pIdMarche)
	 * @param integer
	 * @param integer
	 * @return bool
	 * @desc Retourne si le compte est autorise à effectuer des réservations sur le marche
	 */
	public function reservationCompteAutorise($pIdCompte, $pIdMarche) {
		$lRetour = false;
	
		$lAdherentService = new AdherentService();
		$lAdherents = $lAdherentService->selectByIdCompte($pIdCompte);
		$lNonAdherent = false;
		foreach($lAdherents as $lAdherent) {
			if($lAdherent->getAdhEtat() == 3) {
				$lNonAdherent = true;
			}
		}
	
		if($lNonAdherent) {
			$lMarcheService = new MarcheService();
			$lMarche = $lMarcheService->getInfoMarche($pIdMarche);
				
			if($lMarche->getDroitNonAdherent() == 1) {
				$lRetour = true;
			}
		} else {
			$lRetour = true;
		}
	
		return $lRetour;
	}
}
?>