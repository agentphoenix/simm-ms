<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/user/bio.php
Purpose: Page to display the requested bio

System Version: 2.5.2
Last Modified: 2007-08-09 0002 EST
**/

/* do some advanced checking to make sure someone's not trying to do a SQL injection */
if( !empty( $_GET['crew'] ) && preg_match( "/^\d+$/", $_GET['crew'], $matches ) == 0 ) {
	errorMessageIllegal( "user bio page" );
	exit();
} else {
	/* set the GET variable */
	$crew = $_GET['crew'];
}

/* get the crew type */
$getCrewType = "SELECT crewType FROM sms_crew WHERE crewid = '$crew' LIMIT 1";
$getCrewTypeResult = mysql_query( $getCrewType );
$getType = mysql_fetch_assoc( $getCrewTypeResult );

/* access check */
if(
	( $sessionCrewid == $crew ) ||
	( in_array( "u_bio2", $sessionAccess ) && $getType['crewType'] == "npc" ) ||
	( in_array( "u_bio3", $sessionAccess ) )
) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "user";
	$action = $_POST['action_x'];

	if( $action ) {
	
		/* define the POST vars */
		$firstName = addslashes( $_POST['firstName'] );
		$middleName = addslashes( $_POST['middleName'] );
		$lastName = addslashes( $_POST['lastName'] );
		$rank = $_POST['rank'];
		$position = $_POST['position'];
		$position2 = $_POST['position2'];
		$gender = $_POST['gender'];
		$species = addslashes( $_POST['species'] );
		$age = $_POST['age'];
		$image = $_POST['image'];
		$heightFeet = $_POST['heightFeet'];
		$heightInches = $_POST['heightInches'];
		$weight = $_POST['weight'];
		$eyeColor = addslashes( $_POST['eyeColor'] );
		$hairColor = addslashes( $_POST['hairColor'] );
		$physicalDesc = addslashes( $_POST['physicalDesc'] );
		$personalityOverview = addslashes( $_POST['personalityOverview'] );
		$strengths = addslashes( $_POST['strengths'] );
		$ambitions = addslashes( $_POST['ambitions'] );
		$hobbies = addslashes( $_POST['hobbies'] );
		$languages = addslashes( $_POST['languages'] );
		$father = addslashes( $_POST['father'] );
		$mother = addslashes( $_POST['mother'] );
		$brothers = addslashes( $_POST['brothers'] );
		$sisters = addslashes( $_POST['sisters'] );
		$spouse = addslashes( $_POST['spouse'] );
		$children = addslashes( $_POST['children'] );
		$otherFamily = addslashes( $_POST['otherFamily'] );
		$history = addslashes( $_POST['history'] );
		$serviceRecord = addslashes( $_POST['serviceRecord'] );
		
		$oldPosition = $_POST['oldPosition'];
		$oldPosition2 = $_POST['oldPosition2'];
		
		/* do the update query */
		$updateCrew = "UPDATE sms_crew SET firstName = '$firstName', middleName = '$middleName', ";
		$updateCrew.= "lastName = '$lastName', rankid = '$rank', positionid = '$position', positionid2 = '$position2', ";
		$updateCrew.= "gender = '$gender', species = '$species', age = '$age', image = '$image', heightFeet = '$heightFeet', heightInches = '$heightInches', ";
		$updateCrew.= "weight = '$weight', eyeColor = '$eyeColor', hairColor = '$hairColor', physicalDesc = '$physicalDesc', ";
		$updateCrew.= "personalityOverview = '$personalityOverview', strengths = '$strengths', ambitions = '$ambitions', ";
		$updateCrew.= "hobbies = '$hobbies', languages = '$languages', father = '$father', mother = '$mother', ";
		$updateCrew.= "brothers = '$brothers', sisters = '$sisters', spouse = '$spouse', children = '$children', ";
		$updateCrew.= "otherFamily = '$otherFamily', history = '$history', serviceRecord = '$serviceRecord' ";
		$updateCrew.= "WHERE crewid = '$crew' LIMIT 1";
		$result = mysql_query( $updateCrew );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
		
		if( $getType['crewType'] == "active" || $getType['crewType'] == "inactive" ) {
		
			if( $oldPosition != $position ) {
				
				/* update the position they're being given */
				$positionFetch = "SELECT positionid, positionOpen, positionType FROM sms_positions ";
				$positionFetch.= "WHERE positionid = '$position' LIMIT 1";
				$positionFetchResult = mysql_query( $positionFetch );
				$positionX = mysql_fetch_row( $positionFetchResult );
				$open = $positionX[1];
				$revised = ( $open - 1 );
				$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
				$updatePosition.= "WHERE positionid = '$position' LIMIT 1";
				$updatePositionResult = mysql_query( $updatePosition );
				
				/* if the position is a department head, set the access levels to DH */
				/* otherwise, set it to standard player */
				if( $positionX[2] == "senior" ) {
					$levelsPost = "post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes";
					$levelsManage = "manage,m_createcrew,m_npcs1,m_newscat2";
					$levelsReports = "reports,r_count,r_strikes,r_activity,r_progress,r_milestones";
					$levelsUser = "user,u_account1,u_nominate,u_inbox,u_status,u_options,u_bio2";
					$levelsOther = "";
				} else {
					$levelsPost = "post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes";
					$levelsManage = "";
					$levelsReports = "reports,r_progress,r_milestones";
					$levelsUser = "user,u_account1,u_nominate,u_inbox,u_bio1,u_status,u_options";
					$levelsOther = "";
				}
				
				$crewUpdate = "UPDATE sms_crew SET accessPost = '" . $levelsPost . "', accessManage = '" . $levelsManage . "', ";
				$crewUpdate.= "accessReports = '" . $levelsReport . "', accessUser = '" . $levelsUser . "', ";
				$crewUpdate.= "accessOthers = '" . $levelsOther . "' WHERE crewid = '" . $crew . "' LIMIT 1";
				$crewUpdateResult = mysql_query( $crewUpdate );
				
				/* optimize the table */
				optimizeSQLTable( "sms_crew" );
				
				/* update the position they had */
				$positionFetch = "SELECT positionid, positionOpen FROM sms_positions ";
				$positionFetch.= "WHERE positionid = '$oldPosition' LIMIT 1";
				$positionFetchResult = mysql_query( $positionFetch );
				$positionX = mysql_fetch_row( $positionFetchResult );
				$open = $positionX[1];
				$revised = ( $open + 1 );
				$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
				$updatePosition.= "WHERE positionid = '$oldPosition' LIMIT 1";
				$updatePositionResult = mysql_query( $updatePosition );
				
				/* optimize the table */
				optimizeSQLTable( "sms_positions" );
				
			} if( $oldPosition2 != $position2 ) {
			
				/* update the second position they're being given */
				$positionFetch = "SELECT positionid, positionOpen FROM sms_positions ";
				$positionFetch.= "WHERE positionid = '$position2' LIMIT 1";
				$positionFetchResult = mysql_query( $positionFetch );
				$positionX = mysql_fetch_row( $positionFetchResult );
				$open = $positionX[1];
				$revised = ( $open - 1 );
				$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
				$updatePosition.= "WHERE positionid = '$position2' LIMIT 1";
				$updatePositionResult = mysql_query( $updatePosition );
				
				/* update the second position they had */
				$positionFetch = "SELECT positionid, positionOpen FROM sms_positions ";
				$positionFetch.= "WHERE positionid = '$oldPosition2' LIMIT 1";
				$positionFetchResult = mysql_query( $positionFetch );
				$positionX = mysql_fetch_row( $positionFetchResult );
				$open = $positionX[1];
				$revised = ( $open + 1 );
				$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
				$updatePosition.= "WHERE positionid = '$oldPosition2' LIMIT 1";
				$updatePositionResult = mysql_query( $updatePosition );
				
				/* optimize the table */
				optimizeSQLTable( "sms_positions" );
			
			}
		
		} /* close the crewType check */
		
	}

$getCrew = "SELECT * FROM sms_crew WHERE crewid = '$crew' LIMIT 1";
$getCrewResult = mysql_query( $getCrew );

while( $fetchCrew = mysql_fetch_array( $getCrewResult ) ) {
	extract( $fetchCrew, EXTR_OVERWRITE );

	$getRank = "SELECT rankName, rankImage FROM sms_ranks WHERE rankid = '$fetchCrew[rankid]'";
	$getRankResult = mysql_query( $getRank );
	$fetchRank = mysql_fetch_assoc( $getRankResult );
	
	if( in_array( "u_bio3", $sessionAccess ) ) {
	
		$ranks = "SELECT rank.rankid, rank.rankName, rank.rankImage, dept.deptColor FROM sms_ranks AS rank, ";
		$ranks.= "sms_departments AS dept WHERE dept.deptClass = rank.rankClass AND dept.deptDisplay = 'y' ";
		$ranks.= "GROUP BY rank.rankid ORDER BY rank.rankClass, rank.rankOrder ASC";
		$ranksResult = mysql_query( $ranks );
		
		$positions = "SELECT position.positionid, position.positionName, dept.deptName, ";
		$positions.= "dept.deptColor FROM sms_positions AS position, sms_departments AS dept ";
		$positions.= "WHERE position.positionOpen > '0' AND dept.deptid = position.positionDept ";
		$positions.= "AND dept.deptDisplay = 'y' ORDER BY dept.deptOrder, position.positionid ASC";
		$position1Result = mysql_query( $positions );
		$position2Result = mysql_query( $positions );
		
	} elseif( in_array( "u_bio2", $sessionAccess ) ) {
		
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
		$positions.= "position.positionDept = '$userDept[2]' ORDER BY position.positionOrder ASC";
		$position1Result = mysql_query( $positions );
		$position2Result = mysql_query( $positions );
		
	}
	
	if( $fetchCrew['crewType'] == "npc" ) {
		$type = "NPC";
	} else {
		$type = "Character";
	}
	
	/* define the POST vars */
	$firstName = stripslashes( $firstName );
	$middleName = stripslashes( $middleName );
	$lastName = stripslashes( $lastName );
	$species = stripslashes( $species );
	$eyeColor = stripslashes( $eyeColor );
	$hairColor = stripslashes( $hairColor );
	$physicalDesc = stripslashes( $physicalDesc );
	$personalityOverview = stripslashes( $personalityOverview );
	$strengths = stripslashes( $strengths );
	$ambitions = stripslashes( $ambitions );
	$hobbies = stripslashes( $hobbies );
	$languages = stripslashes( $languages );
	$father = stripslashes( $father );
	$mother = stripslashes( $mother );
	$brothers = stripslashes( $brothers );
	$sisters = stripslashes( $sisters );
	$spouse = stripslashes( $spouse );
	$children = stripslashes( $children );
	$otherFamily = stripslashes( $otherFamily );
	$history = stripslashes( $history );
	$serviceRecord = stripslashes( $serviceRecord );

?>

	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $updateCrew );
				
		if( !empty( $check->query ) ) {
			$check->message( "biography", "update" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage <?=$type;?> Biography</span>
		&nbsp;&nbsp;
		<? if( $fetchCrew['crewType'] == "pending" ) { ?><b class="yellow">[ Activation Pending ]</b><? } ?>
		
		<br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=bio&crew=<?=$crew;?>">
		<table>
			<tr>
				<td colspan="3" align="center" class="fontMedium"><b>Character Information</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">First Name</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image"  name="firstName" value="<?=$firstName;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Middle Name</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image"  name="middleName" value="<?=$middleName;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Last Name</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image"  name="lastName" value="<?=$lastName;?>" /></td>
			</tr>
			
			<? if( in_array( "u_bio2", $sessionAccess ) || in_array( "u_bio3", $sessionAccess ) ) { ?>
			<tr>
				<td class="tableCellLabel">Rank</td>
				<td>&nbsp;</td>
				<td>
					<select name="rank">
						<?
						
						while( $rank = mysql_fetch_array( $ranksResult ) ) {
							extract( $rank, EXTR_OVERWRITE );
							
							if( $client->property('browser') == "ie" || in_array( "u_bio2", $sessionAccess ) ) {
								if( $fetchCrew['rankid'] == $rankid ) {
									echo "<option value='" . $rankid . "' style='color:#" . $deptColor . ";' selected>" . $rankName . "</option>";
								} else {
									echo "<option value='" . $rankid . "' style='color:#" . $deptColor . ";'>" . $rankName . "</option>";
								}
							} else {
								if( $fetchCrew['rankid'] == $rankid ) {
									echo "<option value='" . $rankid . "' style='background:#000 url( images/ranks/" . $rankSet . "/" . $rankImage . " ) no-repeat 0 100%; height:40px; color:#" . $deptColor . ";' selected>" . $rankName . "</option>";
								} else {
									echo "<option value='" . $rankid . "' style='background:#000 url( images/ranks/" . $rankSet . "/" . $rankImage . " ) no-repeat 0 100%; height:40px; color:#" . $deptColor . ";'>" . $rankName . "</option>";
								}
							}
						}
						
						?>
					</select>
				</td>
			</tr>
			<? } else { ?>
			<tr>
				<td class="tableCellLabel">Rank</td>
				<td>&nbsp;</td>
				<td>
					<b><? printText( $fetchRank['rankName'] ); ?></b>
					<input type="hidden" name="rank" value="<?=$fetchCrew['rankid'];?>" />
				</td>
			</tr>
			<? } ?>
			
			<? if( in_array( "u_bio2", $sessionAccess ) || in_array( "u_bio3", $sessionAccess ) ) { ?>
			<tr>
				<td class="tableCellLabel">Position</td>
				<td>&nbsp;</td>
				<td>
					<select name="position">
					<?
					
					$currentPosition = "SELECT position.positionid, position.positionName, dept.deptName, dept.deptColor ";
					$currentPosition.= "FROM sms_positions AS position, sms_departments AS dept WHERE ";
					$currentPosition.= "position.positionid = '$fetchCrew[positionid]' AND position.positionDept = dept.deptid";
					$currentPositionResult = mysql_query( $currentPosition );
					$fetchCurrentPosition = mysql_fetch_assoc( $currentPositionResult );
					
					echo "<option value='" . $fetchCurrentPosition['positionid'] . "' style='color:#" . $fetchCurrentPosition['deptColor'] . "'>" . $fetchCurrentPosition['deptName'] . " - " . $fetchCurrentPosition['positionName'] . "</option>";
					
					while( $position = mysql_fetch_array( $position1Result ) ) {
						extract( $position, EXTR_OVERWRITE );
				
						echo "<option value='" . $positionid . "' style='color:#" . $deptColor . "'>" . $deptName . " - " . $positionName . "</option>";
						
					}
					
					?>
					</select>
					<input type="hidden" name="oldPosition" value="<?=$fetchCrew['positionid'];?>" />
				</td>
			</tr>
			<? } else { ?>
			<tr>
				<td class="tableCellLabel">Position</td>
				<td>&nbsp;</td>
				<td>
					<b><? printPlayerPosition( $fetchCrew['crewid'], $positionid, "" ); ?></b>
					<input type="hidden" name="position" value="<?=$positionid;?>" />
				</td>
			</tr>
			<? } ?>
			
			<? if( in_array( "u_bio3", $sessionAccess ) ) { ?>
			<tr>
				<td class="tableCellLabel">Second Position</td>
				<td>&nbsp;</td>
				<td>
					<select name="position2">
					<?
					
					$currentPosition = "SELECT position.positionid, position.positionName, dept.deptName, dept.deptColor ";
					$currentPosition.= "FROM sms_positions AS position, sms_departments AS dept WHERE ";
					$currentPosition.= "position.positionid = '$fetchCrew[positionid2]' AND position.positionDept = dept.deptid";
					$currentPositionResult = mysql_query( $currentPosition );
					$fetchCurrentPosition = mysql_fetch_assoc( $currentPositionResult );
					
					if( !empty( $fetchCrew['positionid2'] ) ) {
						echo "<option value='" . $fetchCurrentPosition['positionid'] . "' style='color:#" . $fetchCurrentPosition['deptColor'] . "'>" . $fetchCurrentPosition['deptName'] . " - " . $fetchCurrentPosition['positionName'] . "</option>";
					}
					
					echo "<option value='0'>No Position Specified</option>";
					
					while( $position2 = mysql_fetch_array( $position2Result ) ) {
						extract( $position2, EXTR_OVERWRITE );
				
						echo "<option value='" . $position2['positionid'] . "' style='color:#" . $deptColor . "'>" . $position2['deptName'] . " - " . $position2['positionName'] . "</option>";
						
					}
					
					?>
					</select>
					<input type="hidden" name="oldPosition2" value="<?=$fetchCrew['positionid2'];?>" />
				</td>
			</tr>
			<? } elseif( !empty( $positionid2 ) ) { ?>
			<tr>
				<td class="tableCellLabel">Second Position</td>
				<td>&nbsp;</td>
				<td>
					<? printPlayerPosition( $fetchCrew['crewid'], $positionid2, "2" ); ?>
					<input type="hidden" name="position2" value="<?=$positionid2;?>" />
				</td>
			</tr>
			<? } ?>
			
			<tr>
				<td class="tableCellLabel">Gender</td>
				<td>&nbsp;</td>
				<td>
					<select name="gender">
						<option value="Male" <? if( $gender == "Male" ) { echo "selected"; } ?>>Male</option>
						<option value="Female" <? if( $gender == "Female" ) { echo "selected"; } ?>>Female</option>
						<option value="Hermaphrodite" <? if( $gender == "selected" ) { echo "selected"; } ?>>Hermaphrodite</option>
						<option value="Neuter" <? if( $gender == "Neuter" ) { echo "selected"; } ?>>Neuter</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Species</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image"  name="species" value="<?=$species;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Age</td>
				<td>&nbsp;</td>
				<td><input type="text" class="order"  name="age" size="4" maxlength="3" value="<?=$age;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">
					Image<br />
					<b class="yellow fontSmall">All images must be no wider than 200 pixels and no taller than 300
					pixels for the integrity of the image. SMS will automatically crop the image if these guidelines aren't
					met, sometimes resulting in unwanted positioning.
				</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image"  name="image" size="40" value="<?=$image;?>" /></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td colspan="3" align="center" class="fontMedium"><b>Physical Appearance</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Height</td>
				<td>&nbsp;</td>
				<td>
					<input type="text" class="order" name="heightFeet" size="3" maxlength="2" value="<?=$heightFeet;?>" /> &prime;
					<input type="text" class="order" name="heightInches" size="3" maxlength="2" value="<?=$heightInches;?>" /> &Prime;
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Weight</td>
				<td>&nbsp;</td>
				<td><input type="text" class="text" name="weight" size="5" maxlength="4" value="<?=$weight;?>" /> lbs.</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Eye Color</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="eyeColor" value="<?=$eyeColor;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Hair Color</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="hairColor" value="<?=$hairColor;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Physical Description</td>
				<td>&nbsp;</td>
				<td><textarea name="physicalDesc" class="desc" rows="5"><?=$physicalDesc;?></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td colspan="3" align="center" class="fontMedium"><b>Personality &amp; Traits</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">General Overview</td>
				<td>&nbsp;</td>
				<td><textarea name="personalityOverview" class="desc" rows="5"><?=$personalityOverview;?></textarea></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Strengths &amp; Weaknesses</td>
				<td>&nbsp;</td>
				<td><textarea name="strengths" class="desc" rows="5"><?=$strengths;?></textarea></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Ambitions</td>
				<td>&nbsp;</td>
				<td><textarea name="ambitions" class="desc" rows="5"><?=$ambitions;?></textarea></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Hobbies &amp; Interests</td>
				<td>&nbsp;</td>
				<td><textarea name="hobbies" class="desc" rows="5"><?=$hobbies;?></textarea></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Languages</td>
				<td>&nbsp;</td>
				<td><textarea name="languages" class="desc" rows="3"><?=$languages;?></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td colspan="3" align="center" class="fontMedium"><b>Family</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Father</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="father" value="<?=$father;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Mother</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="mother" value="<?=$mother;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Brother(s)</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="brothers" value="<?=$brothers;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Sister(s)</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="sisters" value="<?=$sisters;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Spouse</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="spouse" value="<?=$spouse;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Children</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="children" value="<?=$children;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Other Family</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="otherFamily" value="<?=$otherFamily;?>" /></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td colspan="3" align="center" class="fontMedium"><b>History</b></td>
			</tr>
			<tr>
				<td colspan="3">
					<textarea name="history" rows="15" class="wideTextArea"><?=$history;?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td colspan="3" align="center" class="fontMedium"><b>Service Record</b></td>
			</tr>
			<tr>
				<td colspan="3">
					<textarea name="serviceRecord" rows="10" class="wideTextArea"><?=$serviceRecord;?></textarea>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="25"></td>
			</tr>
			<tr>
				<td colspan="3" align="right">
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action" class="button" value="Update" />
				</td>
			</tr>
		</table>
		
	</div>
	
<? } } else { errorMessage( "this user's bio management" ); } ?>