<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: Nathan Wharry [ mail@herschwolf.net ]
File: pages/dockedships.php
Purpose: To display the ships currently docked at the starbase

System Version: 2.5.0
Last Modified: 2007-04-05 2353 EST
**/

/* define the page class */
$pageClass = "ship";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

if( $simmType == "starbase" ) {
	
	/* define the vars */
	$ship = $_GET['ship'];
	
	/* pull in the docked ships */
	$getShips = "SELECT * ";
	$getShips.= "FROM sms_starbase_docking ";
	$getShips.= "WHERE dockingStatus = 'activated' ORDER BY dockid ASC";
	$getShipsResult = mysql_query( $getShips );
	
	/* code to decide whether to view all ships or specific ship docked */
	if ( !isset( $ship ) ) {
	
	/* display all ships docked */
	?>
	
	<div class="body">
		<span class="fontTitle">Docked Ships</span>
		
		<?
	
		/*
			if the person is logged in and has level 5 access, display an icon
			that will take them to edit the entry
		*/
		if( isset( $sessionCrewid ) && in_array( "m_docking", $sessionAccess ) ) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<a href='" . $webLocation . "admin.php?page=manage&sub=docking'>";
			echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' />";
			echo "</a>";
		}
		
		?>
		<br /><br />
		
		<table cellspacing="2" cellpadding="2">
			<tr class="fontMedium">
				<td width="15%"><b>Ship Name</b></td>
				<td width="15%"><b>Ship Class</b></td>
				<td width="25%"><b>Commanding Officer</b></td>
				<td width="45%"><b>Docking Purpose</b></td>
			</tr>
		
		<?
		
		/* extract the variables for the docked ships and place in table */
		while( $dockedinfo = mysql_fetch_array( $getShipsResult ) ) {
			extract( $dockedinfo, EXTR_OVERWRITE );
			
		?>
		
			<tr>
				<td>
					<a href="<?=$webLocation;?>index.php?page=dockedships&ship=<?=$dockid;?>">
						<? printText( $dockingShipName ); ?>
					</a>
				</td>
				<td><? printText( $dockingShipClass ); ?></td>
				<td><? printText( $dockingShipCO ); ?></td>
				<td><? printText( $dockingDesc ); ?></td>
			</tr>
		
		<? } /* close the while statement */ ?>
		
		</table>
	</div>
		
	<?
	
	} else { 
		
		/* pull specific ship information based on dockid */
		$getShip = "SELECT * ";
		$getShip.= "FROM sms_starbase_docking ";
		$getShip.= "WHERE dockid = '$ship' LIMIT 1";
		$getShipResult = mysql_query( $getShip );
		
		while( $shipinfo = mysql_fetch_array( $getShipResult ) ) {
			extract( $shipinfo, EXTR_OVERWRITE );
		}
		
	?>
		
	<div class="body">
		<span class="fontTitle">Docked Ship: <? printText( $dockingShipName . " " . $dockingShipRegistry ); ?></span>
		
		<?
	
		/*
			if the person is logged in and has level 5 access, display an icon
			that will take them to edit the entry
		*/
		if( isset( $sessionCrewid ) && in_array( "m_docking", $sessionAccess ) ) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<a href='" . $webLocation . "admin.php?page=manage&sub=docking'>";
			echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' />";
			echo "</a>";
		}
		
		?>
		
		<? if( $dockingStatus == "pending" ) { ?>
		<br />
		<b class="yellow">[ Activation Pending ]</b>
		<? } ?>
		
		<? if( $dockingStatus == "departed" ) { ?>
		<br />
		<b class="red">[ Inactive ]</b>
		<? } ?>
		<br /><br />
		
		<table>
			<tr>
				<td colspan="3" class="fontLarge"><b>Ship Information</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Ship Name</td>
				<td>&nbsp;</td>
				<td><? printText( $dockingShipName ); ?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Ship Registry</td>
				<td>&nbsp;</td>
				<td><? printText( $dockingShipRegistry ); ?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Ship Class</td>
				<td>&nbsp;</td>
				<td><? printText( $dockingShipClass ); ?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Ship Website</td>
				<td>&nbsp;</td>
				<td><a href="<?=$dockingShipURL;?>"><?=$dockingShipURL;?></a></td>
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
				<td><? printText( $dockingShipCO ); ?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Email</td>
				<td>&nbsp;</td>
				<td><? printText( $dockingShipCOEmail ); ?></td>
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
				<td><? printText( $dockingDuration ); ?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Docking Purpose</td>
				<td>&nbsp;</td>
				<td><? printText( $dockingDesc ); ?></td>
			</tr>
		</table>
		
		<br /><br />
		<b class="fontMedium">
			<a href="<?=$weblocation;?>index.php?page=dockedships">&laquo; Return to Docked Ship Listing</a>
		</b>
	</div>
	
<?

	} /* close the else statement */
} else { errorMessage( "starship docking" ); }

?>