<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Authors: David VanScott, Justin Chow [ anodyne.sms@gmail.com ]
File: skins/SMS_Lcars/header.php
Purpose: The header file that the system calls for the template

System Version: 2.5.0
Last Modified: 2007-04-29 1305 EST
**/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?=$shipPrefix . " " . $shipName;?></title>
	<link rel="stylesheet" href="<?=$webLocation;?>skins/SMS_Lcars/style.css" type="text/css" media="screen" />
	<script>
		<? include_once( "framework/functionsJavascript.js" ); ?>
	</script>
</head>
<body>
<div id="container">

<div id="header">
	<div id="header2"><div id="header3"></div></div>
</div>
