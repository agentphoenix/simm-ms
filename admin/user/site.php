<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/user/site.php
Purpose: Page that allows a user to various site options

System Version: 2.5.0
Last Modified: 2007-07-10 1103 EST
**/

/* access check */
if( in_array( "u_options", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "user";
	$action = $_GET['action'];
	$options = $_POST['options_x'];
	
	/* explode the strings */
	$allowedRanksArray = explode( ",", $allowedRanks );
	$allowedSkinsArray = explode( ",", $allowedSkins );
	
	/* create a temporary array for the SQL injection check */
	foreach( $allowedSkinsArray as $key => $value ) {
		$skinsArray[] = trim( $value );
	}
	
	/* create a temporary array for the SQL injection check */
	foreach( $allowedRanksArray as $key => $value ) {
		$ranksArray[] = trim( $value );
	}
	
	if( !empty( $_GET['changerank'] ) && !in_array( $_GET['changerank'], $ranksArray ) ) {
		errorMessage( "site options" );
		exit();
	} else {
		/* set the GET variable */
		$changerank = $_GET['changerank'];
	}
	
	if( !empty( $_GET['changeskin'] ) && !in_array( $_GET['changeskin'], $skinsArray ) ) {
		errorMessage( "site options" );
		exit();
	} else {
		/* set the GET variable */
		$changeskin = $_GET['changeskin'];
	}
	
	if( $action == "skin" ) {
		
		/* do the update query */
		$query = "UPDATE sms_crew SET displaySkin = '$changeskin' WHERE crewid = '$sessionCrewid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
		
		/* set a new session variable */
		$_SESSION['sessionDisplaySkin'] = $changeskin;
		$sessionDisplaySkin = $_SESSION['sessionDisplaySkin'];
	
		$type = "skin";
		
	} if( $action == "rank" ) {
		
		/* do the update query */
		$query = "UPDATE sms_crew SET displayRank = '$changerank' WHERE crewid = '$sessionCrewid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
		
		/* set a new session variable */
		$_SESSION['sessionDisplayRank'] = $changerank;
		$sessionDisplayRank = $_SESSION['sessionDisplayRank'];
	
		$type = "rank set";
		
	} if( $options ) {
	
		/* set the vars */
		$cpShowPosts = $_POST['cpShowPosts'];
		$cpShowPostsNum = $_POST['cpShowPostsNum'];
		$cpShowLogs = $_POST['cpShowLogs'];
		$cpShowLogsNum = $_POST['cpShowLogsNum'];
		$cpShowNews = $_POST['cpShowNews'];
		$cpShowNewsNum = $_POST['cpShowNewsNum'];
	
		/* run the update */
		$query = "UPDATE sms_crew SET cpShowPosts = '$cpShowPosts', cpShowLogs = '$cpShowLogs', ";
		$query.= "cpShowNews = '$cpShowNews', cpShowPostsNum = '$cpShowPostsNum', ";
		$query.= "cpShowLogsNum = '$cpShowLogsNum', cpShowNewsNum = '$cpShowNewsNum' WHERE ";
		$query.= "crewid = '$sessionCrewid' LIMIT 1";
		$result = mysql_query( $query );
	
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
	
		$type = "site options";
	
	}
	
	?>
	
	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( $type, "update" );
			$check->display();
		}
	
		?>
		
		<span class="fontTitle">Site Options</span><br /><br />
		
		SMS gives users more control of what they see when they're logged in. From this page, you
		can set the skin you use when you're logged in and the rank set you see. In the future,
		we'll be letting you choose whether you see some of the items in the control panel, and 
		even how many of those items you want to see.
	
		<h1>Presentation</h1>
		<?
	
		$getUserInfo = "SELECT cpShowPosts, cpShowLogs, cpShowNews, cpShowPostsNum, ";
		$getUserInfo.= "cpShowLogsNum, cpShowNewsNum FROM sms_crew WHERE ";
		$getUserInfo.= "crewid = '$sessionCrewid' LIMIT 1";
		$getUserInfoResult = mysql_query( $getUserInfo );
	
		while( $userPrefs = mysql_fetch_assoc( $getUserInfoResult ) ) {
			extract( $userPrefs, EXTR_OVERWRITE );
		}
	
		?>
		<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=site">
		<table>
			<tr>
				<td colspan="3" class="fontMedium"><b>Show the following in the Control Panel?</b></td>
			</tr>
			<tr>
				<td colspan="3" height="1"></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Latest Mission Entries</td>
				<td>&nbsp;</td>
				<td>
					<input type="radio" id="showPostsY" name="cpShowPosts" value="y" <? if( $cpShowPosts == "y" ) { echo "checked"; } ?>/> <label for="showPostsY">Yes</label>
					<input type="radio" id="showPostsN" name="cpShowPosts" value="n" <? if( $cpShowPosts == "n" ) { echo "checked"; } ?>/> <label for="showPostsN">No</label>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Latest Personal Logs</td>
				<td>&nbsp;</td>
				<td>
					<input type="radio" id="showLogsY" name="cpShowLogs" value="y" <? if( $cpShowLogs == "y" ) { echo "checked"; } ?>/> <label for="showLogsY">Yes</label>
					<input type="radio" id="showLogsN" name="cpShowLogs" value="n" <? if( $cpShowLogs == "n" ) { echo "checked"; } ?>/> <label for="showLogsN">No</label>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Latest News Items</td>
				<td>&nbsp;</td>
				<td>
					<input type="radio" id="showNewsY" name="cpShowNews" value="y" <? if( $cpShowNews == "y" ) { echo "checked"; } ?>/> <label for="showNewsY">Yes</label>
					<input type="radio" id="showNewsN" name="cpShowNews" value="n" <? if( $cpShowNews == "n" ) { echo "checked"; } ?>/> <label for="showNewsN">No</label>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="5"></td>
			</tr>
			<tr>
				<td colspan="3" class="fontMedium">
					<b>How many of the following should be shown in the Control Panel?</b>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="1"></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Latest Mission Entries</td>
				<td>&nbsp;</td>
				<td>
					<input type="text" class="order" name="cpShowPostsNum" value="<?=$cpShowPostsNum;?>" maxlength="3" />
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Latest Personal Logs</td>
				<td>&nbsp;</td>
				<td>
					<input type="text" class="order" name="cpShowLogsNum" value="<?=$cpShowLogsNum;?>" maxlength="3" />
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Latest News Items</td>
				<td>&nbsp;</td>
				<td>
					<input type="text" class="order" name="cpShowNewsNum" value="<?=$cpShowNewsNum;?>" maxlength="3" />
				</td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td colspan="3">
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="options" value="Update" class="button" />
				</td>
			</tr>
		</table>
		</form>
		
		<h1>Ranks</h1>
		<h3>Current Rank Set</h3>
		<img src="<?=$webLocation;?>images/ranks/<?=$sessionDisplayRank;?>/preview.png" border="0" alt="<?=$sessionDisplayRank;?>" />
	
		<h3>Other Available Rank Sets</h3>
		<?
			
			foreach( $allowedRanksArray as $key => $value ) {
				
				if( $value != $sessionDisplayRank ) {
					echo "<p><a href='" . $webLocation . "admin.php?page=user&sub=site&action=rank&changerank=" . trim( $value ) . "'>";
					echo "<img src='" . $webLocation . "images/ranks/" . trim( $value ) . "/preview.png' alt='" . $value . "' border='0' />";
					echo "</a>";
	
					/* make a note of which rankSet is the default */
					if( $value == $rankSet ) {
						echo "&nbsp;&nbsp;";
						echo "<span class='yellow fontSmall'>* System Default</span>";
					}
					
					echo "</p>";
				} else {
				}
			}
			
		?>
		
		<h1>Skins</h1>
		<h3>Current Skin</h3>
		<h4><?=ucwords( $displaySkin );?></h4>
		<p>
			<img src="<?=$webLocation;?>skins/<?=$sessionDisplaySkin;?>/preview.jpg" alt="<?=$sessionDisplaySkin;?>" style="border: 2px solid #efefef;" />
		</p>
		
		<h3>Other Available Skins</h3>
		<?
			
			foreach( $allowedSkinsArray as $key => $value ) {
				
				if( $value != $sessionDisplaySkin ) {
					echo "<h4>" . ucwords( $value ) . "</h4>";
					echo "<a href='" . $webLocation . "admin.php?page=user&sub=site&action=skin&changeskin=" . trim( $value ) . "'>";
					echo "<img src='" . $webLocation . "skins/" . trim( $value ) . "/preview.jpg' alt='" . $value . "' style='border: 2px solid #efefef;' />";
					echo "</a>";
				} else {
				}
			}
			
		?>
	</div>
	
<? } else { errorMessage( "site options" ); } ?>