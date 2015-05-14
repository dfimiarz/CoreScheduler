<?php
session_start();

$err_msg = "";
$err_msg_html = "";



if (isset($_SESSION['err_msg'])) {
    $err_msg = $_SESSION['err_msg'];
    unset($_SESSION['err_msg']);
    $err_msg_html = '<div id="password_rec_error" class="alert alert-danger">' . $err_msg . '</div>';
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Password recovery</title>

        <link href="../css/recoverpassword.css" rel="stylesheet" type="text/css" />
        <link href="../css/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <script language="javascript" type="text/javascript" src="../jquery/jquery-1.10.2.min.js"></script>
        <script language="javascript" type="text/javascript" src="../jquery/jquery-ui-1.10.3.recovery_page.min.js"></script>
        <script language="javascript" type="text/javascript" src="../js/bootstrap/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="page_element" id="view_port">
            <div id="calendar_view">
                <div class="calendar_view_element">
                    <table id="top_panel_container">
                        <tr>
                            <td>
                                <div id="info" class="corecal_gen_container navigation_panel">
                                    <div id="logo_container">
                                        <img src="../images/logo.png"/>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="calendar_view_element" id="account_recovery_pannel">
                    <div id="page_intro_lbl">Password recovery</div>
                    <form method="POST" action="./ctrl/initpassreset.php" autocomplete="off">
                        <div class="pass_recovery_msg">
                            <?php
                            if (!empty($err_msg_html))
                                echo $err_msg_html;
                            ?>
                        </div>
                        <div class="pass_rec_cont">
                            <label for="unameinput">Enter you user name:</label>
                            <input type='text' name="uname" value="" id="unameinput" maxlength="64" class="form-control" placeholder="User Name"/>
                        </div>
                        <div class="pass_rec_cont">
                            <button type="submit" class="btn btn-primary">Reset my password</button>
                        </div>
                    </form>
                    <div id="credits">
                        Developed and maintained by: <a href="http://forum.sci.ccny.cuny.edu/people/science-division-directory/danielf">Daniel Fimiarz</a>, The City College of New York, CUNY.
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
