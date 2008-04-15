<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Authors: David VanScott [ davidv@anodyne-productions.com ]
File: pages/post.php
Purpose: To display the individual posts to a mission

System Version: 2.6.0
Last Modified: 2008-04-14 2342 EST
**/

/* define the page class */
$pageClass = "simm";

if(isset($_GET['id']) && is_numeric($_GET['id']))
{
	$mp_id = $_GET['id'];
}
else
{
	$mp_id = NULL;
}

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* get post id for individual message display */
if(isset($mp_id))
{

	/* pull all the information relating to the post */
	$getpost = "SELECT * FROM sms_posts WHERE postid = $mp_id LIMIT 1";
	$getpostResult = mysql_query($getpost);
	
	/* pull all posts to create the next and prev post links */
	$getposts = "SELECT postid FROM sms_posts WHERE postStatus = 'activated' ORDER BY postPosted ASC";
	$getpostsResult = mysql_query($getposts);
	
	/* extract the post data into the MySQL field name variables */
	$postinfo = mysql_fetch_array($getpostResult);
		extract($postinfo, EXTR_OVERWRITE);
		$tempAuthors = explode(",", $postAuthor); /* temporary array for user post editing */
	
	/* pull the mission title */
	$getmission = "SELECT missionid, missionTitle FROM sms_missions WHERE missionid = '$postMission'";
	$getmissionResult = mysql_query($getmission);
	
	/* extract the mission title */
	$mission = mysql_fetch_array($getmissionResult);
		extract($mission, EXTR_OVERWRITE);
	
	/* if post is untitled give it a title */
	if($postTitle == "")
	{
		$postTitle = "[ Untitled ]";
	}
	
?>

<div class="body">

	<span class="fontTitle"><em><? printText( $missionTitle ); ?></em> &ndash; <? printText( $postTitle ); ?></span><br />
	<span class="fontMedium">by <? displayAuthors ( $postid, "noLink" ); ?></span><br /><br />
	
	<span class="fontNormal postDetails">
	<div align="center">

	<?php
		
	/* point the previous and next post buttons to the correct posts */
	$idNumbers = array();
	
	while($myrow = mysql_fetch_array($getpostsResult))
	{
		$idNumbers[] = $myrow['postid'];
	}
	
	$arrayCount = count($idNumbers) -1;
	
	foreach($idNumbers as $key => $value)
	{
		if($mp_id == $value)
		{
			$nextKey = $key+1;
			$prevKey = $key-1;
			
			/* display the previous and next links in the post details box */
			if( $prevKey >= 0 && $idNumbers[$prevKey] != '' ) {
				echo "<a href='" . $webLocation . "/index.php?page=post&id=" . $idNumbers[$prevKey] . "'><img src='" . $webLocation . "/images/previous.png' alt='Previous Entry' border='0' class='image' /></a>";
			} if( ( $prevKey >= 0 && $idNumbers[$prevKey] != '' ) && ( $nextKey <= $arrayCount && $idNumbers[$nextKey] != '' ) ) {
				echo "&nbsp;";
			} if( $nextKey <= $arrayCount && $idNumbers[$nextKey] != '' ) {
				echo "<a href='$webLocation/index.php?page=post&id=$idNumbers[$nextKey]'><img src='$webLocation/images/next.png' alt='Next Entry' border='0' class='image' /></a>";
			}
		} /* close if(mp_id == value) */
	} /* close foreach loop */
		
	?>
	
			<br /><strong>Post Details</strong><br />
			<?
		
			if(
				in_array("m_posts2", $sessionAccess) ||
				(in_array("m_posts1", $sessionAccess) && in_array($sessionCrewid, $tempAuthors))
			) {
				echo "<a href='" . $webLocation . "admin.php?page=manage&sub=posts&id=" . $mp_id . "' class='edit'><b>Edit</b></a>";
			}
			
			if(in_array("m_posts2", $sessionAccess))
			{
				echo "&nbsp; &middot; &nbsp;";

			?>	

				<script type="text/javascript">
					document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=posts&remove=<?=$mp_id;?>\" class=\"delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this mission entry?')\">Delete</a>" );
				</script>
				<noscript>
					<a href="<?=$webLocation;?>admin.php?page=manage&sub=posts&remove=<?=$mp_id;?>" class="delete">Delete</a>
				</noscript>
				
			<?php

				if( $postinfo['postStatus'] == "pending" )
				{
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
				<td class="tableCellLabel">Mission</td>
				<td>&nbsp;</td>
				<td><a href="<?=$webLocation;?>index.php?page=mission&mid=<?=$missionid;?>"><? printText( $missionTitle ); ?></a></td>
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