<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/removeaward.php
Purpose: Page that allows an admin to remove an award from a player

System Version: 2.6.0
Last Modified: 2008-01-19 1450 EST
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
	if( isset($crew) && isset($award) ) {
		
		/* fetch the awards from the db */
		$pullAwards = "SELECT awards FROM sms_crew WHERE crewid = '$crew' LIMIT 1";
		$pullAwardsResult = mysql_query( $pullAwards );
		$stringAwards = mysql_fetch_array( $pullAwardsResult );
		$arrayAwards = explode( ";", $stringAwards[0] );
		
		unset( $arrayAwards[$award] );

		/* put the string back together */
		$joinedString = implode( ";", $arrayAwards );
		
		if( !get_magic_quotes_gpc() ) {
			$string = addslashes( $joinedString );
		} else {
			$string = $joinedString;
		}
			
		/* dump the comma separated field back into the db */
		$updateAwards = "UPDATE sms_crew SET awards = '$string' WHERE crewid = '$crew' LIMIT 1";
		$result = mysql_query( $updateAwards );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );

		$action = "remove";
		
	}

	if( !isset($crew) ) {
		
		/* active crew */
		$getActive = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$getActive.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$getActive.= "WHERE crew.rankid = rank.rankid AND crew.crewType = 'active' ";
		$getActive.= "ORDER BY crew.rankid ASC";
		$getActiveResult = mysql_query( $getActive );
		$activeCount = mysql_num_rows( $getActiveResult );
		
		/* inactive crew */
		$getInactive = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$getInactive.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$getInactive.= "WHERE crew.rankid = rank.rankid AND crew.crewType = 'inactive' ";
		$getInactive.= "ORDER BY crew.rankid ASC";
		$getInactiveResult = mysql_query( $getInactive );
		$inactiveCount = mysql_num_rows( $getInactiveResult );
		
		/* npcs */
		$getNPC = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$getNPC.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$getNPC.= "WHERE crew.rankid = rank.rankid AND crew.crewType = 'npc' ";
		$getNPC.= "ORDER BY crew.rankid ASC";
		$getNPCResult = mysql_query( $getNPC );
		$npcCount = mysql_num_rows( $getNPCResult );
		
		if( $inactiveCount == 0 ) {
			$disableInactive = "2, ";
		} if( $npcCount == 0 ) {
			$disableNPC = "3";
		}

		$disable = $disableInactive . $disableNPC;
	
?>
	
	<script type="text/javascript">
		$(document).ready(function(){
			$('#container-1 > ul').tabs({ disabled: [<?php echo $disable; ?>] });
		});
	</script>
	
	<div class="body">
	
		<span class="fontTitle">Remove Award From Crew Member</span><br /><br />
		To begin, please select a crew member from the list below to view and remove their awards.<br /><br />
		
		<div id="container-1">
			<ul>
				<li><a href="#one"><span>Active Crew (<?php echo $activeCount;?>)</span></a></li>
				<li><a href="#two"><span>Inactive Crew (<?php echo $inactiveCount;?>)</span></a></li>
				<li><a href="#three"><span>Non-Playing Characters (<?php echo $npcCount;?>)</span></a></li>
			</ul>
			
			<div id="one" class="ui-tabs-container ui-tabs-hide">
				<b class="fontLarge">Active Crew</b>
				<ul class="list-dark">
					<?

					while( $userFetch = mysql_fetch_assoc( $getActiveResult ) ) {
						extract( $userFetch, EXTR_OVERWRITE );

						echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=removeaward&crew=" . $userFetch['crewid'] . "'>" . stripslashes( $userFetch['rankName'] . " " . $userFetch['firstName'] . " " . $userFetch['lastName'] ) . "</a></li>";

						}

					?>
				</ul>
			</div>
			
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<b class="fontLarge">Inactive Crew</b>
				<ul class="list-dark">
					<?

					while( $inactiveFetch = mysql_fetch_assoc( $getInactiveResult ) ) {
						extract( $inactiveFetch, EXTR_OVERWRITE );

						echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=removeaward&crew=" . $inactiveFetch['crewid'] . "'>" . stripslashes( $inactiveFetch['rankName'] . " " . $inactiveFetch['firstName'] . " " . $inactiveFetch['lastName'] ) . "</a></li>";

						}

					?>
				</ul>
			</div>
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<b class="fontLarge">Non-Playing Characters</b>
				<ul class="list-dark">
					<?

					while( $npcFetch = mysql_fetch_assoc( $getNPCResult ) ) {
						extract( $npcFetch, EXTR_OVERWRITE );

						echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=removeaward&crew=" . $npcFetch['crewid'] . "'>" . stripslashes( $npcFetch['rankName'] . " " . $npcFetch['firstName'] . " " . $npcFetch['lastName'] ) . "</a></li>";

						}

					?>
				</ul>
			</div>
		</div>
		
	</div>
	<? } elseif( isset($crew) ) { ?>
	
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
		if( !empty( $fetchAwards[0] ) ) {
		
			/* explode the string at the comma */
			$awardsRaw = explode( ";", $fetchAwards[0] );
			
			/* explode the array again */
			foreach($awardsRaw as $a => $b)
			{
				$awardsRaw[$a] = explode( ",", $b );
			}
			
			/*
				Start the loop based on whether there are key/value pairs
				and keep doing 'something' until you run out of pairs
			*/
			foreach( $awardsRaw as $key => $value ) {
	
				/* do the database query */
				$pullAward = "SELECT * FROM sms_awards WHERE awardid = '$value[0]'";
				$pullAwardResult = mysql_query( $pullAward );
	
				/* Start pulling the array and populate the variables */
				while( $awardArray = mysql_fetch_array( $pullAwardResult ) ) {
					extract( $awardArray, EXTR_OVERWRITE );
	
					echo "<tr class='fontNormal'>";
						echo "<td width='70'>";
							echo "<a href='" . $webLocation . "admin.php?page=manage&sub=removeaward&crew=" . $crew . "&award=" . $key . "'>";
							echo "<img src='" . $webLocation . "images/awards/" . $awardImage . "' alt='" . $awardName . "' border='0' class='image' />";
							echo "</a>";
						echo "</td>";
						echo "<td valign='middle'>";
							echo "<a href='" . $webLocation . "admin.php?page=manage&sub=removeaward&crew=" . $crew . "&award=" . $key . "'>";
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