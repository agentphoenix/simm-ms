<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/positions.php
Purpose: Page that moderates the positions

System Version: 2.5.0
Last Modified: 2007-07-10 1007 EST
**/

/* access check */
if( in_array( "m_positions", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$actionCreate = $_POST['action_create_x'];
	$actionUpdate = $_POST['action_update_x'];
	$actionDelete = $_POST['action_delete_x'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['dept'] ) && preg_match( "/^\d+$/", $_GET['dept'], $matches ) == 0 ) {
		errorMessageIllegal( "positions management page" );
		exit();
	} else {
		/* set the GET variable */
		$dept = $_GET['dept'];
	}
	
	/* if there is no department in the URL, set it to 1 */
	if( !$dept ) {
		$dept = "1";
	}

	/* define the POST variables */
	$positionid = $_POST['positionid'];
	$positionName = addslashes( $_POST['positionName'] );
	$positionDept = $_POST['positionDept'];
	$positionOrder = $_POST['positionOrder'];
	$positionDesc = addslashes( $_POST['positionDesc'] );
	$positionOpen = $_POST['positionOpen'];
	$positionType = $_POST['positionType'];
	$positionDisplay = $_POST['positionDisplay'];
	
	/* if the POST action is update */
	if( $actionUpdate ) {
		
		/* do the update query */
		$query = "UPDATE sms_positions SET ";
		$query.= "positionName = '$positionName', positionDept = '$positionDept', ";
		$query.= "positionOrder = '$positionOrder', positionDesc = '$positionDesc', ";
		$query.= "positionOpen = '$positionOpen', positionType = '$positionType', ";
		$query.= "positionDisplay = '$positionDisplay' WHERE positionid = '$positionid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_positions" );
		
		$action = "update";
	
	/* if the POST action is create */
	} elseif( $actionCreate ) {
		
		/* do the create query */
		$query = "INSERT INTO sms_positions ( positionid, positionOrder, positionName, positionDesc, positionDept, positionType, positionOpen, positionDisplay ) ";
		$query.= "VALUES ( '', '$positionOrder', '$positionName', '$positionDesc', '$positionDept', '$positionType', '$positionOpen', '$positionDisplay' )";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_positions" );
		
		$action = "create";
	
	/* if the POST action is delete */
	} elseif( $actionDelete ) {
		
		/* do the delete query */
		$query = "DELETE FROM sms_positions WHERE positionid = '$positionid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_positions" );
		
		$action = "delete";
	
	}
	
	/* strip the slashes */
	$positionName = stripslashes( $positionName );
	$positionDesc = stripslashes( $positionDesc );
	
	/* grab the departments for the menu */
	$getDepts = "SELECT deptid, deptName FROM sms_departments ORDER BY deptOrder ASC";
	$getDeptsResult = mysql_query( $getDepts );
	
	/* count the departments */
	$countDepts = mysql_num_rows( $getDeptsResult );
	$countDeptsFinal = $countDepts - 1;
	
?>

	<div class="body">
		
		<div align="center">
			<span class="fontSmall">Click on the department name to view and edit the positions</span><br />
			<b>
			<?
			
			/* loop through the departments */
			while( $deptFetch = mysql_fetch_array( $getDeptsResult ) ) {
				extract( $deptFetch, EXTR_OVERWRITE );
				
				/*
					create a multi-dimensional array of the data
					
					[x] => Array
					[x][deptid] => 1
					[x][deptName] => Command
				*/
				$depts[] = array( "deptid" => $deptFetch[0], "deptName" => $deptFetch[1] );
				
			}
			
			/* loop through the array */
			foreach( $depts as $key => $value ) {
			
				echo "<a href='" . $webLocation . "admin.php?page=manage&sub=positions&dept=" . $value['deptid'] . "'>";
				
				/*
					if it's the last element of the array, just close the HREF
					otherwise, put a middot between the array values
				*/
				if( $key >= $countDeptsFinal ) {
					echo $value['deptName'] . "</a>";
				} else {
					echo $value['deptName'] . "</a> &nbsp; &middot; &nbsp; ";
				}
			
			}
			
			?>
			</b>
		</div><br />
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "position", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Create New Position</span>
	
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=positions&dept=<?=$dept;?>">
		<table cellpadding="0" cellspacing="3">
			<tr>
				<td colspan="3">
					<span class="fontNormal"><b>Position</b></span><br />
					<input type="text" class="name" name="positionName" />
				</td>
				<td width="5" rowspan="3" align="center" valign="top">&nbsp;</td>
				<td width="80%" rowspan="3" align="center" valign="top">
					<span class="fontNormal"><b>Description</b></span><br />
					<textarea name="positionDesc" rows="5" class="desc"></textarea>
					<br />
					<input type="hidden" name="position" value="<?=$positionid;?>" />
                    <input type="image" src="<?=path_userskin;?>buttons/create.png" class="button" name="action_create" value="Create" />
				</td>
			</tr>
			<tr>
				<td>
					<span class="fontNormal"><b>Order</b></span><br />
					<input type="text" class="order" name="positionOrder" maxlength="3" />
				</td>
				<td>
					<span class="fontNormal"><b>Type</b></span><br />
					<select name="positionType">
						<option value="senior">Senior Staff</option>
						<option value="crew">Crew</option>
					</select>
				</td>
				<td>
					<span class="fontNormal"><b>Display?</b></span><br />
					<select name="positionDisplay">
						<option value="y">Yes</option>
						<option value="n">No</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<span class="fontNormal"><b>Open</b></span><br />
					<input type="text" class="order" maxlength="3" name="positionOpen" />
				</td>
				<td colspan="2">
					<span class="fontNormal"><b>Department</b></span><br />
					<select name="positionDept">
					<?
					
					$getDepts = "SELECT deptid, deptName, deptColor FROM sms_departments ";
					$getDepts.= "WHERE deptDisplay = 'y' ORDER BY deptOrder ASC";
					$getDeptsResult = mysql_query( $getDepts );
		
					while( $deptFetch = mysql_fetch_array( $getDeptsResult ) ) {
						extract( $deptFetch, EXTR_OVERWRITE );
					
					?>
					
						<option value="<?=$deptid;?>" style="color:#<?=$deptColor;?>;" <? if( $dept == $deptid ) { echo "selected"; } ?>><?=$deptName;?></option>
						
					<? } ?>
					</select>
				</td>
			</tr>
		</table>
		</form>
		<br /><br />
		
		<?
		
		$fetchName = "SELECT deptName FROM sms_departments WHERE deptid = '$dept' LIMIT 1";
		$fetchResult = mysql_query( $fetchName );
		$department = mysql_fetch_assoc( $fetchResult );
		
		?>
		
		<span class="fontTitle">Manage Existing <?=$department['deptName'];?> Positions</span>
		
		<? if( $dept == "1" ) { ?>
		<br /><br />
		<span class="yellow">
		<b>Please Note:</b> Several functions that control emails in SMS use position ids 1 and 2 for the
		Commanding Officer and Executive Officer respectively. Because of this, you cannot delete the first
		two positions. If you change either of these positions to have different id numbers, some of the 
		email functions will not work properly.<br /><br />
		</span>
		<? } ?>
	
		<table cellpadding="0" cellspacing="3">
			<?
			
			$getPositions = "SELECT * FROM sms_positions WHERE positionDept = '$dept' ORDER BY positionOrder ASC";
			$getPositionsResult = mysql_query( $getPositions );
		
			while( $positionFetch = mysql_fetch_assoc( $getPositionsResult ) ) {
				extract( $positionFetch, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=positions&dept=<?=$dept;?>">
			<tr>
				<td colspan="3">
					<span class="fontNormal"><b>Position</b></span><br />
					<input type="text" class="name" name="positionName" value="<?=stripslashes( $positionName );?>" />
				</td>
				<td width="5" rowspan="3" align="center" valign="top">&nbsp;</td>
				<td width="80%" rowspan="3" align="center" valign="top">
					<span class="fontNormal"><b>Description</b></span><br />
					<textarea name="positionDesc" rows="5" class="desc"><?=stripslashes( $positionDesc );?></textarea>
					<br />
					
					<? if( $positionid == "1" || $positionid == "2" ) { } else { ?>
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this position?')\" />" );
					</script>
                    <noscript>
                    	<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
                    </noscript>
                    &nbsp;&nbsp;
                    <? } ?>
                    <input type="hidden" name="positionid" value="<?=$positionid;?>" />
                    <input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" />
				</td>
			</tr>
			<tr>
				<td>
					<span class="fontNormal"><b>Order</b></span><br />
					<input type="text" class="order" name="positionOrder" maxlength="3" value="<?=$positionOrder;?>" />
				</td>
				<td>
					<span class="fontNormal"><b>Type</b></span><br />
					<select name="positionType">
						<option value="crew"<? if( $positionType == "crew" ) { echo " selected"; } ?>>Crew</option>
						<option value="senior"<? if( $positionType == "senior" ) { echo " selected"; } ?>>Senior Staff</option>
					</select>
				</td>
				<td>
					<span class="fontNormal"><b>Display?</b></span><br />
					<select name="positionDisplay">
						<option value="y" <? if( $positionDisplay == "y" ) { echo " selected"; } ?>>Yes</option>
						<option value="n" <? if( $positionDisplay == "n" ) { echo " selected"; } ?>>No</option>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<span class="fontNormal"><b>Open</b></span><br />
					<input type="text" class="order" maxlength="3" name="positionOpen" value="<?=$positionOpen;?>" />
				</td>
				<td colspan="2" valign="top"><span class="fontNormal"><b>Department</b></span><br />
					<select name="positionDept">
                    <?
					
					$getDepts = "SELECT deptid, deptName, deptColor FROM sms_departments ";
					$getDepts.= "WHERE deptDisplay = 'y' ORDER BY deptOrder ASC";
					$getDeptsResult = mysql_query( $getDepts );
		
					while( $deptFetch = mysql_fetch_array( $getDeptsResult ) ) {
						extract( $deptFetch, EXTR_OVERWRITE );
					
					?>
                    <option value="<?=$deptid;?>" style="color:#<?=$deptColor;?>;" <? if( $positionDept == $deptid ) { echo "selected"; } ?>>
                      <?=$deptName;?>
                    </option>
					<? } /* close the loop building the select menu */ ?>
					</select>
				</td>
			</tr>
			<tr>
				<td height="25" colspan="4">&nbsp;</td>
			</tr>
			</form>
			<? } ?>
		</table>
	</div>
	
<? } else { errorMessage( "positions management" ); } ?>