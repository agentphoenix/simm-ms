<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/log.php
Purpose: To display the individual personal logs

System Version: 2.6.0
Last Modified: 2008-04-17 1924 EST
**/

/* define the page class */
$pageClass = "simm";

if(isset($_GET['id']) && is_numeric($_GET['id']))
{
	$pl_id = $_GET['id'];
}
else
{
	$pl_id = NULL;
}

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* get post id for individual message display */
if(isset($pl_id))
{

	/* pull all the information relating to the post */
	$getlog = "SELECT * FROM sms_personallogs WHERE logid = $pl_id LIMIT 1";
	$getlogResult = mysql_query ( $getlog );
	
	/* pull all posts to create the next and prev post links */
	$getlogs = "SELECT logid FROM sms_personallogs WHERE logStatus = 'activated' ORDER BY logPosted ASC";
	$getlogsResult = mysql_query ( $getlogs );
	
	/* extract the post data into the MySQL field name variables */
	$loginfo = mysql_fetch_array( $getlogResult );
		extract( $loginfo, EXTR_OVERWRITE ); 
	
	/* if log is untitled give it a title */
	if ( $logTitle == "" ) {
		$logTitle = "Untitled";
	}
	
?>

	<div class="body">
	
		<span class="fontTitle">
			<? printCrewName( $loginfo['logAuthor'], "rank", "noLink" ); ?>&rsquo;s Personal Log - 
			<? printText( $logTitle ); ?>
		</span><br /><br />
		
		<span class="fontNormal postDetails">
		<div align="center">
		
		<?
		
			/* point the previous and next post buttons to the correct posts */
		
			$idNumbers = array();
			
			while ( $myrow = mysql_fetch_array( $getlogsResult ) ) {
				$idNumbers[] = $myrow['logid'];
			}
			
			$arrayCount = count($idNumbers) -1;
			
			foreach( $idNumbers as $key => $value ) {
				if( $pl_id == $value ) {
					
					$nextKey = $key+1;
					$prevKey = $key-1;
			
				/* display the previous and next links in the post details box */
				if( $prevKey >= 0 && $idNumbers[$prevKey] != '' ) {
						echo "<a href='$webLocation/index.php?page=log&id=$idNumbers[$prevKey]'><img src='$webLocation/images/previous.png' alt='Previous Entry' border='0' class='image' /></a>";
					} if( ( $prevKey >= 0 && $idNumbers[$prevKey] != '' ) && ( $nextKey <= $arrayCount && $idNumbers[$nextKey] != '' ) ) {
						echo "&nbsp;";
					} if( $nextKey <= $arrayCount && $idNumbers[$nextKey] != '' ) {
						echo "<a href='$webLocation/index.php?page=log&id=$idNumbers[$nextKey]'><img src='$webLocation/images/next.png' alt='Next Entry' border='0' class='image' /></a>";
					}
				}
			}
		
		?>
				
				<br />
				<b>Log Details</b><br />
				<?
			
				if(
					in_array("m_logs2", $sessionAccess) ||
					(in_array("m_logs1", $sessionAccess) && $sessionCrewid == $logAuthor)
				) {
					echo "<a href='" . $webLocation . "admin.php?page=manage&sub=logs&id=" . $pl_id . "' class='edit'><b>Edit</b></a>";
				}
				
				if(in_array("m_logs2", $sessionAccess))
				{
					echo "&nbsp; &middot; &nbsp;";
	
				?>	
	
					<script type="text/javascript">
						document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=logs&remove=<?=$pl_id;?>\" class=\"delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this personal log?')\">Delete</a>" );
					</script>
					<noscript>
						<a href="<?=$webLocation;?>admin.php?page=manage&sub=logs&remove=<?=$pl_id;?>" class="delete"><b>Delete</b></a>
					</noscript>
					
				<?
					
					if( $loginfo['logStatus'] == "pending" )
					{
						echo "&nbsp; &middot; &nbsp;";
						echo "<a href='" . $webLocation . "admin.php?page=manage&sub=activate'><b>Activate</b></a>";
					}
				}
				
				?><p></p>
			</div> <!-- close the centering div -->
			
			<table>
				<tr>
					<td class="tableCellLabel">Title</td>
					<td>&nbsp;</td>
					<td><? printText( $logTitle ); ?></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Author</td>
					<td>&nbsp;</td>
					<td><? printCrewName( $logAuthor, "rank", "link" ); ?></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Posted</td>
					<td>&nbsp;</td>
					<td><?=dateFormat( "medium", $logPosted );?></td>
				</tr>
			</table>
		
		</span>
	
		<? printText( $logContent ); ?>
	
	</div> <!--Close Div content class tag-->
	
<? } ?>