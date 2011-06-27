--
-- PHPFREERADIUS APPLICATION
--


--
-- 1.2.1 to 1.2.2
--

ALTER TABLE  `nas_devices` ADD  `nas_address_2` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER  `nas_address`;

INSERT INTO `language` (`language`, `label`, `translation`) VALUES ('en_us', 'nas_address_2', 'IPv4 Address (Secondary)');
INSERT INTO `language` (`language`, `label`, `translation`) VALUES ('en_us', 'help_nas_address_2', 'Some NAS devices identify them with a different IP than their network interface (eg routerID address)');
INSERT INTO `language` (`language`, `label`, `translation`) VALUES ('en_us', 'help_nas_address_2_inline', 'Optional IPv4 (eg 192.168.1.2)');


--
-- Set Schema Version
--

UPDATE `config` SET `value` = '20110628' WHERE name='SCHEMA_VERSION' LIMIT 1;


