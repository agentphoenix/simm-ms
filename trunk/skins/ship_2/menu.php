<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: skins/ship_1/menu.php
Purpose: Page that creates the navigation menu for SMS 2

Skin Version: 2.0
Last Modified: 2008-07-04 1622 EST
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

/* pull the ship class */
$get = "SELECT shipClass FROM sms_specs WHERE specid = 1";
$getR = mysql_query($get);
$fetch = mysql_fetch_array($getR);

/* format the ship class right */
$class = strtolower($fetch[0]);
$class = str_replace(' ', '_', $class);

/* define the ship class constant */
define('SHIP_CLASS', $class);

?>

<div id="container">
	<div class="top_color"></div>
	<div class="header">
		<div class="wrapper">
			<!-- <img src="<?=SKIN_PATH;?>images/ship_<?=SHIP_CLASS;?>.jpg" border="0" alt="" /> -->
		</div>
	</div>
	<div class="mainNav">
		<div class="wrapper">
			<?php
		
			$menu->main($sessionCrewid);
		
			if(isset($sessionCrewid))
			{
				$menu->user($sessionCrewid);
			}
		
			?>
		</div>
	</div>

	<div class="content">
		<div class="wrapper">
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
				
						<input type="image" src="skins/akira/buttons/login-small.png" name="submit" class="submitButton" value="Login" />
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