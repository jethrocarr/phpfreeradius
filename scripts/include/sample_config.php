<?php
/*
	Sample Configuration File

	Copy this file to config-settings.php

	This file should be read-only to the user running the radius configuration update scripts.
*/



/*
	API Configuration
*/
$config["api_url"]		= "http://devel-webapps.local.amberdms.com/development/amberdms/phpfreeradius/htdocs/";		// Application Install Location
$config["api_server_name"]	= "devel-auth-openldap.local.amberdms.com";
$config["api_auth_key"]		= "ultrahighsecretkeyok";

// force debugging on for all users + scripts
// (note: debugging can be enabled on a per-user basis by an admin via the web interface)
//$_SESSION["user"]["debug"] = "on";


?>
