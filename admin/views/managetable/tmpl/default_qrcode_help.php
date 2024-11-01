<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

?>

<div style="padding: 20px;">
    
    <p>
        First of all, you should print the <b>QR code</b> for this table and show it above the latter in your restaurant. The same procedure should be followed for all the other tables.
    </p>

    <p>
        Whenever a customer scans a <b>QR code</b>, the system will search for a reservation assigned to the matching <em>table</em> (because each QR code corresponds only to a single table) and with a <em>check-in</em> compatible with the current time.
    </p>

    <p>
        It is mandatory to <span style="text-decoration: underline;">always have a reservation</span> created either from the back-end or from the front-end, otherwise the ordering procedure cannot start. If you are wondering why the system doesn't auto-create a reservation in case it doesn't exist, this is a security measure needed to prevent <b>spam</b> orders. Since the URL of a <b>QR code</b> is preserved within the browser history of the users, none can't prevent them from visiting it again, which may lead to a massive order spamming.
    </p>

    <p>
        Once the system has found the matching reservation, before start ordering it is required to enter a <b>PIN</b> code. The <b>PIN</b> code can be found in the e-mail received by the customer after booking from the front-end. In case the reservation has been created by the administrator, it should be responsibility of the latter to communicate the <b>PIN</b> code to the customers, which can be recovered from the details page of the reservation. The <b>PIN</b> Code is required to preserve the privacy of the users and to avoid a leak of sensitive data.
    </p>

    <p>
        In case a user fails to enter the <b>PIN</b> code for <span style="text-decoration: underline;">3 consecutive times</span>, the ordering procedure will be blocked. It is still possible to unlock it from the management page of the reservation in the back-end.
    </p>

    <p>
        Since the system is always able to recognize the correct reservation from the <b>QR code</b>, multiple customers of the same table can order simultaneously from different devices.
    </p>

    <hr />

    <p><b>P.S.</b> <em>If you feel like the QR code changes at every page refresh, you are absolutely right! This is normal anyway, as the encoding used to generate the QR may vary over time. In fact, several QR codes can point to the same URL.</em></p>

</div>