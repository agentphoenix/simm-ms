<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/260.php
Purpose: Update to 2.6.1
Last Modified: 2008-08-01 1513 EST
**/

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
mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionRev`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.6.1', '630', '', 'This release addresses several bugs and issues with the initial release of SMS 2.6, including bugs while updating and several smaller issues related to emails sent out by the system and creating new ranks.', '' )" );

?>