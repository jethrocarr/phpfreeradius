<?php
/*
	phpfreeradius application libraries/functions

	Provides various functions for phpfreeradius.
*/


@log_debug("start", "");
@log_debug("start", "PHPFREERADIUS LIBRARIES LOADED");
@log_debug("start", "");


// include main code functions
require("inc_nas_devices.php");
require("inc_ldap_authlookup.php");
require("inc_servers.php");
require("inc_api.php");


?>
