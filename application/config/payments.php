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
$config['payment_types'] = array(
	'onetime',
	'recurring'
);

$config['default_payment-type'] = 'recurring';
