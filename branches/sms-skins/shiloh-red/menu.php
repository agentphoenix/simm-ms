<?php

/**
This skin is the property of its owner and should not be duplicated or
reproduced with the express written consent of the author. Edits to this skin
are permissible if the original credits stay intact.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: skins/shiloh-blue/menu.php
Purpose: Page that creates the navigation menu for SMS 2

Skin Version: 1.0
Last Modified: 2007-08-13 1023 EST
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

if(isset($sessionCrewid))
{
	$menu->skin = $sessionDisplaySkin;
}

?>

<div id="header">

	<div class="top">
		<div class="topHeader">
			<?php printText( $shipPrefix . " " . $shipName . " " . $shipRegistry ); ?>
		</div>
	</div>
	<div class="bottom">
		<div class="mainNav">
			<?php

			$menu->main();

			if(isset($sessionCrewid))
			{
				$menu->user($sessionCrewid);
			}

			?>
		</div>
	</div>
	
</div>

<div id="container">

<div class="subcontainer">

<div class="content">
	<div class="contentHeader">
	
	</div>
	
	<div class="nav">
		<div class="login">
		<? if( isset( $sessionCrewid ) ) { ?>
			<i>Hello, <? printCrewName( $sessionCrewid, "noRank", "noLink" ); ?></i><br />
			[ <a href="<?=$webLocation;?>login.php?action=logout">Log Out</a> ]
		<? } else { ?>
			<form method="post" action="<?=$webLocation;?>login.php?action=checkLogin" class="login">
				<b>Username</b><br />
				<input type="text" name="username" size="12" class="text" /><br /><br />
				
				<b>Password</b><br />
				<input type="password" name="password" size="12" class="text" /><br /><br />
				
				<input type="image" src="<?=SKIN_PATH;?>buttons/login-small.png" name="submit" class="buttonSmall" value="Login" />
			</form>
			<br />
			<a href="<?=$webLocation;?>login.php?action=reset">&laquo; Reset Password</a>
		<? } ?>
		<br /><br />
		<? include_once( 'framework/stardate.php' ); ?>
		</div> <!-- close the .login layer -->
		<br />

		<?
		
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
			
		} elseif( $pageClass == "admin" ) {
			
			/* pull in the admin menus */
			$menu->admin( "post", $sessionAccess, $sessionCrewid );
			$menu->admin( "manage", $sessionAccess, $sessionCrewid );
			$menu->admin( "reports", $sessionAccess, $sessionCrewid );
			$menu->admin( "user", $sessionAccess, $sessionCrewid );
		
		}
		
		?>
	</div> <!-- close the .nav layer -->