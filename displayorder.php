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
if (($_SESSION['isCustomer'] != 'Y') && ($_SESSION['isOwner'] != 'Y')){
	echo '<script type="text/javascript"> ';
	echo 'alert("You must logged in as customer or owner to view orders.");';
	echo 'window.location = '."'".$_SERVER['HTTP_REFERER']."'";
	echo '</script>';
	exit();
}

$ordUID = $_GET['ordUID'];
if ($ordUID == ''){
	echo '<script type="text/javascript"> ';
	echo 'alert("Error - invalid order ID: '.$ordUID.'");';
	echo 'window.location = '."'".$_SERVER['HTTP_REFERER']."'";
	echo '</script>';
	exit();
}

//changes function of button at the bottom
$calledBy = $_GET['calledBy'];

?>

<html>
<head>
	<title>Taste It Again - Display Order</title>
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


//load ORDERS record info from database
$sql = "select * from ORDERS where ORDID = '$ordUID';";
//echo "sql: $sql<br />";
if (!($results = $mysqli->query($sql))){
	die("Cannot get ORDERS record: ".$mysqli->error);
}
if ($results->num_rows != 1){
	die("ORDERS - Wrong number of rows: ".$results->num_rows." 	$sql");
}
$order = $results->fetch_assoc();

$sql = "select * from ORDERITEMS where ORDID = '$ordUID'";
if (!($lineitems = $mysqli->query($sql))){
	die("ORDERITEMS - Cannot get records");
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
										<center><h1>Order Confirmation</h1></center>
									</td>
								</tr>
							</table>
							<table cellspacing="1" cellpadding="3" style="border:1px #000000 solid" bgcolor="white" align="center">
								<tr>
									<td colspan="2" align="center">
										<b><ul>Delivery Address</b></ul>
									</td>
								</tr>
								<tr>
									<td>Full Name</td>
									<td><?php echo $order['FULLNAME']; ?></td>
								</tr>
								<tr>
									<td>Address</td>
									<td><?php echo $order['DELADDLINE1']; ?></td>
								</tr>
								<tr>
									<td>Address Line 2</td>
									<td><?php echo $order['DELADDLINE2']; ?></td>
								</tr>
								<tr>
									<td>City</td>
									<td><?php echo $order['DELCITY']; ?></td>
								</tr>
								<tr>
									<td>State</td>
									<td><?php echo $order['DELSTATE']; ?></td>
								</tr>
								<tr>
									<td>Zip Code</td>
									<td><?php echo $order['DELZIP']; ?></td>
									
								</tr>
								<tr>
									<td>Phone Number</td>
									<td><?php echo $order['CONTACTPHONE']; ?></td>
								</tr>
								<tr>
									<td>Time Posted</td>
									<td><?php echo $order['TIME']; ?></td>
								</tr>
							</table>
							<table align="center">
								<tr>
									<td>
									<?php
										if ($calledBy == '')
											echo"<input type=\"button\" value=\"Home\" onclick=\"location.href='home.php'\" >";
										else
											echo"<input type=\"button\" value=\"Return\" onclick=\"window.history.back()\" >";
									?>
									</td>
								</tr>
							</table>
					<table style="border:1px #000000 solid" align="center" bgcolor="white">
						<tr>
							<td><b>#</b></td><td><b>Name</b></td><td><b>Price</b></td><td><b>Taxable</b></td><td><b>Qty</b></td><td><b>Ext Price</b></td>
						</tr>
						<?php
						
						$total = 0;
						while($row = $lineitems->fetch_assoc() ){
							echo'<tr>';
							echo'	<td>'.$row['LINENUM'].'</td>';
							echo'	<td>'.$row['NAME'].'</td>';
							echo'	<td align="right">'.number_format($row['PRICE'],2).'</td>';
							echo'	<td align="center">'.$row['TAXABLE'].'</td>';
							echo'	<td align="center">'.$row['QTY'].'</td>';
							echo'	<td align="right">'.number_format($row['EXTPRICE'],2).'</td>';
							echo'</tr>';
							$total += $row['EXTPRICE'];
						}
						echo'<tr><td></td><td></td><td></td><td></td><td align="right"><b>Total:</b></td><td align="right">'.number_format($total,2).'</td></tr>';
						?>
					</table>
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
