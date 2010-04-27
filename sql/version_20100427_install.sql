--
-- PHPFREERADIUS APPLICATION
--


--
-- 1.0.0 alpha 1 to 1.0.0 upgrade
--

INSERT INTO `config` (`name`, `value`) VALUES ('DEFAULT_NAS_PASSWORD', '');

INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'filter_num_logs_rows', 'Max Number of Log');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'filter_id_radius_server', 'ID of Radius Server');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'config_defaults', 'Default Options');



--
-- Set Schema Version
--

UPDATE `config` SET `value` = '20100427' WHERE name='SCHEMA_VERSION' LIMIT 1;


