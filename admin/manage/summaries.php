<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/manage/summaries.php
Purpose: Page that moderates the various messages found throughout SMS

System Version: 2.5.0
Last Modified: 2007-04-17 1405 EST
**/

/* access check */
if( in_array( "m_missionsummaries", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$actionUpdate = $_POST['action_update_x'];
	$sec = $_GET['sec'];
	
	/* set the default tab section */
	if( !$sec ) {
		$sec = "current";
	}
	
	/* if the POST action is update */
	if( $actionUpdate ) {
		
		/* define the POST variables */
		$missionid = $_POST['missionid'];
		$missionSummary = addslashes( $_POST['missionSummary'] );
		
		/* do the update query */
		$updateSummary = "UPDATE sms_missions SET ";
		$updateSummary.= "missionSummary = '$missionSummary' WHERE missionid = '$missionid' LIMIT 1";
		$result = mysql_query( $updateSummary );
		
		/* optimize the table */
		optimizeSQLTable( "sms_missions" );
		
		/* strip the slashes from the vars */
		$missionSummary = stripslashes( $missionSummary );
		
	}

?>

	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $updateSummary );
		
		if( !empty( $check->query ) ) {
			$check->message( "mission summary", "update" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage Mission Summaries</span><br /><br />
		Mission summaries allow you to summarize your past and current missions so that new users can
		get a feel for what your crew has done in-character.  It's also a great way for players that enter
		during a mission or current players who have fallen behind to get caught up quickly.<br /><br />
		
		<div id="subnav">
			<ul>
				<li <? if( $sec == "current" ) { echo "id='current'"; } ?>><a href="<?=$webLocation;?>admin.php?page=manage&sub=summaries&sec=current">Current Mission</a></li>
				<li <? if( $sec == "completed" ) { echo "id='current'"; } ?>><a href="<?=$webLocation;?>admin.php?page=manage&sub=summaries&sec=completed">Completed Missions</a></li>
				<li <? if( $sec == "upcoming" ) { echo "id='current'"; } ?>><a href="<?=$webLocation;?>admin.php?page=manage&sub=summaries&sec=upcoming">Upcoming Missions</a></li>
			</ul>
		</div>
	
		<div class="tabcontainer">
		
		<? if( $sec == "current" ) { ?>
		<br /><br />
		<table>
			<?
		
			$missions = "SELECT missionid, missionTitle, missionSummary ";
			$missions.= "FROM sms_missions WHERE missionStatus = '$sec' ";
			$missions.= "ORDER BY missionOrder DESC";
			$missionsResult = mysql_query($missions);
			
			while( $summary = mysql_fetch_array( $missionsResult ) ) {
				extract( $summary, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=summaries">
			<tr>
				<td class="tableCellLabel">
					<? printText( $missionTitle );?>
					<input type="hidden" name="missionid" value="<?=$missionid;?>" />
				</td>
				<td>&nbsp;</td>
				<td>
					<textarea name="missionSummary" rows="15" class="wideTextArea"><?=stripslashes( $missionSummary );?></textarea>
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
		
			$missions = "SELECT missionid, missionTitle, missionSummary ";
			$missions.= "FROM sms_missions WHERE missionStatus = '$sec' ";
			$missions.= "ORDER BY missionOrder DESC";
			$missionsResult = mysql_query($missions);
			
			while( $summary = mysql_fetch_array( $missionsResult ) ) {
				extract( $summary, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=summaries">
			<tr>
				<td class="tableCellLabel">
					<? printText( $missionTitle );?>
					<input type="hidden" name="missionid" value="<?=$missionid;?>" />
				</td>
				<td>&nbsp;</td>
				<td>
					<textarea name="missionSummary" rows="15" class="wideTextArea"><?=stripslashes( $missionSummary );?></textarea>
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
		
			$missions = "SELECT missionid, missionTitle, missionSummary ";
			$missions.= "FROM sms_missions WHERE missionStatus = '$sec' ";
			$missions.= "ORDER BY missionOrder DESC";
			$missionsResult = mysql_query($missions);
			
			while( $summary = mysql_fetch_array( $missionsResult ) ) {
				extract( $summary, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=summaries">
			<tr>
				<td class="tableCellLabel">
					<? printText( $missionTitle );?>
					<input type="hidden" name="missionid" value="<?=$missionid;?>" />
				</td>
				<td>&nbsp;</td>
				<td>
					<textarea name="missionSummary" rows="15" class="wideTextArea"><?=stripslashes( $missionSummary );?></textarea>
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

<? } else { errorMessage( "mission summaries management" ); } ?>