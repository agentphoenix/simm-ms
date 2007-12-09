<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: Nathan Wharry [ mail@herschwolf.net ]
File: pages/contact.php
Purpose: To display the list of departments offered by the SIMM and their
	associated positions

System Version: 2.6.0
Last Modified: 2007-10-10 1001 EST
**/

/* define the page class and vars */
$pageClass = "ship";
$dept = $_GET['dept'];

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* pull all the available departments that should be displayed */
$getDept = "SELECT * FROM sms_departments ";
$getDept.= "WHERE deptDisplay = 'y' ORDER BY deptid ASC";
$getDeptResult = mysql_query( $getDept );

/* set colspan to 1 */
$colspan = 1;


/* pull the positions based on dept clicked on */
if ( isset( $dept ) ) {
	$getPositions = "SELECT * FROM sms_positions ";
	$getPositions.= "WHERE positionDept = '$dept' AND positionDisplay = 'y' ORDER BY positionid ASC";
	$getPositionsResult = mysql_query( $getPositions );


	/* set colspan to 2 for added tabledata for position name */
	$colspan = 2;
}

?>

<div class="body">
	<span class="fontTitle">Departments &amp; Positions</span>
	<?
	
	/*
		if the person is logged in and has level 5 access, display an icon
		that will take them to edit the entry
	*/
	if( isset( $sessionCrewid ) && in_array( "m_departments", $sessionAccess ) ) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='" . $webLocation . "admin.php?page=manage&sub=departments'>";
		echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' class='image' />";
		echo "</a>";
	}
	
	?>
	<br /><br />
	
	<table>
	
	<?
	
	while( $deptinfo = mysql_fetch_array( $getDeptResult ) ) {
		extract( $deptinfo, EXTR_OVERWRITE );
		
	?>
	
	<tr>
		<td width="20%">
			<span class="fontMedium">
				<b><div style="color:#<?=$deptColor;?>;"><? printText( $deptName ); ?></div></b>
			</span>
			
			<a href="<?=$webLocation;?>index.php?page=departments&dept=<?=$deptid;?>">
				<span class="fontSmall">[ Show Positions ]</span>
			</a>
		</td>
		<td width="5">&nbsp;</td>
		<td colspan="<?=$colspan;?>"><? printText( $deptDesc ); ?></td>
	</tr>
	
	
	<?
	
	if ( isset( $dept ) && ( $deptid == $dept ) ) { 
	
	/* extract the variables */
	while( $positioninfo = mysql_fetch_array( $getPositionsResult ) ) {
		extract( $positioninfo, EXTR_OVERWRITE );
		
	?>
	
	<tr>
		<td colspan="4" height="3">&nbsp;</td>
	</tr>
	
	
	<tr>
		<td colspan="2">&nbsp;</td>
		<td class="fontNormal" width="25%"><b><? printText( $positionName ); ?></b></td>
		<td class="fontSmall"><? printText( $positionDesc ); ?><td>
	</tr>
	
	<? 
		} /* close the while statement */
	} /* close the if statement */
	?>
	
	<tr>
		<td colspan="2"></td>
		<td colspan="<?=$colspan;?>" height="5">&nbsp;</td>
	</tr>
	<? } /* close the while statement */ ?>
	
	</table>
</div>