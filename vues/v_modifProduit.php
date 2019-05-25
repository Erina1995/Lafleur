<div id="produits">
<?php
	
	$id = $unProduit['id'];
	$description = $unProduit['description'];
	$prix=$unProduit['prix'];
	$image = $unProduit['image'];
?>
<div id="administrer">
	<form method="post" action="index.php?uc=administrer&action=MiseAJour">
	   <p>
			<br />
			<label for="idProduit">Id  :</label>
			<input type="text" name="id" id="id" value="<?php echo $id ?>"/>
			
			<br />
			<label for="description">Description  :</label>
			<input type="text" name="description" id="description" value="<?php echo $description ?>"/>
		   
			<br />
			<label for="prix">Prix :</label>
			<input type="text" name="prix" id="prix" value="<?php echo $prix ?>"/>
		   
			<br />
			<input type="submit"/>
	   </p>
	</form>
</div>
