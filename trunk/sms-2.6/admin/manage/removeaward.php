<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/removeaward.php
Purpose: Page that allows an admin to remove an award from a player

System Version: 2.6.0
Last Modified: 2007-11-05 1006 EST
**/

/* access check */
if( in_array( "m_removeaward", $sessionAccess ) ) {
	
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['crew'] ) && preg_match( "/^\d+$/", $_GET['crew'], $matches ) == 0 ) {
		errorMessageIllegal( "remove awards page" );
		exit();
	} else {
		/* set the GET variable */
		$crew = $_GET['crew'];
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['award'] ) && preg_match( "/^\d+$/", $_GET['award'], $matches ) == 0 ) {
		errorMessageIllegal( "remove awards page" );
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

		$arrayNumber = $award - 1;

		unset( $arrayAwards[$arrayNumber] );

		/* put the string back together */
		$joinedString = implode( ",", $arrayAwards );
			
		/* dump the comma separated field back into the db */
		$updateAwards = "UPDATE sms_crew SET awards = '$joinedString' WHERE crewid = '$crew' LIMIT 1";
		$result = mysql_query( $updateAwards );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );

		$action = "remove";
		
	}

	if( !$crew ) {
	
?>
	
	<div class="body">
	
		<span class="fontTitle">Remove Award From Crew Member</span><br /><br />
		Please select a crew member from the list below to view and remove an award.<br /><br />
	
		<span class="fontMedium"><b>Active Crew</b></span><br /><br />
		<?
		
		$getCrew = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$getCrew.= "FROM sms_crew AS crew, sms_ranks AS rank WHERE crew.rankid = rank.rankid ";
		$getCrew.= "AND crew.crewType = 'active' ORDER BY crew.rankid ASC";
		$getCrewResult = mysql_query( $getCrew );
		
		while( $userFetch = mysql_fetch_assoc( $getCrewResult ) ) {
			extract( $userFetch, EXTR_OVERWRITE );
			
			echo "<a href='" . $webLocation . "admin.php?page=manage&sub=removeaward&crew=" . $userFetch['crewid'] . "'>" . stripslashes( $userFetch['rankName'] . " " . $userFetch['firstName'] . " " . $userFetch['lastName'] ) . "</a><br />";
			
		}
		
		?>
	
		<br /><br />
		<span class="fontMedium"><b>Inactive Crew</b></span><br /><br />
		<?
		
		$getCrew = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$getCrew.= "FROM sms_crew AS crew, sms_ranks AS rank WHERE crew.rankid = rank.rankid ";
		$getCrew.= "AND crew.crewType = 'inactive' ORDER BY crew.rankid ASC";
		$getCrewResult = mysql_query( $getCrew );
		
		while( $userFetch = mysql_fetch_assoc( $getCrewResult ) ) {
			extract( $userFetch, EXTR_OVERWRITE );
			
			echo "<a href='" . $webLocation . "admin.php?page=manage&sub=removeaward&crew=" . $userFetch['crewid'] . "'>" . stripslashes( $userFetch['rankName'] . " " . $userFetch['firstName'] . " " . $userFetch['lastName'] ) . "</a><br />";
			
		}
		
		?>
		
	</div>
	<? } elseif( $crew ) { ?>
	
	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $updateAwards );
				
		if( !empty( $check->query ) ) {
			$check->message( "crew award", "remove" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Remove Award From <? printCrewName( $crew, "rank", "noLink" ); ?></span><br /><br />
		<b class="fontMedium">
			<a href="<?=$webLocation;?>admin.php?page=manage&sub=removeaward">&laquo; Back to Crew List</a>
		</b>
		<br /><br />
	
		<table>
		<?
	
		$getAwards = "SELECT awards FROM sms_crew WHERE crewid = '$crew' LIMIT 1";
		$getAwardsResult = mysql_query( $getAwards );
		$fetchAwards = mysql_fetch_array( $getAwardsResult );
	
		/* if $myrow isn't empty, continue */
		if( !empty( $fetchAwards['0'] ) ) {
		
			/* explode the string at the comma */
			$awardsRaw = explode( ",", $fetchAwards['0'] );
			
			/*
				Start the loop based on whether there are key/value pairs
				and keep doing 'something' until you run out of pairs
			*/
			foreach( $awardsRaw as $key => $value ) {
	
				$keyAdjusted = $key+1;
				
				/* do the database query */
				$pullAward = "SELECT * FROM sms_awards WHERE awardid = '$value'";
				$pullAwardResult = mysql_query( $pullAward );
	
				/* Start pulling the array and populate the variables */
				while( $awardArray = mysql_fetch_array( $pullAwardResult ) ) {
					extract( $awardArray, EXTR_OVERWRITE );
	
					echo "<tr class='fontNormal'>";
						echo "<td width='70'>";
							echo "<a href='" . $webLocation . "admin.php?page=manage&sub=removeaward&crew=" . $crew . "&award=" . $keyAdjusted . "'>";
							echo "<img src='" . $webLocation . "images/awards/" . $awardImage . "' alt='" . $awardName . "' border='0' class='image' />";
							echo "</a>";
						echo "</td>";
						echo "<td valign='middle'>";
							echo "<a href='" . $webLocation . "admin.php?page=manage&sub=removeaward&crew=" . $crew . "&award=" . $keyAdjusted . "'>";
							printText( $awardName );
							echo "</a>";
						echo "</td>";
					echo "</tr>";
	
				}
	
			}
	
		} else {
			echo "<tr class='fontNormal'>";
				echo "<td colspan='2'>";
					echo "There are no awards to remove!";
				echo "</td>";
			echo "</tr>";
		}
	
		?>
		</table>
	</div>
	
	<? } ?>
	
<? } else { errorMessage( "remove crew award" ); } ?>