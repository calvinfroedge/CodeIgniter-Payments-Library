<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payments
{
	function __construct()
	{
		$this->ci =& get_instance();
	}
	
	public function make_payment($payment_module, $payment_type, $billing_data)
	{
		$payment_function = 'make_'.$payment_type.'_payment';
		$this->ci->load->library('payments/'.$payment_module);
		$payment = $this->$payment_function($payment_module, $billing_data, $payment_function);
		if($payment->status == 'success')
		{
			return (object) array('status' => 'success');
		}
		else
		{
			return (object) array('status' => 'failure', 'response' => $payment->response);
		}
	}
	
	private function make_recurring_payment($payment_module, $billing_data, $payment_function)
	{
		return $this->ci->$payment_module->$payment_function($billing_data);
	}
	
	private function make_onetime_payment()
	{
		echo "onetime payment attempted, ";
	}	
	
}
