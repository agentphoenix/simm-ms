<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: pages/simm.php
Purpose: Main page for the simm section

System Version: 2.5.0
Last Modified: 2007-03-21 2343 EST
**/

/* define the page class and vars */
$pageClass = "simm";
$co = printCO();

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

?>

<div class="body">
	<span class="fontTitle">Welcome to the Simm</span>
	<p><? printText( $simmMessage ); ?></p>
	<br />
	
	<p>
		<b><? printText( $co ); ?><br />
		Commanding Officer, <? printText( $shipPrefix . " " . $shipName ); ?><br />
		<?

		if( $tfMember == "y" ) {
			echo $tfName . ", ";
		}
		
		echo $fleet;

		?></b>
	</p>
</div>