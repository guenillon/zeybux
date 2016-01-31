<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 28/01/2016
// Fichier : NbReservationResponse.php
//
// Description : Classe NbReservationResponse
//
//****************************************************************
include_once(CHEMIN_CLASSES . "DataTemplate.php");

/**
 * @name NbReservationResponse
 * @author Julien PIERRE
 * @since 28/01/2016
 * @desc Classe représentant une NbReservationResponse
 */
class NbReservationResponse extends DataTemplate
{
	/**
	 * @var bool
	 * @desc Donne la validité de l'objet
	 */
	protected $mValid = true;

	/**
	 * @var AdherentViewVO
	 * @desc integer
	 */
	protected $mNb = false;
		
	/**
	* @name NbReservationResponse()
	* @desc Le constructeur de NbReservationResponse
	*/	
	public function NbReservationResponse() {
		$this->mValid = true;
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
	* @name getNb()
	* @return integer
	* @desc Renvoie mNb
	*/
	public function getNb() {
		return $this->mNb;
	}

	/**
	* @name setNb($pNb)
	* @param integer
	* @desc Remplace mNb de l'élément par $pNb
	*/
	public function setNb($pNb) {
		$this->mNb = $pNb;
	}
}
?>