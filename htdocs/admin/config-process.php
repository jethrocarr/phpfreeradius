<?php
/*
	admin/config-process.php
	
	Access: radiusadmins only

	Updates the system configuration.
*/


// includes
include_once("../include/config.php");
include_once("../include/amberphplib/main.php");
include_once("../include/application/main.php");


if (user_permissions_get("radiusadmins"))
{
	/*
		Fetch Data
	*/

	$data["DEFAULT_NAS_PASSWORD"]			= security_form_input_predefined("any", "DEFAULT_NAS_PASSWORD", 1, "");

	$data["NAMEDMANAGER_FEATURE"]			= security_form_input_predefined("any", "NAMEDMANAGER_FEATURE", 1, "");

	if ($data["NAMEDMANAGER_FEATURE"] == "enabled")
	{
		$data["NAMEDMANAGER_API_URL"]			= security_form_input_predefined("any", "NAMEDMANAGER_API_URL", 1, "");
		$data["NAMEDMANAGER_API_KEY"]			= security_form_input_predefined("any", "NAMEDMANAGER_API_KEY", 1, "");
		$data["NAMEDMANAGER_DEFAULT_A"]			= security_form_input_predefined("checkbox", "NAMEDMANAGER_DEFAULT_A", 0, "");
		$data["NAMEDMANAGER_DEFAULT_PTR"]		= security_form_input_predefined("checkbox", "NAMEDMANAGER_DEFAULT_PTR", 0, "");
	}

	$data["DATEFORMAT"]				= security_form_input_predefined("any", "DATEFORMAT", 1, "");
	$data["TIMEZONE_DEFAULT"]			= security_form_input_predefined("any", "TIMEZONE_DEFAULT", 1, "");

	$data["FEATURE_LOGS_ENABLE"]			= security_form_input_predefined("checkbox", "FEATURE_LOGS_ENABLE", 0, "");

	if ($data["FEATURE_LOGS_ENABLE"])
	{
		$data["FEATURE_LOGS_PERIOD"]		= security_form_input_predefined("int", "FEATURE_LOGS_PERIOD", 0, "");
		$data["LOG_RETENTION_PERIOD"]		= security_form_input_predefined("int", "LOG_RETENTION_PERIOD", 0, "");
		$data["LOG_UPDATE_INTERVAL"]		= security_form_input_predefined("int", "LOG_UPDATE_INTERVAL", 1, "");

		$data["LOG_RETENTION_CHECKTIME"]	= 0; // reset check time, so that the log retention processes run
	}
	else
	{
		$data["FEATURE_LOGS_CHECKTIME"]		= 0;
		$data["FEATURE_LOGS_PERIOD"]		= 0;
		$data["LOG_RETENTION_CHECKTIME"]	= 0;
		$data["LOG_UPDATE_INTERVAL"]		= "5";
	}


	
	/*
		Error Processing
	*/



	// connect to the API to verify valid information
	if ($data["NAMEDMANAGER_FEATURE"] == "enabled" && $data["NAMEDMANAGER_API_URL"] && $data["NAMEDMANAGER_API_KEY"])
	{
		log_write("debug", "process", "Attempting to connect to the NamedManager API on ". $data["NAMEDMANAGER_API_URL"] ."");


		$obj_named		= New namedmanager;

		$obj_named->api_url	= $data["NAMEDMANAGER_API_URL"];
		$obj_named->api_key	= $data["NAMEDMANAGER_API_KEY"];

		if ($obj_named->authenticate())
		{
			log_write("notification", "process", "Test authentication to Named Manager completed successfully");
		}
		else
		{
			log_write("error", "process", "An error occured whilst attempting to initiate a test connection to NamedManager application");

			error_flag_field("NAMEDMANAGER_API_URL");
			error_flag_field("NAMEDMANAGER_API_KEY");
		}
	}



	if (error_check())
	{
		$_SESSION["error"]["form"]["config"] = "failed";
		header("Location: ../index.php?page=admin/config.php");
		exit(0);
	}
	else
	{
		/*
			Apply Changes
		*/

		
		$_SESSION["error"] = array();

		/*
			Start Transaction
		*/
		$sql_obj = New sql_query;
		$sql_obj->trans_begin();

	
		/*
			Update all the config fields

			We have already loaded the data for all the fields, so simply need to go and set all the values
			based on the naming of the $data array.
		*/

		foreach (array_keys($data) as $data_key)
		{
			$sql_obj->string = "UPDATE config SET value='". $data[$data_key] ."' WHERE name='$data_key' LIMIT 1";
			$sql_obj->execute();
		}


		/*
			Commit
		*/

		if (error_check())
		{
			$sql_obj->trans_rollback();

			log_write("error", "process", "An error occured whilst updating configuration, no changes have been applied.");
		}
		else
		{
			$sql_obj->trans_commit();

			log_write("notification", "process", "Configuration Updated Successfully");
		}

		header("Location: ../index.php?page=admin/config.php");
		exit(0);


	} // if valid data input
	
	
} // end of "is user logged in?"
else
{
	// user does not have permissions to access this page.
	error_render_noperms();
	header("Location: ../index.php?page=message.php");
	exit(0);
}


?>
