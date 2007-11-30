<?php

/**

This is a test file for dynamic substitution of variables for
acceptance and rejection emails. This will not land until SMS 2.6!

Special thanks to Sam Drori of Pump Relationship Technologies, LLC
for helping set up the framework for this code

**/

session_start();

require_once( '../framework/functionsGlobal.php' );

class MessageReplace
{

	var $message;
	var $substitute;
	var $shipName;
	var $player;
	var $rank;
	var $position;
	
	/* this function runs in the setArray() function to substitute values for the array */
	function substitute()
	{
		
		/* get the rank */
		$getRank = "SELECT rankName FROM sms_ranks WHERE rankid = '$this->rank' LIMIT 1";
		$getRankResult = mysql_query( $getRank );
		$fetchRank = mysql_fetch_array( $getRankResult );
		$this->rank = $fetchRank[0];
		
		/* get the position */
		$getPos = "SELECT positionName FROM sms_positions WHERE positionid = '$this->position' LIMIT 1";
		$getPosResult = mysql_query( $getPos );
		$fetchPos = mysql_fetch_array( $getPosResult );
		$this->position = $fetchPos[0];
		
		/* get the first and last names */
		$getPlayer = "SELECT firstName, lastName FROM sms_crew WHERE crewid = '$this->player' LIMIT 1";
		$getPlayerResult = mysql_query( $getPlayer );
		$fetchPlayer = mysql_fetch_array( $getPlayerResult );
		$this->player = $fetchPlayer[0] . " " . $fetchPlayer[1];
	
	}
	
	/* run the substitute() function and then build array for substitution */
	function setArray()
	{
		
		$this->substitute();
	
		$this->substitute = array(
			"player" => $this->player,
			"ship" => $this->shipName,
			"rank" => $this->rank,
			"position" => $this->position
		);
	
	}
	
	/* change the message based on the values */
	function changeMessage()
	{
		$result = $this->message;

		/* iterate over the substitute values and replace each one in the result string */
		foreach( $this->substitute as $key => $value ) {
			if( strpos( $result, "#" . $key . "#" ) !== FALSE ) {
				$result = str_replace( "#" . $key . "#", $value, $result );
			}
		}

		return $result;
	}

}

$message = new MessageReplace;
$message->message = $acceptMessage;
$message->shipName = $shipPrefix . " " . $shipName;
$message->player = 1; // will be POST variable
$message->rank = 8; // will be POST variable
$message->position = 1; // will be POST variable
$message->setArray();
echo nl2br( $message->changeMessage() );

?>