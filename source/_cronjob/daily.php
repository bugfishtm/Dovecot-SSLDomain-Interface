<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  Daily Cronjob to Reset IP BLacklist */
	// Configurations Include
		require_once(dirname(__FILE__) ."/../settings.php");

	// Delete IP Blacklist Table Entries 
		$mysql->query("DELETE FROM "._TABLE_IPBL_." ");
		
	// Output Message
		echo "IP Blacklist has been cleared!\r\n\r\n";
	
	// Output Message
		$log_ip	=	new x_class_log($mysql, _TABLE_LOG_, "blacklistreset");
		$log_ip->info("IP Blacklist has been cleared!");
?>