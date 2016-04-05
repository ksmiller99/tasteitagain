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
	<title>Taste It Again - Contact Info</title> 
</head>

<body leftmargin="0px" topmargin="0px" marginwidth="0px" marginheight="0px" bgcolor="gray">

	<table cellspacing="0" cellpadding="0" width="900" align="center" style="border:3px #000000 solid">

		<?php include("header.php"); ?>

		<tr height="500" bgcolor="white">
			<td colspan="2">
				<table cellspacing="0" cellpadding="0" width="900" border="0" height="100%">
					<tr valign="top">
						<td>
							<center><h1>Contact Us</h1><center>
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
