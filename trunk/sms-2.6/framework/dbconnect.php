<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: framework/dbconnect.php
Purpose: Database connection file
	
System Version: 2.6.0
Last Modified: 2007-08-22 1652 EST
**/

/* pull in the variables */
require_once( 'variables.php' );

/* database connection */
$db = @mysql_connect( $dbServer, $dbUser, $dbPassword ) or die ( "<b>" . $dbErrorMessage . "</b>" );
mysql_select_db( $dbName, $db ) or die ( "<b>Unable to select the appropriate database.  Please try again later.</b>" );

?>