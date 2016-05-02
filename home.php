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
?>

<html>
    <head>
        <title>Taste it Again - Home</title>
        <?php //include("head.php");  ?>
    </head>

    <body leftmargin="0px" topmargin="0px" marginwidth="0px" marginheight="0px" bgcolor="black">

        <table cellspacing="0" cellpadding="0" width="900" align="center" border="0">

            <?php include("header.php"); ?>

            <tr height="500" bgcolor="#82FA58">
                <td colspan="2">
                    <!------------ Start of main content -------------------------------------------->			
                    <table cellspacing="0" cellpadding="0" width="900" border="0" height="100%">
                        <tr height="20">
                            <td>&nbsp;
                            </td>
                        </tr>
                        <tr valign="top">
                            <td align="center">
                                <img src="images/flag.png" border="0">
                                <!--https://www.travelindicator.com/images/flags/111.png-->
                            </td>
                            <td colspan="3">
                                Welcome to Taste It Again!  Here, you can experience the fine cuisine of Jamaica, without actually being there.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="images/food1.jpg" border="0" height="300" width="300">
                                <!--https://tanlinesresorts.files.wordpress.com/2011/09/jamaica-jerk-chicken-2.jpg?w=660-->
                            </td>
                            <td>
                                <img src="images/food2.jpg" border="0" height="300" width="300">
                                <!--http://cdn1.tmbi.com/TOH/Images/Photos/37/300x300/exps29826_TH950745D33C.jpg-->
                            </td>
                            <td>
                                <img src="images/food3.jpg" border="0" height="300" width="300">
                                <!--http://cdn-image.myrecipes.com/sites/default/files/styles/300x300/public/image/recipes/ck/03/07/pork-salad-ck-671029-x.jpg?itok=kRjLNhFi-->
                            </td>
                        </tr>
                    </table>
                    <!------------- End of added content -------------------------------------------->						
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
