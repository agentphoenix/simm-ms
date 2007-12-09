<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/post/message.php
Purpose: Page to send a private message

System Version: 2.6.0
Last Modified: 2007-08-21 0911 EST
**/

/* access check */
if( in_array( "p_pm", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "post";
	
	/* set the vars */
	$send = $_POST['action_x'];
	$reply = $_GET['reply'];
	$replysubject = $_POST['replysubject'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( "send private message page" );
		exit();
	} else {
		/* set the GET variable */
		$id = $_GET['id'];
	}
	
	if( isset( $send ) ) {
		
		/* add the necessary slashes */
		$pmSubject = addslashes( $_POST['pmSubject'] );
		$pmContent = addslashes( $_POST['pmContent'] );
		$pmRecipient = $_POST['pmRecipient'];
		
		if( !isset( $_GET['reply'] ) ) {
			$getLastConvo = "SELECT conversationId from sms_privatemessages ORDER BY conversationId DESC";
			$getLastConvoR = mysql_query( $getLastConvo );
			$lastConvo = mysql_fetch_array( $getLastConvoR );
			$conversation = $lastConvo[0] + 1;
		} else {
			$getLastConvo = "SELECT conversationId from sms_privatemessages WHERE pmid = '$reply'";
			$getLastConvoR = mysql_query( $getLastConvo );
			$lastConvo = mysql_fetch_array( $getLastConvoR );
			$conversation = $lastConvo[0];
		}
		
		echo $getLastConvo;
	
		$insertPM = "INSERT INTO sms_privatemessages ( pmid, pmRecipient, pmAuthor, pmContent, pmDate, pmSubject, pmStatus, conversationId ) ";
		$insertPM.= "VALUES ( '', '$pmRecipient', '$sessionCrewid', '$pmContent', UNIX_TIMESTAMP(), '$pmSubject', 'unread', '$conversation' )";
		$result = mysql_query( $insertPM );
		
		/* optimize the table */
		optimizeSQLTable( "sms_privatemessages" );
		
		/* strip the slashes added for the query */
		$pmSubject = stripslashes( $pmSubject );
		$pmContent = stripslashes( $pmContent );
		
		/** EMAIL THE PM **/
		
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
	
		/* set the email recipient */
		$toFetch = "SELECT email FROM sms_crew WHERE crewid = '$pmRecipient' LIMIT 1";
		$toFetchResult = mysql_query( $toFetch );
		$toEmail = mysql_fetch_array( $toFetchResult );
		
		/* define the variables */
		$to = $toEmail['0'];
		$subject = $emailSubject . " Private Message - " . $pmSubject;
		$message = stripslashes( $pmContent ) . "
	
This private message was sent from " . printCrewNameEmail( $sessionCrewid ) . ".  Please log in to view your Private Message Inbox and reply to this message.  " . $webLocation . "login.php?action=login";
			
		/* send the email */
		mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		
	}
	
	?>
	
	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $insertPM );
				
		if( !empty( $check->query ) ) {
			$check->message( "private message", "send" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Send Private Message</span><br /><br />
	
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=message">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">From</td>
				<td>&nbsp;</td>
				<td><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">To</td>
				<td>&nbsp;</td>
				<td>
					<?
	
					if( !$id ) {
						print_active_crew_select_menu( "pm", "", "", "", "" );
					} else {
						printCrewName( $id, "rank", "noLink" );
						echo "<input type='hidden' name='pmRecipient' value='$id' />";
					}
						
					?>
				</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Subject</td>
				<td>&nbsp;</td>
				<td>
					<?
					
					/* do some logic to figure out what should go in the value field */
					if( isset( $_GET['reply'] ) ) {
						/* if there's already a reply string at the beginning, don't repeat it */
						if( substr( $replysubject, 0, 4 ) == "RE: " ) {
							$fieldValue = $replysubject;
						} else {
							/* if there isn't a reply string at the beginning, put it in */
							$fieldValue = "RE: " . $replysubject;
						}
					} else {
						/* otherwise, don't put anything in the value field */
						$fieldValue = "";
					}
					
					?>
					<input type="text" class="name" name="pmSubject" style="font-weight:bold;" length="100" value="<?=$fieldValue;?>" />
				</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Message</td>
				<td>&nbsp;</td>
				<td><textarea name="pmContent" class="desc" rows="15"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<input type="image" src="<?=path_userskin;?>buttons/send.png" name="action" value="Send" class="button" />
				</td>
			</tr>
		</table>
		</form>
	</div>

<? } else { errorMessage( "private message" ); } ?>