<?php

session_start();

if( !isset( $sessionAccess ) ) {
	$sessionAccess = FALSE;
}

if( !is_array( $sessionAccess ) ) {
	$sessionAccess = explode( ",", $_SESSION['sessionAccess'] );
}

if(in_array("m_departments", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');
	
	$get = "SELECT * FROM sms_departments WHERE deptDisplay = 'y' ORDER BY deptOrder ASC";
	$getR = mysql_query($get);

?>

	<h2>Create New Rank</h2>
	<p>Use the fields below to create a new rank to be used throughout the system. Once the rank is created, you can assign it to characters. <strong class="yellow">Note:</strong> ranks can only be assigned to departments that are set to be displayed. If you want to add a rank that&rsquo;s tied to a hidden department, you will need to set the department display to YES, add the rank, then set the department display back to NO.</p>
	<br />

	<form method="post" action="">
		<table class="hud_guts" cellpadding="3">
			<tr>
				<td class="hudLabel">Name</td>
				<td></td>
				<td><input type="text" class="image" name="rankName" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Order</td>
				<td></td>
				<td><input type="text" class="order" name="rankOrder" value="1" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Display?</td>
				<td></td>
				<td>
					<select name="rankDisplay">
						<option value="y">Yes</option>
						<option value="n">No</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Department</td>
				<td></td>
				<td>
					<select name="rankClass">
						<?php
						
						while($fetch = mysql_fetch_assoc($getR)) {
							extract($fetch, EXTR_OVERWRITE);
							
							echo "<option value='" . $deptid . "'>" . $deptName . "</option>";
							
						}
						
						?>
					</select>
				</td>
			</tr>
			
			<tr>
				<td class="hudLabel">Image</td>
				<td></td>
				<td><input type="text" class="image" name="rankImage" /></td>
			</tr>
		</table>

		<div>
			<input type="hidden" name="action_type" value="create" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>

	</form>

<?php } /* close the referer check */ ?>