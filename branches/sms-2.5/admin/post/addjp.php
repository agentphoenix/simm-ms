<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/post/addjp.php
Purpose: Page to add a joint post

System Version: 2.5.4
Last Modified: 2007-09-17 0911 EST
**/

/* access check */
if( in_array( "p_addjp", $sessionAccess ) ) {
	
	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "post";
	$action = $_POST['action_x'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( "add JP page" );
		exit();
	} else {
		/* set the GET variable */
		$id = $_GET['id'];
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['number'] ) && preg_match( "/^\d+$/", $_GET['number'], $matches ) == 0 ) {
		errorMessageIllegal( "add JP page" );
		exit();
	} else {
		/* set the GET variable */
		$number = $_GET['number'];
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['delete'] ) && preg_match( "/^\d+$/", $_GET['delete'], $matches ) == 0 ) {
		errorMessageIllegal( "activation page" );
		exit();
	} else {
		/* set the GET variable */
		$delete = $_GET['delete'];
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['add'] ) && preg_match( "/^\d+$/", $_GET['add'], $matches ) == 0 ) {
		errorMessageIllegal( "activation page" );
		exit();
	} else {
		/* set the GET variable */
		$add = $_GET['add'];
	}
	
	if( !$number ) {
		$number = "2";
	} elseif( $number > "8" ) {
		$number = "8";
	}
	
	if( $action ) {
		
		/* add the necessary slashes */
		$postTitle = addslashes( $_POST['postTitle'] );
		$postLocation = addslashes( $_POST['postLocation'] );
		$postTimeline = addslashes( $_POST['postTimeline'] );
		$postContent = addslashes( $_POST['postContent'] );
		$postMission = $_POST['postMission'];
		$postTag = addslashes( $_POST['postTag'] );
		$jpnumber = $_POST['jpNumber'];
		
		if( $jpnumber == "2" ) {
			$postAuthors = $_POST['author1'] . "," . $_POST['author2'];
		} if( $jpnumber == "3" ) {
			$postAuthors = $_POST['author1'] . "," . $_POST['author2']  . "," . $_POST['author3'];
		} if( $jpnumber == "4" ) {
			$postAuthors = $_POST['author1'] . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4'];
		} if( $jpnumber == "5" ) {
			$postAuthors = $_POST['author1'] . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4']  . "," . $_POST['author5'];
		} if( $jpnumber == "6" ) {
			$postAuthors = $_POST['author1'] . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4']  . "," . $_POST['author5'] . "," . $_POST['author6'];
		} if( $jpnumber == "7" ) {
			$postAuthors = $_POST['author1'] . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4']  . "," . $_POST['author5'] . "," . $_POST['author6'] . "," . $_POST['author7'];
		} if( $jpnumber == "8" ) {
			$postAuthors = $_POST['author1'] . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4']  . "," . $_POST['author5'] . "," . $_POST['author6'] . "," . $_POST['author7'] . "," . $_POST['author8'];
		}
	
		$postMissionEntry = "INSERT INTO sms_posts ( postid, postAuthor, postTitle, postLocation, postTimeline, postContent, postPosted, postMission, postStatus, postTag ) ";
		$postMissionEntry.= "VALUES ( '', '$postAuthors', '$postTitle', '$postLocation', '$postTimeline', '$postContent', UNIX_TIMESTAMP(), '$postMission', 'activated', '$postTag' )";
		$result = mysql_query( $postMissionEntry );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
	
		for( $i=1; $i<=$number; $i++ ) {
	
			/* set the author var */
			$author = $_POST['postAuthor' . $i];
	
			/* update the player's last post timestamp */
			$updateTimestamp = "UPDATE sms_crew SET lastPost = UNIX_TIMESTAMP() ";
			$updateTimestamp.= "WHERE crewid = '$author' LIMIT 1";
			$updateTimestampResult = mysql_query( $updateTimestamp );
	
		}
	
		/* optimize the crew table */
		optimizeSQLTable( "sms_crew" );
		
		/* strip the slashes added for the query */
		$postTitle = stripslashes( $_POST['postTitle'] );
		$postLocation = stripslashes( $_POST['postLocation'] );
		$postTimeline = stripslashes( $_POST['postTimeline'] );
		$postContent = stripslashes( $_POST['postContent'] );
		$postTag = stripslashes( $_POST['postTag'] );
		
		/* if the user wants to send the email out, do it */
		if( $_POST['sendEmail'] == "y" ) {
		
			/** EMAIL THE POST **/
			
			/* set the email author */
			$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankName ";
			$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
			$userFetch.= "WHERE crew.crewid = '$_POST[author1]' AND crew.rankid = rank.rankid LIMIT 1";
			$userFetchResult = mysql_query( $userFetch );
			
			while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
				extract( $userFetchArray, EXTR_OVERWRITE );
			}
			
			$firstName = str_replace( "'", "", $firstName );
			$lastName = str_replace( "'", "", $lastName );
			
			$from = $rankName . " " . $firstName . " " . $lastName . " < " . $email . " >";
			
			/* define the variables */
			$to = getCrewEmails( "emailPosts" );
			$subject = "[" . $shipPrefix . " " . $shipName . "] " . printMissionTitle( $postMission ) . " - " . $postTitle;
			$message = "A Post By " . displayEmailAuthors( $postAuthors, 'noLink' ) . "
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
			$check->message( "joint post", "add" );
			$check->display();
		}
		
		?>
	
		<span class="fontTitle">Add Joint Mission Entry</span><br /><br />
	
		This page should be used in the event that a member of the crew has accidentally
		posted incorrectly.  For instance, if a player has replied to one of the emails
		sent out to the system instead of logging in and posting, you can copy and paste
		the contents of their email into this form and put the entry into the system. For
		all other joint posts, please use the <a href="<?=$webLocation;?>admin.php?page=post&sub=jp">
		Write Joint Post</a> page.<br /><br />
	
		<span class="fontNormal">
			<b>Select the number of participants:</b> &nbsp;
			<a href="<?=$webLocation;?>admin.php?page=post&sub=addjp&number=2">2 people</a>
			&nbsp; &middot; &nbsp;
			<a href="<?=$webLocation;?>admin.php?page=post&sub=addjp&number=3">3 people</a>
			&nbsp; &middot; &nbsp;
			<a href="<?=$webLocation;?>admin.php?page=post&sub=addjp&number=4">4 people</a>
			&nbsp; &middot; &nbsp;
			<a href="<?=$webLocation;?>admin.php?page=post&sub=addjp&number=5">5 people</a>
			&nbsp; &middot; &nbsp;
			<a href="<?=$webLocation;?>admin.php?page=post&sub=addjp&number=6">6 people</a>
			&nbsp; &middot; &nbsp;
			<a href="<?=$webLocation;?>admin.php?page=post&sub=addjp&number=7">7 people</a>
			&nbsp; &middot; &nbsp;
			<a href="<?=$webLocation;?>admin.php?page=post&sub=addjp&number=8">8 people</a>
		</span><br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=addjp">
		<table>
			<?
			
			$authorNum = 1;
			
			for( $i=1; $i<=$number; $i++ ) {
			
			?>
			
			<tr>
				<td class="narrowLabel tableCellLabel">
					<b>Author #<?=$i;?></b>
				</td>
				<td>&nbsp;</td>
				<td>
					<select name="author<?=$authorNum;?>">
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
			<? $authorNum = $authorNum + 1; } ?>
			
			<? if( !$_GET['number'] ) { ?>
			<input type="hidden" name="jpNumber" value="2" />
			<? } else { ?>
			<input type="hidden" name="jpNumber" value="<?=$_GET['number'];?>" />
			<? } ?>
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
					<input type="image" src="<?=path_userskin;?>buttons/add.png" name="action" class="button" value="Add" />
				</td>
			</tr>
			<? } ?>
		</table>
		</form>
	</div>
<? } else { errorMessage( "add joint post" ); } ?>