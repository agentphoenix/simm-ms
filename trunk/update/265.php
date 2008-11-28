<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/265.php
Purpose: Update to 2.6.6
Last Modified: 2008-11-28 0931 EST
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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ('2.6.6', '', 'This release adds two minor feature updates. The first is the way bio images are displayed and the second is the option to set moderation flags on activation.', 'Updated bio image display to show a main picture and clicking the picture opens a gallery with all the character images;Added ability to set moderation flags at character activation')");

?>