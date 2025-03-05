<?php
	/* 
		 ____  __  __  ___  ____  ____  ___  _   _ 
		(  _ \(  )(  )/ __)( ___)(_  _)/ __)( )_( )
		 ) _ < )(__)(( (_-. )__)  _)(_ \__ \ ) _ ( 
		(____/(______)\___/(__)  (____)(___/(_) (_) www.bugfish.eu
				_                                         _ 
			    | |                          _            (_)
			  _ | | ___ _   _ ____ ____ ___ | |_      ____ _ 
			 / || |/ _ \ | | / _  ) ___) _ \|  _)    / ___) |
			( (_| | |_| \ V ( (/ ( (__| |_| | |__   ( (___| |
			 \____|\___/ \_/ \____)____)___/ \___)   \____)_|
		Copyright (C) 2024 Jan Maurice Dahlmanns [Bugfish]

		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation, either version 3 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <https://www.gnu.org/licenses/>.
	*/
	# Include Settings
	if(file_exists("./settings.php")) {  require_once("./settings.php"); } else {echo "No settings.php found!<br />Please change settings.sample.php and rename this file to settings.php after that!"; exit(); }
	# Load CSRF Class
	$csrf = new x_class_csrf(_COOKIES_, _CSRF_VALID_LIMIT_TIME_);
	# Logout on Request
	if($user->loggedIn) {			
		switch(@$_GET["site"]) {	
			case "logout": $user->logout(); Header("Location: ./"); x_eventBoxPrep("You have been logged out!", "ok", _COOKIES_); exit(); break;
		};
	}
	# Display Cookie Banner Pre Post Function
	x_cookieBanner_Pre(_COOKIES_);	
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html version="-//W3C//DTD XHTML 1.1//EN"
      xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.w3.org/1999/xhtml
                          http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd">
  <head>
	<!-- Meta Tags For Site --> 
	<title><?php echo _TITLE_; ?> | DCI by Bugfish™</title>	 
	<!-- Meta Tags For Site -->
		<meta http-equiv="content-Type" content="text/html; utf-8" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="content-Language" content="en" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/x-icon" href="./_style/favicon.ico">
		<meta name="audience" content="all" />
		<meta name="robots" content="noindex, nofollow" />
		<link rel="stylesheet" href="./_style/main.css.php">
  </head>
  <body><div id="contentwrapper"></div><div id="content">
	<?php if($user->user_loggedIn) { $permsobj = new x_class_perm($mysql, _TABLE_PERM_, "dovint");?>
	<div id="nav">
		<?php if(($permsobj->hasPerm($user->user_id, "usermgr") OR $user->user_rank == 0)) { ?><a href="./?site=users" <?php if(@$_GET["site"] == "users") { echo 'id="nav_active"'; } ?>>Users</a> - <?php } ?>
		<?php if(($permsobj->hasPerm($user->user_id, "logs") OR $user->user_rank == 0)) { ?><a href="./?site=logs" <?php if(@$_GET["site"] == "logs") { echo 'id="nav_active"'; } ?>>Log</a> - <?php } ?>
		<?php if(($permsobj->hasPerm($user->user_id, "debug") OR $user->user_rank == 0) AND _MYSQL_LOGGING_) { ?><a href="./?site=debug" <?php if(@$_GET["site"] == "debug") { echo 'id="nav_active"'; } ?>>Debug</a> - <?php } ?>
		<?php if(($permsobj->hasPerm($user->user_id, "blocklist") OR $user->user_rank == 0) AND _MYSQL_LOGGING_) { ?><a href="./?site=blocks" <?php if(@$_GET["site"] == "blocks") { echo 'id="nav_active"'; } ?>>Blocklist</a> - <?php } ?>
		<a href="./?site=domains" <?php if(@$_GET["site"] == "domains") { echo 'id="nav_active"'; } ?>>Domains</a> - 
		<a href="./?site=profile" <?php if(@$_GET["site"] == "profile") { echo 'id="nav_active"'; } ?>>Profile</a> - 
		<a href="./?site=logout">Logout</a>		
	</div>	
<?php } 
	# Load Content
	if($user->loggedIn) {			
		switch(@$_GET["site"]) {
			case "logs": require_once("./_instance/site_logs.php"); break;
			case "blocks": require_once("./_instance/site_blocks.php"); break;
			case "users": require_once("./_instance/site_users.php"); break;
			case "debug": require_once("./_instance/site_debug.php"); break;
			case "domains": require_once("./_instance/site_domains.php"); break;
			case "profile": require_once("./_instance/site_profile.php"); break;
			default: Header("Location: ./?site=domains"); exit();				
		};
	} else {
		if(isset($_POST["auth"])) {
			if(@$_SESSION[_COOKIES_."captcha_default"] == @$_POST["captcha"] AND isset($_SESSION[_COOKIES_."captcha_default"])) {
				if($csrf->check(@$_POST["csrf"])) {
					if(!$ipbl->isblocked()) {
						if(isset($_POST["username"]) AND isset($_POST["password"])) {
							$x = $user->login_request(@$_POST["username"], @$_POST["password"]);
								if ($x == 1) { x_eventBoxPrep("Login successfull!", "ok", _COOKIES_); Header("Location: ".htmlspecialchars(@$_SERVER['REQUEST_URI'])); exit();}
								elseif ($x == 2) {x_eventBoxPrep("Wrong Username/Password!", "error", _COOKIES_); $ipbl->raise();}
								elseif ($x == 3) {x_eventBoxPrep("Wrong Username/Password!", "error", _COOKIES_); $ipbl->raise();}
								elseif ($x == 4) {x_eventBoxPrep("This user is blocked!", "error", _COOKIES_);} else { $ipbl->raise(); }} 
					} else { x_eventBoxPrep("IP is currently blocked!", "error", _COOKIES_); } 
				} else { x_eventBoxPrep("CSRF error, please retry!", "error", _COOKIES_); } 
			} else { x_eventBoxPrep("Captcha is wrong!", "error", _COOKIES_); } 
		} 
		?>
		<div class="content_box small_box vcenter">
			<form method="post">
				<input type="hidden"	name="csrf"			value="<?php echo $csrf->get(); ?>">
				<input type="text" 		name="username" 	placeholder="Username" >
				<input type="password"  name="password" 	placeholder="Password">
				<img src="./_style/captcha_default.php"><input type="text"  name="captcha" 	placeholder="Captcha">
				<input type="submit" 	value="Authenticate" name="auth" class="primary_button" style="cursor:pointer;">
			</form>
		</div>
	<?php	
	}
	
	# Close Div
	echo "</div>";
	# Display Event Boxes
	x_eventBoxShow(_COOKIES_);
	# Display Cookie Banner
	x_cookieBanner(_COOKIES_, false, "This website is using session cookies!");	
	# Display Footer
	echo _FOOTER_;
 ?>
  </body>
</html>