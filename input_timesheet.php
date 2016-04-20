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
	<title>Taste It Again - Merchandise</title>
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
<!-- CONTENT -->						
							<center><h1>Input Timesheet</h1><center>
						</td>
					</tr>
					<tr>
						<td align="center" valign="center">
							<h1>Coming Soon!</h1>
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
