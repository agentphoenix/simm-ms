<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/user/site.php
Purpose: Page that allows a user to various site options

System Version: 2.6.0
Last Modified: 2007-12-26 2233 EST
**/

/* access check */
if( in_array( "u_options", $sessionAccess ) ) {
	
	$sec = $_GET['sec'];
	
	if( !isset( $sec ) ) {
		$sec = 1;
	}
	
	if( !is_numeric( $sec ) ) {
		$sec = 1;
	}

?>
	
	<script type="text/javascript">
		$(document).ready(function(){
			$('#container-1 > ul').tabs(<?php echo $sec; ?>);
		});
	</script>
	
<?php

	/* set the page variables */
	$pageClass = "admin";
	$subMenuClass = "user";
	$options = $_POST['options_x'];
	$actionSkin = $_POST['action_skin_x'];
	$actionRank = $_POST['action_rank_x'];
	
	if( isset( $actionSkin ) ) {
		/* define the POST variables */
		$changeskin = $_POST['changeskin'];
		
		/* do the update query */
		$query = "UPDATE sms_crew SET displaySkin = '$changeskin' WHERE crewid = '$sessionCrewid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
		
		/* set a new session variable */
		$_SESSION['sessionDisplaySkin'] = $changeskin;
		$sessionDisplaySkin = $_SESSION['sessionDisplaySkin'];
		
		/* set the type for the query check */
		$type = "skin";
		
	} if( isset( $actionRank ) ) {
		/* define the POST variables */
		$rankSet = $_POST['rankSet'];
		
		/* do the update query */
		$query = "UPDATE sms_crew SET displayRank = '$rankSet' WHERE crewid = '$sessionCrewid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
		
		/* set a new session variable */
		$_SESSION['sessionDisplayRank'] = $rankSet;
		$sessionDisplayRank = $_SESSION['sessionDisplayRank'];
		
		/* set the type for the query check */
		$type = "rank set";
		
	} if( isset( $options ) ) {
	
		/* set the variables */
		foreach( $_POST as $k => $v )
		{
			$$k = $v;
		}
		
		/* run the update */
		$query = "UPDATE sms_crew SET cpShowPosts = '$cpShowPosts', cpShowLogs = '$cpShowLogs', ";
		$query.= "cpShowNews = '$cpShowNews', cpShowPostsNum = '$cpShowPostsNum', ";
		$query.= "cpShowLogsNum = '$cpShowLogsNum', cpShowNewsNum = '$cpShowNewsNum', menu1 = '$menu1', ";
		$query.= "menu2 = '$menu2', menu3 = '$menu3', menu4 = '$menu4', menu5 = '$menu5', menu6 = '$menu6', ";
		$query.= "menu7 = '$menu7', menu8 = '$menu8', menu9 = '$menu9', menu10 = '$menu10' WHERE ";
		$query.= "crewid = '$sessionCrewid' LIMIT 1";
		$result = mysql_query( $query );
	
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
	
		$type = "site options";
	
	}
	
	/* query the database for the general items */
	$query1 = "SELECT * FROM sms_menu_items WHERE menuAvailability = 'on' AND menuCat = 'general' ";
	$query1.= "ORDER BY menuMainSec, menuGroup, menuOrder ASC";
	$result1 = mysql_query( $query1 );
	
	/* query the database for the admin items */
	$query2 = "SELECT * FROM sms_menu_items WHERE menuAvailability = 'on' AND menuCat = 'admin' ";
	$query2.= "ORDER BY menuMainSec, menuGroup, menuOrder ASC";
	$result2 = mysql_query( $query2 );
	
	/* loop through the general items and put them into a 2d array */
	while( $fetch1 = mysql_fetch_assoc( $result1 ) ) {
		extract( $fetch1, EXTR_OVERWRITE );
		
		$array1[] = array(
			$fetch1['menuid'],
			$fetch1['menuTitle'],
			$fetch1['menuMainSec']
		);
		
	}
	
	/* loop through the admin items and them into a 2d array */
	while( $fetch2 = mysql_fetch_assoc( $result2 ) ) {
		extract( $fetch2, EXTR_OVERWRITE );
		
		$array2[] = array(
			$fetch2['menuid'],
			$fetch2['menuTitle'],
			$fetch2['menuAccess'],
			$fetch2['menuMainSec']
		);
		
	}
	
	/* rip through the array and remove any items that shouldn't be there */
	foreach( $array2 as $a => $b ) {
		if( !in_array( $b[2], $sessionAccess ) ) {
			unset( $array2[$a] );
		}
	}
	
?>
	
	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( $type, "update" );
			$check->display();
		}
	
		?>
		
		<span class="fontTitle">Site Options</span><br /><br />
		
		SMS gives users more control of what they see when they're logged in now. From this page,
		you can set the skin you use when you're logged in, the rank set you see as well as control
		panel options and personalized menu items.<br />
	
		<?
	
		$getUserInfo = "SELECT cpShowPosts, cpShowLogs, cpShowNews, cpShowPostsNum, ";
		$getUserInfo.= "cpShowLogsNum, cpShowNewsNum, menu1, menu2, menu3, menu4, menu5, ";
		$getUserInfo.= "menu6, menu7, menu8, menu9, menu10 FROM sms_crew WHERE crewid = '$sessionCrewid' LIMIT 1";
		$getUserInfoResult = mysql_query( $getUserInfo );
	
		while( $userPrefs = mysql_fetch_assoc( $getUserInfoResult ) ) {
			extract( $userPrefs, EXTR_OVERWRITE );
		}
	
		?>
		
		<div id="container-1">
			<ul>
				<li><a href="#one"><span>Control Panel</span></a></li>
				<li><a href="#two"><span>Personalized Menu</span></a></li>
				<li><a href="#three"><span>Rank Set</span></a></li>
				<li><a href="#four"><span>Site Skin</span></a></li>
			</ul>
	
			<div id="one" class="ui-tabs-container ui-tabs-hide">
				<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=site&sec=1">
				<table>
					<tr>
						<td colspan="3" class="fontMedium"><b>Show the following in the Control Panel?</b></td>
					</tr>
					<tr>
						<td colspan="3" height="1"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Latest Mission Entries</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="showPostsY" name="cpShowPosts" value="y" <? if( $cpShowPosts == "y" ) { echo "checked"; } ?>/> <label for="showPostsY">Yes</label>
							<input type="radio" id="showPostsN" name="cpShowPosts" value="n" <? if( $cpShowPosts == "n" ) { echo "checked"; } ?>/> <label for="showPostsN">No</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Latest Personal Logs</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="showLogsY" name="cpShowLogs" value="y" <? if( $cpShowLogs == "y" ) { echo "checked"; } ?>/> <label for="showLogsY">Yes</label>
							<input type="radio" id="showLogsN" name="cpShowLogs" value="n" <? if( $cpShowLogs == "n" ) { echo "checked"; } ?>/> <label for="showLogsN">No</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Latest News Items</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="showNewsY" name="cpShowNews" value="y" <? if( $cpShowNews == "y" ) { echo "checked"; } ?>/> <label for="showNewsY">Yes</label>
							<input type="radio" id="showNewsN" name="cpShowNews" value="n" <? if( $cpShowNews == "n" ) { echo "checked"; } ?>/> <label for="showNewsN">No</label>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td colspan="3" class="fontMedium">
							<b>How many of the following should be shown in the Control Panel?</b>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="1"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Latest Mission Entries</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" class="order" name="cpShowPostsNum" value="<?=$cpShowPostsNum;?>" maxlength="3" />
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Latest Personal Logs</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" class="order" name="cpShowLogsNum" value="<?=$cpShowLogsNum;?>" maxlength="3" />
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Latest News Items</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" class="order" name="cpShowNewsNum" value="<?=$cpShowNewsNum;?>" maxlength="3" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="options" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=site&sec=3">
				<table>
					<tr>
						<td colspan="3">Neptune description and instructions here</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<?php

					for( $i=1; $i<11; $i++ )
					{
						$menu = "menu" . $i;

						echo "<tr>";
							echo "<td class='tableCellLabel'>Menu Item #" . $i . "</td>";
							echo "<td>&nbsp;</td>";
							echo "<td>";
								echo "<select name='" . $menu . "'>";
									echo "<option value='0'>No Selection</option>";

									echo "<optgroup label='General'>";
										foreach( $array1 as $key1 => $value1 )
										{
											if( $$menu == $value1[0] ) {
												$selected = " selected";
											} else {
												$selected = "";
											}

											echo "<option value='" . $value1[0] . "'" . $selected . ">";
												echo ucwords( $value1[2] ) . " - " . $value1[1];
											echo "</option>";
										}
									echo "</optgroup>";

									echo "<optgroup label='Admin'>";
										foreach( $array2 as $key2 => $value2 )
										{
											if( $$menu == $value2[0] ) {
												$selected = " selected";
											} else {
												$selected = "";
											}

											echo "<option value='" . $value2[0] . "'" . $selected . ">";
												echo ucwords( $value2[3] ) . " - " . $value2[1];
											echo "</option>";
										}
									echo "</optgroup>";
								echo "</select>";
							echo "</td>";
						echo "</tr>";
					}

					?>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="options" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=site&sec=3">
				<table>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
							<?php
		
							/* break the string into an array */
							$rankArray = explode( ",", $allowedRanks );
		
							/* loop through the array */
							foreach( $rankArray as $key => $value ) {
		
							?>
							
								<input type="radio" id="<?=$value;?>" name="rankSet" value="<?=$value;?>"<? if( $sessionDisplayRank == trim( $value ) ) { echo " checked"; } ?> />
								<label for="<?=$value;?>"><img src="<?=$webLocation;?>images/ranks/<?=trim( $value );?>/preview.png" alt="" border="0" /></label><br />
		
							<?php } ?>
							
						</td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_rank" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
			<div id="four" class="ui-tabs-container ui-tabs-hide">
				<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=site&sec=4">
				<table>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
							<?php
		
							/* break the string into an array */
							$skinArray = explode( ",", $allowedSkins );
		
							/* loop through the array */
							foreach( $skinArray as $key => $value ) {
		
							?>
							
								<input type="radio" id="<?=$value;?>" name="changeskin" value="<?=$value;?>"<? if( $sessionDisplaySkin == trim( $value ) ) { echo " checked"; } ?> />
								<label for="<?=$value;?>"><img src="<?=$webLocation;?>skins/<?=trim( $value );?>/preview.jpg" alt="" border="0" style="border: 2px solid #efefef" class="image" /></label><br /><br />
		
							<?php } ?>
							
						</td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_skin" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
				
				
				
				<? /* ?>
				<h3>Current Skin</h3>
				<h4><?=ucwords( $displaySkin );?></h4>
				<p>
					<img src="<?=$webLocation;?>skins/<?=$sessionDisplaySkin;?>/preview.jpg" alt="<?=$sessionDisplaySkin;?>" style="border: 2px solid #efefef;" />
				</p>

				<h3>Other Available Skins</h3>
				<?

					foreach( $allowedSkinsArray as $key => $value ) {

						if( $value != $sessionDisplaySkin ) {
							echo "<h4>" . ucwords( $value ) . "</h4>";
							echo "<a href='" . $webLocation . "admin.php?page=user&sub=site&action=skin&changeskin=" . trim( $value ) . "'>";
							echo "<img src='" . $webLocation . "skins/" . trim( $value ) . "/preview.jpg' alt='" . $value . "' style='border: 2px solid #efefef;' class='image' />";
							echo "</a>";
						} else {
						}
					}
				*/
				?>
			
			</div>
		</div>
	
	</div>
	
<? } else { errorMessage( "site options" ); } ?>