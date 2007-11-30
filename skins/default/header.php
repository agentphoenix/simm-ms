<?php

/**
This skin is the property of its owner and should not be duplicated or
reproduced with the express written consent of the author. Edits to this skin
are permissible if the original credits stay intact.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: skins/default/header.php
Purpose: The header file that the system calls for the template

Skin Version: 2.0
Last Modified: 2007-08-13 1155 EST
**/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?=$shipPrefix . " " . $shipName;?></title>
	<link rel="stylesheet" href="<?=$webLocation;?>skins/default/style.css" type="text/css" media="screen" />
	<script>
		<? include_once( "framework/functionsJavascript.js" ); ?>
		
		if( document.images )
		{
			preload_image_object = new Image();
			
			// set image url
			image_url = new Array();
			image_url[0] = "skins/default/buttons/button-off.png";
			image_url[1] = "skins/default/buttons/button-hover.png";
			
			var i = 0;
			for( i=0; i<=1; i++ )
			{
				preload_image_object.src = image_url[i];
			}
		}
	</script>
</head>
<body>

<div id="headerTopSpacer"></div>
<div id="header">
	<img src="skins/default/images/header.jpg" alt="SMS 2" style="padding-left:2.5em;" />
</div>
<div id="container">