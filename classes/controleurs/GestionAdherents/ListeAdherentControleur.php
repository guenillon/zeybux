<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 31/01/2010
// Fichier : ListeAdherentControleur.php
//
// Description : Classe ListeAdherentControleur
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_SERVICE . "AdherentService.php" );
include_once(CHEMIN_CLASSES_RESPONSE . MOD_GESTION_ADHERENTS . "/ListeAdherentResponse.php" );

/**
 * @name ListeAdherentControleur
 * @author Julien PIERRE
 * @since 31/01/2010
 * @desc Classe controleur d'une liste d'adherent
 */
class ListeAdherentControleur
{	
	/**
	* @name getListeAdherent()
	* @return ListeAdherentResponse
	* @desc Recherche la liste des adherents
	*/
	public function getListeAdherent($pParam) {	
		if(isset($pParam['type'])) {
			$lType = $pParam['type'];
		} else {
			$lType = array(1);
		}
		
		// Lancement de la recherche
		$lResponse = new ListeAdherentResponse();
		$lAdherentService = new AdherentService();
		$lResponse->setListeAdherent($lAdherentService->getAllResumeSolde($lType));
		return $lResponse;
	}
}
?>
