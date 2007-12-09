<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: Nathan Wharry [ mail@herschwolf.net ]
File: pages/dockingrequest.php
Purpose: To display the form for ships to request docking permission at the starbase

System Version: 2.6.0
Last Modified: 2007-08-21 0935 EST
**/

/* check the simm type */
if( $simmType == "starbase" ) {

	/* define the page class and vars */
	$pageClass = "ship";
	$action = $_POST['action_x'];
	
	/* pull in the menu */
	if( isset( $sessionCrewid ) ) {
		include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
	} else {
		include_once( 'skins/' . $skin . '/menu.php' );
	}
	
	/* do logic to check for which skin to use */
	if( isset( $sessionCrewid ) ) {
		$skinChoice = $sessionDisplaySkin;
	} else {
		$skinChoice = $skin;
	}
	
	/* determine if request form is being sumbitted */
	if ( $action ) {
	
		/* addslashes for sql entry */
		$dockingShipName = addslashes($_POST['dockingShipName']);
		$dockingShipRegistry = addslashes($_POST['dockingShipRegistry']);
		$dockingShipClass = addslashes($_POST['dockingShipClass']);
		$dockingShipURL = addslashes($_POST['dockingShipURL']);
		$dockingShipCO = addslashes($_POST['dockingShipCO']);
		$dockingShipCOEmail = addslashes($_POST['dockingShipCOEmail']);
		$dockingDuration = addslashes($_POST['dockingDuration']);
		$dockingDesc = addslashes($_POST['dockingDesc']);
		
		$dockShip = "INSERT INTO sms_starbase_docking ";
		$dockShip.= "(dockingShipName, dockingShipRegistry, dockingShipClass, dockingShipURL, dockingShipCO, dockingShipCOEmail, dockingDuration, dockingDesc, dockingStatus) ";
		$dockShip.= "VALUES ('$dockingShipName','$dockingShipRegistry','$dockingShipClass','$dockingShipURL','$dockingShipCO','$dockingShipCOEmail','$dockingDuration','$dockingDesc','pending') ";
		$result = mysql_query( $dockShip );
		
		/* optimize the table */
		optimizeSQLTable( "sms_starbase_docking" );
	
		/* stripslashes for emailing request */
		$dockingShipName = stripslashes( $_POST['dockingShipName'] );
		$dockingShipRegistry = stripslashes( $_POST['dockingShipRegistry'] );
		$dockingShipClass = stripslashes( $_POST['dockingShipClass'] );
		$dockingShipURL = stripslashes( $_POST['dockingShipURL'] );
		$dockingShipCO = stripslashes( $_POST['dockingShipCO'] );
		$dockingShipCOEmail = stripslashes( $_POST['dockingShipCOEmail'] );
		$dockingDuration = stripslashes( $_POST['dockingDuration'] );
		$dockingDesc = stripslashes( $_POST['dockingDesc'] );
		
		if( !empty( $result ) ) {
			/* email the ship CO */
			$subject1 = $emailSubject . " Docking Request";
			$to1 = $dockingShipCOEmail;
			$message1 = $dockingShipCO . ", thank you for submitting a request to dock with " . $shipPrefix . " " . $shipName . ".  The CO has been sent a copy of your request and will be reviewing it shortly.  In the meantime, please feel free to browse our site (" . $webLocation . ") until the CO reviews your request.
	
This is an automatically generated message, please do not respond.";
		
			mail( $to1, $subject1, $message1, "From: " . $shipPrefix . " " . $shipName . "< donotreply >\nX-Mailer: PHP/" . phpversion() );
			
			/* email the CO */
			$subject2 = $emailSubject . " Docking Request";
			$to2 = printCOEmail();
			$message2 = "Greetings " . printCO() . ",
		
$dockingShipCO of the $dockingShipName has sent a request to dock with the $shipName.  To answer the Commanding Officer and premit or deny his request please log in to your Control Panel.
	
" . $webLocation . "login.php?action=login";
		
			mail( $to2, $subject2, $message2, "From: " . $dockingShipCO . " < " . $dockingShipCOEmail . ">\nX-Mailer: PHP/" . phpversion() );
		}
	
	}  /* close if action statement */

?>

	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $dockShip );
				
		if( !empty( $check->query ) ) {
			$check->message( "docking request", "submit" );
			$check->display();
		}
		
		?>
	
		<span class="fontTitle">Docking Request Form</span><br /><br />
	
		<form method="post" action="<?=$webLocation;?>index.php?page=dockingrequest">
		<table>
			<tr>
				<td colspan="3" class="fontLarge"><b>Ship Information</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Ship Name</td>
				<td>&nbsp;</td>
				<td><input type="text" class="text" name="dockingShipName" size="20" maxlength="100" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Ship Registry</td>
				<td>&nbsp;</td>
				<td><input type="text" class="text" name="dockingShipRegistry" size="20" maxlength="32" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Ship Class</td>
				<td>&nbsp;</td>
				<td><input type="text" class="text" name="dockingShipClass" size="20" maxlength="50" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Ship Website</td>
				<td>&nbsp;</td>
				<td>
					<input type="text" class="text" name="dockingShipURL" size="20" maxlength="128" />
					&nbsp;&nbsp;&nbsp;<span class="fontSmall">*Note: Please include the "http://" in your website*</span>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10">&nbsp;</td>
			</tr>
			
			<tr>
				<td colspan="3" class="fontLarge"><b>CO Information</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Commanding Officer</td>
				<td>&nbsp;</td>
				<td><input type="text" class="text" name="dockingShipCO" size="20" maxlength="128" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Email</td>
				<td>&nbsp;</td>
				<td><input type="text" class="text" name="dockingShipCOEmail" size="20" maxlength="50" /></td>
			</tr>
			<tr>
				<td colspan="3" height="10">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" class="fontLarge"><b>Docking Information</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Duration</td>
				<td>&nbsp;</td>
				<td><input type="text" class="text" name="dockingDuration" size="20" maxlength="20" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Docking Purpose</td>
				<td>&nbsp;</td>
				<td><textarea name="dockingDesc" rows="5"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="25">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td><input type="image" src="<?=$webLocation;?>skins/<?=$skinChoice;?>/buttons/submit.png" name="action" class="button" value="Submit" /></td>
			</tr>
		</table>
		</form>
	</div>

<? } else { errorMessage( "ship docking request" ); } ?>