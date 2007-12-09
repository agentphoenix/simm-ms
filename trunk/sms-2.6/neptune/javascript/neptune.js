//** Tab Content script- © Dynamic Drive DHTML code library (http://www.dynamicdrive.com)
//** Last updated: Nov 8th, 06

var enabletabpersistence=1 //enable tab persistence via session only cookies, so selected tab is remembered?

////NO NEED TO EDIT BELOW////////////////////////
var tabcontentIDs=new Object()

function expandcontent(linkobj)
{
	var ulid=linkobj.parentNode.parentNode.id //id of UL element
	var ullist=document.getElementById(ulid).getElementsByTagName("li") //get list of LIs corresponding to the tab contents
	
	for (var i=0; i<ullist.length; i++)
	{
		ullist[i].className=""  //deselect all tabs
		if (typeof tabcontentIDs[ulid][i]!="undefined") //if tab content within this array index exists (exception: More tabs than there are tab contents)
			document.getElementById(tabcontentIDs[ulid][i]).style.display="none" //hide all tab contents
	}
	
	linkobj.parentNode.className="selected"  //highlight currently clicked on tab
	document.getElementById(linkobj.getAttribute("rel")).style.display="block" //expand corresponding tab content
	saveselectedtabcontentid(ulid, linkobj.getAttribute("rel"))
}

//interface for selecting a tab (plus expand corresponding content)
function expandtab(tabcontentid, tabnumber)
{
	var thetab=document.getElementById(tabcontentid).getElementsByTagName("a")[tabnumber]
	if (thetab.getAttribute("rel"))
		expandcontent(thetab)
}

// save ids of tab content divs
function savetabcontentids(ulid, relattribute)
{
	if (typeof tabcontentIDs[ulid]=="undefined") //if this array doesn't exist yet
		tabcontentIDs[ulid]=new Array()
	tabcontentIDs[ulid][tabcontentIDs[ulid].length]=relattribute
}

//set id of clicked on tab as selected tab id & enter into cookie
function saveselectedtabcontentid(ulid, selectedtabid)
{
	if (enabletabpersistence==1) //if persistence feature turned on
		setCookie(ulid, selectedtabid)
}

//returns a tab link based on the ID of the associated tab content
function getullistlinkbyId(ulid, tabcontentid)
{
	var ullist=document.getElementById(ulid).getElementsByTagName("li")
	for (var i=0; i<ullist.length; i++)
	{
		if (ullist[i].getElementsByTagName("a")[0].getAttribute("rel")==tabcontentid)
		{
			return ullist[i].getElementsByTagName("a")[0]
			break
		}
	}
}

function initializetabcontent()
{
	//loop through passed UL ids
	for (var i=0; i<arguments.length; i++)
	{
		if (enabletabpersistence==0 && getCookie(arguments[i])!="") //clean up cookie if persist=off
			setCookie(arguments[i], "")
		
		var clickedontab=getCookie(arguments[i]) //retrieve ID of last clicked on tab from cookie, if any
		var ulobj=document.getElementById(arguments[i])
		var ulist=ulobj.getElementsByTagName("li") //array containing the LI elements within UL
		
		//loop through each LI element
		for (var x=0; x<ulist.length; x++)
		{
			var ulistlink=ulist[x].getElementsByTagName("a")[0]
			if (ulistlink.getAttribute("rel"))
			{
				savetabcontentids(arguments[i], ulistlink.getAttribute("rel")) //save id of each tab content as loop runs
				
				ulistlink.onclick=function()
				{
					expandcontent(this)
					return false
				}
	
				if (ulist[x].className=="selected" && clickedontab=="") //if a tab is set to be selected by default
					expandcontent(ulistlink) //auto load currenly selected tab content
			}
		} //end inner for loop
		
		//if a tab has been previously clicked on per the cookie value
		if (clickedontab!="")
		{
			var culistlink=getullistlinkbyId(arguments[i], clickedontab)
			
			if (typeof culistlink!="undefined") //if match found between tabcontent id and rel attribute value
				expandcontent(culistlink) //auto load currenly selected tab content
			else //else if no match found between tabcontent id and rel attribute value (cookie mis-association)
				expandcontent(ulist[0].getElementsByTagName("a")[0]) //just auto load first tab instead
		}
	} //end outer for loop
}


function getCookie(Name)
{ 
	var re=new RegExp(Name+"=[^;]+", "i"); //construct RE to search for target name/value pair
	
	if (document.cookie.match(re)) //if cookie found
		return document.cookie.match(re)[0].split("=")[1] //return its value
	
	return ""
}

function setCookie(name, value)
{
	document.cookie = name+"="+value //cookie value is domain wide (path=/)
}