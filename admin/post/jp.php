<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/post/jp.php
Purpose: Page to post a joint post

System Version: 2.5.4
Last Modified: 2007-09-17 0909 EST
**/

/* access check */
if( in_array( "p_jp", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "post";
	$actionPost = $_POST['action_post_x'];
	$actionSave = $_POST['action_save_x'];
	$actionDelete = $_POST['action_delete_x'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( "post JP page" );
		exit();
	} else {
		/* set the GET variable */
		$id = $_GET['id'];
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['number'] ) && preg_match( "/^\d+$/", $_GET['number'], $matches ) == 0 ) {
		errorMessageIllegal( "post JP page" );
		exit();
	} else {
		/* set the GET variable */
		$number = $_GET['number'];
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['delete'] ) && preg_match( "/^\d+$/", $_GET['delete'], $matches ) == 0 ) {
		errorMessageIllegal( "post JP page" );
		exit();
	} else {
		/* set the GET variable */
		$delete = $_GET['delete'];
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['add'] ) && preg_match( "/^\d+$/", $_GET['add'], $matches ) == 0 ) {
		errorMessageIllegal( "post JP page" );
		exit();
	} else {
		/* set the GET variable */
		$add = $_GET['add'];
	}
	
	if( $actionPost ) {
		
		/* add the necessary slashes */
		$postTitle = addslashes( $_POST['postTitle'] );
		$postLocation = addslashes( $_POST['postLocation'] );
		$postTimeline = addslashes( $_POST['postTimeline'] );
		$postContent = addslashes( $_POST['postContent'] );
		$postMission = $_POST['postMission'];
		$postTag = addslashes( $_POST['postTag'] );
		
		/* create the jp author variable */
		$jpNumber = $_GET['number'];
	
		if( !$jpNumber ) {
			$jpNumber = "2";
		}
		
		if( $id ) {
			if( $_POST['authorCount'] == "2" ) {
				$postAuthors = $_POST['postAuthor0'] . "," . $_POST['postAuthor1'];
			} if( $_POST['authorCount'] == "3" ) {
				$postAuthors = $_POST['postAuthor0'] . "," . $_POST['postAuthor1']  . "," . $_POST['postAuthor2'];
			} if( $_POST['authorCount'] == "4" ) {
				$postAuthors = $_POST['postAuthor0'] . "," . $_POST['postAuthor1']  . "," . $_POST['postAuthor2']  . "," . $_POST['postAuthor3'];
			} if( $_POST['authorCount'] == "5" ) {
				$postAuthors = $_POST['postAuthor0'] . "," . $_POST['postAuthor1']  . "," . $_POST['postAuthor2']  . "," . $_POST['postAuthor3']  . "," . $_POST['postAuthor4'];
			} if( $_POST['authorCount'] == "6" ) {
				$postAuthors = $_POST['postAuthor0'] . "," . $_POST['postAuthor1']  . "," . $_POST['postAuthor2']  . "," . $_POST['postAuthor3']  . "," . $_POST['postAuthor4'] . "," . $_POST['postAuthor5'];
			} if( $_POST['authorCount'] == "7" ) {
				$postAuthors = $_POST['postAuthor0'] . "," . $_POST['postAuthor1']  . "," . $_POST['postAuthor2']  . "," . $_POST['postAuthor3']  . "," . $_POST['postAuthor4'] . "," . $_POST['postAuthor5'] . "," . $_POST['postAuthor6'];
			} if( $_POST['authorCount'] == "8" ) {
				$postAuthors = $_POST['postAuthor0'] . "," . $_POST['postAuthor1']  . "," . $_POST['postAuthor2']  . "," . $_POST['postAuthor3']  . "," . $_POST['postAuthor4'] . "," . $_POST['postAuthor5'] . "," . $_POST['postAuthor6'] . "," . $_POST['postAuthor7'];
			}
		} else {
			if( $jpNumber == "2" ) {
				$postAuthors = $sessionCrewid . "," . $_POST['author2'];
			} elseif( $jpNumber == "3" ) {
				$postAuthors = $sessionCrewid . "," . $_POST['author2']  . "," . $_POST['author3'];
			} elseif( $jpNumber == "4" ) {
				$postAuthors = $sessionCrewid . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4'];
			} elseif( $jpNumber == "5" ) {
				$postAuthors = $sessionCrewid . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4']  . "," . $_POST['author5'];
			} elseif( $jpNumber == "6" ) {
				$postAuthors = $sessionCrewid . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4']  . "," . $_POST['author5'] . "," . $_POST['author6'];
			} elseif( $jpNumber == "7" ) {
				$postAuthors = $sessionCrewid . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4']  . "," . $_POST['author5'] . "," . $_POST['author6'] . "," . $_POST['author7'];
			} elseif( $jpNumber == "8" ) {
				$postAuthors = $sessionCrewid . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4']  . "," . $_POST['author5'] . "," . $_POST['author6'] . "," . $_POST['author7'] . "," . $_POST['author8'];
			}
		}
	
		/** check to see if the user is moderated **/
		$getModerated = "SELECT crewid FROM sms_crew WHERE moderatePosts = 'y'";
		$getModeratedResult = mysql_query( $getModerated );
	
		while( $moderated = mysql_fetch_array( $getModeratedResult ) ) {
			extract( $moderated, EXTR_OVERWRITE );
	
			$modArray[] = $moderated['0'];
	
		}
	
		/* explode the postAuthors string */
		$authorsExploded = explode( ",", $postAuthors );
	
		/*
			loop through the authors array and search for any of the items
			in the array of moderated users. if any are found, set the last
			key of the array to "y", otherwise, set it to "n"
		*/
		foreach( $authorsExploded as $key => $value ) {
			if( count( $modArray ) > "0" && in_array( $value, $modArray ) ) {
				$arrayModerate[] = "y";
			} else {
				$arrayModerate[] = "n";
			}
		}
	
		/*
			if the array coming out of the foreach loop has a single key with
			the value of "y", set the post to pending, otherwise, go through
			with the standard post status checks
		*/
		if( count( $modArray ) > "0" && in_array( "y", $arrayModerate ) ) {
			$postStatus = "pending";
		} else {
			if( ( $sessionCrewid == "" ) || ( $sessionCrewid == "0" ) ) {
				$postStatus = "pending";
			} elseif( $sessionCrewid > "0" ) {
				$postStatus = "activated";
			} if( $_POST['postMission'] == "" ) {
				$postStatus = "pending";
			}
		}
		/** end user moderation **/
	
		if( !$id ) {
			$query = "INSERT INTO sms_posts ( postid, postAuthor, postTitle, postLocation, postTimeline, postContent, postPosted, postMission, postStatus, postTag ) ";
			$query.= "VALUES ( '', '$postAuthors', '$postTitle', '$postLocation', '$postTimeline', '$postContent', UNIX_TIMESTAMP(), '$postMission', '$postStatus', '$postTag' )";
			$result = mysql_query( $query );
		} else {
			$query = "UPDATE sms_posts SET postAuthor = '$postAuthors', postTitle = '$postTitle', ";
			$query.= "postLocation = '$postLocation', postTimeline = '$postTimeline', ";
			$query.= "postContent = '$postContent', postStatus = '$postStatus', postTag = '$postTag', ";
			$query.= "postPosted = UNIX_TIMESTAMP() WHERE postid = '$id' LIMIT 1";
			$result = mysql_query( $query );
		}
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "post";
	
		/* update the crew timestamps */
		if( !$id ) {
			
			/* update the main author's last post timestamp */
			$updateTimestamp = "UPDATE sms_crew SET lastPost = UNIX_TIMESTAMP() ";
			$updateTimestamp.= "WHERE crewid = '$sessionCrewid' LIMIT 1";
			$updateTimestampResult = mysql_query( $updateTimestamp );
	
			for( $i = 2; $i <= $jpNumber; $i++ ) {
	
				/* set the author var */
				$author = $_POST['author' . $i];
	
				/* update the player's last post timestamp */
				$updateTimestamp = "UPDATE sms_crew SET lastPost = UNIX_TIMESTAMP() ";
				$updateTimestamp.= "WHERE crewid = '$author' LIMIT 1";
				$updateTimestampResult = mysql_query( $updateTimestamp );
	
			}
	
		} elseif( $id ) {
	
			/* set the number of authors */
			$number = $_POST['authorCount'];
	
			for( $i = 0; $i <= $number; $i++ ) {
	
				/* set the author var */
				$author = $_POST['postAuthor' . $i];
	
				/* update the player's last post timestamp */
				$updateTimestamp = "UPDATE sms_crew SET lastPost = UNIX_TIMESTAMP() ";
				$updateTimestamp.= "WHERE crewid = '$author' LIMIT 1";
				$updateTimestampResult = mysql_query( $updateTimestamp );
	
			}
	
		}
	
		/* optimize the crew table */
		optimizeSQLTable( "sms_crew" );
		
		/* strip the slashes added for the query */
		$postTitle = stripslashes( $_POST['postTitle'] );
		$postLocation = stripslashes( $_POST['postLocation'] );
		$postTimeline = stripslashes( $_POST['postTimeline'] );
		$postContent = stripslashes( $_POST['postContent'] );
		$postTag = stripslashes( $_POST['postTag'] );
		
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
			$subject = "[" . $shipPrefix . " " . $shipName . "] " . printMissionTitle( $postMission ) . " - " . $postTitle;
			$message = "A Post By " . displayEmailAuthors( $postAuthors, 'noLink' ) . "
Location: " . $postLocation . "
Timeline: " . $postTimeline . "
Tag: " . $postTag . "

" . $postContent . "";
			
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		} elseif( $postStatus == "pending" ) {
		
			/* define the variables */
			$to = printCOEmail();
			$subject = "[" . $shipPrefix . " " . $shipName . "] " . printMissionTitle( $postMission ) . " - " . $postTitle . " (Awaiting Approval)";
			$message = "A Post By " . displayEmailAuthors( $postAuthors, 'noLink' ) . "
Location: " . $postLocation . "
Timeline: " . $postTimeline . "
Tag: " . $postTag . "

" . $postContent . "

Please log in to approve this post.  " . $webLocation . "login.php?action=login";
			
			/* send the nomination email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		
		}
		
	} elseif( $actionSave ) {
	
		/* add the necessary slashes */
		$postTitle = addslashes( $_POST['postTitle'] );
		$postLocation = addslashes( $_POST['postLocation'] );
		$postTimeline = addslashes( $_POST['postTimeline'] );
		$postContent = addslashes( $_POST['postContent'] );
		$postMission = $_POST['postMission'];
		$postTag = addslashes( $_POST['postTag'] );
		
		/* create the jp author variable */
		if( !$id ) {
			$jpNumber = $_POST['jpNumber'];
		} else {
			$getAuthor = "SELECT postAuthor FROM sms_posts WHERE postid = '$id' LIMIT 1";
			$getAuthorResult = mysql_query( $getAuthor );
			$author = mysql_fetch_array( $getAuthorResult );
	
			$arrayAuthors = explode( ",", $author['0'] );
			$jpNumber = count( $arrayAuthors );
		}
	
		if( $id ) {
			if( $jpNumber == "2" ) {
				$postAuthors = $_POST['postAuthor0'] . "," . $_POST['postAuthor1'];
			} elseif( $jpNumber == "3" ) {
				$postAuthors = $_POST['postAuthor0'] . "," . $_POST['postAuthor1']  . "," . $_POST['postAuthor2'];
			} elseif( $jpNumber == "4" ) {
				$postAuthors = $_POST['postAuthor0'] . "," . $_POST['postAuthor1']  . "," . $_POST['postAuthor2']  . "," . $_POST['postAuthor3'];
			} elseif( $jpNumber == "5" ) {
				$postAuthors = $_POST['postAuthor0'] . "," . $_POST['postAuthor1']  . "," . $_POST['postAuthor2']  . "," . $_POST['postAuthor3']  . "," . $_POST['postAuthor4'];
			} elseif( $jpNumber == "6" ) {
				$postAuthors = $_POST['postAuthor0'] . "," . $_POST['postAuthor1']  . "," . $_POST['postAuthor2']  . "," . $_POST['postAuthor3']  . "," . $_POST['postAuthor4'] . "," . $_POST['postAuthor5'];
			} elseif( $jpNumber == "7" ) {
				$postAuthors = $_POST['postAuthor0'] . "," . $_POST['postAuthor1']  . "," . $_POST['postAuthor2']  . "," . $_POST['postAuthor3']  . "," . $_POST['postAuthor4'] . "," . $_POST['postAuthor5'] . "," . $_POST['postAuthor6'];
			} elseif( $jpNumber == "8" ) {
				$postAuthors = $_POST['postAuthor0'] . "," . $_POST['postAuthor1']  . "," . $_POST['postAuthor2']  . "," . $_POST['postAuthor3']  . "," . $_POST['postAuthor4'] . "," . $_POST['postAuthor5'] . "," . $_POST['postAuthor6'] . "," . $_POST['postAuthor7'];
			}
		} else {
			if( $jpNumber == "2" ) {
				$postAuthors = $sessionCrewid . "," . $_POST['author2'];
			} elseif( $jpNumber == "3" ) {
				$postAuthors = $sessionCrewid . "," . $_POST['author2']  . "," . $_POST['author3'];
			} elseif( $jpNumber == "4" ) {
				$postAuthors = $sessionCrewid . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4'];
			} elseif( $jpNumber == "5" ) {
				$postAuthors = $sessionCrewid . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4']  . "," . $_POST['author5'];
			} elseif( $jpNumber == "6" ) {
				$postAuthors = $sessionCrewid . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4']  . "," . $_POST['author5'] . "," . $_POST['author6'];
			} elseif( $jpNumber == "7" ) {
				$postAuthors = $sessionCrewid . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4']  . "," . $_POST['author5'] . "," . $_POST['author6'] . "," . $_POST['author7'];
			} elseif( $jpNumber == "8" ) {
				$postAuthors = $sessionCrewid . "," . $_POST['author2']  . "," . $_POST['author3']  . "," . $_POST['author4']  . "," . $_POST['author5'] . "," . $_POST['author6'] . "," . $_POST['author7'] . "," . $_POST['author8'];
			}
		}
	
		if( $id ) {
			$query = "UPDATE sms_posts SET postAuthor = '$postAuthors', postTitle = '$postTitle', ";
			$query.= "postLocation = '$postLocation', postTimeline = '$postTimeline', ";
			$query.= "postContent = '$postContent', postStatus = 'saved', postTag = '$postTag', ";
			$query.= "postPosted = UNIX_TIMESTAMP(), postSave = '$sessionCrewid' WHERE postid = '$id' LIMIT 1";
			$result = mysql_query( $query );
		} else {
			$query = "INSERT INTO sms_posts ( postid, postAuthor, postTitle, postLocation, postTimeline, postContent, postPosted, postMission, postStatus, postTag, postSave ) ";
			$query.= "VALUES ( '', '$postAuthors', '$postTitle', '$postLocation', '$postTimeline', '$postContent', UNIX_TIMESTAMP(), '$postMission', 'saved', '$postTag', '$sessionCrewid' )";
			$result = mysql_query( $query );
		}
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "save";
		
		/* strip the slashes added for the query */
		$postTitle = stripslashes( $_POST['postTitle'] );
		$postLocation = stripslashes( $_POST['postLocation'] );
		$postTimeline = stripslashes( $_POST['postTimeline'] );
		$postContent = stripslashes( $_POST['postContent'] );
		$postTag = stripslashes( $_POST['postTag'] );
	
		/* send an email out to notify the people there have been changes made */
	
		/* build the author emails and explode the string at the comma */
		$rawAuthors = explode( ",", $postAuthors );
		
		/*
			start the loop based on whether there are key/value pairs
			and keep doing 'something' until you run out of pairs
		*/
		foreach( $rawAuthors as $key => $value ) {
			
			/* do the database query */
			$getSelectEmails = "SELECT email FROM sms_crew WHERE crewid = '$value'";
			$getSelectEmailsResult = mysql_query( $getSelectEmails );
			
			/* Start pulling the array and populate the variables */
			while( $authorsEmails = mysql_fetch_array( $getSelectEmailsResult ) ) {
				extract( $authorsEmails, EXTR_OVERWRITE );
				
				$authors_array[] = $authorsEmails['0'];
				
				$authors_string = implode( ",", $authors_array );
				
			}	/* close the while loop */
		}	/* close the foreach loop */
	
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
		
		/* define the variables */
		$to = $authors_string;
		$subject = "[" . $shipPrefix . " " . $shipName . "] " . printMissionTitle( $postMission ) . " - " . $postTitle . " (Saved Joint Post)";
		$message = "This email is to notify you that your joint post, " . $postTitle . ", has recently been updated.  Please log in to make any changes you want before it is posted.  The content of the new post is below.  This is an automatically generated email.  Please log in to continue working on this post: " . $webLocation . "login.php?action=login
	
A Post By " . displayEmailAuthors( $postAuthors, 'noLink' ) . "
Location: " . $postLocation . "
Timeline: " . $postTimeline . "
Tag: " . $postTag . "

" . $postContent;
	
		/* send the email */
		mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
	
	} if( $delete ) {
			
		/* define the vars */
		$postid = $_GET['id'];
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
		$postid = $_GET['id'];
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
		
	} if( $actionDelete ) {
	
		/* get the authors */
		$getAuthors = "SELECT postAuthor, postTitle FROM sms_posts WHERE postid = '$id' LIMIT 1";
		$getAuthorsResult = mysql_query( $getAuthors );
		$authorFetch = mysql_fetch_array( $getAuthorsResult );
	
		/* delete the JP */
		$query = "DELETE FROM sms_posts WHERE postid = '$id' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "delete";
	
		/* send an email out to notify the people there have been changes made */
	
		/* build the author emails and explode the string at the comma */
		$rawAuthors = explode( ",", $authorFetch['0'] );
		
		/*
			start the loop based on whether there are key/value pairs
			and keep doing 'something' until you run out of pairs
		*/
		foreach( $rawAuthors as $key => $value ) {
			
			/* do the database query */
			$getSelectEmails = "SELECT email FROM sms_crew WHERE crewid = '$value'";
			$getSelectEmailsResult = mysql_query( $getSelectEmails );
			
			/* Start pulling the array and populate the variables */
			while( $authorsEmails = mysql_fetch_array( $getSelectEmailsResult ) ) {
				extract( $authorsEmails, EXTR_OVERWRITE );
				
				$authors_array[] = $authorsEmails['0'];
				
				$authors_string = implode( ",", $authors_array );
				
			}	/* close the while loop */
		}	/* close the foreach loop */
	
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
		
		/* define the variables */
		$to = $authors_string;
		$subject = "[" . $shipPrefix . " " . $shipName . "] Saved Post Update Notification";
		$message = "This email is to notify you that your joint post, " . $authorFetch['1'] . ", has been deleted by " . displayEmailAuthors( $sessionCrewid, 'noLink' ) . ".";
	
		/* send the email */
		mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
	
	}
	
	$number = $_GET['number'];
	
	if( !$number ) {
		$number = "2";
	} elseif( $number > "8" ) {
		$number = "8";
	}
	
?>
	
	<div class="body">
		
		<?
		
		/* set the type */
		if( isset( $delete ) || isset( $add ) ) {
			$type = "joint post author";
		} else {
			$type = "joint mission post";
		}
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( $type, $action );
			$check->display();
		}
		
		?>
	
		<? if( $useMissionNotes == "y" ) { ?>
		<div class="update">
			<a href="javascript:toggleLayer('notes')" style="float:right;">Show/Hide</a>
			<img src="<?=$webLocation;?>images/notes.png" style="float:left; padding-right: 12px;" border="0" />
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
	
		<? if( !$id ) { ?>
		<span class="fontTitle">Post <?=$number;?>-Way Joint Mission Entry</span><br /><br />
		<span class="fontNormal">
			<b>Select the number of participants:</b> &nbsp;
			<a href="<?=$webLocation;?>admin.php?page=post&sub=jp&number=2">2 people</a>
			&nbsp; &middot; &nbsp;
			<a href="<?=$webLocation;?>admin.php?page=post&sub=jp&number=3">3 people</a>
			&nbsp; &middot; &nbsp;
			<a href="<?=$webLocation;?>admin.php?page=post&sub=jp&number=4">4 people</a>
			&nbsp; &middot; &nbsp;
			<a href="<?=$webLocation;?>admin.php?page=post&sub=jp&number=5">5 people</a>
			&nbsp; &middot; &nbsp;
			<a href="<?=$webLocation;?>admin.php?page=post&sub=jp&number=6">6 people</a>
			&nbsp; &middot; &nbsp;
			<a href="<?=$webLocation;?>admin.php?page=post&sub=jp&number=7">7 people</a>
			&nbsp; &middot; &nbsp;
			<a href="<?=$webLocation;?>admin.php?page=post&sub=jp&number=8">8 people</a>
		</span><br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=jp&number=<?=$number;?>">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">Author #1</td>
				<td>&nbsp;</td>
				<td><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></td>
			</tr>
			<?
			
			$authorNum = "2";
			
			for( $i=1; $i<$number; $i++ ) {
			
			?>
			
			<tr>
				<td class="narrowLabel tableCellLabel">
					<b>Author #<?=$authorNum;?></b>
					<input type="hidden" name="jpNumber" value="<?=$number;?>" />
				</td>
				<td>&nbsp;</td>
				<td>
					<select name="author<?=$authorNum;?>">
					<?
					
					/* query the users database */
					$sql = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
					$sql.= "FROM sms_crew AS crew, sms_ranks AS rank WHERE crew.crewType = 'active' ";
					$sql.= "AND crew.rankid = rank.rankid AND crew.crewid != '$sessionCrewid' ";
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
			<tr>
				<td colspan="3" height="10"></td>
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
				<td colspan="3" height="5"></td>
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
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" class="button" value="Save" />
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/post.png" name="action_post" class="button" value="Post" />
				</td>
			</tr>
			<? } ?>
		</table>
		</form>
	
		<? } elseif( $id && !$actionDelete ) { ?>
		<span class="fontTitle">Edit Saved Joint Post</span><br /><br />
		<table cellpadding="2" cellspacing="2">
		<?
		
		$posts = "SELECT * FROM sms_posts WHERE postid = '$id' LIMIT 1";
		$postsResult = mysql_query( $posts );
		
		while( $postFetch = mysql_fetch_assoc( $postsResult ) ) {
			extract( $postFetch, EXTR_OVERWRITE );
		
		?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=jp&id=<?=$id;?>">
			<tr>
				<td class="narrowLabel tableCellLabel">Authors</td>
				<td>&nbsp;</td>
				<td>
					<? $authorCount = print_active_crew_select_menu( "post", $postAuthor, $postid, "post", "jp" ); ?>
					<input type="hidden" name="authorCount" value="<?=$authorCount;?>" />
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10">&nbsp;</td>
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
				<td>
					<input type="text" class="name" style="font-weight:bold;" maxlength="100" name="postTitle" value="<?=stripslashes( $postTitle );?>" />
				</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Location</td>
				<td>&nbsp;</td>
				<td>
					<input type="text" class="name" style="font-weight:bold;" maxlength="100" name="postLocation" value="<?=stripslashes( $postLocation );?>" />
				</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Timeline</td>
				<td>&nbsp;</td>
				<td>
					<input type="text" class="name" style="font-weight:bold;" maxlength="100" name="postTimeline" value="<?=stripslashes( $postTimeline );?>" />
				</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Tag</td>
				<td>&nbsp;</td>
				<td>
					<input type="text" class="name" style="font-weight:bold;" maxlength="100" name="postTag" value="<?=stripslashes( $postTag );?>" />
				</td>
			</tr>
			<tr>
				<td colspan="3" height="5">&nbsp;</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Content</td>
				<td>&nbsp;</td>
				<td>
					<textarea name="postContent" class="desc" rows="15"><?=stripslashes( $postContent );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<input type="hidden" name="postid" value="<?=$postid;?>" />
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this saved joint post?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" class="button" value="Save" />
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/post.png" name="action_post" class="button" value="Post" />
				</td>
			</tr>
			</form>
		<? } ?>
		</table>
		<? } elseif( $id && $actionDelete ) { ?>
	
		Please return to the Control Panel to continue.
	
		<? } ?>
		
	</div>
<? } else { errorMessage( "mission posting" ); } ?>