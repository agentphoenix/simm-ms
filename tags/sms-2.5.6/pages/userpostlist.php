<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: pages/userpostlist.php
Purpose: Page to display all the posts of a specific user

System Version: 2.5.0
Last Modified: 2007-04-24 1832 EST
**/

/* define the page class and vars */
$pageClass = "personnel";
$crew = $_GET['crew'];

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* get mission id for individual mission display */
if( $crew ) {

/* pull all the posts */
$getPosts = "SELECT post.*, mission.* ";
$getPosts.= "FROM sms_posts AS post, sms_missions AS mission ";
$getPosts.= "WHERE post.postStatus = 'activated' AND ( post.postAuthor LIKE '$crew,%' OR ";
$getPosts.= "post.postAuthor LIKE '%,$crew' OR post.postAuthor LIKE '%,$crew,%' OR ";
$getPosts.= "post.postAuthor = '$crew' ) AND post.postMission = mission.missionid ";
$getPosts.= "ORDER BY post.postPosted DESC";
$getPostsResult = mysql_query( $getPosts );

/* pull all the logs */
$getLogs = "SELECT * FROM sms_personallogs WHERE logStatus = 'activated' AND ";
$getLogs.= "logAuthor = '$crew' ORDER BY logPosted DESC";
$getLogsResult = mysql_query( $getLogs );

?>

<div class="body">
	<span class="fontTitle">
		<? printCrewName( $crew, "rank", "noLink" ); ?>'s Complete Post List
	</span><br /><br />
	
	<table cellspacing="0" cellpadding="4">
		<tr>
			<td colspan="3" class="fontLarge"><b>Mission Posts</b></td>
		</tr>
		
		<?
		
		$rowCount = "0";
		$color1 = "rowColor1";
		$color2 = "rowColor2";
		
		while( $postFetch = mysql_fetch_array( $getPostsResult ) ) {
			extract( $postFetch, EXTR_OVERWRITE );
			
			$rowColor = ( $rowCount % 2 ) ? $color1 : $color2;

		?>
		<tr class="<?=$rowColor;?> fontNormal">
			<td>
				<a href="<?=$webLocation;?>index.php?page=post&id=<?=$postid;?>"><? printText( $postTitle ); ?></a>
			</td> 
			<td>
				<a href="<?=$webLocation;?>index.php?page=mission&mid=<?=$missionid;?>"><? printText( $missionTitle ); ?></a>
			</td> 
			<td><?=dateFormat( "medium", $postPosted );?></td> 
		</tr>

		<? $rowCount++; } ?>

		<tr>
			<td colspan="3" height="25"></td>
		</tr>
		<tr>
			<td colspan="3" class="fontLarge">
				<b>Personal Logs</b><a name="logs"></a>
			</td>
		</tr>

		<?
		
		$rowCount = 0;
		
		while( $logFetch = mysql_fetch_array( $getLogsResult ) ) {
			extract( $logFetch, EXTR_OVERWRITE );
			
			$rowColor = ( $rowCount % 2 ) ? $color1 : $color2;

		?>
		<tr class="<?=$rowColor;?> fontNormal">
			<td>
				<a href="<?=$webLocation;?>index.php?page=log&id=<?=$logid;?>"><? printText( $logTitle ); ?></a>
			</td> 
			<td>&nbsp;</td> 
			<td><?=dateFormat( "medium", $logPosted );?></td>
		</tr>

		<? $rowCount++; } ?>
	
	</table> 
	
<? } else { ?>

<div class="body">
	
	<span class="fontTitle">Error!</span><br /><br />
	Please specify a crew member to view posts for. If you believe you have received this 
	message in error, please contact the system administrator.
	
<? } ?>
	
</div> <!-- close .body -->