<?php
	/*
		__________              _____.__       .__     
		\______   \__ __  _____/ ____\__| _____|  |__  
		 |    |  _/  |  \/ ___\   __\|  |/  ___/  |  \ 
		 |    |   \  |  / /_/  >  |  |  |\___ \|   Y  \
		 |______  /____/\___  /|__|  |__/____  >___|  /
				\/     /_____/               \/     \/  Dovecot Certificate Interface (DCI) Configuration File */
	/* ########################################## */
	/* Website Setup
	/* ########################################## */				
	define("_TITLE_", 				"TITLE"); # A Imaginary Server Name to show at Title, can be unchanged	 		
	define("_IMPRESSUM_",			"IMPRESSUMLINK"); # URL to your Impressum Website	
				
	/* Database Setup */				
	define("_SQL_HOST_", 			"DBHOST"); # Mysql Hostname
	define("_SQL_USER_", 			"DBUSER"); # Mysqsl User
	define("_SQL_PASS_", 			"DBPASS"); # MysQL Password
	define("_SQL_DB_", 				"DBNAME"); # MySQL Database				
				
	/* Site Security Setup # Can be left unchanged	 */				
	define("_IP_BLACKLIST_DAILY_OP_LIMIT_", 1000); # Define Blacklist Limit for IP Bans (1000 Recommended) // Can be reseted via daily cronjob.  Can be left unchanged
	define("_CSRF_VALID_LIMIT_TIME_", 	1000); # Define Time for CSRF Validation	(1000 Recommended)	 # Can be left unchanged				
	
	/* Activate MySQL Debugging Area for Developers? */
	define("_MYSQL_LOGGING_", 		false); # Can be left unchanged!
	define("_COOKIES_",     		"dci_"); # Cookie Prefix // Can be unchanged
	
	##########################################################################################################################################
	# File with Configuration to be Written (See Readme for Changes in dovecot.conf!) - Can be left unchanged!
	# This file needs to be included in the dovecot.conf!
	# See readme for more informations about this in the "installation" section
	##########################################################################################################################################
	define("_CRON_DOVECOT_FILE_", 				"/etc/dovecot/dci.certs.conf"); # Can be löeft unchanged	in the normal case!

	##########################################################################################################################################
	# IF YOU USE ISP CONFIG THIS VARIABLE IS FOR THE PATH OF THE WWW FOLDERS
	# This is meant to be a fix for ispconfig which failes to create a named.conf.local file in some cases
	# with this script you can read a folders subfolder (the website folders ispconfig creates)
	# and search them for ssl certs, they will than be added to the database
	# this makes ssl certificates per domain working on ispconfig, without further action of an administrator (automated)
	# This can be left unchanged. (Does not act if cronjob ispconfig_fetch.php is inactive || this is the default ispconfig clients data folder)
	##########################################################################################################################################
	define("_CRON_ISP_FOLDER_SEARCH_", "/var/www/"); # Can be left unchanged / for ispconfig_fetch.php cronjob in _cronjob folder
	
	##########################################################################################################################################
	## DO NOT CHANGE BELOW!
	##########################################################################################################################################
	
	## Determine Document Root - Leave unchanged!
	$current_dir = dirname(__FILE__);
	if(!file_exists($current_dir."/settings.php")) { $current_dir = $current_dir."/../";}
	if(!file_exists($current_dir."/settings.php")) { $current_dir = $current_dir."../";}
	if(!file_exists($current_dir."/settings.php")) { $current_dir = $current_dir."../";}
	if(!file_exists($current_dir."/settings.php")) { echo "No settings.php found!<br />Please change settings.sample.php and rename this file to settings.php after that!"; exit(); }
	define('_MAIN_PATH_', $current_dir);
	
	## Include Functions File - Do not Change!
	require_once(_MAIN_PATH_."/_instance/library.php");
	require_once(_MAIN_PATH_."/_instance/initialize.php");