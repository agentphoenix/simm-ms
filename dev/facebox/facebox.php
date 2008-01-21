<html>
	<head>
		<title>Facebox</title>
		
		<style type="text/css">
			@import url('facebox.css');
		</style>
		
		<script type="text/javascript" src="jquery.js"></script>
		<script type="text/javascript" src="facebox.js"></script>
		
		<script type="text/javascript">
			$(document).ready(function() {
				$("a[rel*=facebox]").click(function() {
					var id = $(this).attr("myID");
					var type = $(this).attr("myType");
					var action = $(this).attr("myAction");
					
					jQuery.facebox(function() {
						jQuery.get('ajax/activate_' + type + "_" + action + '.php?id=' + id, function(data) {
							jQuery.facebox(data);
						});
					});
					return false;
				});
			});
		</script>
	</head>
	<body>
		<?php
		
		if(isset($_POST['foo']))
		{
			echo "<p>SUBMIT!</p>";
		}
		
		?>
		<table>
			<tr>
				<td><a href="#" rel="facebox" myID="1" myType="user" myAction="accept">Click me!</a></td>
			</tr>
			<tr>
				<td><a href="#" rel="facebox" myID="2" myType="decline">Click me!</a></td>
			</tr>
			<tr>
				<td><a href="#" rel="facebox" myID="3">Click me!</a></td>
			</tr>
		</table>
	</body>
</html>