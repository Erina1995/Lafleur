<div id="produits">
<?php
	foreach( $lesProduits as $unProduit) 
{
	$id = $unProduit['id'];
	$description = $unProduit['description'];
	$prix=$unProduit['prix'];
	$image = $unProduit['image'];
	?>
	<ul>
		<li><img src="<?php echo $image ?>" alt=image /></li>
		<li><?php echo $description ?></li>
		<li><?php echo " : ".$prix." Euros" ?>
		<?php
		if(isset($_SESSION['login']))
		{
			?>
			<li><a href=index.php?uc=administrer&categorie=<?php echo $categorie ?>&produit=<?php echo $id ?>&action=Modifier> 
			<img src="images/modifier.png" TITLE="Modifier"</li></a>
			<li><a href=index.php?uc=administrer&categorie=<?php echo $categorie ?>&produit=<?php echo $id ?>&action=Supprimer onclick="return confirm('Voulez-vous vraiment retirer cet article ?');"> 
			<img src="images/supprimer.png" TITLE="Supprimer"></li></a>
			<?php
		}
		else
		{
			?>
			<li><a href=index.php?uc=voirProduits&categorie=<?php echo $categorie ?>&produit=<?php echo $id ?>&action=ajouterAuPanier> 
			<img src="images/mettrepanier.png" TITLE="Ajouter au panier" </li></a>
			<?php
		}?>
	</ul>	
<?php			
}
?>
</div>
