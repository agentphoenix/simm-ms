<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/ranks.php
Purpose: Page that moderates the ranks

System Version: 2.6.0
Last Modified: 2007-10-10 1023 EST
**/

/* access check */
if( in_array( "m_ranks", $sessionAccess ) ) {

	/* set the page class and variables */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$rank = $_GET['rank'];
	$set = $_GET['set'];
	$actionCreate = $_POST['action_create_x'];
	$actionUpdate = $_POST['action_update_x'];
	$actionDelete = $_POST['action_delete_x'];
	
	/* if there is no rank specified, set it to 1 */
	if( !isset( $rank ) ) {
		$rank = "1";
	}
	
	/* if there is no rank set specified, set it to the system default */
	if( !isset( $set ) ) {
		$set = $rankSet;
	}
	
	/* define the POST variables */
	$rankid = $_POST['rankid'];
	$rankName = addslashes( $_POST['rankName'] );
	$rankClass = $_POST['rankClass'];
	$rankOrder = $_POST['rankOrder'];
	$rankImage = $_POST['rankImage'];
	$rankDisplay = $_POST['rankDisplay'];
	
	/* if the POST action is update */
	if( $actionUpdate ) {
		
		$rankArray = array();
		
		foreach( $_POST as $key => $value )
		{
			if( substr( $key, 0, 2 ) == "x_" )
			{
				/* strip the x_ from the key */
				$key = substr_replace( $key, '', 0, 2 );
				
				/* get the id of the rank */
				$offset = strpos( $key, "_" );
				$id = substr( $key, 0, $offset );
				$key = substr_replace( $key, '', 0, ($offset+1) );
				
				if( !array_key_exists( $id ) ) {
					$rankArray[$id] = array();
				}
				
				$rankArray[$id][$key] = $value;
				
				echo "<pre>";
				print_r( $rankArray );
				echo "</pre>";
			}
		}
		
		/* do the update query */
		$query = "UPDATE sms_ranks SET ";
		$query.= "rankName = '$rankName', rankClass = '$rankClass', rankOrder = '$rankOrder', ";
		$query.= "rankImage = '$rankImage', rankDisplay = '$rankDisplay' ";
		$query.= "WHERE rankid = '$rankid' LIMIT 1";
		//$result = mysql_query( $query );
		
		/* optimize table */
		optimizeSQLTable( "sms_ranks" );
		
		$action = "update";
	
	/* if the POST action is create */
	} elseif( $actionCreate ) {
		
		/* do the create query */
		$query = "INSERT INTO sms_ranks ( rankid, rankOrder, rankName, rankImage, rankType, rankDisplay, rankClass ) ";
		$query.= "VALUES ( '', '$rankOrder', '$rankName', '$rankImage', '1', '1', '$rankClass' )";
		$result = mysql_query( $query );
		
		/* optimize table */
		optimizeSQLTable( "sms_ranks" );
		
		$action = "create";
	
	/* if the POST action is delete */
	} elseif( $actionDelete ) {
		
		/* do the delete query */
		$query = "DELETE FROM sms_ranks WHERE rankid = '$rankid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize table */
		optimizeSQLTable( "sms_ranks" );
		
		$action = "delete";
	
	}
	
	/* strip the slashes */
	$rankName = stripslashes( $rankName );

?>
	
	<div class="body">
	
		<div align="center">
			<span class="fontSmall">Click on the rank image to view and edit that <b>rank set</b></span><br /><br />
	
			<?
			
			/* split the array by comma */
			$allowedRanksArray = explode( ",", $allowedRanks );
			
			/* loop through to create the list of rank sets */
			foreach( $allowedRanksArray as $key => $value ) {
	
			?>
	
			<a href="<?=$webLocation;?>admin.php?page=manage&sub=ranks&set=<?=trim( $value );?>">
				<img src="<?=$webLocation;?>images/ranks/<?=trim( $value );?>/preview.png" border="0" alt="" class="image" />
			</a>
			
			<? } ?>
		</div><br />
		
		<div align="center">
			<span class="fontSmall">Click on the rank image to view and edit that <b>color set</b></span><br /><br />
			
			<?
			
			/* get the rank classes from the database */
			$getRankClasses = "SELECT rankClass, rankImage FROM sms_ranks ";
			$getRankClasses.= "WHERE rankImage LIKE '%-blank.png' ";
			$getRankClasses.= "GROUP BY rankClass ORDER BY rankClass ASC";
			$getRankClassesResult = mysql_query( $getRankClasses );
			
			/* loop through the spit out the rank links */
			while( $classFetch = mysql_fetch_array( $getRankClassesResult ) ) {
				extract( $classFetch, EXTR_OVERWRITE );
			
			?>
			
			<a href="<?=$webLocation;?>admin.php?page=manage&sub=ranks&set=<?=trim( $set );?>&rank=<?=$rankClass;?>">
				<img src="<?=$webLocation;?>images/ranks/<?=trim( $set );?>/<?=$rankImage;?>" border="0" alt="Rank Class <?=$rankClass;?>" class="image" />
			</a>
			
			<? } ?>
		</div><br />
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "rank", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Create New Rank</span>
	
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=ranks&set=<?=trim( $set );?>&rank=<?=$rank;?>">
		<table cellpadding="0" cellspacing="3">
			<tr>
				<td width="40">
					<span class="fontNormal"><b>Class</b></span><br />
					<input type="text" class="class" name="rankClass" maxlength="3" />
				</td>
				<td width="40">
					<span class="fontNormal"><b>Order</b></span><br />
					<input type="text" class="order" name="rankOrder" maxlength="3" />
				</td>
				<td width="375">
					<span class="fontNormal"><b>Rank</b></span><br />
					<input type="text" class="name" name="rankName" />
				</td>
				<td align="left" valign="bottom">
					<input type="image" src="<?=path_userskin;?>buttons/create.png" class="button" name="action_create" value="Create" />
				</td>
		    </tr>
			<tr>
				<td></td>
				<td></td>
				<td>
					<span class="fontNormal"><b>Image</b></span><br />
					<span class="fontSmall">images/ranks/<?=trim( $set );?>/</span><input type="text" class="image" name="rankImage" />
				</td>
				<td></td>
			</tr>
		</table>
		</form>
		<br /><br />
		
		<span class="fontTitle">Manage Existing Ranks</span>
	
		<table cellpadding="0" cellspacing="3">
			<?
			
			/* pull the ranks from the database */
			$getRanks = "SELECT * FROM sms_ranks WHERE rankClass = '$rank' ORDER BY rankOrder ASC";
			$getRanksResult = mysql_query( $getRanks );
			
			/* loop through the results and fill the form */
			while( $rankFetch = mysql_fetch_assoc( $getRanksResult ) ) {
				extract( $rankFetch, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=ranks&set=<?=$set;?>&rank=<?=$rank;?>">
			<tr>
				<td width="40">
					<span class="fontNormal"><b>Class</b></span><br />
					<input type="text" class="class" name="x_<?=$rankid;?>_rankClass"  maxlength="3" value="<?=$rankClass;?>" />
				</td>
				<td width="40">
					<span class="fontNormal"><b>Order</b></span><br />
					<input type="text" class="order" name="x_<?=$rankid;?>_rankOrder" maxlength="3" value="<?=$rankOrder;?>" />
				</td>
				<td>
					<span class="fontNormal"><b>Rank</b></span><br />
					<input type="text" class="name" name="x_<?=$rankid;?>_rankName" value="<?=stripslashes( $rankName );?>" />
				</td>
				<td width="150" align="center" valign="bottom">
					<img src="<?=$webLocation . 'images/ranks/' . trim( $set ) . '/' . $rankImage;?>" alt="<?=$rankName;?>" border="0" />
				</td>
				<td rowspan="2" align="center" valign="middle">
					<input type="hidden" name="x_<?=$rankid;?>_rankid" value="<?=$rankid;?>" />
					<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" /><br />
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this rank?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<span class="fontNormal"><b>Display?</b></span><br />
					<select name="x_<?=$rankid;?>_rankDisplay">
						<option value="y"<? if( $rankDisplay == "y" ) { echo " selected"; } ?>>Yes</option>
						<option value="n"<? if( $rankDisplay == "n" ) { echo " selected"; } ?>>No</option>
					</select>
				</td>
				<td>
					<span class="fontNormal"><b>Image</b></span><br />
					<span class="fontSmall">images/ranks/<?=trim( $set );?>/</span><input type="text" class="image" name="x_<?=$rankid;?>_rankImage" value="<?=$rankImage;?>" />
				</td>
			    <td></td>
			</tr>
			<tr>
				<td colspan="5" height="25"></td>
			</tr>
			</form>
			<? } /* close the rank while loop */ ?>
	  </table>
	</div>

<? } else { errorMessage( "rank management" ); } ?>