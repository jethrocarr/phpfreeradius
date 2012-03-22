--
-- PHPFREERADIUS APPLICATION
--


--
-- 1.3.0 to 1.4.0
--


INSERT INTO `config` (`name`, `value`) VALUES('FEATURE_LOGS_API', '1');
INSERT INTO `config` (`name`, `value`) VALUES('FEATURE_LOGS_AUDIT', '1');
INSERT INTO `config` (`name`, `value`) VALUES('FEATURE_LOGS_ENABLE', '1');
INSERT INTO `config` (`name`, `value`) VALUES('LOG_RETENTION_CHECKTIME', '0');
INSERT INTO `config` (`name`, `value`) VALUES('LOG_RETENTION_PERIOD', '0');


ALTER TABLE `menu` ADD `config` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
UPDATE `menu` SET config='FEATURE_LOGS_ENABLE' WHERE topic='menu_logs' LIMIT 1;

--
-- Set Schema Version
--

UPDATE `config` SET `value` = '20120322' WHERE name='SCHEMA_VERSION' LIMIT 1;


