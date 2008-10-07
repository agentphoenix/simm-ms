<?php
/*
|---------------------------------------------------------------
| ROOMULAN SMS MOD
|---------------------------------------------------------------
|
| File: sms_mod_rom.php
| Author: David VanScott [davidv@anodyne-productions.com]
| Version: 1.0
|
| Official Romulan mod for SMS 2.6
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
		echo "The official Anodyne Romulan mod will turn your installation of SMS into a sinister Romulan Star Empire vessel, complete with new departments, positions, and ranks that make it easier to play a Romulan game and take all the work out of modding the system yourself. Before you begin, make sure you have removed all DS9 ranks from your <em>images/ranks</em> directory on your server and uploaded the two rank sets found in the zip archive.<br /><br />The first step of this mod will remove all your department, position, and rank data from SMS. The second step will re-add the department, positions, and ranks tables with the new Romulan data. The third step will insert the departments, positions, and ranks into the database.<br /><br />Click the link below to continue to the next step.<br /><br /><strong><a href='sms_mod_rom.php?step=2'>Next Step &raquo;</a></strong>";
		break;
	case 2:
		mysql_query('DROP TABLE sms_departments');
		mysql_query('DROP TABLE sms_positions');
		mysql_query('DROP TABLE sms_ranks');
		
		mysql_query("UPDATE sms_globals SET allowedRanks = 'default,nemesis2', rankSet = 'default' WHERE globalid = 1 LIMIT 1");
		
		mysql_query("UPDATE sms_crew SET displayRank = 'default' WHERE crewType = 'active'");
		
		echo "Your old departments, positions, and ranks tables have been removed. The next step will re-create those tables with the appropriate default values. Click the link below to continue to the next step.<br /><br /><strong><a href='sms_mod_rom.php?step=3'>Next Step &raquo;</a></strong>";
		
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
				
		echo "You have successfully created the new new departments, positions, and ranks tables that will be used by SMS. The next step will populate those tables with the Romulan data. Click the link below to continue to the next step.<br /><br /><strong><a href='sms_mod_rom.php?step=4'>Next Step &raquo;</a></strong>";
	
		break;
	case 4:
		mysql_query("INSERT INTO `sms_departments` (`deptName`, `deptDesc`, `deptOrder`, `deptClass`) VALUES
			('Command', 'As on most space-faring vessels the Command Department is made up of those officers deemed to be in the day-to-day control of the Warbird. The Apart from the Commanding Officer, other members of the Command Staff also serve in other faculties/ departments.', 0, 1),
			('Scientific Research', 'Responsible for all the scientific data the warbird collects, and the distribution of such data to specific section within the department for analysis. They are also responsible with providing the ship\'s captain with scientific information needed for command decisions.', 1, 1),
			('Medical Sciences', 'Responsible for the physical health of the entire crew, but does more than patch up injured crew members. Their function is to ensure that they do not get sick or injured to begin with, and to this end monitors their health and conditioning with regular check ups. If necessary, the Leader of Medical Sciences can remove anyone from duty, excluding the Commander. Besides this they available to provide medical advice to any individual who requests it. Additionally the Seniors of the 3 bracnhes of Medical Sciences as well as the Leader of the Department are also responsible for all aspect of the medical deck, such as the Medical labs, Surgical suites, Psychiatric treatment areas.', 2, 1),
			('Research &amp; Development', 'This department is of an oddity only found within the Romulan Star Empire. The entire purpose of this department is to take alien technology (e.g. Starfleet) and backwards engineer it and to create something similiar to the original but allowing it to be compatible with other Romulan Technologies.', 3, 1),
			('Flight Control', 'A Flight Controller must always be present on the bridge of a starship, and every vessel has a number of Flight Control Officers to allow shift rotations. They plot courses, supervises the computers piloting, corrects any flight deviations and pilots the ship manually when needed.', 4, 1),
			('Warbird Control', 'Responsibility of ensuring that ship functions, such as the use of the lateral sensor array, do not interfere with one and another. They must prioritize resource allocations, so that the most critical activities can have every chance of success. If so required, they can curtail shipboard functions if they thinks they will interfere with the ship\'s current mission or routine operations.', 5, 1),
			('Singularity Control', 'Responsible for the condition of all systems and equipment on board the Warbird. They oversee maintenance, repairs and upgrades of all equipment. They control the output and maintain the operational status of the Singularity Drive. They also responsible for the many repairs teams during crisis situations.', 6, 1),
			('Cloaking Control', 'Responsible for the smooth operation of the Cloaking Device and other related systems, unlike other Control departments, Cloaking Control is a very small department, with members usually having served for many tours within an Singularity Control Department, and then undergoing specialist Cloaking Technology Training Programmes. The department only contains 3 staff members.', 7, 1),
			('Communications Control', 'Monitors any and all transmissions aboard the warbird, as well as externally. Communications Officers are experienced linguist, proficient in many different languages.', 8, 1),
			('Weapons Control', 'They are the vessels gunman.They responsible for the ships weapon system, and is also the COs tactical advisor in Star Ship Combat matters. Very often Weapons Officers are also trained in ground combat and small unit tactics. There is much more to Weapons Control than simply overseeing the weapons console on the bridge. Weapons Control maintains the weapons systems aboard the warbird, maintaining and reloading photons magazines. Tactical planning and current Intelligence analysis (if no Intelligence operatives are aboard) is also overseen by the tactical department.', 9, 1),
			('Tal Diann', 'Responsible for collected and collating all information that they deem appropriate for delivery to the Command Staff. Unlike most departments the Tal Diann are considered a seperate force within the Galae in the same manner as the Tal Shi\'ar. They are often at odds with the Tal Shi\'ar Agent on-board the warbird.', 10, 1),
			('Reman Commando Corps', 'It is their duty is to ensure the safety of ship and crew. The Commando Commander takes it as their personal duty to protect the Commanding Officer on landing parties. They are also responsible for people under arrest and the safety of guests, liked or not. They are also required to take command of any special ground operations. The Reman Commando Corps is the only branch of the Galae to have an enlisted service. The RCC is always controlled by Galae Officers, but the rank and file is made up of Remans who are considered too inferior to hold a commissioned rank.', 11, 1)");
		
		sleep(1);
		
		mysql_query("INSERT INTO `sms_positions` (`positionName`, `positionDesc`, `positionDept`, `positionOrder`, `positionOpen`, `positionType`) VALUES 
		('Commander', 'Seniormost officer on a warbird and responsible for everything that happens. The Commander receives their orders straight from the Star Empire leadership.', 1, 0, 1, 'senior'),
		('Sub-Commander', 'Second seniormost officer on a warbird and usually hand-selected by the Commander. The Sub-Commander is responsible for helping to run the warbird and carry out the orders of the Commander.', 1, 1, 1, 'senior'),
		('Sciences Head', 'A position held by the most senior Sciences Department Leader, included in Command Staff if not held by the First Officer.', 1, 2, 1, 'senior'),
		('Control Head', 'A position held by the most senior Support Department Leader, included in Command Staff if not held by the First Officer.', 1, 3, 1, 'senior'),
		('Warfare Head', 'A position held by the most senior Warfare Department Leader, included in Command Staff if not held by the First Officer.', 1, 4, 1, 'senior'),
		('Protocol Officer/Tal Prai\'ex Representative', 'A stand alone position, this person is usually a member of the Tal Prai\'ex and ensures that all actions carried out by the Command Staff are in-line with the policies of the Praetorate. Generally seen as the Praetor\'s spy on a Warbird.', 1, 5, 1, 'senior'),
		('Tal Shi\'ar Representative', 'If the Protocol Officer is the Praetor\'s eyes and ears on a Warbird, the Tal Shi\'ar Representative is his/her nemesis, as they act as the eyes and ears of the Tal Shi\'ar on the vessel.', 1, 6, 1, 'senior'),
		
		('Leader of Science', 'The most senior Scientist on board the warbird. An expert in several specialist fields of scientific research, but also an experienced generalist to be able to deal and understand all information they gather.', 2, 0, 1, 'senior'),
		('Senior Scientist', 'The second two most senior Scientist on board the warbird. An expert in two specialist fields of scientific research, but also an experienced generalist to be able to deal and understand all information they gather.', 2, 1, 2, 'crew'),
		('Specialist Scientist', 'A Specialist Scientist on board the warbird. An expert in a specialist field of scientific research, but also an experienced generalist to be able to deal and understand all information they gather.', 2, 2, 3, 'crew'),
		('General Scientist', 'A Generalist Scientist on board the warbird. They are not yet considered to be an expert in a particular field of scientific research, but considered well versed in the many fields of scienctific research to carry out solo study.', 2, 3, 5, 'crew'),
		('Lower Scientist', 'A Lower Generalist Scientist on board the warbird. They are not yet considered to be an expert in a particular field of scientific research, but considered versed in the many fields of scienctific research to carry out supervised study.', 2, 4, 5, 'crew'),
		('Research Aide', 'Generally an officer carrying out Military Service, they act as a general dogs-body to other scientists and aide them in their research programmes.', 2, 5, 10, 'crew'),
		
		('Leader of Medical Sciences', 'The most Senior Doctor on board the Warbird, considered to be an expert in the 3 main branches of Medicial Sciences, Medicine, Surgery and Psychiatry. It is to him/her that the Senior Surgeon, Senior Physician and Senior Psychiatrist all report.', 3, 0, 1, 'senior'),
		('Senior of Surgery', 'The most Senior Surgeon on board the Warbird (save for the Leader of Medical Sciences), considered to be an expert in Surgery. It is to him/her that the Specialist (Surgical) Doctors report.', 3, 1, 1, 'crew'),
		('Senior of Medicine', 'The most Senior Physician on board the Warbird (save for the Leader of Medical Sciences), considered to be an expert in Medicine. It is to him/her that the Specialist (Medical) Doctors report.', 3, 2, 1, 'crew'),
		('Senior of Psychiatry', 'The most Senior Psychiatrist on board the Warbird (save for the Leader of Medical Sciences), considered to be an expert in Psychiatry. It is to him/her that the Specialist (Psychiaric) Doctors report.', 3, 3, 1, 'crew'),
		('Specialist Doctor', 'Doctors whom Specialise in one of the three main branches of Medical Sciences, and report to the Senior of their Branch.', 3, 5, 6, 'crew'),
		('General Doctor', 'Unlike a Specialist, these Doctors are rather seen as jack of all trades, and can deal with most medical situations, but may refer to a Specialist', 3, 6, 6, 'crew'),
		('Medical Aide', 'Generally an officer carrying out Military Service, they act as a general dogs-body for Doctors to assist them in medical treatments etc.', 3, 7, 10, 'crew'),
		
		('Leader of R&amp;D', 'This person is responsible for the gathering of all alien technologies and reverse engineering them to be viable for use with other Romulan Technologies. Also works with Ship and Singularity Control for intergrating such technologies into the Warbird.', 4, 0, 1, 'senior'),
		('Senior Developer', 'This person acts as a deputy to the Leader of R&D, assisting him/her in reverse engineering technology for use within the Romulan Star Empire.', 4, 1, 2, 'crew'),
		('Developer', 'This person assists in reverse engineering technology for use within the Romulan Star Empire.', 4, 2, 4, 'crew'),
		('Researcher', 'This person carries out research into technologies and acts as collector of alien Technologies for use in reverse engineering.', 4, 3, 4, 'crew'),
		('Research Aide', 'Generally an officer carrying out Military Service, they act as a general dogs-body for Developers and Researchers.', 4, 4, 10, 'crew'),
		
		('Leader of Flight Control', 'The most senior Pilot and Navigator on board the warbird. An expert all aspects of flight control and spacial navigation.', 5, 0, 1, 'senior'),
		('Senior Controller', 'The second two most senior Pilots and Navigators on board the warbird. An expert all aspects of flight control and spacial navigation.', 5, 1, 2, 'crew'),
		('Specialist Controller', 'An expert all aspects of flight control and spacial navigation.', 5, 2, 4, 'crew'),
		('General Controller', 'An experienced Pilot and/or Navigator.', 5, 3, 8, 'crew'),
		('Lower Controller', 'A junior Pilot and/or Navigator.', 5, 4, 8, 'crew'),
		('Control Aide', 'enerally an officer carrying out Military Service, they act as a general dogs-body for the Flight Control Department.', 5, 5, 10, 'crew'),
		
		('Leader of Warbird Control', 'The most senior Techie on board the warbird. An expert in all aspects of warbird systems operations.', 6, 0, 1, 'senior'),
		('Senior Controller', 'The second two most senior Techies on board the warbird. An expert in all aspects of warbird systems operations.', 6, 1, 2, 'crew'),
		('Specialist Controller', 'An expert all aspects of warbird systems operations.', 6, 2, 4, 'crew'),
		('General Controller', 'An experienced Techie.', 6, 3, 8, 'crew'),
		('Lower Controller', 'A junior techie.', 6, 4, 8, 'crew'),
		('Control Aide', 'Generally an officer carrying out Military Service, they act as a general dogs-body for the Warbird Control Department.', 6, 5, 10, 'crew'),
		
		('Leader of Singularity Control', 'The most senior Engineer on board the warbird. An expert in all aspects of Warbird Engineering.', 7, 0, 1, 'senior'),
		('Senior Controller', 'The second two most senior Engineers on board the warbird. An expert in all aspects of Warbird Engineering.', 7, 1, 2, 'crew'),
		('Specialist Controller', 'An expert all aspects of warbird engineering.', 7, 2, 4, 'crew'),
		('General Controller', 'An experienced engineer.', 7, 3, 8, 'crew'),
		('Lower Controller', 'A junior engineer.', 7, 4, 8, 'crew'),
		('Control Aide', 'Generally an officer carrying out Military Service, they act as a general dogs-body for the Singularity Control Department.', 7, 5, 10, 'crew'),
		
		('Leader of Cloaking Control', 'A seasoned and experienced Engineer, who has proven him or herself an expert in all brances of Engineering, and has served as a Leader of Singularity Control or Warbird Control and is loyal enough to the RSE to warrant his or her being trained in the classified Cloaking Device.', 8, 0, 1, 'senior'),
		('Senior Controller', 'A seasoned and experienced Engineer although less so than the Leader of Singularity Control, who has proven him or herself an expert in all brances of Engineering, and has served as a Leader of Singularity Control or Warbird Control and is loyal enough to the RSE to warrant his or her being trained in the classified Cloaking Device.', 8, 1, 2, 'crew'),
		('Specialist Controller', 'A seasoned and experienced Engineer although less so than the Senior Controller, who has proven him or herself an expert in all brances of Engineering, and has served as a Leader of Singularity Control or Warbird Control and is loyal enough to the RSE to warrant his or her being trained in the classified Cloaking Device.', 8, 2, 4, 'crew'),
		
		('Leader of Communications Control', 'The most senior Linguists on board the warbird. An expert in over 20 languages, and in all Communications equipment.', 9, 0, 1, 'senior'),
		('Senior Controller', 'The second two most senior Linguists on board the warbird. An expert in over 10 languages, and in all Communications equipment.', 9, 1, 2, 'crew'),
		('Specialist Controller', 'An expert in over 10 languages and Communications equipment.', 9, 2, 4, 'crew'),
		('General Controller', 'An experienced linguist.', 9, 3, 8, 'crew'),
		('Lower Controller', 'An junior linguist.', 9, 4, 8, 'crew'),
		('Control Aide', 'Generally an officer carrying out Military Service, they act as a general dogs-body for the Communications Control Department.', 9, 5, 10, 'crew'),
		
		('Weapons Master/Mistress', 'A seasoned and experienced weapons expert, as well as stratetician, who ensures the safety of the vessel from external forces via useage of weapons. Also maintains all weapons on board the Warbird.', 10, 0, 1, 'senior'),
		('Senior Weapons Controller', 'A highly experienced weapons expert, as well as stratetician, who ensures the safety of the vessel from external forces via useage of weapons. Also maintains all weapons on board the Warbird.', 10, 1, 2, 'crew'),
		('Weapons Master/Mistress', 'An experienced weapons officer who ensures the safety of the vessel from external forces. These individuals have a high level of expertise in all weapons aboard a Warbird.', 10, 0, 2, 'senior'),
		('Specialist Controller', 'Specialized weapons officers who help ensure the safety of the vessel from external forces. Generally, these specialists have trained on one or two specific weapons systems instead of general weapons knowledge.', 10, 2, 4, 'crew'),
		('General Controller', 'An experienced weapons officer who help ensure the safety of the vessel from external forces.', 10, 3, 8, 'crew'),
		('Lower Controller', 'A junior weapons officer who works with the General Controllers to ensure the safety of the vessel from external forces.', 10, 4, 8, 'crew'),
		('Control Aide', 'Generally an officer carrying out Military Service, they act as a general dogs-body for the Weapons Control Department.', 10, 5, 10, 'crew'),
		
		('Tal Diann Master/Mistress', 'The gatherer of all intelligence on the vessel for use by the Command Staff. Often works with the Commander to subvert the machinations of the Tal Shi\'ar Representative due to the animosity between the two Intelligence groups.', 11, 0, 1, 'senior'),
		('Tal Diann Deputy Leader', 'An experienced and trusted intelligence asset that works to help the Master/Mistress subert the machinations of the Tal Shi\'ar aboard Warbirds.', 11, 1, 2, 'crew'),
		('Tal Diann Officer', 'An experienced intelligence asset aboard a Warbird that works to subvert the machinations of the Tal\'Shiar on their vessel.', 11, 2, 4, 'crew'),
		
		('Reman Master/Mistress', 'S/he is the person that controls all the Reman Slaves on board the Warbird, ensuring their loyalty to the RSE, as well as disciplining them. S/he has a station on the Command Deck and monitors internal security. Also s/he is present on landing and boarding parties.', 12, 0, 1, 'senior'),
		('Slave Overseer', 'The Over-seer of one unit of Reman Slaves.', 12, 1, 4, 'crew'),
		('Elder Slave', 'The most senior Reman Commando within a Unit.', 12, 2, 4, 'crew'),
		('Slave', 'A normal Reman Commando.', 12, 3, 20, 'crew')");
		
		sleep(1);
		
		mysql_query("INSERT INTO `sms_ranks` (`rankName`, `rankShortName`, `rankImage`, `rankOrder`, `rankClass`) VALUES 
			('Admiral', 'ADM', 's-o8.png', 0, 1),
			('Commodore', 'COMO', 's-o7.png', 1, 1),
			('Commander', 'CMDR', 's-o6.png', 2, 1),
			('Sub-Commander', 'SCMDR', 's-o5.png', 3, 1),
			('Centurion', 'CENT', 's-o4.png', 4, 1),
			('Lieutenant', 'LT', 's-o3.png', 5, 1),
			('Sub-Lieutenant', 'SLT', 's-o2.png', 6, 1),
			('Uhlan', 'UHL', 's-o1.png', 7, 1),
			('', '', 'blank.png', 8, 1),
			('General', 'GEN', 'g-o8.png', 9, 1),
			('Sub-General', 'SGEN', 'g-o7.png', 10, 1),
			('Colonel', 'COL', 'g-o6.png', 11, 1),
			('Sub-Colonel', 'SCOL', 'g-o5.png', 12, 1),
			('Major', 'MAJ', 'g-o4.png', 13, 1),
			('Captain', 'CAPT', 'g-o3.png', 14, 1),
			('Lieutenant', 'LT', 'g-o2.png', 15, 1),
			('Uhlan', 'UHL', 'g-o1.png', 16, 1),
			('', '', 'blank.png', 17, 1)
		");
		
		echo "You have successfully inserted the data into the tables! You can now begin to use your Romulan SMS site.<br /><br />We recommend that you delete this file now.";
	
		break;
}

?>