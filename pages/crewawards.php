<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: Nathan Wharry [ mail@herschwolf.net ]
File: pages/crewawards.php
Purpose: To display the list of crew awards currently entered

System Version: 2.5.0
Last Modified: 2007-04-05 2349 EST
**/

/* define the page class */
$pageClass = "simm";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* pull the data from the database on all awards */
$getAwards = "SELECT * FROM sms_awards ORDER BY awardOrder ASC";
$getAwardsResult = mysql_query ( $getAwards );

?>

<div class="body">
	<span class="fontTitle">Crew Awards</span>
	
	<?
	
	/*
		if the person is logged in and has level 5 access, display an icon
		that will take them to edit the entry
	*/
	if( isset( $sessionCrewid ) && in_array( "m_awards", $sessionAccess ) ) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='" . $webLocation . "admin.php?page=manage&sub=awards'>";
		echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' />";
		echo "</a>";
	}
	
	?>
	
	<br /><br />
	
	<table>
		<tr>
			<td width="25%" class="fontLarge" align="center"><b>Award</b></td>
			<td>&nbsp;</td>
			<td class="fontLarge"<b>Description</b></td>
		</tr>
	
		<?
		
		/* pull the data and place into variables */
		while( $awardInfo = mysql_fetch_array( $getAwardsResult ) ) {
			extract( $awardInfo, EXTR_OVERWRITE );
		
		?>
		
		<tr>
			<td align="center">
				<? printText( $awardName ); ?><br />
				<img src="<?=$webLocation;?>images/awards/large/<?=$awardImage;?>" alt="<? printText( $awardName ); ?>" />
			</td>
			<td>&nbsp;</td>
			<td><? printText( $awardDesc ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="5">&nbsp;</td>
		</tr>
		
		<? } ?>
	
	</table> 
</div>