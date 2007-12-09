<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/manage/specifications.php
Purpose: Page that moderates the specs

System Version: 2.5.0
Last Modified: 2007-06-18 0914 EST
**/

/* access check */
if( in_array( "m_specs", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$action = $_POST['action_update_x'];
	
	if( $action ) {
		
		/* define the POST variables */
		$shipClass = $_POST['shipClass'];
		$shipRole = $_POST['shipRole'];
		$duration = $_POST['duration'];
		$durationUnit = addslashes( $_POST['durationUnit'] );
		$refit = $_POST['refit'];
		$refitUnit = addslashes( $_POST['refitUnit'] );
		$resupply = $_POST['resupply'];
		$resupplyUnit = addslashes( $_POST['resupplyUnit'] );
		$length = $_POST['length'];
		$width = $_POST['width'];
		$height = $_POST['height'];
		$decks = $_POST['decks'];
		$complimentEmergency = $_POST['complimentEmergency'];
		$complimentOfficers = $_POST['complimentOfficers'];
		$complimentEnlisted = $_POST['complimentEnlisted'];
		$complimentMarines = $_POST['complimentMarines'];
		$complimentCivilians = $_POST['complimentCivilians'];
		$warpCruise = addslashes( $_POST['warpCruise'] );
		$warpMaxCruise = addslashes( $_POST['warpMaxCruise'] );
		$warpEmergency = addslashes( $_POST['warpEmergency'] );
		$warpMaxTime = addslashes( $_POST['warpMaxTime'] );
		$warpEmergencyTime = addslashes( $_POST['warpEmergencyTime'] );
		$phasers = addslashes( $_POST['phasers'] );
		$torpedoLaunchers = addslashes( $_POST['torpedoLaunchers'] );
		$torpedoCompliment = addslashes( $_POST['torpedoCompliment'] );
		$defensive = addslashes( $_POST['defensive'] );
		$shields = addslashes( $_POST['shields'] );
		$shuttlebays = $_POST['shuttlebays'];
		$hasShuttles = $_POST['hasShuttles'];
		$hasRunabouts = $_POST['hasRunabouts'];
		$hasFighters = $_POST['hasFighters'];
		$hasTransports = $_POST['hasTransports'];
		$shuttles = addslashes( $_POST['shuttles'] );
		$runabouts = addslashes( $_POST['runabouts'] );
		$fighters = addslashes( $_POST['fighters'] );
		$transports = addslashes( $_POST['transports'] );
		
		/* do the update query */
		$updateSpecs = "UPDATE sms_specs SET ";
		$updateSpecs.= "shipClass = '$shipClass', shipRole = '$shipRole', duration = '$duration', ";
		$updateSpecs.= "durationUnit = '$durationUnit', refit = '$refit', refitUnit = '$refitUnit', ";
		$updateSpecs.= "resupply = '$resupply', resupplyUnit = '$resupplyUnit', length = '$length', ";
		$updateSpecs.= "width = '$width', height = '$height', decks = '$decks', complimentEmergency = '$complimentEmergency', ";
		$updateSpecs.= "complimentOfficers = '$complimentOfficers', complimentEnlisted = '$complimentEnlisted', ";
		$updateSpecs.= "complimentMarines = '$complimentMarines', complimentCivilians = '$complimentCivilians', ";
		$updateSpecs.= "warpCruise = '$warpCruise', warpMaxCruise = '$warpMaxCruise', warpEmergency = '$warpEmergency', ";
		$updateSpecs.= "warpMaxTime = '$warpMaxTime', warpEmergencyTime = '$warpEmergencyTime', ";
		$updateSpecs.= "phasers = '$phasers', torpedoLaunchers = '$torpedoLaunchers', torpedoCompliment = '$torpedoCompliment', ";
		$updateSpecs.= "defensive = '$defensive', shields = '$shields', shuttlebays = '$shuttlebays', ";
		$updateSpecs.= "hasShuttles = '$hasShuttles', hasRunabouts = '$hasRunabouts', hasFighters = '$hasFighters', ";
		$updateSpecs.= "shuttles = '$shuttles', runabouts = '$runabouts', fighters = '$fighters', hasTransports = '$hasTransports', transports = '$transports' ";
		$updateSpecs.= "WHERE specid = '1' LIMIT 1";
		$result = mysql_query( $updateSpecs );
		
		/* optimize table */
		optimizeSQLTable( "sms_specs" );
		
	}
	
	$getSpecs = "SELECT * FROM sms_specs WHERE specid = '1'";
	$getSpecsResult = mysql_query( $getSpecs );
	
	while( $specFetch = mysql_fetch_array( $getSpecsResult ) ) {
		extract( $specFetch, EXTR_OVERWRITE );
	}
	
	/* strip the slashes from the vars */
	$durationUnit = stripslashes( $durationUnit );
	$refitUnit = stripslashes( $refitUnit );
	$resupplyUnit = stripslashes( $resupplyUnit );
	$warpCruise = stripslashes( $warpCruise );
	$warpMaxCruise = stripslashes( $warpMaxCruise );
	$warpEmergency = stripslashes( $warpEmergency );
	$warpMaxTime = stripslashes( $warpMaxTime );
	$warpEmergencyTime = stripslashes( $warpEmergencyTime );
	$phasers = stripslashes( $phasers );
	$torpedoLaunchers = stripslashes( $torpedoLaunchers );
	$torpedoCompliment = stripslashes( $torpedoCompliment );
	$defensive = stripslashes( $defensive );
	$shields = stripslashes( $shields );
	$shuttles = stripslashes( $shuttles );
	$runabouts = stripslashes( $runabouts );
	$fighters = stripslashes( $fighters );

?>

	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $updateSpecs );
		
		if( !empty( $check->query ) ) {
			$check->message( "specifications", "update" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle"><?=ucwords( $simmType );?> Specifications</span>
			
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=specifications">
			<table>
				<tr>
					<td colspan="3" height="15"></td>
				</tr>
				<tr>
					<td colspan="3" class="fontMedium"><b>Ship Information</b></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Ship Class</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="shipClass" maxlength="50" value="<?=$shipClass;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Ship Role</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="shipRole" maxlength="80" value="<?=$shipRole;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Expected Duration</td>
					<td>&nbsp;</td>
					<td>
						<input type="text" class="text" name="duration" size="3" maxlength="3" value="<?=$duration;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Duration Unit<br />(Years, Months, etc.)</td>
					<td>&nbsp;</td>
					<td>
						<input type="text" class="text" name="durationUnit" maxlength="80" value="<?=$durationUnit;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Time Before Refit</td>
					<td>&nbsp;</td>
					<td>
						<input type="text" class="text" name="refit" size="3" maxlength="3" value="<?=$refit;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Refit Unit<br />(Years, Months, etc.)</td>
					<td>&nbsp;</td>
					<td>
						<input type="text" class="text" name="refitUnit" maxlength="80" value="<?=$refitUnit;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Time Before Resupply</td>
					<td>&nbsp;</td>
					<td>
						<input type="text" class="text" name="resupply" size="3" maxlength="3" value="<?=$resupply;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Resupply Unit<br />(Years, Months, etc.)</td>
					<td>&nbsp;</td>
					<td>
						<input type="text" class="text" name="resupplyUnit" maxlength="80" value="<?=$resupplyUnit;?>" />
					</td>
				</tr>
				
				<tr>
					<td colspan="3" height="15">
				</tr>
				
				<tr>
					<td colspan="3" class="fontMedium"><b>Dimensions</b></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Length</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="length" size="5" maxlength="5" value="<?=$length;?>" /> Meters</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Width</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="width" size="5" maxlength="5" value="<?=$width;?>" /> Meters</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Height</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="height" size="5" maxlength="5" value="<?=$height;?>" /> Meters</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Decks</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="decks" size="5" maxlength="5" value="<?=$decks;?>" /></td>
				</tr>
				
				<tr>
					<td colspan="3" height="15">
				</tr>
				
				<tr>
					<td colspan="3" class="fontMedium">
						<b>Personnel Information</b><br />
						<span class="fontSmall">Leave blank if your simm doesn't have the specific type of personnel</span>
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Officers</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="complimentOfficers" size="6" maxlength="6" value="<?=$complimentOfficers;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Enlisted Officers</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="complimentEnlisted" size="6" maxlength="6" value="<?=$complimentEnlisted;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Marines</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="complimentMarines" size="6" maxlength="6" value="<?=$complimentMarines;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Civilians</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="complimentCivilians" size="6" maxlength="6" value="<?=$complimentCivilians;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Emergency Capacity</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="complimentEmergency" size="8" maxlength="8" value="<?=$complimentEmergency;?>" /></td>
				</tr>
				
				<tr>
					<td colspan="3" height="15">
				</tr>
				
				<tr>
					<td colspan="3" class="fontMedium"><b>Speed Information</b></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Standard Velocity</td>
					<td>&nbsp;</td>
					<td>Warp <input type="text" class="text" name="warpCruise" size="8" maxlength="8" value="<?=$warpCruise;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Maximum Velocity</td>
					<td>&nbsp;</td>
					<td>
						Warp <input type="text" class="text" name="warpMaxCruise" size="8" maxlength="8" value="<?=$warpMaxCruise;?>" />
						<input type="text" class="text" name="warpMaxTime" maxlength="20" value="<?=$warpMaxTime;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Emergency Velocity</td>
					<td>&nbsp;</td>
					<td>
						Warp <input type="text" class="text" name="warpEmergency" size="8" maxlength="8" value="<?=$warpEmergency;?>" />
						<input type="text" class="text" name="warpEmergencyTime" maxlength="20" value="<?=$warpEmergencyTime;?>" />
					</td>
				</tr>
				
				<tr>
					<td colspan="3" height="15">
				</tr>
				
				<tr>
					<td colspan="3" class="fontMedium"><b>Offensive &amp; Defensive Systems</b></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Phasers</td>
					<td>&nbsp;</td>
					<td><textarea name="phasers" rows="5" class="textArea"><?=stripslashes( $phasers );?></textarea></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Torpedo Launchers</td>
					<td>&nbsp;</td>
					<td><textarea name="torpedoLaunchers" rows="5" class="textArea"><?=stripslashes( $torpedoLaunchers );?></textarea></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Torpedo Compliment</td>
					<td>&nbsp;</td>
					<td><textarea name="torpedoCompliment" rows="5" class="textArea"><?=stripslashes( $torpedoCompliment );?></textarea></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Defensive Systems</td>
					<td>&nbsp;</td>
					<td><textarea name="defensive" rows="5" class="textArea"><?=stripslashes( $defensive );?></textarea></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Shields</td>
					<td>&nbsp;</td>
					<td><textarea name="shields" rows="5" class="textArea"><?=stripslashes( $shields );?></textarea></td>
				</tr>
				
				<tr>
					<td colspan="3" height="15">
				</tr>
				
				<tr>
					<td colspan="3" class="fontMedium"><b>Auxiliary Craft</b></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Shuttlebays</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="shuttlebays" size="3" maxlength="3" value="<?=$shuttlebays;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Ship Has Shuttles?</td>
					<td>&nbsp;</td>
					<td>
						<input type="radio" id="shutY" name="hasShuttles" value="y" <? if( $hasShuttles == "y" ) { echo "checked"; } ?>/> <label for="shutY">Yes</label>
						<input type="radio" id="shutN" name="hasShuttles" value="n" <? if( $hasShuttles == "n" ) { echo "checked"; } ?>/> <label for="shutN">No</label>
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Shuttles</td>
					<td>&nbsp;</td>
					<td><textarea name="shuttles" rows="5" class="textArea"><?=$shuttles;?></textarea></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Ship Has Runabouts?</td>
					<td>&nbsp;</td>
					<td>
						<input type="radio" id="runY" name="hasRunabouts" value="y" <? if( $hasRunabouts == "y" ) { echo "checked"; } ?>/> <label for="runY">Yes</label>
						<input type="radio" id="runN" name="hasRunabouts" value="n" <? if( $hasRunabouts == "n" ) { echo "checked"; } ?>/> <label for="runN">No</label>
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Runabouts</td>
					<td>&nbsp;</td>
					<td><textarea name="runabouts" rows="5" class="textArea"><?=$runabouts;?></textarea></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Ship Has Fighters?</td>
					<td>&nbsp;</td>
					<td>
						<input type="radio" id="fightersY" name="hasFighters" value="y" <? if( $hasFighters == "y" ) { echo "checked"; } ?>/> <label for="fightersY">Yes</label>
						<input type="radio" id="fightersN" name="hasFighters" value="n" <? if( $hasFighters == "n" ) { echo "checked"; } ?>/> <label for="fightersN">No</label>
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Fighters</td>
					<td>&nbsp;</td>
					<td><textarea name="fighters" rows="5" class="textArea"><?=$fighters;?></textarea></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Ship Has Transports?</td>
					<td>&nbsp;</td>
					<td>
						<input type="radio" id="tranY" name="hasTransports" value="y" <? if( $hasTransports == "y" ) { echo "checked"; } ?>/> <label for="tranY">Yes</label>
						<input type="radio" id="tranN" name="hasTransports" value="n" <? if( $hasTransports == "n" ) { echo "checked"; } ?>/> <label for="tranN">No</label>
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Transports</td>
					<td>&nbsp;</td>
					<td><textarea name="transports" rows="5" class="textArea"><?=$transports;?></textarea></td>
				</tr>
				
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td colspan="2"></td>
					<td><input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" /></td>
				</tr>
			</table>
		</form>
	</div>
	
<? } else { errorMessage( "specifications management" ); } ?>