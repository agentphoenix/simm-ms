<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/user/nominate.php
Purpose: Page to nominate another crew member for an award

System Version: 2.6.0
Last Modified: 2007-08-21 0917 EST
**/

/* access check */
if( in_array( "u_nominate", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "user";
	$action = $_POST['action_x'];

	if( $action ) {
		
		/* define the POST vars */
		$award = $_POST['award'];
		$crew = $_POST['crew'];
		$nominator = $_POST['nominator'];
		$reason = stripslashes( $_POST['reason'] );
	
		/* set the email author */
		$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankName ";
		$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$userFetch.= "WHERE crew.crewid = '$sessionCrewid' AND crew.rankid = rank.rankid LIMIT 1";
		$userFetchResult = mysql_query( $userFetch );
		
		while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
			extract( $userFetchArray, EXTR_OVERWRITE );
		}
		
		$firstName = str_replace( "'", "", $firstName );
		$lastName = str_replace( "'", "", $lastName );
		
		$from = $rankName . " " . $firstName . " " . $lastName . " < " . $email . " >";
		
		/* define the email variables */
		$to = printCOEmail() . ", " . printXOEmail();
		$subject = $emailSubject . " Crew Award Nomination";
		$message = "Crew Member: $crew
Nominated By: $nominator
Award: $award

Reason: $reason";
		
		/* send the nomination email */
		mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		$result = "true";
	
	}

?>

	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( isset( $action ) ) {
			$check->message( "crew award nomination", "submit" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Crew Award Nomination</span><br /><br />
		
		You can nominate a member of the crew for an award with this form.  Your 
		nomination will be sent to the CO and XO for their review.  Please refer to the 
		<a href="<?=$webLocation;?>index.php?page=crewawards">Crew Awards page</a> 
		for descriptions and any requirements for the awards.<br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=nominate">
		<table>
			<tr>
				<td class="tableCellLabel">Crew Member</td>
				<td>&nbsp;</td>
				<td>
					<select name="crew">
			
						<?
						
						/* query the users database */
						$getCrew = "SELECT crew.firstName, crew.lastName, rank.rankName ";
						$getCrew.= "FROM sms_crew AS crew, sms_ranks AS rank WHERE ";
						$getCrew.= "crew.crewType = 'active' AND crew.rankid = rank.rankid AND ";
						$getCrew.= "crew.crewid != '$sessionCrewid' ORDER BY crew.rankid ASC";
						$getCrewResult = mysql_query( $getCrew );
						
						$author = $firstName . " " . $lastName;
						
						/* start looping through what the query returns */
						/* until it runs out of records */
						while( $crewArray = mysql_fetch_array( $getCrewResult ) ) {
							extract( $crewArray, EXTR_OVERWRITE );
						
						?>
					
						<option value="<? printText( $rankName . ' ' . $firstName . ' ' . $lastName ); ?>"><? printText( $rankName . " " . $firstName . " " . $lastName ); ?></option>
					
						<?php } ?>
					
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Nominated By</td>
				<td>&nbsp;</td>
				<td>
					<? printCrewName( $sessionCrewid, "rank", "noLink" ); ?>
					<input type="hidden" name="nominator" value="<? printCrewName( $sessionCrewid, 'rank', 'noLink' ); ?>" />
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Award</td>
				<td>&nbsp;</td>
				<td>
					<select name="award">
			
						<?
						
						/* query the awards database */
						$pullAwards = "SELECT awardName FROM sms_awards ORDER BY awardOrder ASC";
						$pullAwardsResult = mysql_query( $pullAwards );
						
						/* start looping through what the query returns until it runs out of records */
						while( $awardArray = mysql_fetch_array( $pullAwardsResult ) ) {
							extract( $awardArray, EXTR_OVERWRITE );
						
						?>
			
						<option value="<?=$awardName;?>"><? printText( $awardName ); ?></option>
						
						<? } ?>
		
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td class="tableCellLabel">
					Please give a brief reason why you believe this crew member deserves this award.
				</td>
				<td>&nbsp;</td>
				<td><textarea name="reason" class="desc" rows="6"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td><input type="image" src="<?=path_userskin;?>buttons/nominate.png" name="action" value="Nominate" class="button" /></td>
			</tr>
		</table>
		</form>
		
	</div>

<? } else { errorMessage( "crew award nomination" ); } ?>