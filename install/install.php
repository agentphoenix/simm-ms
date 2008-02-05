<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: install/install.php
Purpose: Installation script for SMS 2.5

System Version: 2.5.6
Last Modified: 2008-02-05 1112 EST
**/

session_start();

/* define the step var */
$step = $_GET['step'];

/* pull in the db connections */
include_once( '../framework/variables.php' );

/* do some logic based on the step */
if( !isset( $step ) ) {
	$step = 1;
}

/*
some error checking in case someone hasn't taken care of
the variables.php stuff
*/
if( $step > 2 && !isset( $webLocation ) ) {
	$step = 2;
	$varError = 1;
}

if( $step == 2 ) {
	
	/** ERROR CHECKING FOR USER INPUT FROM STEP 1 **/
	
	/* make sure the web location has a trailing slash */
	if( substr( $_POST['webLocation'], -1 ) == "/" ) {
		$webLocation1 = $_POST['webLocation'];
	} else {
		$webLocation1 = $_POST['webLocation'] . "/";
	}
	
	/* make sure the web location starts with http:// */
	if( substr( $webLocation1, 0, 7 ) == "http://" ) {
		/* don't do anything if this is right */
		$webLocation = $webLocation1;
	} else {
		$webLocation = "http://" . $webLocation1;
	}
	
	/* make sure the database server doesn't start with http:// */
	if( substr( $_POST['dbServer'], 0, 7 ) == "http://" ) {
		$dbServer = str_replace( "http://", "", $_POST['dbServer'] );
	} else {
		$dbServer = $_POST['dbServer'];
	}
	
	/** END ERROR CHECKING **/

	$dbErrorMessage = stripslashes( $_POST['errorMessage'] );
	
	$filename = '../framework/variables.php';
	$somecontent = "<?php
	
\$webLocation = \"$webLocation\";

\$dbServer = \"$dbServer\";
\$dbTable = \"$_POST[dbTable]\";
\$dbUser = \"$_POST[dbUser]\";
\$dbPassword = \"$_POST[dbPassword]\";
\$dbErrorMessage = \"$dbErrorMessage\";

?>";

	if( !$varError ) {
		/* set up session variables */
		$_SESSION['webLocation'] = $webLocation;
		$_SESSION['dbServer'] = $dbServer;
		$_SESSION['dbTable'] = $_POST['dbTable'];
		$_SESSION['dbUser'] = $_POST['dbUser'];
		$_SESSION['dbPassword'] = $_POST['dbPassword'];
		$_SESSION['dbErrorMessage'] = $dbErrorMessage;
	}
	
	if( is_writable( $filename ) ) {
		chmod( $filename, 0777 );
		
		if( chmod( $filename, 0777 ) === FALSE ) {
			$write = "failed";
		} else {
			if( !$handle = fopen( $filename, 'w' ) ) {
				$write = "failed";
			} if( fwrite( $handle, $somecontent ) === FALSE ) {
				$write = "failed";
			} else {
				fclose( $handle );
				$write = "success";
			}
			
			chmod( $filename, 0644 );
		}
	} else {
		$write = "failed";
	}

} elseif( $step == 3 ) {
	
	/* Database connection parameters */
	$db = @mysql_connect( "$dbServer", "$dbUser", "$dbPassword" ) or die ( "<b>$dbErrorMessage</b>" );
	mysql_select_db( "$dbTable",$db ) or die ( "<b>Unable to select the appropriate database.  Please try again later.</b>" );
	
	/* query the database for the mysql version */
	$t = mysql_query("select version() as ve");
	echo mysql_error();
	$r = mysql_fetch_object( $t );
	
	/* if the server is running mysql 4 and higher, set the default character set */
	if( $r->ve >= 4 ) {
		$tail = "CHARACTER SET utf8";
	} else {
		$tail = "";
	}
	
	/* create the awards table */
	mysql_query( "CREATE TABLE `sms_awards` (
	  `awardid` int(4) NOT NULL auto_increment,
	  `awardName` varchar(100) NOT NULL default '',
	  `awardImage` varchar(50) NOT NULL default '',
	  `awardOrder` int(3) NOT NULL default '0',
	  `awardDesc` text NOT NULL,
	  PRIMARY KEY  (`awardid`)
	) " . $tail . " ;" );
	
	/* create the coc table */
	mysql_query( "CREATE TABLE `sms_coc` (
	  `cocid` int(1) NOT NULL auto_increment,
	  `crewid` int(3) NOT NULL default '0',
	  PRIMARY KEY  (`cocid`)
	) " . $tail . " AUTO_INCREMENT=2 ;" );
	
	/* insert data into the coc table */
	mysql_query( "INSERT INTO `sms_coc` (`cocid`, `crewid`) VALUES (1, 1);" );
	
	/* create the crew table */
	mysql_query( "CREATE TABLE `sms_crew` (
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
	mysql_query( "CREATE TABLE `sms_database` (
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
	mysql_query( "CREATE TABLE `sms_departments` (
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
	
	/* insert data into the department table */
	mysql_query( "INSERT INTO `sms_departments` (`deptid`, `deptOrder`, `deptClass`, `deptName`, `deptDesc`, `deptDisplay`, `deptColor`, `deptType`) 
	VALUES (1, 1, 1, 'Command', 'The Command department is ultimately responsible for the ship and its crew, and those within the department are responsible for commanding the vessel and representing the interests of Starfleet.', 'y', '9c2c2c', 'playing'),
	(2, 2, 1, 'Flight Control', 'Responsible for the navigation and flight control of a vessel and its auxiliary craft, the Flight Control department includes pilots trained in both starship and auxiliary craft piloting. Note that the Flight Control department does not include Fighter pilots.', 'y', '9c2c2c', 'playing'),
	(3, 3, 1, 'Strategic Operations', 'The Strategic Operations department acts as an advisory to the command staff, as well as a resource of knowledge and information concerning hostile races in the operational zone of the ship, as well as combat strategies and other such things.', 'y', '9c2c2c', 'playing'),
	(4, 4, 2, 'Security & Tactical', 'Merging the responsibilities of ship to ship and personnel combat into a single department, the security & tactical department is responsible for the tactical readiness of the vessel and the security of the ship.', 'y', 'c08429', 'playing'),
	(5, 5, 2, 'Operations', 'The operations department is responsible for keeping ship systems functioning properly, rerouting power, bypassing relays, and doing whatever else is necessary to keep the ship operating at peak efficiency.', 'y', 'c08429', 'playing'),
	(6, 6, 2, 'Engineering', 'The engineering department has the enormous task of keeping the ship working; they are responsible for making repairs, fixing problems, and making sure that the ship is ready for anything.', 'y', 'c08429', 'playing'),
	(7, 7, 3, 'Science', 'From sensor readings to figuring out a way to enter the strange spacial anomaly, the science department is responsible for recording data, testing new ideas out, and making discoveries.', 'y', '008080', 'playing'),
	(8, 8, 3, 'Medical & Counseling', 'The medical & counseling department is responsible for the mental and physical health of the crew, from running annual physicals to combatting a strange plague that is afflicting the crew to helping a crew member deal with the loss of a loved one.', 'y', '008080', 'playing'),
	(9, 9, 4, 'Intelligence', 'The Intelligence department is responsible for gathering and providing intelligence as it becomes possible during a mission; during covert missions, the intelligence department also takes a more active role, providing the necessary classified and other information.', 'y', '666666', 'playing'),
	(10, 10, 5, 'Diplomatic Detachment', 'Responsible for representing the Federation and its interest, members of the Diplomatic Corps are members of the civilian branch of the Federation.', 'y', '800080', 'playing'),
	(11, 11, 6, 'Marine Detachment', 'When the standard security detail is not enough, marines come in and clean up; the marine detachment is a powerful tactical addition to any ship, responsible for partaking in personal combat, from sniping to melee.', 'y', '008000', 'playing'),
	(12, 12, 7, 'Starfighter Wing', 'The best pilots in Starfleet, they are responsible for piloting the starfighters in ship to ship battles, as well as providing escort for shuttles, and runabouts.', 'y', '406ceb', 'playing'),
	(13, 13, 8, 'Civilian Affairs', 'Civilians play an important role in Starfleet. Many civilian specialists across a number of fields work on occasion with Starfleet personnel as a Mission Specialist. In other cases, extra ship and station duties, such as running the ship''s lounge, are outsourced to a civilian contract.', 'y', 'ffffff', 'playing');" );
	
	/* create the globals table */
	mysql_query( "CREATE TABLE `sms_globals` (
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
	
	/* insert data into the globals table */
	mysql_query( "INSERT INTO `sms_globals` (`globalid`, `shipPrefix`, `shipName`, `shipRegistry`, `skin`, `allowedSkins`, `allowedRanks`, `fleet`, `fleetURL`, `tfMember`, `tfName`, `tfURL`, `tgMember`, `tgName`, `tgURL`, `hasWebmaster`, `webmasterName`, `webmasterEmail`, `showNews`, `showNewsNum`, `simmYear`, `rankSet`, `simmType`, `postCountDefault`, `manifestDisplay`, `useSamplePost`, `logList`, `bioShowPosts`, `bioShowLogs`, `bioShowPostsNum`, `bioShowLogsNum`, `jpCount`, `usePosting`, `useMissionNotes` ) 
	VALUES (1, '', '', '', 'default', 'default,cobalt,SMS_Lcars', 'default,dress', '', '', 'n', '', '', 'n', '', '', 'n', '', '', 'y', 3, '2384', 'default', 'ship', '14', 'split', 'y', 20, 'y', 'y', 5, 5, 'y', 'y', 'y' );" );
	
	/* create the menu items table */
	mysql_query( "CREATE TABLE `sms_menu_items` (
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
	
	/* populate the menu items table */
	mysql_query( "INSERT INTO `sms_menu_items` ( `menuid`, `menuGroup`, `menuOrder`, `menuTitle`, `menuLinkType`, `menuLink`, `menuAccess`, `menuMainSec`, `menuLogin`, `menuCat` )
	VALUES (1, 0, 0, 'Main', 'onsite', 'index.php?page=main', '', '', 'n', 'main'),
	(2, 0, 1, 'Personnel', 'onsite', 'index.php?page=manifest', '', '', 'n', 'main'),
	(3, 0, 2, 'The Ship', 'onsite', 'index.php?page=ship', '', '', 'n', 'main'),
	(4, 0, 3, 'The Simm', 'onsite', 'index.php?page=simm', '', '', 'n', 'main'),
	(5, 0, 4, 'Database', 'onsite', 'index.php?page=database', '', '', 'n', 'main'),
	(6, 0, 5, 'Control Panel', 'onsite', 'admin.php?page=main', '', '', 'y', 'main'),
	(7, 0, 0, 'Simm News', 'onsite', 'index.php?page=news', '', 'main', 'n', 'general'),
	(8, 0, 1, 'Site Credits', 'onsite', 'index.php?page=credits', '', 'main', 'n', 'general'),
	(9, 0, 2, 'Contact Us', 'onsite', 'index.php?page=contact', '', 'main', 'n', 'general'),
	(10, 0, 3, 'Join', 'onsite', 'index.php?page=join', '', 'main', 'n', 'general'),
	(11, 0, 0, 'Crew Manifest', 'onsite', 'index.php?page=manifest', '', 'personnel', 'n', 'general'),
	(12, 0, 1, 'NPC Manifest', 'onsite', 'index.php?page=manifest&disp=npcs', '', 'personnel', 'n', 'general'),
	(13, 0, 2, 'Open Positions', 'onsite', 'index.php?page=manifest&disp=open', '', 'personnel', 'n', 'general'),
	(14, 0, 3, 'Departed Crew', 'onsite', 'index.php?page=manifest&disp=past', '', 'personnel', 'n', 'general'),
	(15, 0, 4, 'Chain of Command', 'onsite', 'index.php?page=coc', '', 'personnel', 'n', 'general'),
	(16, 0, 5, 'Crew Awards', 'onsite', 'index.php?page=crewawards', '', 'personnel', 'n', 'general'),
	(17, 0, 6, 'Join', 'onsite', 'index.php?page=join', '', 'personnel', 'n', 'general'),
	(18, 0, 0, 'Ship History', 'onsite', 'index.php?page=history', '', 'ship', 'n', 'general'),
	(19, 0, 1, 'Specifications', 'onsite', 'index.php?page=specifications', '', 'ship', 'n', 'general'),
	(20, 0, 2, 'Ship Tour', 'onsite', 'index.php?page=tour', '', 'ship', 'n', 'general'),
	(21, 0, 3, 'Deck Listing', 'onsite', 'index.php?page=decklisting', '', 'ship', 'n', 'general'),
	(22, 0, 4, 'Departments', 'onsite', 'index.php?page=departments', '', 'ship', 'n', 'general'),
	(23, 0, 5, 'Database', 'onsite', 'index.php?page=database', '', 'ship', 'n', 'general'),
	(24, 0, 0, 'Current Mission', 'onsite', 'index.php?page=mission', '', 'simm', 'n', 'general'),
	(25, 0, 1, 'Mission Logs', 'onsite', 'index.php?page=missions', '', 'simm', 'n', 'general'),
	(26, 0, 2, 'Mission Summaries', 'onsite', 'index.php?page=summaries', '', 'simm', 'n', 'general'),
	(27, 1, 0, 'Personal Log List', 'onsite', 'index.php?page=loglist', '', 'simm', 'n', 'general'),
	(28, 1, 4, 'Crew Awards', 'onsite', 'index.php?page=crewawards', '', 'simm', 'n', 'general'),
	(29, 1, 5, 'Simm Statistics', 'onsite', 'index.php?page=statistics', '', 'simm', 'n', 'general'),
	(30, 1, 6, 'Simm Rules', 'onsite', 'index.php?page=rules', '', 'simm', 'n', 'general'),
	(31, 1, 7, 'Database', 'onsite', 'index.php?page=database', '', 'simm', 'n', 'general'),
	(32, 1, 8, 'Join', 'onsite', 'index.php?page=join', '', 'simm', 'n', 'general'),
	(33, 0, 0, 'Write Mission Post', 'onsite', 'admin.php?page=post&sub=mission', 'p_mission', 'post', 'y', 'admin'),
	(34, 0, 1, 'Write Joint Mission Post', 'onsite', 'admin.php?page=post&sub=jp', 'p_jp', 'post', 'y', 'admin'),
	(35, 0, 2, 'Write Personal Log', 'onsite', 'admin.php?page=post&sub=log', 'p_log', 'post', 'y', 'admin'),
	(36, 0, 3, 'Write News Item', 'onsite', 'admin.php?page=post&sub=news', 'p_news', 'post', 'y', 'admin'),
	(37, 0, 4, 'Send Private Message', 'onsite', 'admin.php?page=post&sub=message', 'p_pm', 'post', 'y', 'admin'),
	(38, 1, 0, 'Mission Notes', 'onsite', 'admin.php?page=post&sub=notes', 'p_missionnotes', 'post', 'y', 'admin'),
	(39, 2, 0, 'Add Mission Post', 'onsite', 'admin.php?page=post&sub=addpost', 'p_addmission', 'post', 'y', 'admin'),
	(40, 2, 1, 'Add Joint Mission Post', 'onsite', 'admin.php?page=post&sub=addjp', 'p_addjp', 'post', 'y', 'admin'),
	(41, 2, 2, 'Add Personal Log', 'onsite', 'admin.php?page=post&sub=addlog', 'p_addlog', 'post', 'y', 'admin'),
	(42, 2, 3, 'Add News Item', 'onsite', 'admin.php?page=post&sub=addnews', 'p_addnews', 'post', 'y', 'admin'),
	(43, 0, 0, 'About SMS', 'onsite', 'admin.php?page=reports&sub=about', 'r_about', 'reports', 'y', 'admin'),
	(44, 0, 1, 'Crew Activity', 'onsite', 'admin.php?page=reports&sub=activity', 'r_activity', 'reports', 'y', 'admin'),
	(45, 0, 2, 'Post Count', 'onsite', 'admin.php?page=reports&sub=count', 'r_count', 'reports', 'y', 'admin'),
	(46, 0, 3, 'Sim Progress', 'onsite', 'admin.php?page=reports&sub=progress', 'r_progress', 'reports', 'y', 'admin'),
	(47, 0, 4, 'Strike List', 'onsite', 'admin.php?page=reports&sub=strikes', 'r_strikes', 'reports', 'y', 'admin'),
	(48, 0, 5, 'Version History', 'onsite', 'admin.php?page=reports&sub=history', 'r_versions', 'reports', 'y', 'admin'),
	(49, 0, 0, 'User Account', 'onsite', 'admin.php?page=user&sub=account', 'u_account1', 'user', 'y', 'admin'),
	(50, 0, 0, 'User Account', 'onsite', 'admin.php?page=user&sub=account', 'u_account2', 'user', 'y', 'admin'),
	(51, 0, 1, 'Biography', 'onsite', 'admin.php?page=user&sub=bio', 'u_bio1', 'user', 'y', 'admin'),
	(52, 0, 1, 'Biography', 'onsite', 'admin.php?page=user&sub=bio', 'u_bio2', 'user', 'y', 'admin'),
	(53, 0, 1, 'Biography', 'onsite', 'admin.php?page=user&sub=bio', 'u_bio3', 'user', 'y', 'admin'),
	(54, 0, 2, 'Private Messages', 'onsite', 'admin.php?page=user&sub=inbox', 'u_inbox', 'user', 'y', 'admin'),
	(55, 0, 3, 'Request Status Change', 'onsite', 'admin.php?page=user&sub=status', 'u_status', 'user', 'y', 'admin'),
	(56, 0, 4, 'Site Options', 'onsite', 'admin.php?page=user&sub=site', 'u_options', 'user', 'y', 'admin'),
	(57, 0, 5, 'Award Nominations', 'onsite', 'admin.php?page=user&sub=nominate', 'u_nominate', 'user', 'y', 'admin'),
	(58, 0, 0, 'Site Globals', 'onsite', 'admin.php?page=manage&sub=globals', 'm_globals', 'manage', 'y', 'admin'),
	(59, 0, 1, 'Site Messages', 'onsite', 'admin.php?page=manage&sub=messages', 'm_messages', 'manage', 'y', 'admin'),
	(60, 0, 2, 'Specifications', 'onsite', 'admin.php?page=manage&sub=specifications', 'm_specs', 'manage', 'y', 'admin'),
	(61, 0, 3, 'News Categories', 'onsite', 'admin.php?page=manage&sub=newscategories', 'm_newscat3', 'manage', 'y', 'admin'),
	(62, 0, 4, 'User Access Levels', 'onsite', 'admin.php?page=manage&sub=access', 'x_access', 'manage', 'y', 'admin'),
	(63, 1, 0, 'Mission Posts', 'onsite', 'admin.php?page=manage&sub=posts', 'm_posts', 'manage', 'y', 'admin'),
	(64, 1, 1, 'Personal Logs', 'onsite', 'admin.php?page=manage&sub=logs', 'm_logs', 'manage', 'y', 'admin'),
	(65, 1, 2, 'News Items', 'onsite', 'admin.php?page=manage&sub=news', 'm_news', 'manage', 'y', 'admin'),
	(66, 1, 3, 'Mission Summaries', 'onsite', 'admin.php?page=manage&sub=summaries', 'm_missionsummaries', 'manage', 'y', 'admin'),
	(67, 1, 4, 'Mission Notes', 'onsite', 'admin.php?page=manage&sub=missionnotes', 'm_missionnotes', 'manage', 'y', 'admin'),
	(68, 2, 0, 'Create Character', 'onsite', 'admin.php?page=manage&sub=add', 'm_createcrew', 'manage', 'y', 'admin'),
	(69, 2, 1, 'All NPCs', 'onsite', 'admin.php?page=manage&sub=npcs', 'm_npcs1', 'manage', 'y', 'admin'),
	(70, 2, 1, 'All NPCs', 'onsite', 'admin.php?page=manage&sub=npcs', 'm_npcs2', 'manage', 'y', 'admin'),
	(71, 2, 2, 'All Characters', 'onsite', 'admin.php?page=manage&sub=crew', 'm_crew', 'manage', 'y', 'admin'),
	(72, 3, 0, 'Chain of Command', 'onsite', 'admin.php?page=manage&sub=coc', 'm_coc', 'manage', 'y', 'admin'),
	(73, 3, 1, 'Give Crew Award', 'onsite', 'admin.php?page=manage&sub=addaward', 'm_giveaward', 'manage', 'y', 'admin'),
	(74, 3, 2, 'Remove Crew Award', 'onsite', 'admin.php?page=manage&sub=removeaward', 'm_removeaward', 'manage', 'y', 'admin'),
	(75, 3, 3, 'Strike Player', 'onsite', 'admin.php?page=manage&sub=strikes', 'm_strike', 'manage', 'y', 'admin'),
	(76, 3, 4, 'User Post Moderation', 'onsite', 'admin.php?page=manage&sub=moderate', 'm_moderation', 'manage', 'y', 'admin'),
	(77, 4, 0, 'Missions', 'onsite', 'admin.php?page=manage&sub=missions', 'm_missions', 'manage', 'y', 'admin'),
	(78, 4, 1, 'Departments', 'onsite', 'admin.php?page=manage&sub=departments', 'm_departments', 'manage', 'y', 'admin'),
	(79, 4, 2, 'Positions', 'onsite', 'admin.php?page=manage&sub=positions', 'm_positions', 'manage', 'y', 'admin'),
	(80, 4, 3, 'Ranks', 'onsite', 'admin.php?page=manage&sub=ranks', 'm_ranks', 'manage', 'y', 'admin'),
	(81, 4, 4, 'Awards', 'onsite', 'admin.php?page=manage&sub=awards', 'm_awards', 'manage', 'y', 'admin'),
	(82, 4, 5, 'Database', 'onsite', 'admin.php?page=manage&sub=database', 'm_database', 'manage', 'y', 'admin'),
	(83, 4, 6, 'Ship Tour', 'onsite', 'admin.php?page=manage&sub=tour', 'm_tour', 'manage', 'y', 'admin'),
	(84, 4, 7, 'Deck Listing', 'onsite', 'admin.php?page=manage&sub=decklisting', 'm_decks', 'manage', 'y', 'admin'),
	(85, 0, 6, 'Menu Items', 'onsite', 'admin.php?page=manage&sub=menugeneral', 'x_menu', 'manage', 'y', 'admin'),
	(86, 0, 2, 'Crew Milestones', 'onsite', 'admin.php?page=reports&sub=milestones', 'r_milestones', 'reports', 'y', 'admin')" );
	
	/* create the messages table */
	mysql_query( "CREATE TABLE `sms_messages` (
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
	
	/* insert data into the messages table */
	mysql_query( "INSERT INTO `sms_messages` (`messageid`, `welcomeMessage`, `simmMessage`, `shipMessage`, `shipHistory`, `cpMessage`, `joinDisclaimer`, `samplePostQuestion`, `rules`, `acceptMessage`, `rejectMessage`, `siteCreditsPermanent`, `siteCredits` ) 
	VALUES (1, 'Define your welcome message through the site messages panel...', 'Define your simm\'s welcome message through the site messages section of the Control Panel.', 'Define your ship\'s welcome message through the site messages section of the Control Panel.', 'Define your ship/starbase\'s history through the site messages section of the Control Panel.', 'Define the control panel welcome message through the site messages section of the Control Panel.', 'Members are expected to follow the rules and regulations of both the sim and fleet at all times, both in character and out of character. By continuing, you affirm that you will sim in a proper and adequate manner. Members who choose to make ultra short posts, post very infrequently, or post posts with explicit content (above PG-13) will be removed immediately, and by continuing, you agree to this. In addition, in compliance with the Children\'s Online Privacy Protection Act of 1998 (COPPA), we do not accept players under the age of 13.  Any players found to be under the age of 13 will be immediately removed without question.  By agreeing to these terms, you are also saying that you are above the age of 13.', 'Define your sample post question/scenario through the site messages section of the Control Panel.', 'Define your ship rules through the site messages section of the Control Panel.', 'Thank you for your interest in our sim. I would like to officially welcome you aboard as the newest member of our crew!', 'Thank you for your interest in our sim. Unfortunately, at this time, I cannot offer you a position onboard our sim.', 'Editing or removal of the following credits constitutes a material breach of the SMS Terms of Use outlined at the <a href=\"http://www.anodyne-productions.com/index.php?cat=main&page=legal\" target=\"_blank\">SMS ToU</a> page.\r\n\r\nSMS 2 uses the open source browser detection library <a href=\"http://sourceforge.net/projects/phpsniff/\" target=\"_blank\">phpSniff</a> to check for various versions of browsers for maximum compatibility.\r\n\r\nThe SMS 2 Update notification system uses <a href=\"http://magpierss.sourceforge.net/\" target=\"_blank\">MagpieRSS</a> to parse the necessary XML file. Magpie is distributed under the GPL license. Questions and suggestions about MagpieRSS should be sent to <i>magpierss-general@lists.sf.net</i>.\r\n\r\nSMS 2 uses icons from the open source <a href=\"http://tango.freedesktop.org/Tango_Icon_Gallery\" target=\"_blank\">Tango Icon Library</a>. The update icon used by SMS was created by David VanScott as a derivative of work done for the Tango Icon Library.\r\n\r\nThe rank sets (DS9 Era Duty Uniform Style #2 and DS9 Era Dress Uniform Style #2) used in SMS 2 were created by Kuro-chan of <a href=\"http://www.kuro-rpg.net\" target=\"_blank\">Kuro-RPG</a>. Please do not copy or modify the images in any way, simply contact Kuro-chan and he will see to your rank needs.\r\n\r\n<a href=\"http://www.kuro-rpg.net/\" target=\"_blank\"><img src=\"images/kurorpg-banner.jpg\" border=\"0\" alt=\"Kuro-RPG\" /></a>', 'Please define your site credits in the Site Messages page...');" );
	
	/* create the missions table */
	mysql_query( "CREATE TABLE `sms_missions` (
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
	mysql_query( "CREATE TABLE `sms_news` (
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
	mysql_query( "CREATE TABLE `sms_news_categories` (
	  `catid` int(3) NOT NULL auto_increment,
	  `catName` varchar(50) NOT NULL default '',
	  `catUserLevel` int(2) NOT NULL default '0',
	  `catVisible` enum('y','n') NOT NULL default 'y',
	  PRIMARY KEY  (`catid`)
	) " . $tail . " AUTO_INCREMENT=5 ;" );
	
	/* insert data into the news category table */
	mysql_query( "INSERT INTO `sms_news_categories` (`catid`, `catName`, `catUserLevel`, `catVisible`) 
	VALUES (1, 'General News', 1, 'y'),
	(2, 'Simm Announcement', 2, 'y'),
	(3, 'Website Update', 3, 'y'),
	(4, 'Out of Character', 1, 'y');" );
	
	/* create the personal logs table */
	mysql_query( "CREATE TABLE `sms_personallogs` (
	  `logid` int(4) NOT NULL auto_increment,
	  `logAuthor` int(3) NOT NULL default '0',
	  `logTitle` varchar(100) NOT NULL default '',
	  `logContent` text NOT NULL,
	  `logPosted` varchar(50) NOT NULL default '',
	  `logStatus` enum( 'pending','saved','activated' ) NOT NULL default 'activated',
	  PRIMARY KEY  (`logid`)
	) " . $tail . " ;" );
	
	/* create the positions table */
	mysql_query( "CREATE TABLE `sms_positions` (
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
	
	/* insert the positions data */
	mysql_query( "INSERT INTO `sms_positions` (`positionid`, `positionOrder`, `positionName`, `positionDesc`, `positionDept`, `positionType`, `positionOpen` ) 
	VALUES (1, 0, 'Commanding Officer', 'Ultimately responsible for the ship and crew, the Commanding Officer is the most senior officer aboard a vessel. S/he is responsible for carrying out the orders of Starfleet, and for representing both Starfleet and the Federation.', 1, 'senior', 1),
	(2, 1, 'Executive Officer', 'The liaison between captain and crew, the Executive Officer acts as the disciplinarian, personnel manager, advisor to the captain, and much more. S/he is also one of only two officers, along with the Chief Medical Officer, that can remove a Commanding Officer from duty.', 1, 'senior', 1),
	(3, 2, 'Second Officer', 'At times the XO must assume command of a Starship or base, when this happens the XO needs the help of another officer to assume his/her role as XO. The second officer is not a stand alone position, but a role given to the highest ranked and trusted officer aboard. When required the Second Officer will assume the role of XO, or if needed CO, and performs their duties as listed, for as long as required.', 1, 'crew', 1),
	(4, 10, 'Chief of the Boat', 'The seniormost Chief Petty Officer (including Senior and Master Chiefs), regardless of rating, is designated by the Commanding Officer as the Chief of the Boat (for vessels) or Command Chief (for starbases). In addition to his or her departmental responsibilities, the COB/CC performs the following duties: serves as a liaison between the Commanding Officer (or Executive Officer) and the enlisted crewmen; ensures enlisted crews understand Command policies; advises the Commanding Officer and Executive Officer regarding enlisted morale, and evaluates the quality of noncommissioned officer leadership, management, and supervisory training.\r\n\r\nThe COB/CC works with the other department heads, Chiefs, supervisors, and crewmen to insure discipline is equitably maintained, and the welfare, morale, and health needs of the enlisted personnel are met. The COB/CC is qualified to temporarily act as Commanding or Executive Officer if so ordered. ', 1, 'crew', 1),
	(5, 15, 'Mission Advisor', 'Advises the Commanding Officer and Executive Officer on mission-specific areas of importance. Many times, the Mission Advisor knows just as much about the mission as the CO and XO do, if not even more. He or she also performs mission-specific tasks, and can take on any roles that a mission requires him or her to do. Concurrently holds another position, except in rare circumstances.', 1, 'crew', 1),
	(6, 0, 'Chief Flight Control Officer', 'Originally known as helm, or Flight Control Officer, CONN incorporates two job, Navigation and flight control. A Flight Control Officer must always be present on the bridge of a starship. S/he plots courses, supervises the computers piloting, corrects any flight deviations and pilots the ship manually when needed. The Chief Flight Control Officer is the senior most CONN Officer aboard, serving as a Senior Officer, and chief of the personnel under him/her.', 2, 'senior', 1),
	(7, 1, 'Assistant Chief Flight Control Officer', 'Originally known as helm, or Flight Control Officer, CONN incorporates two job, Navigation and flight control. A Flight Control Officer must always be present on the bridge of a starship. S/he plots courses, supervises the computers piloting, corrects any flight deviations and pilots the ship manually when needed. The Assistant Chief Flight Control Officer is the second senior most CONN Officer aboard and reports directly to the Chief Flight Control Officer.', 2, 'crew', 1),
	(8, 5, 'Flight Control Officer', 'Originally know as helm, or Flight Control Officer, CONN incorporates two job, navigation and flight control. A Flight Control Officer must always be present on the bridge of a starship, and every vessel has a number of Flight Control Officers to allow shift rotations. S/he plots courses, supervises the computers piloting, corrects any flight deviations and pilots the ship manually when needed. Flight Control Officers report to the Chief Flight Control Officer.', 2, 'crew', 5),
	(9, 10, 'Shuttle/Runabout Pilot', 'Responsible for piloting the various auxiliary craft (besides fighters), these pilots are responsible for transporting their passengers safely to and from locations that are inaccessible via the transporter.', 2, 'crew', 4),
	(10, 0, 'Chief Strategic Operations Officer', 'The Chief Strategic Operations Officer is responsible for coordinating all Starfleet and allied assets in within their designated area of space, as well as tactical analysis (in the absence of a dedicated tactical department) and intelligence gathering (in the absence of a dedicated intelligence department).', 3, 'senior', 1),
	(11, 1, 'Assistant Chief Strategic Operations Officer', 'The Assistant Chief Strategic Operations Officer is the second ranked officer in the Strategic Operations department. He or she answers to the Chief Strategic Operations Officer. He or she is responsible for coordinating Starfleet and allied assets within a designated area of space, as well as tactical analysis and intelligence gathering.', 3, 'crew', 1),
	(12, 5, 'Strategic Operations Officer', 'The Strategic Operations Officer is part of the Strategic Operations department. He or she answers to the Chief Strategic Operations Officer. He or she is responsible for coordinating Starfleet and allied assets within a designated area of space, as well as tactical analysis and intelligence gathering.', 3, 'crew', 1),
	(13, 0, 'Chief Security/Tactical Officer', 'The Chief Security Officer is called Chief of Security. Her/his duty is to ensure the safety of ship and crew. Some take it as their personal duty to protect the Commanding Officer/Executive Officer on away teams. She/he is also responsible for people under arrest and the safety of guests, liked or not.  S/he also is a department head and a member of the senior staff, responsible for all the crew members in her/his department and duty rosters. Security could be called the 24th century police force.\r\n\r\nThe Chief of Security role can also be combined with the Chief Tactical Officer position. ', 4, 'senior', 1),
	(14, 1, 'Assistant Chief Security/Tactical Officer', 'The Assistant Chief Security Officer is sometimes called Deputy of Security. S/he assists the Chief of Security in the daily work; in issues regarding Security and any administrative matters.  If required the Deputy must be able to take command of the Security department. ', 4, 'crew', 1),
	(15, 5, 'Security Officer', 'There are several Security Officers aboard each vessel. They are assigned to their duties by the Chief of Security and his/her Deputy and mostly guard sensitive areas, protect people, patrol, and handle other threats to the Federation.', 4, 'crew', 1),
	(16, 10, 'Tactical Officer', 'The Tactical Officers are the vessels gunmen. They assist the Chief Tactical Officer by running and maintaining the numerous weapons systems aboard the ship/starbase, and analysis and tactical planning of current missions. Very often Tactical Officers are also trained in ground combat and small unit tactics.', 4, 'crew', 1),
	(17, 15, 'Security Investigations Officer', 'The Security Investigations Officer is an Enlisted Officer. S/He fulfills the role of a special investigator or detective when dealing with Starfleet matters aboard ship or on a planet. Coordinates with the Chief Security Officer on all investigations as needed. The Security Investigations Officer reports to the Chief of Security.', 4, 'crew', 1),
	(18, 20, 'Brig Officer', 'The Brig Officer is a Security Officer who has chosen to specialize in a specific role. S/he guards the brig and its cells. But there are other duties associated with this post as well. S/he is responsible for any prisoner transport, and the questioning of prisoners. Often Brig Officers have a good knowledge of forcefield technology, and are experts in escaping such confinements.', 4, 'crew', 1),
	(19, 25, 'Master-At-Arms', 'The Master-at-Arms trains and supervises Security crewmen in departmental operations, repairs, and protocols; maintains duty assignments for all Security personnel; supervises weapons locker access and firearm deployment; and is qualified to temporarily act as Chief of Security if so ordered. The Master-at-Arms reports to the Chief of Security.', 4, 'crew', 1),
	(20, 0, 'Chief Operations Officer', 'The Chief Operations Officer has the primary responsibility of ensuring that ship functions, such as the use of the lateral sensor array, do not interfere with one and another. S/he must prioritize resource allocations, so that the most critical activities can have every chance of success. If so required, s/he can curtail shipboard functions if s/he thinks they will interfere with the ship''s current mission or routine operations.\r\n\r\nThe Chief Operations Officer oversees the Operations department, and is a member of the senior staff. ', 5, 'senior', 1),
	(21, 1, 'Assistant Chief Operations Officer', 'The Chief Operations Officer cannot man the bridge at all times. Extra personnel are needed to relive and maintain ship operations. The Operations Officers are thus assistants to the Chief, fulfilling his/her duties when required, and assuming the Operations consoles if required at any time.\r\n\r\nThe Assistant Chief Operations Officer is the second-in-command of the Operations Department, and can assume the role of Chief Operations Officer on a temporary or permanent basis if so needed. ', 5, 'crew', 1),
	(22, 5, 'Operations Officer', 'The Chief Operations Officer cannot man the bridge at all times. Extra personnel are needed to relive and maintain ship operations. The Operations Officers are thus assistants to the Chief, fulfilling his/her duties when required, and assuming the Operations consoles if required at any time.\r\n\r\nThe Operations Officer reports to the Chief Operations Officer.', 5, 'crew', 1),
	(23, 10, 'Quartermaster', 'Replicator usage can allow the fabrication of nearly any critical mission part, but large-scale replication is not considered energy-efficient except in emergency situations. However, in such situations, power usage is strictly limited, so it is unwise to depend upon the availability of replicated spare parts.\r\n\r\nThus a ship/facility must maintain a significant stock of spare parts in inventory at all times. The Quartermaster is the person responsible for the requesting of parts from Starfleet and maintaining the stock and inventory of all spare parts. All request for supplies are passed to the Quartermaster, who check and send the final request to the XO for final approval. A good Quartermaster is never caught short on supplies.\r\n\r\nThe Quartermaster trains and supervises crewmen in Bridge operations, repairs, and protocols and sets the agenda for instruction in general ship and starbase operations for the Boatswain''s Mate; maintains the ship''s log, the ship''s clock, and watch and duty assignments for all Bridge personnel; may assume any Bridge (i.e. CONN) or Operations role (i.e. transporter) as required; and is qualified to temporarily act as Commanding or Executive Officer if so ordered.\r\n\r\nQuartermasters ensure that all officers and crew perform their duties consistent with Starfleet directives. The Quartermaster reports to the Executive Officer.', 5, 'crew', 1),
	(24, 12, 'Boatswain', 'Each vessel and base has one Warrant Officer (or Chief Warrant Officer) who holds the position of Boatswain. The Boatswain (pronounced and also written \"Bosun\" or \"Bos\'n\") trains and supervises personnel (including both the ship\'s company or base personnel as well as passengers or vessels) in general ship and base operations, repairs, and protocols; maintains duty assignments for all Operations personnel; sets the agenda for instruction in general ship and base operations; supervises auxiliary and utility service personnel and daily ship or base maintenance; coordinates all personnel cross-trained in damage control operations and supervises damage control and emergency operations; may assume any Bridge or Operations role as required; and is qualified to temporarily act at Operations if so ordered.\r\n\r\nThe Boatswain reports to the Chief Operations Officer.', 5, 'crew', 1),
	(25, 0, 'Chief Engineering Officer', 'The Chief Engineer is responsible for the condition of all systems and equipment on board a Starfleet ship or facility. S/he oversees maintenance, repairs and upgrades of all equipment. S/he is also responsible for the many repairs teams during crisis situations.\r\n\r\nThe Chief Engineer is not only the department head but also a senior officer, responsible for all the crew members in her/his department and maintenance of the duty rosters.', 6, 'senior', 1),
	(26, 1, 'Assistant Chief Engineering Officer', 'The Assistant Chief Engineer assists the Chief Engineer in the daily work; in issues regarding mechanical, administrative matters and co-ordinating repairs with other departments.\r\n\r\nIf so required, the Assistant Chief Engineer must be able to take over as Chief Engineer, and thus must be versed in current information regarding the ship or facility. ', 6, 'crew', 1),
	(27, 5, 'Engineering Officer', 'There are several non-specialized engineers aboard of each vessel. They are assigned to their duties by the Chief Engineer and his Assistant, performing a number of different tasks as required, i.e. general maintenance and repair. Generally, engineers as assigned to more specialized engineering person to assist in there work is so requested by the specialized engineer.', 6, 'crew', 1),
	(28, 10, 'Communications Specialist', 'The Communications Specialist is a specialized engineer. Communication aboard a ship or facility takes two basic forms, voice and data. Both are handled by the onboard computer system and dedicated hardware. The vastness and complexity of this system requires a dedicated team to maintain the system.\r\n\r\nThe Communications Specialist is the officer in charge of this team, which is made up from NCO personnel, assigned to the team by the Assistant and Chief Engineer. The Communications Specialist reports to the Asst. and Chief Engineer.', 6, 'crew', 1),
	(29, 15, 'Computer Systems Specialist', 'The Computer Systems Specialist is a specialized Engineer. The new generation of Computer systems are highly developed. This system needs much maintenance and the Computer Systems Specialist was introduced to relieve the Science Officer, whose duty this was in the very early days.\r\n\r\nA small team is assigned to the Computer Systems Specialist, which is made up from NCO personnel assigned by the Assistant and Chief Engineer. The Computer Systems Specialist reports to the Assistant and Chief Engineer. ', 6, 'crew', 1),
	(30, 20, 'Damage Control Specialist', 'The Damage Control Specialist is a specialized Engineer. The Damage Control Specialist controls all damage control aboard the ship when it gets damaged in battle. S/he oversees all damage repair aboard the ship, and coordinates repair teams on the smaller jobs so the Chief Engineer can worry about other matters.\r\n\r\nA small team is assigned to the Damage Control Specialist which is made up from NCO personnel assigned by the Assistant and Chief Engineer. The Damage Control Specialist reports to the Assistant and Chief Engineer. ', 6, 'crew', 1),
	(31, 25, 'Matter/Energy Systems Specialist', 'The Matter / Energy Systems Specialist is a specialized Engineer. All aspect of matter energy transfers with the sole exception of the warp drive systems are handled by the Matter/Energy Systems Specialist. Such areas involved are transporter and replicator systems. The Matter/Energy Systems Specialist is the Officer in charge of a small team, which is made up from NCO personnel, assigned by the Assistant and Chief Engineer. The Matter/Energy Systems Specialist reports to the Assistant and Chief Engineer.', 6, 'crew', 1),
	(32, 30, 'Propulsion Specialist', 'Specializing in impulse and warp propulsion, these specialists are often specific to even a single class of ship due to the complexity of warp and impulse systems.', 6, 'crew', 1),
	(33, 35, 'Structural/Environmental Systems Specialist', 'The Structural and Environmental Systems Specialist is a specialised Engineer. From a small ship/facility to a large one, all requires constant monitoring. The hull, bulkheads, walls, Jeffrey''s tubes, turbolifts, structural integrity field, internal dampening field, and environmental systems are all monitored and maintained by this officer and his/her team.\r\n\r\nThe team assigned to the Structural and Environmental Systems Specialist is made up from NCO personnel, assigned by the Assistant and Chief Engineer. The Structural and Environmental Systems Specialist reports to the Asst and Chief Engineer. ', 6, 'crew', 1),
	(34, 40, 'Transporter Chief', 'The Transporter Chief is responsible for all transports to and from other ships and any planetary bodies. When transporting is not going on, the Transporter Chief is responsible for keeping the transporters running at peak efficiency.\r\n\r\nThe team assigned to the Transporter Chief is made up from NCO personnel, assigned by the Assistant and Chief Engineer. The Transporter Chief reports to the Assistant and Chief Engineer. ', 6, 'crew', 1),
	(35, 0, 'Chief Science Officer', 'The Chief Science Officer is responsible for all the scientific data the ship/facility collects, and the distribution of such data to specific section within the department for analysis. S/he is also responsible with providing the ship''s captain with scientific information needed for command decisions.\r\n\r\nS/he also is a department head and a member of the Senior Staff and responsible for all the crew members in her/his department and duty rosters.', 7, 'senior', 1),
	(36, 1, 'Assistant Chief Science Officer', 'The Assistant Chief Science Officer assists Chief Science Officer in all areas, such as administration, and analysis of scientific data. The Assistant often take part in specific analysis of important data along with the Chief Science Officer, however spends most time overseeing current project and their section heads.', 7, 'crew', 1),
	(37, 5, 'Science Officer', 'There are several general Science Officers aboard each vessel. They are assigned to their duties by the Chief Science Officer and his Assistant. Assignments include work for the Specialized Section heads, as well as duties for work being carried out by the Chief and Assistant.', 7, 'crew', 1),
	(38, 10, 'Alien Archaeologist/Anthropologist', 'Specialized Science Officer in charge of the Alien Culture Section. This role involves the study of all newly discovered alien species and life forms, from the long dead to thriving. There knowledge also involves current known alien species. Has close ties to the Historian.\r\n\r\nAnswers to the Chief Science Officer and Assistant Chief Science Officer. ', 7, 'crew', 1),
	(39, 15, 'Biologist', 'Specialized Science Officer in charge of the Biology Section. This role entails the study of biology, botany, zoology and many more Life Sciences. On larger ships there many be a number of Science Officers within this section, under the lead of the Biologist.', 7, 'crew', 1),
	(40, 20, 'Language Specialist', 'Specialized Communications Officer in charge of the Linguistics section. This role involves the study of new and old languages and text in an attempt to better understand and interpret their meaning.\r\n\r\nAnswers to the Chief and Assistant Chief Communications Officer. ', 7, 'crew', 1),
	(41, 25, 'Stellar Cartographer', 'Specialized Science Officer in charge of the Stellar Cartography bay. This role entails the mapping of all spatial phenomenon, and the implications of such phenomenon. Has close ties with the Physicist and Astrometrics Officer.', 7, 'crew', 1),
	(42, 0, 'Chief Medical Officer', 'The Chief Medical Officer is responsible for the physical health of the entire crew, but does more than patch up injured crew members. His/her function is to ensure that they do not get sick or injured to begin with, and to this end monitors their health and conditioning with regular check ups. If necessary, the Chief Medical Officer can remove anyone from duty, even a Commanding Officer. Besides this s/he is available to provide medical advice to any individual who requests it.\r\n\r\nAdditionally the Chief is also responsible for all aspect of the medical deck, such as the Medical labs, Surgical suites and Dentistry labs.\r\n\r\nS/he also is a department head and a member of the Senior Staff and responsible for all the crew members in her/his department and duty rosters. ', 8, 'senior', 1),
	(43, 1, 'Chief Counselor', 'Because of their training in psychology, technically the ship''s/facility''s Counselor is considered part of Starfleet medical. The Counselor is responsible both for advising the Commanding Officer in dealing with other people and races, and in helping crew members with personal, psychological, and emotional problems.\r\n\r\nThe Chief Counselor is considered a member of the Senior Staff. S/he is responsible for the crew in his/her department. The Chief Counselor is the Counselor with the highest rank and most experience. ', 8, 'senior', 1),
	(44, 2, 'Assistant Chief Medical Officer', 'A starship or facility has numerous personnel aboard, and thus the Chief Medical Officer cannot be expect to do all the work required. The Asst. Chief Medical Officer assists Chief in all areas, such as administration, and application of medical care.', 8, 'crew', 1),
	(45, 5, 'Medical Officer', 'Medical Officer undertake the majority of the work aboard the ship/facility, examining the crew, and administering medical care under the instruction of the Chief Medical Officer and Assistant Chief Medical Officer also run the other Medical areas not directly overseen by the Chief Medical Officer.', 8, 'crew', 1),
	(46, 10, 'Counselor', 'Because of their training in psychology, technically the ship''s/facility''s Counselor is considered part of Starfleet medical. The Counselor is responsible both for advising the Commanding Officer in dealing with other people and races, and in helping crew members with personal, psychological, and emotional problems.', 8, 'crew', 1),
	(47, 15, 'Nurse', 'Nurses are trained in basic medical care, and are capable of dealing with less serious medical cases. In more serious matters the nurse assist the medical officer in the examination and administration of medical care, be this injecting required drugs, or simply assuring the injured party that they will be ok. The Nurses also maintain the medical wards, overseeing the patients and ensuring they are receiving medication and care as instructed by the Medical Officer.', 8, 'crew', 2),
	(48, 20, 'Morale Officer', 'Responsible for keeping the morale of the crew high. Delivers regular reports on morale to the Executive Officer. The Morale Officer plans activities that will keep the crew''s morale and demeanor up. If any crew member is having problems, the Morale Officer can assist that crew member.', 8, 'crew', 1),
	(49, 0, 'Chief Intelligence Officer', 'Responsible for managing the intelligence department in its various facets, the Chief Intelligence officer often assists the Strategic Operations officer with information gathering and analysis, and then acts as a channel of information to the CO and bridge crew during combat situations.', 9, 'senior', 1),
	(50, 1, 'Assistant Chief Intelligence Officer', 'Responsible for aiding the Chief Intelligence Officer in managing the intelligence department in its various facets, often assisting the Strategic Operations officer with information gathering and analysis.', 9, 'crew', 1),
	(51, 5, 'Intelligence Officer', 'Responsible for gathering intelligence, an Intelligence officer has the patience to read through a database for hours on end, and the cunning to coax information from an unwilling giver. S/he must provide this information to the Chief Intelligence officer as it becomes needed.', 9, 'crew', 2),
	(52, 10, 'Infiltration Specialist', 'The Infiltration Specialist is trained the arts of covert operations and infiltration. They are trained to get into and out of enemy instillations, territory, etc. Once in, they can gather intel, or if needed plant explosives, and even in times of war capture of enemy personnel. The Infiltration Specialist reports to the Chief Intelligence Officer.', 9, 'crew', 1),
	(53, 15, 'Encryption Specialist', 'This NCO takes submitted Intelligence reports and runs them through algorithms, checking for keywords that denote mistyped classification and then puts the report into crypto form and sends them through the proper channels of communication to either on board ship consoles or off board to who ever needs to receive it. The Encryption Specialist reports to the Chief Intelligence Officer.', 9, 'crew', 1),
	(54, 0, 'Chief Diplomatic Officer', 'The Diplomatic Officer of each vessel/base must be familiar with a variety of areas: history, religion, politics, economics, and military, and understand how they affect potential threats. A wide range of operations can occur in response to these areas and threats. These operations occur within three general states of being: peacetime competition, conflict and war.\r\n\r\nS/he must be equally flexible and demonstrate initiative, agility, depth, synchronization, and improvisation to provide responsive legal services to his/her Commanding Officer as well a diplomatic advise on current status of an Alien Species both aligned and non aligned to the Federation.\r\n\r\nThe Chief Diplomatic Officer is in charge of the Diplomatic Corps Detachment. He or she oversees the operation of it, as well as makes sure everything in that department is carried out according to Starfleet Regulations. ', 10, 'senior', 1),
	(55, 1, 'Assistant Chief Diplomatic Officer', 'The Diplomatic Officer of each vessel/base must be familiar with a variety of areas: history, religion, politics, economics, and military, and understand how they affect potential threats. A wide range of operations can occur in response to these areas and threats. These operations occur within three general states of being: peacetime competition, conflict and war.\r\n\r\nS/he must be equally flexible and demonstrate initiative, agility, depth, synchronization, and improvisation to provide responsive legal services to his/her Commanding Officer aiding in official functions as prescribed by protocol, performing administrative duties, and other tasks as directed by the Chief Diplomatic Officer, as well a diplomatic advise on current status of an Alien Species both aligned and non aligned to the Federation.\r\n\r\nThe Assistant Chief Diplomatic Officer is the second-in-command of the Diplomatic Corps Detachment. If necessary, he or she can take the place of the Chief Diplomatic Officer on a temporary or permanent basis.', 10, 'crew', 1),
	(56, 5, 'Diplomatic Officer', 'The Diplomatic Officer of each vessel/base must be familiar with a variety of areas: history, religion, politics, economics, and military, and understand how they affect potential threats. A wide range of operations can occur in response to these areas and threats. These operations occur within three general states of being: peacetime competition, conflict and war.\r\n\r\nS/he must be equally flexible and demonstrate initiative, agility, depth, synchronization, and improvisation to provide responsive legal services to his/her Commanding Officer aiding in official functions as prescribed by protocol, performing administrative duties, and other tasks as directed by the Chief Diplomatic Officer and/or Assistant Chief Diplomatic Officer as well a diplomatic advice on current status of an Alien Species both aligned and non aligned to the Federation. ', 10, 'crew', 1),
	(57, 10, 'Diplomatic Corpsman', 'The Diplomatic Corpsman is a special position reserved for enlisted officers who wish to study diplomacy, and aid the department in its mission. Their duties consist of, but are not limited to, aiding Diplomatic Officers and Diplomat''s Aide in the construction of various legal documents, researching diplomatic archives, attending and aiding in the preparation for diplomatic functions, and other tasks as prescribed by the Chief Diplomatic Officer and/or Assistant Chief Diplomatic Officer. These individuals are qualified to undertake some of the responsibilities of a Diplomatic Officer, as their training are far less in-depth. They are, however, able to, and adequately trained to function as a paralegal when such services are required by a vessel/base''s crew.', 10, 'crew', 1),
	(58, 15, 'Diplomat''s Aide', 'S/he responds to the Ship/Base''s Chief Diplomatic Officer, and is required to be able to stand in and run the Diplomatic Department as required should the Chief Diplomatic Officer be absent for any reason.\r\n\r\nThe Aide must therefore be versed in all Diplomatic information regarding the current status of the Federation and its aligned and non aligned neighbours.', 10, 'crew', 1),
	(59, 0, 'Marine Commanding Officer', 'The Marine CO is responsible for all the Marine personnel assigned to the ship/facility. S/he is in required to take command of any special ground operations and lease such actions with security. The Marines could be called the 24th century commandos.\r\n\r\nThe CO can range from a Second Lieutenant on a small ship to a Lieutenant Colonel on a large facility or colony. Charged with the training, condition and tactical leadership of the Marine compliment, they are a member of the senior staff.\r\n\r\nAnswers to the Commanding Officer of the ship/facility. ', 11, 'senior', 1),
	(60, 1, 'Marine Executive Officer', 'The Executive Officer of the Marines, works like any Asst. Department head, removing some of the work load from the Marine CO and if the need arises taking on the role of Marine CO. S/he oversees the regular duties of the Marines, from regular drills to equipment training, assignment and supply request to the ship/facilities Materials Officer.\r\n\r\nAnswers to the Marine Commanding Officer.', 11, 'crew', 1),
	(61, 5, 'First Sergeant', 'The First Sergeant is the highest ranked Enlisted marine. S/He is in charge of all of the marine enlisted affairs in the detachment. They assist the Company or Detachment Commander as their Executive Officer would. They act as a bridge, closing the gap between the NCO''s and the Officers.\r\n\r\nAnswers To Marine Commanding Officer.', 11, 'crew', 1),
	(62, 10, 'Marine', 'Serving within a squad, the marine is trained in a variety of means of combat, from melee to ranged projectile to sniping.', 11, 'crew', 99),
	(63, 0, 'Wing Commander', 'Commander of all the squadrons within the wing.', 12, 'senior', 1),
	(64, 1, 'Wing Executive Officer', 'The first officer of the Wing.', 12, 'crew', 1),
	(65, 5, 'Squadron Leader', 'Leader of a starfighter squadron.', 12, 'crew', 1),
	(66, 10, 'Squadron Pilot', 'A pilot in the starfighter squadron', 12, 'crew', 1),
	(67, 0, 'Chef', 'Responsible for preparing all meals served in the Mess Hall and for the food during any diplomatic functions that may be held onboard.', 13, 'crew', 1),
	(68, 1, 'Other', '', 13, 'crew', 1);" );
	
	/* create the post table */
	mysql_query( "CREATE TABLE `sms_posts` (
	  `postid` int(4) NOT NULL auto_increment,
	  `postAuthor` varchar(40) NOT NULL default '',
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
	mysql_query( "CREATE TABLE `sms_privatemessages` (
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
	mysql_query( "CREATE TABLE `sms_ranks` (
	  `rankid` int(3) NOT NULL auto_increment,
	  `rankOrder` int(2) NOT NULL default '0',
	  `rankName` varchar(32) NOT NULL default '',
	  `rankImage` varchar(255) NOT NULL default '',
	  `rankType` int(1) NOT NULL default '1',
	  `rankDisplay` enum('y','n') NOT NULL default 'y',
	  `rankClass` int(3) NOT NULL default '0',
	  PRIMARY KEY  (`rankid`)
	) " . $tail . " AUTO_INCREMENT=185 ;" );
	
	/* insert the ranks data */
	mysql_query( "INSERT INTO `sms_ranks` (`rankid`, `rankOrder`, `rankName`, `rankImage`, `rankType`, `rankDisplay`, `rankClass`) 
	VALUES (1, 1, 'Fleet Admiral', 'Starfleet/r-a5.png', 1, 'y', 1),
	(2, 1, 'Fleet Admiral', 'Starfleet/y-a5.png', 1, 'y', 2),
	(3, 1, 'Fleet Admiral', 'Starfleet/t-a5.png', 1, 'y', 3),
	(4, 1, 'Field Marshall', 'Marine/g-a5.png', 1, 'y', 6),
	(5, 1, 'Fleet Admiral', 'Starfleet/s-a5.png', 1, 'y', 4),
	(6, 1, 'Fleet Admiral', 'Starfleet/v-a5.png', 1, 'y', 5),
	(7, 1, 'Fleet Admiral', 'Starfleet/c-a5.png', 1, 'y', 7),
	(8, 2, 'Admiral', 'Starfleet/r-a4.png', 1, 'y', 1),
	(9, 2, 'Admiral', 'Starfleet/y-a4.png', 1, 'y', 2),
	(10, 2, 'Admiral', 'Starfleet/t-a4.png', 1, 'y', 3),
	(11, 2, 'General', 'Marine/g-a4.png', 1, 'y', 6),
	(12, 2, 'Admiral', 'Starfleet/s-a4.png', 1, 'y', 4),
	(13, 2, 'Admiral', 'Starfleet/v-a4.png', 1, 'y', 5),
	(14, 2, 'Admiral', 'Starfleet/c-a4.png', 1, 'y', 7),
	(15, 3, 'Vice Admiral', 'Starfleet/r-a3.png', 1, 'y', 1),
	(16, 3, 'Vice Admiral', 'Starfleet/y-a3.png', 1, 'y', 2),
	(17, 3, 'Vice Admiral', 'Starfleet/t-a3.png', 1, 'y', 3),
	(18, 3, 'Lieutenant General', 'Marine/g-a3.png', 1, 'y', 6),
	(19, 3, 'Vice Admiral', 'Starfleet/s-a3.png', 1, 'y', 4),
	(20, 3, 'Vice Admiral', 'Starfleet/v-a3.png', 1, 'y', 5),
	(21, 3, 'Vice Admiral', 'Starfleet/c-a3.png', 1, 'y', 7),
	(22, 4, 'Rear Admiral', 'Starfleet/r-a2.png', 1, 'y', 1),
	(23, 4, 'Rear Admiral', 'Starfleet/y-a2.png', 1, 'y', 2),
	(24, 4, 'Rear Admiral', 'Starfleet/t-a2.png', 1, 'y', 3),
	(25, 4, 'Major General', 'Marine/g-a2.png', 1, 'y', 6),
	(26, 4, 'Rear Admiral', 'Starfleet/s-a2.png', 1, 'y', 4),
	(27, 4, 'Rear Admiral', 'Starfleet/v-a2.png', 1, 'y', 5),
	(28, 4, 'Rear Admiral', 'Starfleet/c-a2.png', 1, 'y', 7),
	(29, 5, 'Commodore', 'Starfleet/r-a1.png', 1, 'y', 1),
	(30, 5, 'Commodore', 'Starfleet/y-a1.png', 1, 'y', 2),
	(31, 5, 'Commodore', 'Starfleet/t-a1.png', 1, 'y', 3),
	(32, 5, 'Brigadier General', 'Marine/g-a1.png', 1, 'y', 6),
	(33, 5, 'Commodore', 'Starfleet/s-a1.png', 1, 'y', 4),
	(34, 5, 'Commodore', 'Starfleet/v-a1.png', 1, 'y', 5),
	(35, 5, 'Commodore', 'Starfleet/c-a1.png', 1, 'y', 7),
	(36, 6, 'Captain', 'Starfleet/r-o6.png', 1, 'y', 1),
	(37, 6, 'Captain', 'Starfleet/y-o6.png', 1, 'y', 2),
	(38, 6, 'Captain', 'Starfleet/t-o6.png', 1, 'y', 3),
	(39, 6, 'Colonel', 'Marine/g-o6.png', 1, 'y', 6),
	(40, 6, 'Captain', 'Starfleet/s-o6.png', 1, 'y', 4),
	(41, 6, 'Captain', 'Starfleet/v-o6.png', 1, 'y', 5),
	(42, 6, 'Captain', 'Starfleet/c-o6.png', 1, 'y', 7),
	(43, 7, 'Commander', 'Starfleet/r-o5.png', 1, 'y', 1),
	(44, 7, 'Commander', 'Starfleet/y-o5.png', 1, 'y', 2),
	(45, 7, 'Commander', 'Starfleet/t-o5.png', 1, 'y', 3),
	(46, 7, 'Lieutenant Colonel', 'Marine/g-o5.png', 1, 'y', 6),
	(47, 7, 'Commander', 'Starfleet/s-o5.png', 1, 'y', 4),
	(48, 7, 'Commander', 'Starfleet/v-o5.png', 1, 'y', 5),
	(49, 7, 'Commander', 'Starfleet/c-o5.png', 1, 'y', 7),
	(50, 8, 'Lieutenant Commander', 'Starfleet/r-o4.png', 1, 'y', 1),
	(51, 8, 'Lieutenant Commander', 'Starfleet/y-o4.png', 1, 'y', 2),
	(52, 8, 'Lieutenant Commander', 'Starfleet/t-o4.png', 1, 'y', 3),
	(53, 8, 'Major', 'Marine/g-o4.png', 1, 'y', 6),
	(54, 8, 'Lieutenant Commander', 'Starfleet/s-o4.png', 1, 'y', 4),
	(55, 8, 'Lieutenant Commander', 'Starfleet/v-o4.png', 1, 'y', 5),
	(56, 8, 'Lieutenant Commander', 'Starfleet/c-o4.png', 1, 'y', 7),
	(57, 9, 'Lieutenant', 'Starfleet/r-o3.png', 1, 'y', 1),
	(58, 9, 'Lieutenant', 'Starfleet/y-o3.png', 1, 'y', 2),
	(59, 9, 'Lieutenant', 'Starfleet/t-o3.png', 1, 'y', 3),
	(60, 9, 'Marine Captain', 'Marine/g-o3.png', 1, 'y', 6),
	(61, 9, 'Lieutenant', 'Starfleet/s-o3.png', 1, 'y', 4),
	(62, 9, 'Lieutenant', 'Starfleet/v-o3.png', 1, 'y', 5),
	(63, 9, 'Lieutenant', 'Starfleet/c-o3.png', 1, 'y', 7),
	(64, 10, 'Lieutenant JG', 'Starfleet/r-o2.png', 1, 'y', 1),
	(65, 10, 'Lieutenant JG', 'Starfleet/y-o2.png', 1, 'y', 2),
	(66, 10, 'Lieutenant JG', 'Starfleet/t-o2.png', 1, 'y', 3),
	(67, 10, '1st Lieutenant', 'Marine/g-o2.png', 1, 'y', 6),
	(68, 10, 'Lieutenant JG', 'Starfleet/s-o2.png', 1, 'y', 4),
	(69, 10, 'Lieutenant JG', 'Starfleet/v-o2.png', 1, 'y', 5),
	(70, 10, 'Lieutenant JG', 'Starfleet/c-o2.png', 1, 'y', 7),
	(71, 11, 'Ensign', 'Starfleet/r-o1.png', 1, 'y', 1),
	(72, 11, 'Ensign', 'Starfleet/y-o1.png', 1, 'y', 2),
	(73, 11, 'Ensign', 'Starfleet/t-o1.png', 1, 'y', 3),
	(74, 11, '2nd Lieutenant', 'Marine/g-o1.png', 1, 'y', 6),
	(75, 11, 'Ensign', 'Starfleet/s-o1.png', 1, 'y', 4),
	(76, 11, 'Ensign', 'Starfleet/v-o1.png', 1, 'y', 5),
	(77, 11, 'Ensign', 'Starfleet/c-o1.png', 1, 'y', 7),
	(78, 12, 'Master Warrant Officer', 'Marine/g-w5.png', 1, 'y', 6),
	(79, 12, 'Chief Warrant Officer 3rd Class', 'Starfleet/r-w4.png', 1, 'y', 1),
	(80, 12, 'Chief Warrant Officer 3rd Class', 'Starfleet/y-w4.png', 1, 'y', 2),
	(81, 12, 'Chief Warrant Officer 3rd Class', 'Starfleet/t-w4.png', 1, 'y', 3),
	(82, 13, 'Chief Warrant Officer 3rd Class', 'Marine/g-w4.png', 1, 'y', 6),
	(83, 12, 'Chief Warrant Officer 3rd Class', 'Starfleet/s-w4.png', 1, 'y', 4),
	(84, 12, 'Chief Warrant Officer 3rd Class', 'Starfleet/v-w4.png', 1, 'y', 5),
	(85, 12, 'Chief Warrant Officer 3rd Class', 'Starfleet/c-w4.png', 1, 'y', 7),
	(86, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/r-w3.png', 1, 'y', 1),
	(87, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/y-w3.png', 1, 'y', 2),
	(88, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/t-w3.png', 1, 'y', 3),
	(89, 14, 'Chief Warrant Officer 2nd Class', 'Marine/g-w3.png', 1, 'y', 6),
	(90, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/s-w3.png', 1, 'y', 4),
	(91, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/v-w3.png', 1, 'y', 5),
	(92, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/c-w3.png', 1, 'y', 7),
	(93, 14, 'Chief Warrant Officer 1st Class', 'Starfleet/r-w2.png', 1, 'y', 1),
	(94, 14, 'Chief Warrant Officer 1st Class', 'Starfleet/y-w2.png', 1, 'y', 2),
	(95, 14, 'Chief Warrant Officer 1st Class', 'Starfleet/t-w2.png', 1, 'y', 3),
	(96, 15, 'Chief Warrant Officer 1st Class', 'Marine/g-w2.png', 1, 'y', 6),
	(97, 14, 'Chief Warrant Officer 1st Class', 'Starfleet/s-w2.png', 1, 'y', 4),
	(98, 14, 'Chief Warrant Officer 1st Class', 'Starfleet/v-w2.png', 1, 'y', 5),
	(99, 14, 'Chief Warrant Officer 1st Class', 'Starfleet/c-w2.png', 1, 'y', 7),
	(100, 15, 'Warrant Officer', 'Starfleet/r-w1.png', 1, 'y', 1),
	(101, 15, 'Warrant Officer', 'Starfleet/y-w1.png', 1, 'y', 2),
	(102, 15, 'Warrant Officer', 'Starfleet/t-w1.png', 1, 'y', 3),
	(103, 16, 'Warrant Officer', 'Marine/g-w1.png', 1, 'y', 6),
	(104, 15, 'Warrant Officer', 'Starfleet/s-w1.png', 1, 'y', 4),
	(105, 15, 'Warrant Officer', 'Starfleet/v-w1.png', 1, 'y', 5),
	(106, 15, 'Warrant Officer', 'Starfleet/c-w1.png', 1, 'y', 7),
	(107, 16, 'Master Chief Petty Officer', 'Starfleet/r-e9.png', 1, 'y', 1),
	(108, 16, 'Master Chief Petty Officer', 'Starfleet/y-e9.png', 1, 'y', 2),
	(109, 16, 'Master Chief Petty Officer', 'Starfleet/t-e9.png', 1, 'y', 3),
	(110, 16, 'Master Chief Petty Officer', 'Starfleet/s-e9.png', 1, 'y', 4),
	(111, 16, 'Master Chief Petty Officer', 'Starfleet/v-e9.png', 1, 'y', 5),
	(112, 16, 'Master Chief Petty Officer', 'Starfleet/c-e9.png', 1, 'y', 7),
	(113, 17, 'Senior Chief Petty Officer', 'Starfleet/r-e8.png', 1, 'y', 1),
	(114, 17, 'Senior Chief Petty Officer', 'Starfleet/y-e8.png', 1, 'y', 2),
	(115, 17, 'Senior Chief Petty Officer', 'Starfleet/t-e8.png', 1, 'y', 3),
	(116, 17, 'Sergeant Major', 'Marine/g-e9.png', 1, 'y', 6),
	(117, 17, 'Senior Chief Petty Officer', 'Starfleet/s-e8.png', 1, 'y', 4),
	(118, 17, 'Senior Chief Petty Officer', 'Starfleet/v-e8.png', 1, 'y', 5),
	(119, 17, 'Senior Chief Petty Officer', 'Starfleet/c-e8.png', 1, 'y', 7),
	(120, 18, 'Chief Petty Officer', 'Starfleet/r-e7.png', 1, 'y', 1),
	(121, 18, 'Chief Petty Officer', 'Starfleet/y-e7.png', 1, 'y', 2),
	(122, 18, 'Chief Petty Officer', 'Starfleet/t-e7.png', 1, 'y', 3),
	(123, 18, 'Master Sergeant', 'Marine/g-e8.png', 1, 'y', 6),
	(124, 18, 'Chief Petty Officer', 'Starfleet/s-e7.png', 1, 'y', 4),
	(125, 18, 'Chief Petty Officer', 'Starfleet/v-e7.png', 1, 'y', 5),
	(126, 18, 'Chief Petty Officer', 'Starfleet/c-e7.png', 1, 'y', 7),
	(127, 19, 'Petty Officer 1st Class', 'Starfleet/r-e6.png', 1, 'y', 1),
	(128, 19, 'Petty Officer 1st Class', 'Starfleet/y-e6.png', 1, 'y', 2),
	(129, 19, 'Petty Officer 1st Class', 'Starfleet/t-e6.png', 1, 'y', 3),
	(130, 19, 'Gunnery Sergeant', 'Marine/g-e7.png', 1, 'y', 6),
	(131, 19, 'Petty Officer 1st Class', 'Starfleet/s-e6.png', 1, 'y', 4),
	(132, 19, 'Petty Officer 1st Class', 'Starfleet/v-e6.png', 1, 'y', 5),
	(133, 19, 'Petty Officer 1st Class', 'Starfleet/c-e6.png', 1, 'y', 7),
	(134, 20, 'Petty Officer 2nd Class', 'Starfleet/r-e5.png', 1, 'y', 1),
	(135, 20, 'Petty Officer 2nd Class', 'Starfleet/y-e5.png', 1, 'y', 2),
	(136, 20, 'Petty Officer 2nd Class', 'Starfleet/t-e5.png', 1, 'y', 3),
	(137, 20, 'Staff Sergeant', 'Marine/g-e6.png', 1, 'y', 6),
	(138, 20, 'Petty Officer 2nd Class', 'Starfleet/s-e5.png', 1, 'y', 4),
	(139, 20, 'Petty Officer 2nd Class', 'Starfleet/v-e5.png', 1, 'y', 5),
	(140, 20, 'Petty Officer 2nd Class', 'Starfleet/c-e5.png', 1, 'y', 7),
	(141, 21, 'Petty Officer 3rd Class', 'Starfleet/r-e4.png', 1, 'y', 1),
	(142, 21, 'Petty Officer 3rd Class', 'Starfleet/y-e4.png', 1, 'y', 2),
	(143, 21, 'Petty Officer 3rd Class', 'Starfleet/t-e4.png', 1, 'y', 3),
	(144, 21, 'Sergeant', 'Marine/g-e5.png', 1, 'y', 6),
	(145, 21, 'Petty Officer 3rd Class', 'Starfleet/s-e4.png', 1, 'y', 4),
	(146, 21, 'Petty Officer 3rd Class', 'Starfleet/v-e4.png', 1, 'y', 5),
	(147, 21, 'Petty Officer 3rd Class', 'Starfleet/c-e4.png', 1, 'y', 7),
	(148, 22, 'Crewman 1st Class', 'Starfleet/r-e3.png', 1, 'y', 1),
	(149, 22, 'Crewman 1st Class', 'Starfleet/y-e3.png', 1, 'y', 2),
	(150, 22, 'Crewman 1st Class', 'Starfleet/t-e3.png', 1, 'y', 3),
	(151, 22, 'Corporal', 'Marine/g-e4.png', 1, 'y', 6),
	(152, 22, 'Crewman 1st Class', 'Starfleet/s-e3.png', 1, 'y', 4),
	(153, 22, 'Crewman 1st Class', 'Starfleet/v-e3.png', 1, 'y', 5),
	(154, 22, 'Crewman 1st Class', 'Starfleet/c-e3.png', 1, 'y', 7),
	(155, 23, 'Crewman 2nd Class', 'Starfleet/r-e2.png', 1, 'y', 1),
	(156, 23, 'Crewman 2nd Class', 'Starfleet/y-e2.png', 1, 'y', 2),
	(157, 23, 'Crewman 2nd Class', 'Starfleet/t-e2.png', 1, 'y', 3),
	(158, 23, 'Lance Corporal', 'Marine/g-e3.png', 1, 'y', 6),
	(159, 23, 'Crewman 2nd Class', 'Starfleet/s-e2.png', 1, 'y', 4),
	(160, 23, 'Crewman 2nd Class', 'Starfleet/v-e2.png', 1, 'y', 5),
	(161, 23, 'Crewman 2nd Class', 'Starfleet/c-e2.png', 1, 'y', 7),
	(162, 24, 'Crewman 3rd Class', 'Starfleet/r-e1.png', 1, 'y', 1),
	(163, 24, 'Crewman 3rd Class', 'Starfleet/y-e1.png', 1, 'y', 2),
	(164, 24, 'Crewman 3rd Class', 'Starfleet/t-e1.png', 1, 'y', 3),
	(165, 24, 'Private 1st Class', 'Marine/g-e2.png', 1, 'y', 6),
	(166, 24, 'Crewman 3rd Class', 'Starfleet/s-e1.png', 1, 'y', 4),
	(167, 24, 'Crewman 3rd Class', 'Starfleet/v-e1.png', 1, 'y', 5),
	(168, 24, 'Crewman 3rd Class', 'Starfleet/c-e1.png', 1, 'y', 7),
	(169, 25, 'Crewman Recruit', 'Starfleet/r-e0.png', 1, 'y', 1),
	(170, 25, 'Crewman Recruit', 'Starfleet/y-e0.png', 1, 'y', 2),
	(171, 25, 'Crewman Recruit', 'Starfleet/t-e0.png', 1, 'y', 3),
	(172, 25, 'Private', 'Marine/g-e1.png', 1, 'y', 6),
	(173, 25, 'Crewman Recruit', 'Starfleet/s-e0.png', 1, 'y', 4),
	(174, 25, 'Crewman Recruit', 'Starfleet/v-e0.png', 1, 'y', 5),
	(175, 25, 'Crewman Recruit', 'Starfleet/c-e0.png', 1, 'y', 7),
	(176, 26, '', 'Starfleet/r-blank.png', 1, 'y', 1),
	(177, 26, '', 'Starfleet/y-blank.png', 1, 'y', 2),
	(178, 26, '', 'Starfleet/t-blank.png', 1, 'y', 3),
	(179, 26, '', 'Marine/g-blank.png', 1, 'y', 6),
	(180, 26, '', 'Starfleet/s-blank.png', 1, 'y', 4),
	(181, 26, '', 'Starfleet/v-blank.png', 1, 'y', 5),
	(182, 26, '', 'Starfleet/c-blank.png', 1, 'y', 7),
	(183, 1, '', 'Starfleet/w-blank.png', 1, 'y', 8),
	(184, 2, '', 'Starfleet/b-blank.png', 1, 'y', 8);" );
	
	/* create the specs table */
	mysql_query( "CREATE TABLE `sms_specs` (
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
	
	/* insert the specs data */
	mysql_query( "INSERT INTO `sms_specs` (`specid`, `shipClass`, `shipRole`, `duration`, `durationUnit`, `refit`, `refitUnit`, `resupply`, `resupplyUnit`, `length`, `height`, `width`, `decks`, `complimentEmergency`, `complimentOfficers`, `complimentEnlisted`, `complimentMarines`, `complimentCivilians`, `warpCruise`, `warpMaxCruise`, `warpEmergency`, `warpMaxTime`, `warpEmergencyTime`, `phasers`, `torpedoLaunchers`, `torpedoCompliment`, `defensive`, `shields`, `shuttlebays`, `hasShuttles`, `hasRunabouts`, `hasFighters`, `hasTransports`, `shuttles`, `runabouts`, `fighters`, `transports`) 
	VALUES (1, '', '', '', 'Years', '', 'Years', '', 'Years', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'y', 'y', 'y', 'y', '', '', '', '');" );
	
	/* create the starbase docking table */
	mysql_query( "CREATE TABLE `sms_starbase_docking` (
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
	mysql_query( "CREATE TABLE `sms_strikes` (
	  `strikeid` int(4) NOT NULL auto_increment,
	  `crewid` int(3) NOT NULL default '0',
	  `strikeDate` varchar(50) NOT NULL default '',
	  `reason` text NOT NULL,
	  `number` int(3) NOT NULL default '0',
	  PRIMARY KEY  (`strikeid`)
	) " . $tail . " ;" );

	/* add the system table */
	mysql_query( "CREATE TABLE `sms_system` (
	  `sysid` int(2) NOT NULL auto_increment,
	  `sysuid` varchar(20) NOT NULL default '',
	  `sysVersion` varchar(10) NOT NULL default '',
	  `sysBaseVersion` varchar(10) NOT NULL default '',
	  `sysIncrementVersion` varchar(10) NOT NULL default '',
	  `sysLaunchStatus` enum('y','n') NOT NULL default 'n',
	  PRIMARY KEY  (`sysid`)
	) " . $tail . " ;" );
	
	/** setup the unique sim id **/
	
	/* define the length */
	$length = "20";
	
	/* start with a blank password */
	$string = "";
	
	/* define possible characters */
	$possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
	
	/* set up a counter */
	$i = 0; 
	
	/* add random characters to $password until $length is reached */
	while( $i < $length ) { 
	
		/* pick a random character from the possible ones */
		$char = substr( $possible, mt_rand( 0, strlen( $possible )-1 ), 1 );
		
		/* we don't want this character if it's already in the password */
		if( !strstr( $string, $char ) ) { 
			$string .= $char;
			$i++;
		}
	
	}

	/* populate the system table with data */
	mysql_query( "INSERT INTO sms_system ( sysid, sysuid, sysVersion, sysBaseVersion, sysIncrementVersion ) VALUES ( '1', '$string', '2.5.6', '2.5', '.6' )" );
	
	/* add the system versions table */
	mysql_query( "CREATE TABLE `sms_system_versions` (
		`versionid` int(3) NOT NULL auto_increment,
		`version` varchar(50) NOT NULL default '',
		`versionDate` varchar(50) NOT NULL default '',
		`versionShortDesc` text NOT NULL,
		`versionDesc` text NOT NULL,
		PRIMARY KEY  (`versionid`)
	) " . $tail . " ;" );
	
	/* populate the versions table with data */
	mysql_query( "INSERT INTO `sms_system_versions` ( `versionid`, `version`, `versionDate`, `versionShortDesc`, `versionDesc` )
	VALUES (1, '2.0.1', '1153890000', '', 'Fixed issues relating to bio and account management;Fixed bug with the join form;Fixed bug with personal log posting;Fixed issue with database field length issues on several variables;Fixed bio image reference issues that wouldn\'t allow users to specify an off-site location for their character image;Added minor functionality to the crew manifest, specs, and message items'),
	(2, '2.0.2', '1155099600', '', 'Fixed issues relating to bio and account management (try number 2);Fixed several issues associated with emails being sent out by the system;Added rank and position error checking for the crew listing in the admin control panel. List now provides an error message for users that have an invalid rank and/or position;Fixed manifest display bug;Fixed bug associated with whitespace and updating skins'),
	(3, '2.1.0', '1158123600', '', 'Added database feature left out of the 2.0 release because of time constraints;Added tour feature'),
	(4, '2.1.1', '1158382800', '', 'Fixed bug associated with excessive line breaks in database entries caused by the PHP nl2br() function that was being used;Added ability for COs to edit individual database items'),
	(5, '2.2.0', '1159506000', '', 'Added confirmation prompts for users who have JavaScript turned on in their browser. When deleting an item, the system will prompt the user if they are sure they want to delete it;Added ability for COs to decide whether a JP counts as a single post or as as many posts as there are authors in the JP;Added a tag field to the mission posts to allow users to specify their post tags at the start. The email sent out will display the tag right at the top so another user knows right from the beginning whether or not their character is involved or tagged in the post;Added the ability for users to save posts, logs, and news items to come back to and keep working on;Fixed bug where XOs didn\'t have the right access permissions for creating and editing NPCs;Added ability to set activation and deactivation options for both players as well as NPCs;Fixed bug on the full manifest where positions that were turned off still showed up;Fixed image reference bug that had the tour section looking in the wrong place for tour images;Fixed bug where deleting a database item while it was selected would cause it to stay displayed in the browser despite not existing'),
	(6, '2.2.1', '1159592400', '', 'Fixed bug where posts and logs had disappeared from both crew biographies as well as the admin control panel'),
	(7, '2.3.0', '1163394000', '', 'Added ability for users to set the rank sets they see on the site when they\'re logged in;Improved rank management to include the ability to change other rank sets;Updated icons throughout the system;Added ability for COs to define an accept and reject message that they can edit when accepting or rejecting a player;Fixed bug where posts that were saved first wouldn\'t have an accurate timestamp;Fixed bug where systems that don\'t use SMS posting don\'t have access to posting news items;Fixed bug where simm statistics was in the menu even if the system doesn\'t use SMS posting;Fixed bug when department heads went to create an NPC, they couldn\'t create them at their own rank, just below;Fixed bug where rank select menus for department heads were cut off, not allowing them to select the top items;Added ability for COs to remove an award from a player through a GUI instead of a textbox;Fixed bug where NPCs with an invalid rank and/or position wouldn\'t show up in the NPC manifest (or full manifest). Like playing characters, if an NPC now has an invalid rank and/or position, it\'ll show an error at the bottom of the complete listing of NPCs in the control panel to allow someone to go in and fix the problem;Added links in a player\'s bio for COs to add or remove awards;Improved update notification. System will now check to make sure that both the files and the database are using the same version coming out of the the XML file and if they\'re not, display appropriate warning messages;The system will now email the authors of a JP whenever anyone saves it, notifying them that it\'s been changed;Fixed bug where posts that had been saved and then were posted wouldn\'t show any author info in the email;Fixed bug where posts and logs weren\'t ordered by date posted;Fixed bug when pending posts, logs, or news items were activated, they weren\'t mailed out to the crew;Fixed bug where, besides the from line, there was nowhere in a news item where the author was displayed;Added ability for COs to moderate posts, logs, or news from specific players;Updated layout of site globals page to make more sense;Fixed bug on accounts page for people without access to change a player\'s character type where both the $crewType and switch variables were being echoed out;Fixed bug where crew awards listing was trying to pull the small image from /images/awards instead of the large images from /images/awards/large;Improved efficiency on main control panel page by putting access level check before the system tries to check for pending items. If a user doesn\'t have level 4 access or higher, it won\'t even try to run the function now;Fixed issue where 2.2.0 and 2.2.1 didn\'t address changing the dockingStatus field in sms_starbase_docking'),
	(8, '2.3.1', '1163653200', '', 'Fixed bug when posting PHP error involving in_array() was returned. This was caused when there were no users flagged for moderation;Fixed bug where JP authors\' latest post information wasn\'t updated when saving a JP then posting it;Fixed bug associated with a missing update piece from the 2.2.1 to 2.3.0 update. This bug only affected users who updated from 2.2.x to 2.3.0;Fixed minor bug in update notification where a wrong variable was being used and causing the version number not to be displayed out of the XML file'),
	(9, '2.4.0', '1165122000', '', 'Added built-in deck listing feature;Added mission notes feature to allow COs to remind their crew of important things on the post and JP pages;Added ability for COs to change whether or not they use the mission notes feature;Changed add award feature to use graphical representations of the awards for adding instead of the select menu like before;Added a version history page;Added full list of users and their moderation status allowing for quick change in moderation status;Added ability for COs to allow XOs to receive crew applications and approve/deny them through the control panel;Fixed bug where simm page would try to print the task force even if the simm wasn\'t part of a task force;Changed link in update function to point to the new server;Fixed bug where news items weren\'t being emailed out;Fixed two style inconsistencies in tour management page;Added private messaging;Added tour summary;Added option to use a third image for each tour item;Added feature allowing COs to add a post, JP, log, or news item that a member of the crew has incorrectly posted or forgotten to post;Added sim progress feature that allows users to see how the sim has progressed over the last 3 months, 6 months, 9 months, and year;Added link from bio page to show all a user\'s posts and logs'),
	(10, '2.4.1', '1165813200', '', 'Fixed SQL injection security holes by adding regular expression check to make sure that the GET variables being used were positive integers. If the check fails, the CO will be emailed with the offending user\'s IP address as well as the page they were trying to access and the exact time they attempted to access the page so the CO can forward that information on to the web host if necessary;Fixed incorrect link deck listing page when no decks are listed in the specifications page;Added Kuro-RPG banner on the credits page at his request'),
	(11, '2.4.2', '1166850000', '', 'Fixed issue in update function where new SMS version wouldn\'t be displayed;Moved credits into the database and made them editable through the Site Messages panel;Fixed bug on bio page where the name wasn\'t being run through the printText() function to strip the SQL escaping slashes;Added non-posting departments to allow COs and XOs to create NPCs in departments where it isn\'t plausible to have a posting character;Added link to post entry for players who are logged in that will take them directly to the post mission entry page;Fixed call to undefined function error when editing mission notes;Fixed bug where email notification sent out after updating a JP wouldn\'t have a FROM field;Changed SMS to use the image name with mission images instead of the full path;Added ability to delete a saved post'),
	(12, '2.4.3', '1167800400', '', 'Fixed JavaScript error when logging out;Fixed JavaScript bug with the Mission Notes hide/show feature;Added neuter and hemaphrodite to the gender options;Added player experience to the join form. This information will only be available in the email sent from the system and not stored in the database;Fixed bug where anyone who did a fresh install of 2.4.2 would not be able to access their globals and messages because of a typo in the install'),
	(13, '2.4.4', '1169701200', '', 'Position grouping by department on the Add Character page;Gender and species added to NPC manifest;Bio page presentation cleanup;Deck listing page presentation cleanup;Departments sections in All NPCs list for access levels 4 and higher;HTML character printed after last department on department management page;Mission title wasn\'t being sent out in mission post emails;Mission log listing order - completed missions should be sorted by completion date descending;Email formatting bug in news item emails;Alternating row colors for the Crew Activity list, All NPCs, and All Characters;All Characters list ordering by department first;Editing an NPC\'s position through their bio would change the number of open positions for those positions (old and new);Some character pictures would break the SMS template on the bio page;If a player had a previous character with the same username and password, it\'d generally log them in as their old character;Email formatting bug in Award Nomination page;Changed Award Nomination and Change Status Request to have them email sent from the player nominating/requesting;Added User Stats page;Changed database to make it easier to track senior staff vs crew positions;Logic to make sure the apply link isn\'t show for an NPC occupying a position with no open slots;Added timestamp for when a playing character is deactivated;Updated styling on posting pages (bigger text boxes);Added a count of saved posts on the Post Count report;Post Count report wouldn\'t return the right results under some conditions;Sim statistics page wasn\'t obeying system\'s global preference for how to count posts and was including saved posts;Visual notification of saved JPs the user hasn\'t responded to;Leave date set on player deactivation;More logic in the printCO, printXO, printCOEmail, and printXOEmail functions to narrow down the results;Better layout on individual post management page;Ability to change a post\'s mission;Changing rank sets when spaces are between the values in the allowed ranks field;Fixed sim progress loops to accurately display the number of posts'),
	(14, '2.4.4.1', '1170046800', '', 'Fixed positions table problem introduced in 2.4.4 (this release was only for future fresh installs, a patch fixed the issue for everyone else)'),
	(15, '2.5.0', '1185317009', 'SMS 2.5 is a true milestone and one of the largest releases Anodyne has ever released.  This new version of SMS extends functionality across multiple planes, providing more control for COs with less effort.  A new user access system now allows COs to specify exactly which pages a certain player has access to and a new menu system means that you can now update menu items from within the SMS Control Panel.  On top of that, we\'ve patched dozens of bugs, fixed consistency issues, improved the system\'s overall efficiency, and made SMS smarter than ever before.', 'User access control system changed to let COs select exactly which pages a player has access to;Menus are now pulled from the database and managed through a page in the control panel;Moved alternating row colors to the stylesheet for skinners;Changed all system icons to use alpha-channel PNGs;Fixed JP author slot 3 issues;Added ability for COs to choose whether added posts are emailed out;Added option to select which mission the post is part of for the add post and add JP page;Fixed bug where updating a mission post through the mission posts listing would erase the mission info and cause an extract() error;Added tabs to pages with lots of content;Changed form submit buttons to use the image type instead of the native browser/OS widgets;Changed all timestamps from SQL format to UNIX format;Refreshed default theme;Added Cobalt theme;Added LCARS theme;Removed Alternate rank set to save space;Added phpSniff credits;Added page to display mission notes without having to go to the post pages;Added Top 5 Open Positions list on the main page that can be controlled through Site Globals;Improved site presentation options when it comes to the content on the main page - COs can now select which items they want to see;Added COs to acceptance/rejection emails;Widened text boxes throughout the system;Improved style consistency throughout the system;Changed news setup to allow a single news item to be viewed like a post or log (finally);Rewrote query execution check to be more efficient and smarter;Improved logic of activation page including the plurality based on the number of pending items in each category;Added First Launch feature that will give a CO a brief run-down of the updates to SMS when they first log in;Fixed manifest so that it won\'t show a link to apply for a position if there aren\'t any open slots;Standardized use of preview rank images;Added blank image to the root rank directory and changed system to use the blank image instead of looking for a specific rank image;Fixed manifest so that previous players and NPCs can hold two positions;Added graphical notification of player\'s LOA status on the main control panel;Install script will check to make sure the web location variable is in place, otherwise it won\'t let you continue to the next step;Crew activity report displays number of months now instead of just days;Added crew milestone report to show how long players have been aboard the sim;Fixed bug where user would not be notified if the update query failed because they tried to change their username, real name, or email without giving their current password;Changed password reset to display the new password instead of emailing it out because of problems with the emails not being sent out;Added unique sim identifier to make sure that SMS installations on the same domain don\'t cause problems for each other'),
	(16, '2.5.1', '1185447600', 'This release fixes several bugs not caught during beta testing, including a bug where IE users could not activate new crew members. In addition, there have been some corrections to the install files as well as the join page.', 'Install: fixed typo in permanent credits insert;Install: changed positionDisplay update to make sure it isn\'t trying to update something it shouldn\'t;Install: changed rankDisplay update to make sure it isn\'t trying to update something it shouldn\'t;Fixed bug where IE users couldn\'t activate or reject players;Fixed bug where incorrect timestamp format was used when activating a new player;Update: added smart checking to make sure a timestamp isn\'t trying to be updated if it\'s already a timestamp; Fixed bug where simm statistics weren\'t showing at all;Improved the email and logic code on the join form regarding whether XOs get emails or not'),
	(17, '2.5.1.1', '1185746400', 'This release fixes a bug where SMS wouldn\'t allow players to be accepted or rejected.', 'Fixed bug where players couldn\'t be accepted or rejected'),
	(18, '2.5.2', '1186578000', 'This release patches several outstanding bugs in SMS as well as enhancing existing features with additional functionality.', 'Fixed inconsistencies in granting of permissions across the system;Fixed bug where saving a joint post with 4 participants would overwrite the third author with a blank variable;Added more notes to the user access control sections;Removed inactive crew list from the user access control listing since inactive players shouldn\'t have access;Changed tab text in site globals to prevent from wrapping to a new line at 1024x768 resolutions;Fixed incorrect display of dates on user statistics page;Fixed bug where when changing a user\'s position, the open count wouldn\'t increment;Added automatic access level change when moving between senior and crew positions;Added visual notification of whether menu items are ON or OFF;Added page to add/remove a given access level for the entire crew at the same time;Added page that gives full listing of a given user\'s access;Added user access report link to the full crew listing by the stats link;Added display of second position (if applicable) to the active crew list;When the SMS posting system is turned on or off, the system will take actions to make sure the people are either stripped of or given posting access;Added logic to installation that will detect the MySQL version and add the UTF8 character set if the version is 4.0 and higher;Fixed install bug where if the variables file was written the webLocation variable would be empty;Fixed bug where deck listing textareas would show HTML break tags after updating;Fixed bug where join page set wrong timestamp;Added nice message if the join date is empty instead of the 37 years, etc.;Fixed bug on milestones report where time wouldn\'t display if it was 1 day or less;Updated logic on milestones report to display date in a nicer fashion;Improved display for dates less than 1 day on the activity report;Added on/off switch control to each menu item;Fixed bug on login where error message would extend across entire screen;Reactivated emailing of password on password reset;Added visual separation between items that need a password to be changed and those that don\'t on the edit account page;Removed username from being listed on the edit acount page unless the person viewing it is the owner of the account;Fixed bug where dates wouldn\'t display by recent posts and logs;Fixed account bug where admin couldn\'t change active status of a player from the edit account page;Fixed bug where private messages weren\'t being sent or received'),
	(19, '2.5.3', '1187026200', 'This release patches several bugs related to player acceptance and rejection, display problems and account management.', 'Provided potential fix for skinners related to strange spacing in the Latest Posts section on the main page when moving paddings from anchors to list items;Fixed display bug with reply button on PM view page;Fixed bug where updating own account wouldn\'t work;Fixed bug where player being accepted or rejected wouldn\'t get an email;Fixed potential bug where player being accepted wouldn\'t be updated correctly'),
	(20, '2.5.4', '1190036700', 'This release increases the number of allowed JP authors from 6 to 8.', 'Increased allowed JP authors from 6 to 8'),
	(21, '2.5.5', '1194444000', 'This release fixes a critical security issue and patches a bug with default standard player access levels.', 'Fixed critical security issue;Fixed bug where newly created standard players don\'t have the right permissions for sending news items'),
	(22, '2.5.6', '1202230800', 'This release fixes an annoying issue where spammers trying to access un-authenticated pages produced an email claiming SQL injection.', 'Removed email to CO when an illegal operation is attempted (99% of these attempts are in fact spammers, not a malicious hacking attempt)')" );

	/* add the tour table */
	mysql_query( "CREATE TABLE `sms_tour` (
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
	mysql_query( "CREATE TABLE `sms_tour_decks` (
		`deckid` int(4) NOT NULL auto_increment,
		`deckContent` text,
	  PRIMARY KEY  (`deckid`)
	) " . $tail . " ;" );
	
} elseif( $step == 4 ) {
	
	/* pull in the db connections */
	include_once( '../framework/functionsGlobal.php' );
	
	$md5password = md5( $_POST['password'] );
	
	/* create the variables for access */
	$levelsPost = "post,p_addjp,p_addnews,p_log,p_addlog,p_pm,p_mission,p_addmission,p_jp,p_news,p_missionnotes";
	$levelsManage = "manage,m_globals,m_messages,m_specs,m_posts,m_logs,m_news,m_missionsummaries,m_missionnotes,m_createcrew,m_crew,m_coc,m_npcs2,m_removeaward,m_strike,m_giveaward,m_missions,m_departments,m_moderation,m_ranks,m_awards,m_positions,m_tour,m_decks,m_database,m_newscat3,m_docking,m_catalogue";
	$levelsReports = "reports,r_about,r_count,r_strikes,r_activity,r_progress,r_versions,r_milestones";
	$levelsUser = "user,u_nominate,u_inbox,u_account2,u_status,u_options,u_bio3,u_stats,u_site";
	$levelsOther = "x_skindev,x_approve_users,x_approve_posts,x_approve_logs,x_approve_news,x_approve_docking,x_update,x_access,x_menu";
	
	/* create the user */
	$createUser = "INSERT INTO sms_crew ( crewid, username, password, email, firstName, middleName, lastName, gender, species, rankid, positionid, joinDate, accessPost, accessManage, accessReports, accessUser, accessOthers ) ";
	$createUser.= "VALUES ( '1', '$_POST[username]', '$md5password', '$_POST[email]', '$_POST[firstName]', '$_POST[middleName]', '$_POST[lastName]', '$_POST[gender]', '$_POST[species]', '$_POST[rank]', '$_POST[position]', UNIX_TIMESTAMP(), '$levelsPost', '$levelsManage', '$levelsReports', '$levelsUser', '$levelsOther' )";
	$createUserResult = mysql_query( $createUser );
	
	/* update the position they're being given */
	$positionFetch = "SELECT positionid, positionOpen FROM sms_positions ";
	$positionFetch.= "WHERE positionid = '$_POST[position]' LIMIT 1";
	$positionFetchResult = mysql_query( $positionFetch );
	$positionX = mysql_fetch_row( $positionFetchResult );
	$open = $positionX[1];
	$revised = ( $open - 1 );
	$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
	$updatePosition.= "WHERE positionid = '$_POST[position]' LIMIT 1";
	$updatePositionResult = mysql_query( $updatePosition );

} elseif( $step == 5 ) {

	/* pull in the db connections */
	include_once( '../framework/functionsGlobal.php' );
	
	/* update the globals */
	$updateGlobals = "UPDATE sms_globals SET shipName = '$_POST[shipName]', shipPrefix = '$_POST[shipPrefix]', shipRegistry = '$_POST[shipRegistry]' WHERE globalid = '1' LIMIT 1";
	$updateGlobalsResult = mysql_query( $updateGlobals );

}

?>

<html>
<head>
	<title>SMS 2.5 :: Fresh Install</title>
	<link rel="stylesheet" type="text/css" href="install.css" />
</head>
<body>
	<div id="install">	
		<div class="header">
			<img src="install.jpg" alt="SMS 2.5 Fresh Install" border="0" />
		</div> <!-- close .header -->
		<div class="content">
			
			<? if( $step == 1 ) { ?>
			
			<div align="center"><b>Installation Progress</b><br /></div>
			<div class="status">
			</div>
			<br /><br />
			
			Thank you for choosing the Simm Management System by Anodyne Productions. We have
			worked hard to build the best possible product for you to manage your Star Trek simm
			online. If you have questions, please refer to the documentation on the Anodyne site or our 
			<a href="http://forums.anodyne-productions.com/" target="_blank">support forums</a> 
			to get help.<br /><br />
			
			This installer will guide you through the clean install process for SMS 2.5. If you are attempting
			to upgrade from SMS 1.2 or 1.5, please use one of the upgrade options from the
			<a href="../install.php">main installation page</a>.<br /><br />
			
			Once the installation is complete, you will be able to use SMS for your simm. Please provide 
			the web location and database connection parameters in this step that were provided to you
			by your host. <b>You must complete all the fields in this step or your installation of SMS 
			will not work!</b><br /><br />
			
			<br /><br />
			
			<form method="post" action="install.php?step=2">
				<table width="100%">
					<tr>
						<td class="label">Web Location</td>
						<td>&nbsp;</td>
						<td><input type="text" name="webLocation" size="32" />
					</tr>
					<tr>
						<td class="label">Database Server</td>
						<td>&nbsp;</td>
						<td><input type="text" name="dbServer" size="32" value="localhost" />
					</tr>
					<tr>
						<td class="label">Database Table</td>
						<td>&nbsp;</td>
						<td><input type="text" name="dbTable" size="32" />
					</tr>
					<tr>
						<td class="label">Database User</td>
						<td>&nbsp;</td>
						<td><input type="text" name="dbUser" size="32" />
					</tr>
					<tr>
						<td class="label">Database Password</td>
						<td>&nbsp;</td>
						<td><input type="text" name="dbPassword" size="32" />
					</tr>
					<tr>
						<td class="label">Database Error Message</td>
						<td>&nbsp;</td>
						<td><textarea name="errorMessage" rows="4" style="width:300px;"></textarea></td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td>
							<input type="submit" name="submit" class="installButton" value="Next Step &raquo;" />
						</td>
					</tr>
				</table>
			</form>
			
			<? } elseif( $step == "2" ) { ?>
			
			<div align="center"><b>Installation Progress</b><br /></div>
			<div class="status">
				<div class="step1">&nbsp;</div>
			</div>
			<br /><br />
			
			<? if( $varError ) { ?>
			
			<div class="code">
				<b class="red">The database connection file (framework/variables.php) is
				not correctly formatted and connecting to the database may fail.
				This is possibly due to the file being empty or a missing web
				location variable. You MUST have a web location variable and all
				of your connection information to continue. Please paste the
				following information into the file and try again.</b><br /><br />
				
				<? print( htmlentities( '<?php' ) ); ?><br /><br />
				
				$webLocation = "<?=$_SESSION['webLocation'];?>";<br /><br />
				$dbServer = "<?=$_SESSION['dbServer'];?>";<br />
				$dbTable = "<?=$_SESSION['dbTable'];?>";<br />
				$dbUser = "<?=$_SESSION['dbUser'];?>";<br />
				$dbPassword = "<?=$_SESSION['dbPassword'];?>";<br />
				$dbErrorMessage = "<?=$_SESSION['dbErrorMessage'];?>";<br /><br />
				
				<? print( htmlentities( '?>' ) ); ?>
			</div>
			<br />
			
			<? } ?>
			
			<? if( !$varError && $write == "failed" ) { ?>
			
			It appears that, for security reasons, your server does not allow opening and writing files. 
			Please open the file <b>variables.php</b> from the <b>framework</b> folder and insert 
			the following code:<br /><br />
			
			<div class="code">
				<? print( htmlentities( '<?php' ) ); ?><br /><br />
				
				$webLocation = "<?=$webLocation;?>";<br /><br />
				$dbServer = "<?=$dbServer;?>";<br />
				$dbTable = "<?=$_POST['dbTable'];?>";<br />
				$dbUser = "<?=$_POST['dbUser'];?>";<br />
				$dbPassword = "<?=$_POST['dbPassword'];?>";<br />
				$dbErrorMessage = "<?=$dbErrorMessage;?>";<br /><br />
				
				<? print( htmlentities( '?>' ) ); ?>
			</div>
			<br />
			
			Once you have completed this, you may continue with the installation.<br /><br />
			
			<? } else { ?>
			
			You have successfully written the file containing all of the database connection
			parameters!<br /><br />
			
			<? } ?>
			
			SMS 2 includes many of the features from SMS 1.5, including the awards system and starbase
			docking system. In addition, SMS 2 introduces a strike system so that COs can easily give,
			remove, and track strikes against players. Sporting a redesigned framework, SMS 2 makes it
			easier than ever to do the things you want to do, including the ability for players to hold two
			positions. With more robust user controls, it's easy now to let your department heads take
			care of NPCs within their department, leaving you to spend more time simming with your
			crew instead of taking care of the little things.<br /><br />
			
			Please proceed to the next step to build the SMS 2 database and create your character
			which you'll use to administer SMS.<br /><br />
			
			<h1><a href="install.php?step=3">Next Step &raquo;</a></h1>
			
			<? } elseif( $step == "3" ) { ?>
			
			<div align="center"><b>Installation Progress</b><br /></div>
			<div class="status">
				<div class="step2">&nbsp;</div>
			</div>
			<br /><br />
			
			You have successfully created the SMS database that will drive the site!<br /><br />
			
			Use this page to create your character. You will use the username and password to log in
			to your SMS site, so make sure you remember it. Once you have set up SMS, you can edit 
			your biography.
			<br /><br />
			
			<br /><br />
			
			<form method="post" action="install.php?step=4">
				<table width="100%">
					<tr>
						<td class="label">Username</td>
						<td>&nbsp;</td>
						<td><input type="text" name="username" size="32" />
					</tr>
					<tr>
						<td class="label">Password</td>
						<td>&nbsp;</td>
						<td><input type="password" name="password" size="32" />
					</tr>
					<tr>
						<td class="label">Email Address</td>
						<td>&nbsp;</td>
						<td><input type="text" name="email" size="32" />
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td class="label">First Name</td>
						<td>&nbsp;</td>
						<td><input type="text" name="firstName" /></td>
					</tr>
					<tr>
						<td class="label">Middle Name</td>
						<td>&nbsp;</td>
						<td><input type="text" name="middleName" /></td>
					</tr>
					<tr>
						<td class="label">Last Name</td>
						<td>&nbsp;</td>
						<td><input type="text" name="lastName" /></td>
					</tr>
					<tr>
						<td class="label">Gender</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" name="gender" value="Male" checked="yes" /> Male
							&nbsp;&nbsp;
							<input type="radio" name="gender" value="Female" /> Female
						</td>
					</tr>
					<tr>
						<td class="label">Species</td>
						<td>&nbsp;</td>
						<td><input type="text" name="species" /></td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<?
					
					$ranks = "SELECT rank.rankid, rank.rankName, rank.rankImage, dept.deptColor FROM sms_ranks AS rank, ";
					$ranks.= "sms_departments AS dept WHERE dept.deptClass = rank.rankClass AND dept.deptDisplay = 'y' ";
					$ranks.= "GROUP BY rank.rankid ORDER BY rank.rankClass, rank.rankOrder ASC";
					$ranksResult = mysql_query( $ranks );
					
					$positions = "SELECT position.positionid, position.positionName, dept.deptName, ";
					$positions.= "dept.deptColor FROM sms_positions AS position, sms_departments AS dept ";
					$positions.= "WHERE position.positionOpen > '0' AND dept.deptid = position.positionDept ";
					$positions.= "AND dept.deptDisplay = 'y' ORDER BY position.positionid ASC";
					$positionsResult = mysql_query( $positions );
					
					?>
					<tr>
						<td class="label">Rank</td>
						<td>&nbsp;</td>
						<td>
							<select name="rank">
								<?
								
								while( $rank = mysql_fetch_assoc( $ranksResult ) ) {
									extract( $rank, EXTR_OVERWRITE );
							
									echo "<option value='" . $rank['rankid'] . "' style='background:#000 url( ../images/ranks/default/" . $rank['rankImage'] . " ) no-repeat 0 100%; height:40px; color:#" . $rank['deptColor'] . ";'>" . $rank['rankName'] . "</option>";
								
								}
								
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="label">Position</td>
						<td>&nbsp;</td>
						<td>
							<select name="position">
							<?
							
							while( $position = mysql_fetch_assoc( $positionsResult ) ) {
								extract( $position, EXTR_OVERWRITE );
						
								echo "<option value='" . $position['positionid'] . "' style='color:#" . $position['deptColor'] . ";'>" . $position['deptName'] . " - " . $position['positionName'] . "</option>";
								
							}
							
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td>
							<input type="submit" name="submit" class="installButton" value="Next Step &raquo;" />
						</td>
					</tr>
				</table>
			</form>
			
			<? } elseif( $step == "4" ) { ?>
			
			<div align="center"><b>Installation Progress</b><br /></div>
			<div class="status">
				<div class="step3">&nbsp;</div>
			</div>
			<br /><br />
			
			<form method="post" action="install.php?step=5">
				<table width="100%">
					<tr>
						<td class="label">Ship Prefix</td>
						<td>&nbsp;</td>
						<td><input type="text" name="shipPrefix" size="32" maxlength="10" value="USS" />
					</tr>
					<tr>
						<td class="label">Ship Name</td>
						<td>&nbsp;</td>
						<td><input type="text" name="shipName" size="32" maxlength="32" />
					</tr>
					<tr>
						<td class="label">Ship Registry</td>
						<td>&nbsp;</td>
						<td><input type="text" name="shipRegistry" size="32" maxlength="16" />
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td>
							<input type="submit" name="submit" class="installButton" value="Next Step &raquo;" />
						</td>
					</tr>
				</table>
			</form>
			
			<? } elseif( $step == "5" ) { ?>
			
			<div align="center"><b>Installation Progress</b><br /></div>
			<div class="status">
				<div class="step4">&nbsp;</div>
			</div>
			<br /><br />
			
			<h1>Installation Complete!</h1>
			
			Congratulations, you have successfully installed SMS 2. If the install worked properly, you 
			should now be able to see SMS running on your site. If you need technical support, please 
			visit the <a href="http://anodyne.cyberplexus.net" target="_blank">Anodyne website</a>
			or the <a href="http://anodyne.cyberplexus.net/forums/" target="_blank">Anodyne support \
			forums</a>.<br /><br />
	
			Thank you for choosing SMS from Anodyne Productions. Please delete the install file and install
			folder from your server. Accessing it additional times can cause errors.<br /><br />
	
			<h1><a href="<?=$webLocation;?>login.php?action=login">Login to your SMS site now &raquo;</a></h1>
			
			<? } ?>
		</div>
		<div class="footer">
			Copyright &copy; 2005-<?php echo date('Y'); ?> by <a href="http://www.anodyne-productions.com/" target="_blank">Anodyne Productions</a><br />
			SMS 2 designed by <a href="mailto:anodyne.sms@gmail.com">David VanScott</a>
		</div> <!-- close .footer -->
	</div> <!-- close #install -->
</body>
</html>