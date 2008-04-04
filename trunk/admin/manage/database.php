<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/database.php
Purpose: Page that moderates the database entries

System Version: 2.6.0
Last Modified: 2008-04-03 2058 EST
**/

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* access check */
if( in_array( "m_database1", $sessionAccess ) || in_array( "m_database2", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	$action_type = FALSE;
	
	if(isset($_POST))
	{
		/* define the POST variables */
		foreach($_POST as $key => $value)
		{
			$$key = $value;
		}
		
		/* protecting against SQL injection */
		if(isset($action_id) && !is_numeric($action_id))
		{
			$action_id = FALSE;
			exit();
		}
		
		switch($action_type)
		{
			case 'create':
				
				$create = "INSERT INTO sms_database (dbTitle, dbType, dbDesc, dbOrder, dbDisplay, dbURL, dbContent, dbDept) ";
				$create.= "VALUES (%s, %s, %s, %d, %s, %s, %s, %d)";

				$query = sprintf(
					$create,
					escape_string($_POST['dbTitle']),
					escape_string($_POST['dbType']),
					escape_string($_POST['dbDesc']),
					escape_string($_POST['dbOrder']),
					escape_string($_POST['dbDisplay']),
					escape_string($_POST['dbURL']),
					escape_string($_POST['dbContent']),
					escape_string($_POST['dbDept'])
				);

				$result = mysql_query( $query );
				
				break;
			case 'update':
				
				$update = "UPDATE sms_database SET dbTitle = %s, dbOrder = %d, dbDisplay = %s, dbURL = %s, dbDesc = %s, dbContent = %s, ";
				$update.= "dbType = %s, dbDept = %d WHERE dbid = $action_id LIMIT 1";

				$query = sprintf(
					$update,
					escape_string($_POST['dbTitle']),
					escape_string($_POST['dbOrder']),
					escape_string($_POST['dbDisplay']),
					escape_string($_POST['dbURL']),
					escape_string($_POST['dbDesc']),
					escape_string($_POST['dbContent']),
					escape_string($_POST['dbType']),
					escape_string($_POST['dbDept'])
				);

				$result = mysql_query( $query );
				
				break;
			case 'delete':
				
				$query = "DELETE FROM sms_database WHERE dbid = $action_id LIMIT 1";
				$result = mysql_query($query);
				
				break;
		}
		
		/* optimize the table */
		optimizeSQLTable( "sms_database" );
	}

	/* set up the database array */
	$database = array(0 => array());

	/* pull all the applicable departments */
	$depts = "SELECT * FROM sms_departments WHERE deptDatabaseUse = 'y' ORDER BY deptORDER ASC";
	$deptsR = mysql_query($depts);

	/* set up the department sections */
	while($deptFetch = mysql_fetch_assoc($deptsR)) {
		extract($deptFetch, EXTR_OVERWRITE);
	
		$database[$deptid] = array();
	}

	/* pull all the entries */
	$entries = "SELECT * FROM sms_database WHERE dbDisplay = 'y'";
	$entriesR = mysql_query($entries);

	/* fill in the array */
	while($entryFetch = mysql_fetch_assoc($entriesR)) {
		extract($entryFetch, EXTR_OVERWRITE);
	
		$database[$dbDept][] = array('id' => $dbid, 'title' => $dbTitle, 'type' => $dbType, 'url' => $dbURL, 'order' => $dbOrder);
	}

?>
<script type="text/javascript">
	$(document).ready(function() {
		$("a[rel*=facebox]").click(function() {
			var id = $(this).attr("myID");
			var action = $(this).attr("myAction");
			
			jQuery.facebox(function() {
				jQuery.get('admin/ajax/database_' + action + '.php?id=' + id, function(data) {
					jQuery.facebox(data);
				});
			});
			return false;
		});
		
		$('.zebra tr:odd').addClass('alt');
	});
</script>

	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "database entry", $action_type );
			$check->display();
		}
		
		?>
	
		<? if(!isset($entry) || isset($_POST['action_delete_x'])) { ?>
			
		<span class="fontTitle">Database Entry Management</span><br /><br />
		
		The database feature in SMS 2 allows COs to create an easy-to-manage list of important links, 
		both on-site and off-site, as well as the option to create a database entry for those things that 
		don't require a complete new page created.  If you want to create an entry that uses extensive 
		HTML or PHP, please create a new SMS page and use an on-site URL forwarding entry.  The 
		database feature will display basic HTML, but does not support extensive use of HTML code in 
		the database entries.  For off-site URL forwarding entries, give the full URL (e.g. http:/*www.something.com/), 
		for on-site URL forwarding entries only give what comes after the location of SMS (e.g. index.php?page=manifest).  
		For reference, your web location is: <b><?=$webLocation;?></b><br /><br />
		
		<a href="#" rel="facebox" myAction="add" class="fontMedium add"><strong>Create New Database Entry &raquo;</strong></a>
		<br /><br />
		
		<span class="fontTitle">Manage Existing Database Items</span><br /><br />
			<table class="zebra" cellpadding="3" cellspacing="0">
			<?
			
			/* pull the info from the database */
			$getDB = "SELECT dbid, dbTitle, dbType, dbURL FROM sms_database ORDER BY dbOrder ASC";
			$getDBResult = mysql_query( $getDB );
			
			/* loop through the results and fill the form */
			while( $dbFetch = mysql_fetch_assoc( $getDBResult ) ) {
				extract( $dbFetch, EXTR_OVERWRITE );
	
			?>
			
				<tr>
					<td><? printText( $dbTitle ); ?></td>
					<td align="center" width="10%">
						<?php
						
						switch($dbType)
						{
							case 'entry':
								echo "<strong><a href='" . $webLocation . "index.php?page=database&entry=" . $dbid . "'>";
								break;
							case 'onsite':
								echo "<strong><a href='" . $webLocation . $dbURL . "'>";
								break;
							case 'offsite':
								echo "<strong><a href='" . $dbURL . "'>";
								break;
						}
						
						echo "View</a></strong>";
						
						?>
					</td>
					<td align="center" width="10%">
						<strong><a href="#" rel="facebox" myAction="edit" myID="<?=$dbid;?>" class="edit" >Edit</a></strong>
					</td>
					<td align="center" width="10%">
						<script type="text/javascript">
							document.write( "<b><a href=\"<?=$webLocation;?>admin.php?page=manage&sub=database&delete=<?=$dbid;?>\" class=\"delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this database item?')\">Delete</a></b>" );
						</script>
						<noscript>
							<b><a href="<?=$webLocation;?>admin.php?page=manage&sub=database&delete=<?=$dbid;?>" class="delete">Delete</a></b>
						</noscript>
					</td>
				</tr>
		
			<? } ?>
	
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