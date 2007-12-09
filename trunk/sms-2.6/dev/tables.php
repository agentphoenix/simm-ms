<?php

/**
This utility is distributed by Anodyne Productions for use with the latest 
stable release of the SIMM Management System. Use of this utility with a 
version other than the latest stable versions is not recommended nor supported. 
Questions and support requests should be directed to the Anodyne Forums.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: tables.php
Purpose: Verification utility to ensure all of SMS' tables and
	fields are in place; file should be placed in the root SMS
	directory and run from a web browser

Version: 1.0
Last Modified: 2007-08-21 1039 EST
**/

/* pull in the globals function file to get a db connection */
require_once( '../framework/functionsGlobal.php' );

/* define the variable */
$step = $_GET['step'];

/* set the default step if none is given */
if( !isset( $step ) ) {
	$step = 1;
}

/* function that alters the table if something isn't there */
function alter_table( $table, $field, $statement ) {

	$sql2 = "ALTER TABLE " . $table . " ADD " . $statement;
	$result2 = mysql_query( $sql2 );
	
	echo "<span style='color:#cc0000'>" . $field . " has been added to " . $table . "</span><br />";

}

switch( $step ) {

	case 1:
		echo "This utility will check to make sure that all your SMS database tables exist as they should. The following steps will be taken during verification:";
		
		echo "<ol>";
		echo "<li>Verify that all the necessary database tables exist. If one of the tables doesn't exist, it will be created.</li>";
		echo "<li>Verify that each table has the right fields. If a field doesn't exist, it will be created.</li>";
		echo "</ol>";
		
		echo "<a href='tables.php?step=2'>Click here</a> to continue to the next step.";
		break;
	
	case 2:
	
		/* if the server is running mysql 4 and higher, set the default character set */
		if( $r->ve >= 4 ) {
			$tail = "CHARACTER SET utf8";
		} else {
			$tail = "";
		}
		
		/* create the awards table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_awards` (
		  `awardid` int(4) NOT NULL auto_increment,
		  `awardName` varchar(100) NOT NULL default '',
		  `awardImage` varchar(50) NOT NULL default '',
		  `awardOrder` int(3) NOT NULL default '0',
		  `awardDesc` text NOT NULL,
		  PRIMARY KEY  (`awardid`)
		) " . $tail . " ;" );
		
		/* create the coc table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_coc` (
		  `cocid` int(1) NOT NULL auto_increment,
		  `crewid` int(3) NOT NULL default '0',
		  PRIMARY KEY  (`cocid`)
		) " . $tail . " AUTO_INCREMENT=2 ;" );
		
		/* create the crew table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_crew` (
		  `crewid` int(4) NOT NULL auto_increment,
		  `username` varchar(16) NOT NULL default '',
		  `password` varchar(32) NOT NULL default '',
		  `crewType` enum('active','inactive','pending','npc') NOT NULL default 'active',
		  `email` varchar(64) NOT NULL default '',
		  `realName` varchar(32) NOT NULL default '',
		  `displaySkin` varchar(32) NOT NULL default 'default',
		  `displayRank` varchar(50) NOT NULL default 'default',
		  `positionid` int(3) NOT NULL default '0',
		  `positionid2` int(3) NOT NULL default '0',
		  `rankid` int(3) NOT NULL default '0',
		  `firstName` varchar(32) NOT NULL default '',
		  `middleName` varchar(32) NOT NULL default '',
		  `lastName` varchar(32) NOT NULL default '',
		  `gender` enum('Male','Female','Hermaphrodite','Neuter') NOT NULL default 'Male',
		  `species` varchar(32) NOT NULL default '',
		  `aim` varchar(50) NOT NULL default '',
		  `yim` varchar(50) NOT NULL default '',
		  `msn` varchar(50) NOT NULL default '',
		  `icq` varchar(50) NOT NULL default '',
		  `heightFeet` int(2) NOT NULL default '0',
		  `heightInches` int(2) NOT NULL default '0',
		  `weight` int(4) NOT NULL default '0',
		  `eyeColor` varchar(25) NOT NULL default '',
		  `hairColor` varchar(25) NOT NULL default '',
		  `age` int(4) NOT NULL default '0',
		  `physicalDesc` text NOT NULL,
		  `history` text NOT NULL,
		  `personalityOverview` text NOT NULL,
		  `strengths` text NOT NULL,
		  `ambitions` text NOT NULL,
		  `hobbies` text NOT NULL,
		  `languages` varchar(100) NOT NULL default '',
		  `serviceRecord` text NOT NULL,
		  `father` varchar(100) NOT NULL default '',
		  `mother` varchar(100) NOT NULL default '',
		  `brothers` text NOT NULL,
		  `sisters` text NOT NULL,
		  `spouse` varchar(100) NOT NULL default '',
		  `children` text NOT NULL,
		  `otherFamily` text NOT NULL,
		  `awards` text NOT NULL,
		  `image` varchar(255) NOT NULL default '',
		  `contactInfo` enum('y','n') NOT NULL default 'y',
		  `emailPosts` enum('y','n') NOT NULL default 'y',
		  `emailLogs` enum('y','n') NOT NULL default 'y',
		  `emailNews` enum('y','n') NOT NULL default 'y',
		  `moderatePosts` enum('y','n') NOT NULL default 'n',
		  `moderateLogs` enum('y','n') NOT NULL default 'n',
		  `moderateNews` enum('y','n') NOT NULL default 'n',
		  `cpShowPosts` enum('y','n') not null default 'y',
		  `cpShowPostsNum` int(3) not null default '2',
		  `cpShowLogs` enum('y','n') not null default 'y',
		  `cpShowLogsNum` int(3) not null default '2',
		  `cpShowNews` enum('y','n') not null default 'y',
		  `cpShowNewsNum` int(3) not null default '2',
		  `loa` enum('0','1','2') NOT NULL default '0',
		  `strikes` int(1) NOT NULL default '0',
		  `joinDate` varchar(50) NOT NULL default '',
		  `leaveDate` varchar(50) NOT NULL default '',
		  `lastLogin` varchar(50) NOT NULL default '',
		  `lastPost` varchar(50) NOT NULL default '',
		  `accessPost` text NOT NULL,
		  `accessManage` text NOT NULL,
		  `accessReports` text NOT NULL,
		  `accessUser` text NOT NULL,
		  `accessOthers` text NOT NULL,
		  PRIMARY KEY  (`crewid`)
		) " . $tail . " ;" );
		
		/* create the database table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_database` (
			`dbid` int(4) NOT NULL auto_increment,
			`dbTitle` varchar(200) NOT NULL default '',
			`dbDesc` text NOT NULL,
			`dbContent` text NOT NULL,
			`dbType` enum('onsite','offsite','entry') NOT NULL default 'onsite',
			`dbURL` varchar(255) NOT NULL default '',
			`dbOrder` int(4) NOT NULL default '0',
			`dbDisplay` enum('y','n') NOT NULL default 'y',
			PRIMARY KEY  (`dbid`)
			) " . $tail . " ;" );
			
		/* create the department table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_departments` (
		  `deptid` int(3) NOT NULL auto_increment,
		  `deptOrder` int(3) NOT NULL default '0',
		  `deptClass` int(3) NOT NULL default '0',
		  `deptName` varchar(32) NOT NULL default '',
		  `deptDesc` text NOT NULL,
		  `deptDisplay` enum('y','n') NOT NULL default 'y',
		  `deptColor` varchar(6) NOT NULL default '',
		  `deptType` enum('playing','nonplaying') not null default 'playing',
		  PRIMARY KEY  (`deptid`)
		) " . $tail . " AUTO_INCREMENT=14 ;" );
		
		/* create the globals table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_globals` (
		  `globalid` int(1) NOT NULL default '0',
		  `shipPrefix` varchar(10) NOT NULL default '',
		  `shipName` varchar(32) NOT NULL default '',
		  `shipRegistry` varchar(16) NOT NULL default '',
		  `skin` varchar(16) NOT NULL default '',
		  `allowedSkins` text NOT NULL,
		  `allowedRanks` text NOT NULL,
		  `fleet` varchar(64) NOT NULL default '',
		  `fleetURL` varchar(128) NOT NULL default '',
		  `tfMember` enum('y','n') NOT NULL default 'y',
		  `tfName` varchar(64) NOT NULL default '',
		  `tfURL` varchar(128) NOT NULL default '',
		  `tgMember` enum('y','n') NOT NULL default 'y',
		  `tgName` varchar(64) NOT NULL default '',
		  `tgURL` varchar(128) NOT NULL default '',
		  `hasWebmaster` enum('y','n') NOT NULL default 'y',
		  `webmasterName` varchar(128) NOT NULL default '',
		  `webmasterEmail` varchar(64) NOT NULL default '',
		  `showNews` enum('y','n') NOT NULL default 'y',
		  `showNewsNum` int(2) NOT NULL default '5',
		  `simmYear` varchar(4) NOT NULL default '2383',
		  `rankSet` varchar(50) NOT NULL default 'default',
		  `simmType` enum('ship','starbase') NOT NULL default 'ship',
		  `useArchive` enum('y','n') NOT NULL default 'y',
		  `postCountDefault` int(3) NOT NULL default '14',
		  `manifestDisplay` enum('split','full') NOT NULL default 'split',
		  `useSamplePost` enum('y','n') NOT NULL default 'y',
		  `logList` int(4) NOT NULL default '25',
		  `bioShowPosts` enum('y','n') NOT NULL default 'y',
		  `bioShowLogs` enum('y','n') NOT NULL default 'y',
		  `bioShowPostsNum` int(2) NOT NULL default '5',
		  `bioShowLogsNum` int(2) NOT NULL default '5',
		  `showInfoMission` enum('y','n') NOT NULL default 'y',
		  `showInfoPosts` enum('y','n') NOT NULL default 'y',
		  `showInfoPositions` enum('y','n') NOT NULL default 'y',
		  `jpCount` enum('y','n') NOT NULL default 'y',
		  `usePosting` enum('y','n') NOT NULL default 'y',
		  `useMissionNotes` enum('y','n') NOT NULL default 'y',
		  PRIMARY KEY  (`globalid`)
		) " . $tail . " ;" );
		
		/* create the menu items table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_menu_items` (
			`menuid` int(4) NOT NULL auto_increment,
			`menuGroup` int(3) NOT NULL,
			`menuOrder` int(3) NOT NULL,
			`menuTitle` varchar(200) NOT NULL,
			`menuLinkType` enum('onsite','offsite') NOT NULL default 'onsite',
			`menuLink` varchar(255) NOT NULL,
			`menuAccess` varchar(50) NOT NULL,
			`menuMainSec` varchar(200) NOT NULL,
			`menuLogin` enum('y','n') NOT NULL default 'n',
			`menuCat` enum('main','general','admin') NOT NULL default 'general',
			`menuAvailability` enum('on','off') NOT NULL default 'on',
			PRIMARY KEY  (`menuid`)
		) " . $tail . " AUTO_INCREMENT=87;" );
		
		/* create the messages table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_messages` (
		  `messageid` int(2) NOT NULL auto_increment,
		  `welcomeMessage` text NOT NULL,
		  `simmMessage` text NOT NULL,
		  `shipMessage` text NOT NULL,
		  `shipHistory` text NOT NULL,
		  `cpMessage` text NOT NULL,
		  `joinDisclaimer` text NOT NULL,
		  `samplePostQuestion` text NOT NULL,
		  `rules` text NOT NULL,
		  `acceptMessage` text NOT NULL,
		  `rejectMessage` text NOT NULL,
		  `siteCreditsPermanent` text not null,
		  `siteCredits` text not null,
		  PRIMARY KEY  (`messageid`)
		) " . $tail . " ;" );
		
		/* create the missions table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_missions` (
		  `missionid` int(3) NOT NULL auto_increment,
		  `missionOrder` int(3) NOT NULL default '0',
		  `missionTitle` varchar(100) NOT NULL default '',
		  `missionDesc` text NOT NULL,
		  `missionSummary` text NOT NULL,
		  `missionStatus` enum('current','upcoming','completed') NOT NULL default 'upcoming',
		  `missionStart` varchar(50) NOT NULL default '',
		  `missionEnd` varchar(50) NOT NULL default '',
		  `missionImage` varchar(50) NOT NULL default 'images/missionimages/',
		  `missionNotes` text NOT NULL,
		  PRIMARY KEY  (`missionid`)
		) " . $tail . " ;" );
		
		/* create the news table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_news` (
		  `newsid` int(4) NOT NULL auto_increment,
		  `newsCat` int(3) NOT NULL default '1',
		  `newsAuthor` int(3) NOT NULL default '0',
		  `newsPosted` varchar(50) NOT NULL default '',
		  `newsTitle` varchar(100) NOT NULL default '',
		  `newsContent` text NOT NULL,
		  `newsStatus` enum( 'pending','saved','activated' ) NOT NULL default 'activated',
		  PRIMARY KEY  (`newsid`)
		) " . $tail . " ;" );
		
		/* create the news category table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_news_categories` (
		  `catid` int(3) NOT NULL auto_increment,
		  `catName` varchar(50) NOT NULL default '',
		  `catUserLevel` int(2) NOT NULL default '0',
		  `catVisible` enum('y','n') NOT NULL default 'y',
		  PRIMARY KEY  (`catid`)
		) " . $tail . " AUTO_INCREMENT=5 ;" );
		
		/* create the personal logs table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_personallogs` (
		  `logid` int(4) NOT NULL auto_increment,
		  `logAuthor` int(3) NOT NULL default '0',
		  `logTitle` varchar(100) NOT NULL default '',
		  `logContent` text NOT NULL,
		  `logPosted` varchar(50) NOT NULL default '',
		  `logStatus` enum( 'pending','saved','activated' ) NOT NULL default 'activated',
		  PRIMARY KEY  (`logid`)
		) " . $tail . " ;" );
		
		/* create the positions table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_positions` (
		  `positionid` int(3) NOT NULL auto_increment,
		  `positionOrder` int(3) NOT NULL default '0',
		  `positionName` varchar(64) NOT NULL default '',
		  `positionDesc` text NOT NULL,
		  `positionDept` int(3) NOT NULL default '0',
		  `positionType` enum( 'senior', 'crew' ) NOT NULL default 'crew',
		  `positionOpen` int(2) NOT NULL default '1',
		  `positionDisplay` enum('y','n') NOT NULL default 'y',
		  `positionMainPage` enum('y','n') NOT NULL default 'n',
		  PRIMARY KEY  (`positionid`)
		) " . $tail . " AUTO_INCREMENT=69 ;" );
		
		/* create the post table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_posts` (
		  `postid` int(4) NOT NULL auto_increment,
		  `postAuthor` varchar(23) NOT NULL default '',
		  `postTitle` varchar(100) NOT NULL default '',
		  `postLocation` varchar(100) NOT NULL default '',
		  `postTimeline` varchar(100) NOT NULL default '',
		  `postTag` varchar(255) NOT NULL default '',
		  `postContent` text NOT NULL,
		  `postPosted` varchar(50) NOT NULL default '',
		  `postMission` int(3) NOT NULL default '0',
		  `postStatus` enum( 'pending','saved','activated' ) NOT NULL default 'activated',
		  `postSave` int(4) NOT NULL default '0',
		  PRIMARY KEY  (`postid`)
		) " . $tail . " ;" );
		
		/* create the private messages table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_privatemessages` (
			`pmid` int(5) NOT NULL auto_increment,
			`pmRecipient` int(3) NOT NULL DEFAULT '0',
			`pmAuthor` int(3) NOT NULL DEFAULT '0',
			`pmSubject` varchar(100) NOT NULL default '',
			`pmContent` text NOT NULL,
			`pmDate` varchar(50) NOT NULL default '',
			`pmStatus` enum( 'read','unread' ) default 'unread',
			`pmAuthorDisplay` enum( 'y','n' ) default 'y',
			`pmRecipientDisplay` enum( 'y','n' ) default 'y',
		  PRIMARY KEY  (`pmid`)
		) " . $tail . " ;" );
		
		/* create the ranks table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_ranks` (
		  `rankid` int(3) NOT NULL auto_increment,
		  `rankOrder` int(2) NOT NULL default '0',
		  `rankName` varchar(32) NOT NULL default '',
		  `rankImage` varchar(255) NOT NULL default '',
		  `rankType` int(1) NOT NULL default '1',
		  `rankDisplay` enum('y','n') NOT NULL default 'y',
		  `rankClass` int(3) NOT NULL default '0',
		  PRIMARY KEY  (`rankid`)
		) " . $tail . " AUTO_INCREMENT=185 ;" );
		
		/* create the specs table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_specs` (
		  `specid` int(1) NOT NULL default '1',
		  `shipClass` varchar(50) NOT NULL default '',
		  `shipRole` varchar(80) NOT NULL default '',
		  `duration` int(3) NOT NULL default '0',
		  `durationUnit` varchar(16) NOT NULL default 'Years',
		  `refit` int(3) NOT NULL default '0',
		  `refitUnit` varchar(16) NOT NULL default 'Years',
		  `resupply` int(3) NOT NULL default '0',
		  `resupplyUnit` varchar(16) NOT NULL default 'Years',
		  `length` int(5) NOT NULL default '0',
		  `height` int(5) NOT NULL default '0',
		  `width` int(5) NOT NULL default '0',
		  `decks` int(5) NOT NULL default '0',
		  `complimentEmergency` int(8) NOT NULL default '0',
		  `complimentOfficers` int(6) NOT NULL default '0',
		  `complimentEnlisted` int(6) NOT NULL default '0',
		  `complimentMarines` int(6) NOT NULL default '0',
		  `complimentCivilians` int(6) NOT NULL default '0',
		  `warpCruise` varchar(8) NOT NULL default '',
		  `warpMaxCruise` varchar(8) NOT NULL default '',
		  `warpEmergency` varchar(8) NOT NULL default '',
		  `warpMaxTime` varchar(20) NOT NULL default '',
		  `warpEmergencyTime` varchar(20) NOT NULL default '',
		  `phasers` text NOT NULL,
		  `torpedoLaunchers` text NOT NULL,
		  `torpedoCompliment` text NOT NULL,
		  `defensive` text NOT NULL,
		  `shields` text NOT NULL,
		  `shuttlebays` int(3) NOT NULL default '0',
		  `hasShuttles` enum('y','n') NOT NULL default 'y',
		  `hasRunabouts` enum('y','n') NOT NULL default 'y',
		  `hasFighters` enum('y','n') NOT NULL default 'y',
		  `hasTransports` enum('y','n') NOT NULL default 'y',
		  `shuttles` text NOT NULL,
		  `runabouts` text NOT NULL,
		  `fighters` text NOT NULL,
		  `transports` text NOT NULL,
		  PRIMARY KEY  (`specid`)
		) " . $tail . " ;" );
		
		/* create the starbase docking table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_starbase_docking` (
		  `dockid` int(5) NOT NULL auto_increment,
		  `dockingShipName` varchar(100) NOT NULL default '',
		  `dockingShipRegistry` varchar(32) NOT NULL default '',
		  `dockingShipClass` varchar(50) NOT NULL default '',
		  `dockingShipURL` varchar(128) NOT NULL default '',
		  `dockingShipCO` varchar(128) NOT NULL default '',
		  `dockingShipCOEmail` varchar(50) NOT NULL default '',
		  `dockingDuration` varchar(50) NOT NULL default '',
		  `dockingDesc` text NOT NULL,
		  `dockingStatus` enum( 'pending','activated','departed' ) NOT NULL default 'activated',
		  PRIMARY KEY  (`dockid`)
		) " . $tail . " ;" );
		
		/* create the strikes table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_strikes` (
		  `strikeid` int(4) NOT NULL auto_increment,
		  `crewid` int(3) NOT NULL default '0',
		  `strikeDate` varchar(50) NOT NULL default '',
		  `reason` text NOT NULL,
		  `number` int(3) NOT NULL default '0',
		  PRIMARY KEY  (`strikeid`)
		) " . $tail . " ;" );
		
		/* add the system table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_system` (
		  `sysid` int(2) NOT NULL auto_increment,
		  `sysuid` varchar(20) NOT NULL default '',
		  `sysVersion` varchar(10) NOT NULL default '',
		  `sysBaseVersion` varchar(10) NOT NULL default '',
		  `sysIncrementVersion` varchar(10) NOT NULL default '',
		  `sysLaunchStatus` enum('y','n') NOT NULL default 'n',
		  PRIMARY KEY  (`sysid`)
		) " . $tail . " ;" );
		
		/* add the system versions table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_system_versions` (
			`versionid` int(3) NOT NULL auto_increment,
			`version` varchar(50) NOT NULL default '',
			`versionDate` varchar(50) NOT NULL default '',
			`versionShortDesc` text NOT NULL,
			`versionDesc` text NOT NULL,
			PRIMARY KEY  (`versionid`)
		) " . $tail . " ;" );
		
		/* add the tour table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_tour` (
			`tourid` int( 4 ) NOT NULL auto_increment,
			`tourName` varchar( 100 ) NOT NULL default '',
			`tourLocation` varchar( 100 ) NOT NULL default '',
			`tourPicture1` varchar( 255 ) NOT NULL default '',
			`tourPicture2` varchar( 255 ) NOT NULL default '',
			`tourPicture3` varchar( 255 ) NOT NULL default '',
			`tourDesc` text NOT NULL,
			`tourSummary` text NOT NULL,
			`tourOrder` int( 4 ) NOT NULL default '0',
			`tourDisplay` enum( 'y','n' ) NOT NULL default 'y',
			PRIMARY KEY  (`tourid`)
		) " . $tail . " ;" );
		
		/* add the deck listing table */
		mysql_query( "CREATE TABLE IF NOT EXISTS `sms_tour_decks` (
			`deckid` int(4) NOT NULL auto_increment,
			`deckContent` text,
		  PRIMARY KEY  (`deckid`)
		) " . $tail . " ;" );
		
		echo "We have successfully verified that all the SMS database tables exist. The next step will verify that all the fields are present. <a href='tables.php?step=3'>Click here</a> to continue to the next step.";
		
		break;
		
	case 3:
		
		$columns = array(
			'sms_awards' => array(
				array( 'awardid', '`awardid` int(4) NOT NULL auto_increment' ),
				array( 'awardName', '`awardName` varchar(100) NOT NULL default \'\'' ),
		  		array( 'awardImage', '`awardImage` varchar(50) NOT NULL default \'\'' ),
		  		array( 'awardOrder', '`awardOrder` int(3) NOT NULL default \'0\'' ),
		  		array( 'awardDesc', '`awardDesc` text NOT NULL' )
			),
			'sms_coc' => array(
				array( 'cocid', '`cocid` int(1) NOT NULL auto_increment' ),
		  		array( 'crewid', '`crewid` int(3) NOT NULL default \'0\'' )
			),
			'sms_crew' => array(
				array( 'crewid', '`crewid` int(4) NOT NULL auto_increment' ),
				array( 'username', '`username` varchar(16) NOT NULL default \'\'' ),
				array( 'password', '`password` varchar(32) NOT NULL default \'\'' ),
				array( 'crewType', '`crewType` enum(\'active\',\'inactive\',\'pending\',\'npc\') NOT NULL default \'active\'' ),
				array( 'email', '`email` varchar(64) NOT NULL default \'\'' ),
				array( 'realName', '`realName` varchar(32) NOT NULL default \'\'' ),
				array( 'displaySkin', '`displaySkin` varchar(32) NOT NULL default \'default\'' ),
				array( 'displayRank', '`displayRank` varchar(50) NOT NULL default \'default\'' ),
				array( 'positionid', '`positionid` int(3) NOT NULL default \'0\'' ),
				array( 'positionid2', '`positionid2` int(3) NOT NULL default \'0\'' ),
				array( 'rankid', '`rankid` int(3) NOT NULL default \'0\'' ),
				array( 'firstName', '`firstName` varchar(32) NOT NULL default \'\'' ),
				array( 'middleName', '`middleName` varchar(32) NOT NULL default \'\'' ),
				array( 'lastName', '`lastName` varchar(32) NOT NULL default \'\'' ),
				array( 'gender', '`gender` enum(\'Male\',\'Female\',\'Hermaphrodite\',\'Neuter\') NOT NULL default \'Male\'' ),
				array( 'species', '`species` varchar(32) NOT NULL default \'\'' ),
				array( 'aim', '`aim` varchar(50) NOT NULL default \'\'' ),
				array( 'yim', '`yim` varchar(50) NOT NULL default \'\'' ),
				array( 'msn', '`msn` varchar(50) NOT NULL default \'\'' ),
				array( 'icq', '`icq` varchar(50) NOT NULL default \'\'' ),
				array( 'heightFeet', '`heightFeet` int(2) NOT NULL default \'0\'' ),
				array( 'heightInches', '`heightInches` int(2) NOT NULL default \'0\'' ),
				array( 'weight', '`weight` int(4) NOT NULL default \'0\'' ),
				array( 'eyeColor', '`eyeColor` varchar(25) NOT NULL default \'\'' ),
				array( 'hairColor', '`hairColor` varchar(25) NOT NULL default \'\'' ),
				array( 'age', '`age` int(4) NOT NULL default \'0\'' ),
				array( 'physicalDesc', '`physicalDesc` text NOT NULL' ),
				array( 'history', '`history` text NOT NULL' ),
				array( 'personalityOverview', '`personalityOverview` text NOT NULL' ),
				array( 'strengths', '`strengths` text NOT NULL' ),
				array( 'ambitions', '`ambitions` text NOT NULL' ),
				array( 'hobbies', '`hobbies` text NOT NULL' ),
				array( 'languages', '`languages` varchar(100) NOT NULL default \'\'' ),
				array( 'serviceRecord', '`serviceRecord` text NOT NULL' ),
				array( 'father', '`father` varchar(100) NOT NULL default \'\'' ),
				array( 'mother', '`mother` varchar(100) NOT NULL default \'\'' ),
				array( 'brothers', '`brothers` text NOT NULL' ),
				array( 'sisters', '`sisters` text NOT NULL' ),
				array( 'spouse', '`spouse` varchar(100) NOT NULL default \'\'' ),
				array( 'children', '`children` text NOT NULL' ),
				array( 'otherFamily', '`otherFamily` text NOT NULL' ),
				array( 'awards', '`awards` text NOT NULL' ),
				array( 'image', '`image` varchar(255) NOT NULL default \'\'' ),
				array( 'contactInfo', '`contactInfo` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'emailPosts', '`emailPosts` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'emailLogs', '`emailLogs` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'emailNews', '`emailNews` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'moderatePosts', '`moderatePosts` enum(\'y\',\'n\') NOT NULL default \'n\'' ),
				array( 'moderateLogs', '`moderateLogs` enum(\'y\',\'n\') NOT NULL default \'n\'' ),
				array( 'moderateNews', '`moderateNews` enum(\'y\',\'n\') NOT NULL default \'n\'' ),
				array( 'cpShowPosts', '`cpShowPosts` enum(\'y\',\'n\') not null default \'y\'' ),
				array( 'cpShowPostsNum', '`cpShowPostsNum` int(3) not null default \'2\'' ),
				array( 'cpShowLogs', '`cpShowLogs` enum(\'y\',\'n\') not null default \'y\'' ),
				array( 'cpShowLogsNum', '`cpShowLogsNum` int(3) not null default \'2\'' ),
				array( 'cpShowNews', '`cpShowNews` enum(\'y\',\'n\') not null default \'y\'' ),
				array( 'cpShowNewsNum', '`cpShowNewsNum` int(3) not null default \'2\'' ),
				array( 'loa', '`loa` enum(\'0\',\'1\',\'2\') NOT NULL default \'0\'' ),
				array( 'strikes', '`strikes` int(1) NOT NULL default \'0\'' ),
				array( 'joinDate', '`joinDate` varchar(50) NOT NULL default \'\'' ),
				array( 'leaveDate', '`leaveDate` varchar(50) NOT NULL default \'\'' ),
				array( 'lastLogin', '`lastLogin` varchar(50) NOT NULL default \'\'' ),
				array( 'lastPost', '`lastPost` varchar(50) NOT NULL default \'\'' ),
				array( 'accessPost', '`accessPost` text NOT NULL' ),
				array( 'accessManage', '`accessManage` text NOT NULL' ),
				array( 'accessReports', '`accessReports` text NOT NULL' ),
				array( 'accessUser', '`accessUser` text NOT NULL' ),
				array( 'accessOthers', '`accessOthers` text NOT NULL' )
			),
			'sms_database' => array(
				array( 'dbid', '`dbid` int(4) NOT NULL auto_increment' ),
				array( 'dbTitle', '`dbTitle` varchar(200) NOT NULL default \'\'' ),
				array( 'dbDesc', '`dbDesc` text NOT NULL' ),
				array( 'dbContent', '`dbContent` text NOT NULL' ),
				array( 'dbType', '`dbType` enum(\'onsite\',\'offsite\',\'entry\') NOT NULL default \'onsite\'' ),
				array( 'dbURL', '`dbURL` varchar(255) NOT NULL default \'\'' ),
				array( 'dbOrder', '`dbOrder` int(4) NOT NULL default \'0\'' ),
				array( 'dbDisplay', '`dbDisplay` enum(\'y\',\'n\') NOT NULL default \'y\'' )
			),
			'sms_departments' => array(
				array( 'deptid', '`deptid` int(3) NOT NULL auto_increment' ),
				array( 'deptOrder', '`deptOrder` int(3) NOT NULL default \'0\'' ),
				array( 'deptClass', '`deptClass` int(3) NOT NULL default \'0\'' ),
				array( 'deptName', '`deptName` varchar(32) NOT NULL default \'\'' ),
				array( 'deptDesc', '`deptDesc` text NOT NULL' ),
				array( 'deptDisplay', '`deptDisplay` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'deptColor', '`deptColor` varchar(6) NOT NULL default \'\'' ),
				array( 'deptType', '`deptType` enum(\'playing\',\'nonplaying\') not null default \'playing\'' )
			),
			'sms_globals' => array(
				array( 'globalid', '`globalid` int(1) NOT NULL default \'0\'' ),
				array( 'shipPrefix', '`shipPrefix` varchar(10) NOT NULL default \'\'' ),
				array( 'shipName', '`shipName` varchar(32) NOT NULL default \'\'' ),
				array( 'shipRegistry', '`shipRegistry` varchar(16) NOT NULL default \'\'' ),
				array( 'skin', '`skin` varchar(16) NOT NULL default \'\'' ),
				array( 'allowedSkins', '`allowedSkins` text NOT NULL' ),
				array( 'allowedRanks', '`allowedRanks` text NOT NULL' ),
				array( 'fleet', '`fleet` varchar(64) NOT NULL default \'\'' ),
				array( 'fleetURL', '`fleetURL` varchar(128) NOT NULL default \'\'' ),
				array( 'tfMember', '`tfMember` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'tfName', '`tfName` varchar(64) NOT NULL default \'\'' ),
				array( 'tfURL', '`tfURL` varchar(128) NOT NULL default \'\'' ),
				array( 'tgMember', '`tgMember` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'tgName', '`tgName` varchar(64) NOT NULL default \'\'' ),
				array( 'tgURL', '`tgURL` varchar(128) NOT NULL default \'\'' ),
				array( 'hasWebmaster', '`hasWebmaster` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'webmasterName', '`webmasterName` varchar(128) NOT NULL default \'\'' ),
				array( 'webmasterEmail', '`webmasterEmail` varchar(64) NOT NULL default \'\'' ),
				array( 'showNews', '`showNews` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'showNewsNum', '`showNewsNum` int(2) NOT NULL default \'5\'' ),
				array( 'simmYear', '`simmYear` varchar(4) NOT NULL default \'2383\'' ),
				array( 'rankSet', '`rankSet` varchar(50) NOT NULL default \'default\'' ),
				array( 'simmType', '`simmType` enum(\'ship\',\'starbase\') NOT NULL default \'ship\'' ),
				array( 'useArchive', '`useArchive` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'postCountDefault', '`postCountDefault` int(3) NOT NULL default \'14\'' ),
				array( 'manifestDisplay', '`manifestDisplay` enum(\'split\',\'full\') NOT NULL default \'split\'' ),
				array( 'useSamplePost', '`useSamplePost` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'logList', '`logList` int(4) NOT NULL default \'25\'' ),
				array( 'bioShowPosts', '`bioShowPosts` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'bioShowLogs', '`bioShowLogs` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'bioShowPostsNum', '`bioShowPostsNum` int(2) NOT NULL default \'5\'' ),
				array( 'bioShowLogsNum', '`bioShowLogsNum` int(2) NOT NULL default \'5\'' ),
				array( 'showInfoMission', '`showInfoMission` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'showInfoPosts', '`showInfoPosts` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'showInfoPositions', '`showInfoPositions` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'jpCount', '`jpCount` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'usePosting', '`usePosting` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'useMissionNotes', '`useMissionNotes` enum(\'y\',\'n\') NOT NULL default \'y\'' )
			),
			'sms_menu_items' => array(
				array( 'menuid', '`menuid` int(4) NOT NULL auto_increment' ),
				array( 'menuGroup', '`menuGroup` int(3) NOT NULL' ),
				array( 'menuOrder', '`menuOrder` int(3) NOT NULL' ),
				array( 'menuTitle', '`menuTitle` varchar(200) NOT NULL' ),
				array( 'menuLinkType', '`menuLinkType` enum(\'onsite\',\'offsite\') NOT NULL default \'onsite\'' ),
				array( 'menuLink', '`menuLink` varchar(255) NOT NULL' ),
				array( 'menuAccess', '`menuAccess` varchar(50) NOT NULL' ),
				array( 'menuMainSec', '`menuMainSec` varchar(200) NOT NULL' ),
				array( 'menuLogin', '`menuLogin` enum(\'y\',\'n\') NOT NULL default \'n\'' ),
				array( 'menuCat', '`menuCat` enum(\'main\',\'general\',\'admin\') NOT NULL default \'general\'' ),
				array( 'menuAvailability', '`menuAvailability` enum(\'on\',\'off\') NOT NULL default \'on\'' ),
			),
			'sms_messages' => array(
				array( 'messageid', '`messageid` int(2) NOT NULL auto_increment' ),
				array( 'welcomeMessage', '`welcomeMessage` text NOT NULL' ),
				array( 'simmMessage', '`simmMessage` text NOT NULL' ),
				array( 'shipMessage', '`shipMessage` text NOT NULL' ),
				array( 'shipHistory', '`shipHistory` text NOT NULL' ),
				array( 'cpMessage', '`cpMessage` text NOT NULL' ),
				array( 'joinDisclaimer', '`joinDisclaimer` text NOT NULL' ),
				array( 'samplePostQuestion', '`samplePostQuestion` text NOT NULL' ),
				array( 'rules', '`rules` text NOT NULL' ),
				array( 'acceptMessage', '`acceptMessage` text NOT NULL' ),
				array( 'rejectMessage', '`rejectMessage` text NOT NULL' ),
				array( 'siteCreditsPermanent', '`siteCreditsPermanent` text not null' ),
				array( 'siteCredits', '`siteCredits` text not null' )
			),
			'sms_missions' => array(
				array( 'missionid', '`missionid` int(3) NOT NULL auto_increment' ),
				array( 'missionOrder', '`missionOrder` int(3) NOT NULL default \'0\'' ),
				array( 'missionTitle', '`missionTitle` varchar(100) NOT NULL default \'\'' ),
				array( 'missionDesc', '`missionDesc` text NOT NULL' ),
				array( 'missionSummary', '`missionSummary` text NOT NULL' ),
				array( 'missionStatus', '`missionStatus` enum(\'current\',\'upcoming\',\'completed\') NOT NULL default \'upcoming\'' ),
				array( 'missionStart', '`missionStart` varchar(50) NOT NULL default \'\'' ),
				array( 'missionEnd', '`missionEnd` varchar(50) NOT NULL default \'\'' ),
				array( 'missionImage', '`missionImage` varchar(50) NOT NULL default \'images/missionimages/\'' ),
				array( 'missionNotes', '`missionNotes` text NOT NULL' )
			),
			'sms_news' => array(
				array( 'newsid', '`newsid` int(4) NOT NULL auto_increment' ),
				array( 'newsCat', '`newsCat` int(3) NOT NULL default \'1\'' ),
				array( 'newsAuthor', '`newsAuthor` int(3) NOT NULL default \'0\'' ),
				array( 'newsPosted', '`newsPosted` varchar(50) NOT NULL default \'\'' ),
				array( 'newsTitle', '`newsTitle` varchar(100) NOT NULL default \'\'' ),
				array( 'newsContent', '`newsContent` text NOT NULL' ),
				array( 'newsStatus', '`newsStatus` enum( \'pending\',\'saved\',\'activated\' ) NOT NULL default \'activated\'' )
			),
			'sms_news_categories' => array(
				array( 'catid', '`catid` int(3) NOT NULL auto_increment' ),
				array( 'catName', '`catName` varchar(50) NOT NULL default \'\'' ),
				array( 'catUserLevel', '`catUserLevel` int(2) NOT NULL default \'0\'' ),
				array( 'catVisible', '`catVisible` enum(\'y\',\'n\') NOT NULL default \'y\'' )
			),
			'sms_personallogs' => array(
				array( 'logid', '`logid` int(4) NOT NULL auto_increment' ),
				array( 'logAuthor', '`logAuthor` int(3) NOT NULL default \'0\'' ),
				array( 'logTitle', '`logTitle` varchar(100) NOT NULL default \'\'' ),
				array( 'logContent', '`logContent` text NOT NULL' ),
				array( 'logPosted', '`logPosted` varchar(50) NOT NULL default \'\'' ),
				array( 'logStatus', '`logStatus` enum( \'pending\',\'saved\',\'activated\' ) NOT NULL default \'activated\'' )
			),
			'sms_positions' => array(
				array( 'positionid', '`positionid` int(3) NOT NULL auto_increment' ),
				array( 'positionOrder', '`positionOrder` int(3) NOT NULL default \'0\'' ),
				array( 'positionName', '`positionName` varchar(64) NOT NULL default \'\'' ),
				array( 'positionDesc', '`positionDesc` text NOT NULL' ),
				array( 'positionDept', '`positionDept` int(3) NOT NULL default \'0\'' ),
				array( 'positionType', '`positionType` enum( \'senior\', \'crew\' ) NOT NULL default \'crew\'' ),
				array( 'positionOpen', '`positionOpen` int(2) NOT NULL default \'1\'' ),
				array( 'positionDisplay', '`positionDisplay` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'positionMainPage', '`positionMainPage` enum(\'y\',\'n\') NOT NULL default \'n\'' )
			),
			'sms_posts' => array(
				array( 'postid', '`postid` int(4) NOT NULL auto_increment' ),
				array( 'postAuthor', '`postAuthor` varchar(23) NOT NULL default \'\'' ),
				array( 'postTitle', '`postTitle` varchar(100) NOT NULL default \'\'' ),
				array( 'postLocation', '`postLocation` varchar(100) NOT NULL default \'\'' ),
				array( 'postTimeline', '`postTimeline` varchar(100) NOT NULL default \'\'' ),
				array( 'postTag', '`postTag` varchar(255) NOT NULL default \'\'' ),
				array( 'postContent', '`postContent` text NOT NULL' ),
				array( 'postPosted', '`postPosted` varchar(50) NOT NULL default \'\'' ),
				array( 'postMission', '`postMission` int(3) NOT NULL default \'0\'' ),
				array( 'postStatus', '`postStatus` enum( \'pending\',\'saved\',\'activated\' ) NOT NULL default \'activated\'' ),
				array( 'postSave', '`postSave` int(4) NOT NULL default \'0\'' )
			),
			'sms_privatemessages' => array(
				array( 'pmid', '`pmid` int(5) NOT NULL auto_increment' ),
				array( 'pmRecipient', '`pmRecipient` int(3) NOT NULL DEFAULT \'0\'' ),
				array( 'pmAuthor', '`pmAuthor` int(3) NOT NULL DEFAULT \'0\'' ),
				array( 'pmSubject', '`pmSubject` varchar(100) NOT NULL default \'\'' ),
				array( 'pmContent', '`pmContent` text NOT NULL' ),
				array( 'pmDate', '`pmDate` varchar(50) NOT NULL default \'\'' ),
				array( 'pmStatus', '`pmStatus` enum( \'read\',\'unread\' ) default \'unread\'' ),
				array( 'pmAuthorDisplay', '`pmAuthorDisplay` enum( \'y\',\'n\' ) default \'y\'' ),
				array( 'pmRecipientDisplay', '`pmRecipientDisplay` enum( \'y\',\'n\' ) default \'y\'' )
			),
			'sms_ranks' => array(
				array( 'rankid', '`rankid` int(3) NOT NULL auto_increment' ),
				array( 'rankOrder', '`rankOrder` int(2) NOT NULL default \'0\'' ),
				array( 'rankName', '`rankName` varchar(32) NOT NULL default \'\'' ),
				array( 'rankImage', '`rankImage` varchar(255) NOT NULL default \'\'' ),
				array( 'rankType', '`rankType` int(1) NOT NULL default \'1\'' ),
				array( 'rankDisplay', '`rankDisplay` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'rankClass', '`rankClass` int(3) NOT NULL default \'0\'' )
			),
			'sms_specs' => array(
				array( 'specid', '`specid` int(1) NOT NULL default \'1\'' ),
				array( 'shipClass', '`shipClass` varchar(50) NOT NULL default \'\'' ),
				array( 'shipRole', '`shipRole` varchar(80) NOT NULL default \'\'' ),
				array( 'duration', '`duration` int(3) NOT NULL default \'0\'' ),
				array( 'durationUnit', '`durationUnit` varchar(16) NOT NULL default \'Years\'' ),
				array( 'refit', '`refit` int(3) NOT NULL default \'0\'' ),
				array( 'refitUnit', '`refitUnit` varchar(16) NOT NULL default \'Years\'' ),
				array( 'resupply', '`resupply` int(3) NOT NULL default \'0\'' ),
				array( 'resupplyUnit', '`resupplyUnit` varchar(16) NOT NULL default \'Years\'' ),
				array( 'length', '`length` int(5) NOT NULL default \'0\'' ),
				array( 'height', '`height` int(5) NOT NULL default \'0\'' ),
				array( 'width', '`width` int(5) NOT NULL default \'0\'' ),
				array( 'decks', '`decks` int(5) NOT NULL default \'0\'' ),
				array( 'complimentEmergency', '`complimentEmergency` int(8) NOT NULL default \'0\'' ),
				array( 'complimentOfficers', '`complimentOfficers` int(6) NOT NULL default \'0\'' ),
				array( 'complimentEnlisted', '`complimentEnlisted` int(6) NOT NULL default \'0\'' ),
				array( 'complimentMarines', '`complimentMarines` int(6) NOT NULL default \'0\'' ),
				array( 'complimentCivilians', '`complimentCivilians` int(6) NOT NULL default \'0\'' ),
				array( 'warpCruise', '`warpCruise` varchar(8) NOT NULL default \'\'' ),
				array( 'warpMaxCruise', '`warpMaxCruise` varchar(8) NOT NULL default \'\'' ),
				array( 'warpEmergency', '`warpEmergency` varchar(8) NOT NULL default \'\'' ),
				array( 'warpMaxTime', '`warpMaxTime` varchar(20) NOT NULL default \'\'' ),
				array( 'warpEmergencyTime', '`warpEmergencyTime` varchar(20) NOT NULL default \'\'' ),
				array( 'phasers', '`phasers` text NOT NULL' ),
				array( 'torpedoLaunchers', '`torpedoLaunchers` text NOT NULL' ),
				array( 'torpedoCompliment', '`torpedoCompliment` text NOT NULL' ),
				array( 'defensive', '`defensive` text NOT NULL' ),
				array( 'shields', '`shields` text NOT NULL' ),
				array( 'shuttlebays', '`shuttlebays` int(3) NOT NULL default \'0\'' ),
				array( 'hasShuttles', '`hasShuttles` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'hasRunabouts', '`hasRunabouts` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'hasFighters', '`hasFighters` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'hasTransports', '`hasTransports` enum(\'y\',\'n\') NOT NULL default \'y\'' ),
				array( 'shuttles', '`shuttles` text NOT NULL' ),
				array( 'runabouts', '`runabouts` text NOT NULL' ),
				array( 'fighters', '`fighters` text NOT NULL' ),
				array( 'transports', '`transports` text NOT NULL' )
			),
			'sms_starbase_docking' => array(
				array( 'dockid', '`dockid` int(5) NOT NULL auto_increment' ),
				array( 'dockingShipName', '`dockingShipName` varchar(100) NOT NULL default \'\'' ),
				array( 'dockingShipRegistry', '`dockingShipRegistry` varchar(32) NOT NULL default \'\'' ),
				array( 'dockingShipClass', '`dockingShipClass` varchar(50) NOT NULL default \'\'' ),
				array( 'dockingShipURL', '`dockingShipURL` varchar(128) NOT NULL default \'\'' ),
				array( 'dockingShipCO', '`dockingShipCO` varchar(128) NOT NULL default \'\'' ),
				array( 'dockingShipCOEmail', '`dockingShipCOEmail` varchar(50) NOT NULL default \'\'' ),
				array( 'dockingDuration', '`dockingDuration` varchar(50) NOT NULL default \'\'' ),
				array( 'dockingDesc', '`dockingDesc` text NOT NULL' ),
				array( 'dockingStatus', '`dockingStatus` enum( \'pending\',\'activated\',\'departed\' ) NOT NULL default \'activated\'' )
			),
			'sms_strikes' => array(
				array( 'strikeid', '`strikeid` int(4) NOT NULL auto_increment' ),
				array( 'crewid', '`crewid` int(3) NOT NULL default \'0\'' ),
				array( 'strikeDate', '`strikeDate` varchar(50) NOT NULL default \'\'' ),
				array( 'reason', '`reason` text NOT NULL' ),
				array( 'number', '`number` int(3) NOT NULL default \'0\'' )
			),
			'sms_system' => array(
				array( 'sysid', '`sysid` int(2) NOT NULL auto_increment' ),
				array( 'sysuid', '`sysuid` varchar(20) NOT NULL default \'\'' ),
				array( 'sysVersion', '`sysVersion` varchar(10) NOT NULL default \'\'' ),
				array( 'sysBaseVersion', '`sysBaseVersion` varchar(10) NOT NULL default \'\'' ),
				array( 'sysIncrementVersion', '`sysIncrementVersion` varchar(10) NOT NULL default \'\'' ),
				array( 'sysLaunchStatus', '`sysLaunchStatus` enum(\'y\',\'n\') NOT NULL default \'n\'' )
			),
			'sms_system_versions' => array(
				array( 'versionid', '`versionid` int(3) NOT NULL auto_increment' ),
				array( 'version', '`version` varchar(50) NOT NULL default \'\'' ),
				array( 'versionDate', '`versionDate` varchar(50) NOT NULL default \'\'' ),
				array( 'versionShortDesc', '`versionShortDesc` text NOT NULL' ),
				array( 'versionDesc', '`versionDesc` text NOT NULL' )
			),
			'sms_tour' => array(
				array( 'tourid', '`tourid` int( 4 ) NOT NULL auto_increment' ),
				array( 'tourName', '`tourName` varchar( 100 ) NOT NULL default \'\'' ),
				array( 'tourLocation', '`tourLocation` varchar( 100 ) NOT NULL default \'\'' ),
				array( 'tourPicture1', '`tourPicture1` varchar( 255 ) NOT NULL default \'\'' ),
				array( 'tourPicture2', '`tourPicture2` varchar( 255 ) NOT NULL default \'\'' ),
				array( 'tourPicture3', '`tourPicture3` varchar( 255 ) NOT NULL default \'\'' ),
				array( 'tourDesc', '`tourDesc` text NOT NULL' ),
				array( 'tourSummary', '`tourSummary` text NOT NULL' ),
				array( 'tourOrder', '`tourOrder` int( 4 ) NOT NULL default \'0\'' ),
				array( 'tourDisplay', '`tourDisplay` enum( \'y\',\'n\' ) NOT NULL default \'y\'' )
			),
			'sms_tour_decks' => array(
				array( 'deckid', '`deckid` int(4) NOT NULL auto_increment' ),
				array( 'deckContent', '`deckContent` text' )
			),	
		);
		
		foreach( $columns as $key => $value ) {
			/* count how many items there are in the sub-array */
			$fieldCount = count( $value );
			
			/* loop through the array to check for each field */
			for( $i=0; $i<$fieldCount; $i++ ) {
				$sql1 = "SELECT " . $value[$i][0] . " FROM " . $key . " LIMIT 1";
				$result1 = mysql_query( $sql1 );
				
				if( !$result1 ) {
					alter_table( $key, $value[$i][0], $value[$i][1] );
				}
			}
			
			echo "The " . $key . " table was successfully verified!";
			echo "<br /><br />";
			sleep(1);
		}
		
		echo "We have successfully verified that all the SMS database table fields exist. <a href='tables.php?step=3'>Click here</a> to finish the utility.";
		break;
	
	case 3:
		echo "<div style='color:#406ceb; font-size:200%; font-weight:bold;'>Verification complete!</div>";
		echo "<p>You have successfully verified the tables and fields for the SMS database. If you have problems with SMS, please go to the <a href='http://forums.anodyne-productions.com/' target='_blank'>Anodyne Forums</a> for assistance.</p>";
		break;
		
}

?>