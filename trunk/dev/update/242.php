<?php

/**
Author: David VanScott [ anodyne.sms@gmail.com ]
File: update/242.php
Purpose: Update page - 2.4.2 => Latest
Last Modified: 2007-07-26 0943 EST
**/

/* change the gender field */
mysql_query( "ALTER TABLE `sms_crew` CHANGE `gender` `gender` enum( 'Male', 'Female', 'Hermaphrodite', 'Neuter' ) not null default 'Male'" );

/* fix the site credits field */
mysql_query( "ALTER TABLE `sms_messages` CHANGE `siteCredts` `siteCredits` text not null" );

?>