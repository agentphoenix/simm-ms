<?php

if(isset($_GET['id']) && !is_numeric($_GET['id']))
{
	errorMessageIllegal();
	exit;
}
elseif(isset($_GET['id']) && is_numeric($_GET['id']))
{
	$id = $_GET['id'];
}

if(isset($_POST['submit']))
{
	echo "submit successful!";
}
else
{

?>

<h2>ID: <?php echo $id;?></h2>
<form method="post" action="">
	<input type="text" name="foo" /><br />
	<input type="submit" name="submit" id="submit" value="Submit" onSubmit="$.post();" />
</form>

<?php } ?>