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

?>
	<!-- pull in the right stylesheet -->
	<style type="text/css">
		@import url("skins/<?=$_SESSION['sessionDisplaySkin'];?>/style-misc.css");
	</style>
	
	<h2>Reject Crew Application &ndash; <? printText( $pendingArray['firstName'] . " " . $pendingArray['lastName'] );?></h2>
	<p>Please specify message you want to be sent to the player regarding their rejection.</p>
	<p>Rejection messages can now use wild cards for dynamic elements. For instance, using the <strong class="blue">#rank#</strong> wild card will insert the rank you give them into the email before it is sent. Available wild cards are: <strong>#ship#</strong>, <strong>#position#</strong>, <strong>#player#</strong> (character&rsquo;s name), and <strong>#rank#</strong>.</p>
	
	<form method="post" action="">
		<table>
			<tr>
				<td class="tableCellLabel">Email Message</td>
				<td>&nbsp;</td>
				<td>
					<textarea name="rejectMessage" class="narrowTable" rows="10"><?=stripslashes( $rejectMessage );?></textarea>
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
					<input type="hidden" name="action_type" value="reject" />
					
					<input type="image" src="<?=path_userskin;?>buttons/reject.png" name="activate" class="button" value="Reject" />
				</td>
			</tr>
		</table>
	</form>

<?php } /* close the referer check */ ?>