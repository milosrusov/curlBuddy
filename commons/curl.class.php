<?php
	/**
	 *	CURL method that will be extended by other 
	 * 	method classes.
	**/
	class curl{

		//	Standard/default curl options
		protected $_standard_opts = array(
			//	TRUE to include the header in the output.
			CURLOPT_HEADER => true,
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
		protected $_u_defined_opts = array();
		//	Standard/default curl headers
		protected $_standard_headers = array();
		//	User defined headers
		protected $_u_defined_headers = array();
		//	User defined query used for GET type requests
		protected $_u_query = false;
		//	Execution error
		protected $_error = false;
		//	Response header
		protected $_response_header = false;
		//	Response body
		protected $_response = false;
		//	HTTP status code
		protected $_http_code = false;

		//	Creates a new instance of this class
		//	*	$u_defined_opts 	-	(optional | array) Initial array of user
		//								deefined options.
		//	*	$u_defined_opts 	-	(optional | string) When a string is set
		//								we will interpret it as the CURLOPT_URL
		protected function __construct($u_defined_opts=array()){
			//	Set user defined options
			if(is_array($u_defined_opts)){
				$this->setOpts($u_defined_opts);
			}elseif(is_string($u_defined_opts)){
				$url = filter_var($u_defined_opts, FILTER_SANITIZE_URL);
				if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) !== false){
					$this->setOpt(CURLOPT_URL, $url);
				}
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

		//	Set the POST body
		//	*	$data 	- 	(required | array) Set as the CURLOPT_POSTFIELDS array
		//	*	$data 	-	(required | string) Set as the CURLOPT_POSTFIELDS string
		//					this will also trigger the header Content-Length: int to
		//					be set.
		public function setData($data=array(), $raw_url_encode=false){
			$verb = $this->_standard_opts[CURLOPT_CUSTOMREQUEST];
			if(is_string($data) && in_array($verb, array('POST', 'PUT', 'PATCH', 'DELETE'))){
				$this->setHeader('Content-Length:', strlen($data));
				$this->setOpt(CURLOPT_POSTFIELDS, $data);
			}else if(is_array($data)){
				if($verb == 'GET'){
					// $this->_u_query = http_build_query($data, '', '&amp;');
					$this->_u_query = http_build_query($data);
					return true;
				}
				$this->setOpt(CURLOPT_POSTFIELDS, $data);
			}
		}

		//	Execute the curl request
		public function send(){
			//	Initialize the cURL Handler for the endpoint URL
			$ch = curl_init();
			//	Combine the standard and user defined option arrays
			$verb = $this->_standard_opts[CURLOPT_CUSTOMREQUEST];
			$opts = $this->_u_defined_opts + $this->_standard_opts;
			foreach(array_keys($opts) as $option){
				if($verb == 'GET' && $option == CURLOPT_URL){
					//	Modify the URL to contain the GET query string
					if($this->_u_query !== false){
						$opts[$option] .= (strpos($opts[$option], '?') !== false ? '&' : '?') . $this->_u_query;
					}
				}
				curl_setopt($ch, $option, $opts[$option]);
			}
			//	Combine the standard and user defined header arrays
			$curlHeaders = array();
			$headers = $this->_u_defined_headers + $this->_standard_headers;
			foreach(array_keys($headers) as $name){
				$value = trim($headers[$name]);
				array_push($curlHeaders, "$name $value");
			}
			if(!empty($curlHeaders)){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
			}
			//	Make the cURL request and get the response
			$response = curl_exec($ch);
			if(curl_error($ch)){
				//	Throw an exception
				// throw new Exception('cURL-Error[' . curl_errno($ch) . ']: ' . curl_error($ch), curl_errno($ch));
				$this->_error = 'curlBuddy-Error[' . curl_errno($ch) . ']: ' . curl_error($ch);
				curl_close($ch);
				return false;
			}
			//	Set the HTTP status code
			$this->_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			//	Attempt to parse the response header and body
			if($this->_http_code != 204){
				list($headers, $response) = explode("\r\n\r\n", $response, 2);
				if(empty($response)){
					$response = $headers;
					$headers = false;
				}
				$this->_response_header = ($headers !== false && is_string($headers)) ? explode("\n", $headers) : false;
			}
			// Close request to clear up some resources
			curl_close($ch);
			//	Set the response
			$this->_response = $response;
			//	Return to caller
			return true;
		}

		//	return the response header
		public function responseHeader(){
			return $this->_response_header;
		}

		//	Return the response
		public function response(){
			return $this->_response;
		}

		//	Return the HTTP status code
		public function statusCode(){
			return $this->_http_code;
		}

		//	Return the error message
		public function errorMessage(){
			return $this->_error;
		}

		//	Check if there are any errors
		public function hasError(){
			if($this->_error !== false){
				return true;
			}
			return $this->_error;
		}
	}