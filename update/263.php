<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/263.php
Purpose: Update to 2.6.4
Last Modified: 2008-10-25 0910 EST
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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionRev`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.6.4', '688', '1223848800', 'This release addresses issues related to ...', '' )");

?>