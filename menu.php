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
//get search term
if (isset($_POST['searchinput'])){
		$searchinput = $_POST['searchinput'];
		//echo "searchinput: $searchinput<br />";
}
	
//category filter from main menu
//$cat = $_GET['cat']; //bugfix 2012-12-16 KSM
if(isset($_GET['cat']))
	$cat = $_GET['cat'];
else
	$cat = $_POST['cat'];

if (isset($searchinput)){
	$sql = "select * from PRODUCTS where (HIDEFLAG = 0) and (NAME LIKE '%$searchinput%') order by CATID";
}else{
	$sql = "select * from PRODUCTS where (HIDEFLAG = 0)".((intval($cat) > 0) ? " and (CATID = '$cat')" : "")." order by CATID";
}	

//echo $sql."<br />";
if (!($menuResults = $mysqli->query($sql))){
	die("Error - cannot get menu.\n$sql");
}

$temp = $menuResults->fetch_assoc();
//echo $menuResults->num_rows."<br />";
if ($menuResults->num_rows==0){
//echo"0 results<br />";
echo'<script type="text/javascript" >';
echo'alert("No search results");';
echo'window.location="menu.php?cat=0";';
echo'</script>';
}
$menuResults->data_seek(0);


//get current list of all categories - created in header.php
$userCatList = $_SESSION['userCatList'];

//get, or create cart
//unset($_SESSION['cart']); //for debugging
if (isset($_SESSION['cart'])){
	$cart = $_SESSION['cart'];
}else{
	//echo"new cart!<br />";
	$cart = array();
}

/*
//debug messages
echo "<pre>";	
echo "session cart: <br />";
print_r($cart);
echo "</pre>";
*/

/*
//debug messages
echo "<pre>";	
echo "POST: <br/>";
print_r($_POST);
echo "</pre>";
*/

/*
//debug messages
echo "<pre>";	
echo "GET: <br/>";
print_r($_GET	);
echo "</pre>";
*/

//update cart
foreach($_POST as $key => $value){
	$arr = explode("-",$key);
	if($arr[0] == "key"){
		//if qty = 0, delete from cart; otherwise add/update
		if($value == 0){
			unset($cart[$arr[1]]);
		}
		else{
			$cart[$arr[1]] = $value;
		}	
	}
}	
//put updated cart back into session
$_SESSION['cart'] = $cart;

/*
//debug messages
echo "<pre>";	
echo "SESSION: <br />";
print_r($_SESSION);
echo "</pre>";
*/

/*
//debug messages
echo "<pre>";	
echo "updated cart: <br />";
print_r($cart);
echo "</pre>";
*/

if(isset($cart)){
	if(count($cart)==0)
		$cartStatusMsg = "You have 0 items in cart";
	else{
		$itemCount = 0;
		foreach($cart as $key => $value)
			$itemCount += $value;
			
		$cartStatusMsg = "You have $itemCount items in your cart."; 
	}
}

if (isset($_POST['searchinput'])){
		$searchinput = $_POST['searchinput'];
		echo "searchinput: $searchinput<br />";
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
function test_it(f)
{
	//alert("Searching");
	
	if (f.searchinput.value == "")
    {
       alert("You have to enter a search term!");
       return;
    }

        //changes to catch XSS and SQL injection
    if ((f.searchinput.value.indexOf("<") != -1) || (f.searchinput.value.indexOf(">") != -1) ||
	(f.searchinput.value.indexOf("'") != -1) || (f.searchinput.value.indexOf('"') != -1))
    {
	alert("You have illegal characters in search term!");
	return;
    }
				
   //alert(f.searchinput.value) ; 
   f.submit();    
      
}

</script>

<script type="text/javascript" >
//function viewCart(f){
//	alert("viewing cart");
//	f.action="viewcart.php";
//	alert (f.action);
//	f.submit();
//}
</script>

<html>
<head>
	<?php
		if (isset($_POST['btnViewCart'])){
			//if "View Cart" button was pressed and cart is not empty, redirect to viewcart.php after updating cart
			echo '<meta HTTP-EQUIV="REFRESH" content="0; url=viewcart.php">'."\n";
		}
	?>
	<title>Taste It Again - Menu</title>
</head>
<body leftmargin="0px" topmargin="0px" marginwidth="0px" marginheight="0px" bgcolor="black">

	<table cellspacing="0" cellpadding="0" width="900" align="center" border="0">

		<?php include("header.php"); ?>

		<tr height="500" bgcolor="#82FA58">
			<td colspan="2">
				<table cellspacing="0" cellpadding="0" width="900" border="0" height="100%">
					<tr valign="top">
						<td>
<!-- CONTENT -->						
							<form name="menu" action="<?php echo $_SERVER['SCRIPT_NAME'];?>" id="menu" method = "POST">
							<table cellspacing="0" cellpadding="0" width="900" border="0">
								<?php 
									if($cartStatusMsg != ""){
										echo "<tr><td>$cartStatusMsg</td></tr>\n";
									}
								?>
								<tr>
									<td>
										<input type="text" name="searchinput" value="">
									 
										<input type="button" name="btnSearch" value="Search" onclick="test_it(this.form);">
									</td>
								</tr>
								<tr>
									<td>
										<center><h1>Menu</h1><center>
									</td>
								</tr>
								<?php 
									$currentCatID =0;
									$bgColor = 'white';
									if($product = $menuResults->fetch_assoc()){ //there is at least one record
										while (true){ //break when no more records
											$currentCatID = intval($product['CATID']);
											$currentCatName = $userCatList[$currentCatID];
										
											//output new category header
											echo "<tr>"."\n";
											echo "	<td>"."\n";
											echo "		<h2>$currentCatName</h2>\n";
											echo "	</td>"."\n";
											echo "</tr>"."\n";
											
											//alternate background colors
											if ($bgColor != '#BDBDBD'){
												$bgColor = '#BDBDBD';
											}else{
												$bgColor = 'white';
											}
											
											//print category header
											echo '<tr>'."\n";
											echo '	<td>'."\n";
											echo ' 		<table cellspacing="1" cellpadding="1" style="border:1px #000000 solid" bgcolor="'.$bgColor.'" align="center" width="800">'."\n";
											echo' 			<tr>'."\n";
											echo' 				<td width="150">'."\n";
											echo' 					<b><h3>Product</h3></b>'."\n";
											echo' 				</td>'."\n";
											echo' 				<td width="75">'."\n";
											echo' 					<center><b><h3>Price</h3></b></center>'."\n";
											echo' 				</td>'."\n";
											echo' 				<td width="150">'."\n";
											echo' 					<center><b><h3>Quantity</h3></b></center>'."\n";
											echo' 				</td>'."\n";
											echo' 			</tr>'."\n";
											
											while (true){//break when caetgory changes or no more records
												//print record
												echo'<tr>'."\n";
												echo'	<td>'."\n";
												echo'<span title="'.$product['DESCRIPTION'].'">'."\n";
												echo'<b>'.$product['NAME'].'</b>'."\n";
												echo'	</td>'."\n";
												echo'	<td>'."\n";
												echo'		<center>$'.number_format($product['PRICE'],2).'</center>'."\n";
												echo'	</td>'."\n";
												echo'	<td>'."\n";
												$pKey = 'key-'.$product['PRODID'];
												$pQty = (isset($cart[$product['PRODID']]))? intval($cart[$product['PRODID']]) : 0;
												echo'		<center>
															<input type="text"  
															name="'.$pKey.'"  
															size="2"  
															value="'.$pQty.'">
															</center>'."\n";
												echo'	</td>'."\n";
												echo'</tr>'."\n";
											
												//get next record
												if (!($product = $menuResults->fetch_assoc())){
													//no more records
													break;
												}	
												
												//check if caategory changes
												if (intval($product["CATID"]) != $currentCatID){
													break;
												}//end if
											} //end while same category
											
											//print end of category table
											echo' 		</table>'."\n";
											echo' 	</td>'."\n";
											echo' </tr>'."\n";
											
											if (!($product)){
												//no more records
												break;
											}
										}//end while records exist
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
											echo'<input type="submit" name="btnViewCart" value="View Cart" >'."\n";
										}
										?>
										<input type="hidden" name="cat" value="<?php echo $cat; ?>">
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
