<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/activate.php
Purpose: Page to manage pending users, posts, logs, and docking requests

System Version: 2.5.5
Last Modified: 2007-11-07 0834 EST
**/

/* access check */
if(
	in_array( "x_approve_users", $sessionAccess ) ||
	in_array( "x_approve_posts", $sessionAccess ) ||
	in_array( "x_approve_logs", $sessionAccess ) ||
	in_array( "x_approve_news", $sessionAccess ) ||
	in_array( "x_approve_docking", $sessionAccess )
) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$action = $_GET['action'];
	$type = $_GET['type'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( "activation page" );
		exit();
	} else {
		/* set the GET variable */
		$actionid = $_GET['id'];
	}

	if( $type == "crew" && in_array( "x_approve_users", $sessionAccess ) ) {
		
		if( $action == "activate" && isset( $_POST['activateid'] ) ) {
			
			$activate = $_POST['activateid'];
			$position = $_POST['position'];
			$rank = $_POST['rank'];
			$message = stripslashes( $_POST['acceptMessage'] );
			
			/* get the position type from the database */
			$getPosType = "SELECT positionType FROM sms_positions WHERE positionid = '$position' LIMIT 1";
			$getPosTypeResult = mysql_query( $getPosType );
			$positionType = mysql_fetch_row( $getPosTypeResult );
			
			/* if the position is a department head, set the access levels to DH */
			/* otherwise, set it to standard player */
			if( $positionType['0'] == "senior" ) {
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
			
			$query = "UPDATE sms_crew SET positionid = '$position', crewType = 'active', ";
			$query.= "accessPost = '$levelsPost', accessManage = '$levelsManage', ";
			$query.= "accessReports = '$levelsReports', accessUser = '$levelsUser', ";
			$query.= "accessOthers = '$levelsOther', rankid = '$rank', ";
			$query.= "leaveDate = '' WHERE crewid = '$activate' LIMIT 1";
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
			
			/** EMAIL THE APPROVAL **/
	
			/* set the email author */
			$userFetch = "SELECT email FROM sms_crew WHERE crewid = '$activate' LIMIT 1";
			$userFetchResult = mysql_query( $userFetch );
			$userEmail = mysql_fetch_row( $userFetchResult );
			
			/* define the variables */
			$to = $userEmail[0] . ", " . printCOEmail();
			$from = printCO() . " < " . printCOEmail() . " >";
			$subject = "[" . $shipPrefix . " " . $shipName . "] Your Application";
			
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		} elseif( $action == "activate" && isset( $_POST['rejectid'] ) ) {

			$actionid = $_POST['rejectid'];
			$message = stripslashes( $_POST['rejectMessage'] );
			
			/** EMAIL THE DENIAL **/
	
			/* set the email author */
			$userFetch = "SELECT email FROM sms_crew WHERE crewid = '$actionid' LIMIT 1";
			$userFetchResult = mysql_query( $userFetch );
			$userEmail = mysql_fetch_row( $userFetchResult );
			
			/* define the variables */
			$to = $userEmail[0] . ", " . printCOEmail();
			$from = printCO() . " < " . printCOEmail() . " >";
			$subject = "[" . $shipPrefix . " " . $shipName . "] Your Application";
			
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );

			$action = "reject";
			
			$query = "DELETE FROM sms_crew WHERE crewid = '$actionid' LIMIT 1";
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_crew" );
		
		}
		
	} elseif( $type == "post" && in_array( "x_approve_posts", $sessionAccess ) ) {
		
		if( $action == "activate" ) {
		
			$query = "UPDATE sms_posts SET postStatus = 'activated' WHERE postid = '$actionid' LIMIT 1";
			$result = mysql_query( $query );
			 
			 /* optimize the table */
			optimizeSQLTable( "sms_posts" );

			/** EMAIL THE POST **/

			$getPostContents = "SELECT * FROM sms_posts WHERE postid = '$actionid' LIMIT 1";
			$getPostContentsResult = mysql_query( $getPostContents );
			$fetchPost = mysql_fetch_assoc( $getPostContentsResult );
	
			/* set the email author */
			$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, ";
			$userFetch.= "rank.rankName FROM sms_crew AS crew, sms_ranks AS rank WHERE ";
			$userFetch.= "crew.crewid = '$fetchPost[postAuthor]' AND crew.rankid = rank.rankid LIMIT 1";
			$userFetchResult = mysql_query( $userFetch );
			
			while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
				extract( $userFetchArray, EXTR_OVERWRITE );
			}
			
			$firstName = str_replace( "'", "", $firstName );
			$lastName = str_replace( "'", "", $lastName );
			
			$from = $rankName . " " . $firstName . " " . $lastName . " < " . $email . " >";
			
			/* define the variables */
			$to = getCrewEmails( "emailPosts" );
			$subject = "[" . $shipPrefix . " " . $shipName . "] " . printMissionTitle( $fetchPost['postMission'] ) . " - " . $fetchPost['postTitle'];
			$message = "A Post By " . displayEmailAuthors( $fetchPost['postAuthor'], 'noLink' ) . "
Location: " . $fetchPost['postLocation'] . "
Timeline: " . $fetchPost['postTimeline'] . "
Tag: " . $fetchPost['postTag'] . "

" . $fetchPost['postContent'] . "";
		
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		} elseif( $action == "delete" ) {
		
			$query = "DELETE FROM sms_posts WHERE postid = '$actionid' LIMIT 1";
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_posts" );
		
		}
		
	} elseif( $type == "log" && in_array( "x_approve_logs", $sessionAccess ) ) {
		
		if( $action == "activate" ) {
		
			$query = "UPDATE sms_personallogs SET logStatus = 'activated' WHERE logid = '$actionid' LIMIT 1";
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_personallogs" );

			/** EMAIL THE LOG **/

			$getLogContents = "SELECT * FROM sms_personallogs WHERE logid = '$actionid' LIMIT 1";
			$getLogContentsResult = mysql_query( $getLogContents );
			$fetchLog = mysql_fetch_assoc( $getLogContentsResult );
	
			/* set the email author */
			$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, ";
			$userFetch.= "rank.rankName FROM sms_crew AS crew, sms_ranks AS rank WHERE ";
			$userFetch.= "crew.crewid = '$fetchLog[logAuthor]' AND crew.rankid = rank.rankid LIMIT 1";
			$userFetchResult = mysql_query( $userFetch );
			
			while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
				extract( $userFetchArray, EXTR_OVERWRITE );
			}
			
			$firstName = str_replace( "'", "", $firstName );
			$lastName = str_replace( "'", "", $lastName );
			
			$from = $rankName . " " . $firstName . " " . $lastName . " < " . $email . " >";
			$name = $rankName . " " . $firstName . " " . $lastName;
			
			/* define the variables */
			$to = getCrewEmails( "emailLogs" );
			$subject = "[" . $shipPrefix . " " . $shipName . "] " . $name . "'s Personal Log - " . stripslashes( $fetchLog['logTitle'] );
			$message = stripslashes( $fetchLog['logContent'] );
		
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		} elseif( $action == "delete" ) {
		
			$query = "DELETE FROM sms_personallogs WHERE logid = '$actionid' LIMIT 1";
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_personallogs" );
		
		}
		
	} elseif( $type == "news" && in_array( "x_approve_news", $sessionAccess ) ) {
		
		if( $action == "activate" ) {
		
			$query = "UPDATE sms_news SET newsStatus = 'activated' WHERE newsid = '$actionid' LIMIT 1";
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_news" );

			/** EMAIL THE NEWS **/

			$getNewsContents = "SELECT * FROM sms_news WHERE newsid = '$actionid' LIMIT 1";
			$getNewsContentsResult = mysql_query( $getNewsContents );
			$fetchNews = mysql_fetch_assoc( $getNewsContentsResult );

			/* pull the category name */
			$getCategory = "SELECT catName FROM sms_news_categories WHERE catid = '$fetchNews[newsCat]' LIMIT 1";
			$getCategoryResult = mysql_query( $getCategory );
			$category = mysql_fetch_assoc( $getCategoryResult );

			/* set the email author */
			$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, ";
			$userFetch.= "rank.rankName FROM sms_crew AS crew, sms_ranks AS rank WHERE ";
			$userFetch.= "crew.crewid = '$fetchNews[newsAuthor]' AND crew.rankid = rank.rankid LIMIT 1";
			$userFetchResult = mysql_query( $userFetch );
			
			while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
				extract( $userFetchArray, EXTR_OVERWRITE );
			}

			$firstName = str_replace( "'", "", $firstName );
			$lastName = str_replace( "'", "", $lastName );
			
			$from = $rankName . " " . $firstName . " " . $lastName . " < " . $email . " >";
			
			/* define the variables */
			$to = getCrewEmails( "emailNews" );
			$subject = "[" . $shipPrefix . " " . $shipName . "] " . stripslashes( $category['catName'] ) . " - " . stripslashes( $fetchNews['newsTitle'] );
			$message = "A News Item Posted By " . printCrewNameEmail( $fetchNews['newsAuthor'] ) . "
			
" . stripslashes( $fetchNews['newsContent'] );

			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		} elseif( $action == "delete" ) {
		
			$query = "DELETE FROM sms_news WHERE newsid = '$actionid' LIMIT 1";
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_news" );
		
		}
		
	} elseif( $type == "docking" && $simmType == "starbase" && in_array( "x_approve_docking", $sessionAccess ) ) {
		
		if( $action == "activate" ) {
		
			$query = "UPDATE sms_starbase_docking SET dockingStatus = 'activated' WHERE dockid = '$actionid' LIMIT 1";
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_starbase_docking" );
			
			/** EMAIL THE APPROVAL **/
	
			/* set the email author */
			$emailFetch = "SELECT dockingShipCOEmail FROM sms_starbase_docking WHERE dockid = '$actionid' LIMIT 1";
			$emailFetchResult = mysql_query( $emailFetch );
			$coEmail = mysql_fetch_row( $emailFetchResult );
			
			/* define the variables */
			$to = $coEmail['email'] . ", " . printCOEmail();
			$from = printCO() . " < " . printCOEmail() . " >";
			$subject = "[" . $shipPrefix . " " . $shipName . "] Your Docking Request";
			$message = "Thank you for submitting a request to dock with the " . $shipPrefix . " " . $shipName . ".  After reviewing your application, we are pleased to inform you that your request to dock with our starbase has been approved!

The CO of the station will be in contact with you shortly.  Thank you for interest in docking with us.";
		
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		} elseif( $action == "delete" ) {
		
			$query = "DELETE FROM sms_starbase_docking WHERE dockid = '$actionid' LIMIT 1";
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_stabase_docking" );
			
			/** EMAIL THE DENIAL **/
	
			/* set the email author */
			$emailFetch = "SELECT dockingShipCOEmail FROM sms_starbase_docking WHERE dockid = '$actionid' LIMIT 1";
			$emailFetchResult = mysql_query( $emailFetch );
			$coEmail = mysql_fetch_row( $emailFetchResult );
			
			/* define the variables */
			$to = $coEmail['email'] . ", " . printCOEmail();
			$from = printCO() . " < " . printCOEmail() . " >";
			$subject = "[" . $shipPrefix . " " . $shipName . "] Your Docking Request";
			$message = "Thank you for submitting a request to dock with the " . $shipPrefix . " " . $shipName . ".  After reviewing your application, we regret to inform you that your request to dock with our starbase has been denied.  There can be many reasons for this.  If you would like clarification, please contact the CO.";
		
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		}
		
	}

?>

	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( $type, $action );
			$check->display();
		}
		
		if( $_GET['activate'] == "details" && in_array( "x_approve_users", $sessionAccess ) ) {
			
			/* do some advanced checking to make sure someone's not trying */
			/* to do a SQL injection */
			if( preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
				errorMessageIllegal( "activation page" );
				exit();
			} else {
				/* set the GET variable */
				$id = $_GET['id'];
			}
			
			$getPendingCrew = "SELECT crewid, firstName, lastName, positionid, rankid ";
			$getPendingCrew.= "FROM sms_crew WHERE crewid = '$id' LIMIT 1";
			$getPendingCrewResult = mysql_query( $getPendingCrew );
			$pendingArray = mysql_fetch_assoc( $getPendingCrewResult );
			
		?>
			<div class="update">
				<span class="fontTitle">Activate <? printText( $pendingArray['firstName'] . " " . $pendingArray['lastName'] ); ?></span>
				
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=activate&type=crew&action=activate">
				<table>
					
					<?
					
					$ranks = "SELECT rank.rankid, rank.rankName, rank.rankImage, dept.deptColor FROM sms_ranks AS rank, ";
					$ranks.= "sms_departments AS dept WHERE dept.deptClass = rank.rankClass AND dept.deptDisplay = 'y' ";
					$ranks.= "GROUP BY rank.rankid ORDER BY rank.rankClass, rank.rankOrder ASC";
					$ranksResult = mysql_query( $ranks );
					
					$positions = "SELECT position.positionid, position.positionName, dept.deptName, ";
					$positions.= "dept.deptColor FROM sms_positions AS position, sms_departments AS dept ";
					$positions.= "WHERE position.positionOpen > '0' AND dept.deptDisplay = 'y' AND ";
					$positions.= "dept.deptid = position.positionDept AND dept.deptType = 'playing' ";
					$positions.= "ORDER BY dept.deptOrder, position.positionid ASC";
					$positionsResult = mysql_query( $positions );
					
					?>
					
					<tr>
						<td class="tableCellLabel">Position</td>
						<td>&nbsp;</td>
						<td>
							<select name="position">
							<?
					
							$currentPosition = "SELECT position.positionid, position.positionName, dept.deptName, ";
							$currentPosition.= "dept.deptColor FROM sms_positions AS position, sms_departments ";
							$currentPosition.= "AS dept WHERE position.positionid = '$pendingArray[positionid]' ";
							$currentPosition.= "AND position.positionDept = dept.deptid";
							$currentPositionResult = mysql_query( $currentPosition );
							$fetchCurrentPosition = mysql_fetch_assoc( $currentPositionResult );
							
							echo "<option value='" . $fetchCurrentPosition['positionid'] . "' style='color:#" . $fetchCurrentPosition['deptColor'] . "'>" . $fetchCurrentPosition['deptName'] . " - " . $fetchCurrentPosition['positionName'] . "</option>";
							
							while( $position = mysql_fetch_array( $positionsResult ) ) {
								extract( $position, EXTR_OVERWRITE );
						
								echo "<option value='" . $position['positionid'] . "' style='color:#" . $position['deptColor'] . "'>" . $position['deptName'] . " - " . $position['positionName'] . "</option>";
								
							}
							
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Rank</td>
						<td>&nbsp;</td>
						<td>
							<select name="rank">
							<?
					
							while( $rank = mysql_fetch_array( $ranksResult ) ) {
								extract( $rank, EXTR_OVERWRITE );
								
								if( $client->property('browser') == "ie" ) {
									if( $pendingArray['rankid'] == $rank['rankid'] ) {
										echo "<option value='" . $rankid . "' style='color:#" . $deptColor . ";' selected>" . $rankName . "</option>";
									} else {
										echo "<option value='" . $rankid . "' style='color:#" . $deptColor . ";'>" . $rankName . "</option>";
									}
								} else {
									if( $pendingArray['rankid'] == $rank['rankid'] ) {
										echo "<option value='" . $rankid . "' style='background:#000 url( images/ranks/" . $sessionDisplayRank . "/" . $rankImage . " ) no-repeat 0 100%; height:40px; color:#" . $deptColor . ";' selected>" . $rankName . "</option>";
									} else {
										echo "<option value='" . $rankid . "' style='background:#000 url( images/ranks/" . $sessionDisplayRank . "/" . $rankImage . " ) no-repeat 0 100%; height:40px; color:#" . $deptColor . ";'>" . $rankName . "</option>";
									}
								}
								
							}
							
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Email Message</td>
						<td>&nbsp;</td>
						<td>
							<textarea name="acceptMessage" class="narrowTable" rows="10"><?=stripslashes( $acceptMessage );?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td>
							<input type="hidden" name="activateid" value="<?=$pendingArray['crewid'];?>" />
							<input type="image" src="<?=path_userskin;?>buttons/approve.png" name="activate" class="button" value="Approve" />
						</td>
					</tr>
				</table>
				</form>
			</div><br /><br />
		<?
		
		} elseif( $_GET['reject'] == "details" && in_array( "x_approve_users", $sessionAccess ) ) {
	
			/* do some advanced checking to make sure someone's not trying */
			/* to do a SQL injection */
			if( preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
				errorMessageIllegal( "activation page" );
				exit();
			} else {
				/* set the GET variable */
				$id = $_GET['id'];
			}
			
			$getPendingCrew = "SELECT crewid, firstName, lastName, positionid, rankid ";
			$getPendingCrew.= "FROM sms_crew WHERE crewid = '$id' LIMIT 1";
			$getPendingCrewResult = mysql_query( $getPendingCrew );
			$pendingArray = mysql_fetch_assoc( $getPendingCrewResult );
			
		?>
			<div class="update">
				<span class="fontTitle">Reject <? printText( $pendingArray['firstName'] . " " . $pendingArray['lastName'] ); ?></span>
				
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=activate&type=crew&action=activate">
				<table>
					<tr>
						<td class="tableCellLabel">Email Message</td>
						<td>&nbsp;</td>
						<td>
							<textarea name="rejectMessage" class="narrowTable" rows="10"><?=stripslashes( $rejectMessage );?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td>
							<input type="hidden" name="rejectid" value="<?=$pendingArray['crewid'];?>" />
							<input type="image" src="<?=path_userskin;?>buttons/reject.png" name="reject" class="button" value="Reject" />
						</td>
					</tr>
				</table>
				</form>
			</div><br /><br />
		<? } ?>
	
		<span class="fontTitle">Manage Pending Items</span><br /><br />
		
		<table>
			<? if( in_array( "x_approve_users", $sessionAccess ) ) { ?>
			<tr>
				<td colspan="5" class="fontLarge"><b>Pending Users</b></td>
			</tr>
			
			<?
			
			$getPendingUsers = "SELECT crew.crewid, crew.firstName, crew.lastName, position.positionName ";
			$getPendingUsers.= "FROM sms_crew AS crew, sms_positions AS position WHERE ";
			$getPendingUsers.= "crew.positionid = position.positionid AND crewType = 'pending'";
			$getPendingUsersResult = mysql_query( $getPendingUsers );
			$countPendingUsers = mysql_num_rows( $getPendingUsersResult );
			
			if( $countPendingUsers == 0 ) {
			
			?>
			
			<tr class="fontNormal">
				<td colspan="6">There are currently no pending users</td>
			</tr>
			
			<?
			
			} elseif( $countPendingUsers > 0 ) {
			
				/* loop through the results and fill the form */
				while( $pendingUser = mysql_fetch_assoc( $getPendingUsersResult ) ) {
					extract( $pendingUser, EXTR_OVERWRITE );
			
			?>
			
			<tr class="fontNormal">
				<td width="35%"><? printText( $pendingUser['firstName'] . " " . $pendingUser['lastName'] ); ?></td>
				<td width="35%"><? printText( $pendingUser['positionName'] ); ?></td>
				<td>&nbsp;</td>
				<td width="10%" align="center"><a href="<?=$webLocation;?>index.php?page=bio&crew=<?=$pendingUser['crewid'];?>">View Bio</a></td>
				<td width="10%" align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=activate&reject=details&id=<?=$pendingUser['crewid'];?>">Deny</a></td>
				<td width="10%" align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=activate&activate=details&id=<?=$pendingUser['crewid'];?>">Approve</a></td>
			</tr>
			
			<? } } ?>
		
			<tr>
				<td colspan="6" height="30"></td>
			</tr>
			<? } if( in_array( "x_approve_posts", $sessionAccess ) ) { ?>
			
			<tr>
				<td colspan="6" class="fontLarge"><b>Pending Mission Posts</b></td>
			</tr>
			
			<?
			
			$getPendingPosts = "SELECT postid, postTitle FROM sms_posts WHERE postStatus = 'pending'";
			$getPendingPostsResult = mysql_query( $getPendingPosts );
			$countPendingPosts = mysql_num_rows( $getPendingPostsResult );
			
			if( $countPendingPosts == 0 ) {
			
			?>
			
			<tr class="fontNormal">
				<td colspan="6">There are currently no pending posts</td>
			</tr>
			
			<?
			
			} elseif( $countPendingPosts > 0 ) {
			
				/* loop through the results and fill the form */
				while( $pendingPost = mysql_fetch_assoc( $getPendingPostsResult ) ) {
					extract( $pendingPost, EXTR_OVERWRITE );
			
			?>
			
			<tr class="fontNormal">
				<td><? printText( $pendingPost['postTitle'] ); ?></td>
				<td><? displayAuthors( $pendingPost['postid'], "noLink" ); ?></td>
				<td>&nbsp;</td>
				<td align="center"><a href="<?=$webLocation;?>index.php?page=post&id=<?=$pendingPost['postid'];?>">View Post</a></td>
				<td align="center">
					<script type="text/javascript">
						document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=activate&type=post&id=<?=$pendingPost['postid'];?>&action=delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this pending mission post?')\">Delete</a>" );
					</script>
					<noscript>
						<a href="<?=$webLocation;?>admin.php?page=manage&sub=activate&type=post&id=<?=$pendingPost['postid'];?>&action=delete">Delete</a>
					</noscript>
				</td>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=activate&type=post&id=<?=$pendingPost['postid'];?>&action=activate">Activate</a></td>
			</tr>
			
			<? } } ?>
			
			<tr>
				<td colspan="6" height="30"></td>
			</tr>
			
			<? } if( in_array( "x_approve_logs", $sessionAccess ) ) { ?>
			<tr>
				<td colspan="6" class="fontLarge"><b>Pending Personal Logs</b></td>
			</tr>
			
			<?
			
			$getPendingLogs = "SELECT logid, logTitle, logAuthor ";
			$getPendingLogs.= "FROM sms_personallogs WHERE logStatus = 'pending'";
			$getPendingLogsResult = mysql_query( $getPendingLogs );
			$countPendingLogs = mysql_num_rows( $getPendingLogsResult );
			
			if( $countPendingLogs == 0 ) {
			
			?>
			
			<tr class="fontNormal">
				<td colspan="6">There are currently no pending personal logs</td>
			</tr>
			
			<?
			
			} elseif( $countPendingLogs > 0 ) {
			
				/* loop through the results and fill the form */
				while( $pendingLog = mysql_fetch_assoc( $getPendingLogsResult ) ) {
					extract( $pendingLog, EXTR_OVERWRITE );
			
			?>
			
			<tr class="fontNormal">
				<td><? printText( $pendingLog['logTitle'] ); ?></td>
				<td><? printCrewName( $pendingLog['logAuthor'], "rank", "noLink" ); ?></td>
				<td>&nbsp;</td>
				<td align="center"><a href="<?=$webLocation;?>index.php?page=log&id=<?=$pendingLog['logid'];?>">View Log</a></td>
				<td align="center">
					<script type="text/javascript">
						document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=activate&type=log&id=<?=$pendingLog['logid'];?>&action=delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this pending personal log?')\">Delete</a>" );
					</script>
					<noscript>
						<a href="<?=$webLocation;?>admin.php?page=manage&sub=activate&type=log&id=<?=$pendingLog['logid'];?>&action=delete">Delete</a>
					</noscript>
				</td>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=activate&type=log&id=<?=$pendingLog['logid'];?>&action=activate">Activate</a></td>
			</tr>
			
			<? } } ?>
	
			<tr>
				<td colspan="6" height="30"></td>
			</tr>
			
			<? } if( in_array( "x_approve_news", $sessionAccess ) ) { ?>
			<tr>
				<td colspan="6" class="fontLarge"><b>Pending News Items</b></td>
			</tr>
			
			<?
			
			$getPendingNews = "SELECT newsid, newsTitle, newsAuthor ";
			$getPendingNews.= "FROM sms_news WHERE newsStatus = 'pending'";
			$getPendingNewsResult = mysql_query( $getPendingNews );
			$countPendingNews = mysql_num_rows( $getPendingNewsResult );
			
			if( $countPendingNews == 0 ) {
			
			?>
			
			<tr class="fontNormal">
				<td colspan="6">There are currently no pending news items</td>
			</tr>
			
			<?
			
			} elseif( $countPendingNews > 0 ) {
			
				/* loop through the results and fill the form */
				while( $pendingNews = mysql_fetch_assoc( $getPendingNewsResult ) ) {
					extract( $pendingNews, EXTR_OVERWRITE );
			
			?>
			
			<tr class="fontNormal">
				<td><? printText( $pendingNews['newsTitle'] ); ?></td>
				<td><? printCrewName( $pendingNews['newsAuthor'], "rank", "noLink" ); ?></td>
				<td>&nbsp;</td>
				<td align="center"><a href="<?=$webLocation;?>index.php?page=news">View News</a></td>
				<td align="center">
					<script type="text/javascript">
						document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=activate&type=news&id=<?=$pendingNews['newsid'];?>&action=delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this pending news item?')\">Delete</a>" );
					</script>
					<noscript>
						<a href="<?=$webLocation;?>admin.php?page=manage&sub=activate&type=news&id=<?=$pendingNews['newsid'];?>&action=delete">Delete</a>
					</noscript>
				</td>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=activate&type=news&id=<?=$pendingNews['newsid'];?>&action=activate">Activate</a></td>
			</tr>
			
			<? } } ?>
			
			<? } if( $simmType == "starbase" && in_array( "x_approve_docking", $sessionAccess ) ) { ?>
			<tr>
				<td colspan="6" height="30"></td>
			</tr>
			<tr>
				<td colspan="6" class="fontLarge"><b>Pending Docking Requests</b></td>
			</tr>
			
			<?
			
			$getPendingDockings = "SELECT dockid, dockingShipName, dockingShipRegistry, dockingShipCO ";
			$getPendingDockings.= "FROM sms_starbase_docking WHERE dockingStatus = 'pending'";
			$getPendingDockingsResult = mysql_query( $getPendingDockings );
			$countPendingDockings = mysql_num_rows( $getPendingDockingsResult );
			
			if( $countPendingDockings == 0 ) {
			
			?>
			
			<tr class="fontNormal">
				<td colspan="6">There are currently no pending docking requests</td>
			</tr>
			
			<?
			
			} elseif( $countPendingDockings > 0 ) {
			
				/* loop through the results and fill the form */
				while( $pendingDocking = mysql_fetch_assoc( $getPendingDockingsResult ) ) {
					extract( $pendingDocking, EXTR_OVERWRITE );
			
			?>
			
			<tr class="fontNormal">
				<td><? printText( $pendingDocking['dockingShipName'] . " " . $pendingDocking['dockingShipRegistry'] ); ?></td>
				<td><? printText( $pendingDocking['dockingShipCO'] ); ?></td>
				<td>&nbsp;</td>
				<td align="center"><a href="<?=$webLocation;?>index.php?page=dockedships&ship=<?=$pendingDocking['dockid'];?>">View Request</a></td>
				<td align="center">
					<script type="text/javascript">
						document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=activate&type=docking&id=<?=$pendingDocking['dockid'];?>&action=delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this pending docking request?')\">Delete</a>" );
					</script>
					<noscript>
						<a href="<?=$webLocation;?>admin.php?page=manage&sub=activate&type=docking&id=<?=$pendingDocking['dockid'];?>&action=delete">Delete</a>
					</noscript>
				</td>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=activate&type=docking&id=<?=$pendingDocking['dockid'];?>&action=activate">Activate</a></td>
			</tr>
			
			<? } } ?>
			<? } /* closes the if( simmType ) logic */ ?>
		</table>
		
	</div>
	
<? } else { errorMessage( "activation" ); } ?>