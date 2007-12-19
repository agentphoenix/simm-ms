<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>toggle.php</title>
		
		<script type="text/javascript" src="../framework/jquery.js"></script>
		<script type="text/javascript" src="../framework/jquery/jquery.clickmenu.js"></script>
		<style type="text/css">
			@import url('../framework/jquery/clickmenu.css');
			body { background-color: #000; }
		</style>
		
		<script type="text/javascript">
			$(document).ready(function() 
			{ 
			    $('#list').clickMenu(); 
			});
		</script>
	</head>

	<body>
		<ul id="list"> 
		    <li><img src="arrow.png" alt=">>" border="0" />
		        <ul>
		            <li><a href="#1">Subitem 1</a></li>
		            <li><a href="#2">Subitem 2</a></li>
					<li class="divider"></li>
					<li><a href="#3">Subitem 3</a></li>
		            <li><a href="#4">Subitem 4</a></li>
		        </ul>
		    </li>
		</ul>
	</body>
</html>