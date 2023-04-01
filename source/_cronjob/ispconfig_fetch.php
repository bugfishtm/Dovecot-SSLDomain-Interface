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
		
		

		
		internal_cronlog("START: Getting Folder List\r\n");
		internal_cronlog("INFO: '_CRON_ISP_FOLDER_SEARCH_' is set to '"._CRON_ISP_FOLDER_SEARCH_."' \r\n");
		internal_cronlog("INFO: We will search all folders for ISPConfig generated Certs! \r\n");
		$full_domain_ar	=	array();
		foreach (glob(_CRON_ISP_FOLDER_SEARCH_."/*") as $filename){ 
			$path = $filename;
			$domain = basename($filename);


			
			$realkey = false;
			$realkey_cert = _CRON_ISP_FOLDER_SEARCH_."/".$domain."/ssl/".$domain.".crt";
			$realkey_key = _CRON_ISP_FOLDER_SEARCH_."/".$domain."/ssl/".$domain.".key";
			
			if(file_exists($realkey_cert) AND file_exists($realkey_key)) {
				$realkey = true;
			}


			$le = false;
			$le_cert = _CRON_ISP_FOLDER_SEARCH_."/".$domain."/ssl/".$domain."-le.crt";
			$le_key = _CRON_ISP_FOLDER_SEARCH_."/".$domain."/ssl/".$domain."-le.key";

			if(file_exists($le_cert) AND file_exists($le_key)) {
				$le = true;
			}

			if($realkey) {
				
				
				if($x = dci_domain_name_exists($mysql, $domain)) {
					$mysql->query("UPDATE "._TABLE_DOMAIN_." SET cert = '".$mysql->escape($le_cert)."', `key` = '".$mysql->escape($le_key)."' WHERE id = '".$x."';", $bind);	
				} else {
					$mysql->query("INSERT INTO "._TABLE_DOMAIN_."(domain, cert, `key`, status) VALUES('".$mysql->escape($domain)."', '".$mysql->escape($le_cert)."', '".$mysql->escape($le_key)."', 2);");
				}					
				
				
				array_push($full_domain_ar, $domain);
				internal_cronlog("OK: Added Domain $domain! "); 
				internal_cronlog(" cert: $realkey_cert"); 
				internal_cronlog(" key: $realkey_key!\r\n"); 
			} elseif($le AND !$realkey) {

				if($x = dci_domain_name_exists($mysql, $domain)) {
					$mysql->query("UPDATE "._TABLE_DOMAIN_." SET cert = '".$mysql->escape($le_cert)."', `key` = '".$mysql->escape($le_key)."' WHERE id = '".$x."';", $bind);	
				} else {
					$mysql->query("INSERT INTO "._TABLE_DOMAIN_."(domain, cert, `key`, status) VALUES('".$mysql->escape($domain)."', '".$mysql->escape($le_cert)."', '".$mysql->escape($le_key)."', 2);");
				}					
				
				array_push($full_domain_ar, $domain);
				internal_cronlog("OK: Added Domain $domain "); 
				internal_cronlog(" cert: $le_cert "); 
				internal_cronlog(" key: $le_key!\r\n"); 
			} else {
				internal_cronlog("ERROR: Found no Cert and Key for $domain!\r\n"); 
			}
		}
		internal_cronlog("FINISHED: Last Operation!\r\n\r\n");	
		
	#########################################################
	internal_cronlog("START: Cleanup Domains not Found anymore...\r\n");
	$real_all_domains	= $mysql->select("SELECT * FROM "._TABLE_DOMAIN_."", true);
	if(is_array($real_all_domains)) {
		foreach($real_all_domains as $key => $value) {
			$deleteable = true;
			if(is_numeric($value["fk_user"])) { $deleteable = false; }
			if(is_array($full_domain_ar)) {
				foreach($full_domain_ar as $x => $y) {
					if(strtolower(trim($y)) == strtolower(trim($value["domain"]))) { $deleteable = false; }
				}
			}
			if($deleteable) {
				 internal_cronlog("OK: Deleted Domain: ".$value["domain"]."\r\n");
				 $mysql->query("DELETE FROM "._TABLE_DOMAIN_." WHERE id = '".$value["id"]."'"); 
			}
		}
	} 
	internal_cronlog("FINISHED: Last Operation!\r\n\r\n");			
		
		
		
		









	# --------------------------------------------------------------------------------------
	// Logfile Message
	internal_cronlog("OK: Execution Done at ".date("Y-m-d H:m:i")."");
	$log->info($log_output);
?>