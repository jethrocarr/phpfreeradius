<?php
/*
	phpfreeradius_logpush


	Connects to phpfreeradius and then tails the log file for radius and posts
	any new log messages back to phpfreeradius.

	This solution is better than trying to use FIFO pipes since a hang of the
	logging process will not impact radius in any way, nor will a restart of radius
	affect the logger.


	Copyright (c) 2010 Amberdms Ltd

	Licensed under the GNU AGPL.
*/



/*
	CONFIGURATION
*/

require("include/config.php");
require("include/amberphplib/main.php");




/*
	VERIFY LOG FILE ACCESS
*/

if (!is_readable($GLOBALS["config"]["log_file"]))
{
	log_write("error", "script", "Unable to read log file ". $GLOBALS["config"]["log_file"] ."");
	die("Fatal Error");
}




/*
	LOG PUSH CLASS

	We have a class here for handling the actual logging, it's smart enough to re-authenticate if the session
	gets terminated without dropping log messages.

	(sessions could get terminated if remote API server reboots, connection times out, no logs get generated for long
	time periods, etc)
*/


class phpfreeradius_log_main
{
	var $client;


	/*
		authenticate

		Connects to the phpfreeradius API and authenticates the radius server

		Returns
		0		Failure
		1		Success
	*/
	function authenticate()
	{
		log_write("debug", "log_push", "Executing authenticate()");

		/*
			Initiate connection & authenticate to phpfreeradius

		*/
		$this->client = new SoapClient($GLOBALS["config"]["api_url"] ."/phpfreeradius.wsdl");
		$this->client->__setLocation($GLOBALS["config"]["api_url"] ."/phpfreeradius.php");


		// login & get PHP session ID
		try
		{
			log_write("debug", "script", "Authenticating with API as radius server ". $GLOBALS["config"]["api_server_name"] ."...");

			if ($this->client->authenticate($GLOBALS["config"]["api_server_name"], $GLOBALS["config"]["api_auth_key"]))
			{
				log_write("debug", "script", "Authentication successful");

				return 1;
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
	}


	/*
		log_push

		Send a log message to the server

		Fields
		timestamp		UNIX timestamp
		log_type		Category of logs
		log_contents		Log contents

		Results
		0			Failure
		1			Success
	*/
	function log_push($timestamp, $log_type, $log_contents)
	{
		log_write("debug", "script", "Executing log_push(timestamp, log_type, log_contents)");

		try
		{
			$this->client->log_write($timestamp, $log_type, $log_contents);
		}
		catch (SoapFault $exception)
		{
			if ($exception->getMessage() == "ACCESS_DENIED")
			{
				// no longer able to access API - perhaps the session has timed out?
				if ($this->authenticate())
				{
					$this->client->log_write($timestamp, $log_type, $log_contents);
				}
				else
				{
					log_write("error", "script", "Unable to re-establish connection with phpfreeradius");
					die("Fatal Error");
				}
			}
			else
			{	
				log_write("error", "script", "Unknown failure whilst attempting to push log messages - ". $exception->getMessage() ."");
				die("Fatal Error");
			}
		}

	}


	/*
		log_watch

		Use tail to track the file and push any new log messages to phpfreeradius
	*/
	function log_watch()
	{
		while (true)
		{
			// we have a while here to handle the unexpected termination of the tail command
			// by restarting a new connection

			$handle = popen("tail -f ". $GLOBALS["config"]["log_file"] ." 2>&1", 'r');

			while(!feof($handle))
			{
				$buffer = fgets($handle);

				// process the log input
				if (preg_match("/^[\S\s]*\s:\s(\S*):\s([\S\s]*)$/", $buffer, $matches))
				{
					$this->log_push(time(), $matches[1], $matches[2]);
				
					log_write("debug", "script", "Log Recieved: $buffer");
				}
				else
				{
					log_write("debug", "script", "Unprocessable: $buffer");
				}
			}

			pclose($handle);
		}
	}


} // end of phpfreeradius_log_main



// call class
$obj_main		= New phpfreeradius_log_main;

$obj_main->authenticate();
$obj_main->log_watch();

log_write("notification", "script", "Terminating logging process for phpfreeradius");



?>
