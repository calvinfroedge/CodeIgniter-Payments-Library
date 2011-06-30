Installation
============

Installing is simple.  Just put files from config and libraries folders in their respective places within application.

If you can do...


$this->load->library('payments');
$this->ci->load->config('payments');

...in one of your controllers without trouble you're good to go.

Examples
========

Recurring Payments
------------------

### Making Payments

Note that the fields you put in $billing_data may vary depending on which payment system you use.  Different libraries will have different formats they request your data in.  Here is an example for PayPal (using recurring subscriptions):

First, you need to make sure you have set payment type and payment method to your session (whether it is recurring or onetime, and which payment module you will use).  Note that if this is not set it will default to whatever you have in your config file.

`
$this->session->set_userdata(array('payment_type' => 'recurring', 'payment_system' => 'paypal'));
`

Now, make your call.  Note that the second parameter could be just true or false.  I included both $billing_data array and $trial for clarity.

`
$billing_data = array(
	$this->form_validation->set_value('billing_cc_type'), //credit card type
	$this->form_validation->set_value('billing_cc_number'), //credit card number
	$this->form_validation->set_value('billing_exp_date_mm').set_value('billing_exp_date_yyyy'), //credit card expiration date
	$this->form_validation->set_value('billing_first_name'), //billing first name
	$this->form_validation->set_value('billing_last_name'), //billing last name
	gmdate("c"), 
	$billing_variables->billing_period, //month, year, etc
	$billing_variables->billing_frequency, //how many times per period
	$amount, //the amount to bill
	$this->config->item('max_failed_payments') //The number of times a payment is allowed to fail
);

$trial = true;

$payment = $this->payments->make_payment($billing_data, $trial);
`

This returns an object with $payment->response and $payment->status.  You can process these further from there.