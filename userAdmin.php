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

if($_SESSION['isOwner'] != 'Y'){
	echo '<script type="text/javascript">'."\n"; 
	echo 'alert("Only owners can use this page");'."\n";
	echo 'window.history.back();'."\n";
	echo '</script>'."\n";
	exit();
}

?>

<html>
<head>

<script type="text/javascript" >
function validate(f){
    if(!f.checkValidity()){
        alert("Invalid data - Please check all fields");
        return;
    }
        
    if (f.operation.value == "INSERT" && f.pPW.value == ""){
       alert("You have to enter a password for new users!");
       return;
    }

    if (f.pPW.value != f.pPW2.value){
       alert("Passwords do not match");
       return;
    }

    //changes to catch XSS and SQL injection
    if ((f.pFirstName.value.indexOf("'") != -1)  || (f.pFirstName.value.indexOf("<") != -1)  ||
	(f.pMiddleInit.value.indexOf("'") != -1) || (f.pMiddleInit.value.indexOf("<") != -1) ||
        (f.pLastName.value.indexOf("'") != -1)   || (f.pLastName.value.indexOf("<") != -1)   ||
        (f.pEmail.value.indexOf("'") != -1)      || (f.pEmail.value.indexOf("<") != -1)      ||
        (f.pPhone.value.indexOf("'") != -1)      || (f.pPhone.value.indexOf("<") != -1)      ||
        (f.pPW.value.indexOf("'") != -1)         || (f.pPW.value.indexOf("<") != -1)         ||
        (f.pPW2.value.indexOf("'") != -1)        || (f.pPW2.value.indexOf("<") != -1))
    {
	alert("You have illegal characters in one or more fields!");
	return;
    }
    
    //alert(f.operation_id.value + ' ' + f.operation_pwd.value ) ; 
    f.submit();    
       
}

</script>

		<title>Taste it again - User Admin</title>
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
$operation = (isset($_POST['operation'])) ? $_POST['operation'] : '' ;   //SQL Operation UPDATE, INSERT, or DELETE
$updateFlag = (isset($_POST['UPDATE'])) ? $_POST['UPDATE'] : '';
if ($updateFlag == "UPDATE")
	$operation = "UPDATE";

//user-input fields for new record
$pUserID      = isset($_POST['pUserID'] )     ? $_POST['pUserID']     : '' ;
$pPWHash      = isset($_POST['pPWHash'] )     ? $_POST['pPWHash']     : '' ;
$pFullName    = isset($_POST['pFirstName'])   ? $_POST['pFirstName']  : '' ;
$pMiddleInit  = isset($_POST['pMiddleInit'])  ? $_POST['pMiddleInit'] : '' ;
$pLastName    = isset($_POST['pLastName'])    ? $_POST['pLastName']   : '' ;
$pEmail       = isset($_POST['pEmail'])       ? $_POST['pEmail']      : '' ;
$pPhone       = isset($_POST['pPhone'])       ? $_POST['pPhone']      : '' ;
$pAdmin       = isset($_POST['pAdmin'])       ? $_POST['pAdmin']      : '' ;
$pOwner       = isset($_POST['pOwner'])       ? $_POST['pOwner']      : '' ;
$pCustomer    = isset($_POST['pCustomer'])    ? $_POST['pCustomer']   : '' ;
$pEmployee    = isset($_POST['pEmployee'])    ? $_POST['pEmployee']   : '' ;
$pPW          = isset($_POST['pPW'])          ? $_POST['pPW']         : '' ;

//filter products table
//$filterCatID = isset($_POST['filterCatID']) ? $_POST['filterCatID']  : '';

//default values when user selected Edit
$edUSERID      = isset($_POST['edUserID'] )     ? $_POST['edUserID']     : '' ;
$edPWHash      = isset($_POST['edPWHash'] )     ? $_POST['edPWHash']     : '' ;
$edFULLNAME   = isset($_POST['edFirstName'])   ? $_POST['edFirstName']  : '' ;
$edMiddleInit  = isset($_POST['edMiddleInit'])  ? $_POST['edMiddleInit'] : '' ;
$edLastName    = isset($_POST['edLastName'])    ? $_POST['edLastName']   : '' ;
$edEmail       = isset($_POST['edEmail'])       ? $_POST['edEmail']      : '' ;
$edPhone       = isset($_POST['edPhone'])       ? $_POST['edPhone']      : '' ;
$edAdmin       = isset($_POST['edAdmin'])       ? $_POST['edAdmin']      : 'N' ;
$edOwner       = isset($_POST['edOwner'])       ? $_POST['edOwner']      : 'N' ;
$edCustomer    = isset($_POST['edCustomer'])    ? $_POST['edCustomer']   : 'N' ;
$edEmployee    = isset($_POST['edEmployee'])    ? $_POST['edEmployee']   : 'N' ;

//buttons from each row
$btnEditEmp   = isset($_POST['btnEditUser']  ) ? $_POST['btnEditUser']    : '';
$btnDeleteUser = isset($_POST['btnDeleteUser']) ? $_POST['btnDeleteUser']  : '';
if($btnDeleteUser == "Delete")
	$operation = "DELETE";

$posted = isset($_POST['posted']) ? $_POST['posted']  : '';       //user submitted a form
$table        = "USERS";

//echo"operation    $operation   <br />\n";
//echo"keyFieldData $keyFieldData<br />\n";
//echo"keyFieldName $keyFieldName<br />\n";
//echo"fieldData    $fieldData   <br />\n";
//echo"fieldName    $fieldName   <br />\n";
//echo"table        $table       <br />\n";

/*echo "posted: $posted<br />";
echo "operation: $operation<br />";
echo "keyFieldName: $keyFieldName<br />";
echo "keyFieldData: $keyFieldData<br />";
*/

//perform user selected operation
if ($posted == "Y"){
    if (($operation == "UPDATE")  && ($pUserID != ""))
    {
        if($pPW != "") $pPWHash = md5($pPW);
        $sql="UPDATE {$table} SET EMAILADD     = '{$pEmail}',
                                  PWHASH       = '{$pPWHash}',
                                  FIRSTNAME    = '{$pFullName}',
                                  MINIT        = '{$pMiddleInit}', 
                                  LASTNAME     = '{$pLastName}',
                                  PHONE        = '{$pPhone}',
                                  ADMINFLAG    = '{$pAdmin}',
                                  OWNERFLAG    = '{$pOwner}',
                                  CUSTFLAG     = '{$pCustomer}', 
                                  EMPFLAG      = '{$pEmployee}'
                                  WHERE USERID = '{$pUserID}';";
    }else{	
        if (($operation == "DELETE") && ($edUSERID != "")){
            $sql="DELETE FROM {$table} WHERE USERID = '{$edUSERID}';";
            $edLastName = $edFULLNAME = $edMiddleInit = $edEmail = $edPhone = $edAdmin = $edOwner = $edCustomer = $edEmployee = '';   
        }else{	
            if ($operation == "INSERT"){
                $pPWHash = md5($pPW);
                $sql="INSERT INTO {$table} (EMAILADD,  PWHASH,     FIRSTNAME,     MINIT,          LASTNAME,     PHONE,     ADMINFLAG, OWNERFLAG, CUSTFLAG,     EMPFLAG) 
                                    VALUES ('$pEmail', '$pPWHash', '$pFullName', '$pMiddleInit', '$pLastName', '$pPhone', '$pAdmin', '$pOwner', '$pCustomer', '$pEmployee');" ;
            }else{
                if($btnEditEmp == "Edit"){ //user selected edit - no operation
                }else
                    echo "<h3>Error - Operation or values are incorrect. </h3>";
            }	
        }
    }
}

if (isset($sql) && ($sql != ""))
{
	//echo "<br>attempting to run query $sql" . "<br>";
	if (!($results = $mysqli->query($sql))){
            $msg = $mysqli->error;
            echo("<h3>Operation query failed: $msg.</h3>");
            echo("sql: $sql<br />");
            //exit();
        }
}		

//disply edit/input table
echo "<h1>Edit Table: {$table}</h1>"."\n";
echo '<table style="border:1px #000000 solid">'."\n";
	echo '<form  name="addUser" action="'.$_SERVER['SCRIPT_NAME'].'"  method="POST"  id="newUserForm">';
	echo '<tr>'."\n";
        
        echo '  <td style="text-align:right">First Name: </td>'."\n";
        echo '  <td>
                    <input type="text" 
                    name="pFirstName" 
                    required 
                    title="the user\'s first name"
                    maxlength="32"
                    size="10"
                    value = "'.$edFULLNAME.'">
                </td>'."\n";
	
        echo '<td style="text-align:right">Middle Initial: </td>'."\n";
        echo '  <td>
                    <input type="text" 
                    name="pMiddleInit" 
                    title="the user\'s middle initial"
                    maxlength="1"
                    size="1"
                    value = "'.$edMiddleInit.'">
                </td>'."\n";
	
        echo '<td style="text-align:right">Last Name: </td>'."\n";
        echo '  <td>
                    <input type="text" 
                    name="pLastName" 
                    required 
                    title="the user\'s last name"
                    maxlength="32"
                    size="10"
                    value = "'.$edLastName.'">
                </td>'."\n";
	echo '</tr>';
        echo '<tr>';
        echo '  <td style="text-align:right">Email: </td>'."\n";
        echo '  <td colspan="2">
                    <input type="email" 
                    name="pEmail" 
                    required 
                    title="the user\'s email address"
                    maxlength="32"
                    size="20"
                    value = "'.$edEmail.'">
                </td>'."\n";
	
        echo '  <td style="text-align:right" colspan="2">Phone:</td>'."\n";
        echo '  <td colspan="2">
                    <input type="tel" 
                    name="pPhone" 
                    required 
                    title="the user\'s phone numer (digits only"
                    pattern="^[0-9]{10}$"
                    size="20"
                    value = "'.$edPhone.'">
                </td>'."\n";
	
        echo '</tr>';
        echo '<tr>';
        echo '  <td style="text-align:right">Admin: </td>'."\n";
        echo '  <td>';
        echo '      <input type="radio" name="pAdmin" value="Y" '.(($edAdmin == "Y")?"checked":"").'>Y'; 
        echo '      <input type="radio" name="pAdmin" value="N" '.(($edAdmin == "N")?"checked":"").'>N';
        echo '  </td>'."\n"; 

        echo '  <td style="text-align:right">Owner: </td>'."\n";
        echo '  <td>';
        echo '      <input type="radio" name="pOwner" value="Y" '.(($edOwner == "Y")?"checked":"").'>Y'; 
        echo '      <input type="radio" name="pOwner" value="N" '.(($edOwner == "N")?"checked":"").'>N';
        echo '  </td>'."\n"; 

        echo '  <td style="text-align:right">Customer: </td>'."\n";
        echo '  <td>';
        echo '      <input type="radio" name="pCustomer" value="Y" '.(($edCustomer == "Y")?"checked":"").'>Y'; 
        echo '      <input type="radio" name="pCustomer" value="N" '.(($edCustomer == "N")?"checked":"").'>N';
        echo '  </td>'."\n"; 

        echo '  <td style="text-align:right">Employee: </td>'."\n";
        echo '  <td>';
        echo '      <input type="radio" name="pEmployee" value="Y" '.(($edEmployee == "Y")?"checked":"").'>Y'; 
        echo '      <input type="radio" name="pEmployee" value="N" '.(($edEmployee == "N")?"checked":"").'>N';
        echo '  </td>'."\n"; 

       	echo '</tr>'."\n";
        echo '<tr>'."\n";
        echo '  <td style="text-align:right">New Password (optional): </td>'."\n";
        echo '  <td>'."\n";
        echo '      <input type="password" 
                    name="pPW" 
                    title="new password only if you want to reset password"
                    size="10"
                    value="">';
	echo '  </td>'."\n";
	echo '  <td style="text-align:right">Repeat New Password: </td>'."\n";
        echo '  <td>'."\n";
        echo '      <input type="password" 
                    name="pPW2" 
                    title="repeat new password only if you want to reset password"
                    size="10"
                    value="">';
	echo '  </td>'."\n";
	echo '</tr>'."\n";
	echo "\n".'<input type="hidden" name="posted"      value="Y">';
	echo "\n".'<input type="hidden" name="pPWHash"     value="'.$edPWHash.'">';
	echo "\n".'<input type="hidden" name="pUserID"     value="'.$edUSERID.'">';
	if ($btnEditEmp == "Edit"){
		echo "\n".'<tr><td colspan = "8" align="center"><input type="button" value="Update" onclick="validate(this.form)" >';
	    echo "\n".'<input type="hidden" name="operation"     value="UPDATE">';
	}else{
		echo "\n".'<tr><td colspan = "8" align="center"><input type="button" value="Add User" onclick="validate(this.form)" >';
	    echo "\n".'<input type="hidden" name="operation"     value="INSERT">';
	}
	echo "\n".'</form>';
echo "\n".'</table><br /><br />';

// Get all table data
$sql = "SELECT * FROM $table
	ORDER BY LASTNAME";

//echo $sql;
if (!($results = $mysqli->query($sql))){
    die("Query to show fields from table failed: $sql");
}
/*
echo "<pre>";
print_r($results);
echo "</pre>";
*/

echo '<table style="border:1px #000000 solid" bgcolor="white">';
//$finfo = $results->fetch_fields();
//foreach ($finfo as $val) {
//	echo "<td>{$val->name}</td>";}
	
echo "<tr>\n";
echo '<th>Name</td>'."\n";
echo '<th>Email</td>'."\n";
echo '<th>Phone</td>'."\n";
echo '<th>Admin</td>'."\n";
echo '<th>Owner</td>'."\n";
echo '<th>Cust</td>'."\n";
echo '<th>Emp</td>'."\n";
echo "</tr>\n";

// printing table rows
while($row = $results->fetch_assoc())
{
    echo "<tr>";
        $name = $row['LASTNAME'].", ".$row['FIRSTNAME']." ".$row['MINIT'];
	echo "<tr>\n";
	echo '<td>'.$name.'</td>'."\n";
	echo '<td>'.$row['EMAILADD'].'</td>'."\n";
	echo '<td>'.$row['PHONE'].'</td>'."\n";
	echo '<td style="text-align:center">'.$row['ADMINFLAG'].'</td>'."\n";
	echo '<td style="text-align:center">'.$row['OWNERFLAG'].'</td>'."\n";
	echo '<td style="text-align:center">'.$row['CUSTFLAG'].'</td>'."\n";
	echo '<td style="text-align:center">'.$row['EMPFLAG'].'</td>'."\n";
	echo '<form action="'.$_SERVER['SCRIPT_NAME'].'" method="POST">'."\n";
	echo '<td><input type="submit" name="btnEditUser"      value="Edit"                 ></td>'."\n";
	echo '<td><input type="submit" name="btnDeleteUser"    value="Delete"               ></td>'."\n";
	echo '    <input type="hidden" name="posted"           value="Y"                    >'."\n";//
	echo '    <input type="hidden" name="edUserID"         value="'.$row['USERID'].'">'   ."\n";//
	echo '    <input type="hidden" name="edPWHash"         value="'.$row['PWHASH'].'">'   ."\n";//
	echo '    <input type="hidden" name="edFirstName"      value="'.$row['FIRSTNAME'].'">'."\n";//
	echo '    <input type="hidden" name="edMiddleInit"     value="'.$row['MINIT'].'"    >'."\n";//
	echo '    <input type="hidden" name="edLastName"       value="'.$row['LASTNAME'].'" >'."\n";//
	echo '    <input type="hidden" name="edEmail"          value="'.$row['EMAILADD'].'" >'."\n";//
	echo '    <input type="hidden" name="edPhone"          value="'.$row['PHONE'].'"    >'."\n";//
	echo '    <input type="hidden" name="edAdmin"          value="'.$row['ADMINFLAG'].'">'."\n";//
	echo '    <input type="hidden" name="edOwner"          value="'.$row['OWNERFLAG'].'">'."\n";//
	echo '    <input type="hidden" name="edCustomer"       value="'.$row['CUSTFLAG'].'" >'."\n";//
	echo '    <input type="hidden" name="edEmployee"       value="'.$row['EMPFLAG'].'"  >'."\n";//
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