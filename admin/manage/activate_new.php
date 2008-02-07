<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/activate.php
Purpose: Page to manage pending users, posts, logs, and docking requests

System Version: 2.6.0
Last Modified: 2008-02-07 1805 EST
**/

$debug = 0;

if($debug == 1)
{
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

/* access check */
if(
	in_array( "x_approve_users", $sessionAccess ) ||
	in_array( "x_approve_posts", $sessionAccess ) ||
	in_array( "x_approve_logs", $sessionAccess ) ||
	in_array( "x_approve_news", $sessionAccess ) ||
	in_array( "x_approve_docking", $sessionAccess ) ||
	in_array( "m_giveaward", $sessionAccess )
) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	
	if( isset( $_POST ) )
	{
		/* define the POST variables */
		foreach($_POST as $key => $value)
		{
			$$key = $value;
		}
		
		if($action_category == 'user' && in_array('x_approve_users', $sessionAccess))
		{
			switch($action_type)
			{
				case 'accept':
					
					/* get the position type from the database */
					$getPosType = "SELECT positionType FROM sms_positions WHERE positionid = '$position' LIMIT 1";
					$getPosTypeResult = mysql_query( $getPosType );
					$positionType = mysql_fetch_row( $getPosTypeResult );

					/* set the access levels accordingly */
					if( $positionType[0] == "senior" ) {
						$accessID = 3;
					} else {
						$accessID = 4;
					}

					/* pull the default access levels from the db */
					$getGroupLevels = "SELECT * FROM sms_accesslevels WHERE id = $accessID LIMIT 1";
					$getGroupLevelsResult = mysql_query( $getGroupLevels );
					$groups = mysql_fetch_array( $getGroupLevelsResult );
					
					$update = "UPDATE sms_crew SET positionid = %d, crewType = %s, accessPost = %s, ";
					$update.= "accessManage = %s, accessReports = %s, accessUser = %s, accessOthers = %s, ";
					$update.= "rankid = %d, leaveDate = %s WHERE crewid = $action_id LIMIT 1";
					
					$query = sprintf(
						$update,
						escape_string( $position ),
						escape_string( 'active' ),
						escape_string( $groups[1] ),
						escape_string( $groups[2] ),
						escape_string( $groups[3] ),
						escape_string( $groups[4] ),
						escape_string( $groups[5] ),
						escape_string( $rank ),
						escape_string( '' )
					);

					//$result = mysql_query( $query );

					/* update the position they're being given */
					//update_position( $position );

					/** EMAIL THE APPROVAL **/

					/* set the email author */
					$userFetch = "SELECT email FROM sms_crew WHERE crewid = '$action_id' LIMIT 1";
					$userFetchResult = mysql_query( $userFetch );
					$userEmail = mysql_fetch_row( $userFetchResult );

					/* define the variables */
					$to = $userEmail[0] . ", " . printCOEmail();
					$from = printCO() . " < " . printCOEmail() . " >";
					$subject = $emailSubject . " Your Application";

					/* new instance of the replacement class */
					$message = new MessageReplace;
					$message->message = $acceptMessage;
					$message->shipName = $shipPrefix . " " . $shipName;
					$message->player = $action_id;
					$message->rank = $rank;
					$message->position = $position;
					$message->setArray();
					$accept = nl2br( stripslashes( $message->changeMessage() ) );

					/* send the email */
					//mail( $to, $subject, $accept, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
					
					/* optimize the tables */
					optimizeSQLTable( "sms_crew" );
					optimizeSQLTable( "sms_positions" );
					
					break;
				case 'reject':
					break;
			}
		}
		if($action_category == 'post' && in_array('x_approve_posts', $sessionAccess))
		{
			switch($action_type)
			{
				case 'activate':
					break;
				case 'delete':
					break;
			}
		}
	}

	/* get pending users */
	$getPendingUsers = "SELECT crew.crewid, crew.firstName, crew.lastName, position.positionName ";
	$getPendingUsers.= "FROM sms_crew AS crew, sms_positions AS position WHERE ";
	$getPendingUsers.= "crew.positionid = position.positionid AND crewType = 'pending'";
	$getPendingUsersResult = mysql_query( $getPendingUsers );
	$countPendingUsers = mysql_num_rows( $getPendingUsersResult );
	
	/* get pending mission posts */
	$getPendingPosts = "SELECT postid, postTitle FROM sms_posts WHERE postStatus = 'pending'";
	$getPendingPostsResult = mysql_query( $getPendingPosts );
	$countPendingPosts = mysql_num_rows( $getPendingPostsResult );
	
	if($debug == 1)
	{
		echo "<pre>";
		print_r($_POST);
		echo "</pre>";
	}

?>

<script type="text/javascript">
	$(document).ready(function() {
		$('#container-1 > ul').tabs();
		$('.zebra tr:odd').addClass('alt');
		
		$("a[rel*=facebox]").click(function() {
			var id = $(this).attr("myID");
			var type = $(this).attr("myType");
			var action = $(this).attr("myAction");
			
			jQuery.facebox(function() {
				jQuery.get('admin/ajax/activate_' + type + "_" + action + '.php?id=' + id, function(data) {
					jQuery.facebox(data);
				});
			});
			return false;
		});
	});
</script>

<div class="body">
	<span class="fontTitle">Manage Pending Items</span><br /><br />

	<div id="container-1">
		<ul>
			<li><a href="#one"><span>Users (<?=$countPendingUsers;?>)</span></a></li>
			<li><a href="#two"><span>Mission Posts (<?=$countPendingPosts;?>)</span></a></li>
			<li><a href="#three"><span>Personal Logs</span></a></li>
			<li><a href="#four"><span>News Items</span></a></li>
			<li><a href="#five"><span>Awards</span></a></li>
			<?php if($simmType == "starbase") { ?><li><a href="#six"><span>Docking Requests</span></a></li><?php } ?>
		</ul>
	
		<div id="one" class="ui-tabs-container ui-tabs-hide">
			<b class="fontLarge">Pending Users</b><br /><br />
			<table class="zebra" cellpadding="3" cellspacing="0">
				<thead>
					<tr class="fontMedium">
						<th width="35%">Name</th>
						<th width="35%">Position</th>
						<th width="10%"></th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
				</thead>
				
				<?php
				
				/* loop through the results and fill the form */
				while( $pendingUser = mysql_fetch_assoc( $getPendingUsersResult ) ) {
					extract( $pendingUser, EXTR_OVERWRITE );
				
				?>
				<tr class="fontNormal">
					<td><? printText( $pendingUser['firstName'] . " " . $pendingUser['lastName'] ); ?></td>
					<td><? printText( $pendingUser['positionName'] ); ?></td>
					<td align="center"><a href="<?=$webLocation;?>index.php?page=bio&crew=<?=$pendingUser['crewid'];?>"><b>View Bio</b></a></td>
					<td align="center"><a href="#" class="delete" rel="facebox" myID="<?=$pendingUser['crewid'];?>" myType="user" myAction="reject"><b>Reject</b></a></td>
					<td align="center"><a href="#" class="add" rel="facebox" myID="<?=$pendingUser['crewid'];?>" myType="user" myAction="accept"><b>Accept</b></a></td>
				</tr>
				<?php } ?>
				
			</table>
		</div>
		
		<div id="two" class="ui-tabs-container ui-tabs-hide">
			<b class="fontLarge">Pending Mission Posts</b><br /><br />
			<table class="zebra" cellpadding="3" cellspacing="0">
				<thead>
					<tr class="fontMedium">
						<th width="35%">Title</th>
						<th width="35%">Author</th>
						<th width="10%"></th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
				</thead>
				
				<?php
				
				/* loop through the results and fill the form */
				while( $pendingPosts = mysql_fetch_assoc( $getPendingPostsResult ) ) {
					extract( $pendingPosts, EXTR_OVERWRITE );
				
				?>
				<tr class="fontNormal">
					<td><? printText( $pendingPosts['postTitle'] ); ?></td>
					<td><? displayAuthors( $pendingPosts['postid'], 'noLink' ); ?></td>
					<td align="center"><a href="<?=$webLocation;?>index.php?page=post&id=<?=$pendingPosts['postid'];?>"><b>View Post</b></a></td>
					<td align="center"><a href="#" class="delete" rel="facebox" myID="<?=$pendingPosts['postid'];?>" myType="post" myAction="delete"><b>Delete</b></a></td>
					<td align="center"><a href="#" class="add" rel="facebox" myID="<?=$pendingPosts['postid'];?>" myType="post" myAction="activate"><b>Activate</b></a></td>
				</tr>
				<?php } ?>
				
			</table>
		</div>
		<div id="three" class="ui-tabs-container ui-tabs-hide"></div>
		<div id="four" class="ui-tabs-container ui-tabs-hide"></div>
		<div id="five" class="ui-tabs-container ui-tabs-hide"></div>
		<div id="six" class="ui-tabs-container ui-tabs-hide"></div>
	</div>

</div>

<?php } ?>