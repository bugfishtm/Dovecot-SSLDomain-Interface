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
if(!$permsobj->hasPerm($user->user_id, "domainmgr") AND $user->user_rank != 0) { echo "<div class='content_box'>You do not have Permission!</div>"; } else {
if(isset($_POST["exec_edit"])) {
	if(!$csrf->check($_POST["csrf"])) { x_eventBoxPrep("CSRF Error - Try again!", "error", _COOKIES_); goto endofex; }
	if(trim(@$_POST["domain"]) != "" AND trim(@$_POST["cert"]) != "" AND trim(@$_POST["key"]) != "") {
		if(is_numeric(@$_POST["exec_ref"])) {
			$mysql->query("UPDATE "._TABLE_DOMAIN_." SET domain = '".$mysql->escape(trim($_POST["domain"]))."' WHERE id = \"".$_POST["exec_ref"]."\";");
			$mysql->query("UPDATE "._TABLE_DOMAIN_." SET cert = '".$mysql->escape(trim($_POST["cert"]))."' WHERE id = \"".$_POST["exec_ref"]."\";");
			$mysql->query("UPDATE "._TABLE_DOMAIN_." SET `key` = '".$mysql->escape(trim($_POST["key"]))."' WHERE id = \"".$_POST["exec_ref"]."\";");
			$mysql->query("UPDATE "._TABLE_DOMAIN_." SET status = 2 WHERE id = \"".$_POST["exec_ref"]."\";");
			x_eventBoxPrep("Domain has been updated!", "ok", _COOKIES_);	
		} else {											
			$mysql->query("INSERT INTO "._TABLE_DOMAIN_." (domain, cert, `key`, status, fk_user) 
														VALUES (\"".$mysql->escape(trim($_POST["domain"]))."\"
														, '".$mysql->escape(trim($_POST["cert"]))."'
														, '".$mysql->escape(trim($_POST["key"]))."'
														, 2
														, '".$user->user_id."');");
			x_eventBoxPrep("Domain has been added!", "ok", _COOKIES_);
		}
	} else { x_eventBoxPrep("Error in submitted data!", "error", _COOKIES_);  }
}endofex:

if(isset($_POST["exec_del"])) {
	if(is_numeric($_POST["exec_ref"])) {
			if(!$csrf->check($_POST["csrf"])) { x_eventBoxPrep("CSRF Error - Try again!", "error", _COOKIES_); goto endofexasd; }
			$mysql->query("DELETE FROM `"._TABLE_DOMAIN_."` WHERE id = \"".$_POST["exec_ref"]."\";");
			x_eventBoxPrep("Domain has been deleted!", "ok", _COOKIES_);
	} 
}endofexasd:
	
if(isset($_GET["edityes"])) {
	if(is_numeric($_GET["edityes"])) {
			if(!$csrf->check($_GET["csrf"])) { x_eventBoxPrep("CSRF Error - Try again!", "error", _COOKIES_); goto endoasdasdfexasd; }
			$mysql->query("UPDATE `"._TABLE_DOMAIN_."` SET exclude = 0 WHERE id = \"".$_GET["edityes"]."\";");
			x_eventBoxPrep("Domain has been enabled!", "ok", _COOKIES_);
	} 
}endoasdasdfexasd:

if(isset($_GET["editno"])) {
	if(is_numeric($_GET["editno"])) {
			if(!$csrf->check($_GET["csrf"])) { x_eventBoxPrep("CSRF Error - Try again!", "error", _COOKIES_); goto endodfafeexasd; }
			$mysql->query("UPDATE `"._TABLE_DOMAIN_."` SET exclude = 1 WHERE id = \"".$_GET["editno"]."\";");
			x_eventBoxPrep("Domain has been disabled!", "ok", _COOKIES_);
	} 
}endodfafeexasd:	
	
	echo '<div  class="content_box" style="max-width: 800px;text-align: center;"><a href="./?site=domains&edit=add" class="sysbutton">Add new Domain</a><br />The domains added here will be written to the dovecot configuration file to enable per domain ssl-certificates. The domains will be deep-checked for validation before adding to the configuration. The modulus of the certs will be checked if they equal between the cert and key, and only then the mail domain will be added to dovecot. Is the status is OK - the Domain is written to the dovecot configuration file. Otherwhise there is an error with the key or cert file...<br /><br />If you create or edit domains here, your changes may will be overwritten if you use the ispconfig-fetch cronjob, which acts as a service to add domains out of ispconfig automatically here! You can read more about this in the "<a href="'._HELP_.'" rel="noopener" target="_blank">Help</a>" section!<br /></div>';
		
		$curissue	=	mysqli_query($mysql->mysqlcon, "SELECT *	FROM "._TABLE_DOMAIN_."  ORDER BY id DESC");
		$run = false;
		while ($curissuer	=	mysqli_fetch_array($curissue) ) { 
		echo '<div class="content_box" style="text-align:left;">';
		$usercur = @dci_user_get_name_from_id($mysql, @$curissuer["fk_user"]);
		if(!is_numeric($usercur)) { $usercur = "System"; }
			echo '<div class="label_box">Domain: <b>'.@$curissuer["domain"].'</b></div>';
			echo '<div class="label_box">Cert: <b>'.@$curissuer["cert"].'</b></div>';
			echo '<div class="label_box">Key: <b>'.@$curissuer["key"].'</b></div> ';
			if(@$curissuer["status"] != 1) { $pxx = "<font color='red'>Cert or Keyfile not Found</font>"; } else { $pxx = "<font color='lime'>OK</font>";}
			if(@$curissuer["exclude"] == 1) { echo '<div class="label_box" style="background: red !important;color: white;">Enabled: <b>No</b></div> '; } else { echo '<div class="label_box" style="background: lime !important;color: black;">Enabled: <b>Yes</b></div> ';}
			if(@$curissuer["status"] == 2) { $pxx = "<font color='yellow'>Waiting for Cron</font>"; } 
			echo '<div class="label_box">Status: <b>'. $pxx.'</b></div> ';
			echo '<div class="label_box">Owner: <b>'.$usercur.'</b></div> <br clear="left"/>';
			$run = true;	
			if(@$curissuer["exclude"] == 1) { echo "<a class='sysbutton' href='./?site=domains&edityes=".$curissuer["id"]."&csrf=".$csrf->get()."'>Enable</a> "; } else { echo "<a class='sysbutton' href='./?site=domains&editno=".$curissuer["id"]."&csrf=".$csrf->get()."'>Disable</a> ";}
			echo "<a class='sysbutton' href='./?site=domains&edit=".$curissuer["id"]."'>Edit</a> ";
			echo "<a class='sysbutton' href='./?site=domains&delete=".$curissuer["id"]."'>Delete</a> ";
echo "</div>";	
		}
		
		if(!$run) {echo '<div class="content_box">No data to display!</div>';}
?>	
<?php if(@dci_domain_name_exists_id($mysql, @$_GET["edit"]) OR @$_GET["edit"] == "add") { 
		if(@$_GET["edit"] == "add") { $title = "Add new Domain"; } else { $title = "Edit Domain: ".@dci_domain_get($mysql, $_GET["edit"])["id"]; } ?>
	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title"><?php echo $title; ?></div>		
			<form method="post" action="./?site=domains"><div class="internal_popup_content">			
				<input type="text" placeholder="Domain" name="domain" value="<?php echo @dci_domain_get($mysql, $_GET["edit"])["domain"]; ?>">
				<input type="text" placeholder="Cert Location" name="cert" value="<?php echo @dci_domain_get($mysql, $_GET["edit"])["cert"]; ?>">
				<input type="text" placeholder="Key Location" name="key" value="<?php echo @dci_domain_get($mysql, $_GET["edit"])["key"]; ?>">
				<?php if(is_numeric(@$_GET["edit"])) { ?><input type="hidden" value="<?php echo @$_GET["edit"]; ?>" name="exec_ref"><?php } ?>
			</div>		
			<div class="internal_popup_submit"><input type="hidden" value="<?php echo @$csrf->get(); ?>" name="csrf"><input type="submit" value="Execute" name="exec_edit"><a href="./?site=domains">Cancel</a></div></form>
		</div>
	</div>
<?php } ?>
<?php if(@dci_domain_name_exists_id($mysql, @$_GET["delete"])) { ?>	
	<div class="internal_popup">
		<form method="post" action="./?site=domains"><div class="internal_popup_inner">
			<div class="internal_popup_title">Delete: <?php echo @dci_domain_get($mysql, $_GET["delete"])["id"]; ?></div>
			<div class="internal_popup_content">Do you want to delete this domain for dovecot configuration? It may gets re-fetched if it has been auto-added by the ispconfig-fetch cronjob!</div>
			<div class="internal_popup_submit"><input type="hidden" value="<?php echo @$csrf->get(); ?>" name="csrf"><input type="hidden" value="<?php echo @$_GET["delete"]; ?>" name="exec_ref"><input type="submit" value="Execute" name="exec_del"><a href="./?site=domains">Cancel</a></div>		
		</div></form>
	</div>
<?php }  } ?>