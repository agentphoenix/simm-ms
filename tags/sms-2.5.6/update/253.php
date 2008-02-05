<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/253.php
Purpose: Update page - 2.5.3 => Latest
Last Modified: 2007-09-17 0925 EST
**/

/* update the site permanent credits to make sure everyone has the right tou link */
mysql_query( "ALTER TABLE `sms_posts` CHANGE `postAuthor` `postAuthor` varchar(40) not null default ''" );

/* add the data for FirstLaunch */
mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.5.4', '1190036700', 'This release increases the number of allowed JP authors from 6 to 8.', 'Increased allowed JP authors from 6 to 8' )" );

sleep(1);

/* add the data for FirstLaunch */
mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.5.5', '1194444000', 'This release fixes a major security issue and patches a bug with default standard player access levels.', 'Fixed critical security issue;Fixed bug where newly created standard players don\'t have the right permissions for sending news items' )" );

/* to sms 2.5.6 */
sleep(2);

mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.5.6', '1202230800', 'This release fixes an annoying issue where spammers trying to access un-authenticated pages produced an email claiming SQL injection.', 'Removed email to CO when an illegal operation is attempted (99% of these attempts are in fact spammers, not a malicious hacking attempt)' )" );

?>