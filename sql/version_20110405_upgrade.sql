--
-- PHPFREERADIUS APPLICATION
--


--
-- 1.1.0_beta_1 to 1.1.0
--


CREATE TABLE  `nas_stationid` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , `id_nas` INT UNSIGNED NOT NULL , `station_id` VARCHAR( 255 ) NOT NULL , `nas_ldapgroup` VARCHAR( 255 ) NOT NULL) ENGINE = INNODB;

INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'nas_station_description', 'This section defines what users are permitted to authenticate against the NAS device.\n\nFor a simple NAS you will just want to set the default LDAP group option, which will only allow members of the configured group access to the NAS.');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'nas_station_description2', 'For a more complex multi-service device, such as a NAS with radius for administrator logins AND radius for a service such as VPN or PPPoE, you may want to set each service\'s called-station-id, which allows authentication for any radius requests with that ID to be limited to a specific service.');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'nas_auth_users', 'NAS User/Group Authentication');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'header_stationid', 'Called Station ID');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'header_ldap_group', 'LDAP Group');

--
-- Set Schema Version
--

UPDATE `config` SET `value` = '20110405' WHERE name='SCHEMA_VERSION' LIMIT 1;


