ALTER TABLE `#__vikrestaurants_shifts`
ADD COLUMN `days` varchar(16) DEFAULT NULL AFTER `to`;

ALTER TABLE `#__vikrestaurants_stats_widget`
ADD COLUMN `id_user` int(10) unsigned DEFAULT 0 NOT NULL AFTER `id`;

INSERT INTO `#__vikrestaurants_config` (`param`, `setting`) VALUES
('wizardstate', 1);