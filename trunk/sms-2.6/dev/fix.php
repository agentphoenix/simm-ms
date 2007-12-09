<?php

require_once( 'framework/functionsGlobal.php' );

/**
	set up a multi-dimensional array for the timestamp update
	[x][0] => table's primary key
	[x][1] => field being updated
	[x][2] => table being updated
**/
$array = array(
	0 => array( 'crewid', 'joinDate', 'sms_crew' )
);

/* loop through the array */
foreach( $array as $key => $value ) {

	/* pull in the info from the database */
	$getTime = "SELECT $value[0], $value[1] FROM $value[2] ORDER BY $value[0] ASC";
	$getTimeResult = mysql_query( $getTime );
	$getTimeCount = @mysql_num_rows( $getTimeResult );
	
	/* count the rows to avoid SQL errors */
	if( $getTimeCount >= 1 ) {
	
		/* loop through the results */
		while( $timeFetch = mysql_fetch_array( $getTimeResult ) ) {
			extract( $timeFetch, EXTR_OVERWRITE );
			
			/*
				make sure what the function is being fed is actually a
				SQL timestamp and not a UNIX timestamp 
			*/
			if( preg_match( "/^\d+$/", $timeFetch[1], $matches ) ) {} else {
			
				/* do some logic to make sure things are going to be updated correctly */
				if( $timeFetch[1] == "0000-00-00 00:00:00" || $timeFetch[1] == "-1" ) {
					$newTime = "";
				} else {
					$newTime = strtotime( $timeFetch[1] );
				}
				
				/* update the database */
				$update = "UPDATE $value[2] SET $value[1] = '$newTime' ";
				$update.= "WHERE $value[0] = '$timeFetch[0]' LIMIT 1";
				$updateResult = mysql_query( $update );
			
			}
		
		} /* close the while loop */
		
	} /* close the count check */
	
} /* close the foreach loop */

?>