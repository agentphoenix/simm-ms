<?php

require_once( '../framework/functionsGlobal.php' );

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

$join = "INSERT INTO sms_crew ( username, password, crewType, email, realName, aim, msn, yim, icq, ";
$join.= "positionid, rankid, firstName, middleName, lastName, gender, species, heightFeet, heightInches, ";
$join.= "weight, eyeColor, hairColor, age, physicalDesc, personalityOverview, strengths, ambitions, hobbies, ";
$join.= "languages, history, serviceRecord, father, mother, brothers, sisters, spouse, children, ";
$join.= "otherFamily, image, joinDate ) ";
$join.= "VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, ";
$join.= "%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d )";

$today = getdate();

$query = sprintf(
	$join,
	escape_string( $_POST['username'] ),
	escape_string( md5( $_POST['password'] ) ),
	escape_string( 'pending' ),
	escape_string( $_POST['email'] ),
	escape_string( $_POST['realName'] ),
	escape_string( $_POST['aim'] ),
	escape_string( $_POST['msn'] ),
	escape_string( $_POST['yim'] ),
	escape_string( $_POST['icq'] ),
	escape_string( $_POST['positionid'] ),
	escape_string( $_POST['rankid'] ),
	escape_string( $_POST['firstName'] ),
	escape_string( $_POST['middleName'] ),
	escape_string( $_POST['lastName'] ),
	escape_string( $_POST['gender'] ),
	escape_string( $_POST['species'] ),
	escape_string( $_POST['heightFeet'] ),
	escape_string( $_POST['heightInches'] ),
	escape_string( $_POST['weight'] ),
	escape_string( $_POST['eyeColor'] ),
	escape_string( $_POST['hairColor'] ),
	escape_string( $_POST['age'] ),
	escape_string( $_POST['physicalDesc'] ),
	escape_string( $_POST['personalityOverview'] ),
	escape_string( $_POST['strengths'] ),
	escape_string( $_POST['ambitions'] ),
	escape_string( $_POST['hobbies'] ),
	escape_string( $_POST['languages'] ),
	escape_string( $_POST['history'] ),
	escape_string( $_POST['serviceRecord'] ),
	escape_string( $_POST['father'] ),
	escape_string( $_POST['mother'] ),
	escape_string( $_POST['brothers'] ),
	escape_string( $_POST['sisters'] ),
	escape_string( $_POST['spouse'] ),
	escape_string( $_POST['children'] ),
	escape_string( $_POST['otherFamily'] ),
	escape_string( $_POST['image'] ),
	escape_string( $today[0] )
);

echo $query;

?>