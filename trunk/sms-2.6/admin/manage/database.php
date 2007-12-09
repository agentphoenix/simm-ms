<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/database.php
Purpose: Page that moderates the database entries

System Version: 2.5.0
Last Modified: 2007-07-10 1002 EST
**/

/* access check */
if( in_array( "m_database", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$actionUpdate = $_POST['action_update_x'];
	$actionCreate = $_POST['action_create_x'];
	$actionDelete = $_POST['action_delete_x'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['entry'] ) && preg_match( "/^\d+$/", $_GET['entry'], $matches ) == 0 ) {
		errorMessageIllegal( "database page" );
		exit();
	} else {
		/* set the GET variable */
		$entry = $_GET['entry'];
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['delete'] ) && preg_match( "/^\d+$/", $_GET['delete'], $matches ) == 0 ) {
		errorMessageIllegal( "database page" );
		exit();
	} else {
		/* set the GET variable */
		$delete = $_GET['delete'];
	}
	
	/* define the POST variables */
	$dbid = $_POST['dbid'];
	$dbTitle = addslashes( $_POST['dbTitle'] );
	$dbDesc = addslashes( $_POST['dbDesc'] );
	$dbOrder = $_POST['dbOrder'];
	$dbDisplay = $_POST['dbDisplay'];
	$dbContent = addslashes( $_POST['dbContent'] );
	$dbType = $_POST['dbType'];
	$dbURL = $_POST['dbURL'];
	
	/* if the POST action is update */
	if( $actionUpdate ) {
		
		/* do the update query */
		$query = "UPDATE sms_database SET ";
		$query.= "dbTitle = '$dbTitle', dbOrder = '$dbOrder', dbDisplay = '$dbDisplay', ";
		$query.= "dbURL = '$dbURL', dbDesc = '$dbDesc', dbContent = '$dbContent', ";
		$query.= "dbType = '$dbType' WHERE dbid = '$dbid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_database" );
		
		$action = "update";
	
	/* if the POST action is create */
	} elseif( $actionCreate ) {
		
		/* do the create query */
		$query = "INSERT INTO sms_database ( dbid, dbTitle, dbType, dbDesc, dbOrder, dbDisplay, dbURL, dbContent ) ";
		$query.= "VALUES ( '', '$dbTitle', '$dbType', '$dbDesc', '$dbOrder', '$dbDisplay', '$dbURL', '$dbContent' )";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_database" );
		
		$action = "create";
	
	/* if the POST action is delete */
	} elseif( $actionDelete  || isset( $delete ) ) {
		
		if( $actionDelete ) {
			/* do the delete query */
			$query = "DELETE FROM sms_database WHERE dbid = '$dbid' LIMIT 1";
			$result = mysql_query( $query );
		} elseif( $delete ) {
			/* do the delete query */
			$query = "DELETE FROM sms_database WHERE dbid = '$delete' LIMIT 1";
			$result = mysql_query( $query );
		}
		
		/* optimize the table */
		optimizeSQLTable( "sms_database" );
		
		$action = "delete";
	
	}

	/* strip the slashes */
	$tourName = stripslashes( $tourName );
	$tourLocation = stripslashes( $tourLocation );
	$tourDesc = stripslashes( $tourDesc );

?>

	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "database entry", $action );
			$check->display();
		}
		
		?>
	
		<? if( !$entry || $actionDelete ) { ?>
		<span class="fontTitle">Create New Database Item</span><br /><br />
		The database feature in SMS 2 allows COs to create an easy-to-manage list of important links, 
		both on-site and off-site, as well as the option to create a database entry for those things that 
		don't require a complete new page created.  If you want to create an entry that uses extensive 
		HTML or PHP, please create a new SMS page and use an on-site URL forwarding entry.  The 
		database feature will display basic HTML, but does not support extensive use of HTML code in 
		the database entries.  For off-site URL forwarding entries, give the full URL (e.g. http:/*www.something.com/), 
		for on-site URL forwarding entries only give what comes after the location of SMS (e.g. index.php?page=manifest).  
		For reference, your web location is: <b><?=$webLocation;?></b><br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=database">
		<table cellpadding="0" cellspacing="3">
			<tr>
				<td>
					<span class="fontNormal"><b>Order</b></span><br />
					<input type="text" class="order" name="dbOrder" maxlength="4" value="99" />
				</td>
				<td>
					<span class="fontNormal"><b>Display?</b></span><br />
					<select name="dbDisplay">
						<option value="y">Yes</option>
						<option value="n">No</option>
					</select>
				</td>
				<td rowspan="5" valign="top" align="center" width="5">&nbsp;</td>
				<td rowspan="5" valign="top" align="center" width="70%">
					<span class="fontNormal"><b>Content</b></span><br />
					<span class="fontSmall">* used only for database entries, not URL forwarding</span><br />
					<textarea name="dbContent" rows="12" class="desc"></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<span class="fontNormal"><b>Title</b></span><br />
					<input type="text" class="name" name="dbTitle" maxlength="100" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<span class="fontNormal"><b>Short Description</b></span><br />
					<input type="text" class="name" name="dbDesc" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<span class="fontNormal"><b>Entry Type</b></span><br />
					<select name="dbType">
						<option value="onsite">URL Forward (On-Site)</option>
						<option value="offsite">URL Forward (Off-Site)</option>
						<option value="entry">Database Entry</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<span class="fontNormal"><b>URL</b></span><br />
					<span class="fontSmall">* used only for URL forwarding entries</span><br />
					<input type="text" class="name" name="dbURL" maxlength="255" />
				</td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td align="center">&nbsp;</td>
				<td align="center">
					<input type="image" src="<?=path_userskin;?>buttons/create.png" class="button" name="action_create" value="Create" />
				</td>
			</tr>
		</table>
		</form>
		<br /><br />
		
		<span class="fontTitle">Manage Existing Database Items</span><br /><br />
			<?
	
			$rowCount = "0";
			$color1 = "rowColor1";
			$color2 = "rowColor2";
				
			?>
			<table cellpadding="3" cellspacing="0">
	
			<?
			
			/* pull the info from the database */
			$getDB = "SELECT dbid, dbTitle, dbType, dbURL FROM sms_database ORDER BY dbOrder ASC";
			$getDBResult = mysql_query( $getDB );
			
			/* loop through the results and fill the form */
			while( $dbFetch = mysql_fetch_assoc( $getDBResult ) ) {
				extract( $dbFetch, EXTR_OVERWRITE );
	
				$rowColor = ($rowCount % 2) ? $color1 : $color2;
	
			?>
			
				<tr class="<?=$rowColor;?>">
					<td><? printText( $dbTitle ); ?></td>
					<td align="center" width="10%">
						<? if( $dbType == "entry" ) { ?>
							<b><a href="<?=$webLocation;?>index.php?page=database&entry=<?=$dbid;?>">View</a></b>
						<? } elseif( $dbType == "onsite" ) { ?>
							<b><a href="<?=$webLocation . $dbURL;?>">View</a></b>
						<? } elseif( $dbType == "offsite" ) { ?>
							<b><a href="<?=$dbURL;?>" target="_blank">View</a></b>
						<? } ?>
					</td>
					<td align="center" width="10%"><b><a href="<?=$webLocation;?>admin.php?page=manage&sub=database&entry=<?=$dbid;?>" class="edit" >Edit</a></b></td>
					<td align="center" width="10%">
						<script type="text/javascript">
							document.write( "<b><a href=\"<?=$webLocation;?>admin.php?page=manage&sub=database&delete=<?=$dbid;?>\" class=\"delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this database item?')\">Delete</a></b>" );
						</script>
						<noscript>
							<b><a href="<?=$webLocation;?>admin.php?page=manage&sub=database&delete=<?=$dbid;?>" class="delete">Delete</a></b>
						</noscript>
					</td>
				</tr>
		
			<? $rowCount++; } ?>
	
			</table>
			
		<?
	
		} else {
	
			/* pull the ranks from the database */
			$getEntry = "SELECT * FROM sms_database WHERE dbid = '$entry' LIMIT 1";
			$getEntryResult = mysql_query( $getEntry );
	
			/* loop through the results and fill the form */
			while( $entryFetch = mysql_fetch_assoc( $getEntryResult ) ) {
				extract( $entryFetch, EXTR_OVERWRITE );
			}
	
		?>
	
		<span class="fontTitle">Manage Database Item: <? printText( $dbTitle ); ?></span><br /><br />
		<b class="fontMedium">
			<a href="<?=$webLocation;?>admin.php?page=manage&sub=database">&laquo; Back to Database Management</a><br /><br />
		</b>
		
		<table cellpadding="0" cellspacing="3">
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=database&entry=<?=$entry;?>">
			<tr>
				<td valign="top">
					<b class="fontNormal">Title</b><br />
					<input type="text" class="name" name="dbTitle" maxlength="100" value="<?=stripslashes( $dbTitle );?>" />
				</td>
				<td>
					<b class="fontNormal">Short Description</b><br />
					<input type="text" class="name" name="dbDesc" value="<?=stripslashes( $dbDesc );?>" />
				</td>
			</tr>
			<tr>
				<td>
					<b class="fontNormal">Entry Type</b><br />
					<select name="dbType">
						<option value="onsite" <? if( $dbType == "onsite" ) { echo "selected"; } ?>>URL Forward (On-Site)</option>
						<option value="offsite" <? if( $dbType == "offsite" ) { echo "selected"; } ?>>URL Forward (Off-Site)</option>
						<option value="entry" <? if( $dbType == "entry" ) { echo "selected"; } ?>>Database Entry</option>
					</select>
				</td>
				<td>
					<b class="fontNormal">URL</b><br />
					<span class="fontSmall">* used only for URL forwarding entries</span><br />
					<input type="text" class="name" name="dbURL" maxlength="255" value="<?=$dbURL;?>" />
				</td>
			</tr>
			<tr>
				<td>
					<b class="fontNormal">Order</b><br />
					<input type="text" class="order" name="dbOrder" maxlength="4" value="<?=$dbOrder;?>" />
				</td>
				<td>
					<span class="fontNormal"><b>Display?</b></span><br />
					<select name="dbDisplay">
						<option value="y" <? if( $dbDisplay == "y" ) { echo "selected"; } ?>>Yes</option>
						<option value="n" <? if( $dbDisplay == "n" ) { echo "selected"; } ?>>No</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<b class="fontNormal">Content</b><br />
					<textarea name="dbContent" class="wideTextArea" rows="15"><?=stripslashes( $dbContent );?></textarea>
				</td>
			</tr>
			<tr>
				<td align="right" colspan="2">
					<input type="hidden" name="dbid" value="<?=$entry;?>" />
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this database entry?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
					&nbsp; &nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" />
				</td>
			</tr>
			</form>
		</table>
		<? } ?>
		
	</div>

<? } else { errorMessage( "database management" ); } ?>