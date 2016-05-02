<?php
//connect to database
require "mySQL.php";

if (!@session_start()){
	die("Cannot start session");
}

if (!isset($_SESSION['valid_user'])){
	echo '<script type="text/javascript"> alert("You must login.");';
	echo 'window.location.replace("index.php"); </script>';
	exit();
}

/*
//debug messages
echo "<pre>";	
echo "SESSION: <br />";
print_r($_SESSION);
echo "</pre>";
*/

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : NULL;
/*
//debug messages
echo "<pre>";	
echo "session cart: <br />";
print_r($cart);
echo "</pre>";
*/

//check if cart exists or is empty
//check cart before re-directing
if (count($cart) == 0){
	echo '<script type="text/javascript"> ';
	echo 'alert("Your cart is empty.");';
	echo '</script>';
}

//get all products where $cart[key] = PRODID
$plist = "";
foreach($cart as $key => $value){
	$plist = $plist."'".$key."', ";
}
//remove last comma
$plist = substr($plist,0,strlen($plist)-2);
$sql = "select * from PRODUCTS where PRODID in (".$plist.") order by CATID";
//echo $sql."<br />";
if (!($menuResults = $mysqli->query($sql))){
	die("Error - cannot get menu.\n$sql");
}

//get current list of all categories - created in header.php
$userCatList = $_SESSION['userCatList'];

/*
//debug messages
echo "<pre>";	
echo "POST: <br/>";
print_r($_POST);
echo "</pre>";
*/

//update cart
foreach($_POST as $key => $value){
	$arr = explode("-",$key);
	if($arr[0] == "key"){
		//if qty = 0, delete from cart; otherwise add/update
		if($value == 0){
			unset($cart[$arr[1]]);
		}else
			$cart[$arr[1]] = $value;
	}
}	
$_SESSION['cart'] = $cart;

/*
//debug messages
echo "<pre>";	
echo "updated cart: <br />";
print_r($cart);
echo "</pre>";
*/

//check which, if any buttons were clicked
//$btnCreateUpdate = $_POST['btnCreateUpdate'];
//$btnCheckOut = $_POST['btnCheckOut'];


if(count($cart)==0)
	$cartStatusMsg = "Order NOT created - you have 0 items in cart";
else{
	$itemCount = 0;
	foreach($cart as $key => $value){
		$itemCount += $value;
	}
		
	$cartStatusMsg = "You have $itemCount items in your cart."; 
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

<script type="text/javascript" >
function viewCart(f){
	alert("viewing cart");
	f.action="viewcart.php";
	alert (f.action);
	f.submit();
}
</script>

<html>
<head>
<?php
if (isset($_POST['btnCheckOut'])){
    if((!isset($_SESSION['isCustomer']))||($_SESSION['isCustomer'] != 'Y')){
        echo '<script type="text/javascript"> ';
        echo 'alert("You must login or create an account before you can checkout.");';
        echo '</script>';
    }else{
        //if "Check Out" button was pressed and cart is not empty, redirect to checkout.php after updating cart
        echo '<meta HTTP-EQUIV="REFRESH" content="0; url=checkout.php">'."\n";
    }
}
?>

	<title>Taste It Again - View Order</title>
</head>
<body leftmargin="0px" topmargin="0px" marginwidth="0px" marginheight="0px" bgcolor="black">
	<table cellspacing="0" cellpadding="0" width="900" align="center" border="0">

		<?php include("header.php"); ?>

		<tr height="500" bgcolor="#82FA58">
			<td colspan="2">
				<table cellspacing="0" cellpadding="0" width="900" border="0" height="100%">
					<tr valign="top">
						<td>
							<form name="menu" action="<?php echo $_SERVER['SCRIPT_NAME'];?>" id="menu" method = "POST">
							<table cellspacing="0" cellpadding="0" width="900" border="0">
								<tr>
								<?php 
									echo "<tr><td>$cartStatusMsg</td></tr>\n";
								?>
									<td>
										<center><h1>ORDER</h1><center>
									</td>
								</tr>
								<?php 
									$currentCatID =0;
									$bgColor = 'white';
									
									//print category header
									echo '<tr>'."\n";
									echo '	<td>'."\n";
									echo ' 		<table cellspacing="1" cellpadding="1" style="border:1px #000000 solid" bgcolor="'.$bgColor.'" align="center" width="800">'."\n";
									echo' 			<tr>'."\n";
									//echo' 				<td width="150">'."\n";
									echo' 				<td>'."\n";
									echo' 					<b><h3>Category</h3></b>'."\n";
									echo' 				</td>'."\n";
									//echo' 				<td width="100">'."\n";
									echo' 				<td>'."\n";
									echo' 					<b><h3>Product</h3></b>'."\n";
									echo' 				</td>'."\n";
									//echo' 				<td width="75">'."\n";
									echo' 				<td>'."\n";
									echo' 					<center><b><h3>Price</h3></b></center>'."\n";
									echo' 				</td>'."\n";
									//echo' 				<td width="150">'."\n";
									echo' 				<td>'."\n";
									echo' 					<center><b><h3>Quantity</h3></b></center>'."\n";
									echo' 				</td>'."\n";
									echo' 				<td>'."\n";
									echo' 					<center><b><h3>Ext Price</h3></b></center>'."\n";
									echo' 				</td>'."\n";
									echo' 			</tr>'."\n";
											
									if($product = $menuResults->fetch_assoc()){ //there is at least one record
										$total = 0;
										while (true){ //break when no more records
											$currentCatID = intval($product['CATID']);
											$currentCatName = $userCatList[$currentCatID];
										
											//output new category header
											//echo "<tr>"."\n";
											//echo "	<td>"."\n";
											//echo "		<h2>$currentCatName</h2>\n";
											//echo "	</td>"."\n";
											//echo "</tr>"."\n";
											
											//alternate background colors
											if ($bgColor != '#BDBDBD'){
												$bgColor = '#BDBDBD';
											}else{
												$bgColor = 'white';
											}
											
											//category header was here

											while (true){//break when caetgory changes or no more records
												//print record
												echo'<tr>'."\n";
												echo'	<td>'."\n";
												echo'<b>'.$currentCatName.'</b>'."\n";
												echo'	</td>'."\n";
												echo'	<td>'."\n";
												echo'<span title="'.$product['DESCRIPTION'].'">'."\n";
												echo'<b>'.$product['NAME'].'</b>'."\n";
												echo'	</td>'."\n";
												echo'	<td>'."\n";
												echo'		<center>$'.number_format($product['PRICE'],2).'</center>'."\n";
												echo'	</td>'."\n";
												echo'	<td>'."\n";
												$pKey = 'key-'.$product['PRODID'];
                                                                                                if(!isset($cart[$product['PRODID']])){
                                                                                                    $pQty = 0;
                                                                                                }else{
                                                                                                    $pQty = intval($cart[$product['PRODID']]);
                                                                                                }
												echo'		<center>
															<input type="text"  
															name="'.$pKey.'"  
															size="2"  
															value="'.$pQty.'">
															</center>'."\n";
												echo'	</td>'."\n";
												echo'	<td align="right">'."\n";
												$extprice = $pQty*$product['PRICE'];
												$total += $extprice;
												echo'$'. number_format($extprice,2);
												echo'	</td>'."\n";
												echo'</tr>'."\n";
											
												//get next record
												if (!($product = $menuResults->fetch_assoc())){
													//no more records
													break;
												}	
												
												//check if category changes
												if (intval($product["CATID"]) != $currentCatID){
													break;
												}//end if
											} //end while same category
											
											if (!($product)){
												//no more records
												break;
											}
										}//end while records exist
										
										//print end of products table
										echo'			<tr>
															<td></td>
															<td></td>
															<td></td>
															<td align="right">Total:</td>
															<td align="right">$'.number_format($total,2).'</td>
														</tr>';
														
										echo' 		</table>'."\n";
										echo' 	</td>'."\n";
										echo' </tr>'."\n";
										
									}//end if get record
								?>
							</table>
							<table align="center">
								<tr>
									<td>
										<?php 
											if((count($cart) == 0)){//cart is empty
												echo'<input type="submit" name="btnCreateOrder" value="Create Order">'."\n";
											}else{
												echo'<input type="submit" name="btnUpdateOrder" value="Update Order">'."\n";
												echo'<input type="submit" name="btnCheckOut" value="Check Out" >'."\n";
											}
										?>
									</td>
								</tr>
							</table>
							</form>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr bgcolor="#82FA58">
			<td colspan="2">
				&nbsp;
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
