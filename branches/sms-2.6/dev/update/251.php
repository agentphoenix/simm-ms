<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/250.php
Purpose: Update page - 2.5.1 => Latest
Last Modified: 2007-07-29 1734 EST
**/

/* add the data for FirstLaunch */
mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ('2.5.1.1', '1185746400', 'This release fixes a bug where SMS wouldn\'t allow players to be accepted or rejected.', 'Fixed bug where players couldn\'t be accepted or rejected')" );

?>