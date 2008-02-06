<?php

session_start();

/* get the referer */
$ref_array = explode("/", $_SERVER['HTTP_REFERER']);
$count = count($ref_array) -1;
$ref = htmlspecialchars(urldecode($ref_array[$count]));

/* set the page it should be coming from */
$valid = htmlspecialchars(urldecode("admin.php?page=manage&sub=activate_new"));

if($ref == $valid)
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');
	
	define( "path_userskin", $webLocation . "skins/" . $_SESSION['sessionDisplaySkin'] . "/" );

	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	/* get the data */
	$getPendingCrew = "SELECT crewid, firstName, lastName, positionid, rankid ";
	$getPendingCrew.= "FROM sms_crew WHERE crewid = '$id' LIMIT 1";
	$getPendingCrewResult = mysql_query( $getPendingCrew );
	$pendingArray = mysql_fetch_assoc( $getPendingCrewResult );
	
	$ranks = "SELECT rank.rankid, rank.rankName, rank.rankImage, dept.deptColor FROM sms_ranks AS rank, ";
	$ranks.= "sms_departments AS dept WHERE dept.deptClass = rank.rankClass AND dept.deptDisplay = 'y' ";
	$ranks.= "AND rank.rankDisplay = 'y' GROUP BY rank.rankid ORDER BY rank.rankClass, rank.rankOrder ASC";
	$ranksResult = mysql_query( $ranks );
	
	$positions = "SELECT position.positionid, position.positionName, dept.deptName, ";
	$positions.= "dept.deptColor FROM sms_positions AS position, sms_departments AS dept ";
	$positions.= "WHERE position.positionOpen > '0' AND dept.deptDisplay = 'y' AND ";
	$positions.= "dept.deptid = position.positionDept AND dept.deptType = 'playing' ";
	$positions.= "ORDER BY dept.deptOrder, position.positionid ASC";
	$positionsResult = mysql_query( $positions );
	
	$currentPosition = "SELECT position.positionid, position.positionName, dept.deptName, ";
	$currentPosition.= "dept.deptColor FROM sms_positions AS position, sms_departments ";
	$currentPosition.= "AS dept WHERE position.positionid = '$pendingArray[positionid]' ";
	$currentPosition.= "AND position.positionDept = dept.deptid";
	$currentPositionResult = mysql_query( $currentPosition );
	$fetchCurrentPosition = mysql_fetch_assoc( $currentPositionResult );

?>
	<!-- pull in the right stylesheet -->
	<style type="text/css">
		@import url("skins/<?=$_SESSION['sessionDisplaySkin'];?>/style-misc.css");
	</style>
	
	<h2>Accept Crew Application &ndash; <? printText( $pendingArray['firstName'] . " " . $pendingArray['lastName'] );?></h2>
	<p>Please specify the position and rank you want <? printText( $pendingArray['firstName'] . " " . $pendingArray['lastName'] );?> to be accepted at. After that, please specify the message you want to be sent to the player regarding their acceptance.</p>
	<p>Acceptance messages can now use wild cards for dynamic elements. For instance, using the <strong class="blue">#rank#</strong> wild card will insert the rank you give them into the email before it is sent. Available wild cards are: <strong>#ship#</strong>, <strong>#position#</strong>, <strong>#player#</strong> (character&rsquo;s name), and <strong>#rank#</strong>.</p>
	
	<form method="post" action="">
		<table>
			<tr>
				<td class="tableCellLabel">Position</td>
				<td>&nbsp;</td>
				<td>
					<select name="position">
					<?
			
					echo "<option value='" . $fetchCurrentPosition['positionid'] . "' style='color:#" . $fetchCurrentPosition['deptColor'] . ";background-color:#000;'>" . $fetchCurrentPosition['deptName'] . " - " . $fetchCurrentPosition['positionName'] . "</option>";
					
					while( $position = mysql_fetch_array( $positionsResult ) ) {
						extract( $position, EXTR_OVERWRITE );
				
						echo "<option value='" . $position['positionid'] . "' style='color:#" . $deptColor . ";background-color:#000;'>" . $position['deptName'] . " - " . $position['positionName'] . "</option>";
						
					}
					
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Rank</td>
				<td>&nbsp;</td>
				<td>
					<select name="rank">
					<?
			
					while( $rank = mysql_fetch_array( $ranksResult ) ) {
						extract( $rank, EXTR_OVERWRITE );
						
						if( $pendingArray['rankid'] == $rank['rankid'] ) {
							echo "<option value='" . $rankid . "' style='background:#000 url( images/ranks/default/" . $rankImage . " ) no-repeat 0 100%; height:40px; color:#" . $deptColor . ";' selected>" . $rankName . "</option>";
						} else {
							echo "<option value='" . $rankid . "' style='background:#000 url( images/ranks/default/" . $rankImage . " ) no-repeat 0 100%; height:40px; color:#" . $deptColor . ";'>" . $rankName . "</option>";
						}
						
					}
					
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Email Message</td>
				<td>&nbsp;</td>
				<td>
					<textarea name="acceptMessage" class="narrowTable" rows="10"><?=stripslashes( $acceptMessage );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td>
					<input type="hidden" name="action_id" value="<?=$pendingArray['crewid'];?>" />
					<input type="hidden" name="action_category" value="user" />
					<input type="hidden" name="action_type" value="accept" />
					
					<input type="image" src="<?=path_userskin;?>buttons/approve.png" name="activate" class="button" value="Approve" />
				</td>
			</tr>
		</table>
	</form>

<?php } /* close the referer check */ ?>