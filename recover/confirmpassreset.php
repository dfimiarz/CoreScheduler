<?php
session_start();

$info_msg = "";
$info_msg_html = "";


$info_msg_html = "";

if (isset($_SESSION['info_msg'])) {
    $info_msg = $_SESSION['info_msg'];
    $info_msg_html = '<div id="password_rec_error" class="alert alert-success">' . $info_msg . '</div>';
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>CCNY CoreLABS - Password recovery page</title>

        <link href="../../css/recoverpassword.css" rel="stylesheet" type="text/css" />
        <link href="../../css/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <script language="javascript" type="text/javascript" src="../../jquery/jquery-1.10.2.min.js"></script>
        <script language="javascript" type="text/javascript" src="../../jquery/jquery-ui-1.10.3.recovery_page.min.js" /></script>
        <script language="javascript" type="text/javascript" src="../../js/bootstrap/bootstrap.min.js"></script>
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
                                        <img src="../../images/logo.png"/>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="calendar_view_element" id="account_recovery_pannel">
                    <div id="page_intro_lbl">CoreLABS&trade; password rest page.</div>
                    <div class="pass_rec_cont">
                        <?php echo $info_msg_html; ?>
                    </div>
                    <div class="pass_rec_cont">
                        <a href="../../" class="btn btn-default btn-block">Go Back</a>
                    </div>
                    <div id="credits">
                        Developed and maintained by: <a href="http://forum.sci.ccny.cuny.edu/people/science-division-directory/danielf">Daniel Fimiarz</a>, The City College of New York, CUNY.
                    </div>
                </div>

            </div>
        </div>
    </body>
</html>
