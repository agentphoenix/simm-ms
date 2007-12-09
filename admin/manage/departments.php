<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/departments.php
Purpose: Page that moderates the simm departments

System Version: 2.5.0
Last Modified: 2007-07-10 1003 EST
**/

/* access check */
if( in_array( "m_departments", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$actionCreate = $_POST['action_create_x'];
	$actionUpdate = $_POST['action_update_x'];
	$actionDelete = $_POST['action_delete_x'];

	/* define the POST variables */
	$deptid = $_POST['deptid'];
	$deptName = addslashes( $_POST['deptName'] );
	$deptClass = $_POST['deptClass'];
	$deptOrder = $_POST['deptOrder'];
	$deptColor = $_POST['deptColor'];
	$deptDisplay = $_POST['deptDisplay'];
	$deptDesc = addslashes( $_POST['deptDesc'] );
	$deptType = $_POST['deptType'];
	
	/* if the POST action is update */
	if( $actionUpdate ) {
		
		/* do the update query */
		$query = "UPDATE sms_departments SET ";
		$query.= "deptName = '$deptName', deptClass = '$deptClass', deptOrder = '$deptOrder', ";
		$query.= "deptColor = '$deptColor', deptDisplay = '$deptDisplay', deptDesc = '$deptDesc', ";
		$query.= "deptType = '$deptType' WHERE deptid = '$deptid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_departments" );
		
		$action = "update";
	
	/* if the POST action is create */
	} elseif( $actionCreate ) {
		
		/* do the create query */
		$query = "INSERT INTO sms_departments ( deptid, deptName, deptClass, deptOrder, ";
		$query.= "deptColor, deptDisplay, deptDesc, deptType ) VALUES ( '', '$deptName', '$deptClass', ";
		$query.= "'$deptOrder', '$deptColor', '$deptDisplay', '$deptDesc', '$deptType' )";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_departments" );
		
		$action = "create";
	
	/* if the POST action is delete */
	} elseif( $actionDelete ) {
		
		/* do the delete query */
		$query = "DELETE FROM sms_departments WHERE deptid = '$deptid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_departments" );
		
		$action = "delete";
		
	}
	
	/* strip the slashes from the vars */
	$deptName = stripslashes( $deptName );
	$deptDesc = stripslashes( $deptDesc );

?>

	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "department", $action );
			$check->display();
		}
		
		?>
	
		<span class="fontTitle">Create New Department</span>
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=departments">
		<table cellpadding="0" cellspacing="3">
			<tr>
				<td colspan="5" valign="top">
					<span class="fontNormal"><b>Department</b></span><br />
					<input type="text" class="name" name="deptName" />
				</td>
				<td width="75%" rowspan="2" align="center">
					<span class="fontNormal"><b>Description</b></span><br />
		            <textarea name="deptDesc" rows="4" class="desc"></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<span class="fontNormal"><b>Class</b></span><br />
					<input type="text" class="class" name="deptClass" maxlength="3" />
				</td>
				<td>
					<span class="fontNormal"><b>Order</b></span><br />
					<input type="text" class="order" name="deptOrder" maxlength="3" />
				</td>
				<td>
					<span class="fontNormal"><b>Color</b></span><br />
					<input type="text" class="color" name="deptColor" maxlength="6" />
				</td>
			    <td>
			    	<span class="fontNormal"><b>Dept Type</b></span><br />
					<select name="deptType">
						<option value="playing">Playing Dept</option>
						<option value="nonplaying">Non-Playing Dept</option>
					</select>
				</td>
			    <td>
			    	<span class="fontNormal"><b>Display?</b></span><br />
                    <select name="deptDisplay">
                    	<option value="y">Yes</option>
                    	<option value="n">No</option>
					</select>
				</td>
            </tr>
			<tr>
				<td height="25" colspan="5" align="right"></td>
		        <td height="25" align="center">
		        	<input type="image" src="<?=path_userskin;?>buttons/create.png" class="button" name="action_create" value="Create" />
		        </td>
	        </tr>
		</table>
		</form>
		<br /><br />
		
		<span class="fontTitle">Manage Existing Departments</span>
	
		<table>
			<?
			
			/* get the departments from the database */
			$getDepartments = "SELECT * FROM sms_departments ORDER BY deptOrder ASC";
			$getDepartmentsResult = mysql_query( $getDepartments );
			
			/* loop through the results and fill the form */
			while( $deptFetch = mysql_fetch_assoc( $getDepartmentsResult ) ) {
				extract( $deptFetch, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=departments">
			<tr>
				<td colspan="5" valign="top">
					<span class="fontNormal"><b>Department</b></span><br />
					<input type="text" class="name" name="deptName" value="<?=stripslashes( $deptName );?>" />
				</td>
				<td width="75%" rowspan="2" align="center" valign="top">
					<span class="fontNormal"><b>Description</b></span><br />
					<textarea name="deptDesc" rows="4" class="desc"><?=stripslashes( $deptDesc );?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<span class="fontNormal"><b>Class</b></span><br />
					<input type="text" class="class" name="deptClass" maxlength="3" value="<?=$deptClass;?>" />
				</td>
				<td>
					<span class="fontNormal"><b>Order</b></span><br />
					<input type="text" class="order" name="deptOrder" maxlength="3" value="<?=$deptOrder;?>" />
				</td>
				<td>
					<span class="fontNormal"><b>Color</b></span><br />
                    <input type="text" class="color" style="color:#<?=$deptColor;?>" name="deptColor" size="6" maxlength="6" value="<?=$deptColor;?>" />
				</td>
				<td>
					<span class="fontNormal"><b>Dept Type</b></span><br />
                    <select name="deptType">
                    	<option value="playing"<? if( $deptType == "playing" ) { echo " selected"; } ?>>Playing Dept</option>
                    	<option value="nonplaying"<? if( $deptType == "nonplaying" ) { echo " selected"; } ?>>Non-Playing Dept</option>
                    </select>
				</td>
			    <td>
			    	<span class="fontNormal"><b>Display?</b></span><br />
                    <select name="deptDisplay">
                    	<option value="y"<? if( $deptDisplay == "y" ) { echo " selected"; } ?>>Yes</option>
                    	<option value="n"<? if( $deptDisplay == "n" ) { echo " selected"; } ?>>No</option>
                    </select>
				</td>
			</tr>
			<tr>
				<td colspan="5" align="right">&nbsp;</td>
	            <td align="center">
	            	<input type="hidden" name="deptid" value="<?=$deptid;?>" />
                    <script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this department?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" />
				</td>
			</tr>
			<tr>
				<td colspan="6" height="25">&nbsp;</td>
			</tr>
			</form>
			<? } ?>
		</table>
	</div>

<? } else { errorMessage( "department management" ); } ?>