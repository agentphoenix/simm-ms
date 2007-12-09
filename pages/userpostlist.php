<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/userpostlist.php
Purpose: Page to display all the posts of a specific user

System Version: 2.6.0
Last Modified: 2007-10-12 1705 EST
**/

/* define the page class and vars */
$pageClass = "personnel";
$crew = $_GET['crew'];
$tab = $_GET['t'];

if( !isset( $tab ) ) {
	$tab = 1;
}

/* do some advanced checking to make sure someone's not trying to do a SQL injection */
if( !empty( $_GET['crew'] ) && preg_match( "/^\d+$/", $_GET['crew'], $matches ) == 0 ) {
	errorMessageIllegal( "user post list page" );
	exit();
} else {
	/* set the GET variable */
	$crew = $_GET['crew'];
}

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* get mission id for individual mission display */
if( isset( $crew ) ) {

/* pull all the posts */
$getPosts = "SELECT post.*, mission.* ";
$getPosts.= "FROM sms_posts AS post, sms_missions AS mission ";
$getPosts.= "WHERE post.postStatus = 'activated' AND ( post.postAuthor LIKE '$crew,%' OR ";
$getPosts.= "post.postAuthor LIKE '%,$crew' OR post.postAuthor LIKE '%,$crew,%' OR ";
$getPosts.= "post.postAuthor = '$crew' ) AND post.postMission = mission.missionid ";
$getPosts.= "ORDER BY post.postPosted DESC";
$getPostsResult = mysql_query( $getPosts );
$postCount = @mysql_num_rows( $getPostsResult );

/* pull all the logs */
$getLogs = "SELECT * FROM sms_personallogs WHERE logStatus = 'activated' AND ";
$getLogs.= "logAuthor = '$crew' ORDER BY logPosted DESC";
$getLogsResult = mysql_query( $getLogs );
$logCount = @mysql_num_rows( $getLogsResult );

?>

<div class="body">
	<span class="fontTitle">
		<? printCrewName( $crew, "rank", "noLink" ); ?>&rsquo;s Complete Post List
	</span><br /><br />
	
	<script type="text/javascript">
		$(function() {
			$('#container-1 > ul').tabs(<?php echo $tab; ?>);
		});
	</script>
	
	<div id="container-1">
		<ul>
			<li><a href="#one"><span>Mission Posts (<?php echo $postCount; ?>)</span></a></li>
			<li><a href="#two"><span>Personal Logs (<?php echo $logCount; ?>)</span></a></li>
		</ul>
		
		<div id="one" class="ui-tabs-container ui-tabs-hide">
			<table cellspacing="0" cellpadding="6">
			<?php if( $postCount == 0 ) { ?>
				<tr>
					<td colspan="3" class="fontMedium"><b>No posts recorded</b></td> 
				</tr>
			<?php
			
			} else {
			
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
			<? $rowCount++; } } ?>
			</table>
		</div>
		
		<div id="two" class="ui-tabs-container ui-tabs-hide">
			<table cellspacing="0" cellpadding="6">
			<?php if( $logCount == 0 ) { ?>
				<tr>
					<td colspan="3" class="fontMedium"><b>No personal logs recorded</b></td> 
				</tr>
			<?php
			
			} else {
			
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
			<? $rowCount++; } } ?>
			</table>
		</div>
	</div>
	
<? } else { ?>

<div class="body">
	
	<span class="fontTitle">Error!</span><br /><br />
	Please specify a crew member to view posts for. If you believe you have received this 
	message in error, please contact the system administrator.
	
<? } ?>
	
</div> <!-- close .body -->