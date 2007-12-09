<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/post/main.php
Purpose: Page that can be used as the main page to the post section

System Version: 2.5.0
Last Modified: 2007-03-01 0124 EST
**/

/* access check */
if( in_array( "post", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "post";

?>

	<div class="body">
		<span class="fontTitle">Post Main</span>
	</div>
	
<? } else { errorMessage( "main post" ); } ?>