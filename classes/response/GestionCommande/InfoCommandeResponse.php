<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 27/02/2011
// Fichier : InfoCommandeResponse.php
//
// Description : Classe InfoCommandeResponse
//
//****************************************************************
include_once(CHEMIN_CLASSES . "DataTemplate.php");

/**
 * @name InfoCommandeResponse
 * @author Julien PIERRE
 * @since 27/02/2011
 * @desc Classe représentant une InfoCommandeResponse
 */
class InfoCommandeResponse extends DataTemplate
{
	/**
	 * @var bool
	 * @desc Donne la validité de l'objet
	 */
	protected $mValid;
	
	/**
	 * @var array(InfoCommandeViewVO)
	 * @desc Les infos sur la commande
	 */
	protected $mInfoCommande;
	
	/**
	 * @var CommandeVO
	 * @desc Les infos générales sur le marché
	 */
	protected $mDetailMarche;
	
	/**
	 * @var NbResaAchat
	 * @desc Les réservations et Achats du marche
	 */
	protected $mNbResaAchat;
	
	/**
	 * @var Ca
	 * @desc Le chiffre d'affaire d'un marché
	 */
	protected $mCa;
	
	/**
	 * @var ReservationAbonnement
	 * @desc Le nombre de réservation sur abonnement uniquement
	 */
	protected $mReservationAbonnement;
	
	/**
	 * @var AchatAbonnement
	 * @desc Le nombre d'Achat sur abonnement uniquement
	 */
	protected $mAchatAbonnement;
	
	/**
	 * @var NbAchat
	 * @desc Le nombre d'Achat total
	 */
	protected $mNbAchat;
	
	
	/**
	* @name InfoCommandeResponse()
	* @desc Le constructeur
	*/
	public function InfoCommandeResponse() {
		$this->mValid = true;
		$this->mInfoCommande = array();
		$this->mNbResaAchat = array();
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
	* @name getInfoCommande()
	* @return array(InfoCommandeViewVO)
	* @desc Renvoie les infos sur la commande
	*/
	public function getInfoCommande() {
		return $this->mInfoCommande;
	}

	/**
	* @name setInfoCommande($pInfoCommande)
	* @param array(InfoCommandeViewVO)
	* @desc Remplace le InfoCommande par $pInfoCommande
	*/
	public function setInfoCommande($pInfoCommande) {
		$this->mInfoCommande = $pInfoCommande;
	}
	
	/**
	* @name addInfoCommande($pInfoCommande)
	* @param InfoCommandeViewVO
	* @desc Ajoute $pInfoCommande à InfoCommande
	*/
	public function addInfoCommande($pInfoCommande){
		array_push($this->mInfoCommande,$pInfoCommande);
	}
	
	/**
	* @name getDetailMarche()
	* @return bool
	* @desc Renvoie la DetailMarche de l'élément
	*/
	public function getDetailMarche() {
		return $this->mDetailMarche;
	}

	/**
	* @name setDetailMarche($pDetailMarche)
	* @param bool
	* @desc Remplace la DetailMarche de l'élément par $pDetailMarche
	*/
	public function setDetailMarche($pDetailMarche) {
		$this->mDetailMarche = $pDetailMarche;
	}
	
	/**
	* @name getNbResaAchat()
	* @return bool
	* @desc Renvoie la NbResaAchat de l'élément
	*/
	public function getNbResaAchat() {
		return $this->mNbResaAchat;
	}

	/**
	* @name setNbResaAchat($pNbResaAchat)
	* @param bool
	* @desc Remplace la NbResaAchat de l'élément par $pNbResaAchat
	*/
	public function setNbResaAchat($pNbResaAchat) {
		$this->mNbResaAchat = $pNbResaAchat;
	}
	
	/**
	* @name getCa()
	* @return bool
	* @desc Renvoie la Ca de l'élément
	*/
	public function getCa() {
		return $this->mCa;
	}

	/**
	* @name setCa($pCa)
	* @param bool
	* @desc Remplace la Ca de l'élément par $pCa
	*/
	public function setCa($pCa) {
		$this->mCa = $pCa;
	}
	
	/**
	* @name getReservationAbonnement()
	* @return bool
	* @desc Renvoie la ReservationAbonnement de l'élément
	*/
	public function getReservationAbonnement() {
		return $this->mReservationAbonnement;
	}

	/**
	* @name setReservationAbonnement($pReservationAbonnement)
	* @param bool
	* @desc Remplace la ReservationAbonnement de l'élément par $pReservationAbonnement
	*/
	public function setReservationAbonnement($pReservationAbonnement) {
		$this->mReservationAbonnement = $pReservationAbonnement;
	}
	
	/**
	* @name getAchatAbonnement()
	* @return bool
	* @desc Renvoie la AchatAbonnement de l'élément
	*/
	public function getAchatAbonnement() {
		return $this->mAchatAbonnement;
	}

	/**
	* @name setAchatAbonnement($pAchatAbonnement)
	* @param bool
	* @desc Remplace la AchatAbonnement de l'élément par $pAchatAbonnement
	*/
	public function setAchatAbonnement($pAchatAbonnement) {
		$this->mAchatAbonnement = $pAchatAbonnement;
	}
	
	/**
	* @name getNbAchat()
	* @return bool
	* @desc Renvoie la NbAchat de l'élément
	*/
	public function getNbAchat() {
		return $this->mNbAchat;
	}

	/**
	* @name setNbAchat($pNbAchat)
	* @param bool
	* @desc Remplace la NbAchat de l'élément par $pNbAchat
	*/
	public function setNbAchat($pNbAchat) {
		$this->mNbAchat = $pNbAchat;
	}
}
?>