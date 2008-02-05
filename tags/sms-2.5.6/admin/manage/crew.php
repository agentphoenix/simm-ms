<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/crew.php
Purpose: Page to display the active, inactive, and pending crew on the sim

System Version: 2.5.2
Last Modified: 2007-08-09 0001 EST
**/

/* access check */
if( in_array( "m_crew", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$action = $_GET['action'];
	$activate = $_POST['activate_x'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( "crew listing page" );
		exit();
	} else {
		/* set the GET variable */
		$actionid = $_GET['id'];
	}
	
	if( isset( $action ) ) {
		
		/* determine what the user level should be */
		if( $action == "activate" ) {
			
			/* set the standard user levels */
			$levelsPost = "post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes";
			$levelsManage = "";
			$levelsReports = "reports,r_progress,r_milestones";
			$levelsUser = "user,u_account1,u_nominate,u_inbox,u_bio1,u_status,u_options";
			$levelsOther = "";
			
			/* do the update query */
			$query = "UPDATE sms_crew SET crewType = 'active', accessPost = '$levelsPost', ";
			$query.= "accessManage = '$levelsManage', accessReports = '$levelsReports', ";
			$query.= "accessUser = '$levelsUser', accessOthers = '$levelsOther' ";
			$query.= "WHERE crewid = '$actionid' LIMIT 1";
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_crew" );

			/* get the user's old position */
			$getPos = "SELECT positionid, positionid2 FROM sms_crew WHERE crewid = '$actionid' LIMIT 1";
			$getPosResult = mysql_query( $getPos );
			$oldPosition = mysql_fetch_assoc( $getPosResult );
			
     		/* update the position */
			$positionFetch = "SELECT positionid, positionOpen FROM sms_positions ";
			$positionFetch.= "WHERE positionid = '$oldPosition[positionid]' LIMIT 1";
			$positionFetchResult = mysql_query( $positionFetch );
			$positionX = mysql_fetch_row( $positionFetchResult );
			$open = $positionX[1];
			$revised = ( $open - 1 );
			$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
			$updatePosition.= "WHERE positionid = '$oldPosition[positionid]' LIMIT 1";
			$updatePositionResult = mysql_query( $updatePosition );			
			
			if( !empty( $oldPosition['positionid2'] ) ) {
				
				/* update the position they had */
				$positionFetch = "SELECT positionid, positionOpen FROM sms_positions ";
				$positionFetch.= "WHERE positionid = '$oldPosition[positionid2]' LIMIT 1";
				$positionFetchResult = mysql_query( $positionFetch );
				$positionX = mysql_fetch_row( $positionFetchResult );
				$open = $positionX[1];
				$revised = ( $open - 1 );
				$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
				$updatePosition.= "WHERE positionid = '$oldPosition[positionid2]' LIMIT 1";
				$updatePositionResult = mysql_query( $updatePosition );
				
			}
			
			/* optimize the table */
			optimizeSQLTable( "sms_positions" );
			
		} elseif( $action == "delete" ) {
        	
			/* get the user's old position */
			$getPos = "SELECT positionid, positionid2, crewType FROM sms_crew WHERE crewid = '$actionid' LIMIT 1";
			$getPosResult = mysql_query( $getPos );
			$oldPosition = mysql_fetch_assoc( $getPosResult );
			
			/* if they're active, deal with the positions */
			if( $oldPosition['crewType'] == "pending" || $oldPosition['crewType'] == "inactive" ) {} else {
			
				/* update the position */
				$positionFetch = "SELECT positionid, positionOpen FROM sms_positions ";
				$positionFetch.= "WHERE positionid = '$oldPosition[positionid]' LIMIT 1";
				$positionFetchResult = mysql_query( $positionFetch );
				$positionX = mysql_fetch_row( $positionFetchResult );
				$open = $positionX[1];
				$revised = ( $open + 1 );
				$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
				$updatePosition.= "WHERE positionid = '$oldPosition[positionid]' LIMIT 1";
				$updatePositionResult = mysql_query( $updatePosition );
				
				if( !empty( $oldPosition['positionid2'] ) ) {
					
					/* update the position they had */
					$positionFetch = "SELECT positionid, positionOpen FROM sms_positions ";
					$positionFetch.= "WHERE positionid = '$oldPosition[positionid2]' LIMIT 1";
					$positionFetchResult = mysql_query( $positionFetch );
					$positionX = mysql_fetch_row( $positionFetchResult );
					$open = $positionX[1];
					$revised = ( $open + 1 );
					$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
					$updatePosition.= "WHERE positionid = '$oldPosition[positionid2]' LIMIT 1";
					$updatePositionResult = mysql_query( $updatePosition );
					
				}
				
				/* optimize the table */
				optimizeSQLTable( "sms_positions" );
				
			}
			
			/* do the delete query */
			$query = "DELETE FROM sms_crew WHERE crewid = '$actionid' LIMIT 1";
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_crew" );
            
      } /* close the delete section */

	} elseif( $activate ) {

		/* define the POST vars */
		$type = $_POST['type'];
		$id = $_POST['crew'];
		
		/* set the access levels */
		$levelsPost = "";
		$levelsManage = "";
		$levelsReports = "";
		$levelsUser = "";
		$levelsOther = "";
		
		/* do the update query */
		$query = "UPDATE sms_crew SET crewType = '$type', accessPost = '$levelsPost', ";
		$query.= "accessManage = '$levelsManage', accessReports = '$levelsReports', ";
		$query.= "accessUser = '$levelsUser', accessOthers = '$levelsOther', ";
		$query.= "leaveDate = UNIX_TIMESTAMP() WHERE crewid = '$id' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );

		/* set the action */
		$action = "deactivate";

		/* get the user's old position */
		$getPos = "SELECT positionid, positionid2 FROM sms_crew WHERE crewid = '$id' LIMIT 1";
		$getPosResult = mysql_query( $getPos );
		$oldPosition = mysql_fetch_assoc( $getPosResult );
		
		/* update the position */
		$positionFetch = "SELECT positionid, positionOpen FROM sms_positions ";
		$positionFetch.= "WHERE positionid = '$oldPosition[positionid]' LIMIT 1";
		$positionFetchResult = mysql_query( $positionFetch );
		$positionX = mysql_fetch_row( $positionFetchResult );
		$open = $positionX[1];
		$revised = ( $open + 1 );
		$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
		$updatePosition.= "WHERE positionid = '$oldPosition[positionid]' LIMIT 1";
		$updatePositionResult = mysql_query( $updatePosition );
		
		if( !empty( $oldPosition['positionid2'] ) ) {
			
			/* update the position they had */
			$positionFetch = "SELECT positionid, positionOpen FROM sms_positions ";
			$positionFetch.= "WHERE positionid = '$oldPosition[positionid2]' LIMIT 1";
			$positionFetchResult = mysql_query( $positionFetch );
			$positionX = mysql_fetch_row( $positionFetchResult );
			$open = $positionX[1];
			$revised = ( $open + 1 );
			$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
			$updatePosition.= "WHERE positionid = '$oldPosition[positionid2]' LIMIT 1";
			$updatePositionResult = mysql_query( $updatePosition );
			
		}
		
		/* optimize the table */
		optimizeSQLTable( "sms_positions" );

	} /* close the isset( $activate ) section */

?>

	<div class="body">
	
		<? if( $action == "details" ) { ?>
			<div class="update">
				<span class="fontTitle">
					Deactivation Options - 
					<? printCrewName( $actionid, "noRank", "noLink" ); ?>
				</span><br /><br />
				
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=crew">
					<table>
						<tr>
							<td class="tableCellLabel">Character Type</td>
							<td>&nbsp;</td>
							<td>
								<input type="radio" id="typeNPC" name="type" value="npc" checked="yes" /> <label for="typeNPC">Make An NPC</label><br />
								<input type="radio" id ="typeInactive" name="type" value="inactive" /> <label for="typeInactive">Add to Departed Crew Manifest</label>
							</td>
						</tr>
						<tr>
							<td colspan="3" height="10">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
							<td>
								<input type="hidden" name="crew" value="<?=$actionid;?>" />
								<input type="image" src="<?=path_userskin;?>buttons/deactivate.png" name="activate" class="button" value="Deactivate" />
							</td>
						</tr>
					</table>
				</form>
			</div>
			<br /><br />
		<?
		
		} else {
		
			$check = new QueryCheck;
			$check->checkQuery( $result, $query );
					
			if( !empty( $check->query ) ) {
				$check->message( "player", $action );
				$check->display();
			}
		
		}
		
		?>
		
		<span class="fontTitle">Manage Playing Characters</span>
		<p>From this page, you can select any of the playing characters that exist . You can edit their 
		bios, promote (or demote) them to another position or rank. Additionaly, if need be, you can
		deactivate the character if the player has retired or been removed.  By deactivating a character,
		they will be moved to the Departed Crew Manifest.  Please note that only pending characters can
		be deleted from the system, all other characters will be moved to inactive status.<br /><br />
		
		<a href="<?=$webLocation;?>admin.php?page=manage&sub=add" class="add">Add a Character &raquo;</a>
		</p>
		<table cellpadding="2" cellspacing="2">
		<?
		
		$crew = "SELECT crewid, firstName, lastName FROM sms_crew WHERE crewType = 'pending' ORDER BY crewid ASC";
		$crewResult = mysql_query( $crew );
		$pending = mysql_num_rows( $crewResult );
		
		$rowCount = "0";
		$color1 = "rowColor1";
		$color2 = "rowColor2";
			
		if( $pending > 0 ) {
		
		?>
		
			<tr>
				<td class="fontLarge" colspan="6"><b>Pending Crew</b></td>
			</tr>
		
		<?
		
			while( $players = mysql_fetch_assoc($crewResult) ) {
				extract( $players, EXTR_OVERWRITE );
				
				$rowColor = ($rowCount % 2) ? $color1 : $color2;
		
		?>
		
			<tr class="fontSmall <?=$rowColor;?>">
				<td width="30%"><b><? printText( $firstName . " " . $lastName ); ?></b></td>
				<td width="30%">Unassigned</td>
				<td width="10%" align="center"><a href="<?=$webLocation;?>admin.php?page=user&sub=bio&crew=<?=$players['crewid'];?>" class="edit">Edit Bio</a></td>
				<td width="10%" align="center"><a href="<?=$webLocation;?>admin.php?page=user&sub=account&crew=<?=$players['crewid'];?>" class="edit">Edit Account</a></td>
				<td width="10%" align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=activate&activate=details&id=<?=$players['crewid'];?>" class="add">Approve</a></td>
				<td width="10%" align="center">
					<script type="text/javascript">
						document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=crew&action=delete&id=<?=$players['crewid'];?>\" class=\"delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this crew member?')\">Delete</a>" );
					</script>
					<noscript>
						<a href="<?=$webLocation;?>admin.php?page=manage&sub=crew&action=delete&id=<?=$players['crewid'];?>" class="delete">Delete</a>
					</noscript>
				</td>
			</tr>
			
		<? $rowCount++; } ?>
			
			<tr>
				<td colspan="6" height="15"></td>
			</tr>
		</table>
			
		<?
		} /* end the if pending > 0 logic */
		
		?>
		
		<table cellpadding="2" cellspacing="2">
			<tr>
				<td class="fontLarge" colspan="7"><b>Active Crew</b></td>
			</tr>
			
					<?
					
					$crew = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.rankid, crew.positionid, ";
					$crew.= "crew.positionid2, position.positionDept FROM sms_crew AS crew, sms_positions AS position ";
					$crew.= "WHERE crewType = 'active' AND crew.positionid = position.positionid ORDER BY ";
					$crew.= "position.positionDept, position.positionOrder, crew.rankid ASC";
					$crewResult = mysql_query( $crew );
					
					$rankArray = "SELECT rankid FROM sms_ranks ORDER BY rankid ASC";
					$rankArrayResult = mysql_query( $rankArray );
					
					/* point the previous and next post buttons to the correct posts */
					$rankList = array();
					
					while( $myrow = mysql_fetch_array( $rankArrayResult ) ) {
						$rankList[] = $myrow['rankid'];
					}
					
					$rowCount = "0";
					$color1 = "rowColor1";
					$color2 = "rowColor2";
		
					while( $player = mysql_fetch_assoc( $crewResult ) ) {
						extract( $player, EXTR_OVERWRITE );
						
						$rowColor = ($rowCount % 2) ? $color1 : $color2;
						
						echo "<tr class='fontSmall " . $rowColor . "'>";
							echo "<td width='30%'>";
				
						if( in_array( $player['rankid'], $rankList ) ) {
							
							$crewRank = "SELECT rankName FROM sms_ranks WHERE rankid = '$player[rankid]'";
							$crewRankResult = mysql_query( $crewRank );
							
							while( $playerRank = mysql_fetch_assoc( $crewRankResult ) ) {
								extract( $playerRank, EXTR_OVERWRITE );
							
								echo "<b>" . printText( $playerRank['rankName'] . " " . $firstName . " " . $lastName ) . "</b>";
								
							}
							
						} else {
							echo "<b class='red'>[ Invalid Rank ]</b> ";
							echo "<b>" . printText( $firstName . " " . $lastName ) . "</b>";
						}				
					
					?>
				</td>
				<?
				
				$posArray = "SELECT positionid FROM sms_positions ORDER BY positionid ASC";
				$posArrayResult = mysql_query( $posArray );
				
				/* point the previous and next post buttons to the correct posts */
				$posList = array();
				
				while( $myrow = mysql_fetch_array( $posArrayResult ) ) {
					$posList[] = $myrow['positionid'];
				}
				
				if( in_array( $player['positionid'], $posList ) ) {
				
					$crewPos = "SELECT position.positionName, position.positionDept, dept.deptColor ";
					$crewPos.= "FROM sms_positions AS position, sms_departments AS dept WHERE ";
					$crewPos.= "position.positionid = '$player[positionid]' AND position.positionDept = dept.deptid";
					$crewPosResult = mysql_query( $crewPos );
					
					while( $playerPos = mysql_fetch_assoc( $crewPosResult ) ) {
						extract( $playerPos, EXTR_OVERWRITE );
						
				?>
				<td width="30%">
					<?
					
					echo "<span style='color: #" . $deptColor . ";'>";
					printText( $positionName );
					echo "</span>";
					
					if( !empty( $player['positionid2'] ) && in_array( $player['positionid2'], $posList ) ) {
				
						$crewPos2 = "SELECT position.positionName, position.positionDept, dept.deptColor ";
						$crewPos2.= "FROM sms_positions AS position, sms_departments AS dept WHERE ";
						$crewPos2.= "position.positionid = '$player[positionid2]' AND position.positionDept = dept.deptid";
						$crewPos2Result = mysql_query( $crewPos2 );
						
						while( $playerPos2 = mysql_fetch_assoc( $crewPos2Result ) ) {
							extract( $playerPos2, EXTR_OVERWRITE );
														
							echo " &amp; ";
							echo "<span style='color: #" . $deptColor . ";'>";
							printText( $positionName );
							echo "</span>";
							
						}
						
					}
					
					?>
				</td>
				<? } } else { ?>
				<td width="30%" class="red"><b>[ Invalid Position ]</b></td>
				<? } ?>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=user&sub=bio&crew=<?=$player[crewid];?>" class="edit">Edit Bio</a></td>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=user&sub=account&crew=<?=$player['crewid'];?>" class="edit">Edit Account</a></td>
				<td align="center">
					<a href="<?=$webLocation;?>admin.php?page=user&sub=stats&crew=<?=$player['crewid'];?>" style="font-weight:bold;">Stats</a> &middot;
					<a href="<?=$webLocation;?>admin.php?page=user&sub=access&crew=<?=$player['crewid'];?>" style="font-weight:bold;">Access</a>
				</td>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=crew&action=details&id=<?=$player['crewid'];?>" class="delete">Deactivate</a></td>
				<td align="center">
					<script type="text/javascript">
						document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=crew&action=delete&id=<?=$player['crewid'];?>\" class=\"delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this crew member?')\">Delete</a>" );
					</script>
					<noscript>
						<a href="<?=$webLocation;?>admin.php?page=manage&sub=crew&action=delete&id=<?=$player['crewid'];?>" class="delete">Delete</a>
					</noscript>
				</td>
			</tr>
			
		<? 
		
			$rowCount++;
		
		}
		
		$crew = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName, ";
		$crew.= "position.positionName, dept.deptColor FROM sms_crew AS crew, sms_ranks AS rank, ";
		$crew.= "sms_positions AS position, sms_departments AS dept WHERE crew.crewType = 'inactive' ";
		$crew.= "AND crew.rankid = rank.rankid AND crew.positionid = position.positionid AND ";
		$crew.= "position.positionDept = dept.deptid ORDER BY position.positionDept, position.positionOrder, crew.rankid ASC";
		$crewResult = mysql_query( $crew );
		$inactive = mysql_num_rows( $crewResult );
		
		if( $inactive > 0 ) {
		
		?>
			<tr>
				<td colspan="7" height="15"></td>
			</tr>
		</table>
		
		<table cellpadding="2" cellspacing="2">
			<tr>
				<td class="fontLarge" colspan="7"><b>Inactive Crew</b></td>
			</tr>
		
		<?
			
			$rowCount = "0";
			$color1 = "rowColor1";
			$color2 = "rowColor2";
					
			while( $players = mysql_fetch_assoc($crewResult) ) {
				extract( $players, EXTR_OVERWRITE );
				
				$rowColor = ($rowCount % 2) ? $color1 : $color2;
		
		?>
		
			<tr class="fontSmall <?=$rowColor;?>">
				<td width="30%"><b><? printText( $rankName . " " . $firstName . " " . $lastName ); ?></b></td>
				<td width="30%" style="color: #<?=$deptColor;?>;"><? printText( $positionName ); ?></td>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=user&sub=bio&crew=<?=$players[crewid];?>" class="edit">Edit Bio</a></td>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=user&sub=account&crew=<?=$players['crewid'];?>" class="edit">Edit Account</a></td>
				<td align="center">
					<a href="<?=$webLocation;?>admin.php?page=user&sub=stats&crew=<?=$players['crewid'];?>" style="font-weight:bold;">Stats</a> &middot;
					<a href="<?=$webLocation;?>admin.php?page=user&sub=access&crew=<?=$players['crewid'];?>" style="font-weight:bold;">Access</a>
				</td>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=crew&action=activate&id=<?=$players['crewid'];?>" class="add">Activate</a></td>
				<td align="center">
					<script type="text/javascript">
						document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=crew&action=delete&id=<?=$players['crewid'];?>\" class=\"delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this crew member?')\">Delete</a>" );
					</script>
					<noscript>
						<a href="<?=$webLocation;?>admin.php?page=manage&sub=crew&action=delete&id=<?=$players['crewid'];?>" class="delete">Delete</a>
					</noscript>
				</td>
			</tr>
			
		<? $rowCount++; } } ?>
		
		</table>
	</div>
	
<? } else { errorMessage( "crew management" ); } ?>