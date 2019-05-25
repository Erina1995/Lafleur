<?php
session_start();
require_once("util/fonctions.inc.php");
require_once("util/class.pdoLafleur.inc.php");
include("vues/v_entete.php") ;
include("vues/v_bandeau.php") ;

if(!isset($_REQUEST['uc']))
     $uc = 'accueil';
else
	$uc = $_REQUEST['uc'];

$pdo = PdoLafleur::getPdoLafleur();	 
switch($uc)
{
	case 'accueil':
		{include("vues/v_accueil.php");break;} // accède a la vue accueil
	case 'voirProduits' :
		{include("controleurs/c_voirProduits.php");break;} // accède au controleur voir Produit
	case 'gererPanier' :
		{ include("controleurs/c_gestionPanier.php");break; } // accède au contrôleur gestionPanier
	case 'administrer' :
		{ include("controleurs/c_gestionProduits.php");break;  } // accède au contrôleur gestion Produit
	case 'deconnexion' :
		{ include("controleurs/c_deconnexion.php");break;	} // accède au contrôleur de déconnexion
}
include("vues/v_pied.php") ;
?>

