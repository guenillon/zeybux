<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 23/01/2016
// Fichier : InscriptionVue.php
//
// Description : Script d'inscription
//
//****************************************************************

	
// Inclusion des classes
include_once(CHEMIN_CLASSES_UTILS . "Template.php");
include_once(CHEMIN_CLASSES_UTILS . "StringUtils.php");
include_once(CHEMIN_CLASSES_UTILS . "TestFonction.php");
include_once(CHEMIN_CLASSES_UTILS . "InfobullesUtils.php");
	
// Constante de titre de la page
define("TITRE", ZEYBUX_TITRE_DEBUT . "Inscription - " . ZEYBUX_TITRE_FIN);

// Préparation de l'affichage
$lTemplate = new Template(CHEMIN_TEMPLATE);	
$lTemplate->set_filenames( array('page' => 'Page.html') );

// Entete
$lTemplate->set_filenames( array('entete' =>  COMMUN_TEMPLATE . 'Entete.html') );
$lTemplate->assign_vars( array( 'TITRE' => TITRE) );
InfobullesUtils::generer($lTemplate); // Messages d'erreur

if(isset($_POST["nom"])
	&& isset($_POST["prenom"])
	&& isset($_POST["dateNaissance"])
	&& isset($_POST["commentaire"])
	&& isset($_POST["courrielPrincipal"])
	&& isset($_POST["courrielSecondaire"])
	&& isset($_POST["telephonePrincipal"])
	&& isset($_POST["telephoneSecondaire"])
	&& isset($_POST["adresse"])
	&& isset($_POST["codePostal"])
	&& isset($_POST["ville"])) {
		
	include_once(CHEMIN_CLASSES_CONTROLEURS . MOD_IDENTIFICATION . "/InscriptionControleur.php");						
	$lControleur = new InscriptionControleur();

	$lParam = array(			
		"nom" => $_POST["nom"],
		"prenom" => $_POST["prenom"],
		"dateNaissance" => $_POST["dateNaissance"],
		"commentaire" => $_POST["commentaire"],
		"courrielPrincipal" => $_POST["courrielPrincipal"],
		"courrielSecondaire" => $_POST["courrielSecondaire"],
		"telephonePrincipal" => $_POST["telephonePrincipal"],
		"telephoneSecondaire" => $_POST["telephoneSecondaire"],
		"adresse" => $_POST["adresse"],
		"codePostal" => $_POST["codePostal"],
		"ville" => $_POST["ville"]
	);
	$lPage = $lControleur->ajoutAdherent($lParam);

	if($lPage->getValid()) {
		// Body		
		$lTemplate->set_filenames( array('body' => MOD_IDENTIFICATION . '/' . 'InscriptionConfirm.html') );
	} else {
		$_SESSION['val'] = $lParam;
		$_SESSION['msg'] = $lPage->exportToArray();
		header('location:./index.php?m=IdentificationHTML&v=Inscription');
	}
} else {
	// Body		
	$lTemplate->set_filenames( array('body' => MOD_IDENTIFICATION . '/' . 'InscriptionForm.html') );
}

$lTemplate->assign_var_from_handle('CONTENU', 'body');	

// Pied de Page
$lTemplate->set_filenames( array('piedPage' => COMMUN_TEMPLATE . 'PiedPage.html') );
$lTemplate->assign_vars( array( 
		'PROP_NOM' =>	PROP_NOM,
		'PROP_ADRESSE' =>	PROP_ADRESSE,
		'PROP_CODE_POSTAL' =>	PROP_CODE_POSTAL,
		'PROP_VILLE' =>	PROP_VILLE,
		'PROP_TEL' =>	PROP_TEL,
		'PROP_MEL' =>	PROP_MEL,
		'ZEYBUX_TITRE_SITE' =>	ZEYBUX_TITRE_SITE) );

$lTemplate->assign_var_from_handle('ENTETE', 'entete');
$lTemplate->assign_var_from_handle('PIED_PAGE', 'piedPage');

// Affichage
$lTemplate->pparse('page');

?>