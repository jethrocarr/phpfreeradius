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
	Log Pipe File
*/
$config["log_pipe"]		= "/var/run/phpfreeradius_log";
$config["log_owner"]		= "radiusd";



/*
	Radius Configuration Files
*/

$config["freeradius_format"]		= "1";				// version/format of freeradius config files - either 1 (1.x.x) or 2 (2.x.x)
$config["freeradius_file_clients"]	= "/etc/raddb/clients.conf";	// clients/NAS configuration file
$config["freeradius_file_huntgroups"]	= "/etc/raddb/huntgroups";	// huntgroups
$config["freeradius_file_users"]	= "/etc/raddb/users";		// users
$config["freeradius_reload"]		= "/etc/init.d/radiusd reload";	// command to reload radius configuration




// force debugging on for all users + scripts
// (note: debugging can be enabled on a per-user basis by an admin via the web interface)
//$_SESSION["user"]["debug"] = "on";


?>
