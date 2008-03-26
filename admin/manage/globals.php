<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/globals.php
Purpose: Page that moderates the site globals

System Version: 2.6.0
Last Modified: 2008-01-12 1354 EST
**/

/* access check */
if( in_array( "m_globals", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	
	if( isset( $_POST['action_update_simm_x'] ) ) {
		$action_simm = $_POST['action_update_simm_x'];
	} if( isset( $_POST['action_update_fleet_x'] ) ) {
		$action_fleet = $_POST['action_update_fleet_x'];
	} if( isset( $_POST['action_update_options_x'] ) ) {
		$action_options = $_POST['action_update_options_x'];
	} if( isset( $_POST['action_update_presentation_x'] ) ) {
		$action_presentation = $_POST['action_update_presentation_x'];
	} if( isset( $_POST['action_update_positions_x'] ) ) {
		$action_positions = $_POST['action_update_positions_x'];
	}
	
	if( isset( $_GET['sec'] ) && is_numeric( $_GET['sec'] ) ) {
		$sec = $_GET['sec'];
	} else {
		$sec = 1;
	}
	
	$result = "";
	$updateGlobals = "";
	
	/* crew count for the options section */
	$crewCountRaw = "SELECT count(crewid) FROM sms_crew WHERE crewType = 'active'";
	$crewCountRawResult = mysql_query( $crewCountRaw );
	$crewCount = mysql_fetch_array( $crewCountRawResult );
	
	if( isset( $action_simm ) ) {
		
		/* pop the x, y, and name values off the POST array */
		array_pop( $_POST );
		array_pop( $_POST );
		array_pop( $_POST );
		
		foreach( $_POST as $key => $value )
		{
			if( !get_magic_quotes_gpc() ) {
				$$key = addslashes( $value );
			} else {
				$$key = $value;
			}
		}

		/* do the update query */
		$updateGlobals = "UPDATE sms_globals SET ";
		$updateGlobals.= "shipPrefix = '$shipPrefix', shipName = '$shipName', ";
		$updateGlobals.= "shipRegistry = '$shipRegistry', simmYear = '$simmYear', ";
		$updateGlobals.= "simmType = '$simmType' WHERE globalid = '1' LIMIT 1";
		$result = mysql_query( $updateGlobals );
		
	} if( isset( $action_fleet ) ) {
		
		/* pop the x, y, and name values off the POST array */
		array_pop( $_POST );
		array_pop( $_POST );
		array_pop( $_POST );
		
		foreach( $_POST as $key => $value )
		{
			if( !get_magic_quotes_gpc() ) {
				$$key = addslashes( $value );
			} else {
				$$key = $value;
			}
		}

		/* do the update query */
		$updateGlobals = "UPDATE sms_globals SET ";
		$updateGlobals.= "fleet = '$fleet', fleetURL = '$fleetURL', ";
		$updateGlobals.= "tfMember = '$tfMember', tfName = '$tfName', tfURL = '$tfURL', ";
		$updateGlobals.= "tgMember = '$tgMember', tgName = '$tgName', tgURL = '$tgURL' ";
		$updateGlobals.= "WHERE globalid = '1' LIMIT 1";
		$result = mysql_query( $updateGlobals );
		
	} if( isset( $action_options ) ) {
		
		/* pop the x, y, and name values off the POST array */
		array_pop( $_POST );
		array_pop( $_POST );
		array_pop( $_POST );
		
		foreach( $_POST as $key => $value )
		{
			if( !get_magic_quotes_gpc() ) {
				$$key = addslashes( $value );
			} else {
				$$key = $value;
			}
		}

		/* do the update query */
		$updateGlobals = "UPDATE sms_globals SET ";
		$updateGlobals.= "postCountDefault = '$postCountDefault', jpCount = '$jpCount', ";
		$updateGlobals.= "usePosting = '$usePosting', ";
		$updateGlobals.= "hasWebmaster = '$hasWebmaster', webmasterName = '$webmasterName', ";
		$updateGlobals.= "webmasterEmail = '$webmasterEmail', useMissionNotes = '$useMissionNotes', ";
		$updateGlobals.= "emailSubject = '$emailSubject', updateNotify = '$updateNotify' ";
		$updateGlobals.= "WHERE globalid = '1' LIMIT 1";
		$result = mysql_query( $updateGlobals );
		
		/*
			if the sms posting system is being turned OFF,
			remove all access to posting items in the system;
			if the sms posting system is being turned ON,
			add basic access to the posting items in the system
		*/
		if( $oldPosting == "y" && $usePosting == "n" ) {
		
			$getCrew = "SELECT crewid FROM sms_crew WHERE crewType = 'active'";
			$getCrewR = mysql_query( $getCrew );
			
			while( $crewFetch = mysql_fetch_array( $getCrewR ) ) {
				extract( $crewFetch, EXTR_OVERWRITE );
				
				mysql_query( "UPDATE sms_crew SET accessPost = '', cpShowPosts = 'n', cpShowLogs = 'n', cpShowNews = 'n' WHERE crewid = '$crewid' LIMIT 1" );
				
			}
		
		} if( $oldPosting == "n" && $usePosting == "y" ) {
		
			$getCrew = "SELECT crewid FROM sms_crew WHERE crewType = 'active'";
			$getCrewR = mysql_query( $getCrew );
			
			while( $crewFetch = mysql_fetch_array( $getCrewR ) ) {
				extract( $crewFetch, EXTR_OVERWRITE );
				
				mysql_query( "UPDATE sms_crew SET accessPost = 'post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes', cpShowPosts = 'y', cpShowLogs = 'y', cpShowNews = 'y' WHERE crewid = '$crewid' LIMIT 1" );
				
			}
		
		}
		
	} if( isset( $action_presentation ) ) {
		
		/* pop the x, y, and name values off the POST array */
		array_pop( $_POST );
		array_pop( $_POST );
		array_pop( $_POST );
		
		foreach( $_POST as $key => $value )
		{
			if( !get_magic_quotes_gpc() ) {
				$$key = addslashes( $value );
			} else {
				$$key = $value;
			}
		}
		
		/* build the manifest defaults array */
		$manifest_defaults_raw = array($cb_crew, $cb_npc, $cb_open, $cb_inactive);
		
		/* get rid of empty items */
		foreach($manifest_defaults_raw as $a => $b)
		{
			if(empty($b))
			{
				unset($manifest_defaults_raw[$a]);
			}
		}
		
		/* make it a string to put into the db */
		$manifest_values = implode(',', $manifest_defaults_raw);

		/* do the update query */
		$updateGlobals = "UPDATE sms_globals SET ";
		$updateGlobals.= "allowedSkins = '$allowedSkins', skin = '$skin', ";
		$updateGlobals.= "allowedRanks = '$allowedRanks', rankSet = '$rankSet', ";
		$updateGlobals.= "manifestDisplay = '$manifestDisplay', showInfoMission = '$showInfoMission', ";
		$updateGlobals.= "showInfoPosts = '$showInfoPosts', showInfoPositions = '$showInfoPositions', ";
		$updateGlobals.= "useSamplePost = '$useSamplePost', showNews = '$showNews', ";
		$updateGlobals.= "showNewsNum = '$showNewsNum', logList = '$logList', ";
		$updateGlobals.= "bioShowPosts = '$bioShowPosts', bioShowPostsNum = '$bioShowPostsNum', ";
		$updateGlobals.= "bioShowLogs = '$bioShowLogs', bioShowLogsNum = '$bioShowLogsNum', ";
		$updateGlobals.= "stardateDisplaySD = '$stardateDisplaySD', stardateDisplayDate = '$stardateDisplayDate', ";
		$updateGlobals.= "manifest_defaults = '$manifest_values' WHERE globalid = '1' LIMIT 1";
		$result = mysql_query( $updateGlobals );
		
	} if( isset( $action_positions ) ) {
		
		/* define the POST array */
		$post = $_POST;
		
		/* pop the last 3 items off the array */
		array_pop( $post );
		array_pop( $post );
		array_pop( $post );
		
		/* reset all of the positionMainPage flags */
		$updatePos = "UPDATE sms_positions SET positionMainPage = 'n'";
		$updatePosResult = mysql_query( $updatePos );
		
		/* loop through the array and update the positions specified */
		foreach( $post as $key => $value ) {
			
			$update = "UPDATE sms_positions SET positionMainPage = 'y' WHERE ";
			$update.= "positionid = '$value' LIMIT 1";
			$result = mysql_query( $update );
			
		}
		
		/* optimize the table */
		optimizeSQLTable( "sms_positions" );

	}

	/* optimize the table */
	optimizeSQLTable( "sms_globals" );
		
	/* strip the slashes from the necessary vars */
	$shipName = stripslashes( $shipName );
	$fleet = stripslashes( $fleet );
	$tfName = stripslashes( $tfName );
	$tgName = stripslashes( $tgName );
	$webmasterName = stripslashes( $webmasterName );
	
	$manifest_defaults_array = explode(',', $manifest_defaults);

?>

	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $updateGlobals );
		
		if( !empty( $check->query ) ) {
			$check->message( "site globals", "update" );
			$check->display();
		}
		
		if( $showInfoPositions == "n" ) {
			$disable = 5;
		} else {
			$disable = "";
		}
		
		?>
	
		<span class="fontTitle">Site Globals</span><br />
	
		<script type="text/javascript">
			$(document).ready(function(){
				$('#container-1 > ul').tabs(<?php echo $sec; ?>, { disabled: [<?php echo $disable; ?>] });
			});
		</script>
		
		<div id="container-1">
			<ul>
				<li><a href="#one"><span>Simm</span></a></li>
				<li><a href="#two"><span>Fleet</span></a></li>
				<li><a href="#three"><span>Options</span></a></li>
				<li><a href="#four"><span>Presentation</span></a></li>
				<li><a href="#five"><span>Top Open Positions</span></a></li>
			</ul>
	
			<div id="one" class="ui-tabs-container ui-tabs-hide">
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=globals&sec=1">
				<table>
					<tr>
						<td colspan="3" class="fontLarge"><b>Simm Information</b></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Ship Prefix</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" class="image" name="shipPrefix" value="<?=$shipPrefix;?>" />
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Ship Name</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" class="image" name="shipName" value="<?=$shipName;?>" />
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Ship Registry</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" class="image" name="shipRegistry" value="<?=$shipRegistry;?>" />
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Simm Year</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" class="image" name="simmYear" value="<?=$simmYear;?>" />
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Simm Type</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="typeShip" name="simmType" value="ship" <? if( $simmType == "ship" ) { echo "checked"; } ?>/> <label for="typeShip">Ship</label>
							<input type="radio" id="typeBase" name="simmType" value="starbase" <? if( $simmType == "starbase" ) { echo "checked"; } ?>/> <label for="typeBase">Starbase</label>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update_simm" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
	
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=globals&sec=2">
				<table>	
					<tr>
						<td colspan="3" class="fontLarge"><b>Fleet Information</b></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Fleet</td>
						<td>&nbsp;</td>
						<td><input type="text" class="image" name="fleet" value="<?=$fleet;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Fleet URL</td>
						<td>&nbsp;</td>
						<td><input type="text" class="image" name="fleetURL" value="<?=$fleetURL;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">In a Task Force?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="tfY" name="tfMember" value="y"<? if( $tfMember == "y" ) { echo " checked"; } ?> /> <label for="tfY">Yes</label>
							<input type="radio" id="tfN" name="tfMember" value="n"<? if( $tfMember == "n" ) { echo " checked"; } ?> /> <label for="tfN">No</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Task Force Name</td>
						<td>&nbsp;</td>
						<td><input type="text" class="image" name="tfName" value="<?=$tfName;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Task Force URL</td>
						<td>&nbsp;</td>
						<td><input type="text" class="image" name="tfURL" value="<?=$tfURL;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">In a Task Group?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="tgY" name="tgMember" value="y"<? if( $tgMember == "y" ) { echo " checked"; } ?> /> <label for="tgY">Yes</label>
							<input type="radio" id="tgN" name="tgMember" value="n"<? if( $tgMember == "n" ) { echo " checked"; } ?> /> <label for="tgN">No</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Task Group Name</td>
						<td>&nbsp;</td>
						<td><input type="text" class="image" name="tgName" value="<?=$tgName;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Task Group URL</td>
						<td>&nbsp;</td>
						<td><input type="text" class="image" name="tgURL" value="<?=$tgURL;?>" /></td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update_fleet" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
	
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=globals&sec=3">
				<table>
					<tr>
						<td colspan="3" class="fontLarge"><b>Site Options</b></td>
					</tr>
					<tr>
						<td class="tableCellLabel">
							<b>Default Post Count</b><br />
							<span class="fontSmall">
								This number represents the required posting cycle for your sim in days
							</span>
						</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" class="order" name="postCountDefault" value="<?=$postCountDefault;?>" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">
							JP Counting<br />
							<span class="fontSmall">
								Should JPs count as 1 post or a post per author?
							</span>
						</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="jpY" name="jpCount" value="y" <? if( $jpCount == "y" ) { echo "checked"; } ?>/> <label for="jpY">Post per Author</label>
							<input type="radio" id="jpN" name="jpCount" value="n" <? if( $jpCount == "n" ) { echo "checked"; } ?>/> <label for="jpN">One Post</label>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Use SMS Posting System?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="postingY" name="usePosting" value="y" <? if( $usePosting == "y" ) { echo "checked"; } ?>/> <label for="postingY">Yes</label>
							<input type="radio" id="postingN" name="usePosting" value="n" <? if( $usePosting == "n" ) { echo "checked"; } ?>/> <label for="postingN">No</label>
							<input type="hidden" name="oldPosting" value="<?=$usePosting;?>" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Use Mission Notes?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="notesY" name="useMissionNotes" value="y" <? if( $useMissionNotes == "y" ) { echo "checked"; } ?>/> <label for="notesY">Yes</label>
							<input type="radio" id="notesN" name="useMissionNotes" value="n" <? if( $useMissionNotes == "n" ) { echo "checked"; } ?>/> <label for="notesN">No</label>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="20"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Email Subject Line</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" class="image" name="emailSubject" value="<?=$emailSubject;?>" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">
							SMS Update Notification<br />
							<span class="fontSmall yellow">
								Turning off update notification is not recommended! If you turn the notification
								off you will need to check the Anodyne website yourself for updates.
							</span>
						</td>
						<td>&nbsp;</td>
						<td>
							<select name="updateNotify">
								<option value="all" <?php if( $updateNotify == "all" ) { echo "selected"; } ?>>All Updates</option>
								<option value="major" <?php if( $updateNotify == "major" ) { echo "selected"; } ?>>Major Updates</option>
								<option value="none" <?php if( $updateNotify == "none" ) { echo "selected"; } ?>>None</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="20"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Has Webmaster?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="webY" name="hasWebmaster" value="y" <? if( $hasWebmaster == "y" ) { echo "checked"; } ?> /> <label for="webY">Yes</label>
							<input type="radio" id="webN" name="hasWebmaster" value="n" <? if( $hasWebmaster == "n" ) { echo "checked"; } ?> /> <label for="webN">No</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Webmaster Name</td>
						<td>&nbsp;</td>
						<td><input type="text" class="image" name="webmasterName" value="<?=$webmasterName;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Webmaster Email</td>
						<td>&nbsp;</td>
						<td><input type="text" class="image" name="webmasterEmail" value="<?=$webmasterEmail;?>" /></td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update_options" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
	
			<div id="four" class="ui-tabs-container ui-tabs-hide">
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=globals&sec=4">
				<table>
					<tr>
						<td colspan="3" class="fontLarge"><b>Site Presentation</b></td>
					</tr>
					<tr>
						<td colspan="3">
							Please specify the names of the folders, separated by commas, where the skins are
							located. These folders will be available to registered users for their personal skin 
							selection. You only need to specify the name of the folder, not the complete path.
						</td>
					</tr>
					<tr>
						<td colspan="3" height="10"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Allowed Skins</td>
						<td>&nbsp;</td>
						<td>
							<textarea name="allowedSkins" rows="2"><?=$allowedSkins;?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="20"></td>
					</tr>
					<tr>
						<td colspan="3">
							Below are the available skins for the default display of the site. If you wish to
							change your skin, please select one of the skins from the menu to change the deafult
							presentation of the site. Changes will not override registered users' skin choices, only
							visitors.
						</td>
					</tr>
					<tr>
						<td colspan="3" height="10"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Site Default Skin</td>
						<td>&nbsp;</td>
						<td>
							<select name="skin">
							<?
							
								$allowedSkinsArray = explode( ",", $allowedSkins );
								
								foreach( $allowedSkinsArray as $key => $value ) {
								
							?>
								<option value="<?=trim( $value );?>"<? if( $skin == trim( $value ) ) { echo " selected"; } ?>><?=ucwords( $value );?></option>
							
							<? } ?>
							
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="20"></td>
					</tr>
					<tr>
						<td colspan="3">
							Please specify the names of the folders, separated by commas, where the ranks are
							located in <i>images/ranks</i>. These folders will be available to registered 
							users for their personal skin selection. You only need to specify the name of 
							the folder, not the complete path.
						</td>
					</tr>
					<tr>
						<td colspan="3" height="10"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Allowed Ranks</td>
						<td>&nbsp;</td>
						<td>
							<textarea name="allowedRanks" rows="2"><?=$allowedRanks;?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="10"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Default Rank Set</td>
						<td>&nbsp;</td>
						<td>
							<?
		
							/* break the string into an array */
							$rankArray = explode( ",", $allowedRanks );
		
							/* loop through the array */
							foreach( $rankArray as $key => $value ) {
		
							?>
							
								<input type="radio" id="<?=$value;?>" name="rankSet" value="<?=$value;?>"<? if( $rankSet == trim( $value ) ) { echo " checked"; } ?> />
								<label for="<?=$value;?>"><img src="<?=$webLocation;?>images/ranks/<?=trim( $value );?>/preview.png" alt="" border="0" /></label>
								<br />
		
							<? } ?>
							
						</td>
					</tr>
					<tr>
						<td colspan="3" height="20"></td>
					</tr>
					
					<tr>
						<td class="tableCellLabel">Manifest Defaults</td>
						<td>&nbsp;</td>
						<td>
							<input type="checkbox" id="cb_crew" name="cb_crew" value="$('tr.active').show();" <? if(in_array("$('tr.active').show();", $manifest_defaults_array)) { echo "checked"; } ?>/> <label for="cb_crew">Playing Characters</label><br />
							<input type="checkbox" id="cb_npc" name="cb_npc" value="$('tr.npc').show();" <? if(in_array("$('tr.npc').show();", $manifest_defaults_array)) { echo "checked"; } ?>/> <label for="cb_npc">Non-Playing Characters</label><br />
							<input type="checkbox" id="cb_open" name="cb_open" value="$('tr.open').show();" <? if(in_array("$('tr.open').show();", $manifest_defaults_array)) { echo "checked"; } ?>/> <label for="cb_open">Open Positions</label><br />
							<input type="checkbox" id="cb_inactive" name="cb_inactive" value="$('tr.inactive').show();" <? if(in_array("$('tr.inactive').show();", $manifest_defaults_array)) { echo "checked"; } ?>/> <label for="cb_inactive">Departed Characters</label>
						</td>
					</tr>
					
					<tr>
						<td colspan="3" height="20"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">
							Show Stardate?<br />
							<span class="fontSmall yellow">Stardate display is only available if the
							simm year is greater than or equal to 2265</span>
						</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="displaySDY" name="stardateDisplaySD" value="y"<?php if( $stardateDisplaySD == "y" ) { echo " checked='yes'"; } ?>/><label for="displaySDY">Yes</label>
							<input type="radio" id="displaySDN" name="stardateDisplaySD" value="n"<?php if( $stardateDisplaySD == "n" ) { echo " checked='yes'"; } ?>/><label for="displaySDN">No</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Show Earth Date?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="displayEDY" name="stardateDisplayDate" value="y"<?php if( $stardateDisplayDate == "y" ) { echo " checked='yes'"; } ?>/><label for="displayEDY">Yes</label>
							<input type="radio" id="displayEDN" name="stardateDisplayDate" value="n"<?php if( $stardateDisplayDate == "n" ) { echo " checked='yes'"; } ?>/><label for="displayEDN">No</label>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="20"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Show Current Mission on Main Page?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="infoMissionY" name="showInfoMission" value="y"<? if( $showInfoMission == "y" ) { echo " checked"; } ?> /> <label for="infoMissionY">Yes</label>
							<input type="radio" id="infoMissionN" name="showInfoMission" value="n"<? if( $showInfoMission == "n" ) { echo " checked"; } ?> /> <label for="infoMissionN">No</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Show Latest Posts on Main Page?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="infoPostsY" name="showInfoPosts" value="y"<? if( $showInfoPosts == "y" ) { echo " checked"; } ?> /> <label for="infoPostsY">Yes</label>
							<input type="radio" id="infoPostsN" name="showInfoPosts" value="n"<? if( $showInfoPosts == "n" ) { echo " checked"; } ?> /> <label for="infoPostsN">No</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Show Open Positions on Main Page?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="infoPosY" name="showInfoPositions" value="y"<? if( $showInfoPositions == "y" ) { echo " checked"; } ?> /> <label for="infoPosY">Yes</label>
							<input type="radio" id="infoPosN" name="showInfoPositions" value="n"<? if( $showInfoPositions == "n" ) { echo " checked"; } ?> /> <label for="infoPosN">No</label>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="20"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Use Sample Post on Join Page?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="sampleY" name="useSamplePost" value="y"<? if( $useSamplePost == "y" ) { echo " checked"; } ?> /> <label for="sampleY">Yes</label>
							<input type="radio" id="sampleN" name="useSamplePost" value="n"<? if( $useSamplePost == "n" ) { echo " checked"; } ?> /> <label for="sampleN">No</label>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="20"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Show News Items on Welcome Page?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="newsY" name="showNews" value="y"<? if( $showNews == "y" ) { echo " checked"; } ?> /> <label for="newsY">Yes</label>
							<input type="radio" id="newsN" name="showNews" value="n"<? if( $showNews == "n" ) { echo " checked"; } ?> /> <label for="newsN">No</label>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="20"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Number of News Items to Show</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" class="order" name="showNewsNum" id="showNewsNum" value="<?=$showNewsNum;?>" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="20"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Number of Personal Logs to Show in Personal Logs Listing</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" class="order" name="logList" value="<?=$logList;?>" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="20"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Show Player's Recent Posts in Bio?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="bioPostsY" name="bioShowPosts" value="y"<? if( $bioShowPosts == "y" ) { echo " checked"; } ?> /> <label for="bioPostsY">Yes</label>
							<input type="radio" id="bioPostsN" name="bioShowPosts" value="n"<? if( $bioShowPosts == "n" ) { echo " checked"; } ?> /> <label for="bioPostsN">No</label>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="10"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Number of Recent Posts to Show in Bio</td>
						<td>&nbsp;</td>
						<td><input type="text" class="order" name="bioShowPostsNum" value="<?=$bioShowPostsNum;?>" /></td>
					</tr>
					<tr>
						<td colspan="3" height="20"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Show Player's Recent Logs in Bio?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="bioLogsY" name="bioShowLogs" value="y"<? if( $bioShowLogs == "y" ) { echo " checked"; } ?> /> <label for="bioLogsY">Yes</label>
							<input type="radio" id="bioLogsN" name="bioShowLogs" value="n"<? if( $bioShowLogs == "n" ) { echo " checked"; } ?> /> <label for="bioLogsN">No</label>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="10"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Number of Recent Logs to Show in Bio</td>
						<td>&nbsp;</td>
						<td><input type="text" class="order" name="bioShowLogsNum" value="<?=$bioShowLogsNum;?>" /></td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update_presentation" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
			<div id="five" class="ui-tabs-container ui-tabs-hide">
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=globals&sec=5">
				<table cellspacing="0" cellpadding="4">
					<tr>
						<td colspan="4" class="fontLarge"><b>Top Open Positions List</b></td>
					</tr>
					<?
					
					/* pull the open positions from the database */
					$getPos = "SELECT pos.positionid, pos.positionName, pos.positionMainPage, pos.positionOpen, dep.deptName, dep.deptColor ";
					$getPos.= "FROM sms_positions AS pos, sms_departments AS dep WHERE pos.positionOpen >= 1 AND ";
					$getPos.= "pos.positionDisplay = 'y' AND pos.positionDept = dep.deptid ORDER BY pos.positionDept, pos.positionOrder ASC";
					$getPosResult = mysql_query( $getPos );
					
					$rowCount = "0";
					$color1 = "rowColor1";
					$color2 = "rowColor2";
			
					while( $posFetch = mysql_fetch_assoc( $getPosResult ) ) {
						extract( $posFetch, EXTR_OVERWRITE );
						
						$rowColor = ($rowCount % 2) ? $color1 : $color2;
					
					?>
					<tr class="<?=$rowColor;?>">
						<td width="20" align="center">
							<input type="checkbox" name="position_<?=$posFetch['positionid'];?>" <? if( $posFetch['positionMainPage'] == "y" ) { echo "checked"; } ?> value="<?=$posFetch['positionid'];?>" />
						</td>
						<td><? printText( $posFetch['positionName'] ); ?></td>
						<td>
							<font color="#<?=$posFetch['deptColor'];?>"><? printText( $posFetch['deptName'] ); ?></font>
						</td>
						<td>
							<?
							
							echo $posFetch['positionOpen'] . " Available ";
							
							if( $posFetch['positionOpen'] == 1 ) {
								echo "Slot";
							} else {
								echo "Slots";
							}
							
							?>
						</td>
					</tr>
					<? $rowCount++; } ?>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update_positions" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
		</form>
		
		</div>
	</div>
	
<? } else { errorMessage( "site globals management" ); } ?>