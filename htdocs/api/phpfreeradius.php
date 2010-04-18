<?php
/*
	PHPFREERADIUS SOAP API

	Provides functions for sending logs to phpfreeradius as well as fetching configuration
	information for NAS devices and huntgroups.

	Refer to the Developer API documentation for information on using this service
	as well as sample code.
*/


// include libraries
require("../include/config.php");
require("../include/amberphplib/main.php");
require("../include/application/main.php");


class api_phpfreeradius
{
	var $auth_server;		// ID of the radius server that has authenticated.
	var $auth_online;		// set to 1 if authenticated


	/*
		authenticate

		Authenticates a SOAP client call using the SOAP_API_KEY configuration option to enable/prevent access

		Returns
		0	Failure
		#	ID of the radius server authenticated as
	*/
	function authenticate($server_name, $api_auth_key)
	{
		log_write("debug", "api_phpfreeradius", "Executing authenticate($server_name, $api_auth_key)");

		// sanitise input
		$server_name	= @security_script_input_predefined("any", $server_name);
		$api_auth_key	= @security_script_input_predefined("any", $api_auth_key);

		if (!$server_name || $server_name == "error" || !$api_auth_key || $api_auth_key == "error")
		{
			throw new SoapFault("Sender", "INVALID_INPUT");
		}


		// verify input
		$sql_obj		= New sql_query;
		$sql_obj->string	= "SELECT id FROM radius_servers WHERE server_name='$server_name' AND api_auth_key='$api_auth_key' LIMIT 1";
		$sql_obj->execute();

		if ($sql_obj->num_rows())
		{
			$sql_obj->fetch_array();

			$this->auth_online	= 1;
			$this->auth_server	= $sql_obj->data[0]["id"];

			return $this->auth_server;
		}
		else
		{
			throw new SoapFault("Sender", "INVALID_ID");
		}

	} // end of authenticate




	/*
		log_write

		Fields
		timestamp	UNIX timestamp
		log_type	Category (max 10 char)
		log_contents	Contents of log message
	*/

	function log_write($timestamp, $log_type, $log_contents)
	{
		log_write("debug", "api_phpfreeradius", "Executing get_customer_from_by_code($code_customer)");

		if ($this->auth_online)
		{
			// sanitise input
			$code_customer = @security_script_input_predefined("any", $code_customer);

			if (!$code_customer || $code_customer == "error")
			{
				throw new SoapFault("Sender", "INVALID_INPUT");
			}

			
			// fetch the customer ID
			$sql_obj		= New sql_query;
			$sql_obj->string	= "SELECT id FROM customers WHERE code_customer='$code_customer' LIMIT 1";
			$sql_obj->execute();

			if ($sql_obj->num_rows())
			{
				$sql_obj->fetch_array();

				return $sql_obj->data[0]["id"];
			}
			else
			{
				throw new SoapFault("Sender", "INVALID_ID");
			}
		}
		else
		{
			throw new SoapFault("Sender", "ACCESS_DENIED");
		}

	} // end of log_write



	/*
		server_config_current

		Return whether or not the connected server is out of date with configuration or not.

		Returns
		0	Out of Sync
		1	All Good
	*/
	function server_config_current()
	{
		log_write("debug", "api_phpfreeradius", "Executing server_config_current()");


		if ($this->auth_online)
		{
			$obj_server		= New radius_server;
			$obj_server->id		= $this->auth_server;

			$obj_server->load_data();

			if ($obj_server->data["sync_status_config"])
			{
				log_write("debug", "api_phpfreeradius", "Configuration is OUT OF SYNC!");

				return 0;
			}
			else
			{
				log_write("debug", "api_phpfreeradius", "Configuration is uptodate");

				return 1;
			}
		}
		else
		{
			throw new SoapFault("Sender", "ACCESS_DENIED");
		}

	} // end of server_config_current


				
} // end of api_phpfreeradius class



// define server
$server = new SoapServer("phpfreeradius.wsdl");
$server->setClass("api_phpfreeradius");
$server->handle();



?>

