<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/manifest.php
Purpose: Provides a listing from the database of either the active crew, the 
	inactive crew (departed players), available positions, or non-playing characters 
	on the simm.

System Version: 2.6.0
Last Modified: 2007-12-27 0950 EST
**/

if( $manifestDisplay == "full" && $_GET['disp'] == "crew" ) {
	include_once( 'pages/manifestFull.php' );
} else {

/* define the page class and set vars */
$pageClass = "personnel";

if( isset( $_GET['disp'] ) ) {
	$display = $_GET['disp'];
} else {
	$display = "";
}

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

if( $display == "crew" || $display == "open" ) {
	$departments = "SELECT * FROM sms_departments WHERE deptDisplay = 'y' ";
	$departments.= "AND deptType = 'playing' ORDER BY deptOrder ASC";
} elseif( $display == "npcs" || $display == "past" ) {
	$departments = "SELECT * FROM sms_departments WHERE deptDisplay = 'y' ORDER BY deptOrder ASC";
}

$deptResults = mysql_query( $departments );

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
		<? if( $manifestDisplay == "full" ) { ?>
		<a href="<?=$webLocation;?>index.php?page=manifestFull&disp=crew">Crew Manifest</a>
		&nbsp; &middot; &nbsp;
		<? } else { ?>
		<a href="<?=$webLocation;?>index.php?page=manifest&disp=crew">Crew Manifest</a>
		&nbsp; &middot; &nbsp;
		<? } if( $manifestDisplay == "split" ) { ?>
		<a href="<?=$webLocation;?>index.php?page=manifest&disp=npcs">NPC Manifest</a>
		&nbsp; &middot; &nbsp;
		<? } ?>
		<a href="<?=$webLocation;?>index.php?page=manifest&disp=open">Open Positions</a>
		&nbsp; &middot; &nbsp;
		<a href="<?=$webLocation;?>index.php?page=manifest&disp=past">Departed Crew</a>
	</span>
	</div>

	<table>
	<?

	/* pull the data out of the department query */
	while ( $dept = mysql_fetch_array( $deptResults ) ) {
		extract( $dept, EXTR_OVERWRITE );
		
	?>
		<tr>
			<td colspan="4" height="15"></td>
		</tr>
		<tr>
			<td colspan="4">
				<font class="fontMedium" color="#<?=$deptColor;?>">
					<b><? printText( $deptName ); ?></b>
				</font>
			</td>
		</tr>
		<?
		
		if( $display == "crew" ) {
		
			$manifest = "SELECT position.positionName, position.positionDept, crew.crewid, ";
			$manifest.= "crew.firstName, crew.lastName, crew.rankid, crew.species, crew.gender, ";
			$manifest.= "crew.loa, rank.rankImage, rank.rankName FROM sms_positions AS position, ";
			$manifest.= "sms_crew AS crew, sms_ranks AS rank WHERE ";
			$manifest.= "position.positionDept = '$dept[deptid]' AND ( position.positionid = crew.positionid ";
			$manifest.= "OR position.positionid = crew.positionid2 ) AND position.positionDisplay = 'y' AND ";
			$manifest.= "crew.rankid = rank.rankid AND crew.crewType = 'active' ORDER BY position.positionOrder, rank.rankid ASC";
			$manifestResults = mysql_query( $manifest );
		
		} elseif( $display == "open" ) {
		
			$manifest = "SELECT position.positionid, position.positionName, position.positionOpen, ";
			$manifest.= "position.positionDept FROM sms_positions AS position, sms_departments AS dept ";
			$manifest.= "WHERE position.positionDept = '$dept[deptid]' AND position.positionOpen > '0' ";
			$manifest.= "AND position.positionDisplay = 'y' AND dept.deptid = '$dept[deptid]' AND ";
			$manifest.= "dept.deptType = 'playing' ORDER BY position.positionOrder ASC";
			$manifestResults = mysql_query( $manifest );
		
		} elseif( $display == "npcs" ) {
		
			$manifest = "SELECT position.positionName, position.positionid, position.positionDept, ";
			$manifest.= "position.positionOpen, crew.gender, crew.species, ";
			$manifest.= "crew.crewid, crew.firstName, crew.lastName, crew.rankid, crew.positionid, ";
			$manifest.= "rank.rankid, rank.rankImage, rank.rankName, dept.deptType FROM ";
			$manifest.= "sms_positions AS position, sms_crew AS crew, sms_ranks AS rank, ";
			$manifest.= "sms_departments AS dept WHERE position.positionDept = '$dept[deptid]' ";
			$manifest.= "AND ( position.positionid = crew.positionid OR position.positionid = crew.positionid2 ) ";
			$manifest.= "AND position.positionDisplay = 'y' AND crew.rankid = rank.rankid AND ";
			$manifest.= "crew.crewType = 'npc' AND dept.deptid = '$dept[deptid]' ";
			$manifest.= "ORDER BY position.positionOrder, rank.rankid ASC";
			$manifestResults = mysql_query( $manifest );
		
		} elseif( $display == "past" ) {
		
			$manifest = "SELECT position.positionName, crew.crewid, crew.firstName, crew.lastName, crew.species, ";
			$manifest.= "crew.gender, rank.rankImage, rank.rankName FROM sms_positions AS position, ";
			$manifest.= "sms_crew AS crew, sms_ranks AS rank WHERE position.positionDept = '$dept[deptid]' AND ";
			$manifest.= "( position.positionid = crew.positionid OR position.positionid = crew.positionid2 ) ";
			$manifest.= "AND position.positionDisplay = 'y' AND crew.rankid = rank.rankid ";
			$manifest.= "AND crewType = 'inactive' ORDER BY position.positionOrder, rank.rankid ASC";
			$manifestResults = mysql_query( $manifest );
		
		}
		
		while ( $manifestList = mysql_fetch_assoc($manifestResults) ) {
			extract( $manifestList, EXTR_OVERWRITE );
		
		?>
		<tr>
			<td width="35%" valign="middle" style="padding-left: 1em;"><? printText( $positionName ); ?></td>
			<td width="15%" valign="middle" align="right">
				<?
					if( $display == "open" ) {
						echo "<img src='" . $webLocation . "images/ranks/" . $rankSet . "/blank.png' />";
					} else {
						if( !empty( $rankImage ) ) {
							echo "<img src='" . $webLocation . "images/ranks/" . $rankSet . "/" . $rankImage . "' />";
						} else {
							echo "<img src='" . $webLocation . "images/ranks/" . $rankSet . "/blank.png' />";
						}
					}
				?>
			</td>
			<td width="40%" valign="middle">
				<?
					if( $display == "npcs" ) {
						echo "<font class='fontSmall'><b>";
						printText( $rankName . " " . $firstName . " " . $lastName );
						echo "</b></font>";
						echo "<br />";
						echo "<font class='fontSmall'>";
						printText( $species . " " . $gender );
						echo "</font>";
						
						if( $deptType == "playing" && $positionOpen > "0" ) {
							echo "<br />";
							echo "<font class='fontSmall'><a href='" . $webLocation . "index.php?page=join&position=" . $positionid . "'>Position Available - Apply Now</a></font>";
						}
						
					} elseif( $display == "open" ) {
						echo "<font class='fontSmall'><a href='" . $webLocation . "index.php?page=join&position=" . $positionid . "'>Position Available - Apply Now</a></font>";
					} elseif( $display =="crew" || "past" ) {
						echo "<font class='fontSmall'><b>";
						printText( $rankName . " " . $firstName . " " . $lastName );
						echo "</b></font>";
						echo "<br />";
						echo "<font class='fontSmall'>";
						printText( $species . " " . $gender );
						echo "</font>";
					}
				?>
			</td>
			<td width="10%" valign="middle">
				<?
					if( $display == "npcs" ) {
						echo "<a href='" . $webLocation . "index.php?page=bio&crew=" . $crewid . "'><img src='" . $webLocation . "images/combadge-npc.jpg' border='0' class='image' /></a>";
					} elseif( $display == "open" ) {
						echo "";
					} elseif( $display == "crew" ) {
						if( $loa == "1" ) {
							echo "<a href='" . $webLocation . "index.php?page=bio&crew=" . $crewid . "'><img src='" . $webLocation . "images/combadge-loa.jpg' border='0' class='image' /></a>";
						} elseif( $loa == "2" ) {
							echo "<a href='" . $webLocation . "index.php?page=bio&crew=" . $crewid . "'><img src='" . $webLocation . "images/combadge-eloa.jpg' border='0' class='image' /></a>";
						} elseif( $loa == "0" ) {
							echo "<a href='" . $webLocation . "index.php?page=bio&crew=" . $crewid . "'><img src='" . $webLocation . "images/combadge.jpg' border='0' class='image' /></a>";
						}
					} elseif( $display == "past" ) {
						echo "<a href='" . $webLocation . "index.php?page=bio&crew=" . $crewid . "'><img src='" . $webLocation . "images/combadge.jpg' border='0' class='image' /></a>";
					}
				?>
			</td>
		</tr>
	<? } } ?>
	</table>
</div>
<? } ?>