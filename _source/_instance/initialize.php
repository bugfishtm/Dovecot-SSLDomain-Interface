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
	##########################################################################################################################################
	# DO NOT CHANGE SETTINGS BELOW!!!
	##########################################################################################################################################	
	
	/* Includes of Important Classes and Functions */	
	foreach (glob(_MAIN_PATH_."/_framework/functions/x_*.php") as $filename){require_once $filename;}
	foreach (glob(_MAIN_PATH_."/_framework/classes/x_*.php") as $filename){require_once $filename;}	
	
	/* Init x_class_debug Class */
	$debug = new x_class_debug();
	//var_dump( $debug->php_modules());
	$debug->required_php_module("curl", true);
	$debug->required_php_module("mysqli", true);
	$debug->required_php_module("gd", true);
	
	/* Constants with Website Table Names to be used  */	
	define('_TABLE_PREFIX_',  		"dci_");	
	define('_TABLE_USER_',   		_TABLE_PREFIX_."user");  
	define('_TABLE_USER_SESSION_',	_TABLE_PREFIX_."user_session");
	define('_TABLE_DOMAIN_',		_TABLE_PREFIX_."domain");
	define('_TABLE_IPBL_',			_TABLE_PREFIX_."ipblacklist");
	define('_TABLE_PERM_',			_TABLE_PREFIX_."perms");
	define('_TABLE_LOG_',			_TABLE_PREFIX_."log");	
	define('_TABLE_LOG_MYSQL_',		_TABLE_PREFIX_."mysql_log");	
	
	/* Rename dot.htaccess to .htaccess if Main Path is in Website Folder - Do Not Change */		
	if(@file_exists(_MAIN_PATH_."/dot.htaccess") AND @!file_exists(_MAIN_PATH_."/.htaccess")) { @rename(_MAIN_PATH_."/dot.htaccess", _MAIN_PATH_."/.htaccess"); }
	
	/* Settings for Captcha Generation  */	
	define('_CAPTCHA_FONT_',   	 _MAIN_PATH_."/_style/font_captcha.ttf");
	define('_CAPTCHA_WIDTH_',    "200"); 
	define('_CAPTCHA_HEIGHT_',   "70");	
	define('_CAPTCHA_SQUARES_',   mt_rand(4, 12));	
	define('_CAPTCHA_ELIPSE_',    mt_rand(4, 12));	
	define('_CAPTCHA_RANDOM_',    mt_rand(1000, 9999));		
	
	/* Init x_class_mysql Class */
	$mysql = new x_class_mysql(_SQL_HOST_, _SQL_USER_, _SQL_PASS_, _SQL_DB_);
	if ($mysql->lasterror != false) { $mysql->displayError(true); } 
	if(_MYSQL_LOGGING_) {	$mysql->log_config(_TABLE_LOG_MYSQL_, "log"); }	
	
	// Init x_class_ipbl IP Blacklist Class */	
	$ipbl = new x_class_ipbl($mysql, _TABLE_IPBL_, _IP_BLACKLIST_DAILY_OP_LIMIT_);	
	
	/* Init x_class_user Class */		
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
	
	# Some Variables
	define('_HELP_',    "https://bugfishtm.github.io/Dovecot-Certificate-Interface/");
	define("_FOOTER_", '<div id="footer">DCIv1.2 by <a href="https://bugfish.eu/aboutme" target="_blank" rel="noopeener">Bugfish</a> | <a href="'._IMPRESSUM_.'" target="_blank" rel="noopeener">Impressum</a> | <a href="'._HELP_.'" target="_blank" rel="noopeener">Help</a>');	
	
	/* Rebuild Table Structure */		
	$mysql->query("CREATE TABLE IF NOT EXISTS `"._TABLE_DOMAIN_."` (
		  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
		  `domain` varchar(512) NOT NULL COMMENT 'Related Domain Name',
		  `exclude` int(1) DEFAULT 0 COMMENT '1 - No Sync to Dovecot | 0 - Sync',
		  `status` int(1) DEFAULT 0 COMMENT '1 - Written and OK | 0 - Errors with Cert or Other File',
		  `fk_user` int(9) DEFAULT NULL COMMENT 'Owned User if Not Fetched Elsewhere',
		  `key` text DEFAULT NULL COMMENT 'Key File Location',
		  `cert` text DEFAULT NULL COMMENT 'Certificate Location',
		  `creation` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Domain Entry Date',
		  `last_update` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Domain Update Date',
		  `modification` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Entry Update Date',
		  PRIMARY KEY (`id`), UNIQUE KEY `domain` (`domain`) )"); $mysql->free_all();			