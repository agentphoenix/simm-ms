<?php

session_start();

require_once( '../framework/functionsGlobal.php' );
require_once( '../framework/functionsAdmin.php' );
require_once( '../framework/functionsUtility.php' );
require_once( '../framework/classUtility.php' );
require_once( '../framework/classMenu.php' );

$access = $_SESSION['sessionAccess'];
/* print_r( $access ); */

/* query the database for the general items */
$query1 = "SELECT * FROM sms_menu_items WHERE menuAvailability = 'on' AND menuCat = 'general' ";
$query1.= "ORDER BY menuMainSec, menuGroup, menuOrder ASC";
$result1 = mysql_query( $query1 );

/* query the database for the admin items */
$query2 = "SELECT * FROM sms_menu_items WHERE menuAvailability = 'on' AND menuCat = 'admin' ";
$query2.= "ORDER BY menuMainSec, menuGroup, menuOrder ASC";
$result2 = mysql_query( $query2 );

/* loop through the general items and put them into a 2d array */
while( $fetch1 = mysql_fetch_assoc( $result1 ) ) {
	extract( $fetch1, EXTR_OVERWRITE );
	
	$array1[] = array(
		$fetch1['menuid'],
		$fetch1['menuTitle'],
		$fetch1['menuMainSec']
	);
	
}

/* loop through the admin items and them into a 2d array */
while( $fetch2 = mysql_fetch_assoc( $result2 ) ) {
	extract( $fetch2, EXTR_OVERWRITE );
	
	$array2[] = array(
		$fetch2['menuid'],
		$fetch2['menuTitle'],
		$fetch2['menuAccess'],
		$fetch2['menuMainSec']
	);
	
}

/* rip through the array and remove any items that shouldn't be there */
foreach( $array2 as $a => $b ) {
	if( !in_array( $b[2], $access ) ) {
		unset( $array2[$a] );
	}
}

$menu1 = 8;
$menu2 = 75;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>NEPTUNE</title>
		<link rel="stylesheet" type="text/css" href="/skins/default/style.css" />
		<script type="text/javascript" src="/framework/jquery.js"></script>
	</head>
	<body>
		<div id="container">
			<div class="body"
				<div class="content">
					
					<?php
					
					for( $i=1; $i<7; $i++ )
					{
						$menu = "menu" . $i;
						
						echo "<select name='" . $menu . "'>";
							echo "<optgroup label='General'>";
								foreach( $array1 as $key1 => $value1 )
								{
									if( $$menu == $value1[0] ) {
										$selected = " selected";
									} else {
										$selected = "";
									}
									
									echo "<option value='" . $value1[0] . "'" . $selected . ">";
										echo ucwords( $value1[2] ) . " - " . $value1[1];
									echo "</option>";
								}
							echo "</optgroup>";
							
							echo "<optgroup label='Admin'>";
								foreach( $array2 as $key2 => $value2 )
								{
									if( $$menu == $value2[0] ) {
										$selected = " selected";
									} else {
										$selected = "";
									}
									
									echo "<option value='" . $value2[0] . "'" . $selected . ">";
										echo ucwords( $value2[3] ) . " - " . $value2[1];
									echo "</option>";
								}
							echo "</optgroup>";
						echo "</select>";
						echo "<br /><br />";
					}
					
					?>

				</div>
			</div>
		</div>
	</body>
</html>