<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/user/inbox.php
Purpose: Page that views your private message inbox

System Version: 2.6.0
Last Modified: 2007-10-17 1239 EST
**/

/* access check */
if( in_array( "u_inbox", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "user";
	$action = $_POST['action_x'];
	$box = $_POST['box'];
	$tab = $_GET['tab'];
	$send = $_POST['action_send_x'];
	$reply = $_GET['reply'];
	$replysubject = $_POST['replysubject'];
	
	if( !isset( $tab ) ) {
		$tab = 1;
	}
	
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
		
		$query = "INSERT INTO sms_privatemessages ( pmid, pmRecipient, pmAuthor, pmContent, pmDate, pmSubject, pmStatus, conversationId ) ";
		$query.= "VALUES ( '', '$pmRecipient', '$sessionCrewid', '$pmContent', UNIX_TIMESTAMP(), '$pmSubject', 'unread', '$conversation' )";
		$result = mysql_query( $query );
		
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
		
		$action = "send";
		$subject = "private message";
		
		
	} elseif( isset( $action ) ) {

		$postArray = $_POST;
		array_pop( $postArray );
		array_pop( $postArray );

		foreach( $postArray as $key => $value ) {

			if( $box == "inbox" ) {
				$boxReplace = "pmRecipientDisplay";
			} elseif( $box == "outbox" ) {
				$boxReplace = "pmAuthorDisplay";
			}
				
			$query = "UPDATE sms_privatemessages SET $boxReplace = 'n' ";
			$query.= "WHERE pmid = '$value' LIMIT 1";
			$result = mysql_query( $query );

		}

		/* optimize the table */
		optimizeSQLTable( "sms_privatemessages" );
		
		$action = "remove";
		$subject = "private messages";

	}

	$getMessages = "SELECT * FROM sms_privatemessages WHERE pmRecipient = '$sessionCrewid' ";
	$getMessages.= "AND pmRecipientDisplay = 'y' ORDER BY pmDate DESC";
	$getMessagesResult = mysql_query( $getMessages );
	
	$getMsgCount = "SELECT count(pmid) FROM sms_privatemessages WHERE pmRecipient = '$sessionCrewid' ";
	$getMsgCount.= "AND pmRecipientDisplay = 'y' AND pmStatus = 'unread'";
	$getMsgCountResult = mysql_query( $getMsgCount );
	$msgCount = mysql_fetch_array( $getMsgCountResult );
	
	$msgOut = "SELECT * FROM sms_privatemessages WHERE pmAuthor = '$sessionCrewid' ";
	$msgOut.= "AND pmAuthorDisplay = 'y' ORDER BY pmDate DESC";
	$msgOutResult = mysql_query( $msgOut );

	$rowCount = "0";
	$color1 = "rowColor1";
	$color2 = "rowColor2";

?>

	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( $subject, $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Private Messages</span><br />
		
		<script type="text/javascript">
			$(document).ready(function(){
				$('#container-1 > ul').tabs(<?php echo $tab; ?>);
			});
		</script>
		
		<div id="container-1">
			<ul>
				<li><a href="#one"><span>Inbox<?php if( $msgCount[0] > 0 ) { echo " (" . $msgCount[0] . ")"; } ?></span></a></li>
				<li><a href="#two"><span>Sent Messages</span></a></li>
				<li><a href="#three"><span>Compose New Message</span></a></li>
			</ul>
			
			<div id="one" class="ui-tabs-container ui-tabs-hide">
				<a href="javascript:selectAll(document.received,1);" class="fontNormal"><b>Select/Deselect All</b></a>
				<br /><br />
				
				<form method="post" name="received" action="<?=$webLocation;?>admin.php?page=user&sub=inbox&tab=1">
				<table cellspacing="0" cellpadding="3">
					<tr class="fontMedium">
						<td width="4%"></td>
						<td width="27%"><b>From</b></td>
						<td width="70%"><b>Subject</b></td>
					</tr>
					<tr height="5">
						<td colspan="3"></td>
					</tr>
					<?
					
					/* loop through the results and fill the form */
					while( $msgFetch = mysql_fetch_assoc( $getMessagesResult ) ) {
						extract( $msgFetch, EXTR_OVERWRITE );
						
						$rowColor = ( $rowCount % 2 ) ? $color1 : $color2;
			
					?>
					<tr height="40" class="<?=$rowColor;?>" <? if( $pmStatus == "unread" ) { echo "style='font-weight:bold;'"; } ?>>
						<td align="center" valign="middle"><input type="checkbox" name="inbox_<?=$pmid;?>" value="<?=$pmid;?>" /></td>
						<td>
							<? printCrewName( $pmAuthor, "noRank", "noLink" ); ?><br />
							<span class="fontSmall"><?=dateFormat( "medium", $pmDate );?></span>
						</td>
						<td>
							<a href="<?=$webLocation;?>admin.php?page=user&sub=message&id=<?=$pmid;?>">
								<?
								
								if( $pmStatus == "unread" ) {
									echo "<img src='" . $webLocation . "images/message-unread-icon.png' border='0' alt='' />";
									echo "&nbsp;&nbsp;";
								}
			
								if( !empty( $pmSubject ) ) {
									printText( $pmSubject );
								} else {
									echo "<i>[ No Subject ]</i>";
								}
			
								?>
							</a><br />
							<span class="fontSmall">
								<?
								
								/* build the snippet */
								$length = 15; /* The number of words you want */
								$words = explode( ' ', $pmContent ); /* Creates an array of words */
								$wordsNew = array_slice( $words, 0, $length ); /* Slices the array */
								$pmSnippet = implode( ' ', $wordsNew ); /* Grabs only the specified number of words */
								
								/* print out the snippet */
								printText( $pmSnippet );
								
								/* if the snippet is longer than 10 words, display ellipses at the end */
								if( count( $words ) > $length ) {
									echo " ...";
								}
								
								?>
							</span>
						</td>
					</tr>
					<? $rowCount++; } ?>
					<tr>
						<td colspan="4" height="15"></td>
					</tr>
					<tr>
						<td colspan="4">
							<input type="hidden" name="box" value="inbox" />
							<input type="image" src="<?=path_userskin;?>buttons/remove.png" name="action" value="Remove" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<a href="javascript:selectAll(document.sent,1);" class="fontNormal"><b>Select/Deselect All</b></a>
				<br /><br />
				
				<form method="post" name="sent" action="<?=$webLocation;?>admin.php?page=user&sub=inbox&tab=2">
				<table cellspacing="0" cellpadding="5" border="0">
					<tr class="fontMedium">
						<td width="4%"></td>
						<td width="27%"><b>To</b></td>
						<td width="70%"><b>Subject</b></td>
					</tr>
					<tr height="5">
						<td colspan="3"></td>
					</tr>
					<?
					
					/* loop through the results and fill the form */
					while( $msgOutFetch = mysql_fetch_assoc( $msgOutResult ) ) {
						extract( $msgOutFetch, EXTR_OVERWRITE );
			
						$rowColor = ($rowCount % 2) ? $color1 : $color2;
			
					?>
					<tr height="40" class="<?=$rowColor;?>">
						<td align="center" valign="middle"><input type="checkbox" name="outbox_<?=$pmid;?>" value="<?=$pmid;?>" /></td>
						<td>
							<? printCrewName( $pmRecipient, "noRank", "link" ); ?><br />
							<span class="fontSmall"><?=dateFormat( "medium", $pmDate );?></span>
						</td>
						<td valign="middle">
							<a href="<?=$webLocation;?>admin.php?page=user&sub=message&id=<?=$pmid;?>">
								<?
								
								if( !empty( $pmSubject ) ) {
									printText( $pmSubject );
								} else {
									echo "<i>[ No Subject ]</i>";
								}
								
								?>
							</a><br />
							<span class="fontSmall">
								<?
								
								/* build the snippet */
								$length = 15; /* The number of words you want */
								$words = explode( ' ', $pmContent ); /* Creates an array of words */
								$wordsNew = array_slice( $words, 0, $length ); /* Slices the array */
								$pmSnippet = implode( ' ', $wordsNew ); /* Grabs only the specified number of words */
								
								/* print out the snippet */
								printText( $pmSnippet );
								
								/* if the snippet is longer than 10 words, display ellipses at the end */
								if( count( $words ) > $length ) {
									echo " ...";
								}
								
								?>
							</span>
						</td>
					</tr>
					<? $rowCount++; } ?>
					<tr>
						<td colspan="3" height="20"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="hidden" name="box" value="outbox" />
							<input type="image" src="<?=path_userskin;?>buttons/remove.png" name="action" value="Remove" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<br />
				<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=inbox&tab=3">
				<table>
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
					<tr height="5">
						<td colspan="3"></td>
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
					<tr height="5">
						<td colspan="3"></td>
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
							<input type="image" src="<?=path_userskin;?>buttons/send.png" name="action_send" value="Send" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
		</div>
	
<? } else { errorMessage( "private message inbox" ); } ?>