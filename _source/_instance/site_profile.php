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
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if ($csrf->check($_POST['csrf'])) {
			if (@$_POST["password1"] == @$_POST["password2"]) {
				if (trim(@$_POST["password1"]) != "") {
					if (strlen(@$_POST["password1"]) >= 8) {
						$user->changeUserPass($user->user_id, $_POST["password1"]) ;
						x_eventBoxPrep("Password has been changed!", "ok", _COOKIES_);
					} else  { x_eventBoxPrep("Password needs at least 8 signs!", "error", _COOKIES_); }
				} else  { x_eventBoxPrep("Passwords can not be empty!", "error", _COOKIES_); }
			} else  { x_eventBoxPrep("Passwords are not identical!", "error", _COOKIES_); }
		} else  { x_eventBoxPrep("CSRF Error - Retry!", "error", _COOKIES_); }
	}  

	switch($user->user_rank) {
		case 0: $rank = "Superuser"; break;
		default: $rank = "User"; break; }
	?> 

	<div class="content_box" style="max-width: 500px;text-align: left;">
		Welcome to your profile page! <br />You can change your password here... watch out and not lose your password! <b>There is no way for yourself to restore it...</b><br /><br />
		<?php
			echo 'Username: <b>'.$user->user_name.'</b><br />';
			echo 'Last Login: <b>'.$user->user["last_login"].'</b><br />';
			echo 'Rank: <b>'.$rank.'</b><br />';
			echo 'IP: <b>'.$_SERVER["REMOTE_ADDR"].'</b><br />';
		?>
	</div>

	
<?php
	echo '<div class="content_box" style="max-width: 500px;">';
		echo '<form method="post">';
			echo "<input name='password1' type='password' placeholder='Password'><br clear='both'/>";
			echo "<input name='password2' type='password' placeholder='Confirm Password'><br clear='both'/>";
			echo "<input name='updatepass' type='submit' value='Change Password' style='cursor:pointer !important;'><br clear='both'/>";
			echo "<input name='csrf' type='hidden' value='".$csrf->get()."'>";
		echo '</form>';
	echo '</div>';
?>