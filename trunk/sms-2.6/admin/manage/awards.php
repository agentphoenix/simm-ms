<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/awards.php
Purpose: Page that moderates the awards

System Version: 2.5.0
Last Modified: 2007-07-10 0958 EST
**/

/* access check */
if( in_array( "m_awards", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$update = $_POST['action_update_x'];
	$create = $_POST['action_create_x'];
	$delete = $_POST['action_delete_x'];

	/* define the POST variables */
	$awardid = $_POST['awardid'];
	$awardName = addslashes( $_POST['awardName'] );
	$awardDesc = addslashes( $_POST['awardDesc'] );
	$awardOrder = $_POST['awardOrder'];
	$awardImage = $_POST['awardImage'];
	
	/* if the POST action is update */
	if( $update ) {
		
		/* do the update query */
		$query = "UPDATE sms_awards SET ";
		$query.= "awardName = '$awardName', awardOrder = '$awardOrder', ";
		$query.= "awardImage = '$awardImage', awardDesc = '$awardDesc', awardOrder = '$awardOrder' ";
		$query.= "WHERE awardid = '$awardid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_awards" );
		
		$action = "update";
	
	/* if the POST action is create */
	} elseif( $create ) {
		
		/* do the create query */
		$query = "INSERT INTO sms_awards ( awardid, awardName, awardImage, awardDesc, awardOrder ) ";
		$query.= "VALUES ( '', '$awardName', '$awardImage', '$awardDesc', '$awardOrder' )";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_awards" );
		
		$action = "create";
	
	/* if the POST action is delete */
	} elseif( $delete ) {
		
		/* do the delete query */
		$query = "DELETE FROM sms_awards WHERE awardid = '$awardid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_awards" );
		
		$action = "delete";
	
	}
	
	/* strip the slashes */
	$awardName = stripslashes( $awardName );
	$awardDesc = stripslashes( $awardDesc );

?>

	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "crew award", $action );
			$check->display();
		}
		
		?>
	
		<span class="fontTitle">Create New Award</span><br /><br />
		You can create awards specific to your sim or use the awards from the fleet you are in. When you
		create an award, you will need to have two images, one small image (that will be displayed in the
		character bio) and a larger image (that will be displayed in the list of awards). When you create
		the award, just type the name of the image (award.jpg) and not the full path.<br /><br />
	
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=awards">
		<table cellpadding="0" cellspacing="3">
			<tr>
				<td>
					<span class="fontNormal"><b>Order</b></span><br />
					<input type="text" class="order" name="awardOrder" maxlength="3" />
				</td>
				<td>
					<span class="fontNormal"><b>Award</b></span><br />
					<input type="text" class="name" name="awardName" maxlength="100" />
				</td>
				<td rowspan="2" valign="top" align="center" width="55%">
					<span class="fontNormal"><b>Description</b></span><br />
					<textarea name="awardDesc" class="desc" rows="4"></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<span class="fontNormal"><b>Image</b></span><br />
					<span class="fontSmall">images/awards/</span><input type="text" class="image" name="awardImage" maxlength="50" />
				</td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td align="center">
					<input type="image" src="<?=path_userskin;?>buttons/create.png" class="button" name="action_create" value="Create" />
				</td>
			</tr>
		</table>
		</form>
		<br /><br />
		
		<span class="fontTitle">Manage Existing Awards</span>
	
		<table cellpadding="0" cellspacing="3">
			<?
			
			/* pull the ranks from the database */
			$getAwards = "SELECT * FROM sms_awards ORDER BY awardOrder ASC";
			$getAwardsResult = mysql_query( $getAwards );
			
			/* loop through the results and fill the form */
			while( $awardFetch = mysql_fetch_assoc( $getAwardsResult ) ) {
				extract( $awardFetch, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=awards">
			<tr>
				<td>
					<span class="fontNormal"><b>Order</b></span><br />
					<input type="text" class="order" name="awardOrder" maxlength="3" value="<?=$awardOrder;?>" />
				</td>
				<td>
					<span class="fontNormal"><b>Award</b></span><br />
					<input type="text" class="name" name="awardName" maxlength="100" value="<?=stripslashes( $awardName );?>" />
				</td>
				<td rowspan="2" valign="top" align="center" width="55%">
					<span class="fontNormal"><b>Description</b></span><br />
					<textarea name="awardDesc" class="desc" rows="4"><?=stripslashes( $awardDesc );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<span class="fontNormal"><b>Image</b></span><br />
					<span class="fontSmall">images/awards/</span><input type="text" class="image" name="awardImage" maxlength="50" value="<?=$awardImage;?>" />
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top">
					<img src="<?=$webLocation;?>images/awards/large/<?=$awardImage;?>" alt="<?=$awardName;?>" border="0" />
				</td>
				<td align="center" valign="top">
					<input type="hidden" name="awardid" value="<?=$awardid;?>" />
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this award?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" />
				</td>
			</tr>
			<tr>
				<td colspan="4" height="30">&nbsp;</td>
			</tr>
			</form>
			<? } /* close the award while loop */ ?>
		</table>
	</div>

<? } else { errorMessage( "award management" ); } ?>