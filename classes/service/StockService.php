<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 10/07/2011
// Fichier : StockService.php
//
// Description : Classe StockService
//
//****************************************************************

// Inclusion des classes
include_once(CHEMIN_CLASSES_VALIDATEUR . "StockValid.php");
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php");
include_once(CHEMIN_CLASSES_UTILS . "DbUtils.php");
include_once(CHEMIN_CLASSES_VO . "StockProduitReservationVO.php");
include_once(CHEMIN_CLASSES_MANAGERS . "ProduitManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "NomProduitManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "DetailOperationManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "StockQuantiteManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "DetailCommandeManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "ProduitManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "StockManager.php");
include_once(CHEMIN_CLASSES_VIEW_MANAGER . "StockProduitDisponibleViewManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "HistoriqueStockManager.php");
include_once(CHEMIN_CLASSES_SERVICE . "JPIExportService.php" );

/**
 * @name StockService
 * @author Julien PIERRE
 * @since 10/07/2011
 * @desc Classe Service d'un Stock
 */
class StockService
{		
	/**
	* @name set($pStock)
	* @param StockVO
	* @return integer
	* @desc Ajoute ou modifie une opération
	*/
	public function set($pStock) {
		$lStockValid = new StockValid();
		if($lStockValid->insert($pStock)) {
			return $this->insert($pStock);			
		} else if($lStockValid->update($pStock)) {
			return $this->update($pStock);
		} else {
			return false;
		}
	}
	
	/**
	* @name insert($pStock)
	* @param StockVO
	* @return integer
	* @desc Ajoute une opération
	*/
	private function insert($pStock) {

		// TODO les test : on insere que les types 0/1/2/3/4
		
		$pStock->setDate(StringUtils::dateTimeAujourdhuiDb());
		
		$lId = StockManager::insert($pStock); // Ajout de l'opération
		$pStock->setId($lId);
		$this->insertHistorique($pStock); // Ajout historique

		switch($pStock->getType()) {
			case 0 : // Reservation				
				$lLot = DetailCommandeManager::select($pStock->getIdDetailCommande());
				$lProduit = ProduitManager::select($lLot->getIdProduit());
				if($pStock->getQuantite() > 0) { // Reservation Producteur (commande)
					// Maj Stock Reservation et qté initiale dans le produit
					if($lProduit->getStockInitial() == -1) { // Si Pas de limite (Qte init = -1) il ne faut pas prendre en compte la qte init.
						$lStockReservation = $lProduit->getStockReservation() + $pStock->getQuantite();
					} else {
						$lStockReservation = $lProduit->getStockReservation() - $lProduit->getStockInitial() + $pStock->getQuantite();
					}
					$lProduit->setStockReservation($lStockReservation);
					$lProduit->setStockInitial($pStock->getQuantite());
					ProduitManager::update($lProduit);
				} else { // Reservation Adherent
					// Maj Stock Reservation dans le produit
					$lProduit->setStockReservation($lProduit->getStockReservation() + $pStock->getQuantite());
					ProduitManager::update($lProduit);
				}
				break;
				
			case 1 : // Achat
				// Ajout ou Maj de la qté produit dans le stock
				$lStockQuantiteActuel = $this->selectQuantiteByIdNomProduitUnite($pStock->getIdNomProduit(), $pStock->getUnite());
				$lStockQuantiteActuel = $lStockQuantiteActuel[0];
					
				$lStockQuantite = new StockQuantiteVO();
				if(!is_null($lStockQuantiteActuel->getId())) {
					$lStockQuantite->setId($lStockQuantiteActuel->getId());
					$lStockQuantite->setQuantiteSolidaire($lStockQuantiteActuel->getQuantiteSolidaire());
				}
				$lStockQuantite->setQuantite($lStockQuantiteActuel->getQuantite() + $pStock->getQuantite());
				$lStockQuantite->setIdNomProduit($pStock->getIdNomProduit());
				$lStockQuantite->setUnite($pStock->getUnite());
				$this->setStockQuantite($lStockQuantite);				
				break;
				
			case 2 : // Livraison/Achat Solidaire
				// Ajout ou Maj de la qté produit dans le stock
				$lStockQuantiteActuel = $this->selectQuantiteByIdNomProduitUnite($pStock->getIdNomProduit(), $pStock->getUnite());
				$lStockQuantiteActuel = $lStockQuantiteActuel[0];
				
				$lStockQuantite = new StockQuantiteVO();
				if(!is_null($lStockQuantiteActuel->getId())) {
					$lStockQuantite->setId($lStockQuantiteActuel->getId());
					$lStockQuantite->setQuantite($lStockQuantiteActuel->getQuantite());
				}
				$lStockQuantite->setQuantiteSolidaire($lStockQuantiteActuel->getQuantiteSolidaire() + $pStock->getQuantite());
				$lStockQuantite->setIdNomProduit($pStock->getIdNomProduit());
				$lStockQuantite->setUnite($pStock->getUnite());
				$this->setStockQuantite($lStockQuantite);
				break;
				
			case 4 : // Livraison				
				/*$lLot = DetailCommandeManager::select($pStock->getIdDetailCommande());
				$lProduit = ProduitManager::select($lLot->getIdProduit());
				if($pStock->getQuantite() > 0) { // Livraison Producteur
					// Maj Stock Reservation et qté initiale dans le produit					
					if($lProduit->getStockInitial() == -1) { // Si Pas de limite (Qte init = -1) il ne faut pas prendre en compte la qte init.
						$lStockReservation = $lProduit->getStockReservation() + $pStock->getQuantite();
					} else {
						$lStockReservation = $lProduit->getStockReservation() - $lProduit->getStockInitial() + $pStock->getQuantite();
					}
					$lProduit->setStockReservation($lStockReservation);
					$lProduit->setStockInitial($pStock->getQuantite());
					
					ProduitManager::update($lProduit);
				}*/
				
				// Ajout ou Maj de la qté produit dans le stock
				$lStockQuantiteActuel = $this->selectQuantiteByIdNomProduitUnite($pStock->getIdNomProduit(), $pStock->getUnite());
				$lStockQuantiteActuel = $lStockQuantiteActuel[0];
				
				$lStockQuantite = new StockQuantiteVO();
				if(!is_null($lStockQuantiteActuel->getId())) {
					$lStockQuantite->setId($lStockQuantiteActuel->getId());
					$lStockQuantite->setQuantiteSolidaire($lStockQuantiteActuel->getQuantiteSolidaire());
				}
				$lStockQuantite->setQuantite($lStockQuantiteActuel->getQuantite() + $pStock->getQuantite());
				$lStockQuantite->setIdNomProduit($pStock->getIdNomProduit());
				$lStockQuantite->setUnite($pStock->getUnite());
				$this->setStockQuantite($lStockQuantite);

				break;
		}	
		return $lId;
	}
	
	/**
	* @name update($pStock)
	* @param StockVO
	* @return integer
	* @desc Met à jour une opération
	*/
	private function update($pStock) {
		// TODO les test : on update que les types 0/1/2/3/4/5/6
		
		$lStockActuel = $this->get($pStock->getId());
		$pStock->setDate(StringUtils::dateTimeAujourdhuiDb());
		$this->insertHistorique($pStock); // Ajout historique
		// TODO Mise à jour du stock selon le type
		switch($pStock->getType()) {
			case 0 : // Reservation
				$lLot = DetailCommandeManager::select($pStock->getIdDetailCommande());
				$lProduit = ProduitManager::select($lLot->getIdProduit());
				if($pStock->getQuantite() > 0) { // Reservation Producteur (commande)
					// Maj Stock Reservation dans le produit
					if($lProduit->getStockInitial() == -1) { // Si Pas de limite (Qte init = -1) il ne faut pas prendre en compte la qte init.
						$lStockReservation = $lProduit->getStockReservation() + $pStock->getQuantite();
					} else {
						$lStockReservation = $lProduit->getStockReservation() - $lProduit->getStockInitial() + $pStock->getQuantite();
					}
					$lProduit->setStockReservation($lStockReservation);
					$lProduit->setStockInitial($pStock->getQuantite());
					ProduitManager::update($lProduit);
				} else { // Reservation Adherent
					// Maj Stock Reservation dans le produit
					$lProduit->setStockReservation($lProduit->getStockReservation() + $pStock->getQuantite() - $lStockActuel->getQuantite());
					ProduitManager::update($lProduit);
				}
				break;
				
			case 1 : // Achat
				// Ajout ou Maj de la qté produit dans le stock
				$lStockQuantiteActuel = $this->selectQuantiteByIdNomProduitUnite($pStock->getIdNomProduit(), $pStock->getUnite());
				$lStockQuantiteActuel = $lStockQuantiteActuel[0];
					
				$lStockQuantite = new StockQuantiteVO();
				if(!is_null($lStockQuantiteActuel->getId())) {
					$lStockQuantite->setId($lStockQuantiteActuel->getId());
					$lStockQuantite->setQuantiteSolidaire($lStockQuantiteActuel->getQuantiteSolidaire());
				}
				$lStockQuantite->setQuantite($lStockQuantiteActuel->getQuantite() + $pStock->getQuantite() - $lStockActuel->getQuantite());
				$lStockQuantite->setIdNomProduit($pStock->getIdNomProduit());
				$lStockQuantite->setUnite($pStock->getUnite());
				$this->setStockQuantite($lStockQuantite);
				break;
				
			case 2 : // Livraison/Achat Solidaire
				// Ajout ou Maj de la qté produit dans le stock
				$lStockQuantiteActuel = $this->selectQuantiteByIdNomProduitUnite($pStock->getIdNomProduit(), $pStock->getUnite());
				$lStockQuantiteActuel = $lStockQuantiteActuel[0];
					
				$lStockQuantite = new StockQuantiteVO();
				if(!is_null($lStockQuantiteActuel->getId())) {
					$lStockQuantite->setId($lStockQuantiteActuel->getId());
					$lStockQuantite->setQuantite($lStockQuantiteActuel->getQuantite());
				}
				$lStockQuantite->setQuantiteSolidaire($lStockQuantiteActuel->getQuantiteSolidaire() + $pStock->getQuantite() - $lStockActuel->getQuantite());
				$lStockQuantite->setIdNomProduit($pStock->getIdNomProduit());
				$lStockQuantite->setUnite($pStock->getUnite());
				$this->setStockQuantite($lStockQuantite);
				break;
			
			case 4 : // Livraison				
				/*$lLot = DetailCommandeManager::select($pStock->getIdDetailCommande());
				$lProduit = ProduitManager::select($lLot->getIdProduit());
				if($pStock->getQuantite() > 0) { // Livraison Producteur
					// Maj Stock Reservation et qté initiale dans le produit
					if($lProduit->getStockInitial() == -1) { // Si Pas de limite (Qte init = -1) il ne faut pas prendre en compte la qte init.
						$lStockReservation = $lProduit->getStockReservation() + $pStock->getQuantite();
					} else {
						$lStockReservation = $lProduit->getStockReservation() - $lProduit->getStockInitial() + $pStock->getQuantite();
					}
					$lProduit->setStockReservation($lStockReservation);
					$lProduit->setStockInitial($pStock->getQuantite());
					ProduitManager::update($lProduit);
				}*/
				// Ajout ou Maj de la qté produit dans le stock
				$lStockQuantiteActuel = $this->selectQuantiteByIdNomProduitUnite($pStock->getIdNomProduit(), $pStock->getUnite());
				$lStockQuantiteActuel = $lStockQuantiteActuel[0];
				
				$lStockQuantite = new StockQuantiteVO();
				if(!is_null($lStockQuantiteActuel->getId())) {
					$lStockQuantite->setId($lStockQuantiteActuel->getId());
					$lStockQuantite->setQuantiteSolidaire($lStockQuantiteActuel->getQuantiteSolidaire());
				}
				$lStockQuantite->setQuantite($lStockQuantiteActuel->getQuantite() + $pStock->getQuantite() - $lStockActuel->getQuantite());
				$lStockQuantite->setIdNomProduit($pStock->getIdNomProduit());
				$lStockQuantite->setUnite($pStock->getUnite());
				
				$this->setStockQuantite($lStockQuantite);
				break;
			
			case 6 : // Reservation annulée
				// Maj Stock Reservation dans le produit
				$lLot = DetailCommandeManager::select($pStock->getIdDetailCommande());
				$lProduit = ProduitManager::select($lLot->getIdProduit());
				$lProduit->setStockReservation($lProduit->getStockReservation() - $lStockActuel->getQuantite());
				ProduitManager::update($lProduit);
				break;
				
			case 8 : // Supression d'un achat
				// Ajout ou Maj de la qté produit dans le stock
				$lStockQuantiteActuel = $this->selectQuantiteByIdNomProduitUnite($pStock->getIdNomProduit(), $pStock->getUnite());
				$lStockQuantiteActuel = $lStockQuantiteActuel[0];
			
				$lStockQuantite = new StockQuantiteVO();
				if(!is_null($lStockQuantiteActuel->getId())) {
					$lStockQuantite->setId($lStockQuantiteActuel->getId());
					$lStockQuantite->setQuantiteSolidaire($lStockQuantiteActuel->getQuantiteSolidaire());
				}
				$lStockQuantite->setQuantite($lStockQuantiteActuel->getQuantite() - $lStockActuel->getQuantite());
				$lStockQuantite->setIdNomProduit($pStock->getIdNomProduit());
				$lStockQuantite->setUnite($pStock->getUnite());
				$this->setStockQuantite($lStockQuantite);
				break;
				
			case 9 : // Supression d'une Livraison
				// Ajout ou Maj de la qté produit dans le stock
				$lStockQuantiteActuel = $this->selectQuantiteByIdNomProduitUnite($pStock->getIdNomProduit(), $pStock->getUnite());
				$lStockQuantiteActuel = $lStockQuantiteActuel[0];
					
				$lStockQuantite = new StockQuantiteVO();
				if(!is_null($lStockQuantiteActuel->getId())) {
					$lStockQuantite->setId($lStockQuantiteActuel->getId());
					$lStockQuantite->setQuantiteSolidaire($lStockQuantiteActuel->getQuantiteSolidaire());
				}
				$lStockQuantite->setQuantite($lStockQuantiteActuel->getQuantite() - $lStockActuel->getQuantite());
				$lStockQuantite->setIdNomProduit($pStock->getIdNomProduit());
				$lStockQuantite->setUnite($pStock->getUnite());
				$this->setStockQuantite($lStockQuantite);
				break;
				
			case 10 : // Supression d'une Livraison Solidaire
				// Ajout ou Maj de la qté produit dans le stock
				$lStockQuantiteActuel = $this->selectQuantiteByIdNomProduitUnite($pStock->getIdNomProduit(), $pStock->getUnite());
				$lStockQuantiteActuel = $lStockQuantiteActuel[0];
				
				$lStockQuantite = new StockQuantiteVO();
				if(!is_null($lStockQuantiteActuel->getId())) {
					$lStockQuantite->setId($lStockQuantiteActuel->getId());
					$lStockQuantite->setQuantite($lStockQuantiteActuel->getQuantite());
				}
				$lStockQuantite->setQuantiteSolidaire($lStockQuantiteActuel->getQuantiteSolidaire() - $lStockActuel->getQuantite());
				$lStockQuantite->setIdNomProduit($pStock->getIdNomProduit());
				$lStockQuantite->setUnite($pStock->getUnite());
				$this->setStockQuantite($lStockQuantite);
				break;
		}
		
		return StockManager::update($pStock); // update
	}
	
	/**
	* @name updateStockProduit($pStock)
	* @param StockVO
	* @return integer
	* @desc Met à jour une opération
	*/
	public function updateStockProduit($pStock) {
		// TODO les test : on update que les types 0/1/2/3/4/5/6
		
		$lStockActuel = $this->get($pStock->getId());
		$pStock->setDate(StringUtils::dateTimeAujourdhuiDb());
		// TODO Mise à jour du stock selon le type
		switch($pStock->getType()) {
			case 0 : // Reservation
				$lLot = DetailCommandeManager::select($pStock->getIdDetailCommande());
				$lProduit = ProduitManager::select($lLot->getIdProduit());
				
				
				if($pStock->getQuantite() != -1 && $lProduit->getStockInitial() == -1) {
					// Maj Stock Reservation dans le produit
					$lProduit->setStockReservation($lProduit->getStockReservation() + $pStock->getQuantite());
					$lProduit->setStockInitial($pStock->getQuantite());
					ProduitManager::update($lProduit);
				} else if($pStock->getQuantite() == -1 && $lProduit->getStockInitial() != -1) {
					//echo 2;
					// Maj Stock Reservation dans le produit
					$lProduit->setStockReservation($lProduit->getStockReservation() - $lProduit->getStockInitial());
					$lProduit->setStockInitial(-1);
					ProduitManager::update($lProduit);
					
				} else if($pStock->getQuantite() != -1 && $lProduit->getStockInitial() != -1) {
					//echo 3;
					// Maj Stock Reservation dans le produit
					$lProduit->setStockReservation($lProduit->getStockReservation() - $lProduit->getStockInitial() + $pStock->getQuantite());
					$lProduit->setStockInitial($pStock->getQuantite());
					ProduitManager::update($lProduit);
				}
				break;
		}
		$this->insertHistorique($pStock); // Ajout historique
		return StockManager::update($pStock); // update
	}
	
	/**
	* @name delete($pId)
	* @param integer
	* @desc Met à jour une opération
	*/
	public function delete($pId) {
		$lStockValid = new StockValid();
		if($lStockValid->delete($pId)){
			$lStock = $this->get($pId);
			switch($lStock->getType()) {
				case 0 : // Annulation de la reservation
				case 6 :
					$lStock->setType(6);
					return $this->update($lStock);
					break;
					
				case 1 : // Annulation de l'achat
					$lStock->setType(8);
					return $this->update($lStock);
					break;
					
				case 2 : // Annulation de l'achat solidaire
					$lStock->setType(10);
					return $this->update($lStock);
					break;
					
				case 3 : // Annulation du Bon de commande
					$lStock->setType(7);
					return $this->update($lStock);
					break;
					
				case 4 : // Annulation du Bon de Livraison
					$lStock->setType(9);
					return $this->update($lStock);
					break;
					
				default:
					$lStock->setDate(StringUtils::dateTimeAujourdhuiDb());
					$this->insertHistorique($lStock); // Ajout historique
					return StockManager::delete($pId);
					break;
			}	
		} else {
			return false;
		}
	}
	
	/**
	* @name insertHistorique($pStock)
	* @param StockVO
	* @return integer
	* @desc Insère une nouvelle ligne dans la table, à partir des informations de la StockVO en paramètre (l'id sera automatiquement calculé par la BDD)
	*/
	private function insertHistorique($pStock) {		
		$lHistoriqueStock = new HistoriqueStockVO();
		$lHistoriqueStock->setStoId($pStock->getId());
		$lHistoriqueStock->setDate($pStock->getDate());
		$lHistoriqueStock->setQuantite($pStock->getQuantite());
		$lHistoriqueStock->setType($pStock->getType());
		$lHistoriqueStock->setIdCompte($pStock->getIdCompte());
		$lHistoriqueStock->setIdDetailCommande($pStock->getIdDetailCommande());
		$lHistoriqueStock->setIdModeleLot($pStock->getIdModeleLot());
		$lHistoriqueStock->setIdOperation($pStock->getIdOperation());
		$lHistoriqueStock->setIdConnexion($_SESSION[ID_CONNEXION]);
		return HistoriqueStockManager::insert($lHistoriqueStock);
	}
		
	/**
	* @name get($pId)
	* @param integer
	* @return array(StockVO) ou StockVO
	* @desc Retourne une liste de virement
	*/
	public function get($pId = null) {
		if($pId != null) {
			return $this->select($pId);
		} else {
			return $this->selectAll();
		}
	}
		
	/**
	* @name select($pId)
	* @param integer
	* @return StockVO
	* @desc Retourne une Stock
	*/
	public function select($pId) {
		return StockManager::select($pId);
	}
	
	/**
	* @name selectAll()
	* @return array(StockVO)
	* @desc Retourne une liste d'Stock
	*/
	public function selectAll() {
		return StockManager::selectAll();
	}
	
	/**
	* @name getDetailReservation($pIdOperation)
	* @return array(StockVO)
	* @desc Retourne une liste d'Stock
	*/
	public function getDetailReservation($pIdOperation) {	
		return StockManager::recherche(
			array(StockManager::CHAMP_STOCK_ID_OPERATION),
			array('='),
			array($pIdOperation),
			array(StockManager::CHAMP_STOCK_DATE,StockManager::CHAMP_STOCK_TYPE),
			array('DESC','ASC'));
	}
	
/** Solidaire **/		
	/**
	 * @name selectInfoBonCommandeStockProduitReservation($pIdCommande, $pIdCompteProducteur)
	 * @param integer
	 * @param integer
	 * @return array(StockProduitReservationVO)
	 * @desc Récupères toutes les lignes de la table ayant pour IdCommande $pIdCommande et IdCompteProducteur $pIdCompteProducteur . Puis les renvoie sous forme d'une collection de StockProduitReservationVO
	 */
	public function selectInfoBonCommandeStockProduitReservation($pIdCommande, $pIdCompteProducteur) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
		"(SELECT "
				. ProduitManager::CHAMP_PRODUIT_ID_COMMANDE .
				"," . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME .
				"," . ProduitManager::CHAMP_PRODUIT_ID .
				"," . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE .
				"," . ProduitManager::CHAMP_PRODUIT_TYPE .
				"," . NomProduitManager::CHAMP_NOMPRODUIT_NUMERO .
				"," . NomProduitManager::CHAMP_NOMPRODUIT_NOM .
				//", (" . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . " - " . ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION . ") AS " . StockManager::CHAMP_STOCK_QUANTITE .
				
				", (round(sum(" . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT . " * " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_TAILLE . " / " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_PRIX . "),2) * -(1)) AS " . StockManager::CHAMP_STOCK_QUANTITE .
				
				
				", sum(" . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT .") AS " . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT .
				"," . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID .
				"," . DetailCommandeManager::CHAMP_DETAILCOMMANDE_TAILLE .
				"," . DetailCommandeManager::CHAMP_DETAILCOMMANDE_PRIX .
				" FROM ((("
						. ProduitManager::TABLE_PRODUIT	.
						" JOIN " . NomProduitManager::TABLE_NOMPRODUIT . " ON ((" . NomProduitManager::CHAMP_NOMPRODUIT_ID . " = " . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT .")))
				 LEFT JOIN " . DetailCommandeManager::TABLE_DETAILCOMMANDE . " ON ((" . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . " = " . ProduitManager::CHAMP_PRODUIT_ID .")
				 	AND " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ETAT . " = 0))
				 LEFT JOIN " . DetailOperationManager::TABLE_DETAILOPERATION . " ON (((" . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . " = " . DetailOperationManager::CHAMP_DETAILOPERATION_ID_DETAIL_COMMANDE .") and (" . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . " = 0))))
			WHERE "
					 		. ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . " = " . $pIdCommande
					 		. " AND " . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME . " = " . $pIdCompteProducteur
					 		. " AND " . ProduitManager::CHAMP_PRODUIT_ETAT . " = 0 "
					 		. " AND " . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . " <> -(1) "
					 		. " AND " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ETAT . " = 0 "
					 		. " AND ( " . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . " = 0 "
					 		. " OR ISNULL( " . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . "))
			GROUP BY " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . ")
			UNION
			(SELECT "
						. ProduitManager::CHAMP_PRODUIT_ID_COMMANDE .
						"," . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME .
						"," . ProduitManager::CHAMP_PRODUIT_ID .
						"," . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE .
						"," . ProduitManager::CHAMP_PRODUIT_TYPE .
						"," . NomProduitManager::CHAMP_NOMPRODUIT_NUMERO .
						"," . NomProduitManager::CHAMP_NOMPRODUIT_NOM .
						//", ((" . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . " - " . ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION . ") + 1) AS " . StockManager::CHAMP_STOCK_QUANTITE .
						", (round(sum(" . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT . " * " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_TAILLE . " / " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_PRIX . "),2) * -(1)) AS " . StockManager::CHAMP_STOCK_QUANTITE .
						
						", sum(" . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT .") AS " . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT .
						"," . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID .
						"," . DetailCommandeManager::CHAMP_DETAILCOMMANDE_TAILLE .
						"," . DetailCommandeManager::CHAMP_DETAILCOMMANDE_PRIX .
						" FROM ((("
								. ProduitManager::TABLE_PRODUIT	.
								" JOIN " . NomProduitManager::TABLE_NOMPRODUIT . " ON ((" . NomProduitManager::CHAMP_NOMPRODUIT_ID . " = " . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT .")))
				 LEFT JOIN " . DetailCommandeManager::TABLE_DETAILCOMMANDE . " ON ((" . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . " = " . ProduitManager::CHAMP_PRODUIT_ID .")
				 	AND " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ETAT . " = 0))
				 LEFT JOIN " . DetailOperationManager::TABLE_DETAILOPERATION . " ON (((" . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . " = " . DetailOperationManager::CHAMP_DETAILOPERATION_ID_DETAIL_COMMANDE .") and (" . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . " = 0))))
			WHERE "
					 		. ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . " = " . $pIdCommande
					 		. " AND " . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME . " = " . $pIdCompteProducteur
					 		. " AND " . ProduitManager::CHAMP_PRODUIT_ETAT . " = 0 "
					 		. " AND " . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . " = -(1) "
					 		. " AND " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ETAT . " = 0 "
					 		. " AND ( " . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . " = 0 "
					 		. " OR ISNULL( " . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . "))
			GROUP BY " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . ")
			ORDER BY " . NomProduitManager::CHAMP_NOMPRODUIT_NOM . "," . DetailCommandeManager::CHAMP_DETAILCOMMANDE_TAILLE . ";";
			
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
	
		$lListeStockProduitReservation = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeStockProduitReservation,
				$this->remplirStockProduitReservation(
				$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMMANDE],
				$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME],
				$lLigne[ProduitManager::CHAMP_PRODUIT_ID],
				$lLigne[ProduitManager::CHAMP_PRODUIT_UNITE_MESURE],
				$lLigne[ProduitManager::CHAMP_PRODUIT_TYPE],
				$lLigne[NomProduitManager::CHAMP_NOMPRODUIT_NUMERO],
				$lLigne[NomProduitManager::CHAMP_NOMPRODUIT_NOM],
				$lLigne[StockManager::CHAMP_STOCK_QUANTITE],
				$lLigne[DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT],
				$lLigne[DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID],
				$lLigne[DetailCommandeManager::CHAMP_DETAILCOMMANDE_TAILLE],
				$lLigne[DetailCommandeManager::CHAMP_DETAILCOMMANDE_PRIX]));
			}
		} else {
			$lListeStockProduitReservation[0] = new StockProduitReservationVO();
		}
		return $lListeStockProduitReservation;
	}
	
	/**
	 * @name selectByIdProduitStockProduitReservation($pIdProduit)
	 * @param integer
	 * @return array(StockProduitReservationVO)
	 * @desc Récupères toutes les lignes de la table ayant pour IdProduit $pIdProduit. Puis les renvoie sous forme d'une collection de StockProduitReservationVO
	 */
	public function selectByIdProduitStockProduitReservation($pIdProduit) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
		"(SELECT "
				. ProduitManager::CHAMP_PRODUIT_ID_COMMANDE .
				"," . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME .
				"," . ProduitManager::CHAMP_PRODUIT_ID .
				"," . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE .
				"," . ProduitManager::CHAMP_PRODUIT_TYPE .
				"," . NomProduitManager::CHAMP_NOMPRODUIT_NUMERO .
				"," . NomProduitManager::CHAMP_NOMPRODUIT_NOM .
				", (" . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . " - " . ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION . ") AS " . StockManager::CHAMP_STOCK_QUANTITE .
				", sum(" . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT .") AS " . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT
				. " FROM ((("
						. ProduitManager::TABLE_PRODUIT	.
						" JOIN " . NomProduitManager::TABLE_NOMPRODUIT . " ON ((" . NomProduitManager::CHAMP_NOMPRODUIT_ID . " = " . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT .")))
				 LEFT JOIN " . DetailCommandeManager::TABLE_DETAILCOMMANDE . " ON ((" . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . " = " . ProduitManager::CHAMP_PRODUIT_ID .")))
				 LEFT JOIN " . DetailOperationManager::TABLE_DETAILOPERATION . " ON (((" . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . " = " . DetailOperationManager::CHAMP_DETAILOPERATION_ID_DETAIL_COMMANDE .") and (" . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . " = 0))))
			WHERE "
					 		. ProduitManager::CHAMP_PRODUIT_ID . " = " . $pIdProduit
					 		. " AND " . ProduitManager::CHAMP_PRODUIT_ETAT . " = 0 "
					 				. " AND " . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . " <> -(1) "
					 						. " AND " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ETAT . " = 0 "
					 								. " AND ( " . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . " = 0 "
					 										. " OR ISNULL( " . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . "))
			GROUP BY " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . ")
			UNION
			(SELECT "
						. ProduitManager::CHAMP_PRODUIT_ID_COMMANDE .
						"," . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME .
						"," . ProduitManager::CHAMP_PRODUIT_ID .
						"," . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE .
						"," . ProduitManager::CHAMP_PRODUIT_TYPE .
						"," . NomProduitManager::CHAMP_NOMPRODUIT_NUMERO .
						"," . NomProduitManager::CHAMP_NOMPRODUIT_NOM .
						", ((" . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . " - " . ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION . ") + 1) AS " . StockManager::CHAMP_STOCK_QUANTITE .
						", sum(" . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT .") AS " . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT
						. " FROM ((("
								. ProduitManager::TABLE_PRODUIT	.
								" JOIN " . NomProduitManager::TABLE_NOMPRODUIT . " ON ((" . NomProduitManager::CHAMP_NOMPRODUIT_ID . " = " . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT .")))
				 LEFT JOIN " . DetailCommandeManager::TABLE_DETAILCOMMANDE . " ON ((" . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . " = " . ProduitManager::CHAMP_PRODUIT_ID .")))
				 LEFT JOIN " . DetailOperationManager::TABLE_DETAILOPERATION . " ON (((" . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . " = " . DetailOperationManager::CHAMP_DETAILOPERATION_ID_DETAIL_COMMANDE .") and (" . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . " = 0))))
			WHERE "
					 		. ProduitManager::CHAMP_PRODUIT_ID . " = " . $pIdProduit
					 		. " AND " . ProduitManager::CHAMP_PRODUIT_ETAT . " = 0 "
					 				. " AND " . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . " = -(1) "
					 						. " AND " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ETAT . " = 0 "
					 								. " AND ( " . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . " = 0 "
					 										. " OR ISNULL( " . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . "))
			GROUP BY " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . ");";
			
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
	
		$lListeStockProduitReservation = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeStockProduitReservation,
				$this->remplirStockProduitReservation(
				$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMMANDE],
				$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME],
				$lLigne[ProduitManager::CHAMP_PRODUIT_ID],
				$lLigne[ProduitManager::CHAMP_PRODUIT_UNITE_MESURE],
				$lLigne[ProduitManager::CHAMP_PRODUIT_TYPE],
				$lLigne[NomProduitManager::CHAMP_NOMPRODUIT_NUMERO],
				$lLigne[NomProduitManager::CHAMP_NOMPRODUIT_NOM],
				$lLigne[StockManager::CHAMP_STOCK_QUANTITE],
				$lLigne[DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT],"","",""));
			}
		} else {
			$lListeStockProduitReservation[0] = new StockProduitReservationVO();
		}
		return $lListeStockProduitReservation;
	}
	
	/**
	 * @name remplirStockProduitReservation($pProIdCommande, $pProIdCompteFerme, $pProId, $pProUniteMesure, $pProType, $pNproNumero, $pNproNom, $pStoQuantite, $pDopeMontant, $pDcomId, $pDcomTaille, $pDcomPrix)
	 * @param int(11)
	 * @param int(11)
	 * @param int(11)
	 * @param tinyint(4)
	 * @param varchar(20)
	 * @param varchar(50)
	 * @param varchar(50)
	 * @param decimal(33,2)
	 * @param decimal(32,2)
	 * @param int(11)
	 * @param decimal(10,2)
	 * @param decimal(10,2)
	 * @return StockProduitReservationVO
	 * @desc Retourne une StockProduitReservationVO remplie
	 */
	private function remplirStockProduitReservation($pProIdCommande, $pProIdCompteFerme, $pProId, $pProUniteMesure, $pProType, $pNproNumero, $pNproNom, $pStoQuantite, $pDopeMontant, $pDcomId, $pDcomTaille, $pDcomPrix) {
		$lStockProduitReservation = new StockProduitReservationVO();
		$lStockProduitReservation->setProIdCommande($pProIdCommande);
		$lStockProduitReservation->setProIdCompteFerme($pProIdCompteFerme);
		$lStockProduitReservation->setProId($pProId);
		$lStockProduitReservation->setProUniteMesure($pProUniteMesure);
		$lStockProduitReservation->setProType($pProType);
		$lStockProduitReservation->setNproNumero($pNproNumero);
		$lStockProduitReservation->setNproNom($pNproNom);
		$lStockProduitReservation->setStoQuantite($pStoQuantite);
		$lStockProduitReservation->setDopeMontant($pDopeMontant);
		$lStockProduitReservation->setDcomId($pDcomId);
		$lStockProduitReservation->setDcomTaille($pDcomTaille);
		$lStockProduitReservation->setDcomPrix($pDcomPrix);
		return $lStockProduitReservation;
	}
	
	/**
	 * @name getStockProduitFermes($pIdCompteFerme)
	 * @param array(integer) or integer
	 * @return array(StockProduitFermeVO)
	 * @desc Retourne le stock des produits de ferme(s)
	 */
	public function getStockProduitFermes($pIdCompteFerme) {	
		if(!is_array($pIdCompteFerme)) {
			$pIdCompteFerme = array($pIdCompteFerme);
		}
		return NomProduitManager::selectStockProduitFermes($pIdCompteFerme);
	}
	
	/**
	 * @name exportStock($pIdCompteFerme)
	 * @param array(integer) or integer
	 * @return xls
	 * @desc Export du stock des fermes
	 */
	public function exportStock($pIdCompteFerme) {		
		$lExportService = new JPIExportService();
				
		$lTitre = 'Stock';
		$lFormat = 'xls';
		
		// Création du phpExcel
		$lphpExcelObject = new PHPExcel();
			
		// Le titre de l'onglet
		$lphpExcelObject->getActiveSheet()->setTitle($lTitre);
		
		// Récupération des données
		$lStocks = $this->getStockProduitFermes($pIdCompteFerme);
		
		// L'entête de colonne
		$lExportAttributes = array("header" => array("Produit", "Stock", "Stock Solidaire", "Stock Total", "Valorisation"));
		
		// Le header
		$i = 'A';
		foreach($lExportAttributes["header"] as $nom) {
			$lphpExcelObject->setActiveSheetIndex(0)->setCellValue($i.'1', $nom)->getColumnDimension($i)->setAutoSize(true);
			$i++;
		}

		// Les données
		$lIdFerme = 0;
		$lIdCategorie = 0;
		
		$i = 2;
		foreach ( $lStocks as $lStock ) {
			if($lStock->getFerId() != $lIdFerme) {// Nouvelle ferme
				if($lIdFerme != 0) { // Ce n'est pas la première ferme ajout saut de ligne
					$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, "");
					$i++;
				}
				// Ajout du nom de la ferme
				$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, $lStock->getFerNumero() . " : " . $lStock->getFerNom());
				$i++;
				
				$lIdFerme = $lStock->getFerId();
				$lIdCategorie = 0;
			}
			
			if($lStock->getCproId() != $lIdCategorie) {// Nouvelle Catégorie
				// Ajout du nom de la catégorie
				$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, $lStock->getCproNom() );
				$i++;
				$lIdCategorie = $lStock->getCproId();
			}
						
			$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, $lStock->getNproNom());
			$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, $lStock->getStoQteQuantite() . " " . $lStock->getStoQteUnite());
			$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.$i, $lStock->getStoQteQuantiteSolidaire() . " " . $lStock->getStoQteUnite());
			$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.$i, $lStock->getStoQteQuantite() + $lStock->getStoQteQuantiteSolidaire() . " " . $lStock->getStoQteUnite());
			$lphpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.$i, $lStock->getValorisation());
			$i++;
		}

		$lconfig = new JPIExportConfig(
				$lTitre,
				$lFormat,
				$lExportAttributes,
				array(), $lphpExcelObject);
		
		// Export
		$lExportService->export($lconfig);
	}
	
	/**
	 * @name setStockQuantite($pStockQuantite)
	 * @param StockSolidaireVO
	 * @return bool
	 * @desc Ajoute ou modifie le stock quantite
	 */
	public function setStockQuantite($pStockQuantite) {
		$lStockValid = new StockValid();
		if($lStockValid->inputStockQuantite($pStockQuantite)) {
			if($lStockValid->insertStockQuantite($pStockQuantite)) {
				return $this->insertStockQuantite($pStockQuantite);
			} else if($lStockValid->updateStockQuantite($pStockQuantite)) {
				return $this->updateStockQuantite($pStockQuantite);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * @name insertStockQuantite($pStockQuantite)
	 * @param StockQuantiteVO
	 * @return integer
	 * @desc Ajoute un stock quantite
	 */
	private function insertStockQuantite($pStockQuantite) {
		$pStockQuantite->setDateCreation(StringUtils::dateTimeAujourdhuiDb());
		$pStockQuantite->setIdLogin($_SESSION[DROIT_ID]);
		$pStockQuantite->setEtat(0);
		return StockQuantiteManager::insert($pStockQuantite); // Ajout du stock quantite retour l'Id
	}
	
	/**
	 * @name updateStockQuantite($pStockQuantite)
	 * @param StockQuantiteVO
	 * @return integer
	 * @desc Met à jour un stock quantite
	 */
	private function updateStockQuantite($pStockQuantite) {
		
		$lStockActuel = $this->getStockQuantite($pStockQuantite->getId());
		$pStockQuantite->setIdNomProduit($lStockActuel->getIdNomProduit());
		$pStockQuantite->setUnite($lStockActuel->getUnite());
		$pStockQuantite->setDateCreation($lStockActuel->getDateCreation());
		$pStockQuantite->setDateModification(StringUtils::dateTimeAujourdhuiDb());
		$pStockQuantite->setIdLogin($_SESSION[DROIT_ID]);
		//$pStock->setEtat($lStockActuel->getEtat());
		
		return StockQuantiteManager::update($pStockQuantite); // update
	}
	
	/**
	 * @name deleteStockQuantite($pId)
	 * @param integer
	 * @desc Supprime le stock quantite
	 */
	public function deleteStockQuantite($pId) {
		$lStockValid = new StockValid();
		if($lStockValid->deleteStockQuantite($pId)){
			$lStockQuantite = $this->getStockQuantite($pId);
			$lStockQuantite->setEtat(1);
			return $this->updateStockQuantite($lStockQuantite);
		} else {
			return false;
		}
	}
	
	/**
	 * @name getStockQuantite($pId)
	 * @param integer
	 * @return array(StockQuantiteVO) ou StockQuantiteVO
	 * @desc Retourne une liste de StockQuantite
	 */
	public function getStockQuantite($pId = null) {
		if($pId != null) {
			return $this->selectQuantite($pId);
		} else {
			return $this->selectQuantiteAll();
		}
	}
	
	/**
	 * @name selectQuantite($pId)
	 * @param integer
	 * @return StockQuantiteVO
	 * @desc Retourne une ligne de Stock Quantite
	 */
	public function selectQuantite($pId) {
		return StockQuantiteManager::select($pId);
	}
	
	/**
	 * @name selectQuantiteAll()
	 * @return array(StockQuantiteVO)
	 * @desc Retourne une liste de Stock Quantite
	 */
	public function selectQuantiteAll() {
		return StockQuantiteManager::selectAll();
	}
	
	/**
	 * @name selectQuantiteByIdNomProduitUnite($pIdNomProduit,$pUnite)
	 * @return array(StockQuantiteVO)
	 * @desc Retourne une liste de Stock Quantite
	 */
	public function selectQuantiteByIdNomProduitUnite($pIdNomProduit,$pUnite) {
		return StockQuantiteManager::recherche(
				array(StockQuantiteManager::CHAMP_STOCKQUANTITE_ETAT, StockQuantiteManager::CHAMP_STOCKQUANTITE_ID_NOM_PRODUIT, StockQuantiteManager::CHAMP_STOCKQUANTITE_UNITE),
				array('=','=','='),
				array(0,$pIdNomProduit,$pUnite),
				array(''),
				array(''));
	}
	
	/**
	 * @name selectQuantiteAllActif()
	 * @return array(StockQuantiteVO)
	 * @desc Retourne une liste de Stock Quantite
	 */
	public function selectQuantiteAllActif() {
		return StockQuantiteManager::recherche(
				array(StockQuantiteManager::CHAMP_STOCKQUANTITE_ETAT),
				array('='),
				array(0),
				array(''),
				array(''));
	}
	
	/**
	 * @name getProduitsDisponible()
	 * @return array(ProduitMarcheVO)
	 * @desc Retourne la liste des produits disponible en stock
	 */
	public function getProduitsDisponible() {
		$lStockProduit = StockProduitDisponibleViewManager::selectAll();
		$lProduits = array();
		foreach($lStockProduit as $lProduit) {
			// Le Produit
			if(!isset($lProduits[$lProduit->getNproId()])) {
				$lProduitMarche = new ProduitMarcheVO();
				/*$lProduitMarche->setId();
				$lProduitMarche->setIdCompteFerme();*/
				$lProduitMarche->setIdNom($lProduit->getNproId());
				$lProduitMarche->setNom($lProduit->getNproNom());
			/*	$lProduitMarche->setDescription(); */
				$lProduitMarche->setIdCategorie($lProduit->getCproId());
				$lProduitMarche->setCproNom($lProduit->getCproNom());
				$lProduitMarche->setUnite($lProduit->getStoQteUnite());
				/*$lProduitMarche->setQteMaxCommande($lDetail->getProMaxProduitCommande());
				$lProduitMarche->setStockReservation($lDetail->getProStockReservation());
				$lProduitMarche->setStockInitial($lDetail->getProStockInitial());
				$lProduitMarche->setType($lDetail->getProType());*/
				$lProduitMarche->setFerId($lProduit->getFerId());
				$lProduitMarche->setFerNom($lProduit->getFerNom());
				$lProduits[$lProduit->getNproId()] = $lProduitMarche;
			}
	
			// Le Lot
			$lLot = new DetailMarcheVO();
			$lLot->setId($lProduit->getMLotId());
			$lLot->setTaille($lProduit->getMLotQuantite());
			$lLot->setPrix($lProduit->getMLotPrix());
			$lLots = $lProduits[$lProduit->getNproId()]->getLots();
			$lLots[$lProduit->getMLotId()] = $lLot;
			$lProduits[$lProduit->getNproId()]->setLots($lLots);
		}
		
		return $lProduits;
	}
}
?>