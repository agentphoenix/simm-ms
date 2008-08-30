<?php
/*
|---------------------------------------------------------------
| DEEP SPACE NINE SMS MOD
|---------------------------------------------------------------
|
| File: sms_mod_ds9.php
| Author: David VanScott [davidv@anodyne-productions.com]
| Version: 1.0
| Last Update: 2008.08.24 1707 EST
|
| Official Deep Space Nine mod for SMS 2.6
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
		echo "The official Anodyne Deep Space Nine mod will return your installation of SMS to the default DS9 installation. Before you begin, make sure you have removed all other ranks from your <em>images/ranks</em> directory on your server and uploaded the rank sets found in the zip archive.<br /><br />The first step of this mod will remove all your department, position, and rank data from SMS. The second step will re-add the department, positions, and ranks tables with the new DS9 data. The third step will insert the departments, positions, and ranks into the database.<br /><br />Click the link below to continue to the next step.<br /><br /><strong><a href='sms_mod_ds9.php?step=2'>Next Step &raquo;</a></strong>";
		break;
	case 2:
		mysql_query('DROP TABLE sms_departments');
		mysql_query('DROP TABLE sms_positions');
		mysql_query('DROP TABLE sms_ranks');
		
		echo "Your old departments, positions, and ranks tables have been removed. The next step will re-create those tables with the appropriate default values. Click the link below to continue to the next step.<br /><br /><strong><a href='sms_mod_ds9.php?step=3'>Next Step &raquo;</a></strong>";
		
		break;
	case 3:
		mysql_query( "CREATE TABLE `sms_departments` (
		  `deptid` int(3) NOT NULL auto_increment,
		  `deptOrder` int(3) NOT NULL default '0',
		  `deptClass` int(3) NOT NULL default '0',
		  `deptName` varchar(32) NOT NULL default '',
		  `deptDesc` text NOT NULL,
		  `deptDisplay` enum('y','n') NOT NULL default 'y',
		  `deptColor` varchar(6) NOT NULL default '',
		  `deptType` enum('playing','nonplaying') not null default 'playing',
		  `deptDatabaseUse` enum('y','n') NOT NULL default 'y',
		  PRIMARY KEY  (`deptid`)
		) " . $tail . " AUTO_INCREMENT=14 ;" );
		
		sleep(1);
		
		mysql_query( "CREATE TABLE `sms_positions` (
		  `positionid` int(3) NOT NULL auto_increment,
		  `positionOrder` int(3) NOT NULL default '0',
		  `positionName` varchar(64) NOT NULL default '',
		  `positionDesc` text NOT NULL,
		  `positionDept` int(3) NOT NULL default '0',
		  `positionType` enum( 'senior', 'crew' ) NOT NULL default 'crew',
		  `positionOpen` int(2) NOT NULL default '1',
		  `positionDisplay` enum('y','n') NOT NULL default 'y',
		  `positionMainPage` enum('y','n') NOT NULL default 'n',
		  PRIMARY KEY  (`positionid`)
		) " . $tail . " AUTO_INCREMENT=69 ;" );
		
		sleep(1);
		
		mysql_query( "CREATE TABLE `sms_ranks` (
		  `rankid` int(3) NOT NULL auto_increment,
		  `rankOrder` int(2) NOT NULL default '0',
		  `rankName` varchar(32) NOT NULL default '',
		  `rankShortName` varchar(32) NOT NULL default '',
		  `rankImage` varchar(255) NOT NULL default '',
		  `rankType` int(1) NOT NULL default '1',
		  `rankDisplay` enum('y','n') NOT NULL default 'y',
		  `rankClass` int(3) NOT NULL default '0',
		  PRIMARY KEY  (`rankid`)
		) " . $tail . " AUTO_INCREMENT=213 ;");
				
		echo "You have successfully created the new new departments, positions, and ranks tables that will be used by SMS. The next step will populate those tables with the DS9 data. Click the link below to continue to the next step.<br /><br /><strong><a href='sms_mod_ds9.php?step=4'>Next Step &raquo;</a></strong>";
	
		break;
	case 4:
		mysql_query( "INSERT INTO `sms_departments` (`deptid`, `deptOrder`, `deptClass`, `deptName`, `deptDesc`, `deptDisplay`, `deptColor`, `deptType`) 
			VALUES (1, 1, 1, 'Command', 'The Command department is ultimately responsible for the ship and its crew, and those within the department are responsible for commanding the vessel and representing the interests of Starfleet.', 'y', '9c2c2c', 'playing'),
			(2, 2, 1, 'Flight Control', 'Responsible for the navigation and flight control of a vessel and its auxiliary craft, the Flight Control department includes pilots trained in both starship and auxiliary craft piloting. Note that the Flight Control department does not include Fighter pilots.', 'y', '9c2c2c', 'playing'),
			(3, 3, 1, 'Strategic Operations', 'The Strategic Operations department acts as an advisory to the command staff, as well as a resource of knowledge and information concerning hostile races in the operational zone of the ship, as well as combat strategies and other such things.', 'y', '9c2c2c', 'playing'),
			(4, 4, 2, 'Security & Tactical', 'Merging the responsibilities of ship to ship and personnel combat into a single department, the security & tactical department is responsible for the tactical readiness of the vessel and the security of the ship.', 'y', 'c08429', 'playing'),
			(5, 5, 2, 'Operations', 'The operations department is responsible for keeping ship systems functioning properly, rerouting power, bypassing relays, and doing whatever else is necessary to keep the ship operating at peak efficiency.', 'y', 'c08429', 'playing'),
			(6, 6, 2, 'Engineering', 'The engineering department has the enormous task of keeping the ship working; they are responsible for making repairs, fixing problems, and making sure that the ship is ready for anything.', 'y', 'c08429', 'playing'),
			(7, 7, 3, 'Science', 'From sensor readings to figuring out a way to enter the strange spacial anomaly, the science department is responsible for recording data, testing new ideas out, and making discoveries.', 'y', '008080', 'playing'),
			(8, 8, 3, 'Medical & Counseling', 'The medical & counseling department is responsible for the mental and physical health of the crew, from running annual physicals to combatting a strange plague that is afflicting the crew to helping a crew member deal with the loss of a loved one.', 'y', '008080', 'playing'),
			(9, 9, 4, 'Intelligence', 'The Intelligence department is responsible for gathering and providing intelligence as it becomes possible during a mission; during covert missions, the intelligence department also takes a more active role, providing the necessary classified and other information.', 'y', '666666', 'playing'),
			(10, 10, 5, 'Diplomatic Detachment', 'Responsible for representing the Federation and its interest, members of the Diplomatic Corps are members of the civilian branch of the Federation.', 'y', '800080', 'playing'),
			(11, 11, 6, 'Marine Detachment', 'When the standard security detail is not enough, marines come in and clean up; the marine detachment is a powerful tactical addition to any ship, responsible for partaking in personal combat, from sniping to melee.', 'y', '008000', 'playing'),
			(12, 12, 7, 'Starfighter Wing', 'The best pilots in Starfleet, they are responsible for piloting the starfighters in ship to ship battles, as well as providing escort for shuttles, and runabouts.', 'y', '406ceb', 'playing'),
			(13, 13, 8, 'Civilian Affairs', 'Civilians play an important role in Starfleet. Many civilian specialists across a number of fields work on occasion with Starfleet personnel as a Mission Specialist. In other cases, extra ship and station duties, such as running the ship''s lounge, are outsourced to a civilian contract.', 'y', 'ffffff', 'playing');");
		
		sleep(1);
		
		mysql_query("INSERT INTO `sms_positions` (`positionid`, `positionOrder`, `positionName`, `positionDesc`, `positionDept`, `positionType`, `positionOpen` ) 
		VALUES (1, 0, 'Commanding Officer', 'Ultimately responsible for the ship and crew, the Commanding Officer is the most senior officer aboard a vessel. S/he is responsible for carrying out the orders of Starfleet, and for representing both Starfleet and the Federation.', 1, 'senior', 1),
		(2, 1, 'Executive Officer', 'The liaison between captain and crew, the Executive Officer acts as the disciplinarian, personnel manager, advisor to the captain, and much more. S/he is also one of only two officers, along with the Chief Medical Officer, that can remove a Commanding Officer from duty.', 1, 'senior', 1),
		(3, 2, 'Second Officer', 'At times the XO must assume command of a Starship or base, when this happens the XO needs the help of another officer to assume his/her role as XO. The second officer is not a stand alone position, but a role given to the highest ranked and trusted officer aboard. When required the Second Officer will assume the role of XO, or if needed CO, and performs their duties as listed, for as long as required.', 1, 'crew', 1),
		(4, 10, 'Chief of the Boat', 'The seniormost Chief Petty Officer (including Senior and Master Chiefs), regardless of rating, is designated by the Commanding Officer as the Chief of the Boat (for vessels) or Command Chief (for starbases). In addition to his or her departmental responsibilities, the COB/CC performs the following duties: serves as a liaison between the Commanding Officer (or Executive Officer) and the enlisted crewmen; ensures enlisted crews understand Command policies; advises the Commanding Officer and Executive Officer regarding enlisted morale, and evaluates the quality of noncommissioned officer leadership, management, and supervisory training.\r\n\r\nThe COB/CC works with the other department heads, Chiefs, supervisors, and crewmen to insure discipline is equitably maintained, and the welfare, morale, and health needs of the enlisted personnel are met. The COB/CC is qualified to temporarily act as Commanding or Executive Officer if so ordered. ', 1, 'crew', 1),
		(5, 15, 'Mission Advisor', 'Advises the Commanding Officer and Executive Officer on mission-specific areas of importance. Many times, the Mission Advisor knows just as much about the mission as the CO and XO do, if not even more. He or she also performs mission-specific tasks, and can take on any roles that a mission requires him or her to do. Concurrently holds another position, except in rare circumstances.', 1, 'crew', 1),
		(6, 0, 'Chief Flight Control Officer', 'Originally known as helm, or Flight Control Officer, CONN incorporates two job, Navigation and flight control. A Flight Control Officer must always be present on the bridge of a starship. S/he plots courses, supervises the computers piloting, corrects any flight deviations and pilots the ship manually when needed. The Chief Flight Control Officer is the senior most CONN Officer aboard, serving as a Senior Officer, and chief of the personnel under him/her.', 2, 'senior', 1),
		(7, 1, 'Assistant Chief Flight Control Officer', 'Originally known as helm, or Flight Control Officer, CONN incorporates two job, Navigation and flight control. A Flight Control Officer must always be present on the bridge of a starship. S/he plots courses, supervises the computers piloting, corrects any flight deviations and pilots the ship manually when needed. The Assistant Chief Flight Control Officer is the second senior most CONN Officer aboard and reports directly to the Chief Flight Control Officer.', 2, 'crew', 1),
		(8, 5, 'Flight Control Officer', 'Originally know as helm, or Flight Control Officer, CONN incorporates two job, navigation and flight control. A Flight Control Officer must always be present on the bridge of a starship, and every vessel has a number of Flight Control Officers to allow shift rotations. S/he plots courses, supervises the computers piloting, corrects any flight deviations and pilots the ship manually when needed. Flight Control Officers report to the Chief Flight Control Officer.', 2, 'crew', 5),
		(9, 10, 'Shuttle/Runabout Pilot', 'Responsible for piloting the various auxiliary craft (besides fighters), these pilots are responsible for transporting their passengers safely to and from locations that are inaccessible via the transporter.', 2, 'crew', 4),
		(10, 0, 'Chief Strategic Operations Officer', 'The Chief Strategic Operations Officer is responsible for coordinating all Starfleet and allied assets in within their designated area of space, as well as tactical analysis (in the absence of a dedicated tactical department) and intelligence gathering (in the absence of a dedicated intelligence department).', 3, 'senior', 1),
		(11, 1, 'Assistant Chief Strategic Operations Officer', 'The Assistant Chief Strategic Operations Officer is the second ranked officer in the Strategic Operations department. He or she answers to the Chief Strategic Operations Officer. He or she is responsible for coordinating Starfleet and allied assets within a designated area of space, as well as tactical analysis and intelligence gathering.', 3, 'crew', 1),
		(12, 5, 'Strategic Operations Officer', 'The Strategic Operations Officer is part of the Strategic Operations department. He or she answers to the Chief Strategic Operations Officer. He or she is responsible for coordinating Starfleet and allied assets within a designated area of space, as well as tactical analysis and intelligence gathering.', 3, 'crew', 1),
		(13, 0, 'Chief Security/Tactical Officer', 'The Chief Security Officer is called Chief of Security. Her/his duty is to ensure the safety of ship and crew. Some take it as their personal duty to protect the Commanding Officer/Executive Officer on away teams. She/he is also responsible for people under arrest and the safety of guests, liked or not.  S/he also is a department head and a member of the senior staff, responsible for all the crew members in her/his department and duty rosters. Security could be called the 24th century police force.\r\n\r\nThe Chief of Security role can also be combined with the Chief Tactical Officer position. ', 4, 'senior', 1),
		(14, 1, 'Assistant Chief Security/Tactical Officer', 'The Assistant Chief Security Officer is sometimes called Deputy of Security. S/he assists the Chief of Security in the daily work; in issues regarding Security and any administrative matters.  If required the Deputy must be able to take command of the Security department. ', 4, 'crew', 1),
		(15, 5, 'Security Officer', 'There are several Security Officers aboard each vessel. They are assigned to their duties by the Chief of Security and his/her Deputy and mostly guard sensitive areas, protect people, patrol, and handle other threats to the Federation.', 4, 'crew', 1),
		(16, 10, 'Tactical Officer', 'The Tactical Officers are the vessels gunmen. They assist the Chief Tactical Officer by running and maintaining the numerous weapons systems aboard the ship/starbase, and analysis and tactical planning of current missions. Very often Tactical Officers are also trained in ground combat and small unit tactics.', 4, 'crew', 1),
		(17, 15, 'Security Investigations Officer', 'The Security Investigations Officer is an Enlisted Officer. S/He fulfills the role of a special investigator or detective when dealing with Starfleet matters aboard ship or on a planet. Coordinates with the Chief Security Officer on all investigations as needed. The Security Investigations Officer reports to the Chief of Security.', 4, 'crew', 1),
		(18, 20, 'Brig Officer', 'The Brig Officer is a Security Officer who has chosen to specialize in a specific role. S/he guards the brig and its cells. But there are other duties associated with this post as well. S/he is responsible for any prisoner transport, and the questioning of prisoners. Often Brig Officers have a good knowledge of forcefield technology, and are experts in escaping such confinements.', 4, 'crew', 1),
		(19, 25, 'Master-At-Arms', 'The Master-at-Arms trains and supervises Security crewmen in departmental operations, repairs, and protocols; maintains duty assignments for all Security personnel; supervises weapons locker access and firearm deployment; and is qualified to temporarily act as Chief of Security if so ordered. The Master-at-Arms reports to the Chief of Security.', 4, 'crew', 1),
		(20, 0, 'Chief Operations Officer', 'The Chief Operations Officer has the primary responsibility of ensuring that ship functions, such as the use of the lateral sensor array, do not interfere with one and another. S/he must prioritize resource allocations, so that the most critical activities can have every chance of success. If so required, s/he can curtail shipboard functions if s/he thinks they will interfere with the ship''s current mission or routine operations.\r\n\r\nThe Chief Operations Officer oversees the Operations department, and is a member of the senior staff. ', 5, 'senior', 1),
		(21, 1, 'Assistant Chief Operations Officer', 'The Chief Operations Officer cannot man the bridge at all times. Extra personnel are needed to relive and maintain ship operations. The Operations Officers are thus assistants to the Chief, fulfilling his/her duties when required, and assuming the Operations consoles if required at any time.\r\n\r\nThe Assistant Chief Operations Officer is the second-in-command of the Operations Department, and can assume the role of Chief Operations Officer on a temporary or permanent basis if so needed. ', 5, 'crew', 1),
		(22, 5, 'Operations Officer', 'The Chief Operations Officer cannot man the bridge at all times. Extra personnel are needed to relive and maintain ship operations. The Operations Officers are thus assistants to the Chief, fulfilling his/her duties when required, and assuming the Operations consoles if required at any time.\r\n\r\nThe Operations Officer reports to the Chief Operations Officer.', 5, 'crew', 1),
		(23, 10, 'Quartermaster', 'Replicator usage can allow the fabrication of nearly any critical mission part, but large-scale replication is not considered energy-efficient except in emergency situations. However, in such situations, power usage is strictly limited, so it is unwise to depend upon the availability of replicated spare parts.\r\n\r\nThus a ship/facility must maintain a significant stock of spare parts in inventory at all times. The Quartermaster is the person responsible for the requesting of parts from Starfleet and maintaining the stock and inventory of all spare parts. All request for supplies are passed to the Quartermaster, who check and send the final request to the XO for final approval. A good Quartermaster is never caught short on supplies.\r\n\r\nThe Quartermaster trains and supervises crewmen in Bridge operations, repairs, and protocols and sets the agenda for instruction in general ship and starbase operations for the Boatswain''s Mate; maintains the ship''s log, the ship''s clock, and watch and duty assignments for all Bridge personnel; may assume any Bridge (i.e. CONN) or Operations role (i.e. transporter) as required; and is qualified to temporarily act as Commanding or Executive Officer if so ordered.\r\n\r\nQuartermasters ensure that all officers and crew perform their duties consistent with Starfleet directives. The Quartermaster reports to the Executive Officer.', 5, 'crew', 1),
		(24, 12, 'Boatswain', 'Each vessel and base has one Warrant Officer (or Chief Warrant Officer) who holds the position of Boatswain. The Boatswain (pronounced and also written \"Bosun\" or \"Bos\'n\") trains and supervises personnel (including both the ship\'s company or base personnel as well as passengers or vessels) in general ship and base operations, repairs, and protocols; maintains duty assignments for all Operations personnel; sets the agenda for instruction in general ship and base operations; supervises auxiliary and utility service personnel and daily ship or base maintenance; coordinates all personnel cross-trained in damage control operations and supervises damage control and emergency operations; may assume any Bridge or Operations role as required; and is qualified to temporarily act at Operations if so ordered.\r\n\r\nThe Boatswain reports to the Chief Operations Officer.', 5, 'crew', 1),
		(25, 0, 'Chief Engineering Officer', 'The Chief Engineer is responsible for the condition of all systems and equipment on board a Starfleet ship or facility. S/he oversees maintenance, repairs and upgrades of all equipment. S/he is also responsible for the many repairs teams during crisis situations.\r\n\r\nThe Chief Engineer is not only the department head but also a senior officer, responsible for all the crew members in her/his department and maintenance of the duty rosters.', 6, 'senior', 1),
		(26, 1, 'Assistant Chief Engineering Officer', 'The Assistant Chief Engineer assists the Chief Engineer in the daily work; in issues regarding mechanical, administrative matters and co-ordinating repairs with other departments.\r\n\r\nIf so required, the Assistant Chief Engineer must be able to take over as Chief Engineer, and thus must be versed in current information regarding the ship or facility. ', 6, 'crew', 1),
		(27, 5, 'Engineering Officer', 'There are several non-specialized engineers aboard of each vessel. They are assigned to their duties by the Chief Engineer and his Assistant, performing a number of different tasks as required, i.e. general maintenance and repair. Generally, engineers as assigned to more specialized engineering person to assist in there work is so requested by the specialized engineer.', 6, 'crew', 1),
		(28, 10, 'Communications Specialist', 'The Communications Specialist is a specialized engineer. Communication aboard a ship or facility takes two basic forms, voice and data. Both are handled by the onboard computer system and dedicated hardware. The vastness and complexity of this system requires a dedicated team to maintain the system.\r\n\r\nThe Communications Specialist is the officer in charge of this team, which is made up from NCO personnel, assigned to the team by the Assistant and Chief Engineer. The Communications Specialist reports to the Asst. and Chief Engineer.', 6, 'crew', 1),
		(29, 15, 'Computer Systems Specialist', 'The Computer Systems Specialist is a specialized Engineer. The new generation of Computer systems are highly developed. This system needs much maintenance and the Computer Systems Specialist was introduced to relieve the Science Officer, whose duty this was in the very early days.\r\n\r\nA small team is assigned to the Computer Systems Specialist, which is made up from NCO personnel assigned by the Assistant and Chief Engineer. The Computer Systems Specialist reports to the Assistant and Chief Engineer. ', 6, 'crew', 1),
		(30, 20, 'Damage Control Specialist', 'The Damage Control Specialist is a specialized Engineer. The Damage Control Specialist controls all damage control aboard the ship when it gets damaged in battle. S/he oversees all damage repair aboard the ship, and coordinates repair teams on the smaller jobs so the Chief Engineer can worry about other matters.\r\n\r\nA small team is assigned to the Damage Control Specialist which is made up from NCO personnel assigned by the Assistant and Chief Engineer. The Damage Control Specialist reports to the Assistant and Chief Engineer. ', 6, 'crew', 1),
		(31, 25, 'Matter/Energy Systems Specialist', 'The Matter / Energy Systems Specialist is a specialized Engineer. All aspect of matter energy transfers with the sole exception of the warp drive systems are handled by the Matter/Energy Systems Specialist. Such areas involved are transporter and replicator systems. The Matter/Energy Systems Specialist is the Officer in charge of a small team, which is made up from NCO personnel, assigned by the Assistant and Chief Engineer. The Matter/Energy Systems Specialist reports to the Assistant and Chief Engineer.', 6, 'crew', 1),
		(32, 30, 'Propulsion Specialist', 'Specializing in impulse and warp propulsion, these specialists are often specific to even a single class of ship due to the complexity of warp and impulse systems.', 6, 'crew', 1),
		(33, 35, 'Structural/Environmental Systems Specialist', 'The Structural and Environmental Systems Specialist is a specialised Engineer. From a small ship/facility to a large one, all requires constant monitoring. The hull, bulkheads, walls, Jeffrey''s tubes, turbolifts, structural integrity field, internal dampening field, and environmental systems are all monitored and maintained by this officer and his/her team.\r\n\r\nThe team assigned to the Structural and Environmental Systems Specialist is made up from NCO personnel, assigned by the Assistant and Chief Engineer. The Structural and Environmental Systems Specialist reports to the Asst and Chief Engineer. ', 6, 'crew', 1),
		(34, 40, 'Transporter Chief', 'The Transporter Chief is responsible for all transports to and from other ships and any planetary bodies. When transporting is not going on, the Transporter Chief is responsible for keeping the transporters running at peak efficiency.\r\n\r\nThe team assigned to the Transporter Chief is made up from NCO personnel, assigned by the Assistant and Chief Engineer. The Transporter Chief reports to the Assistant and Chief Engineer. ', 6, 'crew', 1),
		(35, 0, 'Chief Science Officer', 'The Chief Science Officer is responsible for all the scientific data the ship/facility collects, and the distribution of such data to specific section within the department for analysis. S/he is also responsible with providing the ship''s captain with scientific information needed for command decisions.\r\n\r\nS/he also is a department head and a member of the Senior Staff and responsible for all the crew members in her/his department and duty rosters.', 7, 'senior', 1),
		(36, 1, 'Assistant Chief Science Officer', 'The Assistant Chief Science Officer assists Chief Science Officer in all areas, such as administration, and analysis of scientific data. The Assistant often take part in specific analysis of important data along with the Chief Science Officer, however spends most time overseeing current project and their section heads.', 7, 'crew', 1),
		(37, 5, 'Science Officer', 'There are several general Science Officers aboard each vessel. They are assigned to their duties by the Chief Science Officer and his Assistant. Assignments include work for the Specialized Section heads, as well as duties for work being carried out by the Chief and Assistant.', 7, 'crew', 1),
		(38, 10, 'Alien Archaeologist/Anthropologist', 'Specialized Science Officer in charge of the Alien Culture Section. This role involves the study of all newly discovered alien species and life forms, from the long dead to thriving. There knowledge also involves current known alien species. Has close ties to the Historian.\r\n\r\nAnswers to the Chief Science Officer and Assistant Chief Science Officer. ', 7, 'crew', 1),
		(39, 15, 'Biologist', 'Specialized Science Officer in charge of the Biology Section. This role entails the study of biology, botany, zoology and many more Life Sciences. On larger ships there many be a number of Science Officers within this section, under the lead of the Biologist.', 7, 'crew', 1),
		(40, 20, 'Language Specialist', 'Specialized Communications Officer in charge of the Linguistics section. This role involves the study of new and old languages and text in an attempt to better understand and interpret their meaning.\r\n\r\nAnswers to the Chief and Assistant Chief Communications Officer. ', 7, 'crew', 1),
		(41, 25, 'Stellar Cartographer', 'Specialized Science Officer in charge of the Stellar Cartography bay. This role entails the mapping of all spatial phenomenon, and the implications of such phenomenon. Has close ties with the Physicist and Astrometrics Officer.', 7, 'crew', 1),
		(42, 0, 'Chief Medical Officer', 'The Chief Medical Officer is responsible for the physical health of the entire crew, but does more than patch up injured crew members. His/her function is to ensure that they do not get sick or injured to begin with, and to this end monitors their health and conditioning with regular check ups. If necessary, the Chief Medical Officer can remove anyone from duty, even a Commanding Officer. Besides this s/he is available to provide medical advice to any individual who requests it.\r\n\r\nAdditionally the Chief is also responsible for all aspect of the medical deck, such as the Medical labs, Surgical suites and Dentistry labs.\r\n\r\nS/he also is a department head and a member of the Senior Staff and responsible for all the crew members in her/his department and duty rosters. ', 8, 'senior', 1),
		(43, 1, 'Chief Counselor', 'Because of their training in psychology, technically the ship''s/facility''s Counselor is considered part of Starfleet medical. The Counselor is responsible both for advising the Commanding Officer in dealing with other people and races, and in helping crew members with personal, psychological, and emotional problems.\r\n\r\nThe Chief Counselor is considered a member of the Senior Staff. S/he is responsible for the crew in his/her department. The Chief Counselor is the Counselor with the highest rank and most experience. ', 8, 'senior', 1),
		(44, 2, 'Assistant Chief Medical Officer', 'A starship or facility has numerous personnel aboard, and thus the Chief Medical Officer cannot be expect to do all the work required. The Asst. Chief Medical Officer assists Chief in all areas, such as administration, and application of medical care.', 8, 'crew', 1),
		(45, 5, 'Medical Officer', 'Medical Officer undertake the majority of the work aboard the ship/facility, examining the crew, and administering medical care under the instruction of the Chief Medical Officer and Assistant Chief Medical Officer also run the other Medical areas not directly overseen by the Chief Medical Officer.', 8, 'crew', 1),
		(46, 10, 'Counselor', 'Because of their training in psychology, technically the ship''s/facility''s Counselor is considered part of Starfleet medical. The Counselor is responsible both for advising the Commanding Officer in dealing with other people and races, and in helping crew members with personal, psychological, and emotional problems.', 8, 'crew', 1),
		(47, 15, 'Nurse', 'Nurses are trained in basic medical care, and are capable of dealing with less serious medical cases. In more serious matters the nurse assist the medical officer in the examination and administration of medical care, be this injecting required drugs, or simply assuring the injured party that they will be ok. The Nurses also maintain the medical wards, overseeing the patients and ensuring they are receiving medication and care as instructed by the Medical Officer.', 8, 'crew', 2),
		(48, 20, 'Morale Officer', 'Responsible for keeping the morale of the crew high. Delivers regular reports on morale to the Executive Officer. The Morale Officer plans activities that will keep the crew''s morale and demeanor up. If any crew member is having problems, the Morale Officer can assist that crew member.', 8, 'crew', 1),
		(49, 0, 'Chief Intelligence Officer', 'Responsible for managing the intelligence department in its various facets, the Chief Intelligence officer often assists the Strategic Operations officer with information gathering and analysis, and then acts as a channel of information to the CO and bridge crew during combat situations.', 9, 'senior', 1),
		(50, 1, 'Assistant Chief Intelligence Officer', 'Responsible for aiding the Chief Intelligence Officer in managing the intelligence department in its various facets, often assisting the Strategic Operations officer with information gathering and analysis.', 9, 'crew', 1),
		(51, 5, 'Intelligence Officer', 'Responsible for gathering intelligence, an Intelligence officer has the patience to read through a database for hours on end, and the cunning to coax information from an unwilling giver. S/he must provide this information to the Chief Intelligence officer as it becomes needed.', 9, 'crew', 2),
		(52, 10, 'Infiltration Specialist', 'The Infiltration Specialist is trained the arts of covert operations and infiltration. They are trained to get into and out of enemy instillations, territory, etc. Once in, they can gather intel, or if needed plant explosives, and even in times of war capture of enemy personnel. The Infiltration Specialist reports to the Chief Intelligence Officer.', 9, 'crew', 1),
		(53, 15, 'Encryption Specialist', 'This NCO takes submitted Intelligence reports and runs them through algorithms, checking for keywords that denote mistyped classification and then puts the report into crypto form and sends them through the proper channels of communication to either on board ship consoles or off board to who ever needs to receive it. The Encryption Specialist reports to the Chief Intelligence Officer.', 9, 'crew', 1),
		(54, 0, 'Chief Diplomatic Officer', 'The Diplomatic Officer of each vessel/base must be familiar with a variety of areas: history, religion, politics, economics, and military, and understand how they affect potential threats. A wide range of operations can occur in response to these areas and threats. These operations occur within three general states of being: peacetime competition, conflict and war.\r\n\r\nS/he must be equally flexible and demonstrate initiative, agility, depth, synchronization, and improvisation to provide responsive legal services to his/her Commanding Officer as well a diplomatic advise on current status of an Alien Species both aligned and non aligned to the Federation.\r\n\r\nThe Chief Diplomatic Officer is in charge of the Diplomatic Corps Detachment. He or she oversees the operation of it, as well as makes sure everything in that department is carried out according to Starfleet Regulations. ', 10, 'senior', 1),
		(55, 1, 'Assistant Chief Diplomatic Officer', 'The Diplomatic Officer of each vessel/base must be familiar with a variety of areas: history, religion, politics, economics, and military, and understand how they affect potential threats. A wide range of operations can occur in response to these areas and threats. These operations occur within three general states of being: peacetime competition, conflict and war.\r\n\r\nS/he must be equally flexible and demonstrate initiative, agility, depth, synchronization, and improvisation to provide responsive legal services to his/her Commanding Officer aiding in official functions as prescribed by protocol, performing administrative duties, and other tasks as directed by the Chief Diplomatic Officer, as well a diplomatic advise on current status of an Alien Species both aligned and non aligned to the Federation.\r\n\r\nThe Assistant Chief Diplomatic Officer is the second-in-command of the Diplomatic Corps Detachment. If necessary, he or she can take the place of the Chief Diplomatic Officer on a temporary or permanent basis.', 10, 'crew', 1),
		(56, 5, 'Diplomatic Officer', 'The Diplomatic Officer of each vessel/base must be familiar with a variety of areas: history, religion, politics, economics, and military, and understand how they affect potential threats. A wide range of operations can occur in response to these areas and threats. These operations occur within three general states of being: peacetime competition, conflict and war.\r\n\r\nS/he must be equally flexible and demonstrate initiative, agility, depth, synchronization, and improvisation to provide responsive legal services to his/her Commanding Officer aiding in official functions as prescribed by protocol, performing administrative duties, and other tasks as directed by the Chief Diplomatic Officer and/or Assistant Chief Diplomatic Officer as well a diplomatic advice on current status of an Alien Species both aligned and non aligned to the Federation. ', 10, 'crew', 1),
		(57, 10, 'Diplomatic Corpsman', 'The Diplomatic Corpsman is a special position reserved for enlisted officers who wish to study diplomacy, and aid the department in its mission. Their duties consist of, but are not limited to, aiding Diplomatic Officers and Diplomat''s Aide in the construction of various legal documents, researching diplomatic archives, attending and aiding in the preparation for diplomatic functions, and other tasks as prescribed by the Chief Diplomatic Officer and/or Assistant Chief Diplomatic Officer. These individuals are qualified to undertake some of the responsibilities of a Diplomatic Officer, as their training are far less in-depth. They are, however, able to, and adequately trained to function as a paralegal when such services are required by a vessel/base''s crew.', 10, 'crew', 1),
		(58, 15, 'Diplomat''s Aide', 'S/he responds to the Ship/Base''s Chief Diplomatic Officer, and is required to be able to stand in and run the Diplomatic Department as required should the Chief Diplomatic Officer be absent for any reason.\r\n\r\nThe Aide must therefore be versed in all Diplomatic information regarding the current status of the Federation and its aligned and non aligned neighbours.', 10, 'crew', 1),
		(59, 0, 'Marine Commanding Officer', 'The Marine CO is responsible for all the Marine personnel assigned to the ship/facility. S/he is in required to take command of any special ground operations and lease such actions with security. The Marines could be called the 24th century commandos.\r\n\r\nThe CO can range from a Second Lieutenant on a small ship to a Lieutenant Colonel on a large facility or colony. Charged with the training, condition and tactical leadership of the Marine compliment, they are a member of the senior staff.\r\n\r\nAnswers to the Commanding Officer of the ship/facility. ', 11, 'senior', 1),
		(60, 1, 'Marine Executive Officer', 'The Executive Officer of the Marines, works like any Asst. Department head, removing some of the work load from the Marine CO and if the need arises taking on the role of Marine CO. S/he oversees the regular duties of the Marines, from regular drills to equipment training, assignment and supply request to the ship/facilities Materials Officer.\r\n\r\nAnswers to the Marine Commanding Officer.', 11, 'crew', 1),
		(61, 5, 'First Sergeant', 'The First Sergeant is the highest ranked Enlisted marine. S/He is in charge of all of the marine enlisted affairs in the detachment. They assist the Company or Detachment Commander as their Executive Officer would. They act as a bridge, closing the gap between the NCO''s and the Officers.\r\n\r\nAnswers To Marine Commanding Officer.', 11, 'crew', 1),
		(62, 10, 'Marine', 'Serving within a squad, the marine is trained in a variety of means of combat, from melee to ranged projectile to sniping.', 11, 'crew', 99),
		(63, 0, 'Wing Commander', 'Commander of all the squadrons within the wing.', 12, 'senior', 1),
		(64, 1, 'Wing Executive Officer', 'The first officer of the Wing.', 12, 'crew', 1),
		(65, 5, 'Squadron Leader', 'Leader of a starfighter squadron.', 12, 'crew', 1),
		(66, 10, 'Squadron Pilot', 'A pilot in the starfighter squadron', 12, 'crew', 1),
		(67, 0, 'Chef', 'Responsible for preparing all meals served in the Mess Hall and for the food during any diplomatic functions that may be held onboard.', 13, 'crew', 1),
		(68, 1, 'Other', '', 13, 'crew', 1);");
		
		sleep(1);
		
		mysql_query( "INSERT INTO `sms_ranks` (`rankid`, `rankOrder`, `rankName`, `rankImage`, `rankShortName`, `rankType`, `rankDisplay`, `rankClass`) 
		VALUES (1, 1, 'Fleet Admiral', 'Starfleet/r-a5.png', 'FADM', 1, 'y', 1),
		(2, 1, 'Fleet Admiral', 'Starfleet/y-a5.png', 'FADM', 1, 'y', 2),
		(3, 1, 'Fleet Admiral', 'Starfleet/t-a5.png', 'FADM', 1, 'y', 3),
		(4, 1, 'Fleet Admiral', 'Starfleet/s-a5.png', 'FADM', 1, 'y', 4),
		(5, 1, 'Fleet Admiral', 'Starfleet/v-a5.png', 'FADM', 1, 'y', 5),
		(6, 1, 'Field Marshal', 'Marine/g-a5.png', 'FMSL', 1, 'y', 6),
		(7, 1, 'Fleet Admiral', 'Starfleet/c-a5.png', 'FADM', 1, 'y', 7),

		(8, 2, 'Admiral', 'Starfleet/r-a4.png', 'ADM', 1, 'y', 1),
		(9, 2, 'Admiral', 'Starfleet/y-a4.png', 'ADM', 1, 'y', 2),
		(10, 2, 'Admiral', 'Starfleet/t-a4.png', 'ADM', 1, 'y', 3),
		(11, 2, 'Admiral', 'Starfleet/s-a4.png', 'ADM', 1, 'y', 4),
		(12, 2, 'Admiral', 'Starfleet/v-a4.png', 'ADM', 1, 'y', 5),
		(13, 2, 'General', 'Marine/g-a4.png', 'GEN', 1, 'y', 6),
		(14, 2, 'Admiral', 'Starfleet/c-a4.png', 'ADM', 1, 'y', 7),

		(15, 3, 'Vice Admiral', 'Starfleet/r-a3.png', 'VADM', 1, 'y', 1),
		(16, 3, 'Vice Admiral', 'Starfleet/y-a3.png', 'VADM', 1, 'y', 2),
		(17, 3, 'Vice Admiral', 'Starfleet/t-a3.png', 'VADM', 1, 'y', 3),
		(18, 3, 'Vice Admiral', 'Starfleet/s-a3.png', 'VADM', 1, 'y', 4),
		(19, 3, 'Vice Admiral', 'Starfleet/v-a3.png', 'VADM', 1, 'y', 5),
		(20, 3, 'Lieutenant General', 'Marine/g-a3.png', 'LTG', 1, 'y', 6),
		(21, 3, 'Vice Admiral', 'Starfleet/c-a3.png', 'VADM', 1, 'y', 7),

		(22, 4, 'Rear Admiral', 'Starfleet/r-a2.png', 'RADM', 1, 'y', 1),
		(23, 4, 'Rear Admiral', 'Starfleet/y-a2.png', 'RADM', 1, 'y', 2),
		(24, 4, 'Rear Admiral', 'Starfleet/t-a2.png', 'RADM', 1, 'y', 3),
		(25, 4, 'Rear Admiral', 'Starfleet/s-a2.png', 'RADM', 1, 'y', 4),
		(26, 4, 'Rear Admiral', 'Starfleet/v-a2.png', 'RADM', 1, 'y', 5),
		(27, 4, 'Major General', 'Marine/g-a2.png', 'MG', 1, 'y', 6),
		(28, 4, 'Rear Admiral', 'Starfleet/c-a2.png', 'RADM', 1, 'y', 7),

		(29, 5, 'Commodore', 'Starfleet/r-a1.png', 'COMO', 1, 'y', 1),
		(30, 5, 'Commodore', 'Starfleet/y-a1.png', 'COMO', 1, 'y', 2),
		(31, 5, 'Commodore', 'Starfleet/t-a1.png', 'COMO', 1, 'y', 3),
		(32, 5, 'Commodore', 'Starfleet/s-a1.png', 'COMO', 1, 'y', 4),
		(33, 5, 'Commodore', 'Starfleet/v-a1.png', 'COMO', 1, 'y', 5),
		(34, 5, 'Brigadier General', 'Marine/g-a1.png', 'BG', 1, 'y', 6),
		(35, 5, 'Commodore', 'Starfleet/c-a1.png', 'COMO', 1, 'y', 7),

		(36, 6, 'Captain', 'Starfleet/r-o6.png', 'CAPT', 1, 'y', 1),
		(37, 6, 'Captain', 'Starfleet/y-o6.png', 'CAPT', 1, 'y', 2),
		(38, 6, 'Captain', 'Starfleet/t-o6.png', 'CAPT', 1, 'y', 3),
		(39, 6, 'Captain', 'Starfleet/s-o6.png', 'CAPT', 1, 'y', 4),
		(40, 6, 'Captain', 'Starfleet/v-o6.png', 'CAPT', 1, 'y', 5),
		(41, 6, 'Colonel', 'Marine/g-o6.png', 'COL', 1, 'y', 6),
		(42, 6, 'Captain', 'Starfleet/c-o6.png', 'CAPT', 1, 'y', 7),

		(43, 7, 'Commander', 'Starfleet/r-o5.png', 'CDR', 1, 'y', 1),
		(44, 7, 'Commander', 'Starfleet/y-o5.png', 'CDR', 1, 'y', 2),
		(45, 7, 'Commander', 'Starfleet/t-o5.png', 'CDR', 1, 'y', 3),
		(46, 7, 'Commander', 'Starfleet/s-o5.png', 'CDR', 1, 'y', 4),
		(47, 7, 'Commander', 'Starfleet/v-o5.png', 'CDR', 1, 'y', 5),
		(48, 7, 'Lieutenant Colonel', 'Marine/g-o5.png', 'LTC', 1, 'y', 6),
		(49, 7, 'Commander', 'Starfleet/c-o5.png', 'CDR', 1, 'y', 7),

		(50, 8, 'Lieutenant Commander', 'Starfleet/r-o4.png', 'LCDR', 1, 'y', 1),
		(51, 8, 'Lieutenant Commander', 'Starfleet/y-o4.png', 'LCDR', 1, 'y', 2),
		(52, 8, 'Lieutenant Commander', 'Starfleet/t-o4.png', 'LCDR', 1, 'y', 3),
		(53, 8, 'Lieutenant Commander', 'Starfleet/s-o4.png', 'LCDR', 1, 'y', 4),
		(54, 8, 'Lieutenant Commander', 'Starfleet/v-o4.png', 'LCDR', 1, 'y', 5),
		(55, 8, 'Major', 'Marine/g-o4.png', 'MAJ', 1, 'y', 6),
		(56, 8, 'Lieutenant Commander', 'Starfleet/c-o4.png', 'LCDR', 1, 'y', 7),

		(57, 9, 'Lieutenant', 'Starfleet/r-o3.png', 'LT', 1, 'y', 1),
		(58, 9, 'Lieutenant', 'Starfleet/y-o3.png', 'LT', 1, 'y', 2),
		(59, 9, 'Lieutenant', 'Starfleet/t-o3.png', 'LT', 1, 'y', 3),
		(60, 9, 'Lieutenant', 'Starfleet/s-o3.png', 'LT', 1, 'y', 4),
		(61, 9, 'Lieutenant', 'Starfleet/v-o3.png', 'LT', 1, 'y', 5),
		(62, 9, 'Marine Captain', 'Marine/g-o3.png', 'CPT', 1, 'y', 6),
		(63, 9, 'Lieutenant', 'Starfleet/c-o3.png', 'LT', 1, 'y', 7),

		(64, 10, 'Lieutenant JG', 'Starfleet/r-o2.png', 'LTJG', 1, 'y', 1),
		(65, 10, 'Lieutenant JG', 'Starfleet/y-o2.png', 'LTJG', 1, 'y', 2),
		(66, 10, 'Lieutenant JG', 'Starfleet/t-o2.png', 'LTJG', 1, 'y', 3),
		(67, 10, 'Lieutenant JG', 'Starfleet/s-o2.png', 'LTJG', 1, 'y', 4),
		(68, 10, 'Lieutenant JG', 'Starfleet/v-o2.png', 'LTJG', 1, 'y', 5),
		(69, 10, '1st Lieutenant', 'Marine/g-o2.png', '1LT', 1, 'y', 6),
		(70, 10, 'Lieutenant JG', 'Starfleet/c-o2.png', 'LTJG', 1, 'y', 7),

		(71, 11, 'Ensign', 'Starfleet/r-o1.png', 'ENS', 1, 'y', 1),
		(72, 11, 'Ensign', 'Starfleet/y-o1.png', 'ENS', 1, 'y', 2),
		(73, 11, 'Ensign', 'Starfleet/t-o1.png', 'ENS', 1, 'y', 3),
		(74, 11, 'Ensign', 'Starfleet/s-o1.png', 'ENS', 1, 'y', 4),
		(75, 11, 'Ensign', 'Starfleet/v-o1.png', 'ENS', 1, 'y', 5),
		(76, 11, '2nd Lieutenant', 'Marine/g-o1.png', '2LT', 1, 'y', 6),
		(77, 11, 'Ensign', 'Starfleet/c-o1.png', 'ENS', 1, 'y', 7),

		(78, 12, 'Chief Warrant Officer 1st Class', 'Starfleet/r-w4.png', 'CWO1', 1, 'y', 1),
		(79, 12, 'Chief Warrant Officer 1st Class', 'Starfleet/y-w4.png', 'CWO1', 1, 'y', 2),
		(80, 12, 'Chief Warrant Officer 1st Class', 'Starfleet/t-w4.png', 'CWO1', 1, 'y', 3),
		(81, 12, 'Chief Warrant Officer 1st Class', 'Starfleet/s-w4.png', 'CWO1', 1, 'y', 4),
		(82, 12, 'Chief Warrant Officer 1st Class', 'Starfleet/v-w4.png', 'CWO1', 1, 'y', 5),
		(83, 12, 'Chief Warrant Officer 1st Class', 'Marine/g-w4.png', 'CWO1', 1, 'y', 6),
		(84, 12, 'Chief Warrant Officer 1st Class', 'Starfleet/c-w4.png', 'CWO1', 1, 'y', 7),

		(85, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/r-w3.png', 'CWO2', 1, 'y', 1),
		(86, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/y-w3.png', 'CWO2', 1, 'y', 2),
		(87, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/t-w3.png', 'CWO2', 1, 'y', 3),
		(88, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/s-w3.png', 'CWO2', 1, 'y', 4),
		(89, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/v-w3.png', 'CWO2', 1, 'y', 5),
		(90, 13, 'Chief Warrant Officer 2nd Class', 'Marine/g-w3.png', 'CWO2', 1, 'y', 6),
		(91, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/c-w3.png', 'CWO2', 1, 'y', 7),

		(92, 14, 'Chief Warrant Officer 3rd Class', 'Starfleet/r-w2.png', 'CWO3', 1, 'y', 1),
		(93, 14, 'Chief Warrant Officer 3rd Class', 'Starfleet/y-w2.png', 'CWO3', 1, 'y', 2),
		(94, 14, 'Chief Warrant Officer 3rd Class', 'Starfleet/t-w2.png', 'CWO3', 1, 'y', 3),
		(95, 14, 'Chief Warrant Officer 3rd Class', 'Starfleet/s-w2.png', 'CWO3', 1, 'y', 4),
		(96, 14, 'Chief Warrant Officer 3rd Class', 'Starfleet/v-w2.png', 'CWO3', 1, 'y', 5),
		(97, 14, 'Chief Warrant Officer 3rd Class', 'Marine/g-w2.png', 'CWO3', 1, 'y', 6),
		(98, 14, 'Chief Warrant Officer 3rd Class', 'Starfleet/c-w2.png', 'CWO3', 1, 'y', 7),

		(99, 15, 'Warrant Officer', 'Starfleet/r-w1.png', 'WO', 1, 'y', 1),
		(100, 15, 'Warrant Officer', 'Starfleet/y-w1.png', 'WO', 1, 'y', 2),
		(101, 15, 'Warrant Officer', 'Starfleet/t-w1.png', 'WO', 1, 'y', 3),
		(102, 15, 'Warrant Officer', 'Starfleet/s-w1.png', 'WO', 1, 'y', 4),
		(103, 15, 'Warrant Officer', 'Starfleet/v-w1.png', 'WO', 1, 'y', 5),
		(104, 15, 'Warrant Officer', 'Marine/g-w1.png', 'WO', 1, 'y', 6),
		(105, 15, 'Warrant Officer', 'Starfleet/c-w1.png', 'WO', 1, 'y', 7),

		(106, 16, 'Master Chief Petty Officer', 'Starfleet/r-e9.png', 'MCPO', 1, 'y', 1),
		(107, 16, 'Master Chief Petty Officer', 'Starfleet/y-e9.png', 'MCPO', 1, 'y', 2),
		(108, 16, 'Master Chief Petty Officer', 'Starfleet/t-e9.png', 'MCPO', 1, 'y', 3),
		(109, 16, 'Master Chief Petty Officer', 'Starfleet/s-e9.png', 'MCPO', 1, 'y', 4),
		(110, 16, 'Master Chief Petty Officer', 'Starfleet/v-e9.png', 'MCPO', 1, 'y', 5),
		(111, 16, 'Sergeant Major', 'Marine/g-e9.png', 'SGM', 1, 'y', 6),
		(112, 16, 'Master Chief Petty Officer', 'Starfleet/c-e9.png', 'MCPO', 1, 'y', 7),

		(113, 17, 'Senior Chief Petty Officer', 'Starfleet/r-e8.png', 'SCPO', 1, 'y', 1),
		(114, 17, 'Senior Chief Petty Officer', 'Starfleet/y-e8.png', 'SCPO', 1, 'y', 2),
		(115, 17, 'Senior Chief Petty Officer', 'Starfleet/t-e8.png', 'SCPO', 1, 'y', 3),
		(116, 17, 'Senior Chief Petty Officer', 'Starfleet/s-e8.png', 'SCPO', 1, 'y', 4),
		(117, 17, 'Senior Chief Petty Officer', 'Starfleet/v-e8.png', 'SCPO', 1, 'y', 5),
		(118, 17, 'Master Sergeant', 'Marine/g-e8.png', 'MSG', 1, 'y', 6),
		(119, 17, 'Senior Chief Petty Officer', 'Starfleet/c-e8.png', 'SCPO', 1, 'y', 7),

		(120, 18, 'Chief Petty Officer', 'Starfleet/r-e7.png', 'CPO', 1, 'y', 1),
		(121, 18, 'Chief Petty Officer', 'Starfleet/y-e7.png', 'CPO', 1, 'y', 2),
		(122, 18, 'Chief Petty Officer', 'Starfleet/t-e7.png', 'CPO', 1, 'y', 3),
		(123, 18, 'Chief Petty Officer', 'Starfleet/s-e7.png', 'CPO', 1, 'y', 4),
		(124, 18, 'Chief Petty Officer', 'Starfleet/v-e7.png', 'CPO', 1, 'y', 5),
		(125, 18, 'Gunnery Sergeant', 'Marine/g-e7.png', 'GSGT', 1, 'y', 6),
		(126, 18, 'Chief Petty Officer', 'Starfleet/c-e7.png', 'CPO', 1, 'y', 7),

		(127, 19, 'Petty Officer 1st Class', 'Starfleet/r-e6.png', 'PO1', 1, 'y', 1),
		(128, 19, 'Petty Officer 1st Class', 'Starfleet/y-e6.png', 'PO1', 1, 'y', 2),
		(129, 19, 'Petty Officer 1st Class', 'Starfleet/t-e6.png', 'PO1', 1, 'y', 3),
		(130, 19, 'Petty Officer 1st Class', 'Starfleet/s-e6.png', 'PO1', 1, 'y', 4),
		(131, 19, 'Petty Officer 1st Class', 'Starfleet/v-e6.png', 'PO1', 1, 'y', 5),
		(132, 19, 'Staff Sergeant', 'Marine/g-e6.png', 'SSGT', 1, 'y', 6),
		(133, 19, 'Petty Officer 1st Class', 'Starfleet/c-e6.png', 'PO1', 1, 'y', 7),

		(134, 20, 'Petty Officer 2nd Class', 'Starfleet/r-e5.png', 'PO2', 1, 'y', 1),
		(135, 20, 'Petty Officer 2nd Class', 'Starfleet/y-e5.png', 'PO2', 1, 'y', 2),
		(136, 20, 'Petty Officer 2nd Class', 'Starfleet/t-e5.png', 'PO2', 1, 'y', 3),
		(137, 20, 'Petty Officer 2nd Class', 'Starfleet/s-e5.png', 'PO2', 1, 'y', 4),
		(138, 20, 'Petty Officer 2nd Class', 'Starfleet/v-e5.png', 'PO2', 1, 'y', 5),
		(139, 20, 'Sergeant', 'Marine/g-e5.png', 'SGT', 1, 'y', 6),
		(140, 20, 'Petty Officer 2nd Class', 'Starfleet/c-e5.png', 'PO2', 1, 'y', 7),

		(141, 21, 'Petty Officer 3rd Class', 'Starfleet/r-e4.png', 'PO3', 1, 'y', 1),
		(142, 21, 'Petty Officer 3rd Class', 'Starfleet/y-e4.png', 'PO3', 1, 'y', 2),
		(143, 21, 'Petty Officer 3rd Class', 'Starfleet/t-e4.png', 'PO3', 1, 'y', 3),
		(144, 21, 'Petty Officer 3rd Class', 'Starfleet/s-e4.png', 'PO3', 1, 'y', 4),
		(145, 21, 'Petty Officer 3rd Class', 'Starfleet/v-e4.png', 'PO3', 1, 'y', 5),
		(146, 21, 'Corporal', 'Marine/g-e4.png', 'CPL', 1, 'y', 6),
		(147, 21, 'Petty Officer 3rd Class', 'Starfleet/c-e4.png', 'PO3', 1, 'y', 7),

		(148, 22, 'Crewman', 'Starfleet/r-e3.png', 'CN', 1, 'y', 1),
		(149, 22, 'Crewman', 'Starfleet/y-e3.png', 'CN', 1, 'y', 2),
		(150, 22, 'Crewman', 'Starfleet/t-e3.png', 'CN', 1, 'y', 3),
		(151, 22, 'Crewman', 'Starfleet/s-e3.png', 'CN', 1, 'y', 4),
		(152, 22, 'Crewman', 'Starfleet/v-e3.png', 'CN', 1, 'y', 5),
		(153, 22, 'Private 1st Class', 'Marine/g-e3.png', 'PFC', 1, 'y', 6),
		(154, 22, 'Crewman', 'Starfleet/c-e3.png', 'CN', 1, 'y', 7),

		(155, 23, 'Crewman Apprentice', 'Starfleet/r-e2.png', 'CA', 1, 'y', 1),
		(156, 23, 'Crewman Apprentice', 'Starfleet/y-e2.png', 'CA', 1, 'y', 2),
		(157, 23, 'Crewman Apprentice', 'Starfleet/t-e2.png', 'CA', 1, 'y', 3),
		(158, 23, 'Crewman Apprentice', 'Starfleet/s-e2.png', 'CA', 1, 'y', 4),
		(159, 23, 'Crewman Apprentice', 'Starfleet/v-e2.png', 'CA', 1, 'y', 5),
		(160, 23, 'Private E-2', 'Marine/g-e2.png', 'PV2', 1, 'y', 6),
		(161, 23, 'Crewman Apprentice', 'Starfleet/c-e2.png', 'CA', 1, 'y', 7),

		(162, 24, 'Crewman Recruit', 'Starfleet/r-e1.png', 'CR', 1, 'y', 1),
		(163, 24, 'Crewman Recruit', 'Starfleet/y-e1.png', 'CR', 1, 'y', 2),
		(164, 24, 'Crewman Recruit', 'Starfleet/t-e1.png', 'CR', 1, 'y', 3),
		(165, 24, 'Crewman Recruit', 'Starfleet/s-e1.png', 'CR', 1, 'y', 4),
		(166, 24, 'Crewman Recruit', 'Starfleet/v-e1.png', 'CR', 1, 'y', 5),
		(167, 24, 'Private', 'Marine/g-e1.png', 'PV1', 1, 'y', 6),
		(168, 24, 'Crewman Recruit', 'Starfleet/c-e1.png', 'CR', 1, 'y', 7),

		(169, 25, 'Cadet Senior Grade', 'Starfleet/r-c4.png', 'CTSR', 1, 'n', 1),
		(170, 25, 'Cadet Senior Grade', 'Starfleet/y-c4.png', 'CTSR', 1, 'n', 2),
		(171, 25, 'Cadet Senior Grade', 'Starfleet/t-c4.png', 'CTSR', 1, 'n', 3),
		(172, 25, 'Cadet Senior Grade', 'Starfleet/s-c4.png', 'CTSR', 1, 'n', 4),
		(173, 25, 'Cadet Senior Grade', 'Starfleet/v-c4.png', 'CTSR', 1, 'n', 5),
		(174, 25, 'Cadet Senior Grade', 'Marine/g-c4.png', 'CTSR', 1, 'n', 6),
		(175, 25, 'Cadet Senior Grade', 'Starfleet/c-c4.png', 'CTSR', 1, 'n', 7),

		(176, 26, 'Cadet Junior Grade', 'Starfleet/r-c3.png', 'CTJR', 1, 'n', 1),
		(177, 26, 'Cadet Junior Grade', 'Starfleet/y-c3.png', 'CTJR', 1, 'n', 2),
		(178, 26, 'Cadet Junior Grade', 'Starfleet/t-c3.png', 'CTJR', 1, 'n', 3),
		(179, 26, 'Cadet Junior Grade', 'Starfleet/s-c3.png', 'CTJR', 1, 'n', 4),
		(180, 26, 'Cadet Junior Grade', 'Starfleet/v-c3.png', 'CTJR', 1, 'n', 5),
		(181, 26, 'Cadet Junior Grade', 'Marine/g-c3.png', 'CTJR', 1, 'n', 6),
		(182, 26, 'Cadet Junior Grade', 'Starfleet/c-c3.png', 'CTJR', 1, 'n', 7),

		(183, 27, 'Cadet Sophomore Grade', 'Starfleet/r-c2.png', 'CTSO', 1, 'n', 1),
		(184, 27, 'Cadet Sophomore Grade', 'Starfleet/y-c2.png', 'CTSO', 1, 'n', 2),
		(185, 27, 'Cadet Sophomore Grade', 'Starfleet/t-c2.png', 'CTSO', 1, 'n', 3),
		(186, 27, 'Cadet Sophomore Grade', 'Starfleet/s-c2.png', 'CTSO', 1, 'n', 4),
		(187, 27, 'Cadet Sophomore Grade', 'Starfleet/v-c2.png', 'CTSO', 1, 'n', 5),
		(188, 27, 'Cadet Sophomore Grade', 'Marine/g-c2.png', 'CTSO', 1, 'n', 6),
		(189, 27, 'Cadet Sophomore Grade', 'Starfleet/c-c2.png', 'CTSO', 1, 'n', 7),

		(190, 28, 'Cadet Freshman Grade', 'Starfleet/r-c1.png', 'CTFR', 1, 'n', 1),
		(191, 28, 'Cadet Freshman Grade', 'Starfleet/y-c1.png', 'CTFR', 1, 'n', 2),
		(192, 28, 'Cadet Freshman Grade', 'Starfleet/t-c1.png', 'CTFR', 1, 'n', 3),
		(193, 28, 'Cadet Freshman Grade', 'Starfleet/s-c1.png', 'CTFR', 1, 'n', 4),
		(194, 28, 'Cadet Freshman Grade', 'Starfleet/v-c1.png', 'CTFR', 1, 'n', 5),
		(195, 28, 'Cadet Freshman Grade', 'Marine/g-c1.png', 'CTFR', 1, 'n', 6),
		(196, 28, 'Cadet Freshman Grade', 'Starfleet/c-c1.png', 'CTFR', 1, 'n', 7),

		(197, 29, 'Trainee', 'Starfleet/r-c0.png', 'TRN', 1, 'n', 1),
		(198, 29, 'Trainee', 'Starfleet/y-c0.png', 'TRN', 1, 'n', 2),
		(199, 29, 'Trainee', 'Starfleet/t-c0.png', 'TRN', 1, 'n', 3),
		(200, 29, 'Trainee', 'Starfleet/s-c0.png', 'TRN', 1, 'n', 4),
		(201, 29, 'Trainee', 'Starfleet/v-c0.png', 'TRN', 1, 'n', 5),
		(202, 29, 'Trainee', 'Marine/g-c0.png', 'TRN', 1, 'n', 6),
		(203, 29, 'Trainee', 'Starfleet/c-c0.png', 'TRN', 1, 'n', 7),

		(204, 30, '', 'Starfleet/r-blank.png', '', 1, 'y', 1),
		(205, 30, '', 'Starfleet/y-blank.png', '', 1, 'y', 2),
		(206, 30, '', 'Starfleet/t-blank.png', '', 1, 'y', 3),
		(207, 30, '', 'Starfleet/s-blank.png', '', 1, 'y', 4),
		(208, 30, '', 'Starfleet/v-blank.png', '', 1, 'y', 5),
		(209, 30, '', 'Marine/g-blank.png', '', 1, 'y', 6),
		(210, 30, '', 'Starfleet/c-blank.png', '', 1, 'y', 7),

		(211, 1, '', 'Starfleet/w-blank.png', '', 1, 'y', 8),
		(212, 2, '', 'Starfleet/b-blank.png', '', 1, 'y', 8);" );
		
		echo "You have successfully inserted the data into the tables! You can now begin to use your DS9 SMS site.<br /><br />We recommend that you delete this file now.";
	
		break;
}

?>