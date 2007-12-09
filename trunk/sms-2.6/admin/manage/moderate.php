<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/manage/moderate.php
Purpose: Page to show who is moderated and who's not

System Version: 2.5.0
Last Modified: 2007-04-27 1217 EST
**/

/* access check */
if( in_array( "m_moderation", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$action = $_GET['action'];
	$type = $_GET['type'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['crew'] ) && preg_match( "/^\d+$/", $_GET['crew'], $matches ) == 0 ) {
		errorMessageIllegal( "post moderation page" );
		exit();
	} else {
		/* set the GET variable */
		$crew = $_GET['crew'];
	}

	if( $action ) {

		if( $action == "moderate" ) {
			$queryValue = "y";
		} elseif( $action == "unmoderate" ) {
			$queryValue = "n";
		} else {
			errorMessage( "postModeration" );
			exit();
		}

		if( $type == "posts" ) {
			$queryField = "moderatePosts";
			$kind = "mission post moderation flag";
		} elseif( $type == "logs" ) {
			$queryField = "moderateLogs";
			$kind = "personal log moderation flag";
		} elseif( $type == "news" ) {
			$queryField = "moderateNews";
			$kind = "news item moderation flag";
		} else {
			errorMessage( "post moderation" );
			exit();
		}

		$updateMod = "UPDATE sms_crew SET $queryField = '$queryValue' WHERE crewid = '$crew' LIMIT 1";
		$result = mysql_query( $updateMod );

		/* optimize the table */
		optimizeSQLTable( "sms_crew" );

	}

?>

	<div class="body">
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $updateMod );
				
		if( !empty( $check->query ) ) {
			$check->message( $kind, "update" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Crew Post Moderation</span><br /><br />
		Use this page to set the moderation levels for various crew members. These values
		can also be changed from each user's account.<br /><br />
	
			<?
	
			$rowCount = "0";
			$color1 = "rowColor1";
			$color2 = "rowColor2";
	
			?>
			<table>
				<tr class="fontMedium">
					<td><b>Crew Member</b></td>
					<td align="center"><b>Mission Posts</b></td>
					<td align="center"><b>Personal Logs</b></td>
					<td align="center"><b>News Items</b></td>
				</tr>
				<?
	
				$getCrew = "SELECT crewid, moderatePosts, moderateLogs, moderateNews ";
				$getCrew.= "FROM sms_crew WHERE crewType = 'active' ";
				$getCrew.= "ORDER BY positionid, rankid ASC";
				$getCrewResult = mysql_query( $getCrew );
	
				while( $crewMod = mysql_fetch_assoc( $getCrewResult ) ) {
					extract( $crewMod, EXTR_OVERWRITE );
					
					$rowColor = ($rowCount % 2) ? $color1 : $color2; 
	
				?>
				<tr class="fontNormal <?=$rowColor;?>">
					<td><? printCrewName( $crewid, "rank", "noLink" ); ?></td>
					<td align="center">
						<? if( $moderatePosts == "y" ) { ?>
							<a href="<?=$webLocation;?>admin.php?page=manage&sub=moderate&crew=<?=$crewid;?>&type=posts&action=unmoderate">Moderated</a>
						<? } else { ?>
							<a href="<?=$webLocation;?>admin.php?page=manage&sub=moderate&crew=<?=$crewid;?>&type=posts&action=moderate">Not Moderated</a>
						<? } ?>
					</td>
					<td align="center">
						<? if( $moderateLogs == "y" ) { ?>
							<a href="<?=$webLocation;?>admin.php?page=manage&sub=moderate&crew=<?=$crewid;?>&type=logs&action=unmoderate">Moderated</a>
						<? } else { ?>
							<a href="<?=$webLocation;?>admin.php?page=manage&sub=moderate&crew=<?=$crewid;?>&type=logs&action=moderate">Not Moderated</a>
						<? } ?>
					</td>
					<td align="center">
						<? if( $moderateNews == "y" ) { ?>
							<a href="<?=$webLocation;?>admin.php?page=manage&sub=moderate&crew=<?=$crewid;?>&type=news&action=unmoderate">Moderated</a>
						<? } else { ?>
							<a href="<?=$webLocation;?>admin.php?page=manage&sub=moderate&crew=<?=$crewid;?>&type=news&action=moderate">Not Moderated</a>
						<? } ?>
					</td>
				</tr>
				<? $rowCount++; } ?>
			</table>
			
	</div>
	
<? } else { errorMessage( "crew post moderation" ); } ?>