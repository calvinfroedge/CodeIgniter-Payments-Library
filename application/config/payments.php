<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Payment Systems
|--------------------------------------------------------------------------
|
| Payment platforms currently utilized. 
| These values must match the names of their respective config and library file names.
|
*/
$config['payment-system_paypal']		= 'paypal';

/*
|--------------------------------------------------------------------------
| Default Payment System
|--------------------------------------------------------------------------
|
| Payment system to default to if not set in session
|
*/
$config['payment-system_default']		= 'paypal';

/*
|--------------------------------------------------------------------------
| Payment Types
|--------------------------------------------------------------------------
|
| The types of payments which can be sent
|
*/
$config['recurring_payment-type'] = 'recurring';
$config['onetime_payment-type'] = 'flat_rate';

//Payment type to use by default
$config['default_payment-type'] = 'recurring';

/*
|--------------------------------------------------------------------------
|  Public function variables
|--------------------------------------------------------------------------
|
| Variables for commonly used public functions of the payments library.
|
*/
$config['payment-function_update-billing-info'] = 'update_billing_info';
$config['payment-function_cancel_subscription'] = 'cancel_subscription';
$config['payment-function_suspend_subscription'] = 'suspend_subscription';
$config['payment-function_activate_subscription'] = 'activate_subscription';
