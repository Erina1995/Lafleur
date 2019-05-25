<?php
$action = $_REQUEST['action'];
switch($action)
{
	case 'voirPanier':
	{
		$n= nbProduitsDuPanier();
		if($n >0)
		{
			$desIdProduit = getLesIdProduitsDuPanier();
			$lesProduitsDuPanier = $pdo->getLesProduitsDuTableau($desIdProduit);
			$desQtesProduit = getLesQteProduitsDuPanier();
			include("vues/v_panier.php");
		}
		else
		{
			$message = "panier vide !!";
			include ("vues/v_message.php");
		}
		break;
	}

	case 'supprimerUnProduit':
	{
		$n= nbProduitsDuPanier();
		if($n >0)
		{
			$idProduit=$_REQUEST['produit'];
			retirerDuPanier($idProduit);
			$desIdProduit = getLesIdProduitsDuPanier();
			$lesProduitsDuPanier = $pdo->getLesProduitsDuTableau($desIdProduit);
			$desQtesProduit = getLesQteProduitsDuPanier();
			include("vues/v_panier.php");
		}
		else
		{
			$message = "panier vide !!";
			include ("vues/v_message.php");
		}
		break;
	}
		case 'augmenterQte':
	{
		$idProduit=$_REQUEST['produit'];
	
	//Fait appel a la fonction dans la classe PDO qui permet d’augmenter la quantité de 1
	
		augmenteQte($idProduit);
		$desIdProduit = getLesIdProduitsDuPanier();
		$lesProduitsDuPanier = $pdo->getLesProduitsDuTableau($desIdProduit);
		$desQtesProduit = getLesQteProduitsDuPanier();
		include("vues/v_panier.php");
		break;
	}
			case 'diminuerQte':
	{
		$idProduit=$_REQUEST['produit'];

	//Fait appel a la fonction dans la classe PDO qui permet diminuer la quantité de 1

		diminuerQte($idProduit);
		$desIdProduit = getLesIdProduitsDuPanier();
		$lesProduitsDuPanier = $pdo->getLesProduitsDuTableau($desIdProduit);
		$desQtesProduit = getLesQteProduitsDuPanier();
		include("vues/v_panier.php");
		break;
	}

	case 'passerCommande' :
	    $n= nbProduitsDuPanier();

	//Si $n est supérieur a 0 dans ce cas la commande peut être valider sinon un message panier vide apparait

		if($n>0)
		{
			$nom ='';$rue='';$ville ='';$cp='';$mail='';
			include ("vues/v_commande.php");
		}
		else
		{
			$message = "panier vide !!";
			include ("vues/v_message.php");
		}
		break;
		
	case 'confirmerCommande'	:
	{
		//Si il y a des champs oublié dans le formulaire de la commande, une message d’erreur apparait sinon la commande est passé et on fait appel a la fonction dans la 		//classe PDO qui permettra d’inscrire dans la base de donnée les coordonnées de la personne qui a passé commande

		$nom =$_REQUEST['nom'];$rue=$_REQUEST['rue'];$ville =$_REQUEST['ville'];$cp=$_REQUEST['cp'];$mail=$_REQUEST['mail'];
	 	$msgErreurs = getErreursSaisieCommande($nom,$rue,$ville,$cp,$mail);
		if (count($msgErreurs)!=0)
		{
			include ("vues/v_erreurs.php");
			include ("vues/v_commande.php");
		}
		else
		{
			$lesIdProduit = getLesIdProduitsDuPanier();
			$pdo->creerCommande($nom,$rue,$cp,$ville,$mail, $lesIdProduit);
			
			$message = "Commande enregistrée";
			supprimerPanier();
			include ("vues/v_message.php");
		}
		break;
	}
}


?>


