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


