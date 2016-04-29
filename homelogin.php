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

$email = strtolower($_POST['email']);
$password = $_POST['password'];
$hash = md5($password);

if (!$results = $mysqli->query("SELECT * from USERS where EMAILADD = '$email' AND PWHASH = '$hash'")){
	die("Query failed");
}
if ($results->num_rows > 1){
	die("Query error - too many rows returned.");
}

$row = $results->fetch_assoc();
if (!$row){
	echo '<script type="text/javascript"> alert("Invalid username/password");';
	echo 'window.location = '."'".$_SERVER['HTTP_REFERER']."'";
	echo '</script>';
	exit();
}

$_SESSION['userid']     = $row['USERID'];
$_SESSION['firstname']  = $row['FIRSTNAME'];
$_SESSION['isAdmin']    = $row['ADMINFLAG'];
$_SESSION['isOwner']    = $row['OWNERFLAG'];
$_SESSION['isCustomer'] = $row['CUSTFLAG'];
$_SESSION['isEmployee'] = $row['EMPFLAG'];
$_SESSION['email']      = $email;

/*
//debug messages
echo "firstname  ".$_SESSION['firstname']."  <br/>";
echo "isAdmin    ".$_SESSION['isAdmin']."    <br/>";
echo "isOwner    ".$_SESSION['isOwner']."    <br/>";
echo "isCustomer ".$_SESSION['isCustomer']." <br/>";
echo "isEmployee ".$_SESSION['isEmployee']." <br/>";
echo "email      ".$_SESSION['email']."      <br/>";
*/

echo '<script type="text/javascript">';
//echo 'alert("Login Successful");';
echo 'window.location = '."'".$_SERVER['HTTP_REFERER']."'";
echo '</script>';
?>