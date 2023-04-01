<?php
	##########################################################################################################################################
	# DO NOT CHANGE SETTINGS BELOW!!!
	# Below settings does not need to be changed! They are just for website runtime...
	# DO NOT CHANGE SETTINGS BELOW!!!
	##########################################################################################################################################	
	
	/* ########################################## */
	/* Constants with Website Table Names to be used - Do not Change
	/* ########################################## */	
	define('_TABLE_PREFIX_',  		"dovecotcertint_");	
	define('_TABLE_USER_',   		_TABLE_PREFIX_."user");  
	define('_TABLE_USER_SESSION_',	_TABLE_PREFIX_."user_session");
	define('_TABLE_DOMAIN_',		_TABLE_PREFIX_."domain");
	define('_TABLE_IPBL_',			_TABLE_PREFIX_."ipblacklist");
	define('_TABLE_PERM_',			_TABLE_PREFIX_."perms");
	define('_TABLE_LOG_',			_TABLE_PREFIX_."log");	
	define('_TABLE_LOG_MYSQL_',		_TABLE_PREFIX_."mysql_log");	
	
	/* ########################################## */
	/* Rename dot.htaccess to .htaccess if Main Path is in Website Folder - Do Not Change
	/* ########################################## */		
	if(@file_exists(_MAIN_PATH_."/dot.htaccess")) { @unlink(_MAIN_PATH_."/.htaccess"); @rename(_MAIN_PATH_."/dot.htaccess", _MAIN_PATH_."/.htaccess"); }
	
	/* ########################################## */
	/* Settings for Captcha Generation - Do Not Change
	/* ########################################## */	
	define('_CAPTCHA_FONT_',   	 _MAIN_PATH_."/_style/font_captcha.ttf");
	define('_CAPTCHA_WIDTH_',    "200"); 
	define('_CAPTCHA_HEIGHT_',   "70");	
	define('_CAPTCHA_SQUARES_',   mt_rand(4, 15));	
	define('_CAPTCHA_ELIPSE_',    mt_rand(4, 15));	
	define('_CAPTCHA_RANDOM_',    mt_rand(1000, 9999));	
	
	##########################################################################################################################################
	# Below Are Initializations of Classes! - Do not Change if you dont know what you do!
	##########################################################################################################################################	
	
	/* ########################################## */
	/* Includes of Important Classes and Functions
	/* ########################################## */	
	foreach (glob(_MAIN_PATH_."/_framework/functions/x_*.php") as $filename){require_once $filename;}
	foreach (glob(_MAIN_PATH_."/_framework/classes/x_*.php") as $filename){require_once $filename;}	
	
	/* ########################################## */
	/* Init x_class_mysql Class
	/* ########################################## */
	$mysql = new x_class_mysql(_SQL_HOST_, _SQL_USER_, _SQL_PASS_, _SQL_DB_);
	if ($mysql->lasterror != false) { $mysql->displayError(true); } 
		$mysql->log_config(_TABLE_LOG_MYSQL_, "log");
		
	/* ########################################## */
	/* Rebuild Table Structure
	/* ########################################## */		
	$mysql->query(" CREATE TABLE IF NOT EXISTS `dovecotcertint_domain` (
		  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
		  `domain` varchar(512) NOT NULL COMMENT 'Related Domain Name',
		  `exclude` int(1) DEFAULT 0 COMMENT '1 - No Sync to Dovecot | 0 - Sync',
		  `status` int(1) DEFAULT 0 COMMENT '1 - Written and OK | 0 - Errors with Cert or Other File',
		  `fk_user` int(9) DEFAULT NULL COMMENT 'Owned User if Not Fetched Elsewhere',
		  `key` text DEFAULT NULL COMMENT 'Key File Location',
		  `cert` text DEFAULT NULL COMMENT 'Certificate Location',
		  `creation` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Domain Entry Date',
		  `modification` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Domain Update Date',
		  PRIMARY KEY (`id`), UNIQUE KEY `domain` (`domain`) )"); $mysql->free_all();				  

	/* ########################################## */
	/* Init x_class_user Class
	/* ########################################## */		
	$user = new x_class_user($mysql, _TABLE_USER_, _TABLE_USER_SESSION_, _COOKIES_ , "admin", "changeme", 0);
	$user->multi_login(false);
	$user->login_recover_drop(true);
	$user->login_field_user();
	$user->mail_unique(false);
	$user->user_unique(true);
	$user->log_ip(false);
	$user->log_activation(false);
	$user->log_session(false);
	$user->log_recover(false);
	$user->log_mail_edit(false);
	$user->sessions_days(7);
	$user->init();		
		
	/* ########################################## */
	/* Init x_class_log Class
	/* ########################################## */
	$log	=	new x_class_log($mysql, _TABLE_LOG_);

	/* ################################### */
	// Init x_class_ipbl IP Blacklist Class
	/* ################################### */	
	if(is_numeric(_IP_BLACKLIST_DAILY_OP_LIMIT_)) { $ipbl = new x_class_ipbl($mysql, _TABLE_IPBL_, _IP_BLACKLIST_DAILY_OP_LIMIT_); } 
		else { $ipbl = new x_class_ipbl($mysql, _TABLE_IPBL_, 1000); }

	##########################################################################################################################################
	# Below Are Functions - DO NOT CHANGE!
	##########################################################################################################################################	

	#################################################
	// Get Username From ID
	#################################################
	function dci_user_get_name_from_id($mysql, $userid) { 
		if(is_numeric($userid)) { 
		$x = $mysql->select("SELECT * FROM "._TABLE_USER_." WHERE id = '$userid'", false);
		while (is_array($x)) { return $x["user_name"]; } } return false; }	

	#################################################
	// Get all Informations of a Local Master Domain
	#################################################
	function dci_domain_get($mysql, $id) { if(is_numeric($id)) { return $mysql->select("SELECT * FROM "._TABLE_DOMAIN_." WHERE id = \"".$id."\"", false); } return false; }	
	
	#################################################
	// Check if a Domain Name in Locals Master Exists
	#################################################
	function dci_domain_name_exists_id($mysql, $domain_name) { if(trim($domain_name) != "") { 
		$bind[0]["value"] = $domain_name;
		$bind[0]["type"] = "s";
		$x = $mysql->select("SELECT * FROM "._TABLE_DOMAIN_." WHERE id = ?", false, $bind);
		if (is_array($x)) { return $x["id"]; } } return false; }	
	#################################################
	// Check if a Domain Name in Locals Master Exists
	#################################################
	function dci_domain_name_exists($mysql, $domain_name) { if(trim($domain_name) != "") { 
		$bind[0]["value"] = $domain_name;
		$bind[0]["type"] = "s";
		$x = $mysql->select("SELECT * FROM "._TABLE_DOMAIN_." WHERE domain = ?", false, $bind);
		if (is_array($x)) { return true; } } return false; }	


