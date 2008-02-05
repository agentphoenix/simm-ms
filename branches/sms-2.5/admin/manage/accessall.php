<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/accessall.php
Purpose: Page to display all of a user's access levels

System Version: 2.5.2
Last Modified: 2007-08-08 2150 EST
**/

/* set the page class */
$pageClass = "admin";
$subMenuClass = "user";

/* access check */
if( in_array( "x_access", $sessionAccess ) ) {

	/* set up the vars */
	$actionAdd = $_POST['action_add_x'];
	$actionRemove = $_POST['action_remove_x'];
	
	/* set up an array of the different access sections */
	$arrayAccessGroups = array(
		0 => "post",
		1 => "manage",
		2 => "reports",
		3 => "user",
		4 => "others"
	);
	
	/* set up an array of the different access levels */
	$arrayAccessLevels = array(
		"post" => array(
			0 => array( "p_mission", "Write Mission Post" ),
			1 => array( "p_jp", "Write Joint Mission Post" ),
			2 => array( "p_log", "Write Personal Log" ),
			3 => array( "p_news", "Write News Item" ),
			4 => array( "p_pm", "Send Private Message" ),
			5 => array( "p_missionnotes", "Mission Notes" ),
			6 => array( "p_addmission", "Add Mission Post" ),
			7 => array( "p_addjp", "Add Joint Mission Post" ),
			8 => array( "p_addlog", "Add Personal Log" ),
			9 => array( "p_addnews", "Add News Item" )
		),
		"manage" => array(
			0 => array( "m_awards", "Awards" ),
			1 => array( "m_coc", "Chain of Command" ),
			2 => array( "m_crew", "All Characters" ),
			3 => array( "m_createcrew", "Add Character" ),
			4 => array( "m_database", "Database" ),
			5 => array( "m_decks", "Deck Listing" ),
			6 => array( "m_departments", "Departments" ),
			7 => array( "m_docking", "Starship Docking" ),
			8 => array( "m_giveaward", "Give Crew Award" ),
			9 => array( "m_missionnotes", "Mission Notes" ),
			10 => array( "m_posts", "Mission Posts" ),
			11 => array( "m_missionsummaries", "Mission Summaries" ),
			12 => array( "m_missions", "Missions" ),
			13 => array( "m_newscat1", "News Category 1" ),
			14 => array( "m_newscat2", "News Category 2" ),
			15 => array( "m_newscat3", "News Category 3" ),
			16 => array( "m_news", "News Items" ),
			17 => array( "m_npcs1", "NPCs 1" ),
			18 => array( "m_npcs2", "NPCs 2" ),
			19 => array( "m_logs", "Personal Logs" ),
			20 => array( "m_positions", "Positions" ),
			21 => array( "m_ranks", "Ranks" ),
			22 => array( "m_removeaward", "Remove Award" ),
			23 => array( "m_globals", "Site Globals" ),
			24 => array( "m_messages", "Site Messages" ),
			25 => array( "m_specs", "Specifications" ),
			26 => array( "m_strike", "Strikes" ),
			27 => array( "m_catalogue", "System Catalogues (disabled)" ),
			28 => array( "m_tour", "Tour" ),
			29 => array( "m_moderation", "User Moderation" )
		),
		"reports" => array(
			0 => array( "r_about", "About SMS" ),
			1 => array( "r_activity", "Crew Activity" ),
			2 => array( "r_count", "Post Count" ),
			3 => array( "r_milestones", "r_milestones" ),
			4 => array( "r_progress", "Sim Progress" ),
			5 => array( "r_strikes", "Strike List" ),
			6 => array( "r_versions", "Version History" )
		),
		"user" => array(
			0 => array( "u_account1", "User Account 1" ),
			1 => array( "u_account2", "User Account 2" ),
			2 => array( "u_bio1", "Biography 1" ),
			3 => array( "u_bio2", "Biography 2" ),
			4 => array( "u_bio3", "Biography 3" ),
			5 => array( "u_inbox", "Private Messages" ),
			6 => array( "u_status", "Status Change" ),
			7 => array( "u_site", "Site Options" ),
			8 => array( "u_nominate", "Award Nominations" )
		),
		"others" => array(
			0 => array( "x_access", "User Access Management" ),
			1 => array( "x_update", "Update SMS" ),
			2 => array( "x_approve_users", "Approve Users" ),
			3 => array( "x_approve_posts", "Approve Mission Posts" ),
			4 => array( "x_approve_logs", "Approve Personal Logs" ),
			5 => array( "x_approve_news", "Approve News Items" ),
			6 => array( "x_approve_docking", "Approve Docking Request" )
		)
	);
	
	/* explode the access fields into arrays */
	$postAccess = explode( ",", $accessPost );
	$manageAccess = explode( ",", $accessManage );
	$reportsAccess = explode( ",", $accessReports );
	$userAccess = explode( ",", $accessUser );
	$otherAccess = explode( ",", $accessOthers );
	
	if( isset( $actionAdd ) || isset( $actionRemove ) ) {
		
		/* pop the last 3 items off the POST array (x, y, and value) */
		for( $i=1; $i<4; $i++ ) {
			array_pop( $_POST );
		}
		
		/* set up the type being updated */
		$type = ucfirst( $_POST['type'] );
		$typeL = strtolower( $type );
		
		/* get the active crew */
		$get = "SELECT * FROM sms_crew WHERE crewType = 'active'";
		$getR = mysql_query( $get );
		
		/* loop through the results */
		while( $fetch = mysql_fetch_assoc( $getR ) ) {
			extract( $fetch, EXTR_OVERWRITE );
			
			/* break the string into an array */
			${access.$type} = explode( ",", ${access.$type} );
			
			/* find out if the one we're trying to add is already there or not */
			$keyNum = array_search( $_POST[$typeL], ${access.$type} );
			
			/* if the item was found in the array, remove it */
			if( $keyNum === NULL || $keyNum === FALSE || $keyNum === "" || $keyNum === 0 ) {
				/*
				php does something strange here where it doesn't properly evaluate the
				outcome of the array_search() function and causes LOTS of problems, so
				we have to set this up to do nothing if the result of the array_search()
				is NULL, FALSE, blank or a zero
				*/
			} else {
				unset( ${access.$type}[$keyNum] );
				${access.$type} = array_values( ${access.$type} );
			}
			
			/* if we're trying to add, push it onto the end of the array */
			if( isset( $actionAdd ) ) {
				array_push( ${access.$type}, $_POST[$typeL] );
			}
			
			/* implode the array into a string */
			$string = implode( ",", ${access.$type} );
			
			/* update each crew member */
			$query = "UPDATE sms_crew SET access$type = '$string' WHERE crewid = '$crewid'";
			$result = mysql_query( $query );
			
			/* set the appropriate action */
			if( isset( $actionAdd ) ) {
				$action = "add";
			} elseif( isset( $actionRemove ) ) {
				$action = "remove";
			}
		
		}
	
	}

?>

	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "crew access levels", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Crew Access Levels</span><br /><br />
		From here you can add or remove an access level for every member of the active crew. Currently
		only one item can be added or removed at a time from this list. <b class="yellow">Use great
		caution when adding or removing access for the entire crew!</b> The same rules/notes that apply
		for access levels also apply here. Please see the notes associated with the access levels for
		more information.<br /><br />
		
		<table>
			<? foreach( $arrayAccessGroups as $key1 => $value1 ) { ?>
			<form method="post" name="form1" action="<?=$webLocation;?>admin.php?page=manage&sub=accessall">
			<tr>
				<td class="fontLarge" align="right"><b><?=ucfirst( $value1 );?></b></td>
				<td></td>
				<td>
					<?php if( $value1 == "others" ) {} else { ?>
					<input type="radio" name="<?=$value1;?>" value="<?=$value1;?>" />
					<?php } ?>
				</td>
			</tr>
			
			<? foreach( $arrayAccessLevels[$value1] as $key2 => $value2 ) { ?>
			<tr>
				<td class="tableCellLabel"><?=( $value2[1] );?></td>
				<td></td>
				<td>
					<input type="radio" name="<?=$value1;?>" value="<?=$value2[0];?>" />
				</td>
			</tr>
			<? } ?>
			
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<tr>
				<td colspan="3">
					<input type="hidden" name="type" value="<?=$value1;?>" />
					<input type="image" src="<?=path_userskin;?>buttons/reset.png" class="button" onClick="clear_radio_buttons();" />
					
					<!-- add button -->
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/add.png\" name=\"action_add\" value=\"Add\" class=\"button\" onClick=\"javascript:return confirm('Are you sure you want to add this <?=strtoupper( $value1 );?> privilege for the entire crew?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/add.png" name="action_add" value="Add" class="button" />
					</noscript>
					
					<!-- remove button -->
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/remove.png\" name=\"action_remove\" value=\"Remove\" class=\"button\" onClick=\"javascript:return confirm('Are you sure you want to remove this <?=strtoupper( $value1 );?> privilege for the entire crew?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/remove.png" name="action_remove" value="Remove" class="button" />
					</noscript>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			</form>
			<? } /* close the group foreach */ ?>
		</table>
		
		<script language="JavaScript">
			<!--
			function clear_radio_buttons()
			{
				 for (var i = 0; i < document.form1.radio1.length; i++)
				 {
					  document.form1.radio1[i].checked = false;
				 }
			}
			//-->
		</script>
		
	</div>

<? } else { errorMessage( "crew access level management" ); } ?>