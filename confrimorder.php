<?php
if (!@session_start()){
	die("Cannot start session");
}
	
//check if in a valid session	
if (!isset($_SESSION['valid_user'])){
	//they have not tried to login, or logged out
	echo '<script type="text/javascript">';
	echo 'alert("You must login first");';
	echo 'window.location.replace("index.php");';
	echo '</script>';
	exit();
}

//check if is a customer
if ($_SESSION['isCustomer'] != 'Y'){
	echo '<script type="text/javascript"> ';
	echo 'alert("You must logged in to view your order.");';
	echo '</script>';
	echo 'window.location = '."'".$_SERVER['HTTP_REFERER']."'";
	exit();
}

$ordUID = $_GET['ordUID'];
?>

<html>
<head>
	<title>Taste It Again - Check Out</title>
</head>
<body leftmargin="0px" topmargin="0px" marginwidth="0px" marginheight="0px" bgcolor="black">
<script type="text/javascript">
<!--
function test_it(f){
	if(f.checkValidity()){
		f.posted.value = "OK";
		f.submit();
	}
	else{
		alert("Please check all fields for valid data");
		return	;
	}
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
	//user attemped checkout
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
	$address2 = $row['ADDLINE2	'];
	$city     = $row['CITY'];
	$state    = $row['STATE'];
	$zipCode  = $row['ZIP'];
	$mailflag = $row['EMAILFLAG'];
	$txtflag  = $row['TXTMSGFLAG'];
}

if ($posted == "OK"){
	//insert new order
	$ordUID = uniqid(); //create unique ID
	$fullName = $fName." ".$lName;
	$time = time();
	
	$sql1 = "INSERT INTO `ORDERS`
			(`ORDID`  , `CUSTID` , `FULLNAME` , `CONTACTPHONE`, `DELIVERFLAG`, `DELADDLINE1`, `DELADDLINE2`, `DELCITY`, `DELSTATE`, `DELZIP`  , `TIME`)
     VALUES ('$ordUID', '$userid', '$fullName', '$phone'      ,'TRUE'        , '$address'   , '$address2'  , '$city'  , '$state'  , '$zipCode', $time)";
				 	
	//transaction begin with implicit autocommit off 
	if (!$mysqli->query("START TRANSACTION;")){
		echo '<script type="text/javascript">'."\n"; 
		echo 'alert("Start transaction failed: '.$mysqli->error.'");'."\n";
		echo 'window.back();';
		echo '</script>'."\n";
		exit();
	}
	
	//insert into ORDERS table
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
	
	//insert ORDERITEMS information
	$lineNum = 0;
	foreach($cart as $key=>$value){
		echo"in loop<br />";
		++$lineNum;
		$sql2 = "insert into `ORDERITEMS`
              (`ORDID`, 
               `LINENUM`, 
               `QTY`,
               `EXTPRICE`,  
               `NAME`, 
               `PRICE`, 
               `TAXABLE`) 
		select 
               '$ordUID' as ORDID, 
               '$lineNum' as LINENUM, 
               '$value' as QTY, 
               ($value * PRICE) as EXTPRICE,
               NAME, 
               PRICE, 
               TAXABLE 
		from PRODUCTS 
		where PRODID = '$key'";

		echo '<script type="text/javascript">'."\n"; 
		echo 'alert('.$sql2.');'."\n";
		echo 'window.back();';
		echo '</script>'."\n";
					
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
	}//end foreach
	
	if (($lineNum > 0) && ($lineNum == count($cart))){ //make sure all line items were added
		//commit transaction
		if ($mysqli->query("COMMIT;")){
			echo '<script type="text/javascript">'."\n"; 
			echo 'alert("Order Saved, confirmation sent to '.$_SESSION['email'].'");'."\n";
			echo 'window.location.replace("confrimorder.php");';
			echo '</script>'."\n";
		}else{
			echo '<script type="text/javascript">'."\n"; 
			echo 'alert("Commit transaction failed: '.$mysqli->error.'");'."\n";
			echo 'window.back();';
			echo '</script>'."\n";
			exit();
		}
	}else{
		//rollback
		$mysqli->query("ROLLBACK;"); //must add check for rollback failure
		echo '<script type="text/javascript">'."\n"; 
		echo 'alert("Account update failed: '.$err.'");'."\n";
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
							<table cellspacing="0" cellpadding="0" width="900" border="0">
								<tr>
									<td>
										<center><h1>Checkout</h1></center>
									</td>
								</tr>
							</table>
							<form name="update" id="update" action = '' method = "post">
							<table cellspacing="1" cellpadding="3" style="border:1px #000000 solid" bgcolor="#BDBDBD" align="center">
								<tr>
									<td colspan="2" align="center">
										<b><ul>Update Delivery Address</b></ul>
									</td>
								</tr>
								<tr>
									<td>
										First Name
									</td>
									<td>
										<input type="text" 
										name="fName"
										required
										size="20" 
										value="<?php echo $fName; ?>">
									</td>
								</tr>
								<tr>
									<td>
										Last Name
									</td>
									<td>
										<input type="text" 
										name="lName" 
										required
										size="20" 
										value="<?php echo $lName; ?>">
									</td>
								</tr>
								<tr>
									<td>
										Address
									</td>
									<td>
										<input type="text" 
										name="address" 
										required
										size="20" 
										value="<?php echo $address; ?>">
									</td>
								</tr>
								<tr>
									<td>
										Address Line 2
									</td>
									<td>
										<input type="text" 
										name="address2" 
										size="20" 
										value="<?php echo $address2; ?>">
									</td>
								</tr>
								<tr>
									<td>
										City
									</td>
									<td>
										<input type="text" 
										name="city" 
										required
										size="20" 
										value="<?php echo $city; ?>">
									</td>
								</tr>
								<tr>
									<td>
										State
										<input type="text" 
										name="state" 
										size="3" 
										value="<?php echo $state; ?>">
									</td>
									<td>
										Zip Code
										<input type="text" 
										name="zipCode" 
										required
										size="5" 
										value="<?php echo $zipCode; ?>">
									</td>
								</tr>
								<tr>
									<td>
										Phone Number
									</td>
									<td>
										<input type="text" 
										name="phone" 
										required
										size="20" 
										value="<?php echo $phone; ?>">
									</td>
								</tr>
								<tr>
									<td>
										Email Address
									</td>
									<td>
										<input type="text" 
										name="email" size="30" 
										required
										value="<?php echo $email; ?>">
									</td>
								</tr>
								<tr>
									<td>
										Credit Card Number
									</td>
									<td>
										<input type="text" 
										name="ccnum" size="30" 
										required
										pattern="[A-Z]|[a-z]{16}"
										title="for this demonstration system, input 16 letters"
										maxlength="16"
										length
										value="">
									</td>
								</tr>
								
							</table>
							<table align="center">
								<tr>
									<td>
										<input name="btnSave" type="button" value="Checkout" onclick="test_it(this.form);"/>
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
