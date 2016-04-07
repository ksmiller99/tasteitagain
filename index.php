<html>
    <head>
        <title>Project Login</title>
        <script type="text/javascript" defer>
            function test_it(f)
            {
                //alert("Test");
                if (f.u_id.value == "" || f.u_pwd.value == "")
                {
                    alert("You have to enter a user ID and password!");
                    return;
                }

                //changes to catch XSS and SQL injection
                if ((f.u_id.value.indexOf("<") != -1) || (f.u_pwd.value.indexOf("<") != -1) ||
                        (f.u_id.value.indexOf("'") != -1) || (f.u_pwd.value.indexOf("'") != -1))
                {
                    alert("You have illegal characters in user ID or password!");
                    return;
                }

                //alert(f.u_id.value + ' ' + f.u_pwd.value ) ; 
                f.submit();

            }

        </script>
    </head>

    <body leftmargin="0px" topmargin="0px" marginwidth="0px" marginheight="0px" bgcolor="white">
        <table cellspacing="0" cellpadding="0" border="0" width="500" align="center">
            <tr>
                <td width="300">
                    <br /><br /><br /><br />
                    <form action = "cPanelLogin.php" method = "POST" name="login" id="login">
                        <table cellspacing="1" cellpadding="3" style="border:1px #000000 solid" bgcolor="#BDBDBD" align="center">
                            <tr>
                                <td width="300" colspan="2">
                            <center><h1>Project Login</h1></center>
                            </td>
                            </tr>

                            <tr>
                                <td width="75">
                                    Login:
                                </td>
                                <td width="225">
                                    <input type="text" name="u_id" size="30">
                                </td>
                            </tr>

                            <tr>
                                <td width="75">
                                    Password:
                                </td>
                                <td width="225">
                                    <input type="password" 
                                           name="u_pwd" 
                                           size="30" 
                                           onkeydown = "if (event.keyCode == 13)
                                            document.getElementById('btnLogin').click()">
                                </td>
                            </tr>

                        </table>
                        <table align="center">
                            <tr>
                                <td>
                                    <input type = "button" id="btnLogin" value = "Login" onclick="test_it(this.form);">
                                    <input type="reset" value="Reset">
                                </td>
                            </tr>
                        </table>
                    </form><br>
                    <table cellspacing="0" cellpadding="0" align="center">
                        <tr>
                            <td>
                                Kevin S. Miller<br>
                                Final Project<br>
                                CSIT-555 Database Systems<br>
                                Professor Katherine G. Herbert, Ph.D.<br>
                                Montclair State University<br>
                                Spring 2016
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>

    </body>
</html>
