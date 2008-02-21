<?php

session_start();

if( !isset( $sessionAccess ) ) {
	$sessionAccess = "";
}

if( !is_array( $sessionAccess ) ) {
	$sessionAccess = explode( ",", $_SESSION['sessionAccess'] );
}

if(in_array("x_approve_posts", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');

	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	/* get the data */
	$get = "SELECT * FROM sms_posts WHERE postid = '$id' LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );

?>
	<h2>Delete Pending Mission Post?</h2>
	<p>Are you sure you want to delete this post? This action cannot be undone!</p>
	
	<form method="post" action="">
		<table>
			<tr>
				<td class="tableCellLabel">Post Title</td>
				<td></td>
				<td><? printText( $pendingArray['postTitle'] );?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Post Author(s)</td>
				<td></td>
				<td><? displayAuthors( $pendingArray['postid'], 'noLink' );?></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td colspan="2">
				<td>
					<input type="hidden" name="action_id" value="<?=$pendingArray['postid'];?>" />
					<input type="hidden" name="action_category" value="post" />
					<input type="hidden" name="action_type" value="delete" />
					
					<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Delete" />
				</td>
			</tr>
		</table>
	</form>

<?php } /* close the referer check */ ?>