<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/262.php
Purpose: Update to 2.6.3
Last Modified: 2008-08-31 1202 EST
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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionRev`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.6.3', '664', '1220155200', 'This release addresses ...', '...' )");

/**

# need to fix award issue

- Updated skin location code to work better on Windows machines (local and servers)
- Fixed display issues with character images and tour images in Firefox 3
- Updated reflection script to version 1.9

* skins/default/header.php
* skins/cobalt/header.php
* update.php
* pages/bio.php
* pages/tour.php
* framework/js/reflect.js

+ update/262.php

**/

?>