<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/post/news.php
Purpose: Page to post a news item

System Version: 2.5.0
Last Modified: 2007-06-18 1314 EST
**/

/* access check */
if( in_array( "p_news", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "post";
	$actionPost = $_POST['action_post_x'];
	$actionSave = $_POST['action_save_x'];
	$actionDelete = $_POST['action_delete_x'];
	
	/* do some advanced checking to make sure someone's not trying to do a SQL injection */
	if( !empty( $_GET['id'] ) && preg_match( "/^\d+$/", $_GET['id'], $matches ) == 0 ) {
		errorMessageIllegal( "post news item page" );
		exit();
	} else {
		$id = $_GET['id'];
	}
	
	if( $actionPost ) {
		
		/* add the necessary slashes */
		$newsTitle = addslashes( $_POST['newsTitle'] );
		$newsContent = addslashes( $_POST['newsContent'] );
		$newsCat = $_POST['newsCat'];
	
		/* check to see if the user is moderated */
		$getModerated = "SELECT crewid FROM sms_crew WHERE moderateNews = 'y'";
		$getModeratedResult = mysql_query( $getModerated );
	
		while( $moderated = mysql_fetch_array( $getModeratedResult ) ) {
			extract( $moderated, EXTR_OVERWRITE );
	
			$modArray[] = $moderated['0'];
	
		}
		/* end moderation check */
	
		if( count( $modArray ) > "0" && in_array( $sessionCrewid, $modArray ) ) {
			$newsStatus = "pending";
		} elseif( $sessionCrewid == "" ) {
			$newsStatus = "pending";
		} elseif( $sessionCrewid == "0" ) {
			$newsStatus = "pending";
		} elseif( $sessionCrewid > "0" ) {
			$newsStatus = "activated";
		} elseif( $newsCat == "0" || $newsCat == "" ) {
			$newsStatus = "pending";
		}
	
		if( !$id ) {
			$query = "INSERT INTO sms_news ( newsid, newsCat, newsAuthor, newsPosted, newsTitle, newsContent, newsStatus ) ";
			$query.= "VALUES ( '', '$newsCat', '$sessionCrewid', UNIX_TIMESTAMP(), '$newsTitle', '$newsContent', '$newsStatus' )";
		} else {
			$query = "UPDATE sms_news SET newsCat = '$newsCat', newsTitle = '$newsTitle', ";
			$query.= "newsContent = '$newsContent', newsStatus = '$newsStatus', ";
			$query.= "newsPosted = UNIX_TIMESTAMP() WHERE newsid = '$id' LIMIT 1";
		}
		
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_news" );
		
		$action = "post";
		
		/* strip the slashes added for the query */
		$newsTitle = stripslashes( $newsTitle );
		$newsContent = stripslashes( $newsContent );
		
		/** EMAIL THE NEWS **/
		
		/* set the email author */
		$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankName ";
		$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$userFetch.= "WHERE crew.crewid = '$sessionCrewid' AND crew.rankid = rank.rankid LIMIT 1";
		$userFetchResult = mysql_query( $userFetch );
		
		while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
			extract( $userFetchArray, EXTR_OVERWRITE );
		
			$firstName = str_replace( "'", "", $firstName );
			$lastName = str_replace( "'", "", $lastName );
			
			$from = $rankName . " " . $firstName . " " . $lastName . " < " . $email . " >";
	
		}
		
		/* pull the category name */
		$getCategory = "SELECT catName FROM sms_news_categories WHERE catid = '$newsCat' LIMIT 1";
		$getCategoryResult = mysql_query( $getCategory );
		$category = mysql_fetch_assoc( $getCategoryResult );
	
		if( $newsStatus == "activated" ) {
		
			/* define the variables */
			$to = getCrewEmails( "emailNews" );
			$subject = "[" . $shipPrefix . " " . $shipName . "] " . stripslashes( $category['catName'] ) . " - " . stripslashes( $newsTitle );
			$message = "A News Item Posted By " . printCrewNameEmail( $sessionCrewid ) . "
			
" . stripslashes( $newsContent );
			
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
	
		} elseif( $newsStatus == "pending" ) {
	
			/* define the variables  */
			$to = printCOEmail();
			$subject = "[" . $shipPrefix . " " . $shipName . "] " . stripslashes( $category['catName'] ) . " - " . stripslashes( $newsTitle ) . " (Awaiting Approval)";
			$message = "A News Item Posted By " . printCrewNameEmail( $sessionCrewid ) . "
			
" . stripslashes( $newsContent ) . "

Please log in to approve this news item.  " . $webLocation . "login.php?action=login";
			
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
	
		}
			
	} if( $actionSave ) {
	
		/* add the necessary slashes */
		$newsTitle = addslashes( $_POST['newsTitle'] );
		$newsContent = addslashes( $_POST['newsContent'] );
		$newsCat = $_POST['newsCat'];
	
		if( !$id ) {
			$query = "INSERT INTO sms_news ( newsid, newsCat, newsAuthor, newsPosted, newsTitle, newsContent, newsStatus ) ";
			$query.= "VALUES ( '', '$newsCat', '$sessionCrewid', UNIX_TIMESTAMP(), '$newsTitle', '$newsContent', 'saved' )";
		} else {
			$query = "UPDATE sms_news SET newsCat = '$newsCat', newsTitle = '$newsTitle', ";
			$query.= "newsContent = '$newsContent', newsStatus = 'saved', ";
			$query.= "newsPosted = UNIX_TIMESTAMP() WHERE newsid = '$id' LIMIT 1";
		}
	
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_news" );
		
		$action = "save";
		
		/* strip the slashes added for the query */
		$newsTitle = stripslashes( $newsTitle );
		$newsContent = stripslashes( $newsContent );
	
	} if( $actionDelete ) {
	
		/* delete the news item */
		$query = "DELETE FROM sms_news WHERE newsid = '$id' LIMIT 1";
		$result = mysql_query( $query );
	
		/* optimize the table */
		optimizeSQLTable( "sms_news" );
		
		$action = "delete";
	
	}
	
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
	
		<span class="fontTitle">Post News Item</span><br /><br />
	
		<? if( !$id ) { ?>
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=news">
		<table>
			<tr>
				<td class="tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Category</b></td>
				<td>&nbsp;</td>
				<td>
					<select name="newsCat">
						<?
	
						/* do some logic to make sure that the system is pulling the right categories */
						if( in_array( "m_newscat1", $sessionAccess ) ) {
							$catLevel = "1";
						} if( in_array( "m_newscat2", $sessionAccess ) ) {
							$catLevel = "2";
						} if( in_array( "m_newscat3", $sessionAccess ) ) {
							$catLevel = "3";
						}
						
						$availableCats = "SELECT * FROM sms_news_categories WHERE catUserLevel <= '$catLevel' AND catVisible = 'y' ORDER BY catid ASC";
						$availableCatsResult = mysql_query( $availableCats );
						
						while( $available = mysql_fetch_assoc( $availableCatsResult ) ) {
							extract( $available, EXTR_OVERWRITE );
							
							echo "<option value='" . $catid . "'>" . $catName . "</option>";
							
						}
						
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Title</b></td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="newsTitle" style="font-weight:bold;" length="100" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Content</b></td>
				<td>&nbsp;</td>
				<td><textarea name="newsContent" rows="15" class="desc"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" value="Save" class="button" />
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/post.png" name="action_post" value="Post" class="button" />
				</td>
			</tr>
		</table>
		</form>
	
		<?
		
		} elseif( $id && !$actionDelete ) {
	
			$getNews = "SELECT * FROM sms_news WHERE newsid = '$id' LIMIT 1";
			$getNewsResults = mysql_query( $getNews );
			
			while( $fetchNews = mysql_fetch_array( $getNewsResults ) ) {
				extract( $fetchNews, EXTR_OVERWRITE );
			}
			
		?>
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=news&id=<?=$id;?>">
		<table>
			<tr>
				<td class="tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Category</b></td>
				<td>&nbsp;</td>
				<td>
					<select name="newsCat">
						<?
						
						/* do some logic to make sure that the system is pulling the right categories */
						if( in_array( "m_newscat1", $sessionAccess ) ) {
							$catLevel = "1";
						} if( in_array( "m_newscat2", $sessionAccess ) ) {
							$catLevel = "2";
						} if( in_array( "m_newscat3", $sessionAccess ) ) {
							$catLevel = "3";
						}
						
						$availableCats = "SELECT * FROM sms_news_categories WHERE catUserLevel <= '$catLevel' AND catVisible = 'y' ORDER BY catid ASC";
						$availableCatsResult = mysql_query( $availableCats );
						
						while( $available = mysql_fetch_assoc( $availableCatsResult ) ) {
							extract( $available, EXTR_OVERWRITE );
							
							echo "<option value='" . $catid . "'>" . $catName . "</option>";
							
						}
						
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Title</b></td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="newsTitle" style="font-weight:bold;" length="100" value="<?=stripslashes( $newsTitle );?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Content</b></td>
				<td>&nbsp;</td>
				<td><textarea name="newsContent" rows="10" class="desc"><?=stripslashes( $newsContent );?></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this saved news item?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" value="Save" class="button" />
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/post.png" name="action_post" value="Post" class="button" />
				</td>
			</tr>
		</table>
		</form>
	
		<? } elseif( $id && $actionDelete ) { ?>
	
		Please return to the Control Panel to continue.
	
		<? } ?>
		
	</div>

<? } else { errorMessage( "news item posting" ); } ?>