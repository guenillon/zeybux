<?php
//****************************************************************
//
// Createur : Julien PIERRE
// Date de creation : 24/01/2011
// Fichier : AfficheListeProduitBonDeLivraisonResponse.php
//
// Description : Classe AfficheListeProduitBonDeLivraisonResponse
//
//****************************************************************
include_once(CHEMIN_CLASSES . "DataTemplate.php");

/**
 * @name AfficheListeProduitBonDeLivraisonResponse
 * @author Julien PIERRE
 * @since 24/01/2011
 * @desc Classe représentant une AfficheListeProduitBonDeLivraisonResponse
 */
class AfficheListeProduitBonDeLivraisonResponse extends DataTemplate
{
	/**
	 * @var bool
	 * @desc Donne la validité de l'objet
	 */
	protected $mValid;
	
	/**
	 * @var array(StockProduitReservationViewVO)
	 * @desc Les produits
	 */
	protected $mProduits;
	
	/**
	 * @var array(StockCommandeViewVO)
	 * @desc Les produits en commande
	 */
	protected $mProduitsCommande;
	
	/**
	 * @var array(StockCommandeViewVO)
	 * @desc Les produits en commande
	 */
	protected $mProduitsLivraison;
	
	/**
	 * @var array(StockCommandeViewVO)
	 * @desc Les produits en commande
	 */
	protected $mProduitsSolidaire;
	
	/**
	 * @var OperationVO
	 * @desc L'operation pour le producteur
	 */
	protected $mOperationProducteur;
	
	/**
	* @name AfficheListeProduitBonDeLivraisonResponse()
	* @desc Le constructeur
	*/
	public function AfficheListeProduitBonDeLivraisonResponse() {
		$this->mValid = true;
		$this->mProduits = array();
		$this->mProduitsCommande = array();
		$this->mProduitsLivraison = array();
		$this->mProduitsSolidaire = array();
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
	* @name getProduits()
	* @return array(StockProduitReservationViewVO)
	* @desc Renvoie les Produits
	*/
	public function getProduits() {
		return $this->mProduits;
	}

	/**
	* @name setProduits($pProduits)
	* @param array(StockProduitReservationViewVO)
	* @desc Remplace le Produits par $pProduits
	*/
	public function setProduits($pProduits) {
		$this->mProduits = $pProduits;
	}
	
	/**
	* @name addProduits($pProduits)
	* @param StockProduitReservationViewVO
	* @desc Ajoute Produits à Produits
	*/
	public function addProduits($pProduits){
		array_push($this->mProduits,$pProduits);
	}
	
	/**
	* @name getProduitsCommande()
	* @return array(StockCommandeViewVO)
	* @desc Renvoie les ProduitsCommande
	*/
	public function getProduitsCommande() {
		return $this->mProduitsCommande;
	}

	/**
	* @name setProduitsCommande($pProduitsCommande)
	* @param array(StockCommandeViewVO)
	* @desc Remplace le ProduitsCommande par $pProduitsCommande
	*/
	public function setProduitsCommande($pProduitsCommande) {
		$this->mProduitsCommande = $pProduitsCommande;
	}
	
	/**
	* @name addProduitsCommande($pProduitsCommande)
	* @param StockCommandeViewVO
	* @desc Ajoute ProduitsCommande à ProduitsCommande
	*/
	public function addProduitsCommande($pProduitsCommande){
		array_push($this->mProduitsCommande,$pProduitsCommande);
	}
	
	/**
	* @name getProduitsLivraison()
	* @return array(StockProduitReservationViewVO)
	* @desc Renvoie les ProduitsLivraison
	*/
	public function getProduitsLivraison() {
		return $this->mProduitsLivraison;
	}

	/**
	* @name setProduitsLivraison($pProduitsLivraison)
	* @param array(StockProduitReservationViewVO)
	* @desc Remplace le ProduitsLivraison par $pProduitsLivraison
	*/
	public function setProduitsLivraison($pProduitsLivraison) {
		$this->mProduitsLivraison = $pProduitsLivraison;
	}
	
	/**
	* @name addProduitsLivraison($pProduitsLivraison)
	* @param StockProduitReservationViewVO
	* @desc Ajoute ProduitsLivraison à ProduitsLivraison
	*/
	public function addProduitsLivraison($pProduitsLivraison){
		array_push($this->mProduitsLivraison,$pProduitsLivraison);
	}
	
	/**
	* @name getProduitsSolidaire()
	* @return array(StockProduitReservationViewVO)
	* @desc Renvoie les ProduitsSolidaire
	*/
	public function getProduitsSolidaire() {
		return $this->mProduitsSolidaire;
	}

	/**
	* @name setProduitsSolidaire($pProduitsSolidaire)
	* @param array(StockProduitReservationViewVO)
	* @desc Remplace le ProduitsSolidaire par $pProduitsSolidaire
	*/
	public function setProduitsSolidaire($pProduitsSolidaire) {
		$this->mProduitsSolidaire = $pProduitsSolidaire;
	}
	
	/**
	* @name addProduitsSolidaire($pProduitsSolidaire)
	* @param StockProduitReservationViewVO
	* @desc Ajoute ProduitsSolidaire à ProduitsSolidaire
	*/
	public function addProduitsSolidaire($pProduitsSolidaire){
		array_push($this->mProduitsSolidaire,$pProduitsSolidaire);
	}
	
	/**
	* @name getOperationProducteur()
	* @return OperationVO
	* @desc Renvoie les OperationProducteur
	*/
	public function getOperationProducteur() {
		return $this->mOperationProducteur;
	}

	/**
	* @name setOperationProducteur($pOperationProducteur)
	* @param OperationVO
	* @desc Remplace le OperationProducteur par $pOperationProducteur
	*/
	public function setOperationProducteur($pOperationProducteur) {
		$this->mOperationProducteur = $pOperationProducteur;
	}
}
?>