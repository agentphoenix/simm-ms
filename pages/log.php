<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: Nathan Wharry [ mail@herschwolf.net ]
File: pages/log.php
Purpose: To display the individual personal logs

System Version: 2.5.6
Last Modified: 2008-02-04 1747 EST
**/

/* define the page class */
$pageClass = "simm";
$pl_id = $_GET['id'];

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

if( isset( $pl_id ) && !is_numeric( $pl_id ) ) {
	errorMessageIllegal( "view personal log page" );
	exit();
}

/* get post id for individual message display */
if( isset( $pl_id ) ) {

	/* pull all the information relating to the post */
	$getlog = "SELECT * FROM sms_personallogs ";
	$getlog.= "WHERE logid = '$pl_id' LIMIT 1";
	$getlogResult = mysql_query ( $getlog );
	
	/* pull all posts to create the next and prev post links */
	$getlogs = "SELECT logid ";
	$getlogs.= "FROM sms_personallogs ";
	$getlogs.= "WHERE logStatus = 'activated' ";
	$getlogs.= "ORDER BY logPosted ASC";
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
			<? printCrewName( $loginfo['logAuthor'], "rank", "noLink" ); ?>'s Personal Log - 
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
			
			foreach( $idNumbers as $key => $value ) {
				if( $pl_id == $value ) {
					
					$nextKey = $key+1;
					$prevKey = $key-1;
			
				/* display the previous and next links in the post details box */
				if( $idNumbers[$prevKey] != '' ) {
						printText ( "<a href='$webLocation/index.php?page=log&id=$idNumbers[$prevKey]'><img src='$webLocation/images/previous.png' alt='Previous Entry' border='0' /></a>" );
					} if( ($idNumbers[$prevKey] != '') && ($idNumbers[$nextKey] != '') ) {
						echo "&nbsp;";
					} if( $idNumbers[$nextKey] != '' ) {
						printText ( "<a href='$webLocation/index.php?page=log&id=$idNumbers[$nextKey]'><img src='$webLocation/images/next.png' alt='Next Entry' border='0' /></a>" );
					}
				}
			}
		
		?>
				
				<br />
				<b>Log Details</b><br />
				<?
			
				if( in_array( "m_logs", $sessionAccess ) ) {
					echo "<a href='" . $webLocation . "admin.php?page=manage&sub=logs&id=" . $pl_id . "' class='edit'><b>Edit</b></a>";
					echo "&nbsp; &middot; &nbsp;";
	
				?>	
	
					<script type="text/javascript">
						document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=logs&remove=<?=$pl_id;?>\" class=\"delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this personal log?')\">Delete</a>" );
					</script>
					<noscript>
						<a href="<?=$webLocation;?>admin.php?page=manage&sub=logs&remove=<?=$pl_id;?>" class="delete"><b>Delete</b></a>
					</noscript>
					
				<?
					
					if( $loginfo['logStatus'] == "pending" ) {
					
						echo "&nbsp; &middot; &nbsp;";
						echo "<a href='" . $webLocation . "admin.php?page=manage&sub=activate&type=log&id=" . $pl_id . "&action=activate'><b>Activate</b></a>";
					
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