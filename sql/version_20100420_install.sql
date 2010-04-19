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
-- Set Schema Version
--

UPDATE `config` SET `value` = '20100420' WHERE name='SCHEMA_VERSION' LIMIT 1;


