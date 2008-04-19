<?php

session_start();

if( !isset( $sessionAccess ) ) {
	$sessionAccess = FALSE;
}

if( !is_array( $sessionAccess ) ) {
	$sessionAccess = explode( ",", $_SESSION['sessionAccess'] );
}

if(in_array("m_giveaward", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');
	include_once('../../framework/functionsAdmin.php');
	
	if(isset($_GET['c']) && is_numeric($_GET['c']))
	{
		$crew = $_GET['c'];
	}
	
	if(isset($_GET['a']) && is_numeric($_GET['a']))
	{
		$award = $_GET['a'];
	}
	
	/* get the data */
	$get = "SELECT * FROM sms_awards WHERE awardid = $award LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );
	
	if( file_exists( '../../images/awards/large/' . $pendingArray['awardImage'] ) ) {
		$image = $webLocation . 'images/awards/large/' . $pendingArray['awardImage'];
	} else {
		$image = $webLocation . 'images/awards/' . $pendingArray['awardImage'];
	}

?>

	<h2>Remove Award?</h2>
	<p>Are you sure you want to remove this award from <? printCrewName($crew, 'noRank', 'noLink');?>? This action cannot be undone!</p>
	
	<hr size="1" />
	
	<form method="post" action="admin.php?page=manage&sub=addaward&crew=<?=$crew;?>">
		<h2><? printText( $pendingArray['awardName'] );?></h2>
		<h4>
			<img src="<?=$image;?>" alt="<?=$pendingArray['awardName'];?>" border="0" style="float:left; padding-right:10px;" />
			<? printText( $pendingArray['awardDesc'] );?>
		</h4>
		<div style="clear:both;"></div>
		
		<h3>Recipient: <? printCrewName( $crew, 'rank', 'noLink' );?></h3>
		
		<p></p>
		
		<div>
			<input type="hidden" name="action_award" value="<?=$award;?>" />
			<input type="hidden" name="action_crew" value="<?=$crew;?>" />
			<input type="hidden" name="action_type" value="remove" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>

	</form>

<?php } /* close the referer check */ ?>