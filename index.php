<?php
include_once './ccny/scidiv/cores/autoloader.php';	
include_once './ccny/scidiv/cores/components/Utils.php';
        
use ccny\scidiv\cores\components\Utils as Utils;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CCNY - Conference Room Reservations</title>
<link href="fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
<link href="fullcalendar/fullcalendar.print.css" rel="stylesheet" type="text/css" media="print" />
<link href="css/cupertino/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" type="text/css"/>
<link href="jquery/jqueryui-editable/css/jqueryui-editable.css" rel="stylesheet" type="text/css"/>
<link href="css/corecal.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="jquery/jquery-1.10.2.min.js"></script>
<script language="javascript" type="text/javascript" src="jquery/jquery-ui-1.10.3.custom.min.js" /></script>
<script language="javascript" type="text/javascript" src="jquery/jqueryui-editable/js/jqueryui-editable.min.js" /></script>
<script language="javascript" type="text/javascript" src="fullcalendar/fullcalendar.min.js" /></script>
<script language="javascript" type="text/javascript" src="js/noty/packaged/jquery.noty.packaged.min.js" /></script>
<script language="javascript" type="text/javascript" src="js/components.js" /></script>
<script language="javascript" type="text/javascript" src="js/corecal.js" ></script>

</head>
<body>
<div class="page_element" id="view_port">
    <div id="calendar_view">
		<div class="calendar_view_element">
			<!-- Tob pannel -->
			<div id="acc_tools_nav_bar">
					<ul id="acc_tools_nav_cont">
						<!--<li><span id="register_btn">Register</span></li>-->
						<!--<li id="acc_rec_btn"><a href="./loginproblems.html">Cannot log in?</a></li>-->
					</ul>
			</div>
			<!-- Main container-->
			<div id="ctrl_pannel_1">
				<div class="corecal_gen_container navigation_panel top_bar_element ctrl_pannel_left">
					<div id="log_in_panel" class="login_wrapper">
						<form id="login_form">
							<div class="login_form_element login_form_lbl">
								Login Name:
							</div>
							<div class="login_form_element login_form_fld">
								<input type="text" id="username_txt" class="login_txt_box">
							</div>
							<div class="login_form_element login_form_lbl">
								Password:
							</div>
							<div class="login_form_element login_form_fld">
								<input type="password" id="password_txt" class="login_txt_box">
							</div>
							<div class="login_form_element login_form_btn">
								<input type="submit" id="corecal_login" value="Log In">
							</div>
						</form>
					</div>
					<div id="logged_in_panel" class="login_wrapper">
						<div id="logged_in_lbl_cont">
							<div class="logged_in_field larger_label">Login: <span id="user_login"></span></div>
							<div class="logged_in_field">PI Assigned:&nbsp;<span id="user_pi"></span></div>
							<div class="logged_in_field">Last login:&nbsp;<span id="user_last_log"></span></div>
							<div class="logged_in_field">User Type:&nbsp;<span id="user_type"></span>&nbsp;<img src="images/help.gif" class="help_icon_small"/></div>
						</div>
						<div id="logged_in_btn_cont">
							<input type="submit" id="corecal_logout" value="Log Out">
						</div>
					</div>
				</div>
				<div class="corecal_gen_container navigation_panel top_bar_element ctrl_pannel_middle">
					<div id="services_wrapper">
								<div id="service_selector_header">
									Select a room:
								</div>

								<div id="service_selector" title="Select building...">
									<select id="facility_select" class="corecal_select">

									</select>
								</div>
								<div id="service_selector" title="Select room type...">
									<select id="equipment_select" class="corecal_select">

									</select>
								</div>
								<div id="service_selector" title="Select room number...">
									<select id="service_select" class="corecal_select">

									</select>
								</div>
								<div class="dialog_item_hidden" id="sel_res_id"
									<?php
										
										$utils = Utils::getObject();

										$rid = $utils->getRID();

										if( $rid != null )
											echo 'RID="' . $rid . '"';
									?>
									>
								</div>
					</div>
				</div>
				<div class="corecal_gen_container navigation_panel top_bar_element ctrl_pannel_right">
						<div id="logo_container">
							<img src="images/logo.png" width="560" height="112"/>
						</div>
				</div>
			</div>
			<div id="ctrl_pannel_2">
				<div class="top_bar_element ctrl_pannel_left corecal_simple_gen_container">&nbsp;</div>
				<div class="top_bar_element ctrl_pannel_middle corecal_simple_gen_container">
					<div id="dashboard_role_panel" class="corecal_gen_container dashboard_panel">
						<div id="role_txt_cont"><span id="user_role">N/A</span></div>
						<div id="req_access_cont"><input type="submit" id="req_access_btn" value="Request Access"></div>
					</div>
				</div>
				<div id="dashboard_control_panel" class="top_bar_element ctrl_pannel_right corecal_simple_gen_container">
					<div id="help_view_icon"><a href="http://forum.sci.ccny.cuny.edu/administration/deans-office/room-reservations" target="_blank"><img src="images/help.png" title="Help" alt="Help"/></a></div>
				</div>
			</div>
			<!-- Main container-->
		</div>
        <div class="calendar_view_element" id="calendar">
        </div>
        <div id="credits">
         	Developed and maintained by: <a href="http://forum.sci.ccny.cuny.edu/people/science-division-directory/danielf">Daniel Fimiarz</a>, The City College of New York, 160 Convent Ave, MR 1328, New York, NY 10031.
        </div>
    </div>
</div>
<div id="session_info_d" title="Details...">
    <div id="session_info">

    </div>
</div>
</body>
</html>
