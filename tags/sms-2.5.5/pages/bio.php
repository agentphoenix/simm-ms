<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/bio.php
Purpose: Page to display the requested bio

System Version: 2.5.2
Last Modified: 2007-08-01 1115 EST
**/

/* define the page class and set the vars */
$pageClass = "personnel";
$crew = $_GET['crew'];

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* set the rank variable */
if( isset( $sessionCrewid ) ) {
	$rankSet = $sessionDisplayRank;
} else {
	$rankSet = $rankSet;
}

$getCrew = "SELECT * FROM sms_crew WHERE crewid = '$crew' LIMIT 1";
$getCrewResult = mysql_query( $getCrew );

while( $fetchCrew = mysql_fetch_array( $getCrewResult ) ) {
	extract( $fetchCrew, EXTR_OVERWRITE );
	
	/* get the rank information */
	$getRank = "SELECT rankName, rankImage FROM sms_ranks WHERE rankid = '$fetchCrew[rankid]'";
	$getRankResult = mysql_query( $getRank );
	$fetchRank = mysql_fetch_assoc( $getRankResult );

	/* get the latest logs for the user */
	$getLogs = "SELECT * FROM sms_personallogs WHERE logStatus = 'activated' AND logAuthor = '$crew' ";
	$getLogs.= "ORDER BY logPosted DESC LIMIT $bioShowLogsNum";
	$getLogsResult = mysql_query( $getLogs );
	$NumLogs = mysql_num_rows( $getLogsResult );

	/* get the latest posts for the user */
	$getPosts = "SELECT post.*, mission.missionid, mission.missionTitle ";
	$getPosts.= "FROM sms_posts AS post, sms_missions AS mission ";
	$getPosts.= "WHERE post.postMission = missionid AND post.postStatus = 'activated' AND ( postAuthor LIKE '$crew,%' OR ";
	$getPosts.= "postAuthor LIKE '%,$crew' OR postAuthor LIKE '%,$crew,%' OR postAuthor = '$crew' ) ";
	$getPosts.= "ORDER BY post.postPosted DESC LIMIT $bioShowPostsNum";
	$getPostsResult = mysql_query( $getPosts );
	$NumPosts = mysql_num_rows( $getPostsResult );

?>

<div class="body">
	<span class="fontTitle">
		<?
		
		if( !empty( $fetchCrew['rankid'] ) ) {
			printCrewName( $fetchCrew['crewid'], "rank", "noLink" );
		} else {
			printCrewName( $fetchCrew['crewid'], "noRank", "noLink" );
		}
		
		?>
	</span>
	&nbsp;&nbsp;
	<? if( $fetchCrew['crewType'] == "pending" ) { ?><b class="yellow">[ Activation Pending ]</b><? } ?>
	
	<? if( $loa == "1" ) { ?><br /><b class="red">[ On Leave of Absence ]</b><? } ?>
	<? if( $loa == "2" ) { ?><br /><b class="orange">[ On Extended Leave of Absence ]</b><? } ?>
	<? if( $fetchCrew['crewType'] == "npc" ) { ?><br /><b class="blue">[ Non-Playing Character ]</b><? } ?>
	
	<br /><br />
	
	<div class="bioImage">
		
		<? if( !empty( $fetchCrew['image'] ) ) { ?>
			<div class="pic">
				<img src="<?=$image;?>" alt="" border="0" />
			</div>
		<? } if( !empty( $fetchCrew['crewType'] ) ) { ?>
			<div class="rank">
				<img src="<?=$webLocation;?>images/ranks/<?=$rankSet;?>/<?=$fetchRank['rankImage'];?>" alt="" />
			</div>
		<? } ?>
		
	</div>
	
	<table class="narrowTable">
		
		<? if( $contactInfo == "y" && isset( $sessionCrewid ) && ( $fetchCrew['crewType'] == "active" || $fetchCrew['crewType'] == "inactive" || $fetchCrew['crewType'] == "pending") ) { ?>
		<tr>
			<td colspan="3" align="center" class="fontLarge"><b>Player Information</b></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Player Name</td>
			<td>&nbsp;</td>
			<td><?=$realName;?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Email Address</td>
			<td>&nbsp;</td>
			<td><?=$email;?></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td class="fontNormal">
				<a href="<?=$webLocation;?>admin.php?page=post&sub=message&id=<?=$crewid;?>">
					Send a Private Message
				</a>
			</td>
		</tr>
		
		<? if( !empty( $aim ) || !empty( $msn ) || !empty( $yim ) || !empty( $icq ) ) { ?>
		<tr>
			<td colspan="3" height="10"></td>
		</tr>
		<? } ?>
		
		<? if( !empty( $aim ) ) { ?>
		<tr>
			<td class="tableCellLabel">AIM</td>
			<td>&nbsp;</td>
			<td><?=$aim;?></td>
		</tr>
		<? } if( !empty( $msn ) ) { ?>
		<tr>
			<td class="tableCellLabel">MSN</td>
			<td>&nbsp;</td>
			<td><?=$msn;?></td>
		</tr>
		<? } if( !empty( $yim ) ) { ?>
		<tr>
			<td class="tableCellLabel">Yahoo!</td>
			<td>&nbsp;</td>
			<td><?=$yim;?></td>
		</tr>
		<? } if( !empty( $icq ) ) { ?>
		<tr>
			<td class="tableCellLabel">ICQ</td>
			<td>&nbsp;</td>
			<td><?=$icq;?></td>
		</tr>
		<? } ?>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<? } /* close the logic that checks if the user wants their info shown */ ?>
		
		<tr>
			<td colspan="3" align="center" class="fontLarge"><b>Character Information</b></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Name</td>
			<td>&nbsp;</td>
			<td><? printText( $firstName . " " . $middleName . " " . $lastName ); ?></td>
		</tr>
		
		<? if( !empty( $fetchCrew['rankid'] ) ) { ?>
		<tr>
			<td class="tableCellLabel">Rank</td>
			<td>&nbsp;</td>
			<td><?=$fetchRank['rankName'];?></td>
		</tr>
		<? } ?>
		
		<tr>
			<td class="tableCellLabel">Position</td>
			<td>&nbsp;</td>
			<td><? printPlayerPosition( $fetchCrew['crewid'], $positionid, "" ); ?></td>
		</tr>
		<? if( !empty( $positionid2 ) ) { ?>
		<tr>
			<td class="tableCellLabel">Second Position</td>
			<td>&nbsp;</td>
			<td><? printPlayerPosition( $fetchCrew['crewid'], $positionid2, "2" ); ?></td>
		</tr>
		<? } ?>
		<tr>
			<td class="tableCellLabel">Gender</td>
			<td>&nbsp;</td>
			<td><? printText( $gender ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Species</td>
			<td>&nbsp;</td>
			<td><? printText( $species ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Age</td>
			<td>&nbsp;</td>
			<td><?=$age;?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		
		<tr>
			<td colspan="3" align="center" class="fontLarge"><b>Physical Appearance</b></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Height</td>
			<td>&nbsp;</td>
			<td><?=$heightFeet;?>' <?=$heightInches;?>"</td>
		</tr>
		<tr>
			<td class="tableCellLabel">Weight</td>
			<td>&nbsp;</td>
			<td><?=$weight;?> lbs.</td>
		</tr>
		<tr>
			<td class="tableCellLabel">Eye Color</td>
			<td>&nbsp;</td>
			<td><? printText( $eyeColor ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Hair Color</td>
			<td>&nbsp;</td>
			<td><? printText( $hairColor ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Physical Description</td>
			<td>&nbsp;</td>
			<td><? printText( $physicalDesc ); ?></td>
		</tr>
	</table>
	
	<table>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td colspan="3" align="center" class="fontLarge"><b>Personality &amp; Traits</b></td>
		</tr>
		<tr>
			<td colspan="3" class="fontMedium"><b>General Overview</b></td>
		</tr>
		<tr>
			<td colspan="3"><? printText( $personalityOverview ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td colspan="3" class="fontMedium"><b>Strengths &amp; Weaknesses</b></td>
		</tr>
		<tr>
			<td colspan="3"><? printText( $strengths ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td colspan="3" class="fontMedium"><b>Ambitions</b></td>
		</tr>
		<tr>
			<td colspan="3"><? printText( $ambitions ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td colspan="3" class="fontMedium"><b>Hobbies &amp; Interests</b></td>
		</tr>
		<tr>
			<td colspan="3"><? printText( $hobbies ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Languages</td>
			<td>&nbsp;</td>
			<td><? printText( $languages ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		
		<tr>
			<td colspan="3" align="center" class="fontLarge"><b>Family</b></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Father</td>
			<td>&nbsp;</td>
			<td><? printText( $father ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Mother</td>
			<td>&nbsp;</td>
			<td><? printText( $mother ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Brother(s)</td>
			<td>&nbsp;</td>
			<td><? printText( $brothers ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Sister(s)</td>
			<td>&nbsp;</td>
			<td><? printText( $sisters ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Spouse</td>
			<td>&nbsp;</td>
			<td><? printText( $spouse ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Children</td>
			<td>&nbsp;</td>
			<td><? printText( $children ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Other Family</td>
			<td>&nbsp;</td>
			<td><? printText( $otherFamily ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		
		<tr>
			<td colspan="3" align="center" class="fontLarge"><b>History</b></td>
		</tr>
		<tr>
			<td colspan="3"><? printText( $history ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		
		<tr>
			<td colspan="3" align="center" class="fontLarge"><b>Service Record</b></td>
		</tr>
		<tr>
			<td colspan="3"><? printText( $serviceRecord ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		
		<tr>
			<td colspan="3" align="center">
				<span class="fontLarge"><b>Awards</b></span>
				<? if( in_array( "m_giveaward", $sessionAccess ) || in_array( "m_removeaward", $sessionAccess ) ) { ?>
				<br />
				<span class="fontSmall">
					<? if( in_array( "m_giveaward", $sessionAccess ) ) { ?>
					<a href="<?=$webLocation;?>admin.php?page=manage&sub=addaward">Add Award</a>
					<? } ?>
					
					<? if( in_array( "m_giveaward", $sessionAccess ) && in_array( "m_removeaward", $sessionAccess ) ) { ?>
					&nbsp; &middot &nbsp;
					<? } ?>
					
					<? if( in_array( "m_removeaward", $sessionAccess ) ) { ?>
					<a href="<?=$webLocation;?>admin.php?page=manage&sub=removeaward&crew=<?=$crew;?>">Remove Award</a>
					<? } ?>
				</span>
				<? } ?>
			</td>
		</tr>
		<tr>
			<?php

			/* do the database query */
			$getAwards = "SELECT awards FROM sms_crew WHERE crewid = '$_GET[crew]'";
			$getAwardsResult = mysql_query( $getAwards );
			$fetchAwards = mysql_fetch_array( $getAwardsResult );
			
			/* if $myrow isn't empty, continue */
			if( !empty( $fetchAwards['0'] ) ) {
			
				/* explode the string at the comma */
				$awardsRaw = explode( ",", $fetchAwards['0'] );
				
				/* html to start the table */
				echo "<td colspan='3' class='fontSmall'><table>";
				
				/*
					Start the loop based on whether there are key/value pairs
					and keep doing 'something' until you run out of pairs
				*/
				foreach($awardsRaw as $key => $value) {
					
					/* do the database query */
					$pullAward = "SELECT * FROM sms_awards WHERE awardid = '$value'";
					$pullAwardResult = mysql_query( $pullAward );

					while( $awardArray = mysql_fetch_array( $pullAwardResult ) ) {
						extract( $awardArray, EXTR_OVERWRITE );
				
			?>	
			
			<tr>	
				<td width="70"><img src="<?=$webLocation;?>images/awards/<?=$awardImage;?>" alt="<?=$awardName;?>" border="0" />
				<td><i><? printText( $awardName ); ?></i></td>
				<td><? printText( $awardDesc );?></td>
			</tr>				
			
			<?
			
					}	/* close the while loop */
				}	/* close the foreach loop */

			/* close the table */
			echo "</table></td>";

			} else {
			
			?>
			
			<td colspan="3">No Awards</td>
			
			<? } ?>
		</tr>
		
		<? if( $bioShowPosts == "y" || $bioShowLogs == "y" ) { ?>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td colspan="3" align="center" class="fontLarge"><b>Posting Activity</b></td>
		</tr>
		<? } ?>
		
		<? if ( $bioShowPosts == "y" ) { ?>
		<tr>
			<td colspan="3" height="10">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3" class="fontMedium">
				<b>Recent Posts</b>
				&nbsp;
				<span class="fontSmall">
					<a href="<?=$webLocation;?>index.php?page=userpostlist&crew=<?=$crew;?>">[ Show All Posts ]</a>
				</span>
			</td>
		</tr>
		
		<? if( $NumPosts == "0" ) { ?>
		<tr>
			<td colspan="3">No Posts Recorded</td>
		</tr>
		<? } else { ?>
		<tr>
			<td colspan="3">
				<table>
					<tr>
						<td colspan="4" height="5">&nbsp;</td>
					</tr>
					<tr class="fontSmall">
						<td width="30%"><b>Date</b></td> 
						<td width="25%"><b>Title</b></td>
						<td width="20%"><b>Location</b></td>
						<td width="20%"><b>Timeline</b></td>
					</tr>
	
					<?
					
					while( $postinfo = mysql_fetch_array( $getPostsResult ) ) {
						extract( $postinfo, EXTR_OVERWRITE );
						
						/* define title when no title was entered */
						if ( $postTitle == "" ) {
							$postTitle = "[ Untitled ]";
						}
						
					?>
					
					<tr class="fontSmall">
						<td><?=dateFormat( "medium", $postPosted );?></td> 
						<td><a href="<?=$webLocation;?>index.php?page=post&id=<?=$postid;?>"><? printText( $postTitle ); ?></a></td> 
						<td><? printText( $postLocation ); ?></td>
						<td><? printText( $postTimeline ); ?></td>
					</tr>
					<? } /* close the while statement */ ?>
				</table>
			</td>
		</tr>
	
	<?

		} /* close the else statement to show posts if present */
	} if ( $bioShowLogs == "y" ) {
	
	?>
		
		<tr>
			<td colspan="3" height="10">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3" class="fontMedium">
				<b>Recent Logs</b>
				&nbsp;
				<span class="fontSmall">
					<a href="<?=$webLocation;?>index.php?page=userpostlist&crew=<?=$crew;?>#logs">[ Show All Logs ]</a>
				</span>
			</td>
		</tr>
		
		<? if( $NumLogs == "0" ) { ?>
		<tr>
			<td colspan="3">No Logs Recorded</td>
		</tr>
		<? } else { ?>
		<tr>
			<td colspan="3">
				<table>
					<tr>
						<td colspan="4" height="5">&nbsp;</td>
					</tr>
					<tr class="fontSmall">
						<td width="30%"><b>Date</b></td>
						<td><b>Title</b></td> 
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
	
					<?
					
					while( $loginfo = mysql_fetch_array( $getLogsResult ) ) {
						extract( $loginfo, EXTR_OVERWRITE );
						
						/* define title when no title was entered */
						if( $logTitle == "" ) {
							$logTitle = "[ Untitled ]";
						}
						
					?>
	
					<tr class="fontSmall">
						<td><?=dateFormat( "medium", $logPosted );?></td>
						<td><a href="<?=$webLocation;?>index.php?page=log&id=<?=$logid;?>"><? printText( $logTitle ); ?></a></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					
					<? } /* close the while statement */ ?>
				
				</table>
			</td>
		</tr>
<?

		} /* close the else statement to show logs if present */
	} /* close the full if statement to view logs */

?>

	</table>
	
</div>
<? } ?>