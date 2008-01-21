<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/tour.php
Purpose: Page that moderates the ship tour pages

System Version: 2.6.0
Last Modified: 2008-01-21 1139 EST
**/

/* access check */
if( in_array( "m_tour", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$update = $_POST['action_update_x'];
	$delete = $_POST['action_delete_x'];
	$create = $_POST['action_create_x'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['entry'] ) && preg_match( "/^\d+$/", $_GET['entry'], $matches ) == 0 ) {
		errorMessageIllegal( "tour management page" );
		exit();
	} else {
		/* set the GET variable */
		$entry = $_GET['entry'];
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['delete'] ) && preg_match( "/^\d+$/", $_GET['delete'], $matches ) == 0 ) {
		errorMessageIllegal( "tour management page" );
		exit();
	} else {
		/* set the GET variable */
		$remove = $_GET['delete'];
	}
	
	/* define the POST variables */
	$tourid = $_POST['tourid'];
	$tourName = addslashes( $_POST['tourName'] );
	$tourLocation = addslashes( $_POST['tourLocation'] );
	$tourOrder = $_POST['tourOrder'];
	$tourDisplay = $_POST['tourDisplay'];
	$tourPicture1 = $_POST['tourPicture1'];
	$tourPicture2 = $_POST['tourPicture2'];
	$tourPicture3 = $_POST['tourPicture3'];
	$tourDesc = addslashes( $_POST['tourDesc'] );
	$tourSummary = addslashes( $_POST['tourSummary'] );
	
	/* if the POST action is update */
	if( $update ) {
		
		/* do the update query */
		$query = "UPDATE sms_tour SET ";
		$query.= "tourName = '$tourName', tourOrder = '$tourOrder', ";
		$query.= "tourLocation = '$tourLocation', tourDisplay = '$tourDisplay', ";
		$query.= "tourPicture1 = '$tourPicture1', tourPicture2 = '$tourPicture2', ";
		$query.= "tourPicture3 = '$tourPicture3', tourDesc = '$tourDesc', ";
		$query.= "tourSummary = '$tourSummary' WHERE tourid = '$tourid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_tour" );
		
		$action = "update";
	
	/* if the POST action is create */
	} elseif( $create ) {
		
		/* do the create query */
		$query = "INSERT INTO sms_tour ( tourid, tourName, tourLocation, tourDisplay, tourOrder, tourDesc, tourPicture1, tourPicture2, tourPicture3, tourSummary ) ";
		$query.= "VALUES ( '', '$tourName', '$tourLocation', '$tourDisplay', '$tourOrder', '$tourDesc', '$tourPicture1', '$tourPicture2', '$tourPicture3', '$tourSummary' )";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_tour" );
		
		$action = "create";
	
	/* if the POST action is delete */
	} elseif( $delete || isset( $remove ) ) {

		if( $delete ) {
			/* do the delete query */
			$query = "DELETE FROM sms_tour WHERE tourid = '$tourid' LIMIT 1";
			$result = mysql_query( $query );
		} elseif( $remove ) {
			/* do the delete query */
			$query = "DELETE FROM sms_tour WHERE tourid = '$remove' LIMIT 1";
			$result = mysql_query( $query );
		}
		
		/* optimize the table */
		optimizeSQLTable( "sms_tour" );
		
		$action = "delete";
	
	}

	/* strip the slashes */
	$tourName = stripslashes( $tourName );
	$tourLocation = stripslashes( $tourLocation );
	$tourDesc = stripslashes( $tourDesc );
	$tourSummary = stripslashes( $tourSummary );

?>
	
	<script type="text/javascript">
		$(document).ready(function() {
			$(".zebra tr").mouseover(function() {
				$(this).addClass("over");
			})
			.mouseout(function() {
				$(this).removeClass("over");
			});
			$(".zebra tr:even").addClass("alt");
		});
	</script>
	
	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "tour item", $action );
			$check->display();
		}
		
		if( !$entry ) {
		
		?>
		<span class="fontTitle">Create New Tour Item</span><br /><br />
		The tour feature is designed to allow COs to give their players and visitors a visual picture
		of some of the major locations on the ship.  To that end, you can specify not only a name
		and location, but a description as well as show up to three images of the location if you'd like.
		If you want to include more images than three, you'll need to reference them in the description.
		In addition, images for your tour must be stored on your server and be put in the <i>images/tour</i>
		directory.  Simply specify the name of the image and its extension and SMS will take care of the rest!
		<br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=tour">
		<table cellpadding="0" cellspacing="3">
			<tr>
				<td>
					<b class="fontNormal">Order</b><br />
					<input type="text" class="order" name="tourOrder" maxlength="3" value="99" />
				</td>
				<td>
					<b class="fontNormal">Display?</b><br />
					<select name="tourDisplay">
						<option value="y">Yes</option>
						<option value="n">No</option>
					</select>
				</td>
				<td rowspan="5" valign="top" align="center" width="5">&nbsp;</td>
				<td rowspan="5" valign="top" align="center" width="70%">
					<b class="fontNormal">Description</b><br />
					<textarea name="tourDesc" rows="12" class="desc"></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<b class="fontNormal">Name</b><br />
					<input type="text" class="name" name="tourName" maxlength="100" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<b class="fontNormal">Location</b><br />
					<input type="text" class="name" name="tourLocation" maxlength="100" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<b class="fontNormal">Image #1</b><br />
					<span class="fontSmall">images/tour/</span><input type="text" class="image" name="tourPicture1" maxlength="255" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<b class="fontNormal">Image #2</b><br />
					<span class="fontSmall">images/tour/</span><input type="text" class="image" name="tourPicture2" maxlength="255" />
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top">
					<b class="fontNormal">Image #3</b><br />
					<span class="fontSmall">images/tour/</span><input type="text" class="image" name="tourPicture3" maxlength="255" />
				</td>
				<td align="center">&nbsp;</td>
				<td align="center" width="70%">
					<b class="fontNormal">Summary</b><br />
					<textarea rows="3" name="tourSummary" class="desc"></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td align="center">&nbsp;</td>
				<td align="center">
					<input type="image" src="<?=path_userskin;?>buttons/create.png" class="button" name="action_create" value="Create" />
				</td>
			</tr>
		</table>
		</form>
		<br /><br />
		
		<span class="fontTitle">Manage Existing Tour Items</span><br /><br />
			<table class="zebra" cellpadding="3" cellspacing="0">
	
			<?
			
			/* pull the ranks from the database */
			$getTour = "SELECT * FROM sms_tour ORDER BY tourOrder ASC";
			$getTourResult = mysql_query( $getTour );
			
			/* loop through the results and fill the form */
			while( $tourFetch = mysql_fetch_assoc( $getTourResult ) ) {
				extract( $tourFetch, EXTR_OVERWRITE );
	
			?>
			
				<tr>
					<td><? printText( $tourName ); ?></td>
					<td align="center" width="10%"><b><a href="<?=$webLocation;?>index.php?page=tour&id=<?=$tourid;?>">View</a></b></td>
					<td align="center" width="10%"><b><a href="<?=$webLocation;?>admin.php?page=manage&sub=tour&entry=<?=$tourid;?>" class="edit" >Edit</a></b></td>
					<td align="center" width="10%">
						<script type="text/javascript">
							document.write( "<b><a href=\"<?=$webLocation;?>admin.php?page=manage&sub=tour&delete=<?=$tourid;?>\" class=\"delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this tour item?')\">Delete</a></b>" );
						</script>
						<noscript>
							<b><a href="<?=$webLocation;?>admin.php?page=manage&sub=tour&delete=<?=$tourid;?>" class="delete">Delete</a></b>
						</noscript>
					</td>
				</tr>
		
			<? } ?>
	
			</table>
	
		<?
	
		} else {
	
			/* pull the ranks from the database */
			$getTour = "SELECT * FROM sms_tour WHERE tourid = '$entry' ORDER BY tourOrder ASC";
			$getTourResult = mysql_query( $getTour );
	
			/* loop through the results and fill the form */
			while( $tourFetch = mysql_fetch_assoc( $getTourResult ) ) {
				extract( $tourFetch, EXTR_OVERWRITE );
			}
	
		?>
	
		<span class="fontTitle">Manage Tour Item: <? printText( $tourName ); ?></span><br /><br />
		<b class="fontMedium">
			<a href="<?=$webLocation;?>admin.php?page=manage&sub=tour">&laquo; Back to Tour Management</a><br /><br />
		</b>
		
		<table cellpadding="0" cellspacing="3">
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=tour&entry=<?=$entry;?>">
			<tr>
				<td colspan="2">
					<b class="fontNormal">Name</b><br />
					<input type="text" class="name" name="tourName" maxlength="100" value="<?=stripslashes( $tourName );?>" />
				</td>
				<td>
					<b class="fontNormal">Image #1</b><br />
					<span class="fontSmall">images/tour/</span>
					<input type="text" class="image" name="tourPicture1" maxlength="255" value="<?=stripslashes( $tourPicture1 );?>" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<b class="fontNormal">Location</b><br />
					<input type="text" class="name" name="tourLocation" maxlength="100" value="<?=stripslashes( $tourLocation );?>" />
				</td>
				<td>
					<b class="fontNormal">Image #2</b><br />
					<span class="fontSmall">images/tour/</span>
					<input type="text" class="image" name="tourPicture2" maxlength="255" value="<?=stripslashes( $tourPicture2 );?>" />
				</td>
			</tr>
			<tr>
				<td>
					<span class="fontNormal"><b>Display?</b></span><br />
					<select name="tourDisplay">
						<option value="y" <? if( $tourDisplay == "y" ) { echo "selected"; } ?>>Yes</option>
						<option value="n" <? if( $tourDisplay == "n" ) { echo "selected"; } ?>>No</option>
					</select>
				</td>
				<td>
					<b class="fontNormal">Order</b><br />
					<input type="text" class="order" name="tourOrder" maxlength="3" value="<?=$tourOrder;?>" />
				</td>
				<td>
					<b class="fontNormal">Image #3</b><br />
					<span class="fontSmall">images/tour/</span>
					<input type="text" class="image" name="tourPicture3" maxlength="255" value="<?=stripslashes( $tourPicture3 );?>" />
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<b class="fontNormal">Summary</b><br />
					<textarea name="tourSummary" class="wideTextArea" rows="3"><?=stripslashes( $tourSummary );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<b class="fontNormal">Description</b><br />
					<textarea name="tourDesc" class="wideTextArea" rows="15"><?=stripslashes( $tourDesc );?></textarea>
				</td>
			</tr>
			<tr>
				<td align="right" colspan="3">
					<input type="hidden" name="tourid" value="<?=$entry;?>" />
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this tour item?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
					&nbsp; &nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" />
				</td>
			</tr>
			</form>
		</table>
		<? } ?>
		
	</div>

<? } else { errorMessage( "tour management" ); } ?>