<?php

/**
Edits to this skin are permissible if the original credits stay intact.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: skins/pulse/menu.php
Purpose: Page that creates the navigation menu for SMS 2

Skin Version: 1.0
Last Modified: 2008-03-26 1813 EST
**/

?>

<script type="text/javascript">
	$(document).ready(function(){
		$('#list').clickMenu();
		$('ul.hidemenu').show();
	});
</script>

<?php

/* create a new instance of the menu class */
$menu = new Menu;
$menu->skin = $sessionDisplaySkin;

?>

<div class="header">
	<img src="<?=SKIN_PATH;?>images/header_left.png" alt="" style="float:left;" />
	<img src="<?=SKIN_PATH;?>images/header_right.png" alt="" style="float:right;" />
	
	<img src="<?=SKIN_PATH;?>images/header_text.png" alt="" style="float:left;" />
	
	<div class="login">
	<? if( isset( $sessionCrewid ) ) { ?>
		<strong>Welcome, <? printCrewName( $sessionCrewid, "noRank", "noLink" ); ?></strong>
		&nbsp; | &nbsp;
		<strong><a href="<?=$webLocation;?>login.php?action=logout">Log Out</a></strong>
	<? } else { ?>
		<strong><a href="<?=$webLocation;?>login.php?action=login">Login</a></strong>
	<? } ?>
	</div>
</div>

<div class="mainNav">
	<?php
	
	$menu->main();
	
	if( isset( $sessionCrewid ) ) {
		$menu->user( $sessionCrewid );
	}
	
	?>
</div><br />

<div class="nav">
	<?
	
	include_once( 'framework/stardate.php' );
	echo "<br /><br />";
	
	if( $pageClass == "main" ) {
	
		/* pull in the menu */
		$menu->general( "main" );
	
		/* include the info page */
		include_once( "pages/info.php" );
	
	} elseif( $pageClass == "personnel" ) {
	
		/* pull in the menu */
		$menu->general( "personnel" );
	
	} elseif( $pageClass == "ship" ) {
	
		/* pull in the menu */
		$menu->general( $simmType );
	
	} elseif( $pageClass == "simm" ) {
		
		/* pull in the menu */
		$menu->general( "simm" );
		
	}
	
	?>
</div>

<div class="content">
	<?php
	
	if( $pageClass == "admin" ) {
			
			/* pull in the admin menus */
			$menu->admin( "post", $sessionAccess, $sessionCrewid );
			$menu->admin( "manage", $sessionAccess, $sessionCrewid );
			$menu->admin( "reports", $sessionAccess, $sessionCrewid );
			$menu->admin( "user", $sessionAccess, $sessionCrewid );
		
		}
		
		?>