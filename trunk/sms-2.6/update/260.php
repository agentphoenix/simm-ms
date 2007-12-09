<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/260.php
Purpose: Update page - 2.5.3 => Latest
Last Modified: 2007-11-05 0946 EST
**/

/* add the email subject field */
mysql_query( "ALTER TABLE `sms_globals` ADD `emailSubject` varchar(75) not null default '[" . SHIP_PREFIX . " " . SHIP_NAME . "]'" );
mysql_query( "ALTER TABLE `sms_globals` ADD `updateNotify` enum('all','major','none') not null default 'all'" );
mysql_query( "ALTER TABLE `sms_globals` ADD `stardateDisplaySD` enum('y','n') not null default 'y'" );
mysql_query( "ALTER TABLE `sms_globals` ADD `stardateDisplayDate` enum('y','n') not null default 'y'" );

/* change compliments fields to accept characters like commas */
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentEmergency` `complimentEmergency` varchar(20) NOT NULL default ''" );
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentOfficers` `complimentOfficers` varchar(20) NOT NULL default ''" );
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentEnlisted` `complimentEnlisted` varchar(20) NOT NULL default ''" );
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentMarines` `complimentMarines` varchar(20) NOT NULL default ''" );
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentCivilians` `complimentCivilians` varchar(20) NOT NULL default ''" );

/* add the conversation id field */
mysql_query( "ALTER TABLE `sms_privatemessages` ADD `conversationId` int(6) not null" );

/* find the blank ranks and reset their ordering */
$get1 = "SELECT * FROM sms_ranks WHERE rankName = '' ORDER BY rankOrder ASC";
$result1 = mysql_query( $get1 );
$count1 = mysql_num_rows( $result1 );

/* if there are records, loop through and update them */
if( $count1 > 0 ) {
	while( $fetch1 = mysql_fetch_assoc( $result1 ) ) {
		extract( $fetch1, EXTR_OVERWRITE );
		
		mysql_query( "UPDATE sms_ranks SET rankOrder = '31' WHERE rankid = '$rankid' LIMIT 1" );
		
	}
}

/* add the cadet ranks */
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '26', '1', 'Cadet, Senior Grade', 'Starfleet/r-c4.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '26', '2', 'Cadet, Senior Grade', 'Starfleet/y-c4.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '26', '3', 'Cadet, Senior Grade', 'Starfleet/t-c4.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '26', '4', 'Cadet, Senior Grade', 'Starfleet/s-c4.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '26', '5', 'Cadet, Senior Grade', 'Starfleet/v-c4.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '26', '6', 'Cadet, Senior Grade', 'Starfleet/g-c4.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '26', '7', 'Cadet, Senior Grade', 'Starfleet/c-c4.png', 'n' ) " );

mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '27', '1', 'Cadet, Junior Grade', 'Starfleet/r-c3.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '27', '2', 'Cadet, Junior Grade', 'Starfleet/y-c3.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '27', '3', 'Cadet, Junior Grade', 'Starfleet/t-c3.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '27', '4', 'Cadet, Junior Grade', 'Starfleet/s-c3.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '27', '5', 'Cadet, Junior Grade', 'Starfleet/v-c3.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '27', '6', 'Cadet, Junior Grade', 'Starfleet/g-c3.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '27', '7', 'Cadet, Junior Grade', 'Starfleet/c-c3.png', 'n' ) " );

mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '28', '1', 'Cadet, Sophomore Grade', 'Starfleet/r-c2.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '28', '2', 'Cadet, Sophomore Grade', 'Starfleet/y-c2.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '28', '3', 'Cadet, Sophomore Grade', 'Starfleet/t-c2.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '28', '4', 'Cadet, Sophomore Grade', 'Starfleet/s-c2.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '28', '5', 'Cadet, Sophomore Grade', 'Starfleet/v-c2.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '28', '6', 'Cadet, Sophomore Grade', 'Starfleet/g-c2.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '28', '7', 'Cadet, Sophomore Grade', 'Starfleet/c-c2.png', 'n' ) " );

mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '29', '1', 'Cadet, Freshman Grade', 'Starfleet/r-c1.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '29', '2', 'Cadet, Freshman Grade', 'Starfleet/y-c1.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '29', '3', 'Cadet, Freshman Grade', 'Starfleet/t-c1.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '29', '4', 'Cadet, Freshman Grade', 'Starfleet/s-c1.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '29', '5', 'Cadet, Freshman Grade', 'Starfleet/v-c1.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '29', '6', 'Cadet, Freshman Grade', 'Starfleet/g-c1.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '29', '7', 'Cadet, Freshman Grade', 'Starfleet/c-c1.png', 'n' ) " );

mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '30', '1', 'Enlisted Cadet', 'Starfleet/r-c0.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '30', '2', 'Enlisted Cadet', 'Starfleet/y-c0.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '30', '3', 'Enlisted Cadet', 'Starfleet/t-c0.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '30', '4', 'Enlisted Cadet', 'Starfleet/s-c0.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '30', '5', 'Enlisted Cadet', 'Starfleet/v-c0.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '30', '6', 'Enlisted Cadet', 'Starfleet/g-c0.png', 'n' ) " );
mysql_query( "INSERT INTO `sms_ranks` ( rankOrder, rankClass, rankName, rankImage, rankDisplay ) VALUES ( '30', '7', 'Enlisted Cadet', 'Starfleet/c-c0.png', 'n' ) " );

/* update the PM link */
$getPMLink = "SELECT * FROM sms_menu_items WHERE menuAccess = 'p_pm' LIMIT 1";
$getPMLinkResult = mysql_query( $getPMLink );
$pmLink = mysql_fetch_assoc( $getPMLinkResult );

mysql_query( "UPDATE sms_menu_items SET menuLink = 'admin.php?page=user&sub=inbox&tab=3' WHERE menuid = '$pmLink[menuid]' LIMIT 1" );

/* update the inbox title */
$getInboxLink = "SELECT * FROM sms_menu_items WHERE menuAccess = 'u_inbox' LIMIT 1";
$getInboxLinkResult = mysql_query( $getInboxLink );
$inboxLink = mysql_fetch_assoc( $getInboxLinkResult );

mysql_query( "UPDATE sms_menu_items SET menuTitle = 'Inbox' WHERE menuid = '$inboxLink[menuid]' LIMIT 1" );

/* add the user menu item preferences */
mysql_query(
	"ALTER TABLE  `sms_crew` ADD  `menu1` INT( 5 ) NOT NULL DEFAULT  '1',
	ADD  `menu2` INT( 5 ) NOT NULL DEFAULT  '2',
	ADD  `menu3` INT( 5 ) NOT NULL DEFAULT  '3',
	ADD  `menu4` INT( 5 ) NOT NULL DEFAULT  '4',
	ADD  `menu5` INT( 5 ) NOT NULL DEFAULT  '5',
	ADD  `menu6` INT( 5 ) NOT NULL DEFAULT  '7'"
);

/* create the access levels table */
mysql_query( "CREATE TABLE `sms_accesslevels` (
  `id` tinyint(1) NOT NULL auto_increment,
  `post` text NOT NULL,
  `manage` text NOT NULL,
  `reports` text NOT NULL,
  `user` text NOT NULL,
  `other` text NOT NULL,
  PRIMARY KEY  (`id`)
) " . $tail . " ;" );

/* insert data into the department table */
mysql_query( "INSERT INTO `sms_accesslevels` (`id`, `post`, `manage`, `reports`, `user`, `other`) 
VALUES (1, 'post,p_addjp,p_missionnotes,p_jp,p_addlog,p_pm,p_log,p_addmission,p_mission,p_addnews,p_news', 'manage,m_globals,m_messages,m_specs,m_posts,m_logs,m_news,m_missionsummaries,m_missionnotes,m_createcrew,m_crew,m_coc,m_npcs2,m_removeaward,m_strike,m_giveaward,m_missions,m_departments,m_moderation,m_ranks,m_awards,m_positions,m_tour,m_decks,m_database,m_newscat3,m_docking,m_catalogue', 'reports,r_about,r_count,r_strikes,r_activity,r_progress,r_versions,r_milestones', 'user,u_nominate,u_inbox,u_account2,u_status,u_options,u_bio3,u_stats,u_site', 'x_approve_posts,x_skindev,x_approve_logs,x_approve_users,x_update,x_approve_news,x_menu,x_access'),
(2, 'post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes', 'manage,m_posts,m_logs,m_news,m_createcrew,m_npcs2,m_newscat2', 'reports,r_count,r_strikes,r_activity,r_progress,r_milestones', 'user,u_account1,u_nominate,u_inbox,u_status,u_options,u_bio2,u_stats', 'x_approve_posts,x_approve_logs,x_approve_news'),
(3, 'post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes', 'manage,m_createcrew,m_npcs1,m_newscat2', 'reports,r_count,r_strikes,r_activity,r_progress,r_milestones', 'user,u_account1,u_nominate,u_inbox,u_status,u_options,u_bio2', ''),
(4, 'post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes', '', 'reports,r_progress,r_milestones', 'user,u_account1,u_nominate,u_inbox,u_bio1,u_status,u_options', '');" );

/* create the security table */
mysql_query( "CREATE TABLE `sms_security` (
  `id` int(5) NOT NULL auto_increment,
  `page` varchar(100) NOT NULL,
  `reason` text NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `crew` int(4) NOT NULL,
  `time` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) " . $tail . " ;" );

/* add the necessary menu items */
mysql_query( "INSERT INTO sms_menu_items ( menuGroup, menuOrder, menuTitle, menuLinkType, menuLink, menuAccess, menuMainSec, menuLogin, menuCat )
VALUES ( 0, 4, 'Security Report', 'onsite', 'admin.php?page=reports&sub=security', 'r_security', 'reports', 'y', 'admin' ), 
( 0, 5, 'Default Access Levels', 'onsite', 'admin.php?page=manage&sub=accesslevels', 'x_access', 'manage', 'y', 'admin' )" );

/* add the private news item field */
mysql_query( "ALTER TABLE `sms_news` ADD `newsPrivate` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n'" );

/* add the data for FirstLaunch */
mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.6.0', '', '', '' )" );

?>