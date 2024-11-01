# Changelog

## 1.3.3

*Release date - 22 March 2024*

### New Features

* The Kitchen widget now also supports food preparation for the take-away section.
* The Kitchen section in the Operators Area now displays 2 different widgets, one for the restaurant and one for the take-away.
* It is now possible to specify the service order for the dishes to prepare (such as First Course, Second Course, Third Course).
* Introduced a new setting to choose the layout of the take-away menus (grid or list).

### Improvements

* It is now possible to choose the time unit (days or hours) for the cancellation restrictions.
* Various API plugin improvements.

### Bug Fixes

* Fixed an issue that could prevent the system from properly using the Google Address Auto-complete feature.
* Protected some back-end tasks from XSS vulnerabilities.
* Fixed a few PHP warnings.

## 1.3.2

*Release date - 23 January 2024*

### New Features

* The Shortcodes block now lets you create new shortcodes directly from Gutenberg editor.
* The Shortcodes block for Gutenberg is now able to display an instant preview.
* All the widgets have been converted into native blocks for Gutenberg.

### Improvements

* Custom fields configured as required checkboxes (such as terms & conditions or privacy policies) are no more auto-checked in case of returning customers.

### Bug Fixes

* Fixed a critical error that could occur in case the table booking process required the selection of the menus.
* The offline credit card payment method is now able to display the credit card brands in the right way.
* Fixed an issue that was not able to properly use the discount applied by a service.
* The system is now able to properly use the "Auto-delete after usage" setting of the coupon codes.
* Fixed an issue that was not properly updating the number of usages while redeeming a coupon code.
* Fixed an issue that could display the summary of the custom fields with weird labels and values.
* The dates displayed under the rooms closures page are now properly aligned to the site timezone.
* The closing days with weekly recurrence now show the correct name of the related week day.

---

## 1.3.1

*Release date - 30 November 2023*

### Improvements

* The e-mail templates now display the price of the ordered items multiplied by the selected number of units.
* The management page of a take-away order is now able to auto-fill the custom fields after selecting a customer.
* The system is now able to pre-select the correct room after clicking a table from the overview widget.
* Applies some improvements to the address validation during the take-away check-out process.
* Minor adjustments to improve the compatibility with the latest PHP versions.

### Bug Fixes

* Fixed an issue that was not able to properly display menus in the front-end when they were assigned only to "Sunday".
* Fixed layout issue with the invoice template.
* Fixed an issue that was never accepting delivery orders in case of no configured areas.
* Fixed an issue that was displaying the address field even if the customer was not selected.
* Fixed an issue with the coupon submission in the front-end.
* The link used to print the take-away orders now points to the correct URL.
* Fixed an issue that might not properly apply the configured restrictions based on the total cost.
* Fixed an issue that could require a payment during the check-out even if the payments were not configured.
* The collected custom fields are now properly displayed.
* Fixed a fatal error that could occur with the Offline Credit Card payment method.
* Fixed an issue that was always auto-selecting all the existing attributes.
* Fixed a few issues that could cause time discrepancies.
* Checked the configuration integrity as some settings might not have been properly installed during the 1.3 update.
* Fixed an issue with the Shortcodes block in Gutenberg.
* Fixed an issue that could randomly display 401 error messages.

---

## 1.3

*Release date - 18 October 2023*

### New Features

* The back-end now fully supports the management of reservations assigned to multiple tables (table clustering).
* Introduced the status codes management (pending, confirmed, paid and so on).
* The system now supports the ordering process via QR code.
* Added support to composite taxes.
* Introduced a new system to backup the contents of VikRestaurants (import/export).
* Introduced the conditional texts to extend the contents sent within the e-mail templates.
* Implemented a customizer to easily edit the style of the site pages and the e-mail templates.
* It is now possible to create shifts on-the-fly while managing a special day.
* The notification e-mail received by the administrator for new reservations/orders now contains a reject link.
* Implemented the "PayPal Express Checkout" payment gateway.
* Added a "Code Hub" feature to easily implement PHP and JS snippets.
* It is now possible to export the customers, the reviews and the products in the following formats: CSV, PDF and HTML.

### Improvements

* Redesigned the back-end interface and the user experience.
* Improved the algorithm used to detect the best combination of tables.
* It is now possible to create new tags directly while managing a product.
* The reservations/orders CSV export now includes a footer containing the sum of all the totals.
* The back-end now displays a badge for all the take-away orders with "as soon as possible" check-in.
* It is now easier to apply the changes when increasing or decreasing the cost of a topping.
* Improved the SEO for the internal media manager, which now provides the possibility to specify titles, alt(s) and captions.
* Added a dropdown next to the pagination to easily change the number of items per page (all back-end lists).
* It is now possible to automatically send an e-mail notification to the customer when a specific reservation code is selected.
* Added a short text to describe the behavior of all the ambiguous parameters.
* Added some new filters to the operators area.
* The notification e-mail received by the administrator, for a restaurant reservation, now contains the ordered food (if any).
* The program is now fully compatible with all the major PHP versions.
* All the request tasks are now safe from XSS attacks.
* Enhanced the code and the usage of the front-end widgets.
* Improved the speed of some database queries.

### Bug Fixes

* Fixed an issue that was still displaying some take-away sections even if the latter was disabled.
* Fixed an issue that was not able to properly display the native custom fields within the reservations and orders list.
* Fixed an issue that could corrupt the generation of the ICS files for the reservations/orders.
* Fixed a few language definitions.
* Several minor fixes.

---

## 1.2.8

*Release date - 31 January 2023*

### Bug Fixes

* Fixed an issue that could not properly save a reservation with certain PHP configurations.
* Fixed a few PHP warnings that could occur with PHP 8 or higher.
* Minor framework adjustments.

---

## 1.2.7

*Release date - 28 April 2022*

### Improvements

* Improved the performance of the query used to fetch the take-away orders and the restaurant reservations.
* When creating a shortcode with Elementor (as well as any other page builder), the resulting preview should now be visible.
* The system now applies the discount also to the bill value.
* Updated URL used to load Google Maps libraries.

### Bug Fixes

* The URLs displayed within the customer e-mail are now properly routed according to the language selected by the user.
* Fixed a fatal error that could occur with classic editor inside the management of a post/page.
* Fixed the validation of the year applied by the Offline Credit Card payment gateway.
* Fixed issue related to a few missing icons in the front-end.
* Fixed issue with background repeat mode set to none within the back-end Maps page.
* Fixed an issue that was fetching a wrong status for the products of the menus when they were published in different shifts.

---

## 1.2.6

*Release date - 26 November 2021*

### New Features

* Added support for Clicksend SMS provider.

### Bug Fixes

* Fixed an unexpected error that could occur while registering a new account.

---

## 1.2.5

*Release date - 18 November 2021*

### Bug Fixes

* Fixed an issue that was showing a language key in place of its correct translation.

---

## 1.2.4

*Release date - 17 November 2021*

### New Features

* Added a new driver to export the reservations/orders in a CSV format compatible with Microsoft Excel.

### Improvements

* FontAwesome has been updated to the 5.15.3 version.
* The CSV export driver now includes a new column with the selected payment method.
* Added some plugin hooks to allow the extendability of the CSV export drivers.
* The management of the locations have been moved from the Take-Away Map widget to the configuration of the plugin.
* Improved the stability of the PayPal integration.

### Bug Fixes

* Fixed an issue that was allowing the customers to delete the food after the closure of the bill.
* Fixed an issue that was not considering the charge/discount of the products while editing the bill from the operators area.
* Replaced 2 wrong heading titles from the summary page of a take-away order in the front-end.
* Fixed an issue with the information of the internal media files.

---

## 1.2.3

*Release date - 20 October 2021*

### Bug Fixes

* Fixed an issue that could show the WP login page while trying to add a take-away product.
* The description of the rooms is now able to interpret any external shortcodes.
* Fixed an issue that was not showing the button to see the credit card details left by the customers.
* Fixed an error that could occur while trying to cancel a reservation from the front-end.
* Fixed deal detection for items without assigned variations.
* Fixed an issue that could show an empty list of order statuses within the Operators Area.
* Fixed issue that could not disable the required attribute from the delivery custom fields when the pickup was pre-selected.
* Fixed wrong taxes ratio for take-away orders.

---

## 1.2.2

*Release date - 05 March 2021*

### Bug Fixes

* Fixed an issue with the selection of Sunday during the creation of a shift.

---

## 1.2.1

*Release date - 03 March 2021*

### New Features

* Added the possibility to publish/unpublish the variations.
* Implemented a feature to auto-print the orders (external VRE Printer software needed).

### Improvements

* The purchased items are now sorted according to the specified ordering.
* Several enhancements to the whole API Framework.
* Implemented some new plugin hooks.
* Added ALT attribute to the images of the e-mails to improve their score.

### Bug Fixes

* Prevented duplicated notifications for products with low stocks.
* Fixed issue with the calculation of the delivery charge.
* Fixed an error that was not displaying the tables map anymore.
* Fixed a timezone issue that could occur in the back-end.
* Fixed the "jump" issue that could occur after picking a date from the calendar.

---

## 1.2

*Release date - 28 January 2021*

### New Features

* The wizard is now able to download and install the sample data.
* Added parameter to choose whether the dishes are editable after transmit to kitchen.
* Implemented new "Delivery Notes" rule for custom fields.
* Implemented new "Reservation Notes" rule for custom fields.
* Created the "Orders Availability" widget for the dashboard (take-away).
* Added support for take-away menus publishing dates.
* Implemented a framework to override the pages of the plugin from the back-end.

### Improvements

* It is now possible to use the READ MORE separator to write the short description for the items.
* The HTML descriptions are now able to execute the shortcodes of other plugins.
* Further enhancements to the "cluster" search feature.
* It is now possible to override the Minimum Order Total setting with the special days.
* Improved the image caching within the media manager.
* Added fully support to WordPress automatic updates.

### Bug Fixes

* Fixed availability issue within the details page of a product.
* Fixed price calculation after selecting a product variation.
* Fixed error that occurred while trying to edit a page containing the Menus List shortcode.
* Fixed an issue that might not properly display the selected product variation (order dishes).
* Fixed issue with take-away items gallery.

---

## 1.1

*Release date - 11 December 2020*

### New Features

* Implemented an installation wizard for setup guidance.
* It is now possible to assign a working shift to a specific week day without the usage of special days.
* The widgets within the dashboard of the plugin can be different for each user.
* Added the possibility to receive RSS news, tips and offers.

### Improvements

* All site AJAX requests now rely on WP AJAX end-point for a better stability.
* The plugin is now able to work on multi-site networks.
* Orders and reservations within the dashboard can now display up to 50 rows.
* Adjusted some capabilities.

### Bug Fixes

* Fixed notices that could occur when creating a shortcode.
* Adjusted times validation after changing date from the front-end.
* The media field now ignores the images that have been manually deleted.

---

## 1.0

*Release date - 26 November 2020*

* First stable release of the VikRestaurants plugin for WordPress.