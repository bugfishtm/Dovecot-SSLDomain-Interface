<?php
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


