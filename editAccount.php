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
	<title>Taste It Again - Edit Account</title>
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
		(f.phone.value == '')){
			alert("All fields must be completed.");
			return;
		}
		
		f.posted.value = "OK";
		f.submit();
}
//-->
</script>

<?php
require "mySQL.php";

/*echo '<pre>';
print_r($_POST);
echo '</pre>';*/

$posted   = $_POST['posted'];

if($posted == "OK"){
	//user attemped update
	$userid   = $_POST['userid'];
	$fName    = $_POST['fName'];
	$lName    = $_POST['lName'];
	$address  = $_POST['address'];
	$address2 = $_POST['address2'];
	$city     = $_POST['city'];
	$state    = $_POST['state'];
	$zipCode  = $_POST['zipCode'];
	$newemail = $_POST['email'];
	$phone    = $_POST['phone'];
	$txtflag  = $_POST['txtflag'];
	$mailflag = $_POST['mailflag'];
}else{
	$email = $_SESSION['email'];
	//load account info from database
	//get USER record
	$sql = "select * from USERS where EMAILADD = '$email';";
	//echo "sql: $sql<br />";
	if (!($results = $mysqli->query($sql))){
		die("Cannot get USER record: ".$mysqli->error);
	}
	if ($results->num_rows != 1){
		die("USERS - Wrong number of rows: ".$results->num_rows." 	$sql");
	}
	$row = $results->fetch_assoc();
	$userid = $row['USERID'];
	$fName  = $row['FIRSTNAME'];
	$lName  = $row['LASTNAME'];
	$phone  = $row['PHONE'];
	
	//get CUSTOMER record
	$sql = "select * from CUSTOMERS where USERID = '$userid';";
	if (!($results = $mysqli->query($sql))){
		die("Cannot get CUSTOMER record: ".$mysqli->error);
	}
	if ($results->num_rows != 1){
		die("CUSTOMERS - Wrong number of rows: ".$results->num_rows);
	}
	$row = $results->fetch_assoc();
	$address  = $row['ADDLINE1'];
	$address2 = $row['ADDLINE2'];
	$city     = $row['CITY'];
	$state    = $row['STATE'];
	$zipCode  = $row['ZIP'];
	$mailflag = $row['EMAILFLAG'];
	$txtflag  = $row['TXTMSGFLAG'];
}

if ($posted == "OK"){
	$sql1 = "UPDATE USERS SET
		EMAILADD   = '$newemail', 
		FIRSTNAME  = '$fName', 
		LASTNAME   = '$lName', 
		PHONE      = '$phone'
		WHERE USERID = '$userid';";
		
	$sql2 = "UPDATE CUSTOMERS SET
		ADDLINE1   = '$address', 
		ADDLINE2   = '$address2', 
		CITY       = '$city', 
		STATE      = '$state', 
		ZIP        = '$zipCode', 
		EMAILFLAG  = '$mailflag', 
		TXTMSGFLAG = '$txtflag'
		WHERE USERID = '$userid';";
	
	//transaction begin with implicit autocommit off 
	if (!$mysqli->query("START TRANSACTION;")){
		echo '<script type="text/javascript">'."\n"; 
		echo 'alert("Start transaction failed: '.$mysqli->error.'");'."\n";
		echo 'window.back();';
		echo '</script>'."\n";
		exit();
	}
	
	//insert into USERS table
	if (!$mysqli->query($sql1)){
		//rollback
		$err = $mysqli->error; //save error message before rollback
		$mysqli->query("ROLLBACK;"); //must add check for rollback failure
		echo '<script type="text/javascript">'."\n"; 
		echo 'alert("Account update failed: '.$err.'");'."\n";
		echo 'window.back();';
		echo '</script>'."\n";
		exit();
	}
	
	//insert customer information
	if (!$mysqli->query($sql2)){
		$err = $mysqli->error; //save error message before rollback
		//rollback
		$mysqli->query("ROLLBACK;"); //must add check for rollback failure
		echo '<script type="text/javascript">'."\n"; 
		echo 'alert("Account update failed: '.$err.'");'."\n";
		echo 'window.back();';
		echo '</script>'."\n";
		exit();
	}
	//commit transaction
	if ($mysqli->query("COMMIT;")){
		$_SESSION['email'] = $newemail;
		$_SESSION['firstname'] = $fName;
		echo '<script type="text/javascript">'."\n"; 
		echo 'alert("Account updated");'."\n";
		echo 'window.location.replace("home.php");';
		echo '</script>'."\n";
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
							<form name="update" id="update" action = '' method = "post">
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
										<input type="radio" name="txtflag" value="1"<?php echo ($txtflag == "1")?"checked":""; ?> >Y
										<input type="radio" name="txtflag" value="0"<?php echo ($txtflag == "0")?"checked":""; ?> >N
									</td>
								</tr>
							</table>
							<table align="center">
								<tr>
									<td>
										<input name="btnSave" type="button" value="Save Changes" onclick="test_it(this.form);"/>
										<!--<input type="reset" value="Reset">-->
									</td>
								</tr>
							</table>
							<input name="posted" type="hidden" value="">
							<input name="userid" type="hidden" value=<?php echo $userid; ?>>
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
