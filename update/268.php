<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/268.php
Purpose: Update to 2.6.9
Last Modified: 2009-06-15 0622 EST
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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ('2.6.9', '', 'This release fixes bugs with the docking request form, docked ship activation and database entry management.', 'Fixed typos in docking request email sent out to the starbase CO;Fixed bug with docked ship activation and rejection where the docking CO wouldn\'t be sent a copy of the acceptance or rejection email;Fixed location of Facebox loading graphic;Fixed bug in database management page where only entries with a display flag of yes would be shown instead of all entries;Fixed bug in database display page where departments with database use turned off still appeared')");

?>