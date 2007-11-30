<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: framework/utility.php
Purpose: Page with the class that is called by the system to check for the
	successful execution of a SQL query and display the appropriate
	messages

System Version: 2.5.2
Last Modified: 2007-08-06 1638 EST
**/

class QueryCheck
{

	/* set the class variables */
	var $result;
	var $image;
	var $message;
	var $query;

	/* check the query coming through */
	function checkQuery( $result, $query ) {
		
		/*
			if the query is empty (AKA they haven't hit submit yet)
			the result will be a blank string, otherwise, it'll be
			the query which will be evaluated to see if the next 2
			functions should be run or not
		*/
		$this->query = $query;
		
		/*
			evaluate if the result is good or bad and set the
			result and image variables accordingly
		*/
		if( !empty( $result ) ) {
			$this->result = TRUE;
			$this->image = "images/update.png";
		} else {
			$this->result = FALSE;
			$this->image = "images/fail.png";
		}

	} /* close checkQuery() */

	/* set the message that will be displayed */
	function message( $object, $action ) {
		
		/* verb array */
		$verbs = array(
			0 => 'site globals',
			1 => 'site messages',
			2 => 'specifications',
			3 => 'site options',
			4 => 'private messages',
			5 => 'player access levels',
			6 => 'crew access levels'
		);
		
		/* verb tense logic */
		if( in_array( $object, $verbs ) ) {
			$verb = "were";
		} elseif( $object == "chain of command" ) {
			$verb = "position was";
		} else {
			$verb = "was";
		}
		
		/* action array */
		$actions = array(
			'activate' => array( 'activation', 'activated' ),
			'update' => array( 'update', 'updated' ),
			'delete' => array( 'deletion', 'deleted' ),
			'create' => array( 'creation', 'created' ),
			'reject' => array( 'rejection', 'rejected' ),
			'approve' => array( 'approval', 'approved' ),
			'add' => array( 'addition', 'added' ),
			'remove' => array( 'removal', 'removed' ),
			'save' => array( 'save', 'saved' ),
			'send' => array( 'send', 'sent' ),
			'post' => array( 'post', 'posted' ),
			'submit' => array( 'submit', 'submitted' ),
			'deactivate' => array( 'deactivation', 'deactivated' ),
			'reset' => array( 'reset', 'reset' ),
		);
		
		/* take the neccessary action based on whether the result of the query is TRUE or FALSE */
		if( $this->result == TRUE ) {
			
			/* define the successful message */
			$this->message = ucfirst( $object ) . " " . $verb . " successfully " . $actions[$action]['1'] . "!";
			
			/* this phrase should be added for skin and rank set updates */
			if( $object == "skin" || $object == "rank set" ) {
				$this->message .= " Please refresh this page or navigate to a new page to see your changes.";
			}
			
			/* this phrase should be added for skin and rank set updates */
			if( $object == "site globals" ) {
				$this->message .= " Some changes to the Site Globals require menu changes as well. Please check the <a href='admin.php?page=manage&sub=menugeneral'>menu management</a> page for instructions on any menu changes that need to be made.";
			}
			
		} else {
			
			/* define the unsuccessful message */
			$this->message = ucfirst( $actions[$action]['0'] ) . " failed! " . ucfirst( $object ) . " " . $verb . " not successfully " . $actions[$action]['1'] . ".";
			
			/*
				the phrase should be different in the event that an account update has failed
				since it's likely that the user has messed up a password
			*/
			if( $object == "account" ) {
				$this->message .= " If you are trying to update your username, real name, or email address, please make sure you have included your current password. If you are trying to reset your password, please make sure that both passwords match and try again.";
			} else {
				$this->message .= "<br /><br />If this problem persists, please use the <a href='http://forums.anodyne-productions.com/' target='_blank'>Anodyne Support Forums</a> for more help. To expediate the support process, please copy and paste the following query into your support request, making sure to substitute sensitive information, such as passwords, with an *.<br /><br />" . $this->query;
			}
			
		} /* close the logic */

	} /* close message() */

	/* display all the information */
	function display() {

		echo "<div class='update'>";
			echo "<img src='" . $this->image . "' border='0' alt='' style='float:left; padding: 0 6px 0 0;' />";
			echo $this->message;
		echo "</div>";
		echo "<br />";

	} /* close display() */

} /* close the class */

class FirstLaunch
{

	/* set the class variables */
	var $status;
	var $version;
	var $summary;
	
	/* pulls the system launch status */
	function checkStatus() {
	
		/* query the database */
		$query = "SELECT sysLaunchStatus FROM sms_system WHERE sysid = '1'";
		$result = mysql_query( $query );
		$fetch = mysql_fetch_array( $result );
		
		/* update the status variable */
		$this->status = $fetch[0];
	
	} /* close checkStatus() */
	
	/* gather the info */
	function gather() {
	
		$query = "SELECT sys.*, ver.* FROM sms_system AS sys, sms_system_versions AS ver ";
		$query.= "WHERE sys.sysid = '1' AND sys.sysVersion = ver.version LIMIT 1";
		$result = mysql_query( $query );
		$fetch = mysql_fetch_assoc( $result );
		
		$this->version = $fetch['sysVersion'];
		$this->summary = $fetch['versionShortDesc'];
	
	} /* close gather() */
	
	/* print out the info */
	function display() {
		
		echo "<br /><br />";
		echo "<div class='update'>";
			echo "<img src='images/launch.png' border='0' alt='' style='float:left; padding: 0 12px 0 0;' />";
			echo "<span class='fontTitle'>SMS First Launch</span><br /><br />";
			echo "Congratulations, this is your first time launching SMS " . $this->version . "! " . $this->summary;
			echo "<br /><br />";
			echo "For a complete listing of new features and bug fixes, please view the <a href='admin.php?page=reports&sub=history'>version history</a>.";
		echo "</div>";
	
	} /* close display() */
	
	/* update the launch field */
	function update() {
	
		$query = "UPDATE sms_system SET sysLaunchStatus = 'y' WHERE sysid = '1'";
		$result = mysql_query( $query );
	
	} /* close update() */

} /* close the FirstLaunch class */

?>