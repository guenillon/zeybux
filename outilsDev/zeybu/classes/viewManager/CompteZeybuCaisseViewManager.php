<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 24/12/2010
// Fichier : CompteZeybuCaisseViewManager.php
//
// Description : Classe de gestion des CompteZeybuCaisse
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_UTILS . "DbUtils.php");
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php");
include_once(CHEMIN_CLASSES_VIEW_VO . "CompteZeybuCaisseViewVO.php");
include_once(CHEMIN_CLASSES_MANAGERS . "OperationManager.php");

/**
 * @name CompteZeybuCaisseViewManager
 * @author Julien PIERRE
 * @since 24/12/2010
 * 
 * @desc Classe permettant l'accès aux données des CompteZeybuCaisse
 */
class CompteZeybuCaisseViewManager
{
	const VUE_COMPTEZEYBUCAISSE = "view_compte_zeybu_caisse";

	/**
	* @name select($pId)
	* @param integer
	* @return CompteZeybuCaisseViewVO
	* @desc Récupère la ligne correspondant à l'id en paramètre, créé une CompteZeybuCaisseViewVO contenant les informations et la renvoie
	*/
	public static function select($pId) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"SELECT "
			    . OperationManager::CHAMP_OPERATION_MONTANT . "
			FROM " . CompteZeybuCaisseViewManager::VUE_COMPTEZEYBUCAISSE . " 
			WHERE " . OperationManager::CHAMP_OPERATION_MONTANT . " = '" . StringUtils::securiser($pId) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		$lListeCompteZeybuCaisse = array();
		if( mysql_num_rows($lSql) > 0 ) {
			while ($lLigne = mysql_fetch_assoc($lSql)) {
				array_push($lListeCompteZeybuCaisse,
					CompteZeybuCaisseViewManager::remplir(
					$lLigne[OperationManager::CHAMP_OPERATION_MONTANT]));
			}
		} else {
			$lListeCompteZeybuCaisse[0] = new CompteZeybuCaisseViewVO();
		}
		return $lListeCompteZeybuCaisse;
	}

	/**
	* @name selectAll()
	* @return array(CompteZeybuCaisseViewVO)
	* @desc Récupères toutes les lignes de la table et les renvoie sous forme d'une collection de CompteZeybuCaisseViewVO
	*/
	public static function selectAll() {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
			"SELECT "
			    . OperationManager::CHAMP_OPERATION_MONTANT . "
			FROM " . CompteZeybuCaisseViewManager::VUE_COMPTEZEYBUCAISSE;

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		$lListeCompteZeybuCaisse = array();
		if( mysql_num_rows($lSql) > 0 ) {
			while ($lLigne = mysql_fetch_assoc($lSql)) {
				array_push($lListeCompteZeybuCaisse,
					CompteZeybuCaisseViewManager::remplir(
					$lLigne[OperationManager::CHAMP_OPERATION_MONTANT]));
			}
		} else {
			$lListeCompteZeybuCaisse[0] = new CompteZeybuCaisseViewVO();
		}
		return $lListeCompteZeybuCaisse;
	}

	/**
	* @name recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri )
	* @param string nom de la table
	* @param string Le type de critère de recherche
	* @param array(string) champs à récupérer dans la table
	* @param array(array(string, object)) Dictionnaire(champ, valeur)) contenant les champs à filtrer ainsi que la valeur du filtre
	* @param array(array(string, string)) Dictionnaire(champ, sens) contenant les tris à appliquer
	* @return array(CompteZeybuCaisseViewVO)
	* @desc Récupères les lignes de la table selon le critère de recherche puis trie et renvoie la liste de résultat sous forme d'une collection de CompteZeybuCaisseViewVO
	*/
	public static function recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri ) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		// Préparation de la requète
		$lChamps = array( 
			    OperationManager::CHAMP_OPERATION_MONTANT		);

		if(is_array($pTypeRecherche) && is_array($pCritereRecherche)) {
			$lFiltres = array();
			$i = 0;
			foreach($pTypeRecherche as $lTypeRecherche) {
				$lLigne = array();
				$lLigne['champ'] = StringUtils::securiser($lTypeRecherche);
				$lLigne['valeur'] = StringUtils::securiser($pCritereRecherche[$i]);
				array_push($lFiltres,$lLigne);
				$i++;
			}
		} else {
			$lFiltres = array(array( 'champ' => StringUtils::securiser($pTypeRecherche), 'valeur' => StringUtils::securiser($pCritereRecherche) ));
		}

		if(is_array($pTypeCritere)) {
			$lTypeFiltre = $pTypeCritere;
		} else {
			$lTypeFiltre = array($pTypeCritere);
		}

		// Protection du critère de tri
		if($pCritereTri != 'ASC' && $pCritereTri != 'DESC') {
			$pCritereTri = 'ASC';
		}

		// Protection du type de tri
		if($pTypeTri == '') {
			$pTypeTri = OperationManager::CHAMP_OPERATION_MONTANT;
		}

		$lTris = array( array('champ' => StringUtils::securiser($pTypeTri), 'sens'=> StringUtils::securiser($pCritereTri)) );

		// Préparation de la requète de recherche
		$lRequete = DbUtils::prepareRequeteRecherche(CompteZeybuCaisseViewManager::VUE_COMPTEZEYBUCAISSE, $lChamps, $lFiltres, $lTypeFiltre, $lTris);

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		$lListeCompteZeybuCaisse = array();

		if( mysql_num_rows($lSql) > 0 ) {

			while ( $lLigne = mysql_fetch_assoc($lSql) ) {

				array_push($lListeCompteZeybuCaisse,
					CompteZeybuCaisseViewManager::remplir(
					$lLigne[OperationManager::CHAMP_OPERATION_MONTANT]));
			}
		} else {
			$lListeCompteZeybuCaisse[0] = new CompteZeybuCaisseViewVO();
		}

		return $lListeCompteZeybuCaisse;
	}

	/**
	* @name remplirCompteZeybuCaisse($pOpeMontant)
	* @param decimal(33,2) 
	* @return CompteZeybuCaisseViewVO
	* @desc Retourne une CompteZeybuCaisseViewVO remplie
	*/
	private static function remplir($pOpeMontant) {
		$lCompteZeybuCaisse = new CompteZeybuCaisseViewVO();
		$lCompteZeybuCaisse->setOpeMontant($pOpeMontant);
		return $lCompteZeybuCaisse;
	}
}
?>