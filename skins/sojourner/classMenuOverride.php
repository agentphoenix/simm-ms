<?php

class MenuOverride extends Menu
{
	var $skin;
	var $pages = array(
		'main' => array('contact', 'credits', 'join', 'main', 'news'),
		'personnel' => array('bio', 'coc', 'manifest', 'userpostlist'),
		'simm' => array(
			'crewawards', 'loglist', 'mission', 'missions', 'post',
			'postlist','rules', 'simm', 'statistics', 'summaries'),
		'ship' => array('decklisting', 'departments', 'history', 'ship', 'specifications', 'tour'),
		'admin' => array('post', 'reports', 'manage', 'user'),
		'database' => array('database')
	);
	
	function MenuOverride()
	{
		parent::Menu();
	}
	
	function main()
	{
		/* get the mainNav items from the DB */
		$getMenu = "SELECT * FROM sms_menu_items WHERE menuCat = 'main' ";
		$getMenu.= "AND menuAvailability = 'on' ORDER BY menuGroup, menuOrder ASC";
		$getMenuResult = mysql_query( $getMenu );
		
		/* loop through whatever comes out of the database */
		while( $fetchMenu = mysql_fetch_array( $getMenuResult ) ) {
			extract( $fetchMenu, EXTR_OVERWRITE );
			
			/* create a multi-dimensional array with the data
				[x] => array
				[x]['title'] => title
				[x]['link'] => link
				[x]['login'] => login
				[x]['linkType'] => link type
			*/
			$menuArray[] = array(
				'title' => $menuTitle,
				'link' => $menuLink,
				'login' => $menuLogin,
				'linkType' => $menuLinkType,
				'section' => $menuMainSec
			);
		}
		
		$page = (isset($_GET['page'])) ? $_GET['page'] : 'main';
		
		$server = explode('/', $_SERVER['PHP_SELF']);
		$count = count($server);
		$k = $count - 1;
		
		$ext = $server[$k];
		
		$sections = array('main', 'personnel', 'simm', 'ship');
		
		echo "<ul id='nav-main'>";
		
		foreach ($menuArray as $key => $value)
		{
			if ($value['linkType'] == "onsite")
			{
				$prefix = WEBLOC;
				$target = "";
			}
			else
			{
				$prefix = "";
				$target = " target='_blank'";
			}
			
			$active = ($this->_page_check($page) == $value['section']) ? ' class="active"' : FALSE;
				
			if ($value['login'] == "n")
			{
				echo "<li>";
				echo "<a id='". $value['section'] ."' href='" . $prefix . $value['link'] . "'" . $target . " ". $active .">" . $value['title'] . "</a>";
				
				if (in_array($value['section'], $sections))
				{
					$this->general($value['section']);
				}
				
				echo "</li>";
			}
			else
			{
				if (isset($_SESSION['sessionCrewid']) && UID == $_SESSION['systemUID'])
				{
					echo "<li><a href='" . $prefix . $value['link'] . "'" . $target . " ". $active .">" . $value['title'] . "</a></li>";
				}
			}
		}
		
		echo "</ul>";
	}
	
	function general($class)
	{	
		$cat = ($class == 'starbase') ? 'ship' : $class;

		/* get the mainNav items from the DB */
		$getMenu = "SELECT * FROM sms_menu_items ";
		$getMenu.= "WHERE menuCat = 'general' AND menuMainSec = '$cat' ";
		$getMenu.= "AND menuAvailability = 'on' ORDER BY menuGroup, menuOrder ASC";
		$getMenuResult = mysql_query( $getMenu );
		
		/* loop through whatever comes out of the database */
		while( $fetchMenu = mysql_fetch_array( $getMenuResult ) ) {
			extract( $fetchMenu, EXTR_OVERWRITE );
			
			/* create a multi-dimensional array with the data
				[x] => array
				[x]['title'] => title
				[x]['link'] => link
				[x]['login'] => login
				[x]['linkType'] => link type
				[x]['group'] => group
			*/
			$menuArray[] = array(
				'title' => $menuTitle,
				'link' => $menuLink,
				'login' => $menuLogin,
				'linkType' => $menuLinkType,
				'group' => $menuGroup,
				'section' => $menuMainSec
			);
			
			if (!isset($groupArray))
			{
				$groupArray = "";
			}
			
			if (!is_array($groupArray))
			{
				$groupArray = array( $menuGroup );
			}
			elseif (is_array($groupArray) && !in_array($menuGroup, $groupArray))
			{
				$groupArray[] = $menuGroup;
			}	
		}
		
		echo "<ul class='hidemenu'>";
		
		foreach ($groupArray as $key2 => $value2)
		{
			if ($key2 != 0)
			{
				echo "<li class='spacer'>&nbsp;</li>";
			}
		
			foreach ($menuArray as $key => $value)
			{
				if ($value2 == $value['group'])
				{
					if ($value['linkType'] == "onsite")
					{
						$prefix = WEBLOC;
						$target = "";
					}
					else
					{
						$prefix = "";
						$target = " target='_blank'";
					}
					
					if ($value['login'] == "n")
					{
						echo "<li><a class='". $value['section'] ."' href='" . $prefix . $value['link'] . "'" . $target . ">" . $value['title'] . "</a></li>";
					}
					else
					{
						if (isset($sessionCrewid))
						{
							echo "<li><a href='" . $prefix . $value['link'] . "'" . $target . ">" . $value['title'] . "</a></li>";	
						}
					}
				}	
			}
		}
		
		echo "</ul>";
	}
	
	function foo()
	{
		$page = (isset($_GET['page'])) ? $_GET['page'] : 'main';
		
		return $page;
	}
	
	function _page_check($page = '')
	{
		foreach ($this->pages as $key => $value)
		{
			$search = array_search($page, $value);
			
			if ($search !== FALSE)
			{
				return $key;
			}
		}
		
		return FALSE;
	}
	
}

?>