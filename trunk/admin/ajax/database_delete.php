<?php

session_start();

if( !isset( $sessionAccess ) ) {
	$sessionAccess = FALSE;
}

if( !is_array( $sessionAccess ) ) {
	$sessionAccess = explode( ",", $_SESSION['sessionAccess'] );
}

if(in_array("m_database1", $sessionAccess) || in_array("m_database2",$sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');
	
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	/* get the data */
	$get = "SELECT * FROM sms_menu_items WHERE menuid = $id LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );

?>

	<h2>Delete Database Entry?</h2>
	<p>Use the fields below to provide details for your new mission. <strong class="yellow">Note:</strong> Mission images <strong>must</strong> be located in the <em>images/missionimages</em> directory. All you need to do is specify the image in that directory.</p>
	<br />

<form method="post" action="">
<table cellpadding="0" cellspacing="3">
	<tr>
		<td>
			<span class="fontNormal"><b>Order</b></span><br />
			<input type="text" class="order" name="dbOrder" maxlength="4" value="99" />
		</td>
		<td>
			<span class="fontNormal"><b>Display?</b></span><br />
			<select name="dbDisplay">
				<option value="y">Yes</option>
				<option value="n">No</option>
			</select>
		</td>
		<td rowspan="5" valign="top" align="center" width="5">&nbsp;</td>
		<td rowspan="5" valign="top" align="center" width="70%">
			<span class="fontNormal"><b>Content</b></span><br />
			<span class="fontSmall">* used only for database entries, not URL forwarding</span><br />
			<textarea name="dbContent" rows="12" class="desc"></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<span class="fontNormal"><b>Title</b></span><br />
			<input type="text" class="name" name="dbTitle" maxlength="100" />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<span class="fontNormal"><b>Short Description</b></span><br />
			<input type="text" class="name" name="dbDesc" />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<span class="fontNormal"><b>Entry Type</b></span><br />
			<select name="dbType">
				<option value="onsite">URL Forward (On-Site)</option>
				<option value="offsite">URL Forward (Off-Site)</option>
				<option value="entry">Database Entry</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<span class="fontNormal"><b>URL</b></span><br />
			<span class="fontSmall">* used only for URL forwarding entries</span><br />
			<input type="text" class="name" name="dbURL" maxlength="255" />
		</td>
	</tr>
	<tr>
		<td colspan="2"></td>
		<td align="center">&nbsp;</td>
		<td align="center">
			<input type="image" src="<?=path_userskin;?>buttons/create.png" class="button" name="action_create" value="Create" />
		</td>
	</tr>
</table>

<div>
	<input type="hidden" name="action_id" value="<?=$pendingArray['menuid'];?>" />
	<input type="hidden" name="action_type" value="delete" />
	
	<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
</div>

</form>

<?php } /* close the referer check */ ?>