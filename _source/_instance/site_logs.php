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
if(!$permsobj->hasPerm($user->user_id, "logs") AND $user->user_rank != 0) { echo "<div class='content_box'>You do not have Permission!</div>"; } else {	
	echo '<div class="content_box">';
	echo '<span style="font-size: 22px;"><b>Logfiles [Limit 1000]</b></span><br /><hr><div style="text-align:left;">';
	echo "Here you can view the logfiles of the replication cronjob <b>(sync.php)</b> which handles all the dovecot configuration change executions. This file is useful to look what exactly happened at the Execution. You can see errors here and more. The entrie of interest is on the top, as it represents the latest execution of <b>sync.php</b>. More info on the \"<a href=\""._HELP_."\"  rel='noopener' target='_blank'>Help</a>\" page!<br />";
	$res	=	$mysql->select("SELECT * FROM "._TABLE_LOG_."  WHERE section <> 'blacklistreset' ORDER BY id DESC LIMIT 1000", true); 	
	if(is_array($res)) { 
		
		echo "<div style='max-width: 100%;'>";
			echo "<div style='width: 60%;float:left;'><b>Time</b></div>";
			echo "<div style='width: 20%;float:left;'><b>Type</b></div>";
			echo "<div style='width: 20%;float:left;'><b>Action</b></div>";
		echo '</div><br clear="left"/>';	
		
		echo '<style>.hoverdiv345:hover div{background: #363636 !important;}.hoverblackfontas:hover{color: black !important;}</style>';
		
		foreach ($res AS $key => $value) { 	
			//echo "<span style='font-size: 16px;'><b>Cronjob Output Details finished at: ".$value["creation"]."</b></span><br />";
			//echo "<br /> ".$value["message"]."<br /><br />";
			
			echo '<div class="hoverdiv345">';
			echo "<div style='width: 60%;float:left;padding-top: 5px; padding-bottom: 5px;'>".$value["creation"]."</div>";
			echo "<div style='width: 20%;float:left;padding-top: 5px; padding-bottom: 5px;'>".$value["section"]."</div>";
			echo "<div style='width: 20%;float:left;padding-top: 5px; padding-bottom: 5px;'><a class='sysbutton' href='./?site=logs&showcontent=".$value["id"]."'>Content</a></font></div>"; 				
			echo '</div>';
			echo '<br clear="left"/>';
		}
	} else { echo "No Data Available..."; }
	echo '</div></div>';
 
 if(is_numeric(@$_GET["showcontent"])) {
	 $asd = $mysql->select("SELECT * FROM  "._TABLE_LOG_." WHERE id = ".@$_GET["showcontent"]." AND section <> 'blacklistreset'");
 }

 if( $asd) { ?>	
	<div class="internal_popup">
		<div class="internal_popup_inner">
			<div class="internal_popup_title">CRONLOG: <?php echo htmlspecialchars($asd["creation"]); ?></div>
			
			<?php echo "<div style='text-align: left;font-size: 14px;'>".$asd["message"]."</div><br />";?>
			
			<div class="internal_popup_submit"><a class="hoverblackfontas" href="./?site=logs">Cancel</a></div>		
		</div>
	</div>
<?php } }?>
