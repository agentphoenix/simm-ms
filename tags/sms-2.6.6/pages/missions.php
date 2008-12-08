<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/missions.php
Purpose: Page to display the list of missions

System Version: 2.6.6
Last Modified: 2008-12-07 1850 EST
**/

/* define the page class */
$pageClass = "simm";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

$missionsQ = "SELECT missionid, missionTitle, missionDesc, missionStatus FROM sms_missions ORDER BY missionOrder DESC";
$missionsR = mysql_query($missionsQ);

while($mission_array = mysql_fetch_array($missionsR)) {
	extract($mission_array, EXTR_OVERWRITE);
	
	$posts = "SELECT count(postid) FROM sms_posts WHERE postMission = $missionid AND postStatus = 'activated'";
	$postsR = mysql_query($posts);
	$postCount = mysql_fetch_array($postsR);
	
	$missions[$missionStatus][] = array(
		'id' => $missionid,
		'title' => $missionTitle,
		'desc' => $missionDesc,
		'count' => $postCount[0]
	);
	
}

/* mission counts */
$upcomingCount = count($missions['upcoming']);
$currentCount = count($missions['current']);
$completedCount = count($missions['completed']);

/* disabled tabs */
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
			<?php
			
			foreach ($missions['current'] as $key => $value)
			{
				echo "<a href='" . $webLocation . "index.php?page=mission&id=" . $value['id'] . "'><strong class='fontMedium'>";
				printText($value['title']);
				echo "</strong></a>";

				if($usePosting == "y")
				{
					echo "&nbsp;&nbsp; [ Posts: " . $value['count'] . " ]";
				}

				echo "<div style='padding: 1em 0 2em 1em;'>";
				printText($value['desc']);
				echo "</div>";
			}
				
			?>
		</div> <!-- close ONE -->
		
		<div id="two" class="ui-tabs-container ui-tabs-hide">
			<?

			foreach ($missions['upcoming'] as $key => $value)
			{
				echo "<a href='" . $webLocation . "index.php?page=mission&id=" . $value['id'] . "'><strong class='fontMedium'>";
				printText($value['title']);
				echo "</strong></a>";

				if($usePosting == "y")
				{
					echo "&nbsp;&nbsp; [ Posts: " . $value['count'] . " ]";
				}

				echo "<div style='padding: 1em 0 2em 1em;'>";
				printText($value['desc']);
				echo "</div>";
			}
			
			?>
		</div> <!-- close TWO -->
		
		<div id="three" class="ui-tabs-container ui-tabs-hide">
			<?	

			foreach ($missions['completed'] as $key => $value)
			{
				echo "<a href='" . $webLocation . "index.php?page=mission&id=" . $value['id'] . "'><strong class='fontMedium'>";
				printText($value['title']);
				echo "</strong></a>";

				if($usePosting == "y")
				{
					echo "&nbsp;&nbsp; [ Posts: " . $value['count'] . " ]";
				}

				echo "<div style='padding: 1em 0 2em 1em;'>";
				printText($value['desc']);
				echo "</div>";
			}
			
			?>
		</div> <!-- close THREE -->
	</div> <!-- close CONTENT -->
	
</div> <!-- Close .body -->