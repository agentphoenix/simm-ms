<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
$variable = "full";

require_once('../framework/functionsGlobal.php');

?>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title>HIDE</title>
		
		<script type="text/javascript" src="../framework/js/jquery.js"></script>
		
		<style type="text/css">
			.active { background-color: lightgreen; }
			.inactive { background-color: lightblue; }
			.pending { background-color: yellow; }
			.npc { background-color: purple; }
		</style>
	</head>
	<body>
		<script type="text/javascript">
			$(document).ready(function() {
				$('tr.inactive').hide();
				
				$('#all').click(function() {
					$('tr.inactive').hide();
					$('tr.active').show();
					$('tr.npc').show();
					return false;
				});
				
				$('#active').click(function() {
					$('tr.inactive').hide();
					$('tr.active').show();
					$('tr.npc').hide();
					return false;
				});
				
				$('#npc').click(function() {
					$('tr.inactive').hide();
					$('tr.npc').show();
					$('tr.active').hide();
					return false;
				});
				
				$('#inactive').click(function() {
					$('tr.active').hide();
					$('tr.npc').hide();
					$('tr.inactive').show();
					return false;
				});
			});
		</script>
		
		<p>
			<a href="#" id="all">Show All</a>
			&middot;
			<a href="#" id="active">Show PCs</a>
			&middot;
			<a href="#" id="npc">Show NPCs</a>
			&middot;
			<a href="#" id="inactive">Show Previous</a>
		</p>
		
		<table width="600" border="1" cellspacing="5" cellpadding="5">
			
		<?php
		
		$departments = "SELECT * FROM sms_departments WHERE deptDisplay = 'y' ORDER BY deptOrder ASC";
		$deptResults = mysql_query( $departments );

		while ( $dept = mysql_fetch_array( $deptResults ) ) {
			extract( $dept, EXTR_OVERWRITE );

			$manifest = "SELECT position.positionName, position.positionDept, crew.crewid, ";
			$manifest.= "crew.firstName, crew.lastName, crew.rankid, crew.species, crew.gender, ";
			$manifest.= "crew.loa, crew.crewType, rank.rankImage, rank.rankName FROM sms_positions AS position, ";
			$manifest.= "sms_crew AS crew, sms_ranks AS rank WHERE ";
			$manifest.= "position.positionDept = '$dept[deptid]' AND ( position.positionid = crew.positionid ";
			$manifest.= "OR position.positionid = crew.positionid2 ) AND position.positionDisplay = 'y' AND ";
			$manifest.= "crew.rankid = rank.rankid AND crew.crewType != 'pending' ORDER BY position.positionOrder, rank.rankid ASC";
			$manifestResults = mysql_query( $manifest );
		
			while ( $fetch = mysql_fetch_assoc($manifestResults) ) {
				extract( $fetch, EXTR_OVERWRITE );
				
		?>
			<tr class="<?=$fetch['crewType'];?>">
				<td><?=$fetch['firstName'] . " " . $fetch['lastName'];?></td>
				<td><?=$fetch['positionName'];?></td>
				<td><?=$fetch['rankName'];?></td>
			</tr>
			<?php } } ?>
		</table>
	</body>
</html>