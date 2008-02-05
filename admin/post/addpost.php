<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/manage/addpost.php
Purpose: Page to add a mission post

System Version: 2.5.0
Last Modified: 2007-06-18 1308 EST
**/

/* access check */
if( in_array( "p_addmission", $sessionAccess ) ) {
	
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "post";
	$add = $_POST['action_x'];
	
	if( $add ) {
		
		/* add the necessary slashes */
		$postTitle = addslashes( $_POST['postTitle'] );
		$postLocation = addslashes( $_POST['postLocation'] );
		$postTimeline = addslashes( $_POST['postTimeline'] );
		$postContent = addslashes( $_POST['postContent'] );
		$postTag = addslashes( $_POST['postTag'] );
		$postAuthor = $_POST['postAuthor'];
	
		$postMissionEntry = "INSERT INTO sms_posts ( postid, postAuthor, postTitle, postLocation, postTimeline, postContent, postPosted, postMission, postStatus, postTag ) ";
		$postMissionEntry.= "VALUES ( '', '$postAuthor', '$postTitle', '$postLocation', '$postTimeline', '$postContent', UNIX_TIMESTAMP(), '$_POST[postMission]', 'activated', '$postTag' )";
		$result = mysql_query( $postMissionEntry );
		
		/* strip the slashes added for the query */
		$postTitle = stripslashes( $_POST['postTitle'] );
		$postLocation = stripslashes( $_POST['postLocation'] );
		$postTimeline = stripslashes( $_POST['postTimeline'] );
		$postContent = stripslashes( $_POST['postContent'] );
		$postTag = stripslashes( $_POST['postTag'] );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		/* update the player's last post time stamp */
		$updateTimestamp = "UPDATE sms_crew SET lastPost = UNIX_TIMESTAMP() WHERE crewid = '$postAuthor' LIMIT 1";
		$updateTimestampResult = mysql_query( $updateTimestamp );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
		
		/* if they sendEmail box is checked, send the email */
		if( $_POST['sendEmail'] == "y" ) {
			
			/** EMAIL THE POST **/
			
			/* set the email author */
			$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankName ";
			$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
			$userFetch.= "WHERE crew.crewid = '$postAuthor' AND crew.rankid = rank.rankid LIMIT 1";
			$userFetchResult = mysql_query( $userFetch );
			
			while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
				extract( $userFetchArray, EXTR_OVERWRITE );
			}
			
			$firstName = str_replace( "'", "", $firstName );
			$lastName = str_replace( "'", "", $lastName );
			
			$from = $rankName . " " . $firstName . " " . $lastName . " < " . $email . " >";
			
			/* define the variables */
			$to = getCrewEmails( "emailPosts" );
			$subject = "[" . $shipPrefix . " " . $shipName . "] " . printMissionTitle( $_POST['postMission'] ) . " - " . $postTitle;
			$message = "A Post By " . printCrewNameEmail( $postAuthor ) . "
Location: " . $postLocation . "
Timeline: " . $postTimeline . "
Tag: " . $postTag . "

" . $postContent . "";
				
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		}
			
	}
	
	?>
	
	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $postMissionEntry );
				
		if( !empty( $check->query ) ) {
			$check->message( "mission entry", "add" );
			$check->display();
		}
		
		?>
	
		<span class="fontTitle">Add Mission Entry</span><br /><br />
	
		This page should be used in the event that a member of the crew has accidentally
		posted incorrectly.  For instance, if a player has replied to one of the emails
		sent out to the system instead of logging in and posting, you can copy and paste
		the contents of their email into this form and put the entry into the system. For
		all other mission posts, please use the <a href="<?=$webLocation;?>admin.php?page=post&sub=mission">
		Write Mission Post</a> page.<br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=addpost">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td>
					<select name="postAuthor">
					<?
					
					/* query the users database */
					$sql = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
					$sql.= "FROM sms_crew AS crew, sms_ranks AS rank ";
					$sql.= "WHERE crew.crewType = 'active' AND crew.rankid = rank.rankid ";
					$sql.= "ORDER BY crew.rankid ASC";
					$result = mysql_query( $sql );
					
					/*
						start looping through what the query returns
						until it runs out of records
					*/
					while( $myrow = mysql_fetch_array( $result ) ) {
						extract( $myrow, EXTR_OVERWRITE );
						
						$authorNumber = $author . $authorNum;
						$authorNumber = $rankName . " " . $firstName . " " . $lastName;
						
						echo "<option value='" . $myrow['crewid'] . "'>" . $authorNumber . "</option>";
						
					}
					
					?>
					</select>
				</td>
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
					
					if( $missionCount == "0" ) {
						echo "<b>Please create a mission before posting!</b>";
					} else {
					
						$missions = "SELECT missionid, missionTitle, missionStatus FROM sms_missions WHERE ";
						$missions.= "missionStatus != 'upcoming'";
						$missionsResult = mysql_query( $missions );
						
						echo "<select name='postMission'>";
						
						while( $missionArray = mysql_fetch_array( $missionsResult ) ) {
							extract( $missionArray, EXTR_OVERWRITE );
							
							echo "<option value='" . $missionid . "'";
							if( $missionStatus == "current" ) { 
								echo " selected ";
							}
							echo ">";
							printText( $missionTitle );
							echo "</option>";
							
						}
						
						echo "</select>";
					
					}
					
					?>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
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
				<td class="narrowLabel tableCellLabel">Send Email?</td>
				<td>&nbsp;</td>
				<td><input type="checkbox" name="sendEmail" value="y" checked="checked" /></td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Content</td>
				<td>&nbsp;</td>
				<td><textarea name="postContent" class="desc" rows="15"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			
			<? if( $missionCount > "0" ) { ?>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<input type="image" src="<?=path_userskin;?>buttons/add.png" name="action" value="Add" class="button" />
				</td>
			</tr>
			<? } ?>
		</table>
		</form>
	</div>
<? } else { errorMessage( "add mission entry" ); } ?>