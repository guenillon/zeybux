<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 12/06/2011
// Fichier : RechargerCompteVue.php
//
// Description : Permet de recharger le compte d'un adhérent
//
//****************************************************************

// Vérification de la bonne connexion de l'adherent dans le cas contraire redirection vers le formulaire de connexion
if( isset($_SESSION[DROIT_ID]) && ( isset($_SESSION[MOD_RECHARGEMENT_COMPTE]) || isset($_SESSION[DROIT_SUPER_ZEYBU]) ) ) {
	if(isset($_POST['pParam'])) {
		$lParam = json_decode($_POST["pParam"],true);
		
		if(isset($lParam["fonction"])) {
			include_once(CHEMIN_CLASSES_CONTROLEURS . MOD_RECHARGEMENT_COMPTE . "/RechargerCompteControleur.php");							
			$lControleur = new RechargerCompteControleur();
			
			switch($lParam["fonction"]) {					
				case "listeAdherent":
						echo $lControleur->getListeAdherent()->exportToJson();
						$lLogger->log("Affichage de la liste des adhérents : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "infoRechargement":
						echo $lControleur->getInfoRechargement($lParam)->exportToJson();
						$lLogger->log("Affichage des infos de rechargement : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "rechargerCompte":
						echo $lControleur->rechargerCompte($lParam)->exportToJson();
						$lLogger->log("Rechargement de compte par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				default:
					$lLogger->log("Demande d'accés à RechargerCompteVue sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					header('location:./index.php');
					break;
			}
		} else {
			$lLogger->log("Demande d'accés à RechargerCompteVue sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
			header('location:./index.php');
		}
	} else {
		$lLogger->log("Demande d'accés à RechargerCompteVue sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
		header('location:./index.php');
	}
} else {
	$lLogger->log("Demande d'accés sans autorisation à RechargerCompteVue",PEAR_LOG_INFO);	// Maj des logs
	header('location:./index.php?cx=1');
}
?>