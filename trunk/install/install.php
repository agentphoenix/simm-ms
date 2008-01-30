<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: install/install.php
Purpose: Installation script for SMS

System Version: 2.6.0
Last Modified: 2008-01-30 1157 EST
**/

session_start();

/* define the step var */
$step = $_GET['step'];

/* pull in the db connections */
include_once( '../framework/variables.php' );

/* do some logic based on the step */
if( !isset( $step ) ) {
	$step = 1;
}

/*
some error checking in case someone hasn't taken care of
the variables.php stuff
*/
if( $step > 2 && !isset( $webLocation ) ) {
	$step = 2;
	$varError = 1;
}

switch( $step ) {
	
	/*
		step 3 attempts to write the variables file that the admin provided
		all the information for in the previous step
	*/
	case 2:
		/** ERROR CHECKING FOR USER INPUT FROM STEP 1 **/
		
		/* make sure the web location has a trailing slash */
		if( substr( $_POST['webLocation'], -1 ) == "/" ) {
			$webLocation1 = $_POST['webLocation'];
		} else {
			$webLocation1 = $_POST['webLocation'] . "/";
		}
		
		/* make sure the web location starts with http:// */
		if( substr( $webLocation1, 0, 7 ) == "http://" ) {
			/* don't do anything if this is right */
			$webLocation = $webLocation1;
		} else {
			$webLocation = "http://" . $webLocation1;
		}
		
		/* make sure the database server doesn't start with http:// */
		if( substr( $_POST['dbServer'], 0, 7 ) == "http://" ) {
			$dbServer = str_replace( "http://", "", $_POST['dbServer'] );
		} else {
			$dbServer = $_POST['dbServer'];
		}
		
		/** END ERROR CHECKING **/
	
		$dbErrorMessage = stripslashes( $_POST['errorMessage'] );
		
		$filename = '../framework/variables.php';
		$somecontent = "<?php
	
\$webLocation = \"$webLocation\";

\$dbServer = \"$dbServer\";
\$dbName = \"$_POST[dbName]\";
\$dbUser = \"$_POST[dbUser]\";
\$dbPassword = \"$_POST[dbPassword]\";
\$dbErrorMessage = \"$dbErrorMessage\";

?>";

		if( !isset( $varError ) ) {
			/* set up session variables */
			$_SESSION['webLocation'] = $webLocation;
			$_SESSION['dbServer'] = $dbServer;
			$_SESSION['dbName'] = $_POST['dbName'];
			$_SESSION['dbUser'] = $_POST['dbUser'];
			$_SESSION['dbPassword'] = $_POST['dbPassword'];
			$_SESSION['dbErrorMessage'] = $dbErrorMessage;
		}
		
		if( is_writable( $filename ) ) {
			chmod( $filename, 0777 );
			
			if( chmod( $filename, 0777 ) === FALSE ) {
				$write = "failed";
			} else {
				if( !$handle = fopen( $filename, 'w' ) ) {
					$write = "failed";
				} if( fwrite( $handle, $somecontent ) === FALSE ) {
					$write = "failed";
				} else {
					fclose( $handle );
					$write = "success";
				}
				
				chmod( $filename, 0644 );
			}
		} else {
			$write = "failed";
		}
		break;
	
	/* step 4 handles creating the database structure */
	case 3:
		/* pull in the DB connection variables */
		require_once( '../framework/dbconnect.php' );
		
		/* pull in the structure file */
		require_once( "resource_structure.php" );
		break;
	
	/*
		step 5 handles inserting the necessary data into the database that lines up
		with the tables created in step 3
	*/
	case 4:
		/* pull in the DB connection variables */
		require_once( '../framework/dbconnect.php' );
		
		/* pull in the data file */
		require_once( "resource_data.php" );
		break;
	
	/*
		step 6 handles inserting the admin's character into the database, setting their
		access level, and adjusting the position they chose in step 4
	*/
	case 5:
		/* pull in the DB connection variables */
		require_once( '../framework/dbconnect.php' );
		
		$md5password = md5( $_POST['password'] );
		
		/* create the variables for access */
		$levelsPost = "post,p_addjp,p_addnews,p_log,p_addlog,p_pm,p_mission,p_addmission,p_jp,p_news,p_missionnotes";
		$levelsManage = "manage,m_globals,m_messages,m_specs,m_posts,m_logs,m_news,m_missionsummaries,m_missionnotes,m_createcrew,m_crew,m_coc,m_npcs2,m_removeaward,m_strike,m_giveaward,m_missions,m_departments,m_moderation,m_ranks,m_awards,m_positions,m_tour,m_decks,m_database,m_newscat3,m_docking,m_catalogue";
		$levelsReports = "reports,r_about,r_count,r_strikes,r_activity,r_progress,r_versions,r_milestones";
		$levelsUser = "user,u_nominate,u_inbox,u_account2,u_status,u_options,u_bio3,u_stats,u_site";
		$levelsOther = "x_skindev,x_approve_users,x_approve_posts,x_approve_logs,x_approve_news,x_approve_docking,x_update,x_access,x_menu";
		
		/* create the user */
		$createUser = "INSERT INTO sms_crew ( crewid, username, password, email, firstName, middleName, lastName, gender, species, rankid, positionid, joinDate, accessPost, accessManage, accessReports, accessUser, accessOthers ) ";
		$createUser.= "VALUES ( '1', '$_POST[username]', '$md5password', '$_POST[email]', '$_POST[firstName]', '$_POST[middleName]', '$_POST[lastName]', '$_POST[gender]', '$_POST[species]', '$_POST[rank]', '$_POST[position]', UNIX_TIMESTAMP(), '$levelsPost', '$levelsManage', '$levelsReports', '$levelsUser', '$levelsOther' )";
		$createUserResult = mysql_query( $createUser );
		
		/* update the position they're being given */
		$positionFetch = "SELECT positionid, positionOpen FROM sms_positions ";
		$positionFetch.= "WHERE positionid = '$_POST[position]' LIMIT 1";
		$positionFetchResult = mysql_query( $positionFetch );
		$positionX = mysql_fetch_row( $positionFetchResult );
		$open = $positionX[1];
		$revised = ( $open - 1 );
		$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
		$updatePosition.= "WHERE positionid = '$_POST[position]' LIMIT 1";
		$updatePositionResult = mysql_query( $updatePosition );
		
		break;
	
	/*
		step 7 handles updating the globals that are set during step 5, including ship name,
		ship prefix, and ship registry
	*/
	case 6:
		/* pull in the DB connection variables */
		require_once( '../framework/dbconnect.php' );
		
		/* update the globals */
		$updateGlobals = "UPDATE sms_globals SET shipName = '$_POST[shipName]', shipPrefix = '$_POST[shipPrefix]', shipRegistry = '$_POST[shipRegistry]', emailSubject = '[" . $_POST['shipPrefix'] . " " . $_POST['shipName'] . "]' WHERE globalid = '1' LIMIT 1";
		$updateGlobalsResult = mysql_query( $updateGlobals );
		break;

} /* close the switch */

$installSteps = array(
	1	=>	array('Basic Information', 'step-1.png', 'step-1-active.png'),
	2	=>	array('Build the Database', 'step-2.png', 'step-2-active.png'),
	3	=>	array('Populate the Database', 'step-3.png', 'step-3-active.png'),
	4	=>	array('Create Your Character', 'step-4.png', 'step-4-active.png'),
	5	=>	array('Simm Information', 'step-5.png', 'step-5-active.png'),
	6	=>	array('Finalize Installation', 'step-6.png', 'step-6-active.png'),
);

?>

<html>
<head>
	<title>SMS 2.6 :: Fresh Install</title>
	<link rel="stylesheet" type="text/css" href="install.css" />
</head>
<body>
	<div class="header-install">
		<h1>SMS 2.6 Fresh Install</h1>
	</div> <!-- close .header -->
	
	<div id="install">	
		
		<div class="left">
			<ul>
				<?php
				
				foreach($installSteps as $a => $b)
				{
					if($a == $step)
					{
						echo "<li class='active'>";
						echo "<img src='" . $b[2] . "' alt='' border='0' class='step-image' />";
					}
					else
					{
						echo "<li>";
						echo "<img src='" . $b[1] . "' alt='' border='0' class='step-image' />";
					}
					
					echo $b[0];
					echo "</li>";
				}
				
				?>
			</ul>
		</div>
		
		<div class="right">
			<h1>Step <?=$step . " &ndash; " . $installSteps[$step][0];?></h1>
			
		<?php
		
		switch( $step )
		{
			case 1:
			
			/* start to build the url */
			$url = $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
			
			/* toss the url into an array and split at the slashes */
			$urlArray = explode("/", $url);
			
			/* drop the last 2 items off the array (install.php and install/) */
			array_pop($urlArray);
			array_pop($urlArray);
			
			/* put the url back together */
			$url = implode("/", $urlArray);
			
			/* append the http and trailing slash */
			$url = "http://" . $url . "/";
			
		?>
			
			Please provide the following information to continue with the installation.<br /><br />
			
			<form method="post" action="install.php?step=3">
				<table width="100%">
					<tr>
						<td colspan="3" class="fontLarge">Website URL</td>
					</tr>
					<tr>
						<td colspan="3">
							The web location is a required variable in order for SMS to know where
							your site is and where it should be pointing users. We have done our best
							to determine what your web location is, but please verify that the value
							in the text box below is accurate. To verify this, please look at the URL
							in your address bar and make sure it matches (up to, but not including the
							<i>install/install.php</i> portion).
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="text" name="webLocation" size="45" value="<?php echo $url; ?>" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					
					<tr>
						<td colspan="3" class="fontLarge">Database Server</td>
					</tr>
					<tr>
						<td colspan="3">
							The database server is where your MySQL database is located. In most cases,
							using localhost is fine. If your host has given you another means to connect,
							or you would like to connect through a socket, you can change the value in
							the text box below.
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="text" name="dbServer" size="45" value="localhost" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					
					<tr>
						<td colspan="3" class="fontLarge">Database Name</td>
					</tr>
					<tr>
						<td colspan="3">
							The database name is the specific database that SMS will look at for all the
							information it needs. You should have received that information from your
							host when you opened your account. If you do not know the name of your database,
							please contact your host for that information.
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="text" name="dbName" size="45" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					
					<tr>
						<td colspan="3" class="fontLarge">Database User</td>
					</tr>
					<tr>
						<td colspan="3">
							The database user is the username you use to access the database you specified
							above. You should have received that information from your host when you opened
							your account. If you do not know your database username, please contact your 
							host for that information.
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="text" name="dbUser" size="45" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					
					<tr>
						<td colspan="3" class="fontLarge">Database Password</td>
					</tr>
					<tr>
						<td colspan="3">
							The database password is the password used by your MySQL database to make sure
							that no one besides you can access it. You should have received that information
							from your host when you opened your account. If you do not know your database
							password, please contact your host for that information.
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="text" name="dbPassword" size="45" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					
					<tr>
						<td colspan="3" class="fontLarge">Database Error Message</td>
					</tr>
					<tr>
						<td colspan="3">
							On the off chance that your connection to the database fails or is not active
							for whatever reason, the error message will be displayed for your users to
							see. You can choose to leave the default message below or set your own error
							message.
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<textarea name="errorMessage" rows="4" style="width:329px;">A database error has occurred! Please try again later.</textarea>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>

					<tr>
						<td colspan="3">
							<input type="submit" name="submit" class="installButton" value="Next Step &raquo;" />
						</td>
					</tr>
				</table>
			</form>
			
		<?php
		
			break;
			case 2:
		
		?>
			
			<div align="center"><b>Installation Progress</b><br /></div>
			<div class="status">
				<div class="step2">&nbsp;</div>
			</div>
			<br /><br />
			
			<? if( isset( $varError ) ) { ?>
			
			<div class="code">
				<b class="red">The database connection file (framework/variables.php) is
				not correctly formatted and connecting to the database may fail.
				This is possibly due to the file being empty or a missing web
				location variable. You MUST have a web location variable and all
				of your connection information to continue. Please paste the
				following information into the file and try again.</b><br /><br />
				
				<? print( htmlentities( '<?php' ) ); ?><br /><br />
				
				$webLocation = "<?=$_SESSION['webLocation'];?>";<br /><br />
				$dbServer = "<?=$_SESSION['dbServer'];?>";<br />
				$dbName = "<?=$_SESSION['dbName'];?>";<br />
				$dbUser = "<?=$_SESSION['dbUser'];?>";<br />
				$dbPassword = "<?=$_SESSION['dbPassword'];?>";<br />
				$dbErrorMessage = "<?=$_SESSION['dbErrorMessage'];?>";<br /><br />
				
				<? print( htmlentities( '?>' ) ); ?>
			</div>
			<br />
			
			<? } ?>
			
			<? if( !isset( $varError ) && $write == "failed" ) { ?>
			
			It appears that, for security reasons, your server does not allow opening and writing files. 
			Please open the file <b>variables.php</b> from the <b>framework</b> folder and insert 
			the following code:<br /><br />
			
			<div class="code">
				<? print( htmlentities( '<?php' ) ); ?><br /><br />
				
				$webLocation = "<?=$webLocation;?>";<br /><br />
				$dbServer = "<?=$dbServer;?>";<br />
				$dbName = "<?=$_POST['dbName'];?>";<br />
				$dbUser = "<?=$_POST['dbUser'];?>";<br />
				$dbPassword = "<?=$_POST['dbPassword'];?>";<br />
				$dbErrorMessage = "<?=$dbErrorMessage;?>";<br /><br />
				
				<? print( htmlentities( '?>' ) ); ?>
			</div>
			<br />
			
			Once you have completed this, you may continue with the installation.<br /><br />
			
			<? } else { ?>
			
			You have successfully written the file containing all of the database connection
			parameters!<br /><br />
			
			<? } ?>
			
			SMS 2 includes many of the features from SMS 1.5, including the awards system and starbase
			docking system. In addition, SMS 2 introduces a strike system so that COs can easily give,
			remove, and track strikes against players. Sporting a redesigned framework, SMS 2 makes it
			easier than ever to do the things you want to do, including the ability for players to hold two
			positions. With more robust user controls, it's easy now to let your department heads take
			care of NPCs within their department, leaving you to spend more time simming with your
			crew instead of taking care of the little things.<br /><br />
			
			Please proceed to the next step to build the SMS 2 database and create your character
			which you'll use to administer SMS.<br /><br /><br />
			
			<form method="post" action="install.php?step=4">
				<input type="submit" name="submit" class="installButton" value="Next Step &raquo;" />
			</form>
			
		<?php
			
			break;
			case 3:
		
		?>
			
			<div align="center"><b>Installation Progress</b><br /></div>
			<div class="status">
				<div class="step3">&nbsp;</div>
			</div>
			<br /><br />
			
			You have successfully created the SMS database that will drive the site!<br /><br /><br />
			
			<form method="post" action="install.php?step=5">
				<input type="submit" name="submit" class="installButton" value="Next Step &raquo;" />
			</form>
			
		<?php
		
			break;
			case 4:
		
		?>
			
			<div align="center"><b>Installation Progress</b><br /></div>
			<div class="status">
				<div class="step4">&nbsp;</div>
			</div>
			<br /><br />
			
			Use this page to create your character. You will use the username and password to log in
			to your SMS site, so make sure you remember it. Once you have set up SMS, you can edit 
			your biography.
			<br /><br />
			
			<br /><br />
			
			<form method="post" action="install.php?step=6">
				<table width="100%">
					<tr>
						<td class="label">Username</td>
						<td>&nbsp;</td>
						<td><input type="text" name="username" size="32" />
					</tr>
					<tr>
						<td class="label">Password</td>
						<td>&nbsp;</td>
						<td><input type="password" name="password" size="32" />
					</tr>
					<tr>
						<td class="label">Email Address</td>
						<td>&nbsp;</td>
						<td><input type="text" name="email" size="32" />
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td class="label">First Name</td>
						<td>&nbsp;</td>
						<td><input type="text" name="firstName" /></td>
					</tr>
					<tr>
						<td class="label">Middle Name</td>
						<td>&nbsp;</td>
						<td><input type="text" name="middleName" /></td>
					</tr>
					<tr>
						<td class="label">Last Name</td>
						<td>&nbsp;</td>
						<td><input type="text" name="lastName" /></td>
					</tr>
					<tr>
						<td class="label">Gender</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" name="gender" value="Male" checked="yes" /> Male
							&nbsp;&nbsp;
							<input type="radio" name="gender" value="Female" /> Female
						</td>
					</tr>
					<tr>
						<td class="label">Species</td>
						<td>&nbsp;</td>
						<td><input type="text" name="species" /></td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<?
					
					$ranks = "SELECT rank.rankid, rank.rankName, rank.rankImage, dept.deptColor FROM sms_ranks AS rank, ";
					$ranks.= "sms_departments AS dept WHERE dept.deptClass = rank.rankClass AND dept.deptDisplay = 'y' ";
					$ranks.= "GROUP BY rank.rankid ORDER BY rank.rankClass, rank.rankOrder ASC";
					$ranksResult = mysql_query( $ranks );
					
					$positions = "SELECT position.positionid, position.positionName, dept.deptName, ";
					$positions.= "dept.deptColor FROM sms_positions AS position, sms_departments AS dept ";
					$positions.= "WHERE position.positionOpen > '0' AND dept.deptid = position.positionDept ";
					$positions.= "AND dept.deptDisplay = 'y' ORDER BY position.positionid ASC";
					$positionsResult = mysql_query( $positions );
					
					?>
					<tr>
						<td class="label">Rank</td>
						<td>&nbsp;</td>
						<td>
							<select name="rank">
								<?
								
								while( $rank = mysql_fetch_assoc( $ranksResult ) ) {
									extract( $rank, EXTR_OVERWRITE );
							
									echo "<option value='" . $rank['rankid'] . "' style='background:#000 url( ../images/ranks/default/" . $rank['rankImage'] . " ) no-repeat 0 100%; height:40px; color:#" . $rank['deptColor'] . ";'>" . $rank['rankName'] . "</option>";
								
								}
								
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="label">Position</td>
						<td>&nbsp;</td>
						<td>
							<select name="position">
							<?
							
							while( $position = mysql_fetch_assoc( $positionsResult ) ) {
								extract( $position, EXTR_OVERWRITE );
						
								echo "<option value='" . $position['positionid'] . "' style='color:#" . $position['deptColor'] . ";'>" . $position['deptName'] . " - " . $position['positionName'] . "</option>";
								
							}
							
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td>
							<input type="submit" name="submit" class="installButton" value="Next Step &raquo;" />
						</td>
					</tr>
				</table>
			</form>
			
		<?php
		
			break;
			case 5:
			
		?>
					
			<div align="center"><b>Installation Progress</b><br /></div>
			<div class="status">
				<div class="step5">&nbsp;</div>
			</div>
			<br /><br />
			
			<form method="post" action="install.php?step=7">
				<table width="100%">
					<tr>
						<td class="label">Ship Prefix</td>
						<td>&nbsp;</td>
						<td><input type="text" name="shipPrefix" size="32" maxlength="10" value="USS" />
					</tr>
					<tr>
						<td class="label">Ship Name</td>
						<td>&nbsp;</td>
						<td><input type="text" name="shipName" size="32" maxlength="32" />
					</tr>
					<tr>
						<td class="label">Ship Registry</td>
						<td>&nbsp;</td>
						<td><input type="text" name="shipRegistry" size="32" maxlength="16" />
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td>
							<input type="submit" name="submit" class="installButton" value="Next Step &raquo;" />
						</td>
					</tr>
				</table>
			</form>
			
		<?php
		
			break;
			case 6:
		
		?>
			
			<div align="center"><b>Installation Progress</b><br /></div>
			<div class="status">
				<div class="step6">&nbsp;</div>
			</div>
			<br /><br />
			
			<h1>Installation Complete!</h1>
			
			Congratulations, you have successfully installed SMS 2. If the install worked properly, you 
			should now be able to see SMS running on your site. If you need technical support, please 
			visit the <a href="http://forums.anodyne-productions.com" target="_blank">Anodyne support forums</a>.
			<br /><br />
	
			Thank you for choosing the SIMM Management System from Anodyne Productions. Please delete the 
			install file and install folder from your server. Accessing it additional times can cause errors.
			<br /><br />
	
			<h1><a href="<?=$webLocation;?>login.php?action=login">Login to your SMS site now &raquo;</a></h1>
			
		<?php
			
			break;
		}
		
		?>
		
		</div>
		
	</div> <!-- close #install -->
	
	<div class="footer">
		Copyright &copy; 2005-<?php echo date('Y'); ?> by <a href="http://www.anodyne-productions.com/" target="_blank">Anodyne Productions</a>
	</div> <!-- close .footer -->
</body>
</html>