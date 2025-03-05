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
if(!$permsobj->hasPerm($user->user_id, "debug") AND $user->user_rank != 0) { echo "<div class='content_box'>You do not have Permission!</div>"; } else {	
	echo '<div class="content_box">';
	echo '<span style="font-size: 22px;"><b>MySQL Execution Errors [Limit 1000]</b></span><br />Here you may see SQL Errors, <b>This area is only for developers</b>!<br />Dont panic if you see a lot of errors here, its okay, the file you need to take an eye on are the replication logs/protocols in the "<a href="./?site=logs">Logs</a>" section! Besides that you maybe find useful information at the "<a href="'._HELP_.'"  rel="noopener" target="_blank">Help</a>" section...<br /><div style="text-align:left;">';
	$res	=	$mysql->select("SELECT * FROM "._TABLE_LOG_MYSQL_."  ORDER BY id DESC LIMIT 1000", true); 	
		echo '<style>.hoverdiv345:hover div{background: #363636 !important;}</style>';
		echo '<style>.hoverdiv345:hover{background: #363636 !important;}</style>';
	if(is_array($res)) { 
		foreach ($res AS $key => $value) { 	
			echo "<hr>";
			echo '<div class="hoverdiv345">';
			echo "<font color='red'>Error at: ".$value["creation"]."</font><br />";
			echo "Initial Query<br />";
			echo "<textarea style='width: 100%;max-width:100%;background: rgba(0,0,0,0.9); color: white; width: 100%;max-width: 100%;min-width: 100%;min-height: 20px;' readonly>".$value["init"]." </textarea><br />";
			echo "Exception<br />";
			echo "<textarea style='width: 100%;max-width:100%;background: rgba(0,0,0,0.9); color: white; width: 100%;max-width: 100%;min-width: 100%;min-height: 20px;' readonly>".$value["exception"]."</textarea><br />";
			echo "SQL Error<br />";
			echo "<textarea style='width: 100%;max-width:100%;background: rgba(0,0,0,0.9); color: white; width: 100%;max-width: 100%;min-width: 100%;min-height: 20px;' readonly>".$value["sqlerror"]."</textarea><br />";
			echo '</div>';
			echo '<br clear="left"/>';
		}
	} else { echo "No Data Available..."; }
	echo '</div></div>';
 }
?>	