<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: install.php
Purpose: Main page to direct users to one of three installation options

System Version: 2.5.0
Last Modified: 2007-07-11 2106 EST
**/

if( $_GET['type'] == "update" ) {

	/* pull in the DB connection variables */
	require_once( 'framework/variables.php' );
	
	/* database connection */
	$db = @mysql_connect( "$dbServer", "$dbUser", "$dbPassword" ) or die ( "<b>$dbErrorMessage</b>" );
	mysql_select_db( "$dbTable",$db ) or die ( "<b>Unable to select the appropriate database.  Please try again later.</b>" );
	
	/* query the db for the system information */
	$getVer = "SELECT sysVersion FROM sms_system WHERE sysid = 1";
	$getVerResult = mysql_query( $getVer );
	$updateVersion = mysql_fetch_array( $getVerResult );
	
	/* format the version string properly */
	$updateVersion = str_replace( ".", "", $updateVersion[0] );
	
	/* do some checking for the 2.4.4.1 release */
	if( $updateVersion == "2441" ) {
		$updateVersion = "244";
	}
	
}

?>

<html>
<head>
	<title>SMS 2.5 Install</title>
	<link rel="stylesheet" type="text/css" href="install/install.css" />
</head>
<body>
	<div id="install">	
		<div class="header">
			<img src="install/install.jpg" alt="" border="0" />
		</div> <!-- close .header -->
		<div class="content">
			Welcome to the Simm Management System installation center. From here, you will be able
			to proceed with the installation or upgrade of SMS to version 2.5 by one of several ways:
			<ul>
				<li>If you don't have SMS 2 installed on your server, OR, you want to ignore
				any previous versions of SMS and start fresh, select the fresh install option.</li>
				<li>If you have a previous version of SMS 2 on your server and would like to update
				to 2.5, select the update option.</li>
				<li>If you have SMS 1.2 or SMS 1.5 still installed and would like to upgrade to version
				2.5, you will need to get the update scripts from the 
				<a href="http://www.anodyne-productions.com/index.php?sec=sms&page=downloads">Downloads page</a>
				on the Anodyne website. Please follow the directions in the update archives then run the
				necessary update file to bring your copy of SMS to version 2.5.</li>
			</ul>
			
			<div align="center">
				<table cellspacing="0" cellpadding="0">
					<tr>
						<td align="center" colspan="3">
							<a href="install/install.php"><img src="install/sms2.jpg" alt="Fresh Install" border="0" /></a>
						</td>
					</tr>
					<tr>
						<td height="20"></td>
					</tr>
					<tr>
						<td align="center" colspan="3">
							<a href="update.php?version=<?=$updateVersion;?>"><img src="install/update.jpg" alt="Update" border="0" /></a>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="footer">
			Copyright &copy; 2005-<?php echo date('Y'); ?> by <a href="http://www.anodyne-productions.com/" target="_blank">Anodyne Productions</a><br />
			SMS 2 designed by <a href="mailto:anodyne.sms@gmail.com">David VanScott</a>
		</div> <!-- close .footer -->
	</div> <!-- close #install -->
</body>
</html>