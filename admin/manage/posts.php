<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/posts.php
Purpose: Page that moderates the mission posts

System Version: 2.5.4
Last Modified: 2007-09-17 0913 EST
**/

/* access check */
if( in_array( "m_posts", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$actionUpdate = $_POST['action_update_x'];
	$actionDelete = $_POST['action_delete_x'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( "post moderation page" );
		exit();
	} else {
		/* set the GET variable */
		$id = $_GET['id'];
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['remove'] ) && preg_match( "/^\d+$/", $_GET['remove'], $matches ) == 0 ) {
		errorMessageIllegal( "post moderation page" );
		exit();
	} else {
		/* set the GET variable */
		$remove = $_GET['remove'];
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['delete'] ) && preg_match( "/^\d+$/", $_GET['delete'], $matches ) == 0 ) {
		errorMessageIllegal( "post moderation page" );
		exit();
	} else {
		/* set the GET variable */
		$delete = $_GET['delete'];
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['add'] ) && preg_match( "/^\d+$/", $_GET['add'], $matches ) == 0 ) {
		errorMessageIllegal( "post moderation page" );
		exit();
	} else {
		/* set the GET variable */
		$add = $_GET['add'];
	}

	$count = $_POST['authorCount'];
	
	$author1 = $_POST['postAuthor0'];
	$author2 = $_POST['postAuthor1'];
	$author3 = $_POST['postAuthor2'];
	$author4 = $_POST['postAuthor3'];
	$author5 = $_POST['postAuthor4'];
	$author6 = $_POST['postAuthor5'];
	$author7 = $_POST['postAuthor6'];
	$author8 = $_POST['postAuthor7'];
	
	if( $count == "1" ) {
		$postAuthor = $author1;
	} elseif( $count == "2" ) {
		$postAuthor = $author1 . "," . $author2;
	} elseif( $count == "3" ) {
		$postAuthor = $author1 . "," . $author2 . "," . $author3;
	} elseif( $count == "4" ) {
		$postAuthor = $author1 . "," . $author2 . "," . $author3  . "," . $author4;
	} elseif( $count == "5" ) {
		$postAuthor = $author1 . "," . $author2 . "," . $author3  . "," . $author4  . "," . $author5;
	} elseif( $count == "6" ) {
		$postAuthor = $author1 . "," . $author2 . "," . $author3  . "," . $author4  . "," . $author6  . "," . $author6;
	} elseif( $count == "7" ) {
		$postAuthor = $author1 . "," . $author2 . "," . $author3  . "," . $author4  . "," . $author6  . "," . $author6 . "," . $author7;
	} elseif( $count == "8" ) {
		$postAuthor = $author1 . "," . $author2 . "," . $author3  . "," . $author4  . "," . $author6  . "," . $author6 . "," . $author7 . "," . $author8;
	}
	
	$postid = $_POST['postid'];
	$postTitle = addslashes( $_POST['postTitle'] );
	$postLocation = addslashes( $_POST['postLocation'] );
	$postTimeline = addslashes( $_POST['postTimeline'] );
	$postContent = addslashes( $_POST['postContent'] );
	$postStatus = $_POST['postStatus'];
	$postMission = $_POST['postMission'];
	
	if( $actionUpdate ) {
		
		if( $id ) {
			$query = "UPDATE sms_posts SET postTitle = '$postTitle', postLocation = '$postLocation', ";
			$query.= "postTimeline = '$postTimeline', postAuthor = '$postAuthor', postContent = '$postContent', ";
			$query.= "postStatus = '$postStatus', postMission = '$postMission' WHERE postid = '$postid' LIMIT 1";
			$result = mysql_query( $query );
		} else {
			$query = "UPDATE sms_posts SET postTitle = '$postTitle', postLocation = '$postLocation', ";
			$query.= "postTimeline = '$postTimeline', postAuthor = '$postAuthor', postContent = '$postContent', ";
			$query.= "postStatus = '$postStatus' WHERE postid = '$postid' LIMIT 1";
			$result = mysql_query( $query );
		}
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "update";
	
	} elseif( $actionDelete ) {
		
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
	
	} elseif( $delete ) {
		
		/* define the vars */
		$postid = $_GET['postid'];
		$arrayid = $_GET['delete'];
		
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
		
	} elseif( $add ) {
		
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
	if( $id ) {

?>

	<div class="body">
	
		<?
		
		/* do logic to make sure the object is right */
		if( isset( $add ) || isset( $delete ) ) {
			$object = "author";
		} elseif( isset( $actionUpdate ) || isset( $actionDelete ) || isset( $remove ) ) {
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
		} elseif( isset( $actionUpdate ) || isset( $actionDelete ) || isset( $remove ) ) {
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