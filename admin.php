<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin.php
Purpose: The main file that pulls in the requested administration page

System Version: 2.5.0
Last Modified: 2007-07-09 1312 EST
**/

/* start the session */
session_start();

/* pull in the DB connection variables */
require_once( 'framework/variables.php' );

/* database connection */
$db = @mysql_connect( "$dbServer", "$dbUser", "$dbPassword" ) or die ( "<b>$dbErrorMessage</b>" );
mysql_select_db( "$dbTable",$db ) or die ( "<b>Unable to select the appropriate database.  Please try again later.</b>" );

/* query the db for the system information */
$getVer = "SELECT sysVersion FROM sms_system WHERE sysid = 1";
$getVerResult = mysql_query( $getVer );
$updateVersion = mysql_fetch_array( $getVerResult );
	
/*
make sure the user is running 2.5, and if not, push them
to the install page to update from the earlier version
*/
if( $updateVersion[0] < "2.5.0" ) {
	header( 'Location: ' . $webLocation . 'install.php' );
} else {
	
	/* close the db connection to avoid any problems */
	mysql_close( $db );

	/* pull in the global functions file */
	require_once( 'framework/functionsGlobal.php' );
	require_once( 'framework/functionsAdmin.php' );
	require_once( 'framework/functionsUtility.php' );
	require_once( 'framework/classUtility.php' );
	require_once( 'framework/classMenu.php' );
	
	/* Bring in the required files to check browser compatability */
	require_once( 'framework/phpsniff/phpSniff.class.php' );
	require_once( 'framework/phpsniff/phpTimer.class.php' );
	
	/* initialize some vars */
	$GET_VARS = isset( $_GET ) ? $_GET : $HTTP_GET_VARS;
	$POST_VARS = isset( $_POST ) ? $_GET : $HTTP_POST_VARS;
	if( !isset( $GET_VARS['UA'] ) ) $GET_VARS['UA'] = '';
	if( !isset( $GET_VARS['cc'] ) ) $GET_VARS['cc'] = '';
	if( !isset( $GET_VARS['dl'] ) ) $GET_VARS['dl'] = '';
	if( !isset( $GET_VARS['am'] ) ) $GET_VARS['am'] = '';
	
	$timer =& new phpTimer();
	$timer->start( 'main' );
	$timer->start( 'client1' );
	$sniffer_settings = array( 'check_cookies'=>$GET_VARS['cc'],
							  'default_language'=>$GET_VARS['dl'],
							  'allow_masquerading'=>$GET_VARS['am'] );
	$client =& new phpSniff( $GET_VARS['UA'],$sniffer_settings );
	
	$timer->stop( 'client1' );
	
	/* set the variables */
	$page = $_GET['page'];

	/* define the session variables */
	$sessionCrewid = $_SESSION['sessionCrewid'];
	$sessionAccessLevel = $_SESSION['sessionAccessLevel'];
	$sessionDisplaySkin = $_SESSION['sessionDisplaySkin'];
	$sessionDisplayRank = $_SESSION['sessionDisplayRank'];
	
	/* define some path variables */
	define( 'path_userskin', $webLocation . 'skins/' . $sessionDisplaySkin . '/' );
	
	/*
		check to see if the session access variable is an array
		and if it isn't, explode the string
	*/
	if( !is_array( $sessionAccess ) ) {
		$sessionAccess = explode( ",", $_SESSION['sessionAccess'] );
	}
	
	/* if there is no page set, send them to the main page */
	if( !$page ) {
		$page = "main";
	}
	
	/* if the session is set, continue, otherwise, send them to the index page */
	if( isset( $sessionCrewid ) && $code == $_SESSION['systemUID'] ) {
		
		/* grab the user's skin choice, otherwise, use the system default */
		if( isset( $sessionDisplaySkin ) ) {
			include_once( 'skins/' . $sessionDisplaySkin . '/header.php' );
		} else {
			include_once( 'skins/' . $skin . '/header.php' );
		}
			
		/* pull in the page referenced in the URL */
		include_once( 'admin/' . $page . '.php' );
		
		/* grab the user's skin choice, otherwise, use the system default */
		if( isset( $sessionDisplaySkin ) ) {
			include_once( 'skins/' . $sessionDisplaySkin . '/footer.php' );
		} else {
			include_once( 'skins/' . $skin . '/footer.php' );
		}
	
	} else {
		header( 'Location: ' . $webLocation . 'login.php?action=login&login=false&error=3' );
	}
	
}

?>