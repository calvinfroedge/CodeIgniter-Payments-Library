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

### Making Recurring Payments with PayPal

Note that the fields you put in $billing_data may vary depending on which payment system you use.  Different libraries will have different formats they request your data in.  Here is an example for PayPal (using recurring subscriptions):

First, you need to make sure you have set payment type and payment method to your session (whether it is recurring or onetime, and which payment module you will use).  Note that if this is not set it will default to whatever you have in your config file.

`
    $this->session->set_userdata(array('payment_type' => 'recurring', 'payment_system' => 'paypal'));
`

Now, make your call, passing in an array for $billing_data and a boolean for $trial (if you want to specify this is a free trial.

The params are (don't specify a key):

- Credit card type
- Credit card number
- Expire month (mm)
- Expire year (yyyy)
- First name
- Last name
- Billing period (month or year)
- Billing frequency (how many billing cycles during the period)
- Amount
- Max times a payment can fail before the subscription is invalidated.

`
    $payment = $this->payments->make_payment(array('visa', '2039923394027162', '051989', 'Calvin', 'Froedge', gmdate("c"), 'month', '1', '3'), true);
`

This returns an object with $payment->response and $payment->status.  You can process these further from there.