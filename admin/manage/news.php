<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/news.php
Purpose: If there is an ID in the URL, it will pull the corresponding news item
	for editing, otherwise, it'll just show a list of the last 4 news items for
	moderation

System Version: 2.6.0
Last Modified: 2007-11-12 1525 EST
**/

/* access check */
if( in_array( "m_news", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$actionUpdate = $_POST['action_update_x'];
	$actionDelete = $_POST['action_delete_x'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( __FILE__, $sessionCrewid, "numerical value", $_GET['id'] );
		exit();
	} else {
		/* set the GET variable */
		$id = $_GET['id'];
	}
	
	/* define the POST variables */
	$newsCat = $_POST['newsCat'];
	$newsAuthor = $_POST['newsAuthor'];
	$newsTitle = addslashes( $_POST['newsTitle'] );
	$newsContent = addslashes( $_POST['newsContent'] );
	$newsid = $_POST['newsid'];
	$newsStatus = $_POST['newsStatus'];
	$newsPrivate = $_POST['newsPrivate'];
	
	if( $actionUpdate ) {
		
		$query = "UPDATE sms_news SET newsCat = '$newsCat', ";
		$query.= "newsAuthor = '$newsAuthor', newsTitle = '$newsTitle', ";
		$query.= "newsContent = '$newsContent', newsStatus = '$newsStatus', ";
		$query.= "newsPrivate = '$newsPrivate' WHERE newsid = '$newsid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_news" );
		
		$action = "update";
	
	} elseif( $actionDelete ) {
		
		$query = "DELETE FROM sms_news WHERE newsid = '$newsid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_news" );
		
		$action = "delete";
		
	}
	
	/* strip the slashes */
	$newsTitle = stripslashes( $newsTitle );
	$newsContent = stripslashes( $newsContent );
	
	/* if there's an id in the URL, proceed */
	if( isset( $id ) ) {

?>

	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "news item", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage News Item</span><br /><br />
		
		<table cellpadding="2" cellspacing="2">
		<?
		
			$news = "SELECT * FROM sms_news WHERE newsid = '$id'";
			$newsResult = mysql_query( $news );
			
			while( $newsFetch = mysql_fetch_assoc( $newsResult ) ) {
				extract( $newsFetch, EXTR_OVERWRITE );
		
		?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=news&id=<?=$id;?>">
			<tr>
				<td>
					<b>Title</b><br />
					<input type="text" class="name" name="newsTitle" maxlength="100" value="<?=stripslashes( $newsTitle );?>" />
				</td>
				<td colspan="2">
					<b>Category</b><br />
					<select name="newsCat">
					<?
					
					$cats = "SELECT * FROM sms_news_categories ORDER BY catid ASC";
					$catsResult = mysql_query( $cats );
					
					while( $catFetch = mysql_fetch_assoc( $catsResult ) ) {
						extract( $catFetch, EXTR_OVERWRITE );
							
						if( $newsCat == $catid ) {
							echo "<option value='$newsCat' selected>" . stripslashes( $catName ) . "</option>";
						} else {
							echo "<option value='$catid'>" . stripslashes( $catName ) . "</option>";
						}
					}
					
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b>Author</b><br />
					<? print_active_crew_select_menu( "news", $newsAuthor, "", "", "" ); ?>
				</td>
				<td>
					<b>Status</b><br />
					<select name="newsStatus">
						<option value="pending"<? if( $newsStatus == "pending" ) { echo " selected"; } ?>>Pending</option>
						<option value="saved"<? if( $newsStatus == "saved" ) { echo " selected"; } ?>>Saved</option>
						<option value="activated"<? if( $newsStatus == "activated" ) { echo " selected"; } ?>>Activated</option>
					</select>
				</td>
				<td>
					<b>Privacy Status</b><br />
					<select name="newsPrivate">
						<option value="n"<? if( $newsPrivate == "n" ) { echo " selected"; } ?>>Public</option>
						<option value="y"<? if( $newsPrivate == "y" ) { echo " selected"; } ?>>Private</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<b>Content</b><br />
					<textarea name="newsContent" class="wideTextArea" rows="15"><?=stripslashes( $newsContent );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3" align="right">
					<input type="hidden" name="newsid" value="<?=$newsid;?>" />
	
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this news item?')\" />" );
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
	
		} elseif( !isset( $id ) ) {
		
		$getNewsItems = "SELECT * FROM sms_news ORDER BY newsPosted DESC LIMIT 5";
		$getNewsItemsResult = mysql_query( $getNewsItems );
	
		?>
	
	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "news item", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage News Items</span><br /><br />
		
		<table>
			<?
			
			while( $news = mysql_fetch_assoc( $getNewsItemsResult ) ) {
				extract( $news, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=news&action=update">
			<tr>
				<td colspan="2" valign="top">
					<b class="fontNormal">Title</b><br />
					<input type="text" class="name" maxlength="100" name="newsTitle" value="<?=stripslashes( $newsTitle );?>" />
				</td>
				<td rowspan="4" width="70%" align="center" valign="top">
					<b class="fontNormal">Content</b><br />
					<textarea class="desc" rows="10" name="newsContent"><?=stripslashes( $newsContent );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top">
					<b class="fontNormal">Category</b><br />
					<select name="newsCat">
					<?
					
					$cats = "SELECT * FROM sms_news_categories ORDER BY catid ASC";
					$catsResult = mysql_query( $cats );
					
					while( $catFetch = mysql_fetch_assoc( $catsResult ) ) {
						extract( $catFetch, EXTR_OVERWRITE );
							
						if( $newsCat == $catid ) {
							echo "<option value='$newsCat' selected>" . stripslashes( $catName ) . "</option>";
						} else {
							echo "<option value='$catid'>" . stripslashes( $catName ) . "</option>";
						}
					}
					
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<span class="fontNormal"><b>Author</b></span><br />
					<? print_active_crew_select_menu( "news", $newsAuthor, "", "", "" ); ?>
				</td>
			</tr>
			<tr>
				<td>
					<span class="fontNormal"><b>Status</b></span><br />
					<select name="newsStatus">
						<option value="pending"<? if( $newsStatus == "pending" ) { echo " selected"; } ?>>Pending</option>
						<option value="saved"<? if( $newsStatus == "saved" ) { echo " selected"; } ?>>Saved</option>
						<option value="activated"<? if( $newsStatus == "activated" ) { echo " selected"; } ?>>Activated</option>
					</select>
				</td>
				<td>
					<span class="fontNormal"><b>Privacy Status</b></span><br />
					<select name="newsPrivate">
						<option value="n"<? if( $newsPrivate == "n" ) { echo " selected"; } ?>>Public</option>
						<option value="y"<? if( $newsPrivate == "y" ) { echo " selected"; } ?>>Private</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td valign="top" align="center">
					<input type="hidden" name="newsid" value="<?=$newsid;?>" />
	
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this news item?')\" />" );
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
	
<? } } else { errorMessage( "news item moderation" ); } ?>