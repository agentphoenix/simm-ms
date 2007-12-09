<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/manage/newscategories.php
Purpose: Page that moderates the news categories

System Version: 2.5.0
Last Modified: 2007-06-18 1150 EST
**/

/* access check */
if( in_array( "m_newscat3", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$update = $_POST['action_update_x'];
	$create = $_POST['action_create_x'];
	$delete = $_POST['action_delete_x'];

	/* define the variables */
	$catUserLevel = $_POST['catUserLevel'];
	$catVisible = $_POST['catVisible'];
	$catName = addslashes( $_POST['catName'] );
	$catid = $_POST['catid'];
	
	if( $update ) {
		
		/* do the update query */
		$newsCatQuery = "UPDATE sms_news_categories SET catName = '$catName', ";
		$newsCatQuery.= "catUserLevel = '$catUserLevel', catVisible = '$catVisible' ";
		$newsCatQuery.= "WHERE catid = '$catid' LIMIT 1";
		$result = mysql_query( $newsCatQuery );
		
		/* optimize the table */
		optimizeSQLTable( "sms_news_categories" );
		
		$action = "update";
	
	} elseif( $create ) {
		
		/* do the create query */
		$newsCatQuery = "INSERT INTO sms_news_categories ( catid, catName, catUserLevel, catVisible ) ";
		$newsCatQuery.= "VALUES( '', '$catName', '$catUserLevel', '$catVisible' )";
		$result = mysql_query( $newsCatQuery );
		
		/* optimize the table */
		optimizeSQLTable( "sms_news_categories" );
		
		$action = "create";
	
	} elseif( $delete ) {
		
		/* do the delete query */
		$newsCatQuery = "DELETE FROM sms_news_categories WHERE catid = '$catid' LIMIT 1";
		$result = mysql_query( $newsCatQuery );
		
		/* optimize the table */
		optimizeSQLTable( "sms_news_categories" );
		
		$action = "delete";
		
	}
	
	/* strip the slashes */
	$catName = stripslashes( $catName );

?>

	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $newsCatQuery );
		
		if( !empty( $check->query ) ) {
			$check->message( "news category", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Create New Site News Category</span><br /><br />
		
		<table cellpadding="2" cellspacing="2">
			<form method="post" action="admin.php?page=manage&sub=newscategories">
			<tr>
				<td valign="middle">
					<b>Category Name</b><br />
					<input type="text" class="name" name="catName" maxlength="50" />
				</td>
				<td valign="middle">
					<b>Required Access Level</b><br />
					<select name="catUserLevel">
						<option value="1">General User</option>
						<option value="2">Power User</option>
						<option value="3">Admin</option>
					</select>
				</td>
				<td valign="middle">
					<b>Show Category?</b><br />
					<input type="radio" id="visY" name="catVisible" value="y" checked="yes" /> <label for="visY">Yes</label>
					<input type="radio" id="visN" name="catVisible" value="n" /> <label for="visN">No</label>
				</td>
				<td valign="middle" align="right" style="width:125px;">
					<input type="image" src="<?=path_userskin;?>buttons/create.png" class="button" name="action_create" value="Create" />
				</td>
			</tr>
			</form>
		</table>
		
		<br /><br />
			
		<span class="fontTitle">Manage Site News Categories</span><br /><br />
			
		<table cellpadding="2" cellspacing="2">
		<?
			
		/* pull the categories from the db */
		$newsCategories = "SELECT * FROM sms_news_categories ORDER BY catid ASC";
		$newsCategoriesResult = mysql_query( $newsCategories );
		
		/* loop through the results and fill in the form */
		while( $categories = mysql_fetch_assoc( $newsCategoriesResult ) ) {
			extract( $categories, EXTR_OVERWRITE );
		
		?>
			<form method="post" action="admin.php?page=manage&sub=newscategories">
			<tr>
				<td valign="middle">
					<b>Category Name</b><br />
					<input type="text" class="name" name="catName" maxlength="50" value="<?=stripslashes( $catName );?>" />
				</td>
				<td valign="middle">
					<b>Required Access Level</b><br />
					<select name="catUserLevel">
						<option value="1" <? if( $catUserLevel == "1" ) { echo "selected"; } ?>>General User</option>
						<option value="2" <? if( $catUserLevel == "2" ) { echo "selected"; } ?>>Power User</option>
						<option value="3" <? if( $catUserLevel == "3" ) { echo "selected"; } ?>>Admin</option>
					</select>
				</td>
				<td valign="middle">
					<b>Show Category?</b><br />
					<input type="radio" id="visibleY" name="catVisible" value="y" <? if( $catVisible == "y" ) { echo "checked"; } ?>/> <label for="visibleY">Yes</label>
					<input type="radio" id="visibleN" name="catVisible" value="n" <? if( $catVisible == "n" ) { echo "checked"; } ?>/> <label for="visibleN">No</label>
				</td>
				<td valign="middle" align="right">
					<input type="hidden" name="catid" value="<?=$catid;?>" />
					<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" /><br />
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this news category?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" class="button" name="action_delete" value="Delete" />
					</noscript>
				</td>
			</tr>
			<tr>
				<td colspan="4" height="25"></td>
			</tr>
			</form>
		<? } /* close the $categories while loop */ ?>
		</table>
	</div>
	
<? } else { errorMessage( "news category management" ); } ?>