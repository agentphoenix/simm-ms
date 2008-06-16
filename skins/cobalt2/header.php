<?php

/**
Edits to this skin are permissible if the original credits stay intact.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: skins/default/header.php
Purpose: The header file that the system calls for the template

Skin Version: 2.5
Last Modified: 2008-04-23 1929 EST
**/

$path = dirname( __FILE__ ); /* absolute path of the current file (header.php) */
$path = explode( "/", $path ); /* explode the string into an array */
$pcount = count( $path ); /* count the number of keys in the array */

$pathElement1 = $pcount -2; /* create the first element used */
$pathElement2 = $pcount -1; /* create the second element used */

/* define the path */
define( 'SKIN_PATH', $path[$pathElement1] . '/' . $path[$pathElement2] . '/' );

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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