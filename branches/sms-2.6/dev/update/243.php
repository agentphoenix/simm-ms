<?php

/**
Author: David VanScott [ anodyne.sms@gmail.com ]
File: update/243.php
Purpose: Update page - 2.4.3 => Latest
Last Modified: 2007-07-26 0941 EST
**/

/* add the leave date field */
mysql_query( "ALTER TABLE `sms_crew` ADD `leaveDate` datetime not null default '0000-00-00 00:00:00'" );

/* add the field used by the saved post check */
mysql_query( "ALTER TABLE `sms_posts` ADD `postSave` int(4) not null default '0'" );

/* change the type field in the positions table */
mysql_query( "ALTER TABLE `sms_positions` CHANGE `positionType` `positionType` enum( 'senior', 'crew' ) not null default 'crew'" );

/* update all the rows */
$update1 = "UPDATE sms_positions SET positionType = 'crew'";
$result1 = mysql_query( $update1 );

/* create an array of the positions that should be changed to senior */
$senior = array(
	0 => "1",
	1 => "2",
	2 => "6",
	3 => "10",
	4 => "13",
	5 => "20",
	6 => "25",
	7 => "35",
	8 => "42",
	9 => "43",
	10 => "49",
	11 => "54",
	12 => "59",
	13 => "63"
);

/* loop through the array and update the database */
foreach( $senior as $key => $value ) {

	$update = "UPDATE sms_positions SET positionType = 'senior' ";
	$update.= "WHERE positionid = '$value' LIMIT 1";
	$result = mysql_query( $update );

}

?>