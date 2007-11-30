<?php

class Blog extends Controller {

	function index()
	{
		$data['todo_list'] = array('Clean House', 'Call Mom', 'Run Errands');
		
		$data['title'] = "My Real Title";
		$data['heading'] = "My Real Heading";
		
		$this->load->view('blogview', $data);
	}
	
	function comments()
	{
		echo 'Look at this!';
	}
	
	function _utility()
	{
		// some code
	}
	
	function misc()
	{
		// loading model
		$this->load->model('Model_name');
		$this->load->model('blog/queries');
		
		// accessing loaded models
		$this->Model_name->function();
		
		// assigning new name
		$this->load->model('Model_name', 'fubar');
		$this->fubar->function();
	}
	
	function misc2()
	{
		// loading helper
		$this->load->helper('url');
		
		// loading multiple helpers
		$this->load->helper( array('helper1', 'helper2', 'helper3') );
		
		# can be accessed in a view
		
		$this->load->view('blogview', $data);
	}
	
}

?>