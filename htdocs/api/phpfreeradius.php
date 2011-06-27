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
		constructor
	*/
	function api_phpfreeradius()
	{
		$this->auth_server	= $_SESSION["auth_server"];
		$this->auth_online	= $_SESSION["auth_online"];
	}



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

			$this->auth_online		= 1;
			$this->auth_server		= $sql_obj->data[0]["id"];

			$_SESSION["auth_online"]	= $this->auth_online;
			$_SESSION["auth_server"]	= $this->auth_server;

			return $this->auth_server;
		}
		else
		{
			throw new SoapFault("Sender", "INVALID_ID");
		}

	} // end of authenticate




	/*
		log_write

		Writes a new log value to the database

		Fields
		timestamp		UNIX timestamp
		log_type		Category (max 10 char)
		log_contents		Contents of log message
	*/

	function log_write($timestamp, $log_type, $log_contents)
	{
		log_write("debug", "api_phpfreeradius", "Executing get_customer_from_by_code($code_customer)");

		if ($this->auth_online)
		{
			// sanitise input
			$timestamp	= @security_script_input_predefined("int", $timestamp);
			$log_type	= @security_script_input_predefined("any", $log_type);
			$log_contents	= @security_script_input_predefined("any", $log_contents);

			if (!$timestamp || $timestamp == "error" || !$log_type || $log_type == "error" || !$log_contents || $log_contents == "error")
			{
				throw new SoapFault("Sender", "INVALID_INPUT");
			}

			// write log
			$obj_log 		= New radius_logs;
			$obj_log->id_server	= $this->auth_server;

			$obj_log->log_push($timestamp, $log_type, $log_contents);
		}
		else
		{
			throw new SoapFault("Sender", "ACCESS_DENIED");
		}

	} // end of log_write



	/*
		check_update_version

		Return whether or not the connected server is out of date with configuration or not.

		Returns
		0	Radius server has the latest configuration
		#	Out of date, timestamp version ID returned
	*/
	function check_update_version()
	{
		log_write("debug", "api_phpfreeradius", "Executing check_update_version()");


		if ($this->auth_online)
		{
			$obj_server		= New radius_server;
			$obj_server->id		= $this->auth_server;

			$obj_server->load_data();

			if ($obj_server->data["sync_status_config"])
			{
				log_write("debug", "api_phpfreeradius", "Configuration is OUT OF SYNC!");

				return sql_get_singlevalue("SELECT value FROM config WHERE name='SYNC_STATUS_CONFIG' LIMIT 1");
			}
			else
			{
				log_write("debug", "api_phpfreeradius", "Configuration is all up-to-date");

				return 0;
			}
		}
		else
		{
			throw new SoapFault("Sender", "ACCESS_DENIED");
		}

	} // end of check_update_version



	/*
		set_update_version

		Update the version field for the specific radius server

		Fields
		version		Timestamp version of the configuration applied - should be what as originally supplied
				with the check_update_version function.

		Returns
		0		Failure
		1		Success
	*/
	function set_update_version($version)
	{
		log_write("debug", "api_phpfreeradius", "Executing set_update_version($version)");


		if ($this->auth_online)
		{
			$obj_server		= New radius_server;
			$obj_server->id		= $this->auth_server;

			return $obj_server->action_update_config_version($version);
		}
		else
		{
			throw new SoapFault("Sender", "ACCESS_DENIED");
		}

	} // end of set_update_version



	/*
		fetch_nas_config

		Fetches and returns an array of all the NAS configuration, used for writing
		configuration files for FreeRadius.

		Returns
		0		Failure
		array		NAS configuration
	*/
	function fetch_nas_config()
	{
		log_write("debug", "api_phpfreeradius", "Executing fetch_nas_config()");


		if ($this->auth_online)
		{
			// fetch NAS configuration
			$sql_obj		= New sql_query;
			$sql_obj->string	= "SELECT nas_devices.id as id, nas_hostname, nas_shortname, nas_address, nas_address_2, nas_secret, nas_types.nas_type as nas_type, nas_ldapgroup, nas_description FROM nas_devices LEFT JOIN nas_types ON nas_types.id = nas_devices.nas_type";
			$sql_obj->execute();

			if ($sql_obj->num_rows())
			{
				$sql_obj->fetch_array();

				$return		= array();
				$return_tmp	= array();

				foreach ($sql_obj->data as $data_nas)
				{
					$return_tmp			= array();

					// general NAS information
					$return_tmp["nas_hostname"]	= $data_nas["nas_hostname"];
					$return_tmp["nas_shortname"]	= $data_nas["nas_shortname"];
					$return_tmp["nas_address"]	= $data_nas["nas_address"];
					$return_tmp["nas_address_2"]	= $data_nas["nas_address_2"];
					$return_tmp["nas_secret"]	= $data_nas["nas_secret"];
					$return_tmp["nas_type"]		= $data_nas["nas_type"];
					$return_tmp["nas_ldapgroup"]	= $data_nas["nas_ldapgroup"];
					$return_tmp["nas_description"]	= $data_nas["nas_description"];

					// additional conditions
					$return_tmp["nas_conditions"]	= array();

					$sql_obj_cond		= New sql_query;
					$sql_obj_cond->string	= "SELECT station_id, nas_ldapgroup FROM nas_stationid WHERE id_nas='". $data_nas["id"] ."'";
					$sql_obj_cond->execute();

					if ($sql_obj_cond->num_rows())
					{
						$sql_obj_cond->fetch_array();

						foreach ($sql_obj_cond->data as $data_cond)
						{
							$return_tmp2 = array();

							$return_tmp2["cond_attribute"]	= "Called-Station-Id == ". $data_cond["station_id"] ."";
							$return_tmp2["cond_ldapgroup"]	= $data_cond["nas_ldapgroup"];

							$return_tmp["nas_conditions"][]	= $return_tmp2;
						}
					}

					$return[]	= $return_tmp;
				}

				return $return;
			}

			return 0;
		}
		else
		{
			throw new SoapFault("Sender", "ACCESS_DENIED");
		}

	} // end of fetch_nas_config




				
} // end of api_phpfreeradius class



// define server
$server = new SoapServer("phpfreeradius.wsdl");
$server->setClass("api_phpfreeradius");
$server->handle();



?>

