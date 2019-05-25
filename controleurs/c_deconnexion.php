<?php
if(isset($_SESSION['login']))
{
	//Permet de retiré la valeur du $_SESSION pour qu’il n’accède plus au parti connecté
	session_destroy();
	header('Location: index.php');
}
?>