<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 18/07/2011
// Fichier : HistoriqueStockManager.php
//
// Description : Classe de gestion des HistoriqueStock
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_UTILS . "DbUtils.php");
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php");
include_once(CHEMIN_CLASSES_VO . "HistoriqueStockVO.php");
include_once(CHEMIN_CLASSES_VO . "DetailReservationVO.php");
include_once(CHEMIN_CLASSES_MANAGERS . "DetailCommandeManager.php");

define("TABLE_HISTORIQUESTOCK", MYSQL_DB_PREFIXE ."hsto_historique_stock");
/**
 * @name HistoriqueStockManager
 * @author Julien PIERRE
 * @since 18/07/2011
 * 
 * @desc Classe permettant l'accès aux données des HistoriqueStock
 */
class HistoriqueStockManager
{
	const TABLE_HISTORIQUESTOCK = TABLE_HISTORIQUESTOCK;
	const CHAMP_HISTORIQUESTOCK_ID = "hsto_id";
	const CHAMP_HISTORIQUESTOCK_STO_ID = "hsto_sto_id";
	const CHAMP_HISTORIQUESTOCK_DATE = "hsto_date";
	const CHAMP_HISTORIQUESTOCK_QUANTITE = "hsto_quantite";
	const CHAMP_HISTORIQUESTOCK_TYPE = "hsto_type";
	const CHAMP_HISTORIQUESTOCK_ID_COMPTE = "hsto_id_compte";
	const CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE = "hsto_id_detail_commande";
	const CHAMP_HISTORIQUESTOCK_ID_MODELE_LOT = "hsto_id_modele_lot";
	const CHAMP_HISTORIQUESTOCK_ID_OPERATION = "hsto_id_operation";
	const CHAMP_HISTORIQUESTOCK_ID_CONNEXION = "hsto_id_connexion";

	/**
	* @name select($pId)
	* @param integer
	* @return HistoriqueStockVO
	* @desc Récupère la ligne correspondant à l'id en paramètre, créé une HistoriqueStockVO contenant les informations et la renvoie
	*/
	public static function select($pId) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"SELECT "
			    . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_STO_ID . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_DATE . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_QUANTITE . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_TYPE . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_COMPTE . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_MODELE_LOT . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_OPERATION . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_CONNEXION . "
			FROM " . HistoriqueStockManager::TABLE_HISTORIQUESTOCK . " 
			WHERE " . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID . " = '" . StringUtils::securiser($pId) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		if( mysqli_num_rows($lSql) > 0 ) {
			$lLigne = mysqli_fetch_assoc($lSql);
			return HistoriqueStockManager::remplirHistoriqueStock(
				$pId,
				$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_STO_ID],
				$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_DATE],
				$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_QUANTITE],
				$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_TYPE],
				$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_COMPTE],
				$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE],
				$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_MODELE_LOT],
				$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_OPERATION],
				$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_CONNEXION]);
		} else {
			return new HistoriqueStockVO();
		}
	}

	/**
	* @name selectAll()
	* @return array(HistoriqueStockVO)
	* @desc Récupères toutes les lignes de la table et les renvoie sous forme d'une collection de HistoriqueStockVO
	*/
	public static function selectAll() {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
			"SELECT "
			    . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_STO_ID . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_DATE . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_QUANTITE . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_TYPE . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_COMPTE . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_MODELE_LOT . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_OPERATION . 
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_CONNEXION . "
			FROM " . HistoriqueStockManager::TABLE_HISTORIQUESTOCK;

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		$lListeHistoriqueStock = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeHistoriqueStock,
					HistoriqueStockManager::remplirHistoriqueStock(
					$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID],
					$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_STO_ID],
					$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_DATE],
					$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_QUANTITE],
					$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_TYPE],
					$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_COMPTE],
					$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE],
					$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_MODELE_LOT],
					$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_OPERATION],
					$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_CONNEXION]));
			}
		} else {
			$lListeHistoriqueStock[0] = new HistoriqueStockVO();
		}
		return $lListeHistoriqueStock;
	}
	
	/**
	 * @name selectReservation($pIdOperation, &$pReservation)
	 * @param integer
	 * @param ReservationVO
	 * @desc Récupère le détail d'une réservation (achetée) à partir de l'historique du stock
	 */
	public static function selectReservation($pIdOperation, &$pReservation) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
			"SELECT 
				   hStock." .HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_STO_ID .
				", hStock." . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE .
				", hStock." . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_QUANTITE .
				"," . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT .
				
			" FROM " . HistoriqueStockManager::TABLE_HISTORIQUESTOCK . " as hStock " .
			"INNER JOIN (" .
				"SELECT max(" . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_DATE . ") as date" .
				"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE .
				" FROM " . HistoriqueStockManager::TABLE_HISTORIQUESTOCK .
				" WHERE " . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_OPERATION . " = " . $pIdOperation .
				" AND " . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_TYPE . " = 0 " .
				" GROUP BY " . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE .
			") gp " .
			" ON gp.date = hStock.hsto_date AND gp." . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE . " = hStock." .HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE .
			" INNER JOIN " . DetailCommandeManager::TABLE_DETAILCOMMANDE .  
			" ON " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . " =  hStock." . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE .
			" WHERE " . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_OPERATION . " = " . $pIdOperation .
			" AND "  . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_TYPE . " = 0;";
		
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
		
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				$lDetailReservation = new DetailReservationVO();
				$lDetailReservation->getId()->setIdStock($lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_STO_ID]);
				$lDetailReservation->setIdDetailCommande($lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE]);
				$lDetailReservation->setQuantite($lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_QUANTITE]);
				$lDetailReservation->setIdProduit($lLigne[DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT]);
					
				$pReservation->addDetailReservation($lDetailReservation);
			}
		}
	}

	/**
	* @name recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri )
	* @param string nom de la table
	* @param string Le type de critère de recherche
	* @param array(string) champs à récupérer dans la table
	* @param array(array(string, object)) Dictionnaire(champ, valeur)) contenant les champs à filtrer ainsi que la valeur du filtre
	* @param array(array(string, string)) Dictionnaire(champ, sens) contenant les tris à appliquer
	* @return array(HistoriqueStockVO)
	* @desc Récupères les lignes de la table selon le critère de recherche puis trie et renvoie la liste de résultat sous forme d'une collection de HistoriqueStockVO
	*/
	public static function recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri ) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		// Préparation de la requète
		$lChamps = array( 
			    HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID .
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_STO_ID .
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_DATE .
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_QUANTITE .
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_TYPE .
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_COMPTE .
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE .
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_MODELE_LOT .
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_OPERATION .
			"," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_CONNEXION		);

		// Préparation de la requète de recherche
		$lRequete = DbUtils::prepareRequeteRecherche(HistoriqueStockManager::TABLE_HISTORIQUESTOCK, $lChamps, $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri);

		$lListeHistoriqueStock = array();

		if($lRequete !== false) {

			$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
			$lSql = Dbutils::executerRequete($lRequete);

			if( mysqli_num_rows($lSql) > 0 ) {

				while ( $lLigne = mysqli_fetch_assoc($lSql) ) {

					array_push($lListeHistoriqueStock,
						HistoriqueStockManager::remplirHistoriqueStock(
						$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID],
						$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_STO_ID],
						$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_DATE],
						$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_QUANTITE],
						$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_TYPE],
						$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_COMPTE],
						$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE],
						$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_MODELE_LOT],
						$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_OPERATION],
						$lLigne[HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_CONNEXION]));
				}
			} else {
				$lListeHistoriqueStock[0] = new HistoriqueStockVO();
			}

			return $lListeHistoriqueStock;
		}

		$lListeHistoriqueStock[0] = new HistoriqueStockVO();
		return $lListeHistoriqueStock;
	}

	/**
	* @name remplirHistoriqueStock($pId, $pStoId, $pDate, $pQuantite, $pType, $pIdCompte, $pIdDetailCommande, $pIdModeleLot, $pIdOperation, $pIdConnexion)
	* @param int(11)
	* @param int(11)
	* @param datetime
	* @param decimal(10,2)
	* @param int(11)
	* @param int(11)
	* @param int(11)
	* @param int(11)
	* @param int(11)
	* @param int(11)
	* @return HistoriqueStockVO
	* @desc Retourne une HistoriqueStockVO remplie
	*/
	private static function remplirHistoriqueStock($pId, $pStoId, $pDate, $pQuantite, $pType, $pIdCompte, $pIdDetailCommande, $pIdModeleLot, $pIdOperation, $pIdConnexion) {
		$lHistoriqueStock = new HistoriqueStockVO();
		$lHistoriqueStock->setId($pId);
		$lHistoriqueStock->setStoId($pStoId);
		$lHistoriqueStock->setDate($pDate);
		$lHistoriqueStock->setQuantite($pQuantite);
		$lHistoriqueStock->setType($pType);
		$lHistoriqueStock->setIdCompte($pIdCompte);
		$lHistoriqueStock->setIdDetailCommande($pIdDetailCommande);
		$lHistoriqueStock->setIdModeleLot($pIdModeleLot);
		$lHistoriqueStock->setIdOperation($pIdOperation);
		$lHistoriqueStock->setIdConnexion($pIdConnexion);
		return $lHistoriqueStock;
	}

	/**
	* @name insert($pVo)
	* @param HistoriqueStockVO
	* @return integer
	* @desc Insère une nouvelle ligne dans la table, à partir des informations de la HistoriqueStockVO en paramètre (l'id sera automatiquement calculé par la BDD)
	*/
	public static function insert($pVo) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"INSERT INTO " . HistoriqueStockManager::TABLE_HISTORIQUESTOCK . "
				(" . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID . "
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_STO_ID . "
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_DATE . "
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_QUANTITE . "
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_TYPE . "
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_COMPTE . "
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE . "
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_MODELE_LOT . "
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_OPERATION . "
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_CONNEXION . ")
			VALUES ";

		if(is_array($pVo)) {
			$lNbVO = count($pVo);
			$lI = 1;
			foreach($pVo as $lVo) {
				$lRequete .= "(NULL
				,'" . StringUtils::securiser( $lVo->getStoId() ) . "'
				,'" . StringUtils::securiser( $lVo->getDate() ) . "'
				,'" . StringUtils::securiser( $lVo->getQuantite() ) . "'
				,'" . StringUtils::securiser( $lVo->getType() ) . "'
				,'" . StringUtils::securiser( $lVo->getIdCompte() ) . "'
				,'" . StringUtils::securiser( $lVo->getIdDetailCommande() ) . "'
				,'" . StringUtils::securiser( $lVo->getIdModeleLot() ) . "'
				,'" . StringUtils::securiser( $lVo->getIdOperation() ) . "'
				,'" . StringUtils::securiser( $lVo->getIdConnexion() ) . "')";

				if($lNbVO == $lI) {
					$lRequete .= ";";
				} else {
					$lRequete .= ",";
				}
				$lI++;
			}
		} else{
			$lRequete .= "(NULL
				,'" . StringUtils::securiser( $pVo->getStoId() ) . "'
				,'" . StringUtils::securiser( $pVo->getDate() ) . "'
				,'" . StringUtils::securiser( $pVo->getQuantite() ) . "'
				,'" . StringUtils::securiser( $pVo->getType() ) . "'
				,'" . StringUtils::securiser( $pVo->getIdCompte() ) . "'
				,'" . StringUtils::securiser( $pVo->getIdDetailCommande() ) . "'
				,'" . StringUtils::securiser( $pVo->getIdModeleLot() ) . "'
				,'" . StringUtils::securiser( $pVo->getIdOperation() ) . "'
				,'" . StringUtils::securiser( $pVo->getIdConnexion() ) . "');";
		}

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		return Dbutils::executerRequeteInsertRetourId($lRequete);
	}

	/**
	* @name update($pVo)
	* @param HistoriqueStockVO
	* @desc Met à jour la ligne de la table, correspondant à l'id du HistoriqueStockVO, avec les informations du HistoriqueStockVO
	*/
	public static function update($pVo) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete = 
			"UPDATE " . HistoriqueStockManager::TABLE_HISTORIQUESTOCK . "
			 SET
				 " . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_STO_ID . " = '" . StringUtils::securiser( $pVo->getStoId() ) . "'
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_DATE . " = '" . StringUtils::securiser( $pVo->getDate() ) . "'
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_QUANTITE . " = '" . StringUtils::securiser( $pVo->getQuantite() ) . "'
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_TYPE . " = '" . StringUtils::securiser( $pVo->getType() ) . "'
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_COMPTE . " = '" . StringUtils::securiser( $pVo->getIdCompte() ) . "'
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_DETAIL_COMMANDE . " = '" . StringUtils::securiser( $pVo->getIdDetailCommande() ) . "'
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_MODELE_LOT . " = '" . StringUtils::securiser( $pVo->getIdModeleLot() ) . "'
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_OPERATION . " = '" . StringUtils::securiser( $pVo->getIdOperation() ) . "'
				," . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID_CONNEXION . " = '" . StringUtils::securiser( $pVo->getIdConnexion() ) . "'
			 WHERE " . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID . " = '" . StringUtils::securiser( $pVo->getId() ) . "'";

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

		$lRequete = "DELETE FROM " . HistoriqueStockManager::TABLE_HISTORIQUESTOCK . "
			WHERE " . HistoriqueStockManager::CHAMP_HISTORIQUESTOCK_ID . " = '" . StringUtils::securiser($pId) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		return Dbutils::executerRequete($lRequete);
	}
}
?>