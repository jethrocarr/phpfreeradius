--
-- PHPFREERADIUS APPLICATION
--


--
-- 1.0.1 to 1.0.2 upgrade
--

INSERT INTO `config` (`name`, `value`) VALUES ('NAMEDMANAGER_FEATURE', 'disabled');
INSERT INTO `config` (`name`, `value`) VALUES ('NAMEDMANAGER_API_URL', '');
INSERT INTO `config` (`name`, `value`) VALUES ('NAMEDMANAGER_API_KEY', '');
INSERT INTO `config` (`name`, `value`) VALUES ('NAMEDMANAGER_DEFAULT_A', '');
INSERT INTO `config` (`name`, `value`) VALUES ('NAMEDMANAGER_DEFAULT_PTR', '');

ALTER TABLE `nas_devices` ADD `nas_dns_record_a` INT UNSIGNED NOT NULL, ADD `nas_dns_record_ptr` INT UNSIGNED NOT NULL, ADD `nas_dns_record_ptr_altip` VARCHAR( 15 ) NOT NULL;

INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'nas_dns', 'NAS DNS Configuration');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'nas_dns_record_a', 'Create DNS Record');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'nas_dns_record_ptr', 'Create Reverse Record');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'help_nas_dns_record_a', 'Select if you want a DNS record to be added for this NAS device.');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'help_nas_dns_record_ptr', 'Select if you want a reverse DNS record created for the IP address of this NAS.');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'nas_dns_record_ptr_altip', 'Alternate IP Address');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'help_nas_dns_record_ptr_altip', 'If you want the DNS record to use a different IP address, specify here.');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'nas_address_type', 'NAS Address Type');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'ipv4_single', 'Single IPv4 Address');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'ipv4_range', 'IPv4 Network Range');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'hostname', 'Use a DNS name for the NAS');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'nas_address_ipv4', 'IPv4 Address');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'help_nas_address_ipv4', '(eg: 192.168.0.2)');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'nas_address_ipv4_range', 'IPv4 Network Range');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'help_nas_address_ipv4_range', '(eg: 192.168.0.0/24)');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'nas_address_host', 'NAS Hostname');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'help_nas_address_host', '(eg: nas5.example.com)');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'help_namedmanager_default_a', 'Create an DNS A record by default for NAS devices.');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'help_namedmanager_default_ptr', 'Create a DNS reverse (PTR) record by default for NAS devices.');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'help_namedmanager_api_url', 'URL for NamedManager (eg: http://example.com/namedmanager)');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'help_namedmanager_api_key', 'The value of ADMIN_API_KEY from the NamedManager installation.');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(NULL, 'en_us', 'help_nas_dns_record_na', 'Sorry, you can only setup DNS records for single IP NAS devices.');



--
-- Set Schema Version
--

UPDATE `config` SET `value` = '20100608' WHERE name='SCHEMA_VERSION' LIMIT 1;


