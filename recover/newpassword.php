<?php

session_start();

$err_msg = "" ;
$err_msg_html = "";


$id = "";

if( isset($_GET['id']) && ! empty($_GET['id']))
	$id = $_GET['id'];

if( isset($_SESSION['err_msg']))
{
	$err_msg = $_SESSION['err_msg'];
	$err_msg_html = '<div id="password_rec_error" class="alert alert-danger">' . $err_msg . '</div>';
}


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Choose a new password</title>

<link href="../css/recoverpassword.css" rel="stylesheet" type="text/css" />
<link href="../css/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css" />

<script language="javascript" type="text/javascript" src="../jquery/jquery-1.10.2.min.js"></script>
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
        <div class="calendar_view_element" id="password_reset_pannel">
        	<div id="page_intro_lbl">Choose a new password</div>
        	<div id="password_reset_cont">
        		<form method="POST" action="doPassReset.php" autocomplete="off">
	        		<div class="pass_recovery_msg">
						<?php
							if( ! empty($err_msg_html) )
								echo $err_msg_html;
						?>
					</div>
					<div class="pass_rec_cont">
						Please enter your new password:
					</div>
	        		<div class="pass_rec_cont">
						<label for="psw1">New password:</label>
						<input type='password' name="psw1" value="" id="psw1" maxlength="32" class="form-control" placeholder="New password"/>
					</div>
					<div class="pass_rec_cont">
						<label for="psw2">New password (repeat):</label>
						<input type='password' name="psw2" value="" id="psw2" maxlength="32" class="form-control" placeholder="New password"/>
					</div>
					<?php
						if( ! empty($id) )
							echo "<input type='hidden' id='v_code' name='v_code' value= $id >";
					?>
					<div class="pass_rec_cont">
						<input type="submit" class="btn btn-primary" id="comp_pass_reset_btn" value="Reset password">
					</div>
				</form>
        	</div>
        </div>
        <div id="credits">
         	Developed and maintained by: <a href="http://forum.sci.ccny.cuny.edu/people/science-division-directory/danielf">Daniel Fimiarz</a>, The City College of New York, CUNY.
        </div>
    </div>
</div>
</body>
</html>
