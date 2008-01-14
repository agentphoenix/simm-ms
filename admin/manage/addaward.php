<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/addaward.php
Purpose: Page that allows an admin to add an award for a player

System Version: 2.6.0
Last Modified: 2008-01-14 1818 EST
**/

/* access check */
if( in_array( "m_giveaward", $sessionAccess ) ) {
	
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$action = $_POST['action_add_x'];
	
	/* do some checking to make sure someone's not trying to do a SQL injection */
	if( isset( $_GET['crew'] ) && !is_numeric( $_GET['crew'] ) ) {
		errorMessageIllegal( "add award page" );
		exit();
	} elseif( isset( $_GET['crew'] ) && is_numeric( $_GET['crew'] ) ) {
		$crew = $_GET['crew'];
	}
	
	if( isset( $_GET['award'] ) && !is_numeric( $_GET['award'] ) ) {
		errorMessageIllegal( "add award page" );
		exit();
	} elseif( isset( $_GET['award'] ) && is_numeric( $_GET['award'] ) ) {
		$award = $_GET['award'];
	}
		
	/* if an award key is in the URL */
	if( isset( $action ) ) {
		/* define the POST vars */
		$giveCrew = $_POST['crew'];
		$giveAward = $_POST['award'];
		$giveReason = $_POST['reason'];
		
		if( !get_magic_quotes_gpc() ) {
			$reason = addslashes( $giveReason );
		} else {
			$reason = $giveReason;
		}
		
		/* fetch the awards from the db */
		$pullAwards = "SELECT awards FROM sms_crew WHERE crewid = '$crew' LIMIT 1";
		$pullAwardsResult = mysql_query( $pullAwards );
		$stringAwards = mysql_fetch_array( $pullAwardsResult );
		
		/* don't explode the array if there's nothing there to start with */
		if( !empty( $stringAwards[0] ) ) {
			$arrayAwards = explode( ",", $stringAwards['0'] );
		}
		
		/* get the date info from PHP */
		$now = getdate();
		
		/* build the new award entry */
		$arrayAwards[] = $giveAward . "," . $now[0] . "," . $giveReason;

		/* put the string back together */
		$joinedString = implode( ";", $arrayAwards );
		
		/* dump the comma separated field back into the db */
		$updateAwards = "UPDATE sms_crew SET awards = '$joinedString' WHERE crewid = '$giveCrew' LIMIT 1";
		$result = mysql_query( $updateAwards );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
			
	} if( !isset( $crew ) ) {
	
?>
	
	<div class="body">
	
		<span class="fontTitle">Give Award To Crew Member</span><br /><br />
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
	<? } elseif( isset( $crew ) ) { ?>
	
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
		
		<?php
		
		if( isset( $award ) ) {
			
			$getAward = "SELECT * FROM sms_awards WHERE awardid = $award LIMIT 1";
			$getAwardResult = mysql_query( $getAward );
			$awardFetch = mysql_fetch_assoc( $getAwardResult );
		
		?>
		
		<div class="update-new notify-normal">
			<a href="<?=$webLocation;?>admin.php?page=manage&sub=addaward&crew=<?=$crew;?>"><b style="float:right; padding-right:.5em;" class="fontLarge">x</b></a>
			<b class="fontLarge">Confirm Giving Award</b><br />
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=addaward&crew=<?=$crew;?>">
				<table>
					<tr>
						<td class="tableCellLabel">Recipient</td>
						<td></td>
						<td>
							<? printCrewName( $crew, "rank", "noLink" ); ?>
							<input type="hidden" name="crew" value="<?=$crew;?>" />
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Award</td>
						<td></td>
						<td>
							<? printText( $awardFetch['awardName'] ); ?>
							<input type="hidden" name="award" value="<?=$awardFetch['awardid'];?>" />
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Reason</td>
						<td></td>
						<td><textarea name="reason" rows="5"></textarea></td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td><input type="image" src="<?=path_userskin;?>buttons/add.png" name="action_add" value="Add" class="button" /></td>
				</table>
			</form>
		</div><br />
			
		<? } ?>
		
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