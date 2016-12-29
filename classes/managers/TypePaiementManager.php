<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 15/01/2011
// Fichier : TypePaiementManager.php
//
// Description : Classe de gestion des TypePaiement
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_UTILS . "DbUtils.php");
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php");
include_once(CHEMIN_CLASSES_VO . "TypePaiementVO.php");
include_once(CHEMIN_CLASSES_VO . "TypePaiementDetailVO.php");
include_once(CHEMIN_CLASSES_MANAGERS . "TypePaiementChampComplementaireManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "ChampComplementaireManager.php");

define("TABLE_TYPEPAIEMENT", MYSQL_DB_PREFIXE . "tpp_type_paiement");
/**
 * @name TypePaiementManager
 * @author Julien PIERRE
 * @since 15/01/2011
 * 
 * @desc Classe permettant l'accès aux données des TypePaiement
 */
class TypePaiementManager
{
	const TABLE_TYPEPAIEMENT = TABLE_TYPEPAIEMENT;
	const CHAMP_TYPEPAIEMENT_ID = "tpp_id";
	const CHAMP_TYPEPAIEMENT_TYPE = "tpp_type";
	const CHAMP_TYPEPAIEMENT_CHAMP_COMPLEMENTAIRE = "tpp_champ_complementaire";
	const CHAMP_TYPEPAIEMENT_VISIBLE = "tpp_visible";

	/**
	* @name select($pId)
	* @param integer
	* @return TypePaiementVO
	* @desc Récupère la ligne correspondant à l'id en paramètre, créé une TypePaiementVO contenant les informations et la renvoie
	*/
	public static function select($pId) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"SELECT "
			    . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID . 
			"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . 
			"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_CHAMP_COMPLEMENTAIRE . 
			"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_VISIBLE . "
			FROM " . TypePaiementManager::TABLE_TYPEPAIEMENT . " 
			WHERE " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID . " = '" . StringUtils::securiser($pId) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		if( mysqli_num_rows($lSql) > 0 ) {
			$lLigne = mysqli_fetch_assoc($lSql);
			return TypePaiementManager::remplirTypePaiement(
				$pId,
				$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE],
				$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_CHAMP_COMPLEMENTAIRE],
				$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_VISIBLE]);
		} else {
			return new TypePaiementVO();
		}
	}

	/**
	* @name selectAll()
	* @return array(TypePaiementVO)
	* @desc Récupères toutes les lignes de la table et les renvoie sous forme d'une collection de TypePaiementVO
	*/
	public static function selectAll() {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
			"SELECT "
			    . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID . 
			"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . 
			"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_CHAMP_COMPLEMENTAIRE . 
			"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_VISIBLE . "
			FROM " . TypePaiementManager::TABLE_TYPEPAIEMENT;

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		$lListeTypePaiement = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeTypePaiement,
					TypePaiementManager::remplirTypePaiement(
					$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_ID],
					$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE],
					$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_CHAMP_COMPLEMENTAIRE],
					$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_VISIBLE]));
			}
		} else {
			$lListeTypePaiement[0] = new TypePaiementVO();
		}
		return $lListeTypePaiement;
	}

	/**
	 * @name selectDetail($pTypePaiement, $pVisible)
	 * @return array(TypePaiementDetailVO)
	 * @desc Récupères toutes les lignes de la table et les renvoie sous forme d'une collection de TypePaiementDetailVO
	 */
	public static function selectDetail($pTypePaiement = NULL, $pVisible = NULL) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
		"SELECT "
					. TypePaiementManager::CHAMP_TYPEPAIEMENT_ID .
				"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE .
				"," . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID .
				"," . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_LABEL . 
				"," . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_OBLIGATOIRE . "
		FROM " . TypePaiementManager::TABLE_TYPEPAIEMENT . "
		LEFT JOIN " . TypePaiementChampComplementaireManager::TABLE_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE . "
			ON " . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_TPP_ID . " = " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID . "
				AND " . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ETAT . " = 0 ";
		if(!is_null($pVisible)) {	
			$lRequete .= " AND " . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_VISIBLE . " = 1 ";
		}		
		$lRequete .= " LEFT  JOIN " . ChampComplementaireManager::TABLE_CHAMPCOMPLEMENTAIRE . "
			ON " . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_CHCP_ID . " = " . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID . "
				AND " . ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ETAT . " = 0 
		WHERE 1 = 1 ";
		if(!is_null($pVisible)) {		
			$lRequete .= " AND " . TypePaiementManager::CHAMP_TYPEPAIEMENT_VISIBLE . " = 1 ";
		}
		if(!is_null($pTypePaiement)) {
			$lRequete .= " AND " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID . " = '" . StringUtils::securiser($pTypePaiement) . "' ";
		}		
		$lRequete .= " ORDER BY " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID . " ASC, " . TypePaiementChampComplementaireManager::CHAMP_TYPEPAIEMENTCHAMPCOMPLEMENTAIRE_ORDRE . " ASC;";
	
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
	
		$lListeTypePaiement = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			$lLigne = mysqli_fetch_assoc($lSql);

			$lTppId = $lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_ID];
			
			$lTypePaiement = TypePaiementManager::remplirTypePaiementVisibleEntete(
			$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_ID],
			$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE]);
			
			if(!is_null($lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID])) {
				$lTypePaiement->addChampComplementaire( ChampComplementaireManager::remplirChampComplementaire(
					$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID],
					$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_LABEL],
					$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_OBLIGATOIRE],
					NULL));
			}
			
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				 if($lTppId != $lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_ID]) {
				 	$lListeTypePaiement[$lTppId] = $lTypePaiement;
				 	$lTppId = $lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_ID];
				 	
				 	$lTypePaiement = TypePaiementManager::remplirTypePaiementVisibleEntete(
				 			$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_ID],
				 			$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE]);
				 }	

				 if(!is_null($lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID])) {
				 	$lTypePaiement->addChampComplementaire( ChampComplementaireManager::remplirChampComplementaire(
				 			$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_ID],
				 			$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_LABEL],
				 			$lLigne[ChampComplementaireManager::CHAMP_CHAMPCOMPLEMENTAIRE_OBLIGATOIRE],
				 			NULL));
				 }
			}
			if(!is_null($pTypePaiement)) {
				return $lTypePaiement;
			} else {
				$lListeTypePaiement[$lTppId] = $lTypePaiement;
			}
		} else {
			if(!is_null($pTypePaiement)) {
				return new TypePaiementDetailVO();
			} else {
				$lListeTypePaiement[0] = new TypePaiementDetailVO();
			}
		}
		return $lListeTypePaiement;
	}
	
	/**
	* @name recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri )
	* @param string nom de la table
	* @param string Le type de critère de recherche
	* @param array(string) champs à récupérer dans la table
	* @param array(array(string, object)) Dictionnaire(champ, valeur)) contenant les champs à filtrer ainsi que la valeur du filtre
	* @param array(array(string, string)) Dictionnaire(champ, sens) contenant les tris à appliquer
	* @return array(TypePaiementVO)
	* @desc Récupères les lignes de la table selon le critère de recherche puis trie et renvoie la liste de résultat sous forme d'une collection de TypePaiementVO
	*/
	public static function recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri ) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		// Préparation de la requète
		$lChamps = array( 
			    TypePaiementManager::CHAMP_TYPEPAIEMENT_ID .
			"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE .
			"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_CHAMP_COMPLEMENTAIRE .
			"," . TypePaiementManager::CHAMP_TYPEPAIEMENT_VISIBLE		);

		// Préparation de la requète de recherche
		$lRequete = DbUtils::prepareRequeteRecherche(TypePaiementManager::TABLE_TYPEPAIEMENT, $lChamps, $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri);

		$lListeTypePaiement = array();

		if($lRequete !== false) {

			$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
			$lSql = Dbutils::executerRequete($lRequete);

			if( mysqli_num_rows($lSql) > 0 ) {

				while ( $lLigne = mysqli_fetch_assoc($lSql) ) {

					array_push($lListeTypePaiement,
						TypePaiementManager::remplirTypePaiement(
						$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_ID],
						$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE],
						$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_CHAMP_COMPLEMENTAIRE],
						$lLigne[TypePaiementManager::CHAMP_TYPEPAIEMENT_VISIBLE]));
				}
			} else {
				$lListeTypePaiement[0] = new TypePaiementVO();
			}

			return $lListeTypePaiement;
		}

		$lListeTypePaiement[0] = new TypePaiementVO();
		return $lListeTypePaiement;
	}

	/**
	* @name remplirTypePaiement($pId, $pType, $pChampComplementaire, $pVisible)
	* @param int(11)
	* @param varchar(100)
	* @param tinyint(4)
	* @param tinyint(1)
	* @return TypePaiementVO
	* @desc Retourne une TypePaiementVO remplie
	*/
	private static function remplirTypePaiement($pId, $pType, $pChampComplementaire, $pVisible) {
		$lTypePaiement = new TypePaiementVO();
		$lTypePaiement->setId($pId);
		$lTypePaiement->setType($pType);
		$lTypePaiement->setChampComplementaire($pChampComplementaire);
		$lTypePaiement->setVisible($pVisible);
		return $lTypePaiement;
	}
	
	/**
	 * @name remplirTypePaiementVisibleEntete($pId, $pType)
	 * @param int(11)
	 * @param varchar(100)
	 * @return TypePaiementVO
	 * @desc Retourne une TypePaiementVO remplie
	 */
	private static function remplirTypePaiementVisibleEntete($pId, $pType) {
		$lTypePaiement = new TypePaiementDetailVO();
		$lTypePaiement->setId($pId);
		$lTypePaiement->setType($pType);
		return $lTypePaiement;
	}

	/**
	* @name insert($pVo)
	* @param TypePaiementVO
	* @return integer
	* @desc Insère une nouvelle ligne dans la table, à partir des informations de la TypePaiementVO en paramètre (l'id sera automatiquement calculé par la BDD)
	*/
	public static function insert($pVo) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"INSERT INTO " . TypePaiementManager::TABLE_TYPEPAIEMENT . "
				(" . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID . "
				," . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . "
				," . TypePaiementManager::CHAMP_TYPEPAIEMENT_CHAMP_COMPLEMENTAIRE . "
				," . TypePaiementManager::CHAMP_TYPEPAIEMENT_VISIBLE . ")
			VALUES ";

		if(is_array($pVo)) {
			$lNbVO = count($pVo);
			$lI = 1;
			foreach($pVo as $lVo) {
				$lRequete .= "(NULL
				,'" . StringUtils::securiser( $lVo->getType() ) . "'
				,'" . StringUtils::securiser( $lVo->getChampComplementaire() ) . "'
				,'" . StringUtils::securiser( $lVo->getVisible() ) . "')";

				if($lNbVO == $lI) {
					$lRequete .= ";";
				} else {
					$lRequete .= ",";
				}
				$lI++;
			}
		} else{
			$lRequete .= "(NULL
				,'" . StringUtils::securiser( $pVo->getType() ) . "'
				,'" . StringUtils::securiser( $pVo->getChampComplementaire() ) . "'
				,'" . StringUtils::securiser( $pVo->getVisible() ) . "');";
		}

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		return Dbutils::executerRequeteInsertRetourId($lRequete);
	}

	/**
	* @name update($pVo)
	* @param TypePaiementVO
	* @desc Met à jour la ligne de la table, correspondant à l'id du TypePaiementVO, avec les informations du TypePaiementVO
	*/
	public static function update($pVo) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete = 
			"UPDATE " . TypePaiementManager::TABLE_TYPEPAIEMENT . "
			 SET
				 " . TypePaiementManager::CHAMP_TYPEPAIEMENT_TYPE . " = '" . StringUtils::securiser( $pVo->getType() ) . "'
				," . TypePaiementManager::CHAMP_TYPEPAIEMENT_CHAMP_COMPLEMENTAIRE . " = '" . StringUtils::securiser( $pVo->getChampComplementaire() ) . "'
				," . TypePaiementManager::CHAMP_TYPEPAIEMENT_VISIBLE . " = '" . StringUtils::securiser( $pVo->getVisible() ) . "'
			 WHERE " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID . " = '" . StringUtils::securiser( $pVo->getId() ) . "'";

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

		$lRequete = "DELETE FROM " . TypePaiementManager::TABLE_TYPEPAIEMENT . "
			WHERE " . TypePaiementManager::CHAMP_TYPEPAIEMENT_ID . " = '" . StringUtils::securiser($pId) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		return Dbutils::executerRequete($lRequete);
	}
}
?>