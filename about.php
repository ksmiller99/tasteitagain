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
?>
<html>
<head>
	<title>Taste It Again - About Us</title>
</head>

<body leftmargin="0px" topmargin="0px" marginwidth="0px" marginheight="0px" bgcolor="gray">

	<table cellspacing="0" cellpadding="0" width="900" align="center" style="border:3px #000000 solid">

		<?php include("header.php"); ?>

		<tr height="500" bgcolor="white">
			<td colspan="2">
				<table cellspacing="0" cellpadding="0" width="900" border="0" height="100%">
					<tr valign="top">
						<td>
							<center><h1>About Us</h1><center>
						</td>
					</tr>
					<tr>
						<td>
						Taste it Again, across from the Bloomfield Center redevelopment site, was started by Jacquline Warburton and Ivolett Bredwood, lifelong friends from Jamaica, in time for Bloomfield’s restaurant week in early March. Try the Ackee and Saltfish (“Jamaica’s National Dish” $13) and the tender Jerk Chicken ($10.50) and one of the homemade specialty drinks, like Sorrel (made from the herb) or ginger beer.
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
