<?Php

$id        = $_POST['id'];
$emailadd  = $_POST['emailadd'];
$pwhash    = $_POST['pwhash'];
$firstname = $_POST['firstname'];
$minit     = $_POST['minit'];
$lastname  = $_POST['lastname'];
$phone     = $_POST['phone'];
$adminflag = $_POST['adminflag'];
$ownerflag = $_POST['ownerflag'];
$empflag   = $_POST['empflag'];

//
$message=''; // 
$status='success';              // Set the flag  
////// Data validation starts ///
//sleep(2); // if you want any time delay to be added
//if(!is_numeric($mark)){ // checking data
//$message= "Data Error";
//$status='Failed';
// }
//
//if(!is_numeric($id)){  // checking data
//$message= "Data Error";
//$status='Failed';
//}
//
//if($mark > 100 or $mark < 0 ){
//$message= "Mark should be between 0 & 100";
//$status='Failed';
//}
////// Data Validation ends /////
if ($status <> 'Failed') {  // Update the table now

    require "config.php"; // MySQL connection string
    $count = $dbo->prepare(
              "update users set "
            . "emailadd  = :emailadd , "  
            . "pwhash    = :pwhash   , "  
            . "firstname = :firstname, "  
            . "minit     = :minit    , "  
            . "lastname  = :lastname , "  
            . "phone     = :phone    , "  
            . "adminflag = :adminflag, "  
            . "ownerflag = :ownerflag, "  
            . "empflag   = :empflag,   "  
            . "WHERE id  = :id         "
    );
    
    $count->bindParam(":id       " , $id       ,  PDO::PARAM_STR, 1);
    $count->bindParam(":emailadd " , $emailadd ,  PDO::PARAM_INT, 10);
    $count->bindParam(":pwhash   " , $pwhash   ,  PDO::PARAM_STR, 255);
    $count->bindParam(":firstname" , $firstname,  PDO::PARAM_STR, 255);
    $count->bindParam(":minit    " , $minit    ,  PDO::PARAM_STR, 1);
    $count->bindParam(":lastname " , $lastname ,  PDO::PARAM_STR, 255);
    $count->bindParam(":phone    " , $phone    ,  PDO::PARAM_INT, 10);
    $count->bindParam(":adminflag" , $adminflag,  PDO::PARAM_STR, 1);
    $count->bindParam(":ownerflag" , $ownerflag,  PDO::PARAM_STR, 1);
    $count->bindParam(":empflag  " , $empflag  ,  PDO::PARAM_STR, 1);
    
    if ($count->execute()) {
        $no = $count->rowCount();
        $message = " $no  Record updated<br>";
    } else {
        $message = print_r($dbo->errorInfo());
        $message .= ' database error...';
        $status = 'Failed';
    }
    
} else {
    //handle validation errors here
    
}// end of if else if status is success 

$data = array(
    'id     '   => $id       ,
    'emailadd'  => $emailadd ,
    'pwhash '   => $pwhash   ,
    'firstname' => $firstname,
    'minit  '   => $minit    ,
    'lastname'  => $lastname ,
    'phone  '   => $phone    ,
    'adminflag' => $adminflag,
    'ownerflag' => $ownerflag
);

$a = array(
    'data'  => $data, 
    'value' => array(
        "status"  => "$status", 
        "message" => "$message"
    )
);

echo json_encode($a);
?>