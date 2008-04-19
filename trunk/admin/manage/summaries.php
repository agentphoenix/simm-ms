<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/summaries.php
Purpose: Page that moderates the various messages found throughout SMS

System Version: 2.6.0
Last Modified: 2008-04-19 1734 EST
**/

/* access check */
if( in_array( "m_missionsummaries", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	
	if(isset($_GET['t']) && is_numeric($_GET['t'])) {
		$tab = $_GET['t'];
	} else {
		$tab = 1;
	}
	
	/* if the POST action is update */
	if(isset($_POST['action_update_x']))
	{
		if(isset($_POST['missionid']) && is_numeric($_POST['missionid'])) {
			$missionid = $_POST['missionid'];
		} else {
			$missionid = NULL;
		}
		
		$update = "UPDATE sms_missions SET missionSummary = %s WHERE missionid = $missionid LIMIT 1";
		$query = sprintf($update, escape_string($_POST['missionSummary']));
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_missions" );
	}
	
	$currentCount = "SELECT * FROM sms_missions WHERE missionStatus = 'current'";
	$currentCountR = mysql_query($currentCount);
	$current = mysql_num_rows($currentCountR);
	
	$completedCount = "SELECT * FROM sms_missions WHERE missionStatus = 'completed'";
	$completedCountR = mysql_query( $completedCount );
	$complete = mysql_num_rows( $completedCountR );
	
	$upcomingCount = "SELECT * FROM sms_missions WHERE missionStatus = 'upcoming'";
	$upcomingCountR = mysql_query( $upcomingCount );
	$upcoming = mysql_num_rows( $upcomingCountR );

?>

	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "mission summary", "update" );
			$check->display();
		}
		
		?>
		
		<script type="text/javascript">
			$(document).ready(function(){
				$('#container-1 > ul').tabs(<?php echo $tab; ?>);
			});
		</script>
		
		<span class="fontTitle">Manage Mission Summaries</span><br /><br />
		Mission summaries allow you to summarize your past and current missions so that new users can get a feel for what your crew has done in-character.  It's also a great way for players that enter during a mission or current players who have fallen behind to get caught up quickly.<br /><br />
		
		<div id="container-1">
			<ul>
				<li><a href="#one"><span>Current Mission</span></a></li>
				<li><a href="#two"><span>Completed Missions</span></a></li>
				<li><a href="#three"><span>Upcoming Missions</span></a></li>
			</ul>
			
			<div id="one" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				if($current == 0)
				{
					echo "<strong class='orange fontMedium'>No current missions</strong>";
				}
				else
				{
				
				?>
				<table>
					<?php

					while($summary = mysql_fetch_array($currentCountR)) {
						extract($summary, EXTR_OVERWRITE);

					?>
					<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=summaries&t=1">
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
				<?php } ?>
				</table>
				<?php } ?>
			</div>
			
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				if($complete == 0)
				{
					echo "<strong class='orange fontMedium'>No completed missions</strong>";
				}
				else
				{
				
				?>
				<table>
					<?php

					while($summary = mysql_fetch_array($completedCountR)) {
						extract($summary, EXTR_OVERWRITE);

					?>
					<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=summaries&t=2">
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
				<?php } ?>
				</table>
				<?php } ?>
			</div>
			
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				if($upcoming == 0)
				{
					echo "<strong class='orange fontMedium'>No upcoming missions</strong>";
				}
				else
				{
				
				?>
				<table>
					<?php

					while( $summary = mysql_fetch_array( $upcomingCountR ) ) {
						extract( $summary, EXTR_OVERWRITE );

					?>
					<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=summaries&t=3">
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
				<?php } ?>
				</table>
				<?php } ?>
			</div>
		</div>
		
	</div>

<? } else { errorMessage( "mission summaries management" ); } ?>