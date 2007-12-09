<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/manage/logs.php
Purpose: If there is an ID in the URL, the page will display the personal log
	with that ID number, otherwise, it'll display a list of the 5 most recent
	personal logs for moderation

System Version: 2.5.0
Last Modified: 2007-06-18 1142 EST
**/

/* access check */
if( in_array( "m_logs", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$actionUpdate = $_POST['action_update_x'];
	$actionDelete = $_POST['action_delete_x'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( "personal log editing page" );
		exit();
	} else {
		/* set the GET variable */
		$id = $_GET['id'];
	}
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['remove'] ) && preg_match( "/^\d+$/", $_GET['remove'], $matches ) == 0 ) {
		errorMessageIllegal( "personal log editing page" );
		exit();
	} else {
		/* set the GET variable */
		$remove = $_GET['remove'];
	}

	$logAuthor = $_POST['logAuthor'];
	$logTitle = addslashes( $_POST['logTitle'] );
	$logContent = addslashes( $_POST['logContent'] );
	$logid = $_POST['logid'];
	$logStatus = $_POST['logStatus'];
	
	if( $actionUpdate ) {
		
		$query = "UPDATE sms_personallogs SET logAuthor = '$logAuthor', ";
		$query.= "logTitle = '$logTitle', logContent = '$logContent', logStatus = '$logStatus' ";
		$query.= "WHERE logid = '$logid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_personallogs" );
		
		$action = "update";
	
	} elseif( $actionDelete ) {
		
		$query = "DELETE FROM sms_personallogs WHERE logid = '$logid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_personallogs" );
		
		$action = "delete";
		
	} elseif( isset( $remove ) ) {
	
		$query = "DELETE FROM sms_personallogs WHERE logid = '$remove' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_personallogs" );
		
		$action = "remove";
	
	}
	
	$logTitle = stripslashes( $logTitle );
	$logContent = stripslashes( $logContent );
	
	/* if there's an id in the URL, proceed */
	if( $id ) {

?>

	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "personal log", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage Personal Log</span><br /><br />
		
		<table cellpadding="2" cellspacing="2">
		<?
		
			$logs = "SELECT * FROM sms_personallogs WHERE logid = '$id'";
			$logsResult = mysql_query( $logs );
			
			while( $logFetch = mysql_fetch_assoc( $logsResult ) ) {
				extract( $logFetch, EXTR_OVERWRITE );
		
		?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=logs&id=<?=$id;?>">
			<tr>
				<td valign="middle" colspan="2">
					<b>Title</b><br />
					<input type="text" class="name" name="logTitle" maxlength="100" value="<?=stripslashes( $logTitle );?>" />
					<input type="hidden" name="logid" value="<?=$logid;?>" />
				</td>
			</tr>
			<tr>
				<td valign="middle">
					<b>Author</b><br />
					<? print_active_crew_select_menu( "log", $logAuthor, $logid, "", "" ); ?>
				</td>
				<td valign="middle">
					<span class="fontNormal"><b>Status</b></span><br />
					<select name="logStatus">
						<option value="pending"<? if( $logStatus == "pending" ) { echo " selected"; } ?>>Pending</option>
						<option value="saved"<? if( $logStatus == "saved" ) { echo " selected"; } ?>>Saved</option>
						<option value="activated"<? if( $logStatus == "activated" ) { echo " selected"; } ?>>Activated</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<b>Content</b><br />
					<textarea name="logContent" class="wideTextArea" rows="15"><?=stripslashes( $logContent );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="20"></td>
			</tr>
			<tr>
				<td colspan="2" align="right">
					<input type="hidden" name="logid" value="<?=$logid;?>" />
	
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this personal log?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
	
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update" class="button" value="Update" />
				</td>
			</tr>
			</form>
		<? } ?>
		</table>
	</div>
	<?
		
		/* if there is no ID in the URL, show a list of the last 5 personal logs */
		} elseif( !$id ) {
		
		$getLogs = "SELECT * FROM sms_personallogs ORDER BY logPosted DESC LIMIT 5";
		$getLogsResult = mysql_query( $getLogs );
	
	?>
	
	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "personal log", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage Personal Logs</span><br /><br />
		<table>
			<?
			
			while( $logFetch = mysql_fetch_assoc( $getLogsResult ) ) {
				extract( $logFetch, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=logs">
			<tr>
				<td valign="top">
					<b class="fontNormal">Title</b><br />
					<input type="text" class="name" maxlength="100" name="logTitle" value="<?=stripslashes( $logTitle );?>" />
				</td>
				<td rowspan="3" width="70%" align="center" valign="top">
					<b class="fontNormal">Content</b><br />
					<textarea class="desc" rows="10" name="logContent"><?=stripslashes( $logContent );?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<b class="fontNormal">Author</b><br />
					<? print_active_crew_select_menu( "log", $logAuthor, $logid, "", "" ); ?>
				</td>
			</tr>
			<tr>
				<td>
					<span class="fontNormal"><b>Status</b></span><br />
					<select name="logStatus">
						<option value="pending"<? if( $logStatus == "pending" ) { echo " selected"; } ?>>Pending</option>
						<option value="saved"<? if( $logStatus == "saved" ) { echo " selected"; } ?>>Saved</option>
						<option value="activated"<? if( $logStatus == "activated" ) { echo " selected"; } ?>>Activated</option>
					</select>
				</td>
			</tr>
			<tr>
				<td></td>
				<td valign="top" align="center">
					<input type="hidden" name="logid" value="<?=$logid;?>" />
	
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this personal log?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
	
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update" class="button" value="Update" />
				</td>
			</tr>
			<tr>
				<td colspan="3" height="25"></td>
			</tr>
			</form>
			<? } ?>
		</table>
		
	</div>
	
<? } } else { errorMessage( "personal log management" ); } ?>