<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ david.vanscott@gmail.com ]
File: skins/ship_2/header.php
Purpose: The header file that the system calls for the template

Skin Version: 1.0
Last Modified: 2008-07-06 1541 EST
**/

$path = dirname( __FILE__ ); /* absolute path of the current file (header.php) */
$path = explode( "/", $path ); /* explode the string into an array */
$pcount = count( $path ); /* count the number of keys in the array */

$pathElement1 = $pcount -2; /* create the first element used */
$pathElement2 = $pcount -1; /* create the second element used */

/* define the path */
define( 'SKIN_PATH', $path[$pathElement1] . '/' . $path[$pathElement2] . '/' );

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title><?=$shipPrefix . " " . $shipName;?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<link rel="stylesheet" href="<?=$webLocation . SKIN_PATH;?>style.css" type="text/css" />
		
		<script type="text/javascript">
			<? include_once( "framework/functionsJavascript.js" ); ?>
		</script>
	</head>
	<body>