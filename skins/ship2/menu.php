<?php

/**
Edits to this skin are permissible if the original credits stay intact.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: skins/ship2/menu.php
Purpose: Page that creates the navigation menu for SMS 2

Skin Version: 1.0
Last Modified: 2008-03-29 1901 EST
**/

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$getClass = "SELECT shipClass FROM sms_specs WHERE specid = 1";
$getClassR = mysql_query($getClass);
$class = mysql_fetch_row($getClassR);

if(!file_exists(SKIN_PATH . 'images/' . strtolower($class[0]) . '.jpg'))
{
	$ship = "space";
}
else
{
	$ship = $class[0];
}

?>

<div class="mainNav">
	<div class="mainNavInner">
		<?php
	
		$menu->main();
	
		if( isset( $sessionCrewid ) ) {
			$menu->user( $sessionCrewid );
		}
	
		?>
	</div>
</div><br />

<div class="header">
	<div align="center">
		<img src="<?=SKIN_PATH;?>images/<?=$ship;?>.jpg" alt="" border="0" />
	</div>
</div>

<div class="nav">
	<?
	
	include_once( 'framework/stardate.php' );
	echo "<br /><br />";
	
	?>
	
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
			
			<input type="image" src="skins/cobalt/buttons/login-small.png" name="submit" class="buttonLogin" value="Login" />
		</form>
		<br />
		<a href="<?=$webLocation;?>login.php?action=reset">&laquo; Reset Password</a>
	<? } ?>
	</div>
	<br />
	
	<?php
	
	if( $pageClass == "main" ) {
		$menu->general( "main" );
		include_once( "pages/info.php" );
	} elseif( $pageClass == "personnel" ) {
		$menu->general( "personnel" );
	} elseif( $pageClass == "ship" ) {
		$menu->general( $simmType );
	} elseif( $pageClass == "simm" ) {
		$menu->general( "simm" );
	} elseif( $pageClass == "admin" ) {
		$menu->admin( "post", $sessionAccess, $sessionCrewid );
		$menu->admin( "manage", $sessionAccess, $sessionCrewid );
		$menu->admin( "reports", $sessionAccess, $sessionCrewid );
		$menu->admin( "user", $sessionAccess, $sessionCrewid );
	}
	
	?>
</div>

<div class="content">