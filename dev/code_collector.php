<?php

/* php version check */
if( phpversion() < '4.3.0' )
{
	...
}

/* smart quoting function */
function quote_smart( $value )
{
	// Stripslashes
	if( get_magic_quotes_gpc() )
	{
		$value = stripslashes( $value );
	}

	// Quote if not integer
	if( ! is_numeric( $value ) )
	{
		// mysql_real_escape_string requires PHP 4.3.0 and higher!
		$value = "'" . mysql_real_escape_string( $value ) . "'";
	}

	return $value;
}

/* constants */

/* define the variables */
define( 'APP_NAME', 'Aura' );
define( 'APP_VERSION', '1.0.0' );
define( 'PATH', $system_path );
define( 'PATH_IMAGES, $system_path . 'images/' );

/* unset the system path variable now that it's defined */
unset( $system_path );

/* echo out the variables */
echo APP_NAME;
echo APP_VERSION;
echo PATH;
echo PATH_IMAGES;

/* check a variable's definition */
if( defined( 'PATH' ) )
{
	...
}

/* dyanmic system path */
if( ! isset( $system_path ) )
{
	$system_path = './';

	if( @realpath( $system_path ) !== FALSE )
	{
		$system_path = realpath( $system_path ) . '/';
	}
}

$system_path = str_replace( "\\", "/", $system_path );

?>