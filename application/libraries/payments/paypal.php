<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class PayPal
{
	
	function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->config('payments/paypal');
		$this->endpoint = $this->ci->config->item('paypal_api_endpoint');
		$this->settings = array(
			'USER'	=> $this->ci->config->item('paypal_api_username'),
			'PWD'	=> $this->ci->config->item('paypal_api_password'),
			'VERSION' => $this->ci->config->item('paypal_api_version'),
			'SIGNATURE'	=> $this->ci->config->item('paypal_api_signature'),		
		);
	}

	/**
	 * Create a new recurring payment
	 *
	 * @param	array
	 * @return	object
	 */		
	public function make_recurring_payment($billing_data)
	{
		$billing_keys = array(
			'CREDITCARDTYPE',
			'ACCT',
			'EXPDATE',
			'FIRSTNAME',
			'LASTNAME',
			'PROFILESTARTDATE',
			'BILLINGPERIOD',
			'BILLINGFREQUENCY',
			'AMT'
		);
		
		$function_settings = array(
		'DESC'	=> $this->ci->config->item('paypal_api_service_description'),
		'METHOD'	=> 'CreateRecurringPaymentsProfile'
		);
		
		return $this->handle_query(array_merge($this->settings, $function_settings), array_combine($billing_keys, $billing_data), $this->endpoint);
	}

	/**
	 * Build the query for the response and call the request function
	 *
	 * @param	array
	 * @param	array
	 * @param	string
	 * @return	array
	 */		
	private function handle_query($settings, $data, $endpoint)
	{
		$request = http_build_query(array_merge($settings, $data));
		return $this->parse_response($this->make_request($endpoint.$request));
	}
	
	/**
	 * Make a new request to PayPal API
	 *
	 * @param	array
	 * @return	array
	 */		
	private function make_request($request)
	{
		// create a new cURL resource
		$curl = curl_init();
		
		// set URL
		curl_setopt($curl, CURLOPT_URL, $request);
		
		// set to return the data as a string
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		
		// Run the query and get a response
		$response = curl_exec($curl);
		
		// close cURL resource, and free up system resources
		curl_close($curl);
		
		// Return the response
		return $response;	
	}

	/**
	 * Parse the response from the server
	 *
	 * @param	array
	 * @return	object
	 */		
	private function parse_response($response)
	{
		$results = explode('&',urldecode($response));
		foreach($results as $result)
		{
			list($key, $value) = explode('=', $result);
			$response_array[$key]=$value;
		}
		
		//$response = (object) $responses;
		$return_object = array();
		
		//Set the response status
		($response_array['ACK'] == 'Failure' ? $failure = TRUE : $success = TRUE );

		if(isset($failure)){
			$return_object[] = array('status'=>'failure') + array('response'=>$response_array['L_LONGMESSAGE0']);
		}
		if(isset($success)){
			$return_object[] = array('status'=>'success') + array('response'=>$response_array);
		}
		
		return (object) $return_object[0];
		
	}
	
	
	
}