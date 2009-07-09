<?php

function missionInfo()
{
	$query = "SELECT * FROM sms_missions WHERE missionStatus = 'current'";
	$result = mysql_query($query);
	$fetch = mysql_fetch_assoc($result);
	
	$output = '<h2>Current Mission</h2>';
	$output.= '<h3><a href="'. WEBLOC .'index.php?page=missions&id='. $fetch['missionid'] .'">'. $fetch['missionTitle'] .'</a></h3>';
	$output.= '<p>'. $fetch['missionDesc'] .'</p>';
	
	return $output;
}