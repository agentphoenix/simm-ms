<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: update.php
Purpose: New update system that will dynamically pull the right update file based
	on what version of the system is in use

System Version: 2.6.8
Last Modified: 2009-02-09 0907 EST
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
	262,
	263,
	264,
	265,
	266,
	267
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
		$updateVersion = "UPDATE sms_system SET sysVersion = '2.6.8', sysBaseVersion = '2.6', ";
		$updateVersion.= "sysIncrementVersion = '.8', sysLaunchStatus = 'n' WHERE sysid = 1 LIMIT 1";
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
			
			SMS 2.6.8 is an update to latest release of SMS which updates the system with the following changes:
			
			<ul>
				<li>Fixed bug where saved mission posts and joint posts showed the missions in the dropdown twice</li>
				<li>Fixed bug where commas at the end of the character images string caused an additional image to be counted, and when displayed in the gallery, would appear as the full bio page</li>
				<li>Fixed bug where positions and departments on the Positions &amp; Departments page didn&rsquo;t respect the order fields but where ordered by ID instead</li>
				<li>Fixed PHP notice where system UID was&rsquo;t set if the system isn&rsquo;t installed</li>
				<li>Fixed PHP notice on the installation center</li>
				<li>Added note to access pages that if access is given to NPC-2 access key, the user should also be given Bio-3 in order to change ranks for all departments</li>
				<li>Removed images from rank dropdowns in Firefox to fix a bug with department heads having their dropdown menus cut off (Firefox bug)</li>
				<li>Upgraded Facebox to version 1.2</li>
				<li>Updated the character add page to hide and show the username information based on what&rsquo;s clicked and what permissions the player has</li>
				<li>Fixed styling bugs on install pages in IE7</li>
				<li>Updated readme file to reflect updated server requirements</li>
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
			Copyright &copy; <?php echo date('Y'); ?> by <a href="http://www.anodyne-productions.com/" target="_blank">Anodyne Productions</a>
		</div> <!-- close .footer -->
	</div> <!-- close #install -->
</body>
</html>