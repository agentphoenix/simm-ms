<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: framework/functionsAdmin.php
Purpose: List of functions specific to the administration control panel

System Version: 2.5.0
Last Modified: 2007-06-23 1506 EST

Included Functions:
	dateFormat( $type, $time )
	optimizeSQLTable( $table )
	printText( $object )
**/

/**
	Function that will translate the date into one of a few different formats
**/
function dateFormat( $type, $time ) {

	if( $type == "long" ) {
		/*
			returns a variable with the date in the format of:
			Sunday, January 1st, 2007 @ 12:15am
		*/
		return date( "l, F jS, Y @ h:ia", $time );
	} if( $type == "medium" ) {
		/*
			returns a variable with the date in the format of:
			Sun Jan 01, 2007 @ 12:15am
		*/
		return date( "D M d, Y @ g:ia", $time );
	} if( $type == "medium2" ) {
		/*
			returns a variable with the date in the format of:
			Sun Jan 01, 2007
		*/
		return date( "D M d, Y", $time );
	} if( $type == "short" ) {
		/*
			returns a variable with the date in the format of:
			01.01.07 @ 12:15am
		*/
		return date( "m.d.y @ g:ia", $time );
	} if( $type == "short2" ) {
		/*
			returns a variable with the date in the format of:
			01.01.2007
		*/
		return date( "m.d.Y", $time );
	} if( $type == "sql" ) {
		/*
			returns a variable with the date in the format of:
			2007-01-01 12:15:00
		*/
		return date( "Y-m-d G:i:s", $time );
	}

}
/** END FUNCTION **/

/**
	Optimize the SQL table after the query
**/
function optimizeSQLTable( $table ) {

	$optimize = "OPTIMIZE TABLE $table";
	$optimizeResult = mysql_query( $optimize );

}
/** END FUNCTION **/

/**
	Strip slashes and use nl2br() to print out text
**/
function printText( $object ) {

	echo stripslashes( nl2br( $object ) );

}
/** END FUNCTION **/

?>