<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 25/01/2010
// Fichier : DbUtils.php
//
// Description : Classe statique permettant d'executer des requetes dans la base de données
//
//****************************************************************

include_once(CHEMIN_CONFIGURATION . "DB.php"); // Intégration des informations de configuration pour la connexion à la base de données
include_once(CHEMIN_CLASSES_UTILS . "MessagesErreurs.php"); // Intégration des constantes d'erreurs
include_once(CHEMIN_CLASSES_VR . "TemplateVR.php" );
include_once(CHEMIN_CLASSES_VR . "VRerreur.php" );

/**
 * @name DbUtils
 * @author Julien PIERRE
 * @since 25/01/2010
 * 
 * @desc Classe fournissant des méthodes de connexion à la base
 */
class DbUtils
{
	/**
	* @name creerConnexion
	* @return nothing
	* @desc Créé une connexion à la BDD
	*/
	public static function creerConnexion() {
		$mMysqlHost = MYSQL_HOST; // le serveur
		$mMysqlLogin = MYSQL_LOGIN; // le login
		$mMysqlPass = MYSQL_PASS; // mot de passe
		$mMysqlDbnom = MYSQL_DBNOM; // nom de la base de donnee
		
		// Initialisation du Logger
		$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
		$lLogger->setMask(Log::MAX(LOG_LEVEL));
		
		$lDb = @mysqli_connect($mMysqlHost,$mMysqlLogin,$mMysqlPass, $mMysqlDbnom);
		if(!$lDb) {		
			$lLogger->log(MessagesErreurs::ERR_600_MSG ,PEAR_LOG_DEBUG); // Maj des logs
			
			$lVr = new TemplateVR();
			$lVr->setValid(false);
			$lVr->getLog()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_600_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_600_MSG);
			$lVr->getLog()->addErreur($lErreur);
			
			die($lVr->exportToJson());
		} else {	
			if (!@mysqli_set_charset($lDb, "utf8")) { // Permet d'initier une connexion en UTF-8 avec la BDD
		    	$lLogger->log(MessagesErreurs::ERR_603_MSG . " : " . mysqli_error($lDb),PEAR_LOG_DEBUG); // Maj des logs
				
				$lVr = new TemplateVR();
				$lVr->setValid(false);
				$lVr->getLog()->setValid(false);
				$lErreur = new VRerreur();
				$lErreur->setCode(MessagesErreurs::ERR_603_CODE);
				$lErreur->setMessage(MessagesErreurs::ERR_603_MSG);
				$lVr->getLog()->addErreur($lErreur);
				
				die($lVr->exportToJson());
		    } else {
				return $lDb;
    		}
		}
	}
	
	/**
	* @name fermerConnexion
	* @return nothing
	* @desc Ferme la connexion à la BDD
	*/	
	public static function fermerConnexion($pDb) {
		if(!@mysqli_close($pDb)) {			
			// Initialisation du Logger
			$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
			$lLogger->setMask(Log::MAX(LOG_LEVEL));
			$lLogger->log(MessagesErreurs::ERR_602_MSG . " : " . mysqli_error($pDb),PEAR_LOG_DEBUG); // Maj des logs
		
			$lVr = new TemplateVR();
			$lVr->setValid(false);
			$lVr->getLog()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_602_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_602_MSG);
			$lVr->getLog()->addErreur($lErreur);
			
			die($lVr->exportToJson());
		}
	}
		
	/**
	* @name executerRequete ($requete)
	* @param string 
	* @return mysqli_result
	* @desc Exécute la requête passée en paramètre
	*/	
	public static function executerRequete($pRequete) {
		$lDb = DbUtils::creerConnexion();
		$lResultat = @mysqli_query($lDb, $pRequete);
		if (!$lResultat) {			
			// Initialisation du Logger
			$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
			$lLogger->setMask(Log::MAX(LOG_LEVEL));
	    	$lLogger->log(MessagesErreurs::ERR_603_MSG . " : " . mysqli_error($lDb),PEAR_LOG_DEBUG); // Maj des logs
		
			$lVr = new TemplateVR();
			$lVr->setValid(false);
			$lVr->getLog()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_603_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_603_MSG);
			$lVr->getLog()->addErreur($lErreur);
			
			die($lVr->exportToJson());
	    } else {
			DbUtils::fermerConnexion($lDb);
			return $lResultat;
		}
	}
	
	/**
	 * @name executerRequetesMultiples($pRequetes)
	 * @param array(string)
	 * @return mysqli_result
	 * @desc Exécute la requête passée en paramètre
	 */
	public static function executerRequetesMultiples($pRequetes) {
		$lDb = DbUtils::creerConnexion();		
		$lContinuer = true;
		foreach($pRequetes as $lRequete) {
			if($lContinuer)  {
				$lResultat = @mysqli_query($lDb,$lRequete);
			
				if (!$lResultat) {
					$lContinuer = false;
					
					// Initialisation du Logger
					$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
					$lLogger->setMask(Log::MAX(LOG_LEVEL));
					$lLogger->log(MessagesErreurs::ERR_603_MSG . " : " . mysqli_error($lDb),PEAR_LOG_DEBUG); // Maj des logs
			
					$lVr = new TemplateVR();
					$lVr->setValid(false);
					$lVr->getLog()->setValid(false);
					$lErreur = new VRerreur();
					$lErreur->setCode(MessagesErreurs::ERR_603_CODE);
					$lErreur->setMessage(MessagesErreurs::ERR_603_MSG);
					$lVr->getLog()->addErreur($lErreur);
						
					die($lVr->exportToJson());
				} 
			}
		}
		DbUtils::fermerConnexion($lDb);
		return $lResultat;
	}

	/**
	* @name executerRequeteInsertRetourId ($requete)
	* @param string 
	* @return integer
	* @desc Exécute la requête d'insertion passée en paramètre et retourne l'identifiant généré par la BDD
	*/	
	public static function executerRequeteInsertRetourId($pRequete) {
		$lDb = DbUtils::creerConnexion();
		$lResultat = @mysqli_query($lDb, $pRequete);
		if (!$lResultat) {			
			// Initialisation du Logger
			$lLogger = &Log::singleton('file', CHEMIN_FICHIER_LOGS);
			$lLogger->setMask(Log::MAX(LOG_LEVEL));
	    	$lLogger->log(MessagesErreurs::ERR_603_MSG . " : " . mysqli_error($lDb),PEAR_LOG_DEBUG); // Maj des logs
		
			$lVr = new TemplateVR();
			$lVr->setValid(false);
			$lVr->getLog()->setValid(false);
			$lErreur = new VRerreur();
			$lErreur->setCode(MessagesErreurs::ERR_603_CODE);
			$lErreur->setMessage(MessagesErreurs::ERR_603_MSG);
			$lVr->getLog()->addErreur($lErreur);
			
			die($lVr->exportToJson());
	    } else { 
			$lId = mysqli_insert_id($lDb); 
			DbUtils::fermerConnexion($lDb);
			return $lId;
	    }
	}
	
	/**
	 * @name prepareRequeteRecherche($pTable, $pChamps, $pTypeRecherche, $pTypeFiltre, $pCritereRecherche, $pTris)
	 * 
	 * 
	 * @param string nom de la table
	 * @param array(string) champs à récupérer dans la table
	 * 
	 * @param string Le type de critère de recherche
	 * @param array(string) champs à récupérer dans la table
	 * @param array(array(string, object)) Dictionnaire(champ, valeur)) contenant les champs à filtrer ainsi que la valeur du filtre
	 * @param array(array(string, string)) Dictionnaire(champ, sens) contenant les tris à appliquer
	 * @return string requête SQL
	 */
	public static function prepareRequeteRecherche($pTable, $pChamps, $pTypeRecherche, $pTypeFiltre, $pCritereRecherche, $pTypeTri, $pCritereTri) {

		if(	is_array($pChamps)
			&& is_array($pTypeRecherche)
			&& is_array($pTypeFiltre)
			&& is_array($pCritereRecherche)
			&& is_array($pCritereTri)
			&& is_array($pTypeTri)) {
				
	    	$lFiltres = array();
	    	$i = 0;
	    	foreach($pTypeRecherche as $lTypeRecherche) {
	    		$lLigne = array();
	    		$lLigne['champ'] = StringUtils::securiser($lTypeRecherche);
	    		
	    		if(is_array($pCritereRecherche[$i])) {
	    			$lTabCritereRecherche = array();
	    			foreach($pCritereRecherche[$i] as $lCritereRecherche) {
	    				array_push($lTabCritereRecherche,StringUtils::securiser($lCritereRecherche));
	    			}
	    			$lLigne['valeur'] = $lTabCritereRecherche;
	    		} else {
	    			$lLigne['valeur'] = StringUtils::securiser($pCritereRecherche[$i]);
	    		}
	    		
	    		array_push($lFiltres,$lLigne);
	    		$i++;
	    	}
	    			
			$lTris = array();
			$i = 0;
			foreach($pCritereTri as $lVal) {
				array_push($lTris,array('champ' => StringUtils::securiser($pTypeTri[$i]), 'sens' => StringUtils::securiser($lVal)));
				$i++;
			}    
				
			$lResultat = "SELECT ";
			
			$lResultat .= implode(',', $pChamps);
			
			$lResultat .= " FROM " . $pTable;
			
			$lFiltreNonVide = false;
			
			// si il y a des filtres
			if(sizeof($lFiltres) > 0 ) {
				$lFiltresString = " WHERE(";
	
				$i = 0;
				foreach ($lFiltres as $lFiltre) {
					if(!empty($lFiltre['champ']) ) {
						$lFiltresString .= $lFiltre['champ'];
						
						if(is_null($lFiltre['valeur'])) {
							$lFiltresString .= " IS NULL";
						} else {
							switch($pTypeFiltre[$i]) {
								case "LIKE":
									$lFiltresString .= " LIKE '%" . $lFiltre['valeur'] . "%'";
								break;
								
								case "=":
									$lFiltresString .= " = '" . $lFiltre['valeur'] . "'";
								break;
								
								case "<=":
									$lFiltresString .= " <= '" . $lFiltre['valeur'] . "'";
								break;
								
								case ">=":
									$lFiltresString .= " >= '" . $lFiltre['valeur'] . "'";
								break;
								
								case "<>":
									$lFiltresString .= " <> '" . $lFiltre['valeur'] . "'";
								break;
								
								case "<":
									$lFiltresString .= " < '" . $lFiltre['valeur'] . "'";
								break;
								
								case ">":
									$lFiltresString .= " > '" . $lFiltre['valeur'] . "'";
								break;
								
								case "in":
									if(is_array($lFiltre['valeur'])) {
										$lFiltresString .= " in ( ";
										foreach($lFiltre['valeur'] as $lVal) {
											$lFiltresString .= "'" . $lVal . "',";
										}
										// suppression de la dernière virgule
										$lFiltresString = substr($lFiltresString , 0, strlen($lFiltresString) - 1);
										$lFiltresString .= " )";								
									} else {
										$lFiltresString .= " in ( '" . $lFiltre['valeur'] . "' )";
									}
								break;
							}
						}
						$lFiltresString .= " AND ";
						$lFiltreNonVide = true;
					}
					$i++;
				}
				
				// suppression du dernier AND
				$lFiltresString = substr($lFiltresString , 0, sizeof($lResultat) - 6);
				//$lResultat .= '1'; 
				$lFiltresString .= ")";
			}
			
			// Permet de vérifier qu'il y a au moins un filtre non vide
			if($lFiltreNonVide) {
				$lResultat .= $lFiltresString;
			}
			
			// si il y a un tri
			if(isset($lTris[0]['champ']) && isset($lTris[0]['sens']) && !empty($lTris[0]['champ']) && !empty($lTris[0]['sens'])) {
				$lResultat .= " ORDER BY ";
					
				foreach($lTris as $lTri) {
					if(!empty($lTri['champ']) && !empty($lTri['sens'])) {		
						$lResultat .= $lTri['champ'] . " " . $lTri['sens'] . ",";
					}			
				}
									
				// suppression de la dernière virgule
				$lResultat = substr($lResultat, 0, sizeof($lResultat) - 2);
			}
			
			$lResultat .= ";";
			return $lResultat;
		}
		return false; 
	}
}
?>