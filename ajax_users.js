function ajax_users(ssid)
{
    var id = ssid.substring(1);
    var httpxml;
    try
    {
// Firefox, Opera 8.0+, Safari
        httpxml = new XMLHttpRequest();
    } catch (e)
    {
// Internet Explorer
        try
        {
            httpxml = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e)
        {
            try
            {
                httpxml = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e)
            {
                alert("Your browser does not support AJAX!");
                return false;
            }
        }
    }
    function stateChanged()
    {
        if (httpxml.readyState == 4)
        {
            ///////////////////////
            //alert(httpxml.responseText); 
            document.getElementById("msgDsp").innerHTML = httpxml.responseText;
            var myObject = JSON.parse(httpxml.responseText);
            if (myObject.value.status == 'success') {
                
                //create the DOM ids
                var emailadd_id  = 'id_EMAILADD_'  + myObject.data.id;
                var pwhash_id    = 'id_PWHASH_'    + myObject.data.id;
                var firstname_id = 'id_FIRSTNAME_' + myObject.data.id;
                var minit_id     = 'id_MINIT_'     + myObject.data.id;
                var lastname_id  = 'id_LASTNAME_'  + myObject.data.id;
                var phone_id     = 'id_PHONE_'     + myObject.data.id;
                var adminflag_id = 'id_ADMINFLAG_' + myObject.data.id;
                var ownerflag_id = 'id_OWNERFLAG_' + myObject.data.id;
                var custflag_id  = 'id_CUSTFLAG_'  + myObject.data.id;
                var empflag_id   = 'id_EMPFLAG_'   + myObject.data.id;
                
                //use the DOM ids to update the document
                document.getElementById(emailadd_id ).innerHTML = myObject.data.emailadd ;
                document.getElementById(pwhash_id   ).innerHTML = myObject.data.pwhash   ;
                document.getElementById(firstname_id).innerHTML = myObject.data.firstname;
                document.getElementById(minit_id    ).innerHTML = myObject.data.minit    ;
                document.getElementById(lastname_id ).innerHTML = myObject.data.lastname_;
                document.getElementById(phone_id    ).innerHTML = myObject.data.phone    ;
                document.getElementById(adminflag_id).innerHTML = myObject.data.adminflag;
                document.getElementById(ownerflag_id).innerHTML = myObject.data.ownerflag;
                document.getElementById(custflag_id ).innerHTML = myObject.data.custflag ;
                document.getElementById(empflag_id  ).innerHTML = myObject.data.empflag  ;
                
                document.getElementById("msgDsp").innerHTML = myObject.value.message;
                
                var sid = 's' + myObject.data.id;
                document.getElementById(sid).innerHTML = "<input type=button value='Edit' onclick=edit_field(" + myObject.data.id + ")>";
                setTimeout("document.getElementById('msgDsp').innerHTML=' '", 2000)
            }// end of if status is success 
            else {  // if status is not success 
                document.getElementById("msgDsp").innerHTML = myObject.value.message;
                document.getElementById("msgDsp").style.borderColor = 'red';
                setTimeout("document.getElementById('msgDsp').style.display='none'", 2000)
            } // end of if else checking status

        }
    }


    //EMAILADD      PWHASH      FIRSTNAME	MINIT	LASTNAME	PHONE	ADMINFLAG	OWNERFLAG	CUSTFLAG	EMPFLAG	
    
    var url = "display-ajax-users.php";
    
    //create a string that matches the DOM id for each field
    var data_emailadd    = 'id_EMAILADD_'  + id;
    var data_pwhash      = 'id_PWHASH_'    + id;
    var data_firstname   = 'id_FIRSTNAME_' + id;
    var data_minit       = 'id_MINIT_'     + id;
    var data_lastname    = 'id_LASTNAME_'  + id;
    var data_phone       = 'id_PHONE_'     + id;
    var data_adminflag_Y = 'id_ADMINFLAG_' + id + '_Y';
    var data_adminflag_N = 'id_ADMINFLAG_' + id + '_N';
    var data_ownerflag_Y = 'id_OWNERFLAG_' + id + '_Y';
    var data_ownerflag_N = 'id_OWNERFLAG_' + id + '_N';
    var data_custflag_Y  = 'id_CUSTFLAG_'  + id + '_Y';
    var data_custflag_N  = 'id_CUSTFLAG_'  + id + '_N';
    var data_empflag_Y   = 'id_EMPFLAG_'   + id + '_Y';
    var data_empflag_N   = 'id_EMPFLAG_'   + id + '_N';
    
    //use the DOM id to get the data into JS vars
    var emailadd  = document.getElementById(data_emailadd ).value;
    var pwhash    = document.getElementById(data_pwhash   ).value;
    var firstname = document.getElementById(data_firstname).value;
    var minit     = document.getElementById(data_minit    ).value;
    var lastname  = document.getElementById(data_lastname ).value;
    var phone     = document.getElementById(data_phone    ).value;
    if (document.getElementById(data_adminflag_Y).checked) {var adminflag = document.getElementById(data_adminflag_Y).value;}
    if (document.getElementById(data_adminflag_N).checked) {var adminflag = document.getElementById(data_adminflag_N).value;}
    if (document.getElementById(data_ownerflag_Y).checked) {var ownerflag = document.getElementById(data_ownerflag_Y).value;}
    if (document.getElementById(data_ownerflag_N).checked) {var ownerflag = document.getElementById(data_ownerflag_N).value;}
    if (document.getElementById(data_custflag_Y ).checked) {var custflag  = document.getElementById(data_custflag_Y ).value;}
    if (document.getElementById(data_custflag_N ).checked) {var custflag  = document.getElementById(data_custflag_N ).value;}
    if (document.getElementById(data_empflag_Y  ).checked) {var empflag   = document.getElementById(data_empflag_Y  ).value;}
    if (document.getElementById(data_empflag_N  ).checked) {var empflag   = document.getElementById(data_empflag_N  ).value;}

    //put the data into HTTP GET string
    var parameters = 
            "id="         + id        + 
            "&emailadd="  + emailadd  + 
            "&pwhash="    + pwhash    + 
            "&firstname=" + firstname + 
            "&minit="     + minit     + 
            "&lastname="  + lastname  + 
            "&phone="     + phone     + 
            "&adminflag=" + adminflag + 
            "&ownerflag=" + ownerflag + 
            "&custflag="  + custflag  + 
            "&empflag="   + empflag   ;
    
//alert(parameters);
    httpxml.onreadystatechange = stateChanged;
    httpxml.open("POST", url, true)
    httpxml.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
//alert(parameters);
    document.getElementById("msgDsp").style.borderColor = '#ffffff';
    document.getElementById("msgDsp").style.display = 'inline'
    document.getElementById("msgDsp").innerHTML = "Wait .... ";
    httpxml.send(parameters)

////////////////////////////////


}

