<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/user/nominate.php
Purpose: Page to nominate another crew member for an award

System Version: 2.6.0
Last Modified: 2008-04-20 1939 EST
**/

/* access check */
if( in_array( "u_nominate", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "user";
	$result = false;
	$query = false;
	
	if(isset($_GET['t']) && is_numeric($_GET['t']))
	{
		$tab = $_GET['t'];
	}
	else
	{
		$tab = 1;
	}

	if(isset($_POST['action_x']))
	{
		
		$insert = "INSERT INTO sms_awards_queue ( crew, nominated, award, reason ) VALUES ( %d, %d, %d, %s )";
		
		/* run the query through sprintf and the safety function to scrub for security issues */
		$query = sprintf(
			$insert,
			escape_string( $_POST['nominator'] ),
			escape_string( $_POST['crew'] ),
			escape_string( $_POST['award'] ),
			escape_string( $_POST['reason'] )
		);

		/* run the query */
		$result = mysql_query( $query );
		
		optimizeSQLTable( "sms_awards_queue" );
	
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
		
		/* set the TO email addresses */
		$emFetch = "SELECT crewid, email FROM sms_crew WHERE (accessManage LIKE 'm_giveaward,%' OR accessManage LIKE '%,m_giveaward' ";
		$emFetch.= "OR accessManage LIKE '%,m_giveaward,%')";
		$emFetchR = mysql_query($emFetch);
		
		$email_array = array();
		
		while($em_raw = mysql_fetch_array($emFetchR)) {
			extract($em_raw, EXTR_OVERWRITE);
			
			$email_array[] = $em_raw[1];
		}
		
		/* if there isn't anything in the email array, put the CO into the string */
		if(count($email_array) == 0) {
			$to = printCOEmail();
		} else {
			$to = implode(",", $email_array);
		}
		
		/* define the email variables */
		$subject = $emailSubject . " Crew Award Nomination";
		$message = "A member of your crew has nominated a character for an award. The award has been added to the queue and is available for review and activation from the control panel.

Login to your control panel at " . $webLocation . "login.php?action=login to approve or deny this award.";
		
		/* send the nomination email */
		mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
	}

?>
<script type="text/javascript">
	$(document).ready(function() {
		$('#container-1 > ul').tabs(<?php echo $tab; ?>);
	});
</script>

<div class="body">
	
	<?php
	
	$check = new QueryCheck;
	$check->checkQuery( $result, $query );
			
	if( !empty( $check->query ) ) {
		$check->message( "crew award nomination", "submit" );
		$check->display();
	}
	
	?>
	
	<span class="fontTitle">Award Nominations</span><br /><br />
	
	Awards can now be given to both playing characters as well as non-playing characters. If you want to
	nominate a fellow player for an award, use the playing character tab; if you&rsquo;d like to nominate
	an NPC for an award for their in character actions, use the non-playing character tab. Once you nominate
	someone for an award, it will be added to a queue for the CO to review. If the CO agrees with your
	award, they will be able to approve the award. Please refer to the 
	<a href="<?=$webLocation;?>index.php?page=crewawards">Crew Awards page</a> for descriptions and any 
	requirements for the awards.<br /><br />
	
	<div id="container-1">
		<ul>
			<li><a href="#one"><span>Playing Characters</span></a></li>
			<li><a href="#two"><span>Non-Playing Characters</span></a></li>
		</ul>
		
		<div id="one" class="ui-tabs-container ui-tabs-hide">
			<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=nominate&t=1">
			<table>
				<tr>
					<td class="tableCellLabel">Crew Member</td>
					<td>&nbsp;</td>
					<td>
						<select name="crew">

							<?php

							$getCrew = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
							$getCrew.= "FROM sms_crew AS crew, sms_ranks AS rank WHERE ";
							$getCrew.= "crew.crewType = 'active' AND crew.rankid = rank.rankid AND ";
							$getCrew.= "crew.crewid != '$sessionCrewid' ORDER BY crew.rankid ASC";
							$getCrewResult = mysql_query( $getCrew );

							while( $crewArray = mysql_fetch_array( $getCrewResult ) ) {
								extract( $crewArray, EXTR_OVERWRITE );

							?>

							<option value="<?=$crewid;?>"><? printText( $rankName . " " . $firstName . " " . $lastName ); ?></option>

							<?php } ?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Nominated By</td>
					<td>&nbsp;</td>
					<td>
						<? printCrewName( $sessionCrewid, "rank", "noLink" ); ?>
						<input type="hidden" name="nominator" value="<?=$sessionCrewid;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Award</td>
					<td>&nbsp;</td>
					<td>
						<select name="award">

							<?php
							
							$pullAwards = "SELECT awardid, awardName FROM sms_awards ORDER BY awardOrder ASC";
							$pullAwardsResult = mysql_query( $pullAwards );
							
							while( $awardArray = mysql_fetch_array( $pullAwardsResult ) ) {
								extract( $awardArray, EXTR_OVERWRITE );

							?>

							<option value="<?=$awardid;?>"><? printText( $awardName ); ?></option>

							<?php } ?>

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
		
		<div id="two" class="ui-tabs-container ui-tabs-hide">
			<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=nominate&t2">
			<table>
				<tr>
					<td class="tableCellLabel">Crew Member</td>
					<td>&nbsp;</td>
					<td>
						<select name="crew">

							<?php

							$getCrew = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
							$getCrew.= "FROM sms_crew AS crew, sms_ranks AS rank WHERE ";
							$getCrew.= "crew.crewType = 'npc' AND crew.rankid = rank.rankid AND ";
							$getCrew.= "crew.crewid != '$sessionCrewid' ORDER BY crew.rankid ASC";
							$getCrewResult = mysql_query( $getCrew );

							while( $crewArray = mysql_fetch_array( $getCrewResult ) ) {
								extract( $crewArray, EXTR_OVERWRITE );

							?>

							<option value="<?=$crewid;?>"><? printText( $rankName . " " . $firstName . " " . $lastName ); ?></option>

							<?php } ?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Nominated By</td>
					<td>&nbsp;</td>
					<td>
						<? printCrewName( $sessionCrewid, "rank", "noLink" ); ?>
						<input type="hidden" name="nominator" value="<?=$sessionCrewid;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Award</td>
					<td>&nbsp;</td>
					<td>
						<select name="award">

							<?php
							
							$pullAwards = "SELECT awardid, awardName FROM sms_awards WHERE awardCat = 'ic' ORDER BY awardOrder ASC";
							$pullAwardsResult = mysql_query( $pullAwards );
							
							while( $awardArray = mysql_fetch_array( $pullAwardsResult ) ) {
								extract( $awardArray, EXTR_OVERWRITE );

							?>

							<option value="<?=$awardid;?>"><? printText( $awardName ); ?></option>

							<?php } ?>

						</select>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="15"></td>
				</tr>
				<tr>
					<td class="tableCellLabel">
						Please give a brief reason why you believe this NPC deserves this award for their in character actions.
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
	</div>
</div>

<? } else { errorMessage( "crew award nomination" ); } ?>