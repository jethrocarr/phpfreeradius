--
-- PHPFREERADIUS APPLICATION
--


--
-- 1.0.2 to 1.1.0_beta_1 upgrade
--

INSERT INTO `config` (`name`, `value`) VALUES ('LOG_UPDATE_INTERVAL', '10');

ALTER TABLE `nas_devices` ADD `nas_shortname` VARCHAR( 30 ) NOT NULL AFTER `nas_hostname` ;

INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'nas_shortname', 'NAS ShortName');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'help_nas_shortname', 'Short version of the hostname - maximum 30 chars');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'help_nas_shortname_inline', 'Leave blank for auto-generate');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'filter_log_debug', 'Hide Debug Log');


--
-- Set Schema Version
--

UPDATE `config` SET `value` = '20100803' WHERE name='SCHEMA_VERSION' LIMIT 1;


