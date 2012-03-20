<?php
/*
	Sample Configuration File

	Copy this file to config-settings.php

	This file should be read-only to the user running the radius configuration update scripts.
*/



/*
	API Configuration
*/
$config["api_url"]		= "http://example.com/phpfreeradius";			// Application Install Location
$config["api_server_name"]	= "radius.example.com";
$config["api_auth_key"]		= "ultrahighsecretkey";


/*
	LDAP Configuration
*/
$config["ldap_groupdn"]		= "ou=Group,ou=auth,dc=example,dc=com";


/*
	Log File Location

	(must be readable by the user running the phpfreeradius_logpush script)
*/
$config["log_file"]		= "/var/log/radius";



/*
	Lock File

	Used to prevent clashes when multiple instances are accidently run.
*/

$config["lock_file"]		= "/var/lock/phpfreeradius_lock";



/*
	Radius Configuration Files
*/

$config["freeradius_format"]		= "1";				// version/format of freeradius config files - either 1 (1.x.x) or 2 (2.x.x)
$config["freeradius_file_clients"]	= "/etc/raddb/clients.conf";	// clients/NAS configuration file
$config["freeradius_file_huntgroups"]	= "/etc/raddb/huntgroups";	// huntgroups
$config["freeradius_file_users"]	= "/etc/raddb/users";		// users
$config["freeradius_reload"]		= "/etc/init.d/radiusd reload";	// command to reload radius configuration
$config["freeradius_status"]		= "/etc/init.d/radiusd status";	// command to report process status
$config["freeradius_test"]		= "/usr/sbin/radiusd -XC";	// command to test radius configuration (FR 2.x.x only)



// force debugging on for all users + scripts
// (note: debugging can be enabled on a per-user basis by an admin via the web interface)
//$_SESSION["user"]["debug"] = "on";


?>
