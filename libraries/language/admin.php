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
 * Switcher class to translate the VikRestaurants plugin admin languages.
 *
 * @since 	1.0
 */
class VikRestaurantsLanguageAdmin implements JLanguageHandler
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

			case 'VRMAINTITLEDEFAULT':
				$result = __('VikRestaurants - Dashboard', 'vikrestaurants');
				break;

			case 'VRMENUDASHBOARD':
				$result = __('Dashboard', 'vikrestaurants');
				break;

			case 'VRMENUTABLES':
				$result = __('Tables', 'vikrestaurants');
				break;

			case 'VRMENUROOMS':
				$result = __('Rooms', 'vikrestaurants');
				break;

			case 'VRMENUVIEWMAPS':
				$result = __('Tables Maps', 'vikrestaurants');
				break;

			case 'VRMENURESERVATIONS':
				$result = __('Reservations', 'vikrestaurants');
				break;

			case 'VRMENUSHIFTS':
				$result = __('Working Shifts', 'vikrestaurants');
				break;

			case 'VRMENUPAYMENTS':
				$result = __('Payments', 'vikrestaurants');
				break;

			case 'VRMENUCUSTOMFIELDS':
				$result = __('Custom Fields', 'vikrestaurants');
				break;

			case 'VRMENUCOUPONS':
				$result = __('Coupons', 'vikrestaurants');
				break;

			case 'VRMENUCONFIG':
				$result = __('Configuration', 'vikrestaurants');
				break;

			case 'VRMENUMENUS':
				$result = __('Menus', 'vikrestaurants');
				break;

			case 'VRMENUSPECIALDAYS':
				$result = __('Special Days', 'vikrestaurants');
				break;

			case 'VRMENUTITLEHEADER1':
				$result = __('Restaurant', 'vikrestaurants');
				break;

			case 'VRMENUTITLEHEADER2':
				$result = __('Operations', 'vikrestaurants');
				break;

			case 'VRMENUTITLEHEADER3':
				$result = __('Booking', 'vikrestaurants');
				break;

			case 'VRMENUTITLEHEADER4':
				$result = __('Global', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWDASHBOARD':
				$result = __('VikRestaurants - Dashboard', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWTABLE':
				$result = __('VikRestaurants - New Table', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITTABLE':
				$result = __('VikRestaurants - Edit Table', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWTABLES':
				$result = __('VikRestaurants - Tables', 'vikrestaurants');
				break;

			case 'VRROOMMISSINGERROR':
				$result = __('Please create at least one room first.', 'vikrestaurants');
				break;

			case 'VRMANAGETABLE1':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGETABLE2':
				$result = __('Min Capacity', 'vikrestaurants');
				break;

			case 'VRMANAGETABLE3':
				$result = __('Max Capacity', 'vikrestaurants');
				break;

			case 'VRMANAGETABLE4':
				$result = __('Room', 'vikrestaurants');
				break;

			case 'VRMANAGETABLE12':
				$result = __('Can be Shared', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWROOM':
				$result = __('VikRestaurants - New Room', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITROOM':
				$result = __('VikRestaurants - Edit Room', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWROOMS':
				$result = __('VikRestaurants - Rooms', 'vikrestaurants');
				break;

			case 'VRMANAGEROOM1':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGEROOM2':
				$result = __('Description', 'vikrestaurants');
				break;

			case 'VRMANAGEROOM3':
				$result = __('Published', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITMAP':
				$result = __('VikRestaurants - Edit Map', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWMAPS':
				$result = __('VikRestaurants - View Maps', 'vikrestaurants');
				break;

			case 'VRMAPSCHOOSEROOM':
				$result = __('- Select Room -', 'vikrestaurants');
				break;

			case 'VRMAPSDATESEARCH':
				$result = __('Date', 'vikrestaurants');
				break;

			case 'VRMAPSTIMESEARCH':
				$result = __('Time', 'vikrestaurants');
				break;

			case 'VRMAPSPEOPLESEARCH':
				$result = __('People', 'vikrestaurants');
				break;

			case 'VRMAPSSUBMITSEARCH':
				$result = __('Find', 'vikrestaurants');
				break;

			case 'VRMAPSBEFORECHOOSEROOM':
				$result = __('Choose a Room first!', 'vikrestaurants');
				break;

			case 'VRMAPPROPERTIESBUTTON':
				$result = __('Properties', 'vikrestaurants');
				break;

			case 'VRMAPACTIONSBUTTON':
				$result = __('Actions', 'vikrestaurants');
				break;

			case 'VRMAPDETAILSBUTTON':
				$result = __('Details', 'vikrestaurants');
				break;

			case 'VRMAPTABLECHANGEDSUCCESS':
				$result = __('Table changed successfully!', 'vikrestaurants');
				break;

			case 'VRMAPTABLENOTCHANGED':
				$result = __('Unable to change table!', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWRESERVATION':
				$result = __('VikRestaurants - New Reservation', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITRESERVATION':
				$result = __('VikRestaurants - Edit Reservation', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWRESERVATION':
				$result = __('VikRestaurants - Reservations', 'vikrestaurants');
				break;

			case 'VRRESERVATIONTABNOAV':
				$result = __('Table no longer available!', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATIONTITLE1':
				$result = __('Reservation Details', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATIONTITLE2':
				$result = __('Custom Fields', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATIONTITLE3':
				$result = __('Notes', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION1':
				$result = __('Order Number', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION2':
				$result = __('Order Key', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION3':
				$result = __('Check-in', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION4':
				$result = __('People', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION5':
				$result = __('Table', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION6':
				$result = __('E-Mail', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION7':
				$result = __('Order Info', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION8':
				$result = __('Coupon', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION9':
				$result = __('Deposit', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION10':
				$result = __('Bill', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION11':
				$result = __('Bill Closed', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION12':
				$result = __('Status', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION13':
				$result = __('Date', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION14':
				$result = __('Time', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION15':
				$result = __('Notify Customer', 'vikrestaurants');
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

			case 'VRMAPNEWRESBUTTON':
				$result = __('Reserve', 'vikrestaurants');
				break;

			case 'VRMAPCHANGETABLEBUTTON':
				$result = __('Change Table', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWSHIFT':
				$result = __('VikRestaurants - New Working Shift', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITSHIFT':
				$result = __('VikRestaurants - Edit Working Shift', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWSHIFTS':
				$result = __('VikRestaurants - Working Shifts', 'vikrestaurants');
				break;

			case 'VRMANAGESHIFT1':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGESHIFT2':
				$result = __('From Hour', 'vikrestaurants');
				break;

			case 'VRMANAGESHIFT3':
				$result = __('To Hour', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWMENU':
				$result = __('VikRestaurants - New Menu', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITMENU':
				$result = __('VikRestaurants - Edit Menu', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWMENUS':
				$result = __('VikRestaurants - Menus', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU1':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU2':
				$result = __('Special Days', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU3':
				$result = __('Working Shifts', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU4':
				$result = __('Days Filter', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU14':
				$result = __('Preview', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWSPECIALDAY':
				$result = __('VikRestaurants - New Special Days', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITSPECIALDAY':
				$result = __('VikRestaurants - Edit Special Days', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWSPECIALDAYS':
				$result = __('VikRestaurants - Special Days', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY1':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY2':
				$result = __('Start Date', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY3':
				$result = __('End Date', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY4':
				$result = __('Working Shifts', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY5':
				$result = __('Days Filter', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY9':
				$result = __('Menu(s)', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY10':
				$result = __('Menus', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY11':
				$result = __('List', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY12':
				$result = __('Mark on Calendar', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY13':
				$result = __('Ignore Closing Days', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY14':
				$result = __('Special Day Menus', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY15':
				$result = __('Other Menus', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWPAYMENT':
				$result = __('VikRestaurants - New Payment', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITPAYMENT':
				$result = __('VikRestaurants - Edit Payment', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWPAYMENTS':
				$result = __('VikRestaurants - Payments', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT1':
				$result = __('Payment Name', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT2':
				$result = __('File Class', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT3':
				$result = __('Published', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT4':
				$result = __('Cost', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT5':
				$result = __('Auto-Set Order Confirmed', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWCOUPON':
				$result = __('VikRestaurants - New Coupon', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITCOUPON':
				$result = __('VikRestaurants - Edit Coupon', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWCOUPONS':
				$result = __('VikRestaurants - Coupons', 'vikrestaurants');
				break;

			case 'VRCOUPONDATENOTICE':
				$result = __('Dates not added! End date is previous than start date.', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON1':
				$result = __('Code', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON2':
				$result = __('Type', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON3':
				$result = __('Percent or Total', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON4':
				$result = __('Value', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON5':
				$result = __('Date Start', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON6':
				$result = __('Date End', 'vikrestaurants');
				break;

			case 'VRCOUPONTYPEOPTION1':
				$result = __('Permanent', 'vikrestaurants');
				break;

			case 'VRCOUPONTYPEOPTION2':
				$result = __('Gift', 'vikrestaurants');
				break;

			case 'VRCOUPONPERCENTOTOPTION1':
				$result = __('%', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWCUSTOMF':
				$result = __('VikRestaurants - New Custom Field', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITCUSTOMF':
				$result = __('VikRestaurants - Edit Custom Field', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWCUSTOMFS':
				$result = __('VikRestaurants - Custom Fields', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMF1':
				$result = __('Field Name', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMF2':
				$result = __('Type', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMF3':
				$result = __('Required', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMF5':
				$result = __('Popup Link', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMF6':
				$result = __('Ordering', 'vikrestaurants');
				break;

			case 'VRCUSTOMFTYPEOPTION1':
				$result = __('Text', 'vikrestaurants');
				break;

			case 'VRCUSTOMFTYPEOPTION2':
				$result = __('TextArea', 'vikrestaurants');
				break;

			case 'VRCUSTOMFTYPEOPTION3':
				$result = __('Date', 'vikrestaurants');
				break;

			case 'VRCUSTOMFTYPEOPTION4':
				$result = __('Select', 'vikrestaurants');
				break;

			case 'VRCUSTOMFTYPEOPTION5':
				$result = __('Checkbox', 'vikrestaurants');
				break;

			case 'VRCUSTOMFTYPEOPTION6':
				$result = __('Separator', 'vikrestaurants');
				break;

			case 'VRCUSTOMFSELECTADDANSWER':
				$result = __('Add Answer', 'vikrestaurants');
				break;

			case 'VRMAINTITLECONFIG':
				$result = __('VikRestaurants - Settings', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG0':
				$result = __('Restaurant Name', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG1':
				$result = __('Admin e-Mail', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG4':
				$result = __('Company Logo', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG5':
				$result = __('Date Format', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG6':
				$result = __('Time Format', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG7':
				$result = __('Currency Symbol', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG8':
				$result = __('Currency Name', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG10':
				$result = __('Opening Time Mode', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG11':
				$result = __('Minutes Intervals', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG12':
				$result = __('Average Time of Stay', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG13':
				$result = __('Minimum People', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG14':
				$result = __('Maximum People', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG16':
				$result = __('Reservation Requirements', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG17':
				$result = __('Allow clients to', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG18':
				$result = __('Deposit per Reservation', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG19':
				$result = __('Deposit per Person', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG20':
				$result = __('Keep Tables Locked for', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG21':
				$result = __('Closing Days', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG22':
				$result = __('Add Day', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG23':
				$result = __('Display Footer', 'vikrestaurants');
				break;

			case 'VRCONFIGDATEFORMAT1':
				$result = __('Y/m/d', 'vikrestaurants');
				break;

			case 'VRCONFIGDATEFORMAT2':
				$result = __('m/d/Y', 'vikrestaurants');
				break;

			case 'VRCONFIGDATEFORMAT3':
				$result = __('d/m/Y', 'vikrestaurants');
				break;

			case 'VRCONFIGTIMEFORMAT1':
				$result = __('12 Hours AM/PM', 'vikrestaurants');
				break;

			case 'VRCONFIGTIMEFORMAT2':
				$result = __('24 Hours', 'vikrestaurants');
				break;

			case 'VRCONFIGOPENTIME1':
				$result = __('Continuous', 'vikrestaurants');
				break;

			case 'VRCONFIGOPENTIME2':
				$result = __('Shifted', 'vikrestaurants');
				break;

			case 'VRCONFIGRESREQ0':
				$result = __('Choose Table (& Room)', 'vikrestaurants');
				break;

			case 'VRCONFIGRESREQ1':
				$result = __('Choose Only Room', 'vikrestaurants');
				break;

			case 'VRCONFIGRESREQ2':
				$result = __('Choose Nothing', 'vikrestaurants');
				break;

			case 'VRFREQUENCYTYPE0':
				$result = __('Single Day', 'vikrestaurants');
				break;

			case 'VRFREQUENCYTYPE1':
				$result = __('Weekly', 'vikrestaurants');
				break;

			case 'VRFREQUENCYTYPE2':
				$result = __('Monthly', 'vikrestaurants');
				break;

			case 'VRFREQUENCYTYPE3':
				$result = __('Yearly', 'vikrestaurants');
				break;

			case 'VRCONFIGUPLOADERROR':
				$result = __('Error while uploading image', 'vikrestaurants');
				break;

			case 'VRCONFIGFILETYPEERROR':
				$result = __('The selected file is not an image', 'vikrestaurants');
				break;

			case 'VRNEW':
				$result = __('New', 'vikrestaurants');
				break;

			case 'VRCANCEL':
				$result = __('Cancel', 'vikrestaurants');
				break;

			case 'VREDIT':
				$result = __('Edit', 'vikrestaurants');
				break;

			case 'VRSAVE':
				$result = __('Save', 'vikrestaurants');
				break;

			case 'VRDELETE':
				$result = __('Delete', 'vikrestaurants');
				break;

			case 'VRSHORTCUTMINUTE':
				$result = __('min.', 'vikrestaurants');
				break;

			case 'VRDAY1':
				$result = __('Monday', 'vikrestaurants');
				break;

			case 'VRDAY2':
				$result = __('Tuesday', 'vikrestaurants');
				break;

			case 'VRDAY3':
				$result = __('Wednesday', 'vikrestaurants');
				break;

			case 'VRDAY4':
				$result = __('Thursday', 'vikrestaurants');
				break;

			case 'VRDAY5':
				$result = __('Friday', 'vikrestaurants');
				break;

			case 'VRDAY6':
				$result = __('Saturday', 'vikrestaurants');
				break;

			case 'VRDAY7':
				$result = __('Sunday', 'vikrestaurants');
				break;

			case 'VRDAYMON':
				$result = __('Monday', 'vikrestaurants');
				break;

			case 'VRDAYTUE':
				$result = __('Tuesday', 'vikrestaurants');
				break;

			case 'VRDAYWED':
				$result = __('Wednesday', 'vikrestaurants');
				break;

			case 'VRDAYTHU':
				$result = __('Thursday', 'vikrestaurants');
				break;

			case 'VRDAYFRI':
				$result = __('Friday', 'vikrestaurants');
				break;

			case 'VRDAYSAT':
				$result = __('Saturday', 'vikrestaurants');
				break;

			case 'VRDAYSUN':
				$result = __('Sunday', 'vikrestaurants');
				break;

			case 'VRREQUIREDFIELDSERROR':
				$result = __('Error. All * fields must be compiled.', 'vikrestaurants');
				break;

			case 'VRMINGREATERTHANMAX':
				$result = __('Error. Minimum capacity is greater than maximum capacity.', 'vikrestaurants');
				break;

			case 'VRHOURNOTVALIDERROR':
				$result = __('Error. Hour(s) inserted is not valid.', 'vikrestaurants');
				break;

			case 'VRDATESNOTVALIDERROR':
				$result = __('Error. End date must be greater than start date.', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_N_ITEMS_DELETED':
				$result = __('%s items deleted.', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_N_ITEMS_DELETED_1':
				$result = __('%s item deleted.', 'vikrestaurants');
				break;

			// case 'VRCUSTOMEREMAILSUBJECT':
			// 	$result = __('Your reservation for %s', 'vikrestaurants');
			// 	break;

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

			case 'VRFOOTER':
				$result = __('VikRestaurants v.%s - Powered by', 'vikrestaurants');
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

			/**
			 * VikRestaurants 1.2
			 */

			case 'VRMENUTAKEAWAYMENUS':
				$result = __('Menus', 'vikrestaurants');
				break;

			case 'VRMENUTITLEHEADER5':
				$result = __('Take-Away', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMF7':
				$result = __('Group', 'vikrestaurants');
				break;

			case 'VRCUSTOMFGROUPOPTION1':
				$result = __('Restaurant', 'vikrestaurants');
				break;

			case 'VRCUSTOMFGROUPOPTION2':
				$result = __('Take-Away', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITBILL':
				$result = __('VikRestaurants - Edit Bill', 'vikrestaurants');
				break;

			case 'VRMANAGEBILL1':
				$result = __('ID #', 'vikrestaurants');
				break;

			case 'VRMANAGEBILL2':
				$result = __('Total', 'vikrestaurants');
				break;

			case 'VRMANAGEBILL3':
				$result = __('Closed', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWTKMENU':
				$result = __('VikRestaurants - New Take-Away Menu', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITTKMENU':
				$result = __('VikRestaurants - Edit Take-Away Menu', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWTKMENUS':
				$result = __('VikRestaurants - Take-Away Menus', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU1':
				$result = __('Title', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU2':
				$result = __('Description', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU3':
				$result = __('Item', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU4':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU5':
				$result = __('Price', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU9':
				$result = __('No Preparation', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENUADDVAR':
				$result = __('Add Variation', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITTKRES':
				$result = __('VikRestaurants - Edit Take-Away Order', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWTKRES':
				$result = __('VikRestaurants - Take-Away Orders', 'vikrestaurants');
				break;

			case 'VRMANAGETKRESTITLE1':
				$result = __('Order Details', 'vikrestaurants');
				break;

			case 'VRMANAGETKRESTITLE2':
				$result = __('Take-Away Order', 'vikrestaurants');
				break;

			case 'VRMANAGETKRESTITLE3':
				$result = __('Custom Fields', 'vikrestaurants');
				break;

			case 'VRMANAGETKRESTITLE4':
				$result = __('Notes', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES1':
				$result = __('Order Number', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES2':
				$result = __('Order Key', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES3':
				$result = __('Check-in', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES4':
				$result = __('Delivery Service', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES5':
				$result = __('E-Mail', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES6':
				$result = __('Order Info', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES7':
				$result = __('Coupon', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES8':
				$result = __('Total To Pay', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES9':
				$result = __('Status', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES10':
				$result = __('Date', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES11':
				$result = __('Time', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES12':
				$result = __('Notify Customer', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES13':
				$result = __('Service', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES14':
				$result = __('Delivery', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES15':
				$result = __('Pickup', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES16':
				$result = __('Add Item', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES17':
				$result = __('Remove Item', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES18':
				$result = __('Add to Order', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES19':
				$result = __('Remove from Order', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES20':
				$result = __('Quantity', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTITLE1':
				$result = __('Restaurant', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTITLE2':
				$result = __('Take-Away', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK0':
				$result = __('Enable Take-Away', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK1':
				$result = __('Minutes Interval', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK2':
				$result = __('Meals per Interval', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK3':
				$result = __('Delivery Service', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK4':
				$result = __('Delivery Cost', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK5':
				$result = __('Min Cost per Order', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK6':
				$result = __('Front Notes', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK7':
				$result = __('Free Delivery with', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK8':
				$result = __('Keep Order Locked for', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEXPORTRES':
				$result = __('VikRestaurants - Exporting Reservations', 'vikrestaurants');
				break;

			case 'VRMAINTITLETKEXPORTRES':
				$result = __('VikRestaurants - Exporting Take-Away Orders', 'vikrestaurants');
				break;

			case 'VREXPORTRES1':
				$result = __('Filename', 'vikrestaurants');
				break;

			case 'VREXPORTRES2':
				$result = __('Export Type', 'vikrestaurants');
				break;

			case 'VREXPORTRES3':
				$result = __('Start Date', 'vikrestaurants');
				break;

			case 'VREXPORTRES4':
				$result = __('End Date', 'vikrestaurants');
				break;

			case 'VREXPORTRES5':
				$result = __('Number of IDs', 'vikrestaurants');
				break;

			case 'VREXPORTDOWNLOADED':
				$result = __('%s downloaded successfully!', 'vikrestaurants');
				break;

			case 'VREXPORTFILENOTFOUNDERR':
				$result = __('Impossible to export reservations! File [%s] not found...', 'vikrestaurants');
				break;

			case 'VREXPORTNOFILESERR':
				$result = __('You have none exporting file.', 'vikrestaurants');
				break;

			case 'VRSAVENEW':
				$result = __('Save & New', 'vikrestaurants');
				break;

			case 'VRSAVECLOSE':
				$result = __('Save & Close', 'vikrestaurants');
				break;

			case 'VRBILL':
				$result = __('Bill', 'vikrestaurants');
				break;

			case 'VRRELOAD':
				$result = __('Reload', 'vikrestaurants');
				break;

			case 'VREXPORT':
				$result = __('Export', 'vikrestaurants');
				break;

			case 'VRDOWNLOAD':
				$result = __('Download', 'vikrestaurants');
				break;

			case 'VRRESERVATIONREQUESTMSG1':
				$result = __('Invalid Date', 'vikrestaurants');
				break;

			case 'VRRESERVATIONREQUESTMSG2':
				$result = __('Invalid Time', 'vikrestaurants');
				break;

			case 'VRRESERVATIONREQUESTMSG3':
				$result = __('The restaurant is closed in the selected date and time', 'vikrestaurants');
				break;

			case 'VRRESERVATIONREQUESTMSG4':
				$result = __('Number of selected people is invalid', 'vikrestaurants');
				break;

			case 'VRRESERVATIONREQUESTMSG5':
				$result = __('The selected date is in the past', 'vikrestaurants');
				break;

			case 'VRTKORDERREQUESTMSG1':
				$result = __('Invalid Date', 'vikrestaurants');
				break;

			case 'VRTKORDERREQUESTMSG2':
				$result = __('Invalid Time', 'vikrestaurants');
				break;

			case 'VRTKORDERREQUESTMSG3':
				$result = __('The restaurant is closed in the selected date and time', 'vikrestaurants');
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
				$result = __('ZIP Code', 'vikrestaurants');
				break;

			case 'CUSTOMF_TKNOTE':
				$result = __('Delivery Notes', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.3
			 */

			case 'VRRESERVATIONDATEFILTER':
				$result = __('Date', 'vikrestaurants');
				break;

			case 'VRRESERVATIONSHIFTFILTER':
				// @TRANSLATORS: intended as working shift
				$result = _x('Shift', 'intended as working shift', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWSTATISTICS':
				$result = __('VikRestaurants - Restaurant Statistics', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWTKSTATISTICS':
				$result = __('VikRestaurants - Take-Away Statistics', 'vikrestaurants');
				break;

			case 'VRSTATISTICSTH1':
				$result = __('Month', 'vikrestaurants');
				break;

			case 'VRSTATISTICSTH2':
				$result = __('Week Day', 'vikrestaurants');
				break;

			case 'VRSTATISTICSTH3':
				$result = __('Reservations', 'vikrestaurants');
				break;

			case 'VRSTATISTICSTH4':
				$result = __('Total Earning', 'vikrestaurants');
				break;

			case 'VRMANAGEROOM4':
				$result = __('Image', 'vikrestaurants');
				break;

			case 'VRMANAGEROOM5':
				$result = __('Remove Image', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT8':
				$result = __('Parameters', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT9':
				$result = __('No parameters available.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG24':
				$result = __('Booking Minutes Restriction', 'vikrestaurants');
				break;

			case 'VRIMAGESTATUS0':
				$result = __('Image does not exist', 'vikrestaurants');
				break;

			case 'VRIMAGESTATUS1':
				$result = __('Image ok', 'vikrestaurants');
				break;

			case 'VRIMAGESTATUS2':
				$result = __('Image not added', 'vikrestaurants');
				break;

			case 'VRSTAT':
				$result = __('Statistics', 'vikrestaurants');
				break;

			case 'VRMONTH1':
				$result = __('January');
				break;

			case 'VRMONTH2':
				$result = __('February');
				break;

			case 'VRMONTH3':
				$result = __('March');
				break;

			case 'VRMONTH4':
				$result = __('April');
				break;

			case 'VRMONTH5':
				$result = __('May');
				break;

			case 'VRMONTH6':
				$result = __('June');
				break;

			case 'VRMONTH7':
				$result = __('July');
				break;

			case 'VRMONTH8':
				$result = __('August');
				break;

			case 'VRMONTH9':
				$result = __('September');
				break;

			case 'VRMONTH10':
				$result = __('October');
				break;

			case 'VRMONTH11':
				$result = __('November');
				break;

			case 'VRMONTH12':
				$result = __('December');
				break;

			/**
			 * VikRestaurants 1.4
			 */

			case 'VRMANAGESHIFT4':
				$result = __('Group', 'vikrestaurants');
				break;

			case 'VRSHIFTGROUPOPT1':
				$result = __('Restaurant', 'vikrestaurants');
				break;

			case 'VRSHIFTGROUPOPT2':
				$result = __('Take-Away', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION16':
				$result = __('Phone Number', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY16':
				$result = __('Group', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES21':
				$result = __('Taxes', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES22':
				$result = __('Items', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES23':
				$result = __('Phone Number', 'vikrestaurants');
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

			case 'VRCUSTOMEREMAILTKSUBJECT':
				$result = __('Your Take-Away Order', 'vikrestaurants');
				break;

			case 'VRTKCARTROWNOTFOUND':
				$result = __('Item not found!', 'vikrestaurants');
				break;

			case 'VRTKCARTQUANTITYSUFFIX':
				$result = __('x', 'vikrestaurants');
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

			case 'VRMANAGECONFIGTITLE3':
				$result = __('SMS APIs Settings', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG25':
				$result = __('Symbol Position', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG29':
				$result = __('New ZIP Code', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG32':
				$result = __('- None -', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG33':
				$result = __('Login Requirements', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG34':
				$result = __('Enable User Registration', 'vikrestaurants');
				break;

			case 'VRCONFIGSYMBPOSITION1':
				$result = __('After Price', 'vikrestaurants');
				break;

			case 'VRCONFIGSYMBPOSITION2':
				$result = __('Before Price', 'vikrestaurants');
				break;

			case 'VRCONFIGLOGINREQ1':
				$result = __('Never', 'vikrestaurants');
				break;

			case 'VRCONFIGLOGINREQ2':
				$result = __('Possible', 'vikrestaurants');
				break;

			case 'VRCONFIGLOGINREQ3':
				$result = __('Required', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS1':
				$result = __('SMS API file', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS2':
				$result = __('Send SMS for', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS3':
				$result = __('Send SMS to', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS4':
				$result = __('Phone Number', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS5':
				$result = __('Parameters', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS6':
				$result = __('- None Available -', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS7':
				$result = __('User Credit', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS8':
				$result = __('Estimate Credit', 'vikrestaurants');
				break;

			case 'VRCONFIGSMSAPIWHEN0':
				$result = __('Restaurant', 'vikrestaurants');
				break;

			case 'VRCONFIGSMSAPIWHEN1':
				$result = __('Take-Away', 'vikrestaurants');
				break;

			case 'VRCONFIGSMSAPIWHEN2':
				$result = __('Restaurant & Take-Away', 'vikrestaurants');
				break;

			case 'VRCONFIGSMSAPIWHEN3':
				$result = __('Only Manual', 'vikrestaurants');
				break;

			case 'VRCONFIGSMSAPITO0':
				$result = __('Customer', 'vikrestaurants');
				break;

			case 'VRCONFIGSMSAPITO1':
				$result = __('Administrator', 'vikrestaurants');
				break;

			case 'VRCONFIGSMSAPITO2':
				$result = __('Customer & Administrator', 'vikrestaurants');
				break;

			case 'VRSMSESTIMATEERR1':
				$result = __('SMS APIs file does not exists!', 'vikrestaurants');
				break;

			case 'VRSMSESTIMATEERR2':
				$result = __('SMS APIs file can estimate the user credit!', 'vikrestaurants');
				break;

			case 'VRSMSESTIMATEERR3':
				$result = __('An error occurred! Impossible to estimate the user credit.', 'vikrestaurants');
				break;

			case 'VRSMSMESSAGECUSTOMER':
				$result = __('Your reservation for {checkin}, {people} people is now CONFIRMED.', 'vikrestaurants');
				break;

			case 'VRSMSMESSAGETKCUSTOMER':
				$result = __('Your take-away order for {checkin} is now CONFIRMED.', 'vikrestaurants');
				break;

			case 'VRPRINT':
				$result = __('Print', 'vikrestaurants');
				break;

			case 'VRSENDSMS':
				$result = __('Send SMS', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.5
			 */

			case 'VRMENUMENUSPRODUCTS':
				$result = __('Products', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWMENUSPRODUCT':
				$result = __('VikRestaurants - New Menu Product', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITMENUSPRODUCT':
				$result = __('VikRestaurants - Edit Menu Product', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWMENUSPRODUCTS':
				$result = __('VikRestaurants - Menus Products', 'vikrestaurants');
				break;

			case 'VRMANAGEMENUSPRODUCT1':
				$result = __('ID', 'vikrestaurants');
				break;

			case 'VRMANAGEMENUSPRODUCT2':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGEMENUSPRODUCT3':
				$result = __('Description', 'vikrestaurants');
				break;

			case 'VRMANAGEMENUSPRODUCT4':
				$result = __('Price', 'vikrestaurants');
				break;

			case 'VRMANAGEMENUSPRODUCT5':
				$result = __('Image', 'vikrestaurants');
				break;

			case 'VRMANAGEMENUSPRODUCT6':
				$result = __('Published', 'vikrestaurants');
				break;

			case 'VRMANAGEMENUSPRODUCT7':
				$result = __('Ordering', 'vikrestaurants');
				break;

			case 'VRMENUOPERATORS':
				$result = __('Operators', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWOPERATOR':
				$result = __('VikRestaurants - New Operator', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITOPERATOR':
				$result = __('VikRestaurants - Edit Operator', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWOPERATORS':
				$result = __('VikRestaurants - Operators', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR1':
				$result = __('Code', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR2':
				$result = __('First Name', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR3':
				$result = __('Last Name', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR4':
				$result = __('Phone Number', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR5':
				$result = __('E-mail', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR6':
				$result = __('Enable Login', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR7':
				$result = __('Login User', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR8':
				$result = __('Full Name', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR9':
				$result = __('- Create New -', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR10':
				$result = __('User Group', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR11':
				$result = __('Username', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR12':
				$result = __('Password', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR13':
				$result = __('Confirm Password', 'vikrestaurants');
				break;

			case 'VRMENURESCODES':
				$result = __('Res Codes', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWRESCODE':
				$result = __('VikRestaurants - New Reservation Code', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITRESCODE':
				$result = __('VikRestaurants - Edit Reservation Code', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWRESCODES':
				$result = __('VikRestaurants - Reservation Codes', 'vikrestaurants');
				break;

			case 'VRMANAGERESCODE1':
				$result = __('ID', 'vikrestaurants');
				break;

			case 'VRMANAGERESCODE2':
				$result = __('Code', 'vikrestaurants');
				break;

			case 'VRMANAGERESCODE3':
				$result = __('Icon', 'vikrestaurants');
				break;

			case 'VRMANAGERESCODE4':
				$result = __('Group', 'vikrestaurants');
				break;

			case 'VRMANAGEROOM6':
				$result = __('Closed', 'vikrestaurants');
				break;

			case 'VRMANAGEROOM7':
				$result = __('From', 'vikrestaurants');
				break;

			case 'VRMANAGEROOM8':
				$result = __('To', 'vikrestaurants');
				break;

			case 'VRMANAGEROOM9':
				$result = __('Never', 'vikrestaurants');
				break;

			case 'VRMANAGEROOM10':
				$result = __('Status', 'vikrestaurants');
				break;

			case 'VRROOMSTATUSACTIVE':
				$result = __('Active', 'vikrestaurants');
				break;

			case 'VRROOMSTATUSCLOSED':
				$result = __('Closed', 'vikrestaurants');
				break;

			case 'VRMAPGPRESTOREBUTTON':
				$result = __('Restore', 'vikrestaurants');
				break;

			case 'VRMANAGESHIFT5':
				$result = __('Show Label', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU17':
				$result = __('Description', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU18':
				$result = __('Image', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU19':
				$result = __('Ordering', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU20':
				$result = __('Sections', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU21':
				$result = __('Products', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU22':
				$result = __('Add Section', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU23':
				$result = __('Add Products', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU24':
				$result = __('All shifts', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU25':
				$result = __('All weekly days', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU26':
				$result = __('Published', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU27':
				$result = __('Section Name', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU28':
				$result = __('Select the food item you want to add', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU31':
				$result = __('Choosable', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY17':
				$result = __('Images', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY18':
				$result = __('Add Image', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY19':
				$result = __('Choosable Menus', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION17':
				$result = __('Customer', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION18':
				$result = __('Customer Name', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION19':
				$result = __('Code', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION20':
				$result = __('Payment', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU12':
				$result = __('Published', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU13':
				$result = __('Ordering', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES24':
				$result = __('Customer', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES25':
				$result = __('Customer Name', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES26':
				$result = __('Code', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES27':
				$result = __('Payment', 'vikrestaurants');
				break;

			case 'VRECONFIGTABNAME1':
				$result = __('Global', 'vikrestaurants');
				break;

			case 'VRECONFIGTABNAME2':
				$result = __('Restaurant', 'vikrestaurants');
				break;

			case 'VRECONFIGTABNAME3':
				$result = __('Take-Away', 'vikrestaurants');
				break;

			case 'VRECONFIGTABNAME4':
				$result = __('SMS APIs', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTITLE0':
				$result = __('Global', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG35':
				$result = __('Default Status', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG36':
				$result = __('Display on Dashboard', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG37':
				$result = __('Dashboard Refresh Time', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG38':
				$result = __('Columns in Reservations List', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG39':
				$result = __('Choosable Menus', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG40':
				$result = __('Enable Cancellation', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG41':
				$result = __('Accept Cancellation Before', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK12':
				$result = __('Default Status', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK13':
				$result = __('Columns in Orders List', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK14':
				$result = __('Enable Cancellation', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK15':
				$result = __('Accept Cancellation Before', 'vikrestaurants');
				break;

			case 'VRSAVEANDCLOSE':
				$result = __('Save & Close', 'vikrestaurants');
				break;

			case 'VRSAVEANDNEW':
				$result = __('Save & New', 'vikrestaurants');
				break;

			case 'VRPUBLISH':
				$result = __('Publish', 'vikrestaurants');
				break;

			case 'VRUNPUBLISH':
				$result = __('Unpublish', 'vikrestaurants');
				break;

			case 'VRRESERVATIONSTATUSCANCELLED':
				$result = __('Cancelled', 'vikrestaurants');
				break;

			case 'VRCANCELORDERTITLE':
				$result = __('Cancel Reservation', 'vikrestaurants');
				break;

			case 'VRMAPAPPLYALLBUTTON':
				$result = __('Apply to All', 'vikrestaurants');
				break;

			case 'VRDAYS':
				$result = __('day(s)', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.6
			 */

			case 'VRMANAGECLOSURES':
				$result = __('Manage Closures', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWROOMCLOSURES':
				$result = __('VikRestaurants - Rooms Closures', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWROOMCLOSURE':
				$result = __('VikRestaurants - New Room Closure', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITROOMCLOSURE':
				$result = __('VikRestaurants - Edit Room Closure', 'vikrestaurants');
				break;

			case 'VRMANAGEROOMCLOSURE1':
				$result = __('Room', 'vikrestaurants');
				break;

			case 'VRMANAGEROOMCLOSURE2':
				$result = __('Closed From', 'vikrestaurants');
				break;

			case 'VRMANAGEROOMCLOSURE3':
				$result = __('Closed To', 'vikrestaurants');
				break;

			case 'VRMANAGEROOMCLOSURE4':
				$result = __('Duration', 'vikrestaurants');
				break;

			case 'VRMANAGEROOMCLOSURE5':
				$result = __('Status', 'vikrestaurants');
				break;

			case 'VRROOMCLOSURESTATUSCONFIRMED':
				$result = __('Active', 'vikrestaurants');
				break;

			case 'VRROOMCLOSURESTATUSPENDING':
				$result = __('Not Active', 'vikrestaurants');
				break;

			case 'VRROOMCLOSURESTATUSREMOVED':
				$result = __('Expired', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWCUSTOMER':
				$result = __('VikRestaurants - New Customer', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITCUSTOMER':
				$result = __('VikRestaurants - Edit Customer', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWCUSTOMERS':
				$result = __('VikRestaurants - Customers', 'vikrestaurants');
				break;

			case 'VRMENUCUSTOMERS':
				$result = __('Customers', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMERTITLE1':
				$result = __('User Account', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMERTITLE2':
				$result = __('Billing', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMERTITLE3':
				$result = __('Custom Fields', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMERTITLE4':
				$result = __('Notes', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER1':
				$result = __('ID', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER2':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER3':
				$result = __('E-Mail', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER4':
				$result = __('Phone', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER5':
				$result = __('Country', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER6':
				$result = __('State / Province', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER7':
				$result = __('City', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER8':
				$result = __('Address', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER9':
				$result = __('ZIP Code', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER10':
				$result = __('Company Name', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER11':
				$result = __('Vat Number', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER12':
				$result = __('User Account', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER13':
				$result = __('Password', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER14':
				$result = __('Confirm Password', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER15':
				$result = __('Guest', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER16':
				$result = __('Create new Account', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER17':
				$result = __('Generate Password', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER18':
				$result = __('Reservations', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER19':
				$result = __('Address 2', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER20':
				$result = __('SSN / Fiscal Code', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER21':
				$result = __('Orders', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMERERR1':
				$result = __('The password fields are mandatory!', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMERERR2':
				$result = __('The password specified are not equals!', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMERERR3':
				$result = __('Missing Fields! Please fill in all the required fields.', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMF10':
				$result = __('Default Country Code', 'vikrestaurants');
				break;

			case 'VRMENUTAKEAWAYTOPPINGS':
				$result = __('Toppings', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWTKTOPPINGS':
				$result = __('VikRestaurants - Take-Away Toppings', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWTKTOPPING':
				$result = __('VikRestaurants - New Take-Away Topping', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITTKTOPPING':
				$result = __('VikRestaurants - Edit Take-Away Topping', 'vikrestaurants');
				break;

			case 'VRMANAGETKTOPPING1':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGETKTOPPING2':
				$result = __('Price', 'vikrestaurants');
				break;

			case 'VRMANAGETKTOPPING3':
				$result = __('Published', 'vikrestaurants');
				break;

			case 'VRMANAGETKTOPPING4':
				$result = __('Ordering', 'vikrestaurants');
				break;

			case 'VRMANAGETKTOPPING5':
				$result = __('Separator', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWTKPRODUCTS':
				$result = __('VikRestaurants - Take-Away Menu Products', 'vikrestaurants');
				break;

			case 'VRMANAGETKPRODUCT1':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGETKPRODUCT2':
				$result = __('Price', 'vikrestaurants');
				break;

			case 'VRMANAGETKPRODUCT4':
				$result = __('Language', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWTKTOPPINGSEPS':
				$result = __('VikRestaurants - Take-Away Toppings Separators', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWTKTOPPINGSEP':
				$result = __('VikRestaurants - New Take-Away Toppings Separator', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITTKTOPPINGSEP':
				$result = __('VikRestaurants - Edit Take-Away Toppings Separator', 'vikrestaurants');
				break;

			case 'VRMANAGETKTOPPINGSEP1':
				$result = __('Title', 'vikrestaurants');
				break;

			case 'VRMANAGETKTOPPINGSEP2':
				$result = __('Ordering', 'vikrestaurants');
				break;

			case 'VRTKGOTOTOPPINGSEP':
				$result = __('Manage Separators', 'vikrestaurants');
				break;

			case 'VRTKOTHERSSEPARATOR':
				$result = __('Uncategorized', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENUATTRIBUTES':
				$result = __('Manage Menu Attributes', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWTKMENUATTRS':
				$result = __('VikRestaurants - Take-Away Menu Attributes', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWTKMENUATTR':
				$result = __('VikRestaurants - New Take-Away Menu Attribute', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITTKMENUATTR':
				$result = __('VikRestaurants - Edit Take-Away Menu Attribute', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENUATTR1':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENUATTR2':
				$result = __('Description', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENUATTR3':
				$result = __('Published', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENUATTR4':
				$result = __('Icon', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENUATTR5':
				$result = __('Ordering', 'vikrestaurants');
				break;

			case 'VRTKNOATTR':
				$result = __('- No Attribute -', 'vikrestaurants');
				break;

			case 'VRMANAGETKENTRYFIELDSET2':
				$result = __('Variations', 'vikrestaurants');
				break;

			case 'VRMANAGETKENTRYFIELDSET3':
				$result = __('Toppings', 'vikrestaurants');
				break;

			case 'VRTKENTRYADDGROUP':
				$result = __('Add Group', 'vikrestaurants');
				break;

			case 'VRTKENTRYADDTOPPINGS':
				$result = __('Add Toppings', 'vikrestaurants');
				break;

			case 'VRTKTOPPINGSPLACEHOLDER':
				$result = __('Pick a topping to insert it', 'vikrestaurants');
				break;

			case 'VRTKMANAGEENTRYGROUP1':
				$result = __('Title', 'vikrestaurants');
				break;

			case 'VRTKMANAGEENTRYGROUP2':
				$result = __('Multiple Selection', 'vikrestaurants');
				break;

			case 'VRTKMANAGEENTRYGROUP3':
				$result = __('Minimum Toppings', 'vikrestaurants');
				break;

			case 'VRTKMANAGEENTRYGROUP4':
				$result = __('Maximum Toppings', 'vikrestaurants');
				break;

			case 'VRSTOPINCOMINGRES':
				$result = __('Stop Incoming Reservations', 'vikrestaurants');
				break;

			case 'VRSTOPRESDIALOGMESSAGE':
				$result = __('Incoming reservations will be stopped.<br/>Reservations will be available again starting from %s.<br/><br/>Do you want to proceed?', 'vikrestaurants');
				break;

			case 'VRSTARTINCOMINGRES':
				$result = __('Allow Incoming Reservations', 'vikrestaurants');
				break;

			case 'VRSTARTRESDIALOGMESSAGE':
				$result = __('Incoming reservations will be immediately available.<br/><br/>Do you want to proceed?', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION21':
				$result = __('Booked', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION22':
				$result = __('User', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION23':
				$result = __('Guest', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES28':
				$result = __('Booked', 'vikrestaurants');
				break;

			case 'VROPERATORFIELDSET1':
				$result = __('Operator', 'vikrestaurants');
				break;

			case 'VROPERATORFIELDSET2':
				$result = __('Login', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR14':
				$result = __('Report', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR15':
				$result = __('Mail Notifications', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR16':
				$result = __('Keep Track Actions', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWOPERATORLOGS':
				$result = __('VikRestaurants - Operator Logs', 'vikrestaurants');
				break;

			case 'VRMANAGEOPLOG1':
				$result = __('Log', 'vikrestaurants');
				break;

			case 'VRMANAGEOPLOG2':
				$result = __('Date Created', 'vikrestaurants');
				break;

			case 'VRMANAGEOPLOG3':
				$result = __('Group', 'vikrestaurants');
				break;

			case 'VROPLOGTYPE0':
				$result = __('Generic', 'vikrestaurants');
				break;

			case 'VROPLOGTYPE1':
				$result = __('Restaurant', 'vikrestaurants');
				break;

			case 'VROPLOGTYPE2':
				$result = __('Take-Away', 'vikrestaurants');
				break;

			case 'VROPLOGDATEFILTER1':
				$result = __('Today', 'vikrestaurants');
				break;

			case 'VROPLOGDATEFILTER2':
				$result = __('Last Week', 'vikrestaurants');
				break;

			case 'VROPLOGDATEFILTER3':
				$result = __('Last Month', 'vikrestaurants');
				break;

			case 'VROPLOGDATEFILTER4':
				$result = __('Last 3 Months', 'vikrestaurants');
				break;

			case 'VROPLOGDATEFILTER5':
				$result = __('All Time', 'vikrestaurants');
				break;

			case 'VRWORKSHIFTFIELDSET1':
				$result = __('Working Shift', 'vikrestaurants');
				break;

			case 'VRMANAGESHIFT6':
				$result = __('Label', 'vikrestaurants');
				break;

			case 'VRMENUPRODFIELDSET1':
				$result = __('Menu Product', 'vikrestaurants');
				break;

			case 'VRMENUPRODFIELDSET2':
				$result = __('Product Variations', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY20':
				$result = __('Priority', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY21':
				$result = __('Maximum People Allowed', 'vikrestaurants');
				break;

			case 'VRPEOPLEALLOPT1':
				$result = __('Unlimited', 'vikrestaurants');
				break;

			case 'VRPEOPLEALLOPT2':
				$result = __('As Specified', 'vikrestaurants');
				break;

			case 'VRPRIORITY1':
				$result = __('Low', 'vikrestaurants');
				break;

			case 'VRPRIORITY2':
				$result = __('Medium', 'vikrestaurants');
				break;

			case 'VRPRIORITY3':
				$result = __('High', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU32':
				$result = __('Highlight', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU33':
				$result = __('Languages', 'vikrestaurants');
				break;

			case 'VRTKMENUFIELDSET1':
				$result = __('Take-Away Menu', 'vikrestaurants');
				break;

			case 'VRTKMENUFIELDSET2':
				$result = __('Take-Away Items', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU14':
				$result = __('Original Size', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU15':
				$result = __('Take-Away Menu', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU16':
				$result = __('Image', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU17':
				$result = __('Remove Image', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU18':
				$result = __('Attributes', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU19':
				$result = __('Toggle Description', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU20':
				$result = __('Products', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU21':
				$result = __('See List', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWTKRES':
				$result = __('VikRestaurants - New Take-Away Order', 'vikrestaurants');
				break;

			case 'VRMAINTITLETKORDERCART':
				$result = __('Take-Away Order Cart', 'vikrestaurants');
				break;

			case 'VRTKORDERCARTFIELDSET1':
				$result = __('Order', 'vikrestaurants');
				break;

			case 'VRTKORDERCARTFIELDSET2':
				$result = __('Food', 'vikrestaurants');
				break;

			case 'VRTKORDERCARTFIELDSET3':
				$result = __('Cart', 'vikrestaurants');
				break;

			case 'VRTKCARTOPTION3':
				$result = __('Total Cost', 'vikrestaurants');
				break;

			case 'VRTKCARTOPTION5':
				$result = __('Variation', 'vikrestaurants');
				break;

			case 'VRJMODALTKBASKET':
				$result = __('Order Basket', 'vikrestaurants');
				break;

			case 'VRMENUTAKEAWAYDEALS':
				$result = __('Deals', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWTKDEAL':
				$result = __('VikRestaurants - New Take-Away Deal', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITTKDEAL':
				$result = __('VikRestaurants - Edit Take-Away Deal', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWTKDEALS':
				$result = __('VikRestaurants - Take-Away Deals', 'vikrestaurants');
				break;

			case 'VRTKDEALFIELDSET1':
				$result = __('Deal', 'vikrestaurants');
				break;

			case 'VRTKDEALFIELDSET2':
				$result = __('Rules', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL1':
				$result = __('ID', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL2':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL3':
				$result = __('Description', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL4':
				$result = __('Start Date', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL5':
				$result = __('End Date', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL6':
				$result = __('Max Usages', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL7':
				$result = __('Published', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL8':
				$result = __('Type', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL9':
				$result = __('Ordering', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL10':
				$result = __('Amount', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL11':
				$result = __('Percent or Total', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL12':
				$result = __('Auto Insert Gift Food', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL13':
				$result = __('Days Filter', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL14':
				$result = __('Target Food', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL15':
				$result = __('Gift Food', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL16':
				$result = __('Total Cost in Cart', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL17':
				$result = __('Combinations Min Occurrence', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPE0':
				$result = __('- Please Choose -', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPE1':
				$result = __('Above All', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPE2':
				$result = __('Discount Item', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPE3':
				$result = __('Free Item with Combination', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPE4':
				$result = __('Free Item with Total Cost', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPE5':
				$result = __('Coupon', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPEDESC0':
				$result = __('Please choose a deal type from the related dropdown.', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPEDESC1':
				$result = __('Apply a discount to the whole order when certain items are in the cart.', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPEDESC2':
				$result = __('Apply a discount only to the selected item.', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPEDESC3':
				$result = __('Insert free item(s) when certain products are in the cart.', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPEDESC4':
				$result = __('Insert free item(s) when the total cost of the order is equals or higher than the specified amount.', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPEDESC5':
				$result = __('This type of deal is only descriptive and it could be used to promote your active coupon codes.', 'vikrestaurants');
				break;

			case 'VRTKDEALQUANTITYOPT1':
				$result = __('Unlimited', 'vikrestaurants');
				break;

			case 'VRTKDEALQUANTITYOPT2':
				$result = __('As Defined', 'vikrestaurants');
				break;

			case 'VRTKDEALTARGETOPT1':
				$result = __('Required', 'vikrestaurants');
				break;

			case 'VRTKDEALTARGETOPT2':
				$result = __('At least one', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGGLOBSECTION1':
				$result = __('System', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGGLOBSECTION2':
				$result = __('E-Mail', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGGLOBSECTION3':
				$result = __('Currency', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG43':
				$result = __('Sender e-Mail', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG44':
				$result = __('Send to Customers with Order', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG45':
				$result = __('Send to Operators with Order', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG46':
				$result = __('Send to Admin with Order', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG48':
				$result = __('Show Large Party Label', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG49':
				$result = __('Large Party URL', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG50':
				$result = __('Enable Multilanguage', 'vikrestaurants');
				break;

			case 'VRCONFIGSENDMAILWHEN1':
				$result = __('Only Confirmed', 'vikrestaurants');
				break;

			case 'VRCONFIGSENDMAILWHEN2':
				$result = __('Pending or Confirmed', 'vikrestaurants');
				break;

			case 'VRTKZIPPLACEHOLDER1':
				$result = __('From ZIP', 'vikrestaurants');
				break;

			case 'VRTKZIPPLACEHOLDER2':
				$result = __('To ZIP', 'vikrestaurants');
				break;

			case 'VRTKZIPPLACEHOLDER3':
				$result = __('Surcharge', 'vikrestaurants');
				break;

			case 'VRJMODALEMAILTMPL':
				$result = __('E-Mail Template', 'vikrestaurants');
				break;

			case 'VRMANAGELANG1':
				$result = __('ID', 'vikrestaurants');
				break;

			case 'VRMANAGELANG2':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGELANG3':
				$result = __('Description', 'vikrestaurants');
				break;

			case 'VRMANAGELANG4':
				$result = __('Language', 'vikrestaurants');
				break;

			case 'VRGOTOP':
				$result = __('Go Top', 'vikrestaurants');
				break;

			case 'VRSAVEASCOPY':
				$result = __('Save as Copy', 'vikrestaurants');
				break;

			case 'VRSAVEANDCART':
				$result = __('Save & Fill Cart', 'vikrestaurants');
				break;

			case 'VRADDTOCART':
				$result = __('Add to Cart', 'vikrestaurants');
				break;

			case 'VRGOTIT':
				$result = __('Got It!', 'vikrestaurants');
				break;

			case 'VRCLEARFILTER':
				$result = __('Clear', 'vikrestaurants');
				break;

			case 'VRSYSTEMCONNECTIONERR':
				$result = __('Connection Lost! Please try again.', 'vikrestaurants');
				break;

			case 'VRTODAY':
				$result = __('Today', 'vikrestaurants');
				break;

			case 'VRDATEPAST':
				$result = __('The date is in the past', 'vikrestaurants');
				break;

			case 'VRCONFIGSMSTITLE2':
				$result = __('Customer SMS Template', 'vikrestaurants');
				break;

			case 'VRCONFIGSMSTITLE3':
				$result = __('Administrator SMS Template', 'vikrestaurants');
				break;

			case 'VRSMSDIALOGTITLE':
				$result = __('SMS to %s', 'vikrestaurants');
				break;

			case 'VRSMSDIALOGMESSAGE':
				$result = __('Insert below the text to send via SMS (Max 160 characters).', 'vikrestaurants');
				break;

			case 'VRKEEPSMSTEXTDEF':
				$result = __('Keep this text as default', 'vikrestaurants');
				break;

			case 'VRCUSTSMSSENT1':
				$result = __('SMS sent successfully!', 'vikrestaurants');
				break;

			case 'VRCUSTSMSSENT0':
				$result = __('Impossible to send the SMS! Please, try to check the API settings and the credit on your account.', 'vikrestaurants');
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
				$result = __('The operator changed the table of a restaurant reservation.', 'vikrestaurants');
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
			 * VikRestaurants 1.7
			 */

			case 'VRMANAGERESERVATION24':
				$result = __('Leave in', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION25':
				$result = __('Stay Time', 'vikrestaurants');
				break;

			case 'VRMENUTAKEAWAYRESERVATIONS':
				$result = __('Orders', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY22':
				$result = __('Service', 'vikrestaurants');
				break;

			case 'VRSPDAYSERVICEOPT1':
				$result = __('As Global', 'vikrestaurants');
				break;

			case 'VRSPDAYSERVICEOPT2':
				$result = __('Delivery and Pickup', 'vikrestaurants');
				break;

			case 'VRSPDAYSERVICEOPT3':
				$result = __('Only Delivery', 'vikrestaurants');
				break;

			case 'VRSPDAYSERVICEOPT4':
				$result = __('Only Pickup', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT7':
				$result = __('Notes After Purchase', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT10':
				$result = __('Restrictions', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT11':
				$result = __('Notes Before Purchase', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT12':
				$result = __('Icon', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT13':
				$result = __('Output Position', 'vikrestaurants');
				break;

			case 'VRPAYMENTPOSOPT1':
				$result = __('- Ignore -', 'vikrestaurants');
				break;

			case 'VRPAYMENTPOSOPT2':
				$result = __('Top', 'vikrestaurants');
				break;

			case 'VRPAYMENTPOSOPT3':
				$result = __('Bottom', 'vikrestaurants');
				break;

			case 'VRPAYMENTICONOPT0':
				$result = __('- None -', 'vikrestaurants');
				break;

			case 'VRPAYMENTICONOPT1':
				$result = __('Font Icon', 'vikrestaurants');
				break;

			case 'VRPAYMENTICONOPT2':
				$result = __('Upload Image', 'vikrestaurants');
				break;

			case 'VRPAYRESTROPT1':
				$result = __('Always Enabled', 'vikrestaurants');
				break;

			case 'VRPAYRESTROPT2':
				$result = __('When Total Cost is higher than', 'vikrestaurants');
				break;

			case 'VRPAYRESTROPT3':
				$result = __('When Total Cost is lower than', 'vikrestaurants');
				break;

			case 'VRMENUMEDIA':
				$result = __('Media', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWMEDIA':
				$result = __('VikRestaurants - New Media', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITMEDIA':
				$result = __('VikRestaurants - Edit Media', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWMEDIA':
				$result = __('VikRestaurants - Media Manager', 'vikrestaurants');
				break;

			case 'VRMEDIAFIELDSET1':
				$result = __('Media File', 'vikrestaurants');
				break;

			case 'VRMEDIAFIELDSET2':
				$result = __('Image', 'vikrestaurants');
				break;

			case 'VRMEDIAFIELDSET3':
				$result = __('Thumbnail', 'vikrestaurants');
				break;

			case 'VRMEDIAFIELDSET4':
				$result = __('Quick Upload', 'vikrestaurants');
				break;

			case 'VRMEDIAFIELDSET5':
				$result = __('Uploads', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA1':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA2':
				$result = __('Size', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA3':
				$result = __('Created On', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA4':
				$result = __('Image', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA5':
				$result = __('Action', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA6':
				$result = __('Resize Original', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA7':
				$result = __('Resize Width', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA8':
				$result = __('Thumbnail Width', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA9':
				$result = __('Processing...', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA10':
				$result = __('No Image used', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA11':
				$result = __('- Upload New -', 'vikrestaurants');
				break;

			case 'VRMEDIAACTION0':
				$result = __('- None -', 'vikrestaurants');
				break;

			case 'VRMEDIAACTION1':
				$result = __('Replace Image', 'vikrestaurants');
				break;

			case 'VRMEDIAACTION2':
				$result = __('Replace Thumb', 'vikrestaurants');
				break;

			case 'VRMEDIAACTION3':
				$result = __('Replace Image and Thumb', 'vikrestaurants');
				break;

			case 'VRMEDIADRAGDROP':
				$result = __('DRAG & DROP IMAGES HERE', 'vikrestaurants');
				break;

			case 'VRMEDIARENERR':
				$result = __('Impossible to rename the media! The specified name [%s] already exists.', 'vikrestaurants');
				break;

			case 'VRMENUREVIEWS':
				$result = __('Reviews', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWREVIEW':
				$result = __('VikRestaurants - New Review', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITREVIEW':
				$result = __('VikRestaurants - Edit Review', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWREVIEWS':
				$result = __('VikRestaurants - Reviews', 'vikrestaurants');
				break;

			case 'VRMANAGEREVIEW1':
				$result = __('ID', 'vikrestaurants');
				break;

			case 'VRMANAGEREVIEW2':
				$result = __('Title', 'vikrestaurants');
				break;

			case 'VRMANAGEREVIEW3':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGEREVIEW4':
				$result = __('Date', 'vikrestaurants');
				break;

			case 'VRMANAGEREVIEW5':
				$result = __('Rating', 'vikrestaurants');
				break;

			case 'VRMANAGEREVIEW6':
				$result = __('Product', 'vikrestaurants');
				break;

			case 'VRMANAGEREVIEW7':
				$result = __('Published', 'vikrestaurants');
				break;

			case 'VRMANAGEREVIEW8':
				$result = __('Language', 'vikrestaurants');
				break;

			case 'VRMANAGEREVIEW9':
				$result = __('Comment', 'vikrestaurants');
				break;

			case 'VRMANAGEREVIEW10':
				$result = __('User', 'vikrestaurants');
				break;

			case 'VRMANAGEREVIEW11':
				$result = __('E-Mail', 'vikrestaurants');
				break;

			case 'VRMANAGEREVIEW12':
				$result = __('Verified', 'vikrestaurants');
				break;

			case 'VROPERATORFIELDSET3':
				$result = __('Global', 'vikrestaurants');
				break;

			case 'VROPERATORFIELDSET4':
				$result = __('Live Map', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR17':
				$result = __('Manage Coupons', 'vikrestaurants');
				break;

			case 'VROPCOUPONOPT0':
				$result = __('Access denied', 'vikrestaurants');
				break;

			case 'VROPCOUPONOPT1':
				$result = __('See only', 'vikrestaurants');
				break;

			case 'VROPCOUPONOPT2':
				$result = __('Allowed', 'vikrestaurants');
				break;

			case 'VRINVOICE':
				$result = __('Invoice', 'vikrestaurants');
				break;

			case 'VRMENUINVOICES':
				$result = __('Invoices', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWINVOICE':
				$result = __('VikRestaurants - New Invoice', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITINVOICE':
				$result = __('VikRestaurants - Edit Invoice', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWINVOICES':
				$result = __('VikRestaurants - Invoices', 'vikrestaurants');
				break;

			case 'VRINVOICEFIELDSET1':
				$result = __('Orders', 'vikrestaurants');
				break;

			case 'VRINVOICEFIELDSET2':
				$result = __('Details', 'vikrestaurants');
				break;

			case 'VRINVOICEFIELDSET3':
				$result = __('Properties', 'vikrestaurants');
				break;

			case 'VRINVOICEDIALOG':
				$result = __('Issue Invoices', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE1':
				$result = __('Group', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE2':
				$result = __('Reservations on', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE3':
				$result = __('Overwrite Existing', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE4':
				$result = __('Unique Identifier', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE5':
				$result = __('Date', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE6':
				$result = __('Legal Info', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE7':
				$result = __('Notify Customers', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE8':
				$result = __('Page Orientation', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE9':
				$result = __('Page Format', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE10':
				$result = __('Unit', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE11':
				$result = __('Scale Ratio', 'vikrestaurants');
				break;

			case 'VRINVOICEDATEOPT1':
				$result = __('Today - %s', 'vikrestaurants');
				break;

			case 'VRINVOICEDATEOPT2':
				$result = __('Booking Check-in', 'vikrestaurants');
				break;

			case 'VRINVOICEPAGEORIOPT1':
				$result = __('Portrait', 'vikrestaurants');
				break;

			case 'VRINVOICEPAGEORIOPT2':
				$result = __('Landscape', 'vikrestaurants');
				break;

			case 'VRINVOICEUNITOPT1':
				$result = __('Point', 'vikrestaurants');
				break;

			case 'VRINVOICEUNITOPT2':
				$result = __('Millimeter', 'vikrestaurants');
				break;

			case 'VRINVOICEUNITOPT3':
				$result = __('Centimeter', 'vikrestaurants');
				break;

			case 'VRINVOICEUNITOPT4':
				$result = __('Inch', 'vikrestaurants');
				break;

			case 'VRINVOICESOTHERS':
				$result = __('Others', 'vikrestaurants');
				break;

			case 'VRINVOICESOTHERSALL':
				$result = __('All', 'vikrestaurants');
				break;

			case 'VRLOADMOREINVOICES':
				$result = __('Load more invoices', 'vikrestaurants');
				break;

			case 'VRLOADALLINVOICES':
				$result = __('Load all invoices', 'vikrestaurants');
				break;

			case 'VRINVSELECTALL':
				$result = __('Select All', 'vikrestaurants');
				break;

			case 'VRINVSELECTNONE':
				$result = __('Select None', 'vikrestaurants');
				break;

			case 'VRMANAGETKTOPPING6':
				$result = __('Price Quick Update', 'vikrestaurants');
				break;

			case 'VRTKTOPPINGQUICKOPT0':
				$result = __('Do nothing', 'vikrestaurants');
				break;

			case 'VRTKTOPPINGQUICKOPT1':
				$result = __('Update all products toppings', 'vikrestaurants');
				break;

			case 'VRTKTOPPINGQUICKOPT2':
				$result = __('Update products toppings with same price', 'vikrestaurants');
				break;

			case 'VRMENUDETAILSFIELDSET':
				$result = __('Menu Details', 'vikrestaurants');
				break;

			case 'VRTKGROUPVARPLACEHOLDER':
				$result = __('- All -', 'vikrestaurants');
				break;

			case 'VRTKMANAGEENTRYGROUP5':
				$result = __('Variation', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWRESCODEORDER':
				$result = __('VikRestaurants - New Order Status', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITRESCODEORDER':
				$result = __('VikRestaurants - Edit Order Status', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWRESCODESORDER':
				$result = __('VikRestaurants - Order Statuses', 'vikrestaurants');
				break;

			case 'VRMANAGERESCODE5':
				$result = __('Notes', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENUSTOCKS':
				$result = __('Manage Menu Stocks', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWTKMENUSTOCKS':
				$result = __('VikRestaurants - Manage Menu Stocks', 'vikrestaurants');
				break;

			case 'VRMANAGETKSTOCK1':
				$result = __('Product', 'vikrestaurants');
				break;

			case 'VRMANAGETKSTOCK2':
				$result = __('Variation', 'vikrestaurants');
				break;

			case 'VRMANAGETKSTOCK3':
				$result = __('Items in Stock', 'vikrestaurants');
				break;

			case 'VRMANAGETKSTOCK4':
				$result = __('Notify Below', 'vikrestaurants');
				break;

			case 'VRMANAGETKSTOCK6':
				$result = __('Update All Variations', 'vikrestaurants');
				break;

			case 'VRMANAGETKSTOCK7':
				$result = __('Available in Stock', 'vikrestaurants');
				break;

			case 'VRMANAGETKSTOCK8':
				$result = __('Products Used', 'vikrestaurants');
				break;

			case 'VRMANAGETKSTOCK9':
				$result = __('Report', 'vikrestaurants');
				break;

			case 'VRMANAGETKSTOCK10':
				$result = __('Increase/Decrease Stock', 'vikrestaurants');
				break;

			case 'VRSTOCKITEMNOUSED':
				$result = __('Never used', 'vikrestaurants');
				break;

			case 'VRSTOCKITEMUSED':
				$result = __('%d of %d used', 'vikrestaurants');
				break;

			case 'VRTKSTOCKSOVERVIEW':
				$result = __('VikRestaurants - Stocks Overview', 'vikrestaurants');
				break;

			case 'VRTKSTOCKSOVERVIEWBTN':
				$result = __('Stocks Overview', 'vikrestaurants');
				break;

			case 'VRTKSTATSTOCKS':
				$result = __('VikRestaurants - Stocks Statistics', 'vikrestaurants');
				break;

			case 'VRTKSTATSTOCKSBTN':
				$result = __('Stocks Statistics', 'vikrestaurants');
				break;

			case 'VRTKSTATSTOCKSCHARTYEARS':
				$result = __('Years Chart', 'vikrestaurants');
				break;

			case 'VRTKSTATSTOCKSCHARTMONTHS':
				$result = __('Months Chart', 'vikrestaurants');
				break;

			case 'VRTKSTATSTOCKSCHARTWEEKDAYS':
				$result = __('Weekdays Chart', 'vikrestaurants');
				break;

			case 'VRTKSTATSTOCKSNODATA':
				$result = __('No relevant data.', 'vikrestaurants');
				break;

			case 'VRTKSTATSTOCKSNOITEMSEL':
				$result = __('Click on the report button of an item to see its statistics.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTKSECTION2':
				$result = __('Stocks', 'vikrestaurants');
				break;

			case 'VRALLMENUSOPTION':
				$result = __('- All Menus -', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK16':
				$result = __('Enable Stocks System', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK17':
				$result = __('Stocks E-Mail Template', 'vikrestaurants');
				break;

			case 'VRTKSTOCKNOITEMS':
				$result = __('We have no more %s available! Please, select a different item.', 'vikrestaurants');
				break;

			case 'VRTKSTOCKREMOVEDITEMS':
				$result = __('There are not enough %s available!\n%d products haven\'t been added in your cart. Please, select a different item.', 'vikrestaurants');
					break;

			case 'VRTKSTOCKITEMSUCCESS':
				$result = __('Item added into the cart successfully!', 'vikrestaurants');
				break;

			case 'VRCREATENEWOPT':
				$result = __('- Create New -', 'vikrestaurants');
				break;

			case 'VRCREATENEWPROD':
				$result = __('Create New Product', 'vikrestaurants');
				break;

			case 'VRGOTOMENUS':
				$result = __('Back to Menus', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWTKENTRY':
				$result = __('VikRestaurants - New Take-Away Product', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITTKENTRY':
				$result = __('VikRestaurants - Edit Take-Away Product', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU9_HELP':
				$result = __('Check this field if the entry has no preparation time (e.g. beverages). Items without preparation time are never considered during the availability search.', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMF11':
				$result = __('Rule', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMF12':
				$result = __('Values', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDRULE0':
				$result = __('- None -', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDRULE1':
				$result = __('Nominative', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDRULE2':
				$result = __('E-Mail', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDRULE3':
				$result = __('Phone Number', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDRULE4':
				$result = __('Address', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDRULE5':
				$result = __('Delivery', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDRULE6':
				$result = __('ZIP Code', 'vikrestaurants');
				break;

			case 'VRCUSTOMERTABTITLE1':
				$result = __('Billing Details', 'vikrestaurants');
				break;

			case 'VRCUSTOMERTABTITLE2':
				$result = __('Delivery Locations', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMERTITLE5':
				$result = __('Delivery Details', 'vikrestaurants');
				break;

			case 'VRCUSTOMERDELIVERYHEAD':
				$result = __('Drag & Move the addresses to change their ordering.', 'vikrestaurants');
				break;

			case 'VRMENUTAKEAWAYDELIVERYAREAS':
				$result = __('Delivery Areas', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWTKAREA':
				$result = __('VikRestaurants - New Delivery Area', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITTKAREA':
				$result = __('VikRestaurants - Edit Delivery Area', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWTKAREAS':
				$result = __('VikRestaurants - Delivery Areas', 'vikrestaurants');
				break;

			case 'VRTKAREAFIELDSET1':
				$result = __('Area Details', 'vikrestaurants');
				break;

			case 'VRTKAREAFIELDSET2':
				$result = __('Contents', 'vikrestaurants');
				break;

			case 'VRTKAREAFIELDSET3':
				$result = __('Attributes', 'vikrestaurants');
				break;

			case 'VRTKAREAFIELDSET4':
				$result = __('Map', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA1':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA2':
				$result = __('Type', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA3':
				$result = __('Published', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA4':
				$result = __('Charge', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA5':
				$result = __('Ordering', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA6':
				$result = __('Center', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA7':
				$result = __('Latitude', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA8':
				$result = __('Longitude', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA9':
				$result = __('Radius', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA10':
				$result = __('Fill Color', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA11':
				$result = __('Add New Point', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA12':
				$result = __('Display All Areas', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA13':
				$result = __('Toggle Coordinates', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA14':
				$result = __('Stroke Color', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA15':
				$result = __('Stroke Weight', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA16':
				$result = __('Coordinates', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA17':
				$result = __('Area', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA18':
				$result = __('Min. Total Order', 'vikrestaurants');
				break;

			case 'VRTKAREATYPE0':
				$result = __('select a type', 'vikrestaurants');
				break;

			case 'VRTKAREATYPE1':
				$result = __('Polygon', 'vikrestaurants');
				break;

			case 'VRTKAREATYPE2':
				$result = __('Circle', 'vikrestaurants');
				break;

			case 'VRTKAREATYPE3':
				$result = __('ZIP Codes', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA2_HELP':
				$result = __('Build a polygon or circle shape to restrict the delivery area. Otherwise you can also restrict the allowed ZIP codes.', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA4_HELP':
				$result = __('An additional fee to be summed to the base delivery cost.', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA12_HELP':
				$result = __('Enable this option to display all the existing delivery areas. It is useful to draw new areas next to the existing ones.', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA18_HELP':
				$result = __('The minimum cost required to delivery an order in this area.', 'vikrestaurants');
				break;

			case 'VRTKAREA_CIRCLE_LATLNG_HELP':
				$result = __('Insert the coordinates for the center of the circle area. Otherwise you can select the center by clicking directly into the map. In order to display the map, it is needed to insert some coordinates first.', 'vikrestaurants');
				break;

			case 'VRTKAREA_POLYGON_LEGEND_HELP':
				$result = __('Add a new polygon vertex and scroll down to see the legend of the possible actions.', 'vikrestaurants');
				break;

			case 'VRTKMAPDELIVERYAREAS':
				$result = __('Delivery Areas Map', 'vikrestaurants');
				break;

			case 'VRTKAREALEGEND1':
				$result = __('drag to re-order the vertex', 'vikrestaurants');
				break;

			case 'VRTKAREALEGEND2':
				$result = __('drag the pin on the map to update the coordinates of the vertex', 'vikrestaurants');
				break;

			case 'VRTKAREALEGEND3':
				$result = __('click on the map to create a new vertex', 'vikrestaurants');
				break;

			case 'VRTKAREALEGEND4':
				$result = __('assign your current coordinates to the vertex', 'vikrestaurants');
				break;

			case 'VRTKAREALEGEND5':
				$result = __('remove the vertex', 'vikrestaurants');
				break;

			case 'VRTKAREAUSERPOSITION':
				$result = __('Would you like to replace the existing coordinates with your current location?', 'vikrestaurants');
				break;

			case 'VRTKMAPTESTADDRESS':
				$result = __('Insert here an address...', 'vikrestaurants');
				break;

			case 'VRTKDELIVERYLOCNOTFOUND':
				$result = __('The specified address is not available for the delivery.', 'vikrestaurants');
				break;

			case 'VRTKROUTEDELIVERYERR':
				$result = __('Directions request failed due to %s.', 'vikrestaurants');
				break;

			case 'VRSYSTEMCONFIRMATIONMSG':
				$result = __('Are you sure? This action cannot be undone.', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES29':
				$result = __('Address', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES30':
				$result = __('Payment Charge', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES31':
				$result = __('Delivery Charge', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES32':
				$result = __('Restaurant', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES33':
				$result = __('Route', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES34':
				$result = __('Start Delivery @ %s', 'vikrestaurants');
				break;

			case 'VRTKRESADDRESSNOTVALID':
				$result = __('The specified address is not available for the delivery. Would you like to accept it?', 'vikrestaurants');
				break;

			case 'VRSYSPUBLISHED1':
				$result = __('Published', 'vikrestaurants');
				break;

			case 'VRSYSPUBLISHED0':
				$result = __('Unpublished', 'vikrestaurants');
				break;

			case 'VRSYSHIDDEN':
				$result = __('Hidden', 'vikrestaurants');
				break;

			case 'VRCONFIGDATEFORMAT4':
				$result = __('Y-m-d', 'vikrestaurants');
				break;

			case 'VRCONFIGDATEFORMAT5':
				$result = __('m-d-Y', 'vikrestaurants');
				break;

			case 'VRCONFIGDATEFORMAT6':
				$result = __('d-m-Y', 'vikrestaurants');
				break;

			case 'VRCONFIGDATEFORMAT7':
				$result = __('Y.m.d', 'vikrestaurants');
				break;

			case 'VRCONFIGDATEFORMAT8':
				$result = __('m.d.Y', 'vikrestaurants');
				break;

			case 'VRCONFIGDATEFORMAT9':
				$result = __('d.m.Y', 'vikrestaurants');
				break;

			case 'VRECONFIGTABNAME5':
				$result = __('Applications', 'vikrestaurants');
				break;

			case 'VRCONFIGFIELDSETREVIEWS':
				$result = __('Reviews', 'vikrestaurants');
				break;

			case 'VRCONFIGFIELDSETAPIFR':
				$result = __('Framework APIs', 'vikrestaurants');
				break;

			case 'VRCONFIGFIELDSETSEARCH':
				$result = __('Search', 'vikrestaurants');
				break;

			case 'VRCONFIGFIELDSETRESERVATION':
				$result = __('Reservation', 'vikrestaurants');
				break;

			case 'VRCONFIGFIELDSETORDER':
				$result = __('Order', 'vikrestaurants');
				break;

			case 'VRCONFIGFIELDSETDELIVERY':
				$result = __('Delivery', 'vikrestaurants');
				break;

			case 'VRCONFIGFIELDSETTAKEAWAY':
				$result = __('Take-Away', 'vikrestaurants');
				break;

			case 'VRCONFIGFIELDSETTAXES':
				$result = __('Taxes', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG47':
				$result = __('Customer E-Mail Template', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG51':
				$result = __('Decimal Separator', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG52':
				$result = __('Thousands Separator', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG53':
				$result = __('Number of Decimals', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG54':
				$result = __('Enable Restaurant', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG55':
				$result = __('Google API Key', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG56':
				$result = __('Admin E-Mail Template', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG57':
				$result = __('Cancellation E-Mail Template', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG58':
				$result = __('Enable Reviews', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG59':
				$result = __('Take-Away Reviews', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG60':
				$result = __('Reviews Allowed to', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG61':
				$result = __('Comment Required', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG62':
				$result = __('Comment Min Length', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG63':
				$result = __('Comment Max Length', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG64':
				$result = __('List Limit', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG65':
				$result = __('New Auto-Published', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG66':
				$result = __('Filter by Language', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG67':
				$result = __('Prod. Review E-Mail Template', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG68':
				$result = __('Cancellation Reason', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG69':
				$result = __('Enable APIs', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG70':
				$result = __('Applications', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG71':
				$result = __('See Users List', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG72':
				$result = __('Register Logs', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG73':
				$result = __('Auto-Flush Logs', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG74':
				$result = __('Max Failure Attempts', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG75':
				$result = __('When this number of attempts is reached, the calling IP will be automatically banned', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG76':
				$result = __('Plugins', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG77':
				$result = __('See Installed Plugins', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG79':
				$result = __('Current Default Timezone', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG80':
				$result = __('Phone Prefix Selection', 'vikrestaurants');
				break;

			case 'VRCONFIGAPIREGLOGOPT0':
				$result = __('Never', 'vikrestaurants');
				break;

			case 'VRCONFIGAPIREGLOGOPT1':
				$result = __('Only Errors', 'vikrestaurants');
				break;

			case 'VRCONFIGAPIREGLOGOPT2':
				$result = __('Always', 'vikrestaurants');
				break;

			case 'VRCONFIGAPIFLUSHLOGOPT0':
				$result = __('Never', 'vikrestaurants');
				break;

			case 'VRCONFIGAPIFLUSHLOGOPT1':
				$result = __('Every Day', 'vikrestaurants');
				break;

			case 'VRCONFIGAPIFLUSHLOGOPT2':
				$result = __('Every Week', 'vikrestaurants');
				break;

			case 'VRCONFIGAPIFLUSHLOGOPT3':
				$result = __('Every Month', 'vikrestaurants');
				break;

			case 'VRCONFIGREVLEAVEMODEOPT0':
				$result = __('Anyone', 'vikrestaurants');
				break;

			case 'VRCONFIGREVLEAVEMODEOPT1':
				$result = __('Only Logged-in Users', 'vikrestaurants');
				break;

			case 'VRCONFIGREVLEAVEMODEOPT2':
				$result = __('Verified Purchasers', 'vikrestaurants');
				break;

			case 'VRCONFIGCANCREASONOPT0':
				$result = __('Never', 'vikrestaurants');
				break;

			case 'VRCONFIGCANCREASONOPT1':
				$result = __('Optional', 'vikrestaurants');
				break;

			case 'VRCONFIGCANCREASONOPT2':
				$result = __('Always Required', 'vikrestaurants');
				break;

			case 'VRDELIVERYSERVICEOPT1':
				$result = __('Only Pickup', 'vikrestaurants');
				break;

			case 'VRDELIVERYSERVICEOPT2':
				$result = __('Only Delivery', 'vikrestaurants');
				break;

			case 'VRDELIVERYSERVICEOPT3':
				$result = __('Pickup & Delivery', 'vikrestaurants');
				break;

			case 'VRCONFIGSENDMAILWHEN0':
				$result = __('Never', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK9':
				$result = __('Soonest Delivery After', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK18':
				$result = __('Pickup Cost/Discount', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK19':
				$result = __('Origin Addresses', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK20':
				$result = __('Addresses List', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK22':
				$result = __('Insert full address here', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK25':
				$result = __('Max. Meals in Cart', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK26':
				$result = __('Allow Date Selection', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK27':
				$result = __('Live Orders', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK28':
				$result = __('Use Items Overlay', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK29':
				$result = __('Products Description Length', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK30':
				$result = __('Show Products Image', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK26_HELP':
				$result = __('When this parameter is disabled, the orders can be placed only for the current day.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK27_HELP':
				$result = __('Enable this option to allow the customers to order only if the restaurant is currently open.', 'vikrestaurants');
				break;

			case 'VRTKCONFIGOVERLAYOPT0':
				$result = __('Never', 'vikrestaurants');
				break;

			case 'VRTKCONFIGOVERLAYOPT1':
				$result = __('Only with toppings available', 'vikrestaurants');
				break;

			case 'VRTKCONFIGOVERLAYOPT2':
				$result = __('Always', 'vikrestaurants');
				break;

			case 'VRTKCONFIGITEMOPT0':
				$result = __('- use default -', 'vikrestaurants');
				break;

			case 'VRTKCONFIGITEMOPT1':
				$result = __('- as defined -', 'vikrestaurants');
				break;

			case 'VRRESBUSYMODALTITLE':
				$result = __('Restaurant Time Availability', 'vikrestaurants');
				break;

			case 'VRTKRESBUSYMODALTITLE':
				$result = __('Take-Away Time Availability', 'vikrestaurants');
				break;

			case 'VRTKRESEXPIRESIN':
				$result = __('expires %s', 'vikrestaurants');
				break;

			case 'VRTKRESITEMSINCART':
				$result = __('%d items out of %d require a preparation.', 'vikrestaurants');
				break;

			case 'VRTKTOPPINGOPTGROUP':
				// @TRANSLATORS: %s wildcard will be replaced by the group name (e.g. "- all Pizza toppings -")
				$result = _x('- all %s toppings -', '%s wildcard will be replaced by the group name (e.g. "- all Pizza toppings -")', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON7':
				$result = __('Min. Value', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON8':
				$result = __('Min. People', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON9':
				$result = __('Min. Total Cost', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON10':
				$result = __('Group', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON11':
				$result = __('Valid Between', 'vikrestaurants');
				break;

			case 'VRERRORFRAMEWORKPDF':
				$result = __('The TCPDF library does not exist on this program!', 'vikrestaurants');
				break;

			case 'VRNOINVOICESGENERATED':
				$result = __('No invoice generated!', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWAPIUSERS':
				$result = __('VikRestaurants - API Users', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWAPIUSER':
				$result = __('VikRestaurants - New API User', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITAPIUSER':
				$result = __('VikRestaurants - Edit API User', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER1':
				$result = __('ID', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER2':
				$result = __('Application Name', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER3':
				$result = __('Username', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER4':
				$result = __('Password', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER5':
				$result = __('Allowed IPs', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER6':
				$result = __('Active', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER7':
				$result = __('Last Login', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER8':
				$result = __('Application', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER9':
				$result = __('Add IP Address', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER10':
				$result = __('- Never -', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER11':
				$result = __('Logs', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER12':
				$result = __('See Logs', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER13':
				$result = __('Status', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER14':
				$result = __('Content', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER15':
				$result = __('Created On', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER16':
				$result = __('See Banned List', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER17':
				$result = __('IP Address', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER18':
				$result = __('Last Update', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER19':
				$result = __('Failure Attempts', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER20':
				$result = __('Banned', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER21':
				$result = __('Plugins Rules', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER22':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER23':
				$result = __('Plugin File', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER24':
				$result = __('Description', 'vikrestaurants');
				break;

			case 'VRAPIUSERUSERNAMEEXISTS':
				$result = __('The specified username already exists!', 'vikrestaurants');
				break;

			case 'VRAPIUSERUSERNAMEREGEX':
				$result = __('Username must have at least 3 characters and can contain only letters (a-z, A-Z), numbers (0-9), underscores (_) and dots (.)', 'vikrestaurants');
				break;

			case 'VRAPIUSERPASSWORDREGEX':
				$result = __('Password must have at least 8 characters, at least 1 number and at least one letter', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWAPILOGS':
				$result = __('VikRestaurants - API Logs', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWAPIBANS':
				$result = __('VikRestaurants - API Banned List', 'vikrestaurants');
				break;

			case 'VRAPIBANOPT1':
				$result = __('Only Banned', 'vikrestaurants');
				break;

			case 'VRAPIBANOPT2':
				$result = __('All Records', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWAPIPLUGINS':
				$result = __('VikRestaurants - API Plugins', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITAPIPLUGIN':
				$result = __('VikRestaurants - Edit API Plugin', 'vikrestaurants');
				break;

			case 'VRMANAGETKORDDISC1':
				$result = __('Grand Total', 'vikrestaurants');
				break;

			case 'VRMANAGETKORDDISC2':
				$result = __('Total Net', 'vikrestaurants');
				break;

			case 'VRMANAGETKORDDISC3':
				$result = __('Coupon', 'vikrestaurants');
				break;

			case 'VRMANAGETKORDDISC4':
				$result = __('Method', 'vikrestaurants');
				break;

			case 'VRMANAGETKORDDISC5':
				$result = __('Amount', 'vikrestaurants');
				break;

			case 'VRORDDISCMETHOD0':
				$result = __('select one method', 'vikrestaurants');
				break;

			case 'VRORDDISCMETHOD1':
				$result = __('Apply Coupon', 'vikrestaurants');
				break;

			case 'VRORDDISCMETHOD3':
				$result = __('Remove Coupon', 'vikrestaurants');
				break;

			case 'VRORDDISCMETHOD4':
				$result = __('Add Discount', 'vikrestaurants');
				break;

			case 'VRORDDISCMETHOD6':
				$result = __('Remove Discount', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPE6':
				$result = __('Discount with Total Cost', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPEDESC6':
				$result = __('Apply a discount to the whole order when the total cost of the cart is equals or higher than the specified amount.', 'vikrestaurants');
				break;

			case 'VRPREPARATIONMEALS':
				$result = __('meals to prepare', 'vikrestaurants');
				break;

			case 'VRDISCOUNT':
				$result = __('Discount', 'vikrestaurants');
				break;

			case 'VROK':
				$result = __('Ok', 'vikrestaurants');
				break;

			case 'VRERROR':
				$result = __('Error', 'vikrestaurants');
				break;

			case 'VRDELETEALL':
				$result = __('Delete All', 'vikrestaurants');
				break;

			case 'VRREFILL':
				$result = __('Refill', 'vikrestaurants');
				break;

			case 'VRREFILLALL':
				$result = __('Refill All', 'vikrestaurants');
				break;

			case 'VRSEARCHTOOLS':
				$result = __('Search Tools', 'vikrestaurants');
				break;

			case 'VROVERVIEW':
				$result = __('Overview', 'vikrestaurants');
				break;

			case 'VRSEECCDETAILS':
				$result = __('See Credit Card', 'vikrestaurants');
				break;

			case 'VRSTOPINCOMINGORD':
				$result = __('Stop Incoming Orders', 'vikrestaurants');
				break;

			case 'VRSTOPORDDIALOGMESSAGE':
				$result = __('Incoming orders will be stopped.<br/>Orders will be available again starting from %s.<br/><br/>Do you want to proceed?', 'vikrestaurants');
				break;

			case 'VRSTARTINCOMINGORD':
				$result = __('Allow Incoming Orders', 'vikrestaurants');
				break;

			case 'VRSTARTORDDIALOGMESSAGE':
				$result = __('Incoming orders will be immediately available.<br/><br/>Do you want to proceed?', 'vikrestaurants');
				break;

			case 'VRSTAR':
				$result = __('Star', 'vikrestaurants');
				break;

			case 'VRSTARS':
				$result = __('Stars', 'vikrestaurants');
				break;

			case 'VRCHARS':
				$result = __('characters', 'vikrestaurants');
				break;

			case 'VRPERSON':
				$result = __('person', 'vikrestaurants');
				break;

			case 'VRSHORTCUTSECOND':
				$result = __('sec.', 'vikrestaurants');
				break;

			case 'VRREFRESHIN':
				$result = __('Refresh in', 'vikrestaurants');
				break;

			case 'VRPRINTORDERS1':
				$result = __('Header Text', 'vikrestaurants');
				break;

			case 'VRPRINTORDERS2':
				$result = __('Footer Text', 'vikrestaurants');
				break;

			case 'VRPRINTORDERS3':
				$result = __('Update Changes', 'vikrestaurants');
				break;

			case 'VRCREATEDON':
				$result = __('Created On', 'vikrestaurants');
				break;

			case 'VRCREATEDBY':
				$result = __('Created By', 'vikrestaurants');
				break;

			case 'VRRESLISTCREATEDTIP':
				$result = __('Created on %s by %s.', 'vikrestaurants');
				break;

			case 'VRALLOWED':
				$result = __('Allowed', 'vikrestaurants');
				break;

			case 'VRDENIED':
				$result = __('Denied', 'vikrestaurants');
				break;

			case 'VREXPORTSUMMARY':
				$result = __('Reservation for %d people', 'vikrestaurants');
				break;

			case 'VRTKEXPORTSUMMARY':
				$result = __('Take-Away Order for %s', 'vikrestaurants');
				break;

			case 'VRRESERVATIONCPNSEARCH':
				$result = __('Coupon Code', 'vikrestaurants');
				break;

			case 'VRRESERVATIONSHIFTSEARCH':
				$result = __('- Select Shift -', 'vikrestaurants');
				break;

			case 'VRRESERVATIONSTATUSSEARCH':
				$result = __('- Order Status -', 'vikrestaurants');
				break;

			case 'VRFILTERSELECTMENU':
				$result = __('- Select Menu -', 'vikrestaurants');
				break;

			case 'VRFILTERSELECTSTATUS':
				$result = __('- Select Status -', 'vikrestaurants');
				break;

			case 'VRFILTERCIDRES':
				$result = __('You are seeing the reservations for table %s on %s<br />Click <strong>Clear</strong> button to display all reservations.', 'vikrestaurants');
				break;

			case 'VRSHIFTSIGNOREDWARNING':
				$result = __('The shifts will NOT be used unless the <strong>Opening Time Mode</strong> is set to <strong>Shifted</strong>.<br />Change mode from <a href="%s">HERE</a>.', 'vikrestaurants');
				break;

			case 'VRTABNOTAV':
				$result = __('not available', 'vikrestaurants');
				break;

			case 'VRTABNOTFIT':
				$result = __('not fit', 'vikrestaurants');
				break;

			case 'VRCREDITCARDAUTODELMSG':
				$result = __('The details of this credit card will be removed automatically on %s', 'vikrestaurants');
				break;

			case 'VRCREDITCARDREMOVED':
				$result = __('Credit card details removed correctly!', 'vikrestaurants');
				break;

			case 'VRMAINTITLEUPDATEPROGRAM':
				$result = __('VikRestaurants - Software Update', 'vikrestaurants');
				break;

			case 'VRCHECKINGVERSION':
				$result = __('Checking Version...', 'vikrestaurants');
				break;

			case 'VRDOWNLOADUPDATEBTN1':
				$result = __('Download Update & Install', 'vikrestaurants');
				break;

			case 'VRDOWNLOADUPDATEBTN0':
				$result = __('Download & Re-Install', 'vikrestaurants');
				break;

			case 'VRINVNUM':
				$result = __('Invoice Number', 'vikrestaurants');
				break;

			case 'VRINVDATE':
				$result = __('Date', 'vikrestaurants');
				break;

			case 'VRINVITEMDESC':
				$result = __('Description', 'vikrestaurants');
				break;

			case 'VRINVITEMQUANTITY':
				$result = __('Quantity', 'vikrestaurants');
				break;

			case 'VRINVITEMPRICE':
				$result = __('Price', 'vikrestaurants');
				break;

			case 'VRINVCUSTINFO':
				$result = __('Customer Info', 'vikrestaurants');
				break;

			case 'VRINVTOTAL':
				$result = __('Net Price', 'vikrestaurants');
				break;

			case 'VRINVDISCOUNTVAL':
				$result = __('Discount', 'vikrestaurants');
				break;

			case 'VRINVTAXES':
				$result = __('Taxes', 'vikrestaurants');
				break;

			case 'VRINVPAYCHARGE':
				$result = __('Pay Charge', 'vikrestaurants');
				break;

			case 'VRINVDELIVERYCHARGE':
				$result = __('Delivery Charge', 'vikrestaurants');
				break;

			case 'VRINVGRANDTOTAL':
				$result = __('Total Cost', 'vikrestaurants');
				break;

			case 'VRTITLEPDFINVOICE':
				$result = __('Invoice', 'vikrestaurants');
				break;

			case 'VRINVMAILSUBJECT':
				$result = __('%s - Invoice for order #%s', 'vikrestaurants');
				break;

			case 'VRINVMAILCONTENT':
				$result = __('This mail was generated automatically from %s.\nYou can find the invoice of your order %s as attachment.\n\nPlease, do not reply to this message.', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.7.4
			 */

			case 'VRMANAGECONFIG81':
				$result = __('Dashboard List Limit', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG82':
				$result = __('Enable GDPR', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG82_HELP':
				$result = __('The <b>General Data Protection Regulation</b> is a regulation in EU law on data protection and privacy for all individuals within the European Union.<br />Turn on this setting to be compliant with <b>GDPR</b> requirements.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG83':
				$result = __('Privacy Policy Link', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK31':
				$result = __('Default Service', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK32':
				$result = __('Enable Gratuity', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK32_HELP':
				$result = __('When enabled, the customers will be asked to leave an optional tip/gratuity for the restaurant. Percentage gratuities are calculated on the net total.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK33':
				$result = __('Suggested Gratuity', 'vikrestaurants');
				break;

			case 'VRBACK':
				$result = __('Back', 'vikrestaurants');
				break;

			case 'VRTIP':
				// @TRANSLATORS: "tip" is intended as a gratuity (bonus) for the restaurant
				$result = _x('Tip', '"tip" is intended as a gratuity (bonus) for the restaurant', 'vikrestaurants');
				break;

			case 'VRORDTIPMETHOD1':
				// @TRANSLATORS: "tip" is intended as a gratuity (bonus) for the restaurant
				$result = _x('Add Tip', '"tip" is intended as a gratuity (bonus) for the restaurant', 'vikrestaurants');
				break;

			case 'VRORDTIPMETHOD3':
				// @TRANSLATORS: "tip" is intended as a gratuity (bonus) for the restaurant
				$result = _x('Remove Tip', '"tip" is intended as a gratuity (bonus) for the restaurant', 'vikrestaurants');
				break;

			case 'VRTKCARTTOTALTIP':
				// @TRANSLATORS: "tip" is intended as a gratuity (bonus) for the restaurant
				$result = _x('Tip:', '"tip" is intended as a gratuity (bonus) for the restaurant', 'vikrestaurants');
				break;

			case 'VRINVTIP':
				// @TRANSLATORS: "tip" is intended as a gratuity (bonus) for the restaurant
				$result = _x('Tip', '"tip" is intended as a gratuity (bonus) for the restaurant', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SHAPE_ADDED':
				$result = __('Shape added', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SHAPE_REMOVED':
				$result = __('Shape removed', 'vikrestaurants');
				break;

			case 'VRE_UISVG_N_SHAPES_REMOVED':
				$result = __('%d shapes removed', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SHAPE_SELECTED':
				$result = __('Shape selected', 'vikrestaurants');
				break;

			case 'VRE_UISVG_N_SHAPES_SELECTED':
				$result = __('%d shapes selected', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SHAPE_COPIED':
				$result = __('Shape copied', 'vikrestaurants');
				break;

			case 'VRE_UISVG_N_SHAPES_COPIED':
				$result = __('%d shapes copied', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SHAPE_PASTED':
				$result = __('Shape pasted', 'vikrestaurants');
				break;

			case 'VRE_UISVG_N_SHAPES_PASTED':
				$result = __('%d shapes pasted', 'vikrestaurants');
				break;

			case 'VRE_UISVG_ELEMENT_SAVED':
				$result = __('Element saved', 'vikrestaurants');
				break;

			case 'VRE_UISVG_N_ELEMENTS_SAVED':
				$result = __('%d elements saved', 'vikrestaurants');
				break;

			case 'VRE_UISVG_ELEMENT_RESTORED':
				$result = __('Element restored', 'vikrestaurants');
				break;

			case 'VRE_UISVG_N_ELEMENTS_RESTORED':
				$result = __('%d elements restored', 'vikrestaurants');
				break;

			case 'VRE_UISVG_CLONE_CMD_TITLE':
				$result = __('Clone a shape', 'vikrestaurants');
				break;

			case 'VRE_UISVG_CLONE_CMD_PARAM_KEEP_CLONING':
				$result = __('Keep cloning', 'vikrestaurants');
				break;

			case 'VRE_UISVG_CLONE_CMD_PARAM_AUTO_SELECT':
				$result = __('Auto select', 'vikrestaurants');
				break;

			case 'VRE_UISVG_REMOVE_CMD_TITLE':
				$result = __('Remove shapes', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SEARCH_CMD_PLACEHOLDER':
				$result = __('Type something', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SEARCH_CMD_RESULT':
				$result = __('No table found.', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SEARCH_CMD_TITLE':
				$result = __('Search tables', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SHAPE_MOVED':
				$result = __('Shape moved', 'vikrestaurants');
				break;

			case 'VRE_UISVG_N_SHAPES_MOVED':
				$result = __('%d shapes moved', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SHAPE_RESIZED':
				$result = __('Shape resized', 'vikrestaurants');
				break;

			case 'VRE_UISVG_N_SHAPES_RESIZED':
				$result = __('%d shapes resized', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SHAPE_ROTATED':
				$result = __('Shape rotated', 'vikrestaurants');
				break;

			case 'VRE_UISVG_N_SHAPES_ROTATED':
				$result = __('%d shapes rotated', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SELECT_CMD_TITLE':
				$result = __('Selection', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SELECT_CMD_PARAM_SIMPLE_SELECTION':
				$result = __('Simple selection', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SELECT_CMD_PARAM_REVERSE_SELECTION':
				$result = __('Reverse selection', 'vikrestaurants');
				break;

			case 'VRE_UISVG_NEW_CMD_TITLE':
				$result = __('Add new shapes', 'vikrestaurants');
				break;

			case 'VRE_UISVG_NEW_CMD_PARAM_SHAPE_TYPE':
				$result = __('Shape type', 'vikrestaurants');
				break;

			case 'VRE_UISVG_NEW_CMD_PARAM_SHAPE_TYPE_RECT':
				$result = __('Rectangle', 'vikrestaurants');
				break;

			case 'VRE_UISVG_NEW_CMD_PARAM_SHAPE_TYPE_CIRCLE':
				$result = __('Circle', 'vikrestaurants');
				break;

			case 'VRE_UISVG_NEW_CMD_PARAM_SHAPE_TYPE_IMAGE':
				$result = __('Image', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SEARCH':
				$result = __('Search', 'vikrestaurants');
				break;

			case 'VRE_UISVG_NO_MEDIA':
				$result = __('No media found.', 'vikrestaurants');
				break;

			case 'VRE_UISVG_UPLOAD_MEDIA':
				$result = __('Upload Media', 'vikrestaurants');
				break;

			case 'VRE_UISVG_NO_REDO':
				$result = __('Nothing to redo', 'vikrestaurants');
				break;

			case 'VRE_UISVG_NO_UNDO':
				$result = __('Nothing to undo', 'vikrestaurants');
				break;

			case 'VRE_UISVG_CANVAS':
				$result = __('Canvas', 'vikrestaurants');
				break;

			case 'VRE_UISVG_LAYOUT':
				$result = __('Layout', 'vikrestaurants');
				break;

			case 'VRE_UISVG_WIDTH':
				$result = __('Width', 'vikrestaurants');
				break;

			case 'VRE_UISVG_HEIGHT':
				$result = __('Height', 'vikrestaurants');
				break;

			case 'VRE_UISVG_PROP_SIZE':
				$result = __('Proportional Size', 'vikrestaurants');
				break;

			case 'VRE_UISVG_BACKGROUND':
				$result = __('Background', 'vikrestaurants');
				break;

			case 'VRE_UISVG_NONE':
				$result = __('None', 'vikrestaurants');
				break;

			case 'VRE_UISVG_IMAGE':
				$result = __('Image', 'vikrestaurants');
				break;

			case 'VRE_UISVG_COLOR':
				$result = __('Color', 'vikrestaurants');
				break;

			case 'VRE_UISVG_MODE':
				$result = __('Mode', 'vikrestaurants');
				break;

			case 'VRE_UISVG_REPEAT':
				$result = __('Repeat', 'vikrestaurants');
				break;

			case 'VRE_UISVG_HOR_REPEAT':
				$result = __('Repeat Horizontally', 'vikrestaurants');
				break;

			case 'VRE_UISVG_VER_REPEAT':
				$result = __('Repeat Vertically', 'vikrestaurants');
				break;

			case 'VRE_UISVG_COVER':
				$result = __('Cover', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SHOW_GRID':
				$result = __('Display Grid', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SIZE':
				$result = __('Size', 'vikrestaurants');
				break;

			case 'VRE_UISVG_GRID_SNAP':
				$result = __('Snap to Grid', 'vikrestaurants');
				break;

			case 'VRE_UISVG_GRID_CONSTRAINTS':
				$result = __('Enable constraints', 'vikrestaurants');
				break;

			case 'VRE_UISVG_GRID_CONSTRAINTS_ACCURACY':
				$result = __('Constraints Accuracy', 'vikrestaurants');
				break;

			case 'VRE_UISVG_HIGH':
				$result = __('High', 'vikrestaurants');
				break;

			case 'VRE_UISVG_NORMAL':
				$result = __('Normal', 'vikrestaurants');
				break;

			case 'VRE_UISVG_LOW':
				$result = __('Low', 'vikrestaurants');
				break;

			case 'VRE_UISVG_PROP_SIZE_DESCRIPTION':
				$result = __('When checked, the width and height will have always the same value.', 'vikrestaurants');
				break;

			case 'VRE_UISVG_IMAGE_DESCRIPTION':
				$result = __('Select the background image you need to use from the collection below.', 'vikrestaurants');
				break;

			case 'VRE_UISVG_GRID_SNAP_DESCRIPTION':
				$result = __('Enable this value to align/snap the shapes to the grid.', 'vikrestaurants');
				break;

			case 'VRE_UISVG_GRID_CONSTRAINTS_DESCRIPTION':
				$result = __('Constraints help shapes aligning on the grid.', 'vikrestaurants');
				break;

			case 'VRE_UISVG_GRID_CONSTRAINTS_ACCURACY_DESCRIPTION':
				$result = __('The lower the accuracy, the easier the alignment of the shapes.', 'vikrestaurants');
				break;

			case 'VRE_UISVG_TABLE':
				$result = __('Table', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SHAPE':
				$result = __('Shape', 'vikrestaurants');
				break;

			case 'VRE_UISVG_POSX':
				$result = __('Position X', 'vikrestaurants');
				break;

			case 'VRE_UISVG_POSY':
				$result = __('Position Y', 'vikrestaurants');
				break;

			case 'VRE_UISVG_ROTATION':
				$result = __('Rotation', 'vikrestaurants');
				break;

			case 'VRE_UISVG_ROUNDNESS':
				$result = __('Roundness', 'vikrestaurants');
				break;

			case 'VRE_UISVG_RADIUS':
				$result = __('Radius', 'vikrestaurants');
				break;

			case 'VRE_UISVG_BACKGROUND_COLOR':
				$result = __('Background Color', 'vikrestaurants');
				break;

			case 'VRE_UISVG_FOREGROUND_COLOR':
				$result = __('Foreground Color', 'vikrestaurants');
				break;

			case 'VRE_UISVG_BACKGROUND_IMAGE':
				$result = __('Background Image', 'vikrestaurants');
				break;

			case 'VRE_UISVG_ROUNDNESS_DESCRIPTION':
				$result = __('Roundness is the measure of how closely a shape approaches that of a mathematically perfect circle.', 'vikrestaurants');
				break;

			case 'VRE_UISVG_SAVED':
				$result = __('Saved', 'vikrestaurants');
				break;

			case 'VRE_UISVG_EXIT':
				$result = __('Exit', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.8
			 */

			case 'VRADD':
				$result = __('Add', 'vikrestaurants');
				break;

			case 'VRINFO':
				$result = __('Info', 'vikrestaurants');
				break;

			case 'VRCOUNT':
				$result = __('Count', 'vikrestaurants');
				break;

			case 'VRE_DISPLAY_N_TABLES':
				$result = __('Display %d tables', 'vikrestaurants');
				break;

			case 'VRE_DISPLAY_N_TABLES_1':
				$result = __('Display table', 'vikrestaurants');
				break;

			case 'VRE_ADD_TABLE':
				$result = __('Add Table', 'vikrestaurants');
				break;

			case 'VRE_DESIGN_MAP':
				$result = __('Design map layout', 'vikrestaurants');
				break;

			case 'VRE_CUSTOM_FIELDSET':
				$result = __('Custom', 'vikrestaurants');
				break;

			case 'VRMANAGEREVIEW13':
				$result = __('Unverified', 'vikrestaurants');
				break;

			case 'VRE_REVIEW_CARD_TITLE':
				$result = __('Review', 'vikrestaurants');
				break;

			case 'VRE_MENU':
				$result = __('Menu', 'vikrestaurants');
				break;

			case 'VRE_SECTION':
				$result = __('Section', 'vikrestaurants');
				break;

			case 'VRE_PRODUCT_INC_PRICE':
				$result = __('Product price discount/surcharge.<br />Product default price: <strong>%s</strong>.', 'vikrestaurants');
				break;

			case 'VRE_PRODUCT_INC_PRICE_SHORT':
				$result = __('The product price will be increased/decreased by the amount specified here.', 'vikrestaurants');
				break;

			case 'VRE_MENU_COST_HELP':
				$result = __('In case the menu is selected during the booking process, the total cost of the menu will be added to the bill of the reservation.', 'vikrestaurants');
				break;

			case 'VRE_MEDIA_MANAGER':
				$result = __('Media Manager', 'vikrestaurants');
				break;

			case 'VRE_MANUAL_UPLOAD':
				$result = __('Upload File', 'vikrestaurants');
				break;

			case 'VRE_MEDIA_DRAG_DROP':
				$result = __('or DRAG IMAGES HERE', 'vikrestaurants');
				break;

			case 'VRE_DEF_N_SELECTED':
				$result = __('%d selected', 'vikrestaurants');
				break;

			case 'VRE_DEF_N_SELECTED_1':
				$result = __('1 selected', 'vikrestaurants');
				break;

			case 'VRE_DEF_N_SELECTED_0':
				$result = __('No selection', 'vikrestaurants');
				break;

			case 'VRE_DEF_N_ITEMS_DELETED':
				$result = __('%d records deleted.', 'vikrestaurants');
				break;

			case 'VRE_DEF_N_ITEMS_DELETED_1':
				$result = __('1 record deleted.', 'vikrestaurants');
				break;

			case 'VRE_MISSING_REQ_FIELD':
				$result = __('Missing required field "%s".', 'vikrestaurants');
				break;

			case 'VRE_INVALID_REQ_FIELD':
				$result = __('Invalid field "%s".', 'vikrestaurants');
				break;

			case 'VRE_ADD_DELIVERY_LOCATION':
				$result = __('Add Delivery Location', 'vikrestaurants');
				break;

			case 'VRE_EDIT_DELIVERY_LOCATION':
				$result = __('Edit Delivery Location', 'vikrestaurants');
				break;

			case 'VRE_DELIVERY_LOCATION_TYPE_OTHER':
				$result = __('Other', 'vikrestaurants');
				break;

			case 'VRE_DELIVERY_LOCATION_TYPE_HOME':
				$result = __('Home', 'vikrestaurants');
				break;

			case 'VRE_DELIVERY_LOCATION_TYPE_OFFICE':
				$result = __('Office', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMER22':
				$result = __('Use the same address for delivery', 'vikrestaurants');
				break;

			case 'VRE_DELETE_PERMANENTLY':
				$result = __('Delete Permanently', 'vikrestaurants');
				break;

			case 'VRMANAGETABLE13':
				$result = __('Cluster', 'vikrestaurants');
				break;

			case 'VRMANAGETABLE13_DESC':
				$result = __('The tables defined here are used to create a cluster of tables. All these tables can be merged together in order to receive larger groups of people, that each single table wouldn\'t be able to host.', 'vikrestaurants');
					break;

			case 'VRMANAGEOPERATOR18':
				$result = __('All Reservations', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR18_DESC':
				$result = __('Enable this option in case the operator should be able to see all the reservations. Otherwise only the assigned reservations will be accessible.', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR19':
				$result = __('Self Assignment', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR19_DESC':
				$result = __('When this option is turned on the operator will be able to auto-assign itself to reservations that have no assignment yet.', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR20':
				$result = __('Assigned Rooms', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR20_DESC':
				$result = __('Select all the rooms to which the operator should be able to access. Leave empty to access all the rooms.', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR21':
				$result = __('Assigned Products', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR21_DESC':
				$result = __('The operator will be able to see (within the <b>Kitchen</b> page) only the products that belong to the assigned tags. Leave empty to see all the products.', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU34':
				$result = __('Purchase', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU34_DESC':
				$result = __('In case the dishes of the restaurant can be sold online, turn on this value if you wish to allow the customers to purchase the products assigned to this section. Even if a section is unpublished, customers will still be able to see the section for the purchase.', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU34_DESC_SHORT':
				$result = __('This section is available only during the purchase.', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON12':
				$result = __('Max Usages', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON12_DESC':
				$result = __('It is possible to specify here the maximum number of times that this coupon can be redeemed.', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON13':
				$result = __('Usages per Customer', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON13_DESC':
				$result = __('It is possible to specify here the maximum number of times that the same user can redeem this coupon. In case of restrictions, only registered users are allowed to use the coupon.', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON14':
				$result = __('Total Usages', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON14_DESC':
				$result = __('Indicates how many times the coupon code have been already used.', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT14':
				$result = __('Trusted Customer', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT14_DESC':
				$result = __('When this option is enabled, the payment gateway will be accessible only by those customers with a count of reservations/orders higher than the specified amount.', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY23':
				$result = __('Freedom of Choice', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY23_DESC':
				$result = __('Enable this option to allow each participant of the group to choose its preferred menu. Otherwise the whole group will be forced to book the same menu.', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_GROUP':
				$result = __('- Select Group -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_TYPE':
				$result = __('- Select Type -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_DATE':
				$result = __('- Select Date -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_RATING':
				$result = __('- Select Rating -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_PRODUCT':
				$result = __('- Select Product -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_COUNTRY':
				$result = __('- Select Country -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_DRIVER':
				$result = __('- Select Driver -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_PAYMENT':
				$result = __('- Select Payment -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_RULE':
				$result = __('- Select Rule -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_SEPARATOR':
				$result = __('- Select Separator -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_TABLE':
				$result = __('- Select Table -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_SERVICE':
				$result = __('- Select Service -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_APPLICATION':
				$result = __('- Select Application -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_OPERATOR':
				$result = __('- Select Operator -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_TAG':
				$result = __('- Select Tag -', 'vikrestaurants');
				break;

			case 'VRE_USER_SAVE_BIND_ERR':
				$result = __('An error occurred while trying to save the user. Please, try again.', 'vikrestaurants');
				break;

			case 'VRE_USER_SAVE_CHECK_ERR':
				$result = __('An error occurred. The specified fields are not valid.', 'vikrestaurants');
				break;

			case 'VRE_N_PEOPLE':
				$result = __('%d people', 'vikrestaurants');
				break;

			case 'VRE_N_PEOPLE_1':
				$result = __('1 person', 'vikrestaurants');
				break;

			case 'VRE_N_PEOPLE_0':
				$result = __('No people', 'vikrestaurants');
				break;

			case 'VRE_TKRES_PREP_TIME_HINT':
				$result = __('It seems that there are several meals to prepare at this check-in time. It is suggested to start preparing the dishes of this order at <b>%s</b>.', 'vikrestaurants');
				break;

			case 'VRECHECKOUTEXT':
				$result = __('Check-out: %s (%s)', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWTAGS':
				$result = __('VikRestaurants - Tags', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWTAG':
				$result = __('VikRestaurants - New Tag', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITTAG':
				$result = __('VikRestaurants - Edit Tag', 'vikrestaurants');
				break;

			case 'VRTAGS':
				$result = __('Tags', 'vikrestaurants');
				break;

			case 'VRGOTOTAGS':
				$result = __('Manage Tags', 'vikrestaurants');
				break;

			case 'VRTAGDUPLERR':
				$result = __('A tag with the specified name already exists.', 'vikrestaurants');
				break;

			case 'VRE_CONFIG_PARAM':
				$result = __('Param', 'vikrestaurants');
				break;

			case 'VRE_CONFIG_SETTING':
				$result = __('Setting', 'vikrestaurants');
				break;

			case 'VRE_LANG_ORIGINAL':
				$result = __('Original', 'vikrestaurants');
				break;

			case 'VRE_SAVE_TRX_DEF_LANG':
				$result = __('You are saving a translation for the default language of this website (%s). Do you want to proceed?', 'vikrestaurants');
				break;

			case 'VRE_LANG_HINT_TOOLTIP':
				$result = __('Click this button to search for any translations that have been already made for other records.', 'vikrestaurants');
				break;

			case 'VRE_LANG_HINT_SINGLE':
				$result = __('The "%s" translation has been found.<br />Would you like to use it?', 'vikrestaurants');
				break;

			case 'VRE_LANG_HINT_MULTI':
				$result = __('Different translations have been found. Please select the one you would like to use.', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA12':
				$result = __('Image Size', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA13':
				$result = __('File Size', 'vikrestaurants');
				break;

			case 'VRMEDIAPROTECTEDERR':
				$result = __('The selected [%s] file is protected. It is not possible to delete or rename protected files.', 'vikrestaurants');
				break;

			case 'VRE_DISPLAY_N_PRODUCTS':
				$result = __('Display %d products', 'vikrestaurants');
				break;

			case 'VRE_DISPLAY_N_PRODUCTS_1':
				$result = __('Display product', 'vikrestaurants');
				break;

			case 'VRE_N_VARIATIONS':
				$result = __('%d variations', 'vikrestaurants');
				break;

			case 'VRE_N_VARIATIONS_1':
				$result = __('1 variation', 'vikrestaurants');
				break;

			case 'VRE_N_VARIATIONS_0':
				$result = __('No variation', 'vikrestaurants');
				break;

			case 'VRE_ADD_PRODUCT':
				$result = __('Add Product', 'vikrestaurants');
				break;

			case 'VRE_EDIT_PRODUCT':
				$result = __('Edit Product', 'vikrestaurants');
				break;

			case 'VRE_ADD_VARIATION':
				$result = __('Add Product Variation', 'vikrestaurants');
				break;

			case 'VRE_EDIT_VARIATION':
				$result = __('Edit Product Variation', 'vikrestaurants');
				break;

			case 'VRE_N_TOPPINGS':
				$result = __('%d toppings', 'vikrestaurants');
				break;

			case 'VRE_N_TOPPINGS_1':
				$result = __('1 topping', 'vikrestaurants');
				break;

			case 'VRE_N_TOPPINGS_0':
				$result = __('No topping', 'vikrestaurants');
				break;

			case 'VRE_ADD_TOPPING_GROUP':
				$result = __('Add Topping Group', 'vikrestaurants');
				break;

			case 'VRE_EDIT_TOPPING_GROUP':
				$result = __('Edit Topping Group', 'vikrestaurants');
				break;

			case 'VRE_N_RESERVATIONS':
				$result = __('%d reservations', 'vikrestaurants');
				break;

			case 'VRE_N_RESERVATIONS_1':
				$result = __('1 reservation', 'vikrestaurants');
				break;

			case 'VRE_N_RESERVATIONS_0':
				$result = __('No reservation', 'vikrestaurants');
				break;

			case 'VRE_N_ORDERS':
				$result = __('%d orders', 'vikrestaurants');
				break;

			case 'VRE_N_ORDERS_1':
				$result = __('1 order', 'vikrestaurants');
				break;

			case 'VRE_N_ORDERS_0':
				$result = __('No order', 'vikrestaurants');
				break;

			case 'VRE_N_PRODUCTS_SOLD':
				$result = __('%d products sold', 'vikrestaurants');
				break;

			case 'VRE_N_PRODUCTS_SOLD_1':
				$result = __('1 product sold', 'vikrestaurants');
				break;

			case 'VRE_N_DAYS':
				$result = __('%d days', 'vikrestaurants');
				break;

			case 'VRE_N_DAYS_1':
				$result = __('1 day', 'vikrestaurants');
				break;

			case 'VRE_N_WEEKS':
				$result = __('%d weeks', 'vikrestaurants');
				break;

			case 'VRE_N_WEEKS_1':
				$result = __('1 week', 'vikrestaurants');
				break;

			case 'VRE_N_MONTHS':
				$result = __('%d months', 'vikrestaurants');
				break;

			case 'VRE_N_MONTHS_1':
				$result = __('1 month', 'vikrestaurants');
				break;

			case 'VRE_N_YEARS':
				$result = __('%d years', 'vikrestaurants');
				break;

			case 'VRE_N_YEARS_1':
				$result = __('1 year', 'vikrestaurants');
				break;

			case 'VRORDERINVDUE':
				$result = __('Due', 'vikrestaurants');
				break;

			case 'VRORDERDUE':
				$result = __('Due: %s', 'vikrestaurants');
				break;

			case 'VRORDERDEP':
				$result = __('Deposit: %s', 'vikrestaurants');
				break;

			case 'VRORDERDEPNOTPAID':
				$result = __('The deposit have not been paid online through one of the available payment gateways. Make sure that you effectively received that amount before collecting the remaining balance.', 'vikrestaurants');
				break;

			case 'VRORDERTOTPAID':
				$result = __('Total Paid', 'vikrestaurants');
				break;

			case 'VRORDERPAID':
				$result = __('Paid', 'vikrestaurants');
				break;

			case 'VRORDERPAID_HELP':
				$result = __('Check this option if you received the deposit using a different method of payment (e.g. via bank transfer).', 'vikrestaurants');
				break;

			case 'VRORDERBILLCLOSED_HELP':
				$result = __('The bill should be closed in the moment you receive the remaining balance. Only the reservations with closed bills are considered while generating the statistics of the revenue.', 'vikrestaurants');
				break;

			case 'VRORDERBILLDEPOSIT_HELP':
				$result = __('This is the deposit that the customer should have been left in order to confirm the reservation.', 'vikrestaurants');
				break;

			case 'VRMANAGETKORDDISC2_HELP':
				$result = __('The total net amount doesn\'t include any additional charges, such as the delivery charge or the tip.', 'vikrestaurants');
				break;

			case 'VREMPTYCART':
				$result = __('The cart is empty.', 'vikrestaurants');
				break;

			case 'VRMANAGETKSTOCK3_HELP':
				$result = __('The initial quantity of items available in stock. Any refills have to be made from the "<b>Stocks Overview</b>" section, accessible through the "<b>Take-Away Orders</b>" page.', 'vikrestaurants');
				break;

			case 'VRMANAGETKSTOCK4_HELP':
				$result = __('You will receive a notification via e-mail every time the remaining quantity of the product will be lower than the specified amount.', 'vikrestaurants');
				break;

			case 'VRMANAGETKSTOCK5':
				$result = __('Enable Stock', 'vikrestaurants');
				break;

			case 'VRMANAGETKSTOCK5_HELP':
				$result = __('Enable this option to have an independent stock for this variation. Otherwise the stocks will be based on the remaining quantity of the parent product.', 'vikrestaurants');
				break;

			case 'VRE_OPTION_STOCK_DISABLED_HELP':
				$result = __('The stocks of the variation will be based on the remaining quantity of the parent product.', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_DRIVER_ERROR':
				$result = __('The specified payment file is not valid.', 'vikrestaurants');
				break;

			case 'VRE_AJAX_GENERIC_ERROR':
				$result = __('An error occurred! Please try again.', 'vikrestaurants');
				break;

			case 'VRE_CONFIRM_MESSAGE_UNSAVE':
				$result = __('Your changes will be lost if you don\'t save them. Do you want to proceed?', 'vikrestaurants');
				break;

			case 'VRE_GOOGLE_API_KEY_ERROR':
				$result = __('An error with the Google API key occurred. Please make sure that the specified API Key is set and valid. Otherwise check that <b>Maps JavaScript API</b> and <b>Geocoding API</b> libraries are enabled on your Google console.', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDSLEGEND2':
				$result = __('Type Settings', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDSLEGEND3':
				$result = __('Rule Settings', 'vikrestaurants');
				break;

			case 'VRMULTIPLE':
				$result = __('Multiple', 'vikrestaurants');
				break;

			case 'VRSUFFIXCLASS':
				$result = __('Class Suffix', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMF5_DESC':
				$result = __('Insert here the URL that will be opened when clicking the label of this field. In example, you can insert here the link to your "Terms & Conditions" article.', 'vikrestaurants');
				break;

			case 'VRCUSTFIELD':
				$result = __('Custom Field', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDRULE7':
				$result = __('City', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDRULE8':
				$result = __('State/Province', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDRULE9':
				$result = __('Pickup', 'vikrestaurants');
				break;

			case 'VRTOPPING':
				$result = __('Topping', 'vikrestaurants');
				break;

			case 'VRMENUATTR':
				$result = __('Menu Attribute', 'vikrestaurants');
				break;

			case 'VRE_LOG_OLD_VALUE':
				$result = __('Previous', 'vikrestaurants');
				break;

			case 'VRE_LOG_NEW_VALUE':
				$result = __('Current', 'vikrestaurants');
				break;

			case 'VRE_LOG_RES_NUM':
				$result = __('Reservation #%d', 'vikrestaurants');
				break;

			case 'VRE_LOG_ORDER_NUM':
				$result = __('Order #%d', 'vikrestaurants');
				break;

			case 'VRE_OPERATOR_LOGS_TRASH_MSG':
				$result = __('Delete all logs older than:', 'vikrestaurants');
				break;

			case 'VRE_APIUSER_EMPTY_IPS_NOTICE':
				$result = __('Any IP addresses will be accepted.', 'vikrestaurants');
				break;

			case 'VRINVGENERATEDMSG':
				$result = __('%d invoices generated.', 'vikrestaurants');
				break;

			case 'VRINVGENERATEDMSG_1':
				$result = __('Invoice generated.', 'vikrestaurants');
				break;

			case 'VRINVMAILSENT':
				$result = __('%d customers notified via mail.', 'vikrestaurants');
				break;

			case 'VRINVMAILSENT_1':
				$result = __('Customer notified via mail.', 'vikrestaurants');
				break;

			case 'VRCUSTOMERSMSSENT':
				$result = __('%d customers notified via SMS.', 'vikrestaurants');
				break;

			case 'VRCUSTOMERSMSSENT_1':
				$result = __('Customer notified via SMS.', 'vikrestaurants');
				break;

			case 'VRCUSTOMERSMSSENT_0':
				$result = __('No customers have been notified via SMS.', 'vikrestaurants');
				break;

			case 'VRINVOICEFIELDSET4':
				$result = __('Contents', 'vikrestaurants');
				break;

			case 'VRINVOICEFIELDSET5':
				$result = __('Margins', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE12':
				$result = __('Font Family', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE13':
				$result = __('Body Font Size', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE14':
				$result = __('Show Header', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE15':
				$result = __('Header Title', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE16':
				$result = __('Header Font Size', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE17':
				$result = __('Show Footer', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE18':
				$result = __('Footer Font Size', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE19':
				$result = __('Margin Top', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE20':
				$result = __('Margin Bottom', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE21':
				$result = __('Margin Left', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE22':
				$result = __('Margin Right', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE23':
				$result = __('Margin Header', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE24':
				$result = __('Margin Footer', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE2_HELP':
				$result = __('The system will generate an invoice for all the orders that have the check-in within the selected month and year.', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE3_HELP':
				$result = __('Turn on this option to overwrite any existing invoices already generated for the matching order. Leave it unchecked to generate the invoices only for the new orders.', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE4_HELP':
				$result = __('Insert here the invoices progressive number and an optional suffix. The number will be automatically increased by one every time a NEW invoice is generated. When updating an existing invoice, its default number will be re-used, unless it has been manually changed. In example, the resulting invoice number will be equals to <em>1/2020</em> or <em>1/XYZ</em>.', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE5_HELP':
				$result = __('Choose whether the date of the invoice should be equals to the check-in date or to the current date. When updating an existing invoice, it is possible to select a different date from the apposite calendar.', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE6_HELP':
				$result = __('This field can be used to include some legal information within the invoice. The specified text will be reported below the company logo (top-left side of the first page).', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE7_HELP':
				$result = __('Turn on this option to automatically send the generated invoices to the customers. The invoice will be sent via e-mail to the address specified during the purchase.', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE11_HELP':
				$result = __('All the images within the invoice will be scaled by the specified percentage amount. The higher the value, the smaller the images. Use <em>100%</em> to leave the images at their original sizes.', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE15_HELP':
				$result = __('A title to be displayed on the top of the page. In case the text is not visible, it is needed to increase the <b>Margin Top</b> parameter until the title appears.', 'vikrestaurants');
				break;

			case 'VRMANAGEINVOICE17_HELP':
				$result = __('The footer only displays the number of the page (e.g. 1/2). In case the text is not visible, it is needed to increase the <b>Margin Bottom</b> parameter until the footer appears.', 'vikrestaurants');
				break;

			case 'VRGENERATEINVOICESTXT':
				$result = __('Generate an invoice for the selected reservations.<br />In case a reservation already owns an invoice, it WILL NOT be overwritten.', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEARRIVED':
				$result = __('Arrived', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEDESCARRIVED':
				$result = __('Used to flag the customers of the reservation as arrived at the restaurant.', 'vikrestaurants');
				break;

			case 'VRRESCODESRULECLOSEBILL':
				$result = __('Close Bill', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEDESCCLOSEBILL':
				$result = __('Automatically closes the bill.', 'vikrestaurants');
				break;

			case 'VRRESCODESRULELEAVE':
				$result = __('Leave', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEDESCLEAVE':
				$result = __('Automatically updates the stay time of the reservation to immediately free the table.', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEINVOICE':
				$result = __('Invoice', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEDESCINVOICE':
				$result = __('Automatically issues an invoice for the specified bill/order (only if the grand total is higher than 0). The invoice will be also sent to the e-mail address of the customer, if specified.', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEPREPARING':
				$result = __('Preparing', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEDESCPREPARING':
				$result = __('As long as an order is under preparation, it will be kept within the <b>Current</b> widget of the dashboard, even if it shouldn\'t be displayed anymore.', 'vikrestaurants');
				break;

			case 'VRRESCODESRULECOMPLETED':
				$result = __('Completed', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEDESCCOMPLETED':
				$result = __('Immediately hides the order from the <b>Current</b> widget of the dashboard in case it has been delivered or picked.', 'vikrestaurants');
				break;

			case 'VRRESCODESRULECOOKING':
				$result = __('Cooking', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEDESCCOOKING':
				$result = __('As long as a dish is under preparation, it will be kept within the <b>Kitchen</b> widget. In addition, customers won\'t be able to edit/remove anymore a dish when it is being cooked.', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEPREPARED':
				$result = __('Prepared', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEDESCPREPARED':
				$result = __('Waiters can see a list of dishes flagged with <b>Prepared</b> status, so that they can pick and deliver them to the customers.', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEWAITER':
				$result = __('Waiter', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEDESCWAITER':
				$result = __('This rule can be used to dispatch a dish, which won\'t be displayed anymore within the <b>Kitchen</b> widget.', 'vikrestaurants');
				break;

			case 'VRSHOWCUSTFIELDS':
				$result = __('Show Custom Fields', 'vikrestaurants');
				break;

			case 'VRHIDECUSTFIELDS':
				$result = __('Hide Custom Fields', 'vikrestaurants');
				break;

			case 'VRSHOWNOTES':
				$result = __('Show Notes', 'vikrestaurants');
				break;

			case 'VRHIDENOTES':
				$result = __('Hide Notes', 'vikrestaurants');
				break;

			case 'VRORDERSTATUSES':
				$result = __('Order Status History', 'vikrestaurants');
				break;

			case 'VRE_NOTES_AUTO_SAVE':
				$result = __('The notes are automatically saved 3 seconds after the time you stop typing.', 'vikrestaurants');
				break;

			case 'VRE_NOTES_MISSING':
				$result = __('No reservation notes.', 'vikrestaurants');
				break;

			case 'VRE_MAX_N':
				$result = __('Max: %d', 'vikrestaurants');
				break;

			case 'VRE_MIN_N':
				$result = __('Min: %d', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL6_DESC':
				$result = __('The maximum number of times that this deal can be applied during the purchase.', 'vikrestaurants');
				break;

			case 'VRRESERVATIONSTATUSCLOSURE':
				$result = __('Closure', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION26':
				$result = __('Re-open', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION26_HELP':
				$result = __('If you enable this option, the current closure will be permanently <b>DELETED</b> after saving the changes.', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION27':
				$result = __('Arrives %s', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION28':
				$result = __('Leaves %s', 'vikrestaurants');
				break;

			case 'VRMANAGETKCARTSTOCK_HELP':
				$result = __('The number reported next to the product name indicates the remaining quantity available in stock.', 'vikrestaurants');
				break;

			case 'VRMANAGETKCARTSTOCK_VAR_HELP':
				$result = __('The number reported next to the product name indicates the remaining quantity available in stock. That value might be different depending on the product variation.', 'vikrestaurants');
				break;

			case 'VRMANAGETKCARTSTOCK_DIALOG':
				$result = __('The selected quantity of items is not available in stock. Click <b>Use available</b> to hold the quantity that doesn\'t exceed the total number of remaining items. Click <b>Go ahead</b> to ignore this limitation.', 'vikrestaurants');
				break;

			case 'VRMANAGETKCARTSTOCK_DIALOG_EMPTY':
				$result = __('The selected item is no more available in stock. Click the <b>Go ahead</b> button if you want to add it into the cart anyway.', 'vikrestaurants');
				break;

			case 'VRMANAGETKCARTSTOCK_DIALOG_BTN1':
				$result = __('Use available', 'vikrestaurants');
				break;

			case 'VRMANAGETKCARTSTOCK_DIALOG_BTN2':
				$result = __('Go ahead', 'vikrestaurants');
				break;

			case 'VRTKSTOCK_OVERRIDE_CHAIN':
				$result = __('This product shares the availability with other variations of the same parent product.', 'vikrestaurants');
				break;

			case 'VRTK_ADDR_ROUTE_NOTES':
				$result = __('The address is <b>%s</b> away. It should take you <b>%s</b> to arrive.', 'vikrestaurants');
				break;

			case 'VRTK_ADDR_ROUTE_START':
				$result = __('It is suggested to leave at <b>%s</b> in order to arrive on time.', 'vikrestaurants');
				break;

			case 'VRSTATSSHIFTLUNCH':
				$result = __('Lunch', 'vikrestaurants');
				break;

			case 'VRSTATSSHIFTDINNER':
				$result = __('Dinner', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_WEEKRES_TITLE':
				$result = __('Week Reservations', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_WEEKRES_DESC':
				$result = __('The total number of received reservations/orders for each day of the selected week (by default, the last 7 days will be used).', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_WEEKRES_INITIAL_DATE_FIELD':
				$result = __('Initial Date', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_WEEKRES_INITIAL_DATE_FIELD_HELP':
				$result = __('Every time you start a new session, the widget will take the selected date as reference to load the statistics. By default, the current date is used.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_WEEKRES_INITIAL_DATE_OPT_LAST_WEEK':
				$result = __('Last week', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_WEEKRES_INITIAL_DATE_OPT_PREV_DAY':
				$result = __('Yesterday', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_WEEKRES_INITIAL_DATE_OPT_TODAY':
				$result = __('Today', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_WEEKRES_INITIAL_DATE_OPT_TOMORROW':
				$result = __('Tomorrow', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_WEEKREVENUE_TITLE':
				$result = __('Week Revenue', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_WEEKREVENUE_DESC':
				$result = __('The total revenue earned in each day of the selected week (by default, the last 7 days will be used).', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_OCCUPANCY_TITLE':
				$result = __('Occupancy', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_OCCUPANCY_DESC':
				$result = __('Calculates the total occupancy of the restaurant for the specified date and time. This is an approximate result based on the maximum number of available seats.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_STATUSRES_TITLE':
				$result = __('Statuses', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_STATUSRES_DESC':
				$result = __('Displays a doughnut chart containing the total count of reservations for each supported status.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_STATUSRES_INITIAL_RANGE_FIELD':
				$result = __('Initial Range', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_STATUSRES_INITIAL_RANGE_FIELD_HELP':
				$result = __('Every time you start a new session, the widget will take the selected range as reference to load the statistics. By default, the current month is used.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_STATUSRES_INITIAL_RANGE_OPT_ALL':
				$result = __('All', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_STATUSRES_INITIAL_RANGE_OPT_CURR_MONTH':
				$result = __('Current month', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_STATUSRES_INITIAL_RANGE_OPT_PREV_MONTH':
				$result = __('Previous month', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_STATUSRES_INITIAL_RANGE_OPT_LAST_N_MONTHS':
				$result = __('Last %d months', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_STATUSRES_INITIAL_RANGE_OPT_CURR_YEAR':
				$result = __('Current year', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TREND_TITLE':
				$result = __('Trend', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TREND_DESC':
				$result = __('Displays a line chart showing, month by month, either the total amount earned and the total number of reservations received.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TREND_INITIAL_RANGE_FIELD':
				$result = __('Initial Range', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TREND_INITIAL_RANGE_FIELD_HELP':
				$result = __('Every time you start a new session, the widget will take the selected range as reference to load the statistics. By default, the last 6 months are taken.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TREND_INITIAL_RANGE_LAST_N_MONTHS':
				$result = __('Last %d months', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TREND_VALUE_TYPE_FIELD':
				$result = __('Value Type', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TREND_VALUE_TYPE_FIELD_HELP':
				$result = __('Choose whether the chart should calculate the total count of received reservations or the total amount earned.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TREND_VALUE_TYPE_OPT_EARNING':
				$result = __('Total earning', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TREND_VALUE_TYPE_OPT_COUNT':
				$result = __('Reservations count', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TREND_VALUE_TYPE_OPT_GUESTS':
				$result = __('Guests count', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_OVERALL_TITLE':
				$result = __('Overall', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_OVERALL_DESC':
				$result = __('Calculates either the overall total earning and the total number of reservations/orders.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TREND_SHOW_PEOPLE_FIELD':
				$result = __('Show People', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TREND_SHOW_PEOPLE_FIELD_HELP':
				$result = __('Turn on this option to let the widget displays the overall count of guests.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_AVGDAILY_TITLE':
				$result = __('Daily Average', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_AVGDAILY_DESC':
				$result = __('Calculates the average number of received reservations/orders for each day of the week.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_SERVICE_TITLE':
				$result = __('Service', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_SERVICE_DESC':
				$result = __('Calculates the total number of times that the delivery or pickup services have been selected for the purchase, within the specified interval.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_BESTPRODUCTS_TITLE':
				$result = __('Products Ranking', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_BESTPRODUCTS_DESC':
				$result = __('Displays a bar chart showing the top 10 products that have been sold more.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_ROG_TITLE':
				$result = __('Rate of Growth', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_ROG_DESC':
				$result = __('Calculates the rate of growth between the selected month and the previous one.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_ROG_MONTH_FIELD':
				$result = __('Month', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_ROG_YEAR_FIELD':
				$result = __('Year', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_ROG_PROP_FIELD':
				$result = __('Proportional', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_ROG_PROP_FIELD_HELP':
				$result = __('When enabled, the total earning of the month will be proportionally estimated depending on the money already earned and the remaining days (applies only for the current month).', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_CUSTOMERS_TITLE':
				$result = __('Customers', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_CUSTOMERS_DESC':
				$result = __('Calculates the total number of new and returning customers within the selected month. The statistics are based on the <b>creation date</b> of the reservations.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_CUSTOMERS_NEW':
				$result = __('New customers', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_CUSTOMERS_RETURNING':
				$result = __('Returning customers', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_RESERVATIONS_TITLE':
				$result = __('Reservations', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_RESERVATIONS_DESC':
				$result = __('Displays a widget containing all the following lists: latest reservations, incoming reservations and current reservations.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_RESERVATIONS_LATEST_FIELD':
				$result = __('Latest', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_RESERVATIONS_LATEST_FIELD_HELP':
				$result = __('Turn on this option to display the list containing the latest reservations.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_RESERVATIONS_INCOMING_FIELD':
				$result = __('Incoming', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_RESERVATIONS_INCOMING_FIELD_HELP':
				$result = __('Turn on this option to display the list containing the incoming reservations.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_RESERVATIONS_CURRENT_FIELD':
				$result = __('Current', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_RESERVATIONS_CURRENT_FIELD_HELP':
				$result = __('Turn on this option to display the list containing the current reservations.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_ORDERS_TITLE':
				$result = __('Orders', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_ORDERS_DESC':
				$result = __('Displays a widget containing all the following lists: latest orders, incoming orders and current orders.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_OVERVIEW_TITLE':
				$result = __('Overview', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_OVERVIEW_DESC':
				$result = __('This widget can be used to see in real time the availability of the tables for all the hours of a certain day. By default, the current day is taken.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_OVERVIEW_INTERVALS_FIELD':
				$result = __('Intervals', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_OVERVIEW_INTERVALS_OPT':
				$result = __('%d minutes', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_KITCHEN_TITLE':
				$result = __('Kitchen', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_KITCHEN_DESC':
				$result = __('Displays a wall of bills containing all the ordered dishes, grouped by table.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_KITCHEN_OUTGOING_COURSES':
				$result = __('Outgoing Courses', 'vikrestaurants');
				break;

			case 'VRE_ADD_WIDGET':
				$result = __('Add Widget', 'vikrestaurants');
				break;

			case 'VRE_EDIT_WIDGET':
				$result = __('Edit Widget', 'vikrestaurants');
				break;

			case 'VRE_ADD_POSITION':
				$result = __('Add Position', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_NAME':
				$result = __('Name', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_NAME_DESC':
				$result = __('Enter an optional name to identify the widget. If not specified, the default widget title will be used.', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_CLASS':
				$result = __('Widget', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_CLASS_DESC':
				$result = __('Select the type of widget you wish to display in the statistics view.', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_SELECT_CLASS':
				$result = __('- Select Widget -', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_POSITION':
				$result = __('Position', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_POSITION_DESC':
				$result = __('The position block in which the widget will be displayed.', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_POSITION_ADD_HELP':
				$result = __('Enter the alias of the position you wish to create. The alias must be unique and can contain only letters, numbers, dashes and underscores.', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_POSITION_EXISTS_ERR':
				$result = __('The specified position already exists! Please choose a new one.', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_SELECT_POSITION':
				$result = __('- Select Position -', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_SIZE':
				$result = __('Size', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_SIZE_DESC':
				$result = __('Force a specific size to make the widget wider or shorter. Leave empty to let the widget automatically takes the remaining space.', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_SIZE_OPT_DEFAULT':
				$result = __('- Default -', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_SIZE_OPT_EXTRA_SMALL':
				$result = __('Extra Small', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_SIZE_OPT_SMALL':
				$result = __('Small', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_SIZE_OPT_NORMAL':
				$result = __('Normal', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_SIZE_OPT_LARGE':
				$result = __('Large', 'vikrestaurants');
				break;

			case 'VRE_WIDGET_SIZE_OPT_EXTRA_LARGE':
				$result = __('Extra Large', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWDASHBOARDRS':
				$result = __('VikRestaurants - Dashboard Restaurant', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWDASHBOARDTK':
				$result = __('VikRestaurants - Dashboard Take-Away', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGGLOBSECTION4':
				$result = __('Google Maps', 'vikrestaurants');
				break;

			case 'VRCONFIGFIELDSETFOOD':
				$result = __('Food', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG84':
				$result = __('Places API', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG85':
				$result = __('Directions API', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG86':
				$result = __('Maps Static API', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG87':
				$result = __('Min Check-in Date', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG88':
				$result = __('Max Check-in Date', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG89':
				$result = __('Ask for Deposit', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG90':
				$result = __('Accept Cancellation Within', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG91':
				$result = __('Self-Confirmation', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG92':
				$result = __('Safe Distance', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG93':
				$result = __('Distance Factor', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG94':
				$result = __('Allow Courses Ordering', 'vikrestaurants');
				break;

			case 'VROPTIONATREST':
				$result = __('At the restaurant', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG55_HELP':
				$result = __('Insert here your <b>Google API Key</b>. The API Key is mandatory for the usage of certain features of the program, such as the <em>Delivery Areas</em>.<br /><b>Maps JavaScript API</b> and <b>Geocoding API</b> libraries must be turned on from your Google console.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG84_HELP':
				$result = __('Enable this option in order to support the Google Auto-complete feature when typing an address.<br /><b>Places API</b> library must be turned on from your Google console.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG85_HELP':
				$result = __('Enable this option to let the system calculates the distance and the travel time between the address of the restaurant and the address of the customers.<br /><b>Directions API</b> library must be turned on from your Google console.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG86_HELP':
				$result = __('Enable this service to fetch a preview image of the delivery addresses provided by the customers.<br /><b>Maps Static API</b> library must be turned on from your Google console.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG87_HELP':
				$result = __('The minimum number of days required to complete a reservation in advance. In example, by selecting "1 day", the first date available will be one day after the current one (tomorrow).', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG88_HELP':
				$result = __('The number of days from now on for which it will be possible to reserve a table. In example, by selecting "1 week", it will be possible to select a check-in date between today and the next 7 days.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG89_HELP':
				$result = __('It is possible to select here the minimum number of people for which the deposit should be asked.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG90_HELP':
				$result = __('If specified, cancellation requests will be accepted within N minutes since the date time creation of the reservation/order.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG91_HELP':
				$result = __('Enable this option to allow the customers to self-confirm their reservations/orders through the link received via e-mail. The self-confirmation is used only in case the reservation/order doesn\'t require a payment.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG92_HELP':
				$result = __('The containment measures of COVID-19 require that a certain distance is maintained between people who are not part of the same family. When this option is enabled, the system will ask to the customers whether all the members of the group belong to the same family. If not, the system will search for larger tables so that the distance can be maintained.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG93_HELP':
				$result = __('It is possible to define here the factor that will be multiplied by the number of selected people in order to search for larger tables. It is suggested to specify a value between 1,5 and 3.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG94_HELP':
				$result = __('When enabled, the customers will be able to directly order the dishes through the website. Courses can be ordered from the summary page of the reservation, once the status will be confirmed. It is also possible to allow the ordering only when the group actually arrives at the restaurant. This can be accomplished by assigning the <b>Arrived</b> code to the reservation. The ordering of the dishes will be allowed as long as the bill is open.<br />In case the reservation has been assigned to one or more menus, it will be possible to order only the dishes that belong to those menus. Otherwise, all the menus available for the check-in date can be used for ordering.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK34':
				$result = __('Max Preparation Slots', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK35':
				$result = __('Min. Date', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK36':
				$result = __('Max. Date', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK37':
				$result = __('Time Selection', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK38':
				$result = __('Pre-Order', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK34_HELP':
				$result = __('The maximum number of slots to check by going backward in case the ordered meals cannot be booked for the selected date and time. All the exceeding meals will fill the previous time slots recursively, as long as they can fit and until this limit is reached.<br />The selected option indicates the number of time intervals that you can allow for preparing the meals in advance. The higher this amount is, the greater the possibility of receiving more simultaneous orders will be.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK35_HELP':
				$result = __('The minimum number of days required to complete a booking in advance. In example, by selecting "1 day", the first date available will be one day after the current one (tomorrow).', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK36_HELP':
				$result = __('The number of days from now on for which it will be possible to place an order. In example, by selecting "1 week", it will be possible to select a check-in date between today and the next 7 days.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK37_HELP':
				$result = __('Turn on this option if you wish to display the times dropdown also within the menus list page. This is useful in case you have different menus available on the shifts of the same day.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK38_HELP':
				$result = __('When this option is enabled, orders will be accepted only for shifts that are actually in the future. In example, when the restaurant is open, it won\'t be possible to receive orders for the current shift.', 'vikrestaurants');
				break;

			case 'VROPLOGRESTAURANTDELETE':
				$result = __('The operator deleted a restaurant reservation.', 'vikrestaurants');
				break;

			case 'VROPLOGTAKEAWAYDELETE':
				$result = __('The operator deleted a take-away order.', 'vikrestaurants');
				break;

			case 'VRE_UPDATE_PROGRAM_WAIT_MESSAGE':
				$result = __('It may take a few minutes to completion.<br />Please wait without leaving the page or closing the browser.', 'vikrestaurants');
				break;

			case 'VRE_ORDER_EXPORT_DRIVER_ICS':
				$result = __('ICS - iCalendar', 'vikrestaurants');
				break;

			case 'VRE_ORDER_EXPORT_DRIVER_ICS_DESC':
				$result = __('The Internet Calendaring and Scheduling Core Object Specification (iCalendar) is a MIME type which allows users to store and exchange calendaring and scheduling information such as events, to-dos, journal entries, and free/busy information.', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_ICS_PAST_DATES_FIELD':
				$result = __('Include Past Events', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_ICS_PAST_DATES_FIELD_HELP':
				$result = __('Enable this option if you wish to keep all the existing reservations within the calendar. When disabled, reservations older than the current month won\'t be included.', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_ICS_SUBJECT_FIELD':
				$result = __('Subject', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_ICS_SUBJECT_FIELD_HELP':
				$result = __('An optional subject to use instead of the default one. It is possible to include one of the following placeholders: {customer}, {people} and {service}.', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_ICS_REMINDER_FIELD':
				$result = __('Reminder', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_ICS_REMINDER_FIELD_HELP':
				$result = __('The minutes in advance since the event date time for which the alert will be triggered.', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_ICS_REMINDER_OPT_NONE':
				$result = __('None', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_ICS_REMINDER_OPT_EVENT_TIME':
				$result = __('At the event time', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_ICS_REMINDER_OPT_N_MIN':
				$result = __('%d minutes before', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_ICS_REMINDER_OPT_N_HOURS':
				$result = __('%d hours before', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_ICS_REMINDER_OPT_N_HOURS_1':
				$result = __('1 hour before', 'vikrestaurants');
				break;

			case 'VRE_ORDER_EXPORT_DRIVER_CSV_DESC':
				$result = __('CSV is a common data exchange format that is widely supported by consumer, business, and scientific applications.', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_CSV_CONFIRMED_STATUS_FIELD':
				$result = __('Confirmed Status', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_CSV_CONFIRMED_STATUS_FIELD_HELP':
				$result = __('Enable this option if you want to download only the reservations that has been CONFIRMED.', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_CSV_USE_ITEMS_FIELD':
				$result = __('Use Items', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_CSV_USE_ITEMS_FIELD_HELP':
				$result = __('Enable this option if you wish to include the items (meals) assigned to the reservations within the CSV.', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_CSV_DELIMITER_FIELD':
				$result = __('Delimiter', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_CSV_DELIMITER_FIELD_HELP':
				$result = __('The selected character will be used as delimiter between the CSV fields. It is possible to use a comma (,) or a semicolon (;).', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_CSV_DELIMITER_FIELD_OPT_COMMA':
				$result = __('Comma', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_CSV_DELIMITER_FIELD_OPT_SEMICOLON':
				$result = __('Semicolon', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_CSV_ENCLOSURE_FIELD':
				$result = __('Enclosure', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_CSV_ENCLOSURE_FIELD_HELP':
				$result = __('The selected character will be used as enclosure to wrap the separated fields. It is possible to use a double quote (") or a single quote (\').', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_CSV_ENCLOSURE_FIELD_OPT_DOUBLE_QUOTE':
				$result = __('Double Quote', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_DRIVER_CSV_ENCLOSURE_FIELD_OPT_SINGLE_QUOTE':
				$result = __('Single Quote', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_OFFCC_NEWSTATUS':
				$result = __('Order Status', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_OFFCC_NEWSTATUS_HELP':
				$result = __('Use PENDING in case you want to manually verify the credit card. Otherwise the order will be automatically confirmed after submitting the credit card details.', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_OFFCC_USESSL':
				$result = __('Use SSL', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_OFFCC_BRANDS':
				$result = __('Accepted Brands', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_OFFCC_BRANDS_HELP':
				$result = __('Select all the credit card brands that you are able to charge. Leave empty to accept any brands.', 'vikrestaurants');
				break;
			
			/**
			 * VikRestaurants 1.8.1
			 */

			case 'VRMANAGECONFIG91_HELP2':
				$result = __('Enable this option to allow the customers to self-confirm their reservations/orders through the link received via e-mail.', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_ACCOUNT':
				$result = __('PayPal Account', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_ACCOUNT_HELP':
				$result = __('Only the <b>e-mail address</b> assigned to the PayPal account should be typed here. DO NOT specify the PayPal <b>merchant account</b>, otherwise an error will occur while validating the transaction.', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_SANDBOX':
				$result = __('Test Mode', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_SANDBOX_HELP':
				$result = __('When enabled, the PayPal <b>SANDBOX</b> will be used. Turn OFF this option to collect <b>PRODUCTION</b> payments.', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_SSL':
				$result = __('Safe Connection', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_SSL_HELP':
				$result = __('When enabled, the connection to PayPal will be established only through the TLS 1.2 protocol.', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_IMAGE':
				$result = __('Image URL', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_IMAGE_HELP':
				$result = __('The image URL that will be used to display the "Pay Now" button.', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_AUTO_SUBMIT':
				$result = __('Auto-Submit', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_AUTO_SUBMIT_HELP':
				$result = __('Enable this option to auto-submit the payment form when reaching the summary page.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_KITCHEN_EMPTY_BILL':
				$result = __('Empty Bill', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_KITCHEN_EMPTY_BILL_HELP':
				$result = __('When enabled, the widget will display also the tables that currently have no scheduled dishes.', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEORDERDISHES':
				$result = __('Order Dishes', 'vikrestaurants');
				break;

			case 'VRRESCODESRULEDESCORDERDISHES':
				$result = __('Sends a notification via e-mail/SMS to the customer that explains how to start ordering the dishes. It is suggested to assign this rule to the "Arrived" or to the "Seated" reservation codes.', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.8.2
			 */

			case 'VRTKAREATYPE4':
				$result = __('Cities', 'vikrestaurants');
				break;

			case 'VRTKAREACITYADD':
				$result = __('Add City', 'vikrestaurants');
				break;

			case 'VRTESTSPECIALDAYS':
				$result = __('Test Special Days', 'vikrestaurants');
				break;

			case 'VRTESTSPECIALDAYSWARN':
				$result = __('No special days found, the global configuration will be used.', 'vikrestaurants');
				break;

			case 'VRTESTSPECIALDAYSNOMENUS':
				$result = __('The selection of the menus is missing. This means that the customers won\'t be allowed to order anything during this shift/day.', 'vikrestaurants');
				break;

			case 'VRTKMANAGEENTRYGROUPTITLE_HELP':
				$result = __('The title is used when displaying a summary of selected toppings. It is preferred to use short texts here, in example "Seasonings".', 'vikrestaurants');
				break;

			case 'VRTKMANAGEENTRYGROUPDESC_HELP':
				$result = __('The description is used as descriptive label while selecting the toppings. When specified, the description will be shown in place of the title. It is possible to use longer texts here, such as "Choose up to 4 seasonings".', 'vikrestaurants');
				break;

			case 'VRTKMANAGEENTRYGROUP6':
				$result = __('Use Quantity', 'vikrestaurants');
				break;

			case 'VRTKMANAGEENTRYGROUP6_DESC':
				$result = __('Enable this option to allow the customers to pick the same topping more than once.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG79_HELP':
				$result = __('This value indicates the timezone that will be used to save the dates within the database. However, the system will always adjust all the dates and times to the timezone specified from the WordPress configuration: <b>%s</b>.', 'vikrestaurants');
				break;
				
			case 'VRMANAGECONFIGTK39':
				$result = __('Orders per Interval', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK39_HELP':
				$result = __('Define here the maximum number of orders that you can accept on each interval. This setting is useful, in example, to limit the orders according to the number of shippings that your riders are able to handle on each time slot. You can choose whether this restriction should apply to pickup orders, delivery orders or both.', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAYAREAS_SELECT':
				$result = __('- Select Areas -', 'vikrestaurants');
				break;
			
			case 'VRMANAGESPDAYAREAS_HELP':
				$result = __('<p>Define here the list of accepted delivery areas. When this special day takes effect, only the specified areas will be used to accept delivery orders. Leave this field empty to accept all the published areas.</p><p>This parameter is useful if you own a "restaurant on wheels" that periodically moves around the city.</p>', 'vikrestaurants');
				break;

			case 'VRPRICEPERPERSON':
				// @TRANSLATORS: price per person
				$result = _x('%s/person', 'price per person', 'vikrestaurants');
				break;

			case 'VRMEDIAFIRSTCONFIG':
				$result = __('Every time you upload an image, the system always creates a thumbnail of that image. Before to upload your first image, you should set up the default size that will be used to resize all the future images. Click this message to change the default size.', 'vikrestaurants');
				break;

			case 'VRMEDIAFIRSTCONFIG2':
				$result = __('Here you have just to increase/decrease the size of the thumbnail and click the save button to apply the changes. You don\'t need to upload any images here.', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.8.3
			 */

			case 'VRE_TOOLBAR_NEW_WIDGET':
				$result = __('New Widget', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU23':
				$result = __('Start Publishing', 'vikrestaurants');
				break;

			case 'VRMANAGETKMENU24':
				$result = __('Finish Publishing', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDRULE10':
				$result = __('Reservation Notes', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDRULE11':
				$result = __('Delivery Notes', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG95':
				$result = __('Edit after Transmit', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG95_HELP':
				$result = __('Allow the customers to edit the transmitted dishes as long as they are not under preparation. Turn off to block modifications once the products have been transmitted to the kitchen.', 'vikrestaurants');
				break;

			case 'VRRESCODENOSTATUS':
				$result = __('Remove status', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK5_OVERRIDE_HELP':
				$result = __('Specify here the minimum cost required to proceed with the purchase. The amount entered here will always overwrite the minimum cost specified within the configuration and the delivery areas.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TIMES_TITLE':
				$result = __('Orders Availability', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TIMES_DESC':
				$result = __('Displays a table with the available times and the related number of booked orders and dishes. Offers the possibility of increasing the maximum number of orders that can be received on each slot.', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TIMES_SUMMARY':
				$result = __('Summary', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TIMES_SUMMARY_N_ORDERS':
				$result = __('%d orders out of %d', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TIMES_SUMMARY_N_ORDERS_1':
				$result = __('%d order out of %d', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TIMES_SUMMARY_N_ORDERS_0':
				$result = __('No orders made', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TIMES_SUMMARY_N_ITEMS':
				$result = __('%d items out of %d', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TIMES_SUMMARY_N_ITEMS_1':
				$result = __('%d item out of %d', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TIMES_SUMMARY_N_ITEMS_0':
				$result = __('No ordered items', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TIMES_ACTION_INCREASE_N':
				$result = __('Increase by %d', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TIMES_ACTION_DECREASE_N':
				$result = __('Decrease by %d', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_TIMES_ACTION_BLOCK':
				$result = __('Block time', 'vikrestaurants');
				break;

			case 'VRWIZARDWHAT':
				$result = __('<p>To avoid getting lost in the mazes of the software, this wizard helps you setting up a basic configuration.</p><p>After reaching the completion, VikRestaurants will be completely up and running.</p><p>If you are not interested in following the steps of the wizard, you can dismiss it by clicking the apposite button from the toolbar.</p>', 'vikrestaurants');
				break;

			case 'VRWIZARDBTNDONE':
				$result = __('Close Wizard', 'vikrestaurants');
				break;

			case 'VRWIZARDBTNREST':
				$result = __('Restore Wizard', 'vikrestaurants');
				break;

			case 'VRWIZARDBTNDONE_DESC':
				$result = __('Are you sure that you really want to close the Wizard? Anyhow, you\'ll be able to restore it from the configuration.', 'vikrestaurants');
				break;

			case 'VRWIZARDBTNIGNORE':
				$result = __('Ignore', 'vikrestaurants');
				break;

			case 'VRWIZARDBTNDISMISS':
				$result = __('Dismiss', 'vikrestaurants');
				break;

			case 'VRWIZARDDEPEND':
				$result = __('<p>Before to proceed you should complete all the following steps first.</p>', 'vikrestaurants');
				break;

			case 'VRWIZARDOTHER_N_ITEMS':
				$result = __('and other %d items', 'vikrestaurants');
				break;

			case 'VRWIZARDOTHER_N_ITEMS_1':
				$result = __('and another item', 'vikrestaurants');
				break;

			case 'VRWIZARDFORMATH24':
				$result = __('24-hour clock', 'vikrestaurants');
				break;

			case 'VRWIZARDCURRCODE':
				$result = __('Currency', 'vikrestaurants');
				break;

			case 'VRWIZARDCURRSYMB':
				$result = __('Symbol', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_SECTIONS':
				$result = __('System Sections', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_SECTIONS_DESC':
				$result = __('<p>Choose what are the sections that you wish to use in VikRestaurants.</p>', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_SECTIONS_WARN':
				$result = __('Please enable at least a section! Otherwise the software would result useless.', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_OPENINGS':
				$result = __('Opening Times', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_OPENINGS_DESC':
				$result = __('<p>Define the opening hours during which you\'ll be able to receive restaurant reservations and take-away orders.</p><p>You can use the <b>Special Days</b> to define different openings for specific dates.</p>', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_OPENINGS_WARN1':
				$result = __('Define an opening time also for the restaurant section.', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_OPENINGS_WARN2':
				$result = __('Define an opening time also for the take-away section.', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_ROOMS_DESC':
				$result = __('<p>Here you should create all the rooms/areas of your restaurant. Even if you are not interested in this feature, it is mandatory to create at least a room.</p>', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_TABLES_DESC':
				$result = __('<p>The availability of the restaurant is calculated according to the capacity of the created tables.</p><p>It is also possible to create a single table by specifying the total capacity of the restaurant and by enabling the <b>shared</b> option. This way the table will host reservations until the full capacity is reached.</p>', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_PRODUCTS_DESC':
				$result = __('<p>Define here the list of dishes that you offer at your restaurant. The products can either be displayed in a menu or purchased.</p>', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_MENUS_DESC':
				$result = __('<p>Create the menus available at your restaurant. You can also define the sections (e.g. starters, first courses, etc...) and assign them the products previously created.</p>', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_TKMENUS':
				$result = __('Take-Away Menus', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_TKMENUS_DESC':
				$result = __('<p>Create the products (and the related menus/sections) that you wish to sell online.</p>', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_TKMENUS_N_PROD':
				$result = __('%d products', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_TKMENUS_N_PROD_1':
				$result = __('1 product', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_TKMENUS_N_PROD_0':
				$result = __('No product', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_TKATTR_DESC':
				$result = __('<p>Define a list of attributes, characteristics, ingredients, allergens and so on. VikRestaurants comes by default with 3 pre-installed attributes: contains nuts, vegetarian and spicy.</p><p>If you are not interested in using the attributes, unpublish the existing ones to avoid showing them in the front-end.</p>', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_TKTOPPING_DESC':
				$result = __('<p>Toppings are used to allow the customers to pick extra ingredients for specific products (in example to build a pizza) or to build boxes (such as combos or sushi poke bowls).</p><p>Here you need to create all the supported toppings. It is suggested to group them under the separators in order to find them more easily.</p>', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_TKGROUPS':
				$result = __('Assign Toppings', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_TKGROUPS_DESC':
				$result = __('<p>After creating the toppings, you need to assign them to the related products. Within the management page of a product you are able to define several groups of toppings, by also specifying the maximum number of toppings that can be selected per group.</p>', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_TKSERVICES_DESC':
				$result = __('<p>Choose whether you intend to offer the delivery service or not. Through the special days you\'ll be also able to override the configuration value, in example to offer deliveries in specific days only.</p>', 'vikrestaurants');
					break;

			case 'VRE_WIZARD_STEP_TKSERVICES_WARN':
				$result = __('At least one of these 2 services must be enabled!', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_TKAREAS_DESC':
				$result = __('<p>The delivery areas are used to limit the addresses available for delivery. In example, you\'ll be able to accept all the addresses that stay within the drawn shapes (such as polygons and circles).</p>', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_TKAREAS_WARN':
				$result = __('Before configuring the delivery areas, it is strongly recommended to set up a <b>Google API Key</b>. Make sure that <b>Geocoding</b> service has been enabled from the console of your Google account.', 'vikrestaurants');
				break;

			case 'VRE_WIZARD_STEP_PAYMENTS_DESC':
				$result = __('<p>Publish at least a method of payment to receive money directly through your website. There are already 3 pre-installed payment methods: <b>PayPal</b>, <b>Offline Credit Card</b> and <b>Bank Transfer</b> (configurable as <b>pay upon arrival</b> too).</p>', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.8.5
			 */

			case 'VRE_CONFIG_ORIGINS_SCOPE':
				$result = __('Here you can define the locations of the restaurant, which are used by the system to calculate the distance between your locations and the address of the customers.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK21':
				$result = __('Manage Origins', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWORIGINS':
				$result = __('VikRestaurants - Locations', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITORIGIN':
				$result = __('VikRestaurants - Edit Location', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWORIGIN':
				$result = __('VikRestaurants - New Location', 'vikrestaurants');
				break;

			case 'VRE_ORIGIN_MARKER_IMAGE':
				$result = __('Marker Image', 'vikrestaurants');
				break;

			case 'VRE_ORIGIN_MARKER_IMAGE_DESC':
				$result = __('An optional image to be used as marker inside Google Map. Leave empty to use the default one.', 'vikrestaurants');
				break;

			case 'VRE_ORIGIN_DESCRIPTION_SCOPE':
				$result = __('The description is displayed after clicking the marker from Google Map. Do not enter the address of the location because it is automatically taken from the related field.', 'vikrestaurants');
				break;

			case 'VRE_ORIGIN_COORD_INFO':
				$result = __('Start filling the form to let the system fetches the coordinates of the location. Afterwards you will be able to adjust the coordinates by dragging the marker on the map.', 'vikrestaurants');
				break;

			case 'VRE_ORDER_EXPORT_DRIVER_EXCEL':
				$result = __('Microsoft Excel', 'vikrestaurants');
				break;

			case 'VRE_ORDER_EXPORT_DRIVER_EXCEL_DESC':
				$result = __('Exports the rows in a (non-standard) CSV format compatible with Microsoft Excel.', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.9
			 */

			case 'VRMANAGETABLE2_HELP':
				$result = __('The minimum number of participants that this table can host.', 'vikrestaurants');
				break;

			case 'VRMANAGETABLE3_HELP':
				$result = __('The maximum number of participants that this table can host.', 'vikrestaurants');
				break;

			case 'VRMANAGETABLE12_HELP':
				$result = __('Enable this option to allow the table to host different reservations simultaneously as long as the total count of participants is lower than the maximum capacity.', 'vikrestaurants');
				break;

			case 'VRQRCODE_TABLE_NEW':
				$result = __('You will be able to see the QR code after saving the details of this table.', 'vikrestaurants');
				break;

			case 'VRQRCODE_TABLE_DESC':
				$result = __('The QR code can be used to easily access the ordering page of a reservation. By scanning the QR code, the system will search for a reservation assigned to this table with a check-in compatible with the current time.', 'vikrestaurants');
				break;

			case 'VRQRCODE_REGENERATE':
				$result = __('Regenerate', 'vikrestaurants');
				break;

			case 'VRQRCODE_REGENERATE_WARN':
				$result = __('If you regenerate this QR code, the existing ones you might have already used or printed will be immediately invalidated. Do you want to proceed?', 'vikrestaurants');
				break;

			case 'VRPINCODE_RESERVATION_DESC':
				$result = __('This is the PIN code that your customers should use to start the ordering process through the QR code.', 'vikrestaurants');
				break;

			case 'VRPINCODE_RESERVATION_LOCKED':
				$result = __('The ordering process has been locked because someone failed the authentication 3 consecutive times.', 'vikrestaurants');
				break;

			case 'VRPINCODE_RESERVATION_UNLOCK':
				$result = __('Unlock PIN', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU2_HELP':
				$result = __('Enable this option in case the menu should be available only in a particular period of the year. This way, the menu will be published only by creating an apposite special day.', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU31_HELP':
				$result = __('Enable this option to allow the customers to choose this menu during the booking process. This way the restaurant can assume that the customers are going to eat the food listed here.', 'vikrestaurants');
				break;

			case 'VRMANAGEMENU32_HELP':
				$result = __('Enable this option to include this section within the filter bar. From here, the customers are allowed to filter the products by section.', 'vikrestaurants');
				break;

			case 'VRE_ADD_SECTION':
				$result = __('Add Menu Section', 'vikrestaurants');
				break;

			case 'VRE_EDIT_SECTION':
				$result = __('Edit Menu Section', 'vikrestaurants');
				break;

			case 'VRE_N_PRODUCTS':
				$result = __('%d products', 'vikrestaurants');
				break;

			case 'VRE_N_PRODUCTS_1':
				$result = __('1 product', 'vikrestaurants');
				break;

			case 'VRE_N_PRODUCTS_0':
				$result = __('No product', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION9_DESC':
				$result = __('This is the deposit that the customer is supposed to leave in order to confirm this reservation.', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION11_DESC':
				$result = __('When the bill is closed, it is not possible to add or edit items.', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION25_DESC':
				$result = __('Define here how long the table should remain occupied.', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION_MISSING_TABLE_ERR':
				$result = __('You need to select at least a table.', 'vikrestaurants');
				break;

			case 'VRMANAGERESERVATION_CAPACITY_TABLE_WARN':
				$result = __('The capacity of the selected tables doesn\'t seem to be enough to host the specified number of participants. Do you want to proceed anyway?', 'vikrestaurants');
					break;

			case 'VRMANAGERESERVATION_SELECTED_MENUS_WARN':
				$result = __('On this day there are some menus available for booking. The number of selected menus (%d/%d) does not match the number of participants. Do you want to proceed anyway?', 'vikrestaurants');
				break;

			case 'VRE_REM_DISCOUNT_UNDO':
				$result = __('Cancel Discount Removal', 'vikrestaurants');
				break;

			case 'VRE_REM_TIP_UNDO':
				$result = __('Cancel Tip Removal', 'vikrestaurants');
				break;

			case 'VRE_ADD_TIP_UNDO':
				$result = __('Cancel Tip Adding', 'vikrestaurants');
				break;

			case 'VRE_DISC_CHANGE_INFO':
				$result = __('Changes will be applied after saving the page.', 'vikrestaurants');
				break;

			case 'VRE_MANUAL_DISCOUNT_PROMPT':
				$result = __('Enter the discount to apply. Append a "%" at the end to apply a percentage discount.', 'vikrestaurants');
				break;

			case 'VRE_MANUAL_TIP_PROMPT':
				$result = __('Enter the tip to apply. Append a "%" at the end to apply a percentage tip.', 'vikrestaurants');
				break;

			case 'VRE_MANUAL_SERVICE_PROMPT':
				$result = __('Enter the service charge/discount to apply. Append a "%" at the end to apply a percentage value.', 'vikrestaurants');
				break;

			case 'VRMANAGESHIFT6_DESC':
				$result = __('Leave empty to hide the label for this shift.', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY9_HELP':
				$result = __('Choose all the menus available for the dates matching the configuration of this special day (unpublished menus will be ignored).', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY9_HELP_RS':
				$result = __('When empty, the menu selection will be ignored.', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY9_HELP_TK':
				$result = __('When empty, the food ordering won\'t be allowed and the restaurant will be treated as closed.', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY12_HELP':
				$result = __('When enabled, the datepicker will highlight the dates matching the configuration of this special day.', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY13_HELP':
				$result = __('Enable this option to always keep open the restaurant in case a matching date is also covered by a closing day.', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY17_HELP':
				$result = __('The images will be only used by the <b>VikRestaurants Event</b> module.', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY19_HELP':
				$result = __('Enable this option to allow the customers to choose a menu during the booking process. This way the restaurant can assume that the customers are going to eat the food listed in the selected menu(s).', 'vikrestaurants');
				break;

			case 'VRMANAGESPDAY21_HELP':
				$result = __('Choose here the maximum number of guests that the restaurant can host simultaneously. Set this value to 0 to immediately block the future booking for all the matching dates.', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR16':
				$result = __('Track Actions', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR6_HELP':
				$result = __('Allow this user to access the Operators Area within the front-end.', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR11_DESC':
				$result = __('If empty, the username to log in will be equals to the specified e-mail address.', 'vikrestaurants');
				break;

			case 'VRMANAGEOPERATOR16_HELP':
				$result = __('Enable this option to log the actions performed by this operator.', 'vikrestaurants');
				break;

			case 'VRMANAGETKTOPPING5_HELP':
				$result = __('Separators can be used to categorize the toppings at administration level. This can speed up the assignment of the toppings to the products.', 'vikrestaurants');
				break;

			case 'VRMANAGETKTOPPING6_HELP':
				$result = __('You should now update the products to which this topping is assigned.', 'vikrestaurants');
				break;

			case 'VRNEWPRICE':
				$result = __('New Price', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT9':
				$result = __('There are no parameters available.', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT5_DESC':
				$result = __('This parameter is used to skip the payment form and to automatically CONFIRM the reservations/orders. When this option is enabled, there won\'t be a transaction between the bank and the customer. Keep this option disabled in case this payment method is used to collect online credit/debit cards.', 'vikrestaurants');
				break;

			case 'VRMANAGEPAYMENT13_DESC':
				$result = __('The position of the order summary page in which the payment form/button will be displayed.', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMF13':
				$result = __('Editable', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMF13_DESC':
				$result = __('Choose whether the customers are allowed to edit this field. Turn it off if you wish to prevent the customers from changing its value after the first booking. In case of separator field, it will rather be displayed only once.', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMF11_DESC':
				$result = __('The rules can be used to give a specific meaning to the fields. In example, all the fields that apply the <b>Nominative</b> rule will be used to construct the full name of the customer.', 'vikrestaurants');
				break;

			case 'VRMANAGECUSTOMF10_DESC':
				$result = __('The field used to collect the phone number displays a dropdown to select the phone dial code. The default dial code will be equals to the one used by the country selected here.', 'vikrestaurants');
				break;

			case 'VRCUSTOMFLANGHELP':
				$result = __('Choose for which language the custom field should be visible. Do NOT use this parameter to apply fields translations. For this purpose, use the apposite multilingual feature instead.', 'vikrestaurants');
				break;

			case 'VRCUSTOMFSERVICEHELP':
				$result = __('Choose for which service the custom field should be used. Any mandatory fields will be considered as such only in case the selected service is equals to the value specified here.', 'vikrestaurants');
				break;

			case 'VRSUFFIXCLASSHELP':
				$result = __('A suffix to be applied to the CSS class of the separator.', 'vikrestaurants');
				break;

			case 'VRSEPARATORDESCHELP':
				$result = __('When specified, the description will be used in place of the name.', 'vikrestaurants');
				break;

			case 'VRCUSTOMFTYPEOPTION7':
				$result = __('Number', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDRULE12':
				$result = __('Company', 'vikrestaurants');
				break;

			case 'VRCUSTFIELDRULE13':
				$result = __('VAT Number', 'vikrestaurants');
				break;

			case 'VREMINVAL':
				$result = __('Min. Value', 'vikrestaurants');
				break;

			case 'VREMAXVAL':
				$result = __('Max. Value', 'vikrestaurants');
				break;

			case 'VREALLOWDECIMALS':
				$result = __('Accept Decimals', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA14':
				$result = __('Alternative Text', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA15':
				$result = __('Image Title', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIA16':
				$result = __('Caption', 'vikrestaurants');
				break;

			case 'VRMANAGEMEDIANOTRX':
				$result = __('The selected image does not specify any translatable contents.', 'vikrestaurants');
				break;

			case 'VRMEDIAPROPBOXTITLE':
				$result = __('Media Properties', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON15':
				$result = __('Auto Remove After Usage', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON16':
				$result = __('Category', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON2_DESC':
				$result = __('Contrarily to the <b>permanent</b> coupon codes, the <b>gift</b> ones can be redeemed a limited number of times.', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON4_DESC':
				$result = __('Enter here the discount amount that you want to apply when the coupon code is redeemed.', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON8_DESC':
				$result = __('The coupon code will be redeemed only when the number of participants is equals or higher than the specified amount.', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON9_DESC':
				$result = __('The coupon code will be redeemed only when the total cost of the reservation/order is equals or higher than the specified amount.', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON15_DESC':
				$result = __('Enable this option to auto remove the coupon code when the maximum number of usages is reached.', 'vikrestaurants');
				break;

			case 'VRMANAGECOUPON16_DESC':
				$result = __('The categories can be used to group the coupons at administrative level.', 'vikrestaurants');
				break;

			case 'VRE_NOTES_COUPON_DESC':
				$result = __('You can enter here a few notes for administrative purposes.', 'vikrestaurants');
				break;

			case 'VRE_DISPLAY_N_COUPONS':
				$result = __('Display %d coupons', 'vikrestaurants');
				break;

			case 'VRE_DISPLAY_N_COUPONS_1':
				$result = __('Display coupon', 'vikrestaurants');
				break;

			case 'VRE_ADD_COUPON':
				$result = __('Add coupon', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_COUPON':
				$result = __('- Select Coupon -', 'vikrestaurants');
				break;

			case 'VRMANAGEREVIEW12_DESC':
				$result = __('Whether the review has been published by a user that actually purchased the voted subject.', 'vikrestaurants');
				break;

			case 'VRMENUTAXES':
				$result = __('Taxes', 'vikrestaurants');
				break;

			case 'VREMAINTITLEVIEWTAXES':
				$result = __('VikRestaurants - Taxes', 'vikrestaurants');
				break;

			case 'VREMAINTITLEEDITTAX':
				$result = __('VikRestaurants - Edit Tax', 'vikrestaurants');
				break;

			case 'VREMAINTITLENEWTAX':
				$result = __('VikRestaurants - New Tax', 'vikrestaurants');
				break;

			case 'VRETAXFIELDSET':
				$result = __('Tax', 'vikrestaurants');
				break;

			case 'VRETAXRULEFIELDSET':
				$result = __('Tax Rules', 'vikrestaurants');
				break;

			case 'VRETAXBREAKDOWN':
				$result = __('Tax Breakdown', 'vikrestaurants');
				break;

			case 'VRETAXMATHOP':
				$result = __('Math Operation', 'vikrestaurants');
				break;

			case 'VRETAXMATHOP_ADD':
				$result = __('+%', 'vikrestaurants');
				break;

			case 'VRETAXMATHOP_SUB':
				$result = __('-%', 'vikrestaurants');
				break;

			case 'VRETAXMATHOP_VAT':
				$result = __('VAT (included taxes)', 'vikrestaurants');
				break;

			case 'VRETAXAPPLY':
				$result = __('Modifier', 'vikrestaurants');
				break;

			case 'VRETAXAPPLY_OPT1':
				$result = __('Apply to initial price', 'vikrestaurants');
				break;

			case 'VRETAXAPPLY_OPT2':
				$result = __('Apply in sequence to resulting price', 'vikrestaurants');
				break;

			case 'VRETAXCAP':
				$result = __('Tax Cap', 'vikrestaurants');
				break;

			case 'VRETAXCAP_HELP':
				$result = __('A tax cap places an upper bound on the amount of government tax a company might be required to pay. In this case the tax is said to be capped. This function is only required for some countries, where there is a maximum amount of taxes that cannot be exceeded. Please ignore this setting if nothing similar applies to your country of residence.', 'vikrestaurants');
				break;

			case 'VRETAXBDLABEL':
				$result = __('Tax Name', 'vikrestaurants');
				break;

			case 'VRETAXBDPLACEHOLDER':
				$result = __('e.g. Federal taxes', 'vikrestaurants');
				break;

			case 'VRETESTTAXES':
				$result = __('Test Taxes', 'vikrestaurants');
				break;

			case 'VRMENUCATEGORIES':
				$result = __('Categories', 'vikrestaurants');
				break;

			case 'VREMAINTITLEVIEWCATEGORIES':
				$result = __('VikRestaurants - Categories', 'vikrestaurants');
				break;

			case 'VREMAINTITLEEDITCATEGORY':
				$result = __('VikRestaurants - Edit Category', 'vikrestaurants');
				break;

			case 'VREMAINTITLENEWCATEGORY':
				$result = __('VikRestaurants - New Category', 'vikrestaurants');
				break;

			case 'VRE_FILTER_SELECT_CATEGORY':
				$result = __('- Select Category -', 'vikrestaurants');
				break;

			case 'VRE_FILTER_NO_CATEGORY':
				$result = __('- No Category -', 'vikrestaurants');
				break;

			case 'VRMENURESCODES':
				$result = __('Reservation Codes', 'vikrestaurants');
				break;

			case 'VRRESCODESENDMAIL_LABEL':
				$result = __('Send Mail', 'vikrestaurants');
				break;

			case 'VRRESCODESENDMAIL_DESC':
				$result = __('Whenever this code is assigned to a reservation/order, the system will send a notification e-mail to the customer.', 'vikrestaurants');
				break;

			case 'VRRESCODESENDMAIL_TIP':
				$result = __('You can you use the conditional texts to include a specific message within the e-mail and display it only when this code is selected.', 'vikrestaurants');
				break;

			case 'VRRESCODERULE_HELP':
				$result = __('Whenever this code is assigned to a reservation/order, the event of the selected rule will be triggered.', 'vikrestaurants');
				break;

			case 'VREAPPEARANCE':
				$result = __('Appearance', 'vikrestaurants');
				break;

			case 'VRERECURRENCE':
				$result = __('Recurrence', 'vikrestaurants');
				break;

			case 'VRECONTACT':
				$result = __('Contact', 'vikrestaurants');
				break;

			case 'VRECOPY':
				$result = __('Copy', 'vikrestaurants');
				break;

			case 'VRECOPIED':
				$result = __('Copied.', 'vikrestaurants');
				break;

			case 'VRIMPORT':
				$result = __('Import', 'vikrestaurants');
				break;

			case 'VRTRYBTN':
				$result = __('Let me try', 'vikrestaurants');
				break;

			case 'VRE_PIECES_SHORT':
				$result = _x('pcs.', 'abbr. for "pieces"', 'vikrestaurants');
				break;

			case 'VRE_BOOKING_FIELDSET':
				$result = __('Booking', 'vikrestaurants');
				break;

			case 'VRE_PRICING_FIELDSET':
				$result = __('Pricing', 'vikrestaurants');
				break;

			case 'VRE_NOTIFICATIONS_FIELDSET':
				$result = __('Notifications', 'vikrestaurants');
				break;

			case 'VRE_TEMPLATES_FIELDSET':
				$result = __('Templates', 'vikrestaurants');
				break;

			case 'VRE_DATETIME_FIELDSET':
				$result = __('Date & Time', 'vikrestaurants');
				break;

			case 'VRE_COLUMNS_FIELDSET':
				$result = __('Columns', 'vikrestaurants');
				break;

			case 'VRE_CANCELLATION_FIELDSET':
				$result = __('Cancellation', 'vikrestaurants');
				break;

			case 'VRE_SAFETY_FIELDSET':
				$result = __('Safety', 'vikrestaurants');
				break;

			case 'VRE_SHOP_FIELDSET':
				$result = __('Shop', 'vikrestaurants');
				break;

			case 'VRE_MENUSLIST_FIELDSET':
				$result = __('Menus List', 'vikrestaurants');
				break;

			case 'VRE_PURCHASE_FIELDSET':
				$result = __('Purchase', 'vikrestaurants');
				break;

			case 'VRE_GRATUITY_FIELDSET':
				$result = __('Gratuities', 'vikrestaurants');
				break;

			case 'VRE_AVAIL_FIELDSET':
				$result = __('Availability', 'vikrestaurants');
				break;

			case 'VRE_SETTINGS_FIELDSET':
				$result = __('Settings', 'vikrestaurants');
				break;

			case 'VRE_USAGES_FIELDSET':
				$result = __('Usages', 'vikrestaurants');
				break;

			case 'VRE_FILTERS_FIELDSET':
				$result = __('Filters', 'vikrestaurants');
				break;

			case 'VRE_INSTRUCTIONS_FIELDSET':
				$result = __('Instructions', 'vikrestaurants');
				break;

			case 'VRE_QRCODE_FIELDSET':
				$result = __('QR Code', 'vikrestaurants');
				break;

			case 'VRE_PINCODE_FIELDSET':
				$result = __('PIN Code', 'vikrestaurants');
				break;

			case 'VRE_PUBL_START_ON':
				$result = __('Starts on %s', 'vikrestaurants');
				break;

			case 'VRE_PUBL_END_ON':
				$result = __('Ended on %s', 'vikrestaurants');
				break;

			case 'VRE_INCREASE_BY':
				$result = __('Increase by', 'vikrestaurants');
				break;

			case 'VRE_DECREASE_BY':
				$result = __('Decrease by', 'vikrestaurants');
				break;

			case 'VRE_PERCENTAGE_N':
				$result = __('Percentage (%s)', 'vikrestaurants');
				break;

			case 'VRE_FIXED_N':
				$result = __('Fixed (%s)', 'vikrestaurants');
				break;

			case 'VRE_ADD_CUSTOMER':
				$result = __('Add Customer', 'vikrestaurants');
				break;

			case 'VRE_EDIT_CUSTOMER':
				$result = __('Edit Customer', 'vikrestaurants');
				break;

			case 'VRE_EDIT_SORT_DRAG_DROP':
				$result = __('Drag&drop the elements to change their ordering.', 'vikrestaurants');
				break;

			case 'VRE_TRX_LIST_TITLE':
				$result = __('VikRestaurants - Translations', 'vikrestaurants');
				break;

			case 'VRE_TRX_EDIT_TITLE':
				$result = __('VikRestaurants - Edit Translation', 'vikrestaurants');
				break;

			case 'VRE_TRX_NEW_TITLE':
				$result = __('VikRestaurants - New Translation', 'vikrestaurants');
				break;

			case 'VRETAGPLACEHOLDER':
				$result = __('Type or select some tags', 'vikrestaurants');
				break;

			case 'VRCONFIGUPLOADERROR':
				$result = __('Error while uploading the file', 'vikrestaurants');
				break;

			case 'VRCONFIGFILETYPEERROR':
				$result = __('The selected file is not supported', 'vikrestaurants');
				break;

			case 'VRCONFIGFILETYPEERRORWHO':
				$result = __('The selected file is not supported (%s)', 'vikrestaurants');
				break;

			case 'VRMANAGETKRES35':
				$result = __('Edit Item', 'vikrestaurants');
				break;

			case 'VRMANAGETKRESASAP':
				$result = __('This order should be prepared <b>as soon as possible</b>.', 'vikrestaurants');
				break;

			case 'VRMANAGETKRESASAPSHORT':
				$result = __('As soon as possible', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL10':
				$result = __('Discount', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL12':
				$result = __('Auto Insert', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL16':
				$result = __('Total Cost', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL17':
				$result = __('Occurrences', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL18':
				$result = __('Units', 'vikrestaurants');
				break;

			case 'VRMANAGETKDEAL12_DESC':
				$result = __('When enabled, all the gift food will be automatically added into the cart. Otherwise the customers will have to manually pick the gift products. The number of gift foods that can be added/selected depends on the maximum number of times that this deal can be applied.', 'vikrestaurants');
				break;

			case 'VRTKDEAL_DISCOUNT_TOTAL_AMOUNT_DESC':
				$result = __('Enter here the discount amount to apply. In case of percentage discount, it will be calculated on the total cost of the cart.', 'vikrestaurants');
				break;

			case 'VRTKDEAL_DISCOUNT_TOTAL_TCART_DESC':
				$result = __('The discount will be applied when the total cost of the cart reaches the amount specified here.', 'vikrestaurants');
				break;

			case 'VRTKDEAL_DISCOUNT_ITEM_AMOUNT_DESC':
				$result = __('The discount will be applied to each unit of the item. In example, if you have 2 units of the same item in cart, the discount will be applied to both them.', 'vikrestaurants');
				break;

			case 'VRTKDEAL_DISCOUNT_ITEM_UNITS_DESC':
				$result = __('The discount will be applied only when the units of the item in cart are equal to the amount specified here.', 'vikrestaurants');
				break;

			case 'VRTKDEAL_FREE_TOTAL_TCART_DESC':
				$result = __('The specified products will be free when the total cost of the cart reaches the amount specified here.', 'vikrestaurants');
				break;

			case 'VRTKDEAL_FREE_TOTAL_UNITS_DESC':
				$result = __('The maximum number of units of the selected item that can be gifted.', 'vikrestaurants');
				break;

			case 'VRTKDEAL_ABOVE_ALL_AMOUNT_DESC':
				$result = __('Enter here the discount amount to apply whenever a combination of selected products is found within the cart.', 'vikrestaurants');
				break;

			case 'VRTKDEAL_ABOVE_ALL_MIN_DESC':
				$result = __('This value is used to determine the minimum occurrence of the combinations in the cart, required to apply the discount. All the items flagged as required must be inside the cart as the quantity specified here. For optional items, any combination of them must be inside the cart as the quantity specified here.', 'vikrestaurants');
				break;

			case 'VRTKDEAL_ABOVE_ALL_UNITS_DESC':
				$result = __('The number of units that should be in cart in order to include this item while checking whether the deal is eligible or not.', 'vikrestaurants');
				break;

			case 'VRTKDEAL_ABOVE_ALL_REQUIRED_DESC':
				$result = __('When enabled, the deal will be applied only when this item is included within the cart. Otherwise, any combination of optional items will be ok.', 'vikrestaurants');
				break;

			case 'VRTKDEAL_FREE_COMBINATION_MIN_DESC':
				$result = __('This value is used to determine the minimum occurrence of the combinations in the cart, required to gift the food. All the items flagged as required must be inside the cart as the quantity specified here. For optional items, any combination of them must be inside the cart as the quantity specified here.', 'vikrestaurants');
				break;

			case 'VRTKDEALTYPEDESC2':
				$result = __('Apply a discount to the selected items.', 'vikrestaurants');
				break;

			case 'VRTKDEALALLVARS':
				$result = __('(any)', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA2_HELP':
				$result = __('Choose how this area should restrict the addresses typed by the customers during the ordering process. It is possible to accept all the addresses that stay within a polygon or a circle (Google Maps required) or that match a list of accepted cities or ZIP codes.', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA10_DESC':
				$result = __('Choose the color to use for the background of the shape.', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA14_DESC':
				$result = __('Choose the color to use for the border of the shape.', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA15_DESC':
				$result = __('This value indicates how thick the border of the shape should be.', 'vikrestaurants');
				break;

			case 'VRMANAGETKAREA18_HELP':
				$result = __('The minimum cost required to deliver an order in this area.', 'vikrestaurants');
				break;

			case 'VRTKAREATYPEDESC0':
				$result = __('Please choose a delivery area type from the related dropdown.', 'vikrestaurants');
				break;

			case 'VRTKAREACITYEDIT':
				$result = __('Edit City', 'vikrestaurants');
				break;

			case 'VRTKAREAZIPADD':
				$result = __('Add ZIP Code', 'vikrestaurants');
				break;

			case 'VRTKAREAZIPEDIT':
				$result = __('Edit ZIP Code', 'vikrestaurants');
				break;

			case 'VRTKAREA_CIRCLE_LATLNG_HELP':
				$result = __('Insert the coordinates for the center of the circle area. You can also select the center by directly clicking a point from the map.', 'vikrestaurants');
				break;

			case 'VRTKAREA_POLYGON_MANAGE_BTN':
				$result = __('Manage Polygon Points', 'vikrestaurants');
				break;

			case 'VRTKAREA_POLYGON_LEGEND':
				$result = __('<p>Click the %s button to set a point to your current position.</p><p>Click on the map to add a new point to the polygon. Drag the points from the map to automatically update their coordinates. Click a vertex of the polygon to update, rearrange or delete the related point.</p>', 'vikrestaurants');
				break;

			case 'VRE_GOOGLE_API_KEY_ERROR':
				$result = __('An error with your Google API key has occurred. Please make sure that your API Key is set and valid. Otherwise try to check that both the <b>Maps JavaScript API</b> and <b>Geocoding API</b> libraries have been enabled from your Google console.', 'vikrestaurants');
				break;

			case 'VRMENUSTATUSCODES':
				$result = __('Status Codes', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWSTATUSCODES':
				$result = __('VikRestaurants - Status Codes', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITSTATUSCODE':
				$result = __('VikRestaurants - Edit Status Code', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWSTATUSCODE':
				$result = __('VikRestaurants - New Status Code', 'vikrestaurants');
				break;

			case 'VRSTATUSCODEUNIQUEERR':
				$result = __('The status code must be unique!', 'vikrestaurants');
				break;

			case 'VRSTATUSCODECODE_HELP':
				$result = __('The status code accepts only letters (A-Z) and numbers (0-9).', 'vikrestaurants');
				break;

			case 'VRSTATUSCODEROLES':
				$result = __('Roles', 'vikrestaurants');
				break;

			case 'VRSTATUSCODEROLE_APPROVED':
				$result = __('Approved', 'vikrestaurants');
				break;

			case 'VRSTATUSCODEROLE_APPROVED_HELP':
				$result = __('Flag as confirmed. Also used by the system to fetch the statistics.', 'vikrestaurants');
				break;

			case 'VRSTATUSCODEROLE_RESERVED':
				$result = __('Reserved', 'vikrestaurants');
				break;

			case 'VRSTATUSCODEROLE_RESERVED_HELP':
				$result = __('This role can be used to lock the availability.', 'vikrestaurants');
				break;

			case 'VRSTATUSCODEROLE_EXPIRED':
				$result = __('Expired', 'vikrestaurants');
				break;

			case 'VRSTATUSCODEROLE_EXPIRED_HELP':
				$result = __('Used when the confirmation is not made within the established range of time.', 'vikrestaurants');
				break;

			case 'VRSTATUSCODEROLE_CANCELLED':
				$result = __('Cancelled', 'vikrestaurants');
				break;

			case 'VRSTATUSCODEROLE_CANCELLED_HELP':
				$result = __('Assigned after a cancellation, usually made after a confirmation.', 'vikrestaurants');
				break;

			case 'VRSTATUSCODEROLE_PAID':
				$result = __('Paid', 'vikrestaurants');
				break;

			case 'VRSTATUSCODEROLE_PAID_HELP':
				$result = __('Flag as payment done.', 'vikrestaurants');
				break;

			case 'VRE_STATUS_CODES_FACTORY_RESET_CONFIRM':
				$result = __('Do you want to proceed with the reset? If you confirm, the status codes will be restored to the factory settings.', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWMAILTEXTS':
				$result = __('VikRestaurants - Mail Conditional Texts', 'vikrestaurants');
				break;

			case 'VRMAINTITLEEDITMAILTEXT':
				$result = __('VikRestaurants - Edit Mail Conditional Text', 'vikrestaurants');
				break;

			case 'VRMAINTITLENEWMAILTEXT':
				$result = __('VikRestaurants - New Mail Conditional Text', 'vikrestaurants');
				break;

			case 'VRE_MAILTEXT_MANAGE_BTN':
				$result = __('Manage Conditional Texts', 'vikrestaurants');
				break;

			case 'VRE_MAILTEXT_MANAGE_BTN_DESC':
				$result = __('The conditional texts can be used to alter the notification e-mail data (such as the body and the subject) according to specific conditions.', 'vikrestaurants');
				break;

			case 'VRE_MAILTEXT_ADD_ACTION':
				$result = __('Add Action', 'vikrestaurants');
				break;

			case 'VRE_MAILTEXT_EDIT_ACTION':
				$result = __('Edit Action', 'vikrestaurants');
				break;

			case 'VRE_MAILTEXT_ADD_FILTER':
				$result = __('Add Filter', 'vikrestaurants');
				break;

			case 'VRE_MAILTEXT_EDIT_FILTER':
				$result = __('Edit Filter', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_ATTACHMENTS':
				$result = __('Attachments', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_ATTACHMENTS_DESC':
				$result = __('Choose the files that will be included as e-mail attachments.', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_ATTACHMENTS_FILES':
				$result = __('Choose Files', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_BODY':
				$result = __('Body', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_BODY_DESC':
				$result = __('Enter the text that you wish to include within the body into a specific position.', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_BODY_POSITION':
				$result = __('Text Position', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_BODY_POSITION_DESC':
				$result = __('Choose the position of the template where the text will be displayed.', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_BODY_TEXT':
				$result = __('Body Text', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_RECIPIENT':
				$result = __('Recipient', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_RECIPIENT_DESC':
				$result = __('Specify the additional e-mail addresses that should receive the e-mail notification.', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_RECIPIENT_ADDRESSES':
				$result = __('E-mail Address', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_RECIPIENT_ADDRESSES_DESC':
				$result = __('It is possible to separate each address with a comma.', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_SUBJECT':
				$result = __('Subject', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_SUBJECT_DESC':
				$result = __('Alter the default subject of the e-mail with the provided one.', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_SUBJECT_TEXT':
				$result = __('Subject Text', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_SUBJECT_MODE':
				$result = __('Mode', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_SUBJECT_MODE_OPT_REPLACE':
				$result = __('Replace all', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_SUBJECT_MODE_OPT_APPEND':
				$result = __('Insert at the end', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_SUBJECT_MODE_OPT_PREPEND':
				$result = __('Insert at the beginning', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_ACTION_SUBJECT_MODE_DESC':
				$result = __('Choose how the subject should be updated.', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_DATE':
				$result = __('Date', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_DATE_DESC':
				$result = __('Restriction applied to the dates (check-in/booking) of the restaurant reservation and take-away orders.', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_DEBUG':
				$result = __('Debug', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_DEBUG_DESC':
				$result = __('The conditional texts with debug filter turned on will be applied only from the mail preview in the back-end. Use this filter if you wish to see how the mail template looks before committing the changes.', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_LANGUAGE':
				$result = __('Language', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_LANGUAGE_DESC':
				$result = __('The conditional text will be taken only in case of matching language.', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT':
				$result = __('Orders Count', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_DESC':
				$result = __('Applies the conditional text only to those users that booked/purchased a specific number of reservations/orders. The user must be logged in.', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_COMPARATOR':
				$result = __('Comparator', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_COMPARATOR_OPT_ET':
				$result = __('Equal to', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_COMPARATOR_OPT_DT':
				$result = __('Different than', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_COMPARATOR_OPT_LT':
				$result = __('Lower than', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_COMPARATOR_OPT_LTE':
				$result = __('Lower then or equal to', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_COMPARATOR_OPT_GT':
				$result = __('Greater then', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_COMPARATOR_OPT_GTE':
				$result = __('Greater then or equal to', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_PAYMENT':
				$result = __('Payment', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_PAYMENT_DESC':
				$result = __('Restriction applied to the payment method assigned to the restaurant reservations and take-away orders.', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_PAYMENT_METHODS':
				$result = __('Payment Methods', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_RESERVATIONCODE':
				$result = __('Reservation Code', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_RESERVATIONCODE_DESC':
				$result = __('Applies the conditional text only to reservations/orders with specific reservations codes (e.g. arrived, preparing and so on).', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_RESERVATIONCODE_LIST':
				$result = __('Observable Codes', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_ROOM':
				$result = __('Room', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_ROOM_DESC':
				$result = __('Applies the conditional text only to the restaurant reservations that have been assigned to the selected rooms.', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_SERVICE':
				$result = __('Service', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_SERVICE_DESC':
				$result = __('Applies the conditional text only to the orders that have been assigned to the selected service (delivery or takeaway).', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_STATUS':
				$result = __('Status', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_STATUS_DESC':
				$result = __('Applies the conditional text only to reservations/orders with specific status codes (e.g. confirmed, pending, paid and so on).', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_STATUS_LIST':
				$result = __('Allowed Statuses', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_TEMPLATE':
				$result = __('Mail Template', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_TEMPLATE_DESC':
				$result = __('Applies the conditional text only to the selected e-mail templates.', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_TIME':
				$result = __('Time', 'vikrestaurants');
				break;

			case 'VRE_CONDITIONAL_TEXT_FILTER_TIME_DESC':
				$result = __('Restriction applied to the check-in time of the restaurant reservation and take-away orders.', 'vikrestaurants');
				break;

			case 'VRINVDELIVERYCHARGE':
				$result = __('Service', 'vikrestaurants');
				break;

			case 'VRINVOICEDATEOPT2':
				$result = __('Booking Date', 'vikrestaurants');
				break;

			case 'VRINVOICEDATEOPT3':
				$result = __('Check-in Date', 'vikrestaurants');
				break;

			case 'VRMAINTITLECONFIG':
				$result = __('VikRestaurants - Global Configuration', 'vikrestaurants');
				break;

			case 'VRMAINTITLECONFIGRES':
				$result = __('VikRestaurants - Restaurant Configuration', 'vikrestaurants');
				break;

			case 'VRMAINTITLECONFIGTK':
				$result = __('VikRestaurants - Take-Away Configuration', 'vikrestaurants');
				break;

			case 'VRMAINTITLECONFIGAPP':
				$result = __('VikRestaurants - Applications Configuration', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTKSECTION2':
				$result = __('Stocks System', 'vikrestaurants');
				break;

			case 'VRCONFIGFIELDSETAPIFR':
				$result = __('API', 'vikrestaurants');
				break;

			case 'VRECONFIGTABNAME4':
				$result = __('SMS Provider', 'vikrestaurants');
				break;

			case 'VRECONFIGMAILPREVIEW':
				$result = __('Mail Preview', 'vikrestaurants');
				break;

			case 'VRECONFIGTABCUSTOMIZER':
				$result = __('Customizer', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_RESTORE_FACTORY_SETTINGS':
				$result = __('Do you want to restore these fields to the factory settings?', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_TOGGLE_PREVIEW':
				$result = __('Toggle Preview', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_TAB_BUTTON':
				$result = __('Button', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_TAB_ADDITIONALCSS':
				$result = __('Additional CSS', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_FIELDSET_DEFAULT':
				$result = __('Default', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_FIELDSET_PRIMARY':
				$result = __('Primary', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_FIELDSET_SUCCESS':
				$result = __('Success', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_FIELDSET_DANGER':
				$result = __('Danger', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_FONT':
				$result = __('Font', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_SIZE':
				$result = __('Size', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_MARGIN':
				$result = __('Margin', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_PADDING':
				$result = __('Padding', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_VERTICAL':
				$result = __('Vertical', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_HORIZONTAL':
				$result = __('Horizontal', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_WIDTH':
				$result = __('Width', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_HEIGHT':
				$result = __('Height', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_RADIUS':
				$result = __('Radius', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_BORDER':
				$result = __('Border', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_BORDER_STYLE_SOLID':
				$result = __('Solid', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_BORDER_STYLE_DASHED':
				$result = __('Dashed', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_BORDER_STYLE_DOTTED':
				$result = __('Dotted', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_BORDER_STYLE_DOUBLE':
				$result = __('Double', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_HOVER':
				$result = __('Hover', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_ACTIVE':
				$result = __('Active', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_BACKGROUND':
				$result = __('Background', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_COLOR':
				$result = __('Color', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_LINEAR_GRADIENT':
				$result = __('Linear gradient', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_RADIAL_GRADIENT':
				$result = __('Radial gradient', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_BORDER_COLOR':
				$result = __('Border Color', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_TOP':
				$result = __('Top', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_BOTTOM':
				$result = __('Bottom', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_LEFT':
				$result = __('Left', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_RIGHT':
				$result = __('Right', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_INHERIT':
				$result = __('inherit', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_ANGLE':
				$result = __('Angle', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_OFFSET':
				$result = __('Offset', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_PARAM_OPACITY':
				$result = __('Opacity', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_ENABLE_INSPECTOR':
				$result = __('Enable Inspector', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_ENABLE_INSPECTOR_DESC':
				$result = __('When this option is enabled, you can click the elements within the preview to easily change the style and colors.', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_ENABLE_INSPECTOR_WARN':
				$result = __('The inspector does not seem to be available on this page. Make sure you selected a page containing VikRestaurants from the apposite dropdown.', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_ENABLE_MAILTEXT':
				$result = __('Conditional Texts', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_ENABLE_MAILTEXT_DESC':
				$result = __('When this option is enabled, you\'ll be able to easily add new texts into specific positions of the mail template.', 'vikrestaurants');
					break;

			case 'VRE_CUSTOMIZER_FIELDS_STYLE':
				$result = __('Style', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_FIELDS_STYLE_DESC':
				$result = __('Choose the style that will be used to display the custom fields in the front-end.', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_FIELDS_STYLE_OPT_MATERIAL':
				$result = __('Material', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_FIELDS_STYLE_OPT_DEFAULT':
				$result = __('Default', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_ELEMENT_TAB':
				$result = __('Element', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_TAB':
				$result = __('Appearance', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_TEXT_TAB':
				$result = __('Text', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_BOX_TAB':
				$result = __('Box', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_ELEMENT_SELECTOR':
				$result = __('Selector', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_ELEMENT_SELECTOR_DESC':
				$result = __('This is the selector that will be used to identify the element to customize.', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_ELEMENT_IMPORTANT':
				$result = __('Important', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_ELEMENT_IMPORTANT_DESC':
				$result = __('In case the applied styles do not seem to have effect, try to enable this option to grant them higher importance.', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_TOP_LEFT':
				$result = __('Top left', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_TOP_CENTER':
				$result = __('Top center', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_TOP_RIGHT':
				$result = __('Top right', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_CENTER_LEFT':
				$result = __('Center left', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_CENTER':
				$result = __('Center', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_CENTER_RIGHT':
				$result = __('Center right', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_BOTTOM_LEFT':
				$result = __('Bottom left', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_BOTTOM_CENTER':
				$result = __('Bottom center', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_BOTTOM_RIGHT':
				$result = __('Bottom right', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_SIDES':
				$result = __('Sides', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_SIDES_DESC':
				$result = __('Choose what are the sides of the rectangle that should display the border.', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_TEXT_FONT_SIZE':
				$result = __('Font Size', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_TEXT_LINE_HEIGHT':
				$result = __('Line Height', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_TEXT_FONT_STYLE':
				$result = __('Style', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_TEXT_FONT_ALIGN':
				$result = __('Alignment', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_BOX_SIZE':
				$result = __('Size', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_BOX_SIZE_DESC':
				$result = __('The width and height of the rectangle wrapping the element. If you don\'t specify the unit next to the values (i.e. px, %, em, etc...), the default "px" one will be used.', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_BOX_PADDING_DESC':
				$result = __('An element\'s padding area is the space between its content and its border. If you don\'t specify the unit next to the values (i.e. px, %, em, etc...), the default "px" one will be used.', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_BOX_MARGIN_DESC':
				$result = __('Margins create extra space around an element. If you don\'t specify the unit next to the values (i.e. px, %, em, etc...), the default "px" one will be used.', 'vikrestaurants');
					break;

			case 'VRE_CUSTOMIZER_INSPECTOR_ARROWS_LEGEND':
				$result = __('Use the arrows when you are over an element to scroll between parents, children and siblings.', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_INSPECTOR_ENTER_LEGEND':
				$result = __('Press enter to inspect the currently highlighted node.', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_RESTORE_TITLE':
				$result = __('Restored', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_RESTORE_MESSAGE':
				$result = __('The style has been restored successfully.', 'vikrestaurants');
				break;

			case 'VRE_CUSTOMIZER_SAVE_MESSAGE':
				$result = __('The changes have been saved successfully.', 'vikrestaurants');
				break;

			case 'VRE_UISVG_NO_REPEAT':
				$result = __('No repeat', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG11':
				$result = __('Minutes Interval', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG16':
				$result = __('Selection', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG20':
				$result = __('Tables Temporary Lock', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG24':
				$result = __('Booking Restriction', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG33':
				$result = __('User Login', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG34':
				$result = __('User Registration', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG36':
				$result = __('Dashboard', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG38':
				$result = __('Reservations List Columns', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG41':
				$result = __('Check-in Restriction', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG46':
				$result = __('Administrators', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG47':
				$result = __('Customer Notification', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG56':
				$result = __('Admin Notification', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG57':
				$result = __('Cancellation (admin)', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG67':
				$result = __('Product Review', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG90':
				$result = __('Restrict Since Creation', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG96':
				$result = __('Default Tax', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG97':
				$result = __('Use Tax Breakdown', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK3':
				$result = __('Enable Delivery', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK4':
				$result = __('Delivery Charge', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK5':
				$result = __('Minimum Total Cost', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK7':
				$result = __('Free Delivery', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK8':
				$result = __('Orders Temporary Lock', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK13':
				$result = __('Orders List Columns', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK16':
				$result = __('Enable Stocks', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK17':
				$result = __('Stocks Notification', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK18':
				$result = __('Takeaway Charge/Discount', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK25':
				$result = __('Maximum Meals', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK28':
				$result = __('Open Popup', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK40':
				$result = __('Enable Takeaway', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG1_DESC':
				$result = __('The e-mail address of the administrator. All the notifications will be sent to this address. If you want to notify multiple addresses, you have just to specify the e-mails separated by a comma.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG8_DESC':
				$result = __('Insert here the standard 3 letters code of the currency used in your system (like EUR, USD, GBP and so on).<br />Since this value represents the currency to collect payments, it must be a standard of <b>ISO 4217</b>.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG10_DESC':
				$result = __('Choose here whether the restaurant has a <b>continuous</b> working time or a <b>shifted</b> one. Pick the second option if you have working times that may different depending on the day or something else.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG11_DESC':
				$result = __('This value indicates the interval of minutes displayed for every hours. In example, when selecting %d, the time dropdown will display the hours as: %s.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG12_DESC':
				$result = __('This value indicates the average time of stay (in minutes) of the reservations. This means that, by default, the check-out of the reservations will be equals to the sum of the check-in and the value specified here. It is still possible to manually update the time of stay of the reservations from their management page. A table will result as occupied for the whole time of stay set for the related reservation.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG13_DESC':
				$result = __('This value indicates the minimum number of people that can be selected while booking a table from the front-end.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG14_DESC':
				$result = __('This value indicates the maximum number of people that can be selected while booking a table from the front-end.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG16_DESC':
				$result = __('Choose how the users should pick the room and table during the search process in the front-end.<ul><li>The customers need to pick a room and a table from the interactive map.</li><li>The customers need to pick the room where they want to stay. The best table (or combination) will be automatically selected by the system.</li><li>The table (or combination) that mostly suit the search query will be automatically selected by the system. In this case, the second step might be automatically skipped as no action is required by the users.</li></ul>', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG18_DESC':
				$result = __('Specify here the total amount that should be left as deposit in order to confirm the reservation.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG19_DESC':
				$result = __('Choose whether the deposit amount should be left by each participant of the group.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG20_DESC':
				$result = __('This setting defines the number of minutes for which the system should keep a PENDING reservation as locked, so that nobody can reserve a table that is being reserved by another client. In case a reservation is not confirmed within the estabilished range of time, the status of the latter will be flagged as REMOVED and the table will be free again.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG23_DESC':
				$result = __('Shows the software version and credits at the end of the pages (back-end only).', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG24_DESC':
				$result = __('How much time (in minutes) the customers have to book in advance. In case this value was set to <em>120</em> minutes and the current time is <em>10:00</em>, the first available time slot (for the current day) would be at <em>12:00</em>.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG33_DESC':
				$result = __('Select the login requirements for the reservations according to the provided restrictions:<ul><li><b>Never</b> - the system never asks to the users to log in or register an account;</li><li><b>Optional</b> - the users are allowed, but never forced, to log in or register an account;</li><li><b>Mandatory</b> - the users are forced to log in or register an account before to proceed with the reservation.</li></ul>', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG34_DESC':
				$result = __('Choose whether the users can create an account during the booking process. When disabled, the users will be able to register an account only according to the native settings of the CMS.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG35_DESC':
				$result = __('Choose the default status that will be used while registering a reservation/order from the front-end. The default status will be used only in case the reservation/order doesn\'t require a payment. In that case, <b>%s</b> will be always used.', 'vikrestaurants');
					break;

			case 'VRMANAGECONFIG36_DESC':
				$result = __('Choose whether the dashboard should display the restaurant section and all the related widgets.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG37_DESC':
				$result = __('This value indicates the interval that will be used to refresh the widgets of the dashboard. The lower the value, the quicker the dashboard will be updated. It is recommended to choose a value between <em>30</em> and <em>120</em> seconds in order to avoid querying the database too often.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG38_DESC':
				$result = __('These are the default columns that can be shown within the restaurant reservations table.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG39_DESC':
				$result = __('Choose whether the customers are allowed to pre-select a menu during the booking process. Bear in mind that you are always able to overwrite this setting by creating an apposite special day.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG40_DESC':
				$result = __('Choose whether the customers are allowed to auto-cancel their reservations/orders. When enabled, it is possible to set up some restrictions.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG41_HELP':
				$result = __('Cancellation requests will be accepted with an advance equals to the number of specified days/hours compared to the reservation/order check-in. Use <em>0</em> to ignore this restriction.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG44_DESC':
				$result = __('Select all the statuses for which you wish to automatically send an e-mail notification to the customers. This setting is considered only when the status is changed as a result of an action performed by the customer itself.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG45_DESC':
				$result = __('Select all the statuses for which you wish to automatically send an e-mail notification to the operators. This setting is considered only when the status is changed as a result of an action performed by the customer.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG46_DESC':
				$result = __('Select all the statuses for which you wish to automatically send an e-mail notification to the administrators. This setting is considered only when the status is changed as a result of an action performed by the customer.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG47_DESC':
				$result = __('<p>This is the HTML template that will be used to generate the notification e-mail sent to the customers when registering a table booking.</p><p>%s</p>', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG47_DESCTK':
				$result = __('<p>This is the HTML template that will be used to generate the notification e-mail sent to the customers when registering a take-away order.</p><p>%s</p>', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG48_DESC':
				$result = __('Enable this parameter to display an additional option at the end of the dropdown used to pick the number of participants. This option is meant to give alternative booking methods to those groups that are too large to be accepted online by this system.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG49_DESC':
				$result = __('When the users select the "large party" option, they are automatically redirected to the URL specified here. In the landing page you can specify alternative booking methods, such as a contact form or a phone number.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG50_DESC':
				$result = __('Enable this setting if you wish to translate the contents of VikRestaurants in multiple languages.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG56_DESC':
				$result = __('<p>This is the HTML template that will be used to generate the notification e-mail sent to the administrators and operators when a customer registers a table booking from the front-end.</p><p>%s</p>', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG56_DESCTK':
				$result = __('<p>This is the HTML template that will be used to generate the notification e-mail sent to the administrators and operators when a customer registers a take-away order from the front-end.</p><p>%s</p>', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG57_DESC':
				$result = __('<p>This is the HTML template that will be used to generate the notification e-mail sent to the administrators and operators when a customer cancels a table booking from the front-end.</p><p>%s</p>', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG57_DESCTK':
				$result = __('<p>This is the HTML template that will be used to generate the notification e-mail sent to the administrators and operators when a customer cancels a take-away order from the front-end.</p><p>%s</p>', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG58_DESC':
				$result = __('Choose whether the customers can leave reviews.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG59_DESC':
				$result = __('Choose whether the customers can leave reviews for your take-away products.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG60_DESC':
				$result = __('Choose who\'s able to leave a review according to the provided restrictions:<ul><li>anyone can leave a review;</li><li>only registered users can leave a review;</li><li>only registered users who have actually purchased the product they wish to rate can leave a review.</li></ul>', 'vikrestaurants');
					break;

			case 'VRMANAGECONFIG61_DESC':
				$result = __('Enable this option if you want to force the users to write a comment when leaving a review. Otherwise the users will be able to write a comment only whether they want to.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG62_DESC':
				$result = __('Choose the minimum number of characters that the users should write in order to leave a review. This value does not take effect in case the comment is optional and the user didn\'t write anything.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG63_DESC':
				$result = __('Choose the maximum number of characters that the users can write in order to leave a review.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG64_DESC':
				$result = __('Specify here the maximum number of reviews that can be displayed per page.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG65_DESC':
				$result = __('Choose whether the reviews should be automatically published or whether you want to approve them first.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG66_DESC':
				$result = __('Choose whether the reviews should be filtered by language or merged all together. The language of a review is exactly the same of the locale (language tag) selected by the user that posted it.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG67_DESCTK':
				$result = __('<p>This is the HTML template that will be used to generate the notification e-mail sent to the administrators when a customer leaves a review for a product.</p><p>%s</p>', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG68_DESC':
				$result = __('Choose whether the users should provide a reason for the cancellation request.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG69_DESC':
				$result = __('The API framework can be used to connect third-party applications and web servers to this VikRestaurants through HTTP requests.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG72_DESC':
				$result = __('Choose whether the server should log nothing, everything or only the errors. Even if some logs are disabled, the last received one will be always logged.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG73_DESC':
				$result = __('The logs older than the specified threshold will be automatically deleted in order to free some disk space. You can also choose to prevent the system from automatically deleting the logs.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG75':
				$result = __('Whenever an HTTP request fails (login included), the system increases the number of failures for the related IP address, which will be automatically banned when the total number of failures reaches the value specified here. Notice that the number of failures is automatically reset after performing a successful request, in order to avoid banning IP addresses that fail a request due to a possible server downtime.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG80_DESC':
				$result = __('Turn on this value to allow the customers to select the phone prefix of their country. If you accept reservations only from one country, you can turn off this setting. The default phone prefix can be changed from the details of the <b>Phone Number</b> custom field.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG90_HELP':
				$result = __('Cancellation requests will be accepted within N minutes since the creation date time of the reservation/order. Use <em>0</em> to ignore this restriction.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG93_HELP':
				$result = __('It is possible to define here the factor that will be multiplied by the number of selected people in order to search for larger tables. It is suggested to specify a value between 1,5 and 3. In example, when searching for a table for 4 guests, the system will take only the tables that can host %d people, exactly %d (guests) * %d (distance factor). Of course, this will be applied only in case the guests do not belong to the same family.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG96_DESC':
				$result = __('Select here the default type of taxes to use. It is possible to specify different types of taxes from the management page of each single item that has a cost.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG97_DESC':
				$result = __('Enable this option to display line by line all the applied types of taxes before the total amount.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK2_DESC':
				$result = __('This value indicates the maximum number of meals that can be prepared (and ordered) on each interval. Notice that only the items that require a preparation will be counted here. There are no restrictions for the purchase of ready items, such as, in example, the bottles of water.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK3_DESC':
				$result = __('Choose if you are able to deliver the ordered food to the addresses provided by the customers. Thanks to the <b>Delivery Areas</b> you are able to define what are the zones that you are actually able to cover. With the usage of the <b>Special Days</b> instead you are able to choose for what days and times the delivery service should be available.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK4_DESC':
				$result = __('Insert here the base charge that should be applied to those users that choose the delivery service. This charge can be a fixed amount or a percentage of the total cost. It is possible to increase/decrease this amount depending on the delivery area to which the address of the user belongs to.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK5_DESC':
				$result = __('Enter here the minimum amount needed to proceed with the purchase. Orders with a total cost lower than the specified amount won\'t be able to proceed with the purchase. While configuring a delivery area it is possible to overwrite this value. Since the system always takes the highest amount, the value specified here should be the lowest one. Through the special days it is possible to manually overwrite the minimum amount without caring of the delivery areas configuration.', 'vikrestaurants');
					break;

			case 'VRMANAGECONFIGTK6_DESC':
				$result = __('The text specified here will be displayed at the beginning of the take-away menus list page. It is possible to use this text to inform the users about any warnings they should be aware of.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK7_DESC':
				$result = __('The delivery charge won\'t be applied to all the orders with a total NET higher than the amount specified here.', 'vikrestaurants');
					break;

			case 'VRMANAGECONFIGTK8_DESC':
				$result = __('This setting defines the number of minutes for which the system should keep a PENDING order as locked, so that the selected check-in cannot be overbooked. In case an order is not confirmed within the estabilished range of time, the status of the latter will be flagged as REMOVED and the occupied time slot will be free again.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK13_DESC':
				$result = __('These are the default columns that can be shown within the take-away orders table.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK16_DESC':
				$result = __('The stocks system is able to track the remaining units of your products. Enable this parameter if you want to be sure that you are not going to sell more products then you actually own.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK17_DESC':
				$result = __('<p>This is the HTML template that will be used to generate the notification e-mail sent to the administrators when the remaining units of one ore more products reached or exceeded the minimum threshold.</p><p>%s</p>', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK18_DESC':
				$result = __('Insert here the charge that should be applied to those users that choose to take away the ordered food. You can insert a negative amount to apply a discount instead. This is useful to incentivize the users to prefer the takeaway rather than the delivery service. This amount can be fixed or a percentage of the total cost.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK25_DESC':
				$result = __('Enter here the maximum number of meals that a customer can purchase with a single order. Notice that only the items that require a preparation will be counted here. There are no restrictions for the purchase of ready items, such as, in example, the bottles of water. Enter a high value if you don\'t want to apply this kind of restriction.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK28_DESC':
				$result = __('When clicking the button to add an item into the cart, the system may open a popup, which can be used to specify the quantity, write some notes about the food and select the toppings, if any. Thanks to this setting you can choose when this popup should be displayed. Notice that it is always possible to open this popup by clicking the item name from the cart module, which is meant to edit the ordered item.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK29_DESC':
				$result = __('Choose here the maximum number of characters that should be taken while displaying the short description of a product. Use <em>0</em> to always display the whole description. In case a description specifies a <b>READ MORE</b> separator, then the system will use the short description without considering the maximum number of characters.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK31_DESC':
				$result = __('Choose what is the default service that should be pre-selected when booking from the front-end. This field will be ignored in case you have only one service published.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK32_HELP':
				$result = __('When enabled, the customers will be asked to leave an optional tip for the restaurant. Percentage gratuities are calculated on the net total.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK33_DESC':
				$result = __('This is the default tip amount that will be suggested to the customers during the check-out process. You can choose whether the amount should be fixed or a percentage based on the total net.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGTK40_DESC':
				$result = __('Choose if your customers are able to come at the restaurant and personally take away the ordered food.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS1':
				$result = __('Provider', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS1_DESC':
				$result = __('Choose here the provider that will be used to deliver the messages. The cost per message is never free and strictly depends on the selected provider.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS2':
				$result = __('Trigger', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS2_DESC':
				$result = __('Choose whether the administrator should automatically send SMS notifications for restaurant reservations, take-away orders or both. It is also possible to prevent the system from sending automated messages.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS3':
				$result = __('Recipient', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS3_DESC':
				$result = __('Choose whether the system should send a message to the customer, to the administrator or both.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS4_DESC':
				$result = __('Enter here the phone number of the administrator. The system cannot send SMS notifications to multiple administrators.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGSMS7_DESC':
				$result = __('The provider seems to support the possibility to fetch the remaining balance from your account.', 'vikrestaurants');
				break;

			case 'VRCONFIG_SMSTMPL_RESTAURANT_ADMIN':
				$result = __('Restaurant - Administrator', 'vikrestaurants');
				break;

			case 'VRCONFIG_SMSTMPL_RESTAURANT_CUSTOMER':
				$result = __('Restaurant - Customer', 'vikrestaurants');
				break;

			case 'VRCONFIG_SMSTMPL_TAKEAWAY_ADMIN':
				$result = __('Take-Away - Administrator', 'vikrestaurants');
				break;

			case 'VRCONFIG_SMSTMPL_TAKEAWAY_CUSTOMER':
				$result = __('Take-Away - Customer', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIGMAILTMPL':
				$result = __('Click the %s button to start editing the mail template. Don\'t forget to save as copy in order to avoid losing your changes after updating the program to a newer version. Click the %s button to open the customizer, where you\'ll be able to see an instant preview and easily change the style of the mail template.', 'vikrestaurants');
				break;

			case 'VRCONFIG_CFCOLUMNS_TIP':
				$result = __('Choose whether you wish to display some details, collected through the custom fields, within the reservations/orders table. Custom fields that might show duplicate details (such as the customer e-mail) won\'t be included within this list.', 'vikrestaurants');
					break;

			case 'VRCONFIG_API_USERS':
				$result = __('Users', 'vikrestaurants');
				break;

			case 'VRCONFIG_API_USERS_DESC':
				$result = __('You can register here the applications/users that are able to authenticate to your API framework. It is also possible to define what are the plugins/actions that each user is able to execute.', 'vikrestaurants');
				break;

			case 'VRCONFIG_API_PLUGINS':
				$result = __('Plugins', 'vikrestaurants');
				break;

			case 'VRCONFIG_API_PLUGINS_DESC':
				$result = __('The plugins are the actions that can be performed by the users via HTTP requests. Almost all the actions require an authentication first.', 'vikrestaurants');
				break;

			case 'VRCONFIGLOGINREQ2':
				$result = __('Optional', 'vikrestaurants');
				break;

			case 'VRCONFIGLOGINREQ3':
				$result = __('Mandatory', 'vikrestaurants');
				break;

			case 'VRCONFIGRESREQ0':
				$result = __('Choose room and table', 'vikrestaurants');
				break;

			case 'VRCONFIGRESREQ1':
				$result = __('Choose room only', 'vikrestaurants');
				break;

			case 'VRCONFIGRESREQ2':
				$result = __('Choose nothing', 'vikrestaurants');
				break;

			case 'VRCONFIGSYMBPOSITION3':
				$result = __('After Price without space', 'vikrestaurants');
				break;

			case 'VRCONFIGSYMBPOSITION4':
				$result = __('Before Price without space', 'vikrestaurants');
				break;

			case 'VRCONFIGTIMEFORMAT3':
				$result = __('12 Hours Without Leading Zero', 'vikrestaurants');
				break;

			case 'VRCONFIGTIMEFORMAT4':
				$result = __('24 Hours Without Leading Zero', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER23':
				$result = __('Plugin', 'vikrestaurants');
				break;

			case 'VRMANAGEAPIUSER25':
				$result = __('Payload', 'vikrestaurants');
				break;

			case 'VRTKORDERPICKUPOPTION':
				$result = __('Takeaway', 'vikrestaurants');
				break;

			case 'VRSTAR_N':
				$result = __('%d stars', 'vikrestaurants');
				break;

			case 'VRSTAR_N_1':
				$result = __('1 star', 'vikrestaurants');
				break;

			case 'VRJUSTNOW':
				$result = __('just now', 'vikrestaurants');
				break;

			case 'VRANY':
				$result = __('Any', 'vikrestaurants');
				break;

			case 'VRANYLANG':
				$result = __('Any language', 'vikrestaurants');
				break;

			case 'VRANYTMPL':
				$result = __('Any template', 'vikrestaurants');
				break;

			case 'VRINVPAYCHARGE':
				$result = __('Payment Charge', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_PRODUCTION_SEPARATOR':
				$result = __('Production', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SANDBOX_SEPARATOR':
				$result = __('Sandbox', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_APPEARANCE_SEPARATOR':
				$result = __('Appearance', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_CLIENT_ID_DESC':
				$result = __('The <b>Client ID</b> parameter that you can find on your PayPal account, under the API Credentials section. Bear in mind that it is NOT your PayPal merchant e-mail address.', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_CLIENT_SECRET_DESC':
				$result = __('The <b>Client Secret</b> parameter that you can find from your PayPal account, under the API Credentials section.', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_LAYOUT':
				$result = __('Layout', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_LAYOUT_DESC':
				$result = __('Whether the PayPal buttons should be displayed vertically or horizontally.', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_LAYOUT_VERTICAL':
				$result = __('Vertical', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_LAYOUT_HORIZONTAL':
				$result = __('Horizontal', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_COLOR':
				$result = __('Color', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_COLOR_DESC':
				$result = __('The color styling to apply to the default PayPal button.', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_COLOR_GOLD':
				$result = __('Gold', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_COLOR_BLUE':
				$result = __('Blue', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_COLOR_SILVER':
				$result = __('Silver', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_COLOR_WHITE':
				$result = __('White', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_COLOR_BLACK':
				$result = __('Black', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SHAPE':
				$result = __('Shape', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SHAPE_DESC':
				$result = __('The shape of the payment buttons.', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SHAPE_RECTANGULAR':
				$result = __('Rectangular', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SHAPE_ROUNDED':
				$result = __('Rounded', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TAGLINE':
				$result = __('Tagline', 'vikrestaurants');
				break;

			case 'VRE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TAGLINE_DESC':
				$result = __('Whether the PayPal tagline (<em>"The safer, easier way to pay"</em>) should be displayed or not. Supported only by the horizontal layout.', 'vikrestaurants');
				break;

			case 'VRECONFIGTABBACKUP':
				$result = __('Backup', 'vikrestaurants');
				break;

			case 'VREMAINTITLEVIEWBACKUPS':
				$result = __('VikRestaurants - Backups Archive', 'vikrestaurants');
				break;

			case 'VREMAINTITLENEWBACKUP':
				$result = __('VikRestaurants - New Backup', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_CONFIG_TYPE_LABEL':
				$result = __('Export Type', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_CONFIG_TYPE_DESC':
				$result = __('Choose how the system should create the backup.', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_CONFIG_FOLDER_LABEL':
				$result = __('Folder Path', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_CONFIG_FOLDER_DESC':
				$result = __('Enter here the path used to store the backup archives created by VikRestaurants. In case the folder does not exist, the system will attempt to create it.', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_CONFIG_FOLDER_WARN':
				$result = __('It is not safe to use the default temporary folder provided by the system. It is recommended to change it or to use a nested folder with an unpredictable name, such as: %s', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_LIST_BUTTON':
				$result = __('Manage Backups', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_ACTION_CREATE':
				$result = __('Create New', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_ACTION_UPLOAD':
				$result = __('Upload Existing', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_DRAGDROP':
				$result = __('or DRAG ARCHIVE HERE', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_RESTORED':
				$result = __('The backup has been restored successfully!', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_RESTORECONF1':
				$result = __('Do you want to restore the program data with the selected backup?', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_RESTORECONF2':
				$result = __('Confirm that you want to proceed one last time. This action cannot be undone.', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_EXPORT_TYPE_FULL':
				$result = __('Full', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_EXPORT_TYPE_FULL_DESCRIPTION':
				$result = __('The backup will export all the contents created through VikRestaurants.', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_EXPORT_TYPE_MANAGEMENT':
				$result = __('Management', 'vikrestaurants');
				break;

			case 'VRE_BACKUP_EXPORT_TYPE_MANAGEMENT_DESCRIPTION':
				$result = __('The backup will export only the contents used to set up the program. The records related to the customers, such as the reservations and orders, will be completely ignored. This is useful to copy the configuration of this website into a new one.', 'vikrestaurants');
				break;

			case 'VRMAINTITLEVIEWEXPORT':
				$result = __('VikRestaurants - Export', 'vikrestaurants');
				break;

			case 'VREXPORTTABLEFOOTER':
				$result = __('The first %d rows of %d total.', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_RAW':
				$result = __('Raw', 'vikrestaurants');
				break;

			case 'VRE_EXPORT_RAW_DESC':
				$result = __('Enable this option to export the columns of the records with their original values. Otherwise leave it off to properly format the value of the columns.', 'vikrestaurants');
				break;

			case 'VRE_ORDER_EXPORT_DRIVER_HTML_DESC':
				$result = __('Converts the datasheet in a standard HTML table.', 'vikrestaurants');
				break;

			case 'VRE_ORDER_EXPORT_DRIVER_PDF_DESC':
				$result = __('Converts the datasheet in a PDF document.', 'vikrestaurants');
				break;

			case 'VRECONFIGTABCODEHUB':
				$result = __('Code Hub', 'vikrestaurants');
				break;

			case 'VRE_CODEHUB_BLOCK':
				$result = __('Code Hub - Block', 'vikrestaurants');
				break;

			case 'VRE_CODEHUB_BLOCKS':
				$result = __('Code Blocks', 'vikrestaurants');
				break;

			case 'VRE_CODE_BLOCK_EXTENSION':
				$result = __('File Extension', 'vikrestaurants');
				break;

			case 'VRE_CODE_BLOCK_AUTHOR':
				$result = __('Author', 'vikrestaurants');
				break;

			case 'VRE_CODE_BLOCK_AUTHOR_DESC':
				$result = __('If not specified, the name of the current user will be taken.', 'vikrestaurants');
				break;

			case 'VRE_CODE_BLOCK_VERSION':
				$result = __('Version', 'vikrestaurants');
				break;

			case 'VRE_CODE_BLOCK_VERSION_DESC':
				$result = __('If not specified, the current date and time will be used.', 'vikrestaurants');
				break;

			case 'VRE_CODE_BLOCK_SNIPPET':
				$result = __('Snippet', 'vikrestaurants');
				break;

			case 'VRE_CODE_BLOCK_EXPORT_DESC':
				$result = __('You\'ll be able to export this code block in a .json format. This is useful to easily import snippets on different website or to preserve a backup copy.', 'vikrestaurants');
					break;

			case 'VRE_CODE_BLOCK_INSTR_GLOBAL':
				$result = __('<p>The <b>CodeHub</b> is a tool that can be used to safely introduce new blocks of code to extend the default functionalities of the program.</p><p>The system currently supports all the following programming interfaces: %s.</p><p><b>IMPORTANT NOTE:</b> the code blocks are launched only when VikRestaurants is properly executed.</p>', 'vikrestaurants');
				break;

			case 'VRE_CODE_BLOCK_INSTR_JS':
				$result = __('<p>The javascript block is automatically wrapped in a safe <a href="https://developer.mozilla.org/en-US/docs/Glossary/IIFE" target="_blank">IIFE</a> statement to avoid breaking the flow in case of errors.</p><p>Inside this statement you immediately have access to <code>$</code> and <code>w</code> variables, which are an alias for <code>jQuery</code> and <code>window</code> objects respectively.</p><p>In case you need to register functions globally, you must assign them to the window variable. Something like: <code>w.doStuff = () => { /** todo */ }</code>.</p>', 'vikrestaurants');
				break;

			case 'VRE_CODE_BLOCK_INSTR_PHP':
				$result = __('<p>The created PHP code blocks are wrapped in a <code>\Throwable</code> try/catch statement, in order to prevent the system from going down in case of any errors.</p><p>It is strongly recommended to avoid directly echoing bytes, otherwise the output buffer may result compromised. In example do not test the usage of the code blocks by creating a snippet containing a code like this one: <code>echo "Hello World!";</code>. You can rather use <code>JFactory::getApplication->enqueueMessage("Hello World!");</code> to make sure that your scripts are properly executed.</p>', 'vikrestaurants');
				break;

			/**
			 * VikRestaurants 1.9.1
			 */

			case 'VRE_MENUS_LAYOUT_DESC':
				$result = __('Choose how the items that belong to this menu should be arranged in the front-end.', 'vikrestaurants');
				break;

			case 'VRE_MENUS_LAYOUT_OPT_LIST':
				$result = __('One item below the other', 'vikrestaurants');
				break;

			case 'VRE_MENUS_LAYOUT_OPT_GRID':
				$result = __('2 items per column', 'vikrestaurants');
				break;

			case 'VRE_STATS_WIDGET_KITCHEN_TAKEAWAY_DESC':
				$result = __('Displays a wall of bills containing the dishes to prepare for each take-away order.', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG98':
				$result = __('Service Order', 'vikrestaurants');
				break;

			case 'VRMANAGECONFIG98_HELP':
				$result = __('When enabled, the customers will be able to define the ordering of the preparation for the selected items, such as "First course", "Second course" or "Third course".', 'vikrestaurants');
				break;

			case 'VRE_ORDERDISH_SERVING_NUMBER_LABEL_SHORT':
				$result = __('Service Order', 'vikrestaurants');
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
