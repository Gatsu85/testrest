<?php

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
		
	// Response Function
	
	/* write the log for the request */
				
	public function writelog($status, $data){
		$logfile = "./log/logfile.txt";
		$time = "[DATA: " . date("d-m-Y h:i:s", mktime()) . "] ";
		$source = "IP: ". $_SERVER['REMOTE_ADDR'];
		$request = $this->getMethod();
		$log_string = $time ." ". $request . " request from " . $source . " Status: " . $status;
		if ($status != 200 && $status != 201){
			$log_string .= " Error: " .$data;
		}
		$log_string .= "\r\n";
		$log = fopen($logfile, "a+");
		fwrite($log, $log_string);
		fclose($log);
	}
		
	/* Parse the response of the request and return it */
		
	public function response($data,$status=200){
		header("Content-Type:application/json");
		$this->_code = ($status)?$status:200;
		$this->writelog($status,$data);
		echo json_encode($data);
		exit;
	}
}	
?>





