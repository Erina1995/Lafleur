<div id="bandeau">
<!-- Images En-tête -->
<!--<img src="images/menuGauche.gif"	alt="Choisir" title="Choisir"/>-->
<img src="images/lafleur.gif"	alt="Lafleur" title="Lafleur"/>
</div>
<?php 
	if(isset($_SESSION['login']))
	{
		echo 'Bonjour '.$_SESSION['login'].' bienvenue dans l interface de modification :';
	}
?>
<!--  Menu haut-->
<ul id="menu">
	<li><a href="index.php?uc=accueil"> Accueil </a></li>
	<li><a href="index.php?uc=voirProduits&action=voirCategories"> Voir le catalogue de fleurs </a></li>
	<?php
	if(!isset($_SESSION['login']))
	{	
	?>		
		<li><a href="index.php?uc=gererPanier&action=voirPanier"> Voir son panier </a></li>
		<li><a href="index.php?uc=administrer&action=connexion"> Administrer </a></li>	
	<?php
	}else{
	?>
		<li><a href="index.php?uc=administrer&action=Ajouter"> Ajouter un Produit </a></li>
		<li><a href="index.php?uc=deconnexion"> Se Deconnecter </a></li>
	<?php
	}
	?>
</ul>
