<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: pages/ship.php
Purpose: Main page to display the ship welcome

System Version: 2.5.0
Last Modified: 2007-04-05 1504 EST
**/

/* define the page class */
$pageClass = "ship";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

?>

<div class="body">
	<span class="fontTitle"><? printText( $shipPrefix . " " . $shipName . " " . $shipRegistry ); ?></span>
	<p><? printText( $shipMessage ); ?></p>
</div>