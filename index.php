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
						<li id="acc_rec_btn"><a href="./loginproblems.html">Cannot log in?</a></li>
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
					<div id="help_view_icon"><a href="http://forum.sci.ccny.cuny.edu/core_facilities/corelabs-guide" target="_blank"><img src="images/help.png" title="Help" alt="Help"/></a></div>
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
<div id="signup_dialog" title="Create an account">
  <p id="reg_error_msg"></p>
  <form id="registration_form">
  		<table>
  			<tr>
  				<td class="signup_section_column">
  					<fieldset>
  						<legend>User information:</legend>
						<table>
							<tr>
								<td class="signup_label_row">
									<span>User name*: </span>
								</td>
								<td class="signup_field_row">
									<input type="text" name="uname" id="uname" class="text ui-widget-content ui-corner-all" title="6-16 characters long. Starts with a letter." />
								<td>
							</tr>
							<tr>
								<td class="signup_label_row">
									<span>Password*: </span>
								</td>
								<td class="signup_field_row">
									<input type="password" name="psw1" id="psw1" class="text ui-widget-content ui-corner-all" title="At least 6 characters long. Must contain lower, upper case letters and a digit" />
								<td>
							</tr>
							<tr>
								<td class="signup_label_row">
									<span>Confirm Password*: </span>
								</td>
								<td class="signup_field_row">
									<input type="password" name="psw2" id="psw2" class="text ui-widget-content ui-corner-all" />
								<td>
							</tr>

							<tr>
								<td colspan=2>
									<hr>
								</td>
							</tr>
							<tr>
								<td class="signup_label_row">
									<span>First Name*:  </span>
								</td>
								<td class="signup_field_row">
									<input type="text" name="fname" id="fname" class="text ui-widget-content ui-corner-all" />
								<td>
							</tr>

							<tr>
								<td class="signup_label_row">
									<span>Last Name*: </span>
								</td>
								<td class="signup_field_row">
									<input type="text" name="lname" id="lname" class="text ui-widget-content ui-corner-all" />
								<td>
							</tr>


							<tr>
								<td class="signup_label_row">
									<span>Phone Number*: </span>
								</td>
								<td class="signup_field_row">
									<input type="text" name="phone" id="phone" class="text ui-widget-content ui-corner-all" title="Please use format (555) 555-5555"/>
								<td>
							</tr>


							<tr>
								<td class="signup_label_row">
									<span>E-mail*: </span>
								</td>
								<td class="signup_field_row">
									<input type="text" name="email1" id="email1" class="text ui-widget-content ui-corner-all" />
								<td>
							</tr>
							<tr>
								<td class="signup_label_row">
									<span>Confirm E-mail*: </span>
								</td>
								<td class="signup_field_row">
									<input type="text" name="email2" id="email2" class="text ui-widget-content ui-corner-all" />
								<td>
							</tr>
						</table>
					</fieldset>
				</td>
				<td class="signup_section_column">
					<fieldset>
  					<legend>Billing Information:</legend>
					<table>
						<tr>
							<td colspan=2>
								<span class="small_label">Principal investigator (PI) specified below will be billed for all the charges generated by your account.</span>
							</td>
						</tr>
						<tr>
							<td class="signup_label_row">
								<span>Full Name*: </span>
							</td>
							<td class="signup_field_row">
								<input type="text" name="pi_name" id="pi_name" class="text ui-widget-content ui-corner-all" />
							<td>
						</tr>
						<tr>
							<td class="signup_label_row">
								<span>E-mail*: </span>
							</td>
							<td class="signup_field_row">
								<input type="text" name="pi_email" id="pi_email" class="text ui-widget-content ui-corner-all" />
							<td>
						</tr>
						<tr>
							<td class="signup_label_row">
								<span>Phone Number*: </span>
							</td>
							<td class="signup_field_row">
								<input type="text" name="pi_phone" id="pi_phone" class="text ui-widget-content ui-corner-all" title="Please use format (555) 555-5555"/>
							<td>
						</tr>
						<tr>
							<td class="signup_label_row">
								<span>Address 1*: </span>
							</td>
							<td class="signup_field_row">
								<input type="text" name="pi_address_1" id="pi_address_1" class="text ui-widget-content ui-corner-all"/>
							<td>
						</tr>
						<tr>
							<td class="signup_label_row">
								<span>Address 2: </span>
							</td>
							<td class="signup_field_row">
								<input type="text" name="pi_address_2" id="pi_address_2" class="text ui-widget-content ui-corner-all"/>
							<td>
						</tr>
						<tr>
							<td class="signup_label_row">
								<span>City*: </span>
							</td>
							<td class="signup_field_row">
								<input type="text" name="pi_city" id="pi_city" class="text ui-widget-content ui-corner-all"/>
							<td>
						</tr>
						<tr>
							<td class="signup_label_row">
								<span>State*: </span>
							</td>
							<td class="signup_field_row">
								<select name="pi_state" id="pi_state" class="text ui-widget-content ui-corner-all">
									<option value="" selected="selected">Select a State</option>
									<option value="AL">Alabama</option>
									<option value="AK">Alaska</option>
									<option value="AZ">Arizona</option>
									<option value="AR">Arkansas</option>
									<option value="CA">California</option>
									<option value="CO">Colorado</option>
									<option value="CT">Connecticut</option>
									<option value="DE">Delaware</option>
									<option value="DC">District Of Columbia</option>
									<option value="FL">Florida</option>
									<option value="GA">Georgia</option>
									<option value="HI">Hawaii</option>
									<option value="ID">Idaho</option>
									<option value="IL">Illinois</option>
									<option value="IN">Indiana</option>
									<option value="IA">Iowa</option>
									<option value="KS">Kansas</option>
									<option value="KY">Kentucky</option>
									<option value="LA">Louisiana</option>
									<option value="ME">Maine</option>
									<option value="MD">Maryland</option>
									<option value="MA">Massachusetts</option>
									<option value="MI">Michigan</option>
									<option value="MN">Minnesota</option>
									<option value="MS">Mississippi</option>
									<option value="MO">Missouri</option>
									<option value="MT">Montana</option>
									<option value="NE">Nebraska</option>
									<option value="NV">Nevada</option>
									<option value="NH">New Hampshire</option>
									<option value="NJ">New Jersey</option>
									<option value="NM">New Mexico</option>
									<option value="NY">New York</option>
									<option value="NC">North Carolina</option>
									<option value="ND">North Dakota</option>
									<option value="OH">Ohio</option>
									<option value="OK">Oklahoma</option>
									<option value="OR">Oregon</option>
									<option value="PA">Pennsylvania</option>
									<option value="RI">Rhode Island</option>
									<option value="SC">South Carolina</option>
									<option value="SD">South Dakota</option>
									<option value="TN">Tennessee</option>
									<option value="TX">Texas</option>
									<option value="UT">Utah</option>
									<option value="VT">Vermont</option>
									<option value="VA">Virginia</option>
									<option value="WA">Washington</option>
									<option value="WV">West Virginia</option>
									<option value="WI">Wisconsin</option>
									<option value="WY">Wyoming</option>
								</select>
							<td>
						</tr>
						<tr>
							<td class="signup_label_row">
								<span>Zip Code*: </span>
							</td>
							<td class="signup_field_row">
								<input type="text" name="pi_zip" id="pi_zip" maxlength="5" size="5" class="text ui-widget-content ui-corner-all"/>
							<td>
						</tr>
					</table>
					</fieldset>
				<td>
			</tr>
		</table>
		<span class="small_label">* indicated required fields. Hover your mouse over each field for help.</span>
  </form>
</div>
</body>
</html>
