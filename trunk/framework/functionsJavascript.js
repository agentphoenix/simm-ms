/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: framework/functionsJavascript.js
Purpose: Handles all Javascript actions by the system, including pulling in
	the various jQuery elements

System Version: 2.6.0
Last Modified: 2007-12-07 2128 EST
**/

/**
	Function that will include JS files
**/
function include_dom( type, script_filename )
{
	if( type == "js" )
	{
		var html_doc = document.getElementsByTagName( 'head' ).item(0);
		var js = document.createElement( 'script' );
		js.setAttribute( 'language', 'javascript' );
		js.setAttribute( 'type', 'text/javascript' );
		js.setAttribute( 'src', script_filename );
		html_doc.appendChild( js );
		return false;
	}
	if( type == "css" )
	{
		var cssNode = document.createElement( 'link' );
		cssNode.setAttribute( 'rel', 'stylesheet' );
		cssNode.setAttribute( 'type', 'text/css' );
		cssNode.setAttribute( 'href', script_filename );
		document.getElementsByTagName( 'head' )[0].appendChild( cssNode );
		return false;
	}
}
/** END FUNCTION **/

/** pull in the JS files **/
include_dom( 'js', 'framework/jquery.js' );
include_dom( 'js', 'framework/jquery/ui.tabs.js' );
include_dom( 'js', 'framework/jquery/thickbox.js' );
include_dom( 'css', 'framework/jquery/thickbox.css' );
include_dom( 'js', 'framework/jquery/reflection.js' );

/**
	Function that toggles checkboxes
**/
function selectAll(formObj, isInverse) 
{
	for (var i=0;i < formObj.length;i++) 
	{
		fldObj = formObj.elements[i];
		if (fldObj.type == 'checkbox')
		{
			if(isInverse)
				fldObj.checked = (fldObj.checked) ? false : true;
			else fldObj.checked = true; 
		}
	}
}
/** END FUNCTION **/

/**
	Function to make sure that jp authors doesn't go
	outside the acceptable range
**/
function checkNumber(upper, actual)
{
	if(actual < 1)
		window.alert("You can't set the author number below 1! Please try again.");
	else if(actual > upper)
		window.alert("You do not have that many crew members! The limit cannot exceed your crew count. Please try again.")
}