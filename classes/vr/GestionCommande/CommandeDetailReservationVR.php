<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 14/10/2010
// Fichier : CommandeDetailReservationVR.php
//
// Description : Classe CommandeDetailReservationVR
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_VR . "VRelement.php" );
include_once(CHEMIN_CLASSES_UTILS . "MessagesErreurs.php" );
include_once(CHEMIN_CLASSES . "DataTemplate.php");

/**
 * @name CommandeDetailReservationVR
 * @author Julien PIERRE
 * @since 14/10/2010
 * @desc Classe représentant une CommandeDetailReservationVR
 */
class CommandeDetailReservationVR extends DataTemplate
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
	 * @desc StoQuantite de la CommandeDetailReservationVR
	 */
	protected $mStoQuantite;
	
	/**
	 * @var VRelement
	 * @desc mStoIdProduit de la CommandeDetailReservationVR
	 */
	protected $mStoIdProduit;

	/**
	 * @var VRelement
	 * @desc StoIdDetailCommande de la CommandeDetailReservationVR
	 */
	protected $mStoIdDetailCommande;

	/**
	* @name CommandeDetailReservationVR()
	* @return bool
	* @desc Constructeur
	*/
	function CommandeDetailReservationVR() {
		$this->mValid = true;
		$this->mLog = new VRelement();
		$this->mId = new VRelement();
		$this->mStoQuantite = new VRelement();
		$this->mStoIdProduit = new VRelement();
		$this->mStoIdDetailCommande = new VRelement();
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
	* @name getStoQuantite()
	* @return VRelement
	* @desc Renvoie le VRelement mStoQuantite
	*/
	public function getStoQuantite() {
		return $this->mStoQuantite;
	}

	/**
	* @name setStoQuantite($pStoQuantite)
	* @param VRelement
	* @desc Remplace le mStoQuantite par $pStoQuantite
	*/
	public function setStoQuantite($pStoQuantite) {
		$this->mStoQuantite = $pStoQuantite;
	}

	/**
	* @name getStoIdProduit()
	* @return VRelement
	* @desc Renvoie le VRelement StoIdProduit
	*/
	public function getStoIdProduit() {
		return $this->mStoIdProduit;
	}

	/**
	* @name setStoIdProduit($pStoIdProduit)
	* @param VRelement
	* @desc Remplace le StoIdProduit par $pStoIdProduit
	*/
	public function setStoIdProduit($pStoIdProduit) {
		$this->mStoIdProduit = $pStoIdProduit;
	}

	/**
	* @name getStoIdDetailCommande()
	* @return VRelement
	* @desc Renvoie le VRelement mStoIdDetailCommande
	*/
	public function getStoIdDetailCommande() {
		return $this->mStoIdDetailCommande;
	}

	/**
	* @name setStoIdDetailCommande($pStoIdDetailCommande)
	* @param VRelement
	* @desc Remplace le mStoIdDetailCommande par $pStoIdDetailCommande
	*/
	public function setStoIdDetailCommande($pStoIdDetailCommande) {
		$this->mStoIdDetailCommande = $pStoIdDetailCommande;
	}

}
?>