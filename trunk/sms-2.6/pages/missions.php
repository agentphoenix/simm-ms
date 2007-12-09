<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: Nathan Wharry [ mail@herschwolf.net ]
File: pages/mission.php
Purpose: Page to display the list of missions

System Version: 2.5.0
Last Modified: 2007-06-11 1210 EST
**/

/* define the page class */
$pageClass = "simm";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* pull all the missions */
$missionsUpcoming = "SELECT * FROM sms_missions WHERE missionStatus = 'upcoming' ";
$missionsUpcoming.= "ORDER BY missionOrder DESC";
$missionsUpcomingResult = mysql_query( $missionsUpcoming );
$upcomingCount = mysql_num_rows( $missionsUpcomingResult );

$missionsCurrent = "SELECT * FROM sms_missions WHERE missionStatus = 'current' ";
$missionsCurrent.= "ORDER BY missionOrder DESC";
$missionsCurrentResult = mysql_query( $missionsCurrent );
$currentCount = mysql_num_rows( $missionsCurrentResult );

$missionsCompleted = "SELECT * FROM sms_missions WHERE missionStatus = 'completed' ";
$missionsCompleted.= "ORDER BY missionOrder DESC";
$missionsCompletedResult = mysql_query( $missionsCompleted );
$completedCount = mysql_num_rows( $missionsCompletedResult );

?>

<div class="body">
	<span class="fontTitle">Mission Logs</span><br /><br />
	
	<? if( $currentCount >= 1 ) { ?>
	<span class="fontMedium"><b>Current Mission</b></span><br />

	
	<?
	
		while( $current = mysql_fetch_array( $missionsCurrentResult ) ) {
			extract( $current, EXTR_OVERWRITE );
		
			$posts = "SELECT postid FROM sms_posts WHERE postMission = '$missionid' ";
			$posts.= "AND postStatus = 'activated' ORDER BY postid DESC";
			$missionPosts = mysql_query ( $posts );
			$numPosts = mysql_num_rows( $missionPosts );
			
			echo "<div style='padding: 1em 0 0 0;'>";
			echo "<a href='" . $webLocation . "index.php?page=mission&id=" . $missionid . "'><b>";
			
			printText( $missionTitle );
			
			echo "</b></a>";
			
			if( $usePosting == "y" ) {
				echo "&nbsp;&nbsp; [ Posts: " . $numPosts . " ]";
			}
			
			echo "</div><div style='padding: 1em 0 2em 1em;'>";
			
			printText( $missionDesc );
			
			echo "</div>";
			
		} /* close the while loop */
	
	} /* close the check for more than 1 current mission */
	
	/** display upcoming missions **/
	
	if( $upcomingCount >= 1 ) {
	
	?>

	<span class="fontMedium"><b>Upcoming Missions</b></span><br />

	<?
	
		while( $upcoming = mysql_fetch_array( $missionsUpcomingResult ) ) {
			extract( $upcoming, EXTR_OVERWRITE );
		
			$posts = "SELECT postid FROM sms_posts WHERE postMission = '$missionid' ";
			$posts.= "AND postStatus = 'activated' ORDER BY postid DESC";
			$missionPosts = mysql_query ( $posts );
			$numPosts = mysql_num_rows( $missionPosts );
			
			if( $numPosts == "0" ) {
				$numPosts = "N/A";
			} else {
				$numPosts = $numPosts;
			}
				
			echo "<div style='padding: 1em 0 0 0;'>";
			echo "<a href='" . $webLocation . "index.php?page=mission&id=" . $missionid . "'><b>";
			
			printText( $missionTitle );
			
			echo "</b></a>";
			
			if( $usePosting == "y" ) {
				echo "&nbsp;&nbsp; [ Posts: " . $numPosts . " ]";
			}
			
			echo "</div><div style='padding: 1em 0 2em 1em;'>";
			
			printText( $missionDesc );
			
			echo "</div>";
			
		} /* close the while loop */
	
	} /* close the check on upcoming missions */
	
	/** display completed missions **/
	
	if( $completedCount >= 1 ) {
	
	?>

	<span class="fontMedium"><b>Completed Missions</b></span><br />

	<?	
	
		while( $complete = mysql_fetch_array( $missionsCompletedResult ) ) {
			extract( $complete, EXTR_OVERWRITE );
		
			$posts = "SELECT postid FROM sms_posts WHERE postMission = '$missionid' ";
			$posts.= "AND postStatus = 'activated' ORDER BY postid DESC";
			$missionPosts = mysql_query ( $posts );
			$numPosts = mysql_num_rows( $missionPosts );
				
			echo "<div style='padding: 1em 0 0 0;'>";
			echo "<a href='" . $webLocation . "index.php?page=mission&id=" . $missionid . "'><b>";
			
			printText( $missionTitle );
			
			echo "</b></a>";
			
			if( $usePosting == "y" ) {
				echo "&nbsp;&nbsp; [ Posts: " . $numPosts . " ]";
			}
			
			echo "</div><div style='padding: 1em 0 2em 1em;'>";
			
			printText( $missionDesc );
			
			echo "</div>";
				
		} /* close the while loop */
	
	} /* close the completed check */
				
	?>	
	
</div> <!-- Close .body -->