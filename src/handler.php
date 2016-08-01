<?php

namespace testrest;
/* Handler 
Parse the request data and the data response, and return it at front_controller.
*/

class Handler { 
		
	public $_request = array();
	private $_method = "";		
	private $_code = 200;

	public function __construct(){
		$this->inputs();
	}
		
	// Input Function 
		
	/* Returns the method of the request */
	
	public function getMethod(){
		return $_SERVER['REQUEST_METHOD'];
	}
	
	/* Read data content in the body of the request */
		
	public function inputs(){
		switch($this->getMethod()){
			case "POST":
				$this->_request = $this->parseData($_POST);
				break;
			case "GET":
			case "DELETE":
				$this->_request = $this->parseData($_GET);
				break;
			case "PUT":
			case "PATCH":
				parse_str(file_get_contents("php://input"),$this->_request);
				$this->_request = $this->parseData($this->_request);
				break;
			default:
				$this->response('',406);
				break;
		}
		return $this->_request;
	}
	
	/* Remove special characters from input data */
	
	private function parseData($data){
		$parsed_input = array();
		if(is_array($data)){
		foreach($data as $k => $v){
			$parsed_input[$k] = $this->parseData($v);
			}
		}else{
			if(get_magic_quotes_gpc()){
				$data = trim(stripslashes($data));
			}
			$data = strip_tags($data);
			$parsed_input = trim($data);
		}
		return $parsed_input;
	}
	
	/* Returns the data object access class for the class requested */
	
	function requireClassDao($name){
		$classDao = './src/Classes/' . $name . '/model/' . $name . '_dao.php';
		return $classDao;
	}
	// Response Function
	
	/* Parse the response of the request and return it */
		
	public function response($data, $status=200){
		$this->_code = ($status)?$status:200;
		header("HTTP/1.1 ". $this->_code);
		header("Content-Type:application/json");
		echo json_encode($data);
		exit;
	}
}	
?>





