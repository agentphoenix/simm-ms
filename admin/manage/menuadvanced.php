<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/menuadvanced.php
Purpose: Page to manage the menu items

System Version: 2.6.0
Last Modified: 2008-02-25 1339 EST
**/

/* access check */
if( in_array( "x_menu", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$update = $_POST['action_update_x'];
	$delete = $_POST['action_delete_x'];
	$add = $_POST['action_add_x'];
	$sec = $_GET['sec'];
	$subsec = $_GET['subsec'];
	$create = $_GET['create'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( "menu management page" );
		exit();
	} else {
		/* set the GET variable */
		$id = $_GET['id'];
	}
	
	/* set up the default section */
	if( !$sec ) {
		$sec = "main";
	}
	
	/* set up the default sub section for the general tab */
	if( $sec == "general" && !$subsec ) {
		$subsec = "main";
	}
	
	/* set up the default sub section for the admin tab */
	if( $sec == "admin" && !$subsec ) {
		$subsec = "post";
	}
	
	if( $update ) {
		
		/* define the POST variables */
		$menuTitle = addslashes( $_POST['menuTitle'] );
		$menuLink = $_POST['menuLink'];
		$menuLinkType = $_POST['menuLinkType'];
		$menuGroup = $_POST['menuGroup'];
		$menuOrder = $_POST['menuOrder'];
		$menuLogin = $_POST['menuLogin'];
		$menuCat = $_POST['menuCat'];
		$menuMainSec = $_POST['menuMainSec'];
		$menuAvailability = $_POST['menuAvailability'];
		
		if( $sec == "main" ) {
		
			/* run the update query */
			$query = "UPDATE sms_menu_items SET menuTitle = '$menuTitle', menuLink = '$menuLink', ";
			$query.= "menuLinkType = '$menuLinkType', menuOrder = '$menuOrder', menuLogin = '$menuLogin', ";
			$query.= "menuCat = '$menuCat', menuMainSec = '$menuMainSec', menuGroup = '$menuGroup', ";
			$query.= "menuAvailability = '$menuAvailability' WHERE menuid = '$id' LIMIT 1";
			$result = mysql_query( $query );
		
		} if( $sec == "general" ) {
			
			/* run the update query */
			$query = "UPDATE sms_menu_items SET menuTitle = '$menuTitle', menuLink = '$menuLink', ";
			$query.= "menuLinkType = '$menuLinkType', menuOrder = '$menuOrder', menuLogin = '$menuLogin', ";
			$query.= "menuCat = '$menuCat', menuMainSec = '$menuMainSec', menuGroup = '$menuGroup', ";
			$query.= "menuAvailability = '$menuAvailability'WHERE menuid = '$id' LIMIT 1";
			$result = mysql_query( $query );
		
		} if( $sec == "admin" ) {
			
			/* define additional POST variables */
			$menuAccess = $_POST['menuAccess'];
			
			/* run the update query */
			$query = "UPDATE sms_menu_items SET menuTitle = '$menuTitle', menuLink = '$menuLink', ";
			$query.= "menuLinkType = '$menuLinkType', menuOrder = '$menuOrder', menuGroup = '$menuGroup', ";
			$query.= "menuCat = '$menuCat', menuMainSec = '$menuMainSec', menuLogin = '$menuLogin', ";
			$query.= "menuAccess = '$menuAccess', menuAvailability = '$menuAvailability' WHERE menuid = '$id' LIMIT 1";
			$result = mysql_query( $query );
		
		}
		
		/* optimize the table */
		optimizeSQLTable( "sms_menu_items" );
		
		$action = "update";
		
	} if( $add ) {
	
		/* define the POST variables */
		$menuTitle = addslashes( $_POST['menuTitle'] );
		$menuLink = $_POST['menuLink'];
		$menuLinkType = $_POST['menuLinkType'];
		$menuOrder = $_POST['menuOrder'];
		$menuLogin = $_POST['menuLogin'];
		$menuCat = $_POST['menuCat'];
		$menuMainSec = $_POST['menuMainSec'];
		
		/* run the query */
		$query = "INSERT INTO sms_menu_items ";
		$query.= "( menuid, menuTitle, menuLink, menuLinkType, menuOrder, menuLogin, menuCat, menuMainSec ) ";
		$query.= "VALUES ( '', '$menuTitle', '$menuLink', '$menuLinkType', '$menuOrder', '$menuLogin', '$menuCat', '$menuMainSec' )";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_menu_items" );
		
		$action = "add";
	
	} if( $delete ) {
	
		/* run the delete query */
		$query = "DELETE FROM sms_menu_items WHERE menuid = '$id' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_menu_items" );
		
		$action = "delete";
	
	}
	
	/* the queries */
	$getMain = "SELECT * FROM sms_menu_items WHERE menuCat = 'main' ORDER BY menuGroup, menuOrder ASC";
	$getMainResult = mysql_query( $getMain );
	
	$getGeneral = "SELECT * FROM sms_menu_items WHERE menuCat = 'general' ORDER BY menuGroup, menuOrder ASC";
	$getGeneralResult = mysql_query( $getGeneral );
	
	$getAdmin = "SELECT * FROM sms_menu_items WHERE menuCat = 'admin' ORDER BY menuGroup, menuOrder ASC";
	$getAdminResult = mysql_query( $getAdmin );

?>

	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "menu item", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Advanced Menu Management</span><br /><br />
		Use this page to edit the menus used throughout SMS. From the advanced menu management page you will
		be able to change anything about a menu item or delete the item entirely. <b class="red">
		Please use extreme caution when editing menu items. Incorrect modification can cause you to not be able to
		access the menu items any more! Deletions cannot be undone.</b> Changes made to any menu item will affect 
		that item across all skins in the system.<br /><br />
		
		<b class="fontMedium"><a href="<?=$webLocation;?>admin.php?page=manage&sub=menugeneral">&laquo; Basic Menu Management</a></b>
		<br />
		<b class="fontMedium"><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&create">Add Menu Item &raquo;</a></b>
		<br /><br />
		
		<? if( isset( $create ) && !$add ) { ?>
			<div class="update">
				<span class="fontTitle">Add Menu Item</span><br /><br />
				<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>">
					<table>
						<tr>
							<td class="tableCellLabel">Menu Item Title</td>
							<td></td>
							<td><input type="text" class="text" name="menuTitle" size="40" /></td>
						</tr>
						<tr>
							<td class="tableCellLabel">Menu Item Link Type</td>
							<td></td>
							<td>
								<select name="menuLinkType">
									<option value="onsite">Onsite</option>
									<option value="offsite">Offsite</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="tableCellLabel">Menu Item Link</td>
							<td></td>
							<td><input type="text" class="text" name="menuLink" size="40" /></td>
						</tr>
						<tr>
							<td class="tableCellLabel">Menu Category</td>
							<td></td>
							<td>
								<select name="menuCat">
									<option value="main">Main Navigation</option>
									<option value="general">General Menus</option>
									<option value="admin">Admin Menus</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="tableCellLabel">Menu Section</td>
							<td></td>
							<td>
								<select name="menuMainSec">
									<optgroup label="Main Navigation">
										<option value="">Main Navigation</option>
									</optgroup>
									<optgroup label="General Menus">
										<option value="main">Main</option>
										<option value="personnel">Personnel</option>
										<option value="ship"><?=ucfirst( $simmType );?></option>
										<option value="simm">Simm</option>
									</optgroup>
									<optgroup label="Admin Menus">
										<option value="post">Post</option>
										<option value="manage">Manage</option>
										<option value="reports">Reports</option>
										<option value="user">User</option>
									</optgroup>
								</select>
							</td>
						</tr>
						<tr>
							<td class="tableCellLabel">Menu Item Order</td>
							<td></td>
							<td><input type="text" class="text" name="menuOrder" size="3" /></td>
						</tr>
						<tr>
							<td class="tableCellLabel">Requires Login?</td>
							<td></td>
							<td>
								<input type="radio" id="menuLoginY" name="menuLogin" value="y" /><label for="menuLoginY">Yes</label>
								<input type="radio" id="menuLoginN" name="menuLogin" value="n" checked="y" /><label for="menuLoginN">No</label>
							</td>
						</tr>
						<tr>
							<td colspan="3" height="25"></td>
						</tr>
						<tr>
							<td colspan="3">
								<input type="image" src="<?=path_userskin;?>buttons/add.png" class="button" name="action_add" value="add" />
							</td>
						</tr>
					</table>
				</form>
			</div>
			<br />
		<? } ?>
		
		<? if( $simmType == "starbase" || $manifestDisplay == "full" || $usePosting == "n" || $useMissionNotes == "n" ) { ?>
		<div class="update">
			<a href="javascript:toggleLayer('notes')" style="float:right;">Show/Hide</a>
			<img src="<?=$webLocation;?>images/notes.png" style="float:left; padding-right: 12px;" border="0" />
			<span class="fontTitle">Notes</span>
			
			<div id="notes" style="display:none;">
			<br />
			
			Additional information about menu changes is available through Anodyne's <a href="http://docs.anodyne-productions.com/index.php?title=Changing_Menus_Around" target="_blank">
			online documentation</a>.<br /><br />
		
			<? if( $simmType == "starbase" ) { ?>
			Your simm type is set to STARBASE. Please make sure you make the following changes to your menus!
			<ul class="version">
				<li><b>Main Navigation</b>
					<ol>
						<li>Please change your THE SHIP link to read THE STARBASE</li>
						<li>Please change your THE SHIP link (THE STARBASE after you have changed it) to point to
						<i>index.php?page=starbase</i></li>
					</ol>
				</li>
				<li><b>General Menus (The Ship)</b>
					<ol>
						<li>Please change both SHIP HISTORY and SHIP TOUR to reflect the different simm type</li>
						<li>Please add a menu item called DOCKED SHIPS. It should be part of group 0 and have an
						onsite link to <i>index.php?page=dockedships</i></li>
						<li>Please add a menu item called DOCKING REQUEST. It should be part of group 0 and have
						an onsite link to <i>index.php?page=dockingrequest</i></li>
					</ol>
				</li>
				<li><b>Admin Menus (Manage)</b>
					<ol>
						<li>Please add a menu item called DOCKED SHIPS. It should be part of group 4 and have an
						onsite link to <i>admin.php?page=manage&sub=docking</i></li>
					</ol>
				</li>
			</ul>
			<? } if( $usePosting == "n" ) { ?>
			Your simm type is set to NOT use the SMS Posting system. Please make sure you make the following changes to your menus!
			<ul class="version">
				<li><b>General Menus (The Simm)</b>
					<ol>
						<li>If you are not keeping records on your SMS site you will need to remove the CURRENT MISSION link,
						the MISSION LOGS link, and the MISSION SUMMARIES link</li>
						<li>You may also want to change all the existing menu items in the Simm section to use group zero
						instead of group one</li>
					</ol>
				</li>
				<li><b>Admin Menus</b>
					<ol>
						<li>We do NOT advise removing any of the admin menu items! The advisable way is to change each
						user's access levels to NOT include the post menu. If the main post item is not available, the
						post menus will not display.</li>
					</ol>
				</li>
			</ul>
			<? } if( $useMissionNotes == "n" ) { ?>
			Your simm type is set to NOT use the Mission Notes system. Please make sure you make the following changes to your menus!
			<ul class="version">
				<li><b>Admin Menus</b>
					<ol>
						<li>We do NOT advise removing any of the admin menu items! The advisable way is to change each
						user's access levels to NOT include the mission notes item in both the Post menu as well as
						the Manage menu.</li>
					</ol>
				</li>
			</ul>
			<? } ?>
			</div>
		</div>
		<? } ?>
		
		<div id="subnav">
			<ul>
				<li <? if( $sec == "main" ) { echo "id='current'"; } ?>><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=main">Main Navigation</a></li>
				<li <? if( $sec == "general" ) { echo "id='current'"; } ?>><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=general">General Menus</a></li>
				<li <? if( $sec == "admin" ) { echo "id='current'"; } ?>><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=admin">Admin Menus</a></li>
			</ul>
		</div>
	
		<div class="tabcontainer">
		<? if( $sec == "main" ) { ?>
			<br />
			<? if( !$id || isset( $delete ) ) { ?>
			The main navigation links are the links at the top of SMS that will take a user to the various
			sections of the site. By default, the only link that requires the user to be logged in is the
			Control Panel. In order to see changes to the main navigation menu, you may have to refresh
			the page after making changes.
			
			<ul class="list-dark">
				<?
				
				while( $menuadvanced = mysql_fetch_array( $getMainResult ) ) {
					extract( $menuadvanced, EXTR_OVERWRITE );
					
					echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=menuadvanced&sec=" . $sec . "&id=" . $menuid . "'>" . $menuTitle . "</a></li>";
					
				}
				
				?>
			</ul>
			<? } else { ?>
			
				<div class="postDetails fontNormal">
					<b class="fontMedium">Main Navigation Menu Items</b><br /><br />
					<table>
						<tr>
							<td><b>Title</b></td>
							<td><b>Order</b></td>
						</tr>
						<?
						
						while( $menuadvanced = mysql_fetch_array( $getMainResult ) ) {
							extract( $menuadvanced, EXTR_OVERWRITE );
							
							echo "<tr>";
								echo "<td>";
								if( $menuAvailability == "off" ) {
									echo "<b class='red'>OFF</span> &nbsp;";
								}
								echo "<a href='" . $webLocation . "admin.php?page=manage&sub=menuadvanced&sec=" . $sec . "&id=" . $menuid . "'>" . $menuTitle . "</a></td>";
								echo "<td>" . $menuOrder . "</td>";
							echo "</tr>";
							
						}
						
						?>
					</table>
				</div>
				
				<b class="fontLarge">Edit Menu Item</b><br /><br />
				Offsite links will open in a new window.  If you choose an offsite link, please provide the entire URL, otherwise,
				please only provide the information after the domain (i.e. index.php?page=main). Additional information about
				other menu items in this category are provided on the side.<br /><br />
				
				<b><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>">&laquo; Back to Menu Items</a></b><br /><br />
				
				<?
			
				$getItem = "SELECT * FROM sms_menu_items WHERE menuid = '$id' LIMIT 1";
				$getItemResult = mysql_query( $getItem );
				
				while( $itemFetch = mysql_fetch_array( $getItemResult ) ) {
					extract( $itemFetch, EXTR_OVERWRITE );
				}
			
				?>
			
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>&id=<?=$id;?>">
				<table>
					<tr>
						<td class="tableCellLabel">Menu Item Status</td>
						<td></td>
						<td>
							<input type="radio" name="menuAvailability" id="maOn" value="on"<? if( $menuAvailability == "on" ) { echo " checked"; } ?>/><label for="maOn">On</label>
							<input type="radio" name="menuAvailability" id="maOff" value="off"<? if( $menuAvailability == "off" ) { echo " checked"; } ?>/><label for="maOff">Off</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Title</td>
						<td></td>
						<td><input type="text" class="text" name="menuTitle" value="<?=$menuTitle;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Link Type</td>
						<td></td>
						<td>
							<select name="menuLinkType">
								<option value="onsite"<? if( $menuLinkType == "onsite" ) { echo " selected"; } ?>>Onsite</option>
								<option value="offsite"<? if( $menuLinkType == "offsite" ) { echo " selected"; } ?>>Offsite</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Link</td>
						<td></td>
						<td><input type="text" class="text" name="menuLink" value="<?=$menuLink;?>" /></td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Category</td>
						<td></td>
						<td>
							<select name="menuCat">
								<option value="main"<? if( $menuCat == "main" ) { echo " selected"; } ?>>Main Navigation</option>
								<option value="general"<? if( $menuCat == "general" ) { echo " selected"; } ?>>General Menus</option>
								<option value="admin"<? if( $menuCat == "admin" ) { echo " selected"; } ?>>Admin Menus</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Section</td>
						<td></td>
						<td>
							<select name="menuMainSec">
								<optgroup label="Main Navigation">
									<option value=""<? if( $menuMainSec == "" ) { echo " selected"; } ?>>Main Navigation</option>
								</optgroup>
								<optgroup label="General Menus">
									<option value="main"<? if( $menuMainSec == "main" ) { echo " selected"; } ?>>Main</option>
									<option value="personnel"<? if( $menuMainSec == "personnel" ) { echo " selected"; } ?>>Personnel</option>
									<option value="ship"<? if( $menuMainSec == "ship" ) { echo " selected"; } ?>><?=ucfirst( $simmType );?></option>
									<option value="simm"<? if( $menuMainSec == "simm" ) { echo " selected"; } ?>>Simm</option>
								</optgroup>
								<optgroup label="Admin Menus">
									<option value="post"<? if( $menuMainSec == "post" ) { echo " selected"; } ?>>Post</option>
									<option value="manage"<? if( $menuMainSec == "manage" ) { echo " selected"; } ?>>Manage</option>
									<option value="reports"<? if( $menuMainSec == "reports" ) { echo " selected"; } ?>>Reports</option>
									<option value="user"<? if( $menuMainSec == "user" ) { echo " selected"; } ?>>User</option>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Group</td>
						<td></td>
						<td><input type="text" class="text" name="menuGroup" size="3" value="<?=$menuGroup;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Order</td>
						<td></td>
						<td><input type="text" class="text" name="menuOrder" size="3" value="<?=$menuOrder;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Requires Login?</td>
						<td></td>
						<td>
							<input type="radio" id="menuLoginY" name="menuLogin" value="y" <? if( $menuLogin == "y" ) { echo "checked"; } ?>/><label for="menuLoginY">Yes</label>
							<input type="radio" id="menuLoginN" name="menuLogin" value="n" <? if( $menuLogin == "n" ) { echo "checked"; } ?>/><label for="menuLoginN">No</label>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="update" />
							&nbsp;
							<script type="text/javascript">
								document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this menu item?')\" />" );
							</script>
							<noscript>
								<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
							</noscript>
						</td>
					</tr>
				</table>
			</form>
			
			<? } ?>
		<? } if( $sec == "general" ) { ?>
		<br />
		
			<div class="subMenu">
				<ul>
					<li><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>&subsec=main">Main</a></li>
					<li><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>&subsec=personnel">Personnel</a></li>
					<li><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>&subsec=simm">The Simm</a></li>
					<li><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>&subsec=ship">The <?=ucfirst( $simmType );?></a></li>
				</ul>
			</div>
			
			<? if( !$id || isset( $delete ) ) { ?>
			Each section of SMS has its own menu items. Use the sub navigation above to move through the various sections
			and make changes.
			
			<ul class="list-dark">
				<?
				
				while( $menuMain = mysql_fetch_array( $getGeneralResult ) ) {
					extract( $menuMain, EXTR_OVERWRITE );
					
					if( $subsec == $menuMainSec ) {
					
						echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=menuadvanced&sec=" . $sec . "&subsec=" . $subsec . "&id=" . $menuid . "'>" . $menuTitle . "</a></li>";
					
					}
					
				}
				
				?>
			</ul>
			<? } else { ?>
			
				<div class="postDetails fontNormal">
					<b class="fontMedium">General Navigation Menu Items - <?=ucfirst( $subsec );?></b><br /><br />
					<table>
						<tr>
							<td><b>Title</b></td>
							<td><b>Group</b></td>
							<td><b>Order</b></td>
						</tr>
						<?
						
						while( $menuMain = mysql_fetch_array( $getGeneralResult ) ) {
							extract( $menuMain, EXTR_OVERWRITE );
							
							if( $subsec == $menuMainSec ) {
							
								echo "<tr>";
									echo "<td>";
									if( $menuAvailability == "off" ) {
										echo "<b class='red'>OFF</span> &nbsp;";
									}
									echo "<a href='" . $webLocation . "admin.php?page=manage&sub=menuadvanced&sec=" . $sec . "&subsec=" . $subsec . "&id=" . $menuid . "'>" . $menuTitle . "</a></td>";
									echo "<td>" . $menuGroup . "</td>";
									echo "<td>" . $menuOrder . "</td>";
								echo "</tr>";
							
							}
							
						}
						
						?>
					</table>
				</div>
				
				<b class="fontLarge">Edit Menu Item</b><br /><br />
				Offsite links will open in a new window.  If you choose an offsite link, please provide the entire URL, otherwise,
				please only provide the information after the domain (i.e. index.php?page=main). Additional information about
				other menu items in this category are provided on the side.<br /><br />
				
				<b><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>">&laquo; Back to Menu Items</a></b><br /><br />
				
				<?
			
				$getItem = "SELECT * FROM sms_menu_items WHERE menuid = '$id' LIMIT 1";
				$getItemResult = mysql_query( $getItem );
				
				while( $itemFetch = mysql_fetch_array( $getItemResult ) ) {
					extract( $itemFetch, EXTR_OVERWRITE );
				}
			
				?>
			
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>&subsec=<?=$subsec;?>&id=<?=$id;?>">
				<table>
					<tr>
						<td class="tableCellLabel">Menu Item Status</td>
						<td></td>
						<td>
							<input type="radio" name="menuAvailability" id="maOn" value="on"<? if( $menuAvailability == "on" ) { echo " checked"; } ?>/><label for="maOn">On</label>
							<input type="radio" name="menuAvailability" id="maOff" value="off"<? if( $menuAvailability == "off" ) { echo " checked"; } ?>/><label for="maOff">Off</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Title</td>
						<td></td>
						<td><input type="text" class="text" name="menuTitle" value="<?=$menuTitle;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Link Type</td>
						<td></td>
						<td>
							<select name="menuLinkType">
								<option value="onsite"<? if( $menuLinkType == "onsite" ) { echo " selected"; } ?>>Onsite</option>
								<option value="offsite"<? if( $menuLinkType == "offsite" ) { echo " selected"; } ?>>Offsite</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Link</td>
						<td></td>
						<td><input type="text" class="text" name="menuLink" value="<?=$menuLink;?>" /></td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Category</td>
						<td></td>
						<td>
							<select name="menuCat">
								<option value="main"<? if( $menuCat == "main" ) { echo " selected"; } ?>>Main Navigation</option>
								<option value="general"<? if( $menuCat == "general" ) { echo " selected"; } ?>>General Menus</option>
								<option value="admin"<? if( $menuCat == "admin" ) { echo " selected"; } ?>>Admin Menus</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Section</td>
						<td></td>
						<td>
							<select name="menuMainSec">
								<optgroup label="Main Navigation">
									<option value=""<? if( $menuMainSec == "" ) { echo " selected"; } ?>>Main Navigation</option>
								</optgroup>
								<optgroup label="General Menus">
									<option value="main"<? if( $menuMainSec == "main" ) { echo " selected"; } ?>>Main</option>
									<option value="personnel"<? if( $menuMainSec == "personnel" ) { echo " selected"; } ?>>Personnel</option>
									<option value="ship"<? if( $menuMainSec == "ship" ) { echo " selected"; } ?>><?=ucfirst( $simmType );?></option>
									<option value="simm"<? if( $menuMainSec == "simm" ) { echo " selected"; } ?>>Simm</option>
								</optgroup>
								<optgroup label="Admin Menus">
									<option value="post"<? if( $menuMainSec == "post" ) { echo " selected"; } ?>>Post</option>
									<option value="manage"<? if( $menuMainSec == "manage" ) { echo " selected"; } ?>>Manage</option>
									<option value="reports"<? if( $menuMainSec == "reports" ) { echo " selected"; } ?>>Reports</option>
									<option value="user"<? if( $menuMainSec == "user" ) { echo " selected"; } ?>>User</option>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Group</td>
						<td></td>
						<td><input type="text" class="text" name="menuGroup" size="3" value="<?=$menuGroup;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Order</td>
						<td></td>
						<td><input type="text" class="text" name="menuOrder" size="3" value="<?=$menuOrder;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Requires Login?</td>
						<td></td>
						<td>
							<input type="radio" id="menuLoginY" name="menuLogin" value="y" <? if( $menuLogin == "y" ) { echo "checked"; } ?>/><label for="menuLoginY">Yes</label>
							<input type="radio" id="menuLoginN" name="menuLogin" value="n" <? if( $menuLogin == "n" ) { echo "checked"; } ?>/><label for="menuLoginN">No</label>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="update" />
							&nbsp;
							<script type="text/javascript">
								document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this menu item?')\" />" );
							</script>
							<noscript>
								<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
							</noscript>
						</td>
					</tr>
				</table>
			</form>
			
			<? } ?>
		<? } if( $sec == "admin" ) { ?>
		<br />
			
			<div class="subMenu">
				<ul>
					<li><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>&subsec=post">Post</a></li>
					<li><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>&subsec=manage">Manage</a></li>
					<li><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>&subsec=reports">Reports</a></li>
					<li><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>&subsec=user">User</a></li>
				</ul>
			</div>
			
			<? if( !$id || isset( $delete ) ) { ?>
			The administration control panel of SMS is broken up in four distinct sections. Use the sub-navigation
			menu above to move between the sections and make changes.
			
			<?
			
			switch( $subsec ) {
				case "user":
					echo "<b class='yellow'>There are 2 user account links and 3 biography links to account for the various access levels associated with those features. Do not delete any of those links or those features, at certain access levels, will cease to function correctly!</b>";
					break;
				case "manage":
					echo "<b class='yellow'>There are 2 All NPC links to account for the various access levels associated with that feature. Do not delete either of those links or the feature, at certain access levels, will cease to function correctly!</b>";
					break;
			}
			
			?>
			
			<ul class="list-dark">
				<?
				
				while( $menuAdmin = mysql_fetch_array( $getAdminResult ) ) {
					extract( $menuAdmin, EXTR_OVERWRITE );
					
					if( $subsec == $menuMainSec ) {
					
						echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=menuadvanced&sec=" . $sec . "&subsec=" . $subsec . "&id=" . $menuid . "'>" . $menuTitle . "</a></li>";
					
					}
					
				}
				
				?>
			</ul>
			<? } else { ?>
			
				<div class="postDetails fontNormal">
					<b class="fontMedium">Admin Navigation Menu Items - <?=ucfirst( $subsec ); ?></b><br /><br />
					<table>
						<tr>
							<td><b>Title</b></td>
							<td><b>Group</b></td>
							<td><b>Order</b></td>
						</tr>
						<?
						
						while( $menuAdmin = mysql_fetch_array( $getAdminResult ) ) {
							extract( $menuAdmin, EXTR_OVERWRITE );
							
							if( $subsec == $menuMainSec ) {
							
								echo "<tr>";
									echo "<td>";
									if( $menuAvailability == "off" ) {
										echo "<b class='red'>OFF</span> &nbsp;";
									}
									echo "<a href='" . $webLocation . "admin.php?page=manage&sub=menuadvanced&sec=" . $sec . "&subsec=" . $subsec . "&id=" . $menuid . "'>" . $menuTitle . "</a></td>";
									echo "<td>" . $menuGroup . "</td>";
									echo "<td>" . $menuOrder . "</td>";
								echo "</tr>";
							
							}
							
						}
						
						?>
					</table>
				</div>
				
				<b class="fontLarge">Edit Menu Item</b><br /><br />
				Offsite links will open in a new window.  If you choose an offsite link, please provide the entire URL, otherwise,
				please only provide the information after the domain (i.e. index.php?page=manage&amp;sub=globals). Additional
				information about other menu items in this category are provided on the side. <b class="red">Editing the menu
				item access code will cause the menu item to be unusable!</b><br /><br />
				
				<b><a href="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>">&laquo; Back to Menu Items</a></b><br /><br />
				
				<?
			
				$getItem = "SELECT * FROM sms_menu_items WHERE menuid = '$id' LIMIT 1";
				$getItemResult = mysql_query( $getItem );
				
				while( $itemFetch = mysql_fetch_array( $getItemResult ) ) {
					extract( $itemFetch, EXTR_OVERWRITE );
				}
			
				?>
			
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=menuadvanced&sec=<?=$sec;?>&subsec=<?=$subsec;?>&id=<?=$id;?>">
				<table>
					<tr>
						<td class="tableCellLabel">Menu Item Status</td>
						<td></td>
						<td>
							<input type="radio" name="menuAvailability" id="maOn" value="on"<? if( $menuAvailability == "on" ) { echo " checked"; } ?>/><label for="maOn">On</label>
							<input type="radio" name="menuAvailability" id="maOff" value="off"<? if( $menuAvailability == "off" ) { echo " checked"; } ?>/><label for="maOff">Off</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Title</td>
						<td></td>
						<td><input type="text" class="text" name="menuTitle" value="<?=$menuTitle;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Link Type</td>
						<td></td>
						<td>
							<select name="menuLinkType">
								<option value="onsite"<? if( $menuLinkType == "onsite" ) { echo " selected"; } ?>>Onsite</option>
								<option value="offsite"<? if( $menuLinkType == "offsite" ) { echo " selected"; } ?>>Offsite</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Link</td>
						<td></td>
						<td><input type="text" class="text" name="menuLink" value="<?=$menuLink;?>" /></td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Category</td>
						<td></td>
						<td>
							<select name="menuCat">
								<option value="main"<? if( $menuCat == "main" ) { echo " selected"; } ?>>Main Navigation</option>
								<option value="general"<? if( $menuCat == "general" ) { echo " selected"; } ?>>General Menus</option>
								<option value="admin"<? if( $menuCat == "admin" ) { echo " selected"; } ?>>Admin Menus</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Section</td>
						<td></td>
						<td>
							<select name="menuMainSec">
								<optgroup label="Main Navigation">
									<option value=""<? if( $menuMainSec == "" ) { echo " selected"; } ?>>Main Navigation</option>
								</optgroup>
								<optgroup label="General Menus">
									<option value="main"<? if( $menuMainSec == "main" ) { echo " selected"; } ?>>Main</option>
									<option value="personnel"<? if( $menuMainSec == "personnel" ) { echo " selected"; } ?>>Personnel</option>
									<option value="ship"<? if( $menuMainSec == "ship" ) { echo " selected"; } ?>><?=ucfirst( $simmType );?></option>
									<option value="simm"<? if( $menuMainSec == "simm" ) { echo " selected"; } ?>>Simm</option>
								</optgroup>
								<optgroup label="Admin Menus">
									<option value="post"<? if( $menuMainSec == "post" ) { echo " selected"; } ?>>Post</option>
									<option value="manage"<? if( $menuMainSec == "manage" ) { echo " selected"; } ?>>Manage</option>
									<option value="reports"<? if( $menuMainSec == "reports" ) { echo " selected"; } ?>>Reports</option>
									<option value="user"<? if( $menuMainSec == "user" ) { echo " selected"; } ?>>User</option>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Access Code</td>
						<td></td>
						<td><input type="text" class="text" name="menuAccess" value="<?=$menuAccess;?>" /></td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Group</td>
						<td></td>
						<td><input type="text" class="text" name="menuGroup" size="3" value="<?=$menuGroup;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Menu Item Order</td>
						<td></td>
						<td><input type="text" class="text" name="menuOrder" size="3" value="<?=$menuOrder;?>" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Requires Login?</td>
						<td></td>
						<td>
							<input type="radio" id="menuLoginY" name="menuLogin" value="y" <? if( $menuLogin == "y" ) { echo "checked"; } ?>/><label for="menuLoginY">Yes</label>
							<input type="radio" id="menuLoginN" name="menuLogin" value="n" <? if( $menuLogin == "n" ) { echo "checked"; } ?>/><label for="menuLoginN">No</label>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="update" />
							&nbsp;
							<script type="text/javascript">
								document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this menu item?')\" />" );
							</script>
							<noscript>
								<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
							</noscript>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="250"></td>
					</tr>
				</table>
			</form>
			
			<? } ?>
		<? } ?>
		</div>
	</div>

<? } else { errorMessage( "menu management" ); } ?>