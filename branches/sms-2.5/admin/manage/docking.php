<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/manage/docking.php
Purpose: Page to manage the docked ships at a starbase

System Version: 2.5.0
Last Modified: 2007-04-27 1140 EST
**/

/* access check */
if( in_array( "m_docking", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$action = $_GET['action'];
	$status = $_GET['status'];
	$edit = $_GET['edit'];
	$editShip = $_POST['editShip_x'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( "docking management page" );
		exit();
	} else {
		/* set the GET variable */
		$dockid = $_GET['id'];
	}

	if( $action == "activate" || $action == "deactivate" ) {
		
		/* do the update query */
		$query = "UPDATE sms_starbase_docking SET dockingStatus = '$status' WHERE dockid = '$dockid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_starbase_docking" );
	
	} if( $action == "delete" ) {
		
		/* do the delete query */
		$query = "DELETE FROM sms_starbase_docking WHERE dockid = '$dockid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_starbase_docking" );
	
	} if( $editShip ) {
		
		/* define the variables */
		$dockingShipName = addslashes( $_POST['dockingShipName'] );
		$dockingShipRegistry = $_POST['dockingShipRegistry'];
		$dockingShipClass = addslashes( $_POST['dockingShipClass'] );
		$dockingShipURL = $_POST['dockingShipURL'];
		$dockingShipCO = addslashes( $_POST['dockingShipCO'] );
		$dockingShipCOEmail = $_POST['dockingShipCOEmail'];
		$dockingDuration = addslashes( $_POST['dockingDuration'] );
		$dockingDesc = addslashes( $_POST['dockingDesc'] );
		$dock = $_POST['dockid'];
		
		/* do the update query */
		$query = "UPDATE sms_starbase_docking SET dockingShipName = '$dockingShipName', ";
		$query.= "dockingShipRegistry = '$dockingShipRegistry', dockingShipClass = '$dockingShipClass', ";
		$query.= "dockingShipCO = '$dockingShipCO', dockingShipCOEmail = '$dockingShipCOEmail', ";
		$query.= "dockingDuration = '$dockingDuration', dockingDesc = '$dockingDesc', dockingShipURL = '$dockingShipURL' ";
		$query.= "WHERE dockid = '$dock' LIMIT 1";
		$result = mysql_query( $query );
		
		/* strip the slashes */
		$dockingShipName = stripslashes( $dockingShipName );
		$dockingShipClass = stripslashes( $dockingShipClass );
		$dockingShipCO = stripslashes( $dockingShipCO );
		$dockingDuration = stripslashes( $dockingDuration );
		$dockingDesc = stripslashes( $dockingDesc );
		
		$action = "update";
		
		/* optimize the table */
		optimizeSQLTable( "sms_starbase_docking" );
	
	}

?>

	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "ship", $action );
			$check->display();
		}
		
		if( $edit ) {
			
			/* select the ship from the database based on the URL */
			$getShip = "SELECT * FROM sms_starbase_docking WHERE dockid = '$edit' LIMIT 1";
			$getShipResult = mysql_query( $getShip );
			$pendingArray = mysql_fetch_assoc( $getShipResult );
			
		?>
			<div class="update">
				<span class="fontTitle">Edit Docked Ship - <?=$pendingArray['dockingShipName'];?></span>
				
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=docking">
				<table>
					<tr>
						<td class="tableCellLabel">Ship Name</td>
						<td>&nbsp;</td>
						<td><input type="text" class="text" name="dockingShipName" value="<?=stripslashes( $pendingArray['dockingShipName'] );?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Ship Registry</td>
						<td>&nbsp;</td>
						<td><input type="text" class="text" name="dockingShipRegistry" value="<?=$pendingArray['dockingShipRegistry'];?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Ship Class</td>
						<td>&nbsp;</td>
						<td><input type="text" class="text" name="dockingShipClass" value="<?=stripslashes( $pendingArray['dockingShipClass'] );?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Ship URL</td>
						<td>&nbsp;</td>
						<td><input type="text" class="text" name="dockingShipURL" value="<?=$pendingArray['dockingShipURL'];?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Ship CO</td>
						<td>&nbsp;</td>
						<td><input type="text" class="text" name="dockingShipCO" value="<?=stripslashes( $pendingArray['dockingShipCO'] );?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Ship CO Email</td>
						<td>&nbsp;</td>
						<td><input type="text" class="text" name="dockingShipCOEmail" value="<?=$pendingArray['dockingShipCOEmail'];?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Duration</td>
						<td>&nbsp;</td>
						<td><input type="text" class="text" name="dockingDuration" value="<?=stripslashes( $pendingArray['dockingDuration'] );?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Description</td>
						<td>&nbsp;</td>
						<td><textarea name="dockingDesc" rows="3"><?=stripslashes( $pendingArray['dockingDesc'] );?></textarea></td>
					</tr>
					<tr>
						<td colspan="3" height="10"></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td>
							<input type="hidden" name="dockid" value="<?=$pendingArray['dockid'];?>" />
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="editShip" class="button" value="Update" />
						</td>
					</tr>
				</table>
				</form>
			</div><br /><br />
		<? } ?>
	
		<span class="fontTitle">Manage Docked Ships</span><br /><br />
		
		<table>
			
			<?
			
			$getPendingDockings = "SELECT * FROM sms_starbase_docking WHERE dockingStatus = 'pending' ORDER BY dockid ASC";
			$getPendingDockingsResult = mysql_query( $getPendingDockings );
			$countPendingDockings = mysql_num_rows( $getPendingDockingsResult );
			
			if( $countPendingDockings > 0 ) {
			
			?>
			
			<tr>
				<td colspan="5" class="fontLarge"><b>Pending Docking Requests</b></td>
			</tr>
			
			<?
			
			/* loop through the results and fill the form */
			while( $fetchPendings = mysql_fetch_assoc( $getPendingDockingsResult ) ) {
				extract( $fetchPendings, EXTR_OVERWRITE );
			
			?>
			
			<tr class="fontNormal">
				<td width="35%">
					<a href="<?=$webLocation;?>index.php?page=dockedships&ship=<?=$fetchPendings['dockid'];?>">
						<? printText( $fetchPendings['dockingShipName'] . " " . $fetchPendings['dockingShipRegistry'] ); ?>
					</a>
				</td>
				<td width="35%"><? printText( $fetchPendings['dockingShipCO'] ); ?></td>
				<td>&nbsp;</td>
				<td width="10%" align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=docking&edit=<?=$fetchPendings['dockid'];?>">Edit</a></td>
				<td width="10%" align="center">
					<script type="text/javascript">
						document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=docking&action=delete&id=<?=$fetchPendings['dockid'];?>\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this pending docked ship?')\">Delete</a>" );
					</script>
					<noscript>
						<a href="<?=$webLocation;?>admin.php?page=manage&sub=docking&action=delete&id=<?=$fetchPendings['dockid'];?>">Delete</a>
					</noscript>
				</td>
				<td width="10%" align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=docking&action=activate&status=activated&id=<?=$fetchPendings['dockid'];?>">Activate</a></td>
			</tr>
			<tr>
				<td colspan="6" height="30"></td>
			</tr>
			
			<? } } ?>
		
			<tr>
				<td colspan="6" class="fontLarge"><b>Docked Ships</b></td>
			</tr>
			
			<?
			
			$getDockedShips = "SELECT * FROM sms_starbase_docking WHERE dockingStatus = 'activated' ORDER BY dockid ASC";
			$getDockedShipsResult = mysql_query( $getDockedShips );
			$countDockedShips = mysql_num_rows( $getDockedShipsResult );
			
			if( $countDockedShips == 0 ) {
			
			?>
			
			<tr class="fontNormal">
				<td colspan="6">There are currently no docked ships.</td>
			</tr>
			
			<?
			
			} elseif( $countDockedShips > 0 ) {
			
				/* loop through the results and fill the form */
				while( $fetchDocked = mysql_fetch_assoc( $getDockedShipsResult ) ) {
					extract( $fetchDocked, EXTR_OVERWRITE );
			
			?>
			
			<tr class="fontNormal">
				<td>
					<a href="<?=$webLocation;?>index.php?page=dockedships&ship=<?=$fetchDocked['dockid'];?>">
						<? printText( $fetchDocked['dockingShipName'] . " " . $fetchDocked['dockingShipRegistry'] ); ?>
					</a>
				</td>
				<td><? printText( $fetchDocked['dockingShipCO'] ); ?></td>
				<td>&nbsp;</td>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=docking&edit=<?=$fetchDocked['dockid'];?>">Edit</a></td>
				<td align="center">
					<script type="text/javascript">
						document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=docking&action=delete&id=<?=$fetchDocked['dockid'];?>\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this docked ship?')\">Delete</a>" );
					</script>
					<noscript>
						<a href="<?=$webLocation;?>admin.php?page=manage&sub=docking&action=delete&id=<?=$fetchDocked['dockid'];?>">Delete</a>
					</noscript>
				</td>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=docking&action=deactivate&status=departed&id=<?=$fetchDocked['dockid'];?>">Deactivate</a></td>
			</tr>
			
			<? } } ?>
			
			<?
			
			$getPreviousDockings = "SELECT * FROM sms_starbase_docking WHERE dockingStatus = 'departed' ORDER BY dockid ASC";
			$getPreviousDockingsResult = mysql_query( $getPreviousDockings );
			$countPreviousDockings = mysql_num_rows( $getPreviousDockingsResult );
			
			if( $countPreviousDockings > 0 ) {
			
			?>
			
			<tr>
				<td colspan="6" height="30"></td>
			</tr>
			<tr>
				<td colspan="6" class="fontLarge"><b>Previous Docked Ships</b></td>
			</tr>
			
			<?
			
			/* loop through the results and fill the form */
			while( $fetchPrevious = mysql_fetch_assoc( $getPreviousDockingsResult ) ) {
				extract( $fetchPrevious, EXTR_OVERWRITE );
			
			?>
			
			<tr class="fontNormal">
				<td>
					<a href="<?=$webLocation;?>index.php?page=dockedships&ship=<?=$fetchPrevious['dockid'];?>">
						<? printText( $fetchPrevious['dockingShipName'] . " " . $fetchPrevious['dockingShipRegistry'] ); ?>
					</a>
				</td>
				<td><? printText( $fetchPrevious['dockingShipCO'] ); ?></td>
				<td>&nbsp;</td>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=docking&edit=<?=$fetchPrevious['dockid'];?>">Edit</a></td>
				<td align="center">
					<script type="text/javascript">
						document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=docking&action=delete&id=<?=$fetchPrevious['dockid'];?>\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this previously docked ship?')\">Delete</a>" );
					</script>
					<noscript>
						<a href="<?=$webLocation;?>admin.php?page=manage&sub=docking&action=delete&id=<?=$fetchPrevious['dockid'];?>">Delete</a>
					</noscript>
				</td>
				<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=docking&action=activate&status=activated&id=<?=$fetchPrevious['dockid'];?>">Activate</a></td>
			</tr>
			
			<? } } ?>
			
		</table>
		
	</div>
	
<? } else { errorMessage( "docked ship management" ); } ?>