<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: update.php
Purpose: New update system that will dynamically pull the right update file based
	on what version of the system is in use

System Version: 2.6.0
Last Modified: 2007-09-17 1349 EST
**/

/* define the step var */
$step = $_GET['step'];
//$urlVersion = $_GET['version'];
$urlVersion = "2441";

/* array for controlling trailing zeroes */
$versionArray = array(
	0 => "20",
	1 => "21",
	2 => "22",
	3 => "23",
	4 => "24",
	5 => "25",
	6 => "26"
);

/* array with all the possible versions */
$versionsArray = array(
	"230",
	"231",
	"240",
	"241",
	"242",
	"243",
	"244",
	"2441",
	"250",
	"251",
	"2511",
	"252",
	"253",
	"254"
);

/* count the number of items in the versions array */
$versionsCount = count( $versionsArray );

/* make sure the version is formatted right */
if( in_array( $urlVersion, $versionArray ) ) {
	$urlVersion = $urlVersion . "0";
}

/* if there is no step defined, assume they want step 1 */
if( !isset( $step ) ) {
	$step = 1;
}

/* destroy the session if it exists */
session_unset();

/* pull in the globals files */
require_once( '../framework/functionsGlobal.php' );
include_once( '../framework/functionsUtility.php' );

switch( $step ) {

	case 2:
	
		/* if there's not version specified in the URL, try to find out what version it is */
		if( !isset( $urlVersion ) ) {
			
			$getUpdateVersion = "SELECT sysVersion FROM sms_system WHERE sysid = 1 LIMIT 1";
			$getUpdateVersionResult = mysql_query( $getUpdateVersion );
			$updateVersion = mysql_fetch_array( $getUpdateVersionResult );
			
			/* make sure the periods have been removed */
			$urlVersion = str_replace( ".", "", $updateVersion['0'] );
	
		}
		
		/** PULL IN THE UPDATE FILE **/
		foreach( $versionsArray as $key1 => $value1 )
		{
			/* if we're at the right point in the array, start the update code */
			if( $urlVersion == $value1 )
			{
				/* duplicate the versions array */
				$versionsArrayNew = $versionsArray;
				
				/* slice the array so it only includes the files that need to be used */
				$versionsArrayNew = array_slice( $versionsArray, $key1 );
				
				if( $value1 < "2441" )
				{
					$keyAdjust = array_search( "2441", $versionsArrayNew );
					unset( $versionsArrayNew[$keyAdjust] );
					$versionsArrayNew = array_values( $versionsArrayNew );
				}
				
				/* loop through the new array and pull in the update files */
				foreach( $versionsArrayNew as $key2 => $value2 )
				{
					/* pull in the update files sequentially */
					echo "update/" . $value2 . ".php<br />";
					/* require_once( "update/" . $value2 . ".php" ); */
					
					/* delay execution of the next part for 2 seconds */
					sleep(2);
				}
			}
		}
	
		/** UPDATE THE VERSION IN THE DATABASE **/
		$updateVersion = "UPDATE sms_system SET sysVersion = '2.6.0', sysBaseVersion = '2.6', ";
		$updateVersion.= "sysIncrementVersion = '.0', sysLaunchStatus = 'n' WHERE sysid = 1 LIMIT 1";
		//$updateVersionResult = mysql_query( $updateVersion );
		
		break;
	case 3:

		/** PULL IN THE UPDATE FILE **/
		require_once( "update/starbase.php" );
		
		break;

}
/*
?>

<html>
<head>
	<title>SMS 2.6 :: Update</title>
	<link rel="stylesheet" href="update/update.css" type="text/css" />
</head>
<body>
	<div id="install">	
		<div class="header">
			<img src="update/update.jpg" alt="" border="0" />
		</div> <!-- close .header -->
		<div class="content">
			
			<? if( $step == "1" ) { ?>
			
			SMS 2.6 is a minor update that addresses several outstanding bugs in SMS 2.5.x and
			further enhances several features with additional functionality, including:
			
			<ul>
				<li>Added page to add/remove a given access level for the entire crew at the same time</li>
				<li>Added page that gives full listing of a given user&rsquo;s access</li>
				<li>Added user access report link to the full crew listing by the stats link</li>
				<li>Added display of second position (if applicable) to the active crew list</li>
				<li>When the SMS posting system is turned on or off, the system will take actions to make sure the people are either stripped of or given posting access</li>
				<li>Added character set and collation to install (hopefully this will fix problems people were having)</li>
				<li>Fixed bug where if the variables file was written the webLocation variable would be empty</li>
				<li>Fixed bug where textareas would show HTML break tags after updating</li>
				<li>Fixed bug where join page set wrong timestamp</li>
				<li>Added nice message if the join date is empty instead of the 37 years, etc.</li>
				<li>Fixed bug where time wouldn&rsquo;t display if it was 1 day or less</li>
				<li>Updated logic to display date in a nicer fashion</li>
				<li>Improved display for dates less than 1 day</li>
				<li>Added on/off switch control to each menu item</li>
				<li>Fixed bug where error message on login page would extend across entire screen</li>
				<li>Reactivated emailing of password</li>
				<li>Added visual separation between items that need a password to be changed and those that don&rsquo;t in the account managemment page</li>
				<li>Removed username from being listed on the account management page unless the person viewing it is the owner of the account</li>
				<li>Fixed bug where dates wouldn&rsquo;t display by recent posts and logs</li>
				<li>Fixed account bug where admin couldn&rsquo;t change active status of a player</li>
			</ul>
			
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
<? */ ?>