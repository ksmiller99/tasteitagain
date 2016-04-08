<?php
$host_name = "localhost";
$database = "cmpt483a_tasteitagain"; 
$username = "cmpt483a_root";         
$password = "&6Kmz[FOfuH$";          

try {
$dbo = new PDO('mysql:host='.$host_name.';dbname='.$database, $username, $password);
} catch (PDOException $e) {
print "Error!: " . $e->getMessage() . "<br/>";
die();
}
?>