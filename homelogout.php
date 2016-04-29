<?php
include "mySQL.php";

if (!@session_start()){
	die("Cannot start session");
}
	
if (!isset($_SESSION['valid_user'])){
	//they have not tried to login, or logged out
	echo '<script type="text/javascript"> alert("You must login first");';
	echo 'window.location.replace("index.php"); </script>';
	exit();
}
//echo '<pre>';
//print_r($_SESSION); 
//echo '</pre>';

unset($_SESSION['email']);  
unset($_SESSION['firstname']);  
unset($_SESSION['isAdmin']);    
unset($_SESSION['isOwner']);    
unset($_SESSION['isCustomer']); 
unset($_SESSION['isEmployee']); 

echo '<script type="text/javascript">';
echo 'alert("You have been logged out.");';
echo 'window.location = '."'home.php'";
echo '</script>';

?>