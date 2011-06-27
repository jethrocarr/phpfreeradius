--
-- PHPFREERADIUS APPLICATION
--
-- Inital database install SQL.
--

CREATE DATABASE `phpfreeradius` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `phpfreeradius`;




--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`name`, `value`) VALUES('APP_MYSQL_DUMP', '/usr/bin/mysqldump');
INSERT INTO `config` (`name`, `value`) VALUES('APP_PDFLATEX', '/usr/bin/pdflatex');
INSERT INTO `config` (`name`, `value`) VALUES('AUTH_METHOD', 'ldaponly');
INSERT INTO `config` (`name`, `value`) VALUES('BLACKLIST_ENABLE', '');
INSERT INTO `config` (`name`, `value`) VALUES('BLACKLIST_LIMIT', '10');
INSERT INTO `config` (`name`, `value`) VALUES('DATA_STORAGE_LOCATION', 'use_database');
INSERT INTO `config` (`name`, `value`) VALUES('DATA_STORAGE_METHOD', 'database');
INSERT INTO `config` (`name`, `value`) VALUES('DATEFORMAT', 'yyyy-mm-dd');
INSERT INTO `config` (`name`, `value`) VALUES('LANGUAGE_DEFAULT', 'en_us');
INSERT INTO `config` (`name`, `value`) VALUES('LANGUAGE_LOAD', 'preload');
INSERT INTO `config` (`name`, `value`) VALUES('PATH_TMPDIR', '/tmp');
INSERT INTO `config` (`name`, `value`) VALUES('PHONE_HOME', 'disabled');
INSERT INTO `config` (`name`, `value`) VALUES('PHONE_HOME_TIMER', '');
INSERT INTO `config` (`name`, `value`) VALUES('SCHEMA_VERSION', '20100420');
INSERT INTO `config` (`name`, `value`) VALUES('SUBSCRIPTION_ID', '');
INSERT INTO `config` (`name`, `value`) VALUES('SUBSCRIPTION_SUPPORT', 'opensource');
INSERT INTO `config` (`name`, `value`) VALUES('SYNC_STATUS_CONFIG', '1');
INSERT INTO `config` (`name`, `value`) VALUES('TIMEZONE_DEFAULT', 'SYSTEM');
INSERT INTO `config` (`name`, `value`) VALUES('UPLOAD_MAXBYTES', '5242880');

-- --------------------------------------------------------

--
-- Table structure for table `file_uploads`
--

CREATE TABLE IF NOT EXISTS `file_uploads` (
  `id` int(11) NOT NULL auto_increment,
  `customid` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL,
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `file_name` varchar(255) NOT NULL,
  `file_size` varchar(255) NOT NULL,
  `file_location` char(2) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `file_uploads`
--


-- --------------------------------------------------------

--
-- Table structure for table `file_upload_data`
--

CREATE TABLE IF NOT EXISTS `file_upload_data` (
  `id` int(11) NOT NULL auto_increment,
  `fileid` int(11) NOT NULL default '0',
  `data` blob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table for use as database-backed file storage system' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `file_upload_data`
--


-- --------------------------------------------------------

--
-- Table structure for table `journal`
--

CREATE TABLE IF NOT EXISTS `journal` (
  `id` int(11) NOT NULL auto_increment,
  `locked` tinyint(1) NOT NULL default '0',
  `journalname` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL,
  `userid` int(11) NOT NULL default '0',
  `customid` int(11) NOT NULL default '0',
  `timestamp` bigint(20) unsigned NOT NULL default '0',
  `content` text NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `journalname` (`journalname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `journal`
--


-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE IF NOT EXISTS `language` (
  `id` int(11) NOT NULL auto_increment,
  `language` varchar(20) NOT NULL,
  `label` varchar(255) NOT NULL,
  `translation` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `language` (`language`),
  KEY `label` (`label`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(1, 'en_us', 'username_phpfreeradius', 'Username');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(2, 'en_us', 'password_phpfreeradius', 'Password');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(3, 'en_us', 'menu_logs', 'Radius Logs');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(4, 'en_us', 'menu_nasdevices', 'NAS Devices');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(5, 'en_us', 'menu_radius_servers', 'Radius Servers');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(6, 'en_us', 'menu_configuration', 'Configuration');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(7, 'en_us', 'timestamp', 'Timestamp');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(8, 'en_us', 'server_name', 'Radius Server Name');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(9, 'en_us', 'log_type', 'Log Type/Category');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(10, 'en_us', 'log_contents', 'Log Contents');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(11, 'en_us', 'filter_searchbox', 'Searchbox');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(12, 'en_us', 'tbl_lnk_details', 'details');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(13, 'en_us', 'tbl_lnk_logs', 'logs');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(14, 'en_us', 'tbl_lnk_delete', 'delete');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(15, 'en_us', 'nas_hostname', 'NAS Hostname');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(16, 'en_us', 'nas_address', 'NAS Address');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(17, 'en_us', 'nas_type', 'NAS Type');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(18, 'en_us', 'nas_ldapgroup', 'LDAP Radius Group');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(19, 'en_us', 'nas_description', 'Description');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(20, 'en_us', 'nas_secret', 'Secret/Passphrase');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(21, 'en_us', 'nas_details', 'NAS Details');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(22, 'en_us', 'nas_auth', 'NAS Authentication');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(23, 'en_us', 'submit', 'Save Changes');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(24, 'en_us', 'delete_confirm', 'Confirm Deletion');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(25, 'en_us', 'menu_overview', 'Overview');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(26, 'en_us', 'server_description', 'Description');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(27, 'en_us', 'sync_status', 'Synchronisation Status');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(28, 'en_us', 'status_unsynced', 'Unsynchronised');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(29, 'en_us', 'status_synced', 'Synchronised');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(30, 'en_us', 'server_details', 'Radius Server Details');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(31, 'en_us', 'server_api', 'Radius Server API');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(32, 'en_us', 'api_auth_key', 'API Auth Key');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(33, 'en_us', 'server_status', 'Radius Server Status');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(34, 'en_us', 'sync_status_config', 'Sync Status - Config');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(35, 'en_us', 'sync_status_log', 'Sync Status - Logging');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(36, 'en_us', 'server_delete', 'Delete Radius Server');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(37, 'en_us', 'config_dateandtime', 'Date/Time Configuration');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(38, 'en_us', 'help_api_auth_key', 'Used to authenticate radius server connections to phpfreeradius.');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(39, 'en_us', 'help_nas_address', 'IP address, CIDR subnet or hostname of the NAS device');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(40, 'en_us', 'help_nas_hostname', 'Hostname of the NAS for record purposes only.');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(41, 'en_us', 'menu_nasdevices_view', 'View NAS Devices');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(42, 'en_us', 'menu_nasdevices_add', 'Add NAS Device');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(43, 'en_us', 'menu_radius_servers_view', 'View Radius Servers');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES(44, 'en_us', 'menu_radius_servers_add', 'Add Radius Server');

-- --------------------------------------------------------

--
-- Table structure for table `language_avaliable`
--

CREATE TABLE IF NOT EXISTS `language_avaliable` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(5) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `language_avaliable`
--

INSERT INTO `language_avaliable` (`id`, `name`) VALUES(1, 'en_us');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL auto_increment,
  `id_server` int(11) NOT NULL,
  `id_nas` int(11) NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `log_type` char(10) NOT NULL,
  `log_contents` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL auto_increment,
  `priority` int(11) NOT NULL default '0',
  `parent` varchar(50) NOT NULL,
  `topic` varchar(50) NOT NULL,
  `link` varchar(50) NOT NULL,
  `permid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=204 ;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(171, 900, 'top', 'menu_configuration', 'admin/config.php', 2);
INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(181, 100, 'top', 'menu_overview', 'home.php', 0);
INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(184, 200, 'top', 'menu_logs', 'logs/logs.php', 2);
INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(185, 300, 'top', 'menu_nasdevices', 'nasdevices/nasdevices.php', 2);
INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(187, 500, 'top', 'menu_radius_servers', 'servers/servers.php', 2);
INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(188, 301, 'menu_nasdevices', 'menu_nasdevices_view', 'nasdevices/nasdevices.php', 2);
INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(189, 302, 'menu_nasdevices', 'menu_nasdevices_add', 'nasdevices/add.php', 2);
INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(190, 310, 'menu_nasdevices_view', '', 'nasdevices/view.php', 2);
INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(191, 310, 'menu_nasdevices_view', '', 'nasdevices/logs.php', 2);
INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(192, 310, 'menu_nasdevices_view', '', 'nasdevices/delete.php', 2);
INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(199, 501, 'menu_radius_servers', 'menu_radius_servers_view', 'servers/servers.php', 2);
INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(200, 502, 'menu_radius_servers', 'menu_radius_servers_add', 'servers/add.php', 2);
INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(201, 510, 'menu_radius_servers_view', '', 'servers/view.php', 2);
INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(202, 510, 'menu_radius_servers_view', '', 'servers/logs.php', 2);
INSERT INTO `menu` (`id`, `priority`, `parent`, `topic`, `link`, `permid`) VALUES(203, 510, 'menu_radius_servers_view', '', 'servers/delete.php', 2);

-- --------------------------------------------------------

--
-- Table structure for table `nas_devices`
--

CREATE TABLE IF NOT EXISTS `nas_devices` (
  `id` int(11) NOT NULL auto_increment,
  `nas_hostname` varchar(255) character set latin1 NOT NULL,
  `nas_address` varchar(255) character set latin1 NOT NULL,
  `nas_secret` varchar(255) character set latin1 NOT NULL,
  `nas_type` int(11) NOT NULL,
  `nas_ldapgroup` varchar(255) character set latin1 NOT NULL,
  `nas_description` text character set latin1 NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;



-- --------------------------------------------------------

--
-- Table structure for table `nas_types`
--

CREATE TABLE IF NOT EXISTS `nas_types` (
  `id` int(11) NOT NULL auto_increment,
  `nas_type` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `nas_types`
--

INSERT INTO `nas_types` (`id`, `nas_type`) VALUES(1, 'cisco');
INSERT INTO `nas_types` (`id`, `nas_type`) VALUES(2, 'computone');
INSERT INTO `nas_types` (`id`, `nas_type`) VALUES(3, 'max40xx');
INSERT INTO `nas_types` (`id`, `nas_type`) VALUES(4, 'multitech');
INSERT INTO `nas_types` (`id`, `nas_type`) VALUES(5, 'netserver');
INSERT INTO `nas_types` (`id`, `nas_type`) VALUES(6, 'pathras');
INSERT INTO `nas_types` (`id`, `nas_type`) VALUES(7, 'patton');
INSERT INTO `nas_types` (`id`, `nas_type`) VALUES(8, 'portslave');
INSERT INTO `nas_types` (`id`, `nas_type`) VALUES(9, 'tc');
INSERT INTO `nas_types` (`id`, `nas_type`) VALUES(10, 'usrhiper');
INSERT INTO `nas_types` (`id`, `nas_type`) VALUES(11, 'other');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Stores all the possible permissions' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `value`, `description`) VALUES(1, 'disabled', 'Enabling the disabled permission will prevent the user from being able to login.');
INSERT INTO `permissions` (`id`, `value`, `description`) VALUES(2, 'radiusadmins', 'Provides access to user and configuration management features (note: any user with admin can provide themselves with access to any other section of this program)');

-- --------------------------------------------------------

--
-- Table structure for table `radius_servers`
--

CREATE TABLE IF NOT EXISTS `radius_servers` (
  `id` int(11) NOT NULL auto_increment,
  `server_name` varchar(255) NOT NULL,
  `server_description` text NOT NULL,
  `api_auth_key` varchar(255) NOT NULL,
  `api_sync_config` bigint(20) NOT NULL,
  `api_sync_log` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `realname` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_salt` varchar(20) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `time` bigint(20) NOT NULL default '0',
  `ipaddress` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `ipaddress` (`ipaddress`),
  KEY `time` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User authentication system.' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `users`
--


-- --------------------------------------------------------

--
-- Table structure for table `users_blacklist`
--

CREATE TABLE IF NOT EXISTS `users_blacklist` (
  `id` int(11) NOT NULL auto_increment,
  `ipaddress` varchar(15) NOT NULL,
  `failedcount` int(11) NOT NULL default '0',
  `time` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Prevents automated login attacks.' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `users_blacklist`
--


-- --------------------------------------------------------

--
-- Table structure for table `users_options`
--

CREATE TABLE IF NOT EXISTS `users_options` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `users_options`
--


-- --------------------------------------------------------

--
-- Table structure for table `users_permissions`
--

CREATE TABLE IF NOT EXISTS `users_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `permid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores user permissions.' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `users_permissions`
--


-- --------------------------------------------------------

--
-- Table structure for table `users_sessions`
--

CREATE TABLE IF NOT EXISTS `users_sessions` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `authkey` varchar(40) NOT NULL,
  `ipaddress` varchar(15) NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;




--
-- 1.0.0 alpha 1 to 1.0.0 upgrades
--

INSERT INTO `config` (`name`, `value`) VALUES ('DEFAULT_NAS_PASSWORD', '');

INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'filter_num_logs_rows', 'Max Number of Log');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'filter_id_radius_server', 'ID of Radius Server');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'config_defaults', 'Default Options');


--
-- 1.0.0 - no upgrades
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
-- 1.0.2 to 1.1.0_beta_1 upgrade
--

INSERT INTO `config` (`name`, `value`) VALUES ('LOG_UPDATE_INTERVAL', '10');

ALTER TABLE `nas_devices` ADD `nas_shortname` VARCHAR( 30 ) NOT NULL AFTER `nas_hostname` ;

INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'nas_shortname', 'NAS ShortName');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'help_nas_shortname', 'Short version of the hostname - maximum 30 chars');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'help_nas_shortname_inline', 'Leave blank for auto-generate');
INSERT INTO `language` (`id`, `language`, `label`, `translation`) VALUES (NULL, 'en_us', 'filter_log_debug', 'Hide Debug Log');



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



