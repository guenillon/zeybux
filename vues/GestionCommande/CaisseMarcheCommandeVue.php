<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 08/09/2013
// Fichier : CaisseMarcheCommandeVue.php
//
// Description : À REMPLIR
//
//****************************************************************

// Vérification de la bonne connexion de l'adherent dans le cas contraire redirection vers le formulaire de connexion
if( isset($_SESSION[DROIT_ID]) && ( isset($_SESSION[MOD_GESTION_COMMANDE]) || isset($_SESSION[DROIT_SUPER_ZEYBU]) ) ) {
	if(isset($_POST['pParam'])) {
		$lParam = json_decode($_POST["pParam"],true);
		
		if(isset($lParam["fonction"])) {
			include_once(CHEMIN_CLASSES_CONTROLEURS . MOD_GESTION_COMMANDE . "/EditerAchatControleur.php");						
			$lControleur = new EditerAchatControleur();
			
			switch($lParam["fonction"]) {
					
				case "infoAchat":
						echo $lControleur->getInfoAchatMarche($lParam)->exportToJson();
						$lLogger->log("Affichage de la vue Gestion Commande InfoAchat par l'Adhérent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					break;
				
				case "acheter":
						$lResponse = $lControleur->enregistrerAchat($lParam);
						echo $lResponse->exportToJson();
		
						if($lResponse->getValid()) {
							$lLogger->log("Enregistrement d'un achat par l'adherent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
						} else {
							$lLogger->log("Echec de l'enregistrement d'un achat par l'adherent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
						}						
					break;
					
				case "supprimer":
						echo $lControleur->supprimerAchat($lParam)->exportToJson();
						$lLogger->log("Suppressiont d'un achat par l'adherent : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs					
					break;

				default:
					$lLogger->log("Demande d'accés à GestionCommande sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
					header('location:./index.php');
					break;
			}
		} else {
			$lLogger->log("Demande d'accés à GestionCommande sans identifiant commande par : " . $_SESSION[ID_CONNEXION],PEAR_LOG_INFO);	// Maj des logs
			header('location:./index.php');
		}
	}
} else {
	$lLogger->log("Demande d'accés sans autorisation à GestionCommande",PEAR_LOG_INFO);	// Maj des logs
	header('location:./index.php?cx=1');
}
?>