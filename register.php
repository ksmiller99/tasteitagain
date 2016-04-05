<?php
if (!@session_start()){
	die("Cannot start session");
}
	
if (!isset($_SESSION['valid_user'])){
	//they have not tried to login, or logged out
	echo '<script type="text/javascript"> alert("You must login first");';
	echo 'window.location.replace("index.php"); </script>';
	exit();
}
?>

<html>
<head>
	<title>Taste It Again - Register</title>
</head>
<body leftmargin="0px" topmargin="0px" marginwidth="0px" marginheight="0px" bgcolor="black">
<script type="text/javascript">
<!--
function test_it(f){
	if((f.fName.value == '') ||
		(f.lName.value == '') ||
		(f.address.value == '') ||
		(f.city.value == '') ||
		(f.state.value == '') ||
		(f.zipCode.value == '') ||
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

/*
//debug messages
echo '<pre>';
echo "post<br />";
print_r($_POST);
echo '</pre>';
*/

$fName    = $_POST['fName'];
$lName    = $_POST['lName'];
$address  = $_POST['address'];
$address2 = $_POST['address2'];
$city     = $_POST['city'];
$state    = $_POST['state'];
$zipCode  = $_POST['zipCode'];
$email    = $_POST['email'];
$password = $_POST['password'];
$phone    = $_POST['phone'];
$txtflag  = ($_POST['txtflag'] == "1") ? "1":"0";
$mailflag = ($_POST['mailflag'] == "1") ? "1":"0";
$posted   = $_POST['posted'];
$hash     = md5($password);

if ($posted == "OK"){
	$sql1 = "insert into USERS (EMAILADD,  PWHASH, FIRSTNAME, LASTNAME,   PHONE, CUSTFLAG) 
	                     VALUES('$email', '$hash',  '$fName', '$lName','$phone', 'Y');";
						 
	$sql2 = "select USERID from USERS where EMAILADD = '$email';";
	
	//transaction begin with implicit autocommit off 
	//echo "starting transaction<br />";
	if (!$mysqli->query("START TRANSACTION;")){
		echo '<script type="text/javascript">'."\n"; 
		echo 'alert("Start transaction failed: '.$mysqli->error.'");'."\n";
		echo 'window.back();';
		echo '</script>'."\n";
		exit();
	}
	
	//insert into USERS table
	//echo "inserting to USERS<br />";
	//echo $sql1."<br />";
	if (!$mysqli->query($sql1)){
		//rollback
		$err = $mysqli->error; //save error message before rollback
		$mysqli->query("ROLLBACK;"); //must add check for rollback failure
		echo "sql1: $sql1<br />\n";
		echo "err: $err<br />\n";
		echo '<script type="text/javascript">'."\n"; 
		echo 'alert("Account creation failed: '.$err.'");'."\n";
		echo 'window.back();';
		echo '</script>'."\n";
		exit();
	}
	
	//retrieve auto-increment USERID
	//echo "getting userid<br />";
	//echo $sql2."<br />";
	if (!($result = $mysqli->query($sql2))){
		//rollback
		$err = $mysqli->error; //save error message before rollback
		$mysqli->query("ROLLBACK;"); //must add check for rollback failure
		echo $sql2."<br />";
		echo '<script type="text/javascript">'."\n"; 
		echo 'alert("Account creation failed: '.$err.'");'."\n";
		echo 'window.back();';
		echo '</script>'."\n";
		exit();
	}
	
	//echo"<pre>";
	//	print_r($result);
	//echo"</pre>";
	
	$row = $result->fetch_assoc();
	$userid = $row['USERID'];
	//echo "userid: $userid <br />";
	$sql3 = "insert into CUSTOMERS ( USERID,  ADDLINE1,     ADDLINE2,    CITY,    STATE,        ZIP,   EMAILFLAG, TXTMSGFLAG) 
	                         VALUES($userid, '$address', '$address2', '$city', '$state', '$zipCode', '$mailflag', '$txtflag');";
	//echo $sql3."<br />";
	
	//insert customer information
	//echo "inserting CUSTOMER<br />";
	//echo $sql3."<br />";
	if (!$mysqli->query($sql3)){
		$err = $mysqli->error; //save error message before rollback
		//rollback
		$mysqli->query("ROLLBACK;"); //must add check for rollback failure
		echo '<script type="text/javascript">'."\n"; 
		echo 'alert("Account creation failed: '.$err.'");'."\n";
		echo 'window.back();';
		echo '</script>'."\n";
		exit();
	}
	//commit transaction
	//echo "commting<br />";
	if ($mysqli->query("COMMIT;")){
		$_SESSION['email'] = $email;
		$_SESSION['firstname'] = $fName;
		echo '<script type="text/javascript">'."\n"; 
		echo 'alert("Account created");'."\n";
		echo '</script>'."\n";
		$_SESSION['isCustomer'] = "Y";
	}else{
		echo '<script type="text/javascript">'."\n"; 
		echo 'alert("Commit transaction failed: '.$mysqli->error.'");'."\n";
		echo 'window.back();';
		echo '</script>'."\n";
		exit();
	}
		
}
?>

	<table cellspacing="0" cellpadding="0" width="900" align="center" border="0">

		<?php include("header.php"); ?>

		<tr height="500" bgcolor="#82FA58">
			<td colspan="2">
				<table cellspacing="0" cellpadding="0" width="900" border="0" height="100%">
					<tr valign="top">
						<td>
<!------------ Start of main content --------------------------------------------------------------------------------------->	
							<table cellspacing="0" cellpadding="0" width="900" border="0">
								<tr>
									<td>
										<center><h1>Register</h1></center>
									</td>
								</tr>
							</table>
							<form name="register" id="register" action = '' method = "post">
							<table cellspacing="1" cellpadding="3" style="border:1px #000000 solid" bgcolor="#BDBDBD" align="center">
								<tr>
									<td>
										First Name
									</td>
									<td>
										<input type="text" name="fName" size="20" value="<?php echo $fName; ?>">
									</td>
								</tr>
								<tr>
									<td>
										Last Name
									</td>
									<td>
										<input type="text" name="lName" size="20" value="<?php echo $lName; ?>">
									</td>
								</tr>
								<tr>
									<td>
										Address
									</td>
									<td>
										<input type="text" name="address" size="20" value="<?php echo $address; ?>">
									</td>
								</tr>
								<tr>
									<td>
										Address Line 2
									</td>
									<td>
										<input type="text" name="address2" size="20" value="<?php echo $address2; ?>">
									</td>
								</tr>
								<tr>
									<td>
										City
									</td>
									<td>
										<input type="text" name="city" size="20" value="<?php echo $city; ?>">
									</td>
								</tr>
								<tr>
									<td>
										State
										<input type="text" name="state" size="3" value="<?php echo $state; ?>">
									</td>
									<td>
										Zip Code
										<input type="text" name="zipCode" size="5" value="<?php echo $zipCode; ?>">
									</td>
								</tr>
								<tr>
									<td>
										Phone Number
									</td>
									<td>
										<input type="text" name="phone" size="20" value="<?php echo $phone; ?>">
									</td>
								</tr>
								<tr>
									<td>
										Email Address
									</td>
									<td>
										<input type="text" name="email" size="30" value="<?php echo $email; ?>">
									</td>
								</tr>
								<tr>
									<td>
										Password
									</td>
									<td>
										<input type="password" name="password" size="20" value="" >
									</td>
								</tr>
								<tr>
									<td>
										Re-Enter Password
									</td>
									<td>
										<input type="password" name="password2" size="20" value="">
									</td>
								</tr>
								<tr>
									<td>
										Send me email coupons
									</td>	
									<td>
										<input type="radio" name="mailflag" value="1" <?php echo ($mailflag == "1")?"checked":""; ?> >Y
										<input type="radio" name="mailflag" value="0" <?php echo ($mailflag == "0")?"checked":""; ?> >N
									</td>
								</tr>
								<tr>
									<td>
										Send me txt message coupons
									</td>	
									<td>
										<input type="radio" name="txtflag" value="1" <?php echo ($txtflag == "1")?"checked":""; ?> >Y
										<input type="radio" name="txtflag" value="0" <?php echo ($txtflag == "0")?"checked":""; ?> >N
									</td>
								</tr>
							</table>
							<table align="center">
								<tr>
									<td>
										<input name="btnRegister" type="button" value="Register" onclick="test_it(this.form);"/>
										<input type="reset" value="Reset">
									</td>
								</tr>
							</table>
							<input name="posted" type="hidden" value="">
							</form>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr height="30px" bgcolor="#FFFF00">
			<td colspan="2">

				<?php include("footer.php"); ?>

			</td>
		</tr>
	</table>
</body>
</html>
