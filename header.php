
<style type="text/css">
    .nav a:link {color:#04B404; text-decoration:none; font-family: arial black; font-weight: bold}
    .nav a:visited {color:#04B404; font-family: arial black; font-weight: bold}
    .nav a:hover {text-decoration:none; color:#000000; font-family: arial black; font-weight: bold}
    .nav a:active {font-family: arial black; font-weight: bold}

    .header a:link {color:#04B404; text-decoration:none;}
    .header a:visited {color:#04B404;}
    .header a:hover {text-decoration:underline;}
    .header a:active {}

    .vieworder a:link {color:#FFFF00; text-decoration:none; font-weight: bold;}
    .vieworder a:visited {color:#FFFF00;}
    .vieworder a:hover {text-decoration:none; color:#000000}
    .vieworder a:active {}

    .button_link a:link {text-decoration:none;}
    .button_link a:visited {}
    .button_link a:hover {text-decoration:none;}
    .button_link a:active {}

    .welcome {
        color: #FFFF00;
        font-family: verdana;
        font-size: 12px;
    }
</style>

<?php
$firstname  = (isset($_SESSION['firstname']))  ? $_SESSION['firstname'] : "Guest";
$isAdmin    = (isset($_SESSION['isAdmin']))    ? $_SESSION['isAdmin']   : "N";
$isOwner    = (isset($_SESSION['isOwner']))    ? $_SESSION['isOwner']   : "N";
$isCustomer = (isset($_SESSION['isCustomer'])) ? $_SESSION['isCustomer']: "N";
$email      = (isset($_SESSION['email']))      ? $_SESSION['email']     : "";
$cart       = (isset($_SESSION['cart']))       ? $_SESSION['cart']      : NULL;

//unset($_SESSION['userCatList']);
//get category list for users (Admin users have a real-time category list when administering categories and products)
if (!isset($_SESSION['userCatList'])) {
    $sql = "SELECT DISTINCT c.CATID, c.NAME
				FROM CATEGORIES c, PRODUCTS p
				WHERE c.CATID
				IN (
				SELECT p.CATID
				FROM PRODUCTS p
				WHERE 
				(p.CATID = c.CATID)
				AND 
				(p.HIDEFLAG =0))";
//	if ($result = @$mysqli->query("select CATID, NAME from CATEGORIES")){
    if ($result = @$mysqli->query($sql)) {
        $userCatList = array();
        while ($row = $result->fetch_assoc()) {
            $userCatList[intval($row['CATID'])] = $row['NAME'];
        }
        $_SESSION['userCatList'] = $userCatList;
    } else {
        echo '<script type="text/javascript"> alert("Error - cannot get category list: ' . $mysqli->error . '");</script>';
    }
} else {
    $userCatList = $_SESSION['userCatList'];
    /*
      echo "<pre>";
      print_r($userCatList);
      echo "</pre>"; */
}
?>
<link rel="stylesheet" href="styles.css" type="text/css">
<tr height="100px" bgcolor="#04B404">
    <td>
        <table cellspacing="0" cellpadding="0" align="center" border="0">
            <tr align="center">
                <td align="center">
            <center><font color="#FFFF00" face="arial black"><h1>T a s t e &nbsp; I t &nbsp; A g a i n</h1></font></center>
    </td>
</tr>
<tr>
    <td>
<center><font face="fantasy" color="#FFFF00">- F i n e &nbsp; J a m a i c a n &nbsp; D i n i n g -</font></center><br />
<!--							271 Glenwood Avenue Bloomfield, NJ 07003<br />
                                                        Phone: (973)743-5140
-->
</td>
</tr>
</table>
</td>
</tr>
<tr width="100%" height="30px" bgcolor="#FFFF00">
    <td colspan="2">
        <table cellspacing="0" cellpadding="0" width="900" border="0" bgcolor="#FFFF00">
            <tr>
            <div id='cssmenu'>
                <ul>
                    <li class='active '><a href='home.php'><span>HOME</span></a></li>
                    <li class='has-sub '><a href='menu.php?cat=000000'><span>MENU</span></a>
                        <ul>
                            <li><a href="menu.php?cat=000000">All Menu Items </a></li>
                            <?php
                            foreach ($userCatList as $key => $value) {
                                if ($value != "Merchandise") { //merchandise has its own menu
                                    echo '<li><a href="menu.php?cat=' . $key . '">' . $value . '</a></li>' . "\n";
                                }
                            }
                            ?>									
                        </ul>
                    </li>
                    <li><a href='merch.php'><span>MERCHANDISE</span></a></li>
                    <li><a href='info.php'><span>INFORMATION</span></a></li>
                </ul>
            </div>
</tr>
</table>
</td>
</tr>
<tr width="100%" height="25px" bgcolor="#40FF00">
    <td colspan="2">
        <table cellspacing="0" cellpadding="0" width="900" border="0" bgcolor="#04B404">
            <tr>
                <td>
                    <span class="welcome">
                        &nbsp; Welcome, <?php echo $firstname; ?>!    
                        <?php
                        if (count($cart) > 0) {
                            echo '<span class="welcome"> | </span><span class="vieworder"><a href="viewcart.php">View Order</a></span>';
                        }
                        ?>
                    </span>

                    <!--							
                    <?php
                    if ($isCustomer == 'Y') {
                        echo 'CUSTOMER = YES';
                    } else {
                        echo 'CUSTOMER = NO';
                    }
                    ?>
                    -->			

                </td>
            <form name="login" id="login" action="homelogin.php" method="POST">
                <td align="right">
                    <span class="welcome">
                        Email <input type="text" name="email" size="20">
                        Password <input type="password" name="password" size="20">
                    </span>
                    <?php
                    if (!($isCustomer == 'Y')) {
                        echo '<input type="submit" value="Login">';
                    } else {
                        echo '<span class="button_link"><a href="homelogout.php"><input type="button" value="Logout"></a></span>';
                    }
                    ?>	
                    <?php
                    if ($isCustomer == 'Y') {
                        echo '<span class="button_link"><a href="editAccount.php"><input type="button" value="Edit Account"></a></span>';
                    } else {
                        echo '<span class="button_link"><a href="register.php"><input type="button" value="Register"></a></span>';
                    }
                    ?>							
                </td>
            </form>
</tr>
</table>
</td>
</tr>
<?php
if (($isAdmin == 'Y')) {
    echo '<tr width="100%" height="25px" bgcolor="#40FF00">';
    echo '	<td colspan="2">';
    echo '		<table cellspacing="0" cellpadding="0" width="900" border="0" bgcolor="#04B404">';
    echo '			<tr>';
    echo '				<td align="left">';
    echo '					<span class="welcome">';
    echo '						&nbsp; <b>- Admin Menu -</b> &nbsp; <span class="button_link"><a href="categoryAdmin.php"><input type="button" value="Categories"></a><a href="productAdmin.php"><input type="button" value="Products"></a></span>';
    echo '					</span>';
    echo '				</td>';
    echo '			</tr>';
    echo '		</table>';
    echo '	</td>';
    echo '</tr>';
}
?>

<?php
if (($isOwner == 'Y')) {
    echo '<tr width="100%" height="25px" bgcolor="#40FF00">';
    echo '	<td colspan="2">';
    echo '		<table cellspacing="0" cellpadding="0" width="900" border="0" bgcolor="#04B404">';
    echo '			<tr>';
    echo '				<td align="left">';
    echo '					<span class="welcome">';
    echo '						&nbsp; <b>- Owner Menu -</b> &nbsp;<span class="button_link"><a href="vieworders.php"><input type="button" value="View Orders"></a></span>';
    echo '					</span>';
    echo '				</td>';
    echo '			</tr>';
    echo '		</table>';
    echo '	</td>';
    echo '</tr>';
}
?>