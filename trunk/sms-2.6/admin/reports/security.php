<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/reports/security.php
Purpose: Page to view the security reports

System Version: 2.6.0
Last Modified: 2007-11-06 0932 EST
**/

/* access check */
if( in_array( "r_security", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "reports";

?>

	<div class="body">
		<span class="fontTitle">Security Report</span><br /><br />
		
			The list below represents all the logged security breaches against the system.<br /><br />
			
			<table cellspacing="0" cellpadding="4">
				<tr class="fontMedium">
					<td><b>Crew</b></td>
					<td><b>Page</b></td>
					<td width="35%"><b>Reason</b></td>
				</tr>
			
			<?php
						
			$rowCount = "0";
			$color1 = "rowColor1";
			$color2 = "rowColor2";
			
			$get = "SELECT * FROM sms_security ORDER BY time DESC";
			$getResult = mysql_query( $get );
			
			while( $fetch = mysql_fetch_array( $getResult ) ) {
				extract( $fetch, EXTR_OVERWRITE );
				
				$rowColor = ($rowCount % 2) ? $color1 : $color2;
			
			?>
				
				<tr class="<?=$rowColor;?>">
					<td>
						<? printCrewName( $fetch[4], "rank", "noLink" ); ?><br />
						<span class="fontNormal">on <?=dateFormat( 'short', $fetch[5] ); ?></span>
					</td>
					<td>
						<?=$fetch[1];?><br />
						<span class="fontNormal">from <?=$fetch[3];?></span>
					</td>
					<td><? printText( $fetch[2] ); ?></td>
				</tr>
				
			<? $rowCount++; } ?>
			
			</table>
	</div>

<? } else { errorMessage( "security report" ); } ?>