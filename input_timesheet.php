<?php
//connect to database
require "mySQL.php";

if (!@session_start()) {
    die("Cannot start session");
}

if (!isset($_SESSION['valid_user'])) {
    echo '<script type="text/javascript"> alert("You must login.");';
    echo 'window.location.replace("index.php"); </script>';
    exit();
}

if ($_SESSION['isEmployee'] != 'Y') {
    echo '<script type="text/javascript">' . "\n";
    echo 'alert("Only employees can use this page");' . "\n";
    echo 'window.history.back();' . "\n";
    echo '</script>' . "\n";
    exit();
}
?>
<html>
    <head>
        <title>Taste It Again - Input Timesheet</title>
        <?php //include("head.php");  ?>
        <script type="text/javascript" >
        function validate(f){
            if(!f.checkValidity()){
                alert("Invalid data - Please check all fields");
                return;
            }
            
            var punch_in = new Date(f.punch_in.value.toString().replace(" ","T"));
            var punch_out = new Date(f.punch_out.value.toString().replace(" ","T"));
            if (!(punch_out > punch_in)){
                alert("Punch-out must be after punch in");
                return;
            }
            
            alert('submit');
            f.submit();    
        }

        </script>
    </head>

    <body leftmargin="0px" topmargin="0px" marginwidth="0px" marginheight="0px" bgcolor="black">
        <table cellspacing="0" cellpadding="0" width="900" align="center" border="0">
            <?php include("header.php"); ?>
            <tr height="500" bgcolor="#82FA58">
                <td colspan="2">
                    <table cellspacing="0" cellpadding="0" width="900" border="0" height="100%">
                        <tr valign="top">
                            <td><center><h1>Input Timesheet</h1><center>
                                <!-- CONTENT -->						
 <!-- Start ---------------------------------------------------------------------------------------------------------->							

<?php
$userID = $_SESSION['userid'];
/*
echo "<pre>\n";   
print_r($_SESSION);
echo "</pre>\n";
 */

$sql = "SELECT CONCAT(LASTNAME,', ',FIRSTNAME, ' ',MINIT) as 'FullName' FROM USERS WHERE (USERID = '$userID')";
if ($sql != "")
{
    //echo "<br>attempting to run query $sql" . "<br>";
    if (!($results = $mysqli->query($sql))){
            echo("Operation query failed.<br />");
            echo("sql: $sql<br />");
            die();
    }
    
    if($results->num_rows != 0){
        $row = $results->fetch_assoc();
        /*
        echo '<pre>';
        print_r($row);
        echo '</pre>';
         */
        $FullName = $row['FullName'];    
    }
    
    echo '<form  action="'.$_SERVER['SCRIPT_NAME'].'"  method="POST"  >';
    echo '<table style="border:1px #000000 solid" bgcolor="white">';
    echo '    <tr>';
    echo '        <td style="text-align:right">Name:</td>';
    echo '        <td colspan="3">'.$FullName.'</td>';
    echo '    </tr>';
    echo '    <tr>';
    echo '        <td style="text-align:right">In:</td>';
    echo '        <td> <input type="text" ';
    echo '                    required ';
    echo '                    pattern="(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})"';
    echo '                    name="punch_in" ';
    echo '                    title="punch-in date/time in YYYY-MM-DD HH:MM format"';
    echo '                    value="2016-04-28 08:00">';
    echo '        <td style="text-align:right">Out:</td>';
    echo '        <td> <input type="text" ';
    echo '                    required ';
    echo '                    pattern="(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})"';
    echo '                    name="punch_out" ';
    echo '                    title="punch-out date/time in YYYY-MM-DD HH:MM format"';
    echo '                    value="2016-04-28 08:00">';
    echo '    </tr>';
    echo '    <tr>                                                                  '; 
    echo '        <td colspan="4" align="center"><input type="button" value="Add Timesheet" onclick="validate(this.form)"></td>';
    echo '    </tr>';
    echo '</table>';
    echo '</form>   ';
    
}

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
