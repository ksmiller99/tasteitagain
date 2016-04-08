<?Php
$id=$_POST['id'];
$mark=$_POST['mark'];
$name=$_POST['name'];
$sex=$_POST['sex'];
$class=$_POST['class'];

$message=''; // 
$status='success';              // Set the flag  
//sleep(2); // if you want any time delay to be added

//// Data validation starts ///
if(!is_numeric($mark)){ // checking data
$message= "Data Error";
$status='Failed';
 }

if(!is_numeric($id)){  // checking data
$message= "Data Error";
$status='Failed';
}

if($mark > 100 or $mark < 0 ){
$message= "Mark should be between 0 & 100";
$status='Failed';
}
//// Data Validation ends /////
if($status<>'Failed'){  // Update the table now

//$message="update student set mark=$mark, name
require "config.php"; // MySQL connection string
$count=$dbo->prepare("update student set mark=:mark,name=:name,class=:class,sex=:sex WHERE id=:id");
$count->bindParam(":mark",$mark,PDO::PARAM_INT,3);
$count->bindParam(":name",$name,PDO::PARAM_STR,50);
$count->bindParam(":class",$class,PDO::PARAM_STR,9);
$count->bindParam(":sex",$sex,PDO::PARAM_STR,6);

$count->bindParam(":id",$id,PDO::PARAM_INT,3);

if($count->execute()){
$no=$count->rowCount();
$message= " $no  Record updated<br>";
}else{
$message = print_r($dbo->errorInfo());
$message .= ' database error...';
$status='Failed';
}


}else{

}// end of if else if status is success 
$a = array('id'=>$id,'mark'=>$mark,'name'=>$name,'class'=>$class,'sex'=>$sex);
$a = array('data'=>$a,'value'=>array("status"=>"$status","message"=>"$message"));
echo json_encode($a); 
?>