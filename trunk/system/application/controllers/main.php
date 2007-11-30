<?php

/**
	To add a new page to this section of the site, copy and paste the
	following code after the last function, then create a PHP page of
	the same name in the views/default folder with the name
	main_pagename.php. To access your page, go to http://<yoursite>/main/pagename
	
	function pagename()
	{
		$this->load->view('default/main_pagename');
	}
**/

class Main extends Controller {
	
	function Main()
	{
		parent::Controller();	
	}
	
	function index()
	{
		/* get the messages */
		$query = $this->db->query('SELECT * FROM sms_messages WHERE messageid = 1 LIMIT 1');
		
		/* set the content of the page equal to the welcome message */
		foreach ($query->result() as $row)
		{
			$data['content'] = auto_typography($row->welcome);
		}
		
		/* set the page's title */
		$data['title'] = "Welcome to Jefferson";
		
		/* load the view */
		$this->load->view('main_index', $data);
	}
	
	function news()
	{
		/* get the messages */
		$query = $this->db->query('SELECT * FROM sms_news ORDER BY newsOrder DESC');
		
		/* set the content of the page equal to the welcome message */
		foreach ($query->result() as $row)
		{
			$data['newsDate'] = $row->newsDate;
			$data['newsTitle'] = $row->newsTitle;
			$data['newsContent'] = $row->newsContent;
		}
		
		/* set the page's title */
		$data['title'] = "Welcome to Jefferson";
		
		/* load the view */
		$this->load->view('main_news', $data);
	}
	
}

?>