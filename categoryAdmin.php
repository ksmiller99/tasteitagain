<?php
//connect to database
require "mySQL.php";

if (!@session_start()){
	die("Cannot start session");
}

if (!isset($_SESSION['valid_user'])){
	echo '<script type="text/javascript">'."\n";
	echo 'alert("You must login.");'."\n";
	echo 'window.location.replace("index.php");'."\n";
	echo '</script>'."\n";
	exit();
}
/*
echo "firstname  ".$_SESSION['firstname']."  <br/>";
echo "isAdmin    ".$_SESSION['isAdmin']."    <br/>";
echo "isOwner    ".$_SESSION['isOwner']."    <br/>";
echo "isCustomer ".$_SESSION['isCustomer']." <br/>";
echo "email      ".$_SESSION['email']."      <br/>";
*/

if($_SESSION['isAdmin'] != 'Y'){
	echo '<script type="text/javascript">'."\n"; 
	echo 'alert("Only administrators can use this page");'."\n";
	echo 'window.history.back();'."\n";
	echo '</script>'."\n";
	exit();
}

?>

<script type="text/javascript">
function get_name(f) 
{
	f.updateFlag.value = "UPDATE";
	
	name = prompt("Please enter the new name: ",f.fieldData.value);
	if(!name){
		return;
	}else{
		f.fieldData.value = name;
		f.submit();	
	}
}
</script>

<html>
<head>
		<title>Taste it again - Category Admin</title>
	<?php //include("head.php"); ?>
</head>
<body leftmargin="0px" topmargin="0px" marginwidth="0px" marginheight="0px" bgcolor="black">
	<table cellspacing="0" cellpadding="0" width="900" align="center" border="0">

		<?php include("header.php"); ?>

		<tr height="500" bgcolor="#82FA58">
			<td colspan="2">
				<table cellspacing="0" cellpadding="0" width="900" border="0" height="100%">
					<tr valign="top">
						<td><center>
<!------------ Start of main content -------------------------------------------------->						
<?php
/*
echo "<pre>\n";
print_r($_POST);
echo "</pre>\n";
 */
 
//operation variables are the operation being performed by admin
$operation    = $_POST['operation'];     //SQL Operation UPDATE, INSERT, or DELETE
if ($_POST[updateFlag] == "UPDATE")
	$operation = "UPDATE";
$fieldData    = $_POST['fieldData'];    //data of the field being operated on
$fieldName    = $_POST['fieldName'];    //name of the field being operated on
$keyFieldData = $_POST['keyFieldData']; //primary key field name of the record being operated on
$keyFieldName = $_POST['keyFieldName']; //primary key value of the record being operated on
$table        = "CATEGORIES";

/*echo"operation    $operation   <br />\n";
echo"keyFieldData $keyFieldData<br />\n";
echo"keyFieldName $keyFieldName<br />\n";
echo"fieldData    $fieldData   <br />\n";
echo"fieldName    $fieldName   <br />\n";
echo"table        $table       <br />\n";
*/

if ($operation == "UPDATE" && $fieldName != "" && $keyFieldName != "" && $keyFieldData != "")
{
	$sql="UPDATE {$table} SET {$fieldName} = '{$fieldData}' WHERE {$keyFieldName} = '{$keyFieldData}';";
}else{	
	if (($operation == "Delete") && ($keyFieldName != "") && ($keyFieldData != ""))
	{
		$sql="DELETE FROM {$table} WHERE {$keyFieldName} = '{$keyFieldData}';";
	}else{	
		if (($operation == "INSERT") && ($fieldName != "") && ($fieldData != ""))
		{
			$sql="INSERT INTO {$table} ({$fieldName}) VALUES ('{$fieldData}');" ;
		}else{
			echo "Operation or values are incorrect. <br />";
		}	
	}
}

if ($sql != "")
{
	//echo "<br>attempting to run query $sql" . "<br>";
	if (!($results = $mysqli->query($sql))){
		echo("Operation query failed.<br />");
		echo("sql: $sql<br />");
	}
}		

// Get whole table
$sql = "SELECT * from {$table}";
if (!($results = $mysqli->query($sql))){
    die("Query to show fields from table failed: $sql");
}
echo "<h1>Edit Table: {$table}</h1>";
echo 'You are logged in as: '.$_SESSION['valid_user'].'<br/>';
echo "\n".'<table>';
echo "\n".'<form  action="'.$_SERVER['SCRIPT_NAME'].'"  method="POST"  >';
echo "\n".'<tr><td>Enter a new Category Name: </td><td><input type="text" name="fieldData" value="" ></td></tr>';
echo "\n".'<input type="hidden" name="operation"     value="INSERT">';
echo "\n".'<input type="hidden" name="fieldName"     value="NAME">';
echo "\n".'<tr><td align="center" colspan = "2"><input type="submit" value="Add Category" >';
echo "\n".'</form>';
echo "\n".'</table>';

echo '<table style="border:1px #000000 solid" bgcolor="white"><tr>';
$finfo = $results->fetch_fields();
foreach ($finfo as $val) {
	echo "<td>{$val->name}</td>";
}
echo "</tr>\n";

// printing table rows
while($row = $results->fetch_row())
{
    echo "<tr>";

	//test comment
    foreach($row as $cell)
	echo "<td>$cell</td>";
	echo "\n".'<form action="'.$_SERVER['SCRIPT_NAME'].'" method="POST">'."\n";
	echo '<td><input type="button" name="btnChangeName" value="ChangeName" onclick="get_name(this.form);"></td>'."\n";
	echo '<td><input type="submit" name="operation"     value="Delete"  >                                 </td>'."\n";
	echo '    <input type="hidden" name="keyFieldData"  value="'.$row[0].'"                                   >'."\n";//the key value of the record being operated on
	echo '    <input type="hidden" name="keyFieldName"  value="'.$finfo[0]->name.'"                           >'."\n";//the name of the key field
	echo '    <input type="hidden" name="fieldName"     value="'.$finfo[1]->name.'"                           >'."\n";//the name of the field being updated
	echo '    <input type="hidden" name="fieldData"     value="'.$row[1].'"                                   >'."\n";//holds the new name, set in get_name
	echo '    <input type="hidden" name="updateFlag"    value=""                                              >'."\n";//holds the new name, set in get_name
	echo "</form>";
	echo "</tr>\n";
}
echo "</table>";

$results->close();
?>


<!-- End ---------------------------------------------------------------------------------------------------------->							
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