<?php

session_start();

if( !isset( $sessionAccess ) ) {
	$sessionAccess = FALSE;
}

if( !is_array( $sessionAccess ) ) {
	$sessionAccess = explode( ",", $_SESSION['sessionAccess'] );
}

if(in_array("x_menu", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsAdmin.php');
	include_once('../../framework/functionsUtility.php');

	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	/* get the data */
	$get = "SELECT * FROM sms_menu_items WHERE menuid = $id LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );
	
	switch($pendingArray['menuCat'])
	{
		case 'main':
			$section = "Main Navgiation";
			break;
		case 'general':
			$section = "General System menus";
			break;
		case 'admin':
			$section = "Administration System menus";
			break;
	}

?>
	<h2>Delete Menu Item?</h2>
	<p>Are you sure you want to delete the <strong class="orange"><? printText($pendingArray['menuTitle']);?></strong> menu item from the <?=$section;?>?  This action cannot be undone and could cause problems with SMS! If you are unsure, we recommend that you simply turn the menu item&rsquo;s availability to OFF.</p>
	
	<form method="post" action="">
		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['menuid'];?>" />
			<input type="hidden" name="action_type" value="delete" />
			
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>
	</form>

<?php } /* close the referer check */ ?>