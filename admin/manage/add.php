<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/add.php
Purpose: Page to add a player or NPC

System Version: 2.5.5
Last Modified: 2007-11-07 0834 EST
**/

/* access check */
if( in_array( "m_createcrew", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$create = $_POST['action_create_x'];
	
	/* define the POST vars */
	$crewType = $_POST['crewType'];
	$username = $_POST['username'];
	$password = md5( $_POST['password'] );
	$confirmPassword = md5( $_POST['confirmPassword'] );
	$email = $_POST['email'];
	$firstName = addslashes( $_POST['firstName'] );
	$middleName = addslashes( $_POST['middleName'] );
	$lastName = addslashes( $_POST['lastName'] );
	$gender = $_POST['gender'];
	$species = addslashes( $_POST['species'] );
	$rankid = $_POST['rank'];
	$position = $_POST['position'];
	
	/* if the passwords aren't the same, fail */
	if( $password != $confirmPassword ) {
		$result = "";
	} else {
		if( $create ) {
		
			if( $crewType == "npc" ) {
				
				/* do the insert query */
				$query = "INSERT INTO sms_crew ( crewid, crewType, firstName, middleName, lastName, gender, species, rankid, positionid ) ";
				$query.= "VALUES ( '', '$crewType', '$firstName', '$middleName', '$lastName', '$gender', '$species', '$rankid', '$position' )";
				$result = mysql_query( $query );
				
				/* optimize the table */
				optimizeSQLTable( "sms_crew" );
			
			} elseif( $crewType == "active" ) {
				
				/* get the position type from the database */
				$getPosType = "SELECT positionType FROM sms_positions WHERE positionid = '$position' LIMIT 1";
				$getPosTypeResult = mysql_query( $getPosType );
				$positionType = mysql_fetch_row( $getPosTypeResult );
				
				/* if the position is a department head, set the access levels to DH */
				/* otherwise, set it to standard player */
				if( $positionType[0] == "senior" ) {
					$levelsPost = "post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes";
					$levelsManage = "manage,m_createcrew,m_npcs1,m_newscat2";
					$levelsReports = "reports,r_count,r_strikes,r_activity,r_progress,r_milestones";
					$levelsUser = "user,u_account1,u_nominate,u_inbox,u_status,u_options,u_bio2";
					$levelsOther = "";
				} else {
					$levelsPost = "post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes";
					$levelsManage = "m_newscat1";
					$levelsReports = "reports,r_progress,r_milestones";
					$levelsUser = "user,u_account1,u_nominate,u_inbox,u_bio1,u_status,u_options";
					$levelsOther = "";
				}
			
				/* do the insert query */
				$query = "INSERT INTO sms_crew ( crewid, crewType, username, password, email, firstName, middleName, lastName, gender, species, rankid, positionid, joinDate, accessPost, accessManage, accessReports, accessUser, accessOthers ) ";
				$query.= "VALUES ( '', '$crewType', '$username', '$password', '$email', '$firstName', '$middleName', '$lastName', '$gender', '$species', '$rankid', '$position', UNIX_TIMESTAMP(), '$levelsPost', '$levelsManage', '$levelsReports', '$levelsUser', '$levelsOther' )";
				$result = mysql_query( $query );
				
				/* optimize the table */
				optimizeSQLTable( "sms_crew" );
				
				/* update the position they're being given */
				$positionFetch = "SELECT positionid, positionOpen FROM sms_positions ";
				$positionFetch.= "WHERE positionid = '$position' LIMIT 1";
				$positionFetchResult = mysql_query( $positionFetch );
				$positionX = mysql_fetch_row( $positionFetchResult );
				$open = $positionX[1];
				$revised = ( $open - 1 );
				$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
				$updatePosition.= "WHERE positionid = '$position' LIMIT 1";
				$updatePositionResult = mysql_query( $updatePosition );
				
				/* optimize the table */
				optimizeSQLTable( "sms_positions" );
				
				/** EMAIL THE PLAYER **/
	
				/* define the variables */
				$to = $email . ", " . printCOEmail();
				$from = printCO() . " < " . printCOEmail() . " >";
				$subject = "[" . $shipPrefix . " " . $shipName . "] New Character Created";
				$message = "This is an automatic email to notify you that your new character has been created.  Please log in to the site (" . $webLocation . ") using the username and password below to update your biography.  If you have any questions, please contact the CO.

USERNAME: " . $_POST['username'] . "
PASSWORD: " . $_POST['password'] . "";
			
				/* send the email */
				mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
			
			} /* close crewType == player */
		} /* close action == Create */
	} /* close the else logic */
	
	/* strip the slashes */
	$firstName = stripslashes( $_POST['firstName'] );
	$middleName = stripslashes( $_POST['middleName'] );
	$lastName = stripslashes( $_POST['lastName'] );
	$species = stripslashes( $_POST['species'] );

?>

	<div class="body">
	
		<?
		
		if( $crewType == "npc" ) {
			$type = "non-playing character";
		} elseif( $crewType == "active" ) {
			$type = "character";
		}
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( $type, "create" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Add Crew</span><br /><br />
	
		<? if( in_array( "m_npcs1", $sessionAccess ) ) { ?>
		Department Heads are permitted to create NPCs for their own department and at ranks lower than
		their own.  If you want an NPC to hold a rank equal to or higher than your own, please contact the
		CO or XO.  Additionally, you can assign an NPC to any open position.  If you have questions or
		problems, please contact the CO or XO.
		
		<? } elseif( in_array( "m_npcs2", $sessionAccess ) ) { ?>
		Commanding Officers and Executive Officers are permitted to create NPCs for any department and 
		at any rank.  Additionally, COs can assign an NPC to any open position in any department. COs are
		also the only members of the crew authorized to create new playing characters.  New playing
		characters that are created will still need to be approved through the Control Panel before the player
		associated with the character can log in and begin simming.
		<? } ?><br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=add">
		<table>
			<tr>
				<td class="tableCellLabel">Character Type</td>
				<td>&nbsp;</td>
				<td>
					<input type="radio" id="crewTypeP" name="crewType" value="active" <? if( !in_array( "m_crew", $sessionAccess ) ) { echo "disabled"; } else { echo "checked"; } ?>/> <label for="crewTypeP">Playing Character</label>
					<input type="radio" id="crewTypeN" name="crewType" value="npc" <? if( !in_array( "m_crew", $sessionAccess ) && ( in_array( "m_npcs1", $sessionAccess ) || in_array( "m_npcs2", $sessionAccess ) ) ) { echo " checked"; } ?> /> <label for="crewTypeN">Non-Playing Character</label>
				</td>
			</tr>
			<? if( in_array( "m_crew", $sessionAccess ) ) { ?>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Username</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image"  name="username" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Password</td>
				<td>&nbsp;</td>
				<td><input type="password" class="image" name="password" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Confirm Password</td>
				<td>&nbsp;</td>
				<td><input type="password" class="image" name="confirmPassword" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Email Address</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="email" /></td>
			</tr>
			<? } ?>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td class="tableCellLabel">First Name</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="firstName" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Middle Name</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="middleName" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Last Name</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="lastName" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Gender</td>
				<td>&nbsp;</td>
				<td>
					<select name="gender">
						<option value="Male">Male</option>
						<option value="Female">Female</option>
						<option value="Hermaphrodite">Hermaphrodite</option>
						<option value="Neuter">Neuter</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Species</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="species" /></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<?
			
			if( in_array( "m_npcs2", $sessionAccess ) ) {
				$ranks = "SELECT rank.rankid, rank.rankName, rank.rankImage, dept.deptColor FROM sms_ranks AS rank, ";
				$ranks.= "sms_departments AS dept WHERE dept.deptClass = rank.rankClass AND dept.deptDisplay = 'y' ";
				$ranks.= "GROUP BY rank.rankid ORDER BY rank.rankClass, rank.rankOrder ASC";
				$ranksResult = mysql_query( $ranks );
				
				$positions = "SELECT position.positionid, position.positionName, dept.deptName, ";
				$positions.= "dept.deptColor FROM sms_positions AS position, sms_departments AS dept ";
				$positions.= "WHERE position.positionOpen > '0' AND dept.deptid = position.positionDept ";
				$positions.= "AND dept.deptDisplay = 'y' ORDER BY position.positionDept, position.positionid ASC";
				$positionsResult = mysql_query( $positions );
				
			} elseif( in_array( "m_npcs1", $sessionAccess ) ) {
			
				$userDeptQuery = "SELECT crew.positionid, crew.rankid, position.positionDept, rank.rankOrder FROM ";
				$userDeptQuery.= "sms_crew AS crew, sms_positions AS position, sms_ranks AS rank WHERE ";
				$userDeptQuery.= "crew.crewid = '$sessionCrewid' AND crew.positionid = position.positionid AND crew.rankid = rank.rankid LIMIT 1";
				$userDeptResult = mysql_query( $userDeptQuery );
				$userDept = mysql_fetch_row( $userDeptResult );
				
				$ranks = "SELECT rank.rankid, rank.rankName, rank.rankImage, dept.deptColor ";
				$ranks.= "FROM sms_ranks AS rank, sms_departments AS dept ";
				$ranks.= "WHERE dept.deptid = '$userDept[2]' AND dept.deptClass = rank.rankClass ";
				$ranks.= "AND rank.rankOrder >= '$userDept[3]' AND dept.deptDisplay = 'y' ";
				$ranks.= "GROUP BY rank.rankid ORDER BY rank.rankClass, rank.rankOrder ASC";
				$ranksResult = mysql_query( $ranks );
				
				$positions = "SELECT position.positionid, position.positionName, dept.deptName, dept.deptColor ";
				$positions.= "FROM sms_positions AS position, sms_departments AS dept ";
				$positions.= "WHERE position.positionOpen > '0' AND position.positionDept = dept.deptid AND ";
				$positions.= "position.positionDept = '$userDept[2]' ORDER BY positionOrder ASC";
				$positionsResult = mysql_query( $positions );
				
			}
			
			?>
			<tr>
				<td class="tableCellLabel">Rank</td>
				<td>&nbsp;</td>
				<td>
					<select name="rank">
						<?
						
						while( $rank = mysql_fetch_assoc( $ranksResult ) ) {
							extract( $rank, EXTR_OVERWRITE );
							
							if( $client->property('browser') == "ie" ) {
								echo "<option value='" . $rank['rankid'] . "' style='color:#" . $rank['deptColor'] . ";'>" . $rank['rankName'] . "</option>";
							} else {
								echo "<option value='" . $rank['rankid'] . "' style='background:#000 url( images/ranks/" . $sessionDisplayRank . "/" . $rank['rankImage'] . " ) no-repeat 0 100%; height:40px; color:#" . $rank['deptColor'] . ";'>" . $rank['rankName'] . "</option>";
							}
						
						}
						
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Position</td>
				<td>&nbsp;</td>
				<td>
					<select name="position">
					<?
					
					while( $position = mysql_fetch_assoc( $positionsResult ) ) {
						extract( $position, EXTR_OVERWRITE );
				
						echo "<option value='" . $position['positionid'] . "' style='color:#" . $position['deptColor'] . ";'>" . $position['deptName'] . " - " . $position['positionName'] . "</option>";
						
					}
					
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="25"></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td><input type="image" src="<?=path_userskin;?>buttons/create.png" name="action_create" class="button" value="Create" /></td>
			</tr>
		</table>
		</form>
	</div>
	
<? } else { errorMessage( "add character" ); } ?>