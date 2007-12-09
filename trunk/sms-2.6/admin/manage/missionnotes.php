<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/manage/missionnotes.php
Purpose: Page that moderates the notes for each mission

System Version: 2.5.0
Last Modified: 2007-04-17 1406 EST
**/

/* access check */
if( in_array( "m_missionnotes", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$actionUpdate = $_POST['action_update_x'];
	$sec = $_GET['sec'];
	
	/* set the default section */
	if( !$sec ) {
		$sec = "current";
	}
	
	/* if the POST action is update */
	if( $actionUpdate ) {
		
		/* define the POST variables */
		$missionid = $_POST['missionid'];
		$missionNotes = addslashes( $_POST['missionNotes'] );
		
		/* do the update query */
		$updateNotes = "UPDATE sms_missions SET ";
		$updateNotes.= "missionNotes = '$missionNotes' WHERE missionid = '$missionid' LIMIT 1";
		$result = mysql_query( $updateNotes );
		
		/* optimize the table */
		optimizeSQLTable( "sms_missions" );
		
		/* strip the slashes from the vars */
		$missionNotes = stripslashes( $missionNotes );
		
	}

?>

	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $updateNotes );
		
		if( !empty( $check->query ) ) {
			$check->message( "mission note", "update" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage Mission Notes</span><br /><br />
		Mission notes allow COs to give the crew important information before they post. Use it to outline part
		of a mission or to remind people of in-character assignments. Mission notes can be accessed from the
		mission posting pages (single or joint) as well as the Mission Notes link in the Post section of the menu.
		<br /><br />
		
		<div id="subnav">
			<ul>
				<li <? if( $sec == "current" ) { echo "id='current'"; } ?>><a href="<?=$webLocation;?>admin.php?page=manage&sub=missionnotes&sec=current">Current Mission</a></li>
				<li <? if( $sec == "completed" ) { echo "id='current'"; } ?>><a href="<?=$webLocation;?>admin.php?page=manage&sub=missionnotes&sec=completed">Completed Missions</a></li>
				<li <? if( $sec == "upcoming" ) { echo "id='current'"; } ?>><a href="<?=$webLocation;?>admin.php?page=manage&sub=missionnotes&sec=upcoming">Upcoming Missions</a></li>				
			</ul>
		</div>
	
		<div class="tabcontainer">
		
		<? if( $sec == "current" ) { ?>
		<br /><br />
		<table>
			<?
		
			$missions = "SELECT missionid, missionTitle, missionNotes ";
			$missions.= "FROM sms_missions WHERE missionStatus = '$sec' ";
			$missions.= "ORDER BY missionOrder DESC";
			$missionsResult = mysql_query( $missions );
			
			while( $notes = mysql_fetch_array( $missionsResult ) ) {
				extract( $notes, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=missionnotes&sec=<?=$sec;?>">
			<tr>
				<td class="tableCellLabel">
					<? printText( $missionTitle );?>
					<input type="hidden" name="missionid" value="<?=$missionid;?>" />
				</td>
				<td>&nbsp;</td>
				<td>
					<textarea name="missionNotes" rows="10" class="wideTextArea"><?=stripslashes( $missionNotes );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td align="right">
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update" class="button" value="Update" />
				</td>
			</tr>
			</form>
		<? } ?>
		</table>
		
		<? } if( $sec == "completed" ) { ?>
		<br /><br />
		<table>
			<?
		
			$missions = "SELECT missionid, missionTitle, missionNotes ";
			$missions.= "FROM sms_missions WHERE missionStatus = '$sec' ";
			$missions.= "ORDER BY missionOrder DESC";
			$missionsResult = mysql_query( $missions );
			
			while( $notes = mysql_fetch_array( $missionsResult ) ) {
				extract( $notes, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=missionnotes&sec=<?=$sec;?>">
			<tr>
				<td class="tableCellLabel">
					<? printText( $missionTitle );?>
					<input type="hidden" name="missionid" value="<?=$missionid;?>" />
				</td>
				<td>&nbsp;</td>
				<td>
					<textarea name="missionNotes" rows="10" class="wideTextArea"><?=stripslashes( $missionNotes );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td align="right">
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update" class="button" value="Update" />
				</td>
			</tr>
			<tr>
				<td colspan="3" height="25"></td>
			</tr>
			</form>
		<? } ?>
		</table>
		
		<? } if( $sec == "upcoming" ) { ?>
		<br /><br />
		<table>
			<?
		
			$missions = "SELECT missionid, missionTitle, missionNotes ";
			$missions.= "FROM sms_missions WHERE missionStatus = '$sec' ";
			$missions.= "ORDER BY missionOrder DESC";
			$missionsResult = mysql_query( $missions );
			
			while( $notes = mysql_fetch_array( $missionsResult ) ) {
				extract( $notes, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=missionnotes&sec=<?=$sec;?>">
			<tr>
				<td class="tableCellLabel">
					<? printText( $missionTitle );?>
					<input type="hidden" name="missionid" value="<?=$missionid;?>" />
				</td>
				<td>&nbsp;</td>
				<td>
					<textarea name="missionNotes" rows="10" class="wideTextArea"><?=stripslashes( $missionNotes );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td align="right">
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update" class="button" value="Update" />
				</td>
			</tr>
			<tr>
				<td colspan="3" height="25"></td>
			</tr>
			</form>
		<? } ?>
		</table>
		<? } ?>
		
		</div> <!-- close the tab container -->
		
	</div>

<? } else { errorMessage( "mission notes management" ); } ?>