<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 28/10/2010
// Fichier : MenuViewManager.php
//
// Description : Classe de gestion des Menu
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_UTILS . "DbUtils.php");
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php");
include_once(CHEMIN_CLASSES_VIEW_VO . "MenuViewVO.php");
include_once(CHEMIN_CLASSES_MANAGERS . "AdherentManager.php");
include_once(CHEMIN_CLASSES_MANAGERS . "ModuleManager.php");

define("VUE_MENU", MYSQL_DB_PREFIXE . "view_menu");
/**
 * @name MenuViewManager
 * @author Julien PIERRE
 * @since 28/10/2010
 * 
 * @desc Classe permettant l'accès aux données des Menu
 */
class MenuViewManager
{
	const VUE_MENU = VUE_MENU;

	/**
	* @name select($pId)
	* @param integer
	* @return MenuViewVO
	* @desc Récupère la ligne correspondant à l'id en paramètre, créé une MenuViewVO contenant les informations et la renvoie
	*/
	public static function select($pId) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		$lRequete =
			"SELECT "
			    . AdherentManager::CHAMP_ADHERENT_ID .
			"," . ModuleManager::CHAMP_MOD_ID . 
			"," . ModuleManager::CHAMP_MOD_NOM . 
			"," . ModuleManager::CHAMP_MOD_LABEL . 
			"," . ModuleManager::CHAMP_MOD_ADMIN . "
			FROM " . MenuViewManager::VUE_MENU . " 
			WHERE " . AdherentManager::CHAMP_ADHERENT_ID . " = '" . StringUtils::securiser($pId) . "'";

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		$lListeMenu = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeMenu,
					MenuViewManager::remplir(
					$lLigne[AdherentManager::CHAMP_ADHERENT_ID],
					$lLigne[ModuleManager::CHAMP_MOD_ID],
					$lLigne[ModuleManager::CHAMP_MOD_NOM],
					$lLigne[ModuleManager::CHAMP_MOD_LABEL],
					$lLigne[ModuleManager::CHAMP_MOD_ADMIN]));
			}
		} else {
			$lListeMenu[0] = new MenuViewVO();
		}
		return $lListeMenu;
	}

	/**
	* @name selectAll()
	* @return array(MenuViewVO)
	* @desc Récupères toutes les lignes de la table et les renvoie sous forme d'une collection de MenuViewVO
	*/
	public static function selectAll() {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		$lRequete =
			"SELECT "
			    . AdherentManager::CHAMP_ADHERENT_ID .
			"," . ModuleManager::CHAMP_MOD_ID . 
			"," . ModuleManager::CHAMP_MOD_NOM . 
			"," . ModuleManager::CHAMP_MOD_LABEL . 
			"," . ModuleManager::CHAMP_MOD_ADMIN . "
			FROM " . MenuViewManager::VUE_MENU;

		$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
		$lSql = Dbutils::executerRequete($lRequete);

		$lListeMenu = array();
		if( mysqli_num_rows($lSql) > 0 ) {
			while ($lLigne = mysqli_fetch_assoc($lSql)) {
				array_push($lListeMenu,
					MenuViewManager::remplir(
					$lLigne[AdherentManager::CHAMP_ADHERENT_ID],
					$lLigne[ModuleManager::CHAMP_MOD_ID],
					$lLigne[ModuleManager::CHAMP_MOD_NOM],
					$lLigne[ModuleManager::CHAMP_MOD_LABEL],
					$lLigne[ModuleManager::CHAMP_MOD_ADMIN]));
			}
		} else {
			$lListeMenu[0] = new MenuViewVO();
		}
		return $lListeMenu;
	}

	/**
	* @name recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri )
	* @param string nom de la table
	* @param string Le type de critère de recherche
	* @param array(string) champs à récupérer dans la table
	* @param array(array(string, object)) Dictionnaire(champ, valeur)) contenant les champs à filtrer ainsi que la valeur du filtre
	* @param array(array(string, string)) Dictionnaire(champ, sens) contenant les tris à appliquer
	* @return array(MenuViewVO)
	* @desc Récupères les lignes de la table selon le critère de recherche puis trie et renvoie la liste de résultat sous forme d'une collection de MenuViewVO
	*/
	public static function recherche( $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri ) {
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));

		// Préparation de la requète
		$lChamps = array( 
			    AdherentManager::CHAMP_ADHERENT_ID .
			"," . ModuleManager::CHAMP_MOD_ID .
			"," . ModuleManager::CHAMP_MOD_NOM .
			"," . ModuleManager::CHAMP_MOD_LABEL .
			"," . ModuleManager::CHAMP_MOD_ADMIN );

		// Préparation de la requète de recherche
		$lRequete = DbUtils::prepareRequeteRecherche(MenuViewManager::VUE_MENU, $lChamps, $pTypeRecherche, $pTypeCritere, $pCritereRecherche, $pTypeTri, $pCritereTri);

		$lListeMenu = array();
		
		if($lRequete !== false) {
			$lLogger->log("Execution de la requete : " . $lRequete,PEAR_LOG_DEBUG); // Maj des logs
			$lSql = Dbutils::executerRequete($lRequete);
	
			if( mysqli_num_rows($lSql) > 0 ) {
	
				while ( $lLigne = mysqli_fetch_assoc($lSql) ) {
	
					array_push($lListeMenu,
						MenuViewManager::remplir(
						$lLigne[AdherentManager::CHAMP_ADHERENT_ID],
						$lLigne[ModuleManager::CHAMP_MOD_ID],
						$lLigne[ModuleManager::CHAMP_MOD_NOM],
						$lLigne[ModuleManager::CHAMP_MOD_LABEL],
						$lLigne[ModuleManager::CHAMP_MOD_ADMIN]));
				}
			} else {
				$lListeMenu[0] = new MenuViewVO();
			}
	
			return $lListeMenu;
		}

		$lListeMenu[0] = new MenuViewVO();
		return $lListeMenu;
	}

	/**
	* @name remplir($pAdhId, $pModId, $pModNom, $pModLabel)
	* @param int(11)
	* @param int(11)
	* @param varchar(50)
	* @param varchar(80)
	* @return MenuViewVO
	* @desc Retourne une MenuViewVO remplie
	*/
	private static function remplir($pAdhId, $pModId, $pModNom, $pModLabel, $pModAdmin) {
		$lMenu = new MenuViewVO();
		$lMenu->setAdhId($pAdhId);
		$lMenu->setModId($pModId);
		$lMenu->setModNom($pModNom);
		$lMenu->setModLabel($pModLabel);
		$lMenu->setModAdmin($pModAdmin);
		return $lMenu;
	}
}
?>