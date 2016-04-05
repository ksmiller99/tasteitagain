<?php
//connect to database
require "mySQL.php";

if (!@session_start()){
	die("Cannot start session");
}

if (!isset($_SESSION['valid_user'])){
	echo '<script type="text/javascript"> alert("You must login.");';
	echo 'window.location.replace("index.php"); </script>';
	exit();
}

if(!($_SESSION['isOwner'] == 'Y')){
	echo '<script type="text/javascript"> ';
	echo 'alert("You must logged in as owner to view orders.");';
	echo 'window.location = '."'".$_SERVER['HTTP_REFERER']."'";
	echo '</script>';
	exit();
}

$sql = "select * from ORDERS";
if(!($orders = $mysqli->query($sql))){
	die("ORDERS - could not get records.");
}
?>

<html>
<head>
	<title>Taste it Again - Home</title>
	<?php //include("head.php"); ?>	
</head>

<body leftmargin="0px" topmargin="0px" marginwidth="0px" marginheight="0px" bgcolor="black">
	<table cellspacing="0" cellpadding="0" width="900" align="center" border="0">

		<?php include("header.php"); ?>

		<tr height="500" bgcolor="#82FA58">
			<td colspan="2">
				<table cellspacing="0" cellpadding="0" width="900" border="0" height="100%">
					<tr valign="top">
						<td>
<!------------ Start of added content --------------------------------------------------------------------------------------->						
							<center><h1>View Orders</h1><center>
							<table style="border:1px solid black" align="center" bgcolor="white">
								<tr>
									<td>Order ID</td>
									<td>Name</td>
									<td>Phone</td>
									<td>Address 1</td>
									<td>Address 2</td>
									<td>City</td>
									<td>State</td>
									<td>Zip</td>
									<td>Time</td>
								</tr>
								<?php
									while($row = $orders->fetch_assoc()){
									echo '<tr>';
									echo '	<td><a href="displayorder.php?ordUID='.$row['ORDID'].'&calledBy=owner">'.$row['ORDID'].'</a></td>';
									echo '	<td>'.$row['FULLNAME'].'</td>';
									echo '	<td>'.$row['CONTACTPHONE'].'</td>';
									echo '	<td>'.$row['DELADDLINE1'].'</td>';
									echo '	<td>'.$row['DELADDLINE2'].'</td>';
									echo '	<td>'.$row['DELCITY'].'</td>';
									echo '	<td>'.$row['DELSTATE'].'</td>';
									echo '	<td>'.$row['DELZIP'].'</td>';
									echo '	<td>'.$row['TIME'].'</td>';
									echo '</tr>';
									}
								?>
							</table>
<!------------- End of added content ---------------------------------------------------------------------------------------->						
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
