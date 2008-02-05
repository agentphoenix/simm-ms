<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: Nathan Wharry [ mail@herschwolf.net ]
File: pages/summaries.php
Purpose: To display the mission summary for new players

System Version: 2.5.0
Last Modified: 2007-04-06 0013 EST
**/

/* define the page class */
$pageClass = "simm";
$sec = $_GET['sec'];

/* set the default section */
if( !$sec ) {
	$sec = "current";
}

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* pull all info on current mission */
$getmissionCurrent = "SELECT * ,";
$getmissionCurrent.= "DATE_FORMAT(missionStart,'%W, %M %d, %Y at %h:%i%p') as dateStart, ";
$getmissionCurrent.= "DATE_FORMAT(missionEnd,'%W, %M %d, %Y at %h:%i%p') as dateEnd ";
$getmissionCurrent.= "FROM sms_missions ";
$getmissionCurrent.= "WHERE missionStatus = 'current'";
$getmissionCurrentResult = mysql_query( $getmissionCurrent );

$getmissionCompleted = "SELECT * ,";
$getmissionCompleted.= "DATE_FORMAT(missionStart,'%W, %M %d, %Y at %h:%i%p') as dateStart, ";
$getmissionCompleted.= "DATE_FORMAT(missionEnd,'%W, %M %d, %Y at %h:%i%p') as dateEnd ";
$getmissionCompleted.= "FROM sms_missions ";
$getmissionCompleted.= "WHERE missionStatus = 'completed' ORDER BY missionOrder DESC";
$getmissionCompletedResult = mysql_query( $getmissionCompleted );

?>

<div class="body">

	<span class="fontTitle">Mission Summaries</span>
	<?
	
	/*
		if the person is logged in and has level 5 access, display an icon
		that will take them to edit the entry
	*/
	if( isset( $sessionCrewid ) && in_array( "m_missionsummaries", $sessionAccess ) ) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='" . $webLocation . "admin.php?page=manage&sub=summaries'>";
		echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' />";
		echo "</a>";
	}
	
	?>
	<br /><br />
	
	<div id="subnav">
		<ul>
			<li <? if( $sec == "current" ) { echo "id='current'"; } ?>><a href="<?=$webLocation;?>index.php?page=summaries&sec=current">Current Mission</a></li>
			<li <? if( $sec == "completed" ) { echo "id='current'"; } ?>><a href="<?=$webLocation;?>index.php?page=summaries&sec=completed">Completed Missions</a></li>
		</ul>
	</div>

	<div class="tabcontainer">
	
	<?  
	
	if( $sec == "current" ) {
			
		$status = "[In Progress]";
		
		while( $missionCurrent = mysql_fetch_array( $getmissionCurrentResult ) ) {
			extract( $missionCurrent, EXTR_OVERWRITE );
			
	?>
	
	<br />
	<b class="fontLarge">
		<a href="<?=$webLocation;?>index.php?page=mission&id=<?=$missionid;?>">
		<? printText ( $missionTitle ); ?>
		</a>&nbsp;
		<span class="yellow"><?=$status;?></span>
	</b><br />
	
	<div class="specialPadding1">
		<? printText( $missionSummary ); ?>
	</div>
	
	<?
	
		} /* close the while loop */
			
	} elseif( $sec == "completed" ) {
		
		while( $missionComplete = mysql_fetch_array( $getmissionCompletedResult ) ) {
			extract( $missionComplete, EXTR_OVERWRITE );
			
	?>
	
	<br />
	<b class="fontLarge">
		<a href="<?=$webLocation;?>index.php?page=mission&id=<?=$missionid;?>">
		<? printText ( $missionTitle ); ?>
		</a>
	</b><br />
	
	<div class="specialPadding1">
		<? printText( $missionSummary ); ?>
	</div>
	
	<?
	
		} /* close the while loop */
		
	}
	
	?>
	
	</div> <!-- close the tab container -->

</div> <!--Close the div body class tag-->