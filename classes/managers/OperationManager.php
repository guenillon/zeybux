<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 24/05/2010
// Fichier : OperationManager.php
//
// Description : Classe de gestion des Operation
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_UTILS . "DbUtils.php");
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php");
include_once(CHEMIN_CLASSES_VO . "OperationVO.php");
include_once(CHEMIN_CLASSES_VO . "OperationDetailVO.php");
include_once(CHEMIN_CLASSES_VO . "OperationAvenirVO.php");
include_once(CHEMIN_CLASSES_VO . "CompteListeVirementVO.php");
include_once(CHEMIN_CLASSES_VO . "OperationAttenteAdherentVO.php");
include_once(CHEMIN_CLASSES_VO . "OperationAttenteFermeVO.php");
include_once(CHEMIN_CLASSES_VO . "ListeFactureVO.php");

include_once(CHEMIN_CLASSES_VO . "ChampComplementaireDetailOperationVO.php");
include_once(CHEMIN_CLASSES_MANAGERS . "TypePaiementManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "HistoriqueOperationManager.php");

include_once(CHEMIN_CLASSES_MANAGERS . "TypePaiementChampComplementaireManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "ChampComplementaireManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "OperationChampComplementaireManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "CommandeManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "CompteManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "AdherentManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "FermeManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "OperationRemiseChequeManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "RemiseChequeManager.php");

define("TABLE_OPERATION", MYSQL_DB_PREFIXE . "ope_operation");
/**
 * @name OperationManager
 * @author Julien PIERRE
 * @since 24/05/2010
 * 
 * @desc Classe permettant l'accès aux données des Operation
 */
class OperationManager
{
	const TABLE_OPERATION = TABLE_OPERATION;
	const CHAMP_OPERATION_ID = "ope_id";
	const CHAMP_OPERATION_ID_COMPTE = "ope_id_compte";
	const CHAMP_OPERATION_MONTANT = "ope_montant";
	const CHAMP_OPERATION_LIBELLE = "ope_libelle";
	const CHAMP_OPERATION_DATE = "ope_date";
	const CHAMP_OPERATION_TYPE_PAIEMENT = "ope_type_paiement";
	const CHAMP_OPERATION_TYPE = "ope_type";
	const CHAMP_OPERATION_DATE_MAJ = "ope_date_maj";
	const CHAMP_OPERATION_ID_LOGIN = "ope_id_login";

	/**
	* @name select($pId)
	* @param integer
	* @return OperationVO
	* @desc Récupère la ligne correspondant à l'id en paramètre, créé une OperationVO contenant les informations et la renvoie
	*/
	public static function select($pId) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"SELECT "
			    . OperationManager::CHAMP_OPERATION_ID . 
			"," . OperationManager::CHAMP_OPERATION_ID_COMPTE . 
			"," . OperationManager::CHAMP_OPERATION_MONTANT . 
			"," . OperationManager::CHAMP_OPERATION_LIBELLE . 
			"," . OperationManager::CHAMP_OPERATION_DATE . 
			"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . 
			"," . OperationManager::CHAMP_OPERATION_TYPE . 
			"," . OperationManager::CHAMP_OPERATION_DATE_MAJ . 
			"," . OperationManager::CHAMP_OPERATION_ID_LOGIN . "
			FROM " . OperationManager::TABLE_OPERATION . " 
			WHERE " . OperationManager::CHAMP_OPERATION_ID . " = '" . StringUtils::securiser($pId) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		if( mysqli_num_rows($lSql) > 0 ) {
			$lLigne = mysqli_fetch_assoc($lSql);
			return OperationManager::remplirOperation(
				$pId,
				$lLigne[OperationManager::CHAMP_OPERATION_ID_COMPTE],
				$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
				$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
				$lLigne[OperationManager::CHAMP_OPERATION_DATE],
				$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT],
				$lLigne[OperationManager::CHAMP_OPERATION_TYPE],
				$lLigne[OperationManager::CHAMP_OPERATION_DATE_MAJ],
				$lLigne[OperationManager::CHAMP_OPERATION_ID_LOGIN]);
		} else {
			return new OperationVO();
		}
	}
	
	/**
	 * @name selectDetail($pId)
	 * @param integer
	 * @return OperationDetailVO
	 * @desc Récupère la ligne correspondant à l'id en paramètre, créé une OperationVO contenant les informations et la renvoie
	 */
	public static function selectDetail($pId = NULL) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
	
		$lRequete =
		"SELECT "
			. OperationManager::CHAMP_OPERATION_ID .
		"," . OperationManager::CHAMP_OPERATION_ID_COMPTE .
		"," . OperationManager::CHAMP_OPERATION_MONTANT .
		"," . OperationManager::CHAMP_OPERATION_LIBELLE .
		"," . OperationManager::CHAMP_OPERATION_DATE .
		"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT .
		"," . OperationManager::CHAMP_OPERATION_TYPE .
		"," . OperationManager::CHAMP_OPERATION_DATE_MAJ .
		"," . OperationManager::CHAMP_OPERATION_ID_LOGIN . 
		","	. TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_TPP_ID . 
		"," . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_CHCP_ID . 
		"," . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ORDRE . 
		"," . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_VISIBLE . 
		"," . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ETAT . 
		"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID .
		"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE .
		"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_CHAMP_COMPLEMENTAIRE .
		"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_VISIBLE . 
		"," . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID . 
		"," . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_LABEL . 
		"," . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_OBLIGATOIRE . 
		"," . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ETAT . 		
		"," . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . 
		"," . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . 
		"," . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "		
		FROM " . OperationManager::TABLE_OPERATION . "
		JOIN " . TypePaiementManager::TABLE_TYPEPAIEMENT . " ON " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID . " = " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . "
		LEFT JOIN " . TypePaiementChampComplementaireManager::TABLE_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE . " ON " . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_TPP_ID . " = " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . "
		LEFT JOIN " . ChampComplementaireManager::TABLE_CHAMPCOMPLEMENTAIRE . " ON " . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID . " = " . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_CHCP_ID . "
		LEFT JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " ON " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . " AND " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = " . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID . "
		
		WHERE (" . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ETAT . " = 0
				OR " . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ETAT . " IS NULL )
		AND (" . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ETAT . " = 0 
				OR " . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ETAT . " IS NULL ) ";
		if( !is_null($pId) ) {		
			$lRequete .= " AND " . OperationManager::CHAMP_OPERATION_ID . " = '" . StringUtils::securiser($pId) . "' ";
		}
		$lRequete .= " ORDER BY " . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ORDRE . " ASC;";
		
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
		
		$lOperationDetailVO = new OperationDetailVO();
		if( mysqli_num_rows($lSql) > 0 ) {
			
			$lLigne = mysqli_fetch_assoc($lSql);
			$lOperationDetailVO = OperationManager::remplirOperationDetailEntete(
				$lLigne[OperationManager::CHAMP_OPERATION_ID],
				$lLigne[OperationManager::CHAMP_OPERATION_ID_COMPTE],
				$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
				$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
				$lLigne[OperationManager::CHAMP_OPERATION_DATE],
				$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT],
				$lLigne[OperationManager::CHAMP_OPERATION_TYPE],
				$lLigne[OperationManager::CHAMP_OPERATION_DATE_MAJ],
				$lLigne[OperationManager::CHAMP_OPERATION_ID_LOGIN],
				$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_ID],
				$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE],
				$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_CHAMP_COMPLEMENTAIRE],
				$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_VISIBLE]);
			
			// Si il y a un champ complementaire
			if(!is_null($lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID])) {
				$lChampComplementaire = array();
				$lChampComplementaire[$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID]] = OperationManager::remplirOperationDetail(
						$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_TPP_ID],
						$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_CHCP_ID],
						$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ORDRE],
						$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_VISIBLE],
						$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ETAT],
						$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID],
						$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_LABEL],
						$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_OBLIGATOIRE],
						$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ETAT],
						$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID],
						$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR]);
				
				while ($lLigne = mysqli_fetch_assoc($lSql)) {
					$lChampComplementaire[$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID]] = OperationManager::remplirOperationDetail(
						$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_TPP_ID],
						$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_CHCP_ID],
						$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ORDRE],
						$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_VISIBLE],
						$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ETAT],
						$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID],
						$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_LABEL],
						$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_OBLIGATOIRE],
						$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ETAT],
						$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID],
						$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR]);
				}
				
				$lOperationDetailVO->setChampComplementaire($lChampComplementaire);
			}
		}
		return $lOperationDetailVO;
	}
	
	/**
	 * @name remplirOperationDetailEntete($pId, $pIdCompte, $pMontant, $pLibelle, $pDate, $pTypePaiement, $pType, $pDateMaj, $pIdLogin, $pTppId, $pTppType, $pTppChampComplementaire, $pTppVisible)
	 * @param int(11)
	 * @param int(11)
	 * @param decimal(10,2)
	 * @param varchar(100)
	 * @param datetime
	 * @param int(11)
	 * @param int(11)
	 * @param datetime
	 * @param int(11)
	 * @param int(11)
	 * @param varchar(100)
	 * @param tinyint(4)
	 * @param tinyint(1)
	 * @return OperationVO
	 * @desc Retourne une OperationDetailVO remplie
	 */
	private static function remplirOperationDetailEntete($pId, $pIdCompte, $pMontant, $pLibelle, $pDate, $pTypePaiement, $pType, $pDateMaj, $pIdLogin, $pTppId, $pTppType, $pTppChampComplementaire, $pTppVisible) {
		$lOperation = new OperationDetailVO();
		$lOperation->setId($pId);
		$lOperation->setIdCompte($pIdCompte);
		$lOperation->setMontant($pMontant);
		$lOperation->setLibelle($pLibelle);
		$lOperation->setDate($pDate);
		$lOperation->setTypePaiement($pTypePaiement);
		$lOperation->setType($pType);
		$lOperation->setDateMaj($pDateMaj);
		$lOperation->setIdLogin($pIdLogin);
		$lOperation->setTppId($pTppId);
		$lOperation->setTppType($pTppType);
		$lOperation->setTppChampComplementaire($pTppChampComplementaire);
		$lOperation->setTppVisible($pTppVisible);
		return $lOperation;
	}
	
	/**
	 * @name remplirOperationDetail($pTppCpTppId, $pTppCpChcpId, $pTppCpOrdre, $pTppCpVisible, $pTppCpEtat, $pChCpId, $pChCpLabel, $pChCpObligatoire, $pChCpEtat, $pOpeCpOpeId, $pOpeCpValeur)
	 * @param int(11)
	 * @param int(11)
	 * @param int(11)
	 * @param tinyint(1)
	 * @param tinyint(1)
	 * @param int(11)
	 * @param varchar(30)
	 * @param tinyint(1)
	 * @param tinyint(1)
	 * @param int(11)
	 * @param varchar(50)
	 * @return ChampComplementaireDetailOperationVO
	 * @desc Retourne une ChampComplementaireDetailOperationVO remplie
	 */
	private static function remplirOperationDetail($pTppCpTppId, $pTppCpChcpId, $pTppCpOrdre, $pTppCpVisible, $pTppCpEtat, $pChCpId, $pChCpLabel, $pChCpObligatoire, $pChCpEtat, $pOpeCpOpeId, $pOpeCpValeur) {
		$lChampComplementaireDetailOperation = new ChampComplementaireDetailOperationVO();
		$lChampComplementaireDetailOperation->setTppCpTppId($pTppCpTppId);
		$lChampComplementaireDetailOperation->setTppCpChcpId($pTppCpChcpId);
		$lChampComplementaireDetailOperation->setTppCpOrdre($pTppCpOrdre);
		$lChampComplementaireDetailOperation->setTppCpVisible($pTppCpVisible);
		$lChampComplementaireDetailOperation->setTppCpEtat($pTppCpEtat);
		$lChampComplementaireDetailOperation->setChCpId($pChCpId);
		$lChampComplementaireDetailOperation->setChCpLabel($pChCpLabel);
		$lChampComplementaireDetailOperation->setChCpObligatoire($pChCpObligatoire);
		$lChampComplementaireDetailOperation->setChCpEtat($pChCpEtat);
		$lChampComplementaireDetailOperation->setOpeId($pOpeCpOpeId);
		//$lChampComplementaireDetailOperation->setChcpId($pOpeCpChcpId);
		$lChampComplementaireDetailOperation->setValeur($pOpeCpValeur);
		return $lChampComplementaireDetailOperation;
	}
	
	/**
	* @name selectAll()
	* @return array(OperationVO)
	* @desc Récupères toutes les lignes de la table et les renvoie sous forme d'une collection de OperationVO
	*/
	public static function selectAll() {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
			"SELECT "
			    . OperationManager::CHAMP_OPERATION_ID . 
			"," . OperationManager::CHAMP_OPERATION_ID_COMPTE . 
			"," . OperationManager::CHAMP_OPERATION_MONTANT . 
			"," . OperationManager::CHAMP_OPERATION_LIBELLE . 
			"," . OperationManager::CHAMP_OPERATION_DATE . 
			"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . 
			"," . OperationManager::CHAMP_OPERATION_TYPE . 
			"," . OperationManager::CHAMP_OPERATION_DATE_MAJ . 
			"," . OperationManager::CHAMP_OPERATION_ID_LOGIN . "
			FROM " . OperationManager::TABLE_OPERATION;

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		$lListeOperation = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeOperation,
					OperationManager::remplirOperation(
					$lLigne[OperationManager::CHAMP_OPERATION_ID],
					$lLigne[OperationManager::CHAMP_OPERATION_ID_COMPTE],
					$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
					$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
					$lLigne[OperationManager::CHAMP_OPERATION_DATE],
					$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT],
					$lLigne[OperationManager::CHAMP_OPERATION_TYPE],
					$lLigne[OperationManager::CHAMP_OPERATION_DATE_MAJ],
					$lLigne[OperationManager::CHAMP_OPERATION_ID_LOGIN]));
			}
		} else {
			$lListeOperation[0] = new OperationVO();
		}
		return $lListeOperation;
	}

	/**
	 * @name selectRechargementMarche($pIdCompte, $pIdMarche)
	 * @return OperationVO
	 * @desc Récupère l'operation de rechargement d'un compte sur un marche et retourne l'OperationVO correspondant
	 */
	/*public static function selectRechargementMarche($pIdCompte, $pIdMarche) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
		"SELECT "
				. OperationManager::CHAMP_OPERATION_ID .
				"," . OperationManager::CHAMP_OPERATION_ID_COMPTE .
				"," . OperationManager::CHAMP_OPERATION_MONTANT .
				"," . OperationManager::CHAMP_OPERATION_LIBELLE .
				"," . OperationManager::CHAMP_OPERATION_DATE .
				"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT .
				"," . OperationManager::CHAMP_OPERATION_TYPE .
				"," . OperationManager::CHAMP_OPERATION_DATE_MAJ .
				"," . OperationManager::CHAMP_OPERATION_ID_LOGIN . "
			FROM " . OperationManager::TABLE_OPERATION . "
			JOIN " . TypePaiementManager::TABLE_TYPEPAIEMENT . " ON " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID . " = " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT ."
			WHERE " . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = '" . StringUtils::securiser($pIdCompte) . "'
				AND " . OperationManager::CHAMP_OPERATION_ID_COMMANDE . " = '" . StringUtils::securiser($pIdMarche) . "'
				AND " . TypePaiementManager::CHAMP_TYPEPAIEMENT_VISIBLE . " = 1 ;";
	
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
	
		if( mysqli_num_rows($lSql) > 0 ) {
			$lLigne = mysqli_fetch_assoc($lSql);
			return OperationManager::remplirOperation(
				$lLigne[OperationManager::CHAMP_OPERATION_ID],
				$lLigne[OperationManager::CHAMP_OPERATION_ID_COMPTE],
				$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
				$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
				$lLigne[OperationManager::CHAMP_OPERATION_DATE],
				$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT],
				$lLigne[OperationManager::CHAMP_OPERATION_TYPE],
				$lLigne[OperationManager::CHAMP_OPERATION_DATE_MAJ],
				$lLigne[OperationManager::CHAMP_OPERATION_ID_LOGIN]);
		} else {
			return new OperationVO();
		}
	}*/
	
	/**
	 * @name selectOperationAvenir($pIdCompte)
	 * @param integer
	 * @return array(OperationVO)
	 * @desc Récupères toutes les lignes de la table ayant pour IdCompte $pId pour les opérations de réservation. Puis les renvoie sous forme d'une collection de OperationDetailVO
	 */
	public static function selectOperationAvenir($pIdCompte) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
		"SELECT " 
				. OperationManager::CHAMP_OPERATION_ID .
			","	. OperationManager::CHAMP_OPERATION_ID_COMPTE .
			"," . OperationManager::CHAMP_OPERATION_MONTANT .
			"," . OperationManager::CHAMP_OPERATION_LIBELLE .
			"," . OperationManager::CHAMP_OPERATION_DATE .
			"," . CommandeManager::CHAMP_COMMANDE_DATE_MARCHE_DEBUT .
		" FROM " . OperationManager::TABLE_OPERATION .
		
		" LEFT JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE 
			. " ON " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID 
			. " AND " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 1 " . 
		
		" LEFT JOIN " . CommandeManager::TABLE_COMMANDE . " ON " . CommandeManager::CHAMP_COMMANDE_ID . " = " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . 
		" WHERE " . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = '" . StringUtils::securiser($pIdCompte) . "'
			 AND " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = 0  
			 AND " . CommandeManager::CHAMP_COMMANDE_ARCHIVE . " = 0   
			 AND " . CommandeManager::CHAMP_COMMANDE_DATE_DEBUT_RESERVATION . " <= now() 
			 AND " . CommandeManager::CHAMP_COMMANDE_DATE_MARCHE_DEBUT . " >= now() 
		 ORDER BY " . CommandeManager::CHAMP_COMMANDE_DATE_MARCHE_DEBUT . ";";
	
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
		
		$lListeOperation = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeOperation,
					OperationManager::remplirOperationAvenir(
					$lLigne[OperationManager::CHAMP_OPERATION_ID],
					$lLigne[OperationManager::CHAMP_OPERATION_ID_COMPTE],
					$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
					$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
					$lLigne[OperationManager::CHAMP_OPERATION_DATE],
					$lLigne[CommandeManager::CHAMP_COMMANDE_DATE_MARCHE_DEBUT]));
			}
		} else {
			$lListeOperation[0] = new OperationAvenirVO();
		}
		return $lListeOperation;
	}
	
	/**
	 * @name remplirOperationAvenir($pOpeId, $pOpeIdCompte, $pOpeMontant, $pOpeLibelle, $pOpeDate, $pComDateMarche)
	 * @param int(11)
	 * @param int(11)
	 * @param decimal(10,2)
	 * @param varchar(100)
	 * @param datetime
	 * @param datetime
	 * @return OperationAvenirVO
	 * @desc Retourne une OperationAvenirViewVO remplie
	 */
	private static function remplirOperationAvenir($pOpeId, $pOpeIdCompte, $pOpeMontant, $pOpeLibelle, $pOpeDate, $pComDateMarche) {
		$lOperationAvenir = new OperationAvenirVO();
		$lOperationAvenir->setOpeId($pOpeId);
		$lOperationAvenir->setOpeIdCompte($pOpeIdCompte);
		$lOperationAvenir->setOpeMontant($pOpeMontant);
		$lOperationAvenir->setOpeLibelle($pOpeLibelle);
		$lOperationAvenir->setOpeDate($pOpeDate);
		$lOperationAvenir->setComDateMarche($pComDateMarche);
		return $lOperationAvenir;
	}
	
	/**
	* @name selectOperationPassee($pIdCompte)
	* @param integer
	* @return array(OperationVO)
	* @desc Récupères toutes les lignes de la table ayant pour IdCompte $pId pour les opérations passées. Puis les renvoie sous forme d'une collection de OperationDetailVO
	*/
	public static function selectOperationPassee($pIdCompte) {
		return OperationManager::rechercheDetail(
				array(OperationManager::CHAMP_OPERATION_ID_COMPTE,OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT),
				array('=','in'),
				array($pIdCompte, array(-1,1,2,3,4,7,8,9,10)),
				array(OperationManager::CHAMP_OPERATION_DATE),
				array('DESC'));
	}
	
	/**
	 * @name selectOperationReservation($pId, $pActive)
	 * @param IdReservation
	 * @param bool
	 * @return array(OperationVO)
	 * @desc Retourne une liste d'operation
	 */
	public function selectOperationReservation($pId, $pActive = false) {
		$lStatusReservation = array(0,15,16,22);
		if($pActive) { // Ne retourne que les réservations actives
			$lStatusReservation = array(0);
		}

		// ORDER BY date -> récupère la dernière operation en lien avec la commande
		return OperationManager::rechercheDetail(
				array(OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT,OperationManager::CHAMP_OPERATION_ID_COMPTE,OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID, OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR),
				array('in','=','=','='),
				array($lStatusReservation, $pId->getIdCompte(), 1, $pId->getIdCommande()),
				array(OperationManager::CHAMP_OPERATION_DATE, OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT),
				array('DESC','ASC'));
	}
	
	/**
	 * @name selectOperationAchat($pIdCompte, $pIdMarche)
	 * @param int(11) IdCompte
	 * @param int(11) IdMarche
	 * @return array(OperationVO)
	 * @desc Retourne une liste d'operation
	 */
	public function selectOperationAchat($pIdCompte, $pIdMarche) {
		// ORDER BY date -> récupère la dernière operation en lien avec la commande
		return OperationManager::rechercheDetail(
				array(OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT,OperationManager::CHAMP_OPERATION_ID_COMPTE,OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID, OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR),
				array('in','=','=','='),
				array(array(7,8), $pIdCompte, 1, $pIdMarche),
				array(OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT,OperationManager::CHAMP_OPERATION_DATE),
				array('ASC','DESC'));
	}
	
	/**
	 * @name selectOperationRechargementSurMarche($pIdCompte, $pIdMarche)
	 * @param int(11) IdCompte
	 * @param int(11) IdMarche
	 * @return array(OperationVO)
	 * @desc Retourne une liste d'operation
	 */
	public function selectOperationRechargementSurMarche($pIdCompte, $pIdMarche) {
		// ORDER BY date -> récupère la dernière operation en lien avec la commande
		return OperationManager::rechercheDetail(
				array(OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT,OperationManager::CHAMP_OPERATION_ID_COMPTE,OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID, OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR),
				array('in','=','=','='),
				array(array(1,2), $pIdCompte, 1, $pIdMarche),
				array(''),
				array(''));
	}
	
	/**
	 * @name rechercheDetail( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri )
	 * @param string nom de la table
	 * @param string Le type de critère de recherche
	 * @param array(string) champs à récupérer dans la table
	 * @param array(array(string, object)) Dictionnaire(champ, valeur)) contenant les champs à filtrer ainsi que la valeur du filtre
	 * @param array(array(string, string)) Dictionnaire(champ, sens) contenant les tris à appliquer
	 * @return array(OperationVO)
	 * @desc Récupères les lignes de la table selon le critère de recherche puis trie et renvoie la liste de résultat sous forme d'une collection de OperationVO
	 */
	public static function rechercheDetail( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri ) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
	
		// Préparation de la requète
		$lChamps = array(OperationManager::CHAMP_OPERATION_ID .
						"," . OperationManager::CHAMP_OPERATION_ID_COMPTE .
						"," . OperationManager::CHAMP_OPERATION_MONTANT .
						"," . OperationManager::CHAMP_OPERATION_LIBELLE .
						"," . OperationManager::CHAMP_OPERATION_DATE .
						"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT .
						"," . OperationManager::CHAMP_OPERATION_TYPE .
						"," . OperationManager::CHAMP_OPERATION_DATE_MAJ .
						"," . OperationManager::CHAMP_OPERATION_ID_LOGIN .
						","	. TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_TPP_ID .
						"," . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_CHCP_ID .
						"," . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ORDRE .
						"," . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_VISIBLE .
						"," . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ETAT .
						"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID .
						"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE .
						"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_CHAMP_COMPLEMENTAIRE .
						"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_VISIBLE .
						"," . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID .
						"," . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_LABEL .
						"," . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_OBLIGATOIRE .
						"," . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ETAT .
						"," . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID .
						"," . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID .
						"," . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR );
	
		// Préparation de la requète de recherche
		$lRequete = DbUtils::prepareRequeteRecherche(
				OperationManager::TABLE_OPERATION . "
				JOIN " . TypePaiementManager::TABLE_TYPEPAIEMENT . " ON " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID . " = " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . "
				LEFT JOIN " . TypePaiementChampComplementaireManager::TABLE_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE . " ON " . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_TPP_ID . " = " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . "
				LEFT JOIN " . ChampComplementaireManager::TABLE_CHAMPCOMPLEMENTAIRE . " ON " . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID . " = " . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_CHCP_ID . "
				LEFT JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " ON " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . " AND " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = " . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID
				
				, $lChamps, $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri);
		
		$lListeOperation = array();
		
		if($lRequete !== false) {
			$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
			$lSql = Dbutils::executerRequete($lRequete);
			if( mysqli_num_rows($lSql) > 0 ) {
				$lId = 0;
				$lChampComplementaire = array();
				
				while ($lLigne = mysqli_fetch_assoc($lSql)) {
					if($lId != $lLigne[OperationManager::CHAMP_OPERATION_ID]) {
						if($lId != 0) {
							$lOperationDetailVO->setChampComplementaire($lChampComplementaire);
							array_push($lListeOperation, $lOperationDetailVO);
						}
						$lId = $lLigne[OperationManager::CHAMP_OPERATION_ID];
						$lChampComplementaire = array();
						
						$lOperationDetailVO = OperationManager::remplirOperationDetailEntete(
								$lLigne[OperationManager::CHAMP_OPERATION_ID],
								$lLigne[OperationManager::CHAMP_OPERATION_ID_COMPTE],
								$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
								$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
								$lLigne[OperationManager::CHAMP_OPERATION_DATE],
								$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT],
								$lLigne[OperationManager::CHAMP_OPERATION_TYPE],
								$lLigne[OperationManager::CHAMP_OPERATION_DATE_MAJ],
								$lLigne[OperationManager::CHAMP_OPERATION_ID_LOGIN],
								$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_ID],
								$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE],
								$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_CHAMP_COMPLEMENTAIRE],
								$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_VISIBLE]);
					}
					
					if(!is_null($lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID])) {
						$lChampComplementaire[$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID]] = OperationManager::remplirOperationDetail(
								$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_TPP_ID],
								$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_CHCP_ID],
								$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ORDRE],
								$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_VISIBLE],
								$lLigne[TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ETAT],
								$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID],
								$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_LABEL],
								$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_OBLIGATOIRE],
								$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ETAT],
								$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID],
								$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR]);
					}
				}
				$lOperationDetailVO->setChampComplementaire($lChampComplementaire);
				array_push($lListeOperation, $lOperationDetailVO);
			} else {
				$lListeOperation[0] = new OperationDetailVO();
			}
			return $lListeOperation;
		}

		$lListeOperation[0] = new OperationDetailVO();
		return $lListeOperation;
	}
	
	/**
	 * @name rechercheOperationZeybu( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri )
	 * @param string nom de la table
	 * @param string Le type de critère de recherche
	 * @param array(string) champs à récupérer dans la table
	 * @param array(array(string, object)) Dictionnaire(champ, valeur)) contenant les champs à filtrer ainsi que la valeur du filtre
	 * @param array(array(string, string)) Dictionnaire(champ, sens) contenant les tris à appliquer
	 * @return array(OperationVO)
	 * @desc Récupères les lignes de la table selon le critère de recherche puis trie et renvoie la liste de résultat sous forme d'une collection de OperationVO
	 */
	public static function rechercheOperationZeybu( $pDateDebut = null, $pDateFin = null, $pIdMarche = null ) {
		if(!is_null($pIdMarche)) {
			if($pIdMarche == -1) { // Pour les Achats hors marché
				$lIdMarche = NULL;
			} else {
				$lIdMarche = $pIdMarche;
			}
		}
		
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		
		// Préparation de la requète
		
		// Les achats
		$lRequete =
			"SELECT  
				   OperationZeybu."	. OperationManager::CHAMP_OPERATION_ID .
				", OperationZeybu." . OperationManager::CHAMP_OPERATION_DATE .
				", OperationZeybu." . CompteManager::CHAMP_COMPTE_LABEL .
				", OperationZeybu." . OperationManager::CHAMP_OPERATION_LIBELLE .
				", OperationZeybu." . OperationManager::CHAMP_OPERATION_MONTANT .
				", OperationZeybu." . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE .
				", OperationZeybu." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR .
			" FROM (
				( SELECT 
					ope1." . OperationManager::CHAMP_OPERATION_ID  . " AS " . OperationManager::CHAMP_OPERATION_ID . ",
					ope1." . OperationManager::CHAMP_OPERATION_DATE . " AS " . OperationManager::CHAMP_OPERATION_DATE . ",
					" . CompteManager::CHAMP_COMPTE_LABEL . " AS " . CompteManager::CHAMP_COMPTE_LABEL . ",
					ope1." . OperationManager::CHAMP_OPERATION_LIBELLE . " AS " . OperationManager::CHAMP_OPERATION_LIBELLE . ",
					ope1." . OperationManager::CHAMP_OPERATION_MONTANT . " AS " . OperationManager::CHAMP_OPERATION_MONTANT . ",
					" . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . " AS " . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . ",
					NULL AS " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
				FROM " . OperationManager::TABLE_OPERATION . " ope1
				JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " chcp1
					ON ope1." . OperationManager::CHAMP_OPERATION_ID . " = chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . "
					AND chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 8
				JOIN " . OperationManager::TABLE_OPERATION . " ope2
					ON ope2." . OperationManager::CHAMP_OPERATION_ID . " = chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
				JOIN " . CompteManager::TABLE_COMPTE . " 
					ON ope2." . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = " . CompteManager::CHAMP_COMPTE_ID . "
				JOIN " . TypePaiementManager::TABLE_TYPEPAIEMENT . " 
					ON ope1." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID;

		$lRequete .=	" WHERE ope1." . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = -1
				AND ope1." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " in (7,8)";
		if(!is_null($pDateDebut) && !is_null($pDateFin)) {
			$lRequete .= " AND ope1." . OperationManager::CHAMP_OPERATION_DATE . " >= '" . StringUtils::securiser($pDateDebut) . "'
						 AND ope1." . OperationManager::CHAMP_OPERATION_DATE . " <= '" . StringUtils::securiser($pDateFin) . "'";
		}
		
		// Les Virements
		if(is_null($pIdMarche) || is_null($lIdMarche)) { // Les virements ne sont pas dans les marchés
			// Les Virements vers
			$lRequete .= " ) UNION ( SELECT 
						ope1." . OperationManager::CHAMP_OPERATION_ID  . " AS " . OperationManager::CHAMP_OPERATION_ID . ",
						ope1." . OperationManager::CHAMP_OPERATION_DATE . " AS " . OperationManager::CHAMP_OPERATION_DATE . ",
						" . CompteManager::CHAMP_COMPTE_LABEL . " AS " . CompteManager::CHAMP_COMPTE_LABEL . ",
						ope1." . OperationManager::CHAMP_OPERATION_LIBELLE . " AS " . OperationManager::CHAMP_OPERATION_LIBELLE . ",
						ope1." . OperationManager::CHAMP_OPERATION_MONTANT . " AS " . OperationManager::CHAMP_OPERATION_MONTANT . ",
						" . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . " AS " . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . ",
						NULL AS " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
					FROM " . OperationManager::TABLE_OPERATION . " ope1
					JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " chcp1
						ON ope1." . OperationManager::CHAMP_OPERATION_ID . " = chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . "
						AND chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 4
					JOIN " . OperationManager::TABLE_OPERATION . " ope2
						ON ope2." . OperationManager::CHAMP_OPERATION_ID . " = chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
					JOIN " . CompteManager::TABLE_COMPTE . " 
						ON ope2." . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = " . CompteManager::CHAMP_COMPTE_ID . "
					JOIN " . TypePaiementManager::TABLE_TYPEPAIEMENT . " 
						ON ope1." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID;

			$lRequete .=	" WHERE ope1." . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = -1
					AND ope1." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " in (3,9)";
			if(!is_null($pDateDebut) && !is_null($pDateFin)) {
				$lRequete .= " AND ope1." . OperationManager::CHAMP_OPERATION_DATE . " >= '" . StringUtils::securiser($pDateDebut) . "'
							 AND ope1." . OperationManager::CHAMP_OPERATION_DATE . " <= '" . StringUtils::securiser($pDateFin) . "'";
			}
			
			// Les Virements de
			$lRequete .= " ) UNION ( SELECT
						ope1." . OperationManager::CHAMP_OPERATION_ID  . " AS " . OperationManager::CHAMP_OPERATION_ID . ",
						ope1." . OperationManager::CHAMP_OPERATION_DATE . " AS " . OperationManager::CHAMP_OPERATION_DATE . ",
						" . CompteManager::CHAMP_COMPTE_LABEL . " AS " . CompteManager::CHAMP_COMPTE_LABEL . ",
						ope1." . OperationManager::CHAMP_OPERATION_LIBELLE . " AS " . OperationManager::CHAMP_OPERATION_LIBELLE . ",
						ope1." . OperationManager::CHAMP_OPERATION_MONTANT . " AS " . OperationManager::CHAMP_OPERATION_MONTANT . ",
						" . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . " AS " . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . ",
						NULL AS " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
					FROM " . OperationManager::TABLE_OPERATION . " ope1
					JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " chcp1
						ON ope1." . OperationManager::CHAMP_OPERATION_ID . " = chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . "
						AND chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 5
					JOIN " . OperationManager::TABLE_OPERATION . " ope2
						ON ope2." . OperationManager::CHAMP_OPERATION_ID . " = chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
					JOIN " . CompteManager::TABLE_COMPTE . "
						ON ope2." . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = " . CompteManager::CHAMP_COMPTE_ID . "
					JOIN " . TypePaiementManager::TABLE_TYPEPAIEMENT . "
						ON ope1." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID;
	
			$lRequete .=	" WHERE ope1." . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = -1
					AND ope1." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " in (4,10)";
			if(!is_null($pDateDebut) && !is_null($pDateFin)) {
				$lRequete .= " AND ope1." . OperationManager::CHAMP_OPERATION_DATE . " >= '" . StringUtils::securiser($pDateDebut) . "'
							 AND ope1." . OperationManager::CHAMP_OPERATION_DATE . " <= '" . StringUtils::securiser($pDateFin) . "'";
			}
		}
		
		// Livraison (Facture)
		$lRequete .=	") UNION ( SELECT
					LIVRAISON." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . " AS " . OperationManager::CHAMP_OPERATION_ID . ",
					ope3." . OperationManager::CHAMP_OPERATION_DATE . " AS " . OperationManager::CHAMP_OPERATION_DATE . ",
						" . CompteManager::CHAMP_COMPTE_LABEL . " AS " . CompteManager::CHAMP_COMPTE_LABEL . ",
					`ope3`." . OperationManager::CHAMP_OPERATION_LIBELLE . " AS " . OperationManager::CHAMP_OPERATION_LIBELLE . ",
					`ope3`." . OperationManager::CHAMP_OPERATION_MONTANT . " AS " . OperationManager::CHAMP_OPERATION_MONTANT . ",
					" . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . " AS " . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . ",
					chcp3." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . " AS " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
				FROM " . OperationManager::TABLE_OPERATION . " ope3
				JOIN (
					SELECT
						chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . ",
						ope4." . OperationManager::CHAMP_OPERATION_ID_COMPTE . "
					FROM " . OperationManager::TABLE_OPERATION . " ope4
					JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " chcp1
							ON ope4." . OperationManager::CHAMP_OPERATION_ID . " = chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . "
							AND chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 10";
						
			$lRequete .= " WHERE ope4." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = 6 ";
			if(!is_null($pDateDebut) && !is_null($pDateFin)) {
				$lRequete .= " AND ope4." . OperationManager::CHAMP_OPERATION_DATE . " >= '" . StringUtils::securiser($pDateDebut) . "'
							 AND ope4." . OperationManager::CHAMP_OPERATION_DATE . " <= '" . StringUtils::securiser($pDateFin) . "'";
			}
			$lRequete .= "	) LIVRAISON 	
					ON ope3." . OperationManager::CHAMP_OPERATION_ID . " = LIVRAISON. " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
		
				JOIN " . CompteManager::TABLE_COMPTE . " 
						ON LIVRAISON." . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = " . CompteManager::TABLE_COMPTE . "." . CompteManager::CHAMP_COMPTE_ID . "
				JOIN  " . TypePaiementManager::TABLE_TYPEPAIEMENT . " 
						ON ope3." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID . "
				LEFT JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " chcp3
							ON ope3." . OperationManager::CHAMP_OPERATION_ID . " = chcp3." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . "
							AND chcp3." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 3
			) 
		) OperationZeybu ";
			
		if(!is_null($pIdMarche)) {
			if(is_null($lIdMarche)) {
				$lRequete .= " LEFT ";
			}
			$lRequete .= " JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " chcp
						ON OperationZeybu." . OperationManager::CHAMP_OPERATION_ID . " = chcp." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . "
						AND chcp." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 1";
			if(!is_null($lIdMarche)) {
				$lRequete .= " AND chcp." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . " = '" . StringUtils::securiser($lIdMarche) . "'" ;
			} else {
				$lRequete .= " WHERE chcp." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . " IS NULL ";
			}
		}

		$lRequete .= " ORDER BY OperationZeybu." . OperationManager::CHAMP_OPERATION_DATE . " DESC";
	
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
		
		$lListeCompteZeybuOperation = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeCompteZeybuOperation,
				 new CompteZeybuOperationVO(
				$lLigne[OperationManager::CHAMP_OPERATION_ID],
				$lLigne[OperationManager::CHAMP_OPERATION_DATE],
				$lLigne[CompteManager::CHAMP_COMPTE_LABEL],
				$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
				$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
				$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE],
				$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR]));
			}
			
			
		} else {
			$lListeCompteZeybuOperation[0] = new CompteZeybuOperationVO();
		}
		return $lListeCompteZeybuOperation;
	}
	
	/**
	 * @name rechercheOperationAssociation( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri )
	 * @param string nom de la table
	 * @param string Le type de critère de recherche
	 * @param array(string) champs à récupérer dans la table
	 * @param array(array(string, object)) Dictionnaire(champ, valeur)) contenant les champs à filtrer ainsi que la valeur du filtre
	 * @param array(array(string, string)) Dictionnaire(champ, sens) contenant les tris à appliquer
	 * @return array(OperationVO)
	 * @desc Récupères les lignes de la table selon le critère de recherche puis trie et renvoie la liste de résultat sous forme d'une collection de OperationVO
	 */
	public static function rechercheOperationAssociation( $pDateDebut = null, $pDateFin = null ) {	
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
	
		// Préparation de la requète
	
		// Les adhésions et les opérations
		$lRequete =
		"SELECT
		OperationAssociation."	. OperationManager::CHAMP_OPERATION_ID .
		", OperationAssociation." . OperationManager::CHAMP_OPERATION_DATE .
		", OperationAssociation." . CompteManager::CHAMP_COMPTE_LABEL .
		", OperationAssociation." . OperationManager::CHAMP_OPERATION_LIBELLE .
		", OperationAssociation." . OperationManager::CHAMP_OPERATION_MONTANT .
		", OperationAssociation." . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE .
		", OperationAssociation." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR .
		" FROM (
		( SELECT
		" . OperationManager::CHAMP_OPERATION_ID  . " AS " . OperationManager::CHAMP_OPERATION_ID . ",
		" . OperationManager::CHAMP_OPERATION_DATE . " AS " . OperationManager::CHAMP_OPERATION_DATE . ",
		" . CompteManager::CHAMP_COMPTE_LABEL . " AS " . CompteManager::CHAMP_COMPTE_LABEL . ",
		" . OperationManager::CHAMP_OPERATION_LIBELLE . " AS " . OperationManager::CHAMP_OPERATION_LIBELLE . ",
		" . OperationManager::CHAMP_OPERATION_MONTANT . " AS " . OperationManager::CHAMP_OPERATION_MONTANT . ",
		" . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . " AS " . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . ",
		" . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
		FROM " . OperationManager::TABLE_OPERATION . "
		LEFT JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " 
			ON " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . "
			AND " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 3
		LEFT JOIN " . AdhesionAdherentManager::TABLE_ADHESIONADHERENT . " 
			ON " . AdhesionAdherentManager::CHAMP_ADHESIONADHERENT_ID_OPERATION . " = " . OperationManager::CHAMP_OPERATION_ID . "
		LEFT JOIN " . AdherentManager::TABLE_ADHERENT . " 
			ON " . AdherentManager::CHAMP_ADHERENT_ID . " = " . AdhesionAdherentManager::CHAMP_ADHESIONADHERENT_ID_ADHERENT . "
		LEFT JOIN " . CompteManager::TABLE_COMPTE . "
			ON " . AdherentManager::CHAMP_ADHERENT_ID_COMPTE . " = " . CompteManager::CHAMP_COMPTE_ID . "
		JOIN " . TypePaiementManager::TABLE_TYPEPAIEMENT . "
			ON " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID;
	
		$lRequete .=	" WHERE " . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = -4
			AND " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " in (1,2)";
		if(!is_null($pDateDebut) && !is_null($pDateFin)) {
			$lRequete .= " AND " . OperationManager::CHAMP_OPERATION_DATE . " >= '" . StringUtils::securiser($pDateDebut) . "'
			AND " . OperationManager::CHAMP_OPERATION_DATE . " <= '" . StringUtils::securiser($pDateFin) . "'";
		}
		$lRequete .= " GROUP BY " . OperationManager::CHAMP_OPERATION_ID;
		
		// Les Virements
		// Les Virements vers
		$lRequete .= " ) UNION ( SELECT
		ope1." . OperationManager::CHAMP_OPERATION_ID  . " AS " . OperationManager::CHAMP_OPERATION_ID . ",
		ope1." . OperationManager::CHAMP_OPERATION_DATE . " AS " . OperationManager::CHAMP_OPERATION_DATE . ",
		" . CompteManager::CHAMP_COMPTE_LABEL . " AS " . CompteManager::CHAMP_COMPTE_LABEL . ",
		ope1." . OperationManager::CHAMP_OPERATION_LIBELLE . " AS " . OperationManager::CHAMP_OPERATION_LIBELLE . ",
		ope1." . OperationManager::CHAMP_OPERATION_MONTANT . " AS " . OperationManager::CHAMP_OPERATION_MONTANT . ",
		" . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . " AS " . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . ",
		NULL AS " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
		FROM " . OperationManager::TABLE_OPERATION . " ope1
		JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " chcp1
		ON ope1." . OperationManager::CHAMP_OPERATION_ID . " = chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . "
		AND chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 4
		JOIN " . OperationManager::TABLE_OPERATION . " ope2
		ON ope2." . OperationManager::CHAMP_OPERATION_ID . " = chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
		JOIN " . CompteManager::TABLE_COMPTE . "
		ON ope2." . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = " . CompteManager::CHAMP_COMPTE_ID . "
		JOIN " . TypePaiementManager::TABLE_TYPEPAIEMENT . "
		ON ope1." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID;

		$lRequete .=	" WHERE ope1." . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = -4
		AND ope1." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = 3 ";
		if(!is_null($pDateDebut) && !is_null($pDateFin)) {
			$lRequete .= " AND ope1." . OperationManager::CHAMP_OPERATION_DATE . " >= '" . StringUtils::securiser($pDateDebut) . "'
			AND ope1." . OperationManager::CHAMP_OPERATION_DATE . " <= '" . StringUtils::securiser($pDateFin) . "'";
		}
			
		// Les Virements de
		$lRequete .= " ) UNION ( SELECT
		ope1." . OperationManager::CHAMP_OPERATION_ID  . " AS " . OperationManager::CHAMP_OPERATION_ID . ",
		ope1." . OperationManager::CHAMP_OPERATION_DATE . " AS " . OperationManager::CHAMP_OPERATION_DATE . ",
		" . CompteManager::CHAMP_COMPTE_LABEL . " AS " . CompteManager::CHAMP_COMPTE_LABEL . ",
		ope1." . OperationManager::CHAMP_OPERATION_LIBELLE . " AS " . OperationManager::CHAMP_OPERATION_LIBELLE . ",
		ope1." . OperationManager::CHAMP_OPERATION_MONTANT . " AS " . OperationManager::CHAMP_OPERATION_MONTANT . ",
		" . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . " AS " . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . ",
		NULL AS " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
		FROM " . OperationManager::TABLE_OPERATION . " ope1
		JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " chcp1
		ON ope1." . OperationManager::CHAMP_OPERATION_ID . " = chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . "
		AND chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 5
		JOIN " . OperationManager::TABLE_OPERATION . " ope2
		ON ope2." . OperationManager::CHAMP_OPERATION_ID . " = chcp1." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
		JOIN " . CompteManager::TABLE_COMPTE . "
		ON ope2." . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = " . CompteManager::CHAMP_COMPTE_ID . "
		JOIN " . TypePaiementManager::TABLE_TYPEPAIEMENT . "
		ON ope1." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID;

		$lRequete .=	" WHERE ope1." . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = -4
		AND ope1." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = 4 ";
		if(!is_null($pDateDebut) && !is_null($pDateFin)) {
			$lRequete .= " AND ope1." . OperationManager::CHAMP_OPERATION_DATE . " >= '" . StringUtils::securiser($pDateDebut) . "'
			AND ope1." . OperationManager::CHAMP_OPERATION_DATE . " <= '" . StringUtils::securiser($pDateFin) . "'";
		}
		$lRequete .=	")) OperationAssociation  ORDER BY OperationAssociation." . OperationManager::CHAMP_OPERATION_DATE . " DESC";
	
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
	
		$lListeCompteZeybuOperation = array();
		if( mysqli_num_rows($lSql) > 0 ) {
				
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeCompteZeybuOperation,
					 new CompteZeybuOperationVO(
					 		$lLigne[OperationManager::CHAMP_OPERATION_ID],
					 		$lLigne[OperationManager::CHAMP_OPERATION_DATE],
					 		$lLigne[CompteManager::CHAMP_COMPTE_LABEL],
					 		$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
					 		$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
					 		$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE],
					 		$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR]));
			}
				
				
		} else {
			$lListeCompteZeybuOperation[0] = new CompteZeybuOperationVO();
		}
		return $lListeCompteZeybuOperation;
	}
	
	/**
	* @name selectByIdCompte($pId)
	* @param integer
	* @return array(OperationVO)
	* @desc Récupères toutes les lignes de la table ayant pour IdCompte $pId et les renvoie sous forme d'une collection de OperationVO
	*/
	public static function selectByIdCompte($pId) {		
		return OperationManager::recherche(
			array(OperationManager::CHAMP_OPERATION_ID_COMPTE),
			array('='),
			array($pId),
			array(OperationManager::CHAMP_OPERATION_DATE),
			array('DESC'));
	}
	
	/**
	* @name selectOpeReservation($pIdCompte, $pIdCommande)
	* @param integer
	* @param integer
	* @return array(OperationVO)
	* @desc Récupères toutes les lignes de la table ayant pour IdCompte $pId, IdCommande $pIdCommande et de type 0. Puis les renvoie sous forme d'une collection de OperationVO
	*/
	public static function selectOpeReservation($pIdCompte, $pIdCommande) {
		return OperationManager::recherche(
			array(OperationManager::CHAMP_OPERATION_ID_COMPTE,OperationManager::CHAMP_OPERATION_ID_COMMANDE,OperationManager::CHAMP_OPERATION_TYPE),
			array('=','=','='),
			array($pIdCompte, $pIdCommande,0),
			array(OperationManager::CHAMP_OPERATION_ID),
			array('ASC'));
	}
		
	/**
	* @name recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri )
	* @param string nom de la table
	* @param string Le type de critère de recherche
	* @param array(string) champs à récupérer dans la table
	* @param array(array(string, object)) Dictionnaire(champ, valeur)) contenant les champs à filtrer ainsi que la valeur du filtre
	* @param array(array(string, string)) Dictionnaire(champ, sens) contenant les tris à appliquer
	* @return array(OperationVO)
	* @desc Récupères les lignes de la table selon le critère de recherche puis trie et renvoie la liste de résultat sous forme d'une collection de OperationVO
	*/
	public static function recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri ) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		// Préparation de la requète
		$lChamps = array( 
			    OperationManager::CHAMP_OPERATION_ID .
			"," . OperationManager::CHAMP_OPERATION_ID_COMPTE .
			"," . OperationManager::CHAMP_OPERATION_MONTANT .
			"," . OperationManager::CHAMP_OPERATION_LIBELLE .
			"," . OperationManager::CHAMP_OPERATION_DATE .
			"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT .
			"," . OperationManager::CHAMP_OPERATION_TYPE .
			"," . OperationManager::CHAMP_OPERATION_DATE_MAJ .
			"," . OperationManager::CHAMP_OPERATION_ID_LOGIN		);

		// Préparation de la requète de recherche
		$lRequete = DbUtils::prepareRequeteRecherche(OperationManager::TABLE_OPERATION, $lChamps, $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri);

		$lListeOperation = array();

		if($lRequete !== false) {

			$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
			$lSql = Dbutils::executerRequete($lRequete);

			if( mysqli_num_rows($lSql) > 0 ) {

				while ( $lLigne = mysqli_fetch_assoc($lSql) ) {

					array_push($lListeOperation,
						OperationManager::remplirOperation(
						$lLigne[OperationManager::CHAMP_OPERATION_ID],
						$lLigne[OperationManager::CHAMP_OPERATION_ID_COMPTE],
						$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
						$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
						$lLigne[OperationManager::CHAMP_OPERATION_DATE],
						$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT],
						$lLigne[OperationManager::CHAMP_OPERATION_TYPE],
						$lLigne[OperationManager::CHAMP_OPERATION_DATE_MAJ],
						$lLigne[OperationManager::CHAMP_OPERATION_ID_LOGIN]));
				}
			} else {
				$lListeOperation[0] = new OperationVO();
			}

			return $lListeOperation;
		}

		$lListeOperation[0] = new OperationVO();
		return $lListeOperation;
	}
	
	/**
	 * @name remplirOperation($pId, $pIdCompte, $pMontant, $pLibelle, $pDate, $pTypePaiement, $pType, $pDateMaj, $pIdLogin)
	 * @param int(11)
	 * @param int(11)
	 * @param decimal(10,2)
	 * @param varchar(100)
	 * @param datetime
	 * @param int(11)
	 * @param int(11)
	 * @param datetime
	 * @param int(11)
	 * @return OperationVO
	 * @desc Retourne une OperationVO remplie
	 */
	private static function remplirOperation($pId, $pIdCompte, $pMontant, $pLibelle, $pDate, $pTypePaiement, $pType, $pDateMaj, $pIdLogin) {
		$lOperation = new OperationVO();
		$lOperation->setId($pId);
		$lOperation->setIdCompte($pIdCompte);
		$lOperation->setMontant($pMontant);
		$lOperation->setLibelle($pLibelle);
		$lOperation->setDate($pDate);
		$lOperation->setTypePaiement($pTypePaiement);
		$lOperation->setType($pType);
		$lOperation->setDateMaj($pDateMaj);
		$lOperation->setIdLogin($pIdLogin);
		return $lOperation;
	}

	/**
	 * @name selectListeVirementCompte($pIdCompte)
	 * @return array(OperationDetailVO) ou false en erreur
	 * @desc Retourne l'ensemble des virements
	 */
	public static function selectListeVirementCompte($pIdCompte) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"SELECT 
			 ope1." . OperationManager::CHAMP_OPERATION_ID . 
			", ope1." . OperationManager::CHAMP_OPERATION_DATE . 
			"," . CompteManager::CHAMP_COMPTE_LABEL . 
			", ope1." . OperationManager::CHAMP_OPERATION_MONTANT . 
			", ope1." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT .
			", " . AdherentManager::CHAMP_ADHERENT_NOM . 
			", " . AdherentManager::CHAMP_ADHERENT_PRENOM . "
		FROM " . OperationManager::TABLE_OPERATION . " ope1 
		JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . "
			ON ope1." . OperationManager::CHAMP_OPERATION_ID . " = " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . "
			AND " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " in (4,5)
		JOIN " . OperationManager::TABLE_OPERATION . " ope2
			ON ope2." . OperationManager::CHAMP_OPERATION_ID . " = " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
		LEFT JOIN " . CompteManager::TABLE_COMPTE . "
			ON ope2." . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = " .CompteManager::CHAMP_COMPTE_ID . "
		LEFT JOIN " . AdherentManager::TABLE_ADHERENT . "
			ON " . CompteManager::CHAMP_COMPTE_ID_ADHERENT_PRINCIPAL . " = " . AdherentManager::CHAMP_ADHERENT_ID . "
		WHERE ope1." .  OperationManager::CHAMP_OPERATION_ID_COMPTE . " = '" . StringUtils::securiser($pIdCompte) . "'
			AND ope1." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " in (3,4,9,10)
		ORDER BY ope1." . OperationManager::CHAMP_OPERATION_DATE . " DESC;";
		
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
		
		$lListeCompteListeVirement = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeCompteListeVirement,
				OperationManager::remplirListeVirementCompte(
				$lLigne[OperationManager::CHAMP_OPERATION_ID],
				$lLigne[OperationManager::CHAMP_OPERATION_DATE],
				$lLigne[CompteManager::CHAMP_COMPTE_LABEL],
				$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
				$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT],
				$lLigne[AdherentManager::CHAMP_ADHERENT_NOM],
				$lLigne[AdherentManager::CHAMP_ADHERENT_PRENOM]));
			}
		} else {
			$lListeCompteListeVirement[0] = new CompteListeVirementVO();
		}
		return $lListeCompteListeVirement;
	}
	
	/**
	 * @name remplirListeVirementCompte($pOpeId, $pOpeDate, $pCptLabel, $pOpeMontant, $pOpeTypePaiement)
	 * @param int(11)
	 * @param datetime
	 * @param varchar(30)
	 * @param decimal(10,2)
	 * @param int(11)
	 * @return CompteZeybuListeVirementVO
	 * @desc Retourne une CompteZeybuListeVirementVO remplie
	 */
	private static function remplirListeVirementCompte($pOpeId, $pOpeDate, $pCptLabel, $pOpeMontant, $pOpeTypePaiement, $pAdhNom, $pAdhPrenom) {
		$lCompteZeybuListeVirement = new CompteListeVirementVO();
		$lCompteZeybuListeVirement->setOpeId($pOpeId);
		$lCompteZeybuListeVirement->setOpeDate($pOpeDate);
		$lCompteZeybuListeVirement->setCptLabel($pCptLabel);
		$lCompteZeybuListeVirement->setOpeMontant($pOpeMontant);
		$lCompteZeybuListeVirement->setOpeTypePaiement($pOpeTypePaiement);
		$lCompteZeybuListeVirement->setAdhNom($pAdhNom);
		$lCompteZeybuListeVirement->setAdhPrenom($pAdhPrenom);
		return $lCompteZeybuListeVirement;
	}
	
	/**
	 * @name operationAttenteAdherent($pTypePaiement)
	 * @return array(OperationDetailVO) ou false en erreur
	 * @desc Retourne l'ensemble des des opérations des adhérents non pointées
	 */
	public static function operationAttenteAdherent($pTypePaiement) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"SELECT " 
				. AdherentManager::CHAMP_ADHERENT_ID .
			"," . AdherentManager::CHAMP_ADHERENT_NUMERO . 
			"," . AdherentManager::CHAMP_ADHERENT_NOM . 
			"," . AdherentManager::CHAMP_ADHERENT_PRENOM . 
			"," . CompteManager::CHAMP_COMPTE_LABEL . 
			"," . CompteManager::CHAMP_COMPTE_SOLDE .
			"," . OperationManager::CHAMP_OPERATION_MONTANT .
			"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT .
			"," . OperationManager::CHAMP_OPERATION_DATE .
			"," . OperationManager::CHAMP_OPERATION_LIBELLE .
			"," . OperationManager::CHAMP_OPERATION_ID .
			"," . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . 
			"," . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . 
			',' . OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_REMISE_CHEQUE . 
			',' . RemiseChequeManager::CHAMP_REMISECHEQUE_NUMERO . "
		FROM " . OperationManager::TABLE_OPERATION . "
		JOIN " . CompteManager::TABLE_COMPTE . " ON " . CompteManager::CHAMP_COMPTE_ID . " = " . OperationManager::CHAMP_OPERATION_ID_COMPTE . "
		JOIN " . AdherentManager::TABLE_ADHERENT . " ON " . AdherentManager::CHAMP_ADHERENT_ID_COMPTE . " = " . OperationManager::CHAMP_OPERATION_ID_COMPTE . "
		LEFT JOIN  " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " 
			ON " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . "
		LEFT JOIN " . OperationRemiseChequeManager::TABLE_OPERATIONREMISECHEQUE . " 
			ON " .	OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_OPERATION . " = " . OperationManager::CHAMP_OPERATION_ID . "
			AND " . OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ETAT . " = 0
		LEFT JOIN " . RemiseChequeManager::TABLE_REMISECHEQUE . "
			ON " . OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_REMISE_CHEQUE . " = " . RemiseChequeManager::CHAMP_REMISECHEQUE_ID . "
		WHERE " . OperationManager::CHAMP_OPERATION_TYPE . " = 0 
			AND " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " in (1,2)
			AND " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = '" . StringUtils::securiser($pTypePaiement) . "'
		ORDER BY " . OperationManager::CHAMP_OPERATION_DATE . ";";
		
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
		
		$lListeOperationAttente = array();
		$lChampComplementaire = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			
			$lOpeId = NULL;
			
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				 if($lOpeId != $lLigne[OperationManager::CHAMP_OPERATION_ID]) {
				 	if(!is_null($lOpeId)) {
				 		$lOperationAttente->setOpeTypePaiementChampComplementaire($lChampComplementaire);
					 	$lListeOperationAttente[$lOpeId] = $lOperationAttente;
					}
				 	$lOpeId = $lLigne[OperationManager::CHAMP_OPERATION_ID];
				 	
				 	$lOperationAttente = OperationManager::remplirOperationAttenteAdherentEntete(
						$lLigne[AdherentManager::CHAMP_ADHERENT_ID],
						$lLigne[AdherentManager::CHAMP_ADHERENT_NUMERO],
						$lLigne[AdherentManager::CHAMP_ADHERENT_NOM],
						$lLigne[AdherentManager::CHAMP_ADHERENT_PRENOM],
						$lLigne[CompteManager::CHAMP_COMPTE_LABEL],
						$lLigne[CompteManager::CHAMP_COMPTE_SOLDE],
						$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
						$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT],
						$lLigne[OperationManager::CHAMP_OPERATION_DATE],
						$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
						$lLigne[OperationManager::CHAMP_OPERATION_ID],
						$lLigne[OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_REMISE_CHEQUE],
						$lLigne[RemiseChequeManager::CHAMP_REMISECHEQUE_NUMERO]);
				 }	

				 if(!is_null($lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID])) {
				 	$lChampComplementaire[$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID]] = OperationManager::remplirOperationDetail(
						NULL,
						NULL,
						NULL,
						NULL,
						NULL,
						$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID],
						NULL,
						NULL,
						NULL,
						$lLigne[OperationManager::CHAMP_OPERATION_ID],
						$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR]);
				 }
			}
			$lOperationAttente->setOpeTypePaiementChampComplementaire($lChampComplementaire);
			$lListeOperationAttente[$lOpeId] = $lOperationAttente;
		} else {
			$lListeOperationAttente[0] = new OperationAttenteAdherentVO();
		}
		return $lListeOperationAttente;
	}
	
	/**
	 * @name remplirOperationAttenteAdherentEntete($pAdhId, $pAdhNumero, $pAdhNom, $pAdhPrenom, $pCptLabel, $pCptSolde, $pOpeMontant, $pOpeTypePaiement, $pOpeDate, $pOpeLibelle, $pOpeId, $pNumeroRemiseCheque)
	 * @param int(11)
	 * @param varchar(20)
	 * @param varchar(50)
	 * @param varchar(50)
	 * @param varchar(30)
	 * @param decimal(10,2)
	 * @param decimal(10,2)
	 * @param int(11)
	 * @param datetime
	 * @param varchar(100)
	 * @param int(11)
	 * @param int(11)
	 * @return OperationAttenteAdherentVO
	 * @desc Retourne une OperationAttenteAdherentVO remplie
	 */
	private static function remplirOperationAttenteAdherentEntete($pAdhId, $pAdhNumero, $pAdhNom, $pAdhPrenom, $pCptLabel, $pCptSolde, $pOpeMontant, $pOpeTypePaiement, $pOpeDate, $pOpeLibelle, $pOpeId, $pIdRemiseCheque, $pNumeroRemiseCheque) {
		$lOperationAttente = new OperationAttenteAdherentVO();
		$lOperationAttente->setAdhId($pAdhId);
		$lOperationAttente->setAdhNumero($pAdhNumero);
		$lOperationAttente->setAdhNom($pAdhNom);
		$lOperationAttente->setAdhPrenom($pAdhPrenom);
		$lOperationAttente->setCptLabel($pCptLabel);
		$lOperationAttente->setCptSolde($pCptSolde);
		$lOperationAttente->setOpeMontant($pOpeMontant);
		$lOperationAttente->setOpeTypePaiement($pOpeTypePaiement);
		$lOperationAttente->setOpeDate($pOpeDate);
		$lOperationAttente->setOpeLibelle($pOpeLibelle);
		$lOperationAttente->setOpeId($pOpeId);
		$lOperationAttente->setIdRemiseCheque($pIdRemiseCheque);
		$lOperationAttente->setNumeroRemiseCheque($pNumeroRemiseCheque);
		return $lOperationAttente;
	}

	/**
	 * @name operationMarche($pIdMarche, $pTypePaiement)
	 * @return array(OperationDetailVO) ou false en erreur
	 * @desc Retourne l'ensemble des opérations sur un marché
	 */
	public static function operationMarche($pIdMarche, $pTypePaiement) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
	
		$lRequete =
		"(SELECT "
		. AdherentManager::CHAMP_ADHERENT_ID .
		"," . AdherentManager::CHAMP_ADHERENT_NUMERO .
		"," . AdherentManager::CHAMP_ADHERENT_NOM .
		"," . AdherentManager::CHAMP_ADHERENT_PRENOM .
		"," . CompteManager::CHAMP_COMPTE_LABEL .
		"," . CompteManager::CHAMP_COMPTE_SOLDE .
		"," . OperationManager::CHAMP_OPERATION_MONTANT .
		"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT .
		"," . OperationManager::CHAMP_OPERATION_DATE .
		"," . OperationManager::CHAMP_OPERATION_LIBELLE .
		"," . OperationManager::CHAMP_OPERATION_ID .
		", b." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID .
		", b." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
		FROM " . OperationManager::TABLE_OPERATION . "
		JOIN " . CompteManager::TABLE_COMPTE . " ON " . CompteManager::CHAMP_COMPTE_ID . " = " . OperationManager::CHAMP_OPERATION_ID_COMPTE . "
		JOIN " . AdherentManager::TABLE_ADHERENT . " ON " . AdherentManager::CHAMP_ADHERENT_ID_COMPTE . " = " . OperationManager::CHAMP_OPERATION_ID_COMPTE . "
		JOIN  " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " a
			ON a." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . "
			AND a." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 1
		JOIN  " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " b
			ON b." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . "
		WHERE a." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . " = '" . StringUtils::securiser($pIdMarche) . "'
		AND " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " in (1,2)
		AND " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = '" . StringUtils::securiser($pTypePaiement) . "'
		) UNION (
		SELECT 
		  NULL
		, NULL
		, NULL
		, NULL
		," . CompteManager::CHAMP_COMPTE_LABEL .
		"," . CompteManager::CHAMP_COMPTE_SOLDE .
		"," . OperationManager::CHAMP_OPERATION_MONTANT .
		"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT .
		"," . OperationManager::CHAMP_OPERATION_DATE .
		"," . OperationManager::CHAMP_OPERATION_LIBELLE .
		"," . OperationManager::CHAMP_OPERATION_ID .
		", b." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID .
		", b." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
		FROM " . OperationManager::TABLE_OPERATION . "
		JOIN " . CompteManager::TABLE_COMPTE . " ON " . CompteManager::CHAMP_COMPTE_ID . " = " . OperationManager::CHAMP_OPERATION_ID_COMPTE . "
		JOIN  " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " a
			ON a." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . "
			AND a." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 1
		JOIN  " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " b
			ON b." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . "
		WHERE a." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . " = '" . StringUtils::securiser($pIdMarche) . "'
		AND " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " in (1,2)
		AND " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = '" . StringUtils::securiser($pTypePaiement) . "'
		AND " . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = -3
		)
		ORDER BY " . OperationManager::CHAMP_OPERATION_DATE . ";";
	
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
	
		$lListeOperationAttente = array();
		$lChampComplementaire = array();
		if( mysqli_num_rows($lSql) > 0 ) {
				
			$lOpeId = NULL;
				
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				if($lOpeId != $lLigne[OperationManager::CHAMP_OPERATION_ID]) {
					if(!is_null($lOpeId)) {
						$lOperationAttente->setOpeTypePaiementChampComplementaire($lChampComplementaire);
						$lListeOperationAttente[$lOpeId] = $lOperationAttente;
					}
					$lOpeId = $lLigne[OperationManager::CHAMP_OPERATION_ID];
	
					$lOperationAttente = OperationManager::remplirOperationAttenteAdherentEntete(
							$lLigne[AdherentManager::CHAMP_ADHERENT_ID],
							$lLigne[AdherentManager::CHAMP_ADHERENT_NUMERO],
							$lLigne[AdherentManager::CHAMP_ADHERENT_NOM],
							$lLigne[AdherentManager::CHAMP_ADHERENT_PRENOM],
							$lLigne[CompteManager::CHAMP_COMPTE_LABEL],
							$lLigne[CompteManager::CHAMP_COMPTE_SOLDE],
							$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
							$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT],
							$lLigne[OperationManager::CHAMP_OPERATION_DATE],
							$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
							$lLigne[OperationManager::CHAMP_OPERATION_ID], NULL, NULL);
				}
	
				if(!is_null($lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID])) {
					$lChampComplementaire[$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID]] = OperationManager::remplirOperationDetail(
							NULL,
							NULL,
							NULL,
							NULL,
							NULL,
							$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID],
							NULL,
							NULL,
							NULL,
							$lLigne[OperationManager::CHAMP_OPERATION_ID],
							$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR]);
				}
			}
			$lOperationAttente->setOpeTypePaiementChampComplementaire($lChampComplementaire);
			$lListeOperationAttente[$lOpeId] = $lOperationAttente;
		} else {
			$lListeOperationAttente[0] = new OperationAttenteAdherentVO();
		}
		return $lListeOperationAttente;
	}
	
	/**
	 * @name operationAttenteAssociation($pTypePaiement)
	 * @return array(OperationDetailVO) ou false en erreur
	 * @desc Retourne l'ensemble des des opérations pour le comtpe association non pointées
	 */
	public static function operationAttenteAssociation($pTypePaiement) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
	
		$lRequete =
		"SELECT "
		. AdherentManager::CHAMP_ADHERENT_ID .
		"," . AdherentManager::CHAMP_ADHERENT_NUMERO .
		"," . AdherentManager::CHAMP_ADHERENT_NOM .
		"," . AdherentManager::CHAMP_ADHERENT_PRENOM .	
		"," . CompteManager::CHAMP_COMPTE_LABEL .
		"," . CompteManager::CHAMP_COMPTE_SOLDE .	
		"," . OperationManager::CHAMP_OPERATION_MONTANT .
		"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT .
		"," . OperationManager::CHAMP_OPERATION_DATE .
		"," . OperationManager::CHAMP_OPERATION_LIBELLE .
		"," . OperationManager::CHAMP_OPERATION_ID .
		"," . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID .
		"," . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . 
		"," . OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_REMISE_CHEQUE .
		',' . RemiseChequeManager::CHAMP_REMISECHEQUE_NUMERO . "
		FROM " . OperationManager::TABLE_OPERATION . "
		LEFT JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . "
		ON " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . "
		LEFT JOIN " . OperationRemiseChequeManager::TABLE_OPERATIONREMISECHEQUE . " 
			ON " .	OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_OPERATION . " = " . OperationManager::CHAMP_OPERATION_ID . "
			AND " . OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ETAT . " = 0
		LEFT JOIN " . AdhesionAdherentManager::TABLE_ADHESIONADHERENT . "
		ON " . AdhesionAdherentManager::CHAMP_ADHESIONADHERENT_ID_OPERATION . " = " . OperationManager::CHAMP_OPERATION_ID . "
		LEFT JOIN " . AdherentManager::TABLE_ADHERENT . "
		ON " . AdherentManager::CHAMP_ADHERENT_ID . " = " . AdhesionAdherentManager::CHAMP_ADHESIONADHERENT_ID_ADHERENT . "
		LEFT JOIN " . CompteManager::TABLE_COMPTE . "
		ON " . AdherentManager::CHAMP_ADHERENT_ID_COMPTE . " = " . CompteManager::CHAMP_COMPTE_ID . "
		JOIN " . TypePaiementManager::TABLE_TYPEPAIEMENT . "
		ON " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID . "
		LEFT JOIN " . RemiseChequeManager::TABLE_REMISECHEQUE . "
			ON " . OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_REMISE_CHEQUE . " = " . RemiseChequeManager::CHAMP_REMISECHEQUE_ID . "
		WHERE " . OperationManager::CHAMP_OPERATION_TYPE . " = 0
		AND " . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = -4
		AND " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = '" . StringUtils::securiser($pTypePaiement) . "'
		GROUP BY " . OperationManager::CHAMP_OPERATION_ID . ", " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . "
		ORDER BY " . OperationManager::CHAMP_OPERATION_DATE . ";";
	
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
	
		$lListeOperationAttente = array();
		$lChampComplementaire = array();
		if( mysqli_num_rows($lSql) > 0 ) {
				
			$lOpeId = NULL;
				
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				if($lOpeId != $lLigne[OperationManager::CHAMP_OPERATION_ID]) {
					if(!is_null($lOpeId)) {
						$lOperationAttente->setOpeTypePaiementChampComplementaire($lChampComplementaire);
						$lListeOperationAttente[$lOpeId] = $lOperationAttente;
					}
					$lOpeId = $lLigne[OperationManager::CHAMP_OPERATION_ID];
	
					$lOperationAttente = OperationManager::remplirOperationAttenteAdherentEntete(
							$lLigne[AdherentManager::CHAMP_ADHERENT_ID],
							$lLigne[AdherentManager::CHAMP_ADHERENT_NUMERO],
							$lLigne[AdherentManager::CHAMP_ADHERENT_NOM],
							$lLigne[AdherentManager::CHAMP_ADHERENT_PRENOM],
							$lLigne[CompteManager::CHAMP_COMPTE_LABEL],
							$lLigne[CompteManager::CHAMP_COMPTE_SOLDE],
							$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
							$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT],
							$lLigne[OperationManager::CHAMP_OPERATION_DATE],
							$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
							$lLigne[OperationManager::CHAMP_OPERATION_ID],
							$lLigne[OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_REMISE_CHEQUE],
							$lLigne[RemiseChequeManager::CHAMP_REMISECHEQUE_NUMERO]);
				}
	
				if(!is_null($lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID])) {
					$lChampComplementaire[$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID]] = OperationManager::remplirOperationDetail(
							NULL,
							NULL,
							NULL,
							NULL,
							NULL,
							$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID],
							NULL,
							NULL,
							NULL,
							$lLigne[OperationManager::CHAMP_OPERATION_ID],
							$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR]);
				}
			}
			$lOperationAttente->setOpeTypePaiementChampComplementaire($lChampComplementaire);
			$lListeOperationAttente[$lOpeId] = $lOperationAttente;
		} else {
			$lListeOperationAttente[0] = new OperationAttenteAdherentVO();
		}
		return $lListeOperationAttente;
	}
	
	/**
	 * @name operationAttenteFerme($pTypePaiement)
	 * @return array(OperationDetailVO) ou false en erreur
	 * @desc Retourne l'ensemble des opérations des fermes non pointées
	 */
	public static function operationAttenteFerme($pTypePaiement) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
	
		$lRequete =
		"SELECT "
				. FermeManager::CHAMP_FERME_ID . 
			"," . FermeManager::CHAMP_FERME_NUMERO . 
			"," . FermeManager::CHAMP_FERME_NOM . 
			"," . CompteManager::CHAMP_COMPTE_LABEL .
			"," . CompteManager::CHAMP_COMPTE_SOLDE .
			"," . OperationManager::CHAMP_OPERATION_MONTANT .
			"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT .
			"," . OperationManager::CHAMP_OPERATION_DATE .
			"," . OperationManager::CHAMP_OPERATION_LIBELLE .
			"," . OperationManager::CHAMP_OPERATION_ID .
			"," . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID .
			"," . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . 
			"," . OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_REMISE_CHEQUE . 
			',' . RemiseChequeManager::CHAMP_REMISECHEQUE_NUMERO . "
		FROM " . OperationManager::TABLE_OPERATION . "
		JOIN " . CompteManager::TABLE_COMPTE . " ON " . CompteManager::CHAMP_COMPTE_ID . " = " . OperationManager::CHAMP_OPERATION_ID_COMPTE . "
		JOIN " . FermeManager::TABLE_FERME . " ON " . FermeManager::CHAMP_FERME_ID_COMPTE . " = " . OperationManager::CHAMP_OPERATION_ID_COMPTE . "
		LEFT JOIN  " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . "
			ON " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . "
		LEFT JOIN " . OperationRemiseChequeManager::TABLE_OPERATIONREMISECHEQUE . " 
			ON " .	OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_OPERATION . " = " . OperationManager::CHAMP_OPERATION_ID . "
			AND " . OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ETAT . " = 0
		LEFT JOIN " . RemiseChequeManager::TABLE_REMISECHEQUE . "
			ON " . OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_REMISE_CHEQUE . " = " . RemiseChequeManager::CHAMP_REMISECHEQUE_ID . "
		WHERE " . OperationManager::CHAMP_OPERATION_TYPE . " = 0
			AND " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " in (1,2)
			AND " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = '" . StringUtils::securiser($pTypePaiement) . "'
		ORDER BY " . OperationManager::CHAMP_OPERATION_DATE . ";";
	
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
	
		$lListeOperationAttente = array();
		if( mysqli_num_rows($lSql) > 0 ) {
				
			$lOpeId = NULL;
			$lChampComplementaire = array();
				
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				if($lOpeId != $lLigne[OperationManager::CHAMP_OPERATION_ID]) {
					if(!is_null($lOpeId)) {
						$lOperationAttente->setOpeTypePaiementChampComplementaire($lChampComplementaire);
						$lListeOperationAttente[$lOpeId] = $lOperationAttente;
					}
					$lOpeId = $lLigne[OperationManager::CHAMP_OPERATION_ID];
	
					$lOperationAttente = OperationManager::remplirOperationAttenteFermeEntete(
						$lLigne[FermeManager::CHAMP_FERME_ID],
						$lLigne[FermeManager::CHAMP_FERME_NUMERO],
						$lLigne[FermeManager::CHAMP_FERME_NOM],
						$lLigne[CompteManager::CHAMP_COMPTE_LABEL],
						$lLigne[CompteManager::CHAMP_COMPTE_SOLDE],
						$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
						$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT],
						$lLigne[OperationManager::CHAMP_OPERATION_DATE],
						$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
						$lLigne[OperationManager::CHAMP_OPERATION_ID],
						$lLigne[OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_REMISE_CHEQUE],
						$lLigne[RemiseChequeManager::CHAMP_REMISECHEQUE_NUMERO]);
				}
	
				if(!is_null($lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID])) {
					$lChampComplementaire[$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID]] = OperationManager::remplirOperationDetail(
							NULL,
							NULL,
							NULL,
							NULL,
							NULL,
							$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID],
							NULL,
							NULL,
							NULL,
							$lLigne[OperationManager::CHAMP_OPERATION_ID],
							$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR]);
				}
			}
			$lOperationAttente->setOpeTypePaiementChampComplementaire($lChampComplementaire);
			$lListeOperationAttente[$lOpeId] = $lOperationAttente;
		} else {
			$lListeOperationAttente[0] = new OperationAttenteFermeVO();
		}
		return $lListeOperationAttente;
	}
	
	/**
	* @name remplirOperationAttenteFermeEntete($pFerId, $pFerNumero, $pFerNom, $pCptLabel, $pCptSolde, $pOpeMontant, $pOpeTypePaiement, $pOpeDate, $pOpeLibelle, $pOpeId, $pIdRemiseCheque, $pNumeroRemiseCheque)
	* @param int(11)
	* @param varchar(20)
	* @param varchar(50)
	* @param varchar(30)
	* @param decimal(10,2)
	* @param decimal(10,2)
	* @param int(11)
	* @param varchar(50)
	* @param int(11)
	* @param datetime
	* @param varchar(100)
	* @param int(11)
	* @param int(11)
	* @return OperationAttenteFermeVO
	* @desc Retourne une OperationAttenteFermeVO remplie
	*/
	private static function remplirOperationAttenteFermeEntete($pFerId, $pFerNumero, $pFerNom, $pCptLabel, $pCptSolde, $pOpeMontant, $pOpeTypePaiement, $pOpeDate, $pOpeLibelle, $pOpeId, $pIdRemiseCheque, $pNumeroRemiseCheque) {
		$lOperationAttente = new OperationAttenteFermeVO();
		$lOperationAttente->setFerId($pFerId);
		$lOperationAttente->setFerNumero($pFerNumero);
		$lOperationAttente->setFerNom($pFerNom);
		$lOperationAttente->setCptLabel($pCptLabel);
		$lOperationAttente->setCptSolde($pCptSolde);
		$lOperationAttente->setOpeMontant($pOpeMontant);
		$lOperationAttente->setOpeTypePaiement($pOpeTypePaiement);
		$lOperationAttente->setOpeDate($pOpeDate);
		$lOperationAttente->setOpeLibelle($pOpeLibelle);
		$lOperationAttente->setOpeId($pOpeId);
		$lOperationAttente->setIdRemiseCheque($pIdRemiseCheque);
		$lOperationAttente->setNumeroRemiseCheque($pNumeroRemiseCheque);
		return $lOperationAttente;
	}
	
	/**
	 * @name rechercheListeFacture( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri )
	 * @param string nom de la table
	 * @param string Le type de critère de recherche
	 * @param array(string) champs à récupérer dans la table
	 * @param array(array(string, object)) Dictionnaire(champ, valeur)) contenant les champs à filtrer ainsi que la valeur du filtre
	 * @param array(array(string, string)) Dictionnaire(champ, sens) contenant les tris à appliquer
	 * @return array(ListeFactureVO)
	 * @desc Récupères les lignes de la table selon le critère de recherche puis trie et renvoie la liste de résultat sous forme d'une collection de ListeFactureVO
	 */
	public static function rechercheListeFacture( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri ) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
	
		// Préparation de la requète
		$lChamps = array(
				OperationManager::CHAMP_OPERATION_ID .
				",numero." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . " AS valeur
				 ," . OperationManager::CHAMP_OPERATION_DATE .
				"," . CommandeManager::CHAMP_COMMANDE_NUMERO .
				"," . FermeManager::CHAMP_FERME_NOM .
				"," . OperationManager::CHAMP_OPERATION_MONTANT . 
				",cheque." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . " AS cheque ");
	
		//Ajout du filtre pour ne remonter que les factures
		array_push($pTypeRecherche, OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT);
		array_push($pTypeCritere, '=');
		array_push($pCritereRecherche, '6');
		
		// Préparation de la requète de recherche
		$lRequete = DbUtils::prepareRequeteRecherche(
				OperationManager::TABLE_OPERATION . "
				LEFT JOIN " . FermeManager::TABLE_FERME . "
					ON " . FermeManager::CHAMP_FERME_ID_COMPTE . " = " . OperationManager::CHAMP_OPERATION_ID_COMPTE . "
				LEFT JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " AS numero
					ON numero." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . "
					AND numero." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 11
				LEFT JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " AS marche
					ON marche." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . "
					AND marche." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 1
				LEFT JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " AS ope_cpt_marche
					ON ope_cpt_marche." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . "
					AND ope_cpt_marche." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 10
				LEFT JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " AS cheque
					ON cheque." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = ope_cpt_marche." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . "
					AND cheque." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 3
				LEFT JOIN " . CommandeManager::TABLE_COMMANDE . "
					ON " . CommandeManager::CHAMP_COMMANDE_ID . " = marche." . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR
				, $lChamps, $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri);
	
		$lListeFacture = array();	
		if($lRequete !== false) {
			$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
			$lSql = Dbutils::executerRequete($lRequete);
			if( mysqli_num_rows($lSql) > 0 ) {	
				while ($lLigne = mysqli_fetch_assoc($lSql)) {
					array_push($lListeFacture,
					new ListeFactureVO(
					$lLigne[OperationManager::CHAMP_OPERATION_ID],
					$lLigne["valeur"],
					$lLigne[OperationManager::CHAMP_OPERATION_DATE],
					$lLigne[CommandeManager::CHAMP_COMMANDE_NUMERO],
					$lLigne[FermeManager::CHAMP_FERME_NOM],
					$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
					$lLigne["cheque"]));
				}
			} else {
				$lListeFacture[0] = new ListeFactureVO();
			}
			return $lListeFacture;
		}
	
		$lListeFacture[0] = new ListeFactureVO();
		return $lListeFacture;
	}
		
	/**
	 * @name produitCommandeNonFacture($pIdMarche, $pIdCompte)
	 * @return array(ProduitDetailFactureAfficheVO) ou false en erreur
	 * @desc Retourne la liste des produits commandés mais non facturés
	 */
	public static function produitCommandeNonFacture($pIdMarche, $pIdCompte) {
		 // Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		
		$lRequete =
			"SELECT " .
			" pro_commande." . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT . 
			", stock_commande." . StockManager::CHAMP_STOCK_QUANTITE . 
			", dope_commande." . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT . 
			", pro_commande." . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE . 
			"," . CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_ID .
			"," . CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_NOM .
			"," . NomProduitManager::CHAMP_NOMPRODUIT_NUMERO .
			"," . NomProduitManager::CHAMP_NOMPRODUIT_NOM . "
			FROM " . OperationManager::TABLE_OPERATION . " commande 
			JOIN " . StockManager::TABLE_STOCK . " stock_commande 
				ON stock_commande." . StockManager::CHAMP_STOCK_ID_OPERATION . " = commande." . OperationManager::CHAMP_OPERATION_ID . "
				AND stock_commande." . StockManager::CHAMP_STOCK_TYPE . " = 3
			JOIN " . DetailCommandeManager::TABLE_DETAILCOMMANDE . "
				ON " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . " = stock_commande." . StockManager::CHAMP_STOCK_ID_DETAIL_COMMANDE . "
			JOIN " . DetailOperationManager::TABLE_DETAILOPERATION . " dope_commande 
				ON dope_commande." . DetailOperationManager::CHAMP_DETAILOPERATION_ID_OPERATION . " = commande." . OperationManager::CHAMP_OPERATION_ID . "
			    AND dope_commande." . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . " = 5
			    AND dope_commande." . DetailOperationManager::CHAMP_DETAILOPERATION_ID_DETAIL_COMMANDE . " = " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . "
			JOIN " . ProduitManager::TABLE_PRODUIT . " pro_commande 
				ON pro_commande." . ProduitManager::CHAMP_PRODUIT_ID . " = " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . "				
			JOIN " . NomProduitManager::TABLE_NOMPRODUIT . "
				ON pro_commande." . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT . " = " . NomProduitManager::CHAMP_NOMPRODUIT_ID . "
			JOIN " . CategorieProduitManager::TABLE_CATEGORIEPRODUIT . "
				ON " . NomProduitManager::CHAMP_NOMPRODUIT_ID_CATEGORIE . " = " . CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_ID . "
			WHERE commande." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = 5
			AND pro_commande." . ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . " = '" . StringUtils::securiser($pIdMarche) . "'
			AND commande." . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = '" . StringUtils::securiser($pIdCompte) . "'
			AND NOT EXISTS (			
				SELECT 1
				FROM " . OperationManager::TABLE_OPERATION . " facture
				JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " 
					ON " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = facture." . OperationManager::CHAMP_OPERATION_ID . "
					AND " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 1
				JOIN " . StockManager::TABLE_STOCK . " stock_facture 
					ON stock_facture." . StockManager::CHAMP_STOCK_ID_OPERATION . " = facture." . OperationManager::CHAMP_OPERATION_ID . "
					AND stock_facture." . StockManager::CHAMP_STOCK_TYPE . " = 4
				JOIN " . DetailOperationManager::TABLE_DETAILOPERATION . " dope_facture 
					ON dope_facture." . DetailOperationManager::CHAMP_DETAILOPERATION_ID_OPERATION . " = facture." . OperationManager::CHAMP_OPERATION_ID . "
					AND dope_facture." . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . " = 6
				WHERE facture." . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = 6
				AND " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . " = '" . StringUtils::securiser($pIdMarche) . "'
				AND facture." . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = '" . StringUtils::securiser($pIdCompte) . "'				
				AND pro_commande." . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT . " = stock_facture." . StockManager::CHAMP_STOCK_ID_NOM_PRODUIT . "
				AND pro_commande." . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE . " = stock_facture." . StockManager::CHAMP_STOCK_UNITE . ");";
		
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
		
		$lListeDetailFacture = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeDetailFacture,
				new ProduitDetailFactureAfficheVO(
				$lLigne[ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT],
				"",
				"",
				"",
				$lLigne[StockManager::CHAMP_STOCK_QUANTITE],
				$lLigne[ProduitManager::CHAMP_PRODUIT_UNITE_MESURE],
				"",
				"",
				$lLigne[DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT],
				$lLigne[CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_ID],
				$lLigne[CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_NOM],
				$lLigne[NomProduitManager::CHAMP_NOMPRODUIT_NUMERO],
				$lLigne[NomProduitManager::CHAMP_NOMPRODUIT_NOM]));
			}
		} else {
			$lListeDetailFacture[0] = new ProduitDetailFactureAfficheVO();
		}
		return $lListeDetailFacture;
	}
	
	/**
	 * @name operationAttenteInvite($pTypePaiement)
	 * @return array(OperationDetailVO) ou false en erreur
	 * @desc Retourne l'ensemble des des opérations du compte Invite non pointées
	 */
	public static function operationAttenteInvite($pTypePaiement) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"SELECT " 
				. CompteManager::CHAMP_COMPTE_LABEL . 
			"," . CompteManager::CHAMP_COMPTE_SOLDE .
			"," . OperationManager::CHAMP_OPERATION_MONTANT .
			"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT .
			"," . OperationManager::CHAMP_OPERATION_DATE .
			"," . OperationManager::CHAMP_OPERATION_LIBELLE .
			"," . OperationManager::CHAMP_OPERATION_ID .
			"," . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . 
			"," . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . 
			"," . OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_REMISE_CHEQUE .
			',' . RemiseChequeManager::CHAMP_REMISECHEQUE_NUMERO . "
		FROM " . OperationManager::TABLE_OPERATION . "
		JOIN " . CompteManager::TABLE_COMPTE . " ON " . CompteManager::CHAMP_COMPTE_ID . " = " . OperationManager::CHAMP_OPERATION_ID_COMPTE . "
		LEFT JOIN  " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . " 
			ON " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . "
		LEFT JOIN " . OperationRemiseChequeManager::TABLE_OPERATIONREMISECHEQUE . " 
			ON " .	OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_OPERATION . " = " . OperationManager::CHAMP_OPERATION_ID . "
			AND " . OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ETAT . " = 0
		LEFT JOIN " . RemiseChequeManager::TABLE_REMISECHEQUE . "
			ON " . OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_REMISE_CHEQUE . " = " . RemiseChequeManager::CHAMP_REMISECHEQUE_ID . "
		WHERE " . OperationManager::CHAMP_OPERATION_TYPE . " = 0 
			AND " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " in (1,2)
			AND " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = '" . StringUtils::securiser($pTypePaiement) . "'
			AND " . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = -3
		ORDER BY " . OperationManager::CHAMP_OPERATION_DATE . ";";
		
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
		
		$lListeOperationAttente = array();
		$lChampComplementaire = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			
			$lOpeId = NULL;
			
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				 if($lOpeId != $lLigne[OperationManager::CHAMP_OPERATION_ID]) {
				 	if(!is_null($lOpeId)) {
				 		$lOperationAttente->setOpeTypePaiementChampComplementaire($lChampComplementaire);
					 	$lListeOperationAttente[$lOpeId] = $lOperationAttente;
					}
				 	$lOpeId = $lLigne[OperationManager::CHAMP_OPERATION_ID];
				 	
				 	$lOperationAttente = new OperationAttenteAdherentVO (
						NULL,NULL,NULL,NULL,
						$lLigne[CompteManager::CHAMP_COMPTE_LABEL],
						$lLigne[CompteManager::CHAMP_COMPTE_SOLDE],
						$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
						$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT],
				 		NULL,
						$lLigne[OperationManager::CHAMP_OPERATION_DATE],
						$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
						$lLigne[OperationManager::CHAMP_OPERATION_ID],
						$lLigne[OperationRemiseChequeManager::CHAMP_OPERATIONREMISECHEQUE_ID_REMISE_CHEQUE],
						$lLigne[RemiseChequeManager::CHAMP_REMISECHEQUE_NUMERO]);
				 }	

				 if(!is_null($lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID])) {
				 	$lChampComplementaire[$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID]] = new ChampComplementaireDetailOperationVO(
						NULL,NULL,NULL,NULL,NULL,
						$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID],
						NULL,NULL,NULL,
						$lLigne[OperationManager::CHAMP_OPERATION_ID],
						$lLigne[OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR]);
				 }
			}
			$lOperationAttente->setOpeTypePaiementChampComplementaire($lChampComplementaire);
			$lListeOperationAttente[$lOpeId] = $lOperationAttente;
		} else {
			$lListeOperationAttente[0] = new OperationAttenteAdherentVO();
		}
		return $lListeOperationAttente;
	}
	
	/**
	* @name insert($pVo)
	* @param OperationVO
	* @return integer
	* @desc Insère une nouvelle ligne dans la table, à partir des informations de la OperationVO en paramètre (l'id sera automatiquement calculé par la BDD)
	*/
	public static function insert($pVo) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"INSERT INTO " . OperationManager::TABLE_OPERATION . "
				(" . OperationManager::CHAMP_OPERATION_ID . "
				," . OperationManager::CHAMP_OPERATION_ID_COMPTE . "
				," . OperationManager::CHAMP_OPERATION_MONTANT . "
				," . OperationManager::CHAMP_OPERATION_LIBELLE . "
				," . OperationManager::CHAMP_OPERATION_DATE . "
				," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . "
				," . OperationManager::CHAMP_OPERATION_TYPE . "
				," . OperationManager::CHAMP_OPERATION_DATE_MAJ . "
				," . OperationManager::CHAMP_OPERATION_ID_LOGIN . ")
			VALUES ";

		if(is_array($pVo)) {
			$lNbVO = count($pVo);
			$lI = 1;
			foreach($pVo as $lVo) {
				$lRequete .= "(NULL
				,'" . StringUtils::securiser( $lVo->getIdCompte() ) . "'
				,'" . StringUtils::securiser( $lVo->getMontant() ) . "'
				,'" . StringUtils::securiser( $lVo->getLibelle() ) . "'
				,'" . StringUtils::securiser( $lVo->getDate() ) . "'
				,'" . StringUtils::securiser( $lVo->getTypePaiement() ) . "'
				,'" . StringUtils::securiser( $lVo->getType() ) . "'
				,'" . StringUtils::securiser( $lVo->getDateMaj() ) . "'
				,'" . StringUtils::securiser( $lVo->getIdLogin() ) . "')";

				if($lNbVO == $lI) {
					$lRequete .= ";";
				} else {
					$lRequete .= ",";
				}
				$lI++;
			}
		} else{
			$lRequete .= "(NULL
				,'" . StringUtils::securiser( $pVo->getIdCompte() ) . "'
				,'" . StringUtils::securiser( $pVo->getMontant() ) . "'
				,'" . StringUtils::securiser( $pVo->getLibelle() ) . "'
				,'" . StringUtils::securiser( $pVo->getDate() ) . "'
				,'" . StringUtils::securiser( $pVo->getTypePaiement() ) . "'
				,'" . StringUtils::securiser( $pVo->getType() ) . "'
				,'" . StringUtils::securiser( $pVo->getDateMaj() ) . "'
				,'" . StringUtils::securiser( $pVo->getIdLogin() ) . "');";
		}

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		return Dbutils::executerRequeteInsertRetourId($lRequete);
	}

	/**
	* @name update($pVo)
	* @param OperationVO
	* @desc Met à jour la ligne de la table, correspondant à l'id du OperationVO, avec les informations du OperationVO
	*/
	public static function update($pVo) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete = 
			"UPDATE " . OperationManager::TABLE_OPERATION . "
			 SET
				 " . OperationManager::CHAMP_OPERATION_ID_COMPTE . " = '" . StringUtils::securiser( $pVo->getIdCompte() ) . "'
				," . OperationManager::CHAMP_OPERATION_MONTANT . " = '" . StringUtils::securiser( $pVo->getMontant() ) . "'
				," . OperationManager::CHAMP_OPERATION_LIBELLE . " = '" . StringUtils::securiser( $pVo->getLibelle() ) . "'
				," . OperationManager::CHAMP_OPERATION_DATE . " = '" . StringUtils::securiser( $pVo->getDate() ) . "'
				," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = '" . StringUtils::securiser( $pVo->getTypePaiement() ) . "'
				," . OperationManager::CHAMP_OPERATION_TYPE . " = '" . StringUtils::securiser( $pVo->getType() ) . "'
				," . OperationManager::CHAMP_OPERATION_DATE_MAJ . " = '" . StringUtils::securiser( $pVo->getDateMaj() ) . "'
				," . OperationManager::CHAMP_OPERATION_ID_LOGIN . " = '" . StringUtils::securiser( $pVo->getIdLogin() ) . "'
			 WHERE " . OperationManager::CHAMP_OPERATION_ID . " = '" . StringUtils::securiser( $pVo->getId() ) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		Dbutils::executerRequete($lRequete);
		return $pVo->getId();
	}
	
	/**
	 * @name validerByArray($pId)
	 * @param array(IdOperationVO)
	 * @desc Met à jour les lignes au type 1
	 */
	public static function validerByArray($pId) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
	
		$lRequete =
		"UPDATE " . OperationManager::TABLE_OPERATION . "
		SET
		" . OperationManager::CHAMP_OPERATION_TYPE . " = '1'
		," . OperationManager::CHAMP_OPERATION_DATE_MAJ . " = 'now()'
		WHERE " . OperationManager::CHAMP_OPERATION_ID . " in ( '" .  str_replace(",", "','", StringUtils::securiser( implode(",", $pId) ) ) . "')";
				
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		return Dbutils::executerRequete($lRequete);
	}

	/**
	* @name delete($pId)
	* @param integer
	* @desc Supprime la ligne de la table correspondant à l'id en paramètre
	*/
	public static function delete($pId) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete = "DELETE FROM " . OperationManager::TABLE_OPERATION . "
			WHERE " . OperationManager::CHAMP_OPERATION_ID . " = '" . StringUtils::securiser($pId) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		return Dbutils::executerRequete($lRequete);
	}
	
	/**
	 * @name selectOperationReservationAdherent($pIdMarche, $pStatutAdherent)
	 * @return array(OperationDetailVO) ou false en erreur
	 * @desc Retourne l'ensemble des opérations de réservations sur un marché selon un type d'adhérent
	 */
	public static function selectOperationReservationAdherent($pIdMarche, $pStatutAdherent) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		
		$lRequete =
		"SELECT "  
				. OperationManager::CHAMP_OPERATION_ID .
			"," . OperationManager::CHAMP_OPERATION_ID_COMPTE .
			"," . OperationManager::CHAMP_OPERATION_MONTANT .
			"," . OperationManager::CHAMP_OPERATION_LIBELLE .
			"," . OperationManager::CHAMP_OPERATION_DATE .
			"," . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT .
			"," . OperationManager::CHAMP_OPERATION_TYPE .
			"," . OperationManager::CHAMP_OPERATION_DATE_MAJ .
			"," . OperationManager::CHAMP_OPERATION_ID_LOGIN . "
		FROM " .  OperationManager::TABLE_OPERATION . "
		JOIN " . OperationChampComplementaireManager::TABLE_OPERATIONCHAMPCOMPLEMENTAIRE . "
			ON " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_OPE_ID . " = " . OperationManager::CHAMP_OPERATION_ID . "
			AND " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_CHCP_ID . " = 1 
			AND " . OperationChampComplementaireManager::CHAMP_OPERATIONCHAMPCOMPLEMENTAIRE_VALEUR . " = '" . StringUtils::securiser($pIdMarche) . "'
		JOIN " . CompteManager::TABLE_COMPTE . " 
			ON " . CompteManager::CHAMP_COMPTE_ID . " = " . OperationManager::CHAMP_OPERATION_ID_COMPTE . "
		JOIN " . AdherentManager::TABLE_ADHERENT . "
			ON " . AdherentManager::CHAMP_ADHERENT_ID . " = " . CompteManager::CHAMP_COMPTE_ID_ADHERENT_PRINCIPAL . "
			AND " . AdherentManager::CHAMP_ADHERENT_ETAT . " = '" . StringUtils::securiser($pStatutAdherent) . "'
		WHERE " . OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT . " = 0 ;";
				
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
		
		$lListeOperation = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeOperation,
				OperationManager::remplirOperation(
				$lLigne[OperationManager::CHAMP_OPERATION_ID],
				$lLigne[OperationManager::CHAMP_OPERATION_ID_COMPTE],
				$lLigne[OperationManager::CHAMP_OPERATION_MONTANT],
				$lLigne[OperationManager::CHAMP_OPERATION_LIBELLE],
				$lLigne[OperationManager::CHAMP_OPERATION_DATE],
				$lLigne[OperationManager::CHAMP_OPERATION_TYPE_PAIEMENT],
				$lLigne[OperationManager::CHAMP_OPERATION_TYPE],
				$lLigne[OperationManager::CHAMP_OPERATION_DATE_MAJ],
				$lLigne[OperationManager::CHAMP_OPERATION_ID_LOGIN]));
			}
		} else {
			$lListeOperation[0] = new OperationVO();
		}
		return $lListeOperation;
	}
}
?>