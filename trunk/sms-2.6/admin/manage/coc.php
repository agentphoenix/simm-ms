<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/manage/coc.php
Purpose: Page to change the order of the chain of command

System Version: 2.5.0
Last Modified: 2007-04-22 1608 EST
**/

/* access check */
if( in_array( "m_coc", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$action = $_GET['action'];
	
	/* if the POST action is update */
	if( $action == "update" ) {
		
		/* set the variables */
		$cocid = $_POST['cocid'];
		$userid = $_POST['crewid'];
		
		/* do the SQL Update query */
		$sql = "UPDATE sms_coc SET crewid = '$userid' WHERE cocid = '$cocid' LIMIT 1";
		$result = mysql_query( $sql );
		
		/* optimize the table */
		optimizeSQLTable( "sms_coc" );
	
	/* if the POST action is create */
	} elseif( $action == "create" ) {
		
		/* do the SQL Update query */
		$sql = "INSERT INTO sms_coc ( cocid, crewid ) VALUES ( '', '0' )";
		$result = mysql_query( $sql );
		
		/* optimize the table */
		optimizeSQLTable( "sms_coc" );
	
	/* if the POST action is delete */
	} elseif( $action == "delete" ) {
		
		/* get the last cocid */
		$getLastId = "SELECT cocid FROM sms_coc ORDER BY cocid DESC LIMIT 1";
		$getLastIdResult = mysql_query( $getLastId );
		$lastID = mysql_fetch_assoc( $getLastIdResult );
		
		/* do the SQL Update query */
		$sql = "DELETE FROM sms_coc WHERE cocid = '$lastID[cocid]' LIMIT 1";
		$result = mysql_query( $sql );
		
		/* optimize the table */
		optimizeSQLTable( "sms_coc" );

	}

?>

	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $sql );
		
		if( !empty( $check->query ) ) {
			$check->message( "chain of command", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage the Chain of Command</span><br /><br />
		
		<a href="<?=$webLocation;?>admin.php?page=manage&sub=coc&action=create">Add CoC Position &raquo;</a>
		<br />
		<a href="<?=$webLocation;?>admin.php?page=manage&sub=coc&action=delete">Remove Last CoC Position &raquo;</a>
		<br /><br />
		
		<table cellspacing="1">
	
		<?
		
		/* pull the CoC from the database */
		$coc = "SELECT * FROM sms_coc ORDER BY cocid ASC";
		$cocResult = mysql_query($coc);
		
		/* set the i variable */
		$i = 1;
		
		/* Start pulling the array and populate the variables */
		while ($cocList = mysql_fetch_array($cocResult)) {
			extract($cocList, EXTR_OVERWRITE);
		
		?>
			
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=coc&action=update">
			<tr>
				<td valign="middle" align="center" width="30%"><b>CoC Position #<?=$i;?></b></td>
				<td width="30%">
					<input type="hidden" name="cocid" value="<?=$cocList['0'];?>" />
					<select name="crewid">
					
					<?
					
					/* pull the crew from the database */
					$crew = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName FROM ";
					$crew.= "sms_crew AS crew, sms_ranks AS rank WHERE crew.rankid = rank.rankid AND ";
					$crew.= "crew.crewType = 'active' ORDER BY crew.rankid ASC";
					$crewResult = mysql_query($crew);
					
					/* populate the form */
					while ($crewList = mysql_fetch_array($crewResult)) {
						extract($crewList, EXTR_OVERWRITE);
						
						if( $cocList['1'] == $crewid ) {
							echo "<option value='$cocList[1]' selected>$rankName $firstName $lastName</option>";
						} else {
							echo "<option value='$crewid'>$rankName $firstName $lastName</option>";
						}
					
					}
					
					?>
					</select>
				</td>
				<td>&nbsp;</td>
				<td width="30%" valign="middle">
					<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action" value="Update" />
				</td>
			</form>
			</tr>
			<tr>
				<td colspan="4" height="10"></td>
			</tr>
			<? $i = $i+1; } ?>
	</table>
	</div>

<? } else { errorMessage( "chain of command management" ); } ?>