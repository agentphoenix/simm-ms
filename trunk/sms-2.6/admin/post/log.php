<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/post/log.php
Purpose: Page to post a personal log

System Version: 2.6.0
Last Modified: 2007-08-21 0910 EST
**/

/* access check */
if( in_array( "p_log", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "post";
	$actionPost = $_POST['action_post_x'];
	$actionSave = $_POST['action_save_x'];
	$actionDelete = $_POST['action_delete_x'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( "post personal log page" );
		exit();
	} else {
		/* set the GET variable */
		$id = $_GET['id'];
	}

	if( $actionPost ) {
		
		/* add the necessary slashes */
		$logTitle = addslashes( $_POST['logTitle'] );
		$logContent = addslashes( $_POST['logContent'] );
	
		/** check to see if the user is moderated **/
		$getModerated = "SELECT crewid FROM sms_crew WHERE moderateLogs = 'y'";
		$getModeratedResult = mysql_query( $getModerated );
	
		while( $moderated = mysql_fetch_array( $getModeratedResult ) ) {
			extract( $moderated, EXTR_OVERWRITE );
	
			$modArray[] = $moderated['0'];
	
		}
		/** end moderation check **/
		
		if( count( $modArray ) > "0" && in_array( $sessionCrewid, $modArray ) ) {
			$logStatus = "pending";
		} elseif( $sessionCrewid == "" ) {
			$logStatus = "pending";
		} elseif( $sessionCrewid == "0" ) {
			$logStatus = "pending";
		} elseif( $sessionCrewid > "0" ) {
			$logStatus = "activated";
		}
	
		if( !$id ) {
			$query = "INSERT INTO sms_personallogs ( logid, logAuthor, logTitle, logContent, logPosted, logStatus ) ";
			$query.= "VALUES ( '', '$sessionCrewid', '$logTitle', '$logContent', UNIX_TIMESTAMP(), '$logStatus' )";
			$result = mysql_query( $query );
		} else {
			$query = "UPDATE sms_personallogs SET logTitle = '$logTitle', logContent = '$logContent', ";
			$query.= "logStatus = '$logStatus', logPosted = UNIX_TIMESTAMP() WHERE logid = '$id' LIMIT 1";
			$result = mysql_query( $query );
		}
		
		/* optimize the table */
		optimizeSQLTable( "sms_personallogs" );
		
		$action = "post";
		
		/* strip the slashes added for the query */
		$logTitle = stripslashes( $logTitle );
		$logContent = stripslashes( $logContent );
		
		/* update the player's last post timestamp */
		$updateTimestamp = "UPDATE sms_crew SET lastPost = UNIX_TIMESTAMP() WHERE crewid = '$sessionCrewid' LIMIT 1";
		$updateTimestampResult = mysql_query( $updateTimestamp );
		
		/*optimize the table */
		optimizeSQLTable( "sms_crew" );
		
		/** EMAIL THE LOG **/
		
		/* set the email author */
		$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankName ";
		$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$userFetch.= "WHERE crew.crewid = '$sessionCrewid' AND crew.rankid = rank.rankid LIMIT 1";
		$userFetchResult = mysql_query( $userFetch );
		
		while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
			extract( $userFetchArray, EXTR_OVERWRITE );
		
			$firstName = str_replace( "'", "", $firstName );
			$lastName = str_replace( "'", "", $lastName );
			
			$from = $rankName . " " . $firstName . " " . $lastName . " < " . $email . " >";
			$name = $userFetchArray['rankName'] . " " . $userFetchArray['firstName'] . " " . $userFetchArray['lastName'];
	
		}	
		
		/* if the post has an activated status */
		if( $logStatus == "activated" ) {
		
			/* define the variables */
			$to = getCrewEmails( "emailLogs" );
			$subject = $emailSubject . " " . $name . "'s Personal Log - " . stripslashes( $logTitle );
			$message = stripslashes( $logContent );
			
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		} elseif( $logStatus == "pending" ) {
		
			/* define the variables */
			$to = printCOEmail();
			$subject = $emailSubject . " " . $name . "'s Personal Log - " . stripslashes( $logTitle ) . " (Awaiting Approval)";
			$message = stripslashes( $logContent ) . "
	
Please log in to approve this log.  " . $webLocation . "login.php?action=login";
			
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		}
		
	} if( $actionSave ) {
	
		/* add the necessary slashes */
		$logTitle = addslashes( $_POST['logTitle'] );
		$logContent = addslashes( $_POST['logContent'] );
	
		if( !$id ) {
			$query = "INSERT INTO sms_personallogs ( logid, logAuthor, logTitle, logContent, logPosted, logStatus ) ";
			$query.= "VALUES ( '', '$sessionCrewid', '$logTitle', '$logContent', UNIX_TIMESTAMP(), 'saved' )";
		} else {
			$query = "UPDATE sms_personallogs SET logTitle = '$logTitle', logContent = '$logContent', ";
			$query.= "logStatus = 'saved', logPosted = UNIX_TIMESTAMP() WHERE logid = '$id' LIMIT 1";
		}
		
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_personallogs" );
		
		$action = "save";
		
		/* strip the slashes added for the query */
		$logTitle = stripslashes( $logTitle );
		$logContent = stripslashes( $logContent );
	
	} if( $actionDelete ) {
	
		/* delete the log */
		$query = "DELETE FROM sms_personallogs WHERE logid = '$id' LIMIT 1";
		$result = mysql_query( $query );
	
		/* optimize the table */
		optimizeSQLTable( "sms_personallogs" );
		
		$action = "delete";
	
	}
	
	?>
	
	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "personal log", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Post Personal Log</span><br /><br />
	
		<? if( !$id ) { ?>
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=log">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Title</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="logTitle" style="font-weight:bold;" length="100" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Content</td>
				<td>&nbsp;</td>
				<td><textarea name="logContent" class="desc" rows="15"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" value="Save" class="button" />
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/post.png" name="action_post" value="Post" class="button" />
				</td>
			</tr>
		</table>
		</form>
		
		<?
		
		} elseif( $id && !$actionDelete ) {
	
			$getLog = "SELECT * FROM sms_personallogs WHERE logid = '$id' LIMIT 1";
			$getLogResults = mysql_query( $getLog );
			
			while( $fetchLog = mysql_fetch_array( $getLogResults ) ) {
				extract( $fetchLog, EXTR_OVERWRITE );
			}
	
		?>
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=log&id=<?=$id;?>">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Title</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="logTitle" style="font-weight:bold;" length="100" value="<?=stripslashes( $logTitle );?>" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Content</td>
				<td>&nbsp;</td>
				<td><textarea name="logContent" class="desc" rows="15"><?=stripslashes( $logContent );?></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this personal log?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" class="button" value="Save" />
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/post.png" name="action_post" class="button" value="Post" />
				</td>
			</tr>
		</table>
		</form>
	
		<? } elseif( $id && $actionDelete ) { ?>
	
		Please return to the Control Panel to continue.
	
		<? } ?>
		
	</div>

<? } else { errorMessage( "personal log posting" ); } ?>