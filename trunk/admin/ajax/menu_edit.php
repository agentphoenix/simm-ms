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

?>
	<h2>Edit Menu Item</h2>
	<p>Use the fields below to edit the menu item. If you want more advanced options, you can show the advanced options below. <strong class='orange'>Please use extreme caution when editing menu items. Incorrect modification can cause you to not be able to access the menu items any more!</strong></p>
	
	<hr size="1" width="100%" />
	
	<form method="post" action="">
		<h3><? printText( $pendingArray['postTitle'] );?></h3>
		<h4>By <? displayAuthors( $pendingArray['postid'], 'noLink' );?></h4>
		
		<div class="overflow"><? printText( $pendingArray['postContent'] );?></div>
		
		<p></p>
		
		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['menuid'];?>" />
			<input type="hidden" name="action_type" value="edit" />
			
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>
	</form>

<?php } /* close the referer check */ ?>