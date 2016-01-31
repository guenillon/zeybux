<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 23/01/2016
// Fichier : AdherentValid.php
//
// Description : Classe AdherentValid
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_UTILS . "TestFonction.php" );
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php" );
include_once(CHEMIN_CLASSES_VR . "VRerreur.php" );
include_once(CHEMIN_CLASSES_VR . MOD_IDENTIFICATION . "/AdherentVR.php" );
include_once(CHEMIN_CLASSES_MANAGERS . "ModuleManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "AdherentManager.php");

/**
 * @name AdherentVR
 * @author Julien PIERRE
 * @since 09/11/2010
 * @desc Classe représentant une AdherentValid
 */
class AdherentValid
{
	/**
	* @name validAjout($pData)
	* @return AdherentVR
	* @desc Test la validite de l'élément
	*/
	public static function validAjout($pData) {
		$lVr = new AdherentVR();
		//Tests inputs
		if(!isset($pData['nom'])) {
			$lVr->setValid(false);
			$lVr->getNom()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getNom()->addErreur($lErreur);
		}
		if(!isset($pData['prenom'])) {
			$lVr->setValid(false);
			$lVr->getPrenom()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getPrenom()->addErreur($lErreur);
		}
		if(!isset($pData['courrielPrincipal'])) {
			$lVr->setValid(false);
			$lVr->getCourrielPrincipal()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getCourrielPrincipal()->addErreur($lErreur);
		}
		if(!isset($pData['courrielSecondaire'])) {
			$lVr->setValid(false);
			$lVr->getCourrielSecondaire()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getCourrielSecondaire()->addErreur($lErreur);
		}
		if(!isset($pData['telephonePrincipal'])) {
			$lVr->setValid(false);
			$lVr->getTelephonePrincipal()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getTelephonePrincipal()->addErreur($lErreur);
		}
		if(!isset($pData['telephoneSecondaire'])) {
			$lVr->setValid(false);
			$lVr->getTelephoneSecondaire()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getTelephoneSecondaire()->addErreur($lErreur);
		}
		if(!isset($pData['adresse'])) {
			$lVr->setValid(false);
			$lVr->getAdresse()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getAdresse()->addErreur($lErreur);
		}
		if(!isset($pData['codePostal'])) {
			$lVr->setValid(false);
			$lVr->getCodePostal()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getCodePostal()->addErreur($lErreur);
		}
		if(!isset($pData['ville'])) {
			$lVr->setValid(false);
			$lVr->getVille()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getVille()->addErreur($lErreur);
		}
		if(!isset($pData['dateNaissance'])) {
			$lVr->setValid(false);
			$lVr->getDateNaissance()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getDateNaissance()->addErreur($lErreur);
		}
		
		if($lVr->getValid()) {
			//Tests Techniques
			if(!TestFonction::checkLength($pData['nom'],0,50)) {
				$lVr->setValid(false);
				$lVr->getNom()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getNom()->addErreur($lErreur);	
			}
			if(!TestFonction::checkLength($pData['prenom'],0,50)) {
				$lVr->setValid(false);
				$lVr->getPrenom()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getPrenom()->addErreur($lErreur);	
			}
			if(!TestFonction::checkLength($pData['courrielPrincipal'],0,100)) {
				$lVr->setValid(false);
				$lVr->getCourrielPrincipal()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getCourrielPrincipal()->addErreur($lErreur);	
			}
			if(!TestFonction::checkLength($pData['courrielSecondaire'],0,100)) {
				$lVr->setValid(false);
				$lVr->getCourrielSecondaire()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getCourrielSecondaire()->addErreur($lErreur);	
			}
			if(!TestFonction::checkLength($pData['telephonePrincipal'],0,20)) {
				$lVr->setValid(false);
				$lVr->getTelephonePrincipal()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getTelephonePrincipal()->addErreur($lErreur);	
			}
			if(!TestFonction::checkLength($pData['telephoneSecondaire'],0,20)) {
				$lVr->setValid(false);
				$lVr->getTelephoneSecondaire()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getTelephoneSecondaire()->addErreur($lErreur);	
			}
			if(!TestFonction::checkLength($pData['adresse'],0,300)) {
				$lVr->setValid(false);
				$lVr->getAdresse()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getAdresse()->addErreur($lErreur);	
			}
			if(!TestFonction::checkLength($pData['codePostal'],0,10)) {
				$lVr->setValid(false);
				$lVr->getCodePostal()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getCodePostal()->addErreur($lErreur);	
			}
			if(!TestFonction::checkLength($pData['ville'],0,100)) {
				$lVr->setValid(false);
				$lVr->getVille()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getVille()->addErreur($lErreur);	
			}
			if($pData['dateNaissance']	!= '' && !TestFonction::checkDate($pData['dateNaissance'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateNaissance()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_103_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_103_MSG);
				$lVr->getDateNaissance()->addErreur($lErreur);	
			}
			if($pData['dateNaissance']	!= '' && !TestFonction::checkDateExist($pData['dateNaissance'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateNaissance()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_105_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_105_MSG);
				$lVr->getDateNaissance()->addErreur($lErreur);	
			}
			if(!TestFonction::checkLength($pData['commentaire'],0,500)) {
				$lVr->setValid(false);
				$lVr->getCommentaire()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getCommentaire()->addErreur($lErreur);	
			}
	
			//Tests Fonctionnels
			if(empty($pData['nom'])) {
				$lVr->setValid(false);
				$lVr->getNom()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getNom()->addErreur($lErreur);	
			}
			if(empty($pData['prenom'])) {
				$lVr->setValid(false);
				$lVr->getPrenom()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getPrenom()->addErreur($lErreur);	
			}
			if(empty($pData['courrielPrincipal'])) {
				$lVr->setValid(false);
				$lVr->getCourrielPrincipal()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getCourrielPrincipal()->addErreur($lErreur);	
			}
					
			// Les mails sont au bon format
			if($pData['courrielPrincipal']	!= '' && !TestFonction::checkCourriel($pData['courrielPrincipal'])) {
				$lVr->setValid(false);
				$lVr->getCourrielPrincipal()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_224_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_224_MSG);
				$lVr->getCourrielPrincipal()->addErreur($lErreur);
			}
			if($pData['courrielSecondaire']	!= '' && !TestFonction::checkCourriel($pData['courrielSecondaire'])) {
				$lVr->setValid(false);
				$lVr->getCourrielSecondaire()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_224_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_224_MSG);
				$lVr->getCourrielSecondaire()->addErreur($lErreur);
			}
			
			// Date Naissance <= Date Adhésion <= Date Actuelle
			if($pData['dateNaissance'] != '' && !TestFonction::dateEstPLusGrandeEgale(StringUtils::dateAujourdhuiDb(),$pData['dateNaissance'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateNaissance()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_230_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_230_MSG);
				$lVr->getDateNaissance()->addErreur($lErreur);
			}		
		}
		return $lVr;
	}
}
?>