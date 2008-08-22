<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/missions.php
Purpose: Page to display the list of missions

System Version: 2.6.0
Last Modified: 2008-04-06 2042 EST
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

$disable = array();

if($currentCount == 0) {
	$disable[] = 1;
}

if($upcomingCount == 0) {
	$disable[] = 2;
}

if($completedCount == 0) {
	$disable[] = 3;
}

$disable_string = implode(",", $disable);

?>

<div class="body">
	<script type="text/javascript">
		$(document).ready(function(){
			$('#container-1 > ul').tabs({ disabled: [<?php echo $disable_string; ?>] });
		});
	</script>
	
	<span class="fontTitle">Mission Logs</span><br />
	
	<div id="container-1">
		<ul>
			<li><a href="#one"><span>Current Mission (<?=$currentCount;?>)</span></a></li>
			<li><a href="#two"><span>Upcoming Missions (<?=$upcomingCount;?>)</span></a></li>
			<li><a href="#three"><span>Completed Missions (<?=$completedCount;?>)</span></a></li>
		</ul>
		
		<div id="one" class="ui-tabs-container ui-tabs-hide">
			<?

			while( $current = mysql_fetch_array( $missionsCurrentResult ) ) {
				extract( $current, EXTR_OVERWRITE );

				$posts = "SELECT postid FROM sms_posts WHERE postMission = '$missionid' ";
				$posts.= "AND postStatus = 'activated' ORDER BY postid DESC";
				$missionPosts = mysql_query ( $posts );
				$numPosts = mysql_num_rows( $missionPosts );

				echo "<a href='" . $webLocation . "index.php?page=mission&id=" . $missionid . "'><b class='fontMedium'>";

				printText( $missionTitle );

				echo "</b></a>";

				if( $usePosting == "y" ) {
					echo "&nbsp;&nbsp; [ Posts: " . $numPosts . " ]";
				}

				echo "<div style='padding: 1em 0 2em 1em;'>";

				printText( $missionDesc );

				echo "</div>";

			} /* close the while loop */
				
			?>
		</div> <!-- close ONE -->
		
		<div id="two" class="ui-tabs-container ui-tabs-hide">
			<?

			while( $upcoming = mysql_fetch_array( $missionsUpcomingResult ) ) {
				extract( $upcoming, EXTR_OVERWRITE );

				$posts = "SELECT postid FROM sms_posts WHERE postMission = '$missionid' ";
				$posts.= "AND postStatus = 'activated' ORDER BY postid DESC";
				$missionPosts = mysql_query ( $posts );
				$numPosts = mysql_num_rows( $missionPosts );

				echo "<a href='" . $webLocation . "index.php?page=mission&id=" . $missionid . "'><b class='fontMedium'>";

				printText( $missionTitle );

				echo "</b></a>";

				if( $usePosting == "y" ) {
					echo "&nbsp;&nbsp; [ Posts: " . $numPosts . " ]";
				}

				echo "<div style='padding: 1em 0 2em 1em;'>";

				printText( $missionDesc );

				echo "</div>";

			} /* close the while loop */
			
			?>
		</div> <!-- close TWO -->
		
		<div id="three" class="ui-tabs-container ui-tabs-hide">
			<?	

			while( $complete = mysql_fetch_array( $missionsCompletedResult ) ) {
				extract( $complete, EXTR_OVERWRITE );

				$posts = "SELECT postid FROM sms_posts WHERE postMission = '$missionid' ";
				$posts.= "AND postStatus = 'activated' ORDER BY postid DESC";
				$missionPosts = mysql_query ( $posts );
				$numPosts = mysql_num_rows( $missionPosts );

				echo "<a href='" . $webLocation . "index.php?page=mission&id=" . $missionid . "'><b class='fontMedium'>";

				printText( $missionTitle );

				echo "</b></a>";

				if( $usePosting == "y" ) {
					echo "&nbsp;&nbsp; [ Posts: " . $numPosts . " ]";
				}

				echo "<div style='padding: 1em 0 2em 1em;'>";

				printText( $missionDesc );

				echo "</div>";

			} /* close the while loop */
			
			?>
		</div> <!-- close THREE -->
	</div> <!-- close CONTENT -->
	
</div> <!-- Close .body -->