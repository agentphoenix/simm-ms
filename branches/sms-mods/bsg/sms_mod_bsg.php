<?php
/*
|---------------------------------------------------------------
| BATTLESTAR GALACTICA SMS MOD
|---------------------------------------------------------------
|
| File: sms_mod_bsg.php
| Author: David VanScott [davidv@anodyne-productions.com]
| Version: 1.0
| Last Update: 2008.08.19 2328 EST
|
| Official Battlestar Galactica mod for SMS 2.6
|
*/

require_once 'framework/functionsGlobal.php';
include_once 'framework/functionsUtility.php';

if (isset($_GET['step']) && is_numeric($_GET['step']))
{
	$step = $_GET['step'];
}
else
{
	$step = 1;
}

/*
|---------------------------------------------------------------
| Step 1 - Instructions
| Step 2 - Remove departments, positions, and ranks data
| Step 3 - Add departments, positions, and ranks tables
| Step 4 - Add departments, positions, and ranks data
|---------------------------------------------------------------
*/
switch ($step)
{
	case 1:
		echo "The official Anodyne Battlestar Galactica mod will turn your installation of SMS into a bristling Colonial fleet vessel, complete with new departments, positions, and ranks that make it easier to play a BSG game and take all the work out of modding the system yourself. Before you begin, make sure you have removed all DS9 ranks from your <em>images/ranks</em> directory on your server and uploaded the two rank sets found in the zip archive.<br /><br />The first step of this mod will remove all your department, position, and rank data from SMS. The second step will re-add the department, positions, and ranks tables with the new BSG data. The third step will insert the departments, positions, and ranks into the database.<br /><br />Click the link below to continue to the next step.<br /><br /><strong><a href='sms_mod_bsg.php?step=2'>Next Step &raquo;</a></strong>";
		break;
	case 2:
		mysql_query('DROP TABLE sms_departments');
		mysql_query('DROP TABLE sms_positions');
		mysql_query('DROP TABLE sms_ranks');
		
		echo "Your old departments, positions, and ranks tables have been removed. The next step will re-create those tables with the appropriate default values. Click the link below to continue to the next step.<br /><br /><strong><a href='sms_mod_bsg.php?step=3'>Next Step &raquo;</a></strong>";
		
		break;
	case 3:
		mysql_query("CREATE TABLE sms_departments ( 
			`deptid` INT(3) not null AUTO_INCREMENT,
			`deptName` VARCHAR(32) not null DEFAULT '',
			`deptDesc` TEXT not null,
			`deptOrder` INT(3) not null default '0',
			`deptDisplay` ENUM('y','n') not null DEFAULT 'y',
			`deptType` ENUM('playing','nonplaying') not null DEFAULT 'playing',
			`deptColor` VARCHAR(6) not null DEFAULT 'ffffff',
			`deptClass` INT(3) not null default '0',
			`deptDatabaseUse` enum('y','n') NOT NULL default 'y',
			PRIMARY KEY `deptid` (`deptid`) ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
		
		sleep(1);
		
		mysql_query("CREATE TABLE sms_positions ( 
			`positionid` INT(3) not null AUTO_INCREMENT,
			`positionName` VARCHAR(64) not null DEFAULT '',
			`positionDesc` TEXT not null,
			`positionDept` INT(3) not null default '0',
			`positionOrder` INT(3) not null default '0',
			`positionOpen` INT(2) not null default '1',
			`positionDisplay` ENUM('y','n') DEFAULT 'y',
			`positionType` ENUM('senior','crew') DEFAULT 'crew',
			`positionMainPage` enum('y','n') NOT NULL default 'n',
			PRIMARY KEY `positionid` (`positionid`) ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
		
		sleep(1);
		
		mysql_query("CREATE TABLE sms_ranks ( 
			`rankid` INT(3) AUTO_INCREMENT,
			`rankName` VARCHAR(32) not null DEFAULT '',
			`rankShortName` VARCHAR(32) not null DEFAULT '',
			`rankImage` VARCHAR(255) not null DEFAULT '',
			`rankOrder` INT(2) not null default '0',
			`rankDisplay` ENUM('y','n') not null DEFAULT 'y',
			`rankClass` INT(3) not null default '0',
			`rankType` int(1) NOT NULL default '1',
			PRIMARY KEY `rankid` (`rankid`) ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
				
		echo "You have successfully created the new new departments, positions, and ranks tables that will be used by SMS. The next step will populate those tables with the BSG data. Click the link below to continue to the next step.<br /><br /><strong><a href='sms_mod_bsg.php?step=4'>Next Step &raquo;</a></strong>";
	
		break;
	case 4:
		mysql_query("INSERT INTO `sms_departments` (`deptName`, `deptDesc`, `deptOrder`, `deptClass`) VALUES
			('Command', 'The Command Department consists of the Commander and the Executive Officer. The Commander is ultimately responsible for the safety and welfare of the entire crew. S/he has final authority on all decisions regarding the ship and her mission. The Executive officer or XO is the commander\'s immediate subordinate, and is also his/her successor should the need arise.', 0, 1),
			('Combat Information Center Staff', 'The CIC Staff consists of the FTL officers, techs and various other systems techs that keep a battlestar and her systems running smoothly.', 1, 1),
			('Viper Wing', 'The Viper Wing is responsible for engaging the enemy in ship to ship battles, as well as providing escort for military vessels.', 2, 1),
			('Raptor Wing', 'The Raptor Wing often takes on jobs of reconnaissance, rescue, scouting, and transportation.', 3, 1),
			('Hangar Deck Staff', 'The Hangar Deck Staff repairs Vipers and Raptors between missions.', 4, 1),
			('Medical', 'The medical department is responsible for the physical health of the crew, from running annual physicals to treating a wide variety of wounds and diseases.', 5, 1),
			('Engineering', 'The engineering department has the enormous task of keeping the ship working; they are responsible for making repairs, fixing problems, and making sure that the ship is ready for anything.', 6, 1),
			('Marine Detachment', 'A Marine\'s duties include guarding the CIC and the brig as well as other critical areas on the ship, and assisting the Master-at-Arms and are part of Raptor boarding parties. They are also responsible for stopping enemy boarding actions.', 7, 2),
			('Civilians', 'Civilians fill positions that are not related to the Colonial military. Their jobs may help serve military forces in some form.', 7, 3)");
		
		sleep(1);
		
		mysql_query("INSERT INTO `sms_positions` (`positionName`, `positionDesc`, `positionDept`, `positionOrder`, `positionOpen`, `positionType`) VALUES 
		('Commanding Officer', 'Ultimately responsible for the ship and crew, the Commanding Officer is the most senior officer aboard a vessel. S/he is responsible for carrying out the orders of the President, and for representing the Colonial Fleet.', 1, 0, 1, 'senior'),
		('Executive Officer', 'The liaison between captain and crew, the Executive Officer acts as the disciplinarian, personnel manager, advisor to the captain, and much more. S/he is also one of only two officers, along with the Chief Medical Officer, that can remove a Commanding Officer from duty.', 1, 1, 1, 'senior'),
		('Officer of the Deck', 'The OOD, or Officer of the Deck monitors the CIC\'s operation in the absence of the ship\'s commanding officer. The OOD generally carries out or relays the command officer\'s standing orders. In the absence of a command officer, the OOD has the conn, but typically calls the commanding officer before taking any action if time allows.', 2, 0, 1, 'senior'),
		('Tactical Officer', 'The Tactical Officer is tasked with the monitoring of DRADIS and coordinating various command and control functionality, including computer control, the Tactical Officer must relay changes in status and keep the commander updated continuously during the fluid events of battle. The Tactical officer is typically the first to know that an attack is imminent and will address the Battlestar by the public address system to go to battle stations through Condition One or Two alerts.\r\n\r\nTactical officer must manually print or offload data from the various central computers s/he monitors (Fire control, Navigation, FTL, and mainframe computers) and relay this information to the other officers and staff in the room. Fortunately, many stations see the same information on displays similar to those at the Tactical Station, but it is the Tactical Officer who is charged with notifying the commander of the changes and interpreting the results. The Tactical Officer also is the administrator for all central computers onboard and provides maintenance as required.\r\n\r\nWhile the Helm officers drive the ship, it is the Tactical officer that plots FTL jumps, the apparently instantaneous leap from one location in space to another location millions of kilometers away. The Tactical officer not only has to provide Jump coordinates to the battlestar\'s helm, but also relay them if other ships are accompanying the Battlestar.', 2, 1, 4, 'crew'),
		('Communications Officer', 'Communications Officers monitors, directs or relays communications to and from fighters and other ships. In coordination with the Tactical Station, the Communications officer can also verify transponders that register as friendly, and alerts the Tactical Officer or commander if they pick up signals without transponders or recognized enemy transponders. The Communications Officer has a link to the mainframe computer, where a library of Colonial recognition information resides.', 2, 2, 1, 'crew'),
		('Helm Control Officer', 'Navigation is managed by spatial coordinates based on DRADIS and other sensor information. The helm crewmembers drive the battlestar through a series of controls and based on commands from the Executive officer or commanding officer.', 2, 3, 1, 'crew'),
		('Damage Control Officer', 'A Damage Control officer can perform many actions to repair or mitigate the effects of an enemy attack through the controls here, including the venting of compartments, coordination of damage control teams, and the like.', 2, 4, 1, 'crew'),
		('Weapons Control Officer', 'The Weapons Control Officer manages the battlestar\'s gun batteries and other defensive controls. In the event that the Weapons Control Room or CIC is knocked offline or its crew incapacitated, control of the ship\'s batteries can be managed at Auxiliary Fire Control.', 2, 5, 1, 'crew'),
		('Commander, Air Group', 'The Officer in charge of the Viper Wing aboard a battlestar. S/he conducts preflight briefings, is traditionally the lead pilot and is responsible for the Viper pilots as well as the Raptor pilots aboard the ship.', 3, 0, 1, 'senior'),
		('Squadron Leader', 'The Squadron Leader directs his or her lower ranking pilots in the heat of battle. The Squadron leader answers directly to the CAG.', 3, 1, 5, 'crew'),
		('Viper Pilot', 'Pilots are officers in the Colonial Fleet that trained and qualified to operate a Viper fighter. A Viper Pilot’s main function is to engage in military operations that have been prearranged by superior officers and take on enemy fighters that are attempting to destroy a ship.', 3, 5, 15, 'crew'),
		('Raptor Wing Leader', 'The Raptor Wing Leader works directly with the CAG on future rescue and military operations that Raptors may be needed for.', 4, 0, 1, 'senior'),
		('Raptor Pilot', 'Raptor pilots undertake short and medium-range scans to detect electromagnetic, heat or other signatures from other vessels, scan planetary surfaces for signs of life, energy output, or to locate and assess mineral deposits, and scout ahead of its parent warship in other planetary or celestial systems for any signs of hostile intent or stellar conditions prior to the parent ship\'s arrival. Raptor pilots also undertake search & rescue operations after an engagement with enemy forces.', 4, 1, 1, 'crew'),
		('Electronic Countermeasures Officer', 'An ECO, or Electronic Countermeasures Officer, is responsible for the electronic countermeasures on a Raptor. ECOs also operate computer equipment, including scanning and detection equipment. ECOs are also trained to fly a Raptor in case the primary pilot is incapacitated or unavailable.', 4, 5, 10, 'crew'),
		('Chief of the Deck', 'The Deck Chief is responsible the overall repair and readiness of all combat spacecraft on a battlestar.', 5, 0, 1, 'senior'),
		('Landing Signal Officer', 'The Landing Signal Officer (LSO) is the officer who is responsible for all flight operations on the flight pods of battlestars and other military vessels. This includes the landing of all vessels, from Vipers and Raptors to small liners, as well as the operation of the launch tubes.', 5, 5, 5, 'crew'),
		('Deckhand', 'Deckhands are multi-faceted crewmembers on battlestars who prepare and maintain Colonial fighters and reconnaissance vehicles for flight and turnaround.', 5, 10, 15, 'crew'),
		('Chief Engineering Officer', 'The Chief Engineer is responsible for the condition of all systems and equipment on board a battlestar or facility. S/he oversees maintenance, repairs and upgrades of all equipment. S/he is also responsible for the many repairs teams during crisis situations. The Chief Engineer is not only the department head but also a senior officer, responsible for all the crew members in her/his department and maintenance of the duty rosters.', 7, 0, 1, 'senior'),
		('Engineering Officer', 'There are several non-specialized engineers aboard of each vessel. They are assigned to their duties by the Chief Engineer and his Assistant, performing a number of different tasks as required, i.e. general maintenance and repair. Generally, engineers as assigned to more specialized engineering person to assist in there work is so requested by the specialized engineer.', 7, 2, 1, 'crew'),
		('Communications Specialist', 'This engineer maintains all the communication systems throughout the battlestar.', 7, 5, 5, 'crew'),
		('Chief Medical Officer', 'The Chief Medical Officer is responsible for the physical health of the entire crew, but does more than patch up injured crew members. His/her function is to ensure that they do not get sick or injured to begin with, and to this end monitors their health and conditioning with regular check ups. If necessary, the Chief Medical Officer can remove anyone from duty, even a Commanding Officer. Besides this s/he is available to provide medical advice to any individual who requests it.\r\n\r\nS/he also is a department head and a member of the Senior Staff and responsible for all the crew members in her/his department and duty rosters.', 6, 0, 1, 'senior'),
		('Medical Officer', 'Medical Officer undertake the majority of the work aboard the ship/facility, examining the crew, and administering medical care under the instruction of the Chief Medical Officer. Medical Officers also run the other Medical areas not directly overseen by the Chief Medical Officer.', 6, 5, 3, 'crew'),
		('Medic', 'S/he is responsible for providing first aid and trauma care on the battlefield.', 6, 10, 5, 'crew'),
		('Master-At-Arms', 'The Master-at-Arms is a non-commissioned officer responsible for internal security aboard Colonial warships, including battlestars. ', 8, 0, 1, 'senior'),
		('Marine', 'The Colonial Marine Corps is a branch of the Colonial Forces tasked with ground combat operations and ship-board security.', 8, 1, 10, 'crew'),
		('Priest', 'Priests also preside over military funerals, without regard for the beliefs of the deceased. Priests in the Twelve Colonies are apparently not required to practice celibacy, and can be male or female.', 0, 0, 1, 'crew')");
		
		sleep(1);
		
		mysql_query("INSERT INTO `sms_ranks` (`rankName`, `rankShortName`, `rankImage`, `rankOrder`, `rankClass`) VALUES 
			('Admiral', 'ADM', 'a4.png', 0, 1),
			('Rear-Admiral', 'RADM', 'a3.png', 1, 1),
			('Commander', 'CMDR', 'a2.png', 2, 1),
			('Colonel', 'COL', 'a1.png', 3, 1),
			('Major', 'MAJ', 'o5.png', 4, 1),
			('Captain', 'CAPT', 'o4.png', 5, 1),
			('Lieutenant', 'LT', 'o3.png', 6, 1),
			('Lieutenant JG', 'LT(JG)', 'o2.png', 7, 1),
			('Ensign', 'EN', 'o1.png', 8, 1),
			('Master Chief Petty Officer', 'MCPO', 'e9.png', 9, 1),
			('Senior Chief Petty Officer', 'SCPO', 'e8.png', 10, 1),
			('Chief Petty Officer', 'CPO', 'e7.png', 11, 1),
			('Petty Officer, 1st Class', 'PO1', 'e6.png', 12, 1),
			('Petty Officer, 2nd Class', 'PO2', 'e5.png', 13, 1),
			('Petty Officer, 3rd Class', 'PO3', 'e4.png', 14, 1),
			('Crewman Specialist', 'SPEC', 'e3.png', 15, 1),
			('Deckhand', 'DECK', 'e2.png', 16, 1),
			('Recruit', 'REC', 'e1.png', 17, 1),
			('', '', 'blank.png', 18, 1),
			('Sergeant Major', 'SGTM', 'e8.png', 0, 2),
			('Master Sergeant', 'MSGT', 'e7.png', 1, 2),
			('Gunnery Sergeant', 'GSGT', 'e6.png', 2, 2),
			('Staff Sergeant', 'SSGT', 'e5.png', 3, 2),
			('Sergeant', 'SGT', 'e4.png', 4, 2),
			('Corporal', 'CORP', 'e3.png', 5, 2),
			('Private, 1st Class', 'PVT1', 'e2.png', 6, 2),
			('Private', 'PVT', 'e1.png', 7, 2),
			('', '', 'blank.png', 8, 2)
		");
		
		echo "You have successfully inserted the data into the tables! You can now begin to use your BSG SMS site.<br /><br />We recommend that you delete this file now.";
	
		break;
}

?>