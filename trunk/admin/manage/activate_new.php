<?php

/* get pending users */
$getPendingUsers = "SELECT crew.crewid, crew.firstName, crew.lastName, position.positionName ";
$getPendingUsers.= "FROM sms_crew AS crew, sms_positions AS position WHERE ";
$getPendingUsers.= "crew.positionid = position.positionid AND crewType = 'pending'";
$getPendingUsersResult = mysql_query( $getPendingUsers );
$countPendingUsers = mysql_num_rows( $getPendingUsersResult );

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
			<li><a href="#two"><span>Mission Posts</span></a></li>
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
		
		<div id="two" class="ui-tabs-container ui-tabs-hide"></div>
		<div id="three" class="ui-tabs-container ui-tabs-hide"></div>
		<div id="four" class="ui-tabs-container ui-tabs-hide"></div>
		<div id="five" class="ui-tabs-container ui-tabs-hide"></div>
		<div id="six" class="ui-tabs-container ui-tabs-hide"></div>
	</div>

</div>