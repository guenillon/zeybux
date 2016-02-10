<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 31/07/2011
// Fichier : ModifierMarcheValid.php
//
// Description : Classe ModifierMarcheValid
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_UTILS . "TestFonction.php" );
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php" );
include_once(CHEMIN_CLASSES_UTILS . "MessagesErreurs.php" );
include_once(CHEMIN_CLASSES_VR . "VRerreur.php" );
include_once(CHEMIN_CLASSES_VR . MOD_GESTION_COMMANDE . "/ModifierMarcheVR.php" );
include_once(CHEMIN_CLASSES_VALIDATEUR . MOD_GESTION_COMMANDE . "/ProduitCommandeValid.php" );
include_once(CHEMIN_CLASSES_MANAGERS . "CommandeManager.php");

/**
 * @name ModifierMarcheVR
 * @author Julien PIERRE
 * @since 31/07/2011
 * @desc Classe représentant une ModifierMarcheValid
 */
class ModifierMarcheValid
{
	/**
	* @name validUpdate($pData)
	* @return ModifierMarcheVR
	* @desc Test la validite de l'élément
	*/
	public static function validUpdate($pData) {
		$lVr = new ModifierMarcheVR();
		//Tests inputs
		if(!isset($pData['id'])) {
			$lVr->setValid(false);
			$lVr->getId()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getId()->addErreur($lErreur);	
		}		
		if(!isset($pData['numero'])) {
			$lVr->setValid(false);
			$lVr->getNumero()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getNumero()->addErreur($lErreur);	
		}
		if(!isset($pData['dateMarcheDebut'])) {
			$lVr->setValid(false);
			$lVr->getDateMarcheDebut()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getDateMarcheDebut()->addErreur($lErreur);	
		}
		if(!isset($pData['timeMarcheDebut'])) {
			$lVr->setValid(false);
			$lVr->getTimeMarcheDebut()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getTimeMarcheDebut()->addErreur($lErreur);	
		}
		if(!isset($pData['dateMarcheFin'])) {
			$lVr->setValid(false);
			$lVr->getDateMarcheFin()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getDateMarcheFin()->addErreur($lErreur);	
		}
		if(!isset($pData['timeMarcheFin'])) {
			$lVr->setValid(false);
			$lVr->getTimeMarcheFin()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getTimeMarcheFin()->addErreur($lErreur);	
		}
		if(!isset($pData['dateFinReservation'])) {
			$lVr->setValid(false);
			$lVr->getDateFinReservation()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getDateFinReservation()->addErreur($lErreur);	
		}
		if(!isset($pData['timeFinReservation'])) {
			$lVr->setValid(false);
			$lVr->getTimeFinReservation()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getTimeFinReservation()->addErreur($lErreur);	
		}
		if(!isset($pData['archive']) && intval($pData['archive']) != 0) {
			$lVr->setValid(false);
			$lVr->getArchive()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getArchive()->addErreur($lErreur);	
		}
		if(!isset($pData['produits'])) {
			$lVr->setValid(false);
			$lVr->getLog()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_207_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_207_MSG);
			$lVr->getLog()->addErreur($lErreur);	
		}
		
		if($lVr->getValid()) {
			//Tests Techniques
			if(!TestFonction::checkLength($pData['id'],0,11)) {
				$lVr->setValid(false);
				$lVr->getId()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getId()->addErreur($lErreur);	
			}
			if(!is_int((int)$pData['id'])) {
				$lVr->setValid(false);
				$lVr->getId()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_108_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_108_MSG);
				$lVr->getId()->addErreur($lErreur);	
			}
			if(!TestFonction::checkLength($pData['numero'],0,11)) {
				$lVr->setValid(false);
				$lVr->getNumero()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getNumero()->addErreur($lErreur);	
			}
			if(!is_int((int)$pData['numero'])) {
				$lVr->setValid(false);
				$lVr->getNumero()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_108_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_108_MSG);
				$lVr->getNumero()->addErreur($lErreur);	
			}
			if(!TestFonction::checkLength($pData['nom'],0,100)) {
				$lVr->setValid(false);
				$lVr->getNom()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getNom()->addErreur($lErreur);	
			}
			if(!TestFonction::checkDate($pData['dateMarcheDebut'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateMarcheDebut()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_103_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_103_MSG);
				$lVr->getDateMarcheDebut()->addErreur($lErreur);	
			}
			if(!TestFonction::checkDateExist($pData['dateMarcheDebut'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateMarcheDebut()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_105_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_105_MSG);
				$lVr->getDateMarcheDebut()->addErreur($lErreur);	
			}
			if(!TestFonction::checkTime($pData['timeMarcheDebut'])) {
				$lVr->setValid(false);
				$lVr->getTimeMarcheDebut()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_106_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_106_MSG);
				$lVr->getTimeMarcheDebut()->addErreur($lErreur);	
			}
			if(!TestFonction::checkTimeExist($pData['timeMarcheDebut'])) {
				$lVr->setValid(false);
				$lVr->getTimeMarcheDebut()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_107_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_107_MSG);
				$lVr->getTimeMarcheDebut()->addErreur($lErreur);	
			}
			if(!TestFonction::checkDate($pData['dateMarcheFin'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateMarcheFin()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_103_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_103_MSG);
				$lVr->getDateMarcheFin()->addErreur($lErreur);	
			}
			if(!TestFonction::checkDateExist($pData['dateMarcheFin'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateMarcheFin()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_105_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_105_MSG);
				$lVr->getDateMarcheFin()->addErreur($lErreur);	
			}
			if(!TestFonction::checkTime($pData['timeMarcheFin'])) {
				$lVr->setValid(false);
				$lVr->getTimeMarcheFin()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_106_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_106_MSG);
				$lVr->getTimeMarcheFin()->addErreur($lErreur);	
			}
			if(!TestFonction::checkTimeExist($pData['timeMarcheFin'])) {
				$lVr->setValid(false);
				$lVr->getTimeMarcheFin()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_107_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_107_MSG);
				$lVr->getTimeMarcheFin()->addErreur($lErreur);	
			}
			if(!TestFonction::checkDate($pData['dateFinReservation'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateFinReservation()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_103_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_103_MSG);
				$lVr->getDateFinReservation()->addErreur($lErreur);	
			}
			if(!TestFonction::checkDateExist($pData['dateFinReservation'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateFinReservation()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_105_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_105_MSG);
				$lVr->getDateFinReservation()->addErreur($lErreur);	
			}
			if(!TestFonction::checkTime($pData['timeFinReservation'])) {
				$lVr->setValid(false);
				$lVr->getTimeFinReservation()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_106_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_106_MSG);
				$lVr->getTimeFinReservation()->addErreur($lErreur);	
			}
			if(!TestFonction::checkTimeExist($pData['timeFinReservation'])) {
				$lVr->setValid(false);
				$lVr->getTimeFinReservation()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_107_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_107_MSG);
				$lVr->getTimeFinReservation()->addErreur($lErreur);	
			}
			if(!TestFonction::checkLength($pData['archive'],0,1)) {
				$lVr->setValid(false);
				$lVr->getArchive()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getArchive()->addErreur($lErreur);	
			}
			if(!is_array($pData['produits'])) {
				$lVr->setValid(false);
				$lVr->getLog()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_111_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_111_MSG);
				$lVr->getLog()->addErreur($lErreur);	
			}

			//Tests Fonctionnels
			if(empty($pData['id'])) {
				$lVr->setValid(false);
				$lVr->getId()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getId()->addErreur($lErreur);	
			}
			if(empty($pData['numero'])) {
				$lVr->setValid(false);
				$lVr->getNumero()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getNumero()->addErreur($lErreur);	
			}
			if(empty($pData['dateMarcheDebut'])) {
				$lVr->setValid(false);
				$lVr->getDateMarcheDebut()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getDateMarcheDebut()->addErreur($lErreur);	
			}
			if(empty($pData['timeMarcheDebut'])) {
				$lVr->setValid(false);
				$lVr->getTimeMarcheDebut()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getTimeMarcheDebut()->addErreur($lErreur);	
			}
			if(empty($pData['dateMarcheFin'])) {
				$lVr->setValid(false);
				$lVr->getDateMarcheFin()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getDateMarcheFin()->addErreur($lErreur);	
			}
			if(empty($pData['timeMarcheFin'])) {
				$lVr->setValid(false);
				$lVr->getTimeMarcheFin()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getTimeMarcheFin()->addErreur($lErreur);	
			}
			if(empty($pData['dateFinReservation'])) {
				$lVr->setValid(false);
				$lVr->getDateFinReservation()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getDateFinReservation()->addErreur($lErreur);	
			}
			if(empty($pData['timeFinReservation'])) {
				$lVr->setValid(false);
				$lVr->getTimeFinReservation()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getTimeFinReservation()->addErreur($lErreur);	
			}
			if(empty($pData['archive']) && intval($pData['archive']) != 0) {
				$lVr->setValid(false);
				$lVr->getArchive()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getArchive()->addErreur($lErreur);	
			}
			if(empty($pData['produits'])) {
				$lVr->setValid(false);
				$lVr->getLog()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_207_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_207_MSG);
				$lVr->getLog()->addErreur($lErreur);	
			}
			
			if(!TestFonction::dateTimeEstPLusGrandeEgale($pData['dateMarcheDebut'] . " " . $pData['timeMarcheDebut'],$pData['dateFinReservation'] . " " . $pData['timeFinReservation'],"db")) {
				if(!TestFonction::dateEstPLusGrandeEgale($pData['dateMarcheDebut'],$pData['dateFinReservation'],"db")) {			
					$lVr->setValid(false);
					$lVr->getDateMarcheDebut()->setValid(false);
					$lVr->getDateFinReservation()->setValid(false);
					$lErreur = new VRerreur();
					$lErreur->setCode(MessagesErreurs::ERR_202_CODE);
					$lErreur->setMessage(MessagesErreurs::ERR_202_MSG);
					$lVr->getDateMarcheDebut()->addErreur($lErreur);		
					$lVr->getDateFinReservation()->addErreur($lErreur);				
				} else if(TestFonction::timeEstPLusGrandeEgale($pData['timeFinReservation'],$pData['timeMarcheDebut'])) {
					$lVr->setValid(false);
					$lVr->getTimeMarcheDebut()->setValid(false);
					$lVr->getTimeFinReservation()->setValid(false);
					$lErreur = new VRerreur();
					$lErreur->setCode(MessagesErreurs::ERR_203_CODE);
					$lErreur->setMessage(MessagesErreurs::ERR_203_MSG);
					$lVr->getTimeMarcheDebut()->addErreur($lErreur);		
					$lVr->getTimeFinReservation()->addErreur($lErreur);			
				}
			}
			
			if(TestFonction::dateTimeEstPLusGrandeEgale($pData['dateMarcheDebut'] . " " . $pData['timeMarcheDebut'],$pData['dateMarcheFin'] . " " . $pData['timeMarcheFin'],"db")) {
				if(TestFonction::timeEstPLusGrandeEgale($pData['timeMarcheDebut'],$pData['timeMarcheFin'])) {			
					$lVr->setValid(false);
					$lVr->getTimeMarcheDebut()->setValid(false);
					$lVr->getTimeMarcheFin()->setValid(false);
					$lErreur = new VRerreur();
					$lErreur->setCode(MessagesErreurs::ERR_204_CODE);
					$lErreur->setMessage(MessagesErreurs::ERR_204_MSG);
					$lVr->getTimeMarcheDebut()->addErreur($lErreur);		
					$lVr->getTimeMarcheFin()->addErreur($lErreur);	
				} else if(TestFonction::dateEstPLusGrandeEgale($pData['dateMarcheDebut'],$pData['dateMarcheFin'],"db")) {
					$lVr->setValid(false);
					$lVr->getDateMarcheDebut()->setValid(false);
					$lVr->getDateMarcheFin()->setValid(false);
					$lErreur = new VRerreur();
					$lErreur->setCode(MessagesErreurs::ERR_208_CODE);
					$lErreur->setMessage(MessagesErreurs::ERR_208_MSG);
					$lVr->getDateMarcheDebut()->addErreur($lErreur);		
					$lVr->getDateMarcheFin()->addErreur($lErreur);			
				}
			}
						
			if(is_array($pData['produits'])) {
					$lValidProduit = new ProduitCommandeValid();
					$i = 0;
					while(isset($pData['produits'][$i])) {
						$lVrProduit = $lValidProduit->validAjout($pData['produits'][$i]);	
						if(!$lVrProduit->getValid()){$lVr->setValid(false);}
						$lVr->addProduits($lVrProduit);
						$i++;
					}		
			}

			// Test si la commande existe
			$lCommande = CommandeManager::select($pData['id']);
			if($lCommande->getId() != $pData['id']) {
				$lVr->setValid(false);
				$lVr->getId()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_216_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_216_MSG);
				$lVr->getId()->addErreur($lErreur);
			}
					
			return $lVr;
		}
		return $lTestId;
	}

	/**
	* @name validUpdateInformation($pData)
	* @return ModifierMarcheVR
	* @desc Test la validite de l'élément
	*/
	public static function validUpdateInformation($pData) {
		$lVr = new ModifierMarcheVR();
		//Tests inputs
		if(!isset($pData['id'])) {
			$lVr->setValid(false);
			$lVr->getId()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getId()->addErreur($lErreur);	
		}
		if(!isset($pData['dateMarcheDebut'])) {
			$lVr->setValid(false);
			$lVr->getDateMarcheDebut()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getDateMarcheDebut()->addErreur($lErreur);	
		}
		if(!isset($pData['timeMarcheDebut'])) {
			$lVr->setValid(false);
			$lVr->getTimeMarcheDebut()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getTimeMarcheDebut()->addErreur($lErreur);	
		}
		if(!isset($pData['dateMarcheFin'])) {
			$lVr->setValid(false);
			$lVr->getDateMarcheFin()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getDateMarcheFin()->addErreur($lErreur);	
		}
		if(!isset($pData['timeMarcheFin'])) {
			$lVr->setValid(false);
			$lVr->getTimeMarcheFin()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getTimeMarcheFin()->addErreur($lErreur);	
		}
		if(!isset($pData['dateFinReservation'])) {
			$lVr->setValid(false);
			$lVr->getDateFinReservation()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getDateFinReservation()->addErreur($lErreur);	
		}
		if(!isset($pData['timeFinReservation'])) {
			$lVr->setValid(false);
			$lVr->getTimeFinReservation()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getTimeFinReservation()->addErreur($lErreur);	
		}
		if(!isset($pData['droitNonAdherent']) && intval($pData['droitNonAdherent']) != 0) {
			$lVr->setValid(false);
			$lVr->getDroitNonAdherent()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
			$lVr->getDroitNonAdherent()->addErreur($lErreur);	
		}
		
		if($lVr->getValid()) {
			//Tests Techniques
			if(!TestFonction::checkLength($pData['id'],0,11)) {
				$lVr->setValid(false);
				$lVr->getId()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getId()->addErreur($lErreur);	
			}
			if(!is_int((int)$pData['id'])) {
				$lVr->setValid(false);
				$lVr->getId()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_108_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_108_MSG);
				$lVr->getId()->addErreur($lErreur);	
			}
			if(!TestFonction::checkLength($pData['nom'],0,100)) {
				$lVr->setValid(false);
				$lVr->getNom()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getNom()->addErreur($lErreur);	
			}
			if(!TestFonction::checkDate($pData['dateMarcheDebut'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateMarcheDebut()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_103_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_103_MSG);
				$lVr->getDateMarcheDebut()->addErreur($lErreur);	
			}
			if(!TestFonction::checkDateExist($pData['dateMarcheDebut'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateMarcheDebut()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_105_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_105_MSG);
				$lVr->getDateMarcheDebut()->addErreur($lErreur);	
			}
			if(!TestFonction::checkTime($pData['timeMarcheDebut'])) {
				$lVr->setValid(false);
				$lVr->getTimeMarcheDebut()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_106_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_106_MSG);
				$lVr->getTimeMarcheDebut()->addErreur($lErreur);	
			}
			if(!TestFonction::checkTimeExist($pData['timeMarcheDebut'])) {
				$lVr->setValid(false);
				$lVr->getTimeMarcheDebut()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_107_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_107_MSG);
				$lVr->getTimeMarcheDebut()->addErreur($lErreur);	
			}
			if(!TestFonction::checkDate($pData['dateMarcheFin'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateMarcheFin()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_103_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_103_MSG);
				$lVr->getDateMarcheFin()->addErreur($lErreur);	
			}
			if(!TestFonction::checkDateExist($pData['dateMarcheFin'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateMarcheFin()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_105_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_105_MSG);
				$lVr->getDateMarcheFin()->addErreur($lErreur);	
			}
			if(!TestFonction::checkTime($pData['timeMarcheFin'])) {
				$lVr->setValid(false);
				$lVr->getTimeMarcheFin()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_106_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_106_MSG);
				$lVr->getTimeMarcheFin()->addErreur($lErreur);	
			}
			if(!TestFonction::checkTimeExist($pData['timeMarcheFin'])) {
				$lVr->setValid(false);
				$lVr->getTimeMarcheFin()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_107_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_107_MSG);
				$lVr->getTimeMarcheFin()->addErreur($lErreur);	
			}
			if(!TestFonction::checkDate($pData['dateFinReservation'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateFinReservation()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_103_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_103_MSG);
				$lVr->getDateFinReservation()->addErreur($lErreur);	
			}
			if(!TestFonction::checkDateExist($pData['dateFinReservation'],'db')) {
				$lVr->setValid(false);
				$lVr->getDateFinReservation()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_105_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_105_MSG);
				$lVr->getDateFinReservation()->addErreur($lErreur);	
			}
			if(!TestFonction::checkTime($pData['timeFinReservation'])) {
				$lVr->setValid(false);
				$lVr->getTimeFinReservation()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_106_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_106_MSG);
				$lVr->getTimeFinReservation()->addErreur($lErreur);	
			}
			if(!TestFonction::checkTimeExist($pData['timeFinReservation'])) {
				$lVr->setValid(false);
				$lVr->getTimeFinReservation()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_107_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_107_MSG);
				$lVr->getTimeFinReservation()->addErreur($lErreur);	
			}
			if(!TestFonction::checkLength($pData['droitNonAdherent'],0,1)) {
				$lVr->setValid(false);
				$lVr->getDroitNonAdherent()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_101_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_101_MSG);
				$lVr->getDroitNonAdherent()->addErreur($lErreur);
			}

			//Tests Fonctionnels
			if(empty($pData['id'])) {
				$lVr->setValid(false);
				$lVr->getId()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getId()->addErreur($lErreur);	
			}
			if(empty($pData['dateMarcheDebut'])) {
				$lVr->setValid(false);
				$lVr->getDateMarcheDebut()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getDateMarcheDebut()->addErreur($lErreur);	
			}
			if(empty($pData['timeMarcheDebut'])) {
				$lVr->setValid(false);
				$lVr->getTimeMarcheDebut()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getTimeMarcheDebut()->addErreur($lErreur);	
			}
			if(empty($pData['dateMarcheFin'])) {
				$lVr->setValid(false);
				$lVr->getDateMarcheFin()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getDateMarcheFin()->addErreur($lErreur);	
			}
			if(empty($pData['timeMarcheFin'])) {
				$lVr->setValid(false);
				$lVr->getTimeMarcheFin()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getTimeMarcheFin()->addErreur($lErreur);	
			}
			if(empty($pData['dateFinReservation'])) {
				$lVr->setValid(false);
				$lVr->getDateFinReservation()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getDateFinReservation()->addErreur($lErreur);	
			}
			if(empty($pData['timeFinReservation'])) {
				$lVr->setValid(false);
				$lVr->getTimeFinReservation()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getTimeFinReservation()->addErreur($lErreur);	
			}
			if(empty($pData['droitNonAdherent']) && intval($pData['droitNonAdherent']) != 0) {
				$lVr->setValid(false);
				$lVr->getDroitNonAdherent()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_201_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_201_MSG);
				$lVr->getDroitNonAdherent()->addErreur($lErreur);
			}
			
			if(!TestFonction::dateTimeEstPLusGrandeEgale($pData['dateMarcheDebut'] . " " . $pData['timeMarcheDebut'],$pData['dateFinReservation'] . " " . $pData['timeFinReservation'],"db")) {
				if(!TestFonction::dateEstPLusGrandeEgale($pData['dateMarcheDebut'],$pData['dateFinReservation'],"db")) {			
					$lVr->setValid(false);
					$lVr->getDateMarcheDebut()->setValid(false);
					$lVr->getDateFinReservation()->setValid(false);
					$lErreur = new VRerreur();
					$lErreur->setCode(MessagesErreurs::ERR_202_CODE);
					$lErreur->setMessage(MessagesErreurs::ERR_202_MSG);
					$lVr->getDateMarcheDebut()->addErreur($lErreur);		
					$lVr->getDateFinReservation()->addErreur($lErreur);				
				} else if(TestFonction::timeEstPLusGrandeEgale($pData['timeFinReservation'],$pData['timeMarcheDebut'])) {
					$lVr->setValid(false);
					$lVr->getTimeMarcheDebut()->setValid(false);
					$lVr->getTimeFinReservation()->setValid(false);
					$lErreur = new VRerreur();
					$lErreur->setCode(MessagesErreurs::ERR_203_CODE);
					$lErreur->setMessage(MessagesErreurs::ERR_203_MSG);
					$lVr->getTimeMarcheDebut()->addErreur($lErreur);		
					$lVr->getTimeFinReservation()->addErreur($lErreur);			
				}
			}
			
			if(TestFonction::dateTimeEstPLusGrandeEgale($pData['dateMarcheDebut'] . " " . $pData['timeMarcheDebut'],$pData['dateMarcheFin'] . " " . $pData['timeMarcheFin'],"db")) {
				if(TestFonction::timeEstPLusGrandeEgale($pData['timeMarcheDebut'],$pData['timeMarcheFin'])) {			
					$lVr->setValid(false);
					$lVr->getTimeMarcheDebut()->setValid(false);
					$lVr->getTimeMarcheFin()->setValid(false);
					$lErreur = new VRerreur();
					$lErreur->setCode(MessagesErreurs::ERR_204_CODE);
					$lErreur->setMessage(MessagesErreurs::ERR_204_MSG);
					$lVr->getTimeMarcheDebut()->addErreur($lErreur);		
					$lVr->getTimeMarcheFin()->addErreur($lErreur);	
				} else if(TestFonction::dateEstPLusGrandeEgale($pData['dateMarcheDebut'],$pData['dateMarcheFin'],"db")) {
					$lVr->setValid(false);
					$lVr->getDateMarcheDebut()->setValid(false);
					$lVr->getDateMarcheFin()->setValid(false);
					$lErreur = new VRerreur();
					$lErreur->setCode(MessagesErreurs::ERR_208_CODE);
					$lErreur->setMessage(MessagesErreurs::ERR_208_MSG);
					$lVr->getDateMarcheDebut()->addErreur($lErreur);		
					$lVr->getDateMarcheFin()->addErreur($lErreur);			
				}
			}
			
			// Test si la commande existe
			$lCommande = CommandeManager::select($pData['id']);
			if($lCommande->getId() != $pData['id']) {
				$lVr->setValid(false);
				$lVr->getId()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_216_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_216_MSG);
				$lVr->getId()->addErreur($lErreur);
			}
					
			return $lVr;
		}
		return $lTestId;
	}
}
?>