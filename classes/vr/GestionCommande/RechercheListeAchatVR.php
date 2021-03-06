<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 08/09/2013
// Fichier : RechercheListeAchatVR.php
//
// Description : Classe RechercheListeAchatVR
//
//****************************************************************
// Inclusion des classes
include_once(CHEMIN_CLASSES_VR . "TemplateVR.php" );
include_once(CHEMIN_CLASSES_UTILS . "MessagesErreurs.php" );

/**
 * @name RechercheListeAchatVR
 * @author Julien PIERRE
 * @since 08/09/2013
 * @desc Classe représentant une RechercheListeAchatVR
 */
class RechercheListeAchatVR extends TemplateVR
{
	/**
	 * @var VRelement
	 * @desc L'IdMarche de l'objet
	 */
	protected $mIdMarche;

	/**
	 * @var VRelement
	 * @desc Date de debut de la RechercheListeAchatVR
	 */
	protected $mDateDebut;

	/**
	 * @var VRelement
	 * @desc Date de fin de la RechercheListeAchatVR
	 */
	protected $mDateFin;

	/**
	* @name RechercheListeAchatVR()
	* @return bool
	* @desc Constructeur
	*/
	function RechercheListeAchatVR() {
		parent::__construct();		
		$this->mValid = true;
		$this->mLog = new VRelement();
		$this->mIdMarche = new VRelement();
		$this->mDateDebut = new VRelement();
		$this->mDateFin = new VRelement();
	}
	/**
	* @name getIdMarche()
	* @return VRelement
	* @desc Renvoie le VRelement IdMarche
	*/
	public function getIdMarche() {
		return $this->mIdMarche;
	}

	/**
	* @name setIdMarche($pIdMarche)
	* @param VRelement
	* @desc Remplace le VRelement IdMarche par $pIdMarche
	*/
	public function setIdMarche($pIdMarche) {
		$this->mIdMarche = $pIdMarche;
	}

	/**
	* @name getDateDebut()
	* @return VRelement
	* @desc Renvoie le VRelement mDateDebut
	*/
	public function getDateDebut() {
		return $this->mDateDebut;
	}

	/**
	* @name setDateDebut($pDateDebut)
	* @param VRelement
	* @desc Remplace le mDateDebut par $pDateDebut
	*/
	public function setDateDebut($pDateDebut) {
		$this->mDateDebut = $pDateDebut;
	}

	/**
	* @name getDateFin()
	* @return VRelement
	* @desc Renvoie le VRelement mDateFin
	*/
	public function getDateFin() {
		return $this->mDateFin;
	}

	/**
	* @name setDateFin($pDateFin)
	* @param VRelement
	* @desc Remplace le mDateFin par $pDateFin
	*/
	public function setDateFin($pDateFin) {
		$this->mDateFin = $pDateFin;
	}
}
?>