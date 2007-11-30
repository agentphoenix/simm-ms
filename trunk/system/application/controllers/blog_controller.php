<?php

class Blog_controller extends Controller {

	function blog()
	{
		$this->load->model('Blog');
		
		$data['query'] = $this->Blog->get_last_ten_entries();
		
		$this->load->view('blog', $data);
	}
	
}

?>