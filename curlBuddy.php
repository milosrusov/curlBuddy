<?php
	/**
	 *	Simple PHP cURL wrapper 
	**/
	final class curlBuddy{

		//	Standard/default curl options
		private $_standard_opts = array(
			//	TRUE to include the header in the output.
			CURLOPT_HEADER => false,
			//	TRUE to return the transfer as a string of the return value of curl_exec() 
			//	instead of outputting it out directly.
			CURLOPT_RETURNTRANSFER => true,
			//	1 to check the existence of a common name in the SSL peer certificate. 
			//	2 to check the existence of a common name and also verify that it matches the hostname provided. 
			CURLOPT_SSL_VERIFYHOST => false,
			//	FALSE to stop cURL from verifying the peer's certificate. 
			CURLOPT_SSL_VERIFYPEER => false,
			//	TRUE to follow any "Location: " header that the server sends as part of the HTTP header
			CURLOPT_FOLLOWLOCATION => true,
			//	The maximum amount of HTTP redirections to follow.
			CURLOPT_MAXREDIRS => 20,
			//	The maximum number of milliseconds to allow cURL functions to execute.
			//	1000ms in 1s => 60,000ms is 60s
			CURLOPT_TIMEOUT_MS => 60000,
		);
		//	User defined opts.
		//	Can append to the standard opts or override it.
		private $_u_defined_opts = array();
		//	Standard/default curl headers
		private $_standard_headers = array();
		//	User defined headers
		private $_u_defined_headers = array();

		//	Creates a new instance of curlBuddy
		public function __construct($u_defined_opts=array()){
			//	Set user defined options
			if(is_array($u_defined_opts)){
				$this->setOpts($u_defined_opts);
			}
		}

		//	Set options for the curl request
		//	*	$opt 	-	(required) The CURLOPT_XXX option to set
		//	*	$value 	-	(required) The value of the option
		public function setOpt($opt, $value){
			//	Options set here will be contain within the $_u_defined_opts array;
			$this->_u_defined_opts[$opt] = $value;
		}

		//	Set an array of options
		//	*	$opts 	-	(required) Array containing CURLOPT_XXX => value
		public function setOpts($opts=array()){
			if(is_array($opts)){
				foreach($opts as $opt => $value){
					$this->setOpt($opt, $value);
				}
			}
		}

		//	Set headers for the curl request
		//	*	$header 	-	(required | string | Format: Content-Type:) The header name 
		//	*	$value 		-	(required | string) The header value string
		//	
		//	Example: setHeader('Content-Type:', 'application/x-www-form-urlencoded; charset=utf-8')
		public function setHeader($header, $value){
			//	Headers set here will be contained within the $_u_defined_headers array
			//	Format: "myHeader:"
			$header = preg_replace('/::$/', ':', $header . ':');
			$this->_u_defined_headers[$header] = $value;
		}

		//	Set an array of headers
		//	*	$headers 	-	(required | array) Array of header name => value
		//
		//	Example: setHeaders(array('Conent-Type:'=>'application/x-www-form-urlencoded; charset=utf-8', 'Cache-Control:'=>'no-cache'))
		public function setHeaders($headers=array()){
			if(is_array($headers)){
				foreach($headers as $header => $value){
					$this->setHeader($header, $value);
				}
			}
		}

		//	Execute the curl request
		public function send(){
			return true;
		}
	}