<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: update.php
Purpose: New update system that will dynamically pull the right update file based
	on what version of the system is in use

System Version: 2.5.6
Last Modified: 2008-02-04 1751 EST
**/

/* define the step var */
$step = $_GET['step'];
$urlVersion = $_GET['version'];

/* create version array */
$versionArray = array(
	0 => "20",
	1 => "21",
	2 => "22",
	3 => "23",
	4 => "24",
	5 => "25"
);

/* do some logic to make sure the version is right */
if( in_array( $urlVersion, $versionArray ) ) {
	$urlVersion = $urlVersion . "0";
}

/* do some logic based on the step */
if( !$step ) {
	$step = "1";
}

/* destroy the session  */
session_unset();

/* pull in the DB connection variables */
require_once( 'framework/variables.php' );

/* database connection */
$db = @mysql_connect( "$dbServer", "$dbUser", "$dbPassword" ) or die ( "<b>$dbErrorMessage</b>" );
mysql_select_db( "$dbTable",$db ) or die ( "<b>Unable to select the appropriate database.  Please try again later.</b>" );

/* pull in the globals file */
include_once( 'framework/functionsUtility.php' );

if( $step == "2" ) {

	/* if there's not version specified in the URL, try to find out what version it is */
	if( !$urlVersion ) {
		
		$getUpdateVersion = "SELECT sysVersion FROM sms_system WHERE sysid = '1' LIMIT 1";
		$getUpdateVersionResult = mysql_query( $getUpdateVersion );
		$updateVersion = mysql_fetch_array( $getUpdateVersionResult );

		$urlVersion = str_replace( ".", "", $updateVersion['0'] );

	}
	
	/* making sure to catch anyone using 2.4.4.1 */
	if( $urlVersion == "2441" ) {
		$urlVersion = "244";
	}
	
	/** PULL IN THE UPDATE FILE **/
	require_once( "update/" . $urlVersion . ".php" );

	/** UPDATE THE VERSION IN THE DATABASE **/
	$updateVersion = "UPDATE sms_system SET sysVersion = '2.5.6', sysBaseVersion = '2.5', ";
	$updateVersion.= "sysIncrementVersion = '.6', sysLaunchStatus = 'n' WHERE sysid = 1 LIMIT 1";
	$updateVersionResult = mysql_query( $updateVersion );

} if( $step == "3" ) {

	/** PULL IN THE UPDATE FILE **/
	require_once( "update/starbase.php" );

}

?>

<html>
<head>
	<title>SMS 2.5.6 :: Update</title>
	<link rel="stylesheet" href="update/update.css" type="text/css" />
</head>
<body>
	<div id="install">	
		<div class="header">
			<img src="update/update.jpg" alt="" border="0" />
		</div> <!-- close .header -->
		<div class="content">
			
			<? if( $step == "1" ) { ?>
			
			SMS 2.5.6 fixes an annoying bug where illegal inputs on un-authenticated pages would
			generate an email. The email has been turned off since the content was blocked before
			anything could happen.
			
			<h1><a href="update.php?step=2&version=<?=$urlVersion;?>">Next Step &raquo;</a></h1>
			
			<? } elseif( $step == "2" ) { ?>
			
			The changes have been made to your system.  Please make sure the necessary files
			are uploaded to your server.  If you still experience problems with any of the 
			issues that have been fixed, please report them on the Anodyne Support Forum.<br /><br />
			
			<b>Note:</b> If you were logged in to your site, you may receive an error why trying
			to go to the Control Panel. To correct this, please log back in to your site.

			<h1>
				<a href="<?=$webLocation;?>">Return to your site &raquo;</a>
			</h1>
			
			If your simm is a starbase, you can use this additional step to change the necessary menu items for a starbase setup.
			
			<h1><a href="update.php?step=3&version=<?=$urlVersion;?>">Change Menu Items &raquo;</a></h1>
			
			<? } elseif( $step == "3" ) { ?>
			
			The menu changes have been made to your system. Additional changes can be made to the menus through the Menu Management page.
			
			<h1>
				<a href="<?=$webLocation;?>">Return to your site &raquo;</a>
			</h1>
			
			<? } ?>
		</div>
		<div class="footer">
			Copyright &copy; 2005-<?php echo date('Y'); ?> by <a href="http://www.anodyne-productions.com/" target="_blank">Anodyne Productions</a><br />
			SMS 2 designed by <a href="mailto:anodyne.sms@gmail.com">David VanScott</a>
		</div> <!-- close .footer -->
	</div> <!-- close #install -->
</body>
</html>