# Here is the list of all the HOOKS triggered by the plugin

## PAYMENTS

#### vikrestaurants_payment_before_admin_params
`do_action('vikrestaurants_payment_before_admin_params_{driver}', JPayment $payment)`

##### Parameters
- **$payment** (JPayment)
The payment object that has been instantiated (passed by reference).

##### Description
Plugins can manipulate the properties of this object.
Fires before the configuration array is generated.
The name of the driver will be always appended at the end of the hook.

`@since 1.0`

---

#### vikrestaurants_payment_after_admin_params
`do_action('vikrestaurants_payment_after_admin_params_{driver}', JPayment $payment, array $config)`

##### Parameters
- **$payment** (JPayment)
The payment object that has been instantiated (passed by reference).
- **$config** (array)
The configuration array (passed by reference).

##### Description
Plugins can manipulate the configuration form of the payment.
Fires after generating the form that will be used as configuration.
The name of the driver will be always appended at the end of the hook.

`@since 1.0`

---

#### vikrestaurants_payment_before_begin_transaction
`do_action('vikrestaurants_payment_before_begin_transaction_{driver}', JPayment $payment)`

##### Parameters
- **$payment** (JPayment)
The payment object that has been instantiated (passed by reference).

##### Description
Plugins can manipulate the properties of this object.
Fires before the payment form is initiated.
The name of the driver will be always appended at the end of the hook.

`@since 1.0`

---

#### vikrestaurants_payment_after_begin_transaction
`do_action('vikrestaurants_payment_after_begin_transaction_{driver}', JPayment $payment, string $html)`

##### Parameters
- **$payment** (JPayment)
The payment object that has been instantiated (passed by reference).
- **$html** (string)
The resulting HTML form (passed by reference).

##### Description
Plugins can manipulate the generated HTML form.
Fires after generating the HTML payment form.
The name of the driver will be always appended at the end of the hook.

`@since 1.0`

---

#### vikrestaurants_payment_before_validate_transaction
`do_action('vikrestaurants_payment_before_validate_transaction_{driver}', JPayment $payment)`

##### Parameters
- **$payment** (JPayment)
The payment object that has been instantiated (passed by reference).

##### Description
Plugins can manipulate the properties of this object.
Fires before the payment transaction is validated.
The name of the driver will be always appended at the end of the hook.

`@since 1.0`

---

#### vikrestaurants_payment_after_validate_transaction
`do_action('vikrestaurants_payment_after_validate_transaction_{driver}', JPayment $payment, JPaymentStatus $status, mixed $response)`

##### Parameters
- **$payment** (JPayment)
The payment object that has been instantiated (passed by reference).
- **$status** (JPaymentStatus)
The object containing the status information about the transaction (passed by reference).
- **$response** (mixed)
An object containing the final response (passed by reference).
If not manipulated, this value will be NULL.

##### Description
Plugins can manipulate the response object to return.
By filling the `&$response` variable, this method will return it instead of the 
default `&$status` one.
Fires after validating the payment transaction.
The name of the driver will be always appended at the end of the hook.

`@since 1.0`

---

#### vikrestaurants_payment_on_after_validation
`do_action('vikrestaurants_payment_on_after_validation_{driver}', JPayment $payment, boolean $res)`

##### Parameters
- **$payment** (JPayment)
The payment object that has been instantiated (passed by reference).
- **$res** (boolean)
The result of the transaction (*true* when verified, *false* on failure).

##### Description
Plugins can manipulate the properties of this object.
Fires before the payment process is completed.
The name of the driver will be always appended at the end of the hook.

`@since 1.0`

---

#### vikrestaurants_payment_before_refund_transaction
`do_action('vikrestaurants_payment_before_refund_transaction_{driver}', JPayment $payment)`

##### Parameters
- **$payment** (JPayment)
The payment object that has been instantiated (passed by reference).

##### Description
Plugins can manipulate the properties of this object.
Fires before the the refund request is made.
The name of the driver will be always appended at the end of the hook.

`@since 1.2`

---

#### vikrestaurants_payment_after_refund_transaction
`do_action('vikrestaurants_payment_after_refund_transaction_{driver}', JPayment $payment, JPaymentStatus $status, mixed $response)`

##### Parameters
- **$payment** (JPayment)
The payment object that has been instantiated (passed by reference).
- **$status** (JPaymentStatus)
The object containing the status information about the transaction (passed by reference).
- **$response** (mixed)
An object containing the final response (passed by reference).
If not manipulated, this value will be NULL.

##### Description
Plugins can manipulate the response object to return.
By filling the `&$response` variable, this method will return it instead of the 
default `&$status` one.
Fires after validating the refund request.
The name of the driver will be always appended at the end of the hook.

`@since 1.2`

---

#### load_payment_gateway_vikrestaurants
`do_action_ref_array('load_payment_gateway_vikrestaurants', array $drivers, string $payment)`

##### Parameters
- **$drivers** (array) 
A list of supported drivers (passed by reference).
- **$payment** (string)
The name of the gateway to load.

##### Description
Trigger action to obtain a list of classnames of the payment gateway.
The action should autoload the file that contains the classname.
In case the payment should be loaded, the classname MUST be
pushed within the `&$drivers` array.
Fires before the instantiation of the returned classname.

`@since 1.0`

---

### get_supported_payments_vikrestaurants
`apply_filters('get_supported_payments_vikrestaurants', array $drivers)`

##### Parameters
- **$drivers** (array)
An array containing the list of the supported payments.

##### Description
Hook used to filter the list of all the supported drivers.
Every plugin attached to this filter will be able to push one
or more gateways within the `$drivers` array.

It is also possible to manipulate the elements in the array, 
for example to detach a deprecated payment.

`@since 1.0`

---

## SMS

#### sms_driver_before_admin_params_vikrestaurants
`do_action('sms_driver_before_admin_params_vikrestaurants', JSmsDriver &$driver)`

##### Parameters
- **$driver** (JSmsDriver)
The driver object that has been instantiated (passed by reference).

##### Description
Plugins can manipulate the properties of this object.
Fires before the configuration array is generated.

`@since 1.0`

---

#### sms_driver_after_admin_params_vikrestaurants
`do_action('sms_driver_after_admin_params_vikrestaurants', JSmsDriver &$driver, array &$config)`

##### Parameters
- **$driver** (JSmsDriver)
The driver object that has been instantiated (passed by reference).
- **$config** (array)
The configuration array (passed by reference).

##### Description
Plugins can manipulate the configuration form of the driver.
Fires after generating the form that will be used as configuration.

`@since 1.0`

---

#### sms_driver_before_send_vikrestaurants
`do_action('sms_driver_before_send_vikrestaurants', JSmsDriver &$driver, string &$phone, string &$text)`

##### Parameters
- **$driver** (JSmsDriver)
The driver object that has been instantiated (passed by reference).
- **$phone** (string)
The phone number of the receiver (passed by reference).
- **$text** (string)
The message to be sent (passed by reference).

##### Description
Plugins can manipulate the properties of the driver.
Fires before sending the SMS.

`@since 1.0`

---

#### sms_driver_after_send_vikrestaurants
`do_action('sms_driver_after_send_vikrestaurants', JSmsDriver &$driver, JSmsStatus &$status, mixed &$response)`

##### Parameters
- **$driver** (JSmsDriver)
The driver object that has been instantiated (passed by reference).
- **$status** (JSmsStatus)
The object containing the status information about the notification (passed by reference).
- **$response** (mixed)
An object containing the final response (passed by reference).
If not manipulated, this value will be NULL.

##### Description
Plugins can manipulate the response object to return.
Fires after dispatching the sms.

`@since 1.0`

---

#### sms_driver_before_estimate_vikrestaurants
`do_action('sms_driver_before_estimate_vikrestaurants', JSmsDriver &$driver, string &$phone, string &$text)`

##### Parameters
- **$driver** (JSmsDriver)
The driver object that has been instantiated (passed by reference).
- **$phone** (string)
The phone number of the receiver (passed by reference).
- **$text** (string)
The message to be sent (passed by reference).

##### Description
Plugins can manipulate the properties of this object.
Fires before estimating the remaining credit.

`@since 1.0`

---

#### sms_driver_after_estimate_vikrestaurants
`do_action('sms_driver_after_estimate_vikrestaurants', JSmsDriver &$driver, mixed &$credit)`

##### Parameters
- **$driver** (JSmsDriver)
The driver object that has been instantiated (passed by reference).
- **$credit** (mixed)
A reference to the user credit (passed by reference).

##### Description
Plugins can manipulate the resulting credit.
Fires after estimating the user credit.

`@since 1.0`

---

#### get_supported_sms_drivers_vikrestaurants
`do_action('get_supported_sms_drivers_vikrestaurants', array $drivers)`

##### Parameters
- **$drivers** (array)
An array containing the list of the supported SMS providers.

##### Description
Hook used to filter the list of all the supported drivers.
Every plugin attached to this filter will be able to push one
or more gateways within the `$drivers` array.

It is also possible to manipulate the elements in the array, 
for example to detach a deprecated provider.

`@since 1.0`

---

#### load_sms_driver_vikrestaurants
`do_action('load_sms_driver_vikrestaurants', array &$drivers, string $driver)`

##### Parameters
- **$drivers** (array) 
A list of supported drivers (passed by reference).
- **$driver** (string)
The name of the sms driver to load.

##### Description
Trigger action to obtain a list of classnames of the sms providers.
The action should autoload the file that contains the classname.
In case the driver should be loaded, the classname MUST be
pushed within the `&$drivers` array.
Fires before the instantiation of the returned classname.

`@since 1.0`

---

## SYSTEM

#### vikrestaurants_before_dispatch
`do_action('vikrestaurants_before_dispatch')`

##### Description
Fires before the controller of VikRestaurants is dispatched.
Useful to require libraries and to check user global permissions.

`@since 1.0`

---

#### vikrestaurants_after_dispatch
`do_action('vikrestaurants_after_dispatch')`

##### Description
Fires after the controller of VikRestaurants is dispatched. Useful to include
web resources (CSS and JS). In case the controller terminates the process
(exit or die), this hook won't be fired.

`@since 1.0`

---

#### vikrestaurants_before_display_{VIEW}
`do_action_ref_array('vikrestaurants_before_display_{VIEW}', JView &$view)`

##### Parameters
- **&$view** (JView)
The instance of the view to display (passed by reference).

##### Description
Fires before the controller of VikRestaurants displays the requested {VIEW}.

`@since 1.0`

---

#### vikrestaurants_after_display_{VIEW}
`do_action('vikrestaurants_after_display_{VIEW}', JView $view)`

##### Parameters
- **$view** (JView)
The instance of the displayed view.

##### Description
Fires after the controller of VikRestaurants has displayed the requested {VIEW}.

`@since 1.0`

---

## DATABASE

#### vik_get_db_prefix
`apply_filters('vik_get_db_prefix', string $prefix)`

##### Parameters
- **$prefix** (string)
The database prefix to use for queries.

##### Description
Hook used to filter the default WP database prefix before it is used.

`@since 1.0`

---

#### vik_db_suppress_errors 
`apply_filters('vik_db_suppress_errors', boolean $suppress)`

##### Parameters
- **$suppress** (boolean)
True to suppress the errors, false otherwise (false by default).

##### Description
Hook used to suppress/enable database errors.

`@since 1.0`

---

#### vik_db_show_errors
`apply_filters('vik_db_show_errors', boolean $show)`

##### Parameters
- **$show** (boolean)
True to show the errors, false otherwise (true by default).

##### Description
In case errors are suppressed, this hook would result useless.
Errors can be shown only if they are NOT suppressed.

`@since 1.0`

---

## DATE

#### vik_date_default_timezone
`apply_filters('vik_date_default_timezone', string $timezone)`

##### Parameters
- **$timezone** (string)
The default server timezone that will be registered.

##### Description
Hook used to define a different timezone instead of the one of the server.

`@since 1.0`

---

## RESOURCES

#### vik_before_include_script
`apply_filters('vik_before_include_script', boolean true, string $url, string $id, string $version, boolean $footer)`

##### Parameters
- **$load** (boolean)
True to load the resource, false to ignore it.
- **$url** (string)
The resource URL.
- **$id** (string)
The script ID attribute.
- **$version** (string)
The script version, if specified.
- **$footer** (string)
True whether the script is going to be loaded in the footer.

##### Description
Hook used to approve/deny the loading of the given script.

`@since 1.0`

---

#### vik_before_include_style
`apply_filters('vik_before_include_style', boolean true, string $url, string $id, string $version)`

##### Parameters
- **$load** (boolean)
True to load the resource, false to ignore it.
- **$url** (string)
The resource URL.
- **$id** (string)
The stylesheet ID attribute.
- **$version** (string)
The stylesheet version, if specified.

##### Description
Hook used to approve/deny the loading of the given stylesheet.

`@since 1.0`

---

## WIDGETS

#### vik_widget_before_dispatch_site
`do_action_ref_array('vik_widget_before_dispatch_site', array(string $id, JObject &$params))`

##### Parameters
- **$id** (string)
The widget ID (path name).
- **&$params** (JObject)
The widget configuration registry.

##### Description
Plugins can manipulate the configuration of the widget at runtime.
Fires before dispatching the widget in the front-end.

`@since 1.0`

---

#### vik_widget_after_dispatch_site
`do_action_ref_array('vik_widget_after_dispatch_site', array(string $id, string &$html))`

##### Parameters
- **$id** (string)
The widget ID (path name).
- **&$html** (string)
The HTML of the widget to display.

##### Description
Plugins can manipulate the configuration of the widget at runtime.
Fires before dispatching the widget in the front-end.

`@since 1.0`

---

## LANGUAGE

#### vik_plugin_load_language
`apply_filters('vik_plugin_load_language', boolean $loaded, string $domain)`

##### Parameters
- **$loaded** (boolean)
True if a translation for the requested domain has been already loaded.
- **&$domain** (string)
The plugin text domain.

##### Description
Plugins can use this filter to load the plugin translations from different folders.

`@since 1.0`

---
