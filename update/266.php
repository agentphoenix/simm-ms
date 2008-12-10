<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/266.php
Purpose: Update to 2.6.7
Last Modified: 2008-12-10 1420 EST
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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ('2.6.7', '', 'This release...', 'Fixed bug where rank management would only build department class menus for departments that were being displayed, causing issues for unused ranks that were updated;Fixed bug with next/previous links where they didn\'t respect when a log/post/news item was posted')");

?>