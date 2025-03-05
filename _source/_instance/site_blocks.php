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
if(!$permsobj->hasPerm($user->user_id, "blocklist") AND $user->user_rank != 0) { echo "<div class='content_box'>You do not have Permission!</div>"; } else {	
if(is_numeric($_GET["deleteid"])) {
	if($csrf->check($_GET['csrf'])) {
		$mysql->query("DELETE FROM "._TABLE_IPBL_." WHERE id = \"".$_GET["deleteid"]."\";");
		x_eventBoxPrep("The IP-Adress has been released!", "ok", _COOKIES_);
	}else { x_eventBoxPrep("CSRF Error - Try Again!", "error", _COOKIES_); }
}
	echo '<div class="content_box">';
	echo '<span style="font-size: 22px;"><b>IP Blocklist</b><br /></span>The IPs below are blocked from interaction with this webinterface. This includes all kind of API Requests. The table will be cleared, every time the daily cronjob is running!<br /><br />You need to have set up the <b>./_cronjob/daily.php</b> to run <b>every 24 hours</b>, otherwhise this IP-Blocks wont be resetted. You can change the Counter for Block Operations in the <b>settings.php file</b>. The setting is named <b>"_IP_BLACKLIST_DAILY_OP_LIMIT_"</b>. The current setting is : <b>'._IP_BLACKLIST_DAILY_OP_LIMIT_.'</b>. IP-Adresses will be blocked after that certain amount of token or login failures! More information about how to set the cronjob for automatically clearing that list everyday in the "<a href="'._HELP_.'" rel="noopener" target="_blank">Help</a>" section!<br /><br />Blocked IPs will be marked red in this list! You can see the cronjob log to reset this table <a href="#controlcron">here</a>.<hr>';	
	$res = $mysql->select("SELECT * FROM "._TABLE_IPBL_." ORDER BY id DESC", true); 
	if(is_array($res)) { 
		echo "<div style='max-width: 100%;'>";
			echo "<div style='width: 50%;float:left;'><b>IP-Address</b></div>";
			echo "<div style='width: 20%;float:left;'><b>Failure-Count</b></div>";
			echo "<div style='width: 20%;float:left;'><b>Limit</b></div>";
			echo "<div style='width: 10%;float:left;'><b>Delete</b></div>";
		echo '</div><br clear="left"/>';	
		echo '<style>.hoverdiv345:hover div{background: #363636 !important;}</style>';
		//echo '<style>.hoverdiv345:hover{background: #363636 !important;}</style>';	
		foreach ($res AS $key => $value) { 	
			echo '<div class="hoverdiv345">';
			echo "<div style='max-width: 100%;'>";
			if($value["fail"] <= _IP_BLACKLIST_DAILY_OP_LIMIT_) { 	echo "<div style='width: 50%;float:left;'>".$value["ip_adr"]."</div>";
			echo "<div style='width: 20%;float:left;'>".$value["fail"]."</div>"; echo "<div style='width: 20%;float:left;'>"._IP_BLACKLIST_DAILY_OP_LIMIT_."</div>"; 	echo "<div style='width: 10%;float:left;'><a href='./?site=blocks&deleteid=".$value["id"]."&csrf=".$csrf->get()."' class='sysbutton' >Delete</a></div>"; 				}
			else {
				echo "<div style='width: 50%;float:left;'><font color='red'>".$value["ip_adr"]."</font></div>";
				echo "<div style='width: 20%;float:left;'><font color='red'>".$value["fail"]."</font></div>"; 				
				echo "<div style='width: 20%;float:left;'>"._IP_BLACKLIST_DAILY_OP_LIMIT_."</div>"; 				
				echo "<div style='width: 10%;float:left;'><a href='./?site=blocks&deleteid=".$value["id"]."&csrf=".$csrf->get()."' class='sysbutton' >Delete</a></div>"; 				

			}
			echo '</div>';
			echo '</div><br clear="left"/>';
		}
	} else { echo "No Data Available..."; }
	echo '</div>';
	
	
	echo '<div class="content_box" id="controlcron">';
	echo '<span style="font-size: 22px;"><b>Daily.php Cronjob Log [Limit 500]</b></span><br /><hr><div style="text-align:left;">';
	echo "Here you can view the logfiles of the ip blacklist reset cronjob <b>(daily.php)</b> which will clear this database to allow ip's access again after 24 hours.<br />";
	$res	=	$mysql->select("SELECT * FROM "._TABLE_LOG_."  WHERE section = 'blacklistreset' ORDER BY id DESC LIMIT 500", true); 	
	if(is_array($res)) { 
		
		echo "<div style='max-width: 100%;'>";
			echo "<div style='width: 60%;float:left;'><b>Time</b></div>";
			echo "<div style='width: 40%;float:left;'><b>Action</b></div>";
		echo '</div><br clear="left"/>';	
		
		echo '<style>.hoverdiv345:hover div{background: #363636 !important;}.hoverblackfontas:hover{color: black !important;}</style>';
		
		foreach ($res AS $key => $value) { 	
			//echo "<span style='font-size: 16px;'><b>Cronjob Output Details finished at: ".$value["creation"]."</b></span><br />";
			//echo "<br /> ".$value["message"]."<br /><br />";
			
			echo '<div class="hoverdiv345">';
			echo "<div style='width: 60%;float:left;padding-top: 5px; padding-bottom: 5px;'>".$value["creation"]."</div>";
			echo "<div style='width: 40%;float:left;padding-top: 5px; padding-bottom: 5px;'><a class='sysbutton' href='./?site=blocks&showcontent=".$value["id"]."'>Content</a></font></div>"; 				
			echo '</div>';
			echo '<br clear="left"/>';
		}
	} else { echo "No Data Available..."; }
	echo '</div></div>';
 
 if(is_numeric(@$_GET["showcontent"])) {
	 $asd = $mysql->select("SELECT * FROM  "._TABLE_LOG_." WHERE id = ".@$_GET["showcontent"]." AND section='blacklistreset'");
 }

 if( @$asd) { ?>	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title">DATE: <?php echo htmlspecialchars($asd["creation"]); ?></div>
			
			<?php echo "<div style='text-align: left;font-size: 14px;'>".$asd["message"]."</div><br />";?>
			
			<div class="internal_popup_submit"><a class="hoverblackfontas" href="./?site=blocks">Cancel</a></div>		
		</div>
	</div>
 <?php }
	
 }
?>	