<?php

/**

This is a test file for dynamic substitution of variables for
acceptance and rejection emails. This will not land until SMS 2.6!

Special thanks to Sam Drori of Pump Relationship Technologies, LLC
for helping set up the framework for this code

**/

session_start();

require_once( '../framework/functionsGlobal.php' );

$t = mysql_query("select version() as ve");
echo mysql_error();
$r = mysql_fetch_object($t);

if( $r->ve < 4 ) {
	echo $r->ve;
} else {
	echo "Less than 4!";
}

?>