<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: pages/manifestFull.php
Purpose: Provides a full listing from the database of the active crew, available 
	positions, and non-playing characters on the simm.

System Version: 2.5.0
Last Modified: 2007-04-30 2005 EST
**/

/* define the page class and vars */
$pageClass = "personnel";
$display = $_GET['disp'];

/* pull in the main navigation */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* set the rank variable */
if( isset( $sessionCrewid ) ) {
	$rankSet = $sessionDisplayRank;
} else {
	$rankSet = $rankSet;
}

/* if there is no GET variable, set it to the players manifest */
if( !$display ) {
	$display = "crew";
}

?>

<div class="body">
	<span class="fontTitle">
	<?
		if( $display == "crew" ) {
			echo "Crew Manifest";
		} elseif( $display == "open" ) {
			echo "Open Positions";
		} elseif( $display == "past" ) {
			echo "Departed Crew Manifest";
		} elseif( $display == "npcs" ) {
			echo "NPC Manifest";
		}
	?>
	</span><br /><br />
	
	<!-- manifest navigation table -->
	<div align="center">
	<span class="fontSmall">
		<a href="<?=$webLocation;?>index.php?page=manifestFull">Crew Manifest</a>
		&nbsp; &middot; &nbsp;
		<a href="<?=$webLocation;?>index.php?page=manifest&disp=open">Open Positions</a>
		&nbsp; &middot; &nbsp;
		<a href="<?=$webLocation;?>index.php?page=manifest&disp=past">Departed Crew</a>
	</span>
	</div>
	
	<?
	
	$departmentsQuery = "SELECT deptid, deptName, deptColor, deptType FROM sms_departments ";
	$departmentsQuery.= "WHERE deptDisplay = 'y' ORDER BY deptOrder ASC";
	$departments = mysql_query( $departmentsQuery );
	$d_num_rows = mysql_num_rows( $departments );
	
	?>
	
	<table>
	
	<?
	
	for( $i = 0; $i < $d_num_rows; $i++ ) {
		$department = mysql_fetch_assoc( $departments );
		/* assigning the variables */
		$d_id = $department['deptid'];
		$d_name = $department['deptName'];
		$d_color = $department['deptColor'];
		$d_type = $department['deptType'];
	
	?>
	
		<tr>
			<td colspan="4" height="15"></td>
		</tr>
		<tr>
			<td colspan="4">
				<font class="fontMedium" color="#<?=$d_color;?>">
					<b><? printText( $d_name ); ?></b>
				</font>
			</td>
		</tr>
	
	<?
	
		$positionsQuery = "SELECT positionid, positionName, positionDept, positionOpen ";
		$positionsQuery.= "FROM sms_positions WHERE positionDept = '$d_id' AND ";
		$positionsQuery.= "positionDisplay = 'y' ORDER BY positionOrder ASC";
		$positions = mysql_query( $positionsQuery );
		$p_num_rows = mysql_num_rows( $positions );
	
		for( $k = 0; $k < $p_num_rows; $k++ ) {
			$positionX = mysql_fetch_assoc( $positions );
			$p_id = $positionX['positionid'];
			$p_position = $positionX['positionName'];
			$p_department = $positionX['positionDept'];
			$p_open = $positionX['positionOpen'];
			
			$usersQuery = "SELECT crewid, firstName, lastName, gender, species, rankid, loa ";
			$usersQuery.= "FROM sms_crew WHERE crewType = 'active' AND ( positionid = '$p_id' ";
			$usersQuery.= "OR positionid2 = '$p_id' ) ORDER BY rankid ASC";
			$users = mysql_query( $usersQuery );
			$u_num_rows = mysql_num_rows( $users );
			
			if( $u_num_rows > "0" ) {
				for( $j=0; $j<$u_num_rows; $j++ ) {
					$user = mysql_fetch_assoc( $users );
					$u_id = $user['crewid'];
					$u_firstname = $user['firstName'];
					$u_lastname = $user['lastName'];
					$u_gender = $user['gender'];
					$u_species = $user['species'];
					$u_rid = $user['rankid'];
					$u_loa = $user['loa'];
					
					$rankQuery = "SELECT rankName, rankImage FROM sms_ranks WHERE rankid = '$u_rid'";
					$rankfetch = mysql_query( $rankQuery );
					$rankX = mysql_fetch_row( $rankfetch );
					$u_rank = $rankX[0];
					$u_rankimage = $rankX[1];
	
	?>
	
		<tr>
			<td width="35%" valign="middle" style="padding-left: 1em;"><? printText( $p_position ); ?></td>
			<td width="15%" valign="middle" align="right">
				<? if( !empty( $u_rankimage ) ) { ?>
					<img src="<?=$webLocation;?>images/ranks/<?=$rankSet;?>/<?=$u_rankimage;?>" />
				<? } else { ?>
					<img src="<?=$webLocation;?>images/ranks/<?=$rankSet;?>/blank.png" />
				<? } ?>
			</td>
			<td width="40%" valign="middle">
				<span class="fontSmall">
					<b><? printText( $u_rank . " " . $u_firstname . " " . $u_lastname ); ?></b><br />
					<? printText( $u_species . " " . $u_gender ); ?>
				</spann>
			</td>
			<td width="10%" valign="middle">
				<a href="<?=$webLocation;?>index.php?page=bio&crew=<?=$u_id;?>">
				
				<? if($u_loa == 1) { ?>
					<img src="images/combadge-loa.jpg" border="0" />
				<? } elseif($u_loa == 2) { ?>
					<img src="images/combadge-eloa.jpg" border="0" />
				<? } else { ?>
					<img src="images/combadge.jpg" border="0" />
				<? } ?>
				</a>
			</td>
		</tr>
	
	<?
	
				} /* close the crew for loop */
			} /* close the if( $u_num_rows ) logic */
			
			$npcQuery = "SELECT crewid, firstName, lastName, gender, species, rankid FROM sms_crew ";
			$npcQuery.= "WHERE crewType = 'npc' AND ( positionid = '$p_id' OR positionid2 = '$p_id' ) ";
			$npcQuery.= "ORDER BY rankid ASC";
			$npcs = mysql_query( $npcQuery );
			$n_num_rows = mysql_num_rows( $npcs );
			
			if( $n_num_rows > "0" ) {
				for( $j=0; $j<$n_num_rows; $j++ ) {
					$npc = mysql_fetch_assoc( $npcs );
					$n_id = $npc['crewid'];
					$n_firstname = $npc['firstName'];
					$n_lastname = $npc['lastName'];
					$n_gender = $npc['gender'];
					$n_species = $npc['species'];
					$n_rid = $npc['rankid'];
					
					$rankQuery = "SELECT rankName, rankImage FROM sms_ranks WHERE rankid = '$n_rid'";
					$rankfetch = mysql_query( $rankQuery );
					$rankX = mysql_fetch_row( $rankfetch );
					$n_rank = $rankX[0];
					$n_rankimage = $rankX[1];
					
	?>
	
		<tr>
			<td width="35%" valign="middle" style="padding-left: 1em;"><? printText( $p_position );?></td>
			<td width="15%" valign="middle" align="right">
				<img src="<?=$webLocation;?>images/ranks/<?=$rankSet;?>/<?=$n_rankimage;?>" />
			</td>
			<td width="40%" valign="middle">
				<span class="fontSmall">
					<b><? printText( $n_rank . " " . $n_firstname . " " . $n_lastname ); ?></b><br />
					<? printText( $n_species . " " . $n_gender ); ?>
				</span>
			</td>
			<td width="10%" valign="middle">
				<a href="<?=$webLocation;?>index.php?page=bio&crew=<?=$n_id;?>">
					<img src="images/combadge-npc.jpg" border="0" />
				</a>
			</td>
		</tr>
	
	<?
	
				} /* close the NPC for loop */
			} /* close the if( $n_num_row ) logic */
			
			if( $p_open > "0" && $d_type == "playing" ) {
			
	?>
	
		<tr>
			<td width="35%" valign="middle" style="padding-left: 1em;"><? printText( $p_position ); ?></td>
			<td width="15%" valign="middle" align="right">
				<img src="<?=$webLocation;?>images/ranks/<?=$rankSet;?>/blank.png" />
			</td>
			<td width="40%" valign="middle">
				<span class="fontSmall">
					<a href="<?=$webLocation;?>index.php?page=join&position=<?=$p_id;?>"><b>Position Available - Apply Now!</b></a>
				</span>
			</td>
			<td width="10%" valign="middle">&nbsp;</td>
		</tr>
	
	<?
	
			} /* close the if( $p_open) logic */
		} /* close the positions for loop */
	} /* close the departments loop */
	
	?>
	
	</table>
	
</div>