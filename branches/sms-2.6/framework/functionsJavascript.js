/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: framework/functionsJavascript.js
Purpose: Handles all Javascript actions by the system, including pulling in
	the various jQuery elements

System Version: 2.6.0
Last Modified: 2007-11-13 1541 EST
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
include_dom( 'js', '/framework/jquery.js' );
include_dom( 'js', '/framework/jquery/ui.tabs.js' );
include_dom( 'js', '/framework/jquery/thickbox.js' );
include_dom( 'css', '/framework/jquery/thickbox.css' );
include_dom( 'js', '/framework/jquery/reflection.js' );

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