<?php

/**
This skin is the property of its owner and should not be duplicated or
reproduced with the express written consent of the author. Edits to this skin
are permissible if the original credits stay intact.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: skins/default/menu.php
Purpose: Page that creates the navigation menu for SMS 2

Skin Version: 2.1
Last Modified: 2007-11-08 1056 EST
**/

?>

<script type="text/javascript">
	$(document).ready(function(){
		$('#container-mainnav > ul').tabs();
		$('#list').clickMenu();
	});
</script>

<?php

/* create a new instance of the menu class */
$menu = new Menu;

?>

<div class="mainNav">
<?php if( !isset( $sessionCrewid ) ) { ?>
		
		<ul id="list"> 
		    <li><img src="dev/arrow.png" alt=">>" border="0" />
		        <ul>
		            <li><a href="#1">Account</a></li>
		            <li><a href="#2">Biography</a></li>
					<hr size="1" noshade color="#cc0000" />
					<li><a href="#3">Write Personal Log</a></li>
		            <li><a href="#4">All Characters</a></li>
		        </ul>
		    </li>
		</ul>
		
		<? $menu->main( $sessionCrewid ); ?>
<?php } else { ?>
	<div id="container-mainnav">
		<ul>
			<li><a href="#mainNav"><span>Global</span></a></li>
			<li><a href="#userNav"><span>User</span></a></li>
		</ul>
		<div id="mainNav" class="ui-tabs-container ui-tabs-hide">
			<? $menu->main( $sessionCrewid ); ?>
		</div>
		<div id="userNav" class="ui-tabs-container ui-tabs-hide">
			<? $menu->user( $sessionCrewid ); ?>
		</div>
	</div>
<?php } ?>
</div><br />

<div class="content">
	<div class="nav">
		<div class="login">
		<? if( isset( $sessionCrewid ) ) { ?>
			<i>Hello, <? printCrewName( $sessionCrewid, "noRank", "noLink" ); ?></i><br />
			{ <a href="<?=$webLocation;?>login.php?action=logout">Log Out</a> }
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