<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/news.php
Purpose: Page to display the news items

System Version: 2.6.0
Last Modified: 2007-11-12 1509 EST
**/

/* define the page class and vars */
$pageClass = "main";
$display = $_GET['disp'];
$id = $_GET['id'];

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

?>

<div class="body">
	
	<? if( !isset( $id ) ) { ?>
	<div align="center">
	<span class="fontNormal">
		<a href="<?=$webLocation;?>index.php?page=news">All News</a>

		<?
		
		/* get the news categories */
		$categories = "SELECT * FROM sms_news_categories WHERE catVisible = 'y' ORDER BY catid ASC";
		$categoriesResult = mysql_query( $categories );
		
		while ( $catList = mysql_fetch_assoc( $categoriesResult ) ) {
			extract( $catList, EXTR_OVERWRITE );
		
		?>
		
			&nbsp; &middot; &nbsp;
			<a href="<?=$webLocation;?>index.php?page=news&disp=<?=$catid;?>"><?=$catName;?></a>
		
		<?
		
		}
		
		if( isset( $display ) ) {
			
			$newsCatTitle = "SELECT catName FROM sms_news_categories WHERE catid = '$display'";
			$newsCatTitleResult = mysql_query( $newsCatTitle );
			$category = mysql_fetch_assoc( $newsCatTitleResult );
			
		}
		
		?>
	
	</span>
	</div> <!-- close the centering div -->
	<br />
	
	<span class="fontTitle">
	<?
	
	if( !$display ) {
		echo "All News";
	} else {
		echo $category['catName'];
	}
	
	?>
	</span><br /><br />
	
	<?
		
		if( !isset( $display ) ) {
		
			$news = "SELECT news.*, cat.* FROM sms_news AS news, sms_news_categories AS cat ";
			$news.= "WHERE news.newsCat = cat.catid AND news.newsStatus = 'activated' ";
			$news.= "ORDER BY newsPosted DESC";
			$newsResults = mysql_query( $news );
		
		} else {
		
			$news = "SELECT news.*, cat.* FROM sms_news AS news, sms_news_categories AS cat ";
			$news.= "WHERE news.newsCat = '$display' AND news.newsStatus = 'activated' ";
			$news.= "GROUP BY news.newsid ORDER BY news.newsPosted DESC";
			$newsResults = mysql_query( $news );

		}
		
		while ( $newsList = mysql_fetch_assoc( $newsResults ) ) {
			extract( $newsList, EXTR_OVERWRITE );
			
			$length = 50; /* The number of words you want */
			$words = explode(' ', $newsContent); /* Creates an array of words */
			$words = array_slice($words, 0, $length); /* Slices the array */
			$text = implode(' ', $words); /* Grabs only the specified number of words */
			
			if( $newsPrivate == 'y' && !isset( $sessionCrewid ) ) {} else {
			
		?>
		
		<span class="fontMedium"><b><? printText( $newsTitle ); ?></b></span><br />
		<span class="fontSmall">
			Posted by <? printCrewName( $newsAuthor, "rank", "link" ); ?> on <?=dateFormat( "long", $newsPosted );?><br />
			Category: <? printText( $catName ); ?>
		</span><br />
		<div style="padding: 1em 0 3em 1em;">
			<?
			
			printText( $text );
			
			echo " ... [ <a href='" . $webLocation . "index.php?page=news&id=" . $newsid . "'>Read More &raquo;</a> ]";
			
			?>
		</div>
		
		<? } } ?>
		
		<?
		
		} else { /* close the if NO id section */
		
			$news = "SELECT news.*, cat.* FROM sms_news AS news, sms_news_categories AS cat ";
			$news.= "WHERE news.newsid = '$id' AND news.newsCat = cat.catid";
			$newsResults = mysql_query( $news );
			
			$getNews = "SELECT newsid FROM sms_news WHERE newsStatus = 'activated' ";
			$getNews.= "ORDER BY newsPosted ASC";
			$getNewsResult = mysql_query( $getNews );
			
			while ( $newsList = mysql_fetch_assoc( $newsResults ) ) {
				extract( $newsList, EXTR_OVERWRITE );
				
				if( $newsPrivate == 'y' && !isset( $sessionCrewid ) ) {} else {
		
		?>
		
		<span class="fontTitle"><? printText( $newsTitle ); ?></span><br /><br />
		
		<span class="fontNormal postDetails">
		<div align="center">
		
		<?
		
			/* point the previous and next post buttons to the correct posts */
		
			$idNumbers = array();
			
			while ( $myrow = mysql_fetch_array( $getNewsResult ) ) {
				$idNumbers[] = $myrow['newsid'];
			}	
			
			foreach( $idNumbers as $key => $value ) {
				if( $id == $value ) {
					
					$nextKey = $key+1;
					$prevKey = $key-1;
			
				/* display the previous and next links in the post details box */
				if( $idNumbers[$prevKey] != '' ) {
						printText ( "<a href='" . $webLocation . "index.php?page=news&id=$idNumbers[$prevKey]'><img src='" . $webLocation . "images/previous.png' alt='Previous Entry' border='0' class='image' /></a>" );
					} if( ($idNumbers[$prevKey] != '') && ($idNumbers[$nextKey] != '') ) {
						echo "&nbsp;";
					} if( $idNumbers[$nextKey] != '' ) {
						printText ( "<a href='" . $webLocation . "index.php?page=news&id=$idNumbers[$nextKey]'><img src='" . $webLocation . "images/next.png' alt='Next Entry' border='0' class='image' /></a>" );
					}
				}
			}
		
		?>
				
				<br />
				<b>News Details</b><br />
				<?
			
				if( in_array( "m_news", $sessionAccess ) ) {
					echo "<a href='" . $webLocation . "admin.php?page=manage&sub=news&id=" . $id . "' class='edit'><b>Edit</b></a>";
					echo "&nbsp; &middot; &nbsp;";
	
				?>	
	
					<script type="text/javascript">
						document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=news&remove=<?=$id;?>\" class=\"delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this personal log?')\">Delete</a>" );
					</script>
					<noscript>
						<a href="<?=$webLocation;?>admin.php?page=manage&sub=news&remove=<?=$id;?>" class="delete"><b>Delete</b></a>
					</noscript>
					
				<?
					
					if( $loginfo['newsStatus'] == "pending" ) {
					
						echo "&nbsp; &middot; &nbsp;";
						echo "<a href='" . $webLocation . "admin.php?page=manage&sub=activate&type=news&id=" . $id . "&action=activate'><b>Activate</b></a>";
					
					}
				}
				
				?><p></p>
			</div> <!-- close the centering div -->
			
			<table>
				<tr>
					<td class="tableCellLabel">Title</td>
					<td>&nbsp;</td>
					<td><? printText( $newsTitle ); ?></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Category</td>
					<td>&nbsp;</td>
					<td><? printText( $catName ); ?></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Author</td>
					<td>&nbsp;</td>
					<td><? printCrewName( $newsAuthor, "rank", "link" ); ?></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Posted</td>
					<td>&nbsp;</td>
					<td><?=dateFormat( "medium", $newsPosted );?></td>
				</tr>
			</table>
			<br />
			<div align="center">
				<b><a href="<?=$webLocation;?>index.php?page=news">Back to All News</a></b>
			</div>
		</span>
		
		<? printText( $newsContent );?>
		
		
	<? } } } ?>
	
</div>