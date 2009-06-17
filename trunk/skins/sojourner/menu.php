<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: skins/sojourner/menu.php
Purpose: Page that creates the navigation menu for SMS 2

Skin Version: 1.0
Last Modified: 2009-06-01 0832 EST
**/

$name_raw = explode('/', $_SERVER['SCRIPT_NAME']);
$name = end($name_raw);
$page = (isset($_GET['page'])) ? $_GET['page'] : FALSE;

?>

<script type="text/javascript">
	$(document).ready(function(){
		$('#nav-main').clickMenu();
		$('ul.hidemenu').show();
		
		$('#cycle').cycle({ 
		    fx:     'fade', 
		    speed:  'slow', 
		    timeout: 0, 
		    next:   '#next', 
		    prev:   '#prev' 
		});
	});
</script>

<?php

include_once(SKIN_PATH . 'assets/functions.php');
include_once(SKIN_PATH . 'assets/classMenuOverride.php');

/* create a new instance of the menu class */
$menu = new MenuOverride;

if(isset($sessionCrewid))
{
	$menu->skin = $sessionDisplaySkin;
}

?>

<div id="header">
	<div class="mainNav">
		<div class="wrapper">
			<div class="float-right"><img src="<?php echo SKIN_PATH;?>images/sojourner.png" alt="" /></div>
			<?php $menu->main();?>
		</div>
	</div>
</div>

<div id="subhead">
	<div class="wrapper">
		<?php if ($name == 'index.php' && $page == 'main'): ?>
			<div class="cycle-content">
				<div class="cycle-nav">
					<a href="#" id="prev" class="nav-link prev-link">Prev</a>
					<a href="#" id="next" class="nav-link next-link">Next</a>
				</div>
				<div id="cycle" class="cycle-inner">
					<div class="cycle-container cycle-1">
						<div class="cycle-1-content"><?php echo missionInfo();?></div>
					</div>
					<div class="cycle-container">Content 2</div>
					<div class="cycle-container">Content 3</div>
				</div>
			</div>
		<?php else: ?>
		
		<?php endif;?>
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