<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 31/03/2010
// Fichier : AjoutCommandeVue.php
//
// Description : À REMPLIR
//
//****************************************************************

// Vérification de la bonne connexion de l'adherent dans le cas contraire redirection vers le formulaire de connexion
if( isset($_SESSION[DROIT_ID]) && ( isset($_SESSION[MOD_GESTION_COMMANDE]) || isset($_SESSION[DROIT_SUPER_ZEYBU]) ) ) {

	if(isset($_POST['pParam'])) {
		$lParam = json_decode($_POST["pParam"],true);		

		if(isset($lParam["fonction"])) {
			include_once(CHEMIN_CLASSES_CONTROLEURS . MOD_GESTION_COMMANDE . "/AjoutCommandeControleur.php");						
			$lControleur = new AjoutCommandeControleur();
			
			switch($lParam["fonction"]) {					
				case "afficher":
						echo $lControleur->getListeFerme()->exportToJson();
						$lLogger->log("Affichage de la vue AjoutCommande par l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "dupliquer":
						echo $lControleur->getInfoDupliquerMarche($lParam)->exportToJson();
						$lLogger->log("Affichage de la vue Duplication de marché par l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;	

				case "listeProduit":
						echo $lControleur->getListeProduit($lParam)->exportToJson();
						$lLogger->log("Affichage de la liste des produits dans la vue AjoutCommande par l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				case "listeModeleLot":
						echo $lControleur->getModeleLot($lParam)->exportToJson();
						$lLogger->log("Affichage de la liste des modeles de lot dans la vue AjoutCommande par l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;				
				
				case "ajouter":
						echo $lControleur->ajouterMarche($lParam)->exportToJson();
						$lLogger->log("Ajout d'un marche par l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
					
				default:
					$lLogger->log("Demande d'accés à AjoutCommande sans identifiant par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					header('location:./index.php');
					break;
			}
		} else {
			$lLogger->log("Demande d'accés à AjoutCommande sans identifiant par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
			header('location:./index.php');
		}
	} else {
		$lLogger->log("Demande d'accés à AjoutCommande sans fonction par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
		header('location:./index.php');
	}
} else {
	$lLogger->log("Demande d'accés sans autorisation à AjoutCommande",PEAR_LOG_INFO);	// Maj des logs
	header('location:./index.php?cx=1');
}
?>
