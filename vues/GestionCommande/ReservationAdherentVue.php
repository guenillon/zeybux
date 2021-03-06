<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 27/10/2010
// Fichier : ReservationAdherentVue.php
//
// Description : Retourne les infos sur la reservation d'un adherent
//
//****************************************************************

// Vérification de la bonne connexion de l'adherent dans le cas contraire redirection vers le formulaire de connexion
if( isset($_SESSION[DROIT_ID]) && ( isset($_SESSION[MOD_GESTION_COMMANDE]) || isset($_SESSION[DROIT_SUPER_ZEYBU]) ) ) {
	if(isset($_POST['pParam'])) {
		$lParam = json_decode($_POST["pParam"],true);
		
		if(isset($lParam["fonction"])) {
			include_once(CHEMIN_CLASSES_CONTROLEURS . MOD_GESTION_COMMANDE . "/ReservationAdherentControleur.php");							
			$lControleur = new ReservationAdherentControleur();
			
			switch($lParam["fonction"]) {
				case "afficherReservation":
						echo $lControleur->getReservation($lParam)->exportToJson();					
						$lLogger->log("Affichage de la vue ReservationAdherent par le compte de l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "detailProduit":
						echo $lControleur->getDetailProduit($lParam)->exportToJson();
						$lLogger->log("Affichage du détail produit dans ReservationAdherent par l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "modifierReservation":
						echo $lControleur->modifierReservation($lParam)->exportToJson();					
						$lLogger->log("Modification de la réservation par le compte de l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs		
					break;
					
				case "supprimerReservation":
						echo $lControleur->supprimerReservation($lParam)->exportToJson();
						$lLogger->log("Suppression de la réservation par le compte de l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				default:
					$lLogger->log("Demande d'accés à ReservationAdherent sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					header('location:./index.php');
					break;
			}
		} else {
			$lLogger->log("Demande d'accés à ReservationAdherent sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
			header('location:./index.php');
		}
	} else {
		$lLogger->log("Demande d'accés à ReservationAdherent sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
		header('location:./index.php');
	}
} else {
	$lLogger->log("Demande d'accés sans autorisation à ReservationAdherent",PEAR_LOG_INFO);	// Maj des logs
	header('location:./index.php?cx=1');
}
?>