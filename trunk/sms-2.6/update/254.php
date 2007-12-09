<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/254.php
Purpose: Update page - 2.5.4 => Latest
Last Modified: 2007-11-07 0829 EST
**/

/* add the data for FirstLaunch */
mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.5.5', '1194444000', 'This release fixes a critical security issue and patches a bug with default standard player access levels.', 'Fixed critical security issue;Fixed bug where newly created standard players don\'t have the right permissions for sending news items' )" );

?>