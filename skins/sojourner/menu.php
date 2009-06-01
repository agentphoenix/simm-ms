<?php

/**
Edits to this skin are permissible if the original credits stay intact.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: skins/sojourner/menu.php
Purpose: Page that creates the navigation menu for SMS 2

Skin Version: 2.5
Last Modified: 2008-04-19 0130 EST
**/

$navwidth = (isset($sessionCrewid)) ? 558 : 440;
$bodywidth = (CUR_PAGE == 'admin.php') ? '20%' : '0%';

?>

<script type="text/javascript">
	$(document).ready(function(){
		$('#nav-main').clickMenu();
		$('ul.hidemenu').show();
		
		$('.wrapper-center').css('width', <?php echo $navwidth;?>);
		$('#container .content .body').css('margin-right', '<?php echo $bodywidth;?>');
	});
</script>

<?php

include_once(SKIN_PATH . 'classMenuOverride.php');

/* create a new instance of the menu class */
$menu = new MenuOverride;

if(isset($sessionCrewid))
{
	$menu->skin = $sessionDisplaySkin;
}

?>

<div id="header"></div>

<div id="subhead"></div>

<div class="mainNav">
	<div class="wrapper">
		<div class="wrapper-center">
			<?php $menu->main();?>
		</div>
	</div>
</div>

<div id="container" class="wrapper">
	<div class="content">
		
		<?php if (CUR_PAGE == 'admin.php'): ?>
			<div class="nav">
				<div class="login">
				<? if(isset($sessionCrewid)) { ?>
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
				<?php include_once('framework/stardate.php');?>
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
		<?php endif;?>