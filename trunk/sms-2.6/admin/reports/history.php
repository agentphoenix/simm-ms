<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/reports/history.php
Purpose: Page to show the version history of SMS

System Version: 2.6.0
Last Modified: 2007-08-17 1350 EST
**/

/* access check */
if( in_array( "r_versions", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "reports";
	
	/* query the database for the data */
	$query = "SELECT * FROM sms_system_versions ORDER BY versionDate DESC";
	$result = mysql_query( $query );
	
?>
	
	<div class="body">
		<span class="fontTitle">SMS Version History</span><br /><br />
		At Anodyne Productions, we believe that it's not enough to put out a good product,
		you have to maintain it too.  To that end, we are committed to providing frequent
		and substantial updates to SMS to patch bugs that we have missed in testing, add
		additional functionality to existing features in the hope of making life even easier
		for COs, and adding new features that will have COs wondering what they did before
		it came along.  Though updates may not always be the easiest or fastest thing, we
		believe they are beneficial to making life onboard your sim easier for the CO as well
		as the player.  Below is a version history of SMS since the release of SMS 2 on
		July 24, 2006.<br /><br />
			
		<?
		
		/* pull the data out of the query */
		while( $versionFetch = mysql_fetch_array( $result ) ) {
			extract( $versionFetch, EXTR_OVERWRITE );
		
			/* split the description into an array */
			$desc = explode( ";", $versionDesc );
			
			echo "<b class='fontMedium'>" . $version;
			echo "&nbsp;&nbsp;&nbsp;";
			echo "<span class='fontSmall blue'>[ " . dateFormat( 'short2', $versionDate ) . " ]</span></b>";
			echo "<ul class='version'>";
			
			/* loop through the array and print out the data */
			foreach( $desc as $key => $value ) {
			
				echo "<li>" . $value . "</li>";
			
			}
			
			/* close the list */
			echo "</ul>";
		
		} /* close the while loop */
		
		?>
				
	</div>
	
<? } else { errorMessage( "SMS version history" ); } ?>