<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 22/01/2016
// Fichier : ReservationSansAchatControleur.php
//
// Description : Classe ReservationSansAchatControleur
//
//****************************************************************

// Inclusion des classes
include_once(CHEMIN_CLASSES_SERVICE . "ReservationService.php");
include_once(CHEMIN_CLASSES_VALIDATEUR . MOD_GESTION_COMMANDE . "/InfoCommandeValid.php" );
include_once(CHEMIN_CLASSES_RESPONSE . MOD_GESTION_COMMANDE . "/ListeAdherentResponse.php" );

/**
 * @name ReservationSansAchatControleur
 * @author Julien PIERRE
 * @since 22/01/2016
 * @desc Classe controleur d'une ReservationSansAchat
 */
class ReservationSansAchatControleur
{		
	/**
	* @name getListeAdherent($pParam)
	* @return ListeAdherentResponse
	* @desc Recherche la liste des adherents
	*/
	public function getReservation($pParam) {
		$lVr = InfoCommandeValid::get($pParam);		
		
		if($lVr->getValid()) {
			$lResponse = new ListeAdherentResponse();
			$lReservationService = new ReservationService();
			$lResponse->setListeAdherent($lReservationService->getReservationNonAchete($pParam['id_marche']));
			return $lResponse;
		}
		return $lVr;
	}
}
?>