<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/263.php
Purpose: Update to 2.6.4
Last Modified: 2008-11-11 1331 EST
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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionRev`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.6.4', '702', '1226448000', 'This release addresses issues related to nominating crew when no awards are present, display of links and tabs when events tied to those links/tabs are not legitimate, and a major issue with fresh installs where the write process fails.', 'Fixed spacing in rank drop down menus to avoid text and graphic overlap;Fixed bug where players could nominate another player for an award, even if no awards exist;Fixed a potential bug where a player could manually get to the NPC tab and submit a nomination even if the tab was disabled;Removed approve link in Approve Award Nomination list if there is no award associated with that nomination (and award with an id of 0);Fixed bug where web location variable wouldn\'t be written to the proper DIV in the event the file write failed' )");

?>