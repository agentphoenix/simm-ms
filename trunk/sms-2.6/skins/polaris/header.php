<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ david.vanscott@gmail.com ]
File: skins/polaris/header.php
Purpose: The header file that the system calls for the template

System Version: 2.5.0
Last Modified: 2007-06-22 2054 EST
**/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?=$shipPrefix . " " . $shipName;?></title>
	<link rel="stylesheet" href="<?=$webLocation;?>skins/polaris/style.css" type="text/css" media="screen" />
	<script>
		<? include_once( "framework/functionsJavascript.js" ); ?>
		
		if( document.images )
		{
			preload_image_object = new Image();
			
			// set image url
			image_url = new Array();
			image_url[0] = "skins/polaris/buttons/button-off.png";
			image_url[1] = "skins/polaris/buttons/button-hover.png";
			
			var i = 0;
			for( i=0; i<=1; i++ )
			{
				preload_image_object.src = image_url[i];
			}
		}
	</script>
</head>
<body>