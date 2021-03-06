<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 20/03/2012
// Fichier : ReservationMarcheResponse.php
//
// Description : Classe ReservationMarcheResponse
//
//****************************************************************
include_once(CHEMIN_CLASSES . "DataTemplate.php");

/**
 * @name ReservationMarcheResponse
 * @author Julien PIERRE
 * @since 20/03/2012
 * @desc Classe représentant une ReservationMarcheResponse
 */
class ReservationMarcheResponse extends DataTemplate
{
	/**
	 * @var bool
	 * @desc Donne la validité de l'objet
	 */
	protected $mValid = true;

	/**
	 * @var array(MarcheVO)
	 * @desc La Marche
	 */
	protected $mMarche;
		
	/**
	 * @var ReservationVO
	 * @desc Les reservations
	 */
	protected $mReservation;
	
	/**
	 * @var AdherentViewVO
	 * @desc L'adhérent
	 */
	protected $mAdherent;
	
	/**
	* @name ReservationMarcheResponse()
	* @desc Le constructeur de InfoAchatMarcheResponse
	*/	
	public function ReservationMarcheResponse() {
		$this->mValid = true;
		$this->mMarche = array();
		$this->mReservation = array();
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
	* @name getMarche()
	* @return array(MarcheVO)
	* @desc Renvoie le Marche
	*/
	public function getMarche() {
		return $this->mMarche;
	}

	/**
	* @name setMarche($pMarche)
	* @param array(MarcheVO)
	* @desc Remplace le Marche par $pMarche
	*/
	public function setMarche($pMarche) {
		$this->mMarche = $pMarche;
	}
	
	/**
	* @name addMarche($pMarche)
	* @param MarcheVO
	* @desc Ajoute le $pMarche à Marche
	*/
	public function addMarche($pMarche) {
		array_push($this->mMarche, $pMarche);
	}

	/**
	* @name getReservation()
	* @return ReservationVO
	* @desc Renvoie le Reservation
	*/
	public function getReservation() {
		return $this->mReservation;
	}

	/**
	* @name setReservation($pReservation)
	* @param ReservationVO
	* @desc Remplace le Reservation par $pReservation
	*/
	public function setReservation($pReservation) {
		$this->mReservation = $pReservation;
	}
		
	/**
	* @name getAdherent()
	* @return AdherentViewVO
	* @desc Renvoie l'Adherent
	*/
	public function getAdherent() {
		return $this->mAdherent;
	}

	/**
	* @name setAdherent($pAdherent)
	* @param AdherentViewVO
	* @desc Remplace l'Adherent par $pAdherent
	*/
	public function setAdherent($pAdherent) {
		$this->mAdherent = $pAdherent;
	}
}
?>