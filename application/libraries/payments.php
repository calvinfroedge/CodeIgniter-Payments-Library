<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
@author Calvin Froedge
@url	www.calvinfroedge.com

Use however you like!
*/

class Payments
{

	/*
	|--------------------------------------------------------------------------
	| Creating Payments
	|--------------------------------------------------------------------------
	|
	|These functions handle creating payments
	|
	*/
	
	function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->library(array('payments/paypal', 'session'));
		$this->ci->load->config('payments');
	}

	/**
	 * Make a new payment
	 *
	 * @param	array
	 * @param	bool
	 * @return	object
	 */			
	public function make_payment($billing_data, $trial = NULL)
	{
		$payment_function = 'make_'.$this->check_payment_type().'_payment';
		$payment = $this->$payment_function($this->check_payment_module(), $billing_data, $payment_function, $trial);
		
		if($payment->status == 'success')
		{
			return (object) array('status' => 'success', 'response' => $payment->response);
		}
		else
		{
			return (object) array('status' => 'failure', 'response' => $payment->response);
		}
	}
	
	/**
	 * Make a recurring payment
	 *
	 * @param	string
	 * @param	array
	 * @param	string
	 * @param	bool
	 * @return	object
	 */	
	private function make_recurring_payment($payment_module, $billing_data, $payment_function, $trial = FALSE)
	{
		return $this->ci->$payment_module->$payment_function($billing_data, $trial);
	}
	
	private function make_onetime_payment()
	{
		echo "onetime payment attempted, ";
	}

	/*
	|--------------------------------------------------------------------------
	| Subscription Functions
	|--------------------------------------------------------------------------
	|
	|These functions handle dealing with subscriptions
	|
	*/

	/**
	 * Update a profile for a given customer
	 *
	 * @param	array
	 * @param	string
	 * @return	object
	 */			
	public function update_subscription($data, $update_function)
	{
		$payment_module = $this->check_payment_module();
		$update = $this->ci->$payment_module->$update_function($data);
		if($update->status == 'success')
		{
			return (object) array('status' => 'success', 'response' => $update->response);
		}
		else
		{
			return (object) array('status' => 'failure', 'response' => $update->response);
		}
	}
	
	/**
	 * Get profile info for a given customer
	 *
	 * @param	string
	 * @return	object
	 */			
	public function get_profile_info($profile_identifier)
	{
		$payment_function = 'get_'.$this->check_payment_type().'_profile_info';
		return $this->$payment_function($this->check_payment_module(), $profile_identifier);
	}

	/**
	 * Get profile info for a recurring payments customer
	 *
	 * @param	string
	 * @param	string
	 * @return	object
	 */		
	private function get_recurring_profile_info($payment_module, $profile_identifier)
	{
		return $this->ci->$payment_module->get_recurring_profile_info($profile_identifier);
	}	

	/*
	|--------------------------------------------------------------------------
	| Utility Functions
	|--------------------------------------------------------------------------
	|
	|These functions support the rest of the library
	|
	*/
		
	/**
	 * Check payment type
	 *
	 * @return	string
	 */	
	private function check_payment_type()
	{
		return ($this->ci->session->userdata('payment_type') != NULL ? $this->ci->session->userdata('payment_type')
		: $this->ci->config->item('default_payment-type'));
	}

	/**
	 * Check payment module
	 *
	 * @return	string
	 */	
	private function check_payment_module()
	{
		return ($this->ci->session->userdata('payment_system') != NULL ? $this->ci->session->userdata('payment_system') : $this->ci->config->item('payment-system_default'));
	}
	
	/**
	 * Set payment amount
	 *
	 * @param  array
	 * @return	object
	 */	
	public function set_payment_amount($billing_variables)
	{
		//Note, this logic will be dependent on your billing process.  You will need to customize this.
		return number_format($billing_variables->child_billing_rate * $this->ci->session->userdata('num_children') + $billing_variables->family_billing_rate, 2, '.', '');
	}	
	
}