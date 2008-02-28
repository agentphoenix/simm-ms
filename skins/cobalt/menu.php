<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: skins/cobalt/menu.php
Purpose: Page that creates the navigation menu for SMS 2

System Version: 2.5.0
Last Modified: 2007-04-29 1534 EST
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

?>

<div id="mainNav">
	<div class="mainNavInner">
		<?php
	
		$menu->main();
	
		if( isset( $sessionCrewid ) ) {
			$menu->user( $sessionCrewid );
		}
	
		?>
	</div>
</div>

<div id="header">
	<img src="skins/cobalt/images/header-left.jpg" alt="" style="float:left;" />
	<img src="skins/cobalt/images/sms.jpg" alt="" style="float:left;" />
	<img src="skins/cobalt/images/header-right.jpg" alt="" style="float:right;" />
	<img src="skins/cobalt/images/anodyne.jpg" alt="" style="float:right;" />
</div>

<div class="content">
	<div class="nav">
		<div class="login">
		<? if( isset( $sessionCrewid ) ) { ?>
			<i>Hello, <? printCrewName( $sessionCrewid, "noRank", "noLink" ); ?></i><br />
			{ <a href="<?=$webLocation;?>login.php?action=logout">Log Out</a> }
		<? } else { ?>
			<form method="post" action="<?=$webLocation;?>login.php?action=checkLogin" class="login">
				<b>Username</b><br />
				<input type="text" name="username" size="12" class="loginSmallText" /><br /><br />
				
				<b>Password</b><br />
				<input type="password" name="password" size="12" class="loginSmallText" /><br /><br />
				
				<input type="image" src="skins/cobalt/buttons/login-small" name="submit" class="buttonLogin" value="Login" />
			</form>
			<br />
			<a href="<?=$webLocation;?>login.php?action=reset">&laquo; Reset Password</a>
		<? } ?>
		</div>
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
	</div>