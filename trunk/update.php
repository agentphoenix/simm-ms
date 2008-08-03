<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: update.php
Purpose: New update system that will dynamically pull the right update file based
	on what version of the system is in use

System Version: 2.6.0
Last Modified: 2008-06-15 1432 EST
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
	256
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
					/* echo "update/" . $value2 . ".php<br />"; */
					include_once( "update/" . $value2 . ".php" );
				}
			}
		}
	
		/** UPDATE THE VERSION IN THE DATABASE **/
		$updateVersion = "UPDATE sms_system SET sysVersion = '2.6.0', sysBaseVersion = '2.6', ";
		$updateVersion.= "sysIncrementVersion = '.0', sysLaunchStatus = 'n' WHERE sysid = 1 LIMIT 1";
		$updateVersionResult = mysql_query( $updateVersion );
		
		break;
	case 3:

		/** PULL IN THE UPDATE FILE **/
		require_once( "update/starbase.php" );
		
		break;
	case 99:
		
		$sql = "SHOW COLUMNS FROM sms_globals";
		$result = mysql_query($sql);
		
		while($fetch = mysql_fetch_array($result)) {
			extract($fetch, EXTR_OVERWRITE);
			
			$array[] = $fetch[0];
			
		}
		
		echo '<pre>';
		print_r($array);
		echo '</pre>';
		
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
			
			SMS 2.6 is a major update to SMS which not only fixes a handful of existing bugs, but it also enhances existing features as well as offering brand new functionality. A complete list change log is available after the system has been updated in the Reports > Version History page. Some of the major changes in this version include:
			
			<ul>
				<li>Departmental Databases</li>
				<li>Ability for users to edit their own posts and logs</li>
				<li>Ability for admins to set the group access defaults (CO, XO, Department Head, Standard Player)</li>
				<li>Private news items</li>
				<li>Built-in stardate script</li>
				<li>Personalized menu</li>
				<li>New awards system that sends nominated awards to a queue for CO review</li>
				<li>Better Apache compatibility</li>
				<li>Brand new activation page</li>
				<li>Slick new manifest page</li>
				<li>Cleaned up Private Messages</li>
				<li>Using the jQuery Javascript library for better tabs, lightbox functionality, and modal windows</li>
				<li>Two dozen existing bugs fixed</li>
				<li>Better security</li>
				<li>And much more!</li>
			</ul>
			
			<h1><a href="update.php?step=2&version=<?=$urlVersion;?>">Next Step &raquo;</a></h1>
			
			<? } elseif( $step == "2" ) { ?>
			
			The changes have been made to your system.  Please make sure the necessary files are uploaded to your server.  If you still experience problems with any of the issues that have been fixed, please report them on the Anodyne Support Forum.<br /><br />
			
			One of the changes to SMS 2.6 required that the ranks table be blown away and re-built. The script makes every effort to update every crew member from the old rank data to the new rank data, but you may find that a handful of crew members have the wrong rank (and therefore the wrong rank image) and may require manual editing. We apologize for this inconvenience.<br /><br />
			
			<b>Note:</b> If you were logged in to your site, you may receive an error why trying to go to the Control Panel. To correct this, please log back in to your site.

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