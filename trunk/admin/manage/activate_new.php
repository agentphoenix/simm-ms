<?php

?>

<script type="text/javascript">
	$(document).ready(function() {
		$('#container-1 > ul').tabs();
		
		$('.zebra tr').mouseover(function() {
			$(this).addClass('over');
		})
		.mouseout(function() {
			$(this).removeClass('over');
		});
		$('.zebra tr:even').addClass('alt');
	});
</script>

<div class="body">
	<span class="fontTitle">Manage Pending Items</span><br /><br />

	<div id="container-1">
		<ul>
			<li><a href="#one"><span>Users</span></a></li>
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
						<th width="30%">Name</th>
						<th width="30%">Position</th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tr>
					<td>Jean-Luc Picard</td>
					<td>Commanding Officer</td>
					<td>View Bio</td>
					<td><a href="#">Deny</a></td>
					<td><a href="#">Approve</a></td>
				</tr>
				<tr>
					<td>William Riker</td>
					<td>Executive Officer</td>
					<td>View Bio</td>
					<td><a href="#">Deny</a></td>
					<td><a href="#">Approve</a></td>
				</tr>
			</table>
		</div>
		
		<div id="two" class="ui-tabs-container ui-tabs-hide"></div>
		<div id="three" class="ui-tabs-container ui-tabs-hide"></div>
		<div id="four" class="ui-tabs-container ui-tabs-hide"></div>
		<div id="five" class="ui-tabs-container ui-tabs-hide"></div>
		<div id="six" class="ui-tabs-container ui-tabs-hide"></div>
	</div>

</div>