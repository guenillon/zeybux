<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 10/08/2011
// Fichier : CompteZeybuListeVirementViewManager.php
//
// Description : Classe de gestion des CompteZeybuListeVirement
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_UTILS . "DbUtils.php");
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php");
include_once(CHEMIN_CLASSES_VIEW_VO . "CompteZeybuListeVirementViewVO.php");
include_once(CHEMIN_CLASSES_MANAGERS . "OperationManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "CompteManager.php");

/**
 * @name CompteZeybuListeVirementViewManager
 * @author Julien PIERRE
 * @since 10/08/2011
 * 
 * @desc Classe permettant l'accès aux données des CompteZeybuListeVirement
 */
class CompteZeybuListeVirementViewManager
{
	const VUE_COMPTEZEYBULISTEVIREMENT = "view_compte_zeybu_liste_virement";

	/**
	* @name select($pId)
	* @param integer
	* @return CompteZeybuListeVirementViewVO
	* @desc Récupère la ligne correspondant à l'id en paramètre, créé une CompteZeybuListeVirementViewVO contenant les informations et la renvoie
	*/
	public static function select($pId) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"SELECT "
			    . OperationManager::CHAMP_OPERATION_ID . 
			"," . OperationManager::CHAMP_OPERATION_DATE . 
			"," . CompteManager::CHAMP_COMPTE_LABEL . 
			"," . OperationManager::CHAMP_OPERATION_MONTANT . 
			"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . "
			FROM " . CompteZeybuListeVirementViewManager::VUE_COMPTEZEYBULISTEVIREMENT . " 
			WHERE " . OperationManager::CHAMP_OPERATION_ID . " = '" . StringUtils::securiser($pId) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		$lListeCompteZeybuListeVirement = array();
		if( mysql_num_rows($lSql) > 0 ) {
			while ($lLigne = mysql_fetch_assoc($lSql)) {
				array_push($lListeCompteZeybuListeVirement,
					CompteZeybuListeVirementViewManager::remplir(
					$lLigne[OperationManager::CHAMP_OPERATION_ID],
					$lLigne[OperationManager::CHAMP_OPERATION_DATE],
					$lLigne[CompteManager::CHAMP_COMPTE_LABEL],
					$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
					$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT]));
			}
		} else {
			$lListeCompteZeybuListeVirement[0] = new CompteZeybuListeVirementViewVO();
		}
		return $lListeCompteZeybuListeVirement;
	}

	/**
	* @name selectAll()
	* @return array(CompteZeybuListeVirementViewVO)
	* @desc Récupères toutes les lignes de la table et les renvoie sous forme d'une collection de CompteZeybuListeVirementViewVO
	*/
	public static function selectAll() {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
			"SELECT "
			    . OperationManager::CHAMP_OPERATION_ID . 
			"," . OperationManager::CHAMP_OPERATION_DATE . 
			"," . CompteManager::CHAMP_COMPTE_LABEL . 
			"," . OperationManager::CHAMP_OPERATION_MONTANT . 
			"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . "
			FROM " . CompteZeybuListeVirementViewManager::VUE_COMPTEZEYBULISTEVIREMENT;

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		$lListeCompteZeybuListeVirement = array();
		if( mysql_num_rows($lSql) > 0 ) {
			while ($lLigne = mysql_fetch_assoc($lSql)) {
				array_push($lListeCompteZeybuListeVirement,
					CompteZeybuListeVirementViewManager::remplir(
					$lLigne[OperationManager::CHAMP_OPERATION_ID],
					$lLigne[OperationManager::CHAMP_OPERATION_DATE],
					$lLigne[CompteManager::CHAMP_COMPTE_LABEL],
					$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
					$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT]));
			}
		} else {
			$lListeCompteZeybuListeVirement[0] = new CompteZeybuListeVirementViewVO();
		}
		return $lListeCompteZeybuListeVirement;
	}

	/**
	* @name recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri )
	* @param string nom de la table
	* @param string Le type de critère de recherche
	* @param array(string) champs à récupérer dans la table
	* @param array(array(string, object)) Dictionnaire(champ, valeur)) contenant les champs à filtrer ainsi que la valeur du filtre
	* @param array(array(string, string)) Dictionnaire(champ, sens) contenant les tris à appliquer
	* @return array(CompteZeybuListeVirementViewVO)
	* @desc Récupères les lignes de la table selon le critère de recherche puis trie et renvoie la liste de résultat sous forme d'une collection de CompteZeybuListeVirementViewVO
	*/
	public static function recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri ) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		// Préparation de la requète
		$lChamps = array( 
			    OperationManager::CHAMP_OPERATION_ID .
			"," . OperationManager::CHAMP_OPERATION_DATE .
			"," . CompteManager::CHAMP_COMPTE_LABEL .
			"," . OperationManager::CHAMP_OPERATION_MONTANT .
			"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT		);

		// Préparation de la requète de recherche
		$lRequete = DbUtils::prepareRequeteRecherche(CompteZeybuListeVirementViewManager::VUE_COMPTEZEYBULISTEVIREMENT, $lChamps, $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri);

		$lListeCompteZeybuListeVirement = array();

		if($lRequete !== false) {

			$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
			$lSql = Dbutils::executerRequete($lRequete);

			if( mysql_num_rows($lSql) > 0 ) {

				while ( $lLigne = mysql_fetch_assoc($lSql) ) {

					array_push($lListeCompteZeybuListeVirement,
						CompteZeybuListeVirementViewManager::remplir(
						$lLigne[OperationManager::CHAMP_OPERATION_ID],
						$lLigne[OperationManager::CHAMP_OPERATION_DATE],
						$lLigne[CompteManager::CHAMP_COMPTE_LABEL],
						$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
						$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT]));
				}
			} else {
				$lListeCompteZeybuListeVirement[0] = new CompteZeybuListeVirementViewVO();
			}

			return $lListeCompteZeybuListeVirement;
		}

		$lListeCompteZeybuListeVirement[0] = new CompteZeybuListeVirementViewVO();
		return $lListeCompteZeybuListeVirement;
	}

	/**
	* @name remplir($pOpeId, $pOpeDate, $pCptLabel, $pOpeMontant, $pOpeTypePaiement)
	* @param int(11)
	* @param datetime
	* @param varchar(30)
	* @param decimal(10,2)
	* @param int(11)
	* @return CompteZeybuListeVirementViewVO
	* @desc Retourne une CompteZeybuListeVirementViewVO remplie
	*/
	private static function remplir($pOpeId, $pOpeDate, $pCptLabel, $pOpeMontant, $pOpeTypePaiement) {
		$lCompteZeybuListeVirement = new CompteZeybuListeVirementViewVO();
		$lCompteZeybuListeVirement->setOpeId($pOpeId);
		$lCompteZeybuListeVirement->setOpeDate($pOpeDate);
		$lCompteZeybuListeVirement->setCptLabel($pCptLabel);
		$lCompteZeybuListeVirement->setOpeMontant($pOpeMontant);
		$lCompteZeybuListeVirement->setOpeTypePaiement($pOpeTypePaiement);
		return $lCompteZeybuListeVirement;
	}
}
?>
