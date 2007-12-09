<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/info.php
Purpose: The file that pulls in the current mission, latest posts, latest logs for
	the main page

System Version: 2.5.3
Last Modified: 2007-08-13 1033 EST
**/

/* get the current mission info */
$getCurrentMission = "SELECT missionid, missionTitle, missionDesc FROM sms_missions ";
$getCurrentMission.= "WHERE missionStatus = 'current' LIMIT 1";
$getCurrentMissionResult = mysql_query( $getCurrentMission );
$fetchMission = mysql_fetch_row( $getCurrentMissionResult );

/* get the post info */
$getPosts = "SELECT postid, postTitle FROM sms_posts WHERE postStatus = 'activated' ORDER BY postPosted DESC LIMIT 3";
$getPostsResult = mysql_query( $getPosts );

/* get the open positions */
$getPositions = "SELECT positionid, positionName, positionOpen FROM sms_positions ";
$getPositions.= "WHERE positionMainPage = 'y' ORDER BY positionDept, positionOrder ASC";
$getPositionsResult = mysql_query( $getPositions );

?>

<div class="info">
	
	<? if( $showInfoMission == "y" ) { ?>
	<span class="fontLarge"><b>Current Mission</b></span><br />
	<ul>
		<li><a href="<?=$webLocation;?>index.php?page=mission&id=<?=$fetchMission['0'];?>"><? printText( $fetchMission['1'] ); ?></a></li>
		<li class="fontNormal"><? printText( $fetchMission['2'] ); ?></li>
	</ul>
	<? } ?>
	
	<? if( $showInfoPosts == "y" && $usePosting == "y" ) { ?>
	<span class="fontLarge"><b>Latest Posts</b></span><br />
	<b class="fontNormal"><a href="<?=$webLocation;?>index.php?page=postlist&id=<?=$fetchMission['0'];?>">Complete Post List &raquo;</a></b><br />
	
	<ul>
		<?
		
		/* loop through everything until you run out of results */
		while( $fetchPost = mysql_fetch_array( $getPostsResult ) ) {
			extract( $fetchPost, EXTR_OVERWRITE );
		
		?>
		<li class="fontNormal"><a href="<?=$webLocation;?>index.php?page=post&id=<?=$fetchPost['0'];?>" class="fix"><? printText( $fetchPost['1'] ); ?></a> <span class="fontNormal">by <? displayAuthors( $fetchPost['0'], "rank", "noLink" ); ?></span></li>
		<li class="spacer">&nbsp;</li>
		<? } ?>
	</ul>
	<? } ?>
	
	<? if( $showInfoPositions == "y" ) { ?>
	<span class="fontLarge"><b>Open Positions</b></span><br />
	<ul>
		<?
		
		/* loop through everything until you run out of results */
		while( $fetchPositions = mysql_fetch_array( $getPositionsResult ) ) {
			extract( $fetchPositions, EXTR_OVERWRITE );
			
			if( $fetchPositions['2'] > 0 ) {
			
		?>
		<li class="fontNormal"><a href="<?=$webLocation;?>index.php?page=join&position=<?=$fetchPositions['0'];?>"><? printText( $fetchPositions['1'] ); ?></a></li>
		<? } } ?>
	</ul>
	<? } ?>
</div>