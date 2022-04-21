<?php

class Ares {
	public $dp;

	function __construct()
    {
		$this->dp = new MySQLDataProvider();
    }

	function get($id,$cin=0,$name="Asseco")
    {
        return $this->dp->get($cin,$name);
    }
	
	function post($requestData)
    {
        return $this->dp->post($requestData);
    }
	// function hi($to) {
	// 	return  "Hi $to!";
	// }
}