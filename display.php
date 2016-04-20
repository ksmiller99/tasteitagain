<!doctype html public "-//w3c//dtd html 3.2//en">
<html>
    <head>
        <title>(Type a title for your page here)</title>
        <link rel="stylesheet" href="style.css" type="text/css">

        <script language="javascript" src="ajax.js"></script>
        <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
        
        <script language="JavaScript">
            function edit_field(multi) {

                var row = multi[0];
                var enums = multi[1];

                var sid = 's' + row[0];

                for (var key in row) {
                    console.log(' name=' + key + ' value=' + row[key]);

                    //recreate the div id created by the PHP
                    var v_id = key + '_' + row[0];

                    //skip the key/value pairs where the key is an integer
                    try {
                        var val = document.getElementById(v_id).innerHTML.trim(); // Read the present value
                    } catch (err) {
                        continue;
                    }

                    //get the ID
                    var id_v = 'id_' + key + "_" + row[0];

                    //change the display box to an input box (or radio group) and replace the value
                    if (enums[key].length === 0)
                        document.getElementById(v_id).innerHTML = "<input type=text id='" + id_v + "' value='" + val + "' size=" + val.length + ">"; // Display text input 
                    else {
                        var innerHtml = "";
                        enums[key].forEach(function (item) {
                            innerHtml += "<input type=radio name='" + id_v + "'  id='" + id_v + "_" + item + "' value='" + item + "' " + ((item === val) ? "checked" : "") + ">" + item + " ";
                        });
                        document.getElementById(v_id).innerHTML = innerHtml;
                    }
                }

                document.getElementById(sid).innerHTML = '<input type=button value=Update onclick=ajax("' + row[0] + '");>'; // Add different color to background
            } // end of function

        </script>
    </head>

    <body>
        <?Php

        function getSQLEnumArray($table, $field, $db) {
            //from http://stackoverflow.com/questions/10825010/how-to-populate-select-with-enum-values
            $sql = "SHOW COLUMNS FROM " . $table . " LIKE '" . $field . "'";
            $res = $db->query($sql);
            $row = $res->fetch(PDO::FETCH_ASSOC);
            preg_match_all("/'(.*?)'/", $row['Type'], $categories);
            $fields = $categories[1];
            return $fields;
        }

        $table = "USERS";
        require "config.php"; // MySQL connection string

        $enum_r = array();

        echo "<div id=\"msgDsp\" STYLE=\"position: absolute; right: 0px; top: 10px;left:800px;text-align:left; FONT-SIZE: 12px;font-family: Verdana;border-style: solid;border-width: 1px;border-color:white;padding:0px;height:20px;width:250px;top:10px;z-index:1\"> Edit mark </div>";

        //get the column names and display table header
        $result = $dbo->query("SELECT * from " . $table . " LIMIT 1");
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        $cols = empty($rows) ? array() : array_keys($rows[0]);
        echo "<br><br><br><table class='t1' width='1000'><tr>";
        foreach (array_slice($cols, 1) as $attr) {
            echo"<th>$attr</th>";
            $enum_r[$attr] = getSQLEnumArray($table, $attr, $dbo);
        }
        echo"<th></th></tr>";
        $cols_json = json_encode($cols);

        $count = "SELECT * FROM users LIMIT 10";

        $i = 1;

        //display each row
        foreach ($dbo->query($count) as $row) {
            $m = $i % 2; // To manage row style using css file. 

            $sid = 's' . $row[0];
            echo "<tr class='r$m' height=50>";
            $first_col = TRUE;

            //give each cell a unique ID so DOM can manage
            foreach ($cols as $attr) {

                //skip the first column (primary key)
                if ($first_col) {
                    $first_col = FALSE;
                    continue;
                }

                //create an id for the div that DOM can manage
                $div_id = $attr . '_' . $row[0];
                echo "<td><div id=$div_id >$row[$attr] </div> </td>\n";
            }

            $multi_json = json_encode(array($row, $enum_r));
            $hid = 'h'.$row[0];
            $hid_hidden = $hid."_hidden";
            echo "<td> <div id=$sid><input type=button value='Edit' onclick=edit_field($multi_json)></div>\n";
            echo "     <div id=$hid><input type=hidden id='$hid_hidden' value=$multi_json></div>\n";
            echo"</td></tr>";        
            $i = $i + 1;  // To manage row style
        }
        echo "</table>";
        ?>
        <br><br><br>

    </body>
</html>
