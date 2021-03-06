<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 30/07/2011
// Fichier : GetListeProduitCommandeVR.php
//
// Description : Classe GetListeProduitCommandeVR
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_VR . "VRelement.php" );
include_once(CHEMIN_CLASSES_UTILS . "MessagesErreurs.php" );
include_once(CHEMIN_CLASSES . "DataTemplate.php");

/**
 * @name GetListeProduitCommandeVR
 * @author Julien PIERRE
 * @since 30/07/2011
 * @desc Classe représentant une GetListeProduitCommandeVR
 */
class GetListeProduitCommandeVR extends DataTemplate
{
	/**
	 * @var bool
	 * @desc Donne la validité de l'objet
	 */
	protected $mValid;

	/**
	 * @var VRelement
	 * @desc Le Log de l'objet
	 */
	protected $mLog;

	/**
	 * @var VRelement
	 * @desc L'Id de l'objet
	 */
	protected $mId;

	/**
	 * @var VRelement
	 * @desc Id_commande de la ProduitsBonDeCommandeVR
	 */
	protected $mId_commande;

	/**
	 * @var VRelement
	 * @desc Id_producteur de la ProduitsBonDeCommandeVR
	 */
	protected $mId_CompteProducteur;

	/**
	* @name GetListeProduitCommandeVR()
	* @return bool
	* @desc Constructeur
	*/
	function GetListeProduitCommandeVR() {
		$this->mValid = true;
		$this->mId = new VRelement();
		$this->mId_commande = new VRelement();
		$this->mId_CompteProducteur = new VRelement();
	}

	/**
	* @name getValid()
	* @return bool
	* @desc Renvoie la validite de l'élément
	*/
	public function getValid() {
		return $this->mValid;
	}

	/**
	* @name setValid($pValid)
	* @param bool
	* @desc Remplace la validite de l'élément par $pValid
	*/
	public function setValid($pValid) {
		$this->mValid = $pValid;
	}

	/**
	* @name getLog()
	* @return VRelement
	* @desc Renvoie le VRelement Log
	*/
	public function getLog() {
		return $this->mLog;
	}

	/**
	* @name setLog($pLog)
	* @param VRelement
	* @desc Remplace le VRelement Log par $pLog
	*/
	public function setLog($pLog) {
		$this->mLog = $pLog;
	}

	/**
	* @name getId()
	* @return VRelement
	* @desc Renvoie le VRelement Id
	*/
	public function getId() {
		return $this->mId;
	}

	/**
	* @name setId($pId)
	* @param VRelement
	* @desc Remplace le VRelement Id par $pId
	*/
	public function setId($pId) {
		$this->mId = $pId;
	}

	/**
	* @name getId_commande()
	* @return VRelement
	* @desc Renvoie le VRelement mId_commande
	*/
	public function getId_commande() {
		return $this->mId_commande;
	}

	/**
	* @name setId_commande($pId_commande)
	* @param VRelement
	* @desc Remplace le mId_commande par $pId_commande
	*/
	public function setId_commande($pId_commande) {
		$this->mId_commande = $pId_commande;
	}

	/**
	* @name getId_CompteProducteur()
	* @return VRelement
	* @desc Renvoie le VRelement mId_CompteProducteur
	*/
	public function getId_CompteProducteur() {
		return $this->mId_CompteProducteur;
	}

	/**
	* @name setId_CompteProducteur($pId_CompteProducteur)
	* @param VRelement
	* @desc Remplace le mId_CompteProducteur par $pId_CompteProducteur
	*/
	public function setId_CompteProducteur($pId_CompteProducteur) {
		$this->mId_CompteProducteur = $pId_CompteProducteur;
	}
}
?>