<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/manage/strikes.php
Purpose: Page to add and remove strikes against players

System Version: 2.5.0
Last Modified: 2007-04-23 2013 EST
**/

/* access check */
if( in_array( "m_strike", $sessionAccess ) ) {

	/* set the page class and variables */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$action = $_POST['action'];
	$button = $_POST['button_x'];
	$strikeCrewid = $_POST['crew'];
	$reason = addslashes( $_POST['reason'] );
	
	if( $button ) {
		
		/* pull how many strikes the player has from the db */
		$strikes = "SELECT strikes FROM sms_crew WHERE crewid = '$strikeCrewid' LIMIT 1";
		$strikesResult = mysql_query( $strikes );
		$strikeVar = mysql_fetch_row( $strikesResult );
			
		/* do logic to figure out how to change the number of strikes */
		if( $action == "add" ) {
			$strikesNew = ( $strikeVar['0'] + 1 );
		} elseif( $action == "delete" ) {
			$strikesNew = ( $strikeVar['0'] - 1 );
		}
			
		/* insert a new row into the strikes table */
		$strikeTable = "INSERT INTO sms_strikes ( strikeid, crewid, strikeDate, reason, number ) ";
		$strikeTable.= "VALUES ( '', '$strikeCrewid', UNIX_TIMESTAMP(), '$reason', '$strikesNew' )";
		$result = mysql_query( $strikeTable );
			
		/* optimize table */
		optimizeSQLTable( "sms_strikes" );
		
		/* update the user table to give the player the new number of strikes */
		$userTable = "UPDATE sms_crew SET strikes = '$strikesNew' WHERE crewid = '$strikeCrewid' LIMIT 1";
		$userTableResult = mysql_query( $userTable );
		
		/* optimize table */
		optimizeSQLTable( "sms_crew" );
		
	}
	
	/* strip the slashes */
	$reason = stripslashes( $reason );
	
?>
	
	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $strikeTable );
		
		if( !empty( $check->query ) ) {
			$check->message( "strike", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage Player Strikes</span><br /><br />
		
		Use this page to add and remove strikes from players. Once you've added or removed a strike,
		you can see the complete <a href="<?=$webLocation;?>admin.php?page=reports&sub=strikes">
		strike list</a>.<br /><br />
			
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=strikes">
		<table cellspacing="1">
			<tr>
				<td class="tableCellLabel">Add or Remove?</td>
				<td>&nbsp;</td>
				<td >
					<input type="radio" id="add" name="action" value="add" checked="yes" /> <label for="add">Add Strike</label>
					<input type="radio" id="remove" name="action" value="delete" /> <label for="remove">Remove Strike</label>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Crew Member</td>
				<td>&nbsp;</td>
				<td>
					<select name="crew">
			
						<?
						
						/* query the users database */
						$getCrew = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
						$getCrew.= "FROM sms_crew AS crew, sms_ranks AS rank WHERE crew.crewType = 'active' ";
						$getCrew.= "AND crew.rankid = rank.rankid ORDER BY crew.rankid ASC";
						$getCrewResult = mysql_query( $getCrew );
						
						/* start looping through what the query returns */
						/* until it runs out of records */
						while( $fetchCrew = mysql_fetch_assoc( $getCrewResult ) ) {
							extract( $fetchCrew, EXTR_OVERWRITE );
						
						?>
					
						<option value="<?=$fetchCrew['crewid'];?>"><?=$rankName . " " . $firstName . " " . $lastName;?></option>
					
						<? } ?>
					
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">
					Please give the reason why this player is being given a strike
				</td>
				<td>&nbsp;</td>
				<td><textarea name="reason" rows="10" class="wideTextArea"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="button" value="Update" class="button" />
				</td>
			</tr>
		</table>
		</form>
	</div>
	
<? } else { errorMessage( "strike management" ); } ?>