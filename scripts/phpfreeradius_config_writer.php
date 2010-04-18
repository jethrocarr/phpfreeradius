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

include("include/config.php");




/*
	Initiate connection & authenticate to phpfreeradius

*/
$client = new SoapClient("$url/phpfreeradius/phpfreeradius.wsdl");
$client->__setLocation("$url/phpfreeradius/phpfreeradius.php");


// login & get PHP session ID
try
{
	$client->authenticate($GLOBALS["api_server_name"], $GLOBALS["api_auth_key"]);
}
catch (SoapFault $exception)
{
	die( "Fatal Error: ". $exception->getMessage() ."\n");
}



/*
	Find out if any config has changed
*/
try
{
	if (!$client->server_check_current())
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
