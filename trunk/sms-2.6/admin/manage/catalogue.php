<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/manage/globals.php
Purpose: Page that moderates the site globals

System Version: 2.5.0
Last Modified: 2007-05-05 2143 EST
**/

/* access check */
if( in_array( "m_catalogue", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$update = $_POST['action_update_x'];
	$create = $_POST['action_create_x'];
	$delete = $_POST['action_delete_x'];
	$sec = $_GET['sec'];
	
	if( !$sec ) {
		$sec = "ranks";
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( "system catalogue page" );
		exit();
	} else {
		/* set the GET variable */
		$id = $_GET['id'];
	}
	
	if( $update ) {
	
		if( $sec == "ranks" ) {
			
			/* define the POST variables */
			$rankcatName = addslashes( $_POST['rankcatName'] );
			$rankcatStatus = $_POST['rankcatStatus'];
			$rankcatOrder = $_POST['rankcatOrder'];
			$rankcatPreview = $_POST['rankcatPreview'];
			$rankcatBlank = $_POST['rankcatBlank'];
			$rankcatLocation = $_POST['rankcatLocation'];
	
			/* do the update query */
			$query = "UPDATE sms_catalogue_ranks SET rankcatName = '$rankcatName', ";
			$query.= "rankcatStatus = '$rankcatStatus', rankCatOrder = '$rankcatOrder', ";
			$query.= "rankcatPreview = '$rankcatPreview', rankcatBlank = '$rankcatBlank', ";
			$query.= "rankcatLocation = '$rankcatLocation' WHERE rankcatid = '$id' LIMIT 1";
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_catalogue_ranks" );
			
			$type = "rank catalogue item";
			
		} if( $sec == "skins" ) {
			
			/* define the POST variables */
			$fleet = addslashes( $_POST['fleet'] );
			$fleetURL = $_POST['fleetURL'];
			$tfMember = $_POST['tfMember'];
			$tfName = addslashes( $_POST['tfName'] );
			$tfURL = $_POST['tfURL'];
			$tgMember = $_POST['tgMember'];
			$tgName = addslashes( $_POST['tgName'] );
			$tgURL = $_POST['tgURL'];
	
			/* do the update query */
			$updateGlobals = "UPDATE sms_globals SET ";
			$updateGlobals.= "fleet = '$fleet', fleetURL = '$fleetURL', ";
			$updateGlobals.= "tfMember = '$tfMember', tfName = '$tfName', tfURL = '$tfURL', ";
			$updateGlobals.= "tgMember = '$tgMember', tgName = '$tgName', tgURL = '$tgURL' ";
			$updateGlobals.= "WHERE globalid = '1' LIMIT 1";
			$result = mysql_query( $updateGlobals );
			
			/* optimize the table */
			optimizeSQLTable( "sms_catalogue_skins" );
			
			$type = "skin catalogue item";
			
		}
		
		$action = "update";

	}
	
	/* query for the data */
	$ranks = "SELECT * FROM sms_catalogue_ranks ORDER BY rankcatOrder ASC";
	$ranksResult = mysql_query( $ranks );
	
	$skins = "SELECT * FROM sms_catalogue_skins ORDER BY skincatOrder ASC";
	$skinsResult = mysql_query( $skins );

?>

	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( $type, $action );
			$check->display();
		}
		
		?>
	
		<span class="fontTitle">System Catalogues</span>
	
		<div id="subnav">
			<ul>
				<li <? if( $sec == "ranks" ) { echo "id='current'"; } ?>><a href="<?=$webLocation;?>admin.php?page=manage&sub=catalogue&sec=ranks">Rank Sets</a></li>
				<li <? if( $sec == "skins" ) { echo "id='current'"; } ?>><a href="<?=$webLocation;?>admin.php?page=manage&sub=catalogue&sec=skins">Skins</a></li>
			</ul>
		</div>
	
		<div class="tabcontainer">
	
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=catalogue&sec=<?=$sec;?>&id=<?=$id;?>">
	
			<? if( $sec == "ranks" ) { ?>
			<br />
			<b class="fontLarge">Ranks Set Catalogue</b><br /><br />
			
			<? if( !$id ) { ?>
			<table>
				<?
				
				$rowCount = "0";
				$color1 = "rowColor1";
				$color2 = "rowColor2";
				
				while( $rankFetch = mysql_fetch_array( $ranksResult ) ) {
					extract( $rankFetch, EXTR_OVERWRITE );
					
					$rowColor = ($rowCount % 2) ? $color1 : $color2;
				
				?>
				<tr class="<?=$rowColor;?> fontMedium">
					<td width="150" class="<?=$color2;?>">
						<img src="<?=$webLocation;?>images/ranks/<?=$rankcatLocation;?>/<?=$rankcatPreview;?>" />
					</td>
					<td><? printText( $rankcatName ); ?></td>
					<td><?=ucfirst( $rankcatStatus );?></td>
					<td><a href="<?=$webLocation;?>admin.php?page=manage&sub=catalogue&sec=ranks&id=<?=$rankcatid;?>" class="edit">Edit</a></td>
				</tr>
				<? $rowCount++; } ?>
			</table>
			<?
			
			} else {
			
				$oneRank = "SELECT * FROM sms_catalogue_ranks WHERE rankcatid = '$id' LIMIT 1";
				$oneRankResult = mysql_query( $oneRank );
				
				while( $rankFetch = mysql_fetch_array( $oneRankResult ) ) {
					extract( $rankFetch, EXTR_OVERWRITE );
				}
			
			?>
				<img src="<?=$webLocation;?>images/ranks/<?=$rankcatLocation;?>/<?=$rankcatPreview;?>" />
				<img src="<?=$webLocation;?>images/ranks/<?=$rankcatLocation;?>/<?=$rankcatBlank;?>" />
				<br />
				<table>
					<tr>
						<td class="tableCellLabel">Name</td>
						<td></td>
						<td><input type="text" name="rankcatName" value="<?=$rankcatName;?>" class="text" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Order</td>
						<td></td>
						<td><input type="text" name="rankcatOrder" value="<?=$rankcatOrder;?>" size="3" class="text" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Status</td>
						<td></td>
						<td>
							<select name="rankcatStatus">
								<option value="active"<? if( $rankcatStatus == "active" ) { echo " selected"; } ?>>Active</option>
								<option value="development"<? if( $rankcatStatus == "development" ) { echo " selected"; } ?>>In Development</option>
								<option value="inactive"<? if( $rankcatStatus == "inactive" ) { echo " selected"; } ?>>Inactive</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Location</td>
						<td></td>
						<td>
							<span class="fontSmall">images/ranks/<input type="text" name="rankcatLocation" value="<?=$rankcatLocation;?>" class="text" />
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Preview Image</td>
						<td></td>
						<td>
							<span class="fontSmall">images/ranks/<?=strtolower( $rankcatLocation );?>/<input type="text" name="rankcatPreview" value="<?=$rankcatPreview;?>" class="text" />
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Blank Image</td>
						<td></td>
						<td>
							<span class="fontSmall">images/ranks/<?=strtolower( $rankcatLocation );?>/<input type="text" name="rankcatBlank" value="<?=$rankcatBlank;?>" class="text" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update" class="button" value="Update" />
						</td>
					</tr>
				</table>
			<? } ?>
	
			<? } if( $sec == "skins" ) { ?>
			<table>	
				<tr>
					<td colspan="3" height="15"></td>
				</tr>
				<tr>
					<td colspan="3" class="fontLarge"><b>Skins Catalogue</b></td>
				</tr>
			</table>
			<? } ?>
			
		</form>
		
		</div> <!-- close the tab container -->
	</div>
	
<? } else { errorMessage( "catalogue management" ); } ?>