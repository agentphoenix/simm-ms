<?php
/*
if( ! isset( $system_path ) )
{
	$system_path = './';

	if( @realpath( $system_path ) !== FALSE )
	{
		$system_path = realpath( $system_path ) . '/';
	}
}

$system_path = str_replace( "\\", "/", $system_path );

echo $system_path;
*/

/*
$url = "http://".$_SERVER['HTTP_HOST']."/";
//$url .= preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/';

echo $url;
*/

$page = __FILE__;
echo $page . "<br />";
$page = str_replace( $_SERVER['DOCUMENT_ROOT'], "", $page );
echo $page;

?>