<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 23/01/2011
// Fichier : MailingListeService.php
//
// Description : Classe MailingListeService
//
//****************************************************************

// Inclusion des classes
include_once(CHEMIN_CONFIGURATION . "Mail.php"); // Les Constantes de mail
include_once(CHEMIN_CLASSES_VALIDATEUR . "MailingListeValid.php");
include_once(CHEMIN_CONFIGURATION . "ApiOVH.php"); // Les Constantes de mail


require __DIR__ . '/../../vendor/autoload.php';
use \Ovh\Api;



/**
 * @name MailingListeService
 * @author Julien PIERRE
 * @since 23/01/2011
 * @desc Classe Service d'une MailingListe
 */
class MailingListeService
{	
	/**
	* @name insert($pMail)
	* @param String
	* @return VR
	* @desc Ajoute un mail à la mailing liste
	*/
	public function insert($pMail) {
		$lMailingListeValid = new MailingListeValid();
		$lVr = $lMailingListeValid->validAjout($pMail);
		if ($lVr->getValid()) {
			// Initialisation du Logger
			$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
			$lLogger->setMask(Log::MAX(LOG_LEVEL));
			try {
				$conn = new Api(    APPLICATION_KEY,
						APPLICATION_SECRET,
						ENDPOINT,
						CONSUMER_KEY);
				$content = (object) array('email' => $pMail);
				$servers = $conn->post('/email/domain/' . MAIL_MAILING_LISTE_DOMAIN . '/mailingList/' . MAIL_MAILING_LISTE . '/subscriber', $content);
	
				$lLogger->log("Ajout à la mailing liste : " . $pMail . ".",PEAR_LOG_INFO);	// Maj des logs
			} catch (Exception $e) {
				$lLogger->log("Erreur ajout à la mailing liste : " . $pMail . ".",PEAR_LOG_INFO);	// Maj des logs
				$lLogger->log($e->getMessage(),PEAR_LOG_DEBUG);	// Maj des logs
			}
			
		}
		return $lVr;
	}
		
	/**
	* @name delete($pMail)
	* @param String
	* @return VR
	* @desc Supprime un mail de la mailing liste
	*/
	public function delete($pMail) {		
		$lMailingListeValid = new MailingListeValid();
		$lVr = $lMailingListeValid->validAjout($pMail);
		if ($lVr->getValid()) {
			// Initialisation du Logger
			$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
			$lLogger->setMask(Log::MAX(LOG_LEVEL));
			try {			
				$conn = new Api(    APPLICATION_KEY,
						APPLICATION_SECRET,
						ENDPOINT,
						CONSUMER_KEY);
				$servers = $conn->delete('/email/domain/' . MAIL_MAILING_LISTE_DOMAIN . '/mailingList/' . MAIL_MAILING_LISTE . '/subscriber/' . $pMail);
				
				$lLogger->log("Suppression de la mailing liste : " . $pMail . ".",PEAR_LOG_INFO);	// Maj des logs
			} catch (Exception $e) {
				$lLogger->log("Erreur suppression de la mailing liste : " . $pMail . ".",PEAR_LOG_INFO);	// Maj des logs
				$lLogger->log($e->getMessage(),PEAR_LOG_DEBUG);	// Maj des logs
			}
		}
		return $lVr;
	}
}
?>