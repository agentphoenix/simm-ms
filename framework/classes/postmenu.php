<?php

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: framework/classes/postmenu.php
Purpose: Class that generates the menus used on the post pages

System Version: 2.6.0
Last Modified: 2007-12-17 0043 EST
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
	var $display = "";
	
	var $crewArray = array();
	var $deptArray = array();
	
	var $temp;
	
	/* function that will dump the active crew into an array to be used by the display function */
	function getinfo()
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

		if( $this->status == "saved" )
		{
			switch( $this->type )
			{
				case 'mission':
					$table = "sms_posts";
					$field = "postid";
					break;
				case 'log':
					$table = "sms_personallogs";
					$field = "logid";
					break;
				case 'news':
					$table = "sms_news";
					$field = "newsid";
					break;
			}
			
			/* gets the data on the saved post */
			$query = "SELECT * FROM $table WHERE $field = '$this->id' LIMIT 1";
			$result = mysql_query( $query );
			$fetch = mysql_fetch_array( $result );
			$authors = explode( ",", $fetch[1] );
			
			/* creates a marker for a member of the crew involved in the jp */
			foreach( $this->crewArray as $k => $v )
			{
				if( in_array( $v[0], $authors ) )
				{
					$this->crewArray[$k][] = "yes";
				}
			}
		}
		
		if( $this->type == "pm" )
		{
			$query = "SELECT deptid, deptName FROM sms_departments ";
			$query.= "WHERE deptDisplay = 'y' ORDER BY deptOrder ASC";
			$result = mysql_query( $query );
		
			while( $fetch = mysql_fetch_array( $result ) ) {
				extract( $fetch, EXTR_OVERWRITE );
			
				$this->deptArray[] = array( $deptid, $deptName );
			}
		}
	}
	
	/* prints the menu out */
	function printMenu( $name )
	{
		$this->getInfo();
		
		$display = "\n<select name='" . $name . "'>\n";
			
			/*
				groups that can receive CCed PMs:
					Departments (needs to respect the deptDisplay flag)
					Senior Staff
					Command Staff (CO & XO)
			*/
			
			/* default option */
			$display.= "\t<option value='0'>No Author Selected</option>\n";
			
			/* if the post is a PM, groups and departments should be displayed as well */
			if( $this->type == "pm" )
			{
				$display.= "<optgroup label='Groups'>\n";
				$display.= "\t<option value='group_command'>Command Staff</option>\n";
				$display.= "\t<option value='group_senior'>Department Heads</option>\n";
				$display.= "</optgroup>\n";
				
				$display.= "<optgroup label='Departments'>\n";
				foreach( $this->deptArray as $key1 => $value1 )
				{
					$display.= "\t<option value='dept_" . $value1[0] . "'>" . stripslashes( $value1[1] ) . "</option>\n";
				}
				$display.= "</optgroup>\n";
				
				$display.= "<optgroup label='Crew'>\n";
			}
			
			foreach( $this->crewArray as $key2 => $value2 )
			{
				if( array_key_exists( 2, $value2 ) )
				{
					$recipient = " checked";
				}
				else
				{
					$recipient = "";
				}
				
				$display.= "\t<option value='" . $value2[0] . "'" . $recipient . ">" . stripslashes( $value2[1] ) . "</option>\n";
			}
			
			if( $this->type == "pm" ) { $display.= "</optgroup>\n"; }
		
		$display.= "</select>\n";
		
		return $display;
	}
}

?>