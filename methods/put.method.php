<?php
	/**
	 *	HTTP PUT method
	**/
	class putMethod extends basicMethod{

		//	Creates a new instance of this class
		//	*	$u_defined_opts 	-	(optional | array) Initial array of user
		//								deefined options.
		//	*	$u_defined_opts 	-	(optional | string) When a string is set
		//								we will interpret it as the CURLOPT_URL
		public function __construct($u_defined_opts=array()){
			//	Calls the parent's constructor first
			parent::__construct($u_defined_opts);
			//	Set class specific opts
			$this->_standard_opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
			$this->_standard_opts[CURLOPT_POST] = true;
		}
	}