<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/post/notes.php
Purpose: Page that displays the mission notes for the current mission

System Version: 2.5.0
Last Modified: 2007-04-03 1921 EST
**/

/* set the page class */
$pageClass = "admin";
$subMenuClass = "post";

$getNotes = "SELECT missionid, missionNotes FROM sms_missions ";
$getNotes.= "WHERE missionStatus = 'current' LIMIT 1";
$getNotesResult = mysql_query( $getNotes );
$notes = mysql_fetch_array( $getNotesResult );

?>

<div class="body">
	<span class="fontTitle">
		Mission Notes - <i><?=printMissionTitle( $notes['0'] ); ?></i>
		
		<? if( in_array( "m_missionnotes", $sessionAccess ) ) { ?>
		&nbsp;
		<a href="<?=$webLocation;?>admin.php?page=manage&sub=missionnotes">
			<img src="<?=$webLocation;?>images/edit.png" border="0" alt="[ Edit ]" />
		</a>
		<? } ?>
		
	</span><br /><br />
	<? printText( $notes['1'] ); ?>
</div>