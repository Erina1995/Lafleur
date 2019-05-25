<?php
/** 
 * Classe d'accès aux données. 
 
 * Utilise les services de la classe PDO
 * pour l'application lafleur
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * @package default
 * @author Patrice Grand
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class PdoLafleur
{   		
      	private static $serveur='mysql:host=localhost';
      	private static $bdd='dbname=lafleur';   		
      	private static $user='root' ;    		
      	private static $mdp='' ;	
		private static $monPdo;
		private static $monPdoLafleur = null;
/**
 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
 * pour toutes les méthodes de la classe
 */				
	private function __construct() // le constructeur ne peut pas être appelé à l'exterieur de la classe => on utlise get Pdolafleur
	{
    		PdoLafleur::$monPdo = new PDO(PdoLafleur::$serveur.';'.PdoLafleur::$bdd, PdoLafleur::$user, PdoLafleur::$mdp); 
			PdoLafleur::$monPdo->query("SET CHARACTER SET utf8");
	}
	public function _destruct(){
		PdoLafleur::$monPdo = null;
	}
/**
 * Fonction statique qui crée l'unique instance de la classe
 *
 * Appel : $instancePdolafleur = PdoLafleur::getPdoLafleur();
 * @return l'unique objet de la classe PdoLafleur
 */
	public  static function getPdoLafleur()
	{
		if(PdoLafleur::$monPdoLafleur == null)
		{
			PdoLafleur::$monPdoLafleur= new PdoLafleur();
		}
		return PdoLafleur::$monPdoLafleur;  
	}
/**
 * Retourne toutes les catégories sous forme d'un tableau associatif
 *
 * @return le tableau associatif des catégories 
*/
	public function getLesCategories()
	{
		$req = "select * from categorie";
		$res = PdoLafleur::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}

/**
 * Retourne sous forme d'un tableau associatif tous les produits de la
 * catégorie passée en argument
 * 
 * @param $idCategorie 
 * @return un tableau associatif  
*/

	public function getLesProduitsDeCategorie($idCategorie)
	{
	    $req="select * from produit where idCategorie = '$idCategorie'";
		$res = PdoLafleur::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes; 
	}
/**
 * Retourne les produits concernés par le tableau des idProduits passée en argument
 *
 * @param $desIdProduit tableau d'idProduits
 * @return un tableau associatif 
*/
	public function getLesProduitsDuTableau($desIdProduit)
	{
		$nbProduits = count($desIdProduit);
		$lesProduits=array();
		if($nbProduits != 0)
		{
			foreach($desIdProduit as $unIdProduit)
			{
				$req = "select * from produit where id = '$unIdProduit'";
				$res = PdoLafleur::$monPdo->query($req);
				$unProduit = $res->fetch();
				$lesProduits[] = $unProduit;
			}
		}
		return $lesProduits;
	}
/**
 * Crée une commande 
 *
 * Crée une commande à partir des arguments validés passés en paramètre, l'identifiant est
 * construit à partir du maximum existant ; crée les lignes de commandes dans la table contenir à partir du
 * tableau d'idProduit passé en paramètre
 * @param $nom 
 * @param $rue
 * @param $cp
 * @param $ville
 * @param $mail
 * @param $lesIdProduit
 
*/
	public function creerCommande($nom,$rue,$cp,$ville,$mail,$lesIdProduit)
	{
		$req = "select max(id) as maxi from commande";
		echo $req."<br>";
		$res = PdoLafleur::$monPdo->query($req);
		$laLigne = $res->fetch();
		$maxi = $laLigne['maxi'] ;
		$maxi++;
		$idCommande = $maxi;
		echo $idCommande."<br>";
		echo $maxi."<br>";
		$date = date('Y/m/d');
		$req = "insert into commande values ('$idCommande','$date','$nom','$rue','$cp','$ville','$mail')";
		echo $req."<br>";
		$res = PdoLafleur::$monPdo->exec($req);
		foreach($lesIdProduit as $unIdProduit)
		{
			$index =array_search($unIdProduit,$_SESSION['produits']);
			$req = "insert into contenir values ('".$idCommande."','$unIdProduit','".$_SESSION['quantite'][$index]."')";
			echo $req."<br>";
			$res = PdoLafleur::$monPdo->exec($req);
		}
		
	
	}
	
	/**
	* fonction de verification de connexion
		
	* Cette fonction permet de verifier le nom de compte et le mot de passe inscrit 
	par l'utilisateur par rapport à la base de données
	connaitre les bons login et mot de passe
		
	* @param $login
	* @param $mdp
	*/
	
	public function verifconnexion($login, $mdp)
	{
		$req= PdoLafleur::$monPdo->prepare("select nom,mdp from administrateur where nom=':login' and mdp=':mdp'");
		$req->execute(array('login'=>$login, 'mdp'=>$mdp));
		$resultat=$req->fetch();
		
		return resultat;
	}
	/**
	* fonction affichant les produits
		
	* Cette fonction permet d'afficher tout les produits
	inscrit dans la base de donnée
		
	* @param $idProduit
	*/
	public function getProduit($idProduit) {
		$req="SELECT * FROM produit WHERE id='".$idProduit."'";
		$res = PdoLafleur::$monPdo->query($req);
		$produit=$res->fetch();
		return $produit;
	}
	
	/**
	* fonction modification de valeur
		
	* Cette fonction permet modifier une valeur déjà inscrite dans la base de donnée
		
	* @param $idProduit
	* @param $description
	* @param $prix
	*/
	public function modifiValeur($idProduit, $description, $prix)
	{
		$res="UPDATE produit SET description='$description', prix=$prix WHERE id='$idProduit'";
		$req=PdoLaFleur::$monPdo->exec($res);
		
		return req;
	}
	
	/**
	* fonction supprimer 
		
	* Cette fonction permet de supprimer un produit grâce a son ID
		
	* @param $id
	*/
	public function supprimer($id)
	{
		$req="delete from produit where id='".$id."'";
		$res=PdoLaFleur::$monPdo->exec($req);
		
		return res;
	}
	/**
	* fonction ajouter
		
	* Cette fonction permet d'ajouter un produit dans la base de donnée
		
	* @param $id
	* @param $categorie
	* @param $description
	* @param $prix
	* @param $image
	*/
	public function ajouter($id, $categorie, $description, $prix, $image)
	{
		$res="insert into produit(id,description,prix,image,idCategorie) values ('$id','$description',$prix,'$image','$categorie')";
		$req=PdoLaFleur::$monPdo->exec($res);
		
		return req;
	}
}
?>