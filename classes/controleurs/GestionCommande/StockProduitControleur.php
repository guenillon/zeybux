<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 28/04/2013
// Fichier : StockProduitControleur.php
//
// Description : Classe StockProduitControleur
//
//****************************************************************

// Inclusion des classes
include_once(CHEMIN_CLASSES_RESPONSE . MOD_GESTION_COMMANDE . "/ListeFermeResponse.php" );
include_once(CHEMIN_CLASSES_RESPONSE . MOD_GESTION_COMMANDE . "/ListeProduitResponse.php" );
include_once(CHEMIN_CLASSES_SERVICE . "StockService.php");
include_once(CHEMIN_CLASSES_SERVICE . "FermeService.php");
include_once(CHEMIN_CLASSES_VALIDATEUR . MOD_GESTION_COMMANDE . "/FermeValid.php");
include_once(CHEMIN_CLASSES_VALIDATEUR . MOD_GESTION_COMMANDE . "/StockQuantiteValid.php");
include_once(CHEMIN_CLASSES_TOVO . "StockQuantiteToVO.php" );
/**
 * @name StockProduitControleur
 * @author Julien PIERRE
 * @since 28/04/2013
 * @desc Classe controleur d'une AchatAdherent
 */
class StockProduitControleur
{	
	/**
	 * @name getListeFerme()
	 * @return ListeFermeResponse
	 * @desc Recherche la liste des Fermes
	 */
	public function getListeFerme() {
		// Lancement de la recherche
		$lResponse = new ListeFermeResponse();
		$lFermeService = new FermeService();
		$lResponse->setListeFerme($lFermeService->get());
		return $lResponse;
	}
	
	/**
	 * @name getDetailStockProduitFerme($pParam)
	 * @return ListeProduitResponse
	 * @desc Retourne la liste des stocks de produit de la Ferme
	 */
	public function getDetailStockProduitFerme($pParam) {
		$lVr = FermeValid::validGetByIdCompte($pParam);
		if($lVr->getValid()) {
			$lStockService = new StockService();
			$lResponse = new ListeProduitResponse();
			$lResponse->setListeProduit($lStockService->getStockProduitFermes($pParam['idCompte']));
			
			return $lResponse;
		}
		return $lVr;
	}
	
	/**
	 * @name modifierStock($pParam)
	 * @return StockQuantiteVR
	 * @desc Modifie une ligne de stock
	 */
	public function modifierStock($pParam) {
		$lVr = StockQuantiteValid::validUpdate($pParam);
		if($lVr->getValid()) {
			$lStockService = new StockService();
			$lStockService->setStockQuantite(StockQuantiteToVO::convertFromArray($pParam));
		}
		return $lVr;
	}
	
	/**
	 * @name exportStock($pParam)
	 * @return array()
	 * @desc Exporte le stock
	 */
	public function exportStock($pParam) {
		$lStockService = new StockService();
		$lStockService->exportStock($pParam['id_fermes']);
	}
}
?>