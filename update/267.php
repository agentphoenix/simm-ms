<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/267.php
Purpose: Update to 2.6.8
Last Modified: 2008-12-28 2129 EST
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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ('2.6.8', '', 'This release...', 'Fixed bug where saved mission posts and joint posts showed the missions in the dropdown twice')");

?>