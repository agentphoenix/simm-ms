<?php

require_once( '../framework/functionsGlobal.php' );
require_once( '../framework/functionsAdmin.php' );
require_once( '../framework/functionsUtility.php' );
require_once( '../framework/classUtility.php' );
require_once( '../framework/classMenu.php' );

$menu = new Menu;

$login = 1;
$sessionCrewid = 1;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>NEPTUNE</title>
		<link rel="stylesheet" type="text/css" href="/skins/default/style.css" />
		
		<script type="text/javascript" src="/framework/jquery.js"></script>
		<script type="text/javascript" src="/framework/jquery/ui.tabs.js"></script>
		<script type="text/javascript">
            $(document).ready(function(){
				$('#container-mainnav > ul').tabs({ fxFade: true, fxSpeed: 'fast' });
				$('#container-1 > ul').tabs();
			});
        </script>
	</head>
	<body>
		<div id="container">
			
			<?php if( $login == 0 ) { ?>
			
			<div id="mainNav">
				<? $menu->main( $sessionCrewid ); ?>
			</div>
			
			<?php } else { ?>
			
			<div id="container-mainnav">
				<ul>
					<li><a href="#mainNav"><span>Global</span></a></li>
					<li><a href="#userNav"><span>User</span></a></li>
				</ul>
				<div id="mainNav">
					<? $menu->main( $sessionCrewid ); ?>
				</div>
				<div id="userNav">
					<!--<ul>
						<li><a href="#">User 1</a></li>
						<li><a href="#">User 2</a></li>
						<li><a href="#">User 3</a></li>
						<li><a href="#">User 4</a></li>
						<li><a href="#">User 5</a></li>
					</ul>-->
					<? $menu->user( $sessionCrewid ); ?>
				</div>
			</div>
			
			<? } ?>
			
			<div id="container-1">
				<ul>
					<li><a href="#one"><span>One</span></a></li>
					<li><a href="#two"><span>Two</span></a></li>
					<li><a href="#three"><span>Tabs are flexible again</span></a></li>
				</ul>
				<div id="one">
					<p>First tab is active by default:</p>
					<pre><code>$(&#039;#container&#039;).tabs();</code></pre>
				</div>
				<div id="two">
					Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
					Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
				</div>
				<div id="three">
					Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
					Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
					Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
				</div>
			</div>
			
		</div>
	</body>
</html>
<!--
<html>
	
	<body>
		
		<div id="container-mainnav">
            <ul class="options">
                <li><a href="#fragment-1"><span>Global</span></a></li>
                <li><a href="#fragment-2"><span>User</span></a></li>
            </ul>
            <div id="fragment-1">
                <p>First tab is active by default:</p>
                <pre><code>$(&#039;#container&#039;).tabs();</code></pre>
            </div>
            <div id="fragment-2">
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
            </div>
            <div id="fragment-3">
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
            </div>
        </div>
		
	</body>
</html>
-->