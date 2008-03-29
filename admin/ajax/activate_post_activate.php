<?php

session_start();

if( !isset( $sessionAccess ) ) {
	$sessionAccess = FALSE;
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
	$get = "SELECT * FROM sms_posts WHERE postid = $id LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );

?>
	<h2>Activate Pending Mission Post?</h2>
	<p>Are you sure you want to activate this post?  Once activated, the post will be emailed to the entire crew.</p>
	
	<hr size="1" width="100%" />
	
	<form method="post" action="">
		<h3><? printText( $pendingArray['postTitle'] );?></h3>
		<h4>By <? displayAuthors( $pendingArray['postid'], 'noLink' );?></h4>
		
		<div class="overflow"><? printText( $pendingArray['postContent'] );?></div>
		
		<p></p>
		
		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['postid'];?>" />
			<input type="hidden" name="action_category" value="post" />
			<input type="hidden" name="action_type" value="activate" />
			
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>
	</form>

<?php } /* close the referer check */ ?>