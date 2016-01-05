<?php
if(isset($_SESSION['cx']) && $_SESSION['cx'] == 1) {
	require_once("../configuration/ApiOVH.php");
	require_once("../vendor/autoload.php");
	
	
	
	if(isset($_GET['red'])) {
		// Page de retour
		$redirection = "http://" . $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'] . "?m=Ovh&ck";
	
		// Les droits
		$rights = array( (object) [
				'method'    => 'POST',
				'path'      => '/email/domain*'
		], [
	
				'method'    => 'DELETE',
				'path'      => '/email/domain*'
		]);
	
		// Get credentials
		$conn = new \Ovh\Api(APPLICATION_KEY, APPLICATION_SECRET, ENDPOINT);
		$credentials = $conn->requestCredentials($rights, $redirection);
	
		// Save consumer key and redirect to authentication page
		$_SESSION['consumer_key'] = $credentials["consumerKey"];
		header('location: '. $credentials["validationUrl"]);
	
	} else {
?>
		<div class="com-widget-window ui-widget-content menu-lien btn-menu ui-corner-all">
			<div class="com-widget-window ui-widget ui-widget-header ui-corner-all">Liaison OVH</div>
			<div class="com-center">
<?php 
		if(isset($_GET['ck'])) {
			// Enregistrement de la configuration
			 $fp = fopen('../configuration/ApiOVH.php', 'w');
			 fwrite($fp,"<?php\n");
			 fwrite($fp,"//****************************************************************\n");
			 fwrite($fp,"//\n");
			 fwrite($fp,"// Createur : Julien PIERRE\n");
			 fwrite($fp,"// Date de creation : 11/12/2015\n");
			 fwrite($fp,"// Fichier : ApiOVH.php\n");
			 fwrite($fp,"//\n");
			 fwrite($fp,"// Description : Les constantes de WebServices\n");
			 fwrite($fp,"//\n");
			 fwrite($fp,"//****************************************************************\n");
			 fwrite($fp,"define(\"APPLICATION_KEY\", \"4Sxdyhq6tnp9j7Z8\");\n");
			 fwrite($fp,"define(\"APPLICATION_SECRET\", \"s4suawaP9CDCJdu5pz7IVv5p9QNBlGtB\");\n");
			 fwrite($fp,"define(\"CONSUMER_KEY\", \"" . $_SESSION['consumer_key'] . "\");\n");
			 fwrite($fp,"define(\"ENDPOINT\", \"ovh-eu\");\n");
			 fclose($fp);
			 
			 echo 	"<span class=\"com-center\">Liaison OVH mise à jour.</span><br/><br/>";
			
		} 
?>
				<a href="./index.php?m=Ovh&amp;red">
					<button class="com-btn-edt ui-state-default ui-corner-all com-button com-center">Mettre à jour la liaison OVH</button>
				</a>
			</div>
		</div>
<?php 

	}
}
?>
