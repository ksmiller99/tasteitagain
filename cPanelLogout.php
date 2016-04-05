<?php
session_start();
$old_user = $_SESSION['valid_user'];
unset($_SESSION['valid_user']);
session_destroy();
echo '<script type="text/javascript"> alert("You have been logged out.");';
echo 'window.location.replace("index.php"); </script>';
exit();
?>