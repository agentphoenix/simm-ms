<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: update.php
Purpose: New update system that will dynamically pull the right update file based
	on what version of the system is in use

System Version: 2.6.3
Last Modified: 2008-10-12 1645 EST
**/

/* define the step var */
if (isset($_GET['step']) && is_numeric($_GET['step']))
{
	$step = $_GET['step'];
}
else
{
	$step = 1;
}

if (isset($_GET['version']) && is_numeric($_GET['version']))
{
	$urlVersion = $_GET['version'];
}
else
{
	$urlVersion = FALSE;
}

/* array for controlling trailing zeroes */
$versionArray = array(
	0 => 20,
	1 => 21,
	2 => 22,
	3 => 23,
	4 => 24,
	5 => 25,
	6 => 26
);

/* array with all the possible versions */
$versionsArray = array(
	230,
	231,
	240,
	241,
	242,
	243,
	244,
	250,
	251,
	2511,
	252,
	253,
	254,
	255,
	256,
	260,
	261,
	262
);

/* count the number of items in the versions array */
$versionsCount = count($versionsArray);

/* make sure the version is formatted right */
if (in_array($urlVersion, $versionArray))
{
	$urlVersion = $urlVersion . 0;
}

/* destroy the session if it exists */
session_unset();

/* pull in the globals files */
require_once('framework/functionsGlobal.php');
include_once('framework/functionsUtility.php');

switch($step)
{
	case 2:
	
		/* if there's not version specified in the URL, try to find out what version it is */
		if( $urlVersion === FALSE ) {
			
			$getUpdateVersion = "SELECT sysVersion FROM sms_system WHERE sysid = 1 LIMIT 1";
			$getUpdateVersionResult = mysql_query( $getUpdateVersion );
			$updateVersion = mysql_fetch_array( $getUpdateVersionResult );
			
			/* make sure the periods have been removed */
			$urlVersion = str_replace(".", "", $updateVersion[0]);
	
		}
		
		if ($urlVersion == 2441)
		{
			$urlVersion = 244;
		}
		
		/** PULL IN THE UPDATE FILE **/
		foreach ($versionsArray as $key1 => $value1)
		{
			/* if we're at the right point in the array, start the update code */
			if ($urlVersion == $value1)
			{
				/* duplicate the versions array */
				$versionsArrayNew = $versionsArray;
				
				/* slice the array so it only includes the files that need to be used */
				$versionsArrayNew = array_slice($versionsArray, $key1);
				
				/* loop through the new array and pull in the update files */
				foreach ($versionsArrayNew as $key2 => $value2)
				{
					/* pull in the update files sequentially */
					include_once( "update/" . $value2 . ".php" );
				}
			}
		}
	
		/** UPDATE THE VERSION IN THE DATABASE **/
		$updateVersion = "UPDATE sms_system SET sysVersion = '2.6.3', sysBaseVersion = '2.6', ";
		$updateVersion.= "sysIncrementVersion = '.3', sysLaunchStatus = 'n' WHERE sysid = 1 LIMIT 1";
		$updateVersionResult = mysql_query( $updateVersion );
		
		break;
	case 3:

		/** PULL IN THE UPDATE FILE **/
		require_once("update/starbase.php");
		
		break;
}

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
			
			SMS 2.6.3 is an update to latest release of SMS which fixes the following issues:
			
			<ul>
				<li>Updated skin location code to work better on Windows machines (local and servers)</li>
				<li>Fixed display issues with character images and tour images in Firefox 3</li>
				<li>Updated reflection script to version 1.9</li>
				<li>Fixed issue with fresh install which made the system think it was running version 2.6.0</li>
				<li>Fixed issue with commas not being able to be used in award reasons (semicolons can still not be used)</li>
				<li>Fixed confusing issue where crew activity report said Today when it was actually within the last 24 hours</li>
				<li>Fixed bug where reset password would reset a password and try to update a password and send an email even if you didn&rsquo;t put anything in the fields</li>
				<li>Fixed bug where &ldquo;login to update this joint post&rdquo; message was missing from joint post saved notification emails</li>
				<li>Fixed issue where two SMS installations on the same domain would cause weird access issues (thanks to Jon Matterson for letting us use his MOD to fix this issue)</li>
				<li>Updated default rank set to use DS9 rank set using alpha channel transparencies, allowing the ranks to look good on any background color</li>
				<li>Fixed bug in ship skin that would cause it to break when switching directories</li>
			</ul>
			
			<h1><a href="update.php?step=2&version=<?=$urlVersion;?>">Next Step &raquo;</a></h1>
			
			<? } elseif( $step == "2" ) { ?>
			
			The changes have been made to your system.  If you still experience problems with any of the issues that have been fixed, please report them on the Anodyne Support Forum.

			<h1>
				<a href="<?=$webLocation;?>index.php?page=main">Return to your site &raquo;</a>
			</h1>
			
			<? } ?>
		</div>
		<div class="footer">
			Copyright &copy; 2005-<?php echo date('Y'); ?> by <a href="http://www.anodyne-productions.com/" target="_blank">Anodyne Productions</a>
		</div> <!-- close .footer -->
	</div> <!-- close #install -->
</body>
</html>