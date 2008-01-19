<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/addaward.php
Purpose: Page that allows an admin to add an award for a player

System Version: 2.6.0
Last Modified: 2008-01-19 1450 EST
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
			$arrayAwards = explode( ";", $stringAwards[0] );
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
	
		<span class="fontTitle">Give Award To Crew Member</span><br /><br />
		Awards are broken into three categories: <b>in character</b>, <b>out of character</b>, and <b>both</b>. Playing characters
		can be given awards from any of those three categories. Non-playing characters can only be given in character awards. To
		begin, please select a crew member from the list below to give them an award.<br /><br />
	
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

						echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=addaward&crew=" . $userFetch['crewid'] . "'>" . stripslashes( $userFetch['rankName'] . " " . $userFetch['firstName'] . " " . $userFetch['lastName'] ) . "</a></li>";

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

						echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=addaward&crew=" . $inactiveFetch['crewid'] . "'>" . stripslashes( $inactiveFetch['rankName'] . " " . $inactiveFetch['firstName'] . " " . $inactiveFetch['lastName'] ) . "</a></li>";

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

						echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=addaward&crew=" . $npcFetch['crewid'] . "'>" . stripslashes( $npcFetch['rankName'] . " " . $npcFetch['firstName'] . " " . $npcFetch['lastName'] ) . "</a></li>";

						}

					?>
				</ul>
			</div>
		</div>
		
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
			
			$getCrew = "SELECT crewType FROM sms_crew WHERE crewid = $crew LIMIT 1";
			$getCrewResult = mysql_query( $getCrew );
			$crewFetch = mysql_fetch_array( $getCrewResult );
		
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
							<?
							
							printCrewName( $crew, "rank", "noLink" );
							
							switch( $crewFetch[0] )
							{
								case 'inactive':
									echo " <b class='orange'>[ Inactive ]";
									break;
								case 'pending':
									echo " <b class='yellow'>[ Pending ]";
									break;
								case 'npc':
									echo " <b class='blue'>[ NPC ]";
									break;
								default:
									echo "";
							}
							
							?>
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
		
		$getCrew = "SELECT crewType FROM sms_crew WHERE crewid = $crew LIMIT 1";
		$getCrewResult = mysql_query( $getCrew );
		$crewFetch = mysql_fetch_array( $getCrewResult );
		
		if( $crewFetch[0] == "npc" ) {
			$tail = "WHERE awardCat = 'ic' ORDER BY awardOrder ASC";
		} else {
			$tail = "ORDER BY awardOrder ASC";
		}
		
		$getAwards = "SELECT * FROM sms_awards $tail";
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