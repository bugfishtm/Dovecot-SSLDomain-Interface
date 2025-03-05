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
		
	// Logging Related
	$log_output = "";			
	function internal_cronlog($text) {
		global $log_output;
		$finaltext = $text;
		echo $text;
		while(@strpos($finaltext, "\r\n") !== false) { $finaltext = @str_replace("\r\n", "<br />", $finaltext); }
		if(@substr($finaltext, 0, 2) == "OK") { $finaltext = "<font color='lime'>".$finaltext."</font>"; }
		elseif(@substr($finaltext, 0, 2) == "FI") { $finaltext = "<font color='yellow'>".$finaltext."</font>"; }
		elseif(@substr($finaltext, 0, 2) == "ER") { $finaltext = "<font color='red'>".$finaltext."</font>"; }
		elseif(@substr($finaltext, 0, 2) == "WA") { $finaltext = "<font color='red'>".$finaltext."</font>"; }
		elseif(@substr($finaltext, 0, 2) == "IN") { $finaltext = "<font color='lightblue'>".$finaltext."</font>"; }
		elseif(@substr($finaltext, 0, 2) == "ST") { $finaltext = "<font color='yellow'>".$finaltext."</font>"; }
		$log_output .= $finaltext;}		
		
	// Start Main Operation
		internal_cronlog("START: Getting Subfolder Names out of '"._CRON_ISP_FOLDER_SEARCH_."' and writing Folder Names as Domains to Database\r\n");
		internal_cronlog("INFO: At the same time, searching for valid letsencrypt or custom certificate! \r\n");
		internal_cronlog("INFO: Custom Certificate will be prefered, otherwhise letsencrypt cert, if it is existing. \r\n");
		internal_cronlog("INFO: If you want to use the letsencrypt certificate, with a custom certificate set, you have to delete the custom certificate in ispconfig!\r\n\r\n");

		$full_domain_ar	=	array();
		if(file_exists(_CRON_ISP_FOLDER_SEARCH_)) {
			foreach (glob(_CRON_ISP_FOLDER_SEARCH_."/*") as $filename){ 		
				$path = $filename;
				$domain = @basename($filename);		

				$realkey = false;
				$realkey_cert = _CRON_ISP_FOLDER_SEARCH_."/".$domain."/ssl/".$domain.".crt";
				$realkey_key = _CRON_ISP_FOLDER_SEARCH_."/".$domain."/ssl/".$domain.".key";		
				if(@file_exists($realkey_cert) AND @file_exists($realkey_key)) { $realkey = true; }		
			
				$le = false;
				$le_cert = _CRON_ISP_FOLDER_SEARCH_."/".$domain."/ssl/".$domain."-le.crt";
				$le_key = _CRON_ISP_FOLDER_SEARCH_."/".$domain."/ssl/".$domain."-le.key";
				if(file_exists($le_cert) AND file_exists($le_key)) { $le = true; }

				if($realkey) {			
					if($x = dci_domain_name_exists($mysql, $domain)) {
						$mysql->query("UPDATE "._TABLE_DOMAIN_." SET cert = '".$mysql->escape($le_cert)."', `key` = '".$mysql->escape($le_key)."' WHERE id = '".$x."';");	
					} else {
						$mysql->query("INSERT INTO "._TABLE_DOMAIN_."(domain, cert, `key`, status) VALUES('".$mysql->escape($domain)."', '".$mysql->escape($le_cert)."', '".$mysql->escape($le_key)."', 2);");
					}	
					@array_push($full_domain_ar, $domain);
					internal_cronlog("OK: Added Domain with Custom Certificate: $domain!\r\n"); 
				} elseif($le AND !$realkey) {
					if($x = dci_domain_name_exists($mysql, $domain)) {
						$mysql->query("UPDATE "._TABLE_DOMAIN_." SET cert = '".$mysql->escape($le_cert)."', `key` = '".$mysql->escape($le_key)."' WHERE id = '".$x."';");	
					} else {
						$mysql->query("INSERT INTO "._TABLE_DOMAIN_."(domain, cert, `key`, status) VALUES('".$mysql->escape($domain)."', '".$mysql->escape($le_cert)."', '".$mysql->escape($le_key)."', 2);");
					}					
					@array_push($full_domain_ar, $domain);
					internal_cronlog("OK: Added Domain with Letsencrypt Certificate: $domain!\r\n");
				} else { internal_cronlog("ERROR: Found no valid Certificate Custom or Letsencrypt for: $domain!\r\n"); }
			}
		} else { internal_cronlog("ERROR: Folder "._CRON_ISP_FOLDER_SEARCH_." not found to fetch list of subfolders!\r\n"); }
		
	#########################################################
	internal_cronlog("\r\nINFO: Now cleaning database from old entries not valid anymore...");
	internal_cronlog("\r\nINFO: This may will delete user created/edited entries...");
	internal_cronlog("\r\nINFO: Keep this in mind when using this cronjob for ispconfig fix...!\r\n");
	$real_all_domains	= $mysql->select("SELECT * FROM "._TABLE_DOMAIN_."", true);
	if(is_array($real_all_domains)) {
		foreach($real_all_domains as $key => $value) {
			$deleteable = true;
			if(is_array($full_domain_ar)) {
				foreach($full_domain_ar as $x => $y) {
					if(@strtolower(trim($y)) == @strtolower(trim($value["domain"]))) { $deleteable = false; }
				}
			}
			if($deleteable) {
				 internal_cronlog("OK: Deleted Mail Certificate Domain: ".$value["domain"]."\r\n");
				 $mysql->query("DELETE FROM "._TABLE_DOMAIN_." WHERE id = '".$value["id"]."'"); 
			}
		}
	} 

	# --------------------------------------------------------------------------------------
	// Logfile Message
	internal_cronlog("\r\nFINISHED: Execution Done at ".date("Y-m-d H:m:i")." \r\n\r\n");
	$log	=	new x_class_log($mysql, _TABLE_LOG_, "ispconfig_fetch");
	$log->info($log_output);
?>