<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: framework/classes/postmenu.php
Purpose: Class that generates the menus used on the post pages

System Version: 2.6.0
Last Modified: 2007-11-13 0939 EST
**/

class PostMenu
{
	var $author;		/* the person using the menu at that moment */
	var $type;			/* whether it's a post, log, news item or pm */
	var $action;		/* save, post, add, delete, remove */
	var $status;		/* new, saved, activated, pending */
	var $section;		/* manage, reports, post, user */
	var $subsection;	/* specific page */
	var $id;
	var $crewArray = array();
	var $display = "";
	
	/* function that will dump the active crew into an array to be used by the display function */
	function getCrew()
	{
		$query = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$query.= "FROM sms_crew AS crew, sms_ranks AS rank WHERE crew.crewType = 'active' ";
		$query.= "AND crew.rankid = rank.rankid ORDER BY crew.rankid";
		$result = mysql_query( $query );
		
		while( $fetch = mysql_fetch_array( $result ) ) {
			extract( $fetch, EXTR_OVERWRITE );
			
			$name = $rankName . " " . $firstName . " " . $lastName;
			$this->crewArray[] = array( $crewid, $name );
		}
	}
	
	/* prints the menu out */
	function printMenu( $name )
	{
		$display.= "<select name='" . $name . "'>\n";

		foreach( $this->crewArray as $key => $value )
		{
			$display.= "\t<option value='" . $value[0] . "'>" . stripslashes( $value[1] ) . "</option>\n";
		}
		
		$display.= "</select>\n";
		
		return $display;
	}
	
	function add()
	{}
	
	function remove()
	{}
}

?>