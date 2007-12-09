<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/addaward.php
Purpose: Page that allows an admin to add an award for a player

System Version: 2.6.0
Last Modified: 2007-11-05 1005 EST
**/

/* access check */
if( in_array( "m_giveaward", $sessionAccess ) ) {
	
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['crew'] ) && preg_match( "/^\d+$/", $_GET['crew'], $matches ) == 0 ) {
		errorMessageIllegal( "add award page" );
		exit();
	} else {
		/* set the GET variable */
		$crew = $_GET['crew'];
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['award'] ) && preg_match( "/^\d+$/", $_GET['award'], $matches ) == 0 ) {
		errorMessageIllegal( "add award page" );
		exit();
	} else {
		/* set the GET variable */
		$award = $_GET['award'];
	}
		
	/* if an award key is in the URL */
	if( $crew && $award ) {
		
		/* fetch the awards from the db */
		$pullAwards = "SELECT awards FROM sms_crew WHERE crewid = '$crew' LIMIT 1";
		$pullAwardsResult = mysql_query( $pullAwards );
		$stringAwards = mysql_fetch_array( $pullAwardsResult );
		$arrayAwards = explode( ",", $stringAwards['0'] );

		$arrayAwards[] = $award;

		/* put the string back together */
		$joinedString = implode( ",", $arrayAwards );
		
		/* dump the comma separated field back into the db */
		$updateAwards = "UPDATE sms_crew SET awards = '$joinedString' WHERE crewid = '$crew' LIMIT 1";
		$result = mysql_query( $updateAwards );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
			
	} if( !$crew ) {
	
?>
	
	<div class="body">
	
		<span class="fontTitle">Give Award To Crew Member Bio</span><br /><br />
		Please select a crew member from the list below to view and add awards.<br /><br />
	
		<span class="fontMedium"><b>Active Crew</b></span><br /><br />
		<?
		
		$getCrew = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$getCrew.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$getCrew.= "WHERE crew.rankid = rank.rankid AND crew.crewType = 'active' ";
		$getCrew.= "ORDER BY crew.rankid ASC";
		$getCrewResult = mysql_query( $getCrew );
		
		while( $userFetch = mysql_fetch_assoc( $getCrewResult ) ) {
			extract( $userFetch, EXTR_OVERWRITE );
			
			echo "<a href='" . $webLocation . "admin.php?page=manage&sub=addaward&crew=" . $userFetch['crewid'] . "'>" . stripslashes( $userFetch['rankName'] . " " . $userFetch['firstName'] . " " . $userFetch['lastName'] ) . "</a><br />";
			
		}
		
		?>
	
		<br /><br />
		<span class="fontMedium"><b>Inactive Crew</b></span><br /><br />
		<?
		
		$getCrew = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$getCrew.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$getCrew.= "WHERE crew.rankid = rank.rankid AND crew.crewType = 'inactive' ";
		$getCrew.= "ORDER BY crew.rankid ASC";
		$getCrewResult = mysql_query( $getCrew );
		
		while( $userFetch = mysql_fetch_assoc( $getCrewResult ) ) {
			extract( $userFetch, EXTR_OVERWRITE );
			
			echo "<a href='" . $webLocation . "admin.php?page=manage&sub=addaward&crew=" . $userFetch['crewid'] . "'>" . stripslashes( $userFetch['rankName'] . " " . $userFetch['firstName'] . " " . $userFetch['lastName'] ) . "</a><br />";
			
		}
		
		?>
		
	</div>
	<? } elseif( $crew ) { ?>
	
	<div class="body">
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $updateAwards );
		
		if( !empty( $check->query ) ) {
			$check->message( "player award", "add" );
			$check->display();
		}
		
		?>
		<span class="fontTitle">Give Award To <? printCrewName( $crew, "rank", "noLink" ); ?></span><br /><br />
		<b class="fontMedium">
			<a href="<?=$webLocation;?>admin.php?page=manage&sub=addaward">&laquo; Back to Crew List</a>
		</b>
		<br /><br />
	
		<table>
		<?
	
		$getAwards = "SELECT * FROM sms_awards ORDER BY awardid ASC";
		$getAwardsResult = mysql_query( $getAwards );
	
		/* Start pulling the array and populate the variables */
		while( $awardFetch = mysql_fetch_array( $getAwardsResult ) ) {
			extract( $awardFetch, EXTR_OVERWRITE );
	
		?>
	
			<tr class="fontNormal">
				<td width="70" valign="middle">
					<a href="<?=$webLocation;?>admin.php?page=manage&sub=addaward&crew=<?=$crew;?>&award=<?=$awardid;?>">
						<img src="<?=$webLocation;?>images/awards/<?=$awardImage;?>" alt="<?=$awardName;?>" border="0" class="image" />
					</a>
				</td>
				<td valign="top">
					<a href="<?=$webLocation;?>admin.php?page=manage&sub=addaward&crew=<?=$crew;?>&award=<?=$awardid;?>">
						<? printText( $awardName ); ?>
					</a><br />
					<? printText( $awardDesc ); ?>
				</td>
			</tr>
	
		<? } ?>
		</table>
	</div>
	
	<? } ?>

<? } else { errorMessage( "add crew award" ); } ?>