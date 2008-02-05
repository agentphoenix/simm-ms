<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/post.php
Purpose: Page to pull in the necessary post page

System Version: 2.5.0
Last Modified: 2007-03-17 1924 EST
**/

/* define the page class and vars */
$pageClass = "admin";
$subMenuClass = "post";
$sub = $_GET['sub'];

/* if they have a session, continue */
if( isset( $sessionCrewid ) && in_array( "post", $sessionAccess ) ) {

	/* pull in the main navigation */
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
	
	/* pull in the requested page */
	include_once( 'post/' . $sub . '.php' );

}

?>