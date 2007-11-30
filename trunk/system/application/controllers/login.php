<?php

/**
**/

class Login extends Controller {
	
	function Login()
	{
		parent::Controller();	
	}
	
	function index()
	{
		/* load the database */
		$this->load->database('default');
		
		/* get the messages */
		$query = $this->db->query('SELECT * FROM sms_messages WHERE messageid = 1 LIMIT 1');
		
		/* set the content of the page equal to the welcome message */
		foreach ($query->result() as $row)
		{
			$data['content'] = $row->welcome;
		}
		
		/* set the page's title */
		$data['title'] = "Welcome to Jefferson";
		
		/* load url helper */
		$this->load->helper('url');
		
		/* load the view */
		$this->load->view('default/main_index', $data);
	}
	
	function reset()
	{
		/* load the database */
		$this->load->database('default');
		
		/* get the messages */
		$query = $this->db->query('SELECT * FROM sms_messages WHERE messageid = 1 LIMIT 1');
		
		/* set the content of the page equal to the welcome message */
		foreach ($query->result() as $row)
		{
			$data['content'] = $row->welcome;
		}
		
		/* set the page's title */
		$data['title'] = "Welcome to Jefferson";
		
		/* load url helper */
		$this->load->helper('url');
		
		/* load the view */
		$this->load->view('default/main_index', $data);
	}
	
	function check()
	{
		/* load the database */
		$this->load->database('default');
		
		/* get the messages */
		$query = $this->db->query('SELECT * FROM sms_messages WHERE messageid = 1 LIMIT 1');
		
		/* set the content of the page equal to the welcome message */
		foreach ($query->result() as $row)
		{
			$data['content'] = $row->welcome;
		}
		
		/* set the page's title */
		$data['title'] = "Welcome to Jefferson";
		
		/* load url helper */
		$this->load->helper('url');
		
		/* load the view */
		$this->load->view('default/main_index', $data);
	}
	
}

?>