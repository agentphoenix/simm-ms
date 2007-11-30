<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: Nathan Wharry [ mail@herschwolf.net ]
File: pages/post.php
Purpose: To display the individual posts to a mission

System Version: 2.5.0
Last Modified: 2007-04-24 1829 EST
**/

/* define the page class */
$pageClass = "simm";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* do some advanced checking to make sure someone's not trying to do a SQL injection */
if( !empty( $_GET['position'] ) && preg_match( "/^\d+$/", $_GET['position'], $matches ) == 0 ) {
	errorMessageIllegal( "activation page" );
	exit();
} else {
	/* set the GET variable */
	$mp_id = $_GET['id'];
}

/* get post id for individual message display */
if( $mp_id ) {

	/* pull all the information relating to the post */
	$getpost = "SELECT * FROM sms_posts ";
	$getpost.= "WHERE postid = '$mp_id' LIMIT 1";
	$getpostResult = mysql_query ( $getpost );
	
	/* pull all posts to create the next and prev post links */
	$getposts = "SELECT postid FROM sms_posts ";
	$getposts.= "WHERE postStatus = 'activated' ";
	$getposts.= "ORDER BY postPosted ASC";
	$getpostsResult = mysql_query ( $getposts );
	
	/* extract the post data into the MySQL field name variables */
	$postinfo = mysql_fetch_array( $getpostResult );
		extract( $postinfo, EXTR_OVERWRITE ); 
	
	/* pull the mission title */
	$getmission = "SELECT missionid, missionTitle ";
	$getmission.= "FROM sms_missions ";
	$getmission.= "WHERE missionid = '$postMission'";
	$getmissionResult = mysql_query ( $getmission );
	
	/* extract the mission title */
	$mission = mysql_fetch_array( $getmissionResult );
		extract( $mission, EXTR_OVERWRITE );
	
	/* if post is untitled give it a title */
	if ( $postTitle == "" ) {
		 $postTitle = "[ Untitled ]";
	}
	
?>

<div class="body">

	<span class="fontTitle"><i><? printText( $missionTitle ); ?></i> - <? printText( $postTitle ); ?></span>
	<br />
	<span class="fontMedium">by <? displayAuthors ( $postid, "noLink" ); ?></span>
	<br /><br />
	
	<span class="fontNormal postDetails">
	<div align="center">

	<?  
		
		/* point the previous and next post buttons to the correct posts */
		$idNumbers = array();
		
		while( $myrow = mysql_fetch_array( $getpostsResult ) ) {
			$idNumbers[] = $myrow['postid'];
		}
		
		foreach( $idNumbers as $key => $value ) {
			if( $mp_id == $value ) {
				
				$nextKey = $key+1;
				$prevKey = $key-1;
		
			/* display the previous and next links in the post details box */
			if( $idNumbers[$prevKey] != '' ) {
					printText ( "<a href='$webLocation/index.php?page=post&id=$idNumbers[$prevKey]'><img src='$webLocation/images/previous.png' alt='Previous Entry' border='0' /></a>" );
				} if( ($idNumbers[$prevKey] != '') && ($idNumbers[$nextKey] != '') ) {
					echo "&nbsp;";
				} if( $idNumbers[$nextKey] != '' ) {
					printText ( "<a href='$webLocation/index.php?page=post&id=$idNumbers[$nextKey]'><img src='$webLocation/images/next.png' alt='Next Entry' border='0' /></a>" );
				}
			}
		}
		
	?>
	
			<br />
			<b>Post Details</b><br />
			<?
		
			if( in_array( "m_posts", $sessionAccess ) ) {
				echo "<a href='" . $webLocation . "admin.php?page=manage&sub=posts&id=" . $mp_id . "' class='edit'><b>Edit</b></a>";
				echo "&nbsp; &middot; &nbsp;";

			?>	

				<script type="text/javascript">
					document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=posts&remove=<?=$mp_id;?>\" class=\"delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this mission entry?')\">Delete</a>" );
				</script>
				<noscript>
					<a href="<?=$webLocation;?>admin.php?page=manage&sub=posts&remove=<?=$mp_id;?>" class="delete">Delete</a>
				</noscript>
				
			<?

				if( $postinfo['postStatus'] == "pending" ) {
				
					echo "&nbsp; &middot; &nbsp;";
					echo "<a href='" . $webLocation . "admin.php?page=manage&sub=activate&type=post&id=" . $mp_id . "&action=activate'><b>Activate</b></a>";
				
				}
				
			}
			
			?><p></p>
		</div> <!-- close the centering div -->
		
		<table>
			<tr>
				<td class="tableCellLabel">Title</td>
				<td>&nbsp;</td>
				<td><? printText( $postTitle ); ?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Author(s)</td>
				<td>&nbsp;</td>
				<td><? displayAuthors( $postid, "link" ); ?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Posted</td>
				<td>&nbsp;</td>
				<td><?=dateFormat( "medium", $postPosted );?></td>
			</tr>

			<? if( !empty( $postLocation ) ) { ?>
			<tr>
				<td class="tableCellLabel">Location</td>
				<td>&nbsp;</td>
				<td><? printText( $postLocation ); ?></td>
			</tr>
			<? } ?>

			<? if( !empty( $postTimeline ) ) { ?>
			<tr>
				<td class="tableCellLabel">Timeline</td>
				<td>&nbsp;</td>
				<td><? printText( $postTimeline ); ?></td>
			</tr>
			<? } ?>

			<? if( !empty( $postTag ) ) { ?>
			<tr>
				<td class="tableCellLabel">Tag</td>
				<td>&nbsp;</td>
				<td><? printText( $postTag ); ?></td>
			</tr>
			<? } ?>
		</table>

		<?

		if( isset( $sessionCrewid ) ) {
			echo "<div align='center'>";
			echo "<br />";
			echo "<a href='" . $webLocation . "admin.php?page=post&sub=mission'><b>Post a Reply</b></a>";
			echo "</div>";
		}

		?>
		
	</span>
	
	<? printText( $postContent ); ?>

</div> <!--Close Div content class tag-->

<? } ?>