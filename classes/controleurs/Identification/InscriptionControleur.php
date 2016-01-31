<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 23/01/2016
// Fichier : InscriptionControleur.php
//
// Description : Classe InscriptionControleur
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_VALIDATEUR . MOD_IDENTIFICATION . "/AdherentValid.php" );
include_once(CHEMIN_CLASSES_RESPONSE . MOD_IDENTIFICATION . "/AjoutAdherentResponse.php" );
include_once(CHEMIN_CLASSES_SERVICE . "AdherentService.php");
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php" );

/**
 * @name InscriptionControleur
 * @author Julien PIERRE
 * @since 23/01/2016
 * @desc Classe controleur d'un Ajout d'adherent
 */
class InscriptionControleur
{	
	/**
	* @name ajoutAdherent($pAdherent)
	* @return string
	* @desc Controle et formatte les données avant de les insérer dans la BDD. Retourne l'Id en cas de succés ou une erreur.
	*/
	public function ajoutAdherent($pAdherent) {				
		$lVr = AdherentValid::validAjout($pAdherent);
		if($lVr->getValid()) {			
			$lAdherent = new AdherentVO();
			$lAdherent->setIdCompte(0);
			$lAdherent->setNom($pAdherent['nom']);
			$lAdherent->setPrenom($pAdherent['prenom']);
			$lAdherent->setCourrielPrincipal($pAdherent['courrielPrincipal']);
			$lAdherent->setCourrielSecondaire($pAdherent['courrielSecondaire']);
			$lAdherent->setTelephonePrincipal($pAdherent['telephonePrincipal']);
			$lAdherent->setTelephoneSecondaire($pAdherent['telephoneSecondaire']);
			$lAdherent->setAdresse($pAdherent['adresse']);
			$lAdherent->setCodePostal($pAdherent['codePostal']);
			$lAdherent->setVille($pAdherent['ville']);
			$lAdherent->setDateNaissance($pAdherent['dateNaissance']);
			$lAdherent->setDateAdhesion(StringUtils::dateTimeAujourdhuiDb());
			$lAdherent->setCommentaire($pAdherent['commentaire']);
			$lAdherent->setEtat(3);		
			$lAdherentService = new AdherentService();
			$lAdherent = $lAdherentService->set($lAdherent);
			
			$lResponse = new AjoutAdherentResponse();
			$lResponse->setId($lAdherent->getId());
			return $lResponse;						
		}	
		return $lVr;
	}
}
?>