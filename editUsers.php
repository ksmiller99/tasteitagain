<?php
//connect to database
require "mySQL.php";

//based on listing 23.4 in PHP book
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

echo "<pre>";
print_r($_SESSION);
print_r($_POST);
echo "</pre>";
*/

if($_SESSION['cpanel_admin'] != '1'){
	echo '<script type="text/javascript">'."\n"; 
	echo 'alert("Only cPanel administrators can use this page");'."\n";
	echo 'window.history.back();'."\n";
	echo '</script>'."\n";
	exit();
}

?>

<html><head>
<title>Edit Users</title>
</head>

<body>
<script type="text/javascript" >

function get_pw(f)
{
	f.op_ChangePW.value = "ChangePW";
	
	if (f.operation_id.value == "")
	{
		alert("Error - no user account selected.");
		return;
	}
	
	f.operation_pwd.value = prompt("Please enter the new password: ","");
			
	if (f.operation_pwd.value != prompt("Please re-enter the new password: ",""))
	{
		alert("Passwords do not match.");
		return;
	} 
	
	f.submit();	
}
</script>

<script type="text/javascript" >
function test_it(f)
{
        if (f.operation_first_name.value     == ""   || 
            f.operation_middle_initial.value == ""   || 
            f.operation_last_name.value      == ""   || 
            f.operation_email.value          == ""   || 
            f.operation_tel.value            == ""   || 
            f.operation_pwd.value            == "")
    {
       alert("You have to enter all new owner information and a password!");
       return;
    }

    alert("test_it");
        
    if (f.operation_pwd.value != f.operation_pwd2.value)
    {
       alert("Passwords do not match");
       return;
    }

    //changes to catch XSS and SQL injection
    if ((f.operation_first_name.value.indexOf("<") != -1)     || (f.operation_first_name.value.indexOf("'") != -1) || 
        (f.operation_middle_initial.value.indexOf("<") != -1) || (f.operation_middle_initial.value.indexOf("'") != -1) ||
        (f.operation_last_name.value.indexOf("<") != -1)      || (f.operation_last_name.value.indexOf("'") != -1) ||
        (f.operation_email.value.indexOf("<") != -1)          || (f.operation_email.value.indexOf("'") != -1) ||
        (f.operation_tel.value.indexOf("<") != -1)            || (f.operation_tel.value.indexOf("'") != -1) ||
        (f.operation_pwd.value.indexOf("<") != -1)            || (f.operation_pwd.value.indexOf("'") != -1))
    {
	alert("You have illegal characters in user ID or password!");
	return;
    }
				
   //alert(f.operation_id.value + ' ' + f.operation_pwd.value ) ; 
   f.submit();    
       
}

</script>

<?php
  
//operation variables are the operation being performed by cpanel admin
$op_NewOwner              = isset($_POST['op_NewOwner']) && ($_POST['op_NewOwner'] == "NewOwner") ? true : false;
$op_ChangePW              = isset($_POST['op_ChangePW']) && ($_POST['op_ChangePW'] == "ChangePW") ? true : false;
$op_OwnerOn               = isset($_POST['op_OwnerOn'])  && ($_POST['op_OwnerOn']  == "OwnerOn")  ? true : false;
$op_OwnerOff              = isset($_POST['op_OwnerOff']) && ($_POST['op_OwnerOff'] == "OwnerOff") ? true : false;
$op_Delete                = isset($_POST['op_Delete'])   && ($_POST['op_Delete']   == "Delete")   ? true : false;
$operation_id             = isset($_POST['operation_id'])             ? $_POST['operation_id']             : "";
$operation_first_name     = isset($_POST['operation_first_name'])     ? $_POST['operation_first_name']     : "";
$operation_middle_initial = isset($_POST['operation_middle_initial']) ? $_POST['operation_middle_initial'] : "";
$operation_last_name      = isset($_POST['operation_last_name'])      ? $_POST['operation_last_name']      : "";
$operation_email          = isset($_POST['operation_email'])          ? $_POST['operation_email']          : "";
$operation_tel            = isset($_POST['operation_tel'])            ? $_POST['operation_tel']            : "";
$operation_pwd            = isset($_POST['operation_pwd'])            ? $_POST['operation_pwd']            : "";
$operation_hash           = md5($operation_pwd);
$operation_admin          = empty($_POST['operation_admin']) ? 'FALSE' : $_POST['operation_admin'];
$table                    = "USERS";

if (!($_SESSION['cpanel_admin']=='1')){
	echo '<script type="text/javascript"> window.location.replace("home.php") </script>';
	exit;
}

$sql = "";

if ($op_ChangePW && $operation_id != "")
{
	$sql="UPDATE {$table} SET PWHASH = '{$operation_hash}' WHERE USERID = '{$operation_id}';";
}	

if ($op_OwnerOn && $operation_id != "")
{
	$sql="UPDATE {$table} SET OWNERFLAG = 'Y' WHERE USERID = '{$operation_id}';";
}	

if ($op_OwnerOff && $operation_id != "")
{
	$sql="UPDATE {$table} SET OWNERFLAG = 'N' WHERE USERID = '{$operation_id}';";
}	

if ($op_NewOwner && $operation_first_name     != "" && 
                              $operation_middle_initial != "" && 
                              $operation_last_name      != "" && 
                              $operation_email          != "" && 
                              $operation_tel            != "" && 
                              $operation_hash           != "" )
{
	$sql="INSERT INTO {$table} (EMAILADD, "
                                 . "PWHASH, "
                                 . "FIRSTNAME, "
                                 . "MINIT, "
                                 . "LASTNAME, "
                                 . "PHONE, "
                                 . "ADMINFLAG, "
                                 . "OWNERFLAG, "
                                 . "CUSTFLAG, "
                                 . "EMPFLAG)"
        .                  "VALUES ('{$operation_email}', "               
                                 . "'{$operation_hash}', "
                                 . "'{$operation_first_name}', "
                                 . "'{$operation_middle_initial}', "
                                 . "'{$operation_last_name}', "
                                 . "'{$operation_tel}', "
                                 . "'Y', "
                                 . "'Y', "
                                 . "'N', "
                                 . "'N');" ;
}

if ($sql != "")
{
    //echo "<br>attempting to run query $sql" . "<br>";
    if (!($results = $mysqli->query($sql))){
            echo("Operation query failed.");
            echo($sql);
            echo($results);
    }
}		

// sending query
$sql = "SELECT * from {$table}";
if (!($results = $mysqli->query($sql))){
    die("Query to show fields from table failed: $sql");
}
echo "<h1>Edit Table: {$table}</h1>";
echo 'You are logged in as: '.$_SESSION['valid_user'].'<br/>';
echo "\n".'<table>';
echo "\n".'<form  action="'.$_SERVER['SCRIPT_NAME'].'"  method="POST"  >';
echo "\n".'<tr><th>Owner Information</th></tr>';
echo "\n".'<tr><td>First Name:</td>   <td><input type="text"  name="operation_first_name"     value="" ></td>';
echo "\n".'    <td>Middle Inital:</td><td><input type="text"  name="operation_middle_initial" value="" ></td>';
echo "\n".'    <td>Last Name:</td>    <td><input type="text"  name="operation_last_name"      value="" ></td></tr>';
echo "\n".'<tr><td>Email: </td>       <td><input type="email" name="operation_email"          value="" ></td>';
echo "\n".'<td>Phone: </td>           <td><input type=tel     name="operation_tel"            value="" pattern="^[0-9]{10}$" ></td></tr>';
echo "\n".'<tr><td>Enter a password: </td><td><input type="password" name="operation_pwd" value=""></td>';
echo "\n".'<td>Re-enter password: </td><td><input type="password" name="operation_pwd2" value=""></td></tr>';

echo "\n".'<input type="hidden" name="op_NewOwner"     value="NewOwner">';
echo "\n".'<tr><td colspan = "2"><input type="button" value="Add Owner" onclick="test_it(this.form);" >';
echo "\n".'<input type="button" value="Homepage" onclick="location.href='."'home.php'".'" >';
echo "\n".'<input type="button" value="Logout" onclick="location.href='."'cPanelLogout.php'".'" >';
echo '</td></tr>';
echo "\n".'</form>';
echo "\n".'</table>';

echo "<table border='1'><tr>";
$finfo = $results->fetch_fields();
foreach ($finfo as $val) {
	echo "<th>{$val->name}</th>";
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
	echo '<td><input type="button" name="btnChangePW"   value="ChangePW" onclick="get_pw(this.form);"></td>'."\n";
	echo '<td><input type="submit" name="op_OwnerOn"    value="OwnerOn" ></td>'."\n";
	echo '<td><input type="submit" name="op_OwnerOff"   value="OwnerOff"></td>'."\n";
	echo '    <input type="hidden" name="op_ChangePW"   value=""             >'."\n";//the flag for changing password
	echo '    <input type="hidden" name="operation_id"  value="'.$row[0].'"  >'."\n";//the user id being operated on
	echo '    <input type="hidden" name="operation_pwd" value="FALSE"        >'."\n";//holds the new password, set in get_pw
	echo "</form>";
	echo "</tr>\n";
}
echo "</table>";

$results->close();
?>



</body></html>