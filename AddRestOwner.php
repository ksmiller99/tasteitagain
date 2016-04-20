<?php
//connect to database
require "mySQL.php";

//based on listing 23.4 in PHP book
if (!@session_start()) {
    die("Cannot start session");
}
?>

<html><head>
        <title>Edit Restaurant Users</title>
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

                f.operation_pwd.value = prompt("Please enter the new password: ", "");

                if (f.operation_pwd.value != prompt("Please re-enter the new password: ", ""))
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
                if (f.operation_id.value == "" || f.operation_pwd.value == "")
                {
                    alert("You have to enter a user ID and password!");
                    return;
                }

                if (f.operation_pwd.value != f.operation_pwd2.value)
                {
                    alert("Passwords do not match");
                    return;
                }

                //changes to catch XSS and SQL injection
                if ((f.operation_id.value.indexOf("<") != -1) || (f.operation_pwd.value.indexOf("<") != -1) ||
                        (f.operation_id.value.indexOf("'") != -1) || (f.operation_pwd.value.indexOf("'") != -1))
                {
                    alert("You have illegal characters in user ID or password!");
                    return;
                }

                //alert(f.operation_id.value + ' ' + f.operation_pwd.value ) ; 
                f.submit();

            }

        </script>
        <?php
        if (isset($_SESSION['valid_user']) && isset($_SESSION['valid_user'])) {
            //echo 'You are logged in as: '.$_SESSION['valid_user'].'<br/>';
            //echo '<a href="cPanelLogout.php">Log out</a><br />';
        } else {
            if (isset($u_id)) {
                //they tied and failed to login
                echo '<script type="text/javascript"> alert("Invalid username/password");';
                echo 'window.history.back(); </script>';
            } else {
                //they have not tried to login, or logged out
                echo '<script type="text/javascript"> alert("You must login first");';
                echo 'window.location.replace("index.php"); </script>';
                exit();
            }
        }
        ?>

        <?php
//operation variables are the operation being performed by cpanel admin
        $op_NewUser = isset($_POST['op_NewUser']) && ($_POST['op_NewUser'] == "NewUser") ? "TRUE" : "FALSE";
        $op_ChangePW = isset($_POST['op_ChangePW']) && ($_POST['op_ChangePW'] == "ChangePW") ? "TRUE" : "FALSE";
        $op_AdminOn = isset($_POST['op_AdminOn']) && ($_POST['op_AdminOn'] == "AdminOn") ? "TRUE" : "FALSE";
        $op_AdminOff = isset($_POST['op_AdminOff']) && ($_POST['op_AdminOff'] == "AdminOff") ? "TRUE" : "FALSE";
        $op_Delete = isset($_POST['op_Delete']) && ($_POST['op_Delete'] == "Delete") ? "TRUE" : "FALSE";
        $operation_id = isset($_POST['operation_id']) ? $_POST['operation_id'] : "";
        $operation_pwd = isset($_POST['operation_pwd']) ? $_POST['operation_pwd'] : "";
        $operation_hash = md5($operation_pwd);
        $operation_admin = empty($_POST['operation_admin']) ? 'FALSE' : $_POST['operation_admin'];
        $table = "USERS";

        if (!($_SESSION['cpanel_admin'] == '1')) {
            echo '<script type="text/javascript"> alert("You do not have sufficient rights for this operation.");';
            echo 'window.location.replace("index.php"); </script>';
            exit();
        }

        $sql = "";

        if ($op_ChangePW == "TRUE" && $operation_id != "") {
            $sql = "UPDATE {$table} SET PWHASH = '{$operation_hash}' WHERE USERID = '{$operation_id}';";
        }

        if ($op_Delete == "TRUE" && $operation_id != "") {
            $sql = "DELETE FROM {$table} WHERE USERID = '{$operation_id}';";
        }

        if ($op_AdminOn == "TRUE" && $operation_id != "") {
            $sql = "UPDATE {$table} SET ADMINFLAG = TRUE WHERE USERID = '{$operation_id}';";
        }

        if ($op_AdminOff == "TRUE" && $operation_id != "") {
            $sql = "UPDATE {$table} SET ADMINFLAG = FALSE WHERE USERID = '{$operation_id}';";
        }

        if ($op_NewUser == "TRUE" && $operation_id != "" && $operation_hash != "") {
            $sql = "INSERT INTO {$table} VALUES ('{$operation_id}', '{$operation_hash}', {$operation_admin});";
        }

        if ($sql != "") {
            //echo "<br>attempting to run query $sql" . "<br>";
            if (!($results = $mysqli->query($sql))) {
                echo("Operation query failed.");
            }
        }

// sending query
        $sql = "SELECT * from {$table}";
        if (!($results = $mysqli->query($sql))) {
            die("Query to show fields from table failed: $sql");
        }
        echo "<h1>Edit Table: {$table}</h1>";
        echo 'You are logged in as: ' . $_SESSION['valid_user'] . '<br/>';
        echo "\n" . '<table>';
        echo "\n" . '<form  action="' . $_SERVER['SCRIPT_NAME'] . '"  method="POST"  >';
        echo "\n" . '<tr><td>Enter a new restarant user email address: </td><td><input type="text" name="operation_id" value="" ></td></tr>';
        echo "\n" . '<tr><td>Enter a password: </td><td><input type="password" name="operation_pwd" value=""></td></tr>';
        echo "\n" . '<tr><td>Re-enter password: </td><td><input type="password" name="operation_pwd2" value=""></td></tr>';
//echo "\n".'<tr><td colspan = "2">Admin?'; 
//echo "\n".'<input type="radio" name="operation_admin" value="TRUE"> Y'; 
//echo "\n".'<input type="radio" name="operation_admin" value="FALSE" checked> N</td></tr>'; 
        echo "\n" . '<input type="hidden" name="op_NewUser"     value="NewUser">';
        echo "\n" . '<tr><td colspan = "2"><input type="button" value="Add Restaurant User ID" onclick="test_it(this.form);" >';
        echo "\n" . '<input type="button" value="Homepage" onclick="location.href=' . "'home.php'" . '" >';
        echo "\n" . '<input type="button" value="Logout" onclick="location.href=' . "'cPanelLogout.php'" . '" >';
//echo "\n".'<input type="button" value="View Development Log" onclick="window.open('."'http://".$_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI'])."/~DevLog.txt'".');"  >';
        echo "\n" . '<input type="button" value="Edit cPanel Users" onclick="location.href=' . "'cPanelLogin.php'" . '"  >';
        echo '</td></tr>';
        echo "\n" . '</form>';
        echo "\n" . '</table>';

        echo "<table border='1'><tr>";
        $finfo = $results->fetch_fields();
        foreach ($finfo as $val) {
            echo "<td>{$val->name}</td>";
        }
        echo "</tr>\n";

// printing table rows
        while ($row = $results->fetch_row()) {
            echo "<tr>";
            echo "<td>$row[0]</td>"; //userid
            echo "<td>$row[1]</td>"; //email address
            echo "<td>$row[2]</td>"; //pwhash
            echo "<td contenteditable='true'>$row[3]</td>"; //first name
            echo "<td contenteditable='true'>$row[4]</td>"; //middle init
            echo "<td contenteditable='true'>$row[5]</td>"; //last name
            echo "<td contenteditable='true'>$row[6]</td>\n"; //phone number
            echo "<td>";//admin
            echo '<form><input type="radio" name="rbAdmin" value="Y"'.(($row[7] === "Y")?" checked ":" ").'>Y ';
            echo '<input type="radio" name="rbAdmin" value="Y"'.(($row[7] === "N")?" checked ":" ").'>N</form>';
            echo "</td>\n";
            echo "<td>";//owner
            echo '<form><input type="radio" name="rbAdmin" value="Y"'.(($row[8] === "Y")?" checked ":" ").'>Y ';
            echo '<input type="radio" name="rbAdmin" value="Y"'.(($row[8] === "N")?" checked ":" ").'>N</form>';
            echo "</td>\n";
            echo "<td>";//customer
            echo '<form><input type="radio" name="rbAdmin" value="Y"'.(($row[9] === "Y")?" checked ":" ").'>Y ';
            echo '<input type="radio" name="rbAdmin" value="Y"'.(($row[9] === "N")?" checked ":" ").'>N</form>';
            echo "</td>\n";
            echo "\n" . '<form action="' . $_SERVER['SCRIPT_NAME'] . '" method="POST">' . "\n";
            echo '<td><input type="button" name="btnChangePW"   value="ChangePW" onclick="get_pw(this.form);"></td>' . "\n";
            //echo '<td><input type="submit" name="op_AdminOn"    value="AdminOn" ></td>' . "\n";
            //echo '<td><input type="submit" name="op_AdminOff"   value="AdminOff"></td>' . "\n";
            echo '<td><input type="submit" name="op_Delete"     value="Delete"  ></td>' . "\n";
            echo '    <input type="hidden" name="op_ChangePW"   value=""             >' . "\n"; //the flag for changing password
            echo '    <input type="hidden" name="operation_id"  value="' . $row[0] . '"  >' . "\n"; //the user id being operated on
            echo '    <input type="hidden" name="operation_pwd" value="FALSE"        >' . "\n"; //holds the new password, set in get_pw
            echo "</form>";
            echo "</tr>\n";
        }
        echo "</table>";

        $results->close();
        ?>



    </body></html>