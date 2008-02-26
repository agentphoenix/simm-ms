<?php

session_start();

if( !isset( $sessionAccess ) ) {
	$sessionAccess = "";
}

if( !is_array( $sessionAccess ) ) {
	$sessionAccess = explode( ",", $_SESSION['sessionAccess'] );
}

if(in_array("m_missions", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');

?>
	<h2>Create New Mission</h2>

	
	<form method="post" action="">
		<table class="hud_guts" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<td class="hudLabel">Title</td>
				<td></td>
				<td><input type="text" class="image" name="missionTitle" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Order</td>
				<td></td>
				<td><input type="text" class="color" name="missionOrder" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Status</td>
				<td></td>
				<td>
					<select name="missionStatus">
						<option value="upcoming">Upcoming Mission</option>
						<option value="current">Current Mission</option>
						<option value="completed">Completed Mission</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="hudLabel">Image</td>
				<td></td>
				<td>
					<span class="fontSmall">images/missionimages/</span><br /><input type="text" class="image" name="missionImage" maxlength="50" />
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Start Date</td>
				<td></td>
				<td><input type="text" class="date" name="missionStart" value="0000-00-00 00:00:00" /></td>
			</tr>
			<tr>
				<td class="hudLabel">End Date</td>
				<td></td>
				<td><input type="text" class="date" name="missionEnd" value="0000-00-00 00:00:00" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Description</td>
				<td></td>
				<td><textarea name="missionDesc" rows="7"></textarea></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td height="25" colspan="2"></td>
				<td height="25">
					<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="action_create" value="Create" />
				</td>
			</tr>
		</table>
	</form>

<?php } /* close the access check */ ?>