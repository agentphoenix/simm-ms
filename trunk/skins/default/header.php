<?php

/**
This skin is the property of its owner and should not be duplicated or
reproduced with the express written consent of the author. Edits to this skin
are permissible if the original credits stay intact.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: skins/default/header.php
Purpose: The header file that the system calls for the template

Skin Version: 2.1
Last Modified: 2007-10-18 1328 EST
**/

/* get the absolute path of the current file (header.php) */
$path = dirname( __FILE__ );

/* explode the string into an array */
$path = explode( "/", $path );

/* count the number of keys in the array */
$pcount = count( $path );

/* create the elements used */
$pathElement1 = $pcount -2;
$pathElement2 = $pcount -1;

/* define the path */
define( 'SKIN_PATH', $path[$pathElement1] . '/' . $path[$pathElement2] . '/' );

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title><?=$shipPrefix . " " . $shipName;?></title>
		<link rel="stylesheet" href="<?=$webLocation . SKIN_PATH;?>style.css" type="text/css" />
		<script type="text/javascript">
			<? include_once( "framework/functionsJavascript.js" ); ?>
			
			if( document.images )
			{
				preload_image_object = new Image();
				
				image_url = new Array();
				image_url[0] = "<?=SKIN_PATH;?>buttons/button-off.png";
				image_url[1] = "<?=SKIN_PATH;?>buttons/button-hover.png";
				
				var i = 0;
				for( i = 0; i < image_url.length; i++ )
				{
					preload_image_object.src = image_url[i];
				}
			}
		</script>
	</head>
	<body>
		<div id="headerTopSpacer"></div>
		<div id="header">
			<!-- <img src="<?=SKIN_PATH;?>images/header.png" alt="SMS 2" style="padding-left:2.5em;" /> -->
		</div>
		<div id="container">