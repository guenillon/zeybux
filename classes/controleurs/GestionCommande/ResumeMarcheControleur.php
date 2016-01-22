<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 27/02/2011
// Fichier : ResumeMarcheControleur.php
//
// Description : Classe ResumeMarcheControleur
//
//****************************************************************

// Inclusion des classes
include_once(CHEMIN_CLASSES_MANAGERS . "CommandeManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "ProduitManager.php");
include_once(CHEMIN_CLASSES_SERVICE . "MarcheService.php" );
include_once(CHEMIN_CLASSES_RESPONSE . MOD_GESTION_COMMANDE . "/InfoCommandeResponse.php" );
include_once(CHEMIN_CLASSES_VALIDATEUR . MOD_GESTION_COMMANDE . "/InfoCommandeValid.php" );

/**
 * @name ResumeMarcheControleur
 * @author Julien PIERRE
 * @since 27/02/2011
 * @desc Classe controleur d'une ResumeMarche
 */
class ResumeMarcheControleur
{	
	/**
	* @name getInfoMarche()
	* @return InfoCommandeResponse
	* @desc Retourne les infos sur la commande archivée
	*/
	public function getInfoMarche($pParam) {
		$lVr = InfoCommandeValid::get($pParam);		
		if($lVr->getValid()) {
			$lResponse = new InfoCommandeResponse();
			$lResponse->setInfoCommande( ProduitManager::selectResumeMarche($pParam['id_marche'])  );
			$lResponse->setDetailMarche( CommandeManager::select($pParam['id_marche']) );
			
			
			$lMarcheService=  new MarcheService();
			$lResponse->setNbResaAchat( $lMarcheService->getNbReservationEtAchatMarche($pParam['id_marche']) );
			$lResponse->setCa( $lMarcheService->getCaMarche($pParam['id_marche']) );
			$lResponse->setReservationAbonnement($lMarcheService->getNbReservationAbonnement($pParam['id_marche']));
			$lResponse->setAchatAbonnement($lMarcheService->getNbAchatAbonnement($pParam['id_marche']));
			$lResponse->setNbAchat($lMarcheService->getNbAchatMarche($pParam['id_marche']));
			return $lResponse;
		}
		return $lVr;
	}
}
?>