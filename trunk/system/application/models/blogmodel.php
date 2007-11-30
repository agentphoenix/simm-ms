<?php

class Blogmodel extends Model {

	var $title		= '';
	var $content	= '';
	var $date		= '';
	
	function Blogmodel()
	{
		// call the model constructor
		parent::Model();
	}
	
	function get_last_ten_entries()
	{
		$query = $this->db->get('entries', 10);
		return $query->result();
	}
	
	function insert_entry()
	{
		$this->title	= $_POST['title'];
		$this->content	= $_POST['content'];
		$this->date		= time();
		
		$this->db->insert('entries', $this);
	}
	
	function update_entry()
	{
		$this->title	= $_POST['title'];
		$this->content	= $_POST['content'];
		$this->date		= time();
		
		$this->db->update('entries', $this, array('id', $_POST['id']));
	}
	
}

?>