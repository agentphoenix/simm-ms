<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: Nathan Wharry [ mail@herschwolf.net ]
File: pages/history.php
Purpose: To display the history of the ship/starbase

System Version: 2.5.0
Last Modified: 2007-02-08 1332 EST
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
	<span class="fontTitle">History of the <? printText( $shipPrefix . " " . $shipName ); ?></span><br /><br />
	<? printText( $shipHistory );?>
</div>