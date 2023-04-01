<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  Bind9 Web Manager Configuration File */
	##########################################################################################################################################
	# Below are settings which may be changed to adjust the configuration and enter mysql authentication info!
	##########################################################################################################################################
	/* ########################################## */
	/* Website Setup
	/* ########################################## */				
	define("_TITLE_", 				"TITLE"); # A Imaginary Server Name to show at Title, can be unchanged	 		
	define("_COOKIES_",     		"dovecotcertint_"); # Cookie Prefix // Can be unchanged			
	define("_MAIN_PATH_",			"/var/www/html/"); # Main Document root for website! Needs to be corrent for website to run!		
				
	/* ########################################## */
	/* Database Setup
	/* ########################################## */				
	define("_SQL_HOST_", 			"DBHOST"); # Mysql Hostname
	define("_SQL_USER_", 			"DBUSER"); # Mysqsl User
	define("_SQL_PASS_", 			"DBPASS"); # MysQL Password
	define("_SQL_DB_", 				"DBNAME"); # MySQL Database				
				
	/* ########################################## */
	/* Site Security Setup
	/* ########################################## */				
	define("_IP_BLACKLIST_DAILY_OP_LIMIT_", 500); # Define Blacklist Limit for IP Bans (500 Recommended) // Can be reseted via daily cronjob.
	define("_CSRF_VALID_LIMIT_TIME_", 	500); # Define Time for CSRF Validation	(500 Recommended)	 # Can be left unchanged				
				
	##########################################################################################################################################
	# File with Configuration to be Written (See Readme for Changes in dovecot.conf!)
	##########################################################################################################################################
	define("_CRON_DOVECOT_FILE_", 				"/etc/dovecot/dovecotcertinterface.certs.conf"); # Can be löeft unchanged	in the normal case!

	##########################################################################################################################################
	# IF YOU USE ISP CONFIG THIS VARIABLE IS FOR THE PATH OF THE WWW FOLDERS
	##########################################################################################################################################
	define("_CRON_ISP_FOLDER_SEARCH_", "/var/www/"); # Can be left unchanged
	
	/* ########################################################################################################################################## */
	/* DO NOT CHANGE THE VALUES BELOW!
	/* ########################################################################################################################################## */
	## Include Functions File - Do not Change!
	require_once(_MAIN_PATH_."/functions.php");
