<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 22/01/2016
// Fichier : ReservationSansAchatVue.php
//
// Description : Retourne les infos sur les réservations sans achat
//
//****************************************************************

// Vérification de la bonne connexion de l'adherent dans le cas contraire redirection vers le formulaire de connexion
if( isset($_SESSION[DROIT_ID]) && ( isset($_SESSION[MOD_GESTION_COMMANDE]) || isset($_SESSION[DROIT_SUPER_ZEYBU]) ) ) {
	if(isset($_POST['pParam'])) {
		$lParam = json_decode($_POST["pParam"],true);
		
		if(isset($lParam["fonction"])) {
			include_once(CHEMIN_CLASSES_CONTROLEURS . MOD_GESTION_COMMANDE . "/ReservationSansAchatControleur.php");							
			$lControleur = new ReservationSansAchatControleur();
			
			switch($lParam["fonction"]) {
				case "afficher":
						echo $lControleur->getReservation($lParam)->exportToJson();					
						$lLogger->log("Affichage de la vue ReservationSansAchat par le compte de l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				default:
					$lLogger->log("Demande d'accés à ReservationSansAchat sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					header('location:./index.php');
					break;
			}
		} else {
			$lLogger->log("Demande d'accés à ReservationSansAchat sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
			header('location:./index.php');
		}
	} else if(isset($_POST['fonction'])) {
		include_once(CHEMIN_CLASSES_CONTROLEURS . MOD_GESTION_COMMANDE . "/ReservationSansAchatControleur.php");						
		$lControleur = new ReservationSansAchatControleur();
		
		switch($_POST['fonction']) {					
			case "export":
					if(isset($_POST['id_marche']) ) {
						$lParam = array();
						$lParam['id_marche'] = $_POST['id_marche'];						
						echo $lControleur->getReservationExport($lParam);
					} else {
						$lLogger->log("Demande d'accés à ReservationSansAchat pour export des réservations sans identifiant par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
						header('location:./index.php');
					}
				break;

			default:
				$lLogger->log("Demande d'accés à ReservationSansAchat sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
				header('location:./index.php');
				break;
		}		
	} else {
		$lLogger->log("Demande d'accés à ReservationSansAchat sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
		header('location:./index.php');
	}
} else {
	$lLogger->log("Demande d'accés sans autorisation à ReservationSansAchat",PEAR_LOG_INFO);	// Maj des logs
	header('location:./index.php?cx=1');
}
?>