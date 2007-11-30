<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: [ framework/functionsGlobal.php ]
Purpose: File that holds all the necessary global function files for JP author printing,
	database connection, and error catching
	
System Version: 2.5.5
Last Modified: 2007-11-07 0823 EST

Included Functions:
	displayAuthors( $missionID, $link )
	print_active_crew_select_menu( $type, $author, $id, $section, $sub )
	printText( $object )
	printCO()
	printXO()
	printCOEmail()
	printXOEmail()
	printPlayerPosition( $crewid, $position, $positionNumber )
	displayEmailAuthors( $authors, $link )
	printMissionTitle( $missionid )
	getCrewEmails( $type )
	printCrewNameEmail( $id )
	escape_string( $value )
**/

/* pull in the DB connection variables */
require_once( 'variables.php' );

/* database connection */
$db = @mysql_connect( "$dbServer", "$dbUser", "$dbPassword" ) or die ( "<b>$dbErrorMessage</b>" );
mysql_select_db( "$dbTable",$db ) or die ( "<b>Unable to select the appropriate database.  Please try again later.</b>" );

/* query the globals table */
$globals = "SELECT globals.*, messages.*, sys.sysuid FROM sms_globals AS globals, sms_messages AS messages, ";
$globals.= "sms_system AS sys WHERE globals.globalid = '1' AND messages.messageid = '1' AND sys.sysid = '1'";
$globalsResult = mysql_query( $globals );

while( $global = mysql_fetch_assoc( $globalsResult ) ) {
	extract( $global, EXTR_OVERWRITE );
}

/* define the version number */
$version = "2.5.5";
$code = $sysuid;

/**
	JP Author Function
**/
function displayAuthors( $missionID, $link ) {

	$sql = "SELECT postAuthor FROM sms_posts WHERE postid = '$missionID' LIMIT 1";
	$result = mysql_query( $sql );
	$myrow = mysql_fetch_array( $result );
	
	/* explode the string at the comma */
	$authors_raw = explode( ",", $myrow['0'] );
	
	/*
		start the loop based on whether there are key/value pairs
		and keep doing something until you run out of pairs
	*/
	foreach( $authors_raw as $key => $value ) {
		
		/* do the database query */
		$sql = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$sql.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$sql.= "WHERE crew.crewid = '$value' AND crew.rankid = rank.rankid";
		$result = mysql_query( $sql );
		
		/* Start pulling the array and populate the variables */
		while( $authorsStart = mysql_fetch_assoc( $result ) ) {
			extract( $authorsStart, EXTR_OVERWRITE );
			
			$authors = array();
			
			if( $link == "link" ) {
				$authors = array(
					"<a href='" . $webLocation . "index.php?page=bio&crew=" . $authorsStart['crewid'] . "'>" . $rankName . " " . $firstName . " " . $lastName . "</a>"
				);
			} else {
				$authors = array(
					$rankName . " " . $firstName . " " . $lastName
				);
			}
			
			$authors_array[] = $authors[0];
			
			$authorsString = implode( " &amp; ", $authors_array );
			
		}	/* close the while loop */
	}	/* close the foreach loop */
	
	echo stripslashes( $authorsString );
		
}
/* END FUNCTION */

/**
	Active Crew Select Menu
**/
function print_active_crew_select_menu( $type, $author, $id, $section, $sub ) {
	
	if( $type != "post" ) {

		if( $type == "pm" ) {
			echo "<select name='" . $type . "Recipient'>";
		} else {
			echo "<select name='" . $type . "Author'>";
		}
		
		$users = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$users.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$users.= "WHERE crew.crewType = 'active' AND crew.rankid = rank.rankid ORDER BY crew.rankid";
		$usersResult = mysql_query( $users );
		
		if( empty( $author ) ) { 
			echo "<option value='0'>No Author Selected</option>";
		}
		
		while( $userFetch = mysql_fetch_assoc( $usersResult ) ) {
			extract( $userFetch, EXTR_OVERWRITE );
				
			if( $author == $userFetch['crewid'] ) {
				echo "<option value='$author' selected>$rankName $firstName $lastName</option>";
			} else {
				echo "<option value='$userFetch[crewid]'>$rankName $firstName $lastName</option>";
			}
		}
	
	echo "</select>";
	
	} elseif( $type == "post" ) {
		
		$authorArray = explode( ",", $author );
		
		$i = 0;
		
		foreach( $authorArray as $key=>$value ) {
			
			echo "<select name='" . $type . "Author" . $i . "'>";
			
			$users = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
			$users.= "FROM sms_crew AS crew, sms_ranks AS rank ";
			$users.= "WHERE crew.crewType = 'active' AND crew.rankid = rank.rankid ORDER BY crew.rankid";
			$usersResult = mysql_query( $users );
			
			while( $userFetch = mysql_fetch_assoc( $usersResult ) ) {
				extract( $userFetch, EXTR_OVERWRITE );
				
				if( in_array( $authorArray[$i], $userFetch ) ) {
					echo "<option value='$authorArray[$i]' selected>$rankName $firstName $lastName</option>";
				} else {
					echo "<option value='$userFetch[crewid]'>$rankName $firstName $lastName</option>";
				}
				
			}
			
			echo "</select>";
			
			/*
				if there are less than 6 array keys, allow a user to add another one
				if there is a second array key, allow a user to delete a user, otherwise don't
			*/
			if( $i < 7 ) {
				echo "&nbsp;";
				if( $_GET['id'] ) {
					echo "<a href='" . $webLocation . "admin.php?page=" . $section . "&sub=" . $sub . "&id=" . $_GET['id'] . "&add=1&postid=" . $id . "'><img src='" . $webLocation . "images/add.png' border='0' alt='Add' /></a>";
				} else {
					echo "<a href='" . $webLocation . "admin.php?page=manage&sub=posts&add=1&postid=" . $id . "'><img src='" . $webLocation . "images/add.png' border='0' alt='Add' /></a>";
				}
			} if( array_key_exists( "1", $authorArray ) ) {
				echo "&nbsp;&nbsp;";
				if( $_GET['id'] ) {
					echo "<a href='" . $webLocation . "admin.php?page=" . $section . "&sub=" . $sub . "&id=" . $_GET['id'] . "&delete=" . $i . "&postid=" . $id . "'><img src='" . $webLocation . "images/remove.png' border='0' alt='Delete' /></a>";
				} else {
					echo "<a href='" . $webLocation . "admin.php?page=" . $section . "&sub=" . $sub . "&delete=" . $i . "&postid=" . $id . "'><img src='" . $webLocation . "images/remove.png' border='0' alt='Delete' /></a>";
				}
			}
			
			/* as long as $i is under 5, keep adding 1 to it */
			if( $i < 7 ) {
				$i = $i +1;
			}
			
			echo "<br />\n";
			
		}
		
		/* count the number of items in the array */
		$authorCount = count( $authorArray );
		
		/* return the array count to be used to put the author string together */
		return $authorCount;
		
	}
	
}
/* END FUNCTION */

/**
	Print out the commanding officer
**/
function printCO() {
	
	$getCO = "SELECT crew.firstName, crew.lastName, rank.rankName ";
	$getCO.= "FROM sms_crew AS crew, sms_ranks AS rank ";
	$getCO.= "WHERE crew.positionid = '1' AND crew.crewType = 'active' ";
	$getCO.= "AND crew.rankid = rank.rankid LIMIT 1";
	$getCOResult = mysql_query( $getCO );
	
	while( $coFetch = mysql_fetch_assoc( $getCOResult ) ) {
		extract( $coFetch, EXTR_OVERWRITE );
	}
	
	return $rankName . " " . $firstName . " " . $lastName;

}
/* END FUNCTION */

/**
	Print out the executive officer
**/
function printXO() {
	
	$getXO = "SELECT crew.firstName, crew.lastName, rank.rankName ";
	$getXO.= "FROM sms_crew AS crew, sms_ranks AS rank ";
	$getXO.= "WHERE crew.positionid = '2' AND crew.rankid = rank.rankid ";
	$getXO.= "AND crew.crewType = 'active' LIMIT 1";
	$getXOResult = mysql_query( $getXO );
	
	while( $xoFetch = mysql_fetch_assoc( $getXOResult ) ) {
		extract( $xoFetch, EXTR_OVERWRITE );
	}
	
	return $rankName . " " . $firstName . " " . $lastName;

}
/* END FUNCTION */

/**
	Print out the commanding officer
**/
function printCOEmail() {
	
	$getCOEmail = "SELECT email FROM sms_crew WHERE positionid = '1' AND ";
	$getCOEmail.= "crewType = 'active' LIMIT 1";
	$getCOEmailResult = mysql_query( $getCOEmail );
	
	while( $coEmailFetch = mysql_fetch_assoc( $getCOEmailResult ) ) {
		extract( $coEmailFetch, EXTR_OVERWRITE );
	}
	
	return $email;

}
/* END FUNCTION */

/**
	Print out the executive officer
**/
function printXOEmail() {
	
	$getXOEmail = "SELECT email FROM sms_crew WHERE positionid = '2' AND ";
	$getXOEmail.= "crewType = 'active' LIMIT 1";
	$getXOEmailResult = mysql_query( $getXOEmail );
	
	while( $xoEmailFetch = mysql_fetch_assoc( $getXOEmailResult ) ) {
		extract( $xoEmailFetch, EXTR_OVERWRITE );
	}
	
	return $email;

}
/* END FUNCTION */

/**
	Print out the commanding officer
**/
function printPlayerPosition( $crewid, $position, $positionNumber ) {
	
	$getPosition = "SELECT position.positionName FROM sms_crew AS crew, sms_positions AS position ";
	$getPosition.= "WHERE crew.crewid = '$crewid' AND crew.positionid$positionNumber = position.positionid ";
	$getPositionResult = mysql_query( $getPosition );
	
	while( $position = mysql_fetch_array( $getPositionResult ) ) {
		extract( $position, EXTR_OVERWRITE );
	}
	
	echo stripslashes( $positionName );

}
/* END FUNCTION */

/**
	JP Author Function for emails
**/
function displayEmailAuthors( $authors, $link ) {

	/* explode the string at the comma */
	$authors_raw = explode( ",", $authors );
	
	/*
		start the loop based on whether there are key/value pairs
		and keep doing 'something' until you run out of pairs
	*/
	foreach( $authors_raw as $key => $value ) {
		
		/* do the database query */
		$sql = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$sql.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$sql.= "WHERE crew.crewid = '$value' AND crew.rankid = rank.rankid";
		$result = mysql_query( $sql );
		
		/* Start pulling the array and populate the variables */
		while( $authorsStart = mysql_fetch_assoc( $result ) ) {
			extract( $authorsStart, EXTR_OVERWRITE );
			
			$authors = array();
			
			if( $link == "link" ) {
				$authors = array(
					"<a href='" . $webLocation . "index.php?page=bio&crew=" . $authorsStart['crewid'] . "'>" . $rankName . " " . $firstName . " " . $lastName . "</a>"
				);
			} else {
				$authors = array(
					$rankName . " " . $firstName . " " . $lastName
				);
			}
			
			$authors_array[] = $authors[0];
			
			$authorsString = implode( " & ", $authors_array );
			
		}	/* close the while loop */
	}	/* close the foreach loop */
	
	return $authorsString;
		
}
/* END FUNCTION */

/**
	Function to pull the mission title
**/
function printMissionTitle( $missionid ) {
	
	/* query the database to get the title */
	$getTitle = "SELECT missionTitle FROM sms_missions WHERE missionid = '$missionid' LIMIT 1";
	$getTitleResult = mysql_query( $getTitle );
	$fetchTitle = mysql_fetch_assoc( $getTitleResult );
	
	/* return the var */
	return $fetchTitle['missionTitle'];

}
/* END FUNCTION */

/**
	Function to pull the mission title
**/
function getCrewEmails( $type ) {
	
	$getEmails = "SELECT crewid, email FROM sms_crew WHERE $type = 'y' AND crewType = 'active' GROUP BY email";
	$getEmailsResult = mysql_query( $getEmails );
	$countEmails = mysql_num_rows( $getEmailsResult );
	
	$recipients = "";
	for( $j=0; $j < $countEmails; $j++ ) {
		$user = mysql_fetch_assoc( $getEmailsResult );
		$u_id = $user['crewid'];
		$u_email = $user['email'];
		if( empty( $recipients ) ) {
			$recipients = $u_email;
		} else {
			$recipients = $recipients . ", " . $u_email;
		}
	}
                        
	return $recipients;

}
/* END FUNCTION */

/**
	Admin function that will pull the user's first name, last name, rank, and rank image
**/
function printCrewNameEmail( $id ) {
	
	$nameFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
	$nameFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
	$nameFetch.= "WHERE crew.crewid = '$id' AND crew.rankid = rank.rankid LIMIT 1";
	$nameFetchResult = mysql_query( $nameFetch );
	
	while( $userFetchArray = mysql_fetch_array( $nameFetchResult ) ) {
		extract( $userFetchArray, EXTR_OVERWRITE );
	
		$name = $rankName . " " . $firstName . " " . $lastName;
		
	}
	
	return $name;
	
}
/** END FUNCTION **/

/**
	Function to scrub the SQL statements for injection
**/
function escape_string( $value )
{
	if( get_magic_quotes_gpc() )
	{
		$value = stripslashes( $value );
	}
	
	if( !is_numeric( $value ) )
	{
		$value = "'" . mysql_real_escape_string( $value ) . "'";
	}
	
	return $value;
	
}
/** END FUNCTION **/

?>