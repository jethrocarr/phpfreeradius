<?php
/*
	phpfreeradius_config_writer

	Connects to phpfreeradius and fetching NAS/Huntgroup configuration which
	is then written to the filesystem.


	Copyright (c) 2010 Amberdms Ltd

	Licensed under the GNU AGPL.
*/



/*
	CONFIGURATION
*/

require("include/config.php");
require("include/amberphplib/main.php");


/*
	Initiate connection & authenticate to phpfreeradius

*/
$client = new SoapClient($GLOBALS["config"]["api_url"] ."/phpfreeradius.wsdl");
$client->__setLocation($GLOBALS["config"]["api_url"] ."/phpfreeradius.php");


// login & get PHP session ID
try
{
	log_write("debug", "script", "Authenticating with API as radius server ". $GLOBALS["config"]["api_server_name"] ."...");

	if ($client->authenticate($GLOBALS["config"]["api_server_name"], $GLOBALS["config"]["api_auth_key"]))
	{
		log_write("debug", "script", "Authentication successful");
	}

}
catch (SoapFault $exception)
{
	if ($exception->getMessage() == "ACCESS_DENIED")
	{
		log_write("error", "script", "Unable to authenticate with phpfreeradius API - check that auth API key and server name are valid");
		die("Fatal Error");
	}
	else
	{	
		log_write("error", "script", "Unknown failure whilst attempting to authenticate with the API - ". $exception->getMessage() ."");
		die("Fatal Error");
	}
}



/*
	Find out if any config has changed
*/
try
{
	if (!$client->server_config_current())
	{
		/*
			Configuration is out of date - we need to fetch all the NAS records
		*/

		log_write("debug", "script", "Configuration is out of date, fetching new NAS configuration via API");



	}
	else
	{
		// all good
		log_write("debug", "script", "System configuration is uptodate, no changes nessacary");
	}
}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");
}




/*
	Write Configuration
*/






?>
