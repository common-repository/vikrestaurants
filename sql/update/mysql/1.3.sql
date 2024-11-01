--
-- Alter for table `#__vikrestaurants_wpshortcodes`
--

ALTER TABLE `#__vikrestaurants_wpshortcodes`
ADD COLUMN `parent_id` int(10) unsigned DEFAULT 0;

--
-- Table structure for table `#__vikrestaurants_tax`
--

CREATE TABLE IF NOT EXISTS `#__vikrestaurants_tax` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT '',
  `description` varchar(1024) DEFAULT '',
  `createdon` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Table structure for table `#__vikrestaurants_tax_rule`
--

CREATE TABLE IF NOT EXISTS `#__vikrestaurants_tax_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_tax` int(10) unsigned NOT NULL,
  `name` varchar(128) DEFAULT '',
  `operator` varchar(16) NOT NULL,
  `amount` decimal(10,4) DEFAULT 0.0,
  `cap` decimal(10,2) DEFAULT 0.0,
  `apply` tinyint(1) DEFAULT 1 COMMENT '1 base cost, 2 on cascade',
  `breakdown` varchar(1024) DEFAULT NULL COMMENT 'JSON representation of taxes breakdown',
  `ordering` tinyint(2) unsigned DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Table structure for table `#__vikrestaurants_coupon_category`
--

CREATE TABLE IF NOT EXISTS `#__vikrestaurants_coupon_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(48) NOT NULL,
  `description` varchar(2048) DEFAULT '',
  `ordering` int(10) unsigned DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Table structure for table `#__vikrestaurants_media`
--

CREATE TABLE IF NOT EXISTS `#__vikrestaurants_media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(256) NOT NULL,
  `alt` varchar(256) DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `caption` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Table structure for table `#__vikrestaurants_status_code`
--

CREATE TABLE IF NOT EXISTS `#__vikrestaurants_status_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `code` varchar(16) NOT NULL,
  `color` varchar(8) DEFAULT NULL,
  `ordering` int(10) unsigned DEFAULT 1,
  `restaurant` tinyint(1) DEFAULT 1,
  `takeaway` tinyint(1) DEFAULT 1,
  `approved` tinyint(1) DEFAULT 0,
  `reserved` tinyint(1) DEFAULT 0,
  `expired` tinyint(1) DEFAULT 0,
  `cancelled` tinyint(1) DEFAULT 0,
  `paid` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Table structure for table `#__vikrestaurants_lang_status_code`
--

CREATE TABLE IF NOT EXISTS `#__vikrestaurants_lang_status_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `description` varchar(512) NOT NULL,
  `id_status_code` int(10) unsigned NOT NULL,
  `tag` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Table structure for table `#__vikrestaurants_lang_tax`
--

CREATE TABLE IF NOT EXISTS `#__vikrestaurants_lang_tax` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `id_tax` int(10) unsigned NOT NULL,
  `tag` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Table structure for table `#__vikrestaurants_lang_tax_rule`
--

CREATE TABLE IF NOT EXISTS `#__vikrestaurants_lang_tax_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `breakdown` varchar(1024) DEFAULT NULL,
  `id_tax_rule` int(10) unsigned NOT NULL,
  `id_parent` int(10) unsigned NOT NULL,
  `tag` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Table structure for table `#__vikrestaurants_lang_media`
--

CREATE TABLE IF NOT EXISTS `#__vikrestaurants_lang_media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alt` varchar(256) DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `caption` varchar(2048) DEFAULT NULL,
  `image` varchar(256) NOT NULL,
  `tag` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Table structure for table `#__vikrestaurants_mail_text`
--

CREATE TABLE IF NOT EXISTS `#__vikrestaurants_mail_text` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `filters` text DEFAULT NULL,
  `actions` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `ordering` int(10) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Alter for table `#__vikrestaurants_table`
--

ALTER TABLE `#__vikrestaurants_table`
ADD COLUMN `secretkey` varchar(16) DEFAULT NULL AFTER `id_room`;

--
-- Alter for table `#__vikrestaurants_reservation`
--

ALTER TABLE `#__vikrestaurants_reservation`
CHANGE `status` `status` varchar(16) DEFAULT 'W',
ADD COLUMN `total_net` decimal(10,2) DEFAULT 0.0 AFTER `bill_value`,
ADD COLUMN `total_tax` decimal(10,2) DEFAULT 0.0 AFTER `total_net`,
ADD COLUMN `payment_charge` decimal(6,2) DEFAULT 0.0 AFTER `total_tax`,
ADD COLUMN `payment_tax` decimal(6,2) DEFAULT 0.0 AFTER `payment_charge`,
ADD COLUMN `pin` varchar(4) DEFAULT NULL COMMENT 'used to access the ordering page via QR' AFTER `sid`,
ADD COLUMN `pinattempts` int(1) DEFAULT 0 COMMENT 'the number of times the PIN code has been entered erroneously' AFTER `pin`;

--
-- Alter for table `#__vikrestaurants_res_prod_assoc`
--

ALTER TABLE `#__vikrestaurants_res_prod_assoc`
ADD COLUMN `net` decimal(10,2) DEFAULT 0.0 AFTER `price`,
ADD COLUMN `tax` decimal(10,2) DEFAULT 0.0 AFTER `net`,
ADD COLUMN `gross` decimal(10,2) DEFAULT 0.0 AFTER `tax`,
ADD COLUMN `discount` decimal(10,2) DEFAULT 0.0 AFTER `gross`,
ADD COLUMN `tax_breakdown` varchar(1024) DEFAULT NULL AFTER `discount`;

--
-- Alter for table `#__vikrestaurants_section_product`
--

ALTER TABLE `#__vikrestaurants_section_product`
ADD COLUMN `id_tax` int(10) unsigned DEFAULT 0 AFTER `price`;

--
-- Alter for table `#__vikrestaurants_takeaway_reservation`
--

ALTER TABLE `#__vikrestaurants_takeaway_reservation`
CHANGE `status` `status` varchar(16) DEFAULT 'W',
CHANGE `pay_charge` `payment_charge` decimal(6,2) DEFAULT 0.0,
ADD COLUMN `service` varchar(32) DEFAULT 'delivery' AFTER `delivery_service`,
ADD COLUMN `total_net` decimal(10,2) DEFAULT 0.0 AFTER `total_to_pay`,
CHANGE `taxes` `total_tax` decimal(10,2) DEFAULT 0.0 AFTER `total_net`,
ADD COLUMN `payment_tax` decimal(6,2) DEFAULT 0.0 AFTER `payment_charge`,
ADD COLUMN `delivery_tax` decimal(6,2) DEFAULT 0.0 AFTER `delivery_charge`,
ADD COLUMN `asap` tinyint(1) DEFAULT 0 AFTER `preparation_ts`;

--
-- Alter for table `#__vikrestaurants_takeaway_res_prod_assoc`
--

ALTER TABLE `#__vikrestaurants_takeaway_res_prod_assoc`
CHANGE `taxes` `tax` decimal(10,2) DEFAULT 0.0,
ADD COLUMN `net` decimal(10,2) DEFAULT 0.0 AFTER `price`,
ADD COLUMN `gross` decimal(10,2) DEFAULT 0.0 AFTER `tax`,
ADD COLUMN `discount` decimal(10,2) DEFAULT 0.0 AFTER `gross`,
ADD COLUMN `tax_breakdown` varchar(1024) DEFAULT NULL AFTER `discount`;

--
-- Alter for table `#__vikrestaurants_takeaway_menus`
--

ALTER TABLE `#__vikrestaurants_takeaway_menus`
CHANGE `publish_up` `start_publishing` int(11) DEFAULT NULL,
CHANGE `publish_down` `end_publishing` int(11) DEFAULT NULL;

--
-- Alter for table `#__vikrestaurants_takeaway_menus_entry`
--

ALTER TABLE `#__vikrestaurants_takeaway_menus_entry`
ADD COLUMN `id_tax` int(10) unsigned DEFAULT 0 AFTER `price`;

--
-- Alter for table `#__vikrestaurants_takeaway_deal`
--

ALTER TABLE `#__vikrestaurants_takeaway_deal`
CHANGE `type` `__type` tinyint(2) DEFAULT NULL COMMENT '@deprecated 1.9',
CHANGE `amount` `__amount` decimal(10,2) unsigned DEFAULT 0.0 COMMENT '@deprecated 1.9',
CHANGE `percentot` `__percentot` tinyint(1) DEFAULT 1 COMMENT '@deprecated 1.9',
CHANGE `cart_tcost` `__cart_tcost`  decimal(10,2) unsigned DEFAULT 0.0 COMMENT '@deprecated 1.9',
CHANGE `auto_insert` `__auto_insert` tinyint(1) DEFAULT 1 COMMENT '@deprecated 1.9',
CHANGE `min_quantity` `__min_quantity` int(4) unsigned DEFAULT 1 COMMENT '@deprecated 1.9',
ADD COLUMN `type` varchar(64) DEFAULT NULL AFTER `__type`,
ADD COLUMN `params` varchar(2048) DEFAULT NULL;

--
-- Alter for table `#__vikrestaurants_takeaway_deal_product_assoc`
--

ALTER TABLE `#__vikrestaurants_takeaway_deal_product_assoc`
CHANGE `quantity` `__quantity` int(4) unsigned NOT NULL DEFAULT 1 COMMENT '@deprecated 1.9',
CHANGE `required` `__required` tinyint(1) DEFAULT 0 COMMENT '@deprecated 1.9',
ADD COLUMN `params` varchar(2048) DEFAULT NULL;

--
-- Alter for table `#__vikrestaurants_takeaway_deal_free_assoc`
--

ALTER TABLE `#__vikrestaurants_takeaway_deal_free_assoc`
CHANGE `quantity` `__quantity` int(4) unsigned NOT NULL DEFAULT 1 COMMENT '@deprecated 1.9',
ADD COLUMN `params` varchar(2048) DEFAULT NULL;

--
-- Alter for table `#__vikrestaurants_takeaway_delivery_area`
--

ALTER TABLE `#__vikrestaurants_takeaway_delivery_area`
CHANGE `type` `__type` tinyint(1) DEFAULT NULL COMMENT '@deprecated 1.9',
ADD COLUMN `type` varchar(64) DEFAULT NULL AFTER `__type`;

--
-- Alter for table `#__vikrestaurants_specialdays`
--

ALTER TABLE `#__vikrestaurants_specialdays`
ADD COLUMN `custom_shifts` varchar(1024) DEFAULT NULL AFTER `working_shifts`;

--
-- Alter for table `#__vikrestaurants_gpayments`
--

ALTER TABLE `#__vikrestaurants_gpayments`
CHANGE `params` `params` varchar(2048) DEFAULT NULL,
ADD COLUMN `id_tax` int(10) unsigned DEFAULT 0 AFTER `percentot`;

--
-- Alter for table `#__vikrestaurants_custfields`
--

ALTER TABLE `#__vikrestaurants_custfields`
CHANGE `rule` `rule` varchar(32) DEFAULT '0',
ADD COLUMN `description` varchar(2048) DEFAULT NULL,
ADD COLUMN `service` varchar(32) DEFAULT NULL,
ADD COLUMN `locale` varchar(8) DEFAULT '*',
ADD COLUMN `readonly` tinyint(1) unsigned DEFAULT 0 COMMENT 'editable only once';

--
-- Alter for table `#__vikrestaurants_coupons`
--

ALTER TABLE `#__vikrestaurants_coupons`
ADD COLUMN `minpeople` tinyint(2) DEFAULT 0,
ADD COLUMN `remove_gift` tinyint(1),
ADD COLUMN `notes` varchar(512) DEFAULT '',
ADD COLUMN `id_category` int(10) unsigned DEFAULT 0,
ADD COLUMN `start_publishing` datetime DEFAULT NULL,
ADD COLUMN `end_publishing` datetime DEFAULT NULL,
CHANGE `minvalue` `mincost` decimal(10,2) DEFAULT 0.0;

--
-- Alter for table `#__vikrestaurants_res_code`
--

ALTER TABLE `#__vikrestaurants_res_code`
ADD COLUMN `sendmail` tinyint(1) DEFAULT 0 AFTER `rule`;

--
-- Alter for table `#__vikrestaurants_lang_customf`
--

ALTER TABLE `#__vikrestaurants_lang_customf`
ADD COLUMN `description` varchar(2048) DEFAULT NULL AFTER `name`;

--
-- Alter for table `#__vikrestaurants_lang_takeaway_menus_attribute`
--

ALTER TABLE `#__vikrestaurants_lang_takeaway_menus_attribute`
ADD COLUMN `description` varchar(512) DEFAULT NULL AFTER `name`;

--
-- Alter for table `#__vikrestaurants_operator_log`
--

ALTER TABLE `#__vikrestaurants_operator_log`
CHANGE `content` `content` text DEFAULT NULL COMMENT 'a JSON containing the log data';

--
-- Alter for table `#__vikrestaurants_api_login_logs`
--

ALTER TABLE `#__vikrestaurants_api_login_logs`
ADD COLUMN `payload` text DEFAULT NULL AFTER `content`;

--
-- Dumping data for table `#__vikrestaurants_status_code`
--

INSERT INTO `#__vikrestaurants_status_code`
(     `name`, `code`,  `color`, `ordering`, `approved`, `reserved`, `expired`, `cancelled`, `paid`, `restaurant`, `takeaway`) VALUES
('Confirmed',    'C', '008000',          1,          1,          1,         0,           0,      0,            1,          1),
(     'Paid',    'P', '339CCC',          2,          1,          1,         0,           0,      1,            1,          1),
(  'Pending',    'W', 'FF7000',          3,          0,          1,         0,           0,      0,            1,          1),
(  'Removed',    'E', '990000',          4,          0,          0,         1,           0,      0,            1,          1),
('Cancelled',    'X', 'F01B17',          5,          0,          0,         0,           1,      0,            1,          1),
( 'Rejected',    'D', 'BD0045',          6,          0,          0,         1,           1,      0,            1,          1),
( 'Refunded',    'R', '8116C9',          7,          0,          0,         0,           1,      1,            1,          1),
(  'No-Show',    'N', '828282',          8,          1,          1,         0,           0,      0,            1,          1);

--
-- Dumping data for table `#__vikrestaurants_config`
--

INSERT INTO `#__vikrestaurants_config`
(              `param`, `setting`) VALUES
(             'deftax',        ''),
(           'usetaxbd',         0),
(           'tkdeftax',        ''),
(         'tkusetaxbd',         0),
('fields_layout_style', 'default'),
(         'backuptype',    'full'),
(       'backupfolder',        '');

--
-- Map the take-away reservations `service` column
--

UPDATE `#__vikrestaurants_takeaway_reservation`
SET `service` = IF(`delivery_service` = 1, 'delivery', 'takeaway');

--
-- Commit version change
--

UPDATE `#__vikrestaurants_config` SET `setting` = '1.3' WHERE `param` = 'version' LIMIT 1;