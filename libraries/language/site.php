<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  language
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

JLoader::import('adapter.language.handler');

/**
 * Switcher class to translate the VikRestaurants plugin site languages.
 *
 * @since 	1.0
 */
class VikRestaurantsLanguageSite implements JLanguageHandler
{
	/**
	 * Checks if exists a translation for the given string.
	 *
	 * @param 	string 	$string  The string to translate.
	 *
	 * @return 	string 	The translated string, otherwise null.
	 */
	public function translate($string)
	{
		$result = null;

		/**
		 * Translations go here.
		 * @tip Use 'TRANSLATORS:' comment to attach a description of the language.
		 */

		switch ($string)
		{
			/**
			 * VikRestaurants 1.1
			 */

			case 'VRRESERVATIONREQUESTMSG1':
				$result = __('Invalid Date', 'vikrestaurants');
				break;

			case 'VRRESERVATIONREQUESTMSG2':
				$result = __('Invalid Time', 'vikrestaurants');
				break;

			case 'VRRESERVATIONREQUESTMSG3':
				$result = __('The Restaurant is closed in the selected date and time', 'vikrestaurants');
				break;

			case 'VRRESERVATIONREQUESTMSG4':
				$result = __('Number of People selected is invalid', 'vikrestaurants');
				break;

			case 'VRRESERVATIONREQUESTMSG5':
				$result = __('The Date selected is in the past', 'vikrestaurants');
				break;

			case 'VRSTEPONESUBTITLE':
				$result = __('Date and Time', 'vikrestaurants');
				break;

			case 'VRSTEPTWOSUBTITLEZERO':
				$result = __('Select a Table', 'vikrestaurants');
				break;

			case 'VRSTEPTWOSUBTITLEONE':
				$result = __('Select a Room', 'vikrestaurants');
				break;

			case 'VRSTEPTWOSUBTITLETWO':
				$result = __('Tables Availability', 'vikrestaurants');
				break;

			case 'VRSTEPTHREESUBTITLE':
				$result = __('Confirm Reservation', 'vikrestaurants');
				break;

			case 'VRMAKEARESERVATION':
				$result = __('Make a Reservation', 'vikrestaurants');
				break;

			case 'VRDATE':
				$result = __('Date', 'vikrestaurants');
				break;

			case 'VRTIME':
				$result = __('Time', 'vikrestaurants');
				break;

			case 'VRPEOPLE':
				$result = __('People', 'vikrestaurants');
				break;

			case 'VRROOM':
				$result = __('Room', 'vikrestaurants');
				break;

			case 'VRTABLE':
				$result = __('Table', 'vikrestaurants');
				break;

			case 'VRFINDATABLE':
				$result = __('Find a Table', 'vikrestaurants');
				break;

			case 'VRRESNOSINGTABLEFOUND':
				$result = __('No table is available at the selected time', 'vikrestaurants');
				break;

			case 'VRRESTRYHINTS':
				$result = __('Please select a different time:', 'vikrestaurants');
				break;

			case 'VRRESNOTABLESSELECTNEWDATES':
				$result = __('Please select a different date', 'vikrestaurants');
				break;

			case 'VRRESMULTITABLEFOUND':
				$result = __('Only Shared Tables are available at %s', 'vikrestaurants');
				break;

			case 'VRCONTINUE':
				$result = __('Continue', 'vikrestaurants');
				break;

			case 'VRERRINSUFFCUSTF':
				$result = __('Error, please fill in all the required fields', 'vikrestaurants');
				break;

			case 'VRENTERYOURCOUPON':
				$result = __('Enter here your Coupon Code', 'vikrestaurants');
				break;

			case 'VRAPPLYCOUPON':
				$result = __('Apply Coupon', 'vikrestaurants');
				break;

			case 'VRMETHODOFPAYMENT':
				$result = __('Method of Payment', 'vikrestaurants');
				break;

			case 'VRCONFIRMRESERVATION':
				$result = __('Confirm Reservation', 'vikrestaurants');
				break;

			case 'VRERRCHOOSETABLEFIRST':
				$result = __('Error, please choose a table first', 'vikrestaurants');
				break;

			case 'VRYOURTABLESEL':
				$result = __('You have selected the table %s', 'vikrestaurants');
				break;

			case 'VRCHOOSEROOM':
				$result = __('Select Room:', 'vikrestaurants');
				break;

			case 'VRSUCCESSMESSSEARCH':
				$result = __('A table for %d people has been found!', 'vikrestaurants');
				break;

			case 'VRMESSNOWCHOOSETABLE':
				$result = __('Select the table you wish to get', 'vikrestaurants');
				break;

			case 'VRMESSNOWCHOOSEROOM':
				$result = __('Pick a Room from the list', 'vikrestaurants');
				break;

			case 'VRCONTINUEBUTTON0':
				$result = __('Select Table', 'vikrestaurants');
				break;

			case 'VRCONTINUEBUTTON1':
				$result = __('Select Room', 'vikrestaurants');
				break;

			case 'VRCONTINUEBUTTON2':
				$result = __('Continue', 'vikrestaurants');
				break;

			case 'VRCONTINUEBUTTONMULTI0':
				$result = __('Select a Shared Table', 'vikrestaurants');
				break;

			case 'VRTNOTAVAILABLE':
				$result = __('Not Available', 'vikrestaurants');
				break;

			case 'VRLEGENDSHAREDTABLE':
				$result = __('Occupancy of Shared Tables', 'vikrestaurants');
				break;

			case 'VRCOUPONFOUND':
				$result = __('The Coupon code has been applied!', 'vikrestaurants');
				break;

			case 'VRCOUPONNOTVALID':
				$result = __('Error, Invalid Coupon', 'vikrestaurants');
				break;

			case 'VRERRTABNOLONGAV':
				$result = __('Error, the table selected is no longer available', 'vikrestaurants');
				break;

			case 'VRERRINVPAYMENT':
				$result = __('Error, Invalid Payment Selected', 'vikrestaurants');
				break;

			case 'VRINSERTRESERVATIONERROR':
				$result = __('Error, Unable to Save your Reservation', 'vikrestaurants');
				break;

			case 'VRSEARCHDAYCLOSED':
				$result = __('We are sorry but the restaurant is closed for the date and time chosen', 'vikrestaurants');
				break;

			case 'VRWORKINGSHIFT':
				$result = __('Working Shift', 'vikrestaurants');
				break;

			case 'VRMENUSEARCH':
				$result = __('Search', 'vikrestaurants');
				break;

			case 'VRSHOWDESCRIPTION':
				$result = __('Details', 'vikrestaurants');
				break;

			case 'VRHIDEDESCRIPTION':
				$result = __('Hide Details', 'vikrestaurants');
				break;

			case 'VRORDERTITLE1':
				$result = __('Your Reservation', 'vikrestaurants');
				break;

			case 'VRORDERTITLE2':
				$result = __('Details', 'vikrestaurants');
				break;

			case 'VRORDERNUMBER':
				$result = __('Order Number', 'vikrestaurants');
				break;

			case 'VRORDERKEY':
				$result = __('Order Key', 'vikrestaurants');
				break;

			case 'VRORDERSTATUS':
				$result = __('Status', 'vikrestaurants');
				break;

			case 'VRORDERDATETIME':
				$result = __('Date', 'vikrestaurants');
				break;

			case 'VRORDERPEOPLE':
				$result = __('People', 'vikrestaurants');
				break;

			case 'VRPERSONALDETAILS':
				$result = __('Personal Details', 'vikrestaurants');
				break;

			case 'VRORDERLINK':
				$result = __('Order Link', 'vikrestaurants');
				break;

			case 'VRORDERROOM':
				$result = __('Room', 'vikrestaurants');
				break;

			case 'VRORDERTABLE':
				$result = __('Table', 'vikrestaurants');
				break;

			case 'VRORDERPAYMENT':
				$result = __('Payment', 'vikrestaurants');
				break;

			case 'VRORDERRESERVATIONCOST':
				$result = __('Reservation Cost', 'vikrestaurants');
				break;

			case 'VRORDERCOUPON':
				$result = __('Coupon', 'vikrestaurants');
				break;

			case 'VRRESERVATIONSTATUSREMOVED':
				$result = __('Removed', 'vikrestaurants');
				break;

			case 'VRRESERVATIONSTATUSPENDING':
				$result = __('Pending', 'vikrestaurants');
				break;

			case 'VRRESERVATIONSTATUSCONFIRMED':
				$result = __('Confirmed', 'vikrestaurants');
				break;

			case 'CUSTOMF_NAME':
				$result = __('First Name', 'vikrestaurants');
				break;

			case 'CUSTOMF_LNAME':
				$result = __('Last Name', 'vikrestaurants');
				break;

			case 'CUSTOMF_EMAIL':
				$result = __('E-mail', 'vikrestaurants');
				break;

			case 'CUSTOMF_PHONE':
				$result = __('Phone Number', 'vikrestaurants');
				break;

			case 'VRINVALIDINFORMATION':
				$result = __('Invalid Credit Card Information Received, please try again', 'vikrestaurants');
				break;

			case 'VRPAYNOTVERIFIED':
				$result = __('The payment was not verified, please try again', 'vikrestaurants');
				break;

			case 'VRPAYMENTRECEIVED':
				$result = __('Thank you! The payment was verified successfully', 'vikrestaurants');
				break;

			case 'VROFFCCPAYMENTRECEIVED':
				$result = __('Thank you! Credit Card Information Successfully Received.', 'vikrestaurants');
				break;

			case 'VRPAYMENTNOTE':
				$result = __('Payment Note', 'vikrestaurants');
				break;

			case 'VRJQCALDONE':
				$result = __('Done', 'vikrestaurants');
				break;

			case 'VRJQCALPREV':
				$result = __('Prev', 'vikrestaurants');
				break;

			case 'VRJQCALNEXT':
				$result = __('Next', 'vikrestaurants');
				break;

			case 'VRJQCALTODAY':
				$result = __('Today', 'vikrestaurants');
				break;

			case 'VRJQCALSUN':
				$result = __('Sunday');
				break;

			case 'VRJQCALMON':
				$result = __('Monday');
				break;

			case 'VRJQCALTUE':
				$result = __('Tuesday');
				break;

			case 'VRJQCALWED':
				$result = __('Wednesday');
				break;

			case 'VRJQCALTHU':
				$result = __('Thursday');
				break;

			case 'VRJQCALFRI':
				$result = __('Friday');
				break;

			case 'VRJQCALSAT':
				$result = __('Saturday');
				break;

			case 'VRJQCALWKHEADER':
				// TRANSLATORS: abbreviation for week
				$result = _x('Wk', 'abbreviation for week', 'vikrestaurants');
				break;

			case 'VRMONTHONE':
				$result = __('January');
				break;

			case 'VRMONTHTWO':
				$result = __('February');
				break;

			case 'VRMONTHTHREE':
				$result = __('March');
				break;

			case 'VRMONTHFOUR':
				$result = __('April');
				break;

			case 'VRMONTHFIVE':
				$result = __('May');
				break;

			case 'VRMONTHSIX':
				$result = __('June');
				break;

			case 'VRMONTHSEVEN':
				$result = __('July');
				break;

			case 'VRMONTHEIGHT':
				$result = __('August');
				break;

			case 'VRMONTHNINE':
				$result = __('September');
				break;

			case 'VRMONTHTEN':
				$result = __('October');
				break;

			case 'VRMONTHELEVEN':
				$result = __('November');
				break;

			case 'VRMONTHTWELVE':
				$result = __('December');
				break;

			case 'VRJQFIRSTDAY':
				$result = (int) get_option('start_of_week', 0);
				break;

			case 'VRJQISRTL':
				// @TRANSLATORS: DON'T TRANSLATE THIS VALUE! Unless you want to set it true (= use RTL).
				$result = _x('false', 'DON\'T TRANSLATE THIS VALUE! Unless you want to set it true (= use RTL).', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.2
			 */

			case 'VRORDERRESERVATIONERROR':
				$result = __('Error, reservation not found.', 'vikrestaurants');
				break;

			case 'VRMENUSEARCHNOAVERR':
				$result = __('No Menus found for the selected date & time.', 'vikrestaurants');
				break;

			case 'VRORDERTYPE':
				$result = __('Order Type', 'vikrestaurants');
				break;

			case 'VRORDERRESTAURANT':
				$result = __('Restaurant', 'vikrestaurants');
				break;

			case 'VRORDERTAKEAWAY':
				$result = __('Take-Away', 'vikrestaurants');
				break;

			case 'VRTKORDERTOTALTOPAY':
				$result = __('Total To Pay', 'vikrestaurants');
				break;

			case 'VRTKORDERDELIVERYSERVICE':
				$result = __('Service', 'vikrestaurants');
				break;

			case 'VRTKORDERDELIVERYOPTION':
				$result = __('Delivery', 'vikrestaurants');
				break;

			case 'VRTKORDERPICKUPOPTION':
				$result = __('Pickup', 'vikrestaurants');
				break;

			case 'CUSTOMF_TKINFO':
				$result = __('Your Information', 'vikrestaurants');
				break;

			case 'CUSTOMF_TKNAME':
				$result = __('Full Name', 'vikrestaurants');
				break;

			case 'CUSTOMF_TKEMAIL':
				$result = __('E-mail', 'vikrestaurants');
				break;

			case 'CUSTOMF_TKPHONE':
				$result = __('Phone Number', 'vikrestaurants');
				break;

			case 'CUSTOMF_TKDELIVERY':
				$result = __('Delivery Information', 'vikrestaurants');
				break;

			case 'CUSTOMF_TKADDRESS':
				$result = __('Address', 'vikrestaurants');
				break;

			case 'CUSTOMF_TKZIP':
				$result = __('Zip Code', 'vikrestaurants');
				break;

			case 'CUSTOMF_TKNOTE':
				$result = __('Delivery Notes', 'vikrestaurants');
				break;

			case 'VRTRANSACTIONNAME':
				$result = __('Order at %s', 'vikrestaurants');
				break;

			case 'VRRESTRANSACTIONNAME':
				$result = __('Reservation at %s', 'vikrestaurants');
				break;

			case 'VRTKORDERDATETIME':
				$result = __('Delivery Time', 'vikrestaurants');
				break;

			case 'VRTKITEMSLIST':
				$result = __('Items Ordered', 'vikrestaurants');
				break;

			case 'VRTKITEMSTOTORDER':
				$result = __('Total Order', 'vikrestaurants');
				break;

			case 'VRTKCARTROWNOTFOUND':
				$result = __('Item Not Found!', 'vikrestaurants');
				break;

			case 'VRTKCARTQUANTITYSUFFIX':
				$result = __('x', 'vikrestaurants');
				break;

			case 'VRTAKEAWAYDISABLED':
				$result = __('Takeaway Disabled!', 'vikrestaurants');
				break;

			case 'VRTAKEAWAYALLMENUS':
				$result = __('All Menus', 'vikrestaurants');
				break;

			case 'VRTAKEAWAYORDERBUTTON':
				$result = __('Order Now', 'vikrestaurants');
				break;

			case 'VRTAKEAWAYMINIMUMCOST':
				$result = __('The minimum total order is %s', 'vikrestaurants');
				break;

			case 'VRTKCARTTOTALSERVICE':
				$result = __('Service:', 'vikrestaurants');
				break;

			case 'VRTKCARTTOTALDISCOUNT':
				$result = __('Discount:', 'vikrestaurants');
				break;

			case 'VRTKADDMOREITEMS':
				$result = __('Add More Items to your Order', 'vikrestaurants');
				break;

			case 'VRTKSERVICELABEL':
				$result = __('Service', 'vikrestaurants');
				break;

			case 'VRTKDELIVERYFREE':
				$result = __('Free!', 'vikrestaurants');
				break;

			case 'VRTKDATETIMELEGEND':
				$result = __('Choose the Date and Time', 'vikrestaurants');
				break;

			case 'VRTKTIMESELECTASAP':
				$result = __('AS SOON AS POSSIBLE (%s)', 'vikrestaurants');
				break;

			case 'VRTKNOTIMEAVERR':
				$result = __('Sorry, but we cannot accept takeaway reservations any longer. Please select a different date.', 'vikrestaurants');
				break;

			case 'VRTKCONFIRMORDER':
				$result = __('Confirm Order', 'vikrestaurants');
				break;

			case 'VRTKORDERREQUESTMSG1':
				$result = __('Invalid Date', 'vikrestaurants');
				break;

			case 'VRTKORDERREQUESTMSG2':
				$result = __('Invalid Time', 'vikrestaurants');
				break;

			case 'VRTKORDERREQUESTMSG3':
				$result = __('The Restaurant is closed in the selected date and time', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.4
			 */

			case 'VRSEARCHVIEWMENU':
				$result = __('View Menu', 'vikrestaurants');
				break;

			case 'VRORDERDEPOSIT':
				$result = __('Total Paid', 'vikrestaurants');
				break;

			case 'VRTAKEAWAYMOREBUTTON':
				$result = __('More', 'vikrestaurants');
				break;

			case 'VRTAKEAWAYLESSBUTTON':
				$result = __('Less', 'vikrestaurants');
				break;

			case 'VRTKZIPCODENOTVALID':
				$result = __('The selected Zip Code is not valid!', 'vikrestaurants');
				break;

			case 'VRTKCARTTOTALTAXES':
				$result = __('Taxes', 'vikrestaurants');
				break;

			case 'VRTKORDERTAXES':
				$result = __('Taxes', 'vikrestaurants');
				break;

			case 'VRTKORDERTOTALNETPRICE':
				$result = __('Total Net Price', 'vikrestaurants');
				break;

			case 'VRLOGINRADIOCHOOSE1':
				$result = __('Login', 'vikrestaurants');
				break;

			case 'VRLOGINRADIOCHOOSE2':
				$result = __('Create New Account', 'vikrestaurants');
				break;

			case 'VRLOGINTITLE':
				$result = __('Login', 'vikrestaurants');
				break;

			case 'VRLOGINUSERNAME':
				$result = __('Username:', 'vikrestaurants');
				break;

			case 'VRLOGINPASSWORD':
				$result = __('Password:', 'vikrestaurants');
				break;

			case 'VRLOGINSUBMIT':
				$result = __('Log in', 'vikrestaurants');
				break;

			case 'VRREGISTRATIONTITLE':
				$result = __('Registration', 'vikrestaurants');
				break;

			case 'VRREGNAME':
				$result = __('First Name', 'vikrestaurants');
				break;

			case 'VRREGLNAME':
				$result = __('Last Name', 'vikrestaurants');
				break;

			case 'VRREGEMAIL':
				$result = __('E-Mail', 'vikrestaurants');
				break;

			case 'VRREGUNAME':
				$result = __('Username', 'vikrestaurants');
				break;

			case 'VRREGPWD':
				$result = __('Password', 'vikrestaurants');
				break;

			case 'VRREGCONFIRMPWD':
				$result = __('Confirm Password', 'vikrestaurants');
				break;

			case 'VRREGSIGNUPBTN':
				$result = __('Register', 'vikrestaurants');
				break;

			case 'VRREGISTRATIONFAILED1':
				$result = __('The registration feature is disabled!', 'vikrestaurants');
				break;

			case 'VRREGISTRATIONFAILED2':
				$result = __('Arguments not valid! Please fill all required field.', 'vikrestaurants');
				break;

			case 'VRREGISTRATIONFAILED3':
				$result = __('Impossible to register a new User! Try to contact the administrator.', 'vikrestaurants');
				break;

			case 'VRSMSMESSAGECUSTOMER':
				$result = __('Your reservation for {checkin}, {people} people is now CONFIRMED.', 'vikrestaurants');
				break;

			case 'VRSMSMESSAGETKCUSTOMER':
				$result = __('Your take-away order for {checkin} is now CONFIRMED.', 'vikrestaurants');
				break;

			case 'VRSMSMESSAGEADMIN':
				$result = __('A new reservation for {people} people has been CONFIRMED for {checkin}.', 'vikrestaurants');
				break;

			case 'VRSMSMESSAGETKADMIN':
				$result = __('A new take-away order has been CONFIRMED for {checkin}.', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.5
			 */

			case 'VRORDERTITLE3':
				$result = __('Your Menus', 'vikrestaurants');
				break;

			case 'VRRESERVATIONSTATUSCANCELLED':
				$result = __('Cancelled', 'vikrestaurants');
				break;

			case 'VRSEARCHCHOOSEMENU':
				$result = __('Choose at least a menu for all the people in the party', 'vikrestaurants');
				break;

			case 'VRSEARCHCHOOSEMENUSTATUS':
				$result = __('%s selected menus', 'vikrestaurants');
				break;

			case 'VRSEARCHMENUSNOTVALID':
				$result = __('Error while choosing the menus! Please select a valid menu for each person.', 'vikrestaurants');
				break;

			case 'VRMENUDETAILSALLSECTIONS':
				$result = __('All Sections', 'vikrestaurants');
				break;

			case 'VRMAPSDATESEARCH':
				$result = __('Date:', 'vikrestaurants');
				break;

			case 'VRMAPSTIMESEARCH':
				$result = __('Time:', 'vikrestaurants');
				break;

			case 'VRMAPSPEOPLESEARCH':
				$result = __('People:', 'vikrestaurants');
				break;

			case 'VRMAPSSUBMITSEARCH':
				$result = __('Find', 'vikrestaurants');
				break;

			case 'VRMAPSBEFORECHOOSEROOM':
				$result = __('Choose a Room first!', 'vikrestaurants');
				break;

			case 'VRMAPTABLECHANGEDSUCCESS':
				$result = __('Table changed successfully!', 'vikrestaurants');
				break;

			case 'VRMAPTABLENOTCHANGED':
				$result = __('Unable to change table', 'vikrestaurants');
				break;

			case 'VRMAPNEWRESBUTTON':
				$result = __('Reserve', 'vikrestaurants');
				break;

			case 'VRMAPCHANGETABLEBUTTON':
				$result = __('Change Table', 'vikrestaurants');
				break;

			case 'VRMAPDETAILSBUTTON':
				$result = __('Details', 'vikrestaurants');
				break;

			case 'VRNOROOM':
				$result = __('No room found.', 'vikrestaurants');
				break;

			case 'VRMAPSNOTIMEWARNING':
				$result = __('Select a time to see the available tables', 'vikrestaurants');
				break;

			case 'VRCONFRESZIPERROR':
				$result = __('We do not offer services for areas with that ZIP code.', 'vikrestaurants');
				break;

			case 'VRCONFRESFILLERROR':
				$result = __('Please, fill in all the required (*) fields.', 'vikrestaurants');
				break;

			case 'VRLOGOUT':
				$result = __('Logout', 'vikrestaurants');
				break;

			case 'VRLOGINOPERATORHI':
				$result = __('Hi %s!', 'vikrestaurants');
				break;

			case 'VRLOGINOPERATORNOTFOUND':
				$result = __('Hi %s! You are not able to view this resource.', 'vikrestaurants');
				break;

			case 'VRLOGINUSERNOTFOUND':
				$result = __('Error! You are not able to view this resource.', 'vikrestaurants');
				break;

			case 'VRACTIONDENIED':
				$result = __('Permission Error! You are not authorized to perform this action.', 'vikrestaurants');
				break;

			case 'VRNEWQUICKRESERVATION':
				$result = __('New Quick Reservation', 'vikrestaurants');
				break;

			case 'VREDITQUICKRESERVATION':
				$result = __('Edit Quick Reservation', 'vikrestaurants');
				break;

			case 'VREDITQUICKRESERVATIONSHARED':
				$result = __('Reservations for Table: %s', 'vikrestaurants');
				break;

			case 'VRNEWQUICKRESNOTCREATED':
				$result = __('Impossible to create the reservation!', 'vikrestaurants');
				break;

			case 'VRSTATUSRESCODE':
				$result = __('Reservation Code', 'vikrestaurants');
				break;

			case 'VREDITRESSENDMAIL':
				$result = __('Send Notification E-mail', 'vikrestaurants');
				break;

			case 'VRNEWRESMAILSENT':
				$result = __('E-mail sent to the customer as well', 'vikrestaurants');
				break;

			case 'VRRESTIMELEFTSEC':
				$result = __('expires in %d sec.', 'vikrestaurants');
				break;

			case 'VRRESTIMELEFTMIN':
				$result = __('expires in %d min.', 'vikrestaurants');
				break;

			case 'VRSECSHORT':
				$result = __('sec.', 'vikrestaurants');
				break;

			case 'VRMINSHORT':
				$result = __('min.', 'vikrestaurants');
				break;

			case 'VRSAVE':
				$result = __('Save', 'vikrestaurants');
				break;

			case 'VRSAVEANDCLOSE':
				$result = __('Save & Close', 'vikrestaurants');
				break;

			case 'VRCLOSE':
				$result = __('Close', 'vikrestaurants');
				break;

			case 'VRBACK':
				$result = __('Back', 'vikrestaurants');
				break;

			case 'VRLISTRESCURRENT':
				$result = __('Current', 'vikrestaurants');
				break;

			case 'VRLISTRESUPCOMING':
				$result = __('Upcoming', 'vikrestaurants');
				break;

			case 'VRLISTRESTITLE1':
				$result = __('Time', 'vikrestaurants');
				break;

			case 'VRLISTRESTITLE2':
				$result = __('Table', 'vikrestaurants');
				break;

			case 'VRLISTRESTITLE3':
				$result = __('People', 'vikrestaurants');
				break;

			case 'VRLISTRESTITLE4':
				$result = __('Leaves in', 'vikrestaurants');
				break;

			case 'VRLISTRESTITLE5':
				$result = __('Arrive in', 'vikrestaurants');
				break;

			case 'VRLISTRESTITLE6':
				$result = __('Status', 'vikrestaurants');
				break;

			case 'VRLISTRESTITLE7':
				$result = __('Guest', 'vikrestaurants');
				break;

			case 'VRNOWBUTTON':
				$result = __('Now', 'vikrestaurants');
				break;

			case 'VRORDERCANCDISABLEDERROR':
				$result = __('Impossible to cancel your reservation. You have not the requirements to do this action.', 'vikrestaurants');
				break;

			case 'VRORDERCANCEXPIREDERROR':
				$result = __('Impossible to cancel your reservation. You can do this action at least %d day(s) before the check-in.', 'vikrestaurants');
				break;

			case 'VRCANCELORDERTITLE':
				$result = __('Cancel Reservation', 'vikrestaurants');
				break;

			case 'VRCANCELORDERMESSAGE':
				$result = __('Do you want to cancel your reservation?', 'vikrestaurants');
				break;

			case 'VRCANCELORDEROK':
				$result = __('Ok', 'vikrestaurants');
				break;

			case 'VRCANCELORDERCANC':
				$result = __('Close', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.6
			 */

			case 'VRNOMORERESTODAY':
				$result = __('We no longer accept bookings for today.', 'vikrestaurants');
				break;

			case 'VRLARGEPARTYLABEL':
				$result = __('- MORE -', 'vikrestaurants');
				break;

			case 'VRTKMENUNOTAVAILABLE':
				$result = __('This menu is not available for the selected day', 'vikrestaurants');
				break;

			case 'VRTKADDQUANTITY':
				$result = __('Quantity', 'vikrestaurants');
				break;

			case 'VRTKADDREQUEST':
				$result = __('Any special instructions? Do you have an allergy?', 'vikrestaurants');
				break;

			case 'VRTKADDREQUESTSUBT':
				$result = __('Insert here some instructions for your food (e.g. intolerances, sauce on the side...):', 'vikrestaurants');
				break;

			case 'VRTKADDOKBUTTON':
				$result = __('ADD TO CART', 'vikrestaurants');
				break;

			case 'VRTKADDCANCELBUTTON':
				$result = __('Cancel', 'vikrestaurants');
				break;

			case 'VRTKADDITEMERR1':
				$result = __('Error, please fill in all the required toppings.', 'vikrestaurants');
				break;

			case 'VRTKADDITEMERR2':
				$result = __('Error, Connection Lost! Please try again.', 'vikrestaurants');
				break;

			case 'VRTKDELIVERYSURCHARGE':
				$result = __('This area requires a %s surcharge for delivery.', 'vikrestaurants');
				break;

			case 'VRALLORDERSTITLE':
				$result = __('Hi %s!', 'vikrestaurants');
				break;

			case 'VRALLORDERSVOID':
				$result = __('You haven\'t placed yet any reservation', 'vikrestaurants');
					break;

			case 'VRALLTKORDERSVOID':
				$result = __('You haven\'t placed yet any take-away order', 'vikrestaurants');
				break;

			case 'VRALLORDERSBUTTON':
				$result = __('View All Orders', 'vikrestaurants');
				break;

			case 'VRALLORDERSRESTAURANTHEAD':
				$result = __('Restaurant Reservations', 'vikrestaurants');
				break;

			case 'VRALLORDERSTAKEAWAYHEAD':
				$result = __('Take-Away Orders', 'vikrestaurants');
				break;

			case 'VRLOGINFORGOTPWD':
				$result = __('Forgot your password?', 'vikrestaurants');
				break;

			case 'VRLOGINFORGOTUSER':
				$result = __('Forgot your username?', 'vikrestaurants');
				break;

			case 'VRCONFORDNOROWS':
				$result = __('The order was not found!', 'vikrestaurants');
				break;

			case 'VRCONFORDISCONFIRMED':
				$result = __('The order is already CONFIRMED.', 'vikrestaurants');
				break;

			case 'VRCONFORDISREMOVED':
				$result = __('The order was expired!<br/>Please change the status from the administrator.', 'vikrestaurants');
				break;

			case 'VRCONFORDCOMPLETED':
				$result = __('The order is now CONFIRMED.', 'vikrestaurants');
				break;

			case 'VRCONFIRMATIONLINK':
				$result = __('Confirmation Link', 'vikrestaurants');
				break;

			case 'VROVERSIGHTMENUITEM1':
				$result = __('Tables Map', 'vikrestaurants');
				break;

			case 'VROVERSIGHTMENUITEM2':
				$result = __('Dashboard', 'vikrestaurants');
				break;

			case 'VROVERSIGHTMENUITEM3':
				$result = __('Reservations', 'vikrestaurants');
				break;

			case 'VROVERSIGHTMENUITEM4':
				$result = __('Coupons', 'vikrestaurants');
				break;

			case 'VROPCREATECOUPON':
				$result = __('New Coupon', 'vikrestaurants');
				break;

			case 'VROPUPDATECOUPON':
				$result = __('Edit Coupon', 'vikrestaurants');
				break;

			case 'VROPRESKEYFILTER':
				$result = __('Purchaser Name', 'vikrestaurants');
				break;

			case 'VROPRESDATEFILTER':
				$result = __('Date Filter', 'vikrestaurants');
				break;

			case 'VRTODAY':
				$result = __('Today', 'vikrestaurants');
				break;

			case 'VRDATEPAST':
				$result = __('The date is in the past', 'vikrestaurants');
				break;

			case 'VRFREE':
				$result = __('Free', 'vikrestaurants');
				break;

			case 'VRNEW':
				$result = __('New', 'vikrestaurants');
				break;

			case 'VROPLOGRESTAURANTINSERT':
				$result = __('The operator created a restaurant reservation.', 'vikrestaurants');
				break;

			case 'VROPLOGRESTAURANTUPDATE':
				$result = __('The operator updated a restaurant reservation.', 'vikrestaurants');
				break;

			case 'VROPLOGRESTAURANTCONFIRMED':
				$result = __('The operator confirmed a restaurant reservation.', 'vikrestaurants');
				break;

			case 'VROPLOGRESTAURANTTABLECHANGED':
				$result = __('The operator changed table from a restaurant reservation.', 'vikrestaurants');
				break;

			case 'VROPLOGTAKEAWAYINSERT':
				$result = __('The operator created a take-away order.', 'vikrestaurants');
				break;

			case 'VROPLOGTAKEAWAYUPDATE':
				$result = __('The operator updated a take-away order.', 'vikrestaurants');
				break;

			case 'VROPLOGTAKEAWAYCONFIRMED':
				$result = __('The operator confirmed a take-away order.', 'vikrestaurants');
				break;

			case 'VRQRMOD_DATETIMESTR':
				// TRANSLATORS: e.g. [DATE] @ [TIME] for [N] people
				$result = _x('%s @ %s for %d people', 'e.g. [DATE] @ [TIME] for [N] people', 'vikrestaurants');
				break;

			case 'VRQRMOD_ROOMSELSTR':
				$result = __('Selected Room: %s', 'vikrestaurants');
				break;

			case 'VRQRMOD_SPAMATTEMPT':
				$result = __('You have already placed a reservation. Please try again in %d minutes.', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.7
			 */

			case 'VRSTEPONETITLE':
				// @TRANSLATORS: indicates the progressive step of the booking process
				$result = _x('1', 'indicates the progressive step of the booking process', 'vikrestaurants');
				break;

			case 'VRSTEPTWOTITLE':
				// @TRANSLATORS: indicates the progressive step of the booking process
				$result = _x('2', 'indicates the progressive step of the booking process', 'vikrestaurants');
				break;

			case 'VRSTEPTHREETITLE':
				// @TRANSLATORS: indicates the progressive step of the booking process
				$result = _x('3', 'indicates the progressive step of the booking process', 'vikrestaurants');
				break;

			case 'VROVERVIEW':
				$result = __('Overview', 'vikrestaurants');
				break;

			case 'VRCLOSED':
				$result = __('Closed', 'vikrestaurants');
				break;

			case 'VRTKSTOCKNOITEMS':
				$result = __('We have no more %s available! Please, select a different item.', 'vikrestaurants');
				break;

			case 'VRTKSTOCKREMOVEDITEMS':
				// @TRANSLATORS: %s wildcard will be replaced by the product name.
				$result = _x('There are not enough %s available!<br />%d products haven\'t been added in your cart. Please, select a different item.', '%s wildcard will be replaced by the product name.', 'vikrestaurants');
				break;

			case 'VRTKMAXSIZECARTERR':
				$result = __('Impossible to insert the selected quantity! You cannot have more than %d products in your cart.', 'vikrestaurants');
				break;

			case 'VRTKADMINLOWSTOCKSUBJECT':
				// @TRANSLATORS: %s wildcard will be replaced by the restaurant name.
				$result = _x('%s - Low Stocks Products', '%s wildcard will be replaced by the restaurant name.', 'vikrestaurants');
				break;

			case 'VRTKADMINLOWSTOCKCONTENT':
				$result = __('The system has found low stocks of some products.', 'vikrestaurants');
				break;

			case 'VRTKADMINLOWSTOCKREMAINING':
				$result = __('%d remaining', 'vikrestaurants');
				break;

			case 'VRTKDELIVERYLOCNOTFOUND':
				$result = __('The specified address is not available for the delivery.', 'vikrestaurants');
				break;

			case 'VRTKSERVICENOTALLOWEDERR':
				$result = __('The selected service is not allowed!', 'vikrestaurants');
				break;

			case 'VRTKDELIVERYADDRPLACEHOLDER':
				$result = __('choose your address', 'vikrestaurants');
				break;

			case 'VRTKDELIVERYMINCOST':
				$result = __('This area requires an order total net equals or higher than %s', 'vikrestaurants');
				break;

			case 'VRTKDELIVERYADDRNOTFULL':
				$result = __('Please, specify also the route and the street number of your address.', 'vikrestaurants');
				break;

			case 'VRTKDELIVERYLABEL':
				// @TRANSLATORS: %s wildcard will be replaced by the delivery cost or "(FREE)" label.
				$result = _x('Delivery %s', '%s wildcard will be replaced by the delivery cost or "(FREE)" label.', 'vikrestaurants');
				break;

			case 'VRTKPICKUPLABEL':
				// @TRANSLATORS: %s wildcard will be replaced by the pickup charge/discount, if any.
				$result = _x('Pickup %s', '%s wildcard will be replaced by the pickup charge/discount, if any.', 'vikrestaurants');
				break;

			case 'VRRESTAURANTDISABLED':
				$result = __('Restaurant Disabled!', 'vikrestaurants');
				break;

			case 'VRREGFULLNAME':
				$result = __('Full Name', 'vikrestaurants');
				break;

			case 'VRUSERDETAILS':
				$result = __('User Details', 'vikrestaurants');
				break;

			case 'VRTKCARTTOTALPRICE':
				$result = __('Grand Total', 'vikrestaurants');
				break;

			case 'VRTKCARTTOTALNET':
				$result = __('Net', 'vikrestaurants');
				break;

			case 'VRTKCARTTOTALPAYCHARGE':
				$result = __('Payment Charge', 'vikrestaurants');
				break;

			case 'VRCUSTMAILORDDETAILS':
				$result = __('Order Details', 'vikrestaurants');
				break;

			case 'VRCUSTMAILPAYDETAILS':
				$result = __('Payment Details', 'vikrestaurants');
				break;

			case 'VRTKONLYTIMELEGEND':
				$result = __('Choose the Time', 'vikrestaurants');
				break;

			case 'VRTKMENUNOTAVAILABLE2':
				$result = __('It is not possible to order when the restaurant is closed', 'vikrestaurants');
				break;

			case 'VRTKMENUNOTAVAILABLE3':
				$result = __('We no longer accept orders for today', 'vikrestaurants');
				break;

			case 'VRTKCHOOSEVAR':
				$result = __('Choose your variation', 'vikrestaurants');
				break;

			case 'VRTKPLEASECHOOSEOPT':
				$result = __('- please choose -', 'vikrestaurants');
				break;

			case 'VRVIEWORDER':
				$result = __('View Details', 'vikrestaurants');
				break;

			case 'VRTKADDITEMSUCC':
				$result = __('Item correctly added in your cart!', 'vikrestaurants');
				break;

			case 'VRREVIEWSTITLE':
				$result = __('Reviews', 'vikrestaurants');
				break;

			case 'VRREVIEWSNOLEFT':
				$result = __('No review for this item.', 'vikrestaurants');
				break;

			case 'VRREVIEWSCOUNT':
				$result = __('%d ratings', 'vikrestaurants');
				break;

			case 'VRREVIEWSAVG':
				$result = __('%s out of 5 stars', 'vikrestaurants');
				break;

			case 'VRREVIEWSUBHEAD':
				$result = __('Written by %s %s.', 'vikrestaurants');
				break;

			case 'VRREVIEWSUBHEAD2':
				$result = __('Rated by %s %s.', 'vikrestaurants');
				break;

			case 'VRREVIEWVERIFIED':
				$result = __('Verified purchase', 'vikrestaurants');
				break;

			case 'VRREVIEWNOCOMMENT':
				$result = __('No comment left.', 'vikrestaurants');
				break;

			case 'VRREVIEWLEAVEBUTTON':
				$result = __('Leave Review', 'vikrestaurants');
				break;

			case 'VRREVIEWSUBMITBUTTON':
				$result = __('Submit Review', 'vikrestaurants');
				break;

			case 'VRREVIEWSEEALLBUTTON':
				$result = __('See all (%d) reviews', 'vikrestaurants');
				break;

			case 'VRREVIEWSSORTBY':
				$result = __('Sort by:', 'vikrestaurants');
				break;

			case 'VRREVIEWSFILTERBY':
				$result = __('Filter by:', 'vikrestaurants');
				break;

			case 'VRREVIEWSLANGSALL':
				$result = __('All Languages', 'vikrestaurants');
				break;

			case 'VRREVIEWSFIELDUSERNAME':
				$result = __('Your Name', 'vikrestaurants');
				break;

			case 'VRREVIEWSFIELDUSERMAIL':
				$result = __('Your E-Mail', 'vikrestaurants');
				break;

			case 'VRREVIEWSFIELDTITLE':
				$result = __('Title', 'vikrestaurants');
				break;

			case 'VRREVIEWSFIELDRATING':
				$result = __('Rating', 'vikrestaurants');
				break;

			case 'VRREVIEWSFIELDCOMMENT':
				$result = __('Comment', 'vikrestaurants');
				break;

			case 'VRREVIEWSCHARSLEFT':
				$result = __('Characters Left:', 'vikrestaurants');
				break;

			case 'VRREVIEWSMINCHARS':
				$result = __('Minimum Characters:', 'vikrestaurants');
				break;

			case 'VRPOSTREVIEWAUTHERR':
				$result = __('You are not able to leave a review for this item!', 'vikrestaurants');
				break;

			case 'VRPOSTREVIEWFILLERR':
				$result = __('Missing Required Fields! Please, fill in all the requird (*) fields.', 'vikrestaurants');
				break;

			case 'VRPOSTREVIEWINSERTERR':
				$result = __('An error occurred during the creation of the review. If the problem persists, please try to contact the administrator.', 'vikrestaurants');
				break;

			case 'VRPOSTREVIEWCREATEDCONF':
				$result = __('Thank you for your review!', 'vikrestaurants');
				break;

			case 'VRPOSTREVIEWCREATEDPEND':
				$result = __('Thanks, your review has been submitted for approval.', 'vikrestaurants');
				break;

			case 'VRREVIEWLEAVENOTICE1':
				$result = __('Please, login to leave a review', 'vikrestaurants');
				break;

			case 'VRREVIEWLEAVENOTICE2':
				$result = __('Please, purchase this item before to leave a review', 'vikrestaurants');
				break;

			case 'VRREVIEWSORTBY1':
				// @TRANSLATORS: referred to the sorting mode of the reviews
				$result = _x('Most recent', 'referred to the sorting mode of the reviews', 'vikrestaurants');
				break;

			case 'VRREVIEWSORTBY2':
				// @TRANSLATORS: referred to the sorting mode of the reviews
				$result = _x('Oldest', 'referred to the sorting mode of the reviews', 'vikrestaurants');
				break;

			case 'VRREVIEWSORTBY3':
				// @TRANSLATORS: referred to the sorting mode of the reviews
				$result = _x('Top rated', 'referred to the sorting mode of the reviews', 'vikrestaurants');
				break;

			case 'VRREVIEWSTAR1':
				$result = __('1 star', 'vikrestaurants');
				break;

			case 'VRREVIEWSTAR2':
				$result = __('2 stars', 'vikrestaurants');
				break;

			case 'VRREVIEWSTAR3':
				$result = __('3 stars', 'vikrestaurants');
				break;

			case 'VRREVIEWSTAR4':
				$result = __('4 stars', 'vikrestaurants');
				break;

			case 'VRREVIEWSTAR5':
				$result = __('5 stars', 'vikrestaurants');
				break;

			case 'VRREVIEWFILTERSTAR0':
				$result = __('All Stars', 'vikrestaurants');
				break;

			case 'VRREVIEWFILTERSTAR1':
				$result = __('1 star only', 'vikrestaurants');
				break;

			case 'VRREVIEWFILTERSTAR2':
				$result = __('2 stars only', 'vikrestaurants');
				break;

			case 'VRREVIEWFILTERSTAR3':
				$result = __('3 stars only', 'vikrestaurants');
				break;

			case 'VRREVIEWFILTERSTAR4':
				$result = __('4 stars only', 'vikrestaurants');
				break;

			case 'VRREVIEWFILTERSTAR5':
				$result = __('5 stars only', 'vikrestaurants');
				break;

			case 'VRREVIEWSTARDESC0':
				$result = __('click on a star to rate', 'vikrestaurants');
				break;

			case 'VRREVIEWSTARDESC1':
				// @TRANSLATORS: title for 1 star review
				$result = _x('I hate it', 'title for 1 star review', 'vikrestaurants');
				break;

			case 'VRREVIEWSTARDESC2':
				// @TRANSLATORS: title for 2 star review
				$result = _x('I don\'t like it', 'title for 2 star review', 'vikrestaurants');
				break;

			case 'VRREVIEWSTARDESC3':
				// @TRANSLATORS: title for 3 star review
				$result = _x('It\'s ok', 'title for 3 star review', 'vikrestaurants');
					break;

			case 'VRREVIEWSTARDESC4':
				// @TRANSLATORS: title for 4 star review
				$result = _x('I like it', 'title for 4 star review', 'vikrestaurants');
				break;

			case 'VRREVIEWSTARDESC5':
				// @TRANSLATORS: title for 5 star review
				$result = _x('I love it', 'title for 5 star review', 'vikrestaurants');
				break;

			case 'VRCONFREVIEWNOROWS':
				$result = __('The review was not found!', 'vikrestaurants');
				break;

			case 'VRCONFREVIEWISCONFIRMED':
				$result = __('The review is already APPROVED.', 'vikrestaurants');
				break;

			case 'VRCONFREVIEWCOMPLETED':
				$result = __('The review is now CONFIRMED.', 'vikrestaurants');
				break;

			case 'VRTRACKORDERNOSTATUS':
				$result = __('No status has been assigned yet to your order.', 'vikrestaurants');
				break;

			case 'VRTRACKORDERCHECKLINK':
				$result = __('Click <a href="%s">HERE</a> to check the status of your order.', 'vikrestaurants');
				break;

			case 'VRCANCREASONPLACEHOLDER1':
				$result = __('Leave this field empty if you don\'t have a reason to specify.', 'vikrestaurants');
				break;

			case 'VRCANCREASONPLACEHOLDER2':
				$result = __('The cancellation reason is mandatory (min 32 characters).', 'vikrestaurants');
				break;

			case 'VRCANCREASONERR':
				$result = __('Please, write at least 32 characters.', 'vikrestaurants');
				break;

			case 'VRCANCCUSTOMERSAID':
				$result = __('The customer said &#171; %s &#187;', 'vikrestaurants');
				break;

			case 'VREXPORTSUMMARY':
				$result = __('Reservation for %d people', 'vikrestaurants');
				break;

			case 'VRTKEXPORTSUMMARY':
				$result = __('Take-Away Order for %s', 'vikrestaurants');
				break;

			case 'VRORDERPERSON':
				$result = __('Person', 'vikrestaurants');
				break;

			case 'VRSUBMIT':
				$result = __('Submit', 'vikrestaurants');
				break;

			case 'VRMENUTAKEAWAYRESERVATIONS':
				$result = __('Orders', 'vikrestaurants');
				break;

			case 'VRORDERBOOKED':
				$result = __('Booked', 'vikrestaurants');
				break;

			case 'VRORDERCHECKIN':
				$result = __('Check-in', 'vikrestaurants');
				break;

			case 'VRORDERCUSTOMER':
				$result = __('Customer', 'vikrestaurants');
				break;

			case 'VRORDERCODE':
				$result = __('Code', 'vikrestaurants');
				break;

			case 'VRTKRESITEMSINCART':
				$result = __('%d items out of %d require a preparation.', 'vikrestaurants');
				break;

			case 'VROVERSIGHTMENUITEM5':
				$result = __('Take-Away', 'vikrestaurants');
				break;

			case 'VREDITBILL':
				$result = __('Edit Bill', 'vikrestaurants');
				break;

			case 'VROTHER':
				$result = __('Other', 'vikrestaurants');
				break;

			case 'VRCREATENEWPROD':
				$result = __('Create New Product', 'vikrestaurants');
				break;

			case 'VRNAME':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRPRICE':
				$result = __('Price', 'vikrestaurants');
				break;

			case 'VRVARIATION':
				$result = __('Variation', 'vikrestaurants');
				break;

			case 'VRNOTES':
				$result = __('Notes', 'vikrestaurants');
				break;

			case 'VRSEARCHPRODPLACEHOLDER':
				$result = __('Type something to search products', 'vikrestaurants');
				break;

			case 'VRTOTAL':
				$result = __('Total', 'vikrestaurants');
				break;

			case 'VRCCBRAND':
				$result = __('Credit Card Brand', 'vikrestaurants');
				break;

			case 'VRCCNAME':
				$result = __('Cardholder Name', 'vikrestaurants');
				break;

			case 'VRCCNUMBER':
				$result = __('Credit Card Number', 'vikrestaurants');
				break;

			case 'VREXPIRINGDATE':
				$result = __('Valid Through', 'vikrestaurants');
				break;

			case 'VREXPIRINGDATEFMT':
				$result = __('MM / YY', 'vikrestaurants');
				break;

			case 'VRCVV':
				$result = __('CVC', 'vikrestaurants');
				break;

			case 'VROFFCCMAILSUBJECTRS':
				$result = __('Restaurant Offline CC Payment Received', 'vikrestaurants');
				break;

			case 'VROFFCCMAILSUBJECTTK':
				$result = __('Take-Away Offline CC Payment Received', 'vikrestaurants');
				break;

			case 'VROFFCCMAILCONTENT':
				$result = __('The credit card details for the reservation ID %s were partially stored in the database and you can see them from the link below.<br /><br />Remaining Card Number: %s<br /><br />Order Details link:<br />%s', 'vikrestaurants');
				break;

			case 'VRADMINEMAILSUBJECT':
				// @TRANSLATORS: %s wildcard will be replaced by the restaurant name.
				$result = _x('%s - New Restaurant Reservation', '%s wildcard will be replaced by the restaurant name.', 'vikrestaurants');
				break;

			case 'VRCUSTOMEREMAILSUBJECT':
				// @TRANSLATORS: %s wildcard will be replaced by the restaurant name.
				$result = _x('%s - Your Restaurant Reservation', '%s wildcard will be replaced by the restaurant name.', 'vikrestaurants');
				break;

			case 'VRTKADMINEMAILSUBJECT':
				// @TRANSLATORS: %s wildcard will be replaced by the restaurant name.
				$result = _x('%s - New Take-Away Order', '%s wildcard will be replaced by the restaurant name.', 'vikrestaurants');
				break;

			case 'VRCUSTOMEREMAILTKSUBJECT':
				// @TRANSLATORS: %s wildcard will be replaced by the restaurant name.
				$result = _x('%s - Your Take-Away Order', '%s wildcard will be replaced by the restaurant name.', 'vikrestaurants');
				break;

			case 'VRORDERCANCELLEDSUBJECT':
				// @TRANSLATORS: %s wildcard will be replaced by the restaurant name.
				$result = _x('%s - Reservation Cancelled', '%s wildcard will be replaced by the restaurant name.', 'vikrestaurants');
				break;

			case 'VRREVIEWSUBJECT':
				// @TRANSLATORS: %s wildcard will be replaced by the restaurant name.
				$result = _x('%s - New Review Submitted', '%s wildcard will be replaced by the restaurant name.', 'vikrestaurants');
				break;

			case 'VRREVIEWCONTENT':
				$result = __('A new review has been submitted by %s - %s.', 'vikrestaurants');
				break;

			case 'VRSMSFAILEDSUBJECT':
				// @TRANSLATORS: %s wildcard will be replaced by the restaurant name.
				$result = _x('%s - SMS Failed!', '%s wildcard will be replaced by the restaurant name.', 'vikrestaurants');
				break;

			case 'VRDFNOW':
				$result = __('Now', 'vikrestaurants');
				break;

			case 'VRDFMINSAGO':
				$result = __('%d min ago', 'vikrestaurants');
				break;

			case 'VRDFMINSAFT':
				$result = __('in %d min', 'vikrestaurants');
				break;

			case 'VRDFHOURAGO':
				$result = __('1 hour ago', 'vikrestaurants');
				break;

			case 'VRDFHOURAFT':
				$result = __('in 1 hour', 'vikrestaurants');
				break;

			case 'VRDFHOURSAGO':
				$result = __('%d hours ago', 'vikrestaurants');
				break;

			case 'VRDFHOURSAFT':
				$result = __('in %d hours', 'vikrestaurants');
				break;

			case 'VRDFDAYAGO':
				$result = __('1 day ago', 'vikrestaurants');
				break;

			case 'VRDFDAYAFT':
				$result = __('in 1 day', 'vikrestaurants');
				break;

			case 'VRDFDAYSAGO':
				$result = __('%d days ago', 'vikrestaurants');
				break;

			case 'VRDFDAYSAFT':
				$result = __('in %d days', 'vikrestaurants');
				break;

			case 'VRDFWEEKAGO':
				$result = __('1 week ago', 'vikrestaurants');
				break;

			case 'VRDFWEEKAFT':
				$result = __('in 1 week', 'vikrestaurants');
				break;

			case 'VRDFWEEKSAGO':
				$result = __('%d weeks ago', 'vikrestaurants');
				break;

			case 'VRDFWEEKSAFT':
				$result = __('in %d weeks', 'vikrestaurants');
				break;

			case 'VRDFWHEN':
				// @TRANSLATORS: %s wildcard will be replaced by a formatted date.
				$result = _x('on %s', '%s wildcard will be replaced by a formatted date.', 'vikrestaurants');
				break;

			case 'VRFORMATHOUR':
				$result = __('hour', 'vikrestaurants');
				break;

			case 'VRFORMATHOURS':
				$result = __('hours', 'vikrestaurants');
				break;

			case 'VRFORMATDAY':
				$result = __('day', 'vikrestaurants');
				break;

			case 'VRFORMATDAYS':
				$result = __('days', 'vikrestaurants');
				break;

			case 'VRFORMATWEEK':
				$result = __('week', 'vikrestaurants');
				break;

			case 'VRFORMATWEEKS':
				$result = __('weeks', 'vikrestaurants');
				break;

			case 'VRFORMATCOMMASEP':
				$result = __(',', 'vikrestaurants');
				break;

			case 'VRFORMATANDSEP':
				$result = __('&', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.7.4
			 */

			case 'VRINVTIP':
			case 'VRTKCARTTOTALTIP':
				// @TRANSLATORS: "tip" is intended as a gratuity (bonus) for the restaurant
				$result = _x('Tip', '"tip" is intended as a gratuity (bonus) for the restaurant', 'vikrestaurants');
				break;

			case 'VRTKDELIVERYADDRNOTFOUND':
				$result = __('Invalid address, please try again.', 'vikrestaurants');
				break;

			case 'VRE_CREATE_NEW_RESERVATION':
				$result = __('Create New Reservation', 'vikrestaurants');
				break;

			case 'VRE_CLOSE_TABLE':
				$result = __('Close Table', 'vikrestaurants');
				break;

			case 'VRE_NO_ORDER_STATUS':
				$result = __('No order status', 'vikrestaurants');
				break;

			case 'VRE_ORDER_STATUS_NOTES':
				$result = __('Insert here some notes about this order status', 'vikrestaurants');
				break;

			case 'VRE_ADD_NOTES':
				$result = __('Add Notes', 'vikrestaurants');
				break;

			case 'VRE_SAVE_NOTES':
				$result = __('Save Notes', 'vikrestaurants');
				break;

			case 'VRE_CHANGE_TABLE_TIP':
				$result = __('Click the new table to which the reservation should be assigned. Otherwise press ESC to cancel this action.', 'vikrestaurants');
				break;

			case 'VRE_TABLE_OCCUPIED_ERR':
				$result = __('The selected table is already occupied.', 'vikrestaurants');
				break;

			case 'VRRESERVATIONREQUESTMSG6':
				// @TRANSLATORS: %s wildcard will be replaced by a formatted duration, such as [30 min.] or [2 hours] or [1 day, 3 hours & 30 min.]
				$result = _x('Reservations are accepted starting from %s after the current time.', '%s wildcard will be replaced by a formatted duration, such as [30 min.] or [2 hours] or [1 day, 3 hours & 30 min.]', 'vikrestaurants');
				break;

			case 'GDPR_DISCLAIMER':
				$result = __('The specified data won\'t be stored within our system.', 'vikrestaurants');
					break;

			case 'GDPR_POLICY_AUTH_LINK':
				$result = __('I hereby authorize the use of my personal data (<a href="%s" onclick="%s">see Privacy Policy</a>)', 'vikrestaurants');
					break;

			case 'GDPR_POLICY_AUTH_NO_LINK':
				$result = __('I hereby authorize the use of my personal data', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.8
			 */

			case 'VRSAFEDISTLABEL':
				$result = __('The members of the group belong to the same family', 'vikrestaurants');
				break;

			case 'VRSAFEDISTLABEL_TIP':
				$result = __('The containment measures of COVID-19 require that a certain distance be maintained between people who are not part of the same family. You should flag this option if you constantly live with the other members of the group. If not, the system will search for larger tables so that the distance can be maintained.', 'vikrestaurants');
				break;

			case 'VRRESERVATIONREQUESTMSG7':
				$result = __('Reservations are accepted within %s since the current date.', 'vikrestaurants');
				break;

			case 'VRTKCARTDELIVERYCHARGE':
				$result = __('Delivery Charge', 'vikrestaurants');
				break;

			case 'VRE_N_PEOPLE':
				$result = __('%d people', 'vikrestaurants');
				break;

			case 'VRE_N_PEOPLE_1':
				$result = __('1 person', 'vikrestaurants');
				break;

			case 'VREORDERFOOD':
				$result = __('Order Dishes', 'vikrestaurants');
				break;

			case 'VREORDERFOOD_DISABLED_STATUS':
				$result = __('It will be possible to order the dishes only once the reservation will be confirmed.', 'vikrestaurants');
				break;

			case 'VREORDERFOOD_DISABLED_ARRIVED':
				$result = __('It will be possible to order the dishes only at the moment of the check-in.', 'vikrestaurants');
				break;

			case 'VREORDERFOOD_DISABLED_BILLCLOSED':
				$result = __('The bill has been closed. It is no more possible to order further dishes.', 'vikrestaurants');
				break;

			case 'VRORDERCANCELLEDCONTENT':
				$result = __('The customer has just cancelled this order.<br />Please, log in to see the order details.', 'vikrestaurants');
				break;

			case 'VRRESCANCELLEDCONTENT':
				$result = __('The customer has just cancelled this reservation.<br />Please, log in to see the reservation details.', 'vikrestaurants');
				break;

			case 'VRTKADMINLOWSTOCKHELP':
				$result = __('You can visit the <b>Take-Away Orders</b> page in the back-end and click the <b>Stocks Overview</b> button to refill the stocks of these products.', 'vikrestaurants');
				break;

			case 'VRTKADDEDITDISHTITLE':
				$result = __('Edit Dish', 'vikrestaurants');
				break;

			case 'VRTKADDTOTALBUTTON':
				// @TRANSLATORS: %s wildcard will be replaced by the current total amount.
				$result = _x('Total %s', '%s wildcard will be replaced by the current total amount.', 'vikrestaurants');
				break;

			case 'VRTKCARTDISHCANTEDIT':
				$result = __('The selected dish is under preparation. It is no more possible to update it or delete it. Please, try to contact an operator if you wish to apply some changes.', 'vikrestaurants');
				break;

			case 'VRTKCARTDISHTRANSMITTED':
				$result = __('The ordered dishes have been transmitted to the kitchen. It is still possible to update/delete them as long as they are not under preparation.', 'vikrestaurants');
				break;

			case 'VROFFCCWAITAPPROVE':
				$result = __('Please wait for a manual approval of your reservation.', 'vikrestaurants');
				break;

			case 'VROVERSIGHTMENUITEM6':
				$result = __('Kitchen', 'vikrestaurants');
				break;

			case 'VRE_SEF_RESERVATION':
				// @TRANSLATORS: A string to be appended within the URL when accessing the details of a reservation. It should contain only letters, numbers and hyphens.
				$result = _x('reservation', 'A string to be appended within the URL when accessing the details of a reservation. It should contain only letters, numbers and hyphens.', 'vikrestaurants');
				break;

			case 'VRE_SEF_ORDER':
				// @TRANSLATORS: A string to be appended within the URL when accessing the details of a take-away order. It should contain only letters, numbers and hyphens.
				$result = _x('order', 'A string to be appended within the URL when accessing the details of a take-away order. It should contain only letters, numbers and hyphens.', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.8.1
			 */

			case 'VREORDERFOOD_BILL_AMOUNT':
				$result = __('Bill Amount', 'vikrestaurants');
				break;

			case 'VREORDERFOOD_CLOSE_BILL':
				$result = __('Close Bill', 'vikrestaurants');
				break;

			case 'VREORDERFOOD_PAY_NOW':
				$result = __('Pay Now', 'vikrestaurants');
				break;

			case 'VRTKCARTDISHESHOWWORK':
				$result = __('When you are done with dishes ordering, don\'t forget to click the "Order Now" button to transmit the ordered dishes to the kitchen. You are able to order further dishes as long as the bill is open.<br /><br />When you don\'t want to order anything else, just hit the "Close Bill" button.', 'vikrestaurants');
				break;

			case 'VREORDERFOOD_CLOSE_BILL_PENDING':
				$result = __('It seems that some dishes have not been transmitted to the kitchen. Would you like to proceed anyway?', 'vikrestaurants');
				break;

			case 'VREORDERFOOD_CLOSE_BILL_PROCEED':
				$result = __('Would you like to close the bill and proceed with the payment?', 'vikrestaurants');
				break;

			case 'VREORDERFOOD_CLOSE_BILL_DISCLAIMER':
				$result = __('After closing the bill, it will be no more possible to order further dishes.', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_PAID_MSG':
				$result = __('Payment done! The validation may take a few minutes. Please, try to refresh the page.', 'vikrestaurants');
				break;

			case 'VRCARTTOTALBUTTON':
				$result = __('Total %s', 'vikrestaurants');
				break;

			case 'VRCARTPAYNOWTOTALBTN':
				// @TRANSLATORS: %s wildcard will be replaced by the total amount to pay.
				$result = _x('Pay Now %s', '%s wildcard will be replaced by the total amount to pay.', 'vikrestaurants');
				break;

			case 'VRTIPFORPROPERTY':
				// @TRANSLATORS: "tip" is intended as a gratuity (bonus) for the restaurant
				$result = _x('Tip for the restaurant', '"tip" is intended as a gratuity (bonus) for the restaurant', 'vikrestaurants');
				break;

			case 'VRTIPROUNDED':
				// @TRANSLATORS: "tip" is intended as a gratuity (bonus) for the restaurant
				$result = _x('Round tip', '"tip" is intended as a gratuity (bonus) for the restaurant', 'vikrestaurants');
				break;

			case 'VRE_ORDERDISHES_SMS_NOTIFICATION':
				$result = __("Hi %s, you can visit the following link to start ordering your dishes:\n%s", 'vikrestaurants');
				break;

			case 'VRE_ORDERDISHES_EMAIL_NOTIFICATION_SUBJECT':
				$result = __('Start Ordering Dishes', 'vikrestaurants');
				break;

			case 'VRE_ORDERDISHES_EMAIL_NOTIFICATION':
				$result = __("Hi %s,\n\nWelcome at %s! You received this e-mail because you seated at the table and you are waiting to order.\n\nSince we are no more using paper menus, you can visit the following link to start ordering your dishes:\n%s\n\nWhen you are done with ordering, don\'t forget to click the \"Order Now\" button to notify the kitchen. Instead, you can click the \"Close Bill\" button to inform the kitchen that you are not going to order anything else. At this point, you can also decide to pay online, without having to interact with an operator.", 'vikrestaurants');
				break;

			case 'VRE_SEF_ORDERDISHES':
				// @TRANSLATORS: A string to be appended within the URL when accessing the details of a reservation. It should contain only letters, numbers and hyphens.
				$result = _x('order-dishes', 'A string to be appended within the URL when accessing the details of a reservation. It should contain only letters, numbers and hyphens.', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.8.3
			 */

			case 'VRTKCARTDISHTRANSMITTED_SHORT':
				$result = __('The ordered dishes have been transmitted to the kitchen.', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.8.5
			 */

			case 'VRTKORDERTITLE1':
				$result = __('Your Order', 'vikrestaurants');
				break;

			case 'VRTKORDERTITLE3':
				$result = __('Your Food', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.9
			 */

			case 'VRINVOTHERTAX':
				$result = __('Other Tax', 'vikrestaurants');
				break;

			case 'VRINVPAYTAX':
				$result = __('Payment Tax', 'vikrestaurants');
				break;

			case 'VRINVPAYCHARGE':
				$result = __('Payment Charge', 'vikrestaurants');
				break;

			case 'VRCOMPLETERESHEADTITLE':
				$result = __('Complete Reservation', 'vikrestaurants');
				break;

			case 'VRCOMPLETEORDHEADTITLE':
				$result = __('Complete Order', 'vikrestaurants');
				break;

			case 'VRLOGINUSERNAME':
				$result = __('Username', 'vikrestaurants');
				break;

			case 'VRLOGINPASSWORD':
				$result = __('Password', 'vikrestaurants');
				break;

			case 'VRE_RESERVATION_APPROVE_HELP':
				$result = __('Please confirm the reservation by clicking on the apposite link received at the email address specified during the booking process.', 'vikrestaurants');
				break;

			case 'VRE_ORDER_APPROVE_HELP':
				$result = __('Please confirm the order by clicking on the apposite link received at the email address specified during the booking process.', 'vikrestaurants');
				break;

			case 'VRE_ORDER_TRACK_BREADCRUMB':
				$result = __('Tracking Status', 'vikrestaurants');
				break;

			case 'VRE_MISSING_REQ_FIELD':
				$result = __('Missing required field "%s".', 'vikrestaurants');
				break;

			case 'VRORDALREADYREJECTED':
				$result = __('The order has been already REJECTED.', 'vikrestaurants');
				break;

			case 'VRORDCANNOTREJECT':
				$result = __('It is no more possible to reject the order!<br />Please change the status from the administrator.', 'vikrestaurants');
				break;

			case 'VRREJECTORDCOMPLETED':
				$result = __('The order has been rejected successfully.', 'vikrestaurants');
				break;

			case 'VRREJECTIONLINK':
				$result = __('Rejection Link', 'vikrestaurants');
				break;

			case 'VROPRESKEYFILTER':
				$result = __('Search by customer/order', 'vikrestaurants');
				break;

			case 'VRE_QR_RES_PINCODE':
				$result = __('PIN Code', 'vikrestaurants');
				break;

			case 'VRE_QR_RES_PINCODE_DESC':
				$result = __('You\'ll be asked to enter this PIN code after scanning the QR code at the restaurant, needed to start the ordering process at the table.', 'vikrestaurants');
					break;

			case 'VRE_QR_RES_PIN_LABEL':
				$result = __('Enter your PIN here', 'vikrestaurants');
				break;

			case 'VRE_QR_RES_PIN_FIND_HELP':
				$result = __('You can find the PIN code within the notification e-mail received after the confirmation of your reservation. Please contact the staff of the restaurant if you are not able to recover your PIN code.', 'vikrestaurants');
				break;

			case 'VRE_QR_RES_NOT_FOUND':
				$result = __('No reservations found for the selected table. Please contact the staff of the restaurant.', 'vikrestaurants');
				break;

			case 'VRE_QR_RES_MULTI_ERR':
				$result = __('It looks like there are multiple reservations assigned to this table at the current time. Please contact the staff of the restaurant.', 'vikrestaurants');
				break;

			case 'VRE_QR_RES_PIN_WRONG_PIN':
				$result = __('The entered PIN is incorrect. Please, try again.', 'vikrestaurants');
				break;

			case 'VRE_QR_RES_PIN_REMAINING_N_ATTEMPTS':
				$result = __('You still have %d attempts left.', 'vikrestaurants');
				break;

			case 'VRE_QR_RES_PIN_REMAINING_N_ATTEMPTS_1':
				$result = __('You only have 1 attempt left.', 'vikrestaurants');
				break;

			case 'VRE_QR_RES_PIN_REMAINING_N_ATTEMPTS_0':
				$result = __('You have run out of attempts! Please contact the staff of the restaurant.', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.9.1
			 */

			case 'VRORDERCANCEXPIREDERROR_N_DAYS':
				$result = __('Impossible to cancel your reservation. You can do this action at least %d days before the checkin.', 'vikrestaurants');
				break;

			case 'VRORDERCANCEXPIREDERROR_N_DAYS_1':
				$result = __('Impossible to cancel your reservation. You can do this action at least 1 day before the checkin.', 'vikrestaurants');
				break;

			case 'VRORDERCANCEXPIREDERROR_N_HOURS':
				$result = __('Impossible to cancel your reservation. You can do this action at least %d hours before the checkin.', 'vikrestaurants');
				break;

			case 'VRORDERCANCEXPIREDERROR_N_HOURS_1':
				$result = __('Impossible to cancel your reservation. You can do this action at least 1 hour before the checkin.', 'vikrestaurants');
				break;

			case 'VRE_ORDERDISH_SERVING_NUMBER_LABEL_SHORT':
				$result = __('Service Order', 'vikrestaurants');
				break;

			case 'VRE_ORDERDISH_SERVING_NUMBER_LABEL':
				$result = __('When would you like to have this dish?', 'vikrestaurants');
				break;

			case 'VRE_ORDERDISH_SERVING_NUMBER_0':
				$result = __('First course', 'vikrestaurants');
				break;

			case 'VRE_ORDERDISH_SERVING_NUMBER_1':
				$result = __('Second course', 'vikrestaurants');
				break;

			case 'VRE_ORDERDISH_SERVING_NUMBER_2':
				$result = __('Third course', 'vikrestaurants');
				break;
		}

		return $result;
	}
}
