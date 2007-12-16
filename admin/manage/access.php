<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/access.php
Purpose: Page to access levels for individual users

System Version: 2.6.0
Last Modified: 2007-11-05 0956 EST
**/

/* access check */
if( in_array( "x_access", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$action_post = $_POST['action_update_post_x'];
	$action_manage = $_POST['action_update_manage_x'];
	$action_reports = $_POST['action_update_reports_x'];
	$action_user = $_POST['action_update_user_x'];
	$action_other = $_POST['action_update_other_x'];
	$set = $_GET['set'];
	$tab = $_GET['tab'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['crew'] ) && preg_match( "/^\d+$/", $_GET['crew'], $matches ) == 0 ) {
		errorMessageIllegal( "access level page" );
		exit();
	} else {
		/* set the GET variable */
		$crew = $_GET['crew'];
	}
	
	/* set the default tab */
	if( !isset( $tab ) ) {
		$tab = 1;
	}

	/* if update has been hit, continue */
	if(
		isset( $action_post ) ||
		isset( $action_manage ) ||
		isset( $action_reports ) ||
		isset( $action_user ) ||
		isset( $action_other )
	) {

		/* set the POST array */
		$accessValues = $_POST;
		$type = $_POST['type'];
		
		/* pop off the non-essential items from the POST array */
		array_pop( $accessValues );
		array_pop( $accessValues );
		array_pop( $accessValues );
		array_pop( $accessValues );

		/* set implode the array */
		$accessString = implode( ",", $accessValues );
		
		switch( $type )
		{
			case 'post':
				$field = 'accessPost';
				break;
			case 'manage':
				$field = 'accessManage';
				break;
			case 'reports':
				$field = 'accessReports';
				break;
			case 'user':
				$field = 'accessUser';
				break;
			case 'other':
				$field = 'accessOthers';
				break;
		}

		/* update the database */
		$updateAccess = "UPDATE sms_crew SET $field = '$accessString' WHERE crewid = '$crew' LIMIT 1";
		$result = mysql_query( $updateAccess );

		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
	
	} if( isset( $crew ) && isset( $set ) ) {
		
		switch( $set )
		{
			case 'co':
				$id = 1;
				break;
			case 'xo':
				$id = 2;
				break;
			case 'dh':
				$id = 3;
				break;
			case 'player':
				$id = 4;
				break;
		}
		
		$getGroupLevels = "SELECT * FROM sms_accesslevels WHERE id = $id LIMIT 1";
		$getGroupLevelsResult = mysql_query( $getGroupLevels );
		$groups = mysql_fetch_array( $getGroupLevelsResult );
		
		/* do the update query */
		$updateAccess = "UPDATE sms_crew SET accessPost = '$groups[1]', accessManage = '$groups[2]', ";
		$updateAccess.= "accessReports = '$groups[3]', accessUser = '$groups[4]', ";
		$updateAccess.= "accessOthers = '$groups[5]' WHERE crewid = '$crew' LIMIT 1";
		$result = mysql_query( $updateAccess );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
	
	}

	$getCrewAccess = "SELECT accessPost, accessManage, accessReports, accessUser, accessOthers ";
	$getCrewAccess.= "FROM sms_crew WHERE crewid = '$crew' LIMIT 1";
	$getCrewAccessResult = mysql_query( $getCrewAccess );
	$fetchAccess = mysql_fetch_array( $getCrewAccessResult );
	$crewAccessRaw = $fetchAccess[0] . "," . $fetchAccess[1] . "," . $fetchAccess[2] . "," . $fetchAccess[3] . "," . $fetchAccess[4];
	$crewAccess = explode( ",", $crewAccessRaw );

?>

	<div class="body">
	
		<? if( !$_GET['crew'] ) { ?>
	
		<span class="fontTitle">User Access Control</span><br /><br />
		From this page you can set up your crew to have access to specific sections of SMS.
		Pay careful attention to the notes for specific sections as they point out certain
		requirements for various permissions. To begin, please select a crew member from
		the list below to set their access levels to the system.<br /><br />
		
		<b class="fontMedium"><a href="<?=$webLocation;?>admin.php?page=manage&sub=accessall" class="add">Set Access Levels for Entire Crew &raquo;</a></b><br /><br />
	
		<?
		
		$getCrew = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$getCrew.= "FROM sms_crew AS crew, sms_ranks AS rank WHERE crew.rankid = rank.rankid ";
		$getCrew.= "AND crew.crewType = 'active' ORDER BY crew.rankid ASC";
		$getCrewResult = mysql_query( $getCrew );
		
		while( $userFetch = mysql_fetch_assoc( $getCrewResult ) ) {
			extract( $userFetch, EXTR_OVERWRITE );
			
			echo "<a href='" . $webLocation . "admin.php?page=manage&sub=access&crew=" . $userFetch['crewid'] . "'>" . stripslashes( $userFetch['rankName'] . " " . $userFetch['firstName'] . " " . $userFetch['lastName'] ) . "</a><br />";
			
		} /* close while loop */
	
		} elseif( $crew ) {
			
			/* query check */
			$check = new QueryCheck;
			$check->checkQuery( $result, $updateAccess );
			
			if( !empty( $check->query ) ) {
				$check->message( "player access levels", "update" );
				$check->display();
			}
		
		?>
	
		<span class="fontTitle">User Access Levels - <? printCrewName( $crew, "noRank", "noLink" ); ?></span>
		
		<ul class="list-dark">
			<li><a href="<?=$webLocation;?>admin.php?page=manage&sub=access&crew=<?=$crew;?>&set=co">Grant CO Access Levels</a></li>
			<li><a href="<?=$webLocation;?>admin.php?page=manage&sub=access&crew=<?=$crew;?>&set=xo">Grant XO Access Levels</a></li>
			<li><a href="<?=$webLocation;?>admin.php?page=manage&sub=access&crew=<?=$crew;?>&set=dh">Grant Department Head Access Levels</a></li>
			<li><a href="<?=$webLocation;?>admin.php?page=manage&sub=access&crew=<?=$crew;?>&set=player">Grant Standard Access Levels</a></li>
		</ul>
		
		<b class="fontMedium"><a href="<?=$webLocation;?>admin.php?page=manage&sub=access">&laquo; Back to Crew List</a></b>
		<br /><br />
		
		<script type="text/javascript">
			$(document).ready(function(){
				$('#container-1 > ul').tabs(<?=$tab;?>);
			});
		</script>
		
		<div id="container-1">
			<ul>
				<li><a href="#one"><span>Post</span></a></li>
				<li><a href="#two"><span>Manage</span></a></li>
				<li><a href="#three"><span>Reports</span></a></li>
				<li><a href="#four"><span>User</span></a></li>
				<li><a href="#five"><span>Other</span></a></li>
			</ul>
			
			<div id="one" class="ui-tabs-container ui-tabs-hide">
				<div class="pmHeader">
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=access&crew=<?=$crew;?>&tab=1">
					<input type="checkbox" id="post" name="post" value="post" <? if( in_array( "post", $crewAccess ) ) { echo "checked"; } ?>/>
					<label for="post">Post</label>
				</div>
				<table>
					<tr>
						<td width="30%"><input type="checkbox" id="p_addjp" name="p_addjp" value="p_addjp" <? if( in_array( "p_addjp", $crewAccess ) ) { echo "checked"; } ?>/> <label for="p_addjp">Add Joint Post</label></td>
						<td width="30%"><input type="checkbox" id="p_missionnotes" name="p_missionnotes" value="p_missionnotes" <? if( in_array( "p_missionnotes", $crewAccess ) ) { echo "checked"; } ?>/> <label for="p_missionnotes">Mission Notes</label></td>
						<td width="30%"><input type="checkbox" id="p_jp" name="p_jp" value="p_jp" <? if( in_array( "p_jp", $crewAccess ) ) { echo "checked"; } ?>/> <label for="p_jp">Write Joint Post</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="p_addlog" name="p_addlog" value="p_addlog" <? if( in_array( "p_addlog", $crewAccess ) ) { echo "checked"; } ?>/> <label for="p_addlog">Add Personal Log</label></td>
						<td width="30%"><input type="checkbox" id="p_pm" name="p_pm" value="p_pm" <? if( in_array( "p_pm", $crewAccess ) ) { echo "checked"; } ?>/> <label for="p_pm">Send Private Message</label></td>
						<td width="30%"><input type="checkbox" id="p_log" name="p_log" value="p_log" <? if( in_array( "p_log", $crewAccess ) ) { echo "checked"; } ?>/> <label for="p_log">Write Personal Log</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="p_addmission" name="p_addmission" value="p_addmission" <? if( in_array( "p_addmission", $crewAccess ) ) { echo "checked"; } ?>/> <label for="p_addmission">Add Mission Entry</label></td>
						<td width="30%"></td>
						<td width="30%"><input type="checkbox" id="p_mission" name="p_mission" value="p_mission" <? if( in_array( "p_mission", $crewAccess ) ) { echo "checked"; } ?>/> <label for="p_mission">Write Mission Entry</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="p_addnews" name="p_addnews" value="p_addnews" <? if( in_array( "p_addnews", $crewAccess ) ) { echo "checked"; } ?>/> <label for="p_addnews">Add News Item</label></td>
						<td width="30%"></td>
						<td width="30%"><input type="checkbox" id="p_news" name="p_news" value="p_news" <? if( in_array( "p_news", $crewAccess ) ) { echo "checked"; } ?>/> <label for="p_news">Write News Item</label></td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="hidden" name="type" value="post" />
							<input type="image" src="<?=path_userskin;?>/buttons/update.png" name="action_update_post" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<b class="fontMedium">Notes</b>
				<ul class="version">
					<li>In order for someone to create an NPC, they need to have both Create Character privileges as well as either
					NPC-1 or NPC-2 privileges</li>
					<li>In order for someone to create a playing character, they need to have Create Character and Character privileges</li>
					<li>If access is given to the Characters, the user will need Account-2 or Bio-3 privileges as well</li>
				</ul>
		
				<div class="pmHeader">
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=access&crew=<?=$crew;?>&tab=2">
					<input type="checkbox" id="manage" name="manage" value="manage" <? if( in_array( "manage", $crewAccess ) ) { echo "checked"; } ?>/>
					<label for="manage">Manage</label>
				</div>
				<table>
					<tr>
						<td width="30%"><input type="checkbox" id="m_awards" name="m_awards" value="m_awards" <? if( in_array( "m_awards", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_awards">Awards</label></td>
						<td width="30%"><input type="checkbox" id="m_posts" name="m_posts" value="m_posts" <? if( in_array( "m_posts", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_posts">Mission Posts</label></td>
						<td width="30%"><input type="checkbox" id="m_positions" name="m_positions" value="m_positions" <? if( in_array( "m_positions", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_positions">Positions</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="m_coc" name="m_coc" value="m_coc" <? if( in_array( "m_coc", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_coc">Chain of Command</label></td>
						<td width="30%"><input type="checkbox" id="m_missionsummaries" name="m_missionsummaries" value="m_missionsummaries" <? if( in_array( "m_missionsummaries", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_missionsummaries">Mission Summaries</label></td>
						<td width="30%"><input type="checkbox" id="m_ranks" name="m_ranks" value="m_ranks" <? if( in_array( "m_ranks", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_ranks">Ranks</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="m_crew" name="m_crew" value="m_crew" <? if( in_array( "m_crew", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_crew">Characters</label></td>
						<td width="30%"><input type="checkbox" id="m_missions" name="m_missions" value="m_missions" <? if( in_array( "m_missions", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_missions">Missions</label></td>
						<td width="30%"><input type="checkbox" id="m_removeaward" name="m_removeaward" value="m_removeaward" <? if( in_array( "m_removeaward", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_removeaward">Remove Crew Award</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="m_createcrew" name="m_createcrew" value="m_createcrew" <? if( in_array( "m_createcrew", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_createcrew">Create Character</label></td>
						<td width="30%"><input type="checkbox" id="m_newscat1" name="m_newscat1" value="m_newscat1" <? if( in_array( "m_newscat1", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_newscat1">News Categories - <b class="yellow">General Access</b></label></td>
						<td width="30%"><input type="checkbox" id="m_globals" name="m_globals" value="m_globals" <? if( in_array( "m_globals", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_globals">Site Globals</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="m_database" name="m_database" value="m_database" <? if( in_array( "m_database", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_database">Database</label></td>
						<td width="30%"><input type="checkbox" id="m_newscat2" name="m_newscat2" value="m_newscat2" <? if( in_array( "m_newscat2", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_newscat2">News Categories - <b class="yellow">Power User</b></label></td>
						<td width="30%"><input type="checkbox" id="m_messages" name="m_messages" value="m_messages" <? if( in_array( "m_messages", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_messages">Site Messages</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="m_decks" name="m_decks" value="m_decks" <? if( in_array( "m_decks", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_decks">Deck Listing</label></td>
						<td width="30%"><input type="checkbox" id="m_newscat3" name="m_newscat3" value="m_newscat3" <? if( in_array( "m_newscat3", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_newscat3">News Categories - <b class="yellow">Admin</b></label></td>
						<td width="30%"><input type="checkbox" id="m_specs" name="m_specs" value="m_specs" <? if( in_array( "m_specs", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_specs">Specifications</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="m_departments" name="m_departments" value="m_departments" <? if( in_array( "m_departments", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_departments">Departments</label></td>
						<td width="30%"><input type="checkbox" id="m_news" name="m_news" value="m_news" <? if( in_array( "m_news", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_news">News Items</label></td>
						<td width="30%"><input type="checkbox" id="m_strike" name="m_strike" value="m_strike" <? if( in_array( "m_strike", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_strike">Strike Player</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="m_docking" name="m_docking" value="m_docking" <? if( in_array( "m_docking", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_docking">Docked Ships</label></td>
						<td width="30%"><input type="checkbox" id="m_npcs1" name="m_npcs1" value="m_npcs1" <? if( in_array( "m_npcs1", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_npcs1">NPCs - <b class="yellow">User's Dept</b></label></td>
						<td width="30%"><input type="checkbox" id="m_catalogue" name="m_catalogue" value="m_catalogue" <? if( in_array( "m_catalogue", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_catalogue">System Catalogues</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="m_giveaward" name="m_giveaward" value="m_giveaward" <? if( in_array( "m_giveaward", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_giveaward">Give Crew Award</label></td>
						<td width="30%"><input type="checkbox" id="m_npcs2" name="m_npcs2" value="m_npcs2" <? if( in_array( "m_npcs2", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_npcs2">NPCs - <b class="yellow">All Depts</b></label></td>
						<td width="30%"><input type="checkbox" id="m_tour" name="m_tour" value="m_tour" <? if( in_array( "m_tour", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_tour">Tour</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="m_missionnotes" name="m_missionnotes" value="m_missionnotes" <? if( in_array( "m_missionnotes", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_missionnotes">Mission Notes</label></td>
						<td width="30%"><input type="checkbox" id="m_logs" name="m_logs" value="m_logs" <? if( in_array( "m_logs", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_logs">Personal Logs</label></td>
						<td width="30%"><input type="checkbox" id="m_moderation" name="m_moderation" value="m_moderation" <? if( in_array( "m_moderation", $crewAccess ) ) { echo "checked"; } ?>/> <label for="m_moderation">User Post Moderation</label></td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="hidden" name="type" value="manage" />
							<input type="image" src="<?=path_userskin;?>/buttons/update.png" name="action_update_manage" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<div class="pmHeader">
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=access&crew=<?=$crew;?>&tab=3">
					<input type="checkbox" id="reports" name="reports" value="reports" <? if( in_array( "reports", $crewAccess ) ) { echo "checked"; } ?>/>
					<label for="reports"">Reports</label>
				</div>
				<table>
					<tr>
						<td width="30%"><input type="checkbox" id="r_about" name="r_about" value="r_about" <? if( in_array( "r_about", $crewAccess ) ) { echo "checked"; } ?>/> <label for="r_about">About SMS</label></td>
						<td width="30%"><input type="checkbox" id="r_count" name="r_count" value="r_count" <? if( in_array( "r_count", $crewAccess ) ) { echo "checked"; } ?>/> <label for="r_count">Post Count</label></td>
						<td width="30%"><input type="checkbox" id="r_strikes" name="r_strikes" value="r_strikes" <? if( in_array( "r_strikes", $crewAccess ) ) { echo "checked"; } ?>/> <label for="r_strikes">Strike List</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="r_activity" name="r_activity" value="r_activity" <? if( in_array( "r_activity", $crewAccess ) ) { echo "checked"; } ?>/> <label for="r_activity">Crew Activity</label></td>
						<td width="30%"><input type="checkbox" id="r_security" name="r_security" value="r_security" <? if( in_array( "r_security", $crewAccess ) ) { echo "checked"; } ?>/> <label for="r_security">Security Report</label></td>
						<td width="30%"><input type="checkbox" id="r_versions" name="r_versions" value="r_versions" <? if( in_array( "r_versions", $crewAccess ) ) { echo "checked"; } ?>/> <label for="r_versions">Version History</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="r_milestones" name="r_milestones" value="r_milestones" <? if( in_array( "r_milestones", $crewAccess ) ) { echo "checked"; } ?>/> <label for="r_milestones">Crew Milestones</label></td>
						<td width="30%"><input type="checkbox" id="r_progress" name="r_progress" value="r_progress" <? if( in_array( "r_progress", $crewAccess ) ) { echo "checked"; } ?>/> <label for="r_progress">Sim Progress</label></td>
						<td width="30%"></td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="hidden" name="type" value="reports" />
							<input type="image" src="<?=path_userskin;?>/buttons/update.png" name="action_update_reports" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
			<div id="four" class="ui-tabs-container ui-tabs-hide">
				<b class="fontMedium">Notes</b>
				<ul class="version">
					<li>Users with All Account privileges do not need Own Account privileges</li>
					<li>Users with NPC Bios privileges do not need Own Bio privileges</li>
					<li>Users with All Bios privileges do not need NPC Bios or Own Bio privileges</li>
				</ul>
				
				<div class="pmHeader">
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=access&crew=<?=$crew;?>&tab=4">
					<input type="checkbox" id="user" name="user" value="user" <? if( in_array( "user", $crewAccess ) ) { echo "checked"; } ?>/>
					<label for="user">User</label>
				</div>
				<table>
					<tr>
						<td width="30%"><input type="checkbox" id="u_account1" name="u_account1" value="u_account1" <? if( in_array( "u_account1", $crewAccess ) ) { echo "checked"; } ?>/> <label for="u_account1">Account - <b class="yellow">Own Account</b></label></td>
						<td width="30%"><input type="checkbox" id="u_bio2" name="u_bio2" value="u_bio2" <? if( in_array( "u_bio2", $crewAccess ) ) { echo "checked"; } ?>/> <label for="u_bio2">Biography - <b class="yellow">NPC Bios</b></label></td>
						<td width="30%"><input type="checkbox" id="u_options" name="u_options" value="u_options" <? if( in_array( "u_options", $crewAccess ) ) { echo "checked"; } ?>/> <label for="u_options">Site Options</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="u_account2" name="u_account2" value="u_account2" <? if( in_array( "u_account2", $crewAccess ) ) { echo "checked"; } ?>/> <label for="u_account2">Account - <b class="yellow">All Accounts</b></label></td>
						<td width="30%"><input type="checkbox" id="u_bio3" name="u_bio3" value="u_bio3" <? if( in_array( "u_bio3", $crewAccess ) ) { echo "checked"; } ?>/> <label for="u_bio3">Biography - <b class="yellow">All Bios</b></label></td>
						<td width="30%"><input type="checkbox" id="u_stats" name="u_stats" value="u_stats" <? if( in_array( "u_stats", $crewAccess ) ) { echo "checked"; } ?>/> <label for="u_stats">User Stats</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="u_nominate" name="u_nominate" value="u_nominate" <? if( in_array( "u_nominate", $crewAccess ) ) { echo "checked"; } ?>/> <label for="u_nominate">Award Nominations</label></td>
						<td width="30%"><input type="checkbox" id="u_inbox" name="u_inbox" value="u_inbox" <? if( in_array( "u_inbox", $crewAccess ) ) { echo "checked"; } ?>/> <label for="u_inbox">Private Messages Inbox</label></td>
						<td width="30%"></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="u_bio1" name="u_bio1" value="u_bio1" <? if( in_array( "u_bio1", $crewAccess ) ) { echo "checked"; } ?>/> <label for="u_bio1">Biography - <b class="yellow">Own Bio</b></label></td>
						<td width="30%"><input type="checkbox" id="u_status" name="u_status" value="u_status" <? if( in_array( "u_status", $crewAccess ) ) { echo "checked"; } ?>/> <label for="u_status">Request Status Change</label></td>
						<td width="30%"></td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="hidden" name="type" value="user" />
							<input type="image" src="<?=path_userskin;?>/buttons/update.png" name="action_update_user" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
			<div id="five" class="ui-tabs-container ui-tabs-hide">
				<b class="fontMedium">Notes</b>
				<ul class="version">
					<li>In order to use the approve features or the user access feature, the user must also have MANAGE access</li>
				</ul>
		
				<div class="pmHeader">Other</div>
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=access&crew=<?=$crew;?>&tab=5">
				<table>
					<tr>
						<td width="30%"><input type="checkbox" id="x_approve_docking" name="x_approve_docking" value="x_approve_docking" <? if( in_array( "x_approve_docking", $crewAccess ) ) { echo "checked"; } ?>/> <label for="x_approve_docking">Approve Docked Ships</label></td>
						<td width="30%"><input type="checkbox" id="x_approve_posts" name="x_approve_posts" value="x_approve_posts" <? if( in_array( "x_approve_posts", $crewAccess ) ) { echo "checked"; } ?>/> <label for="x_approve_posts">Approve Posts</label></td>
						<td width="30%"><input type="checkbox" id="x_skindev" name="x_skindev" value="x_skindev" <? if( in_array( "x_skindev", $crewAccess ) ) { echo "checked"; } ?>/> <label for="x_skindev">Skin Development</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="x_approve_logs" name="x_approve_logs" value="x_approve_logs" <? if( in_array( "x_approve_logs", $crewAccess ) ) { echo "checked"; } ?>/> <label for="x_approve_logs">Approve Logs</label></td>
						<td width="30%"><input type="checkbox" id="x_approve_users" name="x_approve_users" value="x_approve_users" <? if( in_array( "x_approve_users", $crewAccess ) ) { echo "checked"; } ?>/> <label for="x_approve_users">Approve Users</label></td>
						<td width="30%"><input type="checkbox" id="x_update" name="x_update" value="x_update" <? if( in_array( "x_update", $crewAccess ) ) { echo "checked"; } ?>/> <label for="x_update">Update Site</label></td>
					</tr>
					<tr>
						<td width="30%"><input type="checkbox" id="x_approve_news" name="x_approve_news" value="x_approve_news" <? if( in_array( "x_approve_news", $crewAccess ) ) { echo "checked"; } ?>/> <label for="x_approve_news">Approve News Items</label></td>
						<td width="30%"><input type="checkbox" id="x_menu" name="x_menu" value="x_menu" <? if( in_array( "x_menu", $crewAccess ) ) { echo "checked"; } ?>/> <label for="x_menu">Menu Management</label></td>
						<td width="30%"><input type="checkbox" id="x_access" name="x_access" value="x_access" <? if( in_array( "x_access", $crewAccess ) ) { echo "checked"; } ?>/> <label for="x_access">User Access Levels</label></td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="hidden" name="type" value="other" />
							<input type="image" src="<?=path_userskin;?>/buttons/update.png" name="action_update_other" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
		</div> <!-- close container-1 -->
	
		<? } ?>
	</div>

<? } else { errorMessage( "user access" ); } ?>