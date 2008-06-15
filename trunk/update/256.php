<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/256.php
Purpose: Update to 2.6.0
Last Modified: 2008-06-15 1902 EST
**/

/*
|---------------------------------------------------------------
| MISCELLANEOUS
|---------------------------------------------------------------
|
| This code finds out the version of MySQL so that the page can
| do some logic to avoid collation problems.
|
*/
$t = mysql_query("select version() as ve");
echo mysql_error();
$r = mysql_fetch_object( $t );

if( $r->ve >= 4 ) {
	$tail = "CHARACTER SET utf8";
} else {
	$tail = "";
}

/*
|---------------------------------------------------------------
| SYSTEM GLOBALS
|---------------------------------------------------------------
|
| These changes introduce a few new features to SMS, namely the ability
| to set the email subject, update notification, stardate display and
| manifest display defaults.
|
*/
mysql_query( "ALTER TABLE `sms_globals` ADD `emailSubject` varchar(75) not null default '[" . SHIP_PREFIX . " " . SHIP_NAME . "]'" );
mysql_query( "ALTER TABLE `sms_globals` ADD `updateNotify` enum('all','major','none') not null default 'all'" );
mysql_query( "ALTER TABLE `sms_globals` ADD `stardateDisplaySD` enum('y','n') not null default 'y'" );
mysql_query( "ALTER TABLE `sms_globals` ADD `stardateDisplayDate` enum('y','n') not null default 'y'" );
mysql_query( "ALTER TABLE `sms_globals` ADD `manifest_defaults` text not null" );

/* set the manifest defaults based on what kind of manifest setup there is already */
$get1 = "SELECT manifestDisplay FROM sms_globals WHERE globalid = 1 LIMIT 1";
$getR1 = mysql_query($get1);
$fetch1 = mysql_fetch_array($getR1);

switch($fetch1[0])
{
	case 'full':
		$defaults = "$(\'tr.active\').show();,$(\'tr.npc\').show();,$(\'tr.open\').show();";
		break;
	case 'split':
		$defaults = "$(\'tr.active\').show();";
		break;
}

mysql_query( "UPDATE sms_globals SET manifest_defaults = '$defaults' WHERE globalid = 1" );

/* get rid of the manifest display field now */
mysql_query( "ALTER TABLE `sms_globals` DROP `manifestDisplay`" );
mysql_query( "ALTER TABLE `sms_globals` DROP `useArchive`" );


/*
|---------------------------------------------------------------
| SPECIFICATIONS
|---------------------------------------------------------------
|
| These changes fix a bug with the specs page where commas could
| not be used in the compliment numbers because of the fact that
| the database fields used INT fields instead of VARCHAR.
|
*/
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentEmergency` `complimentEmergency` varchar(20) NOT NULL default ''" );
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentOfficers` `complimentOfficers` varchar(20) NOT NULL default ''" );
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentEnlisted` `complimentEnlisted` varchar(20) NOT NULL default ''" );
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentMarines` `complimentMarines` varchar(20) NOT NULL default ''" );
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentCivilians` `complimentCivilians` varchar(20) NOT NULL default ''" );


/*
|---------------------------------------------------------------
| RANKS
|---------------------------------------------------------------
|
| SMS now includes more rank information, including a short name
| that is now used in the emails sent out. In addition, we now
| include all the cadet ranks, but turn them off by default. This
| step will blow away the ranks in the database and rebuild the
| ranks database, then loop through the crew data and attempt to
| update the crew so their ranks are still accurate.
|
*/
$clear = "TRUNCATE TABLE sms_ranks";
$clearR = mysql_query($clear);

mysql_query( "ALTER TABLE `sms_ranks` ADD `rankShortName` varchar(32) not null default ''" );

require_once('update/ranks.php');

$getCrew = "SELECT * FROM sms_crew";
$getCrewR = mysql_query($getCrew);

while($fetchCrew = mysql_fetch_assoc($getCrewR)) {
	extract($fetchCrew, EXTR_OVERWRITE);
	
	if(array_key_exists($fetchCrew['rankid'], $old_ranks))
	{
		$new_rank = $old_ranks[$fetchCrew['rankid']];
		mysql_query("UPDATE sms_crew SET rankid = $new_rank WHERE crewid = $fetchCrew[crewid] LIMIT 1");
	}
}


/*
|---------------------------------------------------------------
| MENU ITEMS
|---------------------------------------------------------------
|
| The system changes mean we have to update where the private messages
| link points to. In addition, we have changed the name of the private
| messages inbox to be Inbox instead of just Private Messages. The menu
| management page has been consolidated into a single page and the link
| has been changed to reflec that. Finally we have to add the menu item
| for the new Default Access Levels feature.
|
*/
$getPMLink = "SELECT * FROM sms_menu_items WHERE menuAccess = 'p_pm' LIMIT 1";
$getPMLinkResult = mysql_query( $getPMLink );
$pmLink = mysql_fetch_assoc( $getPMLinkResult );
mysql_query( "UPDATE sms_menu_items SET menuLink = 'admin.php?page=user&sub=inbox&tab=3' WHERE menuid = '$pmLink[menuid]' LIMIT 1" );

$getInboxLink = "SELECT * FROM sms_menu_items WHERE menuAccess = 'u_inbox' LIMIT 1";
$getInboxLinkResult = mysql_query( $getInboxLink );
$inboxLink = mysql_fetch_assoc( $getInboxLinkResult );
mysql_query( "UPDATE sms_menu_items SET menuTitle = 'Inbox' WHERE menuid = '$inboxLink[menuid]' LIMIT 1" );

$getMenuLink = "SELECT * FROM sms_menu_items WHERE menuLink = 'admin.php?page=manage&sub=menugeneral' LIMIT 1";
$getMenuLinkResult = mysql_query( $getMenuLink );
$menuLink = mysql_fetch_assoc( $getMenuLinkResult );
mysql_query( "UPDATE sms_menu_items SET menuLink = 'admin.php?page=manage&sub=menus' WHERE menuid = '$menuLink[menuid]' LIMIT 1" );

$getDB = "SELECT * FROM sms_menu_items WHERE menuAccess = 'm_database' LIMIT 1";
$getDBResult = mysql_query( $getDB );
$dbLink = mysql_fetch_assoc( $getDBResult );
mysql_query( "UPDATE sms_menu_items SET menuAccess = 'm_database2' WHERE menuid = '$dbLink[menuid]' LIMIT 1" );

$getPosts = "SELECT * FROM sms_menu_items WHERE menuAccess = 'm_posts' LIMIT 1";
$getPostsResult = mysql_query( $getPosts );
$postLink = mysql_fetch_assoc( $getPostsResult );
mysql_query( "UPDATE sms_menu_items SET menuAccess = 'm_posts2' WHERE menuid = '$postLink[menuid]' LIMIT 1" );

$getLogs = "SELECT * FROM sms_menu_items WHERE menuAccess = 'm_logs' LIMIT 1";
$getLogsResult = mysql_query( $getLogs );
$logLink = mysql_fetch_assoc( $getLogsResult );
mysql_query( "UPDATE sms_menu_items SET menuAccess = 'm_logs2' WHERE menuid = '$logLink[menuid]' LIMIT 1" );

/* default access levels menu item */
mysql_query( "INSERT INTO sms_menu_items ( menuGroup, menuOrder, menuTitle, menuLinkType, menuLink, menuAccess, menuMainSec, menuLogin, menuCat, menuAvailability )
VALUES ( 0, 5, 'Default Access Levels', 'onsite', 'admin.php?page=manage&sub=accesslevels', 'x_access', 'manage', 'y', 'admin', 'y' ),
( 0, 5, 'Database', 'onsite', 'admin.php?page=manage&sub=database', 'm_database1', 'manage', 'y', 'admin', 'y' ),
(1, 0, 'Docking Request', 'onsite', 'index.php?page=dockingrequest', '', 'ship', 'n', 'general', 'n'),
(1, 1, 'Docked Ships', 'onsite', 'index.php?page=dockedships', '', 'ship', 'n', 'general', 'n'),
(4, 3, 'Docked Ships', 'onsite', 'admin.php?page=manage&sub=docking', 'm_docking', 'manage', 'y', 'admin', 'n'),
(0, 0, 'The Starbase', 'onsite', 'index.php?page=starbase', '', '', 'n', 'main', 'n'),
(0, 0, 'Starbase History', 'onsite', 'index.php?page=history', '', 'ship', 'n', 'general', 'n'),
(0, 2, 'Starbase Tour', 'onsite', 'index.php?page=tour', '', 'ship', 'n', 'general', 'n'),
(4, 1, 'Starbase Tour', 'onsite', 'admin.php?page=manage&sub=tour', 'm_tour', 'manage', 'y', 'admin', 'n')" );


/*
|---------------------------------------------------------------
| CREW
|---------------------------------------------------------------
|
| SMS now offers a personalized menu option that allows players to 
| set up 10 of their favorite or most used links, giving them quick
| access to them once they are logged in.
|
*/
/* add the user menu item preferences */
mysql_query("
	ALTER TABLE  `sms_crew` ADD  `menu1` VARCHAR(8) NOT NULL DEFAULT '57',
	ADD  `menu2` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu3` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu4` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu5` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu6` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu7` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu8` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu9` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu10` VARCHAR(8) NOT NULL DEFAULT '0'
");

mysql_query( "ALTER TABLE `sms_crew` CHANGE `image` `image` text NOT NULL" );


/*
|---------------------------------------------------------------
| ACCESS LEVELS
|---------------------------------------------------------------
|
| Default access levels can now be adjusted to make sure that an
| admin has lots of control of what new players are given.
|
*/
mysql_query( "CREATE TABLE `sms_accesslevels` (
  `id` tinyint(1) NOT NULL auto_increment,
  `post` text NOT NULL,
  `manage` text NOT NULL,
  `reports` text NOT NULL,
  `user` text NOT NULL,
  `other` text NOT NULL,
  PRIMARY KEY  (`id`)
) " . $tail . " ;" );

mysql_query( "INSERT INTO `sms_accesslevels` (`id`, `post`, `manage`, `reports`, `user`, `other`) 
VALUES (1, 'post,p_addjp,p_missionnotes,p_jp,p_addlog,p_pm,p_log,p_addmission,p_mission,p_addnews,p_news', 'manage,m_awards,m_logs2,m_coc,m_posts2,m_positions,m_crew,m_missions,m_ranks,m_createcrew,m_missionsummaries,m_removeaward,m_globals,m_database2,m_messages,m_decks,m_newscat3,m_specs,m_departments,m_news,m_strike,m_docking,m_tour,m_giveaward,m_npcs2,m_moderation,m_missionnotes', 'reports,r_about,r_count,r_strikes,r_activity,r_progress,r_versions,r_milestones', 'user,u_nominate,u_inbox,u_account2,u_status,u_options,u_bio3,u_stats,u_site', 'x_approve_docking,x_approve_posts,x_update,x_approve_logs,x_approve_users,x_access,x_approve_news,x_menu'),
(2, 'post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes', 'manage,m_logs2,m_posts2,m_createcrew,m_database1,m_newscat2,m_news,m_npcs2', 'reports,r_count,r_strikes,r_activity,r_progress,r_milestones', 'user,u_account1,u_nominate,u_inbox,u_status,u_options,u_bio2,u_stats', 'x_approve_posts,x_approve_logs,x_approve_news'),
(3, 'post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes', 'manage,m_posts1,m_createcrew,m_database1,m_newscat2,m_npcs1,m_logs1', 'reports,r_count,r_strikes,r_activity,r_progress,r_milestones', 'user,u_account1,u_nominate,u_inbox,u_status,u_options,u_bio2', ''),
(4, 'post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes', 'm_posts1,m_newscat1,m_logs1', 'reports,r_progress,r_milestones', 'user,u_account1,u_nominate,u_inbox,u_bio1,u_status,u_options', '');" );


/*
|---------------------------------------------------------------
| NEWS
|---------------------------------------------------------------
|
| Sometimes news items need to be seen just by the crew. Private
| news items make sure that people who are not logged in cannot
| see those items.
|
*/
mysql_query( "ALTER TABLE `sms_news` ADD `newsPrivate` ENUM('y', 'n') NOT NULL DEFAULT 'n'" );


/*
|---------------------------------------------------------------
| AWARDS
|---------------------------------------------------------------
|
| We are adding award categories to allow NPCs to be given in character
| awards as a CO sees fit. The categories also specify that playing
| characters can get in character and out of character awards. We
| have also added a feature that moves award nominations to a queue
| for a CO to review, and if they approve them, add them to a player
| record immediately.
|
*/
mysql_query( "ALTER TABLE `sms_awards` ADD `awardCat` enum('ic','ooc','both') not null default 'both'" );

$getAwards = "SELECT crewid, awards FROM sms_crew";
$getAwardsR = mysql_query($getAwards);

while($awardsFetch = mysql_fetch_array($getAwardsR)) {
	extract($awardsFetch, EXTR_OVERWRITE);
	
	$award = str_replace(',', ';', $awardsFetch[1]);
	mysql_query("UPDATE sms_crew SET awards = '$award' WHERE crewid = $awardsFetch[0]");
	
}

mysql_query( "CREATE TABLE `sms_awards_queue` (
  `id` int(6) NOT NULL auto_increment,
  `crew` int(6) NOT NULL default '0',
  `nominated` int(6) NOT NULL default '0',
  `award` int(6) NOT NULL default '0',
  `reason` text NOT NULL,
  `status` enum('accepted','pending','rejected') NOT NULL default 'pending',
  PRIMARY KEY  (`id`)
) " . $tail . " ;" );


/*
|---------------------------------------------------------------
| DATABASE
|---------------------------------------------------------------
|
| We have added departmental databases to the system requiring a few
| new fields in both the database table as well as the departments table.
|
*/
mysql_query( "ALTER TABLE `sms_database` ADD `dbDept` int(4) NOT NULL DEFAULT '0'" );
mysql_query( "ALTER TABLE `sms_departments` ADD `deptDatabaseUse` enum('y', 'n') NOT NULL DEFAULT 'y'" );


/*
|---------------------------------------------------------------
| SYSTEM PLUGINS
|---------------------------------------------------------------
|
| With the use of more system-wide plugins, we are adding a plugins
| section to the About SMS page that is fed from this table.
|
*/
mysql_query( "CREATE TABLE `sms_system_plugins` (
	`pid` int(4) NOT NULL auto_increment,
	`plugin` varchar(255) NOT NULL default '',
	`pluginVersion` varchar(15) NOT NULL default '',
	`pluginSite` varchar(200) NOT NULL default '',
	`pluginUse` text NOT NULL,
	`pluginFiles` text NOT NULL,
	PRIMARY KEY  (`pid`)
) " . $tail . " ;" );

mysql_query( "INSERT INTO sms_system_plugins ( pid, plugin, pluginVersion, pluginSite, pluginUse, pluginFiles ) 
VALUES ( '1', 'jQuery', '1.2.6', 'http://www.jquery.com/', 'Javascript library used throughout SMS', 'framework/js/jquery.js' ),
( '2', 'jQuery UI', '1.0', 'http://ui.jquery.com/', 'Tabs throughout the system', 'framework/js/ui.tabs.js;skins/[your skin]/style-ui.tabs.css' ),
( '3', 'clickMenu', '0.1.6', 'http://p.sohei.org/jquery-plugins/clickmenu/', 'Customizable user menu', 'framework/js/clickmenu.js;skins/[your skin]/style-clickmenu.css' ),
( '4', 'Link Scrubber', '1.0', 'http://www.crismancich.de/jquery/plugins/linkscrubber/', 'Remove dotted border around clicked links in Firefox', 'framework/js/linkscrubber.js' ),
( '5', 'Shadowbox', '1.0', 'http://mjijackson.com/shadowbox/', 'Lightbox functionality;Gallery function on tour pages', 'framework/js/shadowbox-jquery.js;framework/js/shadowbox.js;framework/css/shadowbox.css' ),
( '6', 'Facebox', '1.0', 'http://famspam.com/facebox', 'Modal dialogs throughout the system', 'framework/js/facebox.js;framework/css/facebox.css;images/facebox_b.png;images/facebox_bl.png;images/facebox_br.png;images/facebox_closelabel.gif;images/facebox_loading.gif;images/facebox_tl.png;images/facebox_tr.png' ),
( '7', 'Reflect jQuery', '1.0', 'http://plugins.jquery.com/project/reflect', 'Dynamic image reflection on tour pages', 'framework/js/reflect.js' )" );


/*
|---------------------------------------------------------------
| SYSTEM MESSAGES
|---------------------------------------------------------------
|
| Some major changes in SMS mean we have to update the permanent
| credits to reflect some of the new pieces that have been added.
|
*/
mysql_query("UPDATE sms_messages SET siteCreditsPermanent = 'Editing or removal of the following credits constitutes a material breach of the SMS Terms of Use outlined at the <a href=\"http://www.anodyne-productions.com/index.php?cat=sms&page=disclaimers\" target=\"_blank\">SMS Terms of Use</a> page.\r\n\r\nSMS 2 makes extensive use of the <a href=\"http://www.jquery.com\" target=\"_blank\">jQuery</a> Javascript library as well as multiple jQuery plugins. By default, SMS includes the <a href=\"http://ui.jquery.com/\" target=\"_blank\">jQuery UI</a>, <a href=\"http://p.sohei.org/jquery-plugins/clickmenu/\" target=\"_blank\">clickMenu</a>, <a href=\"http://www.crismancich.de/jquery/plugins/linkscrubber/\" target=\"_blank\">Link Scrubber</a>, <a href=\"http://mjijackson.com/shadowbox/\" target=\"_blank\">Shadowbox</a>, <a href=\"http://famspam.com/facebox\" target=\"_blank\">Facebox</a>, and <a href=\"http://plugins.jquery.com/project/reflect\" target=\"_blank\">Reflect jQuery</a>. More information about the versions and uses of the plugins can be obtained from the simm\'s webmaster.\r\n\r\nSMS 2 uses the open source browser detection library <a href=\"http://sourceforge.net/projects/phpsniff/\" target=\"_blank\">phpSniff</a> to check for various versions of browsers for maximum compatibility.\r\n\r\nThe SMS 2 Update notification system uses <a href=\"http://magpierss.sourceforge.net/\" target=\"_blank\">MagpieRSS</a> to parse the necessary XML file. Magpie is distributed under the GPL license. Questions and suggestions about MagpieRSS should be sent to <i>magpierss-general@lists.sf.net</i>.\r\n\r\nSMS 2 uses icons from the open source <a href=\"http://tango.freedesktop.org/Tango_Icon_Gallery\" target=\"_blank\">Tango Icon Library</a>.\r\n\r\nAdd and remove icons from the PI Diagona Pack created by <a href=\"http://pinvoke.com\" target=\"_blank\">Pinvoke.com</a>. Colorization by David VanScott.\r\n\r\nSMS 2 includes a stardate script developed by Phillip Sublett. Information on the script can be found at his site, <a href=\"http://TrekGuide.com/Stardates.htm\" target=\"_blank\">TrekGuide</a>.\r\n\r\nThe rank sets (DS9 Era Duty Uniform Style 2 and DS9 Era Dress Uniform Style 2) used in SMS 2 were created by Kuro-chan of <a href=\"http://www.kuro-rpg.net\" target=\"_blank\">Kuro-RPG</a>. Please do not copy or modify the images in any way, simply contact Kuro-chan and he will see to your rank needs.\r\n\r\n<a href=\"http://www.kuro-rpg.net/\" target=\"_blank\"><img src=\"images/kurorpg-banner.jpg\" border=\"0\" alt=\"Kuro-RPG\" /></a>' WHERE messageid = 1");


/*
|---------------------------------------------------------------
| SYSTEM VERSIONS
|---------------------------------------------------------------
|
| Now that SMS development is happening through SVN and SMS3 releases
| will happen off SVN, we are adding the SVN revision number to the
| release information. This will not mean much during SMS2, but will
| mean more in the future. Finally, we are adding the release information
| for this release.
|
*/
mysql_query( "ALTER TABLE `sms_system_versions` ADD `versionRev` int(5) NOT NULL AFTER `version`" );
mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionRev`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.6.0', '', '', '', '' )" );

?>