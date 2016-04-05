<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Create Account</title>
<style type="text/css">
.auto-style1 {
	text-align: right;
}
</style>
</head>

<body>

<script type="text/javascript">
<!--
function test_it(f){
	if((f.fName.value == '') ||
		(f.lName.value == '') ||
		(f.email.value == '') ||
		(f.password.value == '') ||
		(f.password2.value == '') ||
		(f.phone.value == '')){
			alert("All fields must be completed.");
			return;
		}
		
		if(f.password.value != f.password2.value){
			alert("Passwords do not match.");
			return;
		}
		f.posted.value = "OK";
		f.submit();
}
//-->
</script>

<?php
require "mySQL.php";
$fName    = $_POST['fName'];
$lName    = $_POST['lName'];
$email    = $_POST['email'];
$password = $_POST['password'];
$phone    = $_POST['phone'];
$posted   = $_POST['posted'];
$hash     = md5($password);

echo "<br>posted: $posted";

if ($posted == "OK"){
	$sql = "insert into USERS (EMAILADD, PWHASH, FIRSTNAME, LASTNAME, PHONE) VALUES('$email', '$hash', '$fName', '$lName','$phone');";
	echo "<br>sql: $sql"; 
	
	if($result = $mysqli->query($sql)){
		echo '<script type="text/javascript"> alert("Account created"); </script>';
	}else{
		//echo '<script type="text/javascript"> alert("Account creation failed: "+$mysqli->error); </script>';
		echo '<script type="text/javascript"> alert($mysqli->error); </script>';
	}	         
	         
          
}
?>

<h1>Create Account</h1>
<table>
<form action="" method="post">
	<tr><td class="auto-style1">First Name:        </td><td> <input name="fName"     type="text"     value="<?php echo $fName; ?>" /></td></tr>
	<tr><td class="auto-style1">Last Name:         </td><td> <input name="lName"     type="text"     value="<?php echo $lName; ?>" /></td></tr>
	<tr><td class="auto-style1">E-Mail Address:    </td><td> <input name="email"     type="text"     value="<?php echo $email ?>" /></td></tr>
	<tr><td class="auto-style1">Password:          </td><td> <input name="password"  type="password" value="" /></td></tr>
	<tr><td class="auto-style1">Re-enter Password: </td><td> <input name="password2" type="password" value="" /></td></tr>
	<tr><td class="auto-style1">Phone:             </td><td> <input name="phone"     type="text"     value="<?php echo $phone; ?>" /></td></tr>
	<tr><td><input name="btnRegister" type="button" value="Register" onclick="test_it(this.form);"/></td></tr>
	<input name="posted" type="hidden" value="">
</form>
</table>

</body>

</html>
