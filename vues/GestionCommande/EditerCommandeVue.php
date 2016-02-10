<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 27/10/2010
// Fichier : EditerCommandeVue.php
//
// Description : Retourne les infos sur la commande
//
//****************************************************************

// Vérification de la bonne connexion de l'adherent dans le cas contraire redirection vers le formulaire de connexion
if( isset($_SESSION[DROIT_ID]) && ( isset($_SESSION[MOD_GESTION_COMMANDE]) || isset($_SESSION[DROIT_SUPER_ZEYBU]) ) ) {

	if(isset($_POST['pParam'])) {	
		$lParam = json_decode($_POST["pParam"],true);
		
		if(isset($lParam["fonction"])) {		
			include_once(CHEMIN_CLASSES_CONTROLEURS . MOD_GESTION_COMMANDE . "/EditerCommandeControleur.php");						
			$lControleur = new EditerCommandeControleur();
			
			switch($lParam["fonction"]) {					
				case "afficher":
						echo $lControleur->getInfoCommande($lParam)->exportToJson();
						$lLogger->log("Affichage de la vue EditerCommande par le compte de l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "pause":
						echo $lControleur->setPause($lParam)->exportToJson();
						$lLogger->log("Mise en pause du marché par le compte de l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "play":
						echo $lControleur->setPlay($lParam)->exportToJson();
						$lLogger->log("Mise en play du marché par le compte de l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "cloturer":
						echo $lControleur->setCloturer($lParam)->exportToJson();
						$lLogger->log("Cloture du marché par le compte de l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
										
				case "modifierInformationMarche":
						echo $lControleur->modifierInformationMarche($lParam)->exportToJson();
						$lLogger->log("Modification es informations du marché par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "supprimerProduitMarche":
						echo $lControleur->supprimerProduitMarche($lParam)->exportToJson();
						$lLogger->log("Suppression de produit du marché par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "detailProduitMarche":
						echo $lControleur->detailProduitMarche($lParam)->exportToJson();
						$lLogger->log("Détail du produit du marché par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "modifierProduitMarche":
						echo $lControleur->modifierProduitMarche($lParam)->exportToJson();
						$lLogger->log("Modification du produit du marché par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "ajouterProduitMarche":
						echo $lControleur->ajouterProduitMarche($lParam)->exportToJson();
						$lLogger->log("Ajout du produit du marché par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;	
					
				case "listeFerme":
						echo $lControleur->getListeFerme()->exportToJson();
						$lLogger->log("Affichage de la liste des fermes dans la vue EditerCommande par l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "listeProduit":
						echo $lControleur->getListeProduit($lParam)->exportToJson();
						$lLogger->log("Affichage de la liste des produits dans la vue EditerCommande par l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "listeModeleLot":
						echo $lControleur->getModeleLot($lParam)->exportToJson();
						$lLogger->log("Affichage de la liste des modeles de lot dans la vue EditerCommande par l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "autorisationSupprimerLot":
						echo $lControleur->autorisationSupprimerLot($lParam)->exportToJson();
						$lLogger->log("Demande d'autorisation à la suppression de lot dans la vue EditerCommande par l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "nbResaNonAdherent":
						echo $lControleur->nbResaNonAdherent($lParam)->exportToJson();
						$lLogger->log("Demande du nombre de réservation non adhérent dans la vue EditerCommande par l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;

				default:
					$lLogger->log("Demande d'accés à EditerCommande sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					header('location:./index.php');
					break;
			}	
		} else {
			$lLogger->log("Demande d'accés à EditerCommande sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
			header('location:./index.php');
		}
	} else {
		$lLogger->log("Demande d'accés à EditerCommande sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
		header('location:./index.php');
	}
} else {
	$lLogger->log("Demande d'accés sans autorisation à EditerCommande",PEAR_LOG_INFO);	// Maj des logs
	header('location:./index.php?cx=1');
}
?>