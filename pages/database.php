<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/database.php
Purpose: Page to display the database entries

System Version: 2.6.0
Last Modified: 2007-10-10 0958 EST
**/

/* define the page class and vars */
$pageClass = "simm";
$entry = $_GET['entry'];
$sort = $_GET['sort'];

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* if there isn't a sort var set, make it order */
if( !$sort ) {
	$sort = "order";
}

/* translate the sort var from the URL to the right SQL value */
if( $sort == "order" ) {
	$sort = "dbOrder";
} elseif( $sort == "name" ) {
	$sort = "dbTitle";
} elseif( $sort == "type" ) {
	$sort = "dbType";
}

echo "<div class='body'>";

/* if there's no entry specified in the URL, give the complete listing */
if( !$entry ) {

	/* show the title */
	echo "<span class='fontTitle'>Database Entries</span>";

	/*
		if the person is logged in and has level 5 access, display an icon
		that will take them to edit the entry
	*/
	if( isset( $sessionCrewid ) && in_array( "m_database", $sessionAccess ) ) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='" . $webLocation . "admin.php?page=manage&sub=database'>";
		echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' class='image' />";
		echo "</a>";
	}
	
	echo "<br />";

	/* show the sort menu */
	echo "<span class='fontNormal'><b>Sort By:</b> ";
	echo "&nbsp;";
	echo "<a href='" . $webLocation . "index.php?page=database&sort=name'>Name (Ascending)</a>";
	echo "&nbsp; &middot; &nbsp;";
	echo "<a href='" . $webLocation . "index.php?page=database&sort=order'>Order (Ascending)</a>";
	echo "&nbsp; &middot; &nbsp;";
	echo "<a href='" . $webLocation . "index.php?page=database&sort=type'>Type (URL Forward / Database Entry)</a>";

	echo "</span>";
	echo "<br /><br />";

	$getEntries = "SELECT * FROM sms_database WHERE dbDisplay = 'y' ORDER BY $sort";
	$getEntriesResult = mysql_query( $getEntries );

	/* Start pulling the array and populate the variables */
	while( $entries = mysql_fetch_array( $getEntriesResult ) ) {
		extract( $entries, EXTR_OVERWRITE );
	
		echo "<span class='fontMedium'><b>";

		/* build a different link based on the type of entry it is */
		if( $dbType == "entry" ) {
			echo "<a href='" . $webLocation . "index.php?page=database&entry=" . $dbid . "'>";
		} elseif( $dbType == "onsite" ) {
			echo "<a href='" . $webLocation . $dbURL . "'>";
		} elseif( $dbType == "offsite" ) {
			echo "<a href='" .$dbURL . "' target='_blank'>";
		}
		
		printText( $dbTitle );
		echo "</a>";
		echo "</b></span><br />";
		printText( $dbDesc );
		echo "<br /><br />";

	}

} else {

	$getEntry = "SELECT * FROM sms_database WHERE dbid = '$entry' LIMIT 1";
	$getEntryResult = mysql_query( $getEntry );

	/* Start pulling the array and populate the variables */
	while( $entry = mysql_fetch_array( $getEntryResult ) ) {
		extract( $entry, EXTR_OVERWRITE );
	}

	echo "<span class='fontTitle'>Database: " . $dbTitle . "</span>";
	
	/*
		if the person is logged in and has level 5 access, display an icon
		that will take them to edit the entry
	*/
	if( isset( $sessionCrewid ) && in_array( "m_database", $sessionAccess ) ) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='" . $webLocation . "admin.php?page=manage&sub=database&entry=" . $dbid . "'>";
		echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' class='image' />";
		echo "</a>";
	}
		
	echo "<br /><br />";

	echo stripslashes( $dbContent );

	echo "<br /><br />";
	echo "<span class='fontMedium'><b>";
	echo "<a href='" . $webLocation . "index.php?page=database'>&laquo; Back to Database Entries</a>";
	echo "</b></span>";

?>

	

<? } ?>

</div> <!-- close .body -->