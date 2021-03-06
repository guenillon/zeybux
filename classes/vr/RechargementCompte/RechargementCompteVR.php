<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 21/09/2010
// Fichier : RechargementCompteVR.php
//
// Description : Classe RechargementCompteVR
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_VR . "VRelement.php" );
include_once(CHEMIN_CLASSES_UTILS . "MessagesErreurs.php" );
include_once(CHEMIN_CLASSES . "DataTemplate.php");

/**
 * @name RechargementCompteVR
 * @author Julien PIERRE
 * @since 21/09/2010
 * @desc Classe représentant une RechargementCompteVR
 */
class RechargementCompteVR extends DataTemplate
{
	/**
	 * @var bool
	 * @desc Donne la validté de l'objet
	 */
	protected $mValid;

	/**
	 * @var VRelement
	 * @desc Le Log de l'objet
	 */
	protected $mLog;

	/**
	 * @var VRelement
	 * @desc Montant de la RechargementCompteVR
	 */
	protected $mMontant;

	/**
	 * @var VRelement
	 * @desc TypePaiement de la RechargementCompteVR
	 */
	protected $mTypePaiement;

	/**
	 * @var array(ChampComplementaireVR)
	 * @desc ChampComplementaire de la RechargementCompteVR
	 */
	protected $mChampComplementaire;

	/**
	* @name RechargementCompteVR()
	* @return bool
	* @desc Constructeur
	*/
	function RechargementCompteVR() {
		$this->mValid = true;
		$this->mLog = new VRelement();
		$this->mMontant = new VRelement();
		$this->mTypePaiement = new VRelement();
		$this->mChampComplementaire = array();
	}

	/**
	* @name getValid()
	* @return bool
	* @desc Renvoie la Validite de l'élément
	*/
	public function getValid() {
		return $this->mValid;
	}

	/**
	* @name setValid($pValid)
	* @param bool
	* @desc Remplace la Validite de l'élément par $pValid
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
	* @name getMontant()
	* @return VRelement
	* @desc Renvoie le VRelement mMontant
	*/
	public function getMontant() {
		return $this->mMontant;
	}

	/**
	* @name setMontant($pMontant)
	* @param VRelement
	* @desc Remplace le mMontant par $pMontant
	*/
	public function setMontant($pMontant) {
		$this->mMontant = $pMontant;
	}

	/**
	* @name getTypePaiement()
	* @return VRelement
	* @desc Renvoie le VRelement mTypePaiement
	*/
	public function getTypePaiement() {
		return $this->mTypePaiement;
	}

	/**
	* @name setTypePaiement($pTypePaiement)
	* @param VRelement
	* @desc Remplace le mTypePaiement par $pTypePaiement
	*/
	public function setTypePaiement($pTypePaiement) {
		$this->mTypePaiement = $pTypePaiement;
	}

	/**
	* @name getChampComplementaire()
	* @return array(ChampComplementaireVR)
	* @desc Renvoie le array(ChampComplementaireVR) mChampComplementaire
	*/
	public function getChampComplementaire() {
		return $this->mChampComplementaire;
	}

	/**
	* @name setChampComplementaire($pChampComplementaire)
	* @param array(ChampComplementaireVR)
	* @desc Remplace le array(ChampComplementaireVR) par $pChampComplementaire
	*/
	public function setChampComplementaire($pChampComplementaire) {
		$this->mChampComplementaire = $pChampComplementaire;
	}
	
	/**
	 * @name addChampComplementaire($pChampComplementaire)
	 * @param array(ChampComplementaireVR)
	 * @desc Ajoute $pChampComplementaire à mChampComplementaire 
	 */
	public function addChampComplementaire($pChampComplementaire) {
		array_push($this->mChampComplementaire, $pChampComplementaire);
	}
}
?>