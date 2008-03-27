<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/post/mission.php
Purpose: Page to post a mission entry

System Version: 2.6.0
Last Modified: 2008-03-27 1818 EST
**/

/* access check */
if( in_array( "p_mission", $sessionAccess ) ) {
	
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "post";
	$actionPost = $_POST['action_post_x'];
	$actionSave = $_POST['action_save_x'];
	$actionDelete = $_POST['action_delete_x'];
	$id = $_GET['id'];
	
	/* check to make sure the id is legit */
	if( isset( $id ) && !is_numeric( $id ) ) {
		errorMessageIllegal( "post mission entry page" );
		exit();
	}
	
	if( $actionPost ) {
		
		/* add the necessary slashes */
		$postTitle = addslashes( $_POST['postTitle'] );
		$postLocation = addslashes( $_POST['postLocation'] );
		$postTimeline = addslashes( $_POST['postTimeline'] );
		$postContent = addslashes( $_POST['postContent'] );
		$postTag = addslashes( $_POST['postTag'] );
	
		/** check to see if the user is moderated **/
		$getModerated = "SELECT crewid FROM sms_crew WHERE moderatePosts = 'y'";
		$getModeratedResult = mysql_query( $getModerated );
	
		while( $moderated = mysql_fetch_array( $getModeratedResult ) ) {
			extract( $moderated, EXTR_OVERWRITE );
	
			$modArray[] = $moderated['0'];
	
		}
		/** end moderation check **/
		
		if( count( $modArray ) > "0" && in_array( $sessionCrewid, $modArray ) ) {
			$postStatus = "pending";
		} elseif( ( $sessionCrewid == "" ) || ( $sessionCrewid == "0" ) ) {
			$postStatus = "pending";
		} elseif( $sessionCrewid > "0" ) {
			$postStatus = "activated";
		} if( $_POST['postMission'] == "" ) {
			$postStatus = "pending";
		}
	
		if( !$id ) {
			$query = "INSERT INTO sms_posts ( postid, postAuthor, postTitle, postLocation, postTimeline, postContent, postPosted, postMission, postStatus, postTag ) ";
			$query.= "VALUES ( '', '$sessionCrewid', '$postTitle', '$postLocation', '$postTimeline', '$postContent', UNIX_TIMESTAMP(), '$_POST[postMission]', '$postStatus', '$postTag' )";
		} else {
			$query = "UPDATE sms_posts SET postTitle = '$postTitle', postLocation = '$postLocation', ";
			$query.= "postTimeline = '$postTimeline', postContent = '$postContent', ";
			$query.= "postStatus = '$postStatus', postTag = '$postTag',  ";
			$query.= "postPosted = UNIX_TIMESTAMP() WHERE postid = '$id' LIMIT 1";
		}
	
		$result = mysql_query( $query );
		
		/* strip the slashes added for the query */
		$postTitle = stripslashes( $_POST['postTitle'] );
		$postLocation = stripslashes( $_POST['postLocation'] );
		$postTimeline = stripslashes( $_POST['postTimeline'] );
		$postContent = stripslashes( $_POST['postContent'] );
		$postTag = stripslashes( $_POST['postTag'] );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "post";
		
		/* update the player's last post time stamp */
		$updateTimestamp = "UPDATE sms_crew SET lastPost = UNIX_TIMESTAMP() WHERE crewid = '$sessionCrewid' LIMIT 1";
		$updateTimestampResult = mysql_query( $updateTimestamp );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
		
		/** EMAIL THE POST **/
		
		/* set the email author */
		$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankName ";
		$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$userFetch.= "WHERE crew.crewid = '$sessionCrewid' AND crew.rankid = rank.rankid LIMIT 1";
		$userFetchResult = mysql_query( $userFetch );
		
		while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
			extract( $userFetchArray, EXTR_OVERWRITE );
		}
		
		$firstName = str_replace( "'", "", $firstName );
		$lastName = str_replace( "'", "", $lastName );
		
		$from = $rankName . " " . $firstName . " " . $lastName . " < " . $email . " >";
		
		/* if the post has an activated status */
		if( $postStatus == "activated" ) {
		
			/* define the variables */
			$to = getCrewEmails( "emailPosts" );
			$subject = $emailSubject . " " . printMissionTitle( $_POST['postMission'] ) . " - " . $postTitle;
			$message = "A Post By " . printCrewNameEmail( $sessionCrewid ) . "
Location: " . $postLocation . "
Timeline: " . $postTimeline . "
Tag: " . $postTag . "

" . $postContent . "";
			
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		} elseif( $postStatus == "pending" ) {
		
			/* define the variables */
			$to = printCOEmail();
			$subject = $emailSubject . " " . printMissionTitle( $_POST['postMission'] ) . " - " . $postTitle . " (Awaiting Approval)";
			$message = "A Post By " . printCrewNameEmail( $sessionCrewid ) . "
Location: " . $postLocation . "
Timeline: " . $postTimeline . "
Tag: " . $postTag . "

" . $postContent . "

Please log in to approve this post.  " . $webLocation . "login.php?action=login";
			
			/* send the nomination email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		}
			
	} if( $actionSave ) {
	
		/* add the necessary slashes */
		$postTitle = addslashes( $_POST['postTitle'] );
		$postLocation = addslashes( $_POST['postLocation'] );
		$postTimeline = addslashes( $_POST['postTimeline'] );
		$postContent = addslashes( $_POST['postContent'] );
		$postTag = addslashes( $_POST['postTag'] );
	
		if( !$id ) {
			$query = "INSERT INTO sms_posts ( postid, postAuthor, postTitle, postLocation, postTimeline, postContent, postPosted, postMission, postStatus, postTag ) ";
			$query.= "VALUES ( '', '$sessionCrewid', '$postTitle', '$postLocation', '$postTimeline', '$postContent', UNIX_TIMESTAMP(), '$_POST[postMission]', 'saved', '$postTag' )";
		} else {
			$query = "UPDATE sms_posts SET postTitle = '$postTitle', postLocation = '$postLocation', ";
			$query.= "postTimeline = '$postTimeline', postContent = '$postContent', ";
			$query.= "postStatus = 'saved', postTag = '$postTag', ";
			$query.= "postPosted = UNIX_TIMESTAMP() WHERE postid = '$id' LIMIT 1";
		}
		
		$result = mysql_query( $query );
		
		/* strip the slashes added for the query */
		$postTitle = stripslashes( $_POST['postTitle'] );
		$postLocation = stripslashes( $_POST['postLocation'] );
		$postTimeline = stripslashes( $_POST['postTimeline'] );
		$postContent = stripslashes( $_POST['postContent'] );
		$postTag = stripslashes( $_POST['postTag'] );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "save";
	
	} if( $actionDelete ) {
	
		/* delete the entry */
		$query = "DELETE FROM sms_posts WHERE postid = '$id' LIMIT 1";
		$result = mysql_query( $query );
	
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "delete";
	
	}
	
?>
	
	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "mission entry", $action );
			$check->display();
		}
		
		?>
	
		<? if( $useMissionNotes == "y" && $action != "Delete" ) { ?>
			
		<script type="text/javascript">
			$(document).ready(function() {
				$('a#toggle').click(function() {
					$('#notes').slideToggle('slow');
					return false;
				});
			});
		</script>

		<div class="update notify-normal">
			<a href="#" id="toggle" class="fontNormal" style="float:right;margin-right:.5em;">Show/Hide</a>
			
			<span class="fontTitle">Mission Notes</span>
			<div id="notes" style="display:none;">
				<br />
				<?

				$getNotes = "SELECT missionNotes FROM sms_missions WHERE missionStatus = 'current' LIMIT 1";
				$getNotesResult = mysql_query( $getNotes );
				$notes = mysql_fetch_array( $getNotesResult );

				printText( $notes['0'] );

				?>
			</div>
		</div><br />
		<? } ?>
	
		<span class="fontTitle">Post Mission Entry</span><br /><br />
	
		<? if( !$id ) { ?>
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=mission">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Mission</td>
				<td>&nbsp;</td>
				<td class="fontNormal">
					<?
					
					$missionTitle = "SELECT missionid, missionTitle FROM sms_missions WHERE missionStatus = 'current' LIMIT 1";
					$missionTitleResult = mysql_query( $missionTitle );
					$missionCount = mysql_num_rows( $missionTitleResult );
					
					while( $titleArray = mysql_fetch_array( $missionTitleResult ) ) {
						extract( $titleArray, EXTR_OVERWRITE );
					}
					
					if( $missionCount == 0 ) {
						echo "<b>You must <a href='" . $webLocation . "admin.php?page=manage&sub=missions'>create a mission</a> before posting!</b>";
					} else {
					
					?>
					
					<a href="<?=$webLocation;?>index.php?page=mission&id=<?=$missionid;?>"><? printText( $missionTitle ); ?></a>
					<input type="hidden" name="postMission" value="<?=$missionid;?>" />
					<? } ?>
				</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Title</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postTitle" style="font-weight:bold;" length="100" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Location</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postLocation" style="font-weight:bold;" length="100" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Timeline</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postTimeline" style="font-weight:bold;" length="100" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Tag</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postTag" style="font-weight:bold;" length="100" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Content</td>
				<td>&nbsp;</td>
				<td><textarea name="postContent" class="desc" rows="15"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			
			<? if( $missionCount > 0 ) { ?>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" value="Save" class="button" />
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/post.png" name="action_post" value="Post" class="button" />
				</td>
			</tr>
			<? } ?>
		</table>
		</form>
		
		<?
	
		} elseif( $id && !$actionDelete ) {
	
			$getPost = "SELECT * FROM sms_posts WHERE postid = '$id' LIMIT 1";
			$getPostResults = mysql_query( $getPost );
			
			while( $fetchPost = mysql_fetch_array( $getPostResults ) ) {
				extract( $fetchPost, EXTR_OVERWRITE );
			}
	
		?>
	
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=mission&id=<?=$id;?>">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td><? displayAuthors( $postid, "noLink" ); ?></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Mission</td>
				<td>&nbsp;</td>
				<td class="fontNormal">
					<?
					
					$missionTitle = "SELECT missionid, missionTitle FROM sms_missions WHERE missionStatus = 'current' LIMIT 1";
					$missionTitleResult = mysql_query( $missionTitle );
					$missionCount = mysql_num_rows( $missionTitleResult );
					
					while( $titleArray = mysql_fetch_array( $missionTitleResult ) ) {
						extract( $titleArray, EXTR_OVERWRITE );
					}
					
					if( $missionCount == 0 ) {
						echo "<b>You must <a href='" . $webLocation . "admin.php?page=manage&sub=missions'>create a mission</a> before posting!</b>";
					} else {
					
					?>
					
					<a href="<?=$webLocation;?>index.php?page=mission&id=<?=$missionid;?>"><?=$missionTitle;?></a>
					<input type="hidden" name="postMission" value="<?=$missionid;?>" />
					<? } ?>
				</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Title</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postTitle" style="font-weight:bold;" length="100" value="<?=stripslashes( $postTitle );?>" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Location</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postLocation" style="font-weight:bold;" length="100" value="<?=stripslashes( $postLocation );?>" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Timeline</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postTimeline" style="font-weight:bold;" length="100" value="<?=stripslashes( $postTimeline );?>" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Tag</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postTag" style="font-weight:bold;" length="100" value="<?=stripslashes( $postTag );?>" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Content</td>
				<td>&nbsp;</td>
				<td><textarea name="postContent" class="desc" rows="15"><?=stripslashes( $postContent );?></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			
			<? if( $missionCount > 0 && $sessionCrewid == $postAuthor && $postStatus == "saved" ) { ?>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this saved post?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" value="Save" class="button" />
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/post.png" name="action_post" value="Post" class="button" />
				</td>
			</tr>
			<? } ?>
		</table>
		</form>
		
		<? } elseif( $id && $actionDelete ) { ?>
	
		Please return to the Control Panel to continue.
	
		<? } ?>
		
	</div>

<? } else { errorMessage( "mission posting" ); } ?>