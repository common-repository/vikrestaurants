--
-- Table structure for table `#__vikrestaurants_res_prod_assoc`
--

ALTER TABLE `#__vikrestaurants_res_prod_assoc`
ADD COLUMN `servingnumber` tinyint(1) DEFAULT 0 AFTER `notes`;

--
-- Table structure for table `#__vikrestaurants_takeaway_menus`
--

ALTER TABLE `#__vikrestaurants_takeaway_menus`
ADD COLUMN `layout` varchar(32) DEFAULT 'list';

--
-- Table structure for table `#__vikrestaurants_takeaway_res_prod_assoc`
--

ALTER TABLE `#__vikrestaurants_takeaway_res_prod_assoc`
ADD COLUMN `rescode` int(4) DEFAULT 0,
ADD COLUMN `status` tinyint(1) DEFAULT NULL;

--
-- Dumping data for table `#__vikrestaurants_config`
--

INSERT INTO `#__vikrestaurants_config`
(        `param`, `setting`) VALUES
('servingnumber',         0),
(     'cancunit',    'days'),
(   'tkcancunit',    'days');

--
-- Commit version change
--

UPDATE `#__vikrestaurants_config` SET `setting` = '1.3.3' WHERE `param` = 'version' LIMIT 1;