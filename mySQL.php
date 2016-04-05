<?php
//connect to database and table
$db_host = 'localhost';
$db_user = 'cmpt483a_root';
$db_pwd = '&6Kmz[FOfuH$';
$database = 'cmpt483a_tasteitagain';
$mysqli = new mysqli($db_host, $db_user, $db_pwd, $database);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}

?>