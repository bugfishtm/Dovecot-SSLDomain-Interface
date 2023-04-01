<?php
	// Configurations Include
		require_once(dirname(__FILE__) ."/../settings.php");
		
	// Create needed Folders with Permissions
	$log_output = "";	
	function internal_cronlog($text) {
		global $log_output;
		$finaltext = $text;
		echo $text;
		while(strpos($finaltext, "\r\n") != false) { $finaltext = str_replace("\r\n", "<br />", $finaltext); }
		if(substr($finaltext, 0, 2) == "OK") { $finaltext = "<font color='lime'>".$finaltext."</font>"; }
		elseif(substr($finaltext, 0, 2) == "FI") { $finaltext = "<font color='yellow'>".$finaltext."</font>"; }
		elseif(substr($finaltext, 0, 2) == "ER") { $finaltext = "<font color='red'>".$finaltext."</font>"; }
		elseif(substr($finaltext, 0, 2) == "WA") { $finaltext = "<font color='red'>".$finaltext."</font>"; }
		elseif(substr($finaltext, 0, 2) == "IN") { $finaltext = "<font color='lightblue'>".$finaltext."</font>"; }
		elseif(substr($finaltext, 0, 2) == "ST") { $finaltext = "<font color='yellow'>".$finaltext."</font>"; }
		$log_output .= $finaltext;}

	# --------------------------------------------------------------------------------------
	internal_cronlog("START: Writing Dovecot Configuration File "._CRON_DOVECOT_FILE_."! \r\n");
	$domains = $mysql->select("SELECT * FROM "._TABLE_DOMAIN_." WHERE exclude = 0", true);
	if(is_array($domains)) {
		// Write the dnshttp.conf.local FILE
		$conf_buildstring = "";
		foreach($domains as $key => $value) {
			$validcert = true;
			$mysql->query("UPDATE "._TABLE_DOMAIN_." SET status = 2 WHERE id = \"".$value["id"]."\";");
			if(!file_exists(trim($value["cert"]))) { $mysql->query("UPDATE "._TABLE_DOMAIN_." SET status = 0 WHERE id = \"".$value["id"]."\";"); internal_cronlog("ERROR: ".trim($value["domain"])." with ERR_Cert:".trim($value["cert"])."\r\n"); continue; }
			if(!file_exists(trim($value["key"]))) { $mysql->query("UPDATE "._TABLE_DOMAIN_." SET status = 0 WHERE id = \"".$value["id"]."\";"); internal_cronlog("ERROR: ".trim($value["domain"])." with ERR_Key:".trim($value["key"])."\r\n"); continue; }
			
			if($validcert) { 
			$mysql->query("UPDATE "._TABLE_DOMAIN_." SET status = 1 WHERE id = \"".$value["id"]."\";");
			internal_cronlog("OK: ".trim($value["domain"])." with Ok_Cert:".trim($value["cert"])." AND Ok_Key:".trim($value["key"])."\r\n");
			$conf_buildstring .= '
			
local_name '.trim($value["domain"]).' {
	ssl_cert = <'.trim($value["cert"]).'
	ssl_key = <'.trim($value["key"]).'
}

';			
			}
		}	

		
		if(file_exists(_CRON_DOVECOT_FILE_)) { @unlink(_CRON_DOVECOT_FILE_); }
		file_put_contents(_CRON_DOVECOT_FILE_, $conf_buildstring);
	}
		
		
	internal_cronlog("FINISHED: LAST OPERATION\r\n\r\n"); 
	# --------------------------------------------------------------------------------------
		internal_cronlog("OK: systemctl restart dovecot;\r\n");
		@shell_exec("systemctl restart dovecot; ");
	
	internal_cronlog("FINISHED: LAST OPERATION\r\n\r\n"); 
	# --------------------------------------------------------------------------------------
	// Logfile Message
	internal_cronlog("OK: Execution Done at ".date("Y-m-d H:m:i")."");
	$log->info($log_output);
?>