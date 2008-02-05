<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/user/inbox.php
Purpose: Page that views your private message inbox

System Version: 2.5.0
Last Modified: 2007-04-27 0419 EST
**/

/* access check */
if( in_array( "u_inbox", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "user";
	$action = $_POST['action_x'];
	$box = $_POST['box'];

	if( $action ) {

		$postArray = $_POST;
		array_pop( $postArray );
		array_pop( $postArray );

		foreach( $postArray as $key => $value ) {

			if( $box == "inbox" ) {
				$boxReplace = "pmRecipientDisplay";
			} elseif( $box == "outbox" ) {
				$boxReplace = "pmAuthorDisplay";
			}
				
			$removeQuery = "UPDATE sms_privatemessages SET $boxReplace = 'n' ";
			$removeQuery.= "WHERE pmid = '$value' LIMIT 1";
			$result = mysql_query( $removeQuery );

		}

		/* optimize the table */
		optimizeSQLTable( "sms_privatemessages" );
		
		$action = "remove";

	}

	$getMessages = "SELECT * FROM sms_privatemessages WHERE pmRecipient = '$sessionCrewid' ";
	$getMessages.= "AND pmRecipientDisplay = 'y' ORDER BY pmDate DESC";
	$getMessagesResult = mysql_query( $getMessages );

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
		$check->checkQuery( $result, $removeQuery );
				
		if( !empty( $check->query ) ) {
			$check->message( "private messages", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Private Messages</span><br /><br />
	
		<div class="pmHeader">Received</div>
		<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=inbox">
		<table cellspacing="0" cellpadding="3">
			<tr class="fontMedium">
				<td></td>
				<td width="30%"><b>From</b></td>
				<td width="40%"><b>Subject</b></td>
				<td width="30%"><b>Date Received</b></td>
			</tr>
			<?
			
			/* loop through the results and fill the form */
			while( $msgFetch = mysql_fetch_assoc( $getMessagesResult ) ) {
				extract( $msgFetch, EXTR_OVERWRITE );
	
				$rowColor = ( $rowCount % 2 ) ? $color1 : $color2;
	
			?>
			<tr class="fontNormal <?=$rowColor;?>" <? if( $pmStatus == "unread" ) { echo "style='font-weight:bold;'"; } ?>>
				<td><input type="checkbox" name="inbox_<?=$pmid;?>" value="<?=$pmid;?>" /></td>
				<td><? printCrewName( $pmAuthor, "noRank", "noLink" ); ?></td>
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
					</a>
				</td>
				<td><?=dateFormat( "medium", $pmDate );?></td>
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
	
		<br /><br />
	
		<div class="pmHeader">Sent</div>
		<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=inbox">
		<table cellspacing="0" cellpadding="3">
			<tr class="fontMedium">
				<td></td>
				<td width="30%"><b>To</b></td>
				<td width="40%"><b>Subject</b></td>
				<td width="30%"><b>Date Sent</b></td>
			</tr>
			<?
			
			/* loop through the results and fill the form */
			while( $msgOutFetch = mysql_fetch_assoc( $msgOutResult ) ) {
				extract( $msgOutFetch, EXTR_OVERWRITE );
	
				$rowColor = ($rowCount % 2) ? $color1 : $color2;
	
			?>
			<tr class="fontNormal <?=$rowColor;?>">
				<td><input type="checkbox" name="outbox_<?=$pmid;?>" value="<?=$pmid;?>" /></td>
				<td><? printCrewName( $pmRecipient, "noRank", "noLink" ); ?></td>
				<td>
					<a href="<?=$webLocation;?>admin.php?page=user&sub=message&id=<?=$pmid;?>">
						<?
						
						if( !empty( $pmSubject ) ) {
							printText( $pmSubject );
						} else {
							echo "<i>[ No Subject ]</i>";
						}
						
						?>
					</a>
				</td>
				<td><?=dateFormat( "medium", $pmDate );?></td>
			</tr>
			<? $rowCount++; } ?>
			<tr>
				<td colspan="4" height="15"></td>
			</tr>
			<tr>
				<td colspan="4">
					<input type="hidden" name="box" value="outbox" />
					<input type="image" src="<?=path_userskin;?>buttons/remove.png" name="action" value="Remove" class="button" />
				</td>
			</tr>
		</table>
		</form>
	</div>
	
<? } else { errorMessage( "private message inbox" ); } ?>