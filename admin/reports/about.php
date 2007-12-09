<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/reports/about.php
Purpose: Page to show the information about the site

System Version: 2.5.0
Last Modified: 2007-07-09 1308 EST
**/

/* access check */
if( in_array( "r_about", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "reports";
	
?>
	
	<div class="body">
		<span class="fontTitle">About Simm Management System</span><br /><br />
		
			The information on this page is generated dynamically by SMS and will show you
			the some of the major pieces of information about your version of SMS.  Please
			note, there is no way to change these variables manually.  They are stored in
			the database and updated only in the event of a patch or update. If you need to
			request support with the system, please copy and paste the following information
			into the post on our <a href="http://forums.anodyne-productions.com/" target="_blank">
			support forums</a>.<br /><br />
	
			<? aboutSMS( $version, $webLocation ); ?>
			
	</div>

<? } else { errorMessage( "about SMS" ); } ?>