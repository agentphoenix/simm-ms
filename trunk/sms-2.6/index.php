<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: index.php
Purpose: The main file that pulls in the requested page

System Version: 2.6.0
Last Modified: 2007-11-13 1544 EST
**/

/* start the session */
session_start();

/* pull in the DB connection variables */
require_once( 'framework/dbconnect.php' );

/* query the db for the system information */
$getVer = "SELECT sysVersion FROM sms_system WHERE sysid = 1";
$getVerResult = mysql_query( $getVer );
	
if( !empty( $getVerResult ) ) {
	$updateVersion = mysql_fetch_array( $getVerResult );
}

/*
make sure the user is running 2.5, and if not, push them
to the install page to update from the earlier version
*/
if( $updateVersion[0] < "2.5.0" || empty( $webLocation ) ) {
	header( 'Location: install.php' );
	exit;
} else {
	
	/* close the db connection to avoid any problems */
	mysql_close( $db );

	/* pull in the global functions file */
	require_once( 'framework/functionsGlobal.php' );
	require_once( 'framework/functionsAdmin.php' );
	require_once( 'framework/functionsUtility.php' );
	require_once( 'framework/classUtility.php' );
	require_once( 'framework/classMenu.php' );
	
	/* get the referenced page from the URL */
	$page = $_GET['page'];

	/* define the session variables */
	$sessionCrewid = $_SESSION['sessionCrewid'];
	$sessionAccessLevel = $_SESSION['sessionAccessLevel'];
	$sessionDisplaySkin = $_SESSION['sessionDisplaySkin'];
	$sessionDisplayRank = $_SESSION['sessionDisplayRank'];
	
	/*
		check to see if the session access variable is an array
		and if it isn't, explode the string
	*/
	if( !is_array( $sessionAccess ) ) {
		$sessionAccess = explode( ",", $_SESSION['sessionAccess'] );
	}
	
	/* if there is no page set, send them to the main page */
	if( !isset( $page ) ) {
		$page = "main";
	}
	
	/* grab the user's skin choice, otherwise, use the system default */
	if( isset( $sessionDisplaySkin ) && $code == $_SESSION['systemUID'] ) {
		include_once( 'skins/' . $sessionDisplaySkin . '/header.php' );
	} else {
		include_once( 'skins/' . $skin . '/header.php' );
	}
	
	/* pull in the page referenced in the URL */
	if( file_exists( 'pages/' . $page . '.php' ) ) {	
		include_once( 'pages/' . $page . '.php' );
	} else {
		include_once( 'pages/error.php' );
	}
	
	/* grab the user's skin choice, otherwise, use the system default */
	if( isset( $sessionDisplaySkin ) && $code == $_SESSION['systemUID'] ) {
		include_once( 'skins/' . $sessionDisplaySkin . '/footer.php' );
	} else {
		include_once( 'skins/' . $skin . '/footer.php' );
	}

}

?>