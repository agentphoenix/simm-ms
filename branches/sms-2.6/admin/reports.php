<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/reports.php
Purpose: Page to pull in the necessary report page

System Version: 2.6.0
Last Modified: 2007-11-08 1103 EST
**/

/* define the page class and variables */
$pageClass = "admin";
$subMenuClass = "reports";
$sub = $_GET['sub'];

/* if they have a session, continue processing */
if( isset( $sessionCrewid ) && in_array( "reports", $sessionAccess ) ) {

	/* pull in the main navigation */
	include_once( "skins/" . $sessionDisplaySkin . "/menu.php" );

	/* pull in the requested page */
	if( file_exists( $pageClass . '/' . $subMenuClass . '/' . $sub . '.php' ) ) {
		include_once( $subMenuClass . '/' . $sub . '.php' );
	} else {
		include_once( 'error.php' );
	}

}

?>