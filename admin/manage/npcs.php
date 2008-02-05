<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/npcs.php
Purpose: Page to manage the NPCs on the simm

System Version: 2.5.2
Last Modified: 2007-08-09 0002 EST
**/

/* access check */
if( in_array( "m_npcs1", $sessionAccess ) || in_array( "m_npcs2", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$action = $_GET['action'];
	$activate = $_POST['activate_x'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( "NPC management page" );
		exit();
	} else {
		/* set the GET variable */
		$actionid = $_GET['id'];
	}

	if( $action == "delete" ) {
		
		/* do the delete query */
		$query = "DELETE FROM sms_crew WHERE crewid = '$actionid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
		
	} if( $activate && in_array( "m_npcs2", $sessionAccess ) ) {

		/* define the POST variables */
		$crew = $_POST['crew'];
		$email = $_POST['email'];
		$username = $_POST['username'];
		$password = md5( $_POST['password'] );
		$position = $_POST['position'];
		
		/* set the standard user levels */
		$levelsPost = "post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes";
		$levelsManage = "";
		$levelsReports = "reports,r_progress,r_milestones";
		$levelsUser = "user,u_account1,u_nominate,u_inbox,u_bio1,u_status,u_options";
		$levelsOther = "";

		/* do the delete query */
		$query = "UPDATE sms_crew SET crewType = 'active', accessPost = '$levelsPost', ";
		$query.= "accessManage = '$levelsManage', accessReports = '$levelsReports', ";
		$query.= "accessUser = '$levelsUser', accessOthers = '$levelsOther', ";
		$query.= "email = '$email', username = '$username', password = '$password' ";
		$query.= "WHERE crewid = '$crew' LIMIT 1";
		$result = mysql_query( $query );
		
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

		/* set the action */
		$action = "activate";
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
		optimizeSQLTable( "sms_positions" );

	}

?>

	<div class="body">
		
		<? if( $action == "details" ) { ?>
	
		<div class="update">
			<?
	
			$details = "SELECT crewid, firstName, lastName, email, positionid FROM sms_crew WHERE crewid = '$actionid' LIMIT 1";
			$detailResult = mysql_query( $details );
	
			while( $detailFetch = mysql_fetch_array( $detailResult ) ) {
				extract( $detailFetch, EXTR_OVERWRITE );
	
			?>
	
			<span class="fontTitle">Activate <? printText( $detailFetch['firstName'] . " " . $detailFetch['lastName'] ); ?></span>
			<br /><br />
			
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=npcs">
				<table>
					<tr>
						<td class="tableCellLabel">Username</td>
						<td>&nbsp;</td>
						<td><input type="text" class="image" name="username" maxlength="32" value="<?=$detailFetch['username'];?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Password</td>
						<td>&nbsp;</td>
						<td><input type="password" class="image" name="password" maxlength="32" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Email Address</td>
						<td>&nbsp;</td>
						<td><input type="text" class="image" name="email" maxlength="64" value="<?=$detailFetch['email'];?>" /></td>
					</tr>
					<tr>
						<td colspan="3" height="10"></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
						<td>
							<input type="image" src="<?=path_userskin;?>buttons/activate.png" class="button" name="activate" value="Activate" />
							<input type="hidden" name="crew" value="<?=$detailFetch['crewid'];?>" />
							<input type="hidden" name="position" value="<?=$detailFetch['positionid'];?>" />
						</td>
					</tr>
				</table>
			</form>
			
			<? } ?>
		</div><br /><br />
	
		<?
		
		} else {
		
			$check = new QueryCheck;
			$check->checkQuery( $result, $query );
			
			if( !empty( $check->query ) ) {
				$check->message( "non-playing character", $action );
				$check->display();
			}
		
		}
		
		?>
		
		<span class="fontTitle">Manage Non-Playing Characters</span>
		<p>From this page, you can select any of the NPCs that exist in your own department. You
		can edit their bios, promote (or demote) them to another position or rank (below your own).
		If you want to move an NPC from your own department to another department, please contact 
		the CO or XO. In addition, you can also add your own NPCs for your department.
		
		<? if( in_array( "m_npcs2", $sessionAccess ) ) { ?>If you would like to make an NPC a 
		playing character, simply activate them.  You will then be able to edit their account.
		<? } ?><br /><br />
		
		<a href="<?=$webLocation;?>admin.php?page=manage&sub=add" class="add">Add Non-Playing Character &raquo;</a>
		</p>
		<table cellpadding="2" cellspacing="2">
		
		<?
	
		 if( in_array( "m_npcs2", $sessionAccess ) ) {
			
			$departments = "SELECT * FROM sms_departments WHERE deptDisplay = 'y' ORDER BY deptOrder ASC";
			$deptResults = mysql_query( $departments );
			
			/* pull the data out of the department query */
			while ( $dept = mysql_fetch_array( $deptResults ) ) {
				extract( $dept, EXTR_OVERWRITE );
					
		?>
				
			<tr>
				<td colspan="5" height="5"></td>
			</tr>
			<tr>
				<td colspan="5">
					<font class="fontNormal" color="#<?=$deptColor;?>">
						<b><? printText( $deptName ); ?></b>
					</font>
				</td>
			</tr>
				
			<?
					
			$npcs = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.rankid, crew.positionid ";
			$npcs.= "FROM sms_crew AS crew, sms_positions AS position, sms_departments AS dept ";
			$npcs.= "WHERE crew.crewType = 'npc' AND crew.positionid = position.positionid AND ";
			$npcs.= "position.positionDept = dept.deptid AND dept.deptid = '$dept[0]' ";
			$npcs.= "ORDER BY crew.positionid, crew.rankid ASC";
			$npcsResult = mysql_query( $npcs );
			$npcCount = mysql_num_rows( $npcsResult );
					
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
				
			while( $npc = mysql_fetch_assoc( $npcsResult ) ) {
				extract( $npc, EXTR_OVERWRITE );
						
				$rowColor = ($rowCount % 2) ? $color1 : $color2;
				
				echo "<tr class='fontNormal " . $rowColor . "'>";
					echo "<td width='30%'>";
		
				if( in_array( $npc['rankid'], $rankList ) ) {
					
					$npcRanks = "SELECT rankName FROM sms_ranks WHERE rankid = '$npc[rankid]'";
					$npcRanksResult = mysql_query( $npcRanks );
					
					while( $npcRank = mysql_fetch_assoc( $npcRanksResult ) ) {
						extract( $npcRank, EXTR_OVERWRITE );
					
						echo "<b>" . printText( $npcRank['rankName'] . " " . $firstName . " " . $lastName ) . "</b>";
						
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
				
				if( in_array( $npc['positionid'], $posList ) ) {
				
					$npcPos = "SELECT position.positionName, position.positionDept, dept.deptColor ";
					$npcPos.= "FROM sms_positions AS position, sms_departments AS dept WHERE ";
					$npcPos.= "position.positionid = '$npc[positionid]' AND position.positionDept = dept.deptid";
					$npcPosResult = mysql_query( $npcPos );
					
					while( $npcPos = mysql_fetch_assoc( $npcPosResult ) ) {
						extract( $npcPos, EXTR_OVERWRITE );
						
				?>
				
				<td width="30%" style="color: #<?=$deptColor;?>;"><? printText( $positionName );?></td>
				<? } } else { ?>
				<td width="30%" class="red"><b>[ Invalid Position ]</b></td>
				<? } ?>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=user&sub=bio&crew=<?=$npc['crewid'];?>" class="edit">Edit</a></td>
				
				<? if( in_array( "m_npcs2", $sessionAccess ) ) { ?>
					<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=npcs&id=<?=$npc['crewid'];?>&action=details" class="add">Activate</a></td>
				<? } ?>
				
				<td align="center">
					<script type="text/javascript">
						document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=npcs&id=<?=$npc['crewid'];?>&action=delete\" class=\"delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this NPC?')\">Delete</a>" );
					</script>
					<noscript>
						<a href="<?=$webLocation;?>admin.php?page=manage&sub=npcs&id=<?=$npc['crewid'];?>&action=delete" class="delete">Delete</a>
					</noscript>
				</td>
			</tr>
			
		<?
		
				/* increase the row count by 1 */
				$rowCount++;
				
			}
			
			} /* close the NPC position loop */
		
		} /* close the department check */
		
		if( in_array( "m_npcs1", $sessionAccess ) && !in_array( "m_npcs2", $sessionAccess ) ) {  
					
			$userDeptQuery = "SELECT crew.positionid, position.positionDept ";
			$userDeptQuery.= "FROM sms_crew AS crew, sms_positions AS position ";
			$userDeptQuery.= "WHERE crew.crewid = '$sessionCrewid' AND ";
			$userDeptQuery.= "crew.positionid = position.positionid LIMIT 1";
			$userDeptResult = mysql_query( $userDeptQuery );
			$userDept = mysql_fetch_row( $userDeptResult );
							
			$npcs = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.rankid, ";
			$npcs.= "crew.positionid FROM sms_crew AS crew, sms_positions AS position, ";
			$npcs.= "sms_departments AS dept WHERE crew.crewType = 'npc' AND ";
			$npcs.= "position.positionDept = dept.deptid AND ";
			$npcs.= "crew.positionid = position.positionid AND position.positionDept = '$userDept[1]'";
			$npcsResult = mysql_query( $npcs );
			$npcCount = mysql_num_rows( $npcsResult );
											
		}
				
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
		
		/* make sure that a nasty SQL error doesn't get thrown back if there aren't any results */
		if( $npcCount == 0 && in_array( "m_npcs1", $sessionAccess ) ) {
		
			echo "<tr class='fontNormal'>";
				echo "<td colspan='5'>";
					echo "<b>There are no NPCs to moderate in this department! You can create one by using the link above.</b>";
				echo "</td>";
			echo "</tr>";
			
		} else {
		
		while( $npc = mysql_fetch_assoc( $npcsResult ) ) {
			extract( $npc, EXTR_OVERWRITE );
			
			$rowColor = ($rowCount % 2) ? $color1 : $color2;
						
			echo "<tr class='fontNormal " . $rowColor . "'>";
				echo "<td width='30%'>";
	
			if( in_array( $npc['rankid'], $rankList ) ) {
				
				$npcRanks = "SELECT rankName FROM sms_ranks WHERE rankid = '$npc[rankid]'";
				$npcRanksResult = mysql_query( $npcRanks );
				
				while( $npcRank = mysql_fetch_assoc( $npcRanksResult ) ) {
					extract( $npcRank, EXTR_OVERWRITE );
							
					echo "<b>" . printText( $npcRank['rankName'] . " " . $firstName . " " . $lastName ) . "</b>";
					
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
		
		if( in_array( $npc['positionid'], $posList ) ) {
		
			$npcPos = "SELECT position.positionName, position.positionDept, dept.deptColor ";
			$npcPos.= "FROM sms_positions AS position, sms_departments AS dept WHERE ";
			$npcPos.= "position.positionid = '$npc[positionid]' AND position.positionDept = dept.deptid";
			$npcPosResult = mysql_query( $npcPos );
			
			while( $npcPos = mysql_fetch_assoc( $npcPosResult ) ) {
				extract( $npcPos, EXTR_OVERWRITE );
				
		?>
		
			<td width="30%" style="color: #<?=$deptColor;?>;"><? printText( $positionName );?></td>
			<? } } else { ?>
			<td width="30%" class="red"><b>[ Invalid Position ]</b></td>
			<? } ?>
			<td align="center"><a href="<?=$webLocation;?>admin.php?page=user&sub=bio&crew=<?=$npc['crewid'];?>" class="edit">Edit</a></td>
			
			<? if( in_array( "m_npcs2", $sessionAccess ) ) { ?>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=npcs&id=<?=$npc['crewid'];?>&action=details" class="add">Activate</a></td>
			<? } ?>
			
			<td align="center">
				<script type="text/javascript">
					document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=npcs&id=<?=$npc['crewid'];?>&action=delete\" class=\"delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this NPC?')\">Delete</a>" );
				</script>
				<noscript>
					<a href="<?=$webLocation;?>admin.php?page=manage&sub=npcs&id=<?=$npc['crewid'];?>&action=delete" class="delete">Delete</a>
				</noscript>
			</td>
		</tr>
		
		<? $rowCount++; } } ?>
		
		</table>
	</div>
	
<? } else { errorMessage( "NPC management" ); } ?>