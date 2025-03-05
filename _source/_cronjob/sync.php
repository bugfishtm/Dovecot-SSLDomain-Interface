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
	// Configurations Include
		require_once(dirname(__FILE__) ."/../settings.php");
		
	// Create needed Folders with Permissions
	$log_output = "";	
	function internal_cronlog($text) {
		global $log_output;
		$finaltext = $text;
		echo $text;
		while(strpos($finaltext, "\r\n") !== false) { $finaltext = str_replace("\r\n", "<br />", $finaltext); }
		if(substr($finaltext, 0, 2) == "OK") { $finaltext = "<font color='lime'>".$finaltext."</font>"; }
		elseif(substr($finaltext, 0, 2) == "FI") { $finaltext = "<font color='yellow'>".$finaltext."</font>"; }
		elseif(substr($finaltext, 0, 2) == "ER") { $finaltext = "<font color='red'>".$finaltext."</font>"; }
		elseif(substr($finaltext, 0, 2) == "WA") { $finaltext = "<font color='red'>".$finaltext."</font>"; }
		elseif(substr($finaltext, 0, 2) == "IN") { $finaltext = "<font color='lightblue'>".$finaltext."</font>"; }
		elseif(substr($finaltext, 0, 2) == "ST") { $finaltext = "<font color='yellow'>".$finaltext."</font>"; }
		$log_output .= $finaltext;}

	# --------------------------------------------------------------------------------------
	internal_cronlog("START: Writing Dovecot Configuration File "._CRON_DOVECOT_FILE_."! \r\n");
	$domains = $mysql->select("SELECT * FROM "._TABLE_DOMAIN_."", true);
	if(is_array($domains)) {
		$conf_buildstring = "";
		foreach($domains as $key => $value) {
			if($value["exclude"] == 1) { $mysql->query("UPDATE "._TABLE_DOMAIN_." SET status = 1 WHERE id = \"".$value["id"]."\";"); internal_cronlog("WARN: Domain is disabled: ".trim($value["domain"])." \r\n"); continue; }			
			$validcert = true;
			$mysql->query("UPDATE "._TABLE_DOMAIN_." SET status = 2 WHERE id = \"".$value["id"]."\";");
			if(!file_exists(trim($value["cert"]))) { $mysql->query("UPDATE "._TABLE_DOMAIN_." SET status = 0 WHERE id = \"".$value["id"]."\";"); internal_cronlog("ERROR: Certificate File not Found for ".trim($value["domain"])." at ".trim($value["cert"])." \r\n"); continue; }
			if(!file_exists(trim($value["key"]))) { $mysql->query("UPDATE "._TABLE_DOMAIN_." SET status = 0 WHERE id = \"".$value["id"]."\";"); internal_cronlog("ERROR: Key File not Found for ".trim($value["domain"])." at ".trim($value["key"])." \r\n"); continue; }
			
			$mod_key =  @shell_exec("openssl rsa -modulus -noout -in ".$value["key"]." | openssl md5");
			$mod_key =  substr($mod_key, strpos($mod_key, "=") +1);
			$mod_key =  trim($mod_key);	
			
			$mod_crt =  @shell_exec("openssl x509  -modulus -noout -in ".$value["cert"]." | openssl md5");
			$mod_crt =  substr($mod_crt, strpos($mod_crt, "=") +1);		
			$mod_crt =  trim($mod_crt);		
			
			if(!is_string($mod_crt) OR !is_string($mod_key) OR $mod_crt != $mod_key) {
				internal_cronlog("ERROR: SSL Cert and Key Validation Check ".trim($value["domain"])." \r\n"); continue;
			}
			
			//if() {
			//}
			if($validcert) { 
				$mysql->query("UPDATE "._TABLE_DOMAIN_." SET status = 1 WHERE id = \"".$value["id"]."\";");
				internal_cronlog("OK: Valid Domain added:".trim($value["domain"])."\r\n");
				$conf_buildstring .= "\r\nlocal_name ".trim($value["domain"])." {\r\n\tssl_cert = <".trim($value["cert"])."\r\n\tssl_key = <".trim($value["key"])."\r\n}\r\n\r\n";	
			}
		}
		if(file_exists(_CRON_DOVECOT_FILE_)) { @unlink(_CRON_DOVECOT_FILE_); }
		file_put_contents(_CRON_DOVECOT_FILE_, $conf_buildstring);
		internal_cronlog("INFO: Dovecot Config File Creation of "._CRON_DOVECOT_FILE_." done!\r\n");	
	} else {
		internal_cronlog("INFO: No Domains Found in Database, Writing Empty File\r\n");
		if(file_exists(_CRON_DOVECOT_FILE_)) { @unlink(_CRON_DOVECOT_FILE_); }
		file_put_contents(_CRON_DOVECOT_FILE_, "");
		internal_cronlog("INFO: Cleanup Done!\r\n");		
	}
		
	# --------------------------------------------------------------------------------------
	internal_cronlog("INFO: Executing Commmand: systemctl restart dovecot;\r\n");
	echo @shell_exec("systemctl restart dovecot; ");
	# --------------------------------------------------------------------------------------
	// Logfile Message
	$log	=	new x_class_log($mysql, _TABLE_LOG_, "sync");
	internal_cronlog("OK: Execution Done at ".date("Y-m-d H:m:i")."\r\n\r\n");
	$log->info($log_output);
?>