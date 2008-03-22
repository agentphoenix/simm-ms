<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/posts.php
Purpose: Page that moderates the mission posts

System Version: 2.6.0
Last Modified: 2008-03-22 1806 EST
**/

/* access check */
if( in_array( "m_posts", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	
	if(isset($_GET['id']))
	{
		if(is_numeric($_GET['id'])) {
			$id = $_GET['id'];
		} else {
			errorMessageIllegal( "post moderation page" );
			exit();
		}
	}
	
	if(isset($_GET['remove']))
	{
		if(is_numeric($_GET['remove'])) {
			$remove = $_GET['remove'];
		} else {
			errorMessageIllegal( "post moderation page" );
			exit();
		}
	}
	
	if(isset($_GET['delete']))
	{
		if(is_numeric($_GET['delete'])) {
			$delete = $_GET['delete'];
		} else {
			errorMessageIllegal( "post moderation page" );
			exit();
		}
	}
	
	if(isset($_GET['add']))
	{
		if(is_numeric($_GET['add'])) {
			$add = $_GET['add'];
		} else {
			errorMessageIllegal( "post moderation page" );
			exit();
		}
	}
	
	if(isset($_POST['authorCount'])) {
		$count = $_POST['authorCount'];
	} else {
		$count = FALSE;
	}
	
	$authors_array = array();
	
	for($i = 0; $i < 8; $i++)
	{
		if(isset($_POST['postAuthor' . $i])) {
			$authors_array[] = $_POST['postAuthor' . $i];
		}
	}
	
	if(count($authors_array) > 0) {
		$postAuthor = implode(',', $authors_array);
	} else {
		$postAuthor = FALSE;
	}
	
	if( isset( $_POST['action_update_x'] ) ) {
		
		if(isset($_POST['postid']) && is_numeric($_POST['postid'])) {
			$postid = $_POST['postid'];
		} else {
			$postid = FALSE;
		}
		
		$update = "UPDATE sms_posts SET postTitle = %s, postLocation = %s, postTimeline = %s, ";
		$update.= "postAuthor = %s, postContent = %s, postStatus = %s, postMission = %d ";
		$update.= "WHERE postid = $postid LIMIT 1";
		
		$query = sprintf(
			$update,
			escape_string($_POST['postTitle']),
			escape_string($_POST['postLocation']),
			escape_string($_POST['postTimeline']),
			escape_string($postAuthor),
			escape_string($_POST['postContent']),
			escape_string($_POST['postStatus']),
			escape_string($_POST['postMission'])
		);
		
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "update";
	
	} elseif( isset( $_POST['action_delete_x'] ) ) {
		
		/* do the delete query */
		$query = "DELETE FROM sms_posts WHERE postid = '$postid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "delete";
		
	} elseif( isset( $remove ) ) {
		
		/* do the delete query */
		$query = "DELETE FROM sms_posts WHERE postid = '$remove' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "delete";
	
	} elseif( isset( $delete ) ) {
		
		/* define the vars */
		if(isset($_GET))
		{
			if(is_numeric($_GET['postid'])) {
				$postid = $_GET['postid'];
			} else {
				$postid = FALSE;
			}
			
			if(is_numeric($_GET['delete'])) {
				$arrayid = $_GET['delete'];
			} else {
				$arrayid = FALSE;
			}
		}
		
		/* pull the authors for the specific post */
		$getAuthors = "SELECT postAuthor FROM sms_posts WHERE postid = '$postid' LIMIT 1";
		$getAuthorsResult = mysql_query( $getAuthors );
		
		while( $authorAdjust = mysql_fetch_assoc( $getAuthorsResult ) ) {
			extract( $authorAdjust, EXTR_OVERWRITE );
		}
		
		/* create the new array */
		$authorArray = explode( ",", $postAuthor );
		unset( $authorArray[$arrayid] );
		$authorArray = array_values( $authorArray );
		$newAuthors = implode( ",", $authorArray );
		
		/* update the post */
		$query = "UPDATE sms_posts SET postAuthor = '$newAuthors' WHERE postid = '$postid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "remove";
		
	} elseif( isset( $add ) ) {
		
		/* define the vars */
		$postid = $_GET['postid'];
		$arrayid = $_GET['add'];
		
		/* pull the authors for the specific post */
		$getAuthors = "SELECT postAuthor FROM sms_posts WHERE postid = '$postid' LIMIT 1";
		$getAuthorsResult = mysql_query( $getAuthors );
		
		while( $authorAdjust = mysql_fetch_assoc( $getAuthorsResult ) ) {
			extract( $authorAdjust, EXTR_OVERWRITE );
		}
		
		/* create the new array */
		$authorArray = explode( ",", $postAuthor );
		$authorArray[] = 0;
		$newAuthors = implode( ",", $authorArray );
		
		/* update the post */
		$query = "UPDATE sms_posts SET postAuthor = '$newAuthors' WHERE postid = '$postid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "add";
		
	}
	
	/* if there's an id in the URL, proceed */
	if( isset( $id ) ) {

?>

	<div class="body">
	
		<?
		
		/* do logic to make sure the object is right */
		if( isset( $add ) || isset( $delete ) ) {
			$object = "author";
		} elseif( isset( $_POST['action_update_x'] ) || isset( $_POST['action_delete_x'] ) || isset( $remove ) ) {
			$object = "post";
		}
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( $object, $action );
			$check->display();
		}
		
		?>
	
		<span class="fontTitle">Manage Mission Post</span><br /><br />
		
		<table cellpadding="0" cellspacing="3">
		<?
		
			$posts = "SELECT * FROM sms_posts WHERE postid = '$id' LIMIT 1";
			$postsResult = mysql_query( $posts );
			
			while( $postFetch = mysql_fetch_assoc( $postsResult ) ) {
				extract( $postFetch, EXTR_OVERWRITE );
		
		?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=posts&id=<?=$id;?>">
			<tr>
				<td>
					<b>Post Title</b><br />
					<input type="text" class="name" maxlength="100" name="postTitle" value="<?=stripslashes( $postTitle );?>" />
				</td>
				<td>
					<b>Location</b><br />
					<input type="text" class="name" maxlength="100" name="postLocation" value="<?=stripslashes( $postLocation );?>" />
				</td>
			</tr>
			<tr>
				<td>
					<b>Tag</b><br />
					<input type="text" class="name" maxlength="100" name="postTag" value="<?=stripslashes( $postTag );?>" />
				</td>
				<td>
					<b>Timeline</b><br />
					<input type="text" class="name" maxlength="100" name="postTimeline" value="<?=stripslashes( $postTimeline );?>" />
				</td>
			</tr>
			<tr>
				<td valign="top" rowspan="2">
					<b>Author</b><br />
					<? $authorCount = print_active_crew_select_menu( "post", $postAuthor, $postid, "manage", "posts" ); ?>
					<input type="hidden" name="authorCount" value="<?=$authorCount;?>" />
				</td>
				<td>
					<b>Status</b><br />
					<select name="postStatus">
						<option value="pending"<? if( $postStatus == "pending" ) { echo " selected"; } ?>>Pending</option>
						<option value="saved"<? if( $postStatus == "saved" ) { echo " selected"; } ?>>Saved</option>
						<option value="activated"<? if( $postStatus == "activated" ) { echo " selected"; } ?>>Activated</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b>Mission</b><br />
					<select name="postMission">
					<?
	
					$getMissions = "SELECT * FROM sms_missions WHERE missionStatus != 'upcoming' ORDER BY missionOrder DESC";
					$getMissionsResult = mysql_query( $getMissions );
	
					while( $misFetch = mysql_fetch_assoc( $getMissionsResult ) ) {
						extract( $misFetch, EXTR_OVERWRITE );
	
					?>
	
						<option value="<?=$missionid;?>"<? if( $postMission == $missionid ) { echo " selected"; } ?>><? printText( $missionTitle ); ?></option>
	
					<? } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<b>Content</b><br />
					<textarea rows="15" name="postContent" class="wideTextArea"><?=stripslashes( $postContent );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="15"></td>
			</tr>
			<tr>
				<td colspan="2" valign="top" align="right">
					<input type="hidden" name="postid" value="<?=$postid;?>" />
	
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this mission post?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
	
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update" class="button" value="Update" />
				</td>
			</tr>
			</form>
		<? } ?>
		</table>
	</div>
	
	<? } else { /* if there's no id, continue */ ?>
	
	<div class="body">
	
		<?
		
		/* do logic to make sure the object is right */
		if( isset( $add ) || isset( $delete ) ) {
			$object = "author";
		} elseif( isset( $_POST['action_update_x'] ) || isset( $_POST['action_delete_x'] ) || isset( $remove ) ) {
			$object = "post";
		}
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( $object, $action );
			$check->display();
		}
		
		?>
	
		<span class="fontTitle">Manage Mission Posts</span><br /><br />
		
		<table cellpadding="0" cellspacing="3">
		<?
		
			$posts = "SELECT * FROM sms_posts ORDER BY postPosted DESC LIMIT 5";
			$postsResult = mysql_query( $posts );
			
			while( $postFetch = mysql_fetch_assoc( $postsResult ) ) {
				extract( $postFetch, EXTR_OVERWRITE );
		
		?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=posts">
			<tr>
				<td>
					<b class="fontNormal">Post Title</b><br />
					<input type="text" class="name" maxlength="100" name="postTitle" value="<?=stripslashes( $postTitle );?>" />
				</td>
				<td rowspan="6" align="center" valign="top" width="55%">
					<span class="fontNormal"><b>Content</b></span><br />
					<textarea rows="17" name="postContent" class="desc"><?=stripslashes( $postContent );?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<b class="fontNormal">Location</b><br />
					<input type="text" class="name" maxlength="100" name="postLocation" value="<?=stripslashes( $postLocation );?>" />
				</td>
			</tr>
			<tr>
				<td>
					<b class="fontNormal">Timeline</b><br />
					<input type="text" class="name" maxlength="100" name="postTimeline" value="<?=stripslashes( $postTimeline );?>" />
				</td>
			</tr>
			<tr>
				<td>
					<b class="fontNormal">Tag</b><br />
					<input type="text" class="name" maxlength="100" name="postTag" value="<?=stripslashes( $postTag );?>" />
				</td>
			</tr>
			<tr>
				<td>
					<b class="fontNormal">Author</b><br />
					<? $authorCount = print_active_crew_select_menu( "post", $postAuthor, $postid, "manage", "posts" ); ?>
					<input type="hidden" name="authorCount" value="<?=$authorCount;?>" />
				</td>
			</tr>
			<tr>
				<td>
					<b class="fontNormal">Status</b><br />
					<select name="postStatus">
						<option value="pending"<? if( $postStatus == "pending" ) { echo " selected"; } ?>>Pending</option>
						<option value="saved"<? if( $postStatus == "saved" ) { echo " selected"; } ?>>Saved</option>
						<option value="activated"<? if( $postStatus == "activated" ) { echo " selected"; } ?>>Activated</option>
					</select>
				</td>
			</tr>
			<tr>
				<td></td>
				<td valign="top" align="center">
					<input type="hidden" name="postid" value="<?=$postid;?>" />
					<input type="hidden" name="postMission" value="<?=$postMission;?>" />
	
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this mission post?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
	
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update" class="button" value="Update" />
				</td>
			</tr>
			<tr>
				<td colspan="2" height="25"></td>
			</tr>
			</form>
		<? } ?>
		</table>
	</div>

<? } } else { errorMessage( "post moderation" ); } ?>