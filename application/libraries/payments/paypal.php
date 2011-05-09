<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class PayPal
{
	
	function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->config('payments');
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
	 * Get profile info for a particular profile id
	 *
	 * @param	array
	 * @return	object
	 */		
	public function get_recurring_profile_info($profile_id)
	{
		$function_settings = array(
		'DESC'	=> $this->ci->config->item('paypal_api_service_description'),
		'METHOD'	=> 'GetRecurringPaymentsProfileDetails'
		);
		$data = array(
			'ProfileID' => $profile_id
		);
		$return_data = $this->handle_query(array_merge($this->settings, $function_settings), $data, $this->endpoint);
		
		$return_array = array(
			'profile_id' => $return_data->response['PROFILEID'],
			'status' => $return_data->response['STATUS'],
			'next_billing_date' => $return_data->response['NEXTBILLINGDATE'],
			'amount' => $return_data->response['AMT'],
			'billing_period' => $return_data->response['BILLINGPERIOD'],
			'billing_frequency' => $return_data->response['BILLINGFREQUENCY'],
			'failedpayments'	=> $return_data->response['FAILEDPAYMENTCOUNT'],
			'billing_method' => $this->ci->config->item('payment-system_paypal'),
			'billing_type' => $this->ci->config->item('recurring_payment-type')
		);
		
		return (object) $return_array;
	}

	/**
	 * Create a new recurring payment
	 *
	 * @param	array
	 * @return	object
	 */		
	public function make_recurring_payment($billing_data, $trial = FALSE)
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
			'AMT',
			'MAXFAILEDPAYMENTS'
		);
		
		if($trial)
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
				'AMT',
				'MAXFAILEDPAYMENTS',
				'TRIALBILLINGPERIOD',
				'TRIALBILLINGFREQUENCY',
				'TRIALAMT',
				'TRIALTOTALBILLINGCYCLES'
			);			
		}
		
		$function_settings = array(
		'DESC'	=> $this->ci->config->item('paypal_api_service_description'),
		'METHOD'	=> 'CreateRecurringPaymentsProfile'
		);
		
		return $this->handle_query(array_merge($this->settings, $function_settings), array_combine($billing_keys, $billing_data), $this->endpoint);
	}

	/**
	 * Update an existing payments subscription
	 *
	 * @param	array
	 * @return	object
	 */		
	public function update_billing_info($billing_data)
	{
		$billing_keys = array(
			'PROFILEID',
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
		'METHOD'	=> 'UpdateRecurringPaymentsProfile'
		);
		
		return $this->handle_query(array_merge($this->settings, $function_settings), array_combine($billing_keys, $billing_data), $this->endpoint);
	}

	/**
	 * Cancel a recurring subscription
	 *
	 * @param	array
	 * @return	object
	 */		
	public function cancel_subscription($profile_id)
	{
		$request_params = array(
			'PROFILEID',
			'ACTION'
		);
		
		$request_values = array(
			$profile_id,
			'Cancel'
		);
		
		$function_settings = array(
		'METHOD'	=> 'ManageRecurringPaymentsProfileStatus'
		);
		
		return $this->handle_query(array_merge($this->settings, $function_settings), array_combine($request_params, $request_values), $this->endpoint);
	}

	/**
	 * Suspend a subscription
	 *
	 * @param	string
	 * @return	object
	 */		
	public function suspend_subscription($profile_id)
	{
		$request_params = array(
			'PROFILEID',
			'ACTION'
		);
		
		$request_values = array(
			$profile_id,
			'Suspend'
		);
		
		$function_settings = array(
		'METHOD'	=> 'ManageRecurringPaymentsProfileStatus'
		);
		
		return $this->handle_query(array_merge($this->settings, $function_settings), array_combine($request_params, $request_values), $this->endpoint);
	}

	/**
	 * Activate a subscription
	 *
	 * @param	int
	 * @return	object
	 */		
	public function activate_subscription($profile_id)
	{
		$request_params = array(
			'PROFILEID',
			'ACTION'
		);
		
		$request_values = array(
			$profile_id,
			'Reactivate'
		);
		
		$function_settings = array(
		'METHOD'	=> 'ManageRecurringPaymentsProfileStatus'
		);
		
		return $this->handle_query(array_merge($this->settings, $function_settings), array_combine($request_params, $request_values), $this->endpoint);
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
		
		$return_object = array();
		
		//Set the response status
		
		($response_array['ACK'] == 'Success' ? $success = TRUE : $failure = TRUE );

		if(isset($failure)){
			$return_object[] = array('status'=>'failure') + array('response'=>$response_array['L_LONGMESSAGE0']);
		}
		if(isset($success)){
			$return_object[] = array('status'=>'success') + array('response'=>$response_array);
		}
		
		return (object) $return_object[0];
		
	}
	
	
	
}
