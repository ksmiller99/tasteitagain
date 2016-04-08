function ajax(id)
{
var httpxml;
try
{
// Firefox, Opera 8.0+, Safari
httpxml=new XMLHttpRequest();
}
catch (e)
{
// Internet Explorer
try
{
httpxml=new ActiveXObject("Msxml2.XMLHTTP");
}
catch (e)
{
try
{
httpxml=new ActiveXObject("Microsoft.XMLHTTP");
}
catch (e)
{
alert("Your browser does not support AJAX!");
return false;
}
}
}
function stateChanged() 
{
if(httpxml.readyState==4)
{
///////////////////////
//alert(httpxml.responseText); 
var myObject = JSON.parse(httpxml.responseText); 
if(myObject.value.status=='success'){
var mark_id='mark_'+myObject.data.id;
var name_id='name_'+myObject.data.id;
var class_id='class_'+myObject.data.id;
var sex_id='sex_'+myObject.data.id;
document.getElementById(mark_id).innerHTML = myObject.data.mark;
document.getElementById(name_id).innerHTML = myObject.data.name;
document.getElementById(class_id).innerHTML = myObject.data.class;
document.getElementById(sex_id).innerHTML = myObject.data.sex;


document.getElementById("msgDsp").innerHTML=myObject.value.message;
var sid='s'+myObject.data.id;
document.getElementById(sid).innerHTML = "<input type=button value='Edit' onclick=edit_field("+myObject.data.id+")>";
setTimeout("document.getElementById('msgDsp').innerHTML=' '",2000)
}// end of if status is success 
else {  // if status is not success 
document.getElementById("msgDsp").innerHTML=myObject.value.message;
document.getElementById("msgDsp").style.borderColor='red';
setTimeout("document.getElementById('msgDsp').style.display='none'",2000)
} // end of if else checking status

}
}


var url="display-ajax.php";
var data_mark='data_mark'+ id;
var data_name='data_name'+ id;
var data_class='data_class'+ id;
var data_sexM='data_sex'+ id+'M';
var data_sexF='data_sex'+ id+'F';


var mark = document.getElementById(data_mark).value; 
var name = document.getElementById(data_name).value; 
var class1 = document.getElementById(data_class).value; 
if(document.getElementById(data_sexM).checked){
var sex=document.getElementById(data_sexM).value;
}
if(document.getElementById(data_sexF).checked){
var sex=document.getElementById(data_sexF).value;
}

var parameters="mark=" + mark + "&id=" + id + "&name="+name;
parameters = parameters + "&class="+class1+ "&sex="+sex;
//alert(parameters);
httpxml.onreadystatechange=stateChanged;
httpxml.open("POST", url, true)
httpxml.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
//alert(parameters);
document.getElementById("msgDsp").style.borderColor='#ffffff';
document.getElementById("msgDsp").style.display = 'inline'
document.getElementById("msgDsp").innerHTML="Wait .... ";
httpxml.send(parameters) 

////////////////////////////////


}

