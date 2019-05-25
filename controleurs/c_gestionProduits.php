<?php
$action = $_REQUEST['action'];

switch($action)
{
	//verificonnexion fait appel a login et mot de passe ainsi qu’a la fonction situé dans la classe PDO pour vérifier si ces deux champs corresponde avec la base de donnée
	
	case 'verifconnexion' :
	{
	$login = $_REQUEST['login'];
	$password = $_REQUEST['password'];
	$statut = $pdo->verifconnexion($login,$password);
	
	//Si la valeur de statut est correct alors on initialise le $_SESSION et on le redirige vers la page d’accueil sinon une erreur de login ou de mot de passe apparait

	if($statut)
	{
		$_SESSION['login']=$login;
		header('Location: index.php');
	}
	else
	{
		echo 'erreur de login ou mot de passe';
	}
	break;

	}
	
	//fait appel a la vue connexion, soit le formulaire qui permet d’inscrire le login et mot de passe permettant de se connecté

	case 'connexion' :
	{
		include("vues/v_admin.php");
		break;
	}
		
	case 'voirCategories':
	{
		$lesCategories = $pdo->getLesCategories();
		include("vues/v_categories.php");
  		break;
	}
	
	//Permet d’accéder au modification d’un produit, que lorsque l’on est connecté en tant qu’administrateur

	case 'Modifier' :
	{
		$idProduit=$_REQUEST['produit’];
		$unProduit=$pdo->getProduit($idProduit);
		include("vues/v_modifProduit.php");
		break;
	}

	//Une fois accéder a la vue modifier, ceci va nous permettre de mettre à jour les caractéristique d’un produit, que lorsque l’on est connecté en tant qu’administrateur
	
	case 'MiseAJour' :
	{
		$idProduit=$_REQUEST['id'];
		$description=$_POST['description'];
		$prix=$_POST['prix'];
		
	//Fait appel a une fonction dans la classe PDO

		$statut=$pdo->modifiValeur($idProduit, $description, $prix);
		
	//Si la valeur retourné est correcte alors la modification à réussie sinon cela a échoué

		if($statut)
		{
			echo 'Modification réussite';
		}
		else
		{
			echo 'Modification échoué';
		}
		
		break;
	}
	
	//Permet de supprimer un objet en onction de son ID, en tant qu’administrateur seulement

	case 'Supprimer' :
	{
		$idProduit=$_REQUEST['produit'];
		$statut=$pdo->supprimer($idProduit);
		
		if($statut)
		{
			echo 'Suppression réussite';
		}
		else
		{
			echo 'Suppression échoué';
		}
		
		break;
	}
	
	//Permet d’accéder à l aveu qui permet d’ajouter un produit dans la base de donnée et ainsi dans le catalogue

	case 'Ajouter' :
	{
		include("vues/v_ajout.php");
		break;
	}
	
	//Permet d’ajouter un produit a la base de donnée et ainsi dans le catalogue

	case 'AjouterProduit' :
	{
		$id=$_REQUEST['id'];
		$categorie=$_REQUEST['categorie'];
		$description=$_REQUEST['description'];
		$image=$_REQUEST['image'];
		$prix=$_REQUEST['prix'];
		
		//echo $id,$categorie,$description,$image,$prix;
		
		$ajouter=$pdo->ajouter($id,$categorie,$description,$prix,$image);
		
		if($ajouter)
		{
			header('Location:index.php?uc=administrer&action=Ajouter');
		}
		
		break;
	}
	
	
		
}
?>