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

<script type="text/javascript" >
function validate(f){
	if(f.checkValidity()){
		f.submit();
	}else{
		alert("Invalid data - Please check all fields");
		return;
	}
}
</script>

<html>
<head>
		<title>Taste it again - Product Admin</title>
</head>
<body leftmargin="0px" topmargin="0px" marginwidth="0px" marginheight="0px" bgcolor="black">
	<table cellspacing="0" cellpadding="0" width="900" align="center" border="0">

		<?php include("header.php"); ?>

		<tr height="500" bgcolor="#82FA58">
			<td colspan="2">
				<table cellspacing="0" cellpadding="0" width="900" border="0" height="100%">
					<tr valign="top">
						<td><center>
<!-- Start -------------------------------------------------------------------------------------------------------->							
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

//user-input fields for new record
$pProdID  = $_POST['pProdID'];
$pCatID   = $_POST['pCatID'];
$pCatName = $_POST['pCatName'];
$pName    = $_POST['pName'];
$pDesc    = $_POST['pDesc'];
$pPrice   = $_POST['pPrice'];
$pTax     = (strtoupper($_POST['pTax']== "Y")) ? "1" : "0";
//$pCstart  = !(strtotime($_POST['pCstart'])) ? "NULL" : $_POST['pCstart'];
//$pCend    = !(strtotime($_POST['pCend'])) ? "NULL" : $_POST['pCend'];
$pHide    = (strtoupper($_POST['pHide']== "Y")) ? "1" : "0";

//filter products table
$filterCatID = $_POST['filterCatID'];

//default values when user selected Edit
$edProdID  = $_POST['edProdID'];
$edName    = $_POST['edName']; 
$edDesc    = $_POST['edDesc'];  
$edCatID   = $_POST['edCatID'];   
$edCatName = $_POST['edCatName'];   
$edPrice   = $_POST['edPrice']; 
$edTax     = ($_POST['edTax'] == "1") ? "Y" : "N";   
//$edCstart  = (($_POST['edCstart'] == '0000-00-00') ? "" : $_POST['edCstart']);
//$edCend    = (($_POST['edCend']   == '0000-00-00') ? "" : $_POST['edCend']);
$edHide    = ($_POST['edHide'] == "1") ? "Y" : "N";  

//buttons from each row
$btnEditProduct   = $_POST['btnEditProduct'];
$btnDeleteProduct = $_POST['btnDeleteProduct'];
if($btnDeleteProduct == "Delete")
	$operation = "DELETE";

$posted       = $_POST['posted'];       //user submitted a form
$table        = "PRODUCTS";

//echo"operation    $operation   <br />\n";
//echo"keyFieldData $keyFieldData<br />\n";
//echo"keyFieldName $keyFieldName<br />\n";
//echo"fieldData    $fieldData   <br />\n";
//echo"fieldName    $fieldName   <br />\n";
//echo"table        $table       <br />\n";

//get list of categories
if (!($catList = $mysqli->query("select CATID, NAME from CATEGORIES;"))){
	die("Cannot get category list");
}

/*echo "posted: $posted<br />";
echo "operation: $operation<br />";
echo "keyFieldName: $keyFieldName<br />";
echo "keyFieldData: $keyFieldData<br />";
*/

//perform user selected operation
if ($posted == "Y"){
	if (($operation == "UPDATE")  && ($pProdID != ""))
	{
		/*$sql="UPDATE {$table} SET NAME            = '{$pName}',
								  DESCRIPTION     = '{$pDesc}',
								  CATID           = '{$pCatID}',
								  PRICE           = '{$pPrice}',
								  TAXABLE         = '{$pTax}', 
								  COUPONSTARTDATE = '{$pCstart}', 
								  COUPONENDDATE   = '{$pCend}',
								  HIDEFLAG        = '{$pHide}'
								  WHERE PRODID = '{$pProdID}';";*/
								  
		$sql="UPDATE {$table} SET NAME            = '{$pName}',
								  DESCRIPTION     = '{$pDesc}',
								  CATID           = '{$pCatID}',
								  PRICE           = '{$pPrice}',
								  TAXABLE         = '{$pTax}', 
								  HIDEFLAG        = '{$pHide}'
								  WHERE PRODID = '{$pProdID}';";
	}else{	
		if (($operation == "DELETE") && ($edProdID != ""))
		{
			$sql="DELETE FROM {$table} WHERE PRODID = '{$edProdID}';";
			//delete $ed*
			unset($edProdID, $edName, $edDesc, $edCatID, $edCatName, $edPrice, $edTax, $edCstart, $edCend, $edHide);   
		}else{	
			if ($operation == "INSERT")
			{
//				$sql="INSERT INTO {$table} (NAME, DESCRIPTION, CATID, PRICE, TAXABLE, COUPONSTARTDATE, COUPONENDDATE, HIDEFLAG) 
//									VALUES ('$pName', '$pDesc', '$pCatID', '$pPrice', '$pTax', '$pCstart', '$pCend', '$pHide');" ;
									
				$sql="INSERT INTO {$table} (NAME    , DESCRIPTION, CATID    , PRICE    , TAXABLE, HIDEFLAG) 
								    VALUES ('$pName', '$pDesc'   , '$pCatID', '$pPrice', '$pTax', '$pHide');" ;
			}else{
				if($btnEditProduct == "Edit"){ //user selected edit - no operation
					//if user is editing existing product, get it's CATID
					$pCatID = $edCatID;
				}else
					echo "Error - Operation or values are incorrect. <br />";
			}	
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


//disply edit/input table
echo "<h1>Edit Table: {$table}</h1>"."\n";
echo '<table style="border:1px #000000 solid">'."\n";
	echo '<tr>'."\n";
		echo '<td>Category</td>'."\n";
		echo '<td>Name</td>'."\n";
		echo '<td>Description</td>'."\n";
		echo '<td>Price</td>'."\n";
		echo '<td>Taxable</td>'."\n";
//		echo '<td>CpnStart</td>'."\n";
//		echo '<td>CpnEnd</td>'."\n";
		echo '<td>Hide</td>'."\n";
	echo '</tr>'."\n";
	echo '<tr>'."\n";
		echo '<td>
				<select name="pCatID" form="newProductForm" >'."\n";
					while($row = $catList->fetch_row()){
						echo '<option ';
						if ($row[0] == $edCatID)
							echo 'selected="selected" ';
						echo 'value="'.$row[0].'">'.$row[1].'</option>'."\n";
					}
		echo '	</select>
			</td>'."\n";

		echo '<form  name="addProduct" action="'.$_SERVER['SCRIPT_NAME'].'"  method="POST"  id="newProductForm">';
		echo '<td>
				<input type="text" 
				name="pName" 
				required 
				title="the name of the product"
				maxlength="32"
				size="10"
				value = "'.$edName.'">
			</td>'."\n";
		echo '<td>
				<input type="text" 
				name="pDesc"
				title="the description of the product"
				maxlength="256"
				size="32"
				value = "'.$edDesc.'">
			</td>'."\n";
		echo '<td>
				<input type="number" 
				name="pPrice"  
				title="price in dollars and cents" 
				pattern = "^-?\d+(\.\d{2})?$"
				maxlength="7"
				size="7"
				value = "'.$edPrice,'">
			</td>'."\n";
		echo '<td>
				<input type="text" 
				name="pTax" 
				title="is product taxable? Y/N" 
				pattern="Y|N|y|n"
				maxlength="1"
				size="1"
				value = "'.$edTax.'">
			</td>'."\n";
/*		echo '<td>
				<input type="date" 
				name="pCstart" 
				title="coupon start date in yyyy-mm-dd" 
				pattern="^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$"   
				maxlength="10" 
				size="10" 
				value = "'.$edCstart.'">
			</td>'."\n";
		echo '<td>
				<input type="date" 
				name="pCend" 
				title="coupon end date in yyyy-mm-dd"
				pattern="^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$"   
				maxlength="10" 
				size="10" 
				value = "'.$edCend.'">
			</td>'."\n";*/
		echo '<td>
				<input type="text" 
				name="pHide" 
				title="should product be hidden from menu? Y/N" 
				pattern="Y|N|y|n"
				maxlength="1"
				size="1";
				value = "'.$edHide.'">
			</td>'."\n";
	echo '</tr>'."\n";
	echo "\n".'<input type="hidden" name="posted"      value="Y">';
	echo "\n".'<input type="hidden" name="pProdID"     value="'.$edProdID.'">';
	echo "\n".'<input type="hidden" name="filterCatID" value="'.$filterCatID.'">';
	if ($btnEditProduct == "Edit"){
		echo "\n".'<tr><td colspan = "8" align="center"><input type="button" value="Update" onclick="validate(this.form)" >';
	    echo "\n".'<input type="hidden" name="operation"     value="UPDATE">';
	}else{
		echo "\n".'<tr><td colspan = "8" align="center"><input type="button" value="Add Product" onclick="validate(this.form)" >';
	    echo "\n".'<input type="hidden" name="operation"     value="INSERT">';
	}
	echo "\n".'</form>';
echo "\n".'</table><br /><br />';

// Get all table (or filtered) data
//echo "filterCatID: ".$filterCatID."<br />\n";
if(intval($filterCatID) > 0){
	$sql = "SELECT p.* , c.NAME AS 'CATNAME'
	FROM PRODUCTS p
	LEFT JOIN CATEGORIES c ON p.CATID = c.CATID
	WHERE p.CATID = '".$filterCatID."'
	ORDER BY CATNAME";
}else{
	$sql = "SELECT p.* , c.NAME AS 'CATNAME'
	FROM PRODUCTS p
	LEFT JOIN CATEGORIES c ON p.CATID = c.CATID
	ORDER BY CATNAME";
}
//echo $sql;
if (!($results = $mysqli->query($sql))){
    die("Query to show fields from table failed: $sql");
}

echo '<table><tr><td>';
echo '<select name="filterCatID" form="filterForm" >'."\n";
			echo '<option value="none">None</option>';
			$catList->data_seek(0);
			while($row = $catList->fetch_row()){
				echo '<option ';
				if ($row[0] == $filterCatID)
					echo 'selected="selected" ';
				echo 'value="'.$row[0].'">'.$row[1].'</option>'."\n";
			}
echo '	</select>'."\n";
echo '</td><td>';
echo '<form  name="filter" action="'.$_SERVER['SCRIPT_NAME'].'"  method="POST"  id="filterForm">'."\n";
echo '<input type="submit" name="btnFilter" value="Filter">'."\n";
echo '</form>'."\n";
echo '</td></tr></table>';

echo '<table style="border:1px #000000 solid" bgcolor="white">';
//$finfo = $results->fetch_fields();
//foreach ($finfo as $val) {
//	echo "<td>{$val->name}</td>";}
	
echo "<tr>\n";
echo '<td>PRODID</td>'."\n";
echo '<td>NAME</td>'."\n";
echo '<td>CATNAME</td>'."\n";
echo '<td>PRICE</td>'."\n";
echo '<td>TAXABLE</td>'."\n";
//echo '<td>Cpn Start</td>'."\n";
//echo '<td>CpnEnd</td>'."\n";
echo '<td>HIDEFLAG</td>'."\n";
echo "</tr>\n";

// printing table rows
while($row = $results->fetch_assoc())
{
    echo "<tr>";
	echo "<tr>\n";
	echo '<td>'.$row['PRODID']     .'</td>'."\n";
	echo '<td><span title="'.$row['DESCRIPTION'].'"><b>'.$row['NAME'].'</b></span></td>'."\n";
	echo '<td>'.$row['CATNAME']    .'</td>'."\n";
	echo '<td>'.number_format($row['PRICE'],2).'</td>'."\n";
	echo '<td>'.(($row['TAXABLE'] == "1") ? "Y" : "N").'</td>'."\n";
//	echo '<td>'.(($row['COUPONSTARTDATE'] == '0000-00-00') ? "" : $row['COUPONSTARTDATE']).'</td>'."\n";
//	echo '<td>'.(($row['COUPONENDDATE'] == '0000-00-00')   ? "" : $row['COUPONENDDATE'])  .'</td>'."\n";
	echo '<td>'.(($row['HIDEFLAG'] == "1") ? "Y" : "N").'</td>'."\n";
	echo '<form action="'.$_SERVER['SCRIPT_NAME'].'" method="POST">'."\n";
	echo '<td><input type="submit" name="btnEditProduct"   value="Edit"                              ></td>'."\n";
	echo '<td><input type="submit" name="btnDeleteProduct" value="Delete"                            ></td>'."\n";
	echo '    <input type="hidden" name="filterCatID"      value="'.$filterCatID.'"                  >'."\n";
	echo '    <input type="hidden" name="posted"           value="Y"                                 >'."\n";//
	echo '    <input type="hidden" name="edProdID"         value="'.$row['PRODID'].'"                >'."\n";//
	echo '    <input type="hidden" name="edName"           value="'.$row['NAME'].'"                  >'."\n";//
	echo '    <input type="hidden" name="edDesc"           value="'.$row['DESCRIPTION'].'"           >'."\n";//
	echo '    <input type="hidden" name="edCatID"          value="'.$row['CATID'].'"                 >'."\n";//
	echo '    <input type="hidden" name="edPrice"          value="'.number_format($row['PRICE'],2).'">'."\n";//
	echo '    <input type="hidden" name="edTax"            value="'.$row['TAXABLE'].'"               >'."\n";//
//	echo '    <input type="hidden" name="edCstart"         value="'.$row['COUPONSTARTDATE'].'"       >'."\n";//
//	echo '    <input type="hidden" name="edCend"           value="'.$row['COUPONENDDATE'].'"         >'."\n";//
	echo '    <input type="hidden" name="edHide"           value="'.$row['HIDEFLAG'].'"              >'."\n";//
	echo '    <input type="hidden" name="edCatName"        value="'.$row['CATNAME'].'"               >'."\n";//
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