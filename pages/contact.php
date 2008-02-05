<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: Nathan Wharry [ mail@herschwolf.net ]
File: pages/contact.php
Purpose: Page used to contact the CO, XO, or Webmaster of a simm for any user

System Version: 2.5.0
Last Modified: 2007-07-10 1108 EST
**/

/* define the page class and set the vars */
$pageClass = "main";
$send = $_POST['action_x'];
$query = "";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
	$pageSkin = $sessionDisplaySkin;
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
	$pageSkin = $skin;
}

/* check action and send email if needed */
if ( $send ) {

	/* strip the slashes */
	$message = stripslashes( $_POST['message'] );
	$subject = stripslashes( $_POST['subject'] );
	$from = $_POST['email'];
		
	/* define the subject */
	if( !empty( $title ) ) {
		$subject = "[" . $shipPrefix . " " . $shipName . "] " . $subject . "";
	} else {
		$subject = "[" . $shipPrefix . " " . $shipName . "] Information Request";
	}

	/* determine who to send the email to */
	if ( $recipients == "coNxo" ) {
		$to = printCOEmail() . ", " . printXOEmail();
	} elseif ( $recipients == "co" ) {
		$to = printCOEmail();
	} elseif ( $recipients == "webmaster" ) {
		$to = $webmasterEmail;
	}
	
	/* set action variable */
	$action = "send";
	$query = "query";
	
	/* send email if message contains something */
	if( $message > "" ) { 
		$result = "true";
		
		/* send the email */
		mail ( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
	}
	
} /* end action/send if statement */
	
?>

<div class="body">
	
	<?
		
	$check = new QueryCheck;
	$check->checkQuery( $result, $query );
			
	if( !empty( $check->query ) ) {
		$check->message( "message", $action );
		$check->display();
	}
	
	?>
	
	<span class="fontTitle">Contact Us</span><br /><br />
	
	<table>
		<tr>
			<td class="tableCellLabel">Commanding Officer</td>
			<td>&nbsp;</td>
			<td><?=printCO(); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Executive Officer</td>
			<td>&nbsp;</td>
			<td><?=printXO(); ?></td>
		</tr>
		
		<? if ( $hasWebmaster == "y" ) { ?>
		<tr>
			<td class="tableCellLabel">Webmaster</td>
			<td>&nbsp;</td>
			<td><?=$webmasterName;?></td>
		</tr>
		<? } ?>
		
		<tr>
			<td colspan="3" height="25"></td>
		</tr>
	
		<form action="<?=$webLocation;?>index.php?page=contact" method="post">
		<tr>
			<td class="tableCellLabel">Subject</td>
			<td>&nbsp;</td>
			<td><input type="text" class="name" name="subject" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Email Address</td>
			<td>&nbsp;</td>
			<td><input type="text" class="name" name="email" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Recipients</td>
			<td>&nbsp;</td>
			<td>
				<select name="recipients">
					<option value="coNxo">Commanding and Executive Officers</option>
					<option value="co">Commanding Officer</option>
					<? if ( $hasWebmaster == "y" ) { ?>
						<option value="webmaster">Webmaster</option>
					<? } /* close if hasWebmaster statement */ ?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Message</td>
			<td>&nbsp;</td>
			<td><textarea name="message" class="desc" rows="10"></textarea></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td colspan="2"></td>
			<td><input type="image" src="<?=$webLocation;?>skins/<?=$pageSkin;?>/buttons/send.png" name="action" value="Send" class="button" /></td>
		</tr>
		</form>
	</table>
</div>