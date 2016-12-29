<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 09/08/2013
// Fichier : StockManager.php
//
// Description : Classe de gestion des Stock
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_UTILS . "DbUtils.php");
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php");
include_once(CHEMIN_CLASSES_VO . "StockVO.php");

define("TABLE_STOCK", MYSQL_DB_PREFIXE ."sto_stock");
/**
 * @name StockManager
 * @author Julien PIERRE
 * @since 09/08/2013
 * 
 * @desc Classe permettant l'accès aux données des Stock
 */
class StockManager
{
	const TABLE_STOCK = TABLE_STOCK;
	const CHAMP_STOCK_ID = "sto_id";
	const CHAMP_STOCK_DATE = "sto_date";
	const CHAMP_STOCK_QUANTITE = "sto_quantite";
	const CHAMP_STOCK_TYPE = "sto_type";
	const CHAMP_STOCK_ID_COMPTE = "sto_id_compte";
	const CHAMP_STOCK_ID_DETAIL_COMMANDE = "sto_id_detail_commande";
	const CHAMP_STOCK_ID_MODELE_LOT = "sto_id_modele_lot";
	const CHAMP_STOCK_ID_OPERATION = "sto_id_operation";
	const CHAMP_STOCK_ID_NOM_PRODUIT = "sto_id_nom_produit";
	const CHAMP_STOCK_UNITE = "sto_unite";

	/**
	* @name select($pId)
	* @param integer
	* @return StockVO
	* @desc Récupère la ligne correspondant à l'id en paramètre, créé une StockVO contenant les informations et la renvoie
	*/
	public static function select($pId) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"SELECT "
			    . StockManager::CHAMP_STOCK_ID . 
			"," . StockManager::CHAMP_STOCK_DATE . 
			"," . StockManager::CHAMP_STOCK_QUANTITE . 
			"," . StockManager::CHAMP_STOCK_TYPE . 
			"," . StockManager::CHAMP_STOCK_ID_COMPTE . 
			"," . StockManager::CHAMP_STOCK_ID_DETAIL_COMMANDE . 
			"," . StockManager::CHAMP_STOCK_ID_MODELE_LOT . 
			"," . StockManager::CHAMP_STOCK_ID_OPERATION . 
			"," . StockManager::CHAMP_STOCK_ID_NOM_PRODUIT . 
			"," . StockManager::CHAMP_STOCK_UNITE . "
			FROM " . StockManager::TABLE_STOCK . " 
			WHERE " . StockManager::CHAMP_STOCK_ID . " = '" . StringUtils::securiser($pId) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		if( mysqli_num_rows($lSql) > 0 ) {
			$lLigne = mysqli_fetch_assoc($lSql);
			return StockManager::remplirStock(
				$pId,
				$lLigne[StockManager::CHAMP_STOCK_DATE],
				$lLigne[StockManager::CHAMP_STOCK_QUANTITE],
				$lLigne[StockManager::CHAMP_STOCK_TYPE],
				$lLigne[StockManager::CHAMP_STOCK_ID_COMPTE],
				$lLigne[StockManager::CHAMP_STOCK_ID_DETAIL_COMMANDE],
				$lLigne[StockManager::CHAMP_STOCK_ID_MODELE_LOT],
				$lLigne[StockManager::CHAMP_STOCK_ID_OPERATION],
				$lLigne[StockManager::CHAMP_STOCK_ID_NOM_PRODUIT],
				$lLigne[StockManager::CHAMP_STOCK_UNITE]);
		} else {
			return new StockVO();
		}
	}

	/**
	* @name selectAll()
	* @return array(StockVO)
	* @desc Récupères toutes les lignes de la table et les renvoie sous forme d'une collection de StockVO
	*/
	public static function selectAll() {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
			"SELECT "
			    . StockManager::CHAMP_STOCK_ID . 
			"," . StockManager::CHAMP_STOCK_DATE . 
			"," . StockManager::CHAMP_STOCK_QUANTITE . 
			"," . StockManager::CHAMP_STOCK_TYPE . 
			"," . StockManager::CHAMP_STOCK_ID_COMPTE . 
			"," . StockManager::CHAMP_STOCK_ID_DETAIL_COMMANDE . 
			"," . StockManager::CHAMP_STOCK_ID_MODELE_LOT . 
			"," . StockManager::CHAMP_STOCK_ID_OPERATION . 
			"," . StockManager::CHAMP_STOCK_ID_NOM_PRODUIT . 
			"," . StockManager::CHAMP_STOCK_UNITE . "
			FROM " . StockManager::TABLE_STOCK;

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		$lListeStock = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeStock,
					StockManager::remplirStock(
					$lLigne[StockManager::CHAMP_STOCK_ID],
					$lLigne[StockManager::CHAMP_STOCK_DATE],
					$lLigne[StockManager::CHAMP_STOCK_QUANTITE],
					$lLigne[StockManager::CHAMP_STOCK_TYPE],
					$lLigne[StockManager::CHAMP_STOCK_ID_COMPTE],
					$lLigne[StockManager::CHAMP_STOCK_ID_DETAIL_COMMANDE],
					$lLigne[StockManager::CHAMP_STOCK_ID_MODELE_LOT],
					$lLigne[StockManager::CHAMP_STOCK_ID_OPERATION],
					$lLigne[StockManager::CHAMP_STOCK_ID_NOM_PRODUIT],
					$lLigne[StockManager::CHAMP_STOCK_UNITE]));
			}
		} else {
			$lListeStock[0] = new StockVO();
		}
		return $lListeStock;
	}

	/**
	* @name recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri )
	* @param string nom de la table
	* @param string Le type de critère de recherche
	* @param array(string) champs à récupérer dans la table
	* @param array(array(string, object)) Dictionnaire(champ, valeur)) contenant les champs à filtrer ainsi que la valeur du filtre
	* @param array(array(string, string)) Dictionnaire(champ, sens) contenant les tris à appliquer
	* @return array(StockVO)
	* @desc Récupères les lignes de la table selon le critère de recherche puis trie et renvoie la liste de résultat sous forme d'une collection de StockVO
	*/
	public static function recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri ) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		// Préparation de la requète
		$lChamps = array( 
			    StockManager::CHAMP_STOCK_ID .
			"," . StockManager::CHAMP_STOCK_DATE .
			"," . StockManager::CHAMP_STOCK_QUANTITE .
			"," . StockManager::CHAMP_STOCK_TYPE .
			"," . StockManager::CHAMP_STOCK_ID_COMPTE .
			"," . StockManager::CHAMP_STOCK_ID_DETAIL_COMMANDE .
			"," . StockManager::CHAMP_STOCK_ID_MODELE_LOT .
			"," . StockManager::CHAMP_STOCK_ID_OPERATION .
			"," . StockManager::CHAMP_STOCK_ID_NOM_PRODUIT .
			"," . StockManager::CHAMP_STOCK_UNITE		);

		// Préparation de la requète de recherche
		$lRequete = DbUtils::prepareRequeteRecherche(StockManager::TABLE_STOCK, $lChamps, $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri);

		$lListeStock = array();

		if($lRequete !== false) {

			$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
			$lSql = Dbutils::executerRequete($lRequete);

			if( mysqli_num_rows($lSql) > 0 ) {

				while ( $lLigne = mysqli_fetch_assoc($lSql) ) {

					array_push($lListeStock,
						StockManager::remplirStock(
						$lLigne[StockManager::CHAMP_STOCK_ID],
						$lLigne[StockManager::CHAMP_STOCK_DATE],
						$lLigne[StockManager::CHAMP_STOCK_QUANTITE],
						$lLigne[StockManager::CHAMP_STOCK_TYPE],
						$lLigne[StockManager::CHAMP_STOCK_ID_COMPTE],
						$lLigne[StockManager::CHAMP_STOCK_ID_DETAIL_COMMANDE],
						$lLigne[StockManager::CHAMP_STOCK_ID_MODELE_LOT],
						$lLigne[StockManager::CHAMP_STOCK_ID_OPERATION],
						$lLigne[StockManager::CHAMP_STOCK_ID_NOM_PRODUIT],
						$lLigne[StockManager::CHAMP_STOCK_UNITE]));
				}
			} else {
				$lListeStock[0] = new StockVO();
			}

			return $lListeStock;
		}

		$lListeStock[0] = new StockVO();
		return $lListeStock;
	}

	/**
	* @name remplirStock($pId, $pDate, $pQuantite, $pType, $pIdCompte, $pIdDetailCommande, $pIdModeleLot, $pIdOperation, $pIdNomProduit, $pUnite)
	* @param int(11)
	* @param datetime
	* @param decimal(10,2)
	* @param tinyint(1)
	* @param int(11)
	* @param int(11)
	* @param int(11)
	* @param int(11)
	* @param int(11)
	* @param varchar(20)
	* @return StockVO
	* @desc Retourne une StockVO remplie
	*/
	private static function remplirStock($pId, $pDate, $pQuantite, $pType, $pIdCompte, $pIdDetailCommande, $pIdModeleLot, $pIdOperation, $pIdNomProduit, $pUnite) {
		$lStock = new StockVO();
		$lStock->setId($pId);
		$lStock->setDate($pDate);
		$lStock->setQuantite($pQuantite);
		$lStock->setType($pType);
		$lStock->setIdCompte($pIdCompte);
		$lStock->setIdDetailCommande($pIdDetailCommande);
		$lStock->setIdModeleLot($pIdModeleLot);
		$lStock->setIdOperation($pIdOperation);
		$lStock->setIdNomProduit($pIdNomProduit);
		$lStock->setUnite($pUnite);
		return $lStock;
	}

	/**
	* @name insert($pVo)
	* @param StockVO
	* @return integer
	* @desc Insère une nouvelle ligne dans la table, à partir des informations de la StockVO en paramètre (l'id sera automatiquement calculé par la BDD)
	*/
	public static function insert($pVo) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"INSERT INTO " . StockManager::TABLE_STOCK . "
				(" . StockManager::CHAMP_STOCK_ID . "
				," . StockManager::CHAMP_STOCK_DATE . "
				," . StockManager::CHAMP_STOCK_QUANTITE . "
				," . StockManager::CHAMP_STOCK_TYPE . "
				," . StockManager::CHAMP_STOCK_ID_COMPTE . "
				," . StockManager::CHAMP_STOCK_ID_DETAIL_COMMANDE . "
				," . StockManager::CHAMP_STOCK_ID_MODELE_LOT . "
				," . StockManager::CHAMP_STOCK_ID_OPERATION . "
				," . StockManager::CHAMP_STOCK_ID_NOM_PRODUIT . "
				," . StockManager::CHAMP_STOCK_UNITE . ")
			VALUES ";

		if(is_array($pVo)) {
			$lNbVO = count($pVo);
			$lI = 1;
			foreach($pVo as $lVo) {
				$lRequete .= "(NULL
				,'" . StringUtils::securiser( $lVo->getDate() ) . "'
				,'" . StringUtils::securiser( $lVo->getQuantite() ) . "'
				,'" . StringUtils::securiser( $lVo->getType() ) . "'
				,'" . StringUtils::securiser( $lVo->getIdCompte() ) . "'
				,'" . StringUtils::securiser( $lVo->getIdDetailCommande() ) . "'
				,'" . StringUtils::securiser( $lVo->getIdModeleLot() ) . "'
				,'" . StringUtils::securiser( $lVo->getIdOperation() ) . "'
				,'" . StringUtils::securiser( $lVo->getIdNomProduit() ) . "'
				,'" . StringUtils::securiser( $lVo->getUnite() ) . "')";

				if($lNbVO == $lI) {
					$lRequete .= ";";
				} else {
					$lRequete .= ",";
				}
				$lI++;
			}
		} else{
			$lRequete .= "(NULL
				,'" . StringUtils::securiser( $pVo->getDate() ) . "'
				,'" . StringUtils::securiser( $pVo->getQuantite() ) . "'
				,'" . StringUtils::securiser( $pVo->getType() ) . "'
				,'" . StringUtils::securiser( $pVo->getIdCompte() ) . "'
				,'" . StringUtils::securiser( $pVo->getIdDetailCommande() ) . "'
				,'" . StringUtils::securiser( $pVo->getIdModeleLot() ) . "'
				,'" . StringUtils::securiser( $pVo->getIdOperation() ) . "'
				,'" . StringUtils::securiser( $pVo->getIdNomProduit() ) . "'
				,'" . StringUtils::securiser( $pVo->getUnite() ) . "');";
		}

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		return Dbutils::executerRequeteInsertRetourId($lRequete);
	}

	/**
	* @name update($pVo)
	* @param StockVO
	* @desc Met à jour la ligne de la table, correspondant à l'id du StockVO, avec les informations du StockVO
	*/
	public static function update($pVo) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete = 
			"UPDATE " . StockManager::TABLE_STOCK . "
			 SET
				 " . StockManager::CHAMP_STOCK_DATE . " = '" . StringUtils::securiser( $pVo->getDate() ) . "'
				," . StockManager::CHAMP_STOCK_QUANTITE . " = '" . StringUtils::securiser( $pVo->getQuantite() ) . "'
				," . StockManager::CHAMP_STOCK_TYPE . " = '" . StringUtils::securiser( $pVo->getType() ) . "'
				," . StockManager::CHAMP_STOCK_ID_COMPTE . " = '" . StringUtils::securiser( $pVo->getIdCompte() ) . "'
				," . StockManager::CHAMP_STOCK_ID_DETAIL_COMMANDE . " = '" . StringUtils::securiser( $pVo->getIdDetailCommande() ) . "'
				," . StockManager::CHAMP_STOCK_ID_MODELE_LOT . " = '" . StringUtils::securiser( $pVo->getIdModeleLot() ) . "'
				," . StockManager::CHAMP_STOCK_ID_OPERATION . " = '" . StringUtils::securiser( $pVo->getIdOperation() ) . "'
				," . StockManager::CHAMP_STOCK_ID_NOM_PRODUIT . " = '" . StringUtils::securiser( $pVo->getIdNomProduit() ) . "'
				," . StockManager::CHAMP_STOCK_UNITE . " = '" . StringUtils::securiser( $pVo->getUnite() ) . "'
			 WHERE " . StockManager::CHAMP_STOCK_ID . " = '" . StringUtils::securiser( $pVo->getId() ) . "'";

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

		$lRequete = "DELETE FROM " . StockManager::TABLE_STOCK . "
			WHERE " . StockManager::CHAMP_STOCK_ID . " = '" . StringUtils::securiser($pId) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		return Dbutils::executerRequete($lRequete);
	}
}
?>