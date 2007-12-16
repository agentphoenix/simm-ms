<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/tour.php
Purpose: Page to display the tour items

System Version: 2.6.0
Last Modified: 2007-10-12 1643 EST
**/

/* define the page class and vars */
$pageClass = "ship";
$tour = $_GET['id'];

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* open the body class */
echo "<div class='body'>";

/* if there is an id in the URL, pull that specific entry */
if( !empty( $tour ) ) {

	$getTour = "SELECT * FROM sms_tour WHERE tourid = '$tour' LIMIT 1";
	$getTourResult = mysql_query( $getTour );
	
	/* Start pulling the array and populate the variables */
	while( $tour = mysql_fetch_array( $getTourResult ) ) {
		extract( $tour, EXTR_OVERWRITE );
	}

	echo "<span class='fontTitle'>Tour of ";
	printText( $tourName );
	echo "</span>";

	/*
		if the person is logged in and has level 5 access, display an icon
		that will take them to edit the entry
	*/
	if( isset( $sessionCrewid ) && in_array( "m_tour", $sessionAccess ) ) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='" . $webLocation . "admin.php?page=manage&sub=tour&entry=" . $tourid . "'>";
		echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' class='image' />";
		echo "</a>";
	}
	
	if( !empty( $tourPicture1 ) || !empty( $tourPicture2 ) || !empty( $tourPicture3 ) ) {
	
	?>
	
	<br /><br />
	<table>
		<tr height="110">
		<? if( !empty( $tourPicture1 ) ) { ?>
			<td valign="top" width="10%">
				<a href="<?=$webLocation . "images/tour/" . $tourPicture1;?>" target="_blank" class="thickbox"<?php if( $tourPicture1 > "" ) { echo " rel='tour'"; } else { echo ""; } ?>>
					<img src="<?=$webLocation . "images/tour/" . $tourPicture1;?>" border="0" alt="" height="90" class="image reflect rheight30 ropacity30" />
				</a>
			</td>
		<? } ?>
		
		<? if( !empty( $tourPicture2 ) ) { ?>
			<td width="10">&nbsp;</td>
			<td valign="top" width="10%">
				<a href="<?=$webLocation . "images/tour/" . $tourPicture2;?>" target="_blank" class="thickbox"<?php if( $tourPicture2 > "" ) { echo " rel='tour'"; } else { echo ""; } ?>>
					<img src="<?=$webLocation . "images/tour/" . $tourPicture2;?>" border="0" alt="" height="90" class="image reflect rheight30 ropacity30" />
				</a>
			</td>
		<? } ?>
		
		<? if( !empty( $tourPicture3 ) ) { ?>
			<td width="10">&nbsp;</td>
			<td valign="top" width="10%">
				<a href="<?=$webLocation . "images/tour/" . $tourPicture3;?>" target="_blank" class="thickbox"<?php if( $tourPicture3 > "" ) { echo " rel='tour'"; } else { echo ""; } ?>>
					<img src="<?=$webLocation . "images/tour/" . $tourPicture3;?>" border="0" alt="" height="90" class="image reflect rheight30 ropacity30" />
				</a>
			</td>
		<? } ?>
			<td width="70%"></td>
		</tr>
	</table>
	<? } ?>
	
	<br />

	<b class="fontMedium">Location: <? printText( $tourLocation ); ?></b><br />
	<? printText( $tourDesc ); ?>
	
	<br /><br />
	<b class="fontMedium"><a href="<?=$webLocation;?>index.php?page=tour">&laquo; Back to Tour Index</a></b>

	<?

	} else {

		echo "<span class='fontTitle'><i>";
		printText( $shipPrefix . " " . $shipName );
		echo "</i> Tour</span>";

		/*
			if the person is logged in and have level 5 access, display
			an icon that will take them to edit the tour
		*/
		if( isset( $sessionCrewid ) && in_array( "m_tour", $sessionAccess ) ) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<a href='" . $webLocation . "admin.php?page=manage&sub=tour'>";
			echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' class='image' />";
			echo "</a>";
		}

		echo "<br /><br />";
		
		$getTour = "SELECT * FROM sms_tour WHERE tourDisplay = 'y' ORDER BY tourOrder ASC";
		$getTourResult = mysql_query( $getTour );
	
		/* Start pulling the array and populate the variables */
		while( $tour = mysql_fetch_array( $getTourResult ) ) {
			extract( $tour, EXTR_OVERWRITE );

			echo "<span class='fontMedium'><b>";
			echo "<a href='" . $webLocation . "index.php?page=tour&id=" . $tourid . "'>";
			printText( $tourName );
			echo "</a>";
			echo "</b></span><br />";
			
			if( empty( $tourSummary ) ) {
				printText( $tourDesc );
			} else {
				printText( $tourSummary );
			}
			
			echo "<br /><br />";
			
		}
		
	}
	
	?>
	
</div> <!-- close .body -->