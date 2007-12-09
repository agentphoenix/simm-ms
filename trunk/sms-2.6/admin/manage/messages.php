<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/messages.php
Purpose: Page that moderates the various messages found throughout SMS

System Version: 2.6.0
Last Modified: 2007-08-21 1006 EST
**/

/* access check */
if( in_array( "m_messages", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$action = $_POST['action_update_x'];

	/* if the POST action is update */
	if( $action ) {
	
		/* define the POST variables */
		$welcomeMessage = addslashes( $_POST['welcomeMessage'] );
		$shipMessage = addslashes( $_POST['shipMessage'] );
		$simmMessage = addslashes( $_POST['simmMessage'] );
		$shipHistory = addslashes( $_POST['shipHistory'] );
		$cpMessage = addslashes( $_POST['cpMessage'] );
		$joinDisclaimer = addslashes( $_POST['joinDisclaimer'] );
		$samplePostQuestion = addslashes( $_POST['samplePostQuestion'] );
		$rules = addslashes( $_POST['rules'] );
		$acceptMessage = addslashes( $_POST['acceptMessage'] );
		$rejectMessage = addslashes( $_POST['rejectMessage'] );
		$siteCredits = addslashes( $_POST['siteCredits'] );
		
		/* do the update query */
		$updateMessages = "UPDATE sms_messages SET ";
		$updateMessages.= "welcomeMessage = '$welcomeMessage', shipMessage = '$shipMessage', ";
		$updateMessages.= "simmMessage = '$simmMessage', shipHistory = '$shipHistory', cpMessage = '$cpMessage', ";
		$updateMessages.= "joinDisclaimer = '$joinDisclaimer', samplePostQuestion = '$samplePostQuestion', ";
		$updateMessages.= "rules = '$rules', acceptMessage = '$acceptMessage', rejectMessage = '$rejectMessage', ";
		$updateMessages.= "siteCredits = '$siteCredits' WHERE messageid = '1' LIMIT 1";
		$result = mysql_query( $updateMessages );
		
		/* strip the slashes from the vars */
		$welcomeMessage = stripslashes( $welcomeMessage );
		$shipMessage = stripslashes( $shipMessage );
		$simmMessage = stripslashes( $simmMessage );
		$shipHistory = stripslashes( $shipHistory );
		$cpMessage = stripslashes( $cpMessage );
		$joinDisclaimer = stripslashes( $joinDisclaimer );
		$samplePostQuestion = stripslashes( $samplePostQuestion );
		$rules = stripslashes( $rules );
		$acceptMessage = stripslashes( $acceptMessage );
		$rejectMessage = stripslashes( $rejectMessage );
		$siteCredits = stripslashes( $siteCredits );
		
		/* optimize the table */
		optimizeSQLTable( "sms_messages" );
		
	}
	
	/* strip the slashes from the vars */
	$welcomeMessage = stripslashes( $welcomeMessage );
	$shipMessage = stripslashes( $shipMessage );
	$simmMessage = stripslashes( $simmMessage );
	$shipHistory = stripslashes( $shipHistory );
	$cpMessage = stripslashes( $cpMessage );
	$joinDisclaimer = stripslashes( $joinDisclaimer );
	$samplePostQuestion = stripslashes( $samplePostQuestion );
	$rules = stripslashes( $rules );
	$acceptMessage = stripslashes( $acceptMessage );
	$rejectMessage = stripslashes( $rejectMessage );
	$siteCredits = stripslashes( $siteCredits );

?>

	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $updateMessages );
		
		if( !empty( $check->query ) ) {
			$check->message( "site messages", "update" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Site Messages</span>
			
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=messages">
			<table>
				<tr>
					<td class="tableCellLabel">Welcome Message</td>
					<td>&nbsp;</td>
					<td>
						<textarea name="welcomeMessage" class="desc" rows="10"><?=$welcomeMessage;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel"><b>Ship Message</b></td>
					<td>&nbsp;</td>
					<td>
						<textarea name="shipMessage" class="desc" rows="10"><?=$shipMessage;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel"><b>Simm Message</b></td>
					<td>&nbsp;</td>
					<td>
						<textarea name="simmMessage" class="desc" rows="10"><?=$simmMessage;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel"><b>Ship History</b></td>
					<td>&nbsp;</td>
					<td>
						<textarea name="shipHistory" class="desc" rows="10"><?=$shipHistory;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel"><b>Control Panel Welcome Message</b></td>
					<td>&nbsp;</td>
					<td>
						<textarea name="cpMessage" class="desc" rows="10"><?=$cpMessage;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel"><b>Join Disclaimer</b></td>
					<td>&nbsp;</td>
					<td>
						<textarea name="joinDisclaimer" class="desc" rows="10"><?=$joinDisclaimer;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel">
						Sample Post Qustion <? if( $useSamplePost == "n" ) { ?><br />
						<span class="fontSmall"><b class="yellow">You have chosen not to use a sample 
						post. If you would like to turn this feature on, please use the site globals panel to 
						do so.</b></span>
						<? } ?>
					</td>
					<td>&nbsp;</td>
					<td>
						<textarea name="samplePostQuestion" class="desc" rows="10"><?=$samplePostQuestion;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel"><b>Simm Rules</b></td>
					<td>&nbsp;</td>
					<td>
						<textarea name="rules" class="desc" rows="10"><?=$rules;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Site Credits</td>
					<td>&nbsp;</td>
					<td>
						<textarea name="siteCredits" class="desc" rows="10"><?=$siteCredits;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td colspan="3" class="fontLarge"><b>Emails</b></td>
				</tr>
				<tr class="fontNormal">
					<td colspan="3">
						Define a form letter here that will be displayed when you accept or reject a player.
						Before accepting or rejecting the player, you will have the option of changing the 
						letter to be more specific to the situation.<br /><br />
						
						Acceptance and rejection messages now allow keywords to be placed in the messages.
						These keywords will be replaced out of the database before the message is sent. The
						following keywords are available for use in the acceptance and rejection messages:
						<br /><br />
						
						<span class="yellow">#player# - Player's Name<br />
						#ship# - Ship Name<br />
						#position# - Position<br />
						#rank# - Rank</span>
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Player Acceptance Email</td>
					<td>&nbsp;</td>
					<td>
						<textarea name="acceptMessage" class="desc" rows="10"><?=$acceptMessage;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Player Rejection Email</td>
					<td>&nbsp;</td>
					<td>
						<textarea name="rejectMessage" class="desc" rows="10"><?=$rejectMessage;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td colspan="2"></td>
					<td><input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" /></td>
				</tr>
			</table>
		</form>
	</div>

<? } else { errorMessage( "site messages management" ); } ?>