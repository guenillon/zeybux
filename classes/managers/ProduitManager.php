<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 08/11/2011
// Fichier : ProduitManager.php
//
// Description : Classe de gestion des Produit
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_UTILS . "DbUtils.php");
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php");
include_once(CHEMIN_CLASSES_VO . "ProduitVO.php");
include_once(CHEMIN_CLASSES_VO . "DetailProduitVO.php");
include_once(CHEMIN_CLASSES_VO . "InfoCommandeVO.php");
include_once(CHEMIN_CLASSES_MANAGERS . "ProduitManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "NomProduitManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "CategorieProduitManager.php");


include_once(CHEMIN_CLASSES_MANAGERS . "CommandeManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "StockManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "DetailOperationManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "FermeManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "DetailCommandeManager.php");



define("TABLE_PRODUIT", MYSQL_DB_PREFIXE . "pro_produit");
/**
 * @name ProduitManager
 * @author Julien PIERRE
 * @since 08/11/2011
 * 
 * @desc Classe permettant l'accès aux données des Produit
 */
class ProduitManager
{
	const TABLE_PRODUIT = TABLE_PRODUIT;
	const CHAMP_PRODUIT_ID = "pro_id";
	const CHAMP_PRODUIT_ID_COMMANDE = "pro_id_commande";
	const CHAMP_PRODUIT_ID_NOM_PRODUIT = "pro_id_nom_produit";
	const CHAMP_PRODUIT_UNITE_MESURE = "pro_unite_mesure";
	const CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE = "pro_max_produit_commande";
	const CHAMP_PRODUIT_ID_COMPTE_FERME = "pro_id_compte_ferme";
	const CHAMP_PRODUIT_STOCK_RESERVATION = "pro_stock_reservation";
	const CHAMP_PRODUIT_STOCK_INITIAL = "pro_stock_initial";
	const CHAMP_PRODUIT_TYPE = "pro_type";
	const CHAMP_PRODUIT_ETAT = "pro_etat";

	/**
	* @name select($pId)
	* @param integer
	* @return ProduitVO
	* @desc Récupère la ligne correspondant à l'id en paramètre, créé une ProduitVO contenant les informations et la renvoie
	*/
	public static function select($pId) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"SELECT "
			    . ProduitManager::CHAMP_PRODUIT_ID . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT . 
			"," . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE . 
			"," . ProduitManager::CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME . 
			"," . ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION . 
			"," . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . 
			"," . ProduitManager::CHAMP_PRODUIT_TYPE . 
			"," . ProduitManager::CHAMP_PRODUIT_ETAT . "
			FROM " . ProduitManager::TABLE_PRODUIT . " 
			WHERE " . ProduitManager::CHAMP_PRODUIT_ID . " = '" . StringUtils::securiser($pId) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		if( mysqli_num_rows($lSql) > 0 ) {
			$lLigne = mysqli_fetch_assoc($lSql);
			return ProduitManager::remplirProduit(
				$pId,
				$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMMANDE],
				$lLigne[ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT],
				$lLigne[ProduitManager::CHAMP_PRODUIT_UNITE_MESURE],
				$lLigne[ProduitManager::CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE],
				$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME],
				$lLigne[ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION],
				$lLigne[ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL],
				$lLigne[ProduitManager::CHAMP_PRODUIT_TYPE],
				$lLigne[ProduitManager::CHAMP_PRODUIT_ETAT]);
		} else {
			return new ProduitVO();
		}
	}

	/**
	* @name selectAll()
	* @return array(ProduitVO)
	* @desc Récupères toutes les lignes de la table et les renvoie sous forme d'une collection de ProduitVO
	*/
	public static function selectAll() {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
			"SELECT "
			    . ProduitManager::CHAMP_PRODUIT_ID . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT . 
			"," . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE . 
			"," . ProduitManager::CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME . 
			"," . ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION . 
			"," . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . 
			"," . ProduitManager::CHAMP_PRODUIT_TYPE . 
			"," . ProduitManager::CHAMP_PRODUIT_ETAT . "
			FROM " . ProduitManager::TABLE_PRODUIT;

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		$lListeProduit = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeProduit,
					ProduitManager::remplirProduit(
					$lLigne[ProduitManager::CHAMP_PRODUIT_ID],
					$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMMANDE],
					$lLigne[ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT],
					$lLigne[ProduitManager::CHAMP_PRODUIT_UNITE_MESURE],
					$lLigne[ProduitManager::CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE],
					$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME],
					$lLigne[ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION],
					$lLigne[ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL],
					$lLigne[ProduitManager::CHAMP_PRODUIT_TYPE],
					$lLigne[ProduitManager::CHAMP_PRODUIT_ETAT]));
			}
		} else {
			$lListeProduit[0] = new ProduitVO();
		}
		return $lListeProduit;
	}

	/**
	* @name selectbyIdNomProduitIdMarche($pIdNomProduit,$pIdMarche)
	* @param integer
	* @param integer
	* @return array(ProduitVO)
	* @desc Récupère les lignes de IdNomProduit et IdCommande
	*/
	public static function selectbyIdNomProduitIdMarche($pIdNomProduit,$pIdMarche) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"SELECT "
			    . ProduitManager::CHAMP_PRODUIT_ID . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT . 
			"," . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE . 
			"," . ProduitManager::CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME . 
			"," . ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION . 
			"," . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . 
			"," . ProduitManager::CHAMP_PRODUIT_TYPE .
			"," . ProduitManager::CHAMP_PRODUIT_ETAT . "
			FROM " . ProduitManager::TABLE_PRODUIT . " 
			WHERE " . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT . " = '" . StringUtils::securiser($pIdNomProduit) . "'
			AND " . ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . " = '" . StringUtils::securiser($pIdMarche) . "'
			AND " . ProduitManager::CHAMP_PRODUIT_ETAT . " = '0';";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
		$lListeProduit = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeProduit,
					ProduitManager::remplirProduit(
					$lLigne[ProduitManager::CHAMP_PRODUIT_ID],
					$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMMANDE],
					$lLigne[ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT],
					$lLigne[ProduitManager::CHAMP_PRODUIT_UNITE_MESURE],
					$lLigne[ProduitManager::CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE],
					$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME],
					$lLigne[ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION],
					$lLigne[ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL],
					$lLigne[ProduitManager::CHAMP_PRODUIT_TYPE],
					$lLigne[ProduitManager::CHAMP_PRODUIT_ETAT]));
			}
		} else {
			$lListeProduit[0] = new ProduitVO();
		}
		return $lListeProduit;
	}
	
	/**
	* @name selectbyIdMarcheProduitAbonnement($pIdMarche)
	* @param integer
	* @return array(ProduitVO)
	* @desc Récupère les lignes de $pIdMarche
	*/
	public static function selectbyIdMarcheProduitAbonnement($pIdMarche) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"SELECT "
			    . ProduitManager::CHAMP_PRODUIT_ID . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT . 
			"," . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE . 
			"," . ProduitManager::CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME . 
			"," . ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION . 
			"," . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . 
			"," . ProduitManager::CHAMP_PRODUIT_TYPE .
			"," . ProduitManager::CHAMP_PRODUIT_ETAT . "
			FROM " . ProduitManager::TABLE_PRODUIT . " 
			WHERE " . ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . " = '" . StringUtils::securiser($pIdMarche) . "'
			AND " . ProduitManager::CHAMP_PRODUIT_TYPE . " = '2'
			AND " . ProduitManager::CHAMP_PRODUIT_ETAT . " = '0';";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
		$lListeProduit = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeProduit,
					ProduitManager::remplirProduit(
					$lLigne[ProduitManager::CHAMP_PRODUIT_ID],
					$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMMANDE],
					$lLigne[ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT],
					$lLigne[ProduitManager::CHAMP_PRODUIT_UNITE_MESURE],
					$lLigne[ProduitManager::CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE],
					$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME],
					$lLigne[ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION],
					$lLigne[ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL],
					$lLigne[ProduitManager::CHAMP_PRODUIT_TYPE],
					$lLigne[ProduitManager::CHAMP_PRODUIT_ETAT]));
			}
		} else {
			$lListeProduit[0] = new ProduitVO();
		}
		return $lListeProduit;
	}
		
	/**
	* @name recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri )
	* @param string nom de la table
	* @param string Le type de critère de recherche
	* @param array(string) champs à récupérer dans la table
	* @param array(array(string, object)) Dictionnaire(champ, valeur)) contenant les champs à filtrer ainsi que la valeur du filtre
	* @param array(array(string, string)) Dictionnaire(champ, sens) contenant les tris à appliquer
	* @return array(ProduitVO)
	* @desc Récupères les lignes de la table selon le critère de recherche puis trie et renvoie la liste de résultat sous forme d'une collection de ProduitVO
	*/
	public static function recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri ) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		// Préparation de la requète
		$lChamps = array( 
			    ProduitManager::CHAMP_PRODUIT_ID .
			"," . ProduitManager::CHAMP_PRODUIT_ID_COMMANDE .
			"," . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT .
			"," . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE .
			"," . ProduitManager::CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE .
			"," . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME .
			"," . ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION .
			"," . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL .
			"," . ProduitManager::CHAMP_PRODUIT_TYPE .
			"," . ProduitManager::CHAMP_PRODUIT_ETAT		);

		// Préparation de la requète de recherche
		$lRequete = DbUtils::prepareRequeteRecherche(ProduitManager::TABLE_PRODUIT, $lChamps, $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri);

		$lListeProduit = array();

		if($lRequete !== false) {

			$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
			$lSql = Dbutils::executerRequete($lRequete);

			if( mysqli_num_rows($lSql) > 0 ) {

				while ( $lLigne = mysqli_fetch_assoc($lSql) ) {

					array_push($lListeProduit,
						ProduitManager::remplirProduit(
						$lLigne[ProduitManager::CHAMP_PRODUIT_ID],
						$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMMANDE],
						$lLigne[ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT],
						$lLigne[ProduitManager::CHAMP_PRODUIT_UNITE_MESURE],
						$lLigne[ProduitManager::CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE],
						$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME],
						$lLigne[ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION],
						$lLigne[ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL],
						$lLigne[ProduitManager::CHAMP_PRODUIT_TYPE],
						$lLigne[ProduitManager::CHAMP_PRODUIT_ETAT]));
				}
			} else {
				$lListeProduit[0] = new ProduitVO();
			}

			return $lListeProduit;
		}

		$lListeProduit[0] = new ProduitVO();
		return $lListeProduit;
	}

	/**
	* @name remplirProduit($pId, $pIdCommande, $pIdNomProduit, $pUniteMesure, $pMaxProduitCommande, $pIdCompteFerme, $pStockReservation, $pStockInitial, $pType, $pEtat)
	* @param int(11)
	* @param int(11)
	* @param int(11)
	* @param varchar(20)
	* @param decimal(10,2)
	* @param int(11)
	* @param decimal(10,2)
	* @param decimal(10,2)
	* @param tinyint(4)
	* @param int(11)
	* @return ProduitVO
	* @desc Retourne une ProduitVO remplie
	*/
	private static function remplirProduit($pId, $pIdCommande, $pIdNomProduit, $pUniteMesure, $pMaxProduitCommande, $pIdCompteFerme, $pStockReservation, $pStockInitial, $pType, $pEtat) {
		$lProduit = new ProduitVO();
		$lProduit->setId($pId);
		$lProduit->setIdCommande($pIdCommande);
		$lProduit->setIdNomProduit($pIdNomProduit);
		$lProduit->setUniteMesure($pUniteMesure);
		$lProduit->setMaxProduitCommande($pMaxProduitCommande);
		$lProduit->setIdCompteFerme($pIdCompteFerme);
		$lProduit->setStockReservation($pStockReservation);
		$lProduit->setStockInitial($pStockInitial);
		$lProduit->setType($pType);
		$lProduit->setEtat($pEtat);
		return $lProduit;
	}

	/**
	 * @name selectResumeMarche($pIdMarche)
	 * @param integer
	 * @return array(InfoCommandeVO)
	 * @desc Récupère les infos sur le marche
	 */
	public static function selectResumeMarche($pIdMarche) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		
		$lRequete =
			"SELECT "
				. ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . " AS " . CommandeManager::CHAMP_COMMANDE_ID .
			"," . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME .
			"," . ProduitManager::TABLE_PRODUIT . "." . ProduitManager::CHAMP_PRODUIT_ID .
			"," . ProduitManager::CHAMP_PRODUIT_TYPE .
			"," . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE .
			"," . NomProduitManager::CHAMP_NOMPRODUIT_NOM .
			", info_bon_commande." . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT . " AS dope_montant" .
			", info_bon_commande." . StockManager::CHAMP_STOCK_QUANTITE . " AS sto_quantite" .
			
			", info_bon_livraison." . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT . " AS dope_montant_livraison" .
			", info_bon_livraison." . StockManager::CHAMP_STOCK_QUANTITE . " AS sto_quantite_livraison" .
			
			", stock_solidaire." . StockManager::CHAMP_STOCK_QUANTITE . " AS sto_quantite_solidaire" .
			
			", (info_achat." . StockManager::CHAMP_STOCK_QUANTITE . " * -(1)) AS sto_quantite_vente" .
			", (info_achat_solidaire." . StockManager::CHAMP_STOCK_QUANTITE . " * -(1)) AS sto_quantite_vente_solidaire" .
			", (info_achat." . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT . " * -(1)) AS dope_montant_vente" .
			", (info_achat_solidaire." . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT . " * -(1)) AS dope_montant_vente_solidaire
			
			FROM " . ProduitManager::TABLE_PRODUIT . " 
			JOIN " . NomProduitManager::TABLE_NOMPRODUIT . "
				ON " . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT . " = " . NomProduitManager::CHAMP_NOMPRODUIT_ID . "
			JOIN " . CategorieProduitManager::TABLE_CATEGORIEPRODUIT . " 
				ON " . CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_ID . " = " . NomProduitManager::CHAMP_NOMPRODUIT_ID_CATEGORIE . "
			JOIN " . FermeManager::TABLE_FERME . "
				ON " . FermeManager::CHAMP_FERME_ID_COMPTE . " = " . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME . "
			LEFT JOIN (
				SELECT " 
						. DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . 
					"," . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT .
					"," . StockManager::CHAMP_STOCK_QUANTITE . "
				FROM " . DetailCommandeManager::TABLE_DETAILCOMMANDE . "
				JOIN " . StockManager::TABLE_STOCK . " 
					ON " . StockManager::CHAMP_STOCK_ID_DETAIL_COMMANDE . " = " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . "
				JOIN " . DetailOperationManager::TABLE_DETAILOPERATION . "
					ON " . DetailOperationManager::CHAMP_DETAILOPERATION_ID_DETAIL_COMMANDE . " = " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . "
				WHERE " . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . " = 5
					AND " . StockManager::CHAMP_STOCK_TYPE . " = 3
				GROUP BY " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . ", " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . "
			) AS info_bon_commande
				ON info_bon_commande." . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . " = " . ProduitManager::TABLE_PRODUIT . "." . ProduitManager::CHAMP_PRODUIT_ID . "
			LEFT JOIN (
				SELECT "
						. DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . 
					"," . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT .
					"," . StockManager::CHAMP_STOCK_QUANTITE . "
				FROM " . DetailCommandeManager::TABLE_DETAILCOMMANDE . "
				JOIN " . StockManager::TABLE_STOCK . " 
					ON " . StockManager::CHAMP_STOCK_ID_DETAIL_COMMANDE . " = " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . "
				JOIN " . DetailOperationManager::TABLE_DETAILOPERATION . "
					ON " . DetailOperationManager::CHAMP_DETAILOPERATION_ID_DETAIL_COMMANDE . " = " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . "
				WHERE " . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . " = 6
					AND " . StockManager::CHAMP_STOCK_TYPE . " = 4
				GROUP BY " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . ", " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . "
			) AS info_bon_livraison
				ON info_bon_livraison." . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . " = " . ProduitManager::TABLE_PRODUIT . "." . ProduitManager::CHAMP_PRODUIT_ID . "
			LEFT JOIN (
				SELECT "
						. DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT .
					"," . StockManager::CHAMP_STOCK_QUANTITE . "
				FROM " . StockManager::TABLE_STOCK . " 			
				JOIN " . DetailCommandeManager::TABLE_DETAILCOMMANDE . "
					ON " . StockManager::CHAMP_STOCK_ID_DETAIL_COMMANDE . " = " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . "
				WHERE " . StockManager::CHAMP_STOCK_TYPE . " = 2
					AND " . StockManager::CHAMP_STOCK_QUANTITE . " > 0 
				GROUP BY " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . ", " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . "
			) AS stock_solidaire
				ON stock_solidaire." . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . " = " . ProduitManager::TABLE_PRODUIT . "." . ProduitManager::CHAMP_PRODUIT_ID . "	
			LEFT JOIN (
				SELECT " 
						. ProduitManager::CHAMP_PRODUIT_ID 
					. ", SUM( " . StockManager::CHAMP_STOCK_QUANTITE . " ) AS " . StockManager::CHAMP_STOCK_QUANTITE
					. ", SUM( " . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT . " ) AS " . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT . "
				FROM " . ProduitManager::TABLE_PRODUIT . " 
				JOIN " . DetailCommandeManager::TABLE_DETAILCOMMANDE . "
					ON " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . " = " . ProduitManager::CHAMP_PRODUIT_ID . "
				JOIN " . StockManager::TABLE_STOCK . "
					ON " . StockManager::CHAMP_STOCK_ID_DETAIL_COMMANDE . " = " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . "
				JOIN " . DetailOperationManager::TABLE_DETAILOPERATION . "
					ON " . DetailOperationManager::CHAMP_DETAILOPERATION_ID_DETAIL_COMMANDE . " = " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . "
					AND " . StockManager::CHAMP_STOCK_ID_COMPTE . " = " . DetailOperationManager::CHAMP_DETAILOPERATION_ID_COMPTE . "
				WHERE "
					. ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . " = '" . StringUtils::securiser( $pIdMarche ) . "'
					AND " . StockManager::CHAMP_STOCK_TYPE . " = 1
					AND " . StockManager::CHAMP_STOCK_QUANTITE . " < 0
					AND " . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . " = 7
					AND " . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT . " < 0
					AND " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ETAT . " = 0
				GROUP BY " . ProduitManager::CHAMP_PRODUIT_ID . "
			) AS info_achat
				ON info_achat." . ProduitManager::CHAMP_PRODUIT_ID . " = " . ProduitManager::TABLE_PRODUIT . "." . ProduitManager::CHAMP_PRODUIT_ID . "
			LEFT JOIN (
				SELECT " 
						. ProduitManager::CHAMP_PRODUIT_ID 
					. ", SUM( " . StockManager::CHAMP_STOCK_QUANTITE . " ) AS " . StockManager::CHAMP_STOCK_QUANTITE
					. ", SUM( " . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT . " ) AS " . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT . "
				FROM " . ProduitManager::TABLE_PRODUIT . " 
				JOIN " . DetailCommandeManager::TABLE_DETAILCOMMANDE . "
					ON " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID_PRODUIT . " = " . ProduitManager::CHAMP_PRODUIT_ID . "
				JOIN " . StockManager::TABLE_STOCK . "
					ON " . StockManager::CHAMP_STOCK_ID_DETAIL_COMMANDE . " = " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . "
				JOIN " . DetailOperationManager::TABLE_DETAILOPERATION . "
					ON " . DetailOperationManager::CHAMP_DETAILOPERATION_ID_DETAIL_COMMANDE . " = " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ID . "
					AND " . StockManager::CHAMP_STOCK_ID_COMPTE . " = " . DetailOperationManager::CHAMP_DETAILOPERATION_ID_COMPTE . "
				WHERE "
					. ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . " = '" . StringUtils::securiser( $pIdMarche ) . "'
					AND " . StockManager::CHAMP_STOCK_TYPE . " = 2
					AND " . StockManager::CHAMP_STOCK_QUANTITE . " < 0
					AND " . DetailOperationManager::CHAMP_DETAILOPERATION_TYPE_PAIEMENT . " = 8
					AND " . DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT . " < 0
					AND " . DetailCommandeManager::CHAMP_DETAILCOMMANDE_ETAT . " = 0
				GROUP BY " . ProduitManager::CHAMP_PRODUIT_ID . "
			) AS info_achat_solidaire
				ON info_achat_solidaire." . ProduitManager::CHAMP_PRODUIT_ID . " = " . ProduitManager::TABLE_PRODUIT . "." . ProduitManager::CHAMP_PRODUIT_ID . "	
			WHERE "
				. ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . " = '" . StringUtils::securiser( $pIdMarche ) . "'
				AND " .  ProduitManager::CHAMP_PRODUIT_ETAT . " = 0
			GROUP BY " . ProduitManager::TABLE_PRODUIT . "." . ProduitManager::CHAMP_PRODUIT_ID . "
			ORDER BY " . NomProduitManager::CHAMP_NOMPRODUIT_NOM . ";";	
		
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		$lListeInfoCommande = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeInfoCommande,
						new InfoCommandeVO(
						$lLigne[CommandeManager::CHAMP_COMMANDE_ID],
						$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME],
						$lLigne[ProduitManager::CHAMP_PRODUIT_ID],
						$lLigne[ProduitManager::CHAMP_PRODUIT_TYPE],
						$lLigne[ProduitManager::CHAMP_PRODUIT_UNITE_MESURE],
						$lLigne[NomProduitManager::CHAMP_NOMPRODUIT_NOM],
						$lLigne[DetailOperationManager::CHAMP_DETAILOPERATION_MONTANT],
						$lLigne[StockManager::CHAMP_STOCK_QUANTITE],
						$lLigne["dope_montant_livraison"],
						$lLigne["sto_quantite_livraison"],
						$lLigne["sto_quantite_solidaire"],
						$lLigne["sto_quantite_vente"],
						$lLigne["sto_quantite_vente_solidaire"],
						$lLigne["dope_montant_vente"],
						$lLigne["dope_montant_vente_solidaire"]));
			}
		} else {
			$lListeInfoCommande[0] = new InfoCommandeVO();
		}
		return $lListeInfoCommande;
	}	
	
	/**
	* @name insert($pVo)
	* @param ProduitVO
	* @return integer
	* @desc Insère une nouvelle ligne dans la table, à partir des informations de la ProduitVO en paramètre (l'id sera automatiquement calculé par la BDD)
	*/
	public static function insert($pVo) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"INSERT INTO " . ProduitManager::TABLE_PRODUIT . "
				(" . ProduitManager::CHAMP_PRODUIT_ID . "
				," . ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . "
				," . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT . "
				," . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE . "
				," . ProduitManager::CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE . "
				," . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME . "
				," . ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION . "
				," . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . "
				," . ProduitManager::CHAMP_PRODUIT_TYPE . "
				," . ProduitManager::CHAMP_PRODUIT_ETAT . ")
			VALUES (NULL
				,'" . StringUtils::securiser( $pVo->getIdCommande() ) . "'
				,'" . StringUtils::securiser( $pVo->getIdNomProduit() ) . "'
				,'" . StringUtils::securiser( $pVo->getUniteMesure() ) . "'
				,'" . StringUtils::securiser( $pVo->getMaxProduitCommande() ) . "'
				,'" . StringUtils::securiser( $pVo->getIdCompteFerme() ) . "'
				,'" . StringUtils::securiser( $pVo->getStockReservation() ) . "'
				,'" . StringUtils::securiser( $pVo->getStockInitial() ) . "'
				,'" . StringUtils::securiser( $pVo->getType() ) . "'
				,'" . StringUtils::securiser( $pVo->getEtat() ) . "')";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		return Dbutils::executerRequeteInsertRetourId($lRequete);
	}

	/**
	* @name update($pVo)
	* @param ProduitVO
	* @desc Met à jour la ligne de la table, correspondant à l'id du ProduitVO, avec les informations du ProduitVO
	*/
	public static function update($pVo) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete = 
			"UPDATE " . ProduitManager::TABLE_PRODUIT . "
			 SET
				 " . ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . " = '" . StringUtils::securiser( $pVo->getIdCommande() ) . "'
				," . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT . " = '" . StringUtils::securiser( $pVo->getIdNomProduit() ) . "'
				," . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE . " = '" . StringUtils::securiser( $pVo->getUniteMesure() ) . "'
				," . ProduitManager::CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE . " = '" . StringUtils::securiser( $pVo->getMaxProduitCommande() ) . "'
				," . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME . " = '" . StringUtils::securiser( $pVo->getIdCompteFerme() ) . "'
				," . ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION . " = '" . StringUtils::securiser( $pVo->getStockReservation() ) . "'
				," . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . " = '" . StringUtils::securiser( $pVo->getStockInitial() ) . "'
				," . ProduitManager::CHAMP_PRODUIT_TYPE . " = '" . StringUtils::securiser( $pVo->getType() ) . "'
				," . ProduitManager::CHAMP_PRODUIT_ETAT . " = '" . StringUtils::securiser( $pVo->getEtat() ) . "'
			 WHERE " . ProduitManager::CHAMP_PRODUIT_ID . " = '" . StringUtils::securiser( $pVo->getId() ) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		Dbutils::executerRequete($lRequete);
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

		$lRequete = "DELETE FROM " . ProduitManager::TABLE_PRODUIT . "
			WHERE " . ProduitManager::CHAMP_PRODUIT_ID . " = '" . StringUtils::securiser($pId) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		Dbutils::executerRequete($lRequete);
	}
	
	/**
	 * @name selectDetailProduits($pProduits)
	 * @param array(integer idProduit)
	 * @return array(DetailProduitVO)
	 * @desc Récupères le détail des produits et les renvoie sous forme d'une collection de DetailProduitVO
	 */
	public static function selectDetailProduits($pProduits) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
		"SELECT "
			    . ProduitManager::CHAMP_PRODUIT_ID . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_COMMANDE . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT . 
			"," . ProduitManager::CHAMP_PRODUIT_UNITE_MESURE . 
			"," . ProduitManager::CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE . 
			"," . ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME . 
			"," . ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION . 
			"," . ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL . 
			"," . ProduitManager::CHAMP_PRODUIT_TYPE . 
			"," . ProduitManager::CHAMP_PRODUIT_ETAT . 
			"," . NomProduitManager::CHAMP_NOMPRODUIT_ID . 
			"," . NomProduitManager::CHAMP_NOMPRODUIT_NUMERO . 
			"," . NomProduitManager::CHAMP_NOMPRODUIT_NOM . 
			"," . NomProduitManager::CHAMP_NOMPRODUIT_DESCRIPTION . 
			"," . NomProduitManager::CHAMP_NOMPRODUIT_ID_CATEGORIE . 
			"," . NomProduitManager::CHAMP_NOMPRODUIT_ID_FERME . 
			"," . NomProduitManager::CHAMP_NOMPRODUIT_ETAT . 
			"," . CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_ID . 
			"," . CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_NOM . 
			"," . CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_DESCRIPTION . 
			"," . CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_ETAT . "
 			FROM " . ProduitManager::TABLE_PRODUIT . " 
			JOIN " . NomProduitManager::TABLE_NOMPRODUIT. " on " . NomProduitManager::CHAMP_NOMPRODUIT_ID . " = " . ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT . "
			JOIN " . CategorieProduitManager::TABLE_CATEGORIEPRODUIT . " on " . CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_ID . " = " . NomProduitManager::CHAMP_NOMPRODUIT_ID_CATEGORIE . "
			WHERE " . ProduitManager::CHAMP_PRODUIT_ID . " in ( '" .  str_replace(",", "','", StringUtils::securiser( implode(",", $pProduits) ) ) . "')
			ORDER BY " . CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_NOM . " ASC," . NomProduitManager::CHAMP_NOMPRODUIT_NOM . " ASC;";
	
		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);
	
		$lListeProduit = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeProduit,
				ProduitManager::remplirDetailProduit(
				$lLigne[ProduitManager::CHAMP_PRODUIT_ID],
				$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMMANDE],
				$lLigne[ProduitManager::CHAMP_PRODUIT_ID_NOM_PRODUIT],
				$lLigne[ProduitManager::CHAMP_PRODUIT_UNITE_MESURE],
				$lLigne[ProduitManager::CHAMP_PRODUIT_MAX_PRODUIT_COMMANDE],
				$lLigne[ProduitManager::CHAMP_PRODUIT_ID_COMPTE_FERME],
				$lLigne[ProduitManager::CHAMP_PRODUIT_STOCK_RESERVATION],
				$lLigne[ProduitManager::CHAMP_PRODUIT_STOCK_INITIAL],
				$lLigne[ProduitManager::CHAMP_PRODUIT_TYPE],
				$lLigne[ProduitManager::CHAMP_PRODUIT_ETAT],
				$lLigne[NomProduitManager::CHAMP_NOMPRODUIT_ID],
				$lLigne[NomProduitManager::CHAMP_NOMPRODUIT_NUMERO],
				$lLigne[NomProduitManager::CHAMP_NOMPRODUIT_NOM],
				$lLigne[NomProduitManager::CHAMP_NOMPRODUIT_DESCRIPTION],
				$lLigne[NomProduitManager::CHAMP_NOMPRODUIT_ID_CATEGORIE],
				$lLigne[NomProduitManager::CHAMP_NOMPRODUIT_ID_FERME],
				$lLigne[NomProduitManager::CHAMP_NOMPRODUIT_ETAT],
				$lLigne[CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_ID],
				$lLigne[CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_NOM],
				$lLigne[CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_DESCRIPTION],
				$lLigne[CategorieProduitManager::CHAMP_CATEGORIEPRODUIT_ETAT]));
			}
		} else {
			$lListeProduit[0] = new DetailProduitVO();
		}
		return $lListeProduit;
	}
	
	/**
	* @name remplirDetailProduit($pProId, $pProIdCommande, $pProIdNomProduit, $pProUniteMesure, $pProMaxProduitCommande, $pProIdCompteFerme, $pProStockReservation, $pProStocktInitial, $pProType, $pProEtat, $pNproId, $pNproNumero, $pNproNom, $pNproDescription, $pNproIdCategorie, $pNproIdFerme, $pNproEtat, $pCproId, $pCproNom, $pCproDescription, $pCproEtat)
	* @param int(11)
	* @param int(11)
	* @param int(11)
	* @param varchar(20)
	* @param decimal(10,2) 	
	* @param int(11)
	* @param decimal(10,2)
	* @param decimal(10,2) 	
	* @param tinyint(4)
	* @param int(11)
	* @param int(11)
	* @param varchar(50)
	* @param varchar(50)
	* @param text
	* @param int(11)
	* @param int(11)
	* @param int(11)
	* @param int(11)
	* @param varchar(50)
	* @param text
	* @param tinyint(4)
	* @return DetailProduitViewVO
	* @desc Retourne une DetailProduitViewVO remplie
	*/
	private static function remplirDetailProduit($pProId, $pProIdCommande, $pProIdNomProduit, $pProUniteMesure, $pProMaxProduitCommande, $pProIdCompteFerme, $pProStockReservation, $pProStocktInitial, $pProType, $pProEtat, $pNproId, $pNproNumero, $pNproNom, $pNproDescription, $pNproIdCategorie, $pNproIdFerme, $pNproEtat, $pCproId, $pCproNom, $pCproDescription, $pCproEtat) {
		$lDetailProduit = new DetailProduitVO();
		$lDetailProduit->setProId($pProId);
		$lDetailProduit->setProIdCommande($pProIdCommande);
		$lDetailProduit->setProIdNomProduit($pProIdNomProduit);
		$lDetailProduit->setProUniteMesure($pProUniteMesure);
		$lDetailProduit->setProMaxProduitCommande($pProMaxProduitCommande);
		$lDetailProduit->setProIdCompteFerme($pProIdCompteFerme);
		$lDetailProduit->setProStockReservation($pProStockReservation);
		$lDetailProduit->setProStocktInitial($pProStocktInitial);
		$lDetailProduit->setProType($pProType);
		$lDetailProduit->setProEtat($pProEtat);
		$lDetailProduit->setNproId($pNproId);
		$lDetailProduit->setNproNumero($pNproNumero);
		$lDetailProduit->setNproNom($pNproNom);
		$lDetailProduit->setNproDescription($pNproDescription);
		$lDetailProduit->setNproIdCategorie($pNproIdCategorie);
		$lDetailProduit->setNproIdFerme($pNproIdFerme);
		$lDetailProduit->setNproEtat($pNproEtat);
		$lDetailProduit->setCproId($pCproId);
		$lDetailProduit->setCproNom($pCproNom);
		$lDetailProduit->setCproDescription($pCproDescription);
		$lDetailProduit->setCproEtat($pCproEtat);
		return $lDetailProduit;
	}
}
?>