<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/missions.php
Purpose: Page that creates and moderates the missions

System Version: 2.6.0
Last Modified: 2008-02-26 0951 EST
**/

/* access check */
if( in_array( "m_missions", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	
	/* if the POST is set, set the vars */
	if( isset($_POST) )
	{
		$actionCreate = $_POST['action_create_x'];
		$actionUpdate = $_POST['action_update_x'];
		$actionDelete = $_POST['action_delete_x'];
	}
	
	/* make sure a zero date isn't inserted into the db */
	if( $_POST['missionStart'] == "0000-00-00 00:00:00" || $_POST['missionStart'] == "" ) {
		$missionStart = "";
	} else {
		$missionStart = strtotime( $_POST['missionStart'] );
	}
		
	if( $_POST['missionEnd'] == "0000-00-00 00:00:00" || $_POST['missionEnd'] == "" ) {
		$missionEnd = "";
	} else {
		$missionEnd = strtotime( $_POST['missionEnd'] );
	}
	
	/* if the POST action is create */
	if( isset( $actionCreate ) ) {
		
		$create = "INSERT INTO sms_missions ( missionOrder, missionTitle, missionDesc, missionStatus, missionStart, missionEnd, missionImage ) VALUES ( %d, %s, %s, %s, %d, %d, %s )";
		
		$query = sprintf(
			$create,
			escape_string( $_POST['missionOrder'] ),
			escape_string( $_POST['missionTitle'] ),
			escape_string( $_POST['missionDesc'] ),
			escape_string( $_POST['missionStatus'] ),
			escape_string( $missionStart ),
			escape_string( $missionEnd ),
			escape_string( $_POST['missionImage'] )
		);
		
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_missions" );
		
		$action = "create";
	
	/* if the POST action is update */
	} elseif( isset( $actionUpdate ) ) {
		
		/* make sure the mission id is a number */
		if( is_numeric( $_POST['missionid'] ) )
		{
			$update = "UPDATE sms_missions SET missionOrder = %d, missionTitle = %s, missionDesc = %s, missionStatus = %s, ";
			$update.= "missionStart = %d, missionEnd = %d, missionImage = %s WHERE missionid = $_POST[missionid] LIMIT 1";
			
			$query = sprintf(
				$update,
				escape_string( $_POST['missionOrder'] ),
				escape_string( $_POST['missionTitle'] ),
				escape_string( $_POST['missionDesc'] ),
				escape_string( $_POST['missionStatus'] ),
				escape_string( $missionStart ),
				escape_string( $missionEnd ),
				escape_string( $_POST['missionImage'] )
			);
		
			$result = mysql_query( $query );
		
			/* optimize the table */
			optimizeSQLTable( "sms_missions" );
		
			$action = "update";
		}
	
	/* if the POST action is delete */
	} elseif( isset( $actionDelete ) ) {
		
		/* make sure the mission id is a number */
		if( is_numeric( $_POST['missionid'] ) )
		{
			/* do the query */
			$query = "DELETE FROM sms_missions WHERE missionid = $_POST[missionid] LIMIT 1";
			$result = mysql_query( $query );
		
			/* optimize the table */
			optimizeSQLTable( "sms_missions" );
		
			$action = "delete";
		}
	
	}

?>
<script type="text/javascript">
	$(document).ready(function() {
		$("a[rel*=facebox]").click(function() {
			jQuery.facebox(function() {
				jQuery.get('admin/ajax/mission_create.php', function(data) {
					jQuery.facebox(data);
				});
			});
			return false;
		});
	});
</script>

<div class="body">

	<?
	
	$check = new QueryCheck;
	$check->checkQuery( $result, $query );
			
	if( !empty( $check->query ) ) {
		$check->message( "mission", $action );
		$check->display();
	}
	
	?>
	
	<span class="fontTitle">Mission Management</span><br /><br />
	From here you can manage the missions your sim is participating in. Whether you use the posting system or not, mission
	management lets you provide the pertinent information to your crew. To create a mission, click the link below, or to
	update/delete a mission, use the forms below.<br /><br />
	
	<a href="#" rel="facebox" class="fontMedium"><strong>Create New Mission &raquo;</strong></a>
	<br /><br />
	
	<table cellpadding="0" cellspacing="3">
	<?
	
	/* pull the missions from the database */
	$missions = "SELECT * FROM sms_missions ORDER BY missionOrder DESC";
	$missionsResult = mysql_query( $missions );
	
	/* loop through the result set and fill the form */
	while( $missionFetch = mysql_fetch_assoc( $missionsResult ) ) {
		extract( $missionFetch, EXTR_OVERWRITE );
		
	?>
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=missions">
		<tr>
			<td colspan="2" valign="top">
				<span class="fontNormal"><b>Title</b></span><br />
				<input type="text" class="image" name="missionTitle" value="<?=stripslashes( $missionTitle );?>" />
			</td>
			<td valign="top">
				<span class="fontNormal"><b>Start Date</b></span><br />
				<input type="text" class="date" name="missionStart" value="<? if( empty( $missionStart ) ) { echo "0000-00-00 00:00:00"; } else { echo dateFormat( "sql", $missionStart ); } ?>" />
			</td>
			<td width="55%" rowspan="3" align="center" valign="top">
				<span class="fontNormal"><b>Description</b></span><br />
				<textarea name="missionDesc" class="desc" rows="7"><?=stripslashes( $missionDesc );?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" valign="bottom">
				<span class="fontNormal"><b>Image</b></span><br />
				<span class="fontSmall">images/missionimages/</span>
				<input type="text" class="image" name="missionImage" value="<?=$missionImage;?>" maxlength="50" />
			</td>
		    <td valign="bottom">
		    	<span class="fontNormal"><b>End Date</b></span><br />
				<input type="text" class="date" name="missionEnd" value="<? if( empty( $missionEnd ) ) { echo "0000-00-00 00:00:00"; } else { echo dateFormat( "sql", $missionEnd ); } ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="fontNormal"><b>Order</b></span><br />
				<input type="text" class="color" name="missionOrder" value="<?=$missionOrder;?>" />
			</td>
		    <td>
		    	<span class="fontNormal"><b>Status</b></span><br />
				<select name="missionStatus">
					<option value="upcoming"<? if( $missionStatus == "upcoming" ) { echo " selected"; } ?>>Upcoming Mission</option>
					<option value="current"<? if( $missionStatus == "current" ) { echo " selected"; } ?>>Current Mission</option>
					<option value="completed"<? if( $missionStatus == "completed" ) { echo " selected"; } ?>>Completed Mission</option>
				</select>
			</td>
		    <td></td>
		</tr>
		<tr>
			<td colspan="3"></td>
		    <td align="center" valign="top">
		    	<script type="text/javascript">
					document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this mission?')\" />" );
				</script>
				<noscript>
					<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
				</noscript>
				&nbsp;&nbsp;
				<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" />
				<input type="hidden" name="missionid" value="<?=$missionid;?>" />
			</td>
		</tr>
		<tr>
			<td colspan="4" height="25">&nbsp;</td>
		</tr>
		</form>
	<? } ?>
	</table>
</div>

<? } else { errorMessage( "mission management" ); } ?>