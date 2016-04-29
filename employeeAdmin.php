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
     
     //alert("valid") ; 
     
    //changes to catch XSS and SQL injection
    if ((f.pADDLINE1.value.indexOf("'") != -1)    || (f.pADDLINE1.value.indexOf("<") != -1) ||
	(f.pADDLINE2.value.indexOf("'") != -1)    || (f.pADDLINE2.value.indexOf("<") != -1) ||
        (f.pCITY.value.indexOf("'") != -1)        || (f.pCITY.value.indexOf("<") != -1)     ||
        (f.pSTATE.value.indexOf("'") != -1)       || (f.pSTATE.value.indexOf("<") != -1)    ||
        (f.pZIP.value.indexOf("'") != -1)         || (f.pZIP.value.indexOf("<") != -1)      ||
        (f.pWAGE_AMOUNT.value.indexOf("'") != -1) || (f.pWAGE_AMOUNT.value.indexOf("<") != -1))
    {
	alert("You have illegal characters in one or more fields!");
	return;
    }
    
    //alert("submitting") ; 
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
$operation = (isset($_POST['operation'])) ? $_POST['operation'] : '' ;   //SQL Operation UPDATE or INSERT
$updateFlag = (isset($_POST['UPDATE'])) ? $_POST['UPDATE'] : '';
if ($updateFlag == "UPDATE")
	$operation = "UPDATE";

//user-input fields for new record
$pUSERID      = isset($_POST['pUSERID'] )     ? $_POST['pUSERID']      : '';
$pFULLNAME    = isset($_POST['pFULLNAME'])    ? $_POST['pFULLNAME']    : '';
$pADDLINE1    = isset($_POST['pADDLINE1'])    ? $_POST['pADDLINE1']    : ''; 
$pADDLINE2    = isset($_POST['pADDLINE2'])    ? $_POST['pADDLINE2']    : '';
$pCITY        = isset($_POST['pCITY'])        ? $_POST['pCITY']        : '';
$pSTATE       = isset($_POST['pSTATE'])       ? $_POST['pSTATE']       : '';
$pZIP         = isset($_POST['pZIP'])         ? $_POST['pZIP']         : '';
$pWAGE_TYPE   = isset($_POST['pWAGE_TYPE'])   ? $_POST['pWAGE_TYPE']   : '';
$pWAGE_AMOUNT = isset($_POST['pWAGE_AMOUNT']) ? $_POST['pWAGE_AMOUNT'] : '';
$pEMAILFLAG   = isset($_POST['pEMAILFLAG'])   ? $_POST['pEMAILFLAG']   : '';
$pTXTMSGFLAG  = isset($_POST['pTXTMSGFLAG'])  ? $_POST['pTXTMSGFLAG']  : '';

//default values when user selected Edit
$edUSERID      = isset($_POST['edUSERID'] )     ? $_POST['edUSERID']      : '';
$edFULLNAME    = isset($_POST['edFULLNAME'])    ? $_POST['edFULLNAME']    : '';
$edADDLINE1    = isset($_POST['edADDLINE1'])    ? $_POST['edADDLINE1']    : ''; 
$edADDLINE2    = isset($_POST['edADDLINE2'])    ? $_POST['edADDLINE2']    : '';
$edCITY        = isset($_POST['edCITY'])        ? $_POST['edCITY']        : '';
$edSTATE       = isset($_POST['edSTATE'])       ? $_POST['edSTATE']       : '';
$edZIP         = isset($_POST['edZIP'])         ? $_POST['edZIP']         : '';
$edWAGE_TYPE   = isset($_POST['edWAGE_TYPE'])   ? $_POST['edWAGE_TYPE']   : '';
$edWAGE_AMOUNT = isset($_POST['edWAGE_AMOUNT']) ? $_POST['edWAGE_AMOUNT'] : '';
$edEMAILFLAG   = isset($_POST['edEMAILFLAG'])   ? $_POST['edEMAILFLAG']   : '';
$edTXTMSGFLAG  = isset($_POST['edTXTMSGFLAG'])  ? $_POST['edTXTMSGFLAG']  : '';
$edCOUNT       = isset($_POST['edCOUNT'])       ? $_POST['edCOUNT']       : '';

//buttons from each row
$btnEditEmp   = isset($_POST['btnEditEmp']  ) ? $_POST['btnEditEmp']    : '';
$btnInsertEmp = isset($_POST['btnInsertEmp']) ? $_POST['btnInsertEmp']  : '';

$posted = isset($_POST['posted']) ? $_POST['posted']  : '';       //user submitted a form
$table        = "EMPLOYEES";

//perform user selected operation
if ($posted == "Y"){
    if (($operation == "UPDATE")  && ($pUSERID != ""))
    {
        $sql="UPDATE {$table} SET ADDLINE1      = '{$pADDLINE1}',
                                  ADDLINE2      = '{$pADDLINE2}',
                                  CITY          = '{$pCITY}',
                                  STATE         = '{$pSTATE}', 
                                  ZIP           = '{$pZIP}',
                                  WAGE_TYPE     = '{$pWAGE_TYPE}',
                                  WAGE_AMOUNT   = '{$pWAGE_AMOUNT}',
                                  EMAILFLAG     = '{$pEMAILFLAG}',
                                  TXTMSGFLAG    = '{$pTXTMSGFLAG}'
                                  WHERE USERID  = '{$pUSERID}';";
    }else{	
        if ($operation == "INSERT"){
            $sql="INSERT INTO {$table} (`USERID`  ,`ADDLINE1`  ,`ADDLINE2`  ,`CITY`  ,`STATE`  ,`ZIP`  ,`WAGE_TYPE`  ,`WAGE_AMOUNT`  ,`EMAILFLAG`  ,`TXTMSGFLAG`) 
                                VALUES ('$pUSERID','$pADDLINE1','$pADDLINE2','$pCITY','$pSTATE','$pZIP','$pWAGE_TYPE','$pWAGE_AMOUNT','$pEMAILFLAG','$pTXTMSGFLAG');" ;
        }else{
            if($btnEditEmp == "Edit"){ //user selected edit - no operation
            }else
                echo "<h3>Error - Operation or values are incorrect. </h3>";
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
        echo "  <td style='text-align:right' >Name: </td><td>$edFULLNAME</td>\n";
        echo '</tr>';
        echo '<tr>';
        echo '  <td style="text-align:right">Address: </td>'."\n";
        echo '  <td colspan="2">
                    <input type="text" 
                    name="pADDLINE1" 
                    required 
                    title="the employees\'s address line 1"
                    maxlength="32"
                    size="20"
                    value = "'.$edADDLINE1.'">
                </td>'."\n";
	echo '</tr>';
        echo '<tr>';
        
        echo '  <td style="text-align:right">Address 2: </td>'."\n";
        echo '  <td colspan="2">
                    <input type="text" 
                    name="pADDLINE2" 
                    title="the employees\'s address line 2"
                    maxlength="32"
                    size="20"
                    value = "'.$edADDLINE2.'">
                </td>'."\n";
	echo '</tr>';
        echo '<tr>';
        
        echo '  <td style="text-align:right">City: </td>'."\n";
        echo '  <td>';
        echo '      <input type="text" 
                    name="pCITY" 
                    required
                    title="the employees\'s city"
                    maxlength="32"
                    size="20"
                    value="'.$edCITY.'">
                </td>'."\n"; 
        
        echo '  <td style="text-align:right">St: </td>'."\n";
        echo '  <td>';
        echo '      <input type="text" 
                    name="pSTATE" 
                    required
                    title="the employees\'s state"
                    maxlength="2"
                    size="2"
                    value="'.$edSTATE.'">
                </td>'."\n"; 

        echo '  <td style="text-align:right">Zip: </td>'."\n";
        echo '  <td>';
        echo '      <input type="text" 
                    name="pZIP" 
                    required
                    title="the employees\'s zip code"
                    maxlength="10"
                    size="10"
                    value="'.$edZIP.'"> 
                </td>'."\n"; 
        echo '</tr>';
        
        echo '<tr>';
        echo '  <td style="text-align:right">Type: </td>'."\n";
        echo '  <td>';
        echo '      <input type="radio" name="pWAGE_TYPE" value="hourly" '.(($edWAGE_TYPE == "hourly")?"checked":"").'>hourly'; 
        echo '      <input type="radio" name="pWAGE_TYPE" value="weekly" '.(($edWAGE_TYPE == "weekly")?"checked":"").'>weekly';
        echo '  </td>'."\n"; 
        
        echo '  <td colspan="2" style="text-align:right">Rate: $</td>'."\n";
        echo '  <td colspan="2">';
        echo '      <input type="number" 
                    name="pWAGE_AMOUNT" 
                    required
                    title="the employees\'s wage rate"
                    pattern = "/^\d+(?:\.\d{0,2})$/"
                    step=".01"
                    maxlength="4"
                    size="4"
                    value="'.$edWAGE_AMOUNT.'"> 
                </td>'."\n"; 
        echo '</tr>';
        
        echo '<tr>';
        echo '  <td style="text-align:right">Email Msg: </td>'."\n";
        echo '  <td>';
        echo '      <input type="checkbox" name="pEMAILFLAG" value="1" '.(($edEMAILFLAG == "1")?"checked":"").'>'; 
        echo '  </td>'."\n"; 

        echo '  <td style="text-align:right">Text Msg: </td>'."\n";
        echo '  <td>';
        echo '      <input type="checkbox" name="pTXTMSGFLAG" value="1" '.(($edTXTMSGFLAG == "1")?"checked":"").'>'; 
        echo '  </td>'."\n"; 
	echo '</tr>'."\n";
	echo "\n".'<input type="hidden" name="posted"      value="Y">';
	echo "\n".'<input type="hidden" name="pUSERID"     value="'.$edUSERID.'">';
	if (($btnEditEmp == "Edit") && ($edCOUNT > 0)){
            echo "\n".'<tr><td colspan = "8" align="center"><input type="button" value="Update Employee Info" onclick="validate(this.form)" >';
	    echo "\n".'<input type="hidden" name="operation"     value="UPDATE">';
	}elseif(($btnEditEmp == "Edit") && ($edCOUNT == 0)){
            echo "\n".'<tr><td colspan = "8" align="center"><input type="button" value="Add Employee Info" onclick="validate(this.form)" >';
	    echo "\n".'<input type="hidden" name="operation"     value="INSERT">';
	}
	echo "\n".'</form>';
echo "\n".'</table><br /><br />';

// Get all table data
$sql = "SELECT u.USERID as 'u.USERID', CONCAT( u.LASTNAME,  ', ', u.FIRSTNAME,  ' ', u.MINIT ) AS  'FULLNAME', e. * , COUNT( e.USERID ) AS  'COUNT'
            FROM USERS AS u
            LEFT JOIN $table AS e ON ( u.USERID = e.USERID ) 
            WHERE (u.EMPFLAG =  'Y')
            GROUP BY u.USERID";

//echo $sql;
if (!($results = $mysqli->query($sql))){
    die("Query to show fields from table failed: $sql");
}

//echo "<pre>";
//print_r($results);
//while($row = $results->fetch_assoc()){
//    print_r($row);
//}
//echo "</pre>";

echo '<table style="border:1px #000000 solid" bgcolor="white">';
//$finfo = $results->fetch_fields();
//foreach ($finfo as $val) {
//	echo "<td>{$val->name}</td>";}

echo "<tr>\n";
echo '<th>Name</td>'."\n";
echo '<th>Address 1</td>'."\n";
echo '<th>Address 2</td>'."\n";
echo '<th>City</td>'."\n";
echo '<th>State</td>'."\n";
echo '<th>Zip</td>'."\n";
echo '<th>Type</td>'."\n";
echo '<th>Rate</td>'."\n";
echo '<th>Email</td>'."\n";
echo '<th>Txt</td>'."\n";
echo "</tr>\n";

// printing table rows
while($row = $results->fetch_assoc())
{
    echo "<tr>";
        echo "<tr>\n";
	echo '<td>'.$row['FULLNAME'].'</td>'."\n";
	echo '<td>'.$row['ADDLINE1'].'</td>'."\n";
	echo '<td>'.$row['ADDLINE2'].'</td>'."\n";
	echo '<td>'.$row['CITY'].'</td>'."\n";
	echo '<td>'.$row['STATE'].'</td>'."\n";
	echo '<td>'.$row['ZIP'].'</td>'."\n";
	echo '<td>'.$row['WAGE_TYPE'].'</td>'."\n";
	echo '<td>'.$row['WAGE_AMOUNT'].'</td>'."\n";
	echo '<td><input type="checkbox" name="edEMAILFLAG" disabled value="1" '.(($row['EMAILFLAG'] == "1")?"checked":"").'></td>';
	echo '<td><input type="checkbox" name="edEMAILFLAG" disabled value="1" '.(($row['TXTMSGFLAG'] == "1")?"checked":"").'></td>';
	echo '<form action="'.$_SERVER['SCRIPT_NAME'].'" method="POST">'."\n";
        echo '<td><input type="submit" name="btnEditEmp"    value="Edit"               ></td>'."\n";
	echo '    <input type="hidden" name="posted"        value="Y"                       >'."\n";//
	echo '    <input type="hidden" name="edUSERID"      value="'.$row['u.USERID']   .'" >'."\n";//
	echo '    <input type="hidden" name="edFULLNAME"    value="'.$row['FULLNAME']   .'" >'."\n";//
	echo '    <input type="hidden" name="edADDLINE1"    value="'.$row['ADDLINE1']   .'" >'."\n";//
	echo '    <input type="hidden" name="edADDLINE2"    value="'.$row['ADDLINE2']   .'" >'."\n";//
	echo '    <input type="hidden" name="edCITY"        value="'.$row['CITY']       .'" >'."\n";//
	echo '    <input type="hidden" name="edSTATE"       value="'.$row['STATE']      .'" >'."\n";//
	echo '    <input type="hidden" name="edZIP"         value="'.$row['ZIP']        .'" >'."\n";//
	echo '    <input type="hidden" name="edWAGE_TYPE"   value="'.$row['WAGE_TYPE']  .'" >'."\n";//
	echo '    <input type="hidden" name="edWAGE_AMOUNT" value="'.$row['WAGE_AMOUNT'].'" >'."\n";//
	echo '    <input type="hidden" name="edEMAILFLAG"   value="'.$row['EMAILFLAG']  .'" >'."\n";//
	echo '    <input type="hidden" name="edTXTMSGFLAG"  value="'.$row['TXTMSGFLAG'] .'" >'."\n";//
	echo '    <input type="hidden" name="edCOUNT"       value="'.$row['COUNT']      .'" >'."\n";//
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