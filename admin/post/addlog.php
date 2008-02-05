<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/post/addlog.php
Purpose: Page to add a personal log

System Version: 2.5.0
Last Modified: 2007-06-18 1306 EST
**/

/* access check */
if( in_array( "p_addlog", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "post";
	$add = $_POST['action_x'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( "add personal log page" );
		exit();
	} else {
		/* set the GET variable */
		$id = $_GET['id'];
	}
	
	if( $add ) {
		
		/* add the necessary slashes */
		$logTitle = addslashes( $_POST['logTitle'] );
		$logContent = addslashes( $_POST['logContent'] );
		$logAuthor = $_POST['logAuthor'];
	
		$postLog = "INSERT INTO sms_personallogs ( logid, logAuthor, logTitle, logContent, logPosted, logStatus ) ";
		$postLog.= "VALUES ( '', '$logAuthor', '$logTitle', '$logContent', UNIX_TIMESTAMP(), 'activated' )";
		$result = mysql_query( $postLog );
		
		/* optimize the table */
		optimizeSQLTable( "sms_personallogs" );
		
		/* strip the slashes added for the query */
		$logTitle = stripslashes( $logTitle );
		$logContent = stripslashes( $logContent );
		
		/* update the player's last post timestamp */
		$updateTimestamp = "UPDATE sms_crew SET lastPost = UNIX_TIMESTAMP() WHERE crewid = '$logAuthor' LIMIT 1";
		$updateTimestampResult = mysql_query( $updateTimestamp );
		
		/*optimize the table */
		optimizeSQLTable( "sms_crew" );
		
		/* if the user wants the email sent, do it */
		if( $_POST['sendEmail'] == "y" ) {
		
			/** EMAIL THE LOG **/
			
			/* set the email author */
			$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankName ";
			$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
			$userFetch.= "WHERE crew.crewid = '$logAuthor' AND crew.rankid = rank.rankid LIMIT 1";
			$userFetchResult = mysql_query( $userFetch );
			
			while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
				extract( $userFetchArray, EXTR_OVERWRITE );
			
				$firstName = str_replace( "'", "", $firstName );
				$lastName = str_replace( "'", "", $lastName );
				
				$from = $rankName . " " . $firstName . " " . $lastName . " < " . $email . " >";
				$name = $userFetchArray['rankName'] . " " . $userFetchArray['firstName'] . " " . $userFetchArray['lastName'];
		
			}
			
			/* define the variables */
			$to = getCrewEmails( "emailLogs" );
			$subject = "[" . $shipPrefix . " " . $shipName . "] " . $name . "'s Personal Log - " . stripslashes( $logTitle );
			$message = stripslashes( $logContent );
			
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		}
		
	}
	
	?>
	
	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $postLog );
				
		if( !empty( $check->query ) ) {
			$check->message( "personal log", "add" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Add Personal Log</span><br /><br />
	
		This page should be used in the event that a member of the crew has accidentally
		posted incorrectly.  For instance, if a player has replied to one of the emails
		sent out to the system instead of logging in and posting, you can copy and paste
		the contents of their email into this form and put the entry into the system. For
		all other personal logs, please use the <a href="<?=$webLocation;?>admin.php?page=post&sub=log">
		Write Personal Log</a> page.<br /><br />
	
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=addlog">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td>
					<select name="logAuthor">
					<?
					
					/* query the users database */
					$sql = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
					$sql.= "FROM sms_crew AS crew, sms_ranks AS rank ";
					$sql.= "WHERE crew.crewType = 'active' AND crew.rankid = rank.rankid ";
					$sql.= "ORDER BY crew.rankid ASC";
					$result = mysql_query( $sql );
					
					/*
						start looping through what the query returns
						until it runs out of records
					*/
					while( $myrow = mysql_fetch_array( $result ) ) {
						extract( $myrow, EXTR_OVERWRITE );
						
						$authorNumber = $author . $authorNum;
						$authorNumber = $rankName . " " . $firstName . " " . $lastName;
						
						echo "<option value='" . $myrow['crewid'] . "'>" . $authorNumber . "</option>";
						
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Title</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name"  name="logTitle" style="font-weight:bold;" length="100" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Send Email?</td>
				<td>&nbsp;</td>
				<td><input type="checkbox" name="sendEmail" value="y" checked="checked" /></td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
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
					<input type="image" src="<?=path_userskin;?>buttons/add.png" name="action" value="Add" class="button" />
				</td>
			</tr>
		</table>
		</form>
	</div>
	
<? } else { errorMessage( "add personal log" ); } ?>