<?php
/*
|---------------------------------------------------------------
| ENTERPRISE SMS MOD
|---------------------------------------------------------------
|
| File: sms_mod_bsg.php
| Author: David VanScott [davidv@anodyne-productions.com]
| Version: 1.0
|
| Official Enterprise mod for SMS 2.6
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
		echo "The official Anodyne Enterprise mod will turn your installation of SMS into an early Starfleet vessel, complete with new departments, positions, and ranks that make it easier to play a Enterprise game and take all the work out of modding the system yourself. Before you begin, make sure you have removed all DS9 ranks from your <em>images/ranks</em> directory on your server and uploaded the two rank sets found in the zip archive.<br /><br />The first step of this mod will remove all your department, position, and rank data from SMS. The second step will re-add the department, positions, and ranks tables with the new Enterprise data. The third step will insert the departments, positions, and ranks into the database.<br /><br />Click the link below to continue to the next step.<br /><br /><strong><a href='sms_mod_ent.php?step=2'>Next Step &raquo;</a></strong>";
		break;
	case 2:
		mysql_query('DROP TABLE sms_departments');
		mysql_query('DROP TABLE sms_positions');
		mysql_query('DROP TABLE sms_ranks');
		
		mysql_query("UPDATE sms_globals SET allowedRanks = 'default,dress', rankSet = 'default' WHERE globalid = 1 LIMIT 1");
		
		mysql_query("UPDATE sms_crew SET displayRank = 'default' WHERE crewType = 'active'");
		
		echo "Your old departments, positions, and ranks tables have been removed. The next step will re-create those tables with the appropriate default values. Click the link below to continue to the next step.<br /><br /><strong><a href='sms_mod_ent.php?step=3'>Next Step &raquo;</a></strong>";
		
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
				
		echo "You have successfully created the new new departments, positions, and ranks tables that will be used by SMS. The next step will populate those tables with the Enterprise data. Click the link below to continue to the next step.<br /><br /><strong><a href='sms_mod_ent.php?step=4'>Next Step &raquo;</a></strong>";
	
		break;
	case 4:
		mysql_query("INSERT INTO `sms_departments` (`deptName`, `deptDesc`, `deptOrder`, `deptColor`, `deptClass`) VALUES
			('Command', 'The Command department is ultimately responsible for the ship and its crew, and those within the department are responsible for commanding the vessel and representing the interests of Starfleet.', 0, 'c08429', 1),
			('Helm', 'Responsible for the navigation and flight control of a vessel and its auxiliary craft, the Helm division includes pilots trained in both starship and auxiliary craft piloting.', 1, 'c08429', 1),
			('Armory', 'Merging the responsibilities of ship to ship and personnel combat into a single department, the armory division is responsible for the tactical readiness of the vessel and the security of the ship.', 2, '9c2c2c', 2),
			('Engineering', 'The engineering division has the enormous task of keeping the ship working; they are responsible for making repairs, fixing problems, and making sure that the ship is ready for anything.', 3, '9c2c2c', 2),
			('Science', 'From sensor readings to figuring out a way to enter the strange spacial anomaly, the science division is responsible for recording data, testing new ideas out, and making discoveries.', 4, '008080', 3),
			('Medical', 'The medical division is responsible for the mental and physical health of the crew, from running annual physicals to combatting a strange plague that is afflicting the crew to helping a crew member deal with the loss of a loved one.', 5, '008080', 3),
			('Communications', 'The Communications Corps is responsible for the operation of the Starfleet\'s communications systems. On many ships the Communications department is simply amalgamated with Operations; it is often only on Flagships (where a large amount of communications traffic can be received in a very short space of time) and Starbases (where there is an extremely large amount of communications traffic at almost all times).', 6, '008080', 3),
			('MACO Detachment', 'When the standard security detail is not enough, MACOs come in and clean up; the MACO detachment is a powerful tactical addition to any ship, responsible for partaking in personal combat from sniping to melee.', 7, '666666', 4)");
		
		sleep(1);
		
		mysql_query("INSERT INTO `sms_positions` (`positionName`, `positionDesc`, `positionDept`, `positionOrder`, `positionOpen`, `positionType`) VALUES 
		('Commanding Officer', 'Ultimately responsible for the ship and crew, the Commanding Officer is the most senior officer aboard a vessel. S/he is responsible for carrying out the orders of Starfleet, and for representing both Starfleet and the Federation.', 1, 0, 1, 'senior'),
		('Executive Officer', 'The liaison between captain and crew, the Executive Officer acts as the disciplinarian, personnel manager, advisor to the captain, and much more. S/he is also one of only two officers, along with the Chief Medical Officer, that can remove a Commanding Officer from duty.', 1, 1, 1, 'senior'),
		('Chief of the Boat', 'The senior-most Chief Petty Officer (including Senior and Master Chiefs), regardless of rating, is designated by the Commanding Officer as the Chief of the Boat (for vessels) or Command Chief (for starbases). In addition to his or her departmental responsibilities, the COB/CC performs the following duties: serves as a liaison between the Commanding Officer (or Executive Officer) and the enlisted crewmen; ensures enlisted crews understand Command policies; advises the Commanding Officer and Executive Officer regarding enlisted morale, and evaluates the quality of noncommissioned officer leadership, management, and supervisory training.\r\n\r\nThe COB/CC works with the other department heads, Chiefs, supervisors, and crewmen to insure discipline is equitably maintained, and the welfare, morale, and health needs of the enlisted personnel are met. The COB/CC is qualified to temporarily act as Commanding or Executive Officer if so ordered.', 1, 2, 1, 'crew'),
		('Chief Helm Officer', 'Helm incorporates two job, Navigation and flight control. A Helm Officer must always be present on the bridge of a starship. S/he plots courses, supervises the computers piloting, corrects any flight deviations and pilots the ship manually when needed. The Chief Helm Officer is the senior most Helm Officer aboard, serving as a Senior Officer, and chief of the personnel under him/her.', 2, 0, 1, 'senior'),
		('Helm Officer', 'Helm incorporates two job, Navigation and flight control. A Helm Officer must always be present on the bridge of a starship, and every vessel has a number of Helm Officers to allow shift rotations. S/he plots courses, supervises the computers piloting, corrects any flight deviations and pilots the ship manually when needed. Helm Officers report to the Chief Helm Officer.', 2, 1, 5, 'crew'),
		('Chief Armory Officer', 'Her/his duty is to ensure the safety of ship and crew. Some take it as their personal duty to protect the Commanding Officer/Executive Officer on away teams. She/he is also responsible for people under arrest and the safety of guests, liked or not. S/he also is a department head and a member of the senior staff, responsible for all the crew members in her/his department and duty rosters.', 3, 0, 1, 'senior'),
		('Armory Officer', 'There are several Armory Officers aboard each vessel. They are assigned to their duties by the Armory Chief and mostly guard sensitive areas, protect people, patrol, and handle other threats to the Federation.', 3, 1, 5, 'crew'),
		('Security Investigations Officer', 'The Security Investigations Officer is an Enlisted Officer. S/He fulfills the role of a special investigator or detective when dealing with Starfleet matters aboard ship or on a planet. Coordinates with the Chief Armory Officer on all investigations as needed. The Security Investigations Officer reports to the Chief Armory Officer.', 3, 5, 2, 'crew'),
		('Brig Officer', 'The Brig Officer is an Armory Officer who has chosen to specialize in a specific role. S/he guards the brig and its cells. But there are other duties associated with this post as well. S/he is responsible for any prisoner transport, and the questioning of prisoners.', 3, 10, 5, 'crew'),
		('Master-at-Arms', 'The Master-at-Arms trains and supervises Armory crewmen in departmental operations, repairs, and protocols; maintains duty assignments for all Armory personnel; supervises weapons locker access and firearm deployment; and is qualified to temporarily act as Chief Armory Officer if so ordered. The Master-at-Arms reports to the Chief Armory Officer.', 3, 15, 1, 'crew'),
		('Chief Engineer', 'The Chief Engineer is responsible for the condition of all systems and equipment on board a Starfleet ship or facility. S/he oversees maintenance, repairs and upgrades of all equipment. S/he is also responsible for the many repairs teams during crisis situations.\r\n\r\nThe Chief Engineer is not only the department head but also a senior officer, responsible for all the crew members in her/his department and maintenance of the duty rosters.', 4, 0, 1, 'senior'),
		('Assistant Chief Engineer', 'The Assistant Chief Engineer assists the Chief Engineer in the daily work; in issues regarding mechanical, administrative matters and co-ordinating repairs with other departments.\r\n\r\nIf so required, the Assistant Chief Engineer must be able to take over as Chief Engineer, and thus must be versed in current information regarding the ship or facility.', 4, 1, 1, 'crew'),
		('Engineer', 'There are several non-specialized engineers aboard of each vessel. They are assigned to their duties by the Chief Engineer and his Assistant, performing a number of different tasks as required, i.e. general maintenance and repair. Generally, engineers as assigned to more specialized engineering person to assist in there work is so requested by the specialized engineer.', 4, 5, 10, 'crew'),
		('Damage Control Specialist', 'The Damage Control Specialist is a specialized Engineer. The Damage Control Specialist controls all damage control aboard the ship when it gets damaged in battle. S/he oversees all damage repair aboard the ship, and coordinates repair teams on the smaller jobs so the Chief Engineer can worry about other matters.\r\n\r\nA small team is assigned to the Damage Control Specialist which is made up from NCO personnel assigned by the Assistant and Chief Engineer. The Damage Control Specialist reports to the Assistant and Chief Engineer.', 4, 10, 5, 'crew'),
		('Chief Science Officer', 'The Chief Science Officer is responsible for all the scientific data the ship/facility collects, and the distribution of such data to specific section within the department for analysis. S/he is also responsible with providing the ship\'s captain with scientific information needed for command decisions.\r\n\r\nS/he also is a department head and a member of the Senior Staff and responsible for all the crew members in her/his department and duty rosters.', 5, 0, 1, 'senior'),
		('Science Officer', 'There are several general Science Officers aboard each vessel. They are assigned to their duties by the Chief Science Officer and his Assistant. Assignments include work for the Specialized Section heads, as well as duties for work being carried out by the Chief.', 5, 5, 5, 'crew'),
		('Chief Medical Officer', 'The Chief Medical Officer is responsible for the physical health of the entire crew, but does more than patch up injured crew members. His/her function is to ensure that they do not get sick or injured to begin with, and to this end monitors their health and conditioning with regular check ups. If necessary, the Chief Medical Officer can remove anyone from duty, even a Commanding Officer. Besides this s/he is available to provide medical advice to any individual who requests it.', 6, 0, 1, 'senior'),
		('Counselor', 'Because of their training in psychology, technically the ship\'s/facility\'s Counselor is considered part of Starfleet Medical. The Counselor is responsible both for advising the Commanding Officer in dealing with other people and races, and in helping crew members with personal, psychological, and emotional problems.\r\n\r\nThe Counselor reports to the Chief Medical Officer.', 6, 2, 1, 'crew'),
		('Medical Officer', 'Medical Officer undertake the majority of the work aboard the ship/facility, examining the crew, and administering medical care under the instruction of the Chief Medical Officer.', 6, 5, 5, 'crew'),
		('Nurse', 'Nurses are trained in basic medical care, and are capable of dealing with less serious medical cases. In more serious matters the nurse assist the medical officer in the examination and administration of medical care, be this injecting required drugs, or simply assuring the injured party that they will be ok. The Nurses also maintain the medical wards, overseeing the patients and ensuring they are receiving medication and care as instructed by the Medical Officer.', 6, 10, 10, 'crew'),
		('Chief Communications Officer', 'Responsible for maintaining and upgrading the universal translator, controls the intercom and responsible for coordinating communications with other ships, stations or colonies/planets.', 7, 0, 1, 'senior'),
		('Communications Officer', 'The Communications officer is responsible for managing all incoming and outgoing transmissions. This role involves the study of new and old languages and text in an attempt to better understand and interpret their meaning.', 7, 5, 5, 'crew'),
		('MACO Commanding Officer', 'Responsible for maintaining and upgrading the universal translator, controls the intercom and responsible for coordinating communications with other ships, stations or colonies/planets.', 8, 0, 1, 'senior'),
		('MACO Executive Officer', 'Responsible for maintaining and upgrading the universal translator, controls the intercom and responsible for coordinating communications with other ships, stations or colonies/planets.', 8, 1, 1, 'crew'),
		('First Sergeant', 'Responsible for maintaining and upgrading the universal translator, controls the intercom and responsible for coordinating communications with other ships, stations or colonies/planets.', 8, 5, 1, 'crew'),
		('MACO', 'Responsible for maintaining and upgrading the universal translator, controls the intercom and responsible for coordinating communications with other ships, stations or colonies/planets.', 8, 10, 10, 'crew')");
		
		sleep(1);
		
		mysql_query("INSERT INTO `sms_ranks` (`rankName`, `rankShortName`, `rankImage`, `rankOrder`, `rankClass`, `rankDisplay`) VALUES 
			('Fleet Admiral', 'FADM', 'y-a5.png', 0, 1, 'y'),
			('Fleet Admiral', 'FADM', 'r-a5.png', 0, 2, 'y'),
			('Fleet Admiral', 'FADM', 't-a5.png', 0, 3, 'y'),

			('Admiral', 'ADM', 'y-a4.png', 1, 1, 'y'),
			('Admiral', 'ADM', 'r-a4.png', 1, 2, 'y'),
			('Admiral', 'ADM', 't-a4.png', 1, 3, 'y'),
			('General', 'GEN', 'maco-a4.png', 1, 4, 'y'),

			('Vice-Admiral', 'VADM', 'y-a3.png', 2, 1, 'y'),
			('Vice-Admiral', 'VADM', 'r-a3.png', 2, 1, 'y'),
			('Vice-Admiral', 'VADM', 't-a3.png', 2, 1, 'y'),
			('Lieutenant General', 'LT GEN', 'maco-a3.png', 2, 4, 'y'),

			('Rear-Admiral', 'RADM', 'y-a2.png', 3, 1, 'y'),
			('Rear-Admiral', 'RADM', 'r-a2.png', 3, 2, 'y'),
			('Rear-Admiral', 'RADM', 't-a2.png', 3, 3, 'y'),
			('Major General', 'MAJ GEN', 'maco-a2.png', 3, 4, 'y'),

			('Commodore', 'COMO', 'y-a1.png', 4, 1, 'y'),
			('Commodore', 'COMO', 'r-a1.png', 4, 2, 'y'),
			('Commodore', 'COMO', 't-a1.png', 4, 3, 'y'),
			('Brigadier General', 'BRG GEN', 'maco-a1.png', 4, 4, 'y'),

			('Captain', 'CAPT', 'y-o6.png', 5, 1, 'y'),
			('Captain', 'CAPT', 'r-o6.png', 5, 2, 'y'),
			('Captain', 'CAPT', 't-o6.png', 5, 3, 'y'),
			('Colonel', 'COL', 'maco-o6.png', 5, 4, 'y'),

			('Commander', 'CMDR', 'y-o5.png', 6, 1, 'y'),
			('Commander', 'CMDR', 'r-o5.png', 6, 2, 'y'),
			('Commander', 'CMDR', 't-o5.png', 6, 3, 'y'),
			('Lieutenant Colonel', 'LT COL', 'maco-o5.png', 6, 4, 'y'),

			('Lieutenant Commander', 'LT CMDR', 'y-o4.png', 7, 1, 'y'),
			('Lieutenant Commander', 'LT CMDR', 'r-o4.png', 7, 2, 'y'),
			('Lieutenant Commander', 'LT CMDR', 't-o4.png', 7, 3, 'y'),
			('Major', 'MAJ', 'maco-o4.png', 7, 4, 'y'),

			('Lieutenant', 'LT', 'y-o3.png', 8, 1, 'y'),
			('Lieutenant', 'LT', 'r-o3.png', 8, 2, 'y'),
			('Lieutenant', 'LT', 't-o3.png', 8, 3, 'y'),
			('Captain', 'CAPT', 'maco-o3.png', 8, 4, 'y'),

			('Lieutenant JG', 'LT(JG)', 'y-o2.png', 9, 1, 'y'),
			('Lieutenant JG', 'LT(JG)', 'r-o2.png', 9, 2, 'y'),
			('Lieutenant JG', 'LT(JG)', 't-o2.png', 9, 3, 'y'),
			('1st Lieutenant', '1LT', 'maco-o2.png', 9, 4, 'y'),

			('Ensign', 'EN', 'y-o1.png', 10, 1, 'y'),
			('Ensign', 'EN', 'r-o1.png', 10, 2, 'y'),
			('Ensign', 'EN', 't-o1.png', 10, 3, 'y'),
			('2nd Lieutenant', '2LT', 'maco-o1.png', 10, 4, 'y'),

			('Chief Warrant Officer', 'CWO', 'y-w2.png', 11, 1, 'y'),
			('Chief Warrant Officer', 'CWO', 'r-w2.png', 11, 2, 'y'),
			('Chief Warrant Officer', 'CWO', 't-w2.png', 11, 3, 'y'),
			('Chief Warrant Officer', 'CWO', 'maco-w2.png', 11, 4, 'y'),

			('Warrant Officer', 'WO', 'y-w1.png', 12, 1, 'y'),
			('Warrant Officer', 'WO', 'r-w1.png', 12, 2, 'y'),
			('Warrant Officer', 'WO', 't-w1.png', 12, 3, 'y'),
			('Warrant Officer', 'WO', 'maco-w1.png', 12, 4, 'y'),

			('Master Chief Petty Officer', 'MCPO', 'y-e9.png', 13, 1, 'y'),
			('Master Chief Petty Officer', 'MCPO', 'r-e9.png.png', 13, 2, 'y'),
			('Master Chief Petty Officer', 'MCPO', 't-e9', 13, 3, 'y'),
			('Sergeant Major', 'SGT MAJ', 'maco-e9.png', 13, 4, 'y'),

			('Senior Chief Petty Officer', 'SCPO', 'y-e8.png', 14, 1, 'y'),
			('Senior Chief Petty Officer', 'SCPO', 'r-e8.png', 14, 2, 'y'),
			('Senior Chief Petty Officer', 'SCPO', 't-e8.png', 14, 3, 'y'),
			('Master Sergeant', 'MSGT', 'maco-e8.png', 14, 4, 'y'),

			('Chief Petty Officer', 'CPO', 'y-e7.png', 15, 1, 'y'),
			('Chief Petty Officer', 'CPO', 'r-e7.png', 15, 2, 'y'),
			('Chief Petty Officer', 'CPO', 't-e7.png', 15, 3, 'y'),
			('Sergeant, 1st Class', 'SGT1', 'maco-e7.png', 15, 4, 'y'),

			('Petty Officer, 1st Class', 'PO1', 'y-e6.png', 16, 1, 'y'),
			('Petty Officer, 1st Class', 'PO1', 'r-e6.png', 16, 2, 'y'),
			('Petty Officer, 1st Class', 'PO1', 't-e6.png', 16, 3, 'y'),
			('Staff Sergeant', 'SSGT', 'maco-e6.png', 16, 4, 'y'),

			('Petty Officer, 2nd Class', 'PO2', 'y-e5.png', 17, 1, 'y'),
			('Petty Officer, 2nd Class', 'PO2', 'r-e5.png', 17, 2, 'y'),
			('Petty Officer, 2nd Class', 'PO2', 't-e5.png', 17, 3, 'y'),
			('Sergeant', 'SGT', 'maco-e5.png', 17, 4, 'y'),

			('Petty Officer, 3rd Class', 'PO3', 'y-e4.png', 18, 1, 'y'),
			('Petty Officer, 3rd Class', 'PO3', 'r-e4.png', 18, 2, 'y'),
			('Petty Officer, 3rd Class', 'PO3', 't-e4.png', 18, 3, 'y'),
			('Corporal', 'CORP', 'maco-e4.png', 18, 4, 'y'),

			('Crewman', 'CR', 'y-e3.png', 19, 1, 'y'),
			('Crewman', 'CR', 'r-e3.png', 19, 2, 'y'),
			('Crewman', 'CR', 't-e3.png', 19, 3, 'y'),
			('Private, 1st Class', 'PVT1', 'maco-e3.png', 19, 4, 'y'),

			('Crewman Apprentice', 'CRA', 'y-e2.png', 20, 1, 'y'),
			('Crewman Apprentice', 'CRA', 'r-e2.png', 20, 2, 'y'),
			('Crewman Apprentice', 'CRA', 't-e2.png', 20, 3, 'y'),
			('Private E-2', 'PVT(E2)', 'maco-e2.png', 20, 4, 'y'),

			('Crewman Recruit', 'CRR', 'y-e1.png', 21, 1, 'y'),
			('Crewman Recruit', 'CRR', 'r-e1.png', 21, 2, 'y'),
			('Crewman Recruit', 'CRR', 't-e1.png', 21, 3, 'y'),
			('Private E-1', 'PVT(E1)', 'maco-e1.png', 21, 4, 'y'),

			('Cadet Senior Grade', 'CDT(SR)', 'c4.png', 22, 1, 'n'),
			('Cadet Senior Grade', 'CDT(SR)', 'c4.png', 22, 2, 'n'),
			('Cadet Senior Grade', 'CDT(SR)', 'c4.png', 22, 3, 'n'),
			('Cadet Senior Grade', 'CDT(SR)', 'c4.png', 22, 4, 'n'),

			('Cadet Junior Grade', 'CDT(JR)', 'c3.png', 23, 1, 'n'),
			('Cadet Junior Grade', 'CDT(JR)', 'c3.png', 23, 2, 'n'),
			('Cadet Junior Grade', 'CDT(JR)', 'c3.png', 23, 3, 'n'),
			('Cadet Junior Grade', 'CDT(JR)', 'c3.png', 23, 4, 'n'),

			('Cadet Sophomore Grade', 'CDT(SO)', 'c2.png', 24, 1, 'n'),
			('Cadet Sophomore Grade', 'CDT(SO)', 'c2.png', 24, 2, 'n'),
			('Cadet Sophomore Grade', 'CDT(SO)', 'c2.png', 24, 3, 'n'),
			('Cadet Sophomore Grade', 'CDT(SO)', 'c2.png', 24, 4, 'n'),

			('Cadet Freshman Grade', 'CDT(FR)', 'c1.png', 25, 1, 'n'),
			('Cadet Freshman Grade', 'CDT(FR)', 'c1.png', 25, 2, 'n'),
			('Cadet Freshman Grade', 'CDT(FR)', 'c1.png', 25, 3, 'n'),
			('Cadet Freshman Grade', 'CDT(FR)', 'c1.png', 25, 4, 'n'),

			('', '', 'y-blank.png', 26, 1, 'y'),
			('', '', 'r-blank.png', 26, 2, 'y'),
			('', '', 't-blank.png', 26, 3, 'y'),
			('', '', 'maco-blank.png', 26, 4, 'y')");
		
		echo "You have successfully inserted the data into the tables! You can now begin to use your Enterprise SMS site.<br /><br />We recommend that you delete this file now.";
	
		break;
}

?>