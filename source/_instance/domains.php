<?php
if(!$permsobj->hasPerm($user->user_id, "perm_domains") AND $user->user_rank != 0) { echo "<div class='content_box'>You do not have Permission!</div>"; } else {
if(isset($_POST["exec_edit"])) {
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
}

if(isset($_POST["exec_del"])) {
	if(is_numeric($_POST["exec_ref"])) {
			$mysql->query("DELETE FROM `"._TABLE_DOMAIN_."` WHERE id = \"".$_POST["exec_ref"]."\";");
			x_eventBoxPrep("Domain has been deleted!", "ok", _COOKIES_);
	} 
}
	
	
	
	echo '<div  class="content_box" style="max-width: 800px;text-align: center;"><a href="./?site=domains&edit=add" class="sysbutton">Add new Domain</a></div>';
		
		$curissue	=	mysqli_query($mysql->mysqlcon, "SELECT *	FROM "._TABLE_DOMAIN_."  ORDER BY id DESC");
		$run = false;
		while ($curissuer	=	mysqli_fetch_array($curissue) ) { 
		echo '<div class="content_box" style="text-align:left;">';
			echo '<div class="label_box">Domain: <b>'.@$curissuer["domain"].'</b></div>';
			echo '<div class="label_box">Cert: <b>'.@$curissuer["cert"].'</b></div>';
			echo '<div class="label_box">Key: <b>'.@$curissuer["key"].'</b></div> ';
			if(@$curissuer["status"] != 1) { $pxx = "<font color='red'>Cert or Keyfile not Found</font>"; } else { $pxx = "<font color='lime'>OK</font>";}
			if(@$curissuer["status"] == 2) { $pxx = "<font color='yellow'>Waiting for Cron</font>"; } 
			echo '<div class="label_box">Status: <b>'. $pxx.'</b></div> ';
			echo '<div class="label_box">Owner: <b>'.@dci_user_get_name_from_id($mysql, @$curissuer["fk_user"]).'</b></div> <br clear="left"/>';
			$run = true;	
			echo "<a class='sysbutton' href='./?site=domains&edit=".$curissuer["id"]."'>Edit</a> ";
			echo "<a class='sysbutton' href='./?site=domains&delete=".$curissuer["id"]."'>Delete</a> ";
echo "</div>";	
		}
		
		if(!$run) {echo '<div class="content_box">No data to display!</div>';}
?>	
<?php if(dci_domain_name_exists_id($mysql, @$_GET["edit"]) OR @$_GET["edit"] == "add") { 
		if(@$_GET["edit"] == "add") { $title = "Add new Domain"; } else { $title = "Edit Domain: ".dci_domain_get($mysql, $_GET["edit"])["id"]; } ?>
	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title"><?php echo $title; ?></div>		
			<form method="post" action="./?site=domains"><div class="internal_popup_content">			
				<input type="text" placeholder="Domain" name="domain" value="<?php echo @dci_domain_get($mysql, $_GET["edit"])["domain"]; ?>">
				<input type="text" placeholder="Cert Location" name="cert" value="<?php echo @dci_domain_get($mysql, $_GET["edit"])["cert"]; ?>">
				<input type="text" placeholder="Key Location" name="key" value="<?php echo @dci_domain_get($mysql, $_GET["edit"])["key"]; ?>">
				<?php if(is_numeric(@$_GET["edit"])) { ?><input type="hidden" value="<?php echo @$_GET["edit"]; ?>" name="exec_ref"><?php } ?>
			</div>		
			<div class="internal_popup_submit"><input type="submit" value="Execute" name="exec_edit"><a href="./?site=domains">Cancel</a></div></form>
		</div>
	</div>
<?php } ?>
<?php if(dci_domain_name_exists_id($mysql, @$_GET["delete"])) { ?>	
	<div class="internal_popup">
		<form method="post" action="./?site=domains"><div class="internal_popup_inner">
			<div class="internal_popup_title">Delete: <?php echo dci_domain_get($mysql, $_GET["delete"])["id"]; ?></div>
			<div class="internal_popup_submit"><input type="hidden" value="<?php echo @$_GET["delete"]; ?>" name="exec_ref"><input type="submit" value="Execute" name="exec_del"><a href="./?site=domains">Cancel</a></div>		
		</div></form>
	</div>
<?php }  } ?>